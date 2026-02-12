<?php
date_default_timezone_set('America/Sao_Paulo');
require_once 'config.php';

// Verificar acesso secreto
$access_key = $_GET['key'] ?? '';
if ($access_key !== ADMIN_SECRET) {
    http_response_code(403);
    die('<h1>Acesso Negado</h1>');
}

$db = getDB();

// Ações
$action = $_GET['action'] ?? '';
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db->exec("DELETE FROM leads WHERE id = $id");
    header('Location: ?key=' . ADMIN_SECRET);
    exit;
}

// Filtros
$filter_status = $_GET['status'] ?? 'todos';
$filter_search = trim($_GET['search'] ?? '');
$sort_by = $_GET['sort'] ?? 'newest';

// Query base
$sql = "SELECT * FROM leads WHERE 1=1";
$params = [];

if ($filter_status === 'novos') {
    $sql .= " AND status = 'novo'";
} elseif ($filter_status === 'contatados') {
    $sql .= " AND status = 'contatado'";
}

if (!empty($filter_search)) {
    $sql .= " AND (empresa LIKE :search OR whatsapp LIKE :search OR instagram LIKE :search)";
    $params[':search'] = '%' . $filter_search . '%';
}

// Ordenação
switch ($sort_by) {
    case 'newest':
        $sql .= " ORDER BY created_at DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    case 'faturamento_high':
        $sql .= " ORDER BY faturamento_valor DESC";
        break;
    case 'faturamento_low':
        $sql .= " ORDER BY faturamento_valor ASC";
        break;
}

$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$result = $stmt->execute();

$leads = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $leads[] = $row;
}

// Estatísticas
$total = $db->querySingle("SELECT COUNT(*) FROM leads");
$novos = $db->querySingle("SELECT COUNT(*) FROM leads WHERE status = 'novo'");
$hot_leads = $db->querySingle("SELECT COUNT(*) FROM leads WHERE faturamento IN ('20k_50k', '50k_100k', 'acima_100k')");

// Labels de faturamento
$faturamento_labels = [
    'ate_10k' => 'Até R$ 10k',
    '10k_20k' => 'R$ 10k - R$ 20k',
    '20k_50k' => 'R$ 20k - R$ 50k',
    '50k_100k' => 'R$ 50k - R$ 100k',
    'acima_100k' => 'Acima de R$ 100k'
];

// Função para formatar data relativa
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) return 'Agora';
    if ($diff < 3600) return floor($diff / 60) . ' min atrás';
    if ($diff < 86400) return floor($diff / 3600) . 'h atrás';
    if ($diff < 604800) return floor($diff / 86400) . ' dias atrás';
    return date('d/m/Y', $time);
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Leads | Performar Company</title>
    
    <!-- Tailwind & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;800&family=Oswald:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#2563eb',
                        'brand-dark': '#09090b',
                        'brand-yellow': '#FACC15',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Oswald', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    }
                }
            }
        }
    </script>

    <style>
        body { 
            background-color: #09090b; 
            color: #e4e4e7;
        }
        
        .section-base {
            position: relative;
            background: linear-gradient(180deg, #09090b 0%, #0c0c0f 50%, #09090b 100%);
        }
        
        .section-base::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(37, 99, 235, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(37, 99, 235, 0.02) 0%, transparent 40%),
                radial-gradient(#1f1f24 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 40px 40px;
            pointer-events: none;
            z-index: 0;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        
        .glass-card:hover {
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow: 0 0 40px rgba(37, 99, 235, 0.1);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #fff 0%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .lead-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .lead-card:hover {
            transform: translateY(-4px);
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 40px rgba(37, 99, 235, 0.1);
        }
        
        .lead-card.hot {
            border-color: rgba(250, 204, 21, 0.3);
        }
        
        .lead-card.hot:hover {
            border-color: rgba(250, 204, 21, 0.5);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 40px rgba(250, 204, 21, 0.1);
        }
        
        .input-field {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #2563eb;
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.2);
            outline: none;
        }
        
        .badge-new {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .badge-hot {
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.2), rgba(234, 179, 8, 0.1));
            color: #FACC15;
            border: 1px solid rgba(250, 204, 21, 0.3);
        }
        
        .badge-contacted {
            background: rgba(255, 255, 255, 0.05);
            color: #71717a;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .btn-whatsapp {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }
        
        .btn-whatsapp:hover {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(225, 29, 72, 0.05));
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #f43f5e;
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            color: white;
            transform: translateY(-2px);
        }
        
        .empty-state {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 2px dashed rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 section-base -z-10"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <header class="mb-12">
            <div class="glass-card rounded-2xl p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-blue to-blue-700 flex items-center justify-center shadow-lg shadow-brand-blue/30">
                                <i data-lucide="bar-chart-3" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <span class="font-display font-bold text-white text-2xl tracking-tight uppercase">Performar</span>
                                <span class="text-xs text-brand-blue font-bold uppercase tracking-widest ml-1">Company</span>
                            </div>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-display font-bold text-white mt-4 uppercase tracking-tight">
                            Painel de <span class="text-brand-blue">Leads</span>
                        </h1>
                        <p class="text-zinc-400 text-lg mt-2">Gerencie seus leads e acompanhe oportunidades em tempo real</p>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/30">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-emerald-400 text-sm font-medium">Sistema Online</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="stat-card rounded-2xl p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-brand-blue/20 to-brand-blue/5 flex items-center justify-center border border-brand-blue/20">
                        <i data-lucide="users" class="w-7 h-7 text-brand-blue"></i>
                    </div>
                    <span class="text-zinc-400 text-sm font-medium uppercase tracking-wider">Total de Leads</span>
                </div>
                <div class="text-5xl font-display font-bold text-white"><?php echo $total; ?></div>
            </div>

            <div class="stat-card rounded-2xl p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 flex items-center justify-center border border-emerald-500/20">
                        <i data-lucide="clock" class="w-7 h-7 text-emerald-500"></i>
                    </div>
                    <span class="text-zinc-400 text-sm font-medium uppercase tracking-wider">Não Contatados</span>
                </div>
                <div class="text-5xl font-display font-bold text-white"><?php echo $novos; ?></div>
            </div>

            <div class="stat-card rounded-2xl p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-brand-yellow/20 to-brand-yellow/5 flex items-center justify-center border border-brand-yellow/20">
                        <i data-lucide="flame" class="w-7 h-7 text-brand-yellow"></i>
                    </div>
                    <span class="text-zinc-400 text-sm font-medium uppercase tracking-wider">Hot Leads (>20k)</span>
                </div>
                <div class="text-5xl font-display font-bold text-white"><?php echo $hot_leads; ?></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card rounded-2xl p-6 mb-10">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="key" value="<?php echo ADMIN_SECRET; ?>">
                
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-zinc-400 text-xs font-medium uppercase tracking-wider mb-2">Buscar</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-500"></i>
                        <input type="text" name="search" placeholder="Empresa, WhatsApp..." 
                               value="<?php echo htmlspecialchars($filter_search); ?>"
                               class="input-field w-full pl-10 pr-4 py-3 rounded-xl text-white placeholder-zinc-500">
                    </div>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-zinc-400 text-xs font-medium uppercase tracking-wider mb-2">Status</label>
                    <select name="status" class="input-field w-full px-4 py-3 rounded-xl text-white appearance-none cursor-pointer">
                        <option value="todos" <?php echo $filter_status === 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="novos" <?php echo $filter_status === 'novos' ? 'selected' : ''; ?>>Não Contatados</option>
                        <option value="contatados" <?php echo $filter_status === 'contatados' ? 'selected' : ''; ?>>Contatados</option>
                    </select>
                </div>

                <div class="min-w-[180px]">
                    <label class="block text-zinc-400 text-xs font-medium uppercase tracking-wider mb-2">Ordenar</label>
                    <select name="sort" class="input-field w-full px-4 py-3 rounded-xl text-white appearance-none cursor-pointer">
                        <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Mais Recentes</option>
                        <option value="oldest" <?php echo $sort_by === 'oldest' ? 'selected' : ''; ?>>Mais Antigos</option>
                        <option value="faturamento_high" <?php echo $sort_by === 'faturamento_high' ? 'selected' : ''; ?>>Maior Faturamento</option>
                        <option value="faturamento_low" <?php echo $sort_by === 'faturamento_low' ? 'selected' : ''; ?>>Menor Faturamento</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold transition-all duration-300 flex items-center gap-2">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                    Filtrar
                </button>
            </form>
        </div>

        <!-- Leads Grid -->
        <?php if (empty($leads)): ?>
            <div class="empty-state rounded-3xl p-16 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-zinc-800/50 flex items-center justify-center">
                    <i data-lucide="inbox" class="w-10 h-10 text-zinc-600"></i>
                </div>
                <h3 class="text-2xl font-display font-bold text-white mb-2 uppercase">Nenhum lead encontrado</h3>
                <p class="text-zinc-500">Os leads cadastrados aparecerão aqui automaticamente</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($leads as $lead): 
                    $is_hot = isHotLead($lead['faturamento']);
                ?>
                    <div class="lead-card rounded-2xl p-6 <?php echo $is_hot ? 'hot' : ''; ?>">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-5">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-xl font-display font-bold text-white uppercase tracking-tight truncate">
                                    <?php echo htmlspecialchars($lead['empresa']); ?>
                                </h3>
                                <div class="flex items-center gap-1 mt-1 text-zinc-500 text-sm">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span><?php echo timeAgo($lead['created_at']); ?></span>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-3">
                                <?php if ($is_hot): ?>
                                    <span class="badge-hot px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Hot</span>
                                <?php endif; ?>
                                <?php if ($lead['status'] === 'novo'): ?>
                                    <span class="badge-new px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Novo</span>
                                <?php else: ?>
                                    <span class="badge-contacted px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Contatado</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-zinc-800/50 flex items-center justify-center">
                                    <i data-lucide="phone" class="w-5 h-5 text-zinc-400"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-zinc-500 uppercase tracking-wider">WhatsApp</div>
                                    <div class="text-zinc-200 font-medium"><?php echo htmlspecialchars($lead['whatsapp']); ?></div>
                                </div>
                            </div>

                            <?php if ($lead['instagram']): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-zinc-800/50 flex items-center justify-center">
                                    <i data-lucide="instagram" class="w-5 h-5 text-zinc-400"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-zinc-500 uppercase tracking-wider">Instagram</div>
                                    <div class="text-zinc-200 font-medium">@<?php echo htmlspecialchars($lead['instagram']); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-zinc-800/50 flex items-center justify-center">
                                    <i data-lucide="dollar-sign" class="w-5 h-5 text-zinc-400"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-zinc-500 uppercase tracking-wider">Faturamento Mensal</div>
                                    <div class="font-semibold <?php echo $is_hot ? 'text-brand-yellow' : 'text-white'; ?>">
                                        <?php echo $faturamento_labels[$lead['faturamento']] ?? $lead['faturamento']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3 pt-5 border-t border-zinc-800">
                            <a href="https://wa.me/55<?php echo preg_replace('/[^0-9]/', '', $lead['whatsapp']); ?>?text=Olá! Vi o cadastro da empresa <?php echo urlencode($lead['empresa']); ?> e gostaria de conversar." 
                               target="_blank" 
                               class="btn-whatsapp flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-300">
                                <i data-lucide="message-circle" class="w-5 h-5"></i>
                                WhatsApp
                            </a>
                            <a href="?key=<?php echo ADMIN_SECRET; ?>&action=delete&id=<?php echo $lead['id']; ?>" 
                               class="btn-delete flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-300"
                               onclick="return confirm('Tem certeza que deseja excluir este lead?')">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>