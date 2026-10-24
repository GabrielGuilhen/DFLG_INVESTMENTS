<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administracao de usuarios - DFLG Investments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= URL_BASE ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <?php
    $usuarios = $usuarios ?? [];
    $indicadores = $indicadores ?? ['total' => 0, 'ativos' => 0, 'administradores' => 0];
    ?>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-people me-2"></i>Usuarios</h2>
            <a href="<?= URL_BASE ?>/admin/usuarios/administrador/cadastrar" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Novo administrador
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small class="text-muted">Total</small><h3><?= $indicadores['total'] ?></h3></div></div></div>
            <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small class="text-muted">Ativos</small><h3 class="text-success"><?= $indicadores['ativos'] ?></h3></div></div></div>
            <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><small class="text-muted">Administradores ativos</small><h3><?= $indicadores['administradores'] ?></h3></div></div></div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Usuario</th><th>E-mail</th><th>Perfil</th><th>Status</th><th class="text-end">Acoes</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario->getNome()) ?><br><small class="text-muted">@<?= htmlspecialchars($usuario->getNickname()) ?></small></td>
                            <td><?= htmlspecialchars($usuario->getEmail()) ?></td>
                            <td><span class="badge <?= $usuario->eAdmin() ? 'bg-primary' : 'bg-secondary' ?>"><?= $usuario->eAdmin() ? 'Administrador' : 'Comum' ?></span></td>
                            <td><span class="badge <?= $usuario->getStatus() === 'ativo' ? 'bg-success' : 'bg-danger' ?>"><?= ucfirst($usuario->getStatus()) ?></span></td>
                            <td class="text-end">
                                <a href="<?= URL_BASE ?>/admin/usuarios/editar?id=<?= $usuario->getId() ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <?php if (!$usuario->eAdmin()): ?>
                                    <form method="POST" action="<?= URL_BASE ?>/admin/usuarios/promover" class="d-inline" onsubmit="return confirm('Tornar este usuario administrador?')">
                                        <input type="hidden" name="id" value="<?= $usuario->getId() ?>">
                                        <button class="btn btn-sm btn-outline-primary">Promover</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($usuario->getStatus() === 'ativo' && $usuario->getId() !== $_SESSION['usuario_logado']->getId()): ?>
                                    <form method="POST" action="<?= URL_BASE ?>/admin/usuarios/bloquear" class="d-inline" onsubmit="return confirm('Bloquear este usuario?')">
                                        <input type="hidden" name="id" value="<?= $usuario->getId() ?>">
                                        <button class="btn btn-sm btn-outline-warning">Bloquear</button>
                                    </form>
                                <?php elseif ($usuario->getStatus() === 'bloqueado'): ?>
                                    <form method="POST" action="<?= URL_BASE ?>/admin/usuarios/desbloquear" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $usuario->getId() ?>">
                                        <button class="btn btn-sm btn-outline-success">Desbloquear</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($usuario->getId() !== $_SESSION['usuario_logado']->getId()): ?>
                                    <form method="POST" action="<?= URL_BASE ?>/admin/usuarios/excluir" class="d-inline" onsubmit="return confirm('Excluir este usuario e seus dados?')">
                                        <input type="hidden" name="id" value="<?= $usuario->getId() ?>">
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
</body>
</html>
