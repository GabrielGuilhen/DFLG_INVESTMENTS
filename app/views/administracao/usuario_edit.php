<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario - DFLG Investments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>
<?php
$erros = $erros ?? [];
if (!isset($usuario) || !($usuario instanceof \app\models\Usuario)) {
    exit;
}
?>
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Editar usuario</h2>
        <a href="<?= URL_BASE ?>/admin/usuarios" class="btn btn-outline-secondary">Voltar</a>
    </div>
    <?php if (!empty($erros)): ?><div class="alert alert-danger"><?php foreach ($erros as $erro): ?><?= htmlspecialchars($erro) ?><br><?php endforeach; ?></div><?php endif; ?>
    <div class="card shadow-sm"><div class="card-body">
        <form method="post" action="<?= URL_BASE ?>/admin/usuarios/atualizar">
            <input type="hidden" name="id" value="<?= $usuario->getId() ?>">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nome</label><input class="form-control" name="nome" required value="<?= htmlspecialchars($usuario->getNome()) ?>"></div>
                <div class="col-md-6"><label class="form-label">Nickname</label><input class="form-control" name="nickname" required value="<?= htmlspecialchars($usuario->getNickname()) ?>"></div>
                <div class="col-md-6"><label class="form-label">E-mail</label><input class="form-control" type="email" name="email" required value="<?= htmlspecialchars($usuario->getEmail()) ?>"></div>
                <div class="col-md-3"><label class="form-label">Perfil</label><select class="form-select" name="perfil"><option value="comum" <?= $usuario->getPerfil() === 'comum' ? 'selected' : '' ?>>Comum</option><option value="administrador" <?= $usuario->eAdmin() ? 'selected' : '' ?>>Administrador</option></select></div>
                <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="ativo" <?= $usuario->getStatus() === 'ativo' ? 'selected' : '' ?>>Ativo</option><option value="bloqueado" <?= $usuario->getStatus() === 'bloqueado' ? 'selected' : '' ?>>Bloqueado</option></select></div>
                <div class="col-md-6"><label class="form-label">Data de nascimento</label><input class="form-control" type="date" name="dataNascimento" required value="<?= htmlspecialchars($usuario->getDataNascimento()) ?>"></div>
            </div>
            <div class="text-end mt-4"><button class="btn btn-primary" type="submit">Salvar alteracoes</button></div>
        </form>
    </div></div>
</div>
</body>
</html>
