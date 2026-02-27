<?php
/**
 * ==============================================================================
 * HOOKJOURNEY - GROWTH OPS HUB
 * ==============================================================================
 * File       : views/ads/performance.php
 * Deskripsi  : Antarmuka Analytics untuk Performa Meta Ads.
 * Fitur      : Filter Tanggal/Hasil, Live Search, Live Sort, Two-Tier Header, 
 * Sticky Columns, Smart UX Hints.
 * ==============================================================================
 */
?>

<!-- ==============================================================
     INLINE CSS KHUSUS HALAMAN PERFORMA ADS
     ============================================================== -->
<style>
    /* Custom Scrollbar Premium */
    .table-scroll-wrapper::-webkit-scrollbar {
        height: 10px;
        width: 10px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 10px;
        margin: 0 15px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    /* Sticky Column Shadow */
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 20;
        background: #0a0a1a;
    }

    .sticky-col::after {
        content: '';
        position: absolute;
        top: 0;
        right: -15px;
        width: 15px;
        height: 100%;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.6), transparent);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .table-scroll-wrapper.is-scrolling .sticky-col::after {
        opacity: 1;
    }

    /* Glass Input (Search & Filters) */
    .search-glass,
    .filter-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border-radius: 12px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }

    .search-glass {
        padding: 0.6rem 1rem 0.6rem 2.5rem;
        width: 100%;
    }

    .filter-glass {
        padding: 0.6rem 1rem;
        width: 100%;
    }

    .search-glass::placeholder,
    .filter-glass::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .search-glass:focus,
    .filter-glass:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: #ff007f;
        box-shadow: 0 0 0 3px rgba(255, 0, 127, 0.15);
        outline: none;
    }

    .filter-glass option {
        background: #0f0c29 !important;
        color: #fff !important;
    }

    .search-icon-wrapper {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.4);
    }

    /* Table Sorting Utilities */
    .sortable-th {
        cursor: pointer;
        transition: background 0.2s;
        user-select: none;
    }

    .sortable-th:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .sort-icon {
        opacity: 0.3;
        transition: all 0.2s;
        margin-left: 4px;
        display: inline-block;
    }

    .sort-active-asc .sort-icon {
        opacity: 1;
        color: #ff007f;
        transform: rotate(180deg);
    }

    .sort-active-desc .sort-icon {
        opacity: 1;
        color: #ff007f;
    }
</style>

<!-- ==============================================================
     HEADER & ACTION BAR
     ============================================================== -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-20">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Performa Meta Ads</h1>
        <p class="text-white/60 text-sm mt-1">
            Menampilkan data <strong><?= htmlspecialchars($activeBrand['name']) ?></strong>
            (<?= date('d M Y', strtotime($filters['start_date'])) ?> -
            <?= date('d M Y', strtotime($filters['end_date'])) ?>)
        </p>
    </div>
    <div class="mt-4 sm:mt-0 space-x-2">
        <a href="<?= base_url('ads/import') ?>"
            class="btn px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center transition-all hover:scale-105"
            style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
            <i class="ph ph-upload-simple mr-2 text-lg"></i> Import Laporan Baru
        </a>
    </div>
</div>

<!-- ==============================================================
     PANEL FILTER DATA DINAMIS (SERVER-SIDE)
     ============================================================== -->
<form method="GET" action="<?= base_url('ads/performance') ?>"
    class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-5 rounded-[24px] mb-8 flex flex-col md:flex-row gap-4 md:items-end relative z-20">

    <!-- Rentang Tanggal -->
    <div class="flex-1 min-w-[140px]">
        <label class="block text-[11px] font-bold text-white/50 uppercase tracking-wider mb-1.5"><i
                class="ph ph-calendar-blank mr-1"></i> Dari Tanggal</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date']) ?>"
            class="filter-glass cursor-pointer">
    </div>
    <div class="flex-1 min-w-[140px]">
        <label class="block text-[11px] font-bold text-white/50 uppercase tracking-wider mb-1.5"><i
                class="ph ph-calendar-blank mr-1"></i> Sampai Tanggal</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date']) ?>"
            class="filter-glass cursor-pointer">
    </div>

    <!-- Tipe Hasil (Result Type) -->
    <div class="flex-[2] min-w-[200px]">
        <label class="block text-[11px] font-bold text-white/50 uppercase tracking-wider mb-1.5"><i
                class="ph ph-target mr-1"></i> Objektif / Jenis Hasil</label>
        <select name="result_type" class="filter-glass cursor-pointer">
            <option value="">-- Semua Jenis Hasil (Gabungan) --</option>
            <?php if (!empty($availableResultTypes)): ?>
                <?php foreach ($availableResultTypes as $rt): ?>
                    <option value="<?= htmlspecialchars($rt) ?>" <?= $filters['result_type'] === $rt ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucwords($rt)) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Tombol Aksi Filter -->
    <div class="flex gap-2 mt-4 md:mt-0">
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors flex items-center">
            <i class="ph ph-funnel mr-1.5 text-lg"></i> Terapkan
        </button>
        <a href="<?= base_url('ads/performance') ?>"
            class="bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors flex items-center"
            title="Reset Filter">
            <i class="ph ph-arrows-counter-clockwise text-lg"></i>
        </a>
    </div>
</form>

<!-- ==============================================================
     SCORECARDS (KPI SUMMARY)
     ============================================================== -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 relative z-20">

    <div
        class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-6 rounded-[24px] relative overflow-hidden group hover: transition-all flex flex-col justify-center">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150">
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-white/50 mb-1 flex items-center"><i
                class="ph ph-money text-purple-400 mr-1.5 text-lg"></i> Total Spend</p>
        <h3 class="text-3xl font-bold text-white mt-2"><?= format_rupiah($summary['total_spend']) ?></h3>
    </div>

    <div
        class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-6 rounded-[24px] relative overflow-hidden group hover: transition-all flex flex-col justify-center">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150">
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-white/50 mb-1 flex items-center"><i
                class="ph ph-target text-blue-400 mr-1.5 text-lg"></i> Total Results</p>
        <h3 class="text-3xl font-bold text-white mt-2"><?= format_number($summary['total_results']) ?></h3>
    </div>

    <div
        class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-6 rounded-[24px] relative overflow-hidden group hover: transition-all flex flex-col justify-center">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150">
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-white/50 mb-1 flex items-center"><i
                class="ph ph-chart-line-down text-emerald-400 mr-1.5 text-lg"></i> Avg. Cost per Result (CPR)</p>
        <h3
            class="text-3xl font-bold mt-2 <?= $aggCpr > 0 ? 'text-emerald-400 drop-shadow-[0_0_12px_rgba(52,211,153,0.4)]' : 'text-white' ?>">
            <?= format_rupiah($aggCpr) ?>
        </h3>
    </div>

    <!-- Smart UX Warning untuk CTR -->
    <div
        class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 p-6 rounded-[24px] relative overflow-hidden group hover: transition-all flex flex-col justify-center">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-pink-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150">
        </div>
        <div>
            <p
                class="text-xs font-semibold uppercase tracking-wider text-white/50 mb-1 flex items-center justify-between">
                <span class="flex items-center"><i class="ph ph-cursor-click text-pink-400 mr-1.5 text-lg"></i> Avg.
                    CTR</span>
                <?php if ($aggCtr == 0 && $summary['total_impressions'] > 0): ?>
                    <i class="ph-fill ph-warning-circle text-pink-400/80 text-lg cursor-help"
                        title="Data Klik Kosong di CSV"></i>
                <?php endif; ?>
            </p>
            <h3 class="text-3xl font-bold text-white mt-2"><?= format_percentage($aggCtr) ?></h3>
        </div>

        <?php if ($aggCtr == 0 && $summary['total_impressions'] > 0): ?>
            <!-- Muncul otomatis jika impresi ada tapi klik nol (mengindikasikan kolom terlewat saat export) -->
            <div class="mt-3 pt-3 border-t">
                <p class="text-[9px] text-pink-200/70 leading-relaxed">
                    <span class="font-bold text-pink-400">Tips:</span> Pastikan metrik <b>Klik (Semua)</b> atau <b>Klik
                        Tautan</b> Anda cantumkan saat Export laporan di Meta Ads Manager.
                </p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- ==============================================================
     COMPLEX DATA TABLE & LOCAL FILTERS
     ============================================================== -->
<div
    class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[24px] flex flex-col relative z-20 overflow-hidden">

    <!-- Client-Side Toolbar (Local Search & Local Campaign Filter) -->
    <div
        class="px-6 py-5 border-b bg-white/5 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-white/90 text-lg">Detailed Ads Breakdown</h3>
            <p class="text-xs text-white/40 mt-1"><i class="ph ph-arrows-left-right align-middle"></i> Klik pada header
                tabel (misal: Spend, CTR) untuk mengurutkan (Sort)</p>
        </div>

        <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-3">
            <!-- Dropdown Filter Campaign (Auto-populated by JS) -->
            <select id="campaignFilterJS" class="search-glass cursor-pointer max-w-full sm:max-w-[250px]">
                <option value="">-- Semua Kampanye --</option>
            </select>

            <!-- Live Search Text -->
            <div class="relative w-full sm:w-[250px]">
                <i class="ph ph-magnifying-glass search-icon-wrapper text-lg"></i>
                <input type="text" id="searchAds" class="search-glass" placeholder="Cari nama iklan/adset...">
            </div>
        </div>
    </div>

    <!-- Table Scroll Wrapper -->
    <div class="table-scroll-wrapper overflow-x-auto" id="tableWrapper">
        <table class="w-full text-left whitespace-nowrap min-w-[1200px]" id="adsTable">

            <thead class="bg-black/20">
                <tr>
                    <th rowspan="2"
                        class="px-5 py-3 border-b text-xs font-bold text-white/60 uppercase tracking-wider sticky-col sortable-th group"
                        onclick="sortTable(0, 'string')">
                        <div class="flex items-center">Struktur Kampanye <i class="ph ph-caret-down sort-icon"></i>
                        </div>
                    </th>

                    <th colspan="3"
                        class="px-5 py-2 border-b border-l text-center text-[10.5px] font-bold text-purple-300 uppercase tracking-widest bg-purple-900/10">
                        Konversi (Conversion)
                    </th>
                    <th colspan="4"
                        class="px-5 py-2 border-b border-l text-center text-[10.5px] font-bold text-blue-300 uppercase tracking-widest bg-blue-900/10">
                        Penayangan (Delivery)
                    </th>
                    <th colspan="4"
                        class="px-5 py-2 border-b border-l text-center text-[10.5px] font-bold text-emerald-300 uppercase tracking-widest bg-emerald-900/10">
                        Retensi Video (Plays)
                    </th>

                    <th rowspan="2"
                        class="px-5 py-3 border-b border-l text-center text-xs font-bold text-white/60 uppercase tracking-wider sortable-th group"
                        onclick="sortTable(12, 'string')">
                        <div class="flex items-center justify-center">Status AI <i
                                class="ph ph-caret-down sort-icon"></i></div>
                    </th>
                </tr>
                <tr>
                    <!-- Sub-Kolom Konversi (Data attributes untuk sorting akurat) -->
                    <th class="px-5 py-2 border-b border-l text-right text-xs font-medium text-white/40 sortable-th hover:text-white group"
                        onclick="sortTable(1, 'number')">
                        Spend (Rp) <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 sortable-th hover:text-white group"
                        onclick="sortTable(2, 'number')">
                        Results <i class="ph ph-caret-down sort-icon"></i> <span
                            class="text-[9px] block text-purple-400/50 mt-0.5 pointer-events-none">(Tipe Hasil)</span>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 sortable-th hover:text-white group"
                        onclick="sortTable(3, 'number')">
                        CPR (Rp) <i class="ph ph-caret-down sort-icon"></i>
                    </th>

                    <!-- Sub-Kolom Penayangan -->
                    <th class="px-5 py-2 border-b border-l text-right text-xs font-medium text-white/40 sortable-th hover:text-white group"
                        onclick="sortTable(4, 'number')">
                        Reach <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 sortable-th hover:text-white group"
                        onclick="sortTable(5, 'number')">
                        Impresi <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 sortable-th hover:text-white group"
                        onclick="sortTable(6, 'number')">
                        CPM (Rp) <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 sortable-th hover:text-white group"
                        onclick="sortTable(7, 'number')">
                        CTR (%) <i class="ph ph-caret-down sort-icon"></i>
                    </th>

                    <!-- Sub-Kolom Video -->
                    <th class="px-5 py-2 border-b border-l text-right text-xs font-medium text-white/40 text-emerald-400/60 sortable-th hover:text-white group"
                        onclick="sortTable(8, 'number')">
                        25% <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 text-emerald-400/70 sortable-th hover:text-white group"
                        onclick="sortTable(9, 'number')">
                        50% <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 text-emerald-400/80 sortable-th hover:text-white group"
                        onclick="sortTable(10, 'number')">
                        75% <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                    <th class="px-5 py-2 border-b text-right text-xs font-medium text-white/40 text-emerald-400 sortable-th hover:text-white group"
                        onclick="sortTable(11, 'number')">
                        100% <i class="ph ph-caret-down sort-icon"></i>
                    </th>
                </tr>
            </thead>

            <tbody id="adsTableBody" class="divide-y divide-white/5 bg-transparent">
                <?php if (empty($campaigns)): ?>
                    <tr class="empty-state">
                        <td colspan="13" class="px-6 py-20 text-center text-white/40 text-sm bg-black/10">
                            <i class="ph ph-empty text-6xl mb-3 block opacity-30"></i>
                            <p class="font-medium text-white/60">Data tidak ditemukan untuk filter ini.</p>
                            <p class="text-xs mt-1">Coba sesuaikan rentang tanggal atau tipe hasil di menu atas.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($campaigns as $camp): ?>
                        <?php
                        // Logic Status AI untuk Atribut Pengurutan (Sorting)
                        $statusText = 'Testing';
                        if ($camp['ctr'] >= 1.5 && $camp['cpr'] > 0 && $camp['cpr'] <= $aggCpr)
                            $statusText = 'Winner';
                        elseif ($camp['spend'] > ($summary['total_spend'] * 0.1) && $camp['results'] == 0)
                            $statusText = 'Fatigue';
                        ?>
                        <tr class="hover:bg-white/5 transition-colors group data-row">

                            <!-- Col 0: Hierarchy (Disisipkan atribut data-val untuk sorting dan class untuk filter) -->
                            <td class="px-5 py-4 sticky-col group-hover:bg-[#111126] border-r min-w-[320px] max-w-[400px]"
                                data-val="<?= htmlspecialchars($camp['campaign_name']) ?>">
                                <div class="flex flex-col gap-1.5 searchable-text">
                                    <p class="text-[10px] text-white/40 uppercase tracking-wide truncate camp-name-val"
                                        title="<?= htmlspecialchars($camp['campaign_name']) ?>">
                                        <i class="ph ph-folder text-purple-400/70 mr-1"></i>
                                        <?= htmlspecialchars($camp['campaign_name']) ?>
                                    </p>
                                    <?php if (!empty($camp['adset_name'])): ?>
                                        <p class="text-xs text-white/70 truncate pl-3 border-l ml-1.5"
                                            title="<?= htmlspecialchars($camp['adset_name']) ?>">
                                            <i class="ph ph-users-three text-blue-400/70 mr-1"></i>
                                            <?= htmlspecialchars($camp['adset_name']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($camp['ad_name'])): ?>
                                        <p class="text-sm font-semibold text-white/90 truncate pl-6 border-l ml-1.5"
                                            title="<?= htmlspecialchars($camp['ad_name']) ?>">
                                            <i class="ph ph-monitor-play text-emerald-400 mr-1"></i>
                                            <?= htmlspecialchars($camp['ad_name']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Col 1, 2, 3: Konversi -->
                            <td class="px-5 py-4 text-sm text-white/70 text-right bg-purple-900/5 border-l align-top pt-5"
                                data-val="<?= $camp['spend'] ?>"><?= format_number($camp['spend']) ?></td>
                            <td class="px-5 py-4 text-right bg-purple-900/5 align-top pt-5" data-val="<?= $camp['results'] ?>">
                                <p class="text-sm font-medium text-white"><?= format_number($camp['results']) ?></p>
                                <?php if (!empty($camp['result_types'])): ?>
                                    <p class="text-[9px] text-purple-300/80 mt-1.5 uppercase tracking-wide truncate max-w-[120px] ml-auto border-purple-500/20 bg-purple-500/10 px-1.5 py-0.5 rounded"
                                        title="<?= htmlspecialchars($camp['result_types']) ?>">
                                        <?= htmlspecialchars($camp['result_types']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-purple-300 text-right bg-purple-900/5 align-top pt-5"
                                data-val="<?= $camp['cpr'] ?>"><?= format_rupiah($camp['cpr']) ?></td>

                            <!-- Col 4, 5, 6, 7: Delivery -->
                            <td class="px-5 py-4 text-sm text-white/60 text-right bg-blue-900/5 border-l align-top pt-5"
                                data-val="<?= $camp['reach'] ?>"><?= format_number($camp['reach']) ?></td>
                            <td class="px-5 py-4 text-sm text-white/60 text-right bg-blue-900/5 align-top pt-5"
                                data-val="<?= $camp['impressions'] ?>"><?= format_number($camp['impressions']) ?></td>
                            <td class="px-5 py-4 text-sm text-white/60 text-right bg-blue-900/5 align-top pt-5"
                                data-val="<?= $camp['cpm'] ?>"><?= format_number($camp['cpm']) ?></td>
                            <td class="px-5 py-4 text-sm font-medium <?= $camp['ctr'] == 0 ? 'text-white/30' : 'text-blue-300' ?> text-right bg-blue-900/5 align-top pt-5"
                                data-val="<?= $camp['ctr'] ?>">
                                <?= format_percentage($camp['ctr']) ?>
                            </td>

                            <!-- Col 8, 9, 10, 11: Video Retention -->
                            <td class="px-5 py-4 text-sm text-emerald-100/50 text-right bg-emerald-900/5 border-l align-top pt-5"
                                data-val="<?= $camp['video_25'] ?>"><?= format_number($camp['video_25']) ?></td>
                            <td class="px-5 py-4 text-sm text-emerald-200/60 text-right bg-emerald-900/5 align-top pt-5"
                                data-val="<?= $camp['video_50'] ?>"><?= format_number($camp['video_50']) ?></td>
                            <td class="px-5 py-4 text-sm text-emerald-300/80 text-right bg-emerald-900/5 align-top pt-5"
                                data-val="<?= $camp['video_75'] ?>"><?= format_number($camp['video_75']) ?></td>
                            <td class="px-5 py-4 text-sm font-semibold text-emerald-400 text-right bg-emerald-900/5 align-top pt-5"
                                data-val="<?= $camp['video_100'] ?>"><?= format_number($camp['video_100']) ?></td>

                            <!-- Col 12: Status AI -->
                            <td class="px-5 py-4 text-center border-l bg-black/10 align-middle" data-val="<?= $statusText ?>">
                                <?php if ($statusText == 'Winner'): ?>
                                    <span
                                        class="bg-emerald-500/20 border-emerald-500/40 text-emerald-300 px-3 py-1.5 text-[10px] rounded-full font-bold uppercase tracking-wider shadow-[0_0_12px_rgba(52,211,153,0.25)] flex items-center justify-center w-max mx-auto">
                                        <i class="ph-fill ph-star mr-1"></i> Winner
                                    </span>
                                <?php elseif ($statusText == 'Fatigue'): ?>
                                    <span
                                        class="bg-red-500/20 border-red-500/40 text-red-300 px-3 py-1.5 text-[10px] rounded-full font-bold uppercase tracking-wider flex items-center justify-center w-max mx-auto">
                                        <i class="ph-fill ph-warning-circle mr-1"></i> Fatigue
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="bg-white/10 text-white/70 px-3 py-1.5 text-[10px] rounded-full font-bold uppercase tracking-wider flex items-center justify-center w-max mx-auto">
                                        <i class="ph ph-spinner-gap mr-1 animate-spin"></i> Testing
                                    </span>
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
     CLIENT-SIDE INTERACTIVITY SCRIPTS (SORT & FILTER)
     ============================================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initAdsPage();
    });
    // Also init immediately for HTMX SPA navigation (DOMContentLoaded doesn't re-fire)
    if (document.readyState !== 'loading') {
        initAdsPage();
    }

    function initAdsPage() {
        // Guard: prevent double-init if called twice
        if (window._adsPageInited) return;
        window._adsPageInited = true;
        // Reset on next navigation
        document.body.addEventListener('htmx:beforeRequest', () => { window._adsPageInited = false; }, { once: true });

        const tableBody = document.getElementById('adsTableBody');
        if (!tableBody) return;
        const rows = Array.from(tableBody.querySelectorAll('tr.data-row')); // Ambil hanya baris data
        const searchInput = document.getElementById('searchAds');
        const campFilterJS = document.getElementById('campaignFilterJS');

        // 1. POPULATE CAMPAIGN DROPDOWN OTOMATIS
        if (campFilterJS && rows.length > 0) {
            const uniqueCampaigns = new Set();
            rows.forEach(row => {
                const campElem = row.querySelector('.camp-name-val');
                if (campElem) uniqueCampaigns.add(campElem.innerText.trim());
            });

            // Urutkan nama campaign secara alfabetis
            Array.from(uniqueCampaigns).sort().forEach(campName => {
                let opt = document.createElement('option');
                opt.value = campName.toLowerCase();
                opt.innerText = campName;
                campFilterJS.appendChild(opt);
            });
        }

        // 2. LOGIKA FILTER GABUNGAN (SEARCH + DROPDOWN)
        function applyFilters() {
            const searchVal = searchInput ? searchInput.value.toLowerCase() : '';
            const campVal = campFilterJS ? campFilterJS.value : '';

            rows.forEach(row => {
                const textContent = row.querySelector('.searchable-text').innerText.toLowerCase();
                const rowCampName = row.querySelector('.camp-name-val').innerText.trim().toLowerCase();

                const matchSearch = textContent.includes(searchVal);
                const matchCamp = (campVal === "" || rowCampName === campVal);

                row.style.display = (matchSearch && matchCamp) ? "" : "none";
            });
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (campFilterJS) campFilterJS.addEventListener('change', applyFilters);

        // 3. EFEK BAYANGAN STICKY COLUMN SAAT SCROLL
        const tableWrapper = document.getElementById('tableWrapper');
        if (tableWrapper) {
            tableWrapper.addEventListener('scroll', function () {
                if (this.scrollLeft > 5) this.classList.add('is-scrolling');
                else this.classList.remove('is-scrolling');
            });
        }
    }

    // 4. MESIN PENGURUTAN (SORTING) TABEL
    let currentSortCol = -1;
    let currentSortAsc = true;

    function sortTable(colIndex, type) {
        const tableBody = document.getElementById('adsTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr.data-row'));
        if (rows.length === 0) return;

        // Tentukan arah pengurutan
        if (currentSortCol === colIndex) {
            currentSortAsc = !currentSortAsc;
        } else {
            // Default sort saat pertama kali klik kolom baru (Tinggi -> Rendah untuk angka)
            currentSortAsc = type === 'string' ? true : false;
            currentSortCol = colIndex;
        }

        // Eksekusi Sorting Array
        rows.sort((a, b) => {
            let valA = a.cells[colIndex].getAttribute('data-val');
            let valB = b.cells[colIndex].getAttribute('data-val');

            if (type === 'number') {
                valA = parseFloat(valA) || 0;
                valB = parseFloat(valB) || 0;
                return currentSortAsc ? valA - valB : valB - valA;
            } else {
                valA = valA.toLowerCase();
                valB = valB.toLowerCase();
                if (valA < valB) return currentSortAsc ? -1 : 1;
                if (valA > valB) return currentSortAsc ? 1 : -1;
                return 0;
            }
        });

        // Render ulang baris yang sudah diurutkan
        tableBody.innerHTML = '';
        rows.forEach(row => tableBody.appendChild(row));

        // Update Ikon Panah pada Header (UX Feedback)
        document.querySelectorAll('.sortable-th').forEach(th => {
            th.classList.remove('sort-active-asc', 'sort-active-desc');
        });

        // Mencari th yang di klik (mengingat struktur rowspan/colspan agak rumit, kita cocokkan indexnya manual atau via event.currentTarget)
        const clickedTh = event.currentTarget;
        if (currentSortAsc) {
            clickedTh.classList.add('sort-active-asc');
        } else {
            clickedTh.classList.add('sort-active-desc');
        }
    }
</script>