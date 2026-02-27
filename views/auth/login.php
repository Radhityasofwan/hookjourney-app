<?php if (isset($errors['auth'])): ?>
    <div
        class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-4 rounded-2xl mb-6 text-sm text-center font-medium">
        <?= htmlspecialchars($errors['auth']) ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('auth/login') ?>" method="POST" class="w-full">

    <div class="mb-5 relative">
        <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ms-1">Alamat
            Email</label>
        <input type="email" name="email" id="email"
            class="w-full bg-gray-100 dark:bg-gray-800/50 text-black dark:text-white rounded-2xl px-5 py-4 text-[17px] focus:outline-none focus:ring-2 focus:ring-ios-blue border-none placeholder-gray-400 dark:placeholder-gray-500 transition-all <?= isset($errors['email']) ? 'ring-2 ring-red-500' : '' ?>"
            placeholder="nama@perusahaan.com"
            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
        <?php if (isset($errors['email'])): ?>
            <div class="text-red-500 mt-2 ms-2 text-xs font-medium">
                <?= htmlspecialchars($errors['email']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-5 relative">
        <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ms-1">Kata
            Sandi</label>
        <input type="password" name="password" id="password"
            class="w-full bg-gray-100 dark:bg-gray-800/50 text-black dark:text-white rounded-2xl px-5 py-4 text-[17px] focus:outline-none focus:ring-2 focus:ring-ios-blue border-none placeholder-gray-400 dark:placeholder-gray-500 transition-all <?= isset($errors['password']) ? 'ring-2 ring-red-500' : '' ?>"
            placeholder="••••••••" required>
        <?php if (isset($errors['password'])): ?>
            <div class="text-red-500 mt-2 ms-2 text-xs font-medium">
                <?= htmlspecialchars($errors['password']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="flex items-center justify-between mb-8 px-1">
        <label class="flex items-center space-x-3 cursor-pointer group">
            <div class="relative flex items-center justify-center">
                <input type="checkbox" name="remember_me" id="remember_me"
                    class="peer appearance-none w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-transparent checked:bg-ios-blue checked:border-ios-blue transition-all cursor-pointer">
                <i
                    class="ph-bold ph-check absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs pointer-events-none"></i>
            </div>
            <span
                class="text-[15px] font-medium text-gray-600 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors">Ingat
                saya</span>
        </label>
        <a href="#"
            class="text-[15px] font-medium text-ios-blue hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Lupa
            sandi?</a>
    </div>

    <button type="submit"
        class="w-full bg-ios-blue text-white rounded-2xl px-5 py-4 text-[17px] font-semibold active:scale-[0.96] transition-transform duration-200 mt-2 shadow-[0_4px_14px_rgba(0,122,255,0.3)] dark:shadow-none flex items-center justify-center">
        Masuk ke Sistem
    </button>
</form>