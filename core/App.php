<?php

class App {
    public $router;

    public function __construct() {
        // 1. Muat Konfigurasi Dasar
        require_once __DIR__ . '/../config/app.php';
        require_once __DIR__ . '/../config/constants.php';
        
        // 2. Muat Helper Global
        $this->loadHelpers();

        // 3. Registrasi Autoloader Otomatis
        spl_autoload_register(function ($className) {
            $directories = ['core', 'controllers', 'models', 'middlewares', 'services'];
            foreach ($directories as $dir) {
                $file = __DIR__ . '/../' . $dir . '/' . $className . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });

        // 4. Inisialisasi Router
        $this->router = new Router();
    }

    /**
     * Memuat file-file helper yang dibutuhkan secara global
     */
    private function loadHelpers() {
        $helpers = ['url_helper.php', 'format_helper.php', 'date_helper.php'];
        foreach ($helpers as $helper) {
            $file = __DIR__ . '/../helpers/' . $helper;
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    /**
     * Registrasi Seluruh Rute Aplikasi (Routing Table)
     */
    private function registerRoutes() {
        // --- AUTH ROUTES ---
        $this->router->get('auth/login', 'AuthController@login');
        $this->router->post('auth/login', 'AuthController@login');
        $this->router->get('auth/logout', 'AuthController@logout');

        // --- DASHBOARD ROUTE ---
        $this->router->get('dashboard', 'DashboardController@index');
        $this->router->get('', 'DashboardController@index');

        // --- PROFILE ROUTES ---
        $this->router->get('profile', 'ProfileController@index');
        $this->router->post('profile/update', 'ProfileController@update');

        // --- BRAND SETTINGS & ONBOARDING ---
        $this->router->post('brand/switch', 'BrandController@switch');
        $this->router->get('brand/create', 'BrandController@create');
        $this->router->post('brand/store', 'BrandController@store');
        $this->router->get('brand/settings', 'BrandController@settings');
        $this->router->post('brand/ad-account/store', 'BrandController@storeAdAccount');
        $this->router->post('brand/pillar/store', 'BrandController@storePillar');
        $this->router->post('brand/ticket-category/store', 'BrandController@storeTicketCategory');
        $this->router->post('brand/forum-category/store', 'BrandController@storeForumCategory');
        
        // --- TEAM MANAGEMENT & RBAC ROUTES ---
        $this->router->get('brand/team', 'TeamController@index');
        $this->router->post('brand/team/store', 'TeamController@store');
        $this->router->post('brand/team/delete', 'TeamController@delete');

        // --- META ADS ROUTES ---
        $this->router->get('ads/import', 'AdImportController@index');
        $this->router->post('ads/import/upload', 'AdImportController@upload');
        $this->router->post('ads/import/process', 'AdImportController@processQueue'); 
        $this->router->post('ads/import/delete', 'AdImportController@delete'); 
        $this->router->get('ads/performance', 'AdPerformanceController@index');

        // --- CONTENT PLANNER ROUTES ---
        $this->router->get('content/kanban', 'ContentController@kanban');
        $this->router->get('content/create', 'ContentController@create');
        $this->router->post('content/store', 'ContentController@store');
        $this->router->get('content/edit', 'ContentController@edit');
        $this->router->post('content/update', 'ContentController@update');
        $this->router->post('content/delete', 'ContentController@delete');
        $this->router->post('content/update-status', 'ContentController@updateStatus');

        // --- TICKETING ROUTES ---
        $this->router->get('tickets', 'TicketController@index');
        $this->router->get('tickets/create', 'TicketController@create');
        $this->router->post('tickets/store', 'TicketController@store');
        $this->router->get('tickets/edit', 'TicketController@edit');
        $this->router->post('tickets/update', 'TicketController@update');
        $this->router->post('tickets/delete', 'TicketController@delete');
        $this->router->post('tickets/update-status', 'TicketController@updateStatus');
        $this->router->get('tickets/api-detail', 'TicketController@apiDetail');
        $this->router->post('tickets/store-comment', 'TicketController@storeComment');
        $this->router->post('tickets/toggle-checklist', 'TicketController@toggleChecklist');

        // --- FORUM DISKUSI ROUTES ---
        $this->router->get('forum', 'ForumController@index');
        $this->router->post('forum/store', 'ForumController@store');
        $this->router->get('forum/thread', 'ForumController@show');
        $this->router->post('forum/reply', 'ForumController@reply');
        $this->router->post('forum/update-status', 'ForumController@updateStatus');
        $this->router->post('forum/delete', 'ForumController@delete');

        // --- SEO KEYWORD ROUTES ---
        $this->router->get('keywords', 'KeywordController@index');
        $this->router->post('keywords/store', 'KeywordController@store');
        $this->router->post('keywords/update-status', 'KeywordController@updateStatus');
        $this->router->post('keywords/delete', 'KeywordController@delete');
        $this->router->post('keywords/store-cluster', 'KeywordController@storeCluster');

        // --- CREATIVE LIBRARY ROUTES ---
        $this->router->get('creative', 'CreativeController@index');
        $this->router->post('creative/store', 'CreativeController@store');

        // --- WEEKLY REVIEW ROUTES ---
        $this->router->get('reviews', 'WeeklyReviewController@index');
        $this->router->post('reviews/generate', 'WeeklyReviewController@generate');
        $this->router->get('reviews/edit', 'WeeklyReviewController@edit');
        $this->router->post('reviews/update', 'WeeklyReviewController@update');
        $this->router->post('reviews/delete', 'WeeklyReviewController@delete');

        // --- SHARE LINKS (CLIENT PORTAL) ROUTES ---
        $this->router->get('share-links', 'ShareLinkController@index');
        $this->router->post('share-links/generate', 'ShareLinkController@generate');
        $this->router->post('share-links/delete', 'ShareLinkController@delete');
        $this->router->get('shared', 'ShareLinkController@portal'); 
    }

    public function run() {
        $this->registerRoutes();
        $this->router->run();
    }
}