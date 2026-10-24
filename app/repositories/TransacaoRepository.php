<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Transacao;
use PDO;

class TransacaoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getTransacoesPorUsuario(int $usuarioId, ?string $tipo = null, ?int $categoriaId = null): array
    {
        $sql = "SELECT r.*, c.nome AS nome_categoria FROM registro_financeiro r " .
            "LEFT JOIN categorias c ON c.id = r.categoria_id " .
            "WHERE r.usuario_id = :usuarioId AND r.data_registro <= CURRENT_DATE";

        if ($tipo) {
            $sql .= " AND r.tipo = :tipo";
        }

        if ($categoriaId) {
            $sql .= " AND r.categoria_id = :categoriaId";
        }

        $sql .= " ORDER BY r.data_registro DESC, r.id DESC";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);

        if ($tipo) {
            $stmt->bindValue(':tipo', $tipo);
        }

        if ($categoriaId) {
            $stmt->bindValue(':categoriaId', $categoriaId);
        }

        $stmt->execute();

        $transacoes = [];

        foreach ($stmt->fetchAll() as $transacao) {
            $transacoes[] = Transacao::arrayParaObjeto($transacao);
        }

        return $transacoes;
    }

    // sempre filtra pelo dono, pra um usuário não acessar a transação de outro
    public function getTransacao(int $id, int $usuarioId)
    {
        $sql = "SELECT r.*, c.nome AS nome_categoria FROM registro_financeiro r " .
            "LEFT JOIN categorias c ON c.id = r.categoria_id " .
            "WHERE r.id = :id AND r.usuario_id = :usuarioId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();

        $transacao = $stmt->fetch();

        if ($transacao == null) {
            return false;
        }

        return Transacao::arrayParaObjeto($transacao);
    }

    public function saveTransacao(Transacao $transacao): bool
    {
        $sql = "INSERT INTO registro_financeiro (usuario_id, categoria_id, tipo, valor, data_registro, descricao) " .
            "VALUES (:usuarioId, :categoriaId, :tipo, :valor, :dataRegistro, :descricao)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $transacao->getUsuarioId());
        $stmt->bindValue(':categoriaId', $transacao->getCategoriaId());
        $stmt->bindValue(':tipo', $transacao->getTipo());
        $stmt->bindValue(':valor', $transacao->getValor());
        $stmt->bindValue(':dataRegistro', $transacao->getDataRegistro());
        $stmt->bindValue(':descricao', $transacao->getDescricao());

        return $stmt->execute();
    }

    public function updateTransacao(Transacao $transacao): bool
    {
        $sql = "UPDATE registro_financeiro SET categoria_id = :categoriaId, tipo = :tipo, valor = :valor, " .
            "data_registro = :dataRegistro, descricao = :descricao WHERE id = :id AND usuario_id = :usuarioId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':categoriaId', $transacao->getCategoriaId());
        $stmt->bindValue(':tipo', $transacao->getTipo());
        $stmt->bindValue(':valor', $transacao->getValor());
        $stmt->bindValue(':dataRegistro', $transacao->getDataRegistro());
        $stmt->bindValue(':descricao', $transacao->getDescricao());
        $stmt->bindValue(':id', $transacao->getId());
        $stmt->bindValue(':usuarioId', $transacao->getUsuarioId());

        return $stmt->execute();
    }

    public function deleteTransacao(int $id, int $usuarioId): bool
    {
        $sql = "DELETE FROM registro_financeiro WHERE id = :id AND usuario_id = :usuarioId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':usuarioId', $usuarioId);

        return $stmt->execute();
    }

    public function getTotalPorTipo(int $usuarioId, string $tipo): float
    {
        $sql = "SELECT COALESCE(SUM(valor), 0) FROM registro_financeiro " .
            "WHERE usuario_id = :usuarioId AND tipo = :tipo AND data_registro <= CURRENT_DATE";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->execute();

        return (float) $stmt->fetchColumn();
    }

    public function getEvolucaoMensal(int $usuarioId, int $meses = 6): array
    {
        $meses = max(1, min($meses, 24));
        $sql = "SELECT DATE_FORMAT(data_registro, '%Y-%m') AS mes, " .
            "SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) AS receitas, " .
            "SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) AS despesas " .
            "FROM registro_financeiro " .
            "WHERE usuario_id = :usuarioId " .
            "AND data_registro >= DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL {$meses} MONTH), '%Y-%m-01') " .
            "AND data_registro <= CURRENT_DATE " .
            "GROUP BY DATE_FORMAT(data_registro, '%Y-%m') ORDER BY mes ASC";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getResumoPeriodo(int $usuarioId, string $inicio, string $fim): array
    {
        $stmt = $this->connection->prepare(
            "SELECT COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END), 0) AS receitas, " .
            "COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END), 0) AS despesas " .
            "FROM registro_financeiro WHERE usuario_id = :usuarioId " .
            "AND data_registro BETWEEN :inicio AND :fim"
        );
        $stmt->execute(['usuarioId' => $usuarioId, 'inicio' => $inicio, 'fim' => $fim]);

        return $stmt->fetch() ?: ['receitas' => 0, 'despesas' => 0];
    }

    public function getTransacoesPeriodo(int $usuarioId, string $inicio, string $fim): array
    {
        $stmt = $this->connection->prepare(
            "SELECT r.*, c.nome AS nome_categoria FROM registro_financeiro r " .
            "LEFT JOIN categorias c ON c.id = r.categoria_id " .
            "WHERE r.usuario_id = :usuarioId AND r.data_registro BETWEEN :inicio AND :fim " .
            "ORDER BY r.data_registro ASC, r.id ASC"
        );
        $stmt->execute(['usuarioId' => $usuarioId, 'inicio' => $inicio, 'fim' => $fim]);

        return array_map(
            fn(array $item): Transacao => Transacao::arrayParaObjeto($item),
            $stmt->fetchAll()
        );
    }
}
