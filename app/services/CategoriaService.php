<?php

namespace app\services;

use app\repositories\CategoriaRepository;

class CategoriaService
{
    private CategoriaRepository $repository;

    public function __construct()
    {
        $this->repository = new CategoriaRepository();
    }

    public function getCategorias(?string $tipo = null): array
    {
        return $this->repository->getCategorias($tipo);
    }

    public function getCategoria(int $id)
    {
        return $this->repository->getCategoria($id);
    }

    public function existeCategoria(int $id): bool
    {
        return $this->repository->getCategoria($id) !== false;
    }
}
