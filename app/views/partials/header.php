<nav class="navbar navbar-dark navbar-dflg mb-4 sticky-top">
    <div class="container">
        <a href="<?= URL_BASE ?>/dashboard" class="navbar-brand mb-0 h1 text-white text-decoration-none">
            <img src="<?= URL_BASE ?>/assets/img/logo-dflg-transparent.png" alt="DFLG" height="40" class="me-2"> Investments </a>
        <div>
            <a href="<?= URL_BASE ?>/dashboard" class="btn btn-outline-light btn-sm me-1">Dashboard</a>
            <a href="<?= URL_BASE ?>/metas" class="btn btn-outline-light btn-sm me-1">Metas</a>
            <a href="<?= URL_BASE ?>/transacoes" class="btn btn-outline-light btn-sm me-1">Finanças</a>
            <a href="<?= URL_BASE ?>/parcelamentos" class="btn btn-outline-light btn-sm me-1">Parcelamentos</a>
            <a href="<?= URL_BASE ?>/calculadoras" class="btn btn-outline-light btn-sm me-1">Calculadoras</a>
            <a href="<?= URL_BASE ?>/ranking" class="btn btn-outline-light btn-sm me-1">Ranking</a>
            <a href="<?= URL_BASE ?>/relatorios" class="btn btn-outline-light btn-sm me-1">Relatorios</a>
            <a href="<?= URL_BASE ?>/limites" class="btn btn-outline-light btn-sm me-1">Limites</a>
            <a href="<?= URL_BASE ?>/perfil" class="btn btn-outline-light btn-sm me-1">Perfil</a>
            <?php if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado']->eAdmin()): ?>
                <a href="<?= URL_BASE ?>/admin/usuarios" class="btn btn-outline-light btn-sm me-1">Admin</a>
            <?php endif; ?>
            <a href="<?= URL_BASE ?>/logout" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Sair
            </a>
        </div>
    </div>
</nav>