# 00 - Source Brief (Verbatim)

## Raw User Brief

<<BEGIN BRIEF>>
1. Contexto do projeto: A ChamaLead quer um funil tipo quiz diagnóstico premium para vender automação (R$1.997/1º mês, R$997/mês depois). Módulos: Prospecção, Atendimento Automático, Follow-up Automático.
2. Objetivo: Experiência premium, simples, bonita. Funciona como diagnóstico comercial, não formulário. Salvar progressivamente, chamar webhook no final.
3. Posicionamento: Parece ferramenta inteligente, não "cadastro".
4. Regras de Ouro: Curta na percepção, densa, progressiva, conversacional, sem fricção.
5. Escopo Funcional: Salvar no banco (nome, whatsapp, cargo, faturamento, leads, dor, urgência, score, UTMs). Salvar a cada passo (progressivo). Disparo de Webhook no final (handoff para IA).
6. Etapas (1 pergunta/tela): 0) Abertura. 1) Nome. 2) WhatsApp. 3) Cargo. 4) Faturamento. 5) Canal. 6) Volume. 7) Dor principal. 8) Condicional da dor. 9) Timing. 10) CTA Final.
7. Bifurcações: Faturamento < 20k (Trilha A - consultiva), Faturamento > 20k (Trilha B - acelerada).
8. Lead Scoring: 
   - Autoridade (Dono +3, Gestor +2)
   - Receita (<10k -1, 10-20k +1, 20-50k +3, 50-100k +4, 100k+ +5)
   - Volume (0-10 0, 11-30 +1, 31-100 +2, 100+ +3)
   - Dor (+2 ou +1 dependendo)
   - Timing (Agora +3, Este mês +2, Próximo +1, Entendendo 0)
   - Classificação: Frio (0-4), Morno (5-8), Quente (9+)
9. Banco de Dados: Tabela `quiz_leads` com colunas especificadas. Status: started, in_progress, completed, webhook_pending, webhook_sent, webhook_error.
10. Webhook Payload: JSON estrito detalhado no briefing. Prevenir duplo clique. Idempotente.
11. UX/UI: Dark mode. Fundo roxo quase preto, texto branco, destaque coral/vermelho vivo. Tipografia gigante e pesada. Mobile-first. Uma pergunta por tela.
<<END BRIEF>>

## Source Context

- **Project**: ChamaLead - PHP-based lead management system with SQLite
- **Stack**: PHP 8.2+, SQLite3, Vanilla JS, Tailwind CSS CDN
- **Existing Pattern**: Landing page uses `index.php` to scan `modules/` directory, sort HTML files alphabetically, and include them sequentially
- **Existing API**: `api.php` handles POST requests for lead capture with validation
- **Config**: `config.php` contains DB connection, helper functions, and schema management
- **Assets**: `assets/css/app.css` and `assets/js/app.js` for custom styles and scripts
- **Theme**: Dark mode with flame/ember color palette, Inter + Space Grotesk fonts
