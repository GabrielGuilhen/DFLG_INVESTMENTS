<?php

namespace app\services;

use app\models\Transacao;
use app\repositories\TransacaoRepository;

class TransacaoService
{
    private TransacaoRepository $repository;
    private CategoriaService $categoriaService;
    private array $erros = [];

    public function __construct()
    {
        $this->repository = new TransacaoRepository();
        $this->categoriaService = new CategoriaService();
    }

    public function getTransacoes(int $usuarioId, ?string $tipo = null, ?int $categoriaId = null): array
    {
        return $this->repository->getTransacoesPorUsuario($usuarioId, $tipo, $categoriaId);
    }

    public function getTransacao(int $id, int $usuarioId)
    {
        return $this->repository->getTransacao($id, $usuarioId);
    }

    public function saveTransacao(Transacao $transacao): bool
    {
        if (!$this->validar($transacao)) {
            return false;
        }

        return $this->repository->saveTransacao($transacao);
    }

    public function updateTransacao(Transacao $transacao): bool
    {
        if (!$this->repository->getTransacao($transacao->getId(), $transacao->getUsuarioId())) {
            $this->erros['transacao'] = "Transação não encontrada";

            return false;
        }

        if (!$this->validar($transacao)) {
            return false;
        }

        return $this->repository->updateTransacao($transacao);
    }

    public function deleteTransacao(int $id, int $usuarioId): bool
    {
        return $this->repository->deleteTransacao($id, $usuarioId);
    }

    public function getTotalReceitas(int $usuarioId): float
    {
        return $this->repository->getTotalPorTipo($usuarioId, 'receita');
    }

    public function getTotalDespesas(int $usuarioId): float
    {
        return $this->repository->getTotalPorTipo($usuarioId, 'despesa');
    }

    public function getSaldo(int $usuarioId): float
    {
        return $this->getTotalReceitas($usuarioId) - $this->getTotalDespesas($usuarioId);
    }

    public function getEvolucaoMensal(int $usuarioId, int $meses = 6): array
    {
        return $this->repository->getEvolucaoMensal($usuarioId, $meses);
    }

    public function getErros(): array
    {
        return $this->erros;
    }

    private function validar(Transacao $transacao): bool
    {
        $this->erros = [];

        if ($transacao->getValor() <= 0) {
            $this->erros['valor'] = "O valor deve ser maior que zero";
        }

        if (!in_array($transacao->getTipo(), ['receita', 'despesa'])) {
            $this->erros['tipo'] = "Selecione um tipo válido (receita ou despesa)";
        }

        $categoria = $transacao->getCategoriaId() ? $this->categoriaService->getCategoria($transacao->getCategoriaId()) : false;

        if (!$categoria) {
            $this->erros['categoriaId'] = "Selecione uma categoria válida";
        } elseif ($categoria->getTipo() !== $transacao->getTipo()) {
            $this->erros['categoriaId'] = "Essa categoria é de {$categoria->getTipo()}, não de {$transacao->getTipo()}";
        }

        if (!$transacao->getDataRegistro()) {
            $this->erros['dataRegistro'] = "Informe a data do registro";
        }

        return empty($this->erros);
    }
}