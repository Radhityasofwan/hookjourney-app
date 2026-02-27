<?php foreach ($threads as $index => $t): ?>
    <?php
    $isLast = ($index === count($threads) - 1);
    $htmxAttrs = '';
    // Trigger the next page when the last item of a full page is revealed
    if ($isLast && count($threads) >= 10) {
        $nextPage = $page + 1;
        $htmxAttrs = 'hx-get="' . base_url('forum?page=' . $nextPage) . '" hx-trigger="revealed" hx-swap="afterend"';
    }
    ?>
    <a href="<?= base_url('forum/thread?id=' . $t['id']) ?>" <?= $htmxAttrs ?> class="block bg-white/5 backdrop-blur-xl p-5
    rounded-[24px] border
    <?= $t['is_pinned'] ? 'border-blue-500/50 bg-blue-500/10 shadow-[0_0_15px_rgba(59,130,246,0.1)]' : 'border-white/10 hover:border-white/30 hover:-translate-y-1 hover:shadow-xl' ?>
    transition-all duration-300 relative group text-decoration-none spa-link">

        <!-- Badges Khusus -->
        <div class="absolute top-5 right-5 flex items-center gap-2">
            <?php if ($t['is_locked']): ?>
                <div class="text-red-400 drop-shadow-[0_0_8px_rgba(248,113,113,0.5)]"><i
                        class="ph-fill ph-lock-key text-xl"></i></div>
            <?php endif; ?>
            <?php if ($t['is_pinned']): ?>
                <div class="text-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.5)]"><i
                        class="ph-fill ph-push-pin text-xl"></i></div>
            <?php endif; ?>
        </div>

        <h4 class="text-lg font-bold text-white mb-3 pr-16 leading-snug group-hover:text-blue-200 transition-colors">
            <?= htmlspecialchars($t['title']) ?>
        </h4>

        <div class="flex flex-wrap items-center text-xs text-white/50 gap-x-5 gap-y-2">
            <span class="flex items-center font-medium text-white/80">
                <!-- Smart Avatar -->
                <?php if (!empty($t['creator_avatar'])): ?>
                    <img src="<?= base_url($t['creator_avatar']) ?>?v=<?= time() ?>"
                        class="w-6 h-6 rounded-full object-cover shadow-sm border border-white/20 mr-2"
                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($t['creator_name']) ?>&background=4361ee&color=fff';">
                <?php else: ?>
                    <div
                        class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[9px] mr-2 shadow-sm border border-white/20">
                        <?= strtoupper(substr($t['creator_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <?= htmlspecialchars($t['creator_name']) ?>
            </span>

            <span class="flex items-center"><i class="ph-fill ph-chat-teardrop mr-1.5 text-white/40"></i>
                <?= $t['reply_count'] ?> Balasan
            </span>
            <span class="flex items-center"><i class="ph-bold ph-clock mr-1.5 text-white/40"></i>
                <?= date('d M, H:i', strtotime($t['last_post_at'])) ?>
            </span>

            <?php if ($t['category_name']): ?>
                <span class="bg-white/10 px-2.5 py-1 rounded-full text-white/70 border border-white/10 backdrop-blur-md">
                    <?= htmlspecialchars($t['category_name']) ?>
                </span>
            <?php endif; ?>

            <?php if ($t['is_resolved']): ?>
                <span
                    class="text-emerald-400 font-bold flex items-center bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20 backdrop-blur-md"><i
                        class="ph-fill ph-check-circle mr-1"></i> Resolved</span>
            <?php endif; ?>
        </div>
    </a>
<?php endforeach; ?>