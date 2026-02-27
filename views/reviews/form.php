<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between relative z-50">
    <div class="flex items-center">
        <a href="<?= base_url('reviews') ?>" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all mr-4 shadow-sm text-decoration-none">
            <i class="ph-bold ph-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Editor Evaluasi Mingguan</h1>
            <p class="text-white/60 text-sm mt-0.5">
                Periode: <strong class="text-white"><?= date('d M Y', strtotime($review['week_start_date'])) ?> s/d <?= date('d M Y', strtotime($review['week_end_date'])) ?></strong>
            </p>
        </div>
    </div>
    <?php if($review['review_status'] == 'published'): ?>
        <div class="mt-4 sm:mt-0">
            <span class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 px-4 py-2 text-xs rounded-full font-bold uppercase tracking-wider flex items-center backdrop-blur-md shadow-lg">
                <i class="ph-fill ph-check-circle mr-2 text-base"></i> Telah Dipublish
            </span>
        </div>
    <?php endif; ?>
</div>

<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-[50px] bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300 flex items-center shadow-lg backdrop-blur-md relative z-40">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-40">
    
    <!-- KOLOM KIRI (Data Tarikan Otomatis) -->
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-white/5 backdrop-blur-xl p-6 rounded-[28px] border border-white/10 shadow-lg">
            <h3 class="font-bold text-white/90 mb-5 border-b border-white/10 pb-3 flex items-center text-sm uppercase tracking-wider">
                <i class="ph-bold ph-chart-polar mr-2 text-blue-400 text-lg"></i> Auto-Generated Metrics
            </h3>
            
            <div class="space-y-4">
                <?php 
                    $currentSection = '';
                    foreach($metrics as $m): 
                        if($currentSection != $m['section']) {
                            echo '<p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-6 mb-3 flex items-center"><span class="w-2 h-2 rounded-full bg-white/20 mr-2"></span>'. htmlspecialchars($m['section']) .'</p>';
                            $currentSection = $m['section'];
                        }
                ?>
                    <div class="flex justify-between items-center bg-black/20 p-4 rounded-[16px] border border-white/5 hover:bg-black/30 transition-colors">
                        <span class="text-xs text-white/60 font-medium"><?= htmlspecialchars($m['metric_label']) ?></span>
                        <span class="text-sm font-bold text-white tracking-tight">
                            <?php 
                                if(strpos($m['metric_key'], 'spend') !== false || strpos($m['metric_key'], 'cpr') !== false) {
                                    echo format_rupiah($m['metric_value']);
                                } elseif (strpos($m['metric_key'], 'ctr') !== false) {
                                    echo format_percentage($m['metric_value']);
                                } else {
                                    echo format_number($m['metric_value']);
                                }
                            ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-8 p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-200 text-xs flex items-start">
                <i class="ph-fill ph-info mr-2 text-lg text-blue-400"></i>
                <p class="leading-relaxed">Data ditarik otomatis dari database operasional pada saat report ini di-generate.</p>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN (Input Manual & Action Items) -->
    <div class="lg:col-span-8 space-y-6">
        <form action="<?= base_url('reviews/update') ?>" method="POST" class="bg-white/5 backdrop-blur-xl p-6 rounded-[28px] border border-white/10 shadow-lg flex flex-col h-full">
            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
            
            <h3 class="font-bold text-white/90 mb-6 border-b border-white/10 pb-3 flex items-center text-sm uppercase tracking-wider">
                <i class="ph-fill ph-brain mr-2 text-orange-400 text-lg"></i> Insight & Analisis Manual
            </h3>

            <div class="space-y-6 flex-grow">
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Executive Summary (Ringkasan Kinerja)</label>
                    <textarea name="summary_text" rows="3" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed' : '' ?>" placeholder="Contoh: Minggu ini performa Ads stabil, CPR turun 10% dibanding minggu lalu..." <?= $review['review_status'] == 'published' ? 'readonly' : '' ?>><?= htmlspecialchars($review['summary_text'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Kendala Utama (Top Issues)</label>
                        <textarea name="top_issues_text" rows="5" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed' : '' ?>" placeholder="Contoh: 1. Proses editing terhambat karena..." <?= $review['review_status'] == 'published' ? 'readonly' : '' ?>><?= htmlspecialchars($review['top_issues_text'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Creative Insights</label>
                        <textarea name="insights_text" rows="5" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed' : '' ?>" placeholder="Contoh: Angle testimoni bekerja paling baik dengan CTR 3.5%..." <?= $review['review_status'] == 'published' ? 'readonly' : '' ?>><?= htmlspecialchars($review['insights_text'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- FIX: DYNAMIC ACTION ITEMS -->
                <div class="pt-4 border-t border-white/10">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wide">Rencana Tindakan (Action Items)</label>
                        <?php if($review['review_status'] !== 'published'): ?>
                        <button type="button" onclick="addActionRow()" class="text-xs bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded-lg border border-white/10 transition-colors">
                            <i class="ph-bold ph-plus"></i> Tambah Baris
                        </button>
                        <?php endif; ?>
                    </div>
                    
                    <div id="action-items-container" class="space-y-3">
                        <?php if(empty($actionItems)): ?>
                            <!-- Baris Default Kosong jika belum ada -->
                            <div class="flex items-center gap-3 action-row">
                                <input type="text" name="action_titles[]" placeholder="Deskripsi tugas atau perbaikan..." class="flex-grow px-4 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed' : 'focus:border-white/30' ?>" <?= $review['review_status'] == 'published' ? 'readonly' : '' ?>>
                                <select name="action_owners[]" class="w-40 px-3 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none appearance-none <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed pointer-events-none' : 'focus:border-white/30' ?>">
                                    <option value="">PIC (Opsional)</option>
                                    <?php foreach($teamMembers as $tm): ?>
                                        <option value="<?= $tm['id'] ?>"><?= htmlspecialchars($tm['full_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="date" name="action_dues[]" class="w-36 px-3 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none [color-scheme:dark] <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed' : 'focus:border-white/30' ?>" <?= $review['review_status'] == 'published' ? 'readonly' : '' ?>>
                                <?php if($review['review_status'] !== 'published'): ?>
                                <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex items-center justify-center bg-red-500/10 text-red-400 rounded-xl border border-red-500/20 hover:bg-red-500/20 transition-colors shrink-0">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- Render Baris dari Database -->
                            <?php foreach($actionItems as $ai): ?>
                                <div class="flex items-center gap-3 action-row">
                                    <input type="text" name="action_titles[]" value="<?= htmlspecialchars($ai['title']) ?>" placeholder="Deskripsi tugas atau perbaikan..." class="flex-grow px-4 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed' : 'focus:border-white/30' ?>" <?= $review['review_status'] == 'published' ? 'readonly' : '' ?>>
                                    <select name="action_owners[]" class="w-40 px-3 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none appearance-none <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed pointer-events-none' : 'focus:border-white/30' ?>">
                                        <option value="">PIC (Opsional)</option>
                                        <?php foreach($teamMembers as $tm): ?>
                                            <option value="<?= $tm['id'] ?>" <?= $ai['owner_user_id'] == $tm['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tm['full_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="date" name="action_dues[]" value="<?= $ai['due_date'] ?>" class="w-36 px-3 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none [color-scheme:dark] <?= $review['review_status'] == 'published' ? 'opacity-60 cursor-not-allowed' : 'focus:border-white/30' ?>" <?= $review['review_status'] == 'published' ? 'readonly' : '' ?>>
                                    <?php if($review['review_status'] !== 'published'): ?>
                                    <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex items-center justify-center bg-red-500/10 text-red-400 rounded-xl border border-red-500/20 hover:bg-red-500/20 transition-colors shrink-0">
                                        <i class="ph-bold ph-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <?php if($review['review_status'] !== 'published'): ?>
            <div class="pt-6 mt-8 border-t border-white/10 flex flex-col sm:flex-row justify-end gap-4">
                <button type="submit" name="save_draft" class="px-6 py-3 bg-white/10 border border-white/20 rounded-full text-white hover:bg-white/20 transition-all font-semibold text-sm shadow-sm">
                    Simpan Draf Review
                </button>
                <button type="submit" name="publish" class="flex items-center justify-center px-6 py-3 bg-white text-black rounded-full font-bold shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:scale-105 active:scale-95 transition-all text-sm" onclick="return confirm('Setelah di-publish, evaluasi tidak dapat diedit lagi. Yakin ingin mem-publish?')">
                    <i class="ph-bold ph-paper-plane-tilt mr-2 text-lg"></i> Publish ke Client
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
// Fungsi Menambah Baris Rencana Tindakan Baru
function addActionRow() {
    const container = document.getElementById('action-items-container');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 action-row mt-3';
    
    row.innerHTML = `
        <input type="text" name="action_titles[]" placeholder="Deskripsi tugas atau perbaikan..." class="flex-grow px-4 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none focus:border-white/30">
        <select name="action_owners[]" class="w-40 px-3 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none appearance-none focus:border-white/30">
            <option value="">PIC (Opsional)</option>
            <?php foreach($teamMembers as $tm): ?>
                <option value="<?= $tm['id'] ?>"><?= htmlspecialchars($tm['full_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="action_dues[]" class="w-36 px-3 py-2.5 bg-black/30 border border-white/10 rounded-xl text-white text-sm outline-none focus:border-white/30 [color-scheme:dark]">
        <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex items-center justify-center bg-red-500/10 text-red-400 rounded-xl border border-red-500/20 hover:bg-red-500/20 transition-colors shrink-0">
            <i class="ph-bold ph-trash"></i>
        </button>
    `;
    container.appendChild(row);
}
</script>