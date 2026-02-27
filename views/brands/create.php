<div class="mb-6 flex items-center relative z-50">
    <a href="<?= base_url('dashboard') ?>" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all mr-4 shadow-sm text-decoration-none">
        <i class="ph-bold ph-arrow-left text-xl"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Setup Brand Baru</h1>
        <p class="text-white/60 text-sm mt-0.5">Tambahkan klien atau brand internal baru ke dalam workspace Anda.</p>
    </div>
</div>

<?php if(isset($error_msg)): ?>
    <div class="mb-6 p-4 rounded-[50px] bg-red-500/10 border border-red-500/20 text-sm text-red-300 flex items-center shadow-lg backdrop-blur-md relative z-40">
        <i class="ph-fill ph-warning-circle text-xl mr-3 text-red-400"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-40">
    
    <!-- KOLOM KIRI (Form Input) -->
    <div class="lg:col-span-7">
        <div class="bg-white/5 backdrop-blur-xl p-6 md:p-8 rounded-[28px] border border-white/10 shadow-lg">
            
            <form action="<?= base_url('brand/store') ?>" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Nama Brand / Klien <span class="text-red-400">*</span></label>
                    <input type="text" name="name" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" placeholder="Contoh: Skincare Beauty Glow" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Kategori Industri</label>
                    <input type="text" name="industry" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" placeholder="Contoh: F&B, Fashion, Klinik Kecantikan">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Mata Uang (Ads)</label>
                        <select name="currency" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="IDR" class="bg-gray-900 text-white">IDR (Rupiah)</option>
                            <option value="USD" class="bg-gray-900 text-white">USD (Dolar AS)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Zona Waktu Laporan</label>
                        <select name="timezone" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="Asia/Jakarta" class="bg-gray-900 text-white">Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" class="bg-gray-900 text-white">Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" class="bg-gray-900 text-white">Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-6 mt-4 border-t border-white/10">
                    <button type="submit" class="w-full py-3.5 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                        <i class="ph-bold ph-floppy-disk mr-2 text-lg"></i> Buat Brand Sekarang
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    <!-- KOLOM KANAN (Info Panel) -->
    <div class="lg:col-span-5 hidden lg:flex">
        <div class="w-full h-full p-10 flex flex-col justify-center items-center text-center bg-white/5 backdrop-blur-xl rounded-[28px] border border-white/10 shadow-lg relative overflow-hidden group">
            
            <!-- Soft Gradient Overlay on Hover -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 opacity-50 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <i class="ph-fill ph-rocket text-8xl text-white/20 mb-6 drop-shadow-2xl group-hover:text-white/40 group-hover:-translate-y-2 transition-all duration-500 relative z-10"></i>
            
            <h3 class="text-xl font-bold text-white mb-3 tracking-tight relative z-10">Pisahkan Pekerjaan Klien</h3>
            <p class="text-white/50 text-sm leading-relaxed px-4 relative z-10">
                Dengan menambahkan brand baru, semua aset Meta Ads, kalender konten, tiket operasional, dan laporan mingguan akan diisolasi. Tim Anda tidak akan kebingungan dengan data dari klien atau proyek lain.
            </p>
        </div>
    </div>
</div>