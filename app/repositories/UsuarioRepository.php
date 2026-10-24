<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Usuario;
use PDO;

class UsuarioRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function saveUsuario(Usuario $usuario): bool
    {
        $sql = "INSERT INTO usuarios (nome, nickname, email, senha, data_nascimento, perfil) " .
            "VALUES (:nome, :nickname, :email, :senha, :dataNascimento, :perfil)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':nickname', $usuario->getNickname());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha', password_hash($usuario->getSenha(), PASSWORD_BCRYPT));
        $stmt->bindValue(':dataNascimento', $usuario->getDataNascimento());
        $stmt->bindValue(':perfil', $usuario->getPerfil());

        return $stmt->execute();
    }

    public function getUsuarioById(int $id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if ($usuario == null) {
            return false;
        }

        return Usuario::arrayParaObjeto($usuario);
    }

    public function getUsuarioByEmail(string $email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if ($usuario == null) {
            return false;
        }

        return Usuario::arrayParaObjeto($usuario);
    }

    public function getUsuarioByNickname(string $nickname)
    {
        $sql = "SELECT * FROM usuarios WHERE nickname = :nickname";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if ($usuario == null) {
            return false;
        }

        return Usuario::arrayParaObjeto($usuario);
    }

    public function getUsuarios(): array
    {
        $stmt = $this->connection->query("SELECT * FROM usuarios ORDER BY criado_em DESC, id DESC");
        $usuarios = [];

        foreach ($stmt->fetchAll() as $usuario) {
            $usuarios[] = Usuario::arrayParaObjeto($usuario);
        }

        return $usuarios;
    }

    public function contarUsuarios(): int
    {
        return (int) $this->connection->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    }

    public function contarUsuariosAtivos(): int
    {
        return (int) $this->connection->query("SELECT COUNT(*) FROM usuarios WHERE status = 'ativo'")->fetchColumn();
    }

    public function contarAdministradoresAtivos(): int
    {
        return (int) $this->connection->query(
            "SELECT COUNT(*) FROM usuarios WHERE perfil = 'administrador' AND status = 'ativo'"
        )->fetchColumn();
    }

    public function atualizarStatus(int $id, string $status): bool
    {
        $stmt = $this->connection->prepare("UPDATE usuarios SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function atualizarPerfil(int $id, string $perfil): bool
    {
        $stmt = $this->connection->prepare("UPDATE usuarios SET perfil = :perfil WHERE id = :id");
        $stmt->execute(['perfil' => $perfil, 'id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET nome = :nome, nickname = :nickname, email = :email, " .
            "data_nascimento = :dataNascimento, perfil = :perfil, status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':nickname', $usuario->getNickname());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':dataNascimento', $usuario->getDataNascimento());
        $stmt->bindValue(':perfil', $usuario->getPerfil());
        $stmt->bindValue(':status', $usuario->getStatus());
        $stmt->bindValue(':id', $usuario->getId());

        return $stmt->execute();
    }

    public function updateFoto(int $id, ?string $foto): bool
    {
        $sql = "UPDATE usuarios SET foto = :foto WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':foto', $foto);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function deleteUsuario(int $id): bool
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}