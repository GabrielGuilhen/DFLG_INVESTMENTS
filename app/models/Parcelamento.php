<?php

namespace app\models;

class Parcelamento
{
    private int $id;
    private int $usuarioId;
    private ?int $categoriaId;
    private string $descricao;
    private float $valorParcela;
    private int $quantidadeParcelas;
    private int $parcelasPagas;
    private string $dataPrimeiraParcela;
    private ?string $nomeCategoria;
    private ?string $tag;
    private bool $lancarFuturos;

    public function __construct(
        int $id,
        int $usuarioId,
        ?int $categoriaId,
        string $descricao,
        float $valorParcela,
        int $quantidadeParcelas,
        int $parcelasPagas = 0,
        string $dataPrimeiraParcela = '',
        ?string $nomeCategoria = null,
        ?string $tag = null,
        bool $lancarFuturos = true
    ) {
        $this->id = $id;
        $this->usuarioId = $usuarioId;
        $this->categoriaId = $categoriaId;
        $this->descricao = $descricao;
        $this->valorParcela = $valorParcela;
        $this->quantidadeParcelas = $quantidadeParcelas;
        $this->parcelasPagas = $parcelasPagas;
        $this->dataPrimeiraParcela = $dataPrimeiraParcela;
        $this->nomeCategoria = $nomeCategoria;
        $this->tag = $tag;
        $this->lancarFuturos = $lancarFuturos;
    }

    public function getId(): int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuarioId; }
    public function getCategoriaId(): ?int { return $this->categoriaId; }
    public function getDescricao(): string { return $this->descricao; }
    public function getValorParcela(): float { return $this->valorParcela; }
    public function getValorTotal(): float { return round($this->valorParcela * $this->quantidadeParcelas, 2); }
    public function getQuantidadeParcelas(): int { return $this->quantidadeParcelas; }
    public function getParcelasPagas(): int { return $this->parcelasPagas; }
    public function getDataPrimeiraParcela(): string { return $this->dataPrimeiraParcela; }
    public function getNomeCategoria(): ?string { return $this->nomeCategoria; }
    public function getTag(): ?string { return $this->tag; }
    public function getLancarFuturos(): bool { return $this->lancarFuturos; }

    public function getValorRestante(): float
    {
        return round($this->getValorParcela() * max($this->quantidadeParcelas - $this->parcelasPagas, 0), 2);
    }

    public function getProgresso(): float
    {
        if ($this->quantidadeParcelas <= 0) {
            return 0.0;
        }

        return min(100.0, round(($this->parcelasPagas / $this->quantidadeParcelas) * 100, 2));
    }

    public function eQuitado(): bool
    {
        return $this->parcelasPagas >= $this->quantidadeParcelas;
    }

    public function getDataProximaParcela(): ?string
    {
        if ($this->eQuitado()) {
            return null;
        }

        $data = new \DateTimeImmutable($this->dataPrimeiraParcela);

        return $data->modify("+{$this->parcelasPagas} months")->format('Y-m-d');
    }

    public static function arrayParaObjeto(array $parcelamento): self
    {
        $valorParcela = $parcelamento['valor_parcela'] ?? null;
        if ($valorParcela === null) {
            $valorParcela = (float) $parcelamento['valor_total'] / max((int) $parcelamento['quantidade_parcelas'], 1);
        }

        return new self(
            (int) $parcelamento['id'],
            (int) $parcelamento['usuario_id'],
            $parcelamento['categoria_id'] !== null ? (int) $parcelamento['categoria_id'] : null,
            $parcelamento['descricao'],
            (float) $valorParcela,
            (int) $parcelamento['quantidade_parcelas'],
            (int) ($parcelamento['parcelas_pagas'] ?? 0),
            $parcelamento['data_primeira_parcela'],
            $parcelamento['nome_categoria'] ?? null,
            $parcelamento['tag'] ?? null,
            (bool) ($parcelamento['lancar_futuros'] ?? true)
        );
    }
}
