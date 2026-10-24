<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações • DFLG Investments</title>

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
            <h2 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Minhas Finanças</h2>
            <a href="<?= URL_BASE ?>/transacoes/cadastrar" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Registro
            </a>
        </div>

        <!-- RESUMO -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total de receitas</p>
                        <h4 class="mb-0 text-success">R$ <?= number_format($totalReceitas, 2, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total de despesas</p>
                        <h4 class="mb-0 text-danger">R$ <?= number_format($totalDespesas, 2, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Saldo</p>
                        <h4 class="mb-0 <?= $saldo >= 0 ? 'text-success' : 'text-danger' ?>">R$ <?= number_format($saldo, 2, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="<?= URL_BASE ?>/transacoes" method="get" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="tipo" class="form-label small fw-bold">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="receita" <?= $filtroTipo == 'receita' ? 'selected' : '' ?>>Receitas</option>
                            <option value="despesa" <?= $filtroTipo == 'despesa' ? 'selected' : '' ?>>Despesas</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="categoriaId" class="form-label small fw-bold">Categoria</label>
                        <select class="form-select" id="categoriaId" name="categoriaId">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria->getId() ?>" <?= $filtroCategoria == $categoria->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria->getNome()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary flex-fill">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="<?= URL_BASE ?>/transacoes" class="btn btn-outline-secondary">Limpar</a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($lista)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Nenhum registro financeiro encontrado.
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Data</th>
                                    <th class="px-4 py-3">Descrição</th>
                                    <th class="px-4 py-3">Categoria</th>
                                    <th class="px-4 py-3">Tipo</th>
                                    <th class="px-4 py-3 text-end">Valor</th>
                                    <th class="px-4 py-3 text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista as $transacao): ?>
                                    <tr>
                                        <td class="px-4 py-3"><?= date('d/m/Y', strtotime($transacao->getDataRegistro())) ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($transacao->getDescricao() ?? '-') ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($transacao->getNomeCategoria() ?? 'Sem categoria') ?></td>
                                        <td class="px-4 py-3">
                                            <span class="badge <?= $transacao->eReceita() ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $transacao->eReceita() ? 'Receita' : 'Despesa' ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-end fw-bold <?= $transacao->eReceita() ? 'text-success' : 'text-danger' ?>">
                                            <?= $transacao->eReceita() ? '+' : '-' ?> R$ <?= number_format($transacao->getValor(), 2, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <a href="<?= URL_BASE ?>/transacoes/editar?id=<?= $transacao->getId() ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <form method="POST" action="<?= URL_BASE ?>/transacoes/excluir" class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir este registro?')">
                                                <input type="hidden" name="id" value="<?= $transacao->getId() ?>">
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
