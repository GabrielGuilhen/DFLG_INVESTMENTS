<?php

namespace app\models;

use DateTimeImmutable;

class Usuario
{
    private int $id;
    private string $nome;
    private string $nickname;
    private string $email;
    private string $senha;
    private string $dataNascimento;
    private ?string $foto;
    private string $status;
    private string $perfil;
    private DateTimeImmutable $criadoEm;

    public function __construct(
        int $id,
        string $nome,
        string $nickname,
        string $email,
        string $senha,
        string $dataNascimento,
        string $perfil = 'comum',
        string $status = 'ativo',
        ?string $foto = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->nickname = $nickname;
        $this->email = $email;
        $this->senha = $senha;
        $this->dataNascimento = $dataNascimento;
        $this->perfil = $perfil;
        $this->status = $status;
        $this->foto = $foto;
        $this->criadoEm = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function getDataNascimento(): string
    {
        return $this->dataNascimento;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPerfil(): string
    {
        return $this->perfil;
    }

    public function eAdmin(): bool
    {
        return $this->perfil === 'administrador';
    }

    public function getCriadoEm(): DateTimeImmutable
    {
        return $this->criadoEm;
    }

    public function setCriadoEm(DateTimeImmutable $criadoEm): void
    {
        $this->criadoEm = $criadoEm;
    }

    public static function arrayParaObjeto(array $usuario): self
    {
        return new self(
            $usuario['id'],
            $usuario['nome'],
            $usuario['nickname'],
            $usuario['email'],
            $usuario['senha'],
            $usuario['data_nascimento'],
            $usuario['perfil'],
            $usuario['status'],
            $usuario['foto'] ?? null
        );
    }
}
