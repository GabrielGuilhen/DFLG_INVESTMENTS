<?php

namespace app\controllers;

use app\core\Controller;
use app\services\RelatorioService;
use DateTimeImmutable;

class RelatorioController extends Controller
{
    private RelatorioService $service;

    public function __construct()
    {
        $this->service = new RelatorioService();
    }

    public function index()
    {
        $this->autenticacaoRequired();
        $agora = new DateTimeImmutable('today');
        $ano = (int) ($_GET['ano'] ?? $agora->format('Y'));
        $mes = (int) ($_GET['mes'] ?? $agora->format('m'));
        $tipo = $_GET['tipo'] ?? 'mensal';

        $this->view('relatorios/relatorio', [
            'ano' => $ano,
            'mes' => $mes,
            'tipo' => $tipo,
            'relatorio' => $tipo === 'anual'
                ? $this->service->gerarAnual($this->getUsuarioLogadoId(), $ano)
                : $this->service->gerar($this->getUsuarioLogadoId(), $ano, $mes),
        ]);
    }
}