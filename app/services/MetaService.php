<?php

namespace app\services;

use app\models\Meta;
use app\repositories\MetaRepository;
use DateTimeImmutable;

class MetaService
{
    private MetaRepository $repository;
    private array $erros = [];

    public function __construct()
    {
        $this->repository = new MetaRepository();
    }

    public function getMetas(int $usuarioId): array
    {
        return $this->repository->getMetasPorUsuario($usuarioId);
    }

    public function getMeta(int $id, int $usuarioId)
    {
        return $this->repository->getMeta($id, $usuarioId);
    }

    public function saveMeta(Meta $meta): bool
    {
        if (!$this->validar($meta)) {
            return false;
        }

        return $this->repository->saveMeta($meta);
    }

    public function updateMeta(Meta $meta): bool
    {
        if (!$this->repository->getMeta($meta->getId(), $meta->getUsuarioId())) {
            $this->erros['meta'] = "Meta não encontrada";

            return false;
        }

        if (!$this->validar($meta)) {
            return false;
        }

        return $this->repository->updateMeta($meta);
    }

    public function deleteMeta(int $id, int $usuarioId): bool
    {
        return $this->repository->deleteMeta($id, $usuarioId);
    }

    public function adicionarProgresso(int $id, int $usuarioId, float $valor): bool
    {
        if ($valor <= 0 || !$this->repository->getMeta($id, $usuarioId)) {
            return false;
        }

        return $this->repository->adicionarProgresso($id, $usuarioId, $valor);
    }

    public function getErros(): array
    {
        return $this->erros;
    }

    private function validar(Meta $meta): bool
    {
        $this->erros = [];

        if ($meta->getValorAlvo() <= 0) {
            $this->erros['valorAlvo'] = "O valor alvo deve ser maior que zero";
        }

        if ($meta->getValorAtual() < 0) {
            $this->erros['valorAtual'] = "O valor atual não pode ser negativo";
        }

        $hoje = new DateTimeImmutable('today');
        $dataLimite = DateTimeImmutable::createFromFormat('Y-m-d', $meta->getDataLimite());

        if (!$dataLimite) {
            $this->erros['dataLimite'] = "Informe uma data limite válida";
        } elseif ($dataLimite->setTime(0, 0) <= $hoje) {
            $this->erros['dataLimite'] = "A data limite deve ser posterior à data de hoje";
        }

        return empty($this->erros);
    }
}
