<?php

namespace app\services;

use app\models\Usuario;
use app\repositories\UsuarioRepository;
use app\repositories\UsuarioComumRepository;
use app\repositories\UsuarioAdministradorRepository;
use DateTimeImmutable;

class UsuarioService
{
    private const IDADE_MINIMA = 16;

    private UsuarioRepository $repository;
    private UsuarioComumRepository $usuarioComumRepository;
    private UsuarioAdministradorRepository $usuarioAdministradorRepository;
    private array $erros = [];

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
        $this->usuarioComumRepository = new UsuarioComumRepository();
        $this->usuarioAdministradorRepository = new UsuarioAdministradorRepository();
    }

    public function cadastrar(Usuario $usuario): bool
    {
        if (!$this->validar($usuario)) {
            return false;
        }

        if (!$this->repository->saveUsuario($usuario)) {
            return false;
        }

        $usuarioCriado = $this->repository->getUsuarioByEmail($usuario->getEmail());
        $this->usuarioComumRepository->criarParaUsuario($usuarioCriado->getId());

        return true;
    }

    public function cadastrarAdministrador(Usuario $usuario): bool
    {
        if (!$this->validar($usuario)) {
            return false;
        }

        if (!$this->repository->saveUsuario($usuario)) {
            return false;
        }

        $usuarioCriado = $this->repository->getUsuarioByEmail($usuario->getEmail());

        return $this->usuarioAdministradorRepository->criarParaUsuario($usuarioCriado->getId());
    }

    public function getUsuarioById(int $id)
    {
        return $this->repository->getUsuarioById($id);
    }

    public function getUsuarios(): array
    {
        return $this->repository->getUsuarios();
    }

    public function getIndicadores(): array
    {
        return [
            'total' => $this->repository->contarUsuarios(),
            'ativos' => $this->repository->contarUsuariosAtivos(),
            'administradores' => $this->repository->contarAdministradoresAtivos(),
        ];
    }

    public function bloquear(int $id): bool
    {
        return $this->repository->atualizarStatus($id, 'bloqueado');
    }

    public function desbloquear(int $id): bool
    {
        return $this->repository->atualizarStatus($id, 'ativo');
    }

    public function tornarAdministrador(int $id): bool
    {
        if (!$this->repository->getUsuarioById($id)) {
            return false;
        }

        if (!$this->repository->atualizarPerfil($id, 'administrador')) {
            return false;
        }

        return $this->usuarioAdministradorRepository->criarParaUsuario($id);
    }

    public function excluirAdministrativamente(int $id): bool
    {
        $usuario = $this->repository->getUsuarioById($id);

        if (!$usuario) {
            return false;
        }

        if ($usuario->eAdmin() && $usuario->getStatus() === 'ativo') {
            if ($this->repository->contarAdministradoresAtivos() <= 1) {
                $this->erros['usuario'] = 'O último administrador ativo não pode ser excluído';
                return false;
            }
        }

        return $this->repository->deleteUsuario($id);
    }

    public function atualizarAdministrativamente(Usuario $usuario): bool
    {
        $atual = $this->repository->getUsuarioById($usuario->getId());

        if (!$atual) {
            return false;
        }

        $vaiDeixarDeSerAdminAtivo = $atual->eAdmin()
            && $atual->getStatus() === 'ativo'
            && (!$usuario->eAdmin() || $usuario->getStatus() !== 'ativo');

        if ($vaiDeixarDeSerAdminAtivo && $this->repository->contarAdministradoresAtivos() <= 1) {
            $this->erros['usuario'] = 'O último administrador ativo não pode ser bloqueado ou rebaixado';
            return false;
        }

        return $this->updateUsuario($usuario);
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        if (!$this->validar($usuario)) {
            return false;
        }

        return $this->repository->updateUsuario($usuario);
    }

    public function updateFoto(int $id, ?string $foto): bool
    {
        return $this->repository->updateFoto($id, $foto);
    }

    public function deleteUsuario(int $id): bool
    {
        return $this->repository->deleteUsuario($id);
    }

    public function getErros(): array
    {
        return $this->erros;
    }

    private function validar(Usuario $usuario): bool
    {
        $this->erros = [];

        $nascimento = DateTimeImmutable::createFromFormat('Y-m-d', $usuario->getDataNascimento());

        if (!$nascimento) {
            $this->erros['dataNascimento'] = "Informe uma data de nascimento válida";
        } elseif ($nascimento > new DateTimeImmutable('today')) {
            $this->erros['dataNascimento'] = "A data de nascimento não pode ser no futuro";
        } elseif ($nascimento->diff(new DateTimeImmutable('today'))->y < self::IDADE_MINIMA) {
            $this->erros['dataNascimento'] = "É preciso ter pelo menos " . self::IDADE_MINIMA . " anos para se cadastrar";
        }

        $usuarioComEmail = $this->repository->getUsuarioByEmail($usuario->getEmail());

        // na edição, o e-mail do próprio usuário não conta como duplicado
        if ($usuarioComEmail && $usuarioComEmail->getId() !== $usuario->getId()) {
            $this->erros['email'] = "Este e-mail já está cadastrado";
        }

        $usuarioComNickname = $this->repository->getUsuarioByNickname($usuario->getNickname());

        if ($usuarioComNickname && $usuarioComNickname->getId() !== $usuario->getId()) {
            $this->erros['nickname'] = "Este nickname já está em uso";
        }

        return empty($this->erros);
    }
}