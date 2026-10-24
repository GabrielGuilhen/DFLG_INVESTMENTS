<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard • DFLG Investments</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS próprio -->
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>

<body>

    <?php require __DIR__ . '/../partials/header.php'; ?>

        <?php
        $usuario = $usuario ?? null;
        $gamificacao = $gamificacao ?? null;
        $totalReceitas = $totalReceitas ?? 0;
        $totalDespesas = $totalDespesas ?? 0;
        $saldo = $saldo ?? 0;
        $metas = $metas ?? [];
        $evolucao = $evolucao ?? [];
        ?>

    <div class="container">
            <h4 class="mb-1">Olá, <?= htmlspecialchars($usuario?->getNome() ?? '') ?></h4>
            <p class="text-muted">@<?= htmlspecialchars($usuario?->getNickname() ?? '') ?></p>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Nível</p>
                        <h3 class="mb-0"><?= $gamificacao ? $gamificacao->getNivel() : 1 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">XP Total</p>
                        <h3 class="mb-0"><?= $gamificacao ? $gamificacao->getXpTotal() : 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Sequência atual</p>
                        <h3 class="mb-0"><?= $gamificacao ? $gamificacao->getStreakAtual() : 0 ?> dias</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Receitas até hoje</p>
                        <h3 class="mb-0 text-success">R$ <?= number_format($totalReceitas, 2, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Despesas até hoje</p>
                        <h3 class="mb-0 text-danger">R$ <?= number_format($totalDespesas, 2, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Saldo atual</p>
                        <h3 class="mb-0 <?= $saldo >= 0 ? 'text-success' : 'text-danger' ?>">R$ <?= number_format($saldo, 2, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Evolução dos últimos meses</h5>
                        <?php if (empty($evolucao)): ?>
                            <p class="text-muted mb-0">Ainda não há movimentações para exibir.</p>
                        <?php else: ?>
                            <?php foreach ($evolucao as $mes): ?>
                                <?php $maiorValor = max((float) $mes['receitas'], (float) $mes['despesas'], 1); ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span><?= htmlspecialchars($mes['mes']) ?></span>
                                        <span>Saldo: R$ <?= number_format($mes['receitas'] - $mes['despesas'], 2, ',', '.') ?></span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: <?= ((float) $mes['receitas'] / $maiorValor) * 100 ?>%"></div>
                                        <div class="progress-bar bg-danger" style="width: <?= ((float) $mes['despesas'] / $maiorValor) * 100 ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Minhas metas</h5>
                        <?php if (empty($metas)): ?>
                            <p class="text-muted mb-0">Você ainda não cadastrou metas.</p>
                        <?php else: ?>
                            <?php foreach ($metas as $meta): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span><?= htmlspecialchars($meta->getNomeMeta()) ?></span>
                                        <span><?= $meta->getProgresso() ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: <?= $meta->getProgresso() ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <a href="<?= URL_BASE ?>/metas" class="text-decoration-none">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="bi bi-bullseye me-2 text-primary"></i>Metas</h5>
                            <p class="text-muted small mb-0">Cadastre e acompanhe seus objetivos financeiros.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= URL_BASE ?>/transacoes" class="text-decoration-none">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="bi bi-cash-stack me-2 text-success"></i>Finanças</h5>
                            <p class="text-muted small mb-0">Registre receitas e despesas e acompanhe seu saldo.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= URL_BASE ?>/parcelamentos" class="text-decoration-none">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="bi bi-credit-card-2-front me-2 text-danger"></i>Parcelamentos</h5>
                            <p class="text-muted small mb-0">Controle suas compras a prazo e o quanto falta pagar.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
