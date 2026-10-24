<?php

namespace app\models;

class UsuarioAdministrador
{
    private int $usuarioId;
    private string $criadoEm;

    public function __construct(int $usuarioId, string $criadoEm = '')
    {
        $this->usuarioId = $usuarioId;
        $this->criadoEm = $criadoEm;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getCriadoEm(): string
    {
        return $this->criadoEm;
    }

    public static function arrayParaObjeto(array $usuarioAdministrador): self
    {
        return new self(
            $usuarioAdministrador['usuario_id'],
            $usuarioAdministrador['criado_em']
        );
    }
}
