<?php

namespace app\services;

use app\repositories\TransacaoRepository;
use DateTimeImmutable;

class RelatorioService
{
    private TransacaoRepository $repository;

    public function __construct()
    {
        $this->repository = new TransacaoRepository();
    }

    public function gerar(int $usuarioId, int $ano, int $mes): array
    {
        $mes = max(1, min(12, $mes));
        $inicio = new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes));
        $fim = $inicio->modify('last day of this month');
        $resumo = $this->repository->getResumoPeriodo($usuarioId, $inicio->format('Y-m-d'), $fim->format('Y-m-d'));

        return [
            'inicio' => $inicio->format('Y-m-d'),
            'fim' => $fim->format('Y-m-d'),
            'receitas' => (float) $resumo['receitas'],
            'despesas' => (float) $resumo['despesas'],
            'saldo' => (float) $resumo['receitas'] - (float) $resumo['despesas'],
            'transacoes' => $this->repository->getTransacoesPeriodo($usuarioId, $inicio->format('Y-m-d'), $fim->format('Y-m-d')),
        ];
    }

    public function gerarAnual(int $usuarioId, int $ano): array
    {
        $inicio = new DateTimeImmutable(sprintf('%04d-01-01', $ano));
        $fim = $inicio->modify('last day of December');
        $resumo = $this->repository->getResumoPeriodo($usuarioId, $inicio->format('Y-m-d'), $fim->format('Y-m-d'));

        return [
            'inicio' => $inicio->format('Y-m-d'),
            'fim' => $fim->format('Y-m-d'),
            'receitas' => (float) $resumo['receitas'],
            'despesas' => (float) $resumo['despesas'],
            'saldo' => (float) $resumo['receitas'] - (float) $resumo['despesas'],
            'transacoes' => $this->repository->getTransacoesPeriodo($usuarioId, $inicio->format('Y-m-d'), $fim->format('Y-m-d')),
        ];
    }
}