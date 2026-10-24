<?php

namespace app\controllers;

use app\core\Controller;
use app\services\GamificacaoService;
use app\services\MetaService;
use app\services\TransacaoService;

class DashboardController extends Controller
{
    private GamificacaoService $gamificacaoService;
    private MetaService $metaService;
    private TransacaoService $transacaoService;

    public function __construct()
    {
        $this->gamificacaoService = new GamificacaoService();
        $this->metaService = new MetaService();
        $this->transacaoService = new TransacaoService();
    }

    public function index()
    {
        
        $this->autenticacaoRequired();

        $usuario = $_SESSION['usuario_logado'];

        // RF 09 / RN 03: registra o acesso do dia e atualiza a sequência (streak)
        $this->gamificacaoService->verificarEAtualizarStreak($usuario->getId());

        $data = [];
        $data['usuario'] = $usuario;
        $data['gamificacao'] = $this->gamificacaoService->getGamificacao($usuario->getId());
        $data['totalReceitas'] = $this->transacaoService->getTotalReceitas($usuario->getId());
        $data['totalDespesas'] = $this->transacaoService->getTotalDespesas($usuario->getId());
        $data['saldo'] = $this->transacaoService->getSaldo($usuario->getId());
        $data['metas'] = $this->metaService->getMetas($usuario->getId());
        $data['evolucao'] = $this->transacaoService->getEvolucaoMensal($usuario->getId());

        $this->view('dashboard/dashboard', $data);
    }
}