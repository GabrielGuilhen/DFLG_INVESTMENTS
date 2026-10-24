<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Parcelamento • DFLG Investments</title>

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
            <h2 class="mb-0">Novo Parcelamento</h2>
            <a href="<?= URL_BASE ?>/parcelamentos" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <form action="<?= URL_BASE ?>/parcelamentos/salvar" method="post">

                    <div class="row g-3">

                        <div class="col-md-8">
                            <label for="descricao" class="form-label">Descrição</label>
                            <input type="text" class="form-control" id="descricao" name="descricao"
                                value="<?= htmlspecialchars($parcelamento['descricao'] ?? '') ?>">
                            <?php if (isset($erros['descricao'])): ?>
                                <div class="text-danger small"><?= $erros['descricao'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="tag" class="form-label">Tag da compra</label>
                            <input type="text" class="form-control" id="tag" name="tag" maxlength="50"
                                value="<?= htmlspecialchars($parcelamento['tag'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="categoriaId" class="form-label">Categoria</label>
                            <select class="form-select" id="categoriaId" name="categoriaId">
                                <option value="">Sem categoria</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria->getId() ?>"
                                        <?= (isset($parcelamento['categoriaId']) && $parcelamento['categoriaId'] == $categoria->getId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria->getNome()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="valorParcela" class="form-label">Valor da parcela (R$)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="valorParcela" name="valorParcela"
                                value="<?= htmlspecialchars($parcelamento['valorParcela'] ?? '') ?>">
                            <?php if (isset($erros['valorParcela'])): ?>
                                <div class="text-danger small"><?= $erros['valorParcela'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="quantidadeParcelas" class="form-label">Qtd. de parcelas</label>
                            <input type="number" min="2" step="1" class="form-control" id="quantidadeParcelas" name="quantidadeParcelas"
                                value="<?= htmlspecialchars($parcelamento['quantidadeParcelas'] ?? '') ?>">
                            <?php if (isset($erros['quantidadeParcelas'])): ?>
                                <div class="text-danger small"><?= $erros['quantidadeParcelas'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="dataPrimeiraParcela" class="form-label">Data da 1ª parcela</label>
                            <input type="date" class="form-control" id="dataPrimeiraParcela" name="dataPrimeiraParcela"
                                value="<?= htmlspecialchars($parcelamento['dataPrimeiraParcela'] ?? date('Y-m-d')) ?>">
                            <?php if (isset($erros['dataPrimeiraParcela'])): ?>
                                <div class="text-danger small"><?= $erros['dataPrimeiraParcela'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="lancarFuturos" name="lancarFuturos" value="1" checked>
                                <label class="form-check-label" for="lancarFuturos">
                                    Lançar as parcelas futuras nas minhas despesas
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle"></i> Salvar
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
