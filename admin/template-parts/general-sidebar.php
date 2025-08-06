<div class="col-3">
    <div class="card">
        <div class="card-body">

            <h4 class="subheader">Principais</h4>
            <div class="list-group list-group-transparent">

                <a class="list-group-item list-group-item-action <?= activeSidebarLink('geral'); ?>" href="<?= INCLUDE_PATH_ADMIN; ?>geral">
                    Geral
                </a>

                <a class="list-group-item list-group-item-action <?= activeSidebarLink('webhook'); ?>" href="<?= INCLUDE_PATH_ADMIN; ?>webhook">
                    Webhook
                </a>

                <a class="list-group-item list-group-item-action <?= activeSidebarLink('captcha'); ?>" href="<?= INCLUDE_PATH_ADMIN; ?>captcha">
                    Captcha
                </a>

                <a class="list-group-item list-group-item-action <?= activeSidebarLink('funcoes'); ?>" href="<?= INCLUDE_PATH_ADMIN; ?>funcoes">
                    Funções
                </a>

                <a class="list-group-item list-group-item-action <?= activeSidebarLink('integracoes'); ?>" href="<?= INCLUDE_PATH_ADMIN; ?>integracoes">
                    Integrações
                </a>

            </div>

        </div>
    </div>
</div>