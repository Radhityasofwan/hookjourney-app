<style>
    /* Custom Styling Form Input Glassy */
    .glass-input {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        border-radius: 14px;
        padding: 0.85rem 1.2rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .glass-input::placeholder {
        color: rgba(255, 255, 255, 0.4) !important;
    }
    .glass-input:focus {
        background: rgba(255, 255, 255, 0.1) !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.05) !important;
    }
    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 0.5rem;
    }
    .glass-btn {
        background: #ffffff;
        color: #050511;
        font-weight: 600;
        border-radius: 14px;
        padding: 0.85rem;
        border: none;
        transition: all 0.3s ease;
        margin-top: 1.5rem;
        font-size: 1rem;
    }
    .glass-btn:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.15);
    }
    .form-check-input {
        background-color: rgba(255,255,255,0.1);
        border-color: rgba(255,255,255,0.3);
        cursor: pointer;
    }
    .form-check-input:checked {
        background-color: #ff007f;
        border-color: #ff007f;
    }
    .glass-link {
        color: rgba(255,255,255,0.7);
        font-size: 0.85rem;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .glass-link:hover {
        color: #ffffff;
    }
    .glass-alert {
        background: rgba(255, 60, 60, 0.15);
        border: 1px solid rgba(255, 60, 60, 0.3);
        color: #ffc2c2;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }
</style>

<?php if (isset($errors['auth'])): ?>
    <div class="alert glass-alert p-3 mb-4 text-sm text-center" role="alert">
        <?= htmlspecialchars($errors['auth']) ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('auth/login') ?>" method="POST">
    
    <div class="mb-4">
        <label for="email" class="form-label">Alamat Email</label>
        <input type="email" name="email" id="email" 
               class="form-control glass-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
               placeholder="nama@perusahaan.com" 
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
               required>
        <?php if (isset($errors['email'])): ?>
            <div class="text-danger mt-2" style="font-size: 0.8rem; color: #ff8e8e !important;">
                <?= htmlspecialchars($errors['email']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label">Kata Sandi</label>
        <input type="password" name="password" id="password" 
               class="form-control glass-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
               placeholder="••••••••" 
               required>
        <?php if (isset($errors['password'])): ?>
            <div class="text-danger mt-2" style="font-size: 0.8rem; color: #ff8e8e !important;">
                <?= htmlspecialchars($errors['password']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
        <div class="form-check m-0">
            <input class="form-check-input shadow-none" type="checkbox" name="remember_me" id="remember_me">
            <label class="form-check-label text-white-50" for="remember_me" style="font-size: 0.85rem; cursor: pointer;">
                Ingat perangkat ini
            </label>
        </div>
        <div>
            <a href="#" class="glass-link">Lupa sandi?</a>
        </div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn glass-btn">
            Masuk ke Sistem
        </button>
    </div>
</form>