<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Forum Diskusi Tim</h1>
        <p class="text-white/60 text-sm mt-1">Kolaborasi dan berbagi insight untuk <strong class="text-white"><?= htmlspecialchars($activeBrand['name']) ?></strong></p>
    </div>
</div>

<?php if(isset($success_msg)): ?>
    <div class="mb-6 p-4 rounded-[50px] bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300 flex items-center shadow-lg backdrop-blur-md relative z-40">
        <i class="ph-fill ph-check-circle text-xl mr-3 text-emerald-400"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-40">
    
    <!-- Form Buat Thread Kiri -->
    <div class="lg:col-span-4">
        <div class="bg-white/5 backdrop-blur-xl p-6 rounded-[28px] border border-white/10 shadow-lg sticky top-6">
            <h3 class="font-bold text-white/90 mb-5 border-b border-white/10 pb-3 flex items-center text-sm uppercase tracking-wider">
                <i class="ph-fill ph-chat-circle-text mr-2 text-blue-400 text-lg"></i> Buat Topik Baru
            </h3>
            <form action="<?= base_url('forum/store') ?>" method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Judul Diskusi <span class="text-red-400">*</span></label>
                    <input type="text" name="title" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30" required placeholder="Tulis judul topik...">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Kategori (Opsional)</label>
                    <select name="category_id" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm appearance-none cursor-pointer">
                        <option value="" class="bg-gray-900 text-white/50">-- Tanpa Kategori --</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" class="bg-gray-900 text-white"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/60 mb-2 uppercase tracking-wide">Pesan Pembuka <span class="text-red-400">*</span></label>
                    <textarea name="post_text" rows="5" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-white/20 focus:border-white/30 outline-none transition-all text-sm placeholder-white/30 resize-none" required placeholder="Jelaskan detail diskusi yang ingin dibahas..."></textarea>
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 flex items-center justify-center bg-white text-black rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] text-sm font-bold hover:scale-105 active:scale-95 transition-all">
                        <i class="ph-bold ph-paper-plane-tilt mr-2 text-lg"></i> Posting Diskusi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Thread Kanan -->
    <div class="lg:col-span-8 space-y-4">
        <?php if(empty($threads)): ?>
            <div class="bg-white/5 backdrop-blur-xl p-12 rounded-[28px] border border-white/10 shadow-lg text-center text-white/40 flex flex-col items-center justify-center min-h-[300px]">
                <i class="ph-fill ph-chats text-6xl mb-4 text-white/20"></i>
                <p class="text-sm font-medium">Belum ada topik diskusi.</p>
                <p class="text-xs mt-1 text-white/30">Jadilah yang pertama memulai obrolan dengan tim Anda!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach($threads as $t): ?>
                    <a href="<?= base_url('forum/thread?id=' . $t['id']) ?>" class="block bg-white/5 backdrop-blur-xl p-5 rounded-[24px] border <?= $t['is_pinned'] ? 'border-blue-500/50 bg-blue-500/10 shadow-[0_0_15px_rgba(59,130,246,0.1)]' : 'border-white/10 hover:border-white/30 hover:-translate-y-1 hover:shadow-xl' ?> transition-all duration-300 relative group text-decoration-none">
                        
                        <!-- Badges Khusus -->
                        <div class="absolute top-5 right-5 flex items-center gap-2">
                            <?php if($t['is_locked']): ?>
                                <div class="text-red-400 drop-shadow-[0_0_8px_rgba(248,113,113,0.5)]"><i class="ph-fill ph-lock-key text-xl"></i></div>
                            <?php endif; ?>
                            <?php if($t['is_pinned']): ?>
                                <div class="text-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.5)]"><i class="ph-fill ph-push-pin text-xl"></i></div>
                            <?php endif; ?>
                        </div>
                        
                        <h4 class="text-lg font-bold text-white mb-3 pr-16 leading-snug group-hover:text-blue-200 transition-colors"><?= htmlspecialchars($t['title']) ?></h4>
                        
                        <div class="flex flex-wrap items-center text-xs text-white/50 gap-x-5 gap-y-2">
                            <span class="flex items-center font-medium text-white/80">
                                <!-- FIX: Smart Avatar Injector -->
                                <?php if (!empty($t['creator_avatar'])): ?>
                                    <img src="<?= base_url($t['creator_avatar']) ?>?v=<?= time() ?>" class="w-6 h-6 rounded-full object-cover shadow-sm border border-white/20 mr-2" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($t['creator_name']) ?>&background=4361ee&color=fff';">
                                <?php else: ?>
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[9px] mr-2 shadow-sm border border-white/20">
                                        <?= strtoupper(substr($t['creator_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <?= htmlspecialchars($t['creator_name']) ?>
                            </span>
                            
                            <span class="flex items-center"><i class="ph-fill ph-chat-teardrop mr-1.5 text-white/40"></i> <?= $t['reply_count'] ?> Balasan</span>
                            <span class="flex items-center"><i class="ph-bold ph-clock mr-1.5 text-white/40"></i> <?= date('d M, H:i', strtotime($t['last_post_at'])) ?></span>
                            
                            <?php if($t['category_name']): ?>
                                <span class="bg-white/10 px-2.5 py-1 rounded-full text-white/70 border border-white/10 backdrop-blur-md"><?= htmlspecialchars($t['category_name']) ?></span>
                            <?php endif; ?>
                            
                            <?php if($t['is_resolved']): ?>
                                <span class="text-emerald-400 font-bold flex items-center bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20 backdrop-blur-md"><i class="ph-fill ph-check-circle mr-1"></i> Resolved</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>