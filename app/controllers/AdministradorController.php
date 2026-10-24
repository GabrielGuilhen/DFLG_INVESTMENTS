<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Usuario;
use app\services\UsuarioService;

class AdministradorController extends Controller
{
    private UsuarioService $usuarioService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
    }

    public function usuarios()
    {
        $this->adminRequired();

        $this->view('administracao/usuarios', [
            'usuarios' => $this->usuarioService->getUsuarios(),
            'indicadores' => $this->usuarioService->getIndicadores(),
        ]);
    }

    public function bloquear()
    {
        $this->adminRequired();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id !== $this->getUsuarioLogadoId()) {
            $this->usuarioService->bloquear($id);
        }

        $this->redirect(URL_BASE . '/admin/usuarios');
    }

    public function desbloquear()
    {
        $this->adminRequired();
        $this->usuarioService->desbloquear((int) ($_POST['id'] ?? 0));

        $this->redirect(URL_BASE . '/admin/usuarios');
    }

    public function promover()
    {
        $this->adminRequired();
        $this->usuarioService->tornarAdministrador((int) ($_POST['id'] ?? 0));

        $this->redirect(URL_BASE . '/admin/usuarios');
    }

    public function excluir()
    {
        $this->adminRequired();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id !== $this->getUsuarioLogadoId()) {
            $this->usuarioService->excluirAdministrativamente($id);
        }

        $this->redirect(URL_BASE . '/admin/usuarios');
    }

    public function criarAdministrador()
    {
        $this->adminRequired();
        $this->view('administracao/administrador_create');
    }

    public function editar()
    {
        $this->adminRequired();
        $usuario = $this->usuarioService->getUsuarioById((int) ($_GET['id'] ?? 0));

        if (!$usuario) {
            $this->redirect(URL_BASE . '/admin/usuarios');
        }

        $this->view('administracao/usuario_edit', ['usuario' => $usuario]);
    }

    public function atualizar()
    {
        $this->adminRequired();
        $id = (int) ($_POST['id'] ?? 0);
        $atual = $this->usuarioService->getUsuarioById($id);

        if (!$atual) {
            $this->redirect(URL_BASE . '/admin/usuarios');
        }

        $usuario = new Usuario(
            $id,
            filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS),
            filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS),
            $_POST['email'] ?? '',
            $atual->getSenha(),
            $_POST['dataNascimento'] ?? '',
            $_POST['perfil'] ?? $atual->getPerfil(),
            $_POST['status'] ?? $atual->getStatus(),
            $atual->getFoto()
        );

        if ($this->usuarioService->atualizarAdministrativamente($usuario)) {
            $this->redirect(URL_BASE . '/admin/usuarios');
        }

        $this->view('administracao/usuario_edit', [
            'usuario' => $usuario,
            'erros' => $this->usuarioService->getErros(),
        ]);
    }

    public function salvarAdministrador()
    {
        $this->adminRequired();

        $data = [];
        $validador = new Validador();
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $dataNascimento = $_POST['dataNascimento'] ?? '';

        $validador->obrigatorio('nome', $nome);
        $validador->obrigatorio('nickname', $nickname);
        $validador->obrigatorio('email', $email);
        $validador->obrigatorio('senha', $senha);
        $validador->obrigatorio('dataNascimento', $dataNascimento);

        $usuario = new Usuario(0, (string) $nome, (string) $nickname, $email, $senha, $dataNascimento, 'administrador');

        if (!$validador->temErros() && $this->usuarioService->cadastrarAdministrador($usuario)) {
            $this->redirect(URL_BASE . '/admin/usuarios');
        }

        $data['usuario'] = $_POST;
        $data['erros'] = $validador->getErros() + $this->usuarioService->getErros();
        $this->view('administracao/administrador_create', $data);
    }
}