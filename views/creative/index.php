<?php
/**
 * ==============================================================================
 * HOOKJOURNEY - GROWTH OPS HUB
 * ==============================================================================
 * File       : views/creative/index.php
 * Deskripsi  : Antarmuka Creative Library (Gudang Aset Visual).
 * Fitur      : Smart Thumbnails, Glassmorphism Preview Modal, Direct Download.
 * ==============================================================================
 */
?>

<!-- ==============================================================
     INLINE CSS KHUSUS CREATIVE LIBRARY
     ============================================================== -->
<style>
    /* Efek transisi untuk Modal */
    #previewModal { transition: opacity 0.3s ease; }
    #modalContent { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    
    /* Overlay gradient pada thumbnail untuk memastikan teks/icon selalu terbaca */
    .thumb-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.4) 100%);
    }

    /* Style untuk form input agar konsisten dengan tema Glass */
    .glass-input {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .glass-input:focus {
        background: rgba(255, 255, 255, 0.1) !important;
        border-color: #ff007f !important;
        box-shadow: 0 0 0 3px rgba(255, 0, 127, 0.15) !important;
        outline: none;
    }
    .glass-input option { background: #0f0c29 !important; color: #fff !important; }
</style>

<!-- ==============================================================
     HEADER & ACTION BAR
     ============================================================== -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-20">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Creative Library</h1>
        <p class="text-white/60 text-sm mt-1">Gudang aset visual dan video Ads untuk <strong><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<!-- ==============================================================
     FLASH MESSAGES
     ============================================================== -->
<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border-emerald-500/20 text-sm text-emerald-300 flex items-center shadow-[0_0_15px_rgba(52,211,153,0.1)] backdrop-blur-md relative z-20">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>
<?php if(isset($error_msg)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border-red-500/20 text-sm text-red-300 flex items-center shadow-[0_0_15px_rgba(244,63,94,0.1)] backdrop-blur-md relative z-20">
        <i class="ph-fill ph-warning-circle text-xl mr-3 text-red-400"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<!-- ==============================================================
     MAIN CONTENT GRID
     ============================================================== -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8 relative z-20">
    
    <!-- KOLOM KIRI: UPLOAD FORM -->
    <div class="lg:col-span-1">
        <form action="<?= base_url('creative/store') ?>" method="POST" enctype="multipart/form-data" class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-6 rounded-[24px] sticky top-6">
            <h3 class="font-bold text-white/90 mb-5 border-b pb-3 flex items-center">
                <i class="ph ph-upload-simple text-blue-400 mr-2 text-lg"></i> Unggah Aset Baru
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-white/50 uppercase tracking-wider mb-1.5">Judul Aset *</label>
                    <input type="text" name="title" class="w-full px-4 py-2.5 glass-input text-sm" required placeholder="Contoh: Video Promo Lebaran">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-white/50 uppercase tracking-wider mb-1.5">Jenis Aset</label>
                    <select name="asset_type" class="w-full px-4 py-2.5 glass-input text-sm cursor-pointer">
                        <option value="video">Video (Reels/TikTok/Ads)</option>
                        <option value="image">Gambar (Single/Carousel)</option>
                        <option value="script_only">Dokumen / Script</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-white/50 uppercase tracking-wider mb-1.5">Pilih File *</label>
                    <div class="relative">
                        <input type="file" name="asset_file" class="w-full px-3 py-2 rounded-xl text-sm text-white/70 bg-white/5 cursor-pointer focus:outline-none file:mr-4 file:py-1.5 file:px-4 file:rounded-2xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all" required>
                    </div>
                    <p class="text-[10px] text-white/40 mt-2"><i class="ph ph-info mr-1"></i> Maks. 20MB (MP4, JPG, PNG)</p>
                </div>
                
                <button type="submit" class="w-full mt-2 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-bold hover: hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center">
                    <i class="ph ph-cloud-arrow-up mr-2 text-lg"></i> Simpan ke Library
                </button>
            </div>
        </form>
    </div>

    <!-- KOLOM KANAN: GRID ASSETS -->
    <div class="lg:col-span-3">
        <?php if(empty($assets)): ?>
            <div class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-12 rounded-[24px] text-center text-white/40 flex flex-col items-center justify-center min-h-[400px]">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4">
                    <i class="ph-fill ph-folder-open text-5xl text-white/30"></i>
                </div>
                <h3 class="text-lg font-bold text-white/70 mb-1">Library Masih Kosong</h3>
                <p class="text-sm">Belum ada aset kreatif yang diunggah untuk brand ini.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                <?php foreach($assets as $a): ?>
                    
                    <!-- KARTU ASET KREATIF -->
                    <div class="bg-[#121226]/80 backdrop-blur-md rounded-[20px] overflow-hidden group hover:shadow-[0_10px_30px_rgba(59,130,246,0.15)] transition-all duration-300 flex flex-col h-[280px]">
                        
                        <!-- Thumbnail Area (Klik untuk Preview) -->
                        <div class="relative h-44 w-full bg-black/40 overflow-hidden cursor-pointer flex-shrink-0 flex items-center justify-center" 
                             onclick="openPreview('<?= htmlspecialchars(addslashes($a['title'])) ?>', '<?= $a['asset_type'] ?>', '<?= $a['file_path'] ? base_url($a['file_path']) : '' ?>', '<?= htmlspecialchars(addslashes($a['uploader_name'] ?? 'Sistem')) ?>', '<?= date('d M Y, H:i', strtotime($a['created_at'])) ?>')">
                            
                            <!-- Smart Rendering Image/Video -->
                            <?php if($a['file_path']): ?>
                                <?php if($a['asset_type'] == 'image'): ?>
                                    <img src="<?= base_url($a['file_path']) ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" alt="<?= htmlspecialchars($a['title']) ?>">
                                
                                <?php elseif($a['asset_type'] == 'video'): ?>
                                    <!-- #t=0.1 trik untuk mengambil frame pertama video sebagai thumbnail di browser -->
                                    <video src="<?= base_url($a['file_path']) ?>#t=0.1" class="w-full h-full object-cover opacity-60 group-hover:opacity-80 group-hover:scale-105 transition-all duration-500" preload="metadata"></video>
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="w-12 h-12 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center group-hover:bg-blue-600/80 transition-colors">
                                            <i class="ph-fill ph-play text-white text-xl ml-1"></i>
                                        </div>
                                    </div>
                                    
                                <?php else: ?>
                                    <i class="ph-fill ph-file-text text-5xl text-white/20 group-hover:text-blue-400/50 transition-colors"></i>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Fallback jika tidak ada file (Misal URL Eksternal / Draft) -->
                                <i class="ph-fill ph-link text-5xl text-white/20"></i>
                            <?php endif; ?>

                            <!-- Overlay Gradient & Badges -->
                            <div class="absolute inset-0 thumb-overlay pointer-events-none"></div>
                            
                            <div class="absolute top-3 left-3">
                                <?php 
                                    $typeColor = 'bg-gray-500/80 text-white';
                                    if($a['asset_type'] == 'video') $typeColor = 'bg-purple-600/90 text-white  border-purple-400/50';
                                    if($a['asset_type'] == 'image') $typeColor = 'bg-emerald-600/90 text-white  border-emerald-400/50';
                                ?>
                                <span class="px-2.5 py-1 text-[9px] uppercase font-bold rounded-2xl backdrop-blur-md <?= $typeColor ?>">
                                    <?= htmlspecialchars($a['asset_type']) ?>
                                </span>
                            </div>

                            <!-- Tombol Download Cepat -->
                            <?php if($a['file_path']): ?>
                                <a href="<?= base_url($a['file_path']) ?>" target="_blank" download class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-white hover:bg-white hover:text-black transition-colors opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0" title="Download Aset" onclick="event.stopPropagation()">
                                    <i class="ph-bold ph-download-simple"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Card Info Area -->
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-white/90 text-sm leading-snug line-clamp-2 group-hover:text-blue-400 transition-colors" title="<?= htmlspecialchars($a['title']) ?>">
                                    <?= htmlspecialchars($a['title']) ?>
                                </h4>
                            </div>
                            
                            <div class="flex items-center justify-between mt-3 pt-3 border-t">
                                <div class="flex items-center text-xs text-white/50 font-medium">
                                    <div class="w-5 h-5 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-500 text-white flex items-center justify-center text-[8px] font-bold mr-2">
                                        <?= strtoupper(substr($a['uploader_name'] ?? 'S', 0, 1)) ?>
                                    </div>
                                    <span class="truncate max-w-[100px]"><?= htmlspecialchars(explode(' ', $a['uploader_name'] ?? 'Sistem')[0]) ?></span>
                                </div>
                                <span class="text-[10px] text-white/30"><?= date('d M Y', strtotime($a['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- ==============================================================
     MODAL PREVIEW ASET (LIGHTBOX)
     ============================================================== -->
<div id="previewModal" class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 sm:p-6 opacity-0">
    
    <!-- Modal Backdrop Blur -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md cursor-pointer" onclick="closePreview()"></div>
    
    <!-- Modal Content Box -->
    <div id="modalContent" class="relative z-10 w-full max-w-5xl bg-[#111126] rounded-3xl shadow-[0_25px_50px_rgba(0,0,0,0.5)] flex flex-col md:flex-row overflow-hidden scale-95 opacity-0 max-h-[90vh]">
        
        <!-- Tombol Close Melayang (Mobile & Desktop) -->
        <button onclick="closePreview()" class="absolute top-4 right-4 z-50 w-10 h-10 rounded-full bg-black/50 backdrop-blur-md text-white flex items-center justify-center hover:bg-red-500 transition-all">
            <i class="ph-bold ph-x text-xl"></i>
        </button>

        <!-- Media Area (Kiri / Atas) -->
        <div class="flex-1 bg-black/50 flex items-center justify-center relative min-h-[300px] md:min-h-[500px]" style="background-image: radial-gradient(circle at center, rgba(67,97,238,0.1) 0%, transparent 70%);">
            <div id="modalMediaContainer" class="w-full h-full flex items-center justify-center p-4">
                <!-- Media akan di-inject oleh JavaScript di sini -->
            </div>
        </div>

        <!-- Details Area (Kanan / Bawah) -->
        <div class="w-full md:w-80 p-6 sm:p-8 flex flex-col border-t md:border-t-0 md:border-l bg-[#1a1a35]">
            <div class="mb-6">
                <span id="modalTypeBadge" class="inline-block px-3 py-1 mb-3 text-[10px] uppercase font-bold rounded-2xl bg-blue-500/20 text-blue-300 border-blue-500/30 tracking-wider">
                    TYPE
                </span>
                <h2 id="modalTitle" class="text-xl font-bold text-white leading-snug">Judul Aset</h2>
            </div>
            
            <div class="space-y-4 flex-1">
                <div>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Diupload Oleh</p>
                    <p id="modalUploader" class="text-sm font-medium text-white/80 flex items-center">
                        <i class="ph-fill ph-user-circle mr-2 text-blue-400"></i> <span>Nama</span>
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Tanggal Upload</p>
                    <p id="modalDate" class="text-sm font-medium text-white/80 flex items-center">
                        <i class="ph-fill ph-calendar-blank mr-2 text-purple-400"></i> <span>Tanggal</span>
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi di Modal -->
            <div class="mt-8 pt-6 border-t flex flex-col gap-3">
                <a id="modalDownloadBtn" href="#" target="_blank" download class="w-full py-3 bg-white text-black rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors flex items-center justify-center">
                    <i class="ph-bold ph-download-simple mr-2 text-lg"></i> Unduh File Asli
                </a>
            </div>
        </div>

    </div>
</div>

<!-- ==============================================================
     CLIENT-SIDE INTERACTIVITY SCRIPTS
     ============================================================== -->
<script>
    /**
     * Membuka Lightbox Preview Modal
     */
    function openPreview(title, type, url, uploader, date) {
        if(!url) {
            alert('Aset ini belum memiliki file fisik untuk ditampilkan.');
            return;
        }

        const modal = document.getElementById('previewModal');
        const contentBox = document.getElementById('modalContent');
        const mediaContainer = document.getElementById('modalMediaContainer');
        const dlBtn = document.getElementById('modalDownloadBtn');
        
        // Set Teks Metadata
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalUploader').innerHTML = `<i class="ph-fill ph-user-circle mr-2 text-blue-400"></i> ${uploader}`;
        document.getElementById('modalDate').innerHTML = `<i class="ph-fill ph-calendar-blank mr-2 text-purple-400"></i> ${date}`;
        document.getElementById('modalTypeBadge').innerText = type;
        
        // Set Link Download
        dlBtn.href = url;

        // Reset dan Inject Media Baru
        mediaContainer.innerHTML = '';
        
        if (type === 'image') {
            mediaContainer.innerHTML = `<img src="${url}" class="max-w-full max-h-full object-contain rounded-xl drop-shadow-[0_0_30px_rgba(0,0,0,0.5)]">`;
        } else if (type === 'video') {
            mediaContainer.innerHTML = `<video src="${url}" controls autoplay class="max-w-full max-h-full rounded-xl drop-shadow-[0_0_30px_rgba(0,0,0,0.5)] outline-none"></video>`;
        } else {
            mediaContainer.innerHTML = `
                <div class="text-center text-white/50 flex flex-col items-center">
                    <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mb-4">
                        <i class="ph ph-file-text text-5xl"></i>
                    </div>
                    <p class="font-medium">Preview tidak tersedia untuk format dokumen.</p>
                    <p class="text-xs mt-1">Silakan klik tombol unduh untuk melihat isinya.</p>
                </div>`;
        }
        
        // Tampilkan Modal (Ubah display block)
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Memicu reflow agar transisi CSS berjalan
        void modal.offsetWidth;
        
        // Animate Masuk
        modal.classList.remove('opacity-0');
        contentBox.classList.remove('scale-95', 'opacity-0');
        contentBox.classList.add('scale-100', 'opacity-100');
    }

    /**
     * Menutup Lightbox Preview Modal
     */
    function closePreview() {
        const modal = document.getElementById('previewModal');
        const contentBox = document.getElementById('modalContent');
        const mediaContainer = document.getElementById('modalMediaContainer');
        
        // Animate Keluar
        modal.classList.add('opacity-0');
        contentBox.classList.remove('scale-100', 'opacity-100');
        contentBox.classList.add('scale-95', 'opacity-0');
        
        // Tunggu animasi selesai lalu sembunyikan sepenuhnya
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            mediaContainer.innerHTML = ''; // Hentikan video yang sedang berputar
        }, 300);
    }

    // Menutup modal jika tombol Escape / Esc ditekan
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closePreview();
        }
    });
</script>