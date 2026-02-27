<!-- Header -->
<div class="mb-8 relative z-20">
    <h1 class="text-2xl font-bold text-white tracking-tight">Profil Pengguna</h1>
    <p class="text-white/60 text-sm mt-1">Kelola data diri, foto avatar, dan nomor WhatsApp Anda.</p>
</div>

<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300 flex items-center shadow-[0_0_15px_rgba(52,211,153,0.1)] backdrop-blur-md relative z-20">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<?php if(isset($error_msg)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-sm text-red-300 flex items-center shadow-[0_0_15px_rgba(244,63,94,0.1)] backdrop-blur-md relative z-20">
        <i class="ph-fill ph-warning-circle text-xl mr-3 text-red-400"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<div class="bg-white/5 backdrop-blur-xl p-8 rounded-[28px] border border-white/10 shadow-lg max-w-3xl relative z-20">
    <form action="<?= base_url('profile/update') ?>" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-8">
        
        <!-- Kolom Avatar Upload -->
        <div class="flex flex-col items-center gap-4 w-full md:w-1/3">
            
            <div class="relative group cursor-pointer flex-shrink-0" style="width: 128px; height: 128px;" onclick="document.getElementById('avatar-upload').click()">
                
                <?php if(!empty($user['avatar_url'])): ?>
                    <img id="avatar-preview" src="<?= base_url($user['avatar_url']) ?>?v=<?= time() ?>" class="w-full h-full rounded-full border-4 border-white/10 group-hover:border-blue-500 transition-colors shadow-xl" style="object-fit: cover; object-position: center; display: block;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=4361ee&color=fff&size=128';">
                    <div id="avatar-preview-fallback" class="w-full h-full rounded-full flex items-center justify-center text-white font-bold text-4xl shadow-xl border-4 border-white/10 group-hover:border-blue-500 transition-colors hidden" style="background: linear-gradient(135deg, #4361ee, #7209b7);">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php else: ?>
                    <div id="avatar-preview-fallback" class="w-full h-full rounded-full flex items-center justify-center text-white font-bold text-4xl shadow-xl border-4 border-white/10 group-hover:border-blue-500 transition-colors" style="background: linear-gradient(135deg, #4361ee, #7209b7);">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <!-- FIX: Empty src triggers base_url() -> loads index.php -> infinite loop. Gunakan Pixel Transparan base64 -->
                    <img id="avatar-preview" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="w-full h-full rounded-full border-4 border-white/10 group-hover:border-blue-500 transition-colors shadow-xl hidden" style="object-fit: cover; object-position: center;">
                <?php endif; ?>
                
                <!-- Overlay Edit -->
                <div class="absolute inset-0 bg-black/60 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="ph-bold ph-camera text-2xl text-white"></i>
                </div>
            </div>
            
            <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/jpeg, image/png, image/webp" onchange="previewImage(event)">
            
            <div class="text-center">
                <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Ubah Foto Profil</p>
                <p class="text-[10px] text-white/30 mt-1">Format: JPG, PNG. Maks 2MB.</p>
            </div>
        </div>

        <!-- Kolom Form Data Pribadi -->
        <div class="flex-1 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-sm" required>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Email (Login ID)</label>
                <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full px-4 py-3 bg-white/5 border border-white/5 rounded-2xl text-white/50 text-sm cursor-not-allowed" readonly disabled title="Email tidak dapat diubah dari sini">
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Nomor WhatsApp</label>
                <div class="relative">
                    <i class="ph-fill ph-whatsapp-logo absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400 text-lg pointer-events-none"></i>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full pl-11 pr-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all text-sm placeholder-white/20" placeholder="Contoh: 08123456789">
                </div>
                <p class="text-[10px] text-white/40 mt-1.5 ml-1">Agar tim/klien dapat menghubungi Anda langsung via chat.</p>
            </div>

            <div class="pt-4 border-t border-white/10">
                <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Ubah Password <span class="text-white/30 capitalize tracking-normal">(Opsional)</span></label>
                <input type="password" name="password" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-red-500/50 outline-none transition-all text-sm placeholder-white/20" placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center">
                    <i class="ph-bold ph-floppy-disk mr-2 text-lg"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('avatar-preview');
        output.src = reader.result;
        
        output.classList.remove('hidden');
        output.style.display = 'block';
        
        const fallback = document.getElementById('avatar-preview-fallback');
        if(fallback) {
            fallback.classList.add('hidden');
            fallback.style.display = 'none';
        }
    };
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>