<div class="mb-10 text-center relative z-50">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-white/10 border border-white/20 shadow-2xl backdrop-blur-xl mb-4">
        <i class="ph-fill ph-planet text-3xl text-white"></i>
    </div>
    <h1 class="text-3xl md:text-4xl font-extrabold text-white capitalize tracking-tight drop-shadow-md">Laporan <?= str_replace('_', ' ', $linkData['module_key']) ?></h1>
    <p class="text-white/60 mt-3 text-sm md:text-base font-medium">Disediakan khusus untuk klien / manajemen <strong class="text-white"><?= htmlspecialchars($linkData['brand_name']) ?></strong>.</p>
</div>

<!-- ==========================================
     MODUL: WEEKLY REVIEW
     ========================================== -->
<?php if($linkData['module_key'] == 'weekly_review' && !empty($contentData['review'])): ?>
    <?php 
        $rev = $contentData['review']; 
        $metrics = $contentData['metrics'] ?? [];
        $actions = $contentData['actions'] ?? [];
    ?>
    <div class="bg-white/5 backdrop-blur-2xl rounded-[32px] shadow-2xl border border-white/10 overflow-hidden relative z-40 mx-auto max-w-5xl">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-blue-600/40 to-purple-600/40 backdrop-blur-md px-8 py-6 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white flex items-center tracking-wide">
                <i class="ph-fill ph-calendar-check mr-3 text-2xl text-blue-200"></i>
                Periode: <?= date('d M Y', strtotime($rev['week_start_date'])) ?> s/d <?= date('d M Y', strtotime($rev['week_end_date'])) ?>
            </h2>
        </div>

        <!-- FIX: VISUALISASI METRIK (KPI CARDS) -->
        <?php if(!empty($metrics)): ?>
        <div class="p-6 md:px-10 md:pt-10 bg-black/20 border-b border-white/10">
            <h3 class="text-xs font-bold uppercase tracking-widest text-white/50 mb-5 flex items-center">
                <span class="w-2 h-2 rounded-full bg-blue-400 mr-2 shadow-[0_0_8px_#60a5fa]"></span> Key Performance Indicators (KPI)
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <?php foreach($metrics as $m): ?>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex flex-col justify-center relative overflow-hidden group hover:bg-white/10 transition-colors">
                        <?php 
                            // Tentukan ikon berdasarkan section
                            $icon = 'ph-chart-bar'; $color = 'text-white/20';
                            if($m['section'] == 'ads') { $icon = 'ph-megaphone'; $color = 'text-blue-400/20'; }
                            if($m['section'] == 'seo') { $icon = 'ph-magnifying-glass'; $color = 'text-emerald-400/20'; }
                            if($m['section'] == 'content') { $icon = 'ph-video-camera'; $color = 'text-purple-400/20'; }
                            if($m['section'] == 'team') { $icon = 'ph-check-square-offset'; $color = 'text-orange-400/20'; }
                        ?>
                        <i class="ph-fill <?= $icon ?> <?= $color ?> absolute -right-4 -bottom-4 text-6xl group-hover:scale-110 transition-transform"></i>
                        
                        <span class="text-[10px] text-white/60 uppercase tracking-wider font-bold mb-1.5 relative z-10"><?= htmlspecialchars($m['metric_label']) ?></span>
                        <span class="text-xl md:text-2xl font-extrabold text-white tracking-tight relative z-10">
                            <?php 
                                if(strpos($m['metric_key'], 'spend') !== false || strpos($m['metric_key'], 'cpr') !== false) {
                                    echo format_rupiah($m['metric_value']);
                                } elseif(strpos($m['metric_key'], 'ctr') !== false) {
                                    echo format_percentage($m['metric_value']);
                                } else {
                                    echo format_number($m['metric_value']);
                                }
                            ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="p-6 md:p-10 space-y-8">
            <!-- Eksekutif Summary -->
            <?php if($rev['summary_text']): ?>
            <div class="bg-white/5 backdrop-blur-lg rounded-[24px] p-6 border border-white/10 shadow-inner">
                <h3 class="text-xs font-bold uppercase tracking-widest text-white/50 mb-4 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-blue-400 mr-2"></span> Ringkasan Eksekutif
                </h3>
                <p class="text-white/90 text-sm md:text-base leading-relaxed whitespace-pre-wrap font-medium"><?= htmlspecialchars($rev['summary_text']) ?></p>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                <!-- Top Issues -->
                <?php if($rev['top_issues_text']): ?>
                <div class="bg-red-500/10 backdrop-blur-lg rounded-[24px] p-6 border border-red-500/20 shadow-inner hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                    <h3 class="text-sm font-bold text-red-300 flex items-center mb-4 uppercase tracking-wider">
                        <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center mr-3">
                            <i class="ph-bold ph-warning-circle text-lg"></i>
                        </div>
                        Kendala Utama
                    </h3>
                    <p class="text-red-100/80 text-sm leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($rev['top_issues_text']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Insights -->
                <?php if($rev['insights_text']): ?>
                <div class="bg-emerald-500/10 backdrop-blur-lg rounded-[24px] p-6 border border-emerald-500/20 shadow-inner hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                    <h3 class="text-sm font-bold text-emerald-300 flex items-center mb-4 uppercase tracking-wider">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center mr-3">
                            <i class="ph-bold ph-lightbulb text-lg"></i>
                        </div>
                        Insight Kreatif & Ads
                    </h3>
                    <p class="text-emerald-100/80 text-sm leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($rev['insights_text']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FIX: RENCANA TINDAKAN (ACTION ITEMS) VISUALIZATION -->
        <?php if(!empty($actions) || !empty($rev['next_actions_text'])): ?>
        <div class="p-6 md:p-10 border-t border-white/10 bg-black/30">
            <h3 class="text-xs font-bold uppercase tracking-widest text-white/50 mb-5 flex items-center">
                <span class="w-2 h-2 rounded-full bg-purple-400 mr-2 shadow-[0_0_8px_#c084fc]"></span> Rencana Tindakan (Action Plan)
            </h3>
            
            <?php if(!empty($rev['next_actions_text'])): ?>
                <div class="text-white/80 text-sm md:text-base leading-relaxed whitespace-pre-wrap mb-6">
                    <?= htmlspecialchars($rev['next_actions_text']) ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($actions)): ?>
                <div class="overflow-x-auto rounded-2xl border border-white/10 bg-white/5">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-black/40 text-white/40 text-[10px] uppercase tracking-widest border-b border-white/10">
                            <tr>
                                <th class="px-5 py-4">Tugas / Perbaikan Detail</th>
                                <th class="px-5 py-4">PIC (Penanggung Jawab)</th>
                                <th class="px-5 py-4">Target Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            <?php foreach($actions as $act): ?>
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-5 py-4 text-white/90 font-medium"><?= htmlspecialchars($act['title']) ?></td>
                                <td class="px-5 py-4 text-white/60 flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-[10px] font-bold mr-2">
                                        <?= strtoupper(substr($act['owner_name'] ?? 'T', 0, 1)) ?>
                                    </div>
                                    <?= htmlspecialchars($act['owner_name'] ?? 'Tim Internal') ?>
                                </td>
                                <td class="px-5 py-4 text-emerald-400 font-bold">
                                    <i class="ph-bold ph-calendar-blank mr-1.5"></i> 
                                    <?= $act['due_date'] ? date('d M Y', strtotime($act['due_date'])) : '-' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
    </div>

<!-- ==========================================
     MODUL: DASHBOARD (ADS AGGREGATE)
     ========================================== -->
<?php elseif($linkData['module_key'] == 'dashboard' && !empty($contentData['dashboard'])): ?>
    <?php 
        $dash = $contentData['dashboard']; 
        $cpr = $dash['total_results'] > 0 ? ($dash['total_spend'] / $dash['total_results']) : 0;
        $ctr = $dash['total_impressions'] > 0 ? ($dash['total_clicks'] / $dash['total_impressions']) * 100 : 0;
        $topCampaigns = $contentData['top_campaigns'] ?? [];
    ?>
    
    <!-- 30 Days Summary -->
    <div class="mb-5 px-2">
        <h3 class="text-white/60 font-bold uppercase tracking-widest text-xs flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-400 mr-2"></span> Ringkasan 30 Hari Terakhir</h3>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto relative z-40 mb-10">
        <div class="bg-white/5 backdrop-blur-2xl rounded-[32px] p-8 shadow-2xl border border-white/10 text-center relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:scale-150 transition-transform"></div>
            <i class="ph-fill ph-currency-circle-dollar text-3xl text-blue-400 mb-4 block"></i>
            <p class="text-[10px] text-white/50 uppercase tracking-widest font-bold mb-2 relative z-10">Total Ads Spend</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight relative z-10"><?= format_rupiah($dash['total_spend']) ?></h3>
        </div>

        <div class="bg-white/5 backdrop-blur-2xl rounded-[32px] p-8 shadow-2xl border border-white/10 text-center relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:scale-150 transition-transform"></div>
            <i class="ph-fill ph-target text-3xl text-emerald-400 mb-4 block"></i>
            <p class="text-[10px] text-white/50 uppercase tracking-widest font-bold mb-2 relative z-10">Total Results (Konversi)</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight relative z-10"><?= format_number($dash['total_results']) ?></h3>
        </div>

        <div class="bg-white/5 backdrop-blur-2xl rounded-[32px] p-8 shadow-2xl border border-white/10 text-center relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:scale-150 transition-transform"></div>
            <i class="ph-fill ph-trend-down text-3xl text-purple-400 mb-4 block"></i>
            <p class="text-[10px] text-white/50 uppercase tracking-widest font-bold mb-2 relative z-10">Cost per Result (CPR)</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight relative z-10"><?= format_rupiah($cpr) ?></h3>
        </div>
        
        <div class="bg-white/5 backdrop-blur-2xl rounded-[32px] p-8 shadow-2xl border border-white/10 text-center relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-pink-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:scale-150 transition-transform"></div>
            <i class="ph-fill ph-cursor-click text-3xl text-pink-400 mb-4 block"></i>
            <p class="text-[10px] text-white/50 uppercase tracking-widest font-bold mb-2 relative z-10">Average CTR</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight relative z-10"><?= format_percentage($ctr) ?></h3>
        </div>
    </div>
    
    <!-- Top Campaigns Table -->
    <?php if(!empty($topCampaigns)): ?>
    <div class="bg-white/5 backdrop-blur-2xl rounded-[32px] shadow-2xl border border-white/10 overflow-hidden relative z-40 max-w-5xl mx-auto">
        <div class="px-8 py-6 border-b border-white/10 bg-black/20 flex items-center">
            <i class="ph-fill ph-medal text-2xl text-orange-400 mr-3 shadow-sm"></i>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white/90">Top 5 Kampanye Terbaik (Winning Campaigns)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-white/5 text-white/40 text-[10px] uppercase tracking-widest border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4">Nama Kampanye</th>
                        <th class="px-6 py-4 text-right">Ads Spend</th>
                        <th class="px-6 py-4 text-right">Results</th>
                        <th class="px-6 py-4 text-right">CPR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    <?php foreach($topCampaigns as $tc): ?>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-white/90 font-bold truncate max-w-[300px]"><i class="ph-fill ph-folder text-blue-400/50 mr-2"></i> <?= htmlspecialchars($tc['campaign_name']) ?></td>
                        <td class="px-6 py-4 text-right text-white/70 font-medium"><?= format_rupiah($tc['spend']) ?></td>
                        <td class="px-6 py-4 text-right text-emerald-400 font-bold"><?= format_number($tc['results']) ?></td>
                        <td class="px-6 py-4 text-right text-purple-300 font-bold"><?= format_rupiah($tc['cpr']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
<!-- ==========================================
     EMPTY STATE
     ========================================== -->
<?php else: ?>
    <div class="bg-white/5 backdrop-blur-2xl p-12 md:p-20 rounded-[32px] border border-white/10 text-center text-white/50 shadow-2xl relative z-40 max-w-3xl mx-auto flex flex-col items-center justify-center">
        <div class="w-24 h-24 rounded-full bg-black/20 border border-white/10 flex items-center justify-center mb-6 shadow-inner">
            <i class="ph-fill ph-file-dashed text-5xl text-white/30"></i>
        </div>
        <h2 class="text-xl font-bold text-white/80 mb-2">Laporan Belum Tersedia</h2>
        <p class="text-sm">Laporan untuk tautan ini belum tersedia, masih dalam tahap penyusunan (Draft), atau belum di-publish oleh tim operasional.</p>
    </div>
<?php endif; ?>