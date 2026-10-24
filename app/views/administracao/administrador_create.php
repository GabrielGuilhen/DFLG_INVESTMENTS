<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo administrador - DFLG Investments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <?php
    $usuario = $usuario ?? [];
    $erros = $erros ?? [];
    ?>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Novo administrador</h2>
            <a href="<?= URL_BASE ?>/admin/usuarios" class="btn btn-outline-secondary">Voltar</a>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <?php foreach ($erros as $erro): ?><?= htmlspecialchars($erro) ?><br><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="<?= URL_BASE ?>/admin/usuarios/administrador/salvar">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="nome">Nome</label><input class="form-control" id="nome" name="nome" required value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="nickname">Nickname</label><input class="form-control" id="nickname" name="nickname" required value="<?= htmlspecialchars($usuario['nickname'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="email">E-mail</label><input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label" for="senha">Senha</label><input type="password" class="form-control" id="senha" name="senha" required></div>
                        <div class="col-md-6"><label class="form-label" for="dataNascimento">Data de nascimento</label><input type="date" class="form-control" id="dataNascimento" name="dataNascimento" required value="<?= htmlspecialchars($usuario['dataNascimento'] ?? '') ?>"></div>
                    </div>
                    <div class="text-end mt-4"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Criar administrador</button></div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
