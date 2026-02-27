<?php /* views/auth/login.php — form sinkron dengan glass dark style auth.php */ ?>

<?php if (isset($errors['auth'])): ?>
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/25 text-red-300 px-4 py-3 rounded-2xl mb-6 text-sm font-medium backdrop-blur-sm">
        <i class="ph-fill ph-warning-circle text-xl flex-shrink-0 text-red-400"></i>
        <span><?= htmlspecialchars($errors['auth']) ?></span>
    </div>
<?php endif; ?>

<form action="<?= base_url('auth/login') ?>" method="POST" class="w-full space-y-5">

    <!-- Email -->
    <div>
        <label for="email" class="auth-label">Alamat Email</label>
        <div class="relative">
            <i class="ph ph-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-white/30 text-lg pointer-events-none"></i>
            <input
                type="email"
                name="email"
                id="email"
                class="auth-input pl-10 <?= isset($errors['email']) ? 'error' : '' ?>"
                placeholder="nama@perusahaan.com"
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                autocomplete="email"
                required>
        </div>
        <?php if (isset($errors['email'])): ?>
            <p class="text-red-400 mt-1.5 text-xs font-medium flex items-center gap-1">
                <i class="ph ph-x-circle"></i><?= htmlspecialchars($errors['email']) ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="auth-label">Kata Sandi</label>
        <div class="relative">
            <i class="ph ph-lock-simple absolute left-3.5 top-1/2 -translate-y-1/2 text-white/30 text-lg pointer-events-none"></i>
            <input
                type="password"
                name="password"
                id="password"
                class="auth-input pl-10 <?= isset($errors['password']) ? 'error' : '' ?>"
                placeholder="••••••••"
                autocomplete="current-password"
                required>
            <!-- Toggle password visibility -->
            <button type="button" onclick="togglePw()" id="pw-toggle"
                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/60 transition-colors">
                <i id="pw-icon" class="ph ph-eye text-lg"></i>
            </button>
        </div>
        <?php if (isset($errors['password'])): ?>
            <p class="text-red-400 mt-1.5 text-xs font-medium flex items-center gap-1">
                <i class="ph ph-x-circle"></i><?= htmlspecialchars($errors['password']) ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Remember Me + Lupa Sandi -->
    <div class="flex items-center justify-between pt-1">
        <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <div class="relative flex items-center justify-center">
                <input type="checkbox" name="remember_me" id="remember_me"
                    class="peer appearance-none w-[18px] h-[18px] border border-white/25 rounded-md bg-white/5 checked:bg-ios-blue checked:border-ios-blue transition-all cursor-pointer">
                <i class="ph-bold ph-check absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity text-[10px] pointer-events-none"></i>
            </div>
            <span class="text-sm font-medium text-white/55 hover:text-white/80 transition-colors">Ingat saya</span>
        </label>
        <a href="#" class="text-sm font-semibold text-[#007AFF] hover:text-blue-400 transition-colors">Lupa sandi?</a>
    </div>

    <!-- Submit -->
    <div class="pt-2">
        <button type="submit" class="auth-btn flex items-center justify-center gap-2">
            <i class="ph-bold ph-sign-in text-lg"></i>
            Masuk ke Sistem
        </button>
    </div>

</form>

<script>
function togglePw() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pw-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ph ph-eye-slash text-lg';
    } else {
        input.type = 'password';
        icon.className = 'ph ph-eye text-lg';
    }
}
</script>