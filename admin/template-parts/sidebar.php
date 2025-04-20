<?php
    //Funcao '.active' Sidebar
    function activeSidebarLink($par) {
        $url = explode('/',@$_GET['url'])[0];
        if ($url == $par)
        {
            echo "active";
        }
    }
?>
<!-- Sidebar -->
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand navbar-brand-autodark">
            <a href="<?= INCLUDE_PATH_ADMIN; ?>sobre">
		<h1>Kitanda</h1>
<!--                <img src="<?= INCLUDE_PATH_ADMIN; ?>images/logo-inverse.png" alt="Logo <?php echo $project['name']; ?>" class="navbar-brand-image" style="width: 149px; height: 21px;"> -->
            </a>
        </div>
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>Paweł Kuna</div>
                        <div class="mt-1 small text-secondary">UI Designer</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item">Status</a>
                    <a href="./profile.html" class="dropdown-item">Profile</a>
                    <a href="#" class="dropdown-item">Feedback</a>
                    <div class="dropdown-divider"></div>
                    <a href="./settings.html" class="dropdown-item">Settings</a>
                    <a href="./sign-in.html" class="dropdown-item">Logout</a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">

                <?php if (verificaPermissao($_SESSION['user_id'], 'sobre', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('sobre'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>sobre" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-browser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8h16" /><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M8 4v4" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Checkout
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'produtos', 'read', $conn) || verificaPermissao($_SESSION['user_id'], 'produtos', 'only_own', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('produtos'); ?> <?= activeSidebarLink('cadastrar-produto'); ?> <?= activeSidebarLink('editar-produto'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>produtos" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/building-store -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-building-store"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" /><path d="M5 21l0 -10.15" /><path d="M19 21l0 -10.15" /><path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Produtos
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'doadores', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('doadores'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>doadores" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-chart-bar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20h14" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Pedidos
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'financeiro', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('financeiro'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>financeiro" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-pig-money"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 11v.01" /><path d="M5.173 8.378a3 3 0 1 1 4.656 -1.377" /><path d="M16 4v3.803a6.019 6.019 0 0 1 2.658 3.197h1.341a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-1.342c-.336 .95 -.907 1.8 -1.658 2.473v2.027a1.5 1.5 0 0 1 -3 0v-.583a6.04 6.04 0 0 1 -1 .083h-4a6.04 6.04 0 0 1 -1 -.083v.583a1.5 1.5 0 0 1 -3 0v-2l0 -.027a6 6 0 0 1 4 -10.473h2.5l4.5 -3h0z" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Vendas
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'captcha', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('captcha'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>captcha" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-lock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>
                        </span>
                        <span class="nav-link-title">
                            CAPTCHA
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'integracoes', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('integracoes'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>integracoes" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Integrações
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'mensagens', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('mensagens'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>mensagens" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-messages"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 14l-3 -3h-7a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1h9a1 1 0 0 1 1 1v10" /><path d="M14 15v2a1 1 0 0 1 -1 1h-7l-3 3v-10a1 1 0 0 1 1 -1h2" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Mensagens
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'novidades', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('novidades'); ?> <?= activeSidebarLink('email_em_massa'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>novidades" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-telegram"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Novidades
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

                <?php if (verificaPermissao($_SESSION['user_id'], 'aparencia', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('aparencia'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>aparencia" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-paint"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" /><path d="M19 6h1a2 2 0 0 1 2 2a5 5 0 0 1 -5 5l-5 0v2" /><path d="M10 15m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Aparência
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'cabecalho', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('cabecalho'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>cabecalho" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-layout-navbar-inactive"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M4 9h1" /><path d="M19 9h1" /><path d="M9 9h1" /><path d="M14 9h1" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Cabeçalho
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'rodape', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('rodape'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>rodape" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-layout-bottombar-inactive"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M4 15h1" /><path d="M19 15h1" /><path d="M9 15h1" /><path d="M14 15h1" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Rodapé
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'webhook', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('webhook'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>webhook" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-webhook"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4.876 13.61a4 4 0 1 0 6.124 3.39h6" /><path d="M15.066 20.502a4 4 0 1 0 1.934 -7.502c-.706 0 -1.424 .179 -2 .5l-3 -5.5" /><path d="M16 8a4 4 0 1 0 -8 0c0 1.506 .77 2.818 2 3.5l-3 5.5" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Webhook
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'funcoes', 'read', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('funcoes'); ?> <?= activeSidebarLink('criar-funcao'); ?> <?= activeSidebarLink('editar-funcao'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>funcoes" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-lock-square-rounded"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" /><path d="M8 11m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" /><path d="M10 11v-2a2 2 0 1 1 4 0v2" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Funções
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (verificaPermissao($_SESSION['user_id'], 'usuarios', 'read', $conn) || verificaPermissao($_SESSION['user_id'], 'usuarios', 'only_own', $conn)): ?>
                <li class="nav-item <?= activeSidebarLink('usuarios'); ?> <?= activeSidebarLink('criar-usuario'); ?> <?= activeSidebarLink('editar-usuario'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>usuarios" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Usuários
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item <?= activeSidebarLink('minha-loja'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>minha-loja" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/building-store -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-building-store"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" /><path d="M5 21l0 -10.15" /><path d="M19 21l0 -10.15" /><path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Minha Loja
                        </span>
                    </a>
                </li>

                <li class="nav-item <?= activeSidebarLink('editar-perfil'); ?>">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>editar-perfil" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-id"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 8l2 0" /><path d="M15 12l2 0" /><path d="M7 16l10 0" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Senha
                        </span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/logout.php" >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler text-danger icons-tabler-outline icon-tabler-logout-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" /><path d="M15 12h-12l3 -3" /><path d="M6 15l-3 -3" /></svg>
                        </span>
                        <span class="nav-link-title text-danger">
                            Sair
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
