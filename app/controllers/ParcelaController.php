<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Parcelamento;
use app\services\CategoriaService;
use app\services\ParcelamentoService;

class ParcelaController extends Controller
{
    private ParcelamentoService $service;
    private CategoriaService $categoriaService;

    public function __construct()
    {
        $this->service = new ParcelamentoService();
        $this->categoriaService = new CategoriaService();
    }

    public function listarTodos()
    {
        $this->autenticacaoRequired();

        $data = [];
        $data['lista'] = $this->service->getParcelamentos($this->getUsuarioLogadoId());

        $this->view('parcelamentos/parcelamentos_list', $data);
    }

    public function criar()
    {
        $this->autenticacaoRequired();

        $data = [];
        $data['categorias'] = $this->categoriaService->getCategorias('despesa');

        $this->view('parcelamentos/parcelamentos_create', $data);
    }

    public function salvar()
    {
        $this->autenticacaoRequired();

        $data = [];
        $validador = new Validador();

        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
        $tag = filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_SPECIAL_CHARS);
        $valorParcela = $_POST['valorParcela'] ?? '';
        $quantidadeParcelas = $_POST['quantidadeParcelas'] ?? '';
        $dataPrimeiraParcela = $_POST['dataPrimeiraParcela'] ?? '';
        $categoriaId = $_POST['categoriaId'] ?? '';
        $lancarFuturos = isset($_POST['lancarFuturos']);

        $validador->obrigatorio('descricao', $descricao);
        $validador->obrigatorio('valorParcela', $valorParcela);
        $validador->obrigatorio('quantidadeParcelas', $quantidadeParcelas);
        $validador->obrigatorio('dataPrimeiraParcela', $dataPrimeiraParcela);

        if ($validador->temErros()) {

            $data['parcelamento'] = $_POST;
            $data['erros'] = $validador->getErros();
            $data['categorias'] = $this->categoriaService->getCategorias('despesa');

            $this->view('parcelamentos/parcelamentos_create', $data);

            return;
        }

        $parcelamento = new Parcelamento(
            0,
            $this->getUsuarioLogadoId(),
            $categoriaId !== '' ? (int) $categoriaId : null,
            $descricao,
            (float) $valorParcela,
            (int) $quantidadeParcelas,
            0,
            $dataPrimeiraParcela,
            null,
            $tag,
            $lancarFuturos
        );

        if ($this->service->saveParcelamento($parcelamento)) {
            $this->redirect(URL_BASE . '/parcelamentos');
        } else {

            $data['parcelamento'] = $_POST;
            $data['erros'] = $this->service->getErros();
            $data['categorias'] = $this->categoriaService->getCategorias('despesa');

            $this->view('parcelamentos/parcelamentos_create', $data);
        }
    }

    public function editar()
    {
        $this->autenticacaoRequired();

        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/parcelamentos');
        }

        $parcelamento = $this->service->getParcelamento((int) $_GET['id'], $this->getUsuarioLogadoId());

        if (!$parcelamento) {
            $this->redirect(URL_BASE . '/parcelamentos');
        }

        $data = [];
        $data['parcelamento'] = $parcelamento;
        $data['categorias'] = $this->categoriaService->getCategorias('despesa');

        $this->view('parcelamentos/parcelamentos_edit', $data);
    }

    public function atualizar()
    {
        $this->autenticacaoRequired();

        $data = [];
        $validador = new Validador();

        $id = $_POST['id'] ?? 0;
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
        $tag = filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_SPECIAL_CHARS);
        $valorParcela = $_POST['valorParcela'] ?? '';
        $quantidadeParcelas = $_POST['quantidadeParcelas'] ?? '';
        $dataPrimeiraParcela = $_POST['dataPrimeiraParcela'] ?? '';
        $categoriaId = $_POST['categoriaId'] ?? '';
        $lancarFuturos = isset($_POST['lancarFuturos']);

        $validador->obrigatorio('descricao', $descricao);
        $validador->obrigatorio('valorParcela', $valorParcela);
        $validador->obrigatorio('quantidadeParcelas', $quantidadeParcelas);
        $validador->obrigatorio('dataPrimeiraParcela', $dataPrimeiraParcela);

        $parcelamento = new Parcelamento(
            (int) $id,
            $this->getUsuarioLogadoId(),
            $categoriaId !== '' ? (int) $categoriaId : null,
            $descricao,
            (float) $valorParcela,
            (int) $quantidadeParcelas,
            0,
            $dataPrimeiraParcela,
            null,
            $tag,
            $lancarFuturos
        );

        if ($validador->temErros()) {

            $data['parcelamento'] = $parcelamento;
            $data['erros'] = $validador->getErros();
            $data['categorias'] = $this->categoriaService->getCategorias('despesa');

            $this->view('parcelamentos/parcelamentos_edit', $data);

            return;
        }

        if ($this->service->updateParcelamento($parcelamento)) {
            $this->redirect(URL_BASE . '/parcelamentos');
        } else {

            $data['parcelamento'] = $parcelamento;
            $data['erros'] = $this->service->getErros();
            $data['categorias'] = $this->categoriaService->getCategorias('despesa');

            $this->view('parcelamentos/parcelamentos_edit', $data);
        }
    }

    public function excluir()
    {
        $this->autenticacaoRequired();

        if (isset($_POST['id'])) {
            $this->service->deleteParcelamento((int) $_POST['id'], $this->getUsuarioLogadoId());
        }

        $this->redirect(URL_BASE . '/parcelamentos');
    }
}
