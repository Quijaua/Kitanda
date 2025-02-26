<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item"><a href="https://floema-doar.org/demonstracao.html" target="_blank" class="link-secondary" rel="noopener">Demonstração</a></li>
                    <li class="list-inline-item"><a href="<?= INCLUDE_PATH; ?>LICENSE" class="link-secondary">Licença</a></li>
                    <li class="list-inline-item"><a href="https://github.com/Quijaua/floema" target="_blank" class="link-secondary" rel="noopener">Source code</a></li>
                    <li class="list-inline-item">
                        <a href="https://github.com/Quijaua/floema" target="_blank" class="link-secondary" rel="noopener">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/heart -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-pink icon-inline icon-4"><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg>
                            Curtir Projeto
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Copyright &copy; <?= date('Y'); ?>
                        <a href="https://floema-doar.org" class="link-secondary"><?= $project['name']; ?></a>.
                        Todos os direitos reservados.
                    </li>
                    <li class="list-inline-item">
                        <a href="https://github.com/Quijaua/floema/releases/tag/<?= $project['version']; ?>" class="link-secondary" rel="noopener">
                            <?= $project['version']; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>