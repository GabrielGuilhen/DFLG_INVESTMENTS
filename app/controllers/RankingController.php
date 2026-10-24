<?php

namespace app\controllers;

use app\core\Controller;
use app\services\RankingService;

class RankingController extends Controller
{
    private RankingService $service;

    public function __construct()
    {
        $this->service = new RankingService();
    }

    public function index()
    {
        $this->autenticacaoRequired();
        $periodo = $_GET['periodo'] ?? 'geral';

        $this->view('ranking/ranking', [
            'ranking' => $this->service->getRanking($periodo),
            'periodo' => $periodo,
        ]);
    }
}