<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro • DFLG Investments</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS próprio -->
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>

<body class="auth-body">

    <div class="card auth-card shadow-lg" style="max-width: 460px;">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                <h3 class="card-title mt-2">Criar Conta</h3>
                <p class="text-muted small">Comece a organizar suas finanças</p>
            </div>

            <?php if (isset($erros) && !empty($erros)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <?= implode('<br>', $erros) ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= URL_BASE ?>/cadastrar/salvar" method="post">
                <div class="mb-3">
                    <label for="nome" class="form-label small fw-bold">Nome completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="nickname" class="form-label small fw-bold">Nickname</label>
                    <input type="text" class="form-control" id="nickname" name="nickname" value="<?= htmlspecialchars($usuario['nickname'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label small fw-bold">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>

                <div class="mb-4">
                    <label for="dataNascimento" class="form-label small fw-bold">Data de nascimento</label>
                    <input type="date" class="form-control" id="dataNascimento" name="dataNascimento" value="<?= htmlspecialchars($usuario['dataNascimento'] ?? '') ?>" required>
                </div>

                <div class="d-grid shadow-sm">
                    <button type="submit" class="btn btn-primary btn-auth text-white">
                        Cadastrar <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>

            <p class="text-center small text-muted mt-3 mb-0">
                Já tem conta? <a href="<?= URL_BASE ?>/login">Entrar</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
