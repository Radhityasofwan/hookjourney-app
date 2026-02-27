<!-- ==============================================================
     HEADER & TITLE AREA
     ============================================================== -->
<div class="mb-6 relative z-20 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Import Report Meta Ads</h1>
        <p class="text-white/60 text-sm mt-1">Unggah file CSV dari Facebook Ads Manager untuk Brand <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="<?= base_url('ads/performance') ?>" class="btn px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center transition-all hover:scale-105" 
           style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: white; backdrop-filter: blur(10px);">
            <i class="ph ph-chart-line-up mr-2 text-lg"></i> Lihat Performa
        </a>
    </div>
</div>

<!-- ==============================================================
     FLASH MESSAGES (ALERTS)
     ============================================================== -->
<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border-emerald-500/20 text-sm text-emerald-300 flex items-center shadow-[0_0_15px_rgba(52,211,153,0.1)] backdrop-blur-md relative z-20">
        <i class="ph-fill ph-check-circle text-2xl mr-3 text-emerald-400"></i> 
        <span class="font-medium"><?= htmlspecialchars($success_msg) ?></span>
    </div>
<?php endif; ?>

<?php if(isset($error_msg)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border-red-500/20 text-sm text-red-300 flex items-center shadow-[0_0_15px_rgba(244,63,94,0.1)] backdrop-blur-md relative z-20">
        <i class="ph-fill ph-warning-circle text-2xl mr-3 text-red-400"></i> 
        <span class="font-medium"><?= htmlspecialchars($error_msg) ?></span>
    </div>
<?php endif; ?>

<!-- ==============================================================
     MAIN CONTENT GRID
     ============================================================== -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 relative z-20">
    
    <!-- KOLOM KIRI: FORM UPLOAD (GLASS CARD) -->
    <div class="xl:col-span-1">
        <div class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[24px] p-6 lg:p-8 relative overflow-hidden group">
            <!-- Subtle Glow Effect in the background of the card -->
            <div class="absolute top-0 left-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl -ml-10 -mt-10 transition-transform group-hover:scale-150 pointer-events-none"></div>

            <h3 class="font-bold text-white/90 mb-6 border-b pb-3 flex items-center">
                <i class="ph ph-upload-simple text-blue-400 mr-2 text-xl"></i> Unggah File Baru
            </h3>
            
            <form action="<?= base_url('ads/import/upload') ?>" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
                
                <!-- Pilih Ad Account -->
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-2">Pilih Ad Account</label>
                    <div class="relative">
                        <i class="ph ph-briefcase absolute left-4 top-1/2 -translate-y-1/2 text-white/40 text-lg pointer-events-none"></i>
                        <select name="ad_account_id" class="w-full pl-11 pr-4 py-3 bg-black/30 rounded-2xl text-white focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 outline-none transition-all appearance-none cursor-pointer" required>
                            <option value="" class="bg-[#0f0c29] text-white">-- Pilih Akun Meta --</option>
                            <?php foreach($adAccounts as $acc): ?>
                                <option value="<?= $acc['id'] ?>" class="bg-[#0f0c29] text-white">
                                    <?= htmlspecialchars($acc['account_name']) ?> <?= $acc['account_id_external'] ? '('.$acc['account_id_external'].')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-white/40 pointer-events-none"></i>
                    </div>
                    <?php if(empty($adAccounts)): ?>
                        <p class="text-xs text-orange-400 mt-2 flex items-center"><i class="ph ph-warning mr-1"></i> Belum ada Ad Account aktif.</p>
                    <?php endif; ?>
                </div>

                <!-- Dropzone Area -->
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-2">File Report (CSV Format)</label>
                    
                    <div class="mt-1 flex justify-center px-6 pt-8 pb-8 border-2 rounded-[20px] bg-white/5 hover:bg-white/10 transition-all relative group/dropzone cursor-pointer" onclick="document.getElementById('file-upload').click()">
                        <div class="space-y-2 text-center">
                            <div class="w-16 h-16 rounded-full bg-blue-500/10 flex items-center justify-center mx-auto mb-3 group-hover/dropzone:scale-110 group-hover/dropzone:bg-blue-500/20 transition-all">
                                <i class="ph-fill ph-file-csv text-3xl text-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.5)]"></i>
                            </div>
                            
                            <div class="flex flex-col text-sm text-white/60 justify-center items-center mt-3">
                                <label for="file-upload" class="relative cursor-pointer rounded-2xl font-semibold text-blue-400 hover:text-blue-300 focus-within:outline-none transition-colors">
                                    <span>Pilih File dari Perangkat</span>
                                    <input id="file-upload" name="report_file" type="file" class="sr-only" accept=".csv" required>
                                </label>
                                <p class="mt-1 text-xs text-white/40">atau drag and drop file ke area ini</p>
                            </div>
                            
                            <div id="file-name-display" class="mt-4 pt-3 border-t text-xs text-white/30 font-medium tracking-wide">
                                MAKSIMAL UKURAN 10MB
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-[0.98] transition-all disabled:opacity-50 disabled:active:scale-100 disabled:cursor-not-allowed" <?= empty($adAccounts) ? 'disabled' : '' ?>>
                    <i class="ph ph-rocket-launch mr-2 text-lg"></i> Mulai Proses Import
                </button>
            </form>
        </div>
    </div>

    <!-- KOLOM KANAN: RIWAYAT IMPORT (TABLE) -->
    <div class="xl:col-span-2">
        <div class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[24px] flex flex-col h-full overflow-hidden">
            
            <div class="px-6 py-5 border-b bg-white/5 flex items-center justify-between">
                <h3 class="font-bold text-white/90">Riwayat Import Terakhir</h3>
                <span class="text-xs font-medium px-2.5 py-1 bg-white/10 rounded-2xl text-white/60">10 Data Terakhir</span>
            </div>
            
            <div class="overflow-x-auto scrollbar-hide flex-1">
                <table class="w-full text-left whitespace-nowrap min-w-[700px]">
                    <thead class="bg-black/20">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-white/50 uppercase tracking-wider border-b">Waktu Diunggah</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-white/50 uppercase tracking-wider border-b">Ad Account</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-white/50 uppercase tracking-wider border-b">File Report</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-white/50 uppercase tracking-wider border-b text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-white/50 uppercase tracking-wider border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-transparent">
                        <?php if(empty($importHistory)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-white/30 text-sm bg-black/10">
                                <i class="ph ph-clock-counter-clockwise text-5xl mb-3 block opacity-40"></i>
                                <p class="font-medium text-white/60">Belum ada riwayat import.</p>
                                <p class="text-xs mt-1">Data yang Anda unggah akan muncul di sini.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($importHistory as $row): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 text-sm text-white/80 font-medium align-middle">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    <span class="text-white/40 ml-1 text-xs"><?= date('H:i', strtotime($row['created_at'])) ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-white/70 align-middle">
                                    <div class="flex items-center">
                                        <i class="ph-fill ph-briefcase text-blue-400/50 mr-2 text-lg"></i>
                                        <?= htmlspecialchars($row['account_name'] ?? 'Akun Dihapus') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-white/70 align-middle">
                                    <div class="flex items-center">
                                        <i class="ph-fill ph-file-csv text-emerald-400/50 mr-2 text-xl"></i>
                                        <span class="truncate max-w-[180px] font-medium" title="<?= htmlspecialchars($row['file_name']) ?>">
                                            <?= htmlspecialchars($row['file_name']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center align-middle">
                                    <?php 
                                        $badgeClass = 'bg-white/5   text-white/50'; 
                                        $icon = 'ph-upload-simple';
                                        $label = 'Uploaded';

                                        if ($row['import_status'] == 'processing') {
                                            $badgeClass = 'bg-blue-500/20  border-blue-500/30 text-blue-300 shadow-[0_0_10px_rgba(59,130,246,0.2)]';
                                            $icon = 'ph-spinner-gap animate-spin';
                                            $label = 'Processing';
                                        } elseif ($row['import_status'] == 'done') {
                                            $badgeClass = 'bg-emerald-500/20  border-emerald-500/30 text-emerald-300 shadow-[0_0_10px_rgba(52,211,153,0.2)]';
                                            $icon = 'ph-check-circle';
                                            $label = 'Selesai';
                                        } elseif ($row['import_status'] == 'failed') {
                                            $badgeClass = 'bg-red-500/20  border-red-500/30 text-red-300';
                                            $icon = 'ph-warning-circle';
                                            $label = 'Gagal';
                                        }
                                    ?>
                                    <span class="px-3 py-1.5 inline-flex items-center text-[10.5px] font-bold rounded-full uppercase tracking-wider backdrop-blur-md <?= $badgeClass ?>" title="<?= htmlspecialchars($row['error_log'] ?? '') ?>">
                                        <i class="ph <?= $icon ?> mr-1.5 text-sm"></i> <?= $label ?>
                                    </span>

                                    <?php if(!empty($row['error_log']) && $row['import_status'] !== 'uploaded'): ?>
                                        <div class="mt-2 text-[10px] <?= $row['import_status'] == 'failed' ? 'text-red-400 bg-red-900/20 border-red-500/20' : 'text-emerald-400 bg-emerald-900/20 border-emerald-500/20' ?> p-2.5 rounded text-left leading-relaxed whitespace-normal max-w-[220px] mx-auto">
                                            <?= htmlspecialchars($row['error_log']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- FIX: Kolom Tambahan (Aksi Delete) -->
                                <td class="px-6 py-4 text-center align-middle">
                                    <form action="<?= base_url('ads/import/delete') ?>" method="POST" class="m-0" onsubmit="return confirm('Peringatan: Menghapus riwayat ini juga akan menghapus SEMUA Data Performa Iklan yang berasal dari file CSV ini. Yakin ingin melanjutkan? (Anda bisa unggah ulang nantinya)')">
                                        <input type="hidden" name="import_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 px-2.5 py-2 rounded-2xl transition-colors border-red-500/20 flex items-center justify-center mx-auto" title="Hapus Riwayat & Data Performa">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    </form>
                                </td>
                                
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ==============================================================
     CLIENT-SIDE INTERACTIVITY SCRIPTS
     ============================================================== -->
<script>
    // 1. Script Tampilan Dropzone File
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const fileNameDisplay = document.getElementById('file-name-display');
        
        if(e.target.files.length > 0) {
            const fileName = e.target.files[0].name;
            const fileSize = (e.target.files[0].size / (1024 * 1024)).toFixed(2); // Convert to MB
            
            // Tampilkan nama file dengan styling aksen biru/emerald
            fileNameDisplay.innerHTML = `
                <div class="flex flex-col items-center animate-in fade-in zoom-in duration-300">
                    <span class="font-bold text-blue-400 drop- text-sm">${fileName}</span>
                    <span class="text-[10px] text-white/50 mt-1 uppercase tracking-widest">${fileSize} MB • Siap Diunggah</span>
                </div>
            `;
        } else {
            // Revert ke default jika batal milih file
            fileNameDisplay.innerHTML = "MAKSIMAL UKURAN 10MB";
        }
    });

    // 2. AUTO-TRIGGER QUEUE ENGINE
    <?php if(isset($hasPending) && $hasPending): ?>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[System] Mendeteksi data "Uploaded", menjalankan proses background...');
        
        fetch('<?= base_url('ads/import/process') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                console.log('[System] Data berhasil diproses.');
                // Refresh halaman agar label berubah
                window.location.reload(); 
            }
        })
        .catch(err => {
            console.error('[Error] Gagal menjalankan trigger antrean:', err);
        });
    });
    <?php endif; ?>
</script>