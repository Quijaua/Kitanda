<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Erro na Configuração do Webhook</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Para configurar o Webhook corretamente, você precisa inserir o <strong>Token de autenticação da API do Asaas</strong>.</p>

                        <h5>Passo a passo para resolver:</h5>
                        <ol>
                            <li>Abra o arquivo <code>.env</code> na raiz do seu projeto.</li>
                            <li>Verifique se as seguintes variáveis estão corretamente preenchidas:</li>
                            <pre>ASAAS_API_URL=https://www.asaas.com/api/v3/<br>ASAAS_API_KEY=sua_chave_de_api_aqui</pre>
                            <li>Salve o arquivo após editar.</li>
                            <li>Repita o processo.</li>
                        </ol>

                        <hr>

                        <a href="<?= INCLUDE_PATH_ADMIN; ?>webhook" class="btn btn-success">
                            Já configurei, tentar novamente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>