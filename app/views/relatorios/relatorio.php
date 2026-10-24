<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorios - DFLG Investments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>
<?php
$ano = $ano ?? (int) date('Y');
$mes = $mes ?? (int) date('m');
$tipo = $tipo ?? 'mensal';
$relatorio = $relatorio ?? ['receitas' => 0, 'despesas' => 0, 'saldo' => 0, 'transacoes' => []];
?>
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4"><h2>Relatorio <?= $tipo === 'anual' ? 'anual' : 'mensal' ?></h2><a href="<?= URL_BASE ?>/dashboard" class="btn btn-outline-secondary">Voltar</a></div>
    <form method="get" action="<?= URL_BASE ?>/relatorios" class="row g-2 mb-4">
        <div class="col-md-4"><label class="form-label" for="tipo">Periodo</label><select class="form-select" id="tipo" name="tipo"><option value="mensal" <?= $tipo === 'mensal' ? 'selected' : '' ?>>Mensal</option><option value="anual" <?= $tipo === 'anual' ? 'selected' : '' ?>>Anual</option></select></div>
        <div class="col-md-4"><label class="form-label" for="mes">Mes</label><select class="form-select" id="mes" name="mes"><?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>" <?= $mes === $i ? 'selected' : '' ?>><?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?></option><?php endfor; ?></select></div>
        <div class="col-md-4"><label class="form-label" for="ano">Ano</label><input class="form-control" type="number" id="ano" name="ano" value="<?= $ano ?>" min="2000" max="2100"></div>
        <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Gerar relatorio</button></div>
    </form>
    <div class="row g-3 mb-4"><div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small>Receitas</small><h3 class="text-success">R$ <?= number_format($relatorio['receitas'], 2, ',', '.') ?></h3></div></div></div><div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small>Despesas</small><h3 class="text-danger">R$ <?= number_format($relatorio['despesas'], 2, ',', '.') ?></h3></div></div></div><div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small>Saldo</small><h3>R$ <?= number_format($relatorio['saldo'], 2, ',', '.') ?></h3></div></div></div></div>
    <div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Data</th><th>Descricao</th><th>Categoria</th><th>Tipo</th><th>Valor</th></tr></thead><tbody><?php foreach ($relatorio['transacoes'] as $transacao): ?><tr><td><?= date('d/m/Y', strtotime($transacao->getDataRegistro())) ?></td><td><?= htmlspecialchars($transacao->getDescricao() ?? '-') ?></td><td><?= htmlspecialchars($transacao->getNomeCategoria() ?? 'Sem categoria') ?></td><td><?= $transacao->eReceita() ? 'Receita' : 'Despesa' ?></td><td>R$ <?= number_format($transacao->getValor(), 2, ',', '.') ?></td></tr><?php endforeach; ?><?php if (empty($relatorio['transacoes'])): ?><tr><td colspan="5" class="text-center text-muted">Nenhuma movimentacao no periodo.</td></tr><?php endif; ?></tbody></table></div></div>
</div>
</body>
</html>