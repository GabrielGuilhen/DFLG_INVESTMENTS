<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\UsuarioAdministrador;
use PDO;

class UsuarioAdministradorRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function criarParaUsuario(int $usuarioId): bool
    {
        $sql = "INSERT IGNORE INTO administradores (usuario_id) VALUES (:usuarioId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);

        return $stmt->execute();
    }

    public function getPorUsuarioId(int $usuarioId)
    {
        $sql = "SELECT * FROM administradores WHERE usuario_id = :usuarioId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();
        $usuarioAdministrador = $stmt->fetch();

        if ($usuarioAdministrador == null) {
            return false;
        }

        return UsuarioAdministrador::arrayParaObjeto($usuarioAdministrador);
    }
}
