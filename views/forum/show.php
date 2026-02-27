<!-- Header Interaktif -->
<div class="mb-6 flex items-center justify-between relative z-50">
    <div class="flex items-center">
        <a href="<?= base_url('forum') ?>" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition-all mr-4 text-decoration-none">
            <i class="ph-bold ph-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight leading-snug"><?= htmlspecialchars($thread['title']) ?></h1>
            
            <!-- Badges Status Dinamis -->
            <div class="flex flex-wrap gap-2 mt-2">
                <?php if($thread['category_name'] ?? false): ?>
                    <span class="px-2.5 py-0.5 rounded bg-white/10 text-[9px] font-bold text-white/70 uppercase tracking-widest"><?= htmlspecialchars($thread['category_name']) ?></span>
                <?php endif; ?>
                
                <?php if($thread['is_pinned']): ?>
                    <span class="px-2.5 py-0.5 rounded border-blue-500/30 bg-blue-500/20 text-[9px] font-bold text-blue-300 uppercase tracking-widest flex items-center"><i class="ph-fill ph-push-pin mr-1.5"></i> Pinned</span>
                <?php endif; ?>
                
                <?php if($thread['is_resolved']): ?>
                    <span class="px-2.5 py-0.5 rounded border-emerald-500/30 bg-emerald-500/20 text-[9px] font-bold text-emerald-300 uppercase tracking-widest flex items-center"><i class="ph-fill ph-check-circle mr-1.5"></i> Resolved</span>
                <?php endif; ?>
                
                <?php if($thread['is_locked']): ?>
                    <span class="px-2.5 py-0.5 rounded border-red-500/30 bg-red-500/20 text-[9px] font-bold text-red-300 uppercase tracking-widest flex items-center"><i class="ph-fill ph-lock-key mr-1.5"></i> Locked</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- FIX: Dropdown Menu Aksi Khusus Leader -->
    <?php if($user['role_global'] === 'leader'): ?>
        <div class="dropdown">
            <button class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 text-white transition-all" data-bs-toggle="dropdown">
                <i class="ph-bold ph-dots-three-vertical text-xl"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark p-1.5" style="border-radius: 16px; background: rgba(15,12,41,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.15); min-width: 180px; z-index: 100;">
                <li>
                    <form action="<?= base_url('forum/update-status') ?>" method="POST" class="m-0">
                        <input type="hidden" name="id" value="<?= $thread['id'] ?>">
                        <input type="hidden" name="action" value="is_pinned">
                        <input type="hidden" name="value" value="<?= $thread['is_pinned'] ? 0 : 1 ?>">
                        <button type="submit" class="dropdown-item py-2 px-3 rounded-2xl flex items-center text-xs text-white/80 hover:text-white hover:bg-white/10">
                            <i class="ph-bold <?= $thread['is_pinned'] ? 'ph-push-pin-slash' : 'ph-push-pin' ?> mr-2 text-sm"></i> <?= $thread['is_pinned'] ? 'Unpin Diskusi' : 'Pin Diskusi' ?>
                        </button>
                    </form>
                </li>
                <li>
                    <form action="<?= base_url('forum/update-status') ?>" method="POST" class="m-0">
                        <input type="hidden" name="id" value="<?= $thread['id'] ?>">
                        <input type="hidden" name="action" value="is_resolved">
                        <input type="hidden" name="value" value="<?= $thread['is_resolved'] ? 0 : 1 ?>">
                        <button type="submit" class="dropdown-item py-2 px-3 rounded-2xl flex items-center text-xs <?= $thread['is_resolved'] ? 'text-white/80 hover:text-white' : 'text-emerald-400 hover:text-emerald-300' ?> hover:bg-white/10">
                            <i class="ph-bold <?= $thread['is_resolved'] ? 'ph-x-circle' : 'ph-check-circle' ?> mr-2 text-sm"></i> <?= $thread['is_resolved'] ? 'Batal Resolve' : 'Tandai Selesai' ?>
                        </button>
                    </form>
                </li>
                <li>
                    <form action="<?= base_url('forum/update-status') ?>" method="POST" class="m-0">
                        <input type="hidden" name="id" value="<?= $thread['id'] ?>">
                        <input type="hidden" name="action" value="is_locked">
                        <input type="hidden" name="value" value="<?= $thread['is_locked'] ? 0 : 1 ?>">
                        <button type="submit" class="dropdown-item py-2 px-3 rounded-2xl flex items-center text-xs text-orange-400 hover:text-orange-300 hover:bg-white/10">
                            <i class="ph-bold <?= $thread['is_locked'] ? 'ph-lock-key-open' : 'ph-lock-key' ?> mr-2 text-sm"></i> <?= $thread['is_locked'] ? 'Buka Kunci (Unlock)' : 'Kunci Diskusi (Lock)' ?>
                        </button>
                    </form>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form action="<?= base_url('forum/delete') ?>" method="POST" class="m-0" onsubmit="return confirm('Hapus seluruh diskusi ini secara permanen?')">
                        <input type="hidden" name="id" value="<?= $thread['id'] ?>">
                        <button type="submit" class="dropdown-item py-2 px-3 rounded-2xl flex items-center text-xs text-red-400 hover:bg-red-500/20">
                            <i class="ph-bold ph-trash mr-2 text-sm"></i> Hapus Diskusi
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<div class="bg-ios-cardLight dark:bg-ios-cardDark backdrop-blur-3xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-none active:scale-[0.98] transition-transform duration-200 rounded-[28px] overflow-hidden mb-6 relative z-40 flex flex-col">
    <!-- List Posting / Balasan -->
    <div class="divide-y divide-white/5">
        <?php foreach($posts as $idx => $p): ?>
            <div class="p-6 <?= $idx === 0 ? 'bg-white/5' : '' ?> transition-colors hover:bg-white/5">
                <div class="flex items-start">
                    
                    <!-- FIX: Smart Avatar Injector untuk Postingan -->
                    <div class="flex-shrink-0 h-10 w-10 md:h-12 md:w-12 rounded-full overflow-hidden bg-black/50">
                        <?php if (!empty($p['author_avatar'])): ?>
                            <img src="<?= base_url($p['author_avatar']) ?>?v=<?= time() ?>" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($p['author_name']) ?>&background=4361ee&color=fff';">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white text-base md:text-lg">
                                <?= strtoupper(substr($p['author_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="ml-4 flex-1">
                        <div class="flex items-baseline justify-between mb-2">
                            <div class="flex items-center">
                                <span class="font-bold text-white text-sm"><?= htmlspecialchars($p['author_name']) ?></span>
                                <?php 
                                    $roleColor = 'bg-white/10 text-white/70 ';
                                    if ($p['role_global'] == 'leader') $roleColor = 'bg-purple-500/20 text-purple-300 border-purple-500/30';
                                ?>
                                <span class="text-[9px] font-bold uppercase <?= $roleColor ?> backdrop-blur-md px-2 py-0.5 rounded-full ml-3 tracking-wider"><?= $p['role_global'] ?></span>
                            </div>
                            <span class="text-xs font-medium text-white/40"><i class="ph-bold ph-clock mr-1"></i> <?= date('d M Y, H:i', strtotime($p['created_at'])) ?></span>
                        </div>
                        <div class="text-white/80 text-sm whitespace-pre-wrap mt-2 leading-relaxed" style="line-height: 1.6;"><?= htmlspecialchars($p['post_text']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Form Balas (Sticky di bawah Card) -->
    <?php if(!$thread['is_locked']): ?>
    <div class="p-5 bg-black/20 border-t backdrop-blur-2xl">
        <form action="<?= base_url('forum/reply') ?>" method="POST" class="flex flex-col sm:flex-row gap-4 items-end sm:items-center">
            <input type="hidden" name="thread_id" value="<?= $thread['id'] ?>">
            <div class="flex-grow w-full relative">
                <textarea name="post_text" rows="1" class="w-full px-5 py-3.5 bg-white/5 rounded-3xl text-white focus:ring-2 focus:ring-white/20 focus: outline-none transition-all text-sm placeholder-white/40 resize-none min-h-[52px]" placeholder="Ketik balasan Anda di sini..." required style="border-radius: 26px;"></textarea>
            </div>
            <button type="submit" class="flex items-center justify-center px-6 py-3.5 bg-white text-black rounded-full font-bold shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:scale-105 active:scale-95 transition-all text-sm flex-shrink-0">
                <i class="ph-bold ph-paper-plane-right mr-2 text-lg"></i> Kirim
            </button>
        </form>
    </div>
    <?php else: ?>
        <div class="p-6 bg-red-500/10 backdrop-blur-md text-center text-red-300 text-sm font-medium border-t border-red-500/20 flex items-center justify-center">
            <i class="ph-fill ph-lock-key mr-2 text-lg text-red-400"></i> Diskusi ini telah dikunci (Locked) dan tidak dapat menerima balasan baru.
        </div>
    <?php endif; ?>
</div>