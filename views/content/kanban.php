<?php
/**
 * ==============================================================================
 * HOOKJOURNEY - GROWTH OPS HUB
 * ==============================================================================
 * File       : views/content/kanban.php
 * Deskripsi  : Content Planner Multi-View (Board, Table, Calendar, Timeline).
 * Fitur      : Minimalist UI, Dense Data Layout, Drag & Drop, Interactive Modal.
 * ==============================================================================
 */

// Helper untuk Badge Status
function getStatusBadge($status)
{
    $map = [
        'idea' => ['bg' => 'bg-gray-500/20', 'text' => 'text-gray-400', 'label' => 'Ide Konten', 'dot' => 'bg-gray-400'],
        'script_ready' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'label' => 'Script Ready', 'dot' => 'bg-blue-400'],
        'production' => ['bg' => 'bg-orange-500/20', 'text' => 'text-orange-400', 'label' => 'Produksi', 'dot' => 'bg-orange-400'],
        'editing' => ['bg' => 'bg-purple-500/20', 'text' => 'text-purple-400', 'label' => 'Editing', 'dot' => 'bg-purple-400'],
        'ready_post' => ['bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-400', 'label' => 'Siap Tayang', 'dot' => 'bg-emerald-400'],
        'scheduled' => ['bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-400', 'label' => 'Terjadwal', 'dot' => 'bg-emerald-400'],
        'published' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-400', 'label' => 'Published', 'dot' => 'bg-green-400'],
    ];
    $s = $map[$status] ?? $map['idea'];
    return "<div class=\"flex items-center px-2 py-0.5 w-max rounded   {$s['bg']} {$s['text']}\">
                <div class=\"w-1.5 h-1.5 rounded-full {$s['dot']} mr-1.5\"></div>
                <span class=\"text-[9px] font-bold uppercase tracking-wider\">{$s['label']}</span>
            </div>";
}

// Helper untuk Badge Prioritas
function getPriorityBadge($priority)
{
    $map = [
        'high' => 'bg-red-500/20 text-red-400',
        'medium' => 'bg-orange-500/20 text-orange-400',
        'low' => 'bg-blue-500/20 text-blue-400',
    ];
    $c = $map[$priority] ?? $map['medium'];
    return "<span class=\"px-1.5 py-0.5 text-[8px] uppercase font-bold rounded   tracking-widest {$c}\">{$priority}</span>";
}

// Helper untuk Ikon Format Konten (Pengganti Thumbnail)
function getFormatIcon($type)
{
    if (strpos($type, 'video') !== false || $type == 'reels' || $type == 'tiktok')
        return 'ph-video-camera';
    if ($type == 'carousel')
        return 'ph-images';
    if ($type == 'story')
        return 'ph-device-mobile';
    return 'ph-image';
}
?>

<style>
    /* Custom Scrollbar */
    .kanban-scroll::-webkit-scrollbar,
    .table-scroll::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }

    .kanban-scroll::-webkit-scrollbar-track,
    .table-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 8px;
        margin: 0 10px;
    }

    .kanban-scroll::-webkit-scrollbar-thumb,
    .table-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    .kanban-scroll::-webkit-scrollbar-thumb:hover,
    .table-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    .column-body::-webkit-scrollbar {
        display: none;
    }

    .column-body {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Drag effects */
    .kanban-column.drag-over {
        background: rgba(255, 255, 255, 0.06) !important;
        border-color: rgba(96, 165, 250, 0.4) !important;
    }

    .content-card.is-dragging {
        opacity: 0.5;
        transform: scale(0.95) rotate(-2deg);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        cursor: grabbing !important;
    }

    .content-card {
        cursor: grab;
    }

    /* Modal Animation */
    #briefModal {
        transition: opacity 0.2s ease;
    }

    #briefContent {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Data Grid Utilities */
    .data-grid th {
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    .data-grid td {
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    .sticky-left {
        position: sticky;
        left: 0;
        z-index: 10;
        background: #121226;
    }
</style>

<!-- ==============================================================
     HEADER & ACTION BAR
     ============================================================== -->
<div class="mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Content Planner</h1>
        <p class="text-white/60 text-sm mt-1">Kelola lini masa konten untuk <strong
                class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <!-- Toggle Switcher (4 Mode) -->
        <div class="flex items-center bg-black/40 p-1.5 rounded-[12px] backdrop-blur-md">
            <a href="?v=kanban"
                class="flex items-center px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-all duration-200 <?= $activeView == 'kanban' ? 'bg-white/10 text-white ' : 'text-white/40 hover:text-white hover:bg-white/5' ?>"
                title="Kanban Board">
                <i class="ph-fill ph-kanban mr-1.5 text-base"></i> Board
            </a>
            <a href="?v=table"
                class="flex items-center px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-all duration-200 <?= $activeView == 'table' ? 'bg-white/10 text-white ' : 'text-white/40 hover:text-white hover:bg-white/5' ?>"
                title="Data Grid">
                <i class="ph-fill ph-table mr-1.5 text-base"></i> Table
            </a>
            <a href="?v=calendar"
                class="flex items-center px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-all duration-200 <?= $activeView == 'calendar' ? 'bg-white/10 text-white ' : 'text-white/40 hover:text-white hover:bg-white/5' ?>"
                title="Editorial Calendar">
                <i class="ph-fill ph-calendar-blank mr-1.5 text-base"></i> Calendar
            </a>
            <a href="?v=timeline"
                class="flex items-center px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-all duration-200 <?= $activeView == 'timeline' ? 'bg-white/10 text-white ' : 'text-white/40 hover:text-white hover:bg-white/5' ?>"
                title="Gantt Chart">
                <i class="ph-fill ph-sliders-horizontal mr-1.5 text-base"></i> Timeline
            </a>
        </div>

        <a href="<?= base_url('content/create') ?>"
            class="flex items-center justify-center px-4 py-2 rounded-[10px] shadow-[0_0_15px_rgba(67,97,238,0.2)] text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-[0.98] transition-all text-decoration-none">
            <i class="ph-bold ph-plus text-base mr-1.5"></i> Buat Ide Baru
        </a>
    </div>
</div>

<!-- ==============================================================
     VIEW 1: KANBAN BOARD (COMPACT & MINIMALIST)
     ============================================================== -->
<?php if ($activeView === 'kanban'): ?>
    <div class="kanban-scroll flex gap-4 overflow-x-auto pb-6 pt-1 items-start"
        style="min-height: 70vh; position: relative; z-index: 40;">
        <?php foreach ($kanbanColumns as $status => $col): ?>
            <div class="kanban-column flex flex-col flex-shrink-0 w-[290px] rounded-[16px] bg-white/5 backdrop-blur-md transition-all duration-300 max-h-[75vh]"
                data-status="<?= $status ?>" ondragover="allowDrop(event)" ondragleave="leaveDrop(event)" ondrop="drop(event)">

                <div class="p-3 border-b flex justify-between items-center bg-black/20 rounded-t-[16px]">
                    <div class="flex items-center gap-2">
                        <?php
                        $dotColor = 'bg-gray-400';
                        if ($status == 'idea')
                            $dotColor = 'bg-white/50';
                        if ($status == 'planning')
                            $dotColor = 'bg-blue-400';
                        if ($status == 'production')
                            $dotColor = 'bg-orange-400';
                        if ($status == 'review')
                            $dotColor = 'bg-purple-400';
                        if ($status == 'scheduled' || $status == 'published')
                            $dotColor = 'bg-emerald-400';
                        ?>
                        <div class="w-2 h-2 rounded-full <?= $dotColor ?> shadow-[0_0_5px_currentColor]"></div>
                        <h3 class="font-bold text-white/90 text-[11px] uppercase tracking-wider"><?= $col['title'] ?></h3>
                    </div>
                    <span
                        class="col-counter flex items-center justify-center min-w-[20px] h-[20px] px-1 text-[10px] font-bold text-white/70 bg-white/10 rounded-2xl">
                        <?= count($col['items']) ?>
                    </span>
                </div>

                <div class="column-body flex-1 overflow-y-auto p-3 flex flex-col gap-3 min-h-[100px]">
                    <div class="empty-placeholder text-center py-6 rounded-[12px] text-white/30 text-[10px] font-medium flex-col items-center justify-center"
                        style="<?= empty($col['items']) ? 'display: flex;' : 'display: none;' ?>">
                        <i class="ph-fill ph-tray text-2xl mb-1.5 opacity-30"></i> Kosong
                    </div>

                    <?php if (!empty($col['items'])): ?>
                        <?php foreach ($col['items'] as $item): ?>

                            <!-- COMPACT CONTENT CARD -->
                            <div class="content-card p-3 rounded-[12px] relative bg-[#0f0c29]/70 hover:bg-white/10 hover: hover:-translate-y-0.5 hover: transition-all duration-200 group"
                                draggable="true" ondragstart="drag(event)" id="card-<?= $item['id'] ?>" data-id="<?= $item['id'] ?>">

                                <div onclick="openContentModal(this)"
                                    data-json="<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>" class="cursor-pointer">

                                    <!-- Header Card: Badges -->
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <?= getPriorityBadge($item['priority']) ?>
                                            <span
                                                class="px-1.5 py-0.5 text-[8px] uppercase font-bold rounded bg-white/5 text-white/60 tracking-widest flex items-center">
                                                <i class="ph-fill <?= getFormatIcon($item['content_type']) ?> mr-1"></i>
                                                <?= str_replace('_', ' ', $item['content_type']) ?>
                                            </span>
                                        </div>

                                        <!-- Context Menu Trigger -->
                                        <div class="dropdown" onclick="event.stopPropagation()">
                                            <button
                                                class="flex items-center justify-center w-5 h-5 rounded hover:bg-white/10 text-white/30 hover:text-white transition-colors border-0 p-0"
                                                data-bs-toggle="dropdown">
                                                <i class="ph-bold ph-dots-three text-base"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-dark p-1"
                                                style="border-radius: 12px; background: rgba(15,12,41,0.95); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.15); min-width: 130px; z-index: 100;">
                                                <li>
                                                    <a class="dropdown-item py-1.5 px-2.5 rounded-2xl flex items-center text-[11px] text-white/80 hover:text-white hover:bg-white/10"
                                                        href="<?= base_url('content/edit?id=' . $item['id']) ?>">
                                                        <i class="ph ph-pencil-simple mr-1.5"></i> Edit Setup
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider my-1">
                                                </li>
                                                <li>
                                                    <form action="<?= base_url('content/delete') ?>" method="POST" class="m-0"
                                                        onsubmit="return confirm('Hapus ide konten ini?')">
                                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                        <button type="submit"
                                                            class="dropdown-item w-full py-1.5 px-2.5 rounded-2xl flex items-center text-[11px] text-red-400 hover:bg-red-500/20">
                                                            <i class="ph ph-trash mr-1.5"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4
                                        class="font-semibold text-white/90 mb-2.5 text-[13px] leading-snug line-clamp-2 group-hover:text-blue-300 transition-colors pr-2">
                                        <?= htmlspecialchars($item['title']) ?></h4>

                                    <!-- Objective Minimal -->
                                    <div class="flex items-center mb-3">
                                        <span class="text-[9px] text-white/40 uppercase tracking-widest font-bold flex items-center">
                                            <i class="ph-fill ph-target mr-1 text-white/30"></i> <?= htmlspecialchars($item['goal']) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Footer: Meta & Users -->
                                <div class="flex items-center justify-between pt-2.5 border-t">

                                    <div class="flex items-center gap-1.5">
                                        <?php if ($item['pic_user_id']): ?>
                                            <?php if (!empty($item['pic_avatar_url'])): ?>
                                                <img src="<?= base_url($item['pic_avatar_url']) ?>" class="w-5 h-5 rounded-full object-cover"
                                                    title="<?= htmlspecialchars($item['pic_name']) ?>"
                                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($item['pic_name']) ?>&background=4361ee&color=fff&size=128';">
                                            <?php else: ?>
                                                <div class="w-5 h-5 rounded-full flex items-center justify-center text-white font-bold text-[8px]"
                                                    style="background: linear-gradient(135deg, #4361ee, #7209b7);"
                                                    title="<?= htmlspecialchars($item['pic_name']) ?>">
                                                    <?= strtoupper(substr($item['pic_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($item['pic_phone'])): ?>
                                                <?php
                                                $waText = urlencode("Halo " . explode(' ', $item['pic_name'])[0] . ", reminder task konten *" . $item['title'] . "* (" . ($item['scheduled_post_at'] ? date('d M', strtotime($item['scheduled_post_at'])) : 'Draft') . "). Mohon di-update ya. Terimakasih!🚀");
                                                ?>
                                                <a href="https://wa.me/<?= $item['pic_phone'] ?>?text=<?= $waText ?>" target="_blank"
                                                    onclick="event.stopPropagation()"
                                                    class="text-emerald-400 bg-emerald-500/10 w-5 h-5 flex items-center justify-center rounded-full hover:bg-emerald-500/20 hover:scale-110 transition-all border-emerald-500/20"
                                                    title="Ping via WA">
                                                    <i class="ph-fill ph-whatsapp-logo text-[11px]"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="w-5 h-5 rounded-full flex items-center justify-center bg-white/5 text-white/30"
                                                title="Unassigned"><i class="ph-fill ph-user text-[9px]"></i></div>
                                        <?php endif; ?>
                                    </div>

                                    <?php
                                    $isDraft = empty($item['scheduled_post_at']);
                                    $dateClass = $isDraft ? 'text-white/30 bg-white/5' : 'text-blue-300 bg-blue-500/10 border-blue-500/20';
                                    ?>
                                    <div
                                        class="text-[9px] font-medium flex items-center px-1.5 py-0.5 rounded border-transparent <?= $dateClass ?>">
                                        <i class="ph-bold ph-calendar-blank mr-1"></i>
                                        <?= $isDraft ? 'Draft' : date('d M', strtotime($item['scheduled_post_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ==============================================================
     VIEW 2: TABLE (DATA GRID) VIEW - MINIMALIST
     ============================================================== -->
<?php elseif ($activeView === 'table'): ?>
    <div
        class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[20px] relative z-40 overflow-hidden flex flex-col h-[75vh]">
        <div class="px-5 py-4 border-b bg-white/5 flex justify-between items-center">
            <h3 class="font-bold text-white/90 text-sm">Database Konten</h3>
            <span class="text-[10px] text-white/50 bg-black/20 px-2.5 py-1 rounded-2xl font-bold"><?= count($rawItems) ?>
                Ide Terdaftar</span>
        </div>

        <div class="table-scroll overflow-auto flex-1">
            <table class="w-full text-left whitespace-nowrap min-w-[900px] data-grid">
                <thead class="bg-black/40 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-[9px] font-bold text-white/50 uppercase tracking-widest sticky-left">Judul
                            & Format Konten</th>
                        <th class="px-4 py-3 text-[9px] font-bold text-white/50 uppercase tracking-widest">Status</th>
                        <th class="px-4 py-3 text-[9px] font-bold text-white/50 uppercase tracking-widest">Prioritas</th>
                        <th class="px-4 py-3 text-[9px] font-bold text-white/50 uppercase tracking-widest">Objektif</th>
                        <th class="px-4 py-3 text-[9px] font-bold text-white/50 uppercase tracking-widest">Tenggat</th>
                        <th class="px-4 py-3 text-[9px] font-bold text-white/50 uppercase tracking-widest">Assignee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 bg-transparent">
                    <?php if (empty($rawItems)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-white/40 text-sm">Belum ada konten dibuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rawItems as $item): ?>
                            <tr class="hover:bg-white/5 transition-colors group cursor-pointer" onclick="openContentModal(this)"
                                data-json="<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>">
                                <td class="px-4 py-3 sticky-left group-hover:bg-[#15152b] transition-colors border-r">
                                    <p class="text-[13px] font-semibold text-white/90 truncate max-w-[300px] group-hover:text-blue-300"
                                        title="<?= htmlspecialchars($item['title']) ?>"><?= htmlspecialchars($item['title']) ?></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-[9px] text-white/40 uppercase tracking-widest font-bold flex items-center">
                                            <i class="ph-fill <?= getFormatIcon($item['content_type']) ?> mr-1"></i>
                                            <?= str_replace('_', ' ', $item['content_type']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3"><?= getStatusBadge($item['content_status']) ?></td>
                                <td class="px-4 py-3"><?= getPriorityBadge($item['priority']) ?></td>
                                <td class="px-4 py-3 text-[11px] text-white/60 capitalize font-medium flex items-center"><i
                                        class="ph-fill ph-target mr-1.5 text-white/30"></i> <?= $item['goal'] ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($item['scheduled_post_at']): ?>
                                        <span
                                            class="text-[11px] text-blue-300 font-bold bg-blue-500/10 px-2 py-0.5 rounded border-blue-500/20"><i
                                                class="ph-bold ph-calendar-blank mr-1"></i>
                                            <?= date('d M Y', strtotime($item['scheduled_post_at'])) ?></span>
                                    <?php else: ?>
                                        <span class="text-[11px] text-white/30 italic">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($item['pic_user_id']): ?>
                                        <div class="flex items-center text-[11px] text-white/80 font-medium">
                                            <?php if (!empty($item['pic_avatar_url'])): ?>
                                                <img src="<?= base_url($item['pic_avatar_url']) ?>"
                                                    class="w-5 h-5 rounded-full object-cover mr-2 shrink-0"
                                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($item['pic_name']) ?>&background=4361ee&color=fff&size=128';">
                                            <?php else: ?>
                                                <div class="w-5 h-5 rounded-full flex items-center justify-center text-white font-bold text-[8px] mr-2 shrink-0"
                                                    style="background: linear-gradient(135deg, #4361ee, #7209b7);">
                                                    <?= strtoupper(substr($item['pic_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>

                                            <span class="truncate max-w-[120px]"><?= htmlspecialchars($item['pic_name']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-[11px] text-white/30 italic flex items-center"><i
                                                class="ph ph-user-minus mr-1.5"></i> Kosong</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==============================================================
     VIEW 3: CALENDAR VIEW
     ============================================================== -->
<?php elseif ($activeView === 'calendar'): ?>
    <div
        class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[20px] relative z-40 overflow-hidden flex flex-col min-h-[75vh]">

        <?php
        $prevMonth = date('Y-m', strtotime($firstDayOfMonth . ' -1 month'));
        $nextMonth = date('Y-m', strtotime($firstDayOfMonth . ' +1 month'));
        $currentLabel = date('F Y', strtotime($firstDayOfMonth));
        $monthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthsId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $currentLabel = str_replace($monthsEn, $monthsId, $currentLabel);
        ?>
        <div class="px-5 py-4 border-b bg-white/5 flex justify-between items-center">
            <h3 class="font-bold text-white/90 text-sm flex items-center">
                <i class="ph ph-calendar-check mr-2 text-blue-400"></i> Kalender Editorial
            </h3>
            <div class="flex items-center gap-2">
                <a href="?v=calendar&month=<?= $prevMonth ?>"
                    class="p-1.5 rounded-2xl bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition"><i
                        class="ph-bold ph-caret-left text-sm"></i></a>
                <span class="font-bold text-white w-28 text-center tracking-wide text-xs"><?= $currentLabel ?></span>
                <a href="?v=calendar&month=<?= $nextMonth ?>"
                    class="p-1.5 rounded-2xl bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition"><i
                        class="ph-bold ph-caret-right text-sm"></i></a>
            </div>
        </div>

        <div class="flex-1 p-5">
            <div class="grid grid-cols-7 gap-2.5 h-full">
                <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $dayName): ?>
                    <div class="text-center text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1.5">
                        <?= $dayName ?></div>
                <?php endforeach; ?>

                <?php
                $timestamp = strtotime($firstDayOfMonth);
                $daysInMonth = date('t', $timestamp);
                $startDayOfWeek = date('N', $timestamp);

                for ($i = 1; $i < $startDayOfWeek; $i++) {
                    echo '<div class="bg-transparent rounded-xl min-h-[90px]"></div>';
                }

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $currentDate = date('Y-m-', strtotime($firstDayOfMonth)) . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $isToday = ($currentDate === date('Y-m-d'));

                    $boxClass = $isToday ? 'bg-blue-900/10 border-blue-500/30 shadow-[0_0_15px_rgba(59,130,246,0.1)]' : 'bg-black/10  hover:';
                    $textClass = $isToday ? 'text-blue-400 font-bold bg-blue-500/10' : 'text-white/50 font-semibold';

                    echo '<div class="rounded-xl p-2 flex flex-col transition-colors ' . $boxClass . ' min-h-[90px] max-h-[140px] overflow-y-auto table-scroll">';
                    echo '<span class="text-[10px] inline-block w-6 h-6 flex items-center justify-center rounded-full mb-1.5 ' . $textClass . '">' . $day . '</span>';

                    if (isset($calendarData[$currentDate])) {
                        foreach ($calendarData[$currentDate] as $cItem) {
                            $isDone = in_array($cItem['content_status'], ['ready_post', 'scheduled', 'published']);
                            $dot = $isDone ? 'bg-emerald-400' : 'bg-orange-400';
                            $icon = getFormatIcon($cItem['content_type']);
                            $jsonAttr = htmlspecialchars(json_encode($cItem), ENT_QUOTES, 'UTF-8');

                            echo '<div onclick="openContentModal(this)" data-json="' . $jsonAttr . '" class="cursor-pointer block bg-white/5 hover:bg-white/10 p-1.5 rounded-2xl mb-1 transition group">';
                            echo '<div class="flex items-center gap-1 mb-1 text-white/40 group-hover:text-white/70 transition-colors">';
                            echo '<div class="w-1 h-1 rounded-full ' . $dot . ' shadow-[0_0_3px_currentColor]"></div>';
                            echo '<i class="ph-fill ' . $icon . ' text-[9px]"></i>';
                            echo '<span class="text-[7px] font-bold uppercase tracking-widest truncate">' . substr(str_replace('_', ' ', $cItem['content_type']), 0, 10) . '</span>';
                            echo '</div>';
                            echo '<p class="text-[10px] leading-tight text-white/80 font-medium truncate group-hover:text-blue-300 transition-colors">' . htmlspecialchars($cItem['title']) . '</p>';
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- ==============================================================
     VIEW 4: TIMELINE (GANTT CHART) VIEW
     ============================================================== -->
<?php elseif ($activeView === 'timeline'): ?>
    <div
        class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[20px] relative z-40 overflow-hidden flex flex-col h-[75vh]">

        <?php
        $prevMonth = date('Y-m', strtotime($firstDayOfMonth . ' -1 month'));
        $nextMonth = date('Y-m', strtotime($firstDayOfMonth . ' +1 month'));
        $currentLabel = date('F Y', strtotime($firstDayOfMonth));
        $monthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthsId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $currentLabel = str_replace($monthsEn, $monthsId, $currentLabel);

        $daysInMonth = date('t', strtotime($firstDayOfMonth));
        $monthStartTS = strtotime($firstDayOfMonth);
        $monthEndTS = strtotime(date('Y-m-t 23:59:59', $monthStartTS));

        $timelineItems = [];
        foreach ($rawItems as $item) {
            $start = strtotime($item['created_at']);
            $end = $item['scheduled_post_at'] ? strtotime($item['scheduled_post_at']) : $start;
            if ($end >= $monthStartTS && $start <= $monthEndTS) {
                $timelineItems[] = $item;
            }
        }
        ?>

        <div class="px-5 py-4 border-b bg-white/5 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-white/90 text-sm flex items-center">
                    <i class="ph ph-sliders-horizontal mr-2 text-blue-400"></i> Project Timeline
                </h3>
            </div>
            <div class="flex items-center gap-2">
                <a href="?v=timeline&month=<?= $prevMonth ?>"
                    class="p-1.5 rounded-2xl bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition"><i
                        class="ph-bold ph-caret-left text-sm"></i></a>
                <span class="font-bold text-white w-28 text-center tracking-wide text-xs"><?= $currentLabel ?></span>
                <a href="?v=timeline&month=<?= $nextMonth ?>"
                    class="p-1.5 rounded-2xl bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition"><i
                        class="ph-bold ph-caret-right text-sm"></i></a>
            </div>
        </div>

        <div class="flex flex-1 overflow-hidden">

            <div class="w-56 border-r bg-black/20 z-20 flex flex-col">
                <div
                    class="h-9 border-b px-4 flex items-center text-[9px] font-bold text-white/40 uppercase tracking-widest shrink-0">
                    Nama Konten</div>
                <div class="overflow-y-hidden flex-1 table-scroll" id="tl-left-scroll">
                    <?php foreach ($timelineItems as $item): ?>
                        <div class="h-10 border-b px-4 flex items-center cursor-pointer hover:bg-white/5 transition"
                            onclick="openContentModal(this)"
                            data-json="<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>">
                            <p class="text-[11px] text-white/80 font-medium truncate"
                                title="<?= htmlspecialchars($item['title']) ?>">
                                <?= htmlspecialchars($item['title']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex-1 overflow-x-auto overflow-y-auto table-scroll relative" id="tl-right-scroll"
                onscroll="document.getElementById('tl-left-scroll').scrollTop = this.scrollTop;">
                <div class="inline-block min-w-full">

                    <div class="flex h-9 border-b bg-white/5 sticky top-0 z-10">
                        <?php for ($i = 1; $i <= $daysInMonth; $i++): ?>
                            <div class="w-10 shrink-0 flex items-center justify-center border-r relative">
                                <?php $isToday = (date('Y-m-d') === date('Y-m-', $monthStartTS) . str_pad($i, 2, '0', STR_PAD_LEFT)); ?>
                                <span
                                    class="text-[9px] font-bold <?= $isToday ? 'text-blue-400 bg-blue-500/20 px-1.5 py-0.5 rounded' : 'text-white/40' ?>"><?= $i ?></span>
                                <?php if ($isToday): ?>
                                    <div class="absolute top-9 left-1/2 w-px h-[1000px] bg-blue-500/20 z-0 pointer-events-none">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <div class="relative">
                        <?php foreach ($timelineItems as $item): ?>
                            <div class="flex h-10 border-b relative group hover:bg-white/5 transition-colors">

                                <?php for ($i = 1; $i <= $daysInMonth; $i++): ?>
                                    <div class="w-10 shrink-0 border-r"></div>
                                <?php endfor; ?>

                                <?php
                                $start = strtotime($item['created_at']);
                                $end = $item['scheduled_post_at'] ? strtotime($item['scheduled_post_at']) : $start;
                                $pStart = max($start, $monthStartTS);
                                $pEnd = min($end, $monthEndTS);

                                $sDay = (int) date('j', $pStart);
                                $eDay = (int) date('j', $pEnd);
                                $duration = max(1, $eDay - $sDay + 1);

                                $leftPx = ($sDay - 1) * 40;
                                $widthPx = $duration * 40;

                                $barColor = 'bg-blue-500/70 border-blue-400 hover:bg-blue-400 text-blue-50';
                                if ($item['content_status'] == 'published' || $item['content_status'] == 'scheduled') {
                                    $barColor = 'bg-emerald-500/70 border-emerald-400 hover:bg-emerald-400 text-emerald-50';
                                } elseif ($item['priority'] == 'high') {
                                    $barColor = 'bg-red-500/70 border-red-400 hover:bg-red-400 text-red-50';
                                }
                                ?>
                                <div class="absolute top-1.5 h-7 rounded flex items-center px-1.5 cursor-pointer transition-all z-10 <?= $barColor ?>"
                                    style="left: <?= $leftPx + 3 ?>px; width: <?= $widthPx - 6 ?>px;"
                                    onclick="openContentModal(this)"
                                    data-json="<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>"
                                    title="Klik untuk melihat Detail">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ==============================================================
     MODAL PREVIEW CONTENT BRIEF (LIGHTBOX)
     ============================================================== -->
<div id="briefModal" class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 sm:p-6 opacity-0">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md cursor-pointer" onclick="closeContentModal()"></div>

    <div id="briefContent"
        class="relative z-10 w-full max-w-4xl bg-[#111126] rounded-2xl shadow-[0_25px_50px_rgba(0,0,0,0.5)] flex flex-col md:flex-row overflow-hidden scale-95 opacity-0 max-h-[90vh]">

        <button onclick="closeContentModal()"
            class="absolute top-4 right-4 z-50 w-8 h-8 rounded-full bg-white/10 backdrop-blur-sm text-white flex items-center justify-center hover:bg-red-500 transition-all">
            <i class="ph-bold ph-x text-sm"></i>
        </button>

        <div class="flex-[1.5] p-6 md:p-8 overflow-y-auto table-scroll bg-black/20"
            style="background-image: radial-gradient(circle at top left, rgba(67,97,238,0.05) 0%, transparent 50%);">

            <div class="flex items-center gap-2 mb-3">
                <span id="mdl-status"
                    class="px-2 py-0.5 text-[9px] uppercase font-bold rounded bg-white/10 text-white tracking-widest">STATUS</span>
                <span id="mdl-priority"
                    class="px-2 py-0.5 text-[9px] uppercase font-bold rounded tracking-widest">PRIORITY</span>
            </div>

            <h2 id="mdl-title" class="text-xl font-bold text-white leading-snug mb-6">Judul Konten</h2>

            <div class="space-y-4">
                <div class="bg-white/5 rounded-xl p-4 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-pink-500"></div>
                    <h4 class="text-[10px] font-bold text-pink-400 uppercase tracking-widest mb-2 flex items-center"><i
                            class="ph-fill ph-fish-hook mr-1.5 text-sm"></i> Hook (3 Detik Pertama)</h4>
                    <p id="mdl-hook" class="text-[13px] text-white/80 leading-relaxed font-medium whitespace-pre-wrap">
                        ...</p>
                </div>

                <div class="bg-white/5 rounded-xl p-4 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                    <h4 class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-2 flex items-center"><i
                            class="ph-fill ph-article mr-1.5 text-sm"></i> Body / Main Value</h4>
                    <p id="mdl-body" class="text-[13px] text-white/80 leading-relaxed whitespace-pre-wrap">...</p>
                </div>

                <div class="bg-white/5 rounded-xl p-4 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
                    <h4 class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-2 flex items-center">
                        <i class="ph-fill ph-cursor-click mr-1.5 text-sm"></i> Call to Action (CTA)</h4>
                    <p id="mdl-cta" class="text-[13px] text-white/80 leading-relaxed font-medium whitespace-pre-wrap">
                        ...</p>
                </div>
            </div>
        </div>

        <div class="flex-1 p-6 md:p-8 border-t md:border-t-0 md:border-l bg-[#1a1a35] flex flex-col">

            <h3 class="text-sm font-bold text-white mb-5 border-b pb-3 flex items-center">
                <i class="ph-fill ph-target text-orange-400 mr-1.5 text-lg"></i> Strategy Details
            </h3>

            <div class="space-y-4 flex-1">
                <div>
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest mb-1">Angle Komunikasi</p>
                    <p id="mdl-angle" class="text-[13px] font-medium text-white/90">...</p>
                </div>

                <div>
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest mb-1">Target Audience</p>
                    <p id="mdl-audience" class="text-[13px] font-medium text-white/90">...</p>
                </div>

                <div>
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest mb-1.5">Platform Distribusi
                    </p>
                    <div id="mdl-platforms" class="flex flex-wrap gap-1.5"></div>
                </div>

                <div class="pt-4 border-t space-y-4">
                    <div>
                        <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest mb-1.5">PIC / Author</p>
                        <div id="mdl-pic-container" class="flex items-center mt-1">
                            <!-- Injected by JS -->
                        </div>
                    </div>

                    <div>
                        <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest mb-1">Jadwal Tayang</p>
                        <p id="mdl-date" class="text-[13px] font-bold text-emerald-300 flex items-center"><i
                                class="ph-bold ph-calendar-blank mr-1.5"></i> <span>...</span></p>
                    </div>
                </div>
            </div>

            <!-- Tombol Edit -->
            <div class="mt-6 pt-5 border-t">
                <a id="mdl-edit-btn" href="#"
                    class="w-full py-2.5 bg-white text-black rounded-2xl text-xs font-bold hover:bg-gray-200 transition-colors flex items-center justify-center">
                    <i class="ph-bold ph-pencil-simple mr-1.5 text-base"></i> Edit Ide Konten Ini
                </a>
            </div>
        </div>

    </div>
</div>

<!-- ==============================================================
     CLIENT-SIDE INTERACTIVITY SCRIPTS
     ============================================================== -->
<script>
    // -------------------------------------------------------------
    // DRAG & DROP ENGINE (Khusus Kanban Board)
    // -------------------------------------------------------------
    let draggedItem = null;
    function drag(ev) {
        draggedItem = ev.currentTarget;
        ev.dataTransfer.setData("text/plain", ev.currentTarget.id);
        ev.dataTransfer.effectAllowed = "move";
        setTimeout(() => { draggedItem.classList.add('is-dragging'); }, 0);
    }
    function allowDrop(ev) { ev.preventDefault(); ev.dataTransfer.dropEffect = "move"; ev.currentTarget.classList.add('drag-over'); }
    function leaveDrop(ev) { ev.currentTarget.classList.remove('drag-over'); }
    function drop(ev) {
        ev.preventDefault();
        const col = ev.currentTarget;
        col.classList.remove('drag-over');
        if (!draggedItem) return;

        const cardId = draggedItem.getAttribute('data-id');
        const newStatus = col.getAttribute('data-status');
        const prevColumn = draggedItem.closest('.kanban-column') || col; // fallback
        const targetBody = col.querySelector('.column-body');

        // Optimistically move card in DOM immediately
        targetBody.appendChild(draggedItem);
        draggedItem.classList.remove('is-dragging');
        draggedItem = null;
        updateCounters();

        fetch('<?= base_url('content/update-status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${cardId}&status=${newStatus}`
        }).then(r => r.json()).then(d => {
            // On failure, silently do nothing (card already moved, data will re-sync on next page load)
            if (!d.success) console.warn('Status update failed for card', cardId);
        }).catch(err => console.error('Drag-drop network error:', err));
    }
    document.addEventListener('dragend', function () {
        if (draggedItem) draggedItem.classList.remove('is-dragging');
        document.querySelectorAll('.kanban-column').forEach(c => c.classList.remove('drag-over'));
        draggedItem = null;
    });
    function updateCounters() {
        document.querySelectorAll('.kanban-column').forEach(col => {
            const count = col.querySelectorAll('.content-card').length;
            const badge = col.querySelector('.col-counter');
            const ph = col.querySelector('.empty-placeholder');
            if (badge) badge.innerText = count;
            if (ph) ph.style.display = (count === 0) ? 'flex' : 'none';
        });
    }

    // -------------------------------------------------------------
    // CONTENT PREVIEW MODAL (LIGHTBOX) ENGINE
    // -------------------------------------------------------------
    function openContentModal(element) {
        const dataStr = element.getAttribute('data-json');
        if (!dataStr) return;

        const item = JSON.parse(dataStr);
        const modal = document.getElementById('briefModal');
        const contentBox = document.getElementById('briefContent');

        document.getElementById('mdl-title').innerText = item.title || 'Untitled';
        document.getElementById('mdl-status').innerText = item.content_status.replace('_', ' ');
        document.getElementById('mdl-priority').innerText = item.priority;

        const pBadge = document.getElementById('mdl-priority');
        pBadge.className = 'px-2 py-0.5 text-[9px] uppercase font-bold rounded  tracking-widest';
        if (item.priority === 'high') pBadge.classList.add('bg-red-500/20', 'text-red-400', 'border-red-500/30');
        else if (item.priority === 'low') pBadge.classList.add('bg-blue-500/20', 'text-blue-400', 'border-blue-500/30');
        else pBadge.classList.add('bg-orange-500/20', 'text-orange-400', 'border-orange-500/30');

        const emptyState = '<span class="text-white/30 italic">Belum ditulis...</span>';
        document.getElementById('mdl-hook').innerHTML = item.hook_text ? escapeHtml(item.hook_text) : emptyState;
        document.getElementById('mdl-body').innerHTML = item.body_text ? escapeHtml(item.body_text) : emptyState;
        document.getElementById('mdl-cta').innerHTML = item.cta_text ? escapeHtml(item.cta_text) : emptyState;

        document.getElementById('mdl-angle').innerHTML = item.angle ? escapeHtml(item.angle) : '-';
        document.getElementById('mdl-audience').innerHTML = item.target_audience ? escapeHtml(item.target_audience) : '-';

        const picContainer = document.getElementById('mdl-pic-container');
        if (item.pic_user_id) {
            let avatarHtml = '';
            const nameInitial = item.pic_name.charAt(0).toUpperCase();

            if (item.pic_avatar_url) {
                avatarHtml = `<img src="<?= base_url() ?>/${item.pic_avatar_url}" class="w-6 h-6 rounded-full object-cover shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(item.pic_name)}&background=4361ee&color=fff&size=128';">`;
            } else {
                avatarHtml = `<div class="w-6 h-6 rounded-full flex items-center justify-center text-white font-bold text-[10px] shrink-0" style="background: linear-gradient(135deg, #4361ee, #7209b7);">${nameInitial}</div>`;
            }

            let waHtml = '';
            if (item.pic_phone) {
                let dateStr = item.scheduled_post_at ? new Date(item.scheduled_post_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Draft';
                let waText = encodeURIComponent(`Halo ${item.pic_name.split(' ')[0]}, reminder untuk task konten *${item.title}* (${dateStr}). Mohon dicek dan di-update ya. Semangat!🚀`);
                waHtml = `<a href="https://wa.me/${item.pic_phone}?text=${waText}" target="_blank" class="text-emerald-400 bg-emerald-500/10 ml-3 px-2 py-1 rounded border-emerald-500/20 hover:bg-emerald-500/20 transition-all flex items-center gap-1 shrink-0" title="Reminder via WhatsApp"><i class="ph-fill ph-whatsapp-logo text-xs"></i> <span class="text-[8px] uppercase tracking-widest font-bold">Reminder</span></a>`;
            }

            picContainer.innerHTML = `${avatarHtml} <span class="text-[13px] font-bold text-blue-300 ml-2 truncate max-w-[120px]">${escapeHtml(item.pic_name)}</span> ${waHtml}`;
        } else {
            picContainer.innerHTML = `<div class="w-6 h-6 rounded-full bg-white/5 flex items-center justify-center text-white/30 shrink-0"><i class="ph-fill ph-user-minus text-[10px]"></i></div> <span class="text-[13px] font-medium text-white/40 ml-2 italic">Unassigned</span>`;
        }

        if (item.scheduled_post_at) {
            const d = new Date(item.scheduled_post_at);
            document.getElementById('mdl-date').innerHTML = `<i class="ph-bold ph-calendar-blank mr-1.5"></i> <span>${d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</span>`;
            document.getElementById('mdl-date').className = 'text-[13px] font-bold text-emerald-300 flex items-center';
        } else {
            document.getElementById('mdl-date').innerHTML = `<i class="ph-bold ph-calendar-blank mr-1.5"></i> <span>Draft</span>`;
            document.getElementById('mdl-date').className = 'text-[13px] font-medium text-white/40 flex items-center italic';
        }

        const platContainer = document.getElementById('mdl-platforms');
        platContainer.innerHTML = '';
        try {
            const platforms = JSON.parse(item.platforms_json) || [];
            if (platforms.length > 0) {
                platforms.forEach(p => {
                    const iconMap = {
                        'instagram': '<i class="ph-fill ph-instagram-logo mr-1"></i>',
                        'tiktok': '<i class="ph-fill ph-tiktok-logo mr-1"></i>',
                        'facebook': '<i class="ph-fill ph-facebook-logo mr-1"></i>',
                        'youtube': '<i class="ph-fill ph-youtube-logo mr-1"></i>'
                    };
                    const icon = iconMap[p.toLowerCase()] || '';
                    platContainer.innerHTML += `<span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-white/5 text-white/80 flex items-center">${icon} ${p}</span>`;
                });
            } else {
                platContainer.innerHTML = '<span class="text-xs text-white/30 italic">Belum diset...</span>';
            }
        } catch (e) { }

        document.getElementById('mdl-edit-btn').href = '<?= base_url('content/edit?id=') ?>' + item.id;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        contentBox.classList.remove('scale-95', 'opacity-0');
        contentBox.classList.add('scale-100', 'opacity-100');
    }

    function closeContentModal() {
        const modal = document.getElementById('briefModal');
        const contentBox = document.getElementById('briefContent');

        modal.classList.add('opacity-0');
        contentBox.classList.remove('scale-100', 'opacity-100');
        contentBox.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === "Escape") closeContentModal();
    });
</script>