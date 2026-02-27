<!-- Header Konteks & Date Filter -->
<div class="mb-8 relative z-50 flex flex-col xl:flex-row xl:items-end justify-between gap-6">
    <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight drop-shadow-md">
            Selamat datang, <?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?>!
        </h1>
        <p class="text-white/60 mt-2 text-sm md:text-base font-medium">
            <?php if($activeBrand): ?>
                Overview performa untuk <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong>.
            <?php else: ?>
                Anda belum memiliki atau belum ditugaskan pada brand apapun.
            <?php endif; ?>
        </p>
    </div>
    
    <!-- Filter Tanggal (Glass Style) -->
    <form action="<?= base_url('dashboard') ?>" method="GET" class="flex flex-wrap items-center gap-2 bg-white/5 backdrop-blur-xl p-2 rounded-[20px] border border-white/10 shadow-lg w-max">
        <div class="flex items-center px-3 py-2 bg-black/20 rounded-xl border border-white/5">
            <i class="ph-bold ph-calendar text-white/50 mr-2"></i>
            <input type="date" name="start_date" value="<?= htmlspecialchars($metrics['start_date']) ?>" class="bg-transparent border-none text-white text-xs font-medium focus:ring-0 outline-none [color-scheme:dark] cursor-pointer">
        </div>
        <span class="text-white/30 font-bold">-</span>
        <div class="flex items-center px-3 py-2 bg-black/20 rounded-xl border border-white/5">
            <input type="date" name="end_date" value="<?= htmlspecialchars($metrics['end_date']) ?>" class="bg-transparent border-none text-white text-xs font-medium focus:ring-0 outline-none [color-scheme:dark] cursor-pointer">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow-md ml-1">
            Filter Data
        </button>
    </form>
</div>

<!-- Widget Metrics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6 mb-8 relative z-40">
    
    <!-- Widget 1: Ads Spend & CTR -->
    <div class="bg-white/5 backdrop-blur-2xl rounded-[28px] border border-white/10 shadow-xl p-6 relative overflow-hidden group hover:-translate-y-1 hover:border-white/30 transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl group-hover:bg-blue-500/30 transition-colors"></div>
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-white/50 mb-2">Spend Meta Ads</p>
                <p class="text-2xl font-black text-white tracking-tight"><?= format_rupiah($metrics['ads_spend']) ?></p>
            </div>
            <div class="h-12 w-12 rounded-[18px] bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white shadow-[0_0_15px_rgba(59,130,246,0.5)]">
                <i class="ph-bold ph-currency-circle-dollar text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-medium text-white/40 bg-black/20 w-max px-3 py-1.5 rounded-full border border-white/5">
            <i class="ph-fill ph-cursor-click mr-1.5"></i> Avg CTR: <?= format_percentage($metrics['ctr']) ?>
        </div>
    </div>

    <!-- Widget 2: CPR/Results -->
    <div class="bg-white/5 backdrop-blur-2xl rounded-[28px] border border-white/10 shadow-xl p-6 relative overflow-hidden group hover:-translate-y-1 hover:border-white/30 transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/20 rounded-full blur-2xl group-hover:bg-emerald-500/30 transition-colors"></div>
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-white/50 mb-2">Total Results</p>
                <p class="text-2xl font-black text-white tracking-tight"><?= format_number($metrics['ads_results']) ?></p>
            </div>
            <div class="h-12 w-12 rounded-[18px] bg-gradient-to-br from-emerald-500 to-green-400 flex items-center justify-center text-white shadow-[0_0_15px_rgba(16,185,129,0.5)]">
                <i class="ph-bold ph-trend-up text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs font-medium text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 w-max px-3 py-1.5 rounded-full backdrop-blur-md">
            Cost per Result: <?= format_rupiah($metrics['cpr']) ?>
        </div>
    </div>

    <!-- Widget 3: Konten On Progress -->
    <div class="bg-white/5 backdrop-blur-2xl rounded-[28px] border border-white/10 shadow-xl p-6 relative overflow-hidden group hover:-translate-y-1 hover:border-white/30 transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/20 rounded-full blur-2xl group-hover:bg-purple-500/30 transition-colors"></div>
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-white/50 mb-2">Konten Aktif</p>
                <p class="text-2xl font-black text-white tracking-tight"><?= number_format($metrics['active_contents']) ?></p>
            </div>
            <div class="h-12 w-12 rounded-[18px] bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white shadow-[0_0_15px_rgba(168,85,247,0.5)]">
                <i class="ph-bold ph-video-camera text-2xl"></i>
            </div>
        </div>
        <a href="<?= base_url('content/kanban') ?>" class="mt-4 inline-flex items-center text-[10px] font-medium text-white/60 hover:text-white bg-white/5 border border-white/10 w-max px-3 py-1.5 rounded-full backdrop-blur-md transition-colors text-decoration-none">
            Lihat Planner <i class="ph-bold ph-arrow-right ml-1.5"></i>
        </a>
    </div>

    <!-- Widget 4: Open Tickets -->
    <div class="bg-white/5 backdrop-blur-2xl rounded-[28px] border border-white/10 shadow-xl p-6 relative overflow-hidden group hover:-translate-y-1 hover:border-white/30 transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-500/20 rounded-full blur-2xl group-hover:bg-orange-500/30 transition-colors"></div>
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-white/50 mb-2">Tiket Terbuka</p>
                <p class="text-2xl font-black text-white tracking-tight"><?= number_format($metrics['open_tickets']) ?></p>
            </div>
            <div class="h-12 w-12 rounded-[18px] bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center text-white shadow-[0_0_15px_rgba(249,115,22,0.5)]">
                <i class="ph-bold ph-ticket text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs font-bold text-orange-300 bg-orange-500/10 border border-orange-500/20 w-max px-3 py-1.5 rounded-full backdrop-blur-md">
            <i class="ph-fill ph-warning-circle mr-1.5"></i> Butuh Perhatian
        </div>
    </div>

</div>

<!-- Data Visualization & Activity Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 relative z-40">
    
    <!-- Visualisasi Chart Dinamis (Terkoneksi) -->
    <div class="lg:col-span-2 bg-white/5 backdrop-blur-2xl rounded-[32px] border border-white/10 shadow-2xl p-6 md:p-8 flex flex-col overflow-x-auto scrollbar-hide">
        <div class="flex justify-between items-center mb-6 min-w-[500px]">
            <h3 class="text-lg font-bold text-white tracking-tight flex items-center">
                <i class="ph-fill ph-chart-bar text-blue-400 mr-2 text-xl"></i> Tren Ads Spend
            </h3>
            <span class="bg-black/30 border border-white/10 text-white/60 text-[10px] uppercase font-bold px-3 py-1 rounded-full">
                Sesuai Filter
            </span>
        </div>
        
        <!-- CSS Based Dynamic Chart -->
        <div class="flex-grow flex items-end justify-between gap-1 md:gap-3 h-48 mt-4 min-w-[500px]">
            <?php 
                $count = count($metrics['chart_data']);
                foreach($metrics['chart_data'] as $idx => $item): 
                    $isToday = $item['date'] == date('Y-m-d'); 
                    $showLabel = $count <= 14 || $idx % floor($count/7) == 0 || $idx == $count - 1; 
            ?>
            <div class="flex flex-col items-center flex-1 group relative">
                <div class="w-full relative flex justify-center items-end bg-black/20 rounded-full h-full p-0.5 md:p-1 border border-white/5 hover:border-white/20 transition-colors cursor-crosshair">
                    <div class="w-full rounded-full transition-all duration-700 ease-out group-hover:opacity-100 relative overflow-hidden <?= $isToday ? 'bg-gradient-to-t from-blue-600 to-cyan-400 shadow-[0_0_15px_rgba(59,130,246,0.5)] opacity-100' : 'bg-white/20 opacity-70' ?>" style="height: <?= $item['percent'] ?>%;">
                        <div class="absolute top-0 left-0 w-full h-1/3 bg-gradient-to-b from-white/30 to-transparent"></div>
                    </div>
                </div>
                <span class="mt-3 text-[9px] md:text-[10px] font-medium <?= $isToday ? 'text-white font-bold' : 'text-white/40' ?>" style="<?= !$showLabel ? 'opacity: 0;' : '' ?>">
                    <?= $count <= 7 ? htmlspecialchars($item['day']) : date('d/m', strtotime($item['date'])) ?>
                </span>
                
                <!-- Tooltip Hover Dinamis -->
                <div class="absolute -top-12 bg-black/90 backdrop-blur-md text-white text-xs px-3 py-2 rounded-xl border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 flex flex-col items-center shadow-xl">
                    <span class="font-bold text-blue-300"><?= format_rupiah($item['spend']) ?></span>
                    <span class="text-[9px] text-white/60 mt-0.5"><?= format_number($item['results']) ?> Results</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Seksi Lintas Modul: Aktivitas Terbaru -->
    <div class="lg:col-span-1 bg-white/5 backdrop-blur-2xl rounded-[32px] border border-white/10 shadow-2xl p-6 md:p-8 flex flex-col h-full">
        <h3 class="text-lg font-bold text-white tracking-tight flex items-center mb-6">
            <i class="ph-fill ph-clock-counter-clockwise text-purple-400 mr-2 text-xl"></i> Pantauan Aktivitas
        </h3>
        
        <div class="flex-grow flex flex-col <?= empty($recentActivities) ? 'justify-center items-center text-center py-8' : '' ?>">
            
            <?php if(empty($recentActivities)): ?>
                <div class="w-20 h-20 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-5 shadow-inner">
                    <i class="ph-fill ph-ghost text-4xl text-white/30 drop-shadow-md"></i>
                </div>
                <p class="text-sm font-medium text-white/80 mb-1">Tidak ada rekam jejak</p>
                <p class="text-xs text-white/40 leading-relaxed px-2">Data tidak ditemukan pada rentang tanggal yang dipilih.</p>
            <?php else: ?>
                <div class="w-full space-y-3">
                    <?php foreach($recentActivities as $act): ?>
                        <div class="flex items-center p-3.5 bg-black/20 hover:bg-white/5 transition-colors rounded-[20px] border border-white/5 group">
                            
                            <?php 
                                $icon = ''; $color = '';
                                switch($act['type']) {
                                    case 'ticket':   $icon = 'ph-ticket'; $color = 'bg-orange-500/20 text-orange-400 border-orange-500/20'; break;
                                    case 'content':  $icon = 'ph-video-camera'; $color = 'bg-purple-500/20 text-purple-400 border-purple-500/20'; break;
                                    case 'creative': $icon = 'ph-image'; $color = 'bg-pink-500/20 text-pink-400 border-pink-500/20'; break;
                                    case 'forum':    $icon = 'ph-chat-circle-text'; $color = 'bg-blue-500/20 text-blue-400 border-blue-500/20'; break;
                                    case 'keyword':  $icon = 'ph-magnifying-glass'; $color = 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20'; break;
                                }
                            ?>
                            
                            <div class="w-10 h-10 rounded-[14px] flex items-center justify-center mr-3.5 shrink-0 border <?= $color ?>">
                                <i class="ph-fill <?= $icon ?> text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-white/90 truncate pr-2 group-hover:text-white transition-colors" title="<?= htmlspecialchars($act['title']) ?>">
                                    <?= htmlspecialchars($act['title']) ?>
                                </p>
                                <p class="text-[10px] font-medium text-white/40 mt-0.5 uppercase tracking-wider flex items-center">
                                    <span class="mr-2"><?= ucfirst($act['type']) ?></span> • <span class="ml-2"><?= date('d M H:i', strtotime($act['created_at'])) ?></span>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>

</div>