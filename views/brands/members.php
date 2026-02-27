<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Tim & Hak Akses</h1>
        <p class="text-white/60 text-sm mt-1">Atur siapa yang bisa mengakses fitur tertentu pada Brand <strong
                class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<?php if (isset($success_msg)): ?>
    <div
        class="mb-6 p-4 rounded-[50px] bg-emerald-500/10 border-emerald-500/20 text-sm text-emerald-300 flex items-center backdrop-blur-md relative z-40">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<?php if (isset($error_msg)): ?>
    <div
        class="mb-6 p-4 rounded-[50px] bg-red-500/10 border-red-500/20 text-sm text-red-300 flex items-center backdrop-blur-md relative z-40">
        <i class="ph-fill ph-warning-circle text-xl mr-3 text-red-400"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-40">

    <!-- Form Tambah User & Izin (Kiri) -->
    <div class="lg:col-span-5">
        <div
            class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-6 rounded-[28px] sticky top-6">
            <h3 class="font-bold text-white/90 mb-5 border-b pb-3 flex items-center text-sm uppercase tracking-wider">
                <i class="ph-bold ph-user-plus mr-2 text-blue-400 text-lg"></i> Undang Anggota Baru
            </h3>

            <form action="<?= base_url('brand/team/store') ?>" method="POST" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-white/60 mb-1.5 uppercase tracking-wider">Nama
                        Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="full_name"
                        class="w-full px-4 py-2.5 bg-black/30 rounded-xl text-white focus:ring-2 focus:ring-white/20 outline-none transition-all text-sm placeholder-white/30"
                        placeholder="Contoh: Alex J" required>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-white/60 mb-1.5 uppercase tracking-wider">Email <span
                            class="text-red-400">*</span></label>
                    <input type="email" name="email"
                        class="w-full px-4 py-2.5 bg-black/30 rounded-xl text-white focus:ring-2 focus:ring-white/20 outline-none transition-all text-sm placeholder-white/30"
                        placeholder="email@perusahaan.com" required>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-white/60 mb-1.5 uppercase tracking-wider">No.
                        WhatsApp <span class="text-white/30">(Opsional)</span></label>
                    <input type="text" name="phone"
                        class="w-full px-4 py-2.5 bg-black/30 rounded-xl text-white focus:ring-2 focus:ring-white/20 outline-none transition-all text-sm placeholder-white/30"
                        placeholder="Contoh: 081234...">
                </div>

                <div class="flex gap-4">
                    <div class="flex-1">
                        <label
                            class="block text-[10px] font-bold text-white/60 mb-1.5 uppercase tracking-wider">Password
                            <span class="text-white/30">(Opsional untuk Edit)</span></label>
                        <input type="password" id="passwordInput" name="password"
                            class="w-full px-4 py-2.5 bg-black/30 rounded-xl text-white focus:ring-2 focus:ring-white/20 outline-none transition-all text-sm placeholder-white/30"
                            placeholder="••••••••">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-white/60 mb-1.5 uppercase tracking-wider">Peran
                            (Role)</label>
                        <select name="role_in_brand"
                            class="w-full px-4 py-2.5 bg-black/30 rounded-xl text-white focus:ring-2 focus:ring-white/20 outline-none transition-all text-sm appearance-none cursor-pointer"
                            required>
                            <option value="team" class="bg-gray-900 text-white">Team (Staff)</option>
                            <option value="client" class="bg-gray-900 text-white">Client (Klien)</option>
                        </select>
                    </div>
                </div>

                <!-- Modul Permissions dengan desain Toggle Pills -->
                <div class="pt-2">
                    <label class="block text-[10px] font-bold text-white/60 mb-2 uppercase tracking-wider">Izinkan Akses
                        Menu:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <?php
                        $menus = [
                            'ads' => 'Meta Ads',
                            'creative' => 'Creative Assets',
                            'content' => 'Content Planner',
                            'tickets' => 'Tasks / Tickets',
                            'forum' => 'Forum Diskusi',
                            'keywords' => 'SEO Keywords',
                            'reviews' => 'Weekly Review'
                        ];
                        foreach ($menus as $key => $label): ?>
                            <label class="relative flex items-center cursor-pointer group">
                                <input type="checkbox" name="permissions[]" value="<?= $key ?>" class="peer sr-only"
                                    checked>
                                <div
                                    class="w-full px-3 py-2 rounded-xl bg-black/20 text-white/50 text-xs font-medium peer-checked:bg-white/15 peer-checked:text-white peer-checked: transition-all duration-200 text-center">
                                    <?= $label ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-4 border-t mt-2">
                    <button type="submit" id="submitBtn"
                        class="w-full py-3 flex items-center justify-center bg-white text-black rounded-xl shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="ph-bold ph-plus mr-2 text-lg" id="submitIcon"></i> <span id="submitText">Simpan
                            Anggota Baru</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Anggota Aktif (Kanan) -->
    <div class="lg:col-span-7">
        <div
            class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[28px] overflow-hidden h-full flex flex-col">
            <div class="px-6 py-5 border-b bg-transparent flex justify-between items-center">
                <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider">Daftar Anggota Tim</h3>
                <span class="text-xs font-medium bg-black/30 px-3 py-1 rounded-2xl text-white/50"><?= count($members) ?>
                    Orang</span>
            </div>

            <div class="overflow-x-auto scrollbar-hide flex-grow">
                <table class="min-w-full divide-y divide-white/5">
                    <thead class="bg-transparent">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">
                                Identitas</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">
                                Izin Akses</th>
                            <th
                                class="px-6 py-4 text-center text-[10px] font-bold text-white/50 uppercase tracking-widest w-20">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-transparent">
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-white/40 text-sm">
                                    <i class="ph-fill ph-users text-4xl mb-2 opacity-50 block"></i>
                                    Belum ada anggota yang ditambahkan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $m): ?>
                                <tr class="hover:bg-white/5 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">

                                            <!-- FIX: SMART AVATAR & INITIALS -->
                                            <?php if (!empty($m['avatar_url'])): ?>
                                                <img src="<?= base_url($m['avatar_url']) ?>?v=<?= time() ?>"
                                                    class="w-10 h-10 rounded-full object-cover mr-3 shrink-0"
                                                    alt="<?= htmlspecialchars($m['full_name']) ?>"
                                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($m['full_name']) ?>&background=4361ee&color=fff';">
                                            <?php else: ?>
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm mr-3 shrink-0">
                                                    <?= strtoupper(substr($m['full_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>

                                            <div>
                                                <p
                                                    class="mb-0 font-bold text-white/90 text-sm flex items-center group-hover:text-blue-300 transition-colors">
                                                    <?= htmlspecialchars($m['full_name']) ?>

                                                    <!-- FIX: TOMBOL WHATSAPP CHAT -->
                                                    <?php if (!empty($m['phone'])): ?>
                                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $m['phone']) ?>"
                                                            target="_blank"
                                                            class="text-emerald-400 ml-2 hover:text-emerald-300 hover:scale-110 transition-all flex items-center bg-emerald-500/10 px-1.5 py-0.5 rounded border-emerald-500/20"
                                                            title="Chat WhatsApp">
                                                            <i class="ph-fill ph-whatsapp-logo mr-1"></i> <span
                                                                class="text-[9px] uppercase tracking-widest font-bold">Chat</span>
                                                        </a>
                                                    <?php endif; ?>
                                                </p>
                                                <p class="mb-0 text-white/50 text-xs mt-1 font-medium">
                                                    <?= htmlspecialchars($m['email']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 max-w-[200px]">
                                        <div class="flex flex-wrap gap-1.5">
                                            <?php
                                            $perms = json_decode($m['permissions_json'] ?? '[]', true);
                                            if ($m['role_in_brand'] == 'leader'): ?>
                                                <span
                                                    class="px-2.5 py-1 text-[9px] font-bold uppercase rounded-2xl bg-blue-500/20 text-blue-300 border-blue-500/30 tracking-wider">All
                                                    Access (Leader)</span>
                                            <?php elseif (empty($perms)): ?>
                                                <span class="text-white/30 text-xs italic">Tidak ada akses menu</span>
                                            <?php else: ?>
                                                <?php foreach ($perms as $p): ?>
                                                    <span
                                                        class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-white/5 text-white/60 tracking-wider"><?= htmlspecialchars($p) ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap align-middle">
                                        <?php if ($m['role_in_brand'] !== 'leader'): ?>
                                            <div class="flex items-center justify-center space-x-2">
                                                <button type="button"
                                                    onclick="editMember('<?= htmlspecialchars($m['full_name']) ?>', '<?= htmlspecialchars($m['email']) ?>', '<?= htmlspecialchars($m['phone']) ?>', '<?= htmlspecialchars($m['role_in_brand']) ?>', '<?= htmlspecialchars($m['permissions_json']) ?>')"
                                                    class="flex items-center justify-center w-8 h-8 rounded-2xl bg-transparent hover:bg-blue-500/20 text-white/50 hover:text-blue-400 transition-all border-transparent"
                                                    title="Edit Akses">
                                                    <i class="ph-fill ph-pencil-simple text-lg"></i>
                                                </button>
                                                <form action="<?= base_url('brand/team/delete') ?>" method="POST"
                                                    onsubmit="return confirm('Cabut akses anggota ini dari brand?')">
                                                    <input type="hidden" name="member_id" value="<?= $m['member_id'] ?>">
                                                    <button type="submit"
                                                        class="flex items-center justify-center w-8 h-8 rounded-2xl bg-transparent hover:bg-red-500/20 text-white/50 hover:text-red-400 transition-all border-transparent"
                                                        title="Hapus Anggota">
                                                        <i class="ph-fill ph-trash text-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-white/20">-</span>
                                        <?php endif; ?>
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

<script>
    function editMember(name, email, phone, role, permsJson) {
        document.querySelector('input[name="full_name"]').value = name;
        document.querySelector('input[name="email"]').value = email;
        document.querySelector('input[name="email"]').readOnly = true; // Email sebagai identifier unik
        document.querySelector('input[name="phone"]').value = phone;
        document.querySelector('select[name="role_in_brand"]').value = role;

        // Reset password req
        document.getElementById('passwordInput').removeAttribute('required');
        document.getElementById('passwordInput').placeholder = "Kosongkan jika tidak ingin mengubah";

        // Uncheck all first
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);

        // Check appropriate ones
        try {
            const perms = JSON.parse(permsJson || '[]');
            perms.forEach(p => {
                const cb = document.querySelector(`input[name="permissions[]"][value="${p}"]`);
                if (cb) cb.checked = true;
            });
        } catch (e) { }

        // Ubah tampilan tombol
        document.getElementById('submitText').innerText = "Sinkronisasi Perubahan";
        document.getElementById('submitIcon').className = "ph-bold ph-arrows-clockwise mr-2 text-lg text-blue-500";
        document.querySelector('button[type="submit"]').classList.remove('bg-white', 'text-black');
        document.querySelector('button[type="submit"]').classList.add('bg-blue-600', 'text-white');
    }
</script>