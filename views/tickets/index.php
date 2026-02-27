<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Task & Ticketing</h1>
        <p class="text-white/60 text-sm mt-1">Kelola penugasan tim untuk Brand <strong
                class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>

    <?php if ($user['role_global'] === 'leader'): ?>
        <a href="<?= base_url('tickets/create') ?>"
            class="mt-4 sm:mt-0 flex items-center justify-center px-5 py-2.5 bg-white text-black rounded-full font-semibold shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:scale-105 active:scale-95 transition-all text-sm w-max text-decoration-none">
            <i class="ph-bold ph-plus text-lg mr-2"></i> Buat Tiket
        </a>
    <?php endif; ?>
</div>

<?php if (isset($success_msg)): ?>
    <div
        class="mb-6 p-4 rounded-[50px] bg-emerald-500/10 border-emerald-500/20 text-sm text-emerald-300 flex items-center backdrop-blur-md relative z-40">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<div
    class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[24px] overflow-hidden relative z-40 flex flex-col min-h-[60vh]">
    <!-- Filter Bar -->
    <div class="px-6 py-4 border-b flex justify-between items-center bg-white/5">
        <div class="text-sm text-white/60 flex items-center font-medium"><i class="ph-bold ph-funnel mr-2"></i> Filter:
            <?= $user['role_global'] === 'leader' ? 'Semua Tugas' : 'Tugas Milik Saya' ?>
        </div>
        <div class="text-xs text-white/40"><i class="ph-fill ph-info mr-1"></i> Ubah status langsung dari tabel</div>
    </div>

    <div class="overflow-x-auto scrollbar-hide flex-grow">
        <table class="min-w-full divide-y divide-white/5">
            <thead class="bg-transparent">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest w-16">
                        ID</th>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Tugas
                        / Tiket</th>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Status
                    </th>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">
                        Prioritas</th>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">PIC
                        (Assignee)</th>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-white/50 uppercase tracking-widest">Due
                        Date</th>
                    <?php if ($user['role_global'] === 'leader'): ?>
                        <th
                            class="px-6 py-4 text-center text-[10px] font-bold text-white/50 uppercase tracking-widest w-24">
                            Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 bg-transparent">
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="<?= $user['role_global'] === 'leader' ? '7' : '6' ?>"
                            class="px-6 py-20 text-center text-white/40 text-sm flex-col items-center">
                            <i class="ph-fill ph-ticket text-5xl mb-3 opacity-30"></i>
                            <p>Belum ada tugas atau tiket yang dibuat.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr class="hover:bg-white/5 cursor-pointer transition-colors group"
                            onclick="openTicketModal(<?= $t['id'] ?>)">

                            <td
                                class="px-6 py-4 whitespace-nowrap text-xs font-bold text-white/40 group-hover:text-blue-400 transition-colors">
                                #<?= htmlspecialchars(explode('-', $t['ticket_no'])[2] ?? $t['ticket_no']) ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <p
                                        class="text-sm font-semibold text-white/90 leading-tight group-hover:text-white transition-colors truncate max-w-[280px]">
                                        <?= htmlspecialchars($t['title']) ?>
                                    </p>
                                    <!-- Indikator Ekstra -->
                                    <?php if ($t['comment_count'] > 0): ?>
                                        <span class="text-[10px] text-white/40 flex items-center"><i
                                                class="ph-fill ph-chats mr-1 text-blue-400"></i> <?= $t['comment_count'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($t['checklist_total'] > 0): ?>
                                        <span
                                            class="text-[10px] text-white/40 flex items-center <?= $t['checklist_done'] == $t['checklist_total'] ? 'text-emerald-400' : '' ?>"><i
                                                class="ph-bold ph-check-square-offset mr-1"></i>
                                            <?= $t['checklist_done'] ?>/<?= $t['checklist_total'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($t['category_name']): ?>
                                    <span
                                        class="text-[9px] font-bold uppercase tracking-widest text-white/50 inline-flex items-center bg-white/10 px-2 py-0.5 rounded backdrop-blur-sm"><i
                                            class="ph-fill ph-tag mr-1 text-white/40"></i>
                                        <?= htmlspecialchars($t['category_name']) ?></span>
                                <?php endif; ?>
                            </td>

                            <!-- DROPDOWN STATUS -->
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <?php
                                $sColor = [
                                    'open' => 'bg-white/10 text-white ',
                                    'in_progress' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                    'review' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                    'done' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                    'blocked' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                    'cancelled' => 'bg-gray-500/20 text-gray-400 '
                                ][$t['ticket_status']] ?? 'bg-white/10 text-white/80 ';
                                ?>
                                <div class="relative inline-flex items-center">
                                    <select onchange="updateTicketStatus(<?= $t['id'] ?>, this)"
                                        class="appearance-none bg-transparent text-[10px] font-bold uppercase tracking-wider py-1 pl-3 pr-7 rounded-full cursor-pointer focus:outline-none <?= $sColor ?> backdrop-blur-md transition-all">
                                        <option value="open" class="bg-gray-900 text-white" <?= $t['ticket_status'] == 'open' ? 'selected' : '' ?>>OPEN</option>
                                        <option value="in_progress" class="bg-gray-900 text-white"
                                            <?= $t['ticket_status'] == 'in_progress' ? 'selected' : '' ?>>IN PROGRESS</option>
                                        <option value="review" class="bg-gray-900 text-white" <?= $t['ticket_status'] == 'review' ? 'selected' : '' ?>>REVIEW</option>
                                        <option value="done" class="bg-gray-900 text-white" <?= $t['ticket_status'] == 'done' ? 'selected' : '' ?>>DONE</option>
                                        <option value="blocked" class="bg-gray-900 text-white" <?= $t['ticket_status'] == 'blocked' ? 'selected' : '' ?>>BLOCKED</option>
                                        <option value="cancelled" class="bg-gray-900 text-white"
                                            <?= $t['ticket_status'] == 'cancelled' ? 'selected' : '' ?>>CANCEL</option>
                                    </select>
                                    <i
                                        class="ph-bold ph-caret-down absolute right-2.5 pointer-events-none text-[10px] opacity-70"></i>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $pColor = 'text-white/50';
                                $pIcon = 'ph-minus';
                                if ($t['priority'] == 'high') {
                                    $pColor = 'text-orange-400';
                                    $pIcon = 'ph-arrow-up';
                                }
                                if ($t['priority'] == 'urgent') {
                                    $pColor = 'text-red-400 drop-shadow-[0_0_8px_rgba(248,113,113,0.5)]';
                                    $pIcon = 'ph-warning-circle';
                                }
                                ?>
                                <span class="text-xs font-semibold <?= $pColor ?> flex items-center capitalize">
                                    <i class="ph-bold <?= $pIcon ?> mr-1.5 text-sm"></i> <?= $t['priority'] ?>
                                </span>
                            </td>

                            <!-- AVATAR & WA PING -->
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <?php if ($t['assignee_name']): ?>
                                    <div class="flex items-center">
                                        <?php if (!empty($t['assignee_avatar'])): ?>
                                            <img src="<?= base_url($t['assignee_avatar']) ?>?v=<?= time() ?>"
                                                class="w-7 h-7 rounded-full object-cover mr-2.5 shrink-0"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($t['assignee_name']) ?>&background=4361ee&color=fff';">
                                        <?php else: ?>
                                            <div
                                                class="h-7 w-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-[10px] font-bold text-white mr-2.5 shrink-0">
                                                <?= strtoupper(substr($t['assignee_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>

                                        <span
                                            class="text-sm text-white/80 font-medium truncate max-w-[120px]"><?= htmlspecialchars($t['assignee_name']) ?></span>

                                        <?php if (!empty($t['assignee_phone']) && $user['role_global'] === 'leader'): ?>
                                            <?php
                                            $dateStr = $t['due_at'] ? date('d M Y', strtotime($t['due_at'])) : 'Tanpa Tenggat';
                                            $waText = urlencode("Halo " . explode(' ', $t['assignee_name'])[0] . ", reminder untuk tugas tiket *" . $t['title'] . "* (Due: " . $dateStr . "). Mohon di-update progressnya ya. Semangat! 🚀");
                                            ?>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $t['assignee_phone']) ?>?text=<?= $waText ?>"
                                                target="_blank"
                                                class="text-emerald-400 bg-emerald-500/10 w-6 h-6 flex items-center justify-center rounded-full hover:bg-emerald-500/20 hover:scale-110 transition-all border-emerald-500/20 ml-3 shrink-0"
                                                title="Ping Reminder via WA">
                                                <i class="ph-fill ph-whatsapp-logo text-[12px]"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center text-white/40">
                                        <div class="h-7 w-7 rounded-full bg-white/5 flex items-center justify-center mr-2.5"><i
                                                class="ph-fill ph-user text-xs"></i></div>
                                        <span class="text-xs font-medium italic">Unassigned</span>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($t['due_at']): ?>
                                    <?php $isOverdue = strtotime($t['due_at']) < time() && !in_array($t['ticket_status'], ['done', 'cancelled']); ?>
                                    <span
                                        class="flex items-center <?= $isOverdue ? 'text-red-400 font-bold' : 'text-white/60 font-medium' ?>">
                                        <i class="ph-bold ph-calendar-blank mr-1.5"></i>
                                        <?= date('d M Y', strtotime($t['due_at'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-white/30 text-xs italic">Tanpa Tenggat</span>
                                <?php endif; ?>
                            </td>

                            <!-- ACTION: EDIT & DELETE (Khusus Leader) -->
                            <?php if ($user['role_global'] === 'leader'): ?>
                                <td class="px-6 py-4 text-center whitespace-nowrap" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?= base_url('tickets/edit?id=' . $t['id']) ?>"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-2xl bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 transition-colors border-blue-500/20"
                                            title="Edit Detail">
                                            <i class="ph-bold ph-pencil-simple text-sm"></i>
                                        </a>
                                        <form action="<?= base_url('tickets/delete') ?>" method="POST" class="inline-block m-0"
                                            onsubmit="return confirm('Hapus tugas ini permanen?')">
                                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-2xl bg-red-500/10 hover:bg-red-500/20 text-red-400 transition-colors border-red-500/20"
                                                title="Hapus">
                                                <i class="ph-bold ph-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==============================================================
     MODAL PREVIEW TIKET INTERAKTIF (LIGHTBOX)
     ============================================================== -->
<style>
    /* Transisi Modal */
    #ticketModal {
        transition: opacity 0.2s ease;
    }

    #ticketContent {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
</style>

<!-- Z-INDEX 9999 memastikan di atas semua elemen -->
<div id="ticketModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 sm:p-6 opacity-0">
    <!-- Backdrop Blur -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md cursor-pointer" onclick="closeTicketModal()"></div>

    <!-- Wrapper Modal -->
    <div id="ticketContent"
        class="relative z-10 w-full max-w-5xl bg-[#111126] rounded-3xl shadow-[0_25px_50px_rgba(0,0,0,0.5)] flex flex-col overflow-hidden scale-95 opacity-0 max-h-[90vh]">

        <!-- Header Controls -->
        <div class="px-6 py-4 border-b bg-white/5 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <span id="mdl-ticket-no" class="text-blue-400 font-bold text-sm tracking-wider"></span>
                <span id="mdl-category"
                    class="px-2 py-0.5 text-[9px] uppercase font-bold rounded bg-white/10 text-white tracking-widest hidden"></span>
            </div>
            <button onclick="closeTicketModal()"
                class="w-8 h-8 rounded-full bg-white/10 hover:bg-red-500/20 text-white/70 hover:text-red-400 flex items-center justify-center transition-all border-transparent">
                <i class="ph-bold ph-x text-sm"></i>
            </button>
        </div>

        <!-- Loader Layout -->
        <div id="mdl-loader" class="flex-1 flex flex-col items-center justify-center p-20 min-h-[400px]">
            <i class="ph ph-spinner-gap animate-spin text-5xl text-blue-500 mb-4"></i>
            <p class="text-white/50 text-sm font-medium tracking-wider uppercase animate-pulse">Menarik Data Tiket...
            </p>
        </div>

        <!-- Content Layout Terbelah (Kiri Utama, Kanan Atribut) -->
        <div id="mdl-content-body" class="hidden flex-1 overflow-hidden flex-col md:flex-row">

            <!-- Sisi Kiri (Teks Utama, Checklist, Komentar) -->
            <div class="flex-[1.8] p-6 md:p-8 overflow-y-auto table-scroll bg-black/20"
                style="background-image: radial-gradient(circle at top right, rgba(67,97,238,0.05) 0%, transparent 50%);">

                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span id="mdl-status"
                        class="px-2.5 py-1 text-[10px] uppercase font-bold rounded-full tracking-widest"></span>
                    <span id="mdl-priority"
                        class="px-2.5 py-1 text-[10px] uppercase font-bold rounded-full tracking-widest"></span>
                </div>

                <h2 id="mdl-title" class="text-2xl font-bold text-white leading-snug mb-6"></h2>

                <!-- Deskripsi Pekerjaan -->
                <div class="bg-black/30 rounded-xl p-5 mb-8">
                    <h4 class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-3 flex items-center"><i
                            class="ph-fill ph-text-align-left mr-1.5 text-sm"></i> Deskripsi Pekerjaan</h4>
                    <div id="mdl-desc" class="text-sm text-white/80 leading-relaxed whitespace-pre-wrap font-medium">
                    </div>
                </div>

                <!-- Sub-Task Checklists (Injeksi AJAX) -->
                <div id="mdl-checklists-container" class="mb-8 hidden"></div>

                <!-- Diskusi / Komentar (Injeksi AJAX) -->
                <div class="border-t pt-6">
                    <h4 class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-4 flex items-center"><i
                            class="ph-fill ph-chats mr-1.5 text-sm"></i> Diskusi & Aktivitas</h4>

                    <div id="mdl-comments-container" class="space-y-3 mb-5"></div>

                    <!-- Form Input Komentar -->
                    <form onsubmit="submitComment(event)" class="bg-black/30 p-3 rounded-2xl flex items-end gap-3 mt-4">
                        <input type="hidden" name="ticket_id" id="mdl-comment-ticket-id">
                        <div class="flex-1">
                            <textarea name="comment_text" rows="1"
                                class="w-full bg-transparent border-0 text-sm text-white focus:ring-0 resize-none px-2 py-1 placeholder-white/30"
                                placeholder="Tulis komentar atau update progres..." required></textarea>
                        </div>
                        <button type="submit"
                            class="w-10 h-10 shrink-0 rounded-xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center transition-colors">
                            <i class="ph-fill ph-paper-plane-right"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sisi Kanan (Metadata, File, Aksi) -->
            <div
                class="flex-1 p-6 md:p-8 border-t md:border-t-0 md:border-l bg-[#1a1a35] flex flex-col overflow-y-auto table-scroll">

                <h3 class="text-sm font-bold text-white mb-6 border-b pb-3 flex items-center">
                    <i class="ph-fill ph-info text-blue-400 mr-2 text-lg"></i> Informasi Tiket
                </h3>

                <div class="space-y-6 flex-1">
                    <div>
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-2">Assignee (PIC)</p>
                        <div id="mdl-pic-container" class="flex items-center"></div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-2">Tenggat Waktu</p>
                        <p id="mdl-date" class="text-sm font-bold flex items-center"></p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-2">Dibuat Oleh /
                            Tanggal</p>
                        <p id="mdl-creator" class="text-sm font-medium text-white/60"></p>
                    </div>

                    <!-- Lampiran File (Injeksi AJAX) -->
                    <div id="mdl-attachments-section" class="hidden border-t pt-6 mt-6">
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-3">Lampiran File</p>
                        <div id="mdl-attachments-container" class="space-y-2"></div>
                    </div>
                </div>

                <!-- Footer Modal (Hanya Tampil Jika User = Leader) -->
                <?php if ($user['role_global'] === 'leader'): ?>
                    <div class="pt-6 mt-6 border-t space-y-3 shrink-0">
                        <a id="mdl-edit-btn" href="#"
                            class="w-full py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold shadow-[0_0_15px_rgba(67,97,238,0.3)] transition-all active:scale-95 flex items-center justify-center">
                            <i class="ph-bold ph-pencil-simple mr-1.5"></i> Edit / Tambah Sub-Task
                        </a>
                        <form id="mdl-delete-form" action="<?= base_url('tickets/delete') ?>" method="POST" class="m-0"
                            onsubmit="return confirm('Hapus tugas ini secara permanen?')">
                            <input type="hidden" name="id" id="mdl-delete-id" value="">
                            <button type="submit"
                                class="w-full py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-bold transition-colors border-red-500/20 flex items-center justify-center">
                                <i class="ph-bold ph-trash mr-1.5"></i> Hapus Tugas
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     CLIENT-SIDE SCRIPTS (AJAX & DOM MANIPULATION)
     ============================================================== -->
<script>
    // 1. UPDATE STATUS LANGSUNG DARI TABEL (OPTIMISTIC UI — No Reload)
    function updateTicketStatus(ticketId, selectEl) {
        const status = selectEl.value;
        const wrapper = selectEl.parentElement; // .relative.inline-flex
        const colorMap = {
            'open': 'bg-white/10 text-white ',
            'in_progress': 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            'review': 'bg-purple-500/20 text-purple-300 border-purple-500/30',
            'done': 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
            'blocked': 'bg-red-500/20 text-red-300 border-red-500/30',
            'cancelled': 'bg-gray-500/20 text-gray-400 '
        };

        fetch('<?= base_url('tickets/update-status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${ticketId}&status=${status}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Optimistic: update the select's colour classes in place
                    const allClasses = Object.values(colorMap).join(' ').split(' ').filter(Boolean);
                    selectEl.classList.remove(...allClasses);
                    const newClasses = (colorMap[status] || colorMap['open']).trim().split(' ');
                    selectEl.classList.add(...newClasses.filter(Boolean));
                } else {
                    alert('Sistem: Gagal mengubah status tugas.');
                    // Revert select to original value would need the old value cached;
                    // simple fallback: reload only on failure
                    location.reload();
                }
            }).catch(err => {
                console.error(err);
                location.reload();
            });
    }

    // 2. MEMBUKA MODAL & MENARIK DATA DETAIL API
    function openTicketModal(ticketId) {
        // FIX STACKING CONTEXT: Angkat z-index main container agar modal tidak tertutup sidebar/topbar
        const mainWrapper = document.getElementById('main-wrapper');
        const mainScroll = document.getElementById('main-scroll-area');
        if (mainWrapper) mainWrapper.style.zIndex = '1090'; // Di atas sidebar (1080)
        if (mainScroll) mainScroll.style.zIndex = '1090';   // Di atas topbar (1030)

        const modal = document.getElementById('ticketModal');
        const contentBox = document.getElementById('ticketContent');
        const loader = document.getElementById('mdl-loader');
        const body = document.getElementById('mdl-content-body');

        // Setup state loading
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        contentBox.classList.remove('scale-95', 'opacity-0');
        contentBox.classList.add('scale-100', 'opacity-100');

        loader.classList.remove('hidden');
        body.classList.add('hidden');
        body.classList.remove('flex');

        // Fetch Detail Tiket beserta Relasinya
        fetch('<?= base_url('tickets/api-detail?id=') ?>' + ticketId)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    alert('Sistem: ' + (res.error || 'Gagal memuat tiket'));
                    closeTicketModal();
                    return;
                }

                renderModalData(res);

                loader.classList.add('hidden');
                body.classList.remove('hidden');
                body.classList.add('flex');
            })
            .catch(err => {
                console.error(err);
                alert('Gagal terhubung ke server saat memuat data tiket.');
                closeTicketModal();
            });
    }

    // 3. MERENDER DATA JSON KE DOM MODAL
    function renderModalData(res) {
        const t = res.ticket;
        const checklists = res.checklists;
        const comments = res.comments;
        const attachments = res.attachments;

        // Header Kiri
        const tNoParts = t.ticket_no.split('-');
        document.getElementById('mdl-ticket-no').innerText = '#' + (tNoParts[2] || t.ticket_no);
        document.getElementById('mdl-title').innerText = t.title || 'Untitled';

        const catBadge = document.getElementById('mdl-category');
        if (t.category_name) {
            catBadge.innerText = t.category_name;
            catBadge.classList.remove('hidden');
        } else {
            catBadge.classList.add('hidden');
        }

        // Badge Status & Prioritas
        const statusBadge = document.getElementById('mdl-status');
        statusBadge.innerText = t.ticket_status.replace('_', ' ');
        statusBadge.className = 'px-2.5 py-1 text-[10px] uppercase font-bold rounded-full  tracking-widest ';
        const statusColors = {
            'open': 'bg-white/10 text-white ',
            'in_progress': 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            'review': 'bg-purple-500/20 text-purple-300 border-purple-500/30',
            'done': 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
            'blocked': 'bg-red-500/20 text-red-300 border-red-500/30',
            'cancelled': 'bg-gray-500/20 text-gray-400 '
        };
        statusBadge.className += (statusColors[t.ticket_status] || statusColors['open']);

        const pBadge = document.getElementById('mdl-priority');
        pBadge.innerText = t.priority;
        pBadge.className = 'px-2.5 py-1 text-[10px] uppercase font-bold rounded-full  tracking-widest ';
        if (t.priority === 'high') pBadge.classList.add('bg-orange-500/20', 'text-orange-400', 'border-orange-500/30');
        else if (t.priority === 'urgent') pBadge.classList.add('bg-red-500/20', 'text-red-400', 'border-red-500/30');
        else pBadge.classList.add('bg-white/10', 'text-white/50', '');

        // Deskripsi Pekerjaan
        const emptyState = '<span class="text-white/30 italic">Tidak ada deskripsi yang dilampirkan pada tugas ini...</span>';
        document.getElementById('mdl-desc').innerHTML = t.description ? escapeHtml(t.description) : emptyState;

        // SISI KANAN: Avatar & WA Button (Hanya jika ada Nomor dan Khusus Leader)
        const picContainer = document.getElementById('mdl-pic-container');
        if (t.assigned_to) {
            let avatarHtml = '';
            const nameInitial = t.assignee_name ? t.assignee_name.charAt(0).toUpperCase() : 'U';

            if (t.assignee_avatar) {
                avatarHtml = `<img src="<?= base_url() ?>/${t.assignee_avatar}?v=<?= time() ?>" class="w-8 h-8 rounded-full object-cover shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(t.assignee_name || 'User')}&background=4361ee&color=fff&size=128';">`;
            } else {
                avatarHtml = `<div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs shrink-0" style="background: linear-gradient(135deg, #4361ee, #7209b7);">${nameInitial}</div>`;
            }

            let waHtml = '';
            <?php if ($user['role_global'] === 'leader'): ?>
                if (t.assignee_phone) {
                    let waText = encodeURIComponent(`Halo ${t.assignee_name.split(' ')[0]}, reminder untuk tugas *${t.title}*. Mohon di-update progressnya ya. Semangat! 🚀`);
                    waHtml = `<a href="https://wa.me/${t.assignee_phone.replace(/[^0-9]/g, '')}?text=${waText}" target="_blank" onclick="event.stopPropagation()" class="text-emerald-400 bg-emerald-500/10 ml-3 px-2.5 py-1 rounded-2xl border-emerald-500/20 hover:bg-emerald-500/20 transition-all flex items-center gap-1.5 shrink-0" title="Ping via WA"><i class="ph-fill ph-whatsapp-logo text-sm"></i> <span class="text-[9px] uppercase tracking-widest font-bold">Reminder</span></a>`;
                }
            <?php endif; ?>

            picContainer.innerHTML = `${avatarHtml} <span class="text-sm font-bold text-white/90 ml-3 truncate max-w-[150px]">${escapeHtml(t.assignee_name || 'User')}</span> ${waHtml}`;
        } else {
            picContainer.innerHTML = `<div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-white/30 shrink-0"><i class="ph-fill ph-user-minus"></i></div> <span class="text-sm font-medium text-white/40 ml-3 italic">Unassigned</span>`;
        }

        // Metadata Kanan
        document.getElementById('mdl-creator').innerHTML = `${escapeHtml(t.creator_name || 'Sistem')} <br><span class="text-[10px] text-white/40 font-normal">${t.created_at_formatted}</span>`;

        if (t.due_at) {
            const d = new Date(t.due_at);
            const isOverdue = d.getTime() < Date.now() && !['done', 'cancelled'].includes(t.ticket_status);
            document.getElementById('mdl-date').innerHTML = `<i class="ph-bold ph-calendar-blank mr-1.5"></i> <span>${t.due_at_formatted}</span>`;
            document.getElementById('mdl-date').className = `text-sm font-bold flex items-center ${isOverdue ? 'text-red-400' : 'text-emerald-300'}`;
        } else {
            document.getElementById('mdl-date').innerHTML = `<i class="ph-bold ph-calendar-blank mr-1.5"></i> <span>Tanpa Tenggat</span>`;
            document.getElementById('mdl-date').className = 'text-sm font-medium text-white/40 flex items-center italic';
        }

        // --- SUB-TASKS / CHECKLIST RENDER ---
        const chkContainer = document.getElementById('mdl-checklists-container');
        if (checklists.length > 0) {
            let chkHtml = '<h4 class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-3 flex items-center"><i class="ph-bold ph-check-square-offset mr-1.5 text-sm"></i> Sub-Tasks / Checklist</h4><div class="space-y-2">';
            checklists.forEach(c => {
                const isChecked = c.is_done == 1 ? 'checked' : '';
                const lineThrough = c.is_done == 1 ? 'line-through text-white/40' : 'text-white/90';
                chkHtml += `
                    <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 cursor-pointer hover:bg-white/10 transition">
                        <input type="checkbox" class="mt-0.5 rounded bg-black/50 text-blue-500 focus:ring-0 cursor-pointer" onchange="toggleChecklistAPI(${c.id}, this)" ${isChecked}>
                        <span class="text-sm font-medium ${lineThrough} transition-all">${escapeHtml(c.item_text)}</span>
                    </label>
                `;
            });
            chkHtml += '</div>';
            chkContainer.innerHTML = chkHtml;
            chkContainer.classList.remove('hidden');
        } else {
            chkContainer.classList.add('hidden');
            chkContainer.innerHTML = '';
        }

        // --- COMMENTS RENDER ---
        const cmtContainer = document.getElementById('mdl-comments-container');
        if (comments.length > 0) {
            let cmtHtml = '';
            comments.forEach(c => {
                let avatar = c.author_avatar
                    ? `<img src="<?= base_url() ?>/${c.author_avatar}" class="w-8 h-8 rounded-full object-cover shrink-0">`
                    : `<div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-[10px] font-bold text-white shrink-0">${c.author_name.charAt(0).toUpperCase()}</div>`;

                let date = new Date(c.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });

                cmtHtml += `
                    <div class="flex gap-3 bg-white/5 p-4 rounded-2xl">
                        ${avatar}
                        <div>
                            <div class="flex items-baseline gap-2 mb-1.5">
                                <span class="text-xs font-bold text-white/90">${escapeHtml(c.author_name)}</span>
                                <span class="text-[9px] text-white/40 font-medium">${date}</span>
                            </div>
                            <p class="text-sm text-white/80 leading-relaxed whitespace-pre-wrap">${escapeHtml(c.comment_text)}</p>
                        </div>
                    </div>
                `;
            });
            cmtContainer.innerHTML = cmtHtml;
        } else {
            cmtContainer.innerHTML = '<p class="text-xs text-white/30 italic text-center py-5 bg-white/5 rounded-xl">Belum ada diskusi atau komentar. Jadilah yang pertama!</p>';
        }

        // Set ID di Form Komentar
        document.getElementById('mdl-comment-ticket-id').value = t.id;

        // --- ATTACHMENTS RENDER ---
        const attSection = document.getElementById('mdl-attachments-section');
        const attContainer = document.getElementById('mdl-attachments-container');
        if (attachments.length > 0) {
            let attHtml = '';
            attachments.forEach(a => {
                attHtml += `
                    <a href="<?= base_url() ?>/${a.file_path}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition group text-decoration-none">
                        <div class="w-8 h-8 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                            <i class="ph-fill ph-file text-lg"></i>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-bold text-white/80 truncate group-hover:text-blue-300 transition">${escapeHtml(a.file_name)}</p>
                            <p class="text-[9px] text-white/40 mt-0.5">${escapeHtml(a.uploader_name || 'System')}</p>
                        </div>
                        <i class="ph-bold ph-download-simple text-white/20 group-hover:text-white/80 transition-colors mr-2"></i>
                    </a>
                `;
            });
            attContainer.innerHTML = attHtml;
            attSection.classList.remove('hidden');
        } else {
            attSection.classList.add('hidden');
            attContainer.innerHTML = '';
        }

        // Tautkan Action Buttons (Edit & Hapus Khusus Leader)
        const editBtn = document.getElementById('mdl-edit-btn');
        const delId = document.getElementById('mdl-delete-id');
        if (editBtn) editBtn.href = '<?= base_url('tickets/edit?id=') ?>' + t.id;
        if (delId) delId.value = t.id;
    }

    // 4. API TUGAS CENTANG CHECKLIST (AJAX)
    function toggleChecklistAPI(id, checkbox) {
        const isDone = checkbox.checked ? 1 : 0;
        fetch('<?= base_url('tickets/toggle-checklist') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&is_done=${isDone}`
        })
            .then(r => r.json())
            .then(d => {
                if (!d.success) {
                    alert('Gagal memperbarui progres sub-task.');
                    checkbox.checked = !isDone;
                } else {
                    const span = checkbox.nextElementSibling;
                    if (isDone) span.classList.add('line-through', 'text-white/40');
                    else span.classList.remove('line-through', 'text-white/40');
                }
            });
    }

    // 5. API POST KOMENTAR (AJAX) Tanpa Reload Penuh
    function submitComment(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const btn = form.querySelector('button');
        const originalIcon = btn.innerHTML;

        btn.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i>';
        btn.disabled = true;

        fetch('<?= base_url('tickets/store-comment') ?>', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    form.reset();
                    // Tarik ulang data (merefresh komentar saja secara visual)
                    openTicketModal(formData.get('ticket_id'));
                } else {
                    alert('Sistem: ' + data.message);
                }
            })
            .finally(() => {
                btn.innerHTML = originalIcon;
                btn.disabled = false;
            });
    }

    function closeTicketModal() {
        const modal = document.getElementById('ticketModal');
        const contentBox = document.getElementById('ticketContent');

        modal.classList.add('opacity-0');
        contentBox.classList.remove('scale-100', 'opacity-100');
        contentBox.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // RESTORE STACKING CONTEXT
            const mainWrapper = document.getElementById('main-wrapper');
            const mainScroll = document.getElementById('main-scroll-area');
            if (mainWrapper) mainWrapper.style.zIndex = '10';
            if (mainScroll) mainScroll.style.zIndex = '10';
        }, 200);
    }

    function escapeHtml(unsafe) {
        return (unsafe || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === "Escape") closeTicketModal();
    });
</script>