<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Pengaturan Brand</h1>
        <p class="text-white/60 text-sm mt-1">Kelola preferensi dan master data untuk <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
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

<!-- FIX: Menggunakan Grid yang responsif untuk 4 kartu -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 relative z-40">
    
    <!-- ==============================================
         1. KOLOM: AD ACCOUNTS (META)
         ============================================== -->
    <div class="space-y-6">
        <div class="bg-white/5 backdrop-blur-xl p-6 md:p-8 rounded-[28px] border border-white/10 shadow-lg h-full flex flex-col">
            <div class="flex items-center justify-between mb-5 border-b border-white/10 pb-4">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider flex items-center">
                    <i class="ph-fill ph-megaphone mr-2 text-blue-400 text-lg"></i> Akun Iklan (Meta Ads)
                </h3>
                <span class="px-3 py-1 text-[10px] font-bold rounded-full border bg-blue-500/20 text-blue-300 border-blue-500/30 backdrop-blur-md tracking-wider">
                    <?= count($adAccounts) ?> Akun
                </span>
            </div>
            
            <div class="space-y-3 mb-6 max-h-[220px] overflow-y-auto pr-2 scrollbar-hide flex-grow">
                <?php if(empty($adAccounts)): ?>
                    <div class="p-8 text-center border border-dashed border-white/20 rounded-[20px] bg-white/5 text-white/40">
                        <i class="ph-fill ph-empty text-3xl mb-2 opacity-50 block"></i>
                        <p class="text-xs font-medium">Belum ada Ad Account tertaut.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($adAccounts as $acc): ?>
                        <div class="flex items-center justify-between p-4 bg-black/20 hover:bg-white/5 transition-colors rounded-[20px] border border-white/5 group">
                            <div>
                                <p class="text-sm font-semibold text-white/90 flex items-center group-hover:text-white transition-colors">
                                    <i class="ph-fill ph-meta-logo text-blue-400 mr-2 text-lg drop-shadow-md"></i> <?= htmlspecialchars($acc['account_name']) ?>
                                </p>
                                <?php if($acc['account_id_external']): ?>
                                    <p class="text-[11px] text-white/40 mt-1 ml-7 font-mono tracking-wide">ID: <?= htmlspecialchars($acc['account_id_external']) ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="text-[9px] uppercase font-bold text-white/60 bg-white/10 border border-white/10 px-2.5 py-1 rounded-full backdrop-blur-md">
                                <?= $acc['currency'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form action="<?= base_url('brand/ad-account/store') ?>" method="POST" class="bg-black/20 p-5 rounded-[24px] border border-white/5 mt-auto">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-4 flex items-center">
                    <i class="ph-bold ph-plus-circle mr-1.5"></i> Tambah Akun Baru
                </p>
                <div class="space-y-3 mb-4">
                    <input type="text" name="account_name" placeholder="Nama Akun Iklan *" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" required>
                    <input type="text" name="account_id_external" placeholder="Meta Account ID (Opsional)" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 font-mono">
                    <select name="currency" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer">
                        <option value="IDR" class="bg-gray-900 text-white">IDR (Rupiah)</option>
                        <option value="USD" class="bg-gray-900 text-white">USD (Dollar)</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                    Simpan Akun
                </button>
            </form>
        </div>
    </div>

    <!-- ==============================================
         2. KOLOM: CONTENT PILLARS
         ============================================== -->
    <div class="space-y-6">
        <div class="bg-white/5 backdrop-blur-xl p-6 md:p-8 rounded-[28px] border border-white/10 shadow-lg h-full flex flex-col">
            <div class="flex items-center justify-between mb-5 border-b border-white/10 pb-4">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider flex items-center">
                    <i class="ph-fill ph-hash mr-2 text-purple-400 text-lg"></i> Pilar Konten (Pillars)
                </h3>
                <span class="px-3 py-1 text-[10px] font-bold rounded-full border bg-purple-500/20 text-purple-300 border-purple-500/30 backdrop-blur-md tracking-wider">
                    <?= count($pillars) ?> Pilar
                </span>
            </div>
            
            <div class="space-y-3 mb-6 max-h-[220px] overflow-y-auto pr-2 scrollbar-hide flex-grow">
                <?php if(empty($pillars)): ?>
                    <div class="p-8 text-center border border-dashed border-white/20 rounded-[20px] bg-white/5 text-white/40">
                        <i class="ph-fill ph-empty text-3xl mb-2 opacity-50 block"></i>
                        <p class="text-xs font-medium">Belum ada Pilar Konten.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($pillars as $p): ?>
                        <div class="p-4 bg-black/20 hover:bg-white/5 transition-colors rounded-[20px] border border-white/5 group">
                            <p class="text-sm font-semibold text-white/90 flex items-center group-hover:text-white transition-colors">
                                <span class="w-6 h-6 rounded-full bg-purple-500/20 flex items-center justify-center mr-2 text-purple-400 text-xs shrink-0"><i class="ph-bold ph-hash"></i></span>
                                <?= htmlspecialchars($p['name']) ?>
                            </p>
                            <?php if($p['description']): ?>
                                <p class="text-[11px] text-white/50 mt-2 ml-8 leading-relaxed"><?= htmlspecialchars($p['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form action="<?= base_url('brand/pillar/store') ?>" method="POST" class="bg-black/20 p-5 rounded-[24px] border border-white/5 mt-auto">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-4 flex items-center">
                    <i class="ph-bold ph-plus-circle mr-1.5"></i> Tambah Pilar Baru
                </p>
                <div class="space-y-3 mb-4">
                    <input type="text" name="name" placeholder="Nama Pilar (Contoh: Edukasi) *" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" required>
                    <textarea name="description" rows="2" placeholder="Deskripsi singkat (Opsional)" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none"></textarea>
                </div>
                <button type="submit" class="w-full py-3 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                    Simpan Pilar
                </button>
            </form>
        </div>
    </div>

    <!-- ==============================================
         3. KOLOM: TICKET CATEGORIES (NEW)
         ============================================== -->
    <div class="space-y-6">
        <div class="bg-white/5 backdrop-blur-xl p-6 md:p-8 rounded-[28px] border border-white/10 shadow-lg h-full flex flex-col">
            <div class="flex items-center justify-between mb-5 border-b border-white/10 pb-4">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider flex items-center">
                    <i class="ph-fill ph-ticket mr-2 text-emerald-400 text-lg"></i> Kategori Tugas / Tiket
                </h3>
                <span class="px-3 py-1 text-[10px] font-bold rounded-full border bg-emerald-500/20 text-emerald-300 border-emerald-500/30 backdrop-blur-md tracking-wider">
                    <?= count($ticketCategories) ?> Kategori
                </span>
            </div>
            
            <div class="space-y-3 mb-6 max-h-[220px] overflow-y-auto pr-2 scrollbar-hide flex-grow">
                <?php if(empty($ticketCategories)): ?>
                    <div class="p-8 text-center border border-dashed border-white/20 rounded-[20px] bg-white/5 text-white/40">
                        <i class="ph-fill ph-empty text-3xl mb-2 opacity-50 block"></i>
                        <p class="text-xs font-medium">Belum ada Kategori Tugas.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($ticketCategories as $tc): ?>
                        <div class="p-4 bg-black/20 hover:bg-white/5 transition-colors rounded-[20px] border border-white/5 flex items-center group">
                            <div class="w-3 h-3 rounded-full mr-3 shadow-sm" style="background-color: <?= htmlspecialchars($tc['color_hex'] ?: '#3b82f6') ?>;"></div>
                            <p class="text-sm font-semibold text-white/90 group-hover:text-white transition-colors">
                                <?= htmlspecialchars($tc['name']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form action="<?= base_url('brand/ticket-category/store') ?>" method="POST" class="bg-black/20 p-5 rounded-[24px] border border-white/5 mt-auto">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-4 flex items-center">
                    <i class="ph-bold ph-plus-circle mr-1.5"></i> Tambah Kategori
                </p>
                
                <div class="flex items-center bg-white/5 border border-white/10 rounded-2xl p-2 focus-within:ring-2 focus-within:ring-white/20 transition-all mb-4">
                    <!-- Native Color Picker: UX Rapi -->
                    <input type="color" name="color_hex" value="#3b82f6" class="w-10 h-10 rounded cursor-pointer border-0 bg-transparent p-0 shrink-0 mx-1" title="Pilih Warna Kategori">
                    <input type="text" name="name" placeholder="Nama Kategori (Cth: Design, Video) *" class="w-full bg-transparent border-0 text-white text-sm focus:ring-0 px-3 placeholder-white/30" required>
                </div>
                
                <button type="submit" class="w-full py-3 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                    Simpan Kategori Tiket
                </button>
            </form>
        </div>
    </div>

    <!-- ==============================================
         4. KOLOM: FORUM CATEGORIES (NEW)
         ============================================== -->
    <div class="space-y-6">
        <div class="bg-white/5 backdrop-blur-xl p-6 md:p-8 rounded-[28px] border border-white/10 shadow-lg h-full flex flex-col">
            <div class="flex items-center justify-between mb-5 border-b border-white/10 pb-4">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider flex items-center">
                    <i class="ph-fill ph-chats-circle mr-2 text-orange-400 text-lg"></i> Kategori Forum Diskusi
                </h3>
                <span class="px-3 py-1 text-[10px] font-bold rounded-full border bg-orange-500/20 text-orange-300 border-orange-500/30 backdrop-blur-md tracking-wider">
                    <?= count($forumCategories) ?> Kategori
                </span>
            </div>
            
            <div class="space-y-3 mb-6 max-h-[220px] overflow-y-auto pr-2 scrollbar-hide flex-grow">
                <?php if(empty($forumCategories)): ?>
                    <div class="p-8 text-center border border-dashed border-white/20 rounded-[20px] bg-white/5 text-white/40">
                        <i class="ph-fill ph-empty text-3xl mb-2 opacity-50 block"></i>
                        <p class="text-xs font-medium">Belum ada Kategori Forum.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($forumCategories as $fc): ?>
                        <div class="p-4 bg-black/20 hover:bg-white/5 transition-colors rounded-[20px] border border-white/5 group">
                            <p class="text-sm font-semibold text-white/90 flex items-center group-hover:text-white transition-colors">
                                <span class="w-6 h-6 rounded-full bg-orange-500/20 flex items-center justify-center mr-2 text-orange-400 text-xs shrink-0"><i class="ph-bold ph-folder"></i></span>
                                <?= htmlspecialchars($fc['name']) ?>
                            </p>
                            <?php if($fc['description']): ?>
                                <p class="text-[11px] text-white/50 mt-2 ml-8 leading-relaxed"><?= htmlspecialchars($fc['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form action="<?= base_url('brand/forum-category/store') ?>" method="POST" class="bg-black/20 p-5 rounded-[24px] border border-white/5 mt-auto">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-4 flex items-center">
                    <i class="ph-bold ph-plus-circle mr-1.5"></i> Tambah Kategori
                </p>
                <div class="space-y-3 mb-4">
                    <input type="text" name="name" placeholder="Nama Kategori (Contoh: Pengumuman) *" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" required>
                    <textarea name="description" rows="2" placeholder="Deskripsi singkat (Opsional)" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none"></textarea>
                </div>
                <button type="submit" class="w-full py-3 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                    Simpan Kategori Forum
                </button>
            </form>
        </div>
    </div>

</div>