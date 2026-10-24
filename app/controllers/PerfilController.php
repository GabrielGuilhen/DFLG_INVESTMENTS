<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\UploadImagem;
use app\helpers\Validador;
use app\models\Usuario;
use app\services\GamificacaoService;
use app\services\UsuarioService;

class PerfilController extends Controller
{
    private UsuarioService $usuarioService;
    private GamificacaoService $gamificacaoService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->gamificacaoService = new GamificacaoService();
    }

    public function visualizar()
    {
        $this->autenticacaoRequired();

        $usuarioId = $this->getUsuarioLogadoId();

        $data = [];
        $data['usuario'] = $this->usuarioService->getUsuarioById($usuarioId);
        $data['gamificacao'] = $this->gamificacaoService->getGamificacao($usuarioId);
        $data['progresso'] = $data['gamificacao']
            ? $this->gamificacaoService->getProgressoNivel($data['gamificacao']->getXpTotal())
            : null;

        $this->view('perfil/perfil_view', $data);
    }

    public function editar()
    {
        $this->autenticacaoRequired();

        $data = [];
        $data['usuario'] = $this->usuarioService->getUsuarioById($this->getUsuarioLogadoId());

        $this->view('perfil/perfil_edit', $data);
    }

    public function excluir()
    {
        $this->autenticacaoRequired();

        $data = [];
        $data['usuario'] = $this->usuarioService->getUsuarioById($this->getUsuarioLogadoId());

        $this->view('perfil/perfil_excluir', $data);
    }

    public function excluirConta()
    {
        $this->autenticacaoRequired();

        $usuarioId = $this->getUsuarioLogadoId();
        $usuario = $this->usuarioService->getUsuarioById($usuarioId);

        if (!$usuario || !$this->usuarioService->deleteUsuario($usuarioId)) {
            $data['usuario'] = $usuario;
            $data['erro'] = 'Não foi possível excluir seu perfil.';

            $this->view('perfil/perfil_excluir', $data);

            return;
        }

        if ($usuario->getFoto()) {
            $upload = new UploadImagem(__DIR__ . '/../../public/assets/uploads/perfil');
            $upload->remover($usuario->getFoto());
        }

        session_unset();
        session_destroy();

        $this->redirect(URL_BASE . '/login');
    }

    public function atualizar()
    {
        $this->autenticacaoRequired();

        $data = [];
        $validador = new Validador();
        $upload = new UploadImagem(__DIR__ . '/../../public/assets/uploads/perfil');
        $usuarioAtual = $this->usuarioService->getUsuarioById($this->getUsuarioLogadoId());

        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $_POST['email'] ?? '';
        $dataNascimento = $_POST['dataNascimento'] ?? '';
        $foto = $_FILES['foto'] ?? null;

        $validador->obrigatorio('nome', $nome);
        $validador->obrigatorio('nickname', $nickname);
        $validador->obrigatorio('email', $email);
        $validador->obrigatorio('dataNascimento', $dataNascimento);

        $erros = $validador->getErros();

        if ($upload->enviado($foto) && !$upload->validar($foto)) {
            $erros['foto'] = $upload->getErro();
        }

        $usuario = new Usuario(
            $usuarioAtual->getId(),
            (string) $nome,
            (string) $nickname,
            $email,
            $usuarioAtual->getSenha(),
            $dataNascimento,
            $usuarioAtual->getPerfil(),
            $usuarioAtual->getStatus(),
            $usuarioAtual->getFoto()
        );

        if (!empty($erros)) {

            $data['usuario'] = $usuario;
            $data['erros'] = $erros;

            $this->view('perfil/perfil_edit', $data);

            return;
        }

        if (!$this->usuarioService->updateUsuario($usuario)) {

            $data['usuario'] = $usuario;
            $data['erros'] = $this->usuarioService->getErros();

            $this->view('perfil/perfil_edit', $data);

            return;
        }

        if ($upload->enviado($foto)) {
            $nomeFoto = $upload->salvar($foto, 'usuario_' . $usuarioAtual->getId());

            if (!$nomeFoto) {
                $this->atualizarSessao($usuarioAtual->getId());

                $data['usuario'] = $usuario;
                $data['erros'] = ['foto' => 'Seus dados foram salvos, mas a foto não: ' . $upload->getErro()];

                $this->view('perfil/perfil_edit', $data);

                return;
            }

            $this->usuarioService->updateFoto($usuarioAtual->getId(), $nomeFoto);
            $upload->remover($usuarioAtual->getFoto());
        }

        $this->atualizarSessao($usuarioAtual->getId());

        // Quadro 5: perfil completo (foto e dados pessoais) dá XP uma única vez
        if ($_SESSION['usuario_logado']->getFoto()) {
            $this->gamificacaoService->concederXp($usuarioAtual->getId(), 'perfil_completo');
        }

        $this->redirect(URL_BASE . '/perfil');
    }

    public function atualizarPreferencias()
    {
        $this->autenticacaoRequired();

        $visibilidade = isset($_POST['visibilidadeRanking']);

        $this->gamificacaoService->atualizarVisibilidade($this->getUsuarioLogadoId(), $visibilidade);

        $this->redirect(URL_BASE . '/perfil');
    }

    // atualiza o usuário guardado na sessão com os dados novos do banco
    private function atualizarSessao(int $usuarioId)
    {
        $_SESSION['usuario_logado'] = $this->usuarioService->getUsuarioById($usuarioId);
    }
}