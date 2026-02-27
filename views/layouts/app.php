<?php
/**
 * HOOKJOURNEY - GROWTH OPS HUB
 * Master Layout: views/layouts/app.php
 * * KRITIKAL FIX & OPTIMIZATION:
 * 1. Auto-Initialization Brand untuk Leader & Team (Mencegah Menu Stuck).
 * 2. Sinkronisasi Hak Akses Dinamis (RBAC) per Brand.
 * 3. Layering Z-Index Premium (iOS 26 Liquid Glass Effect).
 * 4. Interactive Desktop Sidebar: Auto Expand on Hover, Locked on Hamburger Toggle.
 * 5. Native Mobile UI: Floating Bottom Navigation Pill (iPhone 17 Style).
 * 6. Native SPA Engine: Zero-latency prefetching & View Transitions API.
 */

$db = Database::getInstance()->getConnection();
$sessionUser = Auth::user();

// FIX: Ambil data user utuh dari DB agar avatar_url tersedia secara global di sidebar
$stmtUFull = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmtUFull->execute([$sessionUser['id']]);
$user = $stmtUFull->fetch();
if (!$user)
    $user = $sessionUser;

// --- 1. LOGIKA AUTO-SWITCH BRAND (ANTI-STUCK ENGINE) ---
if (!isset($_SESSION['active_brand_id']) || empty($_SESSION['active_brand_id'])) {
    $stmtM = $db->prepare("SELECT brand_id FROM brand_members WHERE user_id = ? AND is_active = 1 LIMIT 1");
    $stmtM->execute([$user['id']]);
    $resM = $stmtM->fetch();

    if ($resM) {
        $_SESSION['active_brand_id'] = $resM['brand_id'];
    } else {
        if ($user['role_global'] === 'leader') {
            $stmtL = $db->prepare("SELECT id FROM brands WHERE workspace_id = ? AND is_active = 1 LIMIT 1");
            $stmtL->execute([$user['workspace_id']]);
            $resL = $stmtL->fetch();
            if ($resL) {
                $_SESSION['active_brand_id'] = $resL['id'];
            }
        }
    }
}

$activeBrandId = $_SESSION['active_brand_id'] ?? 0;
$stmtB = $db->prepare("SELECT * FROM brands WHERE id = ? LIMIT 1");
$stmtB->execute([$activeBrandId]);
$activeBrand = $stmtB->fetch();

// --- 2. LOGIKA IZIN MENU (RBAC REAL-TIME) ---
$sqlPerm = "SELECT role_in_brand, permissions_json FROM brand_members 
            WHERE user_id = ? AND brand_id = ? AND is_active = 1 LIMIT 1";
$stmtP = $db->prepare($sqlPerm);
$stmtP->execute([$user['id'], $activeBrandId]);
$memberData = $stmtP->fetch();

$userRoleInBrand = $memberData['role_in_brand'] ?? $user['role_global'];
$userPerms = json_decode($memberData['permissions_json'] ?? '[]', true);

$canAccess = function ($menuKey) use ($userRoleInBrand, $userPerms) {
    if ($userRoleInBrand === 'leader')
        return true;
    return is_array($userPerms) && in_array($menuKey, $userPerms);
};

$brandModel = new Brand();
$availableBrands = $brandModel->getActiveBrandsByUser($user);

// Dapatkan Current URI untuk active state menu desktop & mobile
$current = current_uri();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <!-- View Transitions API untuk efek transisi native antar halaman -->
    <meta name="view-transition" content="same-origin">

    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>

    <!-- PWA & Mobile App Settings -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#060609">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icon-192.png') ?>">

    <!-- Mencegah FOUC (Flash of Unstyled Content) dengan membaca state Sidebar dari LocalStorage -->
    <script>
        if (window.innerWidth >= 992 && localStorage.getItem('sidebarState') === 'collapsed') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>

    <!-- CSS ASSETS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <style>
        /* --- CORE THEME: iOS LIQUID GLASS (SOFT & CLEAN) --- */
        :root {
            --sidebar-w: 280px;
            --sidebar-w-collapsed: 90px;
            --accent: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-highlight: rgba(255, 255, 255, 0.12);
            --ios-blur: blur(40px) saturate(140%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #060609;
            color: #ffffff;
            margin: 0;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
        }

        /* --- SPA NATIVE PROGRESS BAR --- */
        #ios-loader {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #00fa9a);
            width: 0;
            z-index: 9999;
            opacity: 0;
            transition: width 0.3s ease, opacity 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 250, 154, 0.5);
        }

        /* --- HARDWARE ACCELERATED AURORA --- */
        .aurora-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
            background: #060609;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(140px);
            opacity: 0.35;
            animation: float-blob 25s infinite ease-in-out alternate;
            transform: translateZ(0);
            /* Hardware Acceleration */
            will-change: transform;
        }

        .blob-1 {
            background: #2563eb;
            width: 55vw;
            height: 55vw;
            top: -15%;
            left: -10%;
        }

        .blob-2 {
            background: #7c3aed;
            width: 45vw;
            height: 45vw;
            bottom: -10%;
            right: -5%;
            animation-delay: -7s;
        }

        .blob-3 {
            background: #0d9488;
            width: 40vw;
            height: 40vw;
            top: 35%;
            left: 35%;
            animation-delay: -14s;
        }

        @keyframes float-blob {
            0% {
                transform: translate3d(0, 0, 0) scale(1);
            }

            50% {
                transform: translate3d(5vw, -5vh, 0) scale(1.05);
            }

            100% {
                transform: translate3d(-3vw, 4vh, 0) scale(0.95);
            }
        }

        /* --- SIDEBAR ARCHITECTURE --- */
        #sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1080;
            background: var(--glass-bg);
            backdrop-filter: var(--ios-blur);
            -webkit-backdrop-filter: var(--ios-blur);
            border-right: 1px solid var(--glass-border);
            transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), background 0.4s;
            display: flex;
            flex-direction: column;
            box-shadow: 1px 0 20px rgba(0, 0, 0, 0.1);
            overflow-x: hidden;
            white-space: nowrap;
        }

        #main-wrapper {
            margin-left: var(--sidebar-w);
            height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            z-index: 10;
        }

        /* --- COLLAPSED STATE LOGIC (DESKTOP) --- */
        @media (min-width: 992px) {
            html.sidebar-collapsed #sidebar {
                width: var(--sidebar-w-collapsed);
            }

            html.sidebar-collapsed #main-wrapper {
                margin-left: var(--sidebar-w-collapsed);
            }

            /* Auto Expand Effect on Hover */
            html.sidebar-collapsed #sidebar:hover {
                width: var(--sidebar-w);
                background: rgba(15, 15, 20, 0.85);
                box-shadow: 20px 0 60px rgba(0, 0, 0, 0.6);
            }

            /* Smoothly hiding text */
            html.sidebar-collapsed #sidebar:not(:hover) .hide-on-collapse {
                opacity: 0;
                width: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                height: 0 !important;
                visibility: hidden;
            }

            html.sidebar-collapsed #sidebar:not(:hover) .sidebar-logo-container {
                padding: 0 !important;
                justify-content: center !important;
            }

            html.sidebar-collapsed #sidebar:not(:hover) .logo-icon {
                margin-right: 0 !important;
            }

            html.sidebar-collapsed #sidebar:not(:hover) .nav-link-glass {
                padding: 0 !important;
                width: 48px;
                height: 48px;
                justify-content: center !important;
                margin-left: auto;
                margin-right: auto;
            }

            html.sidebar-collapsed #sidebar:not(:hover) .nav-link-glass i {
                margin-right: 0 !important;
                font-size: 1.5rem;
            }

            html.sidebar-collapsed #sidebar:not(:hover) .nav-section-title {
                opacity: 0;
                height: 0;
                margin: 0;
                padding: 0;
            }

            html.sidebar-collapsed #sidebar:not(:hover) .sidebar-footer {
                justify-content: center !important;
                padding: 0 !important;
            }

            html.sidebar-collapsed #sidebar:not(:hover) .sidebar-footer .user-avatar {
                margin-right: 0 !important;
            }
        }

        .hide-on-collapse {
            transition: opacity 0.2s ease, width 0.3s ease, margin 0.3s ease;
            opacity: 1;
            overflow: hidden;
            white-space: nowrap;
        }

        /* TOPBAR */
        .glass-topbar {
            height: 80px;
            background: rgba(6, 6, 9, 0.4);
            backdrop-filter: var(--ios-blur);
            -webkit-backdrop-filter: var(--ios-blur);
            border-bottom: 1px solid var(--glass-border);
            z-index: 1030;
            flex-shrink: 0;
        }

        #sidebarBackdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 1070;
            display: none;
            transition: opacity 0.3s;
        }

        #sidebarBackdrop.show {
            display: block;
        }

        /* --- NATIVE MOBILE BOTTOM NAVIGATION --- */
        .mobile-bottom-nav {
            position: fixed;
            bottom: max(24px, env(safe-area-inset-bottom));
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 48px);
            max-width: 400px;
            height: 72px;
            background: rgba(30, 30, 35, 0.45);
            backdrop-filter: blur(40px) saturate(200%);
            -webkit-backdrop-filter: blur(40px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 40px;
            z-index: 1060;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0 8px;
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.15);
        }

        .nav-item-mobile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            border-radius: 50%;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .nav-item-mobile:active {
            transform: scale(0.9);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item-mobile.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: inset 0 2px 8px rgba(255, 255, 255, 0.05);
        }

        .nav-item-mobile i {
            font-size: 1.6rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .nav-item-mobile.active i {
            filter: drop-shadow(0 2px 8px rgba(255, 255, 255, 0.4));
            transform: translateY(-2px);
        }

        .nav-item-mobile.active::after {
            content: '';
            position: absolute;
            bottom: 6px;
            width: 4px;
            height: 4px;
            background: #ffffff;
            border-radius: 50%;
            box-shadow: 0 0 6px #fff;
        }

        /* RESPONSIVE LOGIC */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.show {
                transform: translateX(0);
                box-shadow: 20px 0 50px rgba(0, 0, 0, 0.5);
            }

            #main-wrapper {
                margin-left: 0;
                padding-bottom: 100px;
            }

            .glass-topbar {
                padding: 0 20px !important;
            }
        }

        @media (min-width: 992px) {
            .mobile-bottom-nav {
                display: none !important;
            }
        }

        /* DESKTOP NAV LINKS */
        .nav-link-glass {
            color: rgba(255, 255, 255, 0.65);
            border-radius: 14px;
            padding: 0.75rem 1.15rem;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            font-size: 0.92rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }

        .nav-link-glass:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
        }

        .nav-link-glass.active {
            color: #ffffff;
            background: var(--glass-highlight);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-weight: 500;
        }

        .nav-link-glass i {
            font-size: 1.35rem;
            margin-right: 0.9rem;
            color: rgba(255, 255, 255, 0.4);
            transition: all 0.3s;
        }

        .nav-link-glass:hover i,
        .nav-link-glass.active i {
            color: #ffffff;
        }

        .nav-section-title {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255, 255, 255, 0.35);
            margin: 1.8rem 1.15rem 0.6rem;
            transition: all 0.3s;
        }

        /* MAGIC UI OVERRIDES */
        .glass-select {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid var(--glass-border) !important;
            color: #ffffff !important;
            border-radius: 50px;
            padding: 0.55rem 2.6rem 0.55rem 1.2rem;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            backdrop-filter: var(--ios-blur);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .glass-select:hover {
            background: rgba(255, 255, 255, 0.12) !important;
        }

        .glass-select option {
            background: #12121a;
            color: #fff;
        }

        .btn-glass {
            border-radius: 50px !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid var(--glass-border) !important;
            backdrop-filter: var(--ios-blur);
            color: white !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-glass:hover,
        .btn-glass:active {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: scale(0.95);
        }

        main .bg-white,
        main .bg-gray-50,
        main .card,
        .content-box {
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: var(--ios-blur) !important;
            -webkit-backdrop-filter: var(--ios-blur) !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 24px !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            will-change: transform;
        }

        main .card:not(form):active,
        div.content-box:not(form):active {
            transform: scale(0.96) !important;
        }

        main .form-control,
        main .form-select {
            background: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border-radius: 14px !important;
            padding: 0.7rem 1rem;
            transition: all 0.3s;
        }

        main .form-control:focus,
        main .form-select:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
            background: rgba(0, 0, 0, 0.4) !important;
        }

        main .form-control::placeholder {
            color: rgba(255, 255, 255, 0.35) !important;
        }

        main table {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        main th {
            background: transparent !important;
            color: #fff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            font-weight: 600;
        }

        main td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            color: rgba(255, 255, 255, 0.7) !important;
        }

        main .text-gray-900,
        main .text-gray-800,
        main .text-dark {
            color: #ffffff !important;
        }

        main .text-gray-600,
        main .text-gray-500,
        main .text-muted {
            color: rgba(255, 255, 255, 0.55) !important;
        }

        .capsule-alert {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: var(--ios-blur);
            -webkit-backdrop-filter: var(--ios-blur);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            color: #ffffff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            padding: 0.65rem 1.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            z-index: 50;
            animation: slideDownFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideDownFade {
            from {
                transform: translateY(-20px) scale(0.95);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body>

    <!-- Native App Progress Bar -->
    <div id="ios-loader"></div>

    <!-- AURORA BACKGROUND -->
    <div class="aurora-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- BACKDROP MOBILE -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR NAVIGATION -->
    <aside id="sidebar" class="scrollbar-hide">
        <!-- Logo Header -->
        <div class="d-flex align-items-center px-4 sidebar-logo-container transition-all"
            style="height: 80px; border-bottom: 1px solid var(--glass-border);">
            <div
                class="h-9 w-9 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-content-center shrink-0 shadow-sm transition-all logo-icon">
                <i class="ph-fill ph-planet text-white fs-5"></i>
            </div>
            <span class="fs-6 fw-bold tracking-tight text-white ms-3 hide-on-collapse" style="letter-spacing:-0.2px;">
                HOOK JOURNEY
            </span>
            <!-- Close button mobile -->
            <button onclick="toggleSidebar()"
                class="btn btn-link text-white-50 p-0 ms-auto d-lg-none shadow-none border-0 hover:text-white shrink-0 absolute right-4">
                <i class="ph ph-x fs-4"></i>
            </button>
        </div>

        <nav class="flex-grow-1 overflow-auto py-4 px-3 scrollbar-hide" hx-boost="true" hx-target="#content-body"
            hx-indicator="#ios-loader">
            <?php $current = current_uri(); ?>
            <a href="<?= base_url('dashboard') ?>"
                class="nav-link-glass spa-link <?= $current == 'dashboard' || $current == '' ? 'active' : '' ?>">
                <i class="<?= $current == 'dashboard' ? 'ph-fill' : 'ph' ?> ph-squares-four shrink-0"></i>
                <span class="hide-on-collapse">Dashboard</span>
            </a>

            <p class="nav-section-title hide-on-collapse">Operasional</p>

            <?php if ($canAccess('ads')): ?>
                <a href="<?= base_url('ads/performance') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'ads') !== false ? 'active' : '' ?>">
                    <i class="<?= strpos($current, 'ads') !== false ? 'ph-fill' : 'ph' ?> ph-trend-up shrink-0"></i>
                    <span class="hide-on-collapse">Meta Ads</span>
                </a>
            <?php endif; ?>

            <?php if ($canAccess('creative')): ?>
                <a href="<?= base_url('creative') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'creative') !== false ? 'active' : '' ?>">
                    <i class="<?= strpos($current, 'creative') !== false ? 'ph-fill' : 'ph' ?> ph-folder-star shrink-0"></i>
                    <span class="hide-on-collapse">Creative Assets</span>
                </a>
            <?php endif; ?>

            <?php if ($canAccess('content')): ?>
                <a href="<?= base_url('content/kanban') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'content') !== false ? 'active' : '' ?>">
                    <i class="<?= strpos($current, 'content') !== false ? 'ph-fill' : 'ph' ?> ph-video-camera shrink-0"></i>
                    <span class="hide-on-collapse">Content Planner</span>
                </a>
            <?php endif; ?>

            <?php if ($canAccess('tickets')): ?>
                <a href="<?= base_url('tickets') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'tickets') !== false ? 'active' : '' ?>">
                    <i class="<?= strpos($current, 'tickets') !== false ? 'ph-fill' : 'ph' ?> ph-ticket shrink-0"></i>
                    <span class="hide-on-collapse">Tasks / Tickets</span>
                </a>
            <?php endif; ?>

            <?php if ($canAccess('forum')): ?>
                <a href="<?= base_url('forum') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'forum') !== false ? 'active' : '' ?>">
                    <i class="<?= strpos($current, 'forum') !== false ? 'ph-fill' : 'ph' ?> ph-users-three shrink-0"></i>
                    <span class="hide-on-collapse">Forum Diskusi</span>
                </a>
            <?php endif; ?>

            <?php if ($canAccess('keywords')): ?>
                <a href="<?= base_url('keywords') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'keywords') !== false ? 'active' : '' ?>">
                    <i
                        class="<?= strpos($current, 'keywords') !== false ? 'ph-fill' : 'ph' ?> ph-magnifying-glass shrink-0"></i>
                    <span class="hide-on-collapse">SEO Keywords</span>
                </a>
            <?php endif; ?>

            <p class="nav-section-title hide-on-collapse">Evaluasi</p>

            <?php if ($canAccess('reviews')): ?>
                <a href="<?= base_url('reviews') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'reviews') !== false ? 'active' : '' ?>">
                    <i class="<?= strpos($current, 'reviews') !== false ? 'ph-fill' : 'ph' ?> ph-chart-bar shrink-0"></i>
                    <span class="hide-on-collapse">Weekly Review</span>
                </a>
            <?php endif; ?>

            <?php if ($userRoleInBrand === 'leader'): ?>
                <p class="nav-section-title hide-on-collapse">Konfigurasi</p>

                <a href="<?= base_url('brand/team') ?>"
                    class="nav-link-glass spa-link <?= $current == 'brand/team' ? 'active' : '' ?>">
                    <i class="<?= $current == 'brand/team' ? 'ph-fill' : 'ph' ?> ph-users-four shrink-0"></i>
                    <span class="hide-on-collapse">Kelola Tim</span>
                </a>

                <a href="<?= base_url('share-links') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'share-links') !== false ? 'active' : '' ?>">
                    <i
                        class="<?= strpos($current, 'share-links') !== false ? 'ph-fill' : 'ph' ?> ph-share-network shrink-0"></i>
                    <span class="hide-on-collapse">Akses Klien</span>
                </a>

                <a href="<?= base_url('brand/settings') ?>"
                    class="nav-link-glass spa-link <?= strpos($current, 'brand/settings') !== false ? 'active' : '' ?>">
                    <i
                        class="<?= strpos($current, 'brand/settings') !== false ? 'ph-fill' : 'ph' ?> ph-gear-six shrink-0"></i>
                    <span class="hide-on-collapse">Pengaturan</span>
                </a>
            <?php endif; ?>
            <div style="height: 20px;"></div>
        </nav>

        <!-- FIX: USER PROFILE FOOTER -->
        <div class="p-3 sidebar-footer d-flex align-items-center justify-content-between transition-all"
            style="border-top: 1px solid var(--glass-border);">
            <div class="d-flex align-items-center w-100">
                <a href="<?= base_url('profile') ?>"
                    class="d-flex align-items-center text-decoration-none group w-100 spa-link"
                    title="Edit Profil Saya">
                    <?php if (!empty($user['avatar_url'])): ?>
                        <!-- Jika file tidak ditemukan, fallback ke API Initials -->
                        <img src="<?= base_url($user['avatar_url']) ?>?v=<?= time() ?>"
                            class="rounded-circle object-cover shadow-lg border border-white/20 group-hover:border-pink-400 transition-colors shrink-0 user-avatar"
                            style="width: 40px; height: 40px;"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=4361ee&color=fff&size=128';">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-medium shadow-sm shrink-0 user-avatar"
                            style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--accent-pink), var(--accent-purple)); border: 1px solid rgba(255,255,255,0.2);">
                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <div class="ms-3 overflow-hidden flex-1 hide-on-collapse" style="max-width: 140px;">
                        <p class="mb-0 text-white fw-medium text-truncate group-hover:text-pink-400 transition-colors"
                            style="font-size: 0.88rem;"><?= htmlspecialchars($user['full_name']) ?></p>
                        <p class="mb-0 text-white-50 text-uppercase flex items-center"
                            style="font-size: 0.65rem; letter-spacing: 0.5px;">
                            <?= htmlspecialchars($userRoleInBrand) ?>
                            <i
                                class="ph-bold ph-pencil-simple ms-1 opacity-0 group-hover:opacity-100 transition-opacity text-pink-400"></i>
                        </p>
                    </div>
                </a>

                <a href="<?= base_url('auth/logout') ?>"
                    class="text-white-50 hover:text-red-400 transition-colors ms-2 hide-on-collapse shrink-0 p-2 rounded-circle hover:bg-white/5"
                    title="Logout" hx-boost="false">
                    <i class="ph ph-sign-out fs-5"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div id="main-wrapper">

        <!-- TOPBAR -->
        <header class="glass-topbar d-flex align-items-center justify-content-between px-md-5">
            <div class="d-flex align-items-center w-100">

                <!-- HAMBURGER TOGGLE (NOW VISIBLE ON DESKTOP) -->
                <button onclick="toggleSidebar()"
                    class="btn btn-link text-white p-0 me-3 shadow-none border-0 hover:text-white transition-colors opacity-75 hover:opacity-100">
                    <i class="ph ph-list fs-3"></i>
                </button>

                <!-- BRAND SWITCHER -->
                <div class="position-relative d-flex align-items-center w-100 ms-1">
                    <?php if (!empty($availableBrands)): ?>
                        <form action="<?= base_url('brand/switch') ?>" method="POST" id="form-brand-switcher"
                            class="m-0 flex-grow-1 flex-md-grow-0">
                            <select name="brand_id" onchange="this.form.submit()"
                                class="form-select glass-select shadow-none w-100">
                                <?php foreach ($availableBrands as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= ($activeBrandId == $b['id']) ? 'selected' : '' ?>>🚀
                                        <?= htmlspecialchars($b['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php else: ?>
                        <div
                            class="text-white-50 small italic px-3 py-2 bg-white/5 rounded-xl border border-white/10 backdrop-blur-md">
                            Belum Ada Brand Aktif</div>
                    <?php endif; ?>

                    <?php if ($user['role_global'] === 'leader'): ?>
                        <a href="<?= base_url('brand/create') ?>"
                            class="btn-glass ms-3 d-flex align-items-center justify-content-center text-decoration-none spa-link"
                            style="height: 38px; width: 38px; min-width: 38px;" title="Tambah Brand Baru">
                            <i class="ph ph-plus fw-bold"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT ALIGNED UTILITIES -->
            <div class="d-none d-md-flex align-items-center gap-4">
                <div class="text-end">
                    <p id="clock" class="mb-0 text-white fw-medium tracking-tight"
                        style="font-size: 0.95rem; line-height: 1.2;"></p>
                    <p class="mb-0 text-white-50" style="font-size: 0.7rem;"><?= date('D, d M Y') ?></p>
                </div>
                <button
                    class="btn-glass p-0 d-flex align-items-center justify-content-center position-relative shadow-none border-0"
                    style="width: 38px; height: 38px;">
                    <i class="ph ph-bell-simple fs-5"></i>
                    <span
                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-dark rounded-circle"
                        style="margin-top: 8px; margin-left: -8px;"></span>
                </button>
            </div>
        </header>

        <!-- SCROLLABLE AREA -->
        <main class="flex-grow-1 overflow-auto p-4 p-md-5" style="position: relative; z-index: 10;"
            id="main-scroll-area">

            <!-- DYNAMIC SPA CONTENT WRAPPER -->
            <div id="spa-content">
                <!-- NOTIFIKASI KAPSUL GLASS -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="d-flex justify-content-center mb-4">
                        <div class="capsule-alert d-flex align-items-center shadow-sm">
                            <div class="d-flex align-items-center pe-3">
                                <div
                                    class="bg-white/10 rounded-circle p-1 me-2 d-flex align-items-center justify-content-center">
                                    <i class="ph-fill ph-check-circle fs-5 text-white"></i>
                                </div>
                                <span class="fw-medium text-white"
                                    style="letter-spacing: 0.2px; font-size: 0.9rem;"><?= $_SESSION['success'] ?></span>
                            </div>
                            <button type="button" class="btn-close btn-close-white opacity-50 hover:opacity-100 ms-3"
                                data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.65rem;"></button>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="d-flex justify-content-center mb-4">
                        <div class="capsule-alert d-flex align-items-center shadow-sm"
                            style="background: rgba(255, 60, 60, 0.15); border-color: rgba(255, 60, 60, 0.3);">
                            <div class="d-flex align-items-center pe-3">
                                <div
                                    class="bg-white/10 rounded-circle p-1 me-2 d-flex align-items-center justify-content-center">
                                    <i class="ph-fill ph-warning-circle fs-5 text-white"></i>
                                </div>
                                <span class="fw-medium text-white"
                                    style="letter-spacing: 0.2px; font-size: 0.9rem;"><?= $_SESSION['error'] ?></span>
                            </div>
                            <button type="button" class="btn-close btn-close-white opacity-50 hover:opacity-100 ms-3"
                                data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.65rem;"></button>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div id="content-body" class="content-body transition-opacity duration-200">
                    <?= $content ?>
                </div>
            </div>

            <footer class="mt-auto pt-5 pb-3 text-center" style="margin-bottom: env(safe-area-inset-bottom);">
                <p class="text-white-50" style="font-size: 0.75rem; font-weight: 400;">&copy; <?= date('Y') ?> HOOK
                    JOURNEY. Fast SPA Interface.</p>
            </footer>
        </main>
    </div>

    <!-- FLOATING BOTTOM NAVIGATION PILL (MOBILE ONLY) -->
    <nav class="mobile-bottom-nav" hx-boost="true" hx-target="#content-body" hx-indicator="#ios-loader">
        <a href="<?= base_url('dashboard') ?>"
            class="nav-item-mobile spa-link <?= $current == 'dashboard' ? 'active' : '' ?>">
            <i class="<?= $current == 'dashboard' ? 'ph-fill' : 'ph' ?> ph-house"></i>
        </a>

        <?php if ($canAccess('content')): ?>
            <a href="<?= base_url('content/kanban') ?>"
                class="nav-item-mobile spa-link <?= strpos($current, 'content') !== false ? 'active' : '' ?>">
                <i class="<?= strpos($current, 'content') !== false ? 'ph-fill' : 'ph' ?> ph-video-camera"></i>
            </a>
        <?php endif; ?>

        <?php if ($canAccess('ads')): ?>
            <a href="<?= base_url('ads/performance') ?>"
                class="nav-link-mobile nav-item-mobile spa-link <?= strpos($current, 'ads') !== false ? 'active' : '' ?>">
                <i class="<?= strpos($current, 'ads') !== false ? 'ph-fill' : 'ph' ?> ph-trend-up"></i>
            </a>
        <?php endif; ?>

        <?php if ($canAccess('tickets')): ?>
            <a href="<?= base_url('tickets') ?>"
                class="nav-item-mobile spa-link <?= strpos($current, 'tickets') !== false ? 'active' : '' ?>">
                <i class="<?= strpos($current, 'tickets') !== false ? 'ph-fill' : 'ph' ?> ph-ticket"></i>
            </a>
        <?php endif; ?>

        <button onclick="toggleSidebar()" class="nav-item-mobile border-0 bg-transparent p-0">
            <i class="ph ph-list"></i>
        </button>
    </nav>

    <!-- UI SCRIPTS -->
    <script>
        function toggleSidebar() {
            const isDesktop = window.innerWidth >= 992;
            if (isDesktop) {
                document.documentElement.classList.toggle('sidebar-collapsed');
                const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
            } else {
                const s = document.getElementById('sidebar');
                const b = document.getElementById('sidebarBackdrop');
                if (s && b) { s.classList.toggle('show'); b.classList.toggle('show'); }
            }
        }

        function updateClock() {
            const n = new Date();
            const c = document.getElementById('clock');
            if (c) c.innerText = n.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(updateClock, 1000); updateClock();

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('sidebarBackdrop').classList.remove('show');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
                    .then(reg => console.log('SW Registered', reg))
                    .catch(err => console.log('SW Registration failed: ', err));
            });
        }
    </script>

    <!-- NATIVE SPA ENGINE VIA HTMX -->
    <script>
        const loader = document.getElementById('ios-loader');

        // Show progress bar on HTMX request
        document.body.addEventListener('htmx:send', function (e) {
            loader.style.width = '30%';
            loader.style.opacity = '1';
        });

        // Hide progress bar on HTMX complete and update active classes
        document.body.addEventListener('htmx:afterOnLoad', function (e) {
            // Fill loader
            loader.style.width = '100%';

            // Re-eval scripts inside new content
            const newContent = document.getElementById('content-body');
            if (newContent) {
                const scripts = newContent.querySelectorAll('script');
                scripts.forEach(s => {
                    const newScript = document.createElement('script');
                    Array.from(s.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.textContent = s.textContent;
                    document.body.appendChild(newScript).parentNode.removeChild(newScript);
                });
            }

            // Update active state in nav links
            const currentPath = new URL(e.detail.requestConfig.path, window.location.origin).pathname;
            document.querySelectorAll('a.spa-link').forEach(link => {
                const linkPath = new URL(link.href).pathname;
                const icon = link.querySelector('i');
                if (linkPath === currentPath || (currentPath === '/' && linkPath.endsWith('/dashboard'))) {
                    link.classList.add('active');
                    if (icon && icon.classList.contains('ph')) {
                        icon.classList.remove('ph');
                        icon.classList.add('ph-fill');
                    }
                } else {
                    link.classList.remove('active');
                    if (icon && icon.classList.contains('ph-fill')) {
                        icon.classList.remove('ph-fill');
                        icon.classList.add('ph');
                    }
                }
            });

            // Close mobile sidebar if open
            if (window.innerWidth < 992) {
                const s = document.getElementById('sidebar');
                const b = document.getElementById('sidebarBackdrop');
                if (s) s.classList.remove('show');
                if (b) b.classList.remove('show');
            }

            // Scroll to top
            const scrollArea = document.getElementById('main-scroll-area');
            if (scrollArea) scrollArea.scrollTop = 0;

            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.width = '0', 300);
            }, 300);
        });

        // Handle Error
        document.body.addEventListener('htmx:responseError', function (e) {
            window.location.href = e.detail.requestConfig.path;
        });
    </script>
</body>

</html>