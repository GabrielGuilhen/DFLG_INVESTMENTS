<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - DFLG Investments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>
    <?php
    $periodo = $periodo ?? 'geral';
    $ranking = $ranking ?? [];
    ?>
    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-trophy me-2"></i>Ranking</h2>
            <div class="btn-group">
                <?php foreach (['semanal' => 'Semanal', 'mensal' => 'Mensal', 'geral' => 'Geral'] as $chave => $label): ?>
                    <a class="btn btn-outline-primary <?= $periodo === $chave ? 'active' : '' ?>" href="<?= URL_BASE ?>/ranking?periodo=<?= $chave ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>#</th><th>Usuario</th><th>Nivel</th><th>XP no periodo</th><th>Streak</th></tr></thead>
                    <tbody>
                    <?php foreach ($ranking as $posicao => $usuario): ?>
                        <tr><td><?= $posicao + 1 ?></td><td>@<?= htmlspecialchars($usuario['nickname']) ?></td><td><?= $usuario['nivel'] ?></td><td><?= $usuario['xp_periodo'] ?></td><td><?= $usuario['streak_atual'] ?> dias</td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($ranking)): ?><tr><td colspan="5" class="text-center text-muted">Ainda não há usuários visíveis no ranking.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>