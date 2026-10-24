<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Parcelamento;
use PDO;

class ParcelamentoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getParcelamentosPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT p.*, c.nome AS nome_categoria, " .
            "COALESCE(p.valor_parcela, p.valor_total / NULLIF(p.quantidade_parcelas, 0)) AS valor_parcela, " .
            "COUNT(r.id) AS parcelas_pagas FROM parcelamentos p " .
            "LEFT JOIN categorias c ON c.id = p.categoria_id " .
            "LEFT JOIN registro_financeiro r ON r.parcelamento_id = p.id AND r.data_registro <= CURRENT_DATE " .
            "WHERE p.usuario_id = :usuarioId ORDER BY p.data_primeira_parcela DESC, p.id DESC";

        $sql = str_replace('WHERE p.usuario_id', 'WHERE p.usuario_id', $sql);
        $sql = str_replace('ORDER BY p.data_primeira_parcela DESC, p.id DESC', 'GROUP BY p.id ORDER BY p.data_primeira_parcela DESC, p.id DESC', $sql);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();

        $parcelamentos = [];

        foreach ($stmt->fetchAll() as $parcelamento) {
            $parcelamentos[] = Parcelamento::arrayParaObjeto($parcelamento);
        }

        return $parcelamentos;
    }

    // sempre filtra pelo dono, pra um usuário não acessar o parcelamento de outro
    public function getParcelamento(int $id, int $usuarioId)
    {
        $sql = "SELECT p.*, c.nome AS nome_categoria, " .
            "COALESCE(p.valor_parcela, p.valor_total / NULLIF(p.quantidade_parcelas, 0)) AS valor_parcela, " .
            "COUNT(r.id) AS parcelas_pagas FROM parcelamentos p " .
            "LEFT JOIN categorias c ON c.id = p.categoria_id " .
            "LEFT JOIN registro_financeiro r ON r.parcelamento_id = p.id AND r.data_registro <= CURRENT_DATE " .
            "WHERE p.id = :id AND p.usuario_id = :usuarioId";

        $sql = str_replace('WHERE p.id', 'WHERE p.id', $sql) . ' GROUP BY p.id';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();

        $parcelamento = $stmt->fetch();

        if ($parcelamento == null) {
            return false;
        }

        return Parcelamento::arrayParaObjeto($parcelamento);
    }

    public function saveParcelamento(Parcelamento $parcelamento): bool
    {
        $this->connection->beginTransaction();

        try {
            $sql = "INSERT INTO parcelamentos " .
                "(usuario_id, categoria_id, descricao, valor_total, valor_parcela, quantidade_parcelas, parcelas_pagas, data_primeira_parcela, tag, lancar_futuros) " .
                "VALUES (:usuarioId, :categoriaId, :descricao, :valorTotal, :valorParcela, :quantidadeParcelas, 0, :dataPrimeiraParcela, :tag, :lancarFuturos)";
            $stmt = $this->connection->prepare($sql);
            $this->bindParcelamento($stmt, $parcelamento);
            $stmt->execute();

            $this->criarRegistros($parcelamento, (int) $this->connection->lastInsertId(), 0);
            $this->connection->commit();

            return true;
        } catch (\Throwable $erro) {
            $this->connection->rollBack();
            return false;
        }
    }

    public function updateParcelamento(Parcelamento $parcelamento): bool
    {
        $this->connection->beginTransaction();

        try {
            $sql = "UPDATE parcelamentos SET categoria_id = :categoriaId, descricao = :descricao, valor_total = :valorTotal, " .
                "valor_parcela = :valorParcela, quantidade_parcelas = :quantidadeParcelas, data_primeira_parcela = :dataPrimeiraParcela, " .
                "tag = :tag, lancar_futuros = :lancarFuturos " .
                "WHERE id = :id AND usuario_id = :usuarioId";

            $stmt = $this->connection->prepare($sql);
            $this->bindParcelamento($stmt, $parcelamento);
            $stmt->bindValue(':id', $parcelamento->getId());
            $stmt->execute();

        $delete = $this->connection->prepare(
            "DELETE FROM registro_financeiro WHERE parcelamento_id = :id AND data_registro > CURRENT_DATE"
        );
        $delete->execute(['id' => $parcelamento->getId()]);
            $this->criarRegistros($parcelamento, $parcelamento->getId(), false);
            $this->connection->commit();

            return true;
        } catch (\Throwable $erro) {
            $this->connection->rollBack();
            return false;
        }
    }

    public function deleteParcelamento(int $id, int $usuarioId): bool
    {
        $this->connection->beginTransaction();
        $deleteRegistros = $this->connection->prepare("DELETE FROM registro_financeiro WHERE parcelamento_id = :id AND usuario_id = :usuarioId");
        $deleteRegistros->execute(['id' => $id, 'usuarioId' => $usuarioId]);
        $sql = "DELETE FROM parcelamentos WHERE id = :id AND usuario_id = :usuarioId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':usuarioId', $usuarioId);

        return $stmt->execute();
        $resultado = $stmt->execute();
        $this->connection->commit();

        return $resultado;
    }

    private function bindParcelamento($stmt, Parcelamento $parcelamento): void
    {
        $stmt->bindValue(':usuarioId', $parcelamento->getUsuarioId());
        $stmt->bindValue(':categoriaId', $parcelamento->getCategoriaId());
        $stmt->bindValue(':descricao', $parcelamento->getDescricao());
        $stmt->bindValue(':valorTotal', $parcelamento->getValorTotal());
        $stmt->bindValue(':valorParcela', $parcelamento->getValorParcela());
        $stmt->bindValue(':quantidadeParcelas', $parcelamento->getQuantidadeParcelas());
        $stmt->bindValue(':dataPrimeiraParcela', $parcelamento->getDataPrimeiraParcela());
        $stmt->bindValue(':tag', $parcelamento->getTag());
        $stmt->bindValue(':lancarFuturos', $parcelamento->getLancarFuturos(), PDO::PARAM_BOOL);
    }

    private function criarRegistros(Parcelamento $parcelamento, int $parcelamentoId, int $inicio): void
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO registro_financeiro " .
            "(usuario_id, categoria_id, tipo, valor, data_registro, descricao, parcelamento_id) " .
            "VALUES (:usuarioId, :categoriaId, 'despesa', :valor, :dataRegistro, :descricao, :parcelamentoId)"
        );
        $hoje = new \DateTimeImmutable('today');
        $dataInicial = new \DateTimeImmutable($parcelamento->getDataPrimeiraParcela());

        for ($indice = $inicio; $indice < $parcelamento->getQuantidadeParcelas(); $indice++) {
            if (!$parcelamento->getLancarFuturos() && $indice > 0) {
                break;
            }

            $data = $dataInicial->modify("+{$indice} months");
            if ($data <= $hoje && $inicio > 0) {
                continue;
            }

            $stmt->execute([
                'usuarioId' => $parcelamento->getUsuarioId(),
                'categoriaId' => $parcelamento->getCategoriaId(),
                'valor' => $parcelamento->getValorParcela(),
                'dataRegistro' => $data->format('Y-m-d'),
                'descricao' => $parcelamento->getTag() ?: $parcelamento->getDescricao(),
                'parcelamentoId' => $parcelamentoId,
            ]);
        }
    }
}
