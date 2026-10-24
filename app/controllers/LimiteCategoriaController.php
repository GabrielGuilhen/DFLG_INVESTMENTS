<?php

namespace app\controllers;

use app\core\Controller;
use app\services\LimiteCategoriaService;

class LimiteCategoriaController extends Controller
{
    private LimiteCategoriaService $service;

    public function __construct()
    {
        $this->service = new LimiteCategoriaService();
    }

    public function index()
    {
        $this->autenticacaoRequired();
        $this->view('limites/limites', ['limites' => $this->service->listar($this->getUsuarioLogadoId())]);
    }

    public function salvar()
    {
        $this->autenticacaoRequired();
        $this->service->salvar(
            $this->getUsuarioLogadoId(),
            (int) ($_POST['categoriaId'] ?? 0),
            (float) ($_POST['limite'] ?? 0)
        );
        $this->redirect(URL_BASE . '/limites');
    }
}