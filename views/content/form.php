<div class="mb-6 flex items-center relative z-50">
    <a href="<?= base_url('content/kanban') ?>" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all mr-4 shadow-sm text-decoration-none">
        <i class="ph-bold ph-arrow-left text-xl"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight"><?= isset($item) ? 'Edit Ide Konten' : 'Buat Ide Konten Baru' ?></h1>
        <p class="text-white/60 text-sm mt-0.5">Brand: <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<form action="<?= base_url(isset($item) ? 'content/update' : 'content/store') ?>" method="POST" class="relative z-40">
    <?php if(isset($item)): ?>
        <input type="hidden" name="id" value="<?= $item['id'] ?>">
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Brief & Metadata -->
        <div class="lg:col-span-4">
            <div class="p-6 rounded-[28px] shadow-lg h-full bg-white/5 backdrop-blur-xl border border-white/10">
                <h3 class="text-sm font-bold text-white mb-5 border-b border-white/10 pb-3 uppercase tracking-wider text-white/80">Atribut Konten</h3>
                
                <div class="space-y-5">
                    <!-- Judul -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Judul Konten <span class="text-red-400">*</span></label>
                        <input type="text" name="title" value="<?= $old['title'] ?? '' ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" required placeholder="Contoh: Tips memilih mesin laundry">
                    </div>

                    <!-- Pilar Konten -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Pilar Konten</label>
                        <select name="pillar_id" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none">
                            <option value="" class="bg-gray-900 text-white">-- Pilih Pilar (Opsional) --</option>
                            <?php foreach($pillars as $p): ?>
                                <option value="<?= $p['id'] ?>" class="bg-gray-900 text-white" <?= (isset($old['pillar_id']) && $old['pillar_id'] == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Goal & Format -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Objective</label>
                            <select name="goal" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none">
                                <?php 
                                $goals = ['awareness', 'promotion', 'education', 'engagement', 'conversion', 'retention'];
                                foreach($goals as $g): ?>
                                    <option value="<?= $g ?>" class="bg-gray-900 text-white" <?= (isset($old['goal']) && $old['goal'] == $g) ? 'selected' : '' ?>><?= ucfirst($g) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Format</label>
                            <select name="content_type" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none">
                                <?php 
                                $formats = ['reels', 'carousel', 'single_image', 'story', 'short_video', 'live'];
                                foreach($formats as $c): ?>
                                    <option value="<?= $c ?>" class="bg-gray-900 text-white" <?= (isset($old['content_type']) && $old['content_type'] == $c) ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $c)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Platform Checkboxes -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-3 uppercase tracking-wide">Platform Distribusi</label>
                        <div class="flex flex-wrap gap-3">
                            <?php 
                            $selectedPlat = isset($old['platforms_json']) ? (is_array($old['platforms_json']) ? $old['platforms_json'] : json_decode($old['platforms_json'], true)) : [];
                            if (!$selectedPlat) $selectedPlat = [];
                            
                            $platforms = ['instagram', 'tiktok', 'facebook', 'youtube'];
                            foreach($platforms as $plat): 
                            ?>
                            <label class="relative flex items-center cursor-pointer group">
                                <input type="checkbox" name="platforms[]" value="<?= $plat ?>" class="peer sr-only" <?= in_array($plat, $selectedPlat) ? 'checked' : '' ?>>
                                <div class="px-4 py-2 rounded-full border border-white/20 bg-black/20 text-white/60 text-xs font-medium peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-500 transition-all duration-200">
                                    <?= ucfirst($plat) ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- PIC Assignment -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">PIC (Penanggung Jawab)</label>
                        <select name="pic_user_id" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none">
                            <option value="" class="bg-gray-900 text-white">-- Unassigned --</option>
                            <?php foreach($teamMembers as $team): ?>
                                <option value="<?= $team['id'] ?>" class="bg-gray-900 text-white" <?= (isset($old['pic_user_id']) && $old['pic_user_id'] == $team['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($team['full_name']) ?> (<?= ucfirst($team['role_global']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Jadwal & Prioritas -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Jadwal Tayang</label>
                            <input type="date" name="scheduled_post_at" value="<?= isset($old['scheduled_post_at']) && $old['scheduled_post_at'] ? date('Y-m-d', strtotime($old['scheduled_post_at'])) : '' ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm [color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Prioritas</label>
                            <div class="flex p-1 bg-black/30 border border-white/10 rounded-2xl segment-control">
                                <?php foreach(['low', 'medium', 'high'] as $p): ?>
                                    <label class="flex-1 text-center cursor-pointer relative">
                                        <input type="radio" name="priority" value="<?= $p ?>" class="peer sr-only" <?= (isset($old['priority']) && $old['priority'] == $p) ? 'checked' : ($p == 'medium' && !isset($old['priority']) ? 'checked' : '') ?>>
                                        <div class="py-2 rounded-xl text-xs font-semibold text-white/40 peer-checked:text-white peer-checked:bg-white/15 peer-checked:shadow-sm transition-all duration-300 capitalize">
                                            <?= $p ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Scripting & Strategy -->
        <div class="lg:col-span-8">
            <div class="p-6 rounded-[28px] shadow-lg h-full bg-white/5 backdrop-blur-xl border border-white/10 flex flex-col">
                <h3 class="text-sm font-bold text-white mb-5 border-b border-white/10 pb-3 uppercase tracking-wider text-white/80 flex items-center">
                    <i class="ph-bold ph-note-pencil mr-2"></i> Strategy & Scripting
                </h3>
                
                <div class="space-y-6 flex-grow">
                    <!-- Angle & Audience -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Angle Komunikasi</label>
                            <input type="text" name="angle" value="<?= $old['angle'] ?? '' ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" placeholder="Misal: Pain point, Testimoni, FOMO...">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Target Audience</label>
                            <input type="text" name="target_audience" value="<?= $old['target_audience'] ?? '' ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" placeholder="Misal: Pria karir 25-35th">
                        </div>
                    </div>

                    <!-- Script Fields -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">1. Hook (Teks/Visual Pembuka 3 Detik)</label>
                        <textarea name="hook_text" rows="2" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none" placeholder="Apa yang membuat orang berhenti scrolling?"><?= $old['hook_text'] ?? '' ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">2. Body / Content Value (Isi Utama)</label>
                        <textarea name="body_text" rows="10" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none" placeholder="Tuliskan detail alur video, poin-poin carousel, atau naskah lengkap..."><?= $old['body_text'] ?? '' ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">3. Call to Action (CTA)</label>
                        <textarea name="cta_text" rows="2" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none" placeholder="Apa yang harus dilakukan penonton setelah melihat konten?"><?= $old['cta_text'] ?? '' ?></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end items-center gap-4 mt-8 pt-5 border-t border-white/10">
                    <a href="<?= base_url('content/kanban') ?>" class="px-5 py-2.5 rounded-full border border-white/10 text-white/60 hover:text-white hover:bg-white/5 transition-all text-sm font-medium text-decoration-none">
                        Batal
                    </a>
                    <button type="submit" class="flex items-center justify-center px-6 py-3 bg-white text-black rounded-full font-bold shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:scale-105 active:scale-95 transition-all text-sm">
                        <i class="ph-bold ph-floppy-disk mr-2 text-lg"></i> <?= isset($item) ? 'Perbarui Konten' : 'Simpan Ide Konten' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>