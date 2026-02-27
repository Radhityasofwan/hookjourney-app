<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle ?? 'Client Portal') ?> | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --ios-blur: blur(40px) saturate(150%);
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #060609; 
            color: #ffffff;
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Liquid Aurora Background */
        .aurora-bg {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; pointer-events: none;
            overflow: hidden; background: #060609;
        }
        .blob {
            position: absolute; border-radius: 50%; opacity: 0.35;
            animation: float-blob 20s infinite ease-in-out alternate;
            filter: blur(120px);
        }
        .blob-1 { background: #2563eb; width: 65vw; height: 65vw; top: -10%; left: -15%; }
        .blob-2 { background: #7c3aed; width: 55vw; height: 55vw; bottom: -5%; right: -10%; animation-delay: -5s; }
        .blob-3 { background: #0d9488; width: 45vw; height: 45vw; top: 25%; left: 20%; animation-delay: -10s; }

        @media (max-width: 768px) {
            .blob { filter: blur(80px); opacity: 0.45; }
            .blob-1 { width: 85vw; height: 85vw; }
            .blob-2 { width: 75vw; height: 75vw; }
            .blob-3 { width: 65vw; height: 65vw; }
        }

        @keyframes float-blob {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(4vw, -4vh) scale(1.05); }
            100% { transform: translate(-2vw, 4vh) scale(0.95); }
        }

        /* Glass Topbar */
        .glass-topbar {
            background: rgba(6, 6, 9, 0.4);
            backdrop-filter: var(--ios-blur);
            -webkit-backdrop-filter: var(--ios-blur);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="antialiased">
    
    <!-- AURORA BACKGROUND -->
    <div class="aurora-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    
    <!-- Public Topbar -->
    <header class="glass-topbar sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 md:h-20 flex items-center justify-between">
            <div class="flex items-center">
                <div class="h-9 w-9 md:h-10 md:w-10 rounded-2xl bg-white/10 text-white flex items-center justify-center font-bold text-sm md:text-base mr-3 backdrop-blur-md">
                    <?= strtoupper(substr($linkData['brand_name'] ?? 'B', 0, 1)) ?>
                </div>
                <span class="text-base md:text-lg font-bold text-white tracking-tight"><?= htmlspecialchars($linkData['brand_name'] ?? 'Brand') ?></span>
            </div>
            <div class="text-[10px] md:text-xs font-semibold text-white/70 flex items-center bg-white/10 px-3 py-1.5 rounded-full backdrop-blur-md uppercase tracking-wider">
                <i class="ph-bold ph-lock-key mr-1.5"></i> Read-Only
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 relative z-10 flex flex-col">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="text-center py-8 text-[10px] md:text-xs text-white/40 font-medium tracking-wider uppercase relative z-10 border-t mt-auto">
        Powered by <strong class="text-white/60"><?= APP_NAME ?></strong> • Premium Client Interface
    </footer>
</body>
</html>