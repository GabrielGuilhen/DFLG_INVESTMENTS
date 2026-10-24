<?php

namespace app\services;

use app\repositories\LimiteCategoriaRepository;

class LimiteCategoriaService
{
    private LimiteCategoriaRepository $repository;

    public function __construct()
    {
        $this->repository = new LimiteCategoriaRepository();
    }

    public function listar(int $usuarioId): array { return $this->repository->listar($usuarioId); }

    public function salvar(int $usuarioId, int $categoriaId, float $limite): bool
    {
        return $limite > 0 && $this->repository->salvar($usuarioId, $categoriaId, $limite);
    }
}