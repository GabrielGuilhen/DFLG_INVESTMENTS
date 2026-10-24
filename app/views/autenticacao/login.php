<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login • DFLG Investments</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS próprio -->
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">

    <style>
        .input-group-text {
            background-color: transparent;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
    </style>
</head>

<body class="auth-body">

    <div class="card auth-card shadow-lg" style="max-width: 400px;">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="<?= URL_BASE ?>/assets/img/logo-dflg-transparent.png" alt="DFLG" style="width: 64px;">
                <h3 class="card-title mt-2">DFLG Investments</h3>
                <p class="text-muted small">Dream. Fund. Learn. Grow.</p>
            </div>

            <?php if (isset($erros)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <?= is_array($erros) ? implode('<br>', $erros) : $erros ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= URL_BASE ?>/logar" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="senha" class="form-label small fw-bold">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Sua senha" required>
                    </div>
                </div>

                <div class="d-grid shadow-sm">
                    <button type="submit" class="btn btn-primary btn-auth text-white">
                        Entrar <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>

            <p class="text-center small text-muted mt-3 mb-0">
                Não tem conta? <a href="<?= URL_BASE ?>/cadastrar">Cadastre-se</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>