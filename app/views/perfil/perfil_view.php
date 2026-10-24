<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil • DFLG Investments</title>

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
            <h2 class="mb-0">Meu Perfil</h2>
            <a href="<?= URL_BASE ?>/perfil/editar" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar perfil
            </a>
            <a href="<?= URL_BASE ?>/perfil/excluir" class="btn btn-danger">
                <i class="bi bi-pencil"></i> Excluir Conta
            </a>
        </div>

        <div class="row g-3">

            <div class="col-md-5">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">

                        <?php if ($usuario->getFoto()): ?>
                            <img src="<?= URL_BASE ?>/assets/uploads/perfil/<?= htmlspecialchars($usuario->getFoto()) ?>"
                                alt="Foto de perfil" class="rounded-circle object-fit-cover mb-3" width="120" height="120">
                        <?php else: ?>
                            <i class="bi bi-person-circle display-1 text-secondary d-block mb-2"></i>
                        <?php endif; ?>

                        <h4 class="mb-0"><?= htmlspecialchars($usuario->getNome()) ?></h4>
                        <p class="text-muted">@<?= htmlspecialchars($usuario->getNickname()) ?></p>

                        <p class="mb-1"><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($usuario->getEmail()) ?></p>
                        <p class="mb-0"><i class="bi bi-calendar-event me-2"></i>Nascimento:
                            <?= date('d/m/Y', strtotime($usuario->getDataNascimento())) ?></p>

                    </div>
                </div>
            </div>

            <?php if ($gamificacao): ?>
                <div class="col-md-7">

                    <div class="card shadow-sm mb-3">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Progresso</h5>

                            <div class="d-flex justify-content-between">
                                <span>Nível <strong><?= $gamificacao->getNivel() ?></strong></span>
                                <span class="text-muted small"><?= $gamificacao->getXpTotal() ?> XP no total</span>
                            </div>

                            <div class="progress my-2" role="progressbar" aria-valuenow="<?= $progresso['percentual'] ?>"
                                aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: <?= $progresso['percentual'] ?>%"></div>
                            </div>
                            <p class="text-muted small mb-3">
                                <?= $progresso['xpNoNivel'] ?> / <?= $progresso['xpParaProximo'] ?> XP para o nível
                                <?= $gamificacao->getNivel() + 1 ?>
                            </p>

                            <div class="row text-center">
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Sequência atual</p>
                                    <h4 class="mb-0"><?= $gamificacao->getStreakAtual() ?> dias</h4>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Maior sequência</p>
                                    <h4 class="mb-0"><?= $gamificacao->getMaiorStreak() ?> dias</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Preferências</h5>

                            <form action="<?= URL_BASE ?>/perfil/preferencias" method="post">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="visibilidadeRanking"
                                        name="visibilidadeRanking" <?= $gamificacao->getVisibilidadeRanking() ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="visibilidadeRanking">Aparecer no ranking</label>
                                </div>

                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-check-circle"></i> Salvar preferências
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            <?php endif; ?>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>