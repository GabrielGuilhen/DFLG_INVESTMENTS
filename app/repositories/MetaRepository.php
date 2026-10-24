<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Meta;
use PDO;

class MetaRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getMetasPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT * FROM metas WHERE usuario_id = :usuarioId ORDER BY data_limite ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();

        $metas = [];

        foreach ($stmt->fetchAll() as $meta) {
            $metas[] = Meta::arrayParaObjeto($meta);
        }

        return $metas;
    }

    // sempre filtra pelo dono, pra um usuário não acessar a meta de outro
    public function getMeta(int $id, int $usuarioId)
    {
        $sql = "SELECT * FROM metas WHERE id = :id AND usuario_id = :usuarioId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();

        $meta = $stmt->fetch();

        if ($meta == null) {
            return false;
        }

        return Meta::arrayParaObjeto($meta);
    }

    public function saveMeta(Meta $meta): bool
    {
        $sql = "INSERT INTO metas (usuario_id, nome_meta, tipo_meta, valor_alvo, valor_atual, data_limite) " .
            "VALUES (:usuarioId, :nomeMeta, :tipoMeta, :valorAlvo, :valorAtual, :dataLimite)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $meta->getUsuarioId());
        $stmt->bindValue(':nomeMeta', $meta->getNomeMeta());
        $stmt->bindValue(':tipoMeta', $meta->getTipoMeta());
        $stmt->bindValue(':valorAlvo', $meta->getValorAlvo());
        $stmt->bindValue(':valorAtual', $meta->getValorAtual());
        $stmt->bindValue(':dataLimite', $meta->getDataLimite());

        return $stmt->execute();
    }

    public function updateMeta(Meta $meta): bool
    {
        $sql = "UPDATE metas SET nome_meta = :nomeMeta, tipo_meta = :tipoMeta, valor_alvo = :valorAlvo, " .
            "valor_atual = :valorAtual, data_limite = :dataLimite WHERE id = :id AND usuario_id = :usuarioId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nomeMeta', $meta->getNomeMeta());
        $stmt->bindValue(':tipoMeta', $meta->getTipoMeta());
        $stmt->bindValue(':valorAlvo', $meta->getValorAlvo());
        $stmt->bindValue(':valorAtual', $meta->getValorAtual());
        $stmt->bindValue(':dataLimite', $meta->getDataLimite());
        $stmt->bindValue(':id', $meta->getId());
        $stmt->bindValue(':usuarioId', $meta->getUsuarioId());

        return $stmt->execute();
    }

    public function deleteMeta(int $id, int $usuarioId): bool
    {
        $sql = "DELETE FROM metas WHERE id = :id AND usuario_id = :usuarioId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':usuarioId', $usuarioId);

        return $stmt->execute();
    }

    public function adicionarProgresso(int $id, int $usuarioId, float $valor): bool
    {
        $stmt = $this->connection->prepare(
            "UPDATE metas SET valor_atual = LEAST(valor_alvo, valor_atual + :valor) " .
            "WHERE id = :id AND usuario_id = :usuarioId"
        );
        $stmt->execute(['valor' => $valor, 'id' => $id, 'usuarioId' => $usuarioId]);

        return $stmt->rowCount() > 0;
    }
}
