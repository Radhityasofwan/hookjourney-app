<?php
    // Deteksi cerdas apakah halaman ini Create atau Edit
    $isEdit = isset($ticketId);
?>

<div class="mb-6 flex items-center relative z-50">
    <a href="<?= base_url('tickets') ?>" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all mr-4 shadow-sm text-decoration-none">
        <i class="ph-bold ph-arrow-left text-xl"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight"><?= $isEdit ? 'Edit Tiket Tugas' : 'Buat Task / Tiket Baru' ?></h1>
        <p class="text-white/60 text-sm mt-0.5">Tugaskan pekerjaan ke anggota tim di Brand <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<!-- Form terintegrasi untuk Store & Update -->
<form action="<?= base_url($isEdit ? 'tickets/update' : 'tickets/store') ?>" method="POST" class="relative z-40">
    
    <?php if($isEdit): ?>
        <input type="hidden" name="id" value="<?= $ticketId ?>">
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- KOLOM KIRI (Utama) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white/5 backdrop-blur-xl p-6 rounded-[28px] border border-white/10 shadow-lg h-full">
                <div class="space-y-6">
                    
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Judul Tugas <span class="text-red-400">*</span></label>
                        <input type="text" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" class="w-full px-4 py-3 bg-black/30 border <?= isset($errors['title']) ? 'border-red-500/50 focus:ring-red-500/20' : 'border-white/10 focus:ring-white/20' ?> rounded-2xl text-white focus:ring-2 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" placeholder="Contoh: Desain Banner Promo Akhir Bulan" required>
                        <?php if(isset($errors['title'])): ?><p class="text-xs text-red-400 mt-2 flex items-center"><i class="ph-fill ph-warning-circle mr-1"></i> <?= $errors['title'] ?></p><?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Detail / Deskripsi Pekerjaan</label>
                        <textarea name="description" rows="12" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none" placeholder="Jelaskan secara detail ekspektasi dari tugas ini..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (Atribut) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white/5 backdrop-blur-xl p-6 rounded-[28px] border border-white/10 shadow-lg flex flex-col h-full">
                <h3 class="text-sm font-bold text-white mb-5 border-b border-white/10 pb-3 uppercase tracking-wider text-white/80 flex items-center">
                    <i class="ph-bold ph-sliders mr-2"></i> Atribut Tiket
                </h3>
                
                <div class="space-y-6 flex-grow">
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Assign ke (PIC)</label>
                        <select name="assigned_to" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="" class="bg-gray-900 text-white/50">-- Biarkan Kosong (Unassigned) --</option>
                            <?php foreach($teamMembers as $team): ?>
                                <option value="<?= $team['id'] ?>" class="bg-gray-900 text-white" <?= (isset($old['assigned_to']) && $old['assigned_to'] == $team['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($team['full_name']) ?> (<?= $team['role_global'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Prioritas <span class="text-red-400">*</span></label>
                        <select name="priority" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer" required>
                            <option value="low" class="bg-gray-900 text-white" <?= (isset($old['priority']) && $old['priority'] == 'low') ? 'selected' : '' ?>>Low</option>
                            <option value="medium" class="bg-gray-900 text-white" <?= (!isset($old['priority']) || $old['priority'] == 'medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="high" class="bg-gray-900 text-white" <?= (isset($old['priority']) && $old['priority'] == 'high') ? 'selected' : '' ?>>High</option>
                            <option value="urgent" class="bg-gray-900 text-white" <?= (isset($old['priority']) && $old['priority'] == 'urgent') ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Kategori Tugas</label>
                        <select name="category_id" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="" class="bg-gray-900 text-white/50">-- Tidak ada kategori --</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" class="bg-gray-900 text-white" <?= (isset($old['category_id']) && $old['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[10px] text-white/40 mt-2 flex items-center"><i class="ph-fill ph-info mr-1"></i> Kategori diatur di Pengaturan Brand.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Tenggat Waktu (Due Date)</label>
                        <input type="datetime-local" name="due_at" value="<?= $old['due_at'] ?? '' ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm [color-scheme:dark]">
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-white/10">
                    <button type="submit" class="w-full py-3.5 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                        <i class="ph-bold ph-floppy-disk mr-2 text-lg"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan & Buat Tugas' ?>
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>