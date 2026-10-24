<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas • DFLG Investments</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS próprio -->
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>

<body>

    <?php require __DIR__ . '/../partials/header.php'; ?>

    <div class="container pb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-bullseye me-2"></i>Minhas Metas</h2>
            <a href="<?= URL_BASE ?>/metas/cadastrar" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Meta
            </a>
        </div>

        <?php if (empty($lista)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Você ainda não cadastrou nenhuma meta. Que tal começar agora?
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Meta</th>
                                    <th class="px-4 py-3">Tipo</th>
                                    <th class="px-4 py-3">Progresso</th>
                                    <th class="px-4 py-3">Data limite</th>
                                    <th class="px-4 py-3 text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista as $meta): ?>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <?= htmlspecialchars($meta->getNomeMeta()) ?>
                                            <?php if ($meta->eConcluida()): ?>
                                                <span class="badge bg-success ms-1">Concluída</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-secondary text-capitalize"><?= htmlspecialchars($meta->getTipoMeta()) ?></span>
                                        </td>
                                        <td class="px-4 py-3" style="min-width: 220px;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $meta->getProgresso() ?>%"></div>
                                            </div>
                                            <small class="text-muted">
                                                R$ <?= number_format($meta->getValorAtual(), 2, ',', '.') ?>
                                                de R$ <?= number_format($meta->getValorAlvo(), 2, ',', '.') ?>
                                                (<?= $meta->getProgresso() ?>%)
                                            </small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?= date('d/m/Y', strtotime($meta->getDataLimite())) ?>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <a href="<?= URL_BASE ?>/metas/editar?id=<?= $meta->getId() ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <form method="POST" action="<?= URL_BASE ?>/metas/excluir" class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir esta meta?')">
                                                <input type="hidden" name="id" value="<?= $meta->getId() ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
                                            <?php if (!$meta->eConcluida()): ?>
                                                <form method="POST" action="<?= URL_BASE ?>/metas/progredir" class="d-inline-flex gap-1 mt-1">
                                                    <input type="hidden" name="id" value="<?= $meta->getId() ?>">
                                                    <input type="number" name="valor" min="0.01" step="0.01" class="form-control form-control-sm" placeholder="Valor" required style="max-width: 90px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Adicionar progresso"><i class="bi bi-plus-circle"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
