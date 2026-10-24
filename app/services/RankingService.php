<?php

namespace app\services;

use app\repositories\HistoricoXpRepository;

class RankingService
{
    private HistoricoXpRepository $repository;

    public function __construct()
    {
        $this->repository = new HistoricoXpRepository();
    }

    public function getRanking(string $periodo): array
    {
        if (!in_array($periodo, ['semanal', 'mensal', 'geral'], true)) {
            $periodo = 'geral';
        }

        return $this->repository->getRanking($periodo);
    }
}