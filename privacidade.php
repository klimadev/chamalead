<?php
$pageTitle = "Privacidade e LGPD | ChamaLead";
$pageDescription = "Política de Privacidade e Proteção de Dados Pessoais do ChamaLead. Conformidade com a Lei Geral de Proteção de Dados (LGPD).";
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $pageDescription ?>">
    <title><?= $pageTitle ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'flame': {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#431407',
                        },
                        'dark': {
                            DEFAULT: '#0a0a0a',
                            900: '#111111',
                            800: '#1a1a1a',
                            700: '#262626',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        body { background: #0a0a0a; }
    </style>
</head>
<body class="selection:bg-flame-500 selection:text-white antialiased text-zinc-300">
    
    <nav class="fixed top-0 left-0 right-0 z-50 bg-dark/90 backdrop-blur-md border-b border-white/5">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-flame-500 to-ember-600 flex items-center justify-center">
                    <i data-lucide="zap" class="w-5 h-5 text-white"></i>
                </div>
                <span class="font-display font-bold text-white text-xl">ChamaLead</span>
            </a>
            <a href="index.php" class="text-zinc-400 hover:text-white transition-colors text-sm font-medium">
                ← Voltar ao site
            </a>
        </div>
    </nav>

    <main class="pt-24 pb-20">
        <div class="max-w-4xl mx-auto px-6">
            
            <header class="mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/10 border border-green-500/20 rounded-full mb-6">
                    <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                    <span class="text-sm text-green-400 font-medium">Conformidade com a LGPD</span>
                </div>
                <h1 class="font-display text-4xl md:text-5xl font-bold text-white mb-4">
                    Política de Privacidade<br>e Proteção de Dados
                </h1>
                <p class="text-lg text-zinc-400">
                    Última atualização: Fevereiro de 2026
                </p>
            </header>

            <div class="prose prose-invert max-w-none space-y-12">
                
                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="info" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Introdução
                    </h2>
                    <p class="text-zinc-400 leading-relaxed">
                        O <strong class="text-white">ChamaLead</strong> ("nós", "nosso" ou "nossa empresa") está comprometido em proteger a privacidade e os dados pessoais de seus clientes, parceiros e usuários. Esta Política de Privacidade explica como coletamos, usamos, compartilhamos e protegemos suas informações pessoais em conformidade com a <strong class="text-white">Lei Geral de Proteção de Dados (Lei nº 13.709/2018)</strong>.
                    </p>
                    <p class="text-zinc-400 leading-relaxed mt-4">
                        Ao utilizar nossos serviços, você declara ter lido, compreendido e aceito esta Política de Privacidade. Caso não concorde com algum termo aqui descrito, solicitamos que não utilize nossos serviços.
                    </p>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="database" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Dados que Coletamos
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        Coletamos apenas os dados necessários para a prestação de nossos serviços. Os dados podem incluir:
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-dark-800 rounded-xl p-5 border border-white/5">
                            <h3 class="text-white font-semibold mb-3">Dados de Clientes (Empresas)</h3>
                            <ul class="space-y-2 text-sm text-zinc-400">
                                <li>• Nome da empresa</li>
                                <li>• Nome do responsável</li>
                                <li>• Telefone/WhatsApp</li>
                                <li>• E-mail profissional</li>
                                <li>• Faturamento mensal</li>
                            </ul>
                        </div>
                        <div class="bg-dark-800 rounded-xl p-5 border border-white/5">
                            <h3 class="text-white font-semibold mb-3">Dados de Leads (Clientes Finais)</h3>
                            <ul class="space-y-2 text-sm text-zinc-400">
                                <li>• Nome</li>
                                <li>• Telefone/WhatsApp</li>
                                <li>• Preferences indicated during conversation</li>
                                <li>• Histórico de interações</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="target" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Finalidade do Tratamento
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        Tratamos seus dados pessoais para as seguintes finalidades:
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-4 bg-dark-800 rounded-xl border border-white/5">
                            <div class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-500"></i>
                            </div>
                            <div>
                                <span class="text-white font-medium">Execução de Contrato</span>
                                <p class="text-sm text-zinc-400">Prestação dos serviços de automação de WhatsApp contratados</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-dark-800 rounded-xl border border-white/5">
                            <div class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-500"></i>
                            </div>
                            <div>
                                <span class="text-white font-medium">Comunicação Comercial</span>
                                <p class="text-sm text-zinc-400">Envio de propostas, orçamentos e informações sobre nossos serviços</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-dark-800 rounded-xl border border-white/5">
                            <div class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-500"></i>
                            </div>
                            <div>
                                <span class="text-white font-medium">Suporte ao Cliente</span>
                                <p class="text-sm text-zinc-400">Atendimento técnico e suporte aos nossos clientes</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-dark-800 rounded-xl border border-white/5">
                            <div class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-500"></i>
                            </div>
                            <div>
                                <span class="text-white font-medium">Melhoria de Serviços</span>
                                <p class="text-sm text-zinc-400">Análise de dados para aprimoramento contínuo de nossas soluções</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="scale" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Base Legal
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        O tratamento de seus dados pessoais é realizado com base nas seguintes hipóteses legais previstas na LGPD:
                    </p>
                    <div class="space-y-3">
                        <div class="p-4 bg-dark-800 rounded-xl border border-white/5">
                            <h3 class="text-white font-semibold mb-2">Execução de Contrato (Art. 7º, V)</h3>
                            <p class="text-sm text-zinc-400">Quando necessário para a execução de contrato do qual você é parte ou para procedimentos preparatórios</p>
                        </div>
                        <div class="p-4 bg-dark-800 rounded-xl border border-white/5">
                            <h3 class="text-white font-semibold mb-2">Consentimento (Art. 7º, I)</h3>
                            <p class="text-sm text-zinc-400">Quando você nos concede autorização expressa para o tratamento de seus dados para finalidades específicas</p>
                        </div>
                        <div class="p-4 bg-dark-800 rounded-xl border border-white/5">
                            <h3 class="text-white font-semibold mb-2">Legítimo Interesse (Art. 7º, IX)</h3>
                            <p class="text-sm text-zinc-400">Para fins legítimos do controlador, respeitando sua expectativa razoável de privacidade</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="share-2" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Compartilhamento de Dados
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        Мы compartilhamos seus dados pessoais apenas nas seguintes circunstâncias:
                    </p>
                    <div class="space-y-3">
                        <div class="p-4 bg-dark-800 rounded-xl border border-white/5">
                            <h3 class="text-white font-semibold mb-2">Provedores de Serviços</h3>
                            <p class="text-sm text-zinc-400">Com empresas que nos auxiliam na prestação de serviços (hospedagem, infraestrutura, ferramentas de comunicação)</p>
                        </div>
                        <div class="p-4 bg-dark-800 rounded-xl border border-white/5">
                            <h3 class="text-white font-semibold mb-2">Obrigações Legais</h3>
                            <p class="text-sm text-zinc-400">Quando exigido por lei, ordem judicial ou solicitação de autoridades competentes</p>
                        </div>
                        <div class="p-4 bg-dark-800 rounded-xl border border-white/5">
                            <h3 class="text-white font-semibold mb-2">Consentimento</h3>
                            <p class="text-sm text-zinc-400">Apenas com sua autorização prévia e expressa para finalidades específicas</p>
                        </div>
                    </div>
                    <p class="text-zinc-400 leading-relaxed mt-4">
                        <strong class="text-white">Não vendemos seus dados pessoais</strong> a terceiros sob nenhuma circunstância.
                    </p>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="lock" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Segurança dos Dados
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        Adotamos medidas técnicas e administrativas adequadas para proteger seus dados pessoais contra acessos não autorizados, situações acidentais ou ilícitas:
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-3 bg-dark-800 rounded-xl border border-white/5">
                            <i data-lucide="lock" class="w-5 h-5 text-green-500"></i>
                            <span class="text-sm text-zinc-300">Criptografia HTTPS em trânsito</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-dark-800 rounded-xl border border-white/5">
                            <i data-lucide="server" class="w-5 h-5 text-green-500"></i>
                            <span class="text-sm text-zinc-300">Criptografia de dados em repouso</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-dark-800 rounded-xl border border-white/5">
                            <i data-lucide="users" class="w-5 h-5 text-green-500"></i>
                            <span class="text-sm text-zinc-300">Controle de acesso rigoroso</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-dark-800 rounded-xl border border-white/5">
                            <i data-lucide="backup" class="w-5 h-5 text-green-500"></i>
                            <span class="text-sm text-zinc-300">Backups seguros regulares</span>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="clock" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Retenção de Dados
                    </h2>
                    <p class="text-zinc-400 leading-relaxed">
                        Manemos seus dados pessoais apenas pelo período necessário para cumprir as finalidades descritas nesta política, respeitando os prazos legais de retenção. Quando os dados não forem mais necessários, procederemos com sua exclusão segura ou anonimização, conforme apropriado.
                    </p>
                </section>

                <section class="bg-dark-800 rounded-2xl p-8 border border-flame-500/20">
                    <h2 class="font-display text-2xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-flame-500 flex items-center justify-center">
                            <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                        </span>
                        Seus Direitos como Titular de Dados
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-6">
                        A LGPD garante a você os seguintes direitos sobre seus dados pessoais:
                    </p>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="p-4 bg-dark-900 rounded-xl border border-white/5">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="search" class="w-4 h-4 text-flame-500"></i>
                                <span class="text-white font-semibold">Confirmação e Acesso</span>
                            </div>
                            <p class="text-sm text-zinc-400">Direito de confirmar se tratamos seus dados e obter cópia dos mesmos</p>
                        </div>
                        <div class="p-4 bg-dark-900 rounded-xl border border-white/5">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="edit-3" class="w-4 h-4 text-flame-500"></i>
                                <span class="text-white font-semibold">Correção</span>
                            </div>
                            <p class="text-sm text-zinc-400">Direito de solicitar correção de dados incompletos, inexatos ou desatualizados</p>
                        </div>
                        <div class="p-4 bg-dark-900 rounded-xl border border-white/5">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="trash-2" class="w-4 h-4 text-flame-500"></i>
                                <span class="text-white font-semibold">Eliminação</span>
                            </div>
                            <p class="text-sm text-zinc-400">Direito de solicitar exclusão de dados tratados com base no consentimento</p>
                        </div>
                        <div class="p-4 bg-dark-900 rounded-xl border border-white/5">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="package" class="w-4 h-4 text-flame-500"></i>
                                <span class="text-white font-semibold">Portabilidade</span>
                            </div>
                            <p class="text-sm text-zinc-400">Direito de receber seus dados em formato estruturado e legível</p>
                        </div>
                        <div class="p-4 bg-dark-900 rounded-xl border border-white/5">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="shield-off" class="w-4 h-4 text-flame-500"></i>
                                <span class="text-white font-semibold">Anonimização/Bloqueio</span>
                            </div>
                            <p class="text-sm text-zinc-400">Direito de solicitar anonimização ou bloqueio de dados desnecessários</p>
                        </div>
                        <div class="p-4 bg-dark-900 rounded-xl border border-white/5">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="x-circle" class="w-4 h-4 text-flame-500"></i>
                                <span class="text-white font-semibold">Revogação</span>
                            </div>
                            <p class="text-sm text-zinc-400">Direito de revogar o consentimento a qualquer tempo</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-flame-500/10 rounded-xl border border-flame-500/20">
                        <p class="text-zinc-300">
                            <strong class="text-white">Como exercer seus direitos:</strong> Envie um e-mail para 
                            <a href="mailto:privacidade@chama-lead.com.br" class="text-flame-400 hover:text-flame-300 underline">privacidade@chama-lead.com.br</a> 
                            com sua solicitação. Responderemos em até 15 dias, conforme prazo legal.
                        </p>
                    </div>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="cookie" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Cookies
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        Nosso site utiliza cookies para melhorar sua experiência de navegação. Cookies são pequenos arquivos de texto armazenados em seu dispositivo.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 bg-dark-800 rounded-xl border border-white/5">
                            <span class="text-zinc-300">Cookies Essenciais</span>
                            <span class="text-green-500 text-sm">Sempre ativos</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-dark-800 rounded-xl border border-white/5">
                            <span class="text-zinc-300">Cookies de Análise</span>
                            <span class="text-zinc-500 text-sm">Opt-in</span>
                        </div>
                    </div>
                    <p class="text-zinc-400 text-sm mt-4">
                        Você pode configurar seu navegador para recusar cookies, mas isso pode afetar a funcionalidade do site.
                    </p>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span name="user-check">Encarregado de Proteção de Dados (DPO)</span>
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        Designamos um Encarregado de Proteção de Dados (DPO) para atuar como ponto de contato entre a empresa, os titulares dos dados e a Autoridade Nacional de Proteção de Dados (ANPD).
                    </p>
                    <div class="p-4 bg-dark-800 rounded-xl border border-white/5">
                        <p class="text-zinc-300">
                            <strong class="text-white">Contato do Encarregado:</strong><br>
                            E-mail: <a href="mailto:privacidade@chama-lead.com.br" class="text-flame-400 hover:text-flame-300">privacidade@chama-lead.com.br</a>
                        </p>
                    </div>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Alterações nesta Política
                    </h2>
                    <p class="text-zinc-400 leading-relaxed">
                        Podemos atualizar esta Política de Privacidade periodicamente. Qualquer alteração será publicada nesta página com a data de atualização no topo. Recomendamos que você revise esta política regularmente.
                    </p>
                </section>

                <section>
                    <h2 class="font-display text-2xl font-bold text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-flame-500/20 flex items-center justify-center">
                            <i data-lucide="mail" class="w-4 h-4 text-flame-500"></i>
                        </span>
                        Fale Conosco
                    </h2>
                    <p class="text-zinc-400 leading-relaxed mb-4">
                        Em caso de dúvidas sobre esta Política de Privacidade ou sobre o tratamento de seus dados pessoais, entre em contato conosco:
                    </p>
                    <div class="p-6 bg-dark-800 rounded-xl border border-white/5 space-y-3">
                        <div class="flex items-center gap-3">
                            <i data-lucide="mail" class="w-5 h-5 text-flame-500"></i>
                            <span class="text-zinc-300">privacidade@chama-lead.com.br</span>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </main>

    <footer class="border-t border-white/5 py-8">
        <div class="max-w-6xl mx-auto px-6 text-center text-zinc-500 text-sm">
            <p>© 2026 ChamaLead. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
