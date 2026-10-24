<?php

namespace app\services;

use app\models\HistoricoXp;
use app\repositories\HistoricoXpRepository;
use app\repositories\UsuarioComumRepository;
use DateTimeImmutable;

class GamificacaoService
{
    // Quadro 5: ações que geram XP, com a pontuação e a frequência (diaria ou unica)
    private const ACOES = [
        'acesso_diario' => ['xp' => 5, 'frequencia' => 'diaria'],
        'simulador' => ['xp' => 10, 'frequencia' => 'diaria'],
        'streak_7' => ['xp' => 25, 'frequencia' => 'unica'],
        'streak_30' => ['xp' => 100, 'frequencia' => 'unica'],
        'streak_100' => ['xp' => 300, 'frequencia' => 'unica'],
        'streak_1000' => ['xp' => 5000, 'frequencia' => 'unica'],
        'perfil_completo' => ['xp' => 20, 'frequencia' => 'unica'],
        'preferencias' => ['xp' => 10, 'frequencia' => 'unica'],
        'relatorios' => ['xp' => 15, 'frequencia' => 'unica'],
    ];

    // Quadro 5: marcos da sequência de acesso (dias => ação que concede o XP do marco)
    private const MARCOS_STREAK = [
        7 => 'streak_7',
        30 => 'streak_30',
        100 => 'streak_100',
        1000 => 'streak_1000',
    ];

    private UsuarioComumRepository $repository;
    private HistoricoXpRepository $historicoRepository;

    public function __construct()
    {
        $this->repository = new UsuarioComumRepository();
        $this->historicoRepository = new HistoricoXpRepository();
    }

    // concede o XP da ação (Quadro 5), respeitando a frequência: uma vez por dia ou uma única vez
    public function concederXp(int $usuarioId, string $acao): bool
    {
        if (!isset(self::ACOES[$acao])) {
            return false;
        }

        $usuarioComum = $this->repository->getPorUsuarioId($usuarioId);

        if (!$usuarioComum) {
            return false;
        }

        $hoje = (new DateTimeImmutable())->format('Y-m-d');
        $regra = self::ACOES[$acao];
        $data = $regra['frequencia'] === 'diaria' ? $hoje : null;

        if ($this->historicoRepository->existeConcessao($usuarioId, $acao, $data)) {
            return false;
        }

        $this->historicoRepository->registrar(new HistoricoXp(0, $usuarioId, $acao, $regra['xp'], $hoje));

        $usuarioComum->setXpTotal($usuarioComum->getXpTotal() + $regra['xp']);
        $usuarioComum->setNivel($this->calcularNivel($usuarioComum->getXpTotal()));

        return $this->repository->atualizarXp($usuarioComum);
    }

    private function calcularNivel(int $xpTotal): int 
    {
        $nivel = 1;
        $xpRestante = $xpTotal;

        while ($xpRestante >= $nivel * 100) {
            $xpRestante -= $nivel * 100;
            $nivel++;
        }

        return $nivel;
    }

    public function getGamificacao(int $usuarioId)
    {
        return $this->repository->getPorUsuarioId($usuarioId);
    }

    // seção 2.3.2: o nível n para o n+1 exige n x 100 XP; o XP acumulado até chegar ao nível n é 50 x n x (n - 1)
    public function getProgressoNivel(int $xpTotal): array
    {
        $nivel = $this->calcularNivel($xpTotal);
        $xpNoNivel = $xpTotal - 50 * $nivel * ($nivel - 1);
        $xpParaProximo = $nivel * 100;

        return [
            'xpNoNivel' => $xpNoNivel,
            'xpParaProximo' => $xpParaProximo,
            'percentual' => intdiv($xpNoNivel * 100, $xpParaProximo),
        ];
    }

    // salva a visibilidade no ranking; a primeira vez que o usuário personaliza as preferências dá XP (Quadro 5)
    public function atualizarVisibilidade(int $usuarioId, bool $visibilidade): bool
    {
        $usuarioComum = $this->repository->getPorUsuarioId($usuarioId);

        if (!$usuarioComum) {
            return false;
        }

        $usuarioComum->setVisibilidadeRanking($visibilidade);

        if (!$this->repository->atualizarVisibilidade($usuarioComum)) {
            return false;
        }

        $this->concederXp($usuarioId, 'preferencias');

        return true;
    }

    public function verificarEAtualizarStreak(int $usuarioId): void
{
    $usuarioComum = $this->repository->getPorUsuarioId($usuarioId);

    if (!$usuarioComum) {
        return;
    }

    $hoje = (new DateTimeImmutable())->format('Y-m-d');
    $ontem = (new DateTimeImmutable('-1 day'))->format('Y-m-d');

    if ($usuarioComum->getUltimoAcesso() === $hoje) {
        return; // já processado hoje
    }

    if ($usuarioComum->getUltimoAcesso() === $ontem) {
        $usuarioComum->setStreakAtual($usuarioComum->getStreakAtual() + 1);
    } else {
        $usuarioComum->setStreakAtual(1); // quebrou a sequência, recomeça
    }

    if ($usuarioComum->getStreakAtual() > $usuarioComum->getMaiorStreak()) {
        $usuarioComum->setMaiorStreak($usuarioComum->getStreakAtual());
    }

    $usuarioComum->setUltimoAcesso($hoje);
    $this->repository->atualizarStreak($usuarioComum);

    $this->concederXp($usuarioId, 'acesso_diario'); // Quadro 5: 5 XP, uma vez por dia

    // Quadro 5: marco de sequência, XP único por marco alcançado
    foreach (self::MARCOS_STREAK as $dias => $acao) {
        if ($usuarioComum->getStreakAtual() >= $dias) {
            $this->concederXp($usuarioId, $acao);
        }
    }
}
}