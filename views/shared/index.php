<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Akses Klien (Client Portal)</h1>
        <p class="text-white/60 text-sm mt-1">Buat tautan aman untuk membagikan laporan ke klien <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-[50px] bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300 flex items-center shadow-lg backdrop-blur-md relative z-40">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-40">
    
    <!-- Form Generate Link (Kiri) -->
    <div class="lg:col-span-4">
        <form action="<?= base_url('share-links/generate') ?>" method="POST" class="bg-white/5 backdrop-blur-xl p-6 rounded-[28px] border border-white/10 shadow-lg sticky top-6">
            <h3 class="font-bold text-white/90 mb-5 border-b border-white/10 pb-3 flex items-center text-sm uppercase tracking-wider">
                <i class="ph-bold ph-link mr-2 text-blue-400 text-lg"></i> Buat Tautan Baru
            </h3>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Modul yang Dibagikan</label>
                    <select name="module_key" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer">
                        <option value="weekly_review" class="bg-gray-900 text-white">Laporan Mingguan (Weekly Review)</option>
                        <option value="dashboard" class="bg-gray-900 text-white">Dashboard Ads Performa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Berlaku Hingga (Opsional)</label>
                    <input type="date" name="expires_at" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm [color-scheme:dark]">
                    <p class="text-[10px] text-white/40 mt-2 flex items-center"><i class="ph-fill ph-info mr-1"></i> Biarkan kosong agar tautan aktif selamanya.</p>
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                        <i class="ph-bold ph-magic-wand mr-2 text-lg"></i> Generate Link
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Data Link (Kanan) -->
    <div class="lg:col-span-8">
        <div class="bg-white/5 backdrop-blur-xl rounded-[28px] border border-white/10 shadow-lg overflow-hidden h-full flex flex-col">
            <div class="px-6 py-5 border-b border-white/10 bg-transparent flex justify-between items-center">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider">Daftar Akses Aktif</h3>
                <span class="text-xs font-medium bg-black/30 px-3 py-1 rounded-lg text-white/50 border border-white/5"><?= count($links) ?> Tautan</span>
            </div>
            
            <div class="overflow-x-auto scrollbar-hide flex-grow">
                <table class="min-w-full divide-y divide-white/5">
                    <thead class="bg-black/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Modul Target</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Tautan Bagikan (Link URL)</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Kedaluwarsa</th>
                            <?php if($user['role_global'] === 'leader'): ?>
                                <th class="px-6 py-4 text-center text-[10px] font-bold text-white/50 uppercase tracking-widest w-16">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-transparent">
                        <?php if(empty($links)): ?>
                            <tr>
                                <td colspan="<?= $user['role_global'] === 'leader' ? '4' : '3' ?>" class="px-6 py-20 text-center text-white/40 text-sm flex-col items-center">
                                    <i class="ph-fill ph-link-break text-5xl mb-3 opacity-30 block"></i>
                                    <p>Belum ada link akses yang dibuat.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($links as $l): ?>
                                <?php $url = base_url('shared?token=' . $l['token']); ?>
                                <tr class="hover:bg-white/5 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-bold text-white/90 capitalize whitespace-nowrap group-hover:text-blue-300 transition-colors">
                                        <?= str_replace('_', ' ', $l['module_key']) ?>
                                        <p class="text-[9px] text-white/40 mt-1 uppercase tracking-widest font-medium font-sans">Oleh: <?= htmlspecialchars($l['creator_name']) ?></p>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex items-center w-full min-w-[280px]">
                                            <input type="text" value="<?= $url ?>" class="bg-black/30 border border-white/10 text-xs text-white/70 rounded-xl px-3 py-2.5 w-full outline-none focus:text-white transition-colors cursor-text" readonly id="link-<?= $l['id'] ?>">
                                            
                                            <button type="button" onclick="copyLink(this, '<?= $url ?>')" class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all shadow-sm ml-2" title="Salin Tautan">
                                                <i class="ph-bold ph-copy text-sm"></i>
                                            </button>
                                            
                                            <a href="<?= $url ?>" target="_blank" class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 transition-all shadow-sm ml-2" title="Kunjungi Tautan">
                                                <i class="ph-bold ph-arrow-square-out text-sm"></i>
                                            </a>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-xs font-medium text-white/50 whitespace-nowrap">
                                        <?php if($l['expires_at']): ?>
                                            <span class="flex items-center text-orange-300 bg-orange-500/10 px-2.5 py-1 rounded-full border border-orange-500/20 backdrop-blur-md w-max">
                                                <i class="ph-fill ph-clock mr-1.5"></i> <?= date('d M Y', strtotime($l['expires_at'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="flex items-center text-emerald-300 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20 backdrop-blur-md w-max">
                                                <i class="ph-fill ph-infinity mr-1.5"></i> Selamanya
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Tombol Cabut Tautan -->
                                    <?php if($user['role_global'] === 'leader'): ?>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <form action="<?= base_url('share-links/delete') ?>" method="POST" class="m-0 inline-block" onsubmit="return confirm('Cabut tautan ini? Klien tidak akan bisa mengaksesnya lagi.')">
                                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                            <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-400 transition-colors shadow-sm" title="Cabut Akses">
                                                <i class="ph-bold ph-link-break text-sm"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function copyLink(btn, url) {
        // Fallback robust untuk menyalin teks (Mendukung iFrame)
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        
        // Animasi Sukses Copy
        const icon = btn.querySelector('i');
        icon.className = 'ph-bold ph-check text-emerald-400 text-lg';
        btn.classList.replace('bg-white/10', 'bg-emerald-500/20');
        btn.classList.add('border', 'border-emerald-500/30');
        
        setTimeout(() => { 
            icon.className = 'ph-bold ph-copy text-sm text-white'; 
            btn.classList.replace('bg-emerald-500/20', 'bg-white/10');
            btn.classList.remove('border', 'border-emerald-500/30');
        }, 2000);
    }
</script>