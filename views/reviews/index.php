<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Evaluasi Mingguan (Weekly Review)</h1>
        <p class="text-white/60 text-sm mt-1">Laporan performa terpadu untuk Brand <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-[50px] bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300 flex items-center shadow-lg backdrop-blur-md relative z-40">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<?php if(isset($error_msg)): ?>
    <div class="mb-6 p-4 rounded-[50px] bg-red-500/10 border border-red-500/20 text-sm text-red-300 flex items-center shadow-lg backdrop-blur-md relative z-40">
        <i class="ph-fill ph-warning-circle text-xl mr-3 text-red-400"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-40">
    
    <!-- WIZARD GENERATOR KIRI -->
    <div class="lg:col-span-4">
        <div class="bg-white/5 backdrop-blur-xl p-6 rounded-[28px] border border-white/10 shadow-lg sticky top-6">
            <h3 class="font-bold text-white/90 mb-2 border-b border-white/10 pb-3 flex items-center text-sm uppercase tracking-wider">
                <i class="ph-fill ph-magic-wand mr-2 text-purple-400 text-lg"></i> Generate Review Baru
            </h3>
            <p class="text-xs text-white/50 mb-6 leading-relaxed">Sistem akan menarik data performa Ads, status pembuatan konten, SEO, dan tiket operasional secara otomatis.</p>
            
            <form action="<?= base_url('reviews/generate') ?>" method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Mulai Tanggal (Senin)</label>
                    <input type="date" name="week_start_date" value="<?= date('Y-m-d', strtotime('last monday')) ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm [color-scheme:dark]" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Sampai Tanggal (Minggu)</label>
                    <input type="date" name="week_end_date" value="<?= date('Y-m-d', strtotime('last sunday')) ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm [color-scheme:dark]" required>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                        Tarik Data Otomatis <i class="ph-bold ph-arrow-right ml-2 text-lg"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RIWAYAT REVIEW KANAN -->
    <div class="lg:col-span-8">
        <div class="bg-white/5 backdrop-blur-xl rounded-[28px] border border-white/10 shadow-lg overflow-hidden flex flex-col h-full">
            <div class="px-6 py-5 border-b border-white/10 bg-transparent">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider">Riwayat Laporan</h3>
            </div>
            
            <div class="divide-y divide-white/5 flex-grow">
                <?php if(empty($reviews)): ?>
                    <div class="p-16 text-center text-white/40 text-sm flex flex-col items-center justify-center h-full">
                        <i class="ph-fill ph-files text-5xl mb-3 opacity-30"></i>
                        <p>Belum ada evaluasi yang dibuat.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($reviews as $r): ?>
                        <div class="p-6 hover:bg-white/5 flex flex-col sm:flex-row sm:items-center justify-between transition-colors group">
                            <div class="mb-4 sm:mb-0">
                                <h4 class="text-base font-bold text-white mb-1.5 group-hover:text-purple-200 transition-colors">
                                    Periode: <?= date('d M', strtotime($r['week_start_date'])) ?> - <?= date('d M Y', strtotime($r['week_end_date'])) ?>
                                </h4>
                                <p class="text-xs text-white/50 flex items-center font-medium">
                                    <i class="ph-fill ph-user-circle mr-1.5 text-white/40 text-sm"></i> Dibuat oleh: <?= htmlspecialchars($r['generator_name']) ?>
                                </p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <?php if($r['review_status'] == 'published'): ?>
                                    <span class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 px-3 py-1 text-[10px] rounded-full font-bold uppercase tracking-wider backdrop-blur-md">Published</span>
                                    <a href="#" class="flex items-center justify-center w-9 h-9 bg-white/10 border border-white/20 rounded-full text-white hover:bg-white/20 transition-all shadow-sm" title="Client Link">
                                        <i class="ph-bold ph-link text-base"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="bg-orange-500/20 border border-orange-500/30 text-orange-300 px-3 py-1 text-[10px] rounded-full font-bold uppercase tracking-wider backdrop-blur-md">Draft</span>
                                    <a href="<?= base_url('reviews/edit?id=' . $r['id']) ?>" class="flex items-center px-4 py-2 bg-white/10 border border-white/20 rounded-full text-white text-xs font-semibold hover:bg-white/20 transition-all shadow-sm">
                                        <i class="ph-bold ph-pencil-simple mr-1.5"></i> Lanjutkan
                                    </a>
                                <?php endif; ?>

                                <!-- Tombol Hapus khusus Leader -->
                                <?php if($user['role_global'] === 'leader'): ?>
                                    <form action="<?= base_url('reviews/delete') ?>" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus evaluasi ini secara permanen?')">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button type="submit" class="flex items-center justify-center w-9 h-9 bg-red-500/10 border border-red-500/20 rounded-full text-red-400 hover:bg-red-500/20 transition-all shadow-sm" title="Hapus Laporan">
                                            <i class="ph-bold ph-trash text-sm"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>