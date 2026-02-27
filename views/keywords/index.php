<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">SEO Keyword Hub</h1>
        <p class="text-white/60 text-sm mt-1">Bank kata kunci (Keywords) untuk artikel dan Landing Page <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-[50px] bg-emerald-500/10 border-emerald-500/20 text-sm text-emerald-300 flex items-center backdrop-blur-md relative z-40">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-40">
    
    <!-- Form Tambah Cepat (Kiri) -->
    <div class="lg:col-span-4">
        <form action="<?= base_url('keywords/store') ?>" method="POST" class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-6 rounded-[28px] sticky top-6">
            <h3 class="font-bold text-white/90 mb-5 border-b pb-3 flex items-center text-sm uppercase tracking-wider">
                <i class="ph-bold ph-plus-circle mr-2 text-blue-400 text-lg"></i> Tambah Keyword
            </h3>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Keyword / Query <span class="text-red-400">*</span></label>
                    <input type="text" name="keyword_text" class="w-full px-4 py-3 bg-black/30 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus: outline-none transition-all text-sm placeholder-white/30" required placeholder="Contoh: jasa seo jakarta">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Volume</label>
                        <input type="number" name="volume" class="w-full px-4 py-3 bg-black/30 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus: outline-none transition-all text-sm placeholder-white/30" placeholder="e.g 1000">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Diff (KD)</label>
                        <input type="number" step="0.1" name="difficulty" class="w-full px-4 py-3 bg-black/30 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus: outline-none transition-all text-sm placeholder-white/30" placeholder="e.g 45">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Intent</label>
                    <select name="intent" class="w-full px-4 py-3 bg-black/30 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus: outline-none transition-all text-sm appearance-none cursor-pointer">
                        <option value="informational" class="bg-gray-900 text-white">Informational</option>
                        <option value="commercial" class="bg-gray-900 text-white">Commercial</option>
                        <option value="transactional" class="bg-gray-900 text-white">Transactional</option>
                        <option value="navigational" class="bg-gray-900 text-white">Navigational</option>
                    </select>
                </div>

                <!-- FIX: UI Tombol Cepat Tambah Cluster -->
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Cluster Topik (Opsional)</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <select name="cluster_id" class="w-full pl-4 pr-10 py-3 bg-black/30 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus: outline-none transition-all text-sm appearance-none cursor-pointer">
                                <option value="" class="bg-gray-900 text-white/50">-- Tanpa Cluster --</option>
                                <?php foreach($clusters as $cls): ?>
                                    <option value="<?= $cls['id'] ?>" class="bg-gray-900 text-white"><?= htmlspecialchars($cls['cluster_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i class="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-white/40 pointer-events-none"></i>
                        </div>
                        <button type="button" onclick="addCluster()" class="w-12 flex-shrink-0 flex items-center justify-center bg-blue-600/20 hover:bg-blue-600 border-blue-500/30 rounded-2xl text-blue-400 hover:text-white transition-all" title="Buat Cluster Baru">
                            <i class="ph-bold ph-plus text-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Prioritas</label>
                    <select name="priority" class="w-full px-4 py-3 bg-black/30 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus: outline-none transition-all text-sm appearance-none cursor-pointer">
                        <option value="high" class="bg-gray-900 text-white">Tinggi (High)</option>
                        <option value="medium" selected class="bg-gray-900 text-white">Sedang (Medium)</option>
                        <option value="low" class="bg-gray-900 text-white">Rendah (Low)</option>
                    </select>
                </div>
                
                <div class="pt-4 border-t mt-2">
                    <button type="submit" class="w-full py-3.5 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="ph-bold ph-floppy-disk mr-2 text-lg"></i> Simpan Keyword
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Data Keywords (Kanan) -->
    <div class="lg:col-span-8">
        <div class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[28px] overflow-hidden flex flex-col h-full min-h-[60vh]">
            <div class="px-6 py-5 border-b bg-transparent flex justify-between items-center">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider">Database Keyword</h3>
                <span class="text-xs font-medium bg-black/30 px-3 py-1 rounded-2xl text-white/50"><?= count($keywords) ?> Kata Kunci</span>
            </div>

            <div class="overflow-x-auto scrollbar-hide flex-grow">
                <table class="min-w-full divide-y divide-white/5">
                    <thead class="bg-black/20 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Keyword & Cluster</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Intent</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-white/50 uppercase tracking-widest">Volume</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-white/50 uppercase tracking-widest">KD</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-white/50 uppercase tracking-widest">Status Tracking</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-white/50 uppercase tracking-widest">Prioritas</th>
                            <?php if($user['role_global'] === 'leader'): ?>
                                <th class="px-6 py-4 text-center text-[10px] font-bold text-white/50 uppercase tracking-widest w-16">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-transparent">
                        <?php if(empty($keywords)): ?>
                            <tr>
                                <td colspan="<?= $user['role_global'] === 'leader' ? '7' : '6' ?>" class="px-6 py-20 text-center text-white/40 text-sm flex-col items-center">
                                    <i class="ph-fill ph-magnifying-glass text-5xl mb-3 opacity-30"></i>
                                    <p>Belum ada data keyword yang ditambahkan.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($keywords as $k): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-white/90 group-hover:text-blue-300 transition-colors"><?= htmlspecialchars($k['keyword_text']) ?></p>
                                    <?php if($k['cluster_name']): ?>
                                        <span class="text-[9px] text-white/50 uppercase font-bold tracking-widest mt-1.5 flex items-center"><i class="ph-fill ph-folder-open mr-1"></i> <?= htmlspecialchars($k['cluster_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-xs text-white/60 capitalize font-medium">
                                    <?php if($k['intent'] == 'informational'): ?>
                                        <i class="ph-fill ph-info text-blue-400 mr-1"></i>
                                    <?php elseif($k['intent'] == 'commercial'): ?>
                                        <i class="ph-fill ph-shopping-cart text-orange-400 mr-1"></i>
                                    <?php elseif($k['intent'] == 'transactional'): ?>
                                        <i class="ph-fill ph-credit-card text-emerald-400 mr-1"></i>
                                    <?php elseif($k['intent'] == 'navigational'): ?>
                                        <i class="ph-fill ph-compass text-purple-400 mr-1"></i>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                    <?= $k['intent'] ?? '-' ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-white/80 text-right font-medium"><?= $k['volume'] ? number_format($k['volume'], 0, ',', '.') : '-' ?></td>
                                <td class="px-6 py-4 text-sm text-white/80 text-right font-medium">
                                    <?php if($k['difficulty']): ?>
                                        <span class="<?= $k['difficulty'] > 70 ? 'text-red-400 font-bold' : ($k['difficulty'] > 30 ? 'text-orange-400 font-bold' : 'text-emerald-400 font-bold') ?>">
                                            <?= $k['difficulty'] ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <?php 
                                        $sColor = [
                                            'new' => 'bg-white/10 text-white/80 ',
                                            'planned' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                            'used' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                            'won' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                            'ignored' => 'bg-gray-500/20 text-gray-400 '
                                        ][$k['keyword_status']] ?? 'bg-white/10 text-white/80 ';
                                    ?>
                                    <div class="relative inline-flex items-center">
                                        <select onchange="updateKwStatus(<?= $k['id'] ?>, this.value)" class="appearance-none bg-transparent text-[9px] font-bold uppercase tracking-wider py-1 pl-3 pr-7 rounded-full cursor-pointer focus:outline-none <?= $sColor ?> backdrop-blur-md transition-all">
                                            <option value="new" class="bg-gray-900 text-white" <?= $k['keyword_status'] == 'new' ? 'selected' : '' ?>>NEW</option>
                                            <option value="planned" class="bg-gray-900 text-white" <?= $k['keyword_status'] == 'planned' ? 'selected' : '' ?>>PLANNED</option>
                                            <option value="used" class="bg-gray-900 text-white" <?= $k['keyword_status'] == 'used' ? 'selected' : '' ?>>USED (CONTENT)</option>
                                            <option value="won" class="bg-gray-900 text-white" <?= $k['keyword_status'] == 'won' ? 'selected' : '' ?>>WON (PAGE 1)</option>
                                            <option value="ignored" class="bg-gray-900 text-white" <?= $k['keyword_status'] == 'ignored' ? 'selected' : '' ?>>IGNORED</option>
                                        </select>
                                        <i class="ph-bold ph-caret-down absolute right-2.5 pointer-events-none text-[10px] opacity-70"></i>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <?php 
                                        $pColor = 'text-white/50';
                                        if($k['priority'] == 'high') $pColor = 'text-red-400 drop-shadow-[0_0_8px_rgba(248,113,113,0.5)]';
                                        if($k['priority'] == 'medium') $pColor = 'text-orange-400';
                                    ?>
                                    <span class="text-[10px] font-bold <?= $pColor ?> uppercase tracking-widest"><?= $k['priority'] ?></span>
                                </td>

                                <?php if($user['role_global'] === 'leader'): ?>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <form action="<?= base_url('keywords/delete') ?>" method="POST" class="m-0 inline-block" onsubmit="return confirm('Hapus keyword ini?')">
                                        <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                        <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-2xl bg-red-500/10 hover:bg-red-500/20 border-red-500/20 text-red-400 transition-colors">
                                            <i class="ph-bold ph-trash text-sm"></i>
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
    function updateKwStatus(kwId, status) {
        fetch('<?= base_url('keywords/update-status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${kwId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.reload(); 
            } else {
                alert('Sistem: Gagal mengubah status keyword.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Kesalahan koneksi ke server.');
        });
    }

    // FIX: AJAX untuk Menambah Cluster Langsung
    function addCluster() {
        const clusterName = prompt("Masukkan nama Cluster baru (Contoh: Produk A, SEO Edukasi):");
        
        if (clusterName && clusterName.trim() !== "") {
            const formData = new URLSearchParams();
            formData.append('cluster_name', clusterName.trim());

            fetch('<?= base_url('keywords/store-cluster') ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload(); // Reload untuk merefresh daftar dropdown
                } else {
                    alert('Sistem: Gagal menambahkan cluster. ' + (data.error || ''));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat menambah cluster.');
            });
        }
    }
</script>