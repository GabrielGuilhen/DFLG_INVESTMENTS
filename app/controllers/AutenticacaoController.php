<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Usuario;
use app\services\AutenticacaoService;
use app\services\UsuarioService;

class AutenticacaoController extends Controller
{

    private AutenticacaoService $autenticacaoService;
    private UsuarioService $usuarioService;

    public function __construct()
    {
        $this->autenticacaoService = new AutenticacaoService();
        $this->usuarioService = new UsuarioService();
    }

    public function login()
    {

        $this->view('autenticacao/login');
    }

    public function logar()
    {
        $dados = [];

        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $resultado = $this->autenticacaoService->logar($email, $senha);

        if ($resultado) {
            $this->redirect(URL_BASE . '/dashboard');
        } else {

            $dados['erros'] = "E-mail ou senha inválidos";

            $this->view('autenticacao/login', $dados);
        }
    }

    public function logout()
    {
        $this->autenticacaoService->logout();
        $this->redirect(URL_BASE . '/login');
    }

    public function cadastrar()
    {
        $this->view('autenticacao/cadastro');
    }

    public function salvar()
    {
        $data = [];
        $validador = new Validador();

        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $dataNascimento = $_POST['dataNascimento'];

        $validador->obrigatorio('nome', $nome);
        $validador->obrigatorio('nickname', $nickname);
        $validador->obrigatorio('email', $email);
        $validador->obrigatorio('senha', $senha);
        $validador->obrigatorio('dataNascimento', $dataNascimento);

        if ($validador->temErros()) {

            $data['usuario'] = $_POST;
            $data['erros'] = $validador->getErros();

            $this->view('autenticacao/cadastro', $data);

            return;
        }

        // cadastro público sempre cria usuário comum, nunca administrador
        $usuario = new Usuario(0, $nome, $nickname, $email, $senha, $dataNascimento, 'comum');

        if ($this->usuarioService->cadastrar($usuario)) {
            $this->redirect(URL_BASE . '/login');
        } else {

            $data['usuario'] = $_POST;
            $data['erros'] = $this->usuarioService->getErros();

            $this->view('autenticacao/cadastro', $data);
        }
    }
}
