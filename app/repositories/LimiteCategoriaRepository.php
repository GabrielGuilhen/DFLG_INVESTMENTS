<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use PDO;

class LimiteCategoriaRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function listar(int $usuarioId): array
    {
        $stmt = $this->connection->prepare(
            "SELECT c.id, c.nome, c.icone, c.tipo, l.limite_mensal, " .
            "COALESCE(SUM(CASE WHEN r.data_registro >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01') " .
            "AND r.data_registro <= CURRENT_DATE THEN r.valor ELSE 0 END), 0) AS gasto_mes " .
            "FROM categorias c LEFT JOIN limites_categoria l ON l.categoria_id = c.id AND l.usuario_id = :usuarioId " .
            "LEFT JOIN registro_financeiro r ON r.categoria_id = c.id AND r.usuario_id = :usuarioId2 " .
            "WHERE c.tipo = 'despesa' GROUP BY c.id, c.nome, c.icone, c.tipo, l.limite_mensal ORDER BY c.nome"
        );
        $stmt->execute(['usuarioId' => $usuarioId, 'usuarioId2' => $usuarioId]);

        return $stmt->fetchAll();
    }

    public function salvar(int $usuarioId, int $categoriaId, float $limite): bool
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO limites_categoria (usuario_id, categoria_id, limite_mensal) VALUES (:usuarioId, :categoriaId, :limite) " .
            "ON DUPLICATE KEY UPDATE limite_mensal = VALUES(limite_mensal)"
        );

        return $stmt->execute(['usuarioId' => $usuarioId, 'categoriaId' => $categoriaId, 'limite' => $limite]);
    }
}