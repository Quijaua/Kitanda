<!-- Navbar -->
<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none" >
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                <!-- <a class="nav-link dropdown-toggle show" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="true"> -->
                    <span class="avatar avatar-sm" style="background-image: url(<?= $usuario['imagem']; ?>)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div><?php echo $clientes[0]['nome']; ?></div>
                        <div class="mt-1 small text-secondary"><?php echo $permissao; ?></div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a class="dropdown-item" href="<?= INCLUDE_PATH_ADMIN; ?>minha-loja">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler icons-tabler-outline icon-tabler-building-store"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" /><path d="M5 21l0 -10.15" /><path d="M19 21l0 -10.15" /><path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" /></svg>
                        Minha Loja
                    </a>
                    <a class="dropdown-item" href="<?= INCLUDE_PATH_ADMIN; ?>webhook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                        Configurações
                    </a>
                    <a class="dropdown-item text-danger" href="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/logout.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 text-danger icons-tabler-outline icon-tabler-logout-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" /><path d="M15 12h-12l3 -3" /><path d="M6 15l-3 -3" /></svg>
                        Sair
                    </a>
                </div>
            </div>
        </div>
        <div class="nav-item d-none d-md-flex me-3">
            <div class="btn-list">
                <a href="<?php echo INCLUDE_PATH; ?>" class="btn btn-5" target="_blank" rel="noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-2 icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                    Website
                </a>
                <a href="https://www.asaas.com" class="btn btn-6" target="_blank" rel="noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler text-green icon-2 icons-tabler-outline icon-tabler-wallet"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                    Asaas
                </a>
            </div>
        </div>
    </div>
</header>
<?php
    if (
        $url == 'geral' ||
        $url == 'webhook' ||
        $url == 'funcoes' ||
        $url == 'mensagens' ||
        $url == 'usuarios' ||
        $url == 'vendedoras' ||
        $url == 'contatos' ||
        $url == 'aparencia' ||
        $url == 'paginas' ||
        $url == 'politica-de-privacidade' ||
        $url == 'captcha' ||
        $url == 'integracoes'
    ):
?>
<header class="navbar-expand-md sub-header">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <div class="row flex-column flex-md-row flex-fill align-items-center">
                    <div class="col">
                        <!-- BEGIN NAVBAR MENU -->
                        <ul class="navbar-nav">
                            <?php if (verificaPermissao($_SESSION['user_id'], 'geral', 'read', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('geral'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>geral" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Geral
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>
<!--
                            <?php if (verificaPermissao($_SESSION['user_id'], 'webhook', 'read', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('webhook'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>webhook" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-webhook"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4.876 13.61a4 4 0 1 0 6.124 3.39h6" /><path d="M15.066 20.502a4 4 0 1 0 1.934 -7.502c-.706 0 -1.424 .179 -2 .5l-3 -5.5" /><path d="M16 8a4 4 0 1 0 -8 0c0 1.506 .77 2.818 2 3.5l-3 5.5" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Webhook
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>
                            -->

                            <?php if (verificaPermissao($_SESSION['user_id'], 'funcoes', 'read', $conn)): ?>
<!--
                            <li class="nav-item <?= activeSidebarLink('funcoes'); ?> <?= activeSidebarLink('criar-funcao'); ?> <?= activeSidebarLink('editar-funcao'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>funcoes" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-lock-square-rounded"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" /><path d="M8 11m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" /><path d="M10 11v-2a2 2 0 1 1 4 0v2" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Funções
                                    </span>
                                </a>
                            </li>
-->
                            <?php endif; ?>

                            <?php if (verificaPermissao($_SESSION['user_id'], 'usuarios', 'read', $conn) || verificaPermissao($_SESSION['user_id'], 'usuarios', 'only_own', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('usuarios'); ?> <?= activeSidebarLink('criar-usuario'); ?> <?= activeSidebarLink('editar-usuario'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>usuarios" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Usuários
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if (verificaPermissao($_SESSION['user_id'], 'usuarios', 'read', $conn) || verificaPermissao($_SESSION['user_id'], 'usuarios', 'only_own', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('vendedoras'); ?> <?= activeSidebarLink('criar-usuario'); ?> <?= activeSidebarLink('editar-usuario'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>vendedoras" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Vendedoras
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if (verificaPermissao($_SESSION['user_id'], 'contatos', 'read', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('contatos'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>contatos" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-messages"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 14l-3 -3h-7a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1h9a1 1 0 0 1 1 1v10" /><path d="M14 15v2a1 1 0 0 1 -1 1h-7l-3 3v-10a1 1 0 0 1 1 -1h2" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Contatos
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if (verificaPermissao($_SESSION['user_id'], 'aparencia', 'read', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('aparencia'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>aparencia" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-paint"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" /><path d="M19 6h1a2 2 0 0 1 2 2a5 5 0 0 1 -5 5l-5 0v2" /><path d="M10 15m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Aparência
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if (verificaPermissao($_SESSION['user_id'], 'paginas', 'read', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('paginas'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>paginas" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/file-text -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Páginas
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if (verificaPermissao($_SESSION['user_id'], 'politica-de-privacidade', 'read', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('politica-de-privacidade'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>politica-de-privacidade" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Privacidade e Termo
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>

<!--
                            <?php if (verificaPermissao($_SESSION['user_id'], 'captcha', 'read', $conn)): ?>
                            <li class="nav-item <?= activeSidebarLink('captcha'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>captcha" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-lock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        CAPTCHA
                                    </span>
                                </a>
                            </li>
-->
                            <?php endif; ?>


                            <?php if (verificaPermissao($_SESSION['user_id'], 'integracoes', 'read', $conn)): ?>
<!--
                            <li class="nav-item <?= activeSidebarLink('integracoes'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>integracoes" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Integrações
                                    </span>
                                </a>
                            </li>
-->
                            <?php endif; ?>

                        </ul>
                        <!-- END NAVBAR MENU -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<?php endif; ?>
<?php if ($url == 'editar-perfil'): ?>
<header class="navbar-expand-md sub-header">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <div class="row flex-column flex-md-row flex-fill align-items-center">
                    <div class="col">
                        <!-- BEGIN NAVBAR MENU -->
                        <ul class="navbar-nav">
                            <li class="nav-item <?= activeSidebarLink('editar-perfil'); ?>">
                                <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>editar-perfil" >
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/lock-square-rounded -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-lock-square-rounded"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" /><path d="M8 11m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" /><path d="M10 11v-2a2 2 0 1 1 4 0v2" /></svg>
                                    </span>
                                    <span class="nav-link-title">
                                        Senha
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <!-- END NAVBAR MENU -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<?php endif; ?>
