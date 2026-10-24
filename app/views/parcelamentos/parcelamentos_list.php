<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcelamentos • DFLG Investments</title>

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
            <h2 class="mb-0"><i class="bi bi-credit-card-2-front me-2"></i>Meus Parcelamentos</h2>
            <a href="<?= URL_BASE ?>/parcelamentos/cadastrar" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Parcelamento
            </a>
        </div>

        <?php if (empty($lista)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Você ainda não cadastrou nenhum parcelamento.
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Descrição</th>
                                    <th class="px-4 py-3">Categoria</th>
                                    <th class="px-4 py-3 text-end">Valor total</th>
                                    <th class="px-4 py-3 text-end">Parcela</th>
                                    <th class="px-4 py-3">Progresso</th>
                                    <th class="px-4 py-3 text-end">Falta pagar</th>
                                    <th class="px-4 py-3 text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista as $parcelamento): ?>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <?php if ($parcelamento->getTag()): ?>
                                                <span class="badge bg-secondary me-1"><?= htmlspecialchars($parcelamento->getTag()) ?></span>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($parcelamento->getDescricao()) ?>
                                            <?php if ($parcelamento->eQuitado()): ?>
                                                <span class="badge bg-success ms-1">Quitado</span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php if ($parcelamento->eQuitado()): ?>
                                                    Todas as parcelas pagas
                                                <?php else: ?>
                                                    Próxima em <?= date('d/m/Y', strtotime($parcelamento->getDataProximaParcela())) ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($parcelamento->getNomeCategoria() ?? 'Sem categoria') ?></td>
                                        <td class="px-4 py-3 text-end">R$ <?= number_format($parcelamento->getValorTotal(), 2, ',', '.') ?></td>
                                        <td class="px-4 py-3 text-end">
                                            <?= $parcelamento->getQuantidadeParcelas() ?>x
                                            R$ <?= number_format($parcelamento->getValorParcela(), 2, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-3" style="min-width: 180px;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $parcelamento->getProgresso() ?>%"></div>
                                            </div>
                                            <small class="text-muted">
                                                <?= $parcelamento->getParcelasPagas() ?> de <?= $parcelamento->getQuantidadeParcelas() ?> pagas
                                            </small>
                                        </td>
                                        <td class="px-4 py-3 text-end fw-bold">R$ <?= number_format($parcelamento->getValorRestante(), 2, ',', '.') ?></td>
                                        <td class="px-4 py-3 text-end">
                                            <a href="<?= URL_BASE ?>/parcelamentos/editar?id=<?= $parcelamento->getId() ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <form method="POST" action="<?= URL_BASE ?>/parcelamentos/excluir" class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir este parcelamento?')">
                                                <input type="hidden" name="id" value="<?= $parcelamento->getId() ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
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
