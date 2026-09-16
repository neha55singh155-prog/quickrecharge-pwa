<?php
$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';
$store = new DataStore($basePath . '/admin/data');
$h = $store->getHomeContent();
$operators = $store->getOperators();
$activeOperators = array_filter($operators, fn($o) => !empty($o['active']));
$h = array_merge([
    'app_name' => 'QuickRecharge',
    'app_tagline' => 'Fast • Secure • Instant',
    'offer_title' => 'Special Offer Ends In',
    'offer_minutes' => 9,
    'offer_seconds' => 48,
    'promo_brand' => 'PhonePe',
    'promo_title' => 'BEST SAVINGS OFFERS',
    'promo_subtitle' => 'Save more on every recharge • Limited period',
    'promo_hot_deal' => 'HOT DEAL — 2M+ Recharges',
    'promo_plans' => [],
    'stats' => [],
    'trust_items' => ['Protected', 'Instant Plans', 'Verified'],
    'top_banner_slides' => [],
], $h);
$slides = $h['top_banner_slides'];
if (is_array($slides)) {
    usort($slides, fn($a,$b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
    $slides = array_values(array_filter($slides, fn($s) => !empty($s['active'])));
}
$pp = $h['promo_plans'];
if (count($pp) < 4) { while (count($pp) < 4) $pp[] = ['price'=>'','data'=>'','validity'=>'','calls'=>'','featured'=>false,'ribbon'=>'']; }
$pp = array_slice($pp, 0, 4);
$promoImgs = [
    'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=260&fit=crop&crop=center',
    'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=400&h=260&fit=crop&crop=center',
    'https://images.unsplash.com/photo-1611162616805-cab63840091b?w=400&h=260&fit=crop&crop=center',
    'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?w=400&h=260&fit=crop&crop=center',
];
$st = $h['stats'];
if (count($st) < 4) { while (count($st) < 4) $st[] = ['value'=>'','label'=>'']; }
$ti = $h['trust_items'];
if (count($ti) < 3) { while (count($ti) < 3) $ti[] = ''; }
?>

<div class="home-screen" id="homeScreen">
    <div class="home-skeleton" id="homeSkeleton">
        <div class="hs-header"><div class="skeleton" style="width:40px;height:40px;border-radius:12px;"></div><div><div class="skeleton" style="width:120px;height:16px;border-radius:8px;margin-bottom:6px;"></div><div class="skeleton" style="width:80px;height:10px;border-radius:6px;"></div></div></div>
        <div class="skeleton" style="width:100%;height:48px;border-radius:14px;margin:16px 0;"></div>
        <div class="skeleton" style="width:100%;height:180px;border-radius:18px;margin-bottom:16px;"></div>
        <div class="skeleton" style="width:100%;height:220px;border-radius:22px;margin-bottom:16px;"></div>
    </div>
    <div class="home-bg-orb orb1"></div>
    <div class="home-bg-orb orb2"></div>
    <div class="home-bg-orb orb3"></div>

    <!-- Header -->
    <header class="home-header">
        <div class="home-header-left">
            <div class="home-logo">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 2L15 8.5L22 9L17 14L18.5 21L12 17.5L5.5 21L7 14L2 9L9 8.5L12 2Z" fill="white" stroke="white" stroke-width="1.2" stroke-linejoin="round"/></svg>
                <span class="home-logo-ring"></span>
            </div>
            <div class="home-user-info">
                <h2><?php echo htmlspecialchars($h['app_name']); ?></h2>
                <p><span class="live-dot"></span> <?php echo htmlspecialchars($h['app_tagline']); ?></p>
            </div>
        </div>
        <div class="home-header-actions">
            <button class="home-header-btn" aria-label="Help" onclick="App.showToast('Help coming soon','info')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.8 2.8 0 0 1 4.2 2.1c0 1.7-2.2 2.5-2.2 2.5"/><circle cx="12" cy="17" r="1" fill="currentColor" stroke="none"/></svg>
            </button>
            <button class="home-header-btn has-dot" aria-label="Notifications" onclick="App.showToast('No new notifications','info')">
                <span class="notification-dot"></span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M6 13a6 6 0 0 1 12 0c0 4.2-1.2 5.6-1.8 6H7.8C7.2 18.6 6 17.2 6 13Z"/><path d="M10.2 19.8a2.5 2.5 0 0 0 3.6 0"/><path d="M12 3v1"/></svg>
            </button>
        </div>
    </header>

    <!-- Top Banner Carousel -->
    <?php if (!empty($slides)): ?>
    <div class="top-banner-carousel" id="topBannerCarousel">
        <div class="top-banner-track" id="topBannerTrack">
            <?php foreach ($slides as $idx => $s): ?>
            <div class="top-banner-slide" style="background:linear-gradient(135deg,<?php echo htmlspecialchars($s['bg_color'] ?? '#5F259F'); ?> 0%,<?php echo htmlspecialchars($s['accent_color'] ?? '#9B59B6'); ?>CC 100%);">
                <div class="top-banner-content">
                    <div class="top-banner-text">
                        <div class="top-banner-title"><?php echo htmlspecialchars($s['title'] ?? ''); ?></div>
                        <div class="top-banner-subtitle"><?php echo htmlspecialchars($s['subtitle'] ?? ''); ?></div>
                        <button class="top-banner-btn">Recharge Now</button>
                    </div>
                    <div class="top-banner-icon"><?php echo htmlspecialchars($s['icon'] ?? '🎁'); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="top-banner-dots" id="topBannerDots">
            <?php foreach ($slides as $idx => $s): ?>
            <span class="top-banner-dot<?php echo $idx === 0 ? ' active' : ''; ?>" data-idx="<?php echo $idx; ?>"></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Offer Ticker -->
    <div class="offer-ticker slide-up">
        <div class="offer-ticker-left">
            <span class="offer-flame"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3s5 3.2 5 7.2c0 1.9-.8 3.2-2 4.3 0 0 1.2-1 1.2-3 0 2.6-1.7 4.8-4.2 5.5A6.2 6.2 0 0 1 7 10c0-1.8 1-3.3 2.2-4.4-.4 1-.4 2.1.1 3.1C9 7.2 9.8 4.6 12 3Z" fill="#FF6B6B" stroke="#FF4444" stroke-width="1.2"/></svg></span>
            <span class="offer-ticker-text"><?php echo htmlspecialchars($h['offer_title']); ?></span>
        </div>
        <div class="offer-ticker-right">
            <span class="offer-timer-badge" id="offerTimer"><?php echo str_pad($h['offer_minutes'], 2, '0', STR_PAD_LEFT) . ':' . str_pad($h['offer_seconds'], 2, '0', STR_PAD_LEFT); ?></span>
            <span class="offer-ticker-progress"><i id="offerProgress"></i></span>
        </div>
    </div>

    <!-- Promo Banner — Auto Image Slider -->
    <div class="promo-banner slide-up" style="animation-delay:0.08s; padding:0; overflow:hidden;">
        <div class="promo-image-full-wrap" id="promoImageFullWrap">
            <div class="promo-image-track-full" id="promoImgFullTrack">
                <div class="promo-image-slide" style="background:white; height:auto;"><img src="uploads/pw1.png" alt="Recharge Plans" loading="lazy" style="width:100%; height:auto; object-fit:contain; display:block; background:white;"></div>
                <div class="promo-image-slide" style="background:white; height:auto;"><img src="uploads/pw2.png" alt="Big Savings" loading="lazy" style="width:100%; height:auto; object-fit:contain; display:block; background:white;"></div>
                <div class="promo-image-slide" style="background:white; height:auto;"><img src="uploads/pw4.png" alt="Recharge Anytime Anywhere" loading="lazy" style="width:100%; height:auto; object-fit:contain; display:block; background:white;"></div>
            </div>
            <div class="promo-image-dots-full" id="promoImgFullDots">
                <span class="active" data-idx="0"></span>
                <span data-idx="1"></span>
            </div>
        </div>
    </div>
    <div style="display:none">
        <div class="promo-plans-wrap">
            <div class="promo-plans" id="promoSlider">
                <?php foreach ($pp as $i => $c): ?>
                <div class="promo-plan-card<?php echo !empty($c['featured']) ? ' featured' : ''; ?>">
                    <?php if (!empty($c['ribbon'])): ?><span class="featured-ribbon"><?php echo htmlspecialchars($c['ribbon']); ?></span><?php endif; ?>
                    <div class="promo-img-wrap"><img src="<?php echo htmlspecialchars($promoImgs[$i % 4]); ?>" alt="Offer" loading="lazy"></div>
                    <div class="promo-plan-price">₹<?php echo htmlspecialchars($c['price'] ?: [399,299,499,199][$i % 4]); ?><span>/-</span></div>
                    <div class="promo-plan-feature"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M12 8v-2"/><path d="M12 18v-2"/><path d="M8 12H6"/><path d="M18 12h-2"/></svg> <?php echo htmlspecialchars($c['data'] ?: ['2GB/DAY','2GB/DAY','3GB/DAY','1.5GB/DAY'][$i % 4]); ?></div>
                    <div class="promo-plan-feature"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12,7 12,12 15,14"/></svg> <?php echo htmlspecialchars($c['validity'] ?: ['180 DAYS','84 DAYS','365 DAYS','56 DAYS'][$i % 4]); ?></div>
                    <div class="promo-plan-feature"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-2"/><path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M12 12v4"/></svg> <?php echo htmlspecialchars($c['calls'] ?: 'Unlimited'); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="promo-dots" id="promoDots"></div>
        </div>
        <div class="promo-bottom">
            <div class="hot-deal-badge"><span class="hot-dot"></span> <?php echo htmlspecialchars($h['promo_hot_deal']); ?></div>
            <div class="promo-operators">
                <?php foreach ($activeOperators as $ao): ?>
                <?php if (!empty($ao['image'])): ?>
                <img src="<?php echo htmlspecialchars($ao['image']); ?>" alt="<?php echo htmlspecialchars($ao['name']); ?>" class="promo-operator-logo" style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid #E5E5E5">
                <?php else: ?>
                <div class="promo-operator-logo <?php echo htmlspecialchars($ao['code']); ?>" style="background:<?php echo htmlspecialchars($ao['color'] ?? '#6C2BFF'); ?>;width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px"><?php echo strtoupper(substr($ao['name'], 0, 2)); ?></div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recharge Card -->
    <div class="recharge-card slide-up" style="animation-delay:0.16s">
        <div class="recharge-glow"></div>
        <div class="recharge-card-header">
            <div class="recharge-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="6" y="2" width="12" height="20" rx="3"/><path d="M12 18h.01"/><path d="M9 6h6"/><path d="M8 10h8"/><path d="M8 13h8"/></svg>
            </div>
            <div class="recharge-card-titles">
                <h3 class="recharge-card-title">Recharge your mobile</h3>
                <p class="recharge-card-subtitle">Select operator & enter number</p>
            </div>
        </div>
        <div class="secure-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/><polyline points="9,12 11.2,14 15,10"/></svg>
            100% SECURE
        </div>

        <div class="network-provider-section">
            <div class="section-head">
                <label class="network-provider-label">Network provider</label>
                <span class="section-hint" id="operatorHint">Jio selected</span>
            </div>
            <div class="operator-grid">
                <?php foreach ($activeOperators as $idx => $ao): ?>
                <button class="operator-item<?php echo $idx === 0 ? ' selected' : ''; ?>" data-operator="<?php echo htmlspecialchars($ao['code']); ?>" onclick="HomeScreen.selectOperator('<?php echo htmlspecialchars($ao['code']); ?>')">
                    <?php if (!empty($ao['image'])): ?>
                    <img src="<?php echo htmlspecialchars($ao['image']); ?>" alt="<?php echo htmlspecialchars($ao['name']); ?>" class="operator-logo" style="width:40px;height:40px;border-radius:10px;object-fit:cover">
                    <?php else: ?>
                    <div class="operator-logo <?php echo htmlspecialchars($ao['code']); ?>" style="background:<?php echo htmlspecialchars($ao['color'] ?? '#6C2BFF'); ?>;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px"><?php echo strtoupper(substr($ao['name'], 0, 2)); ?></div>
                    <?php endif; ?>
                    <span class="operator-name"><?php echo htmlspecialchars($ao['name']); ?></span>
                    <span class="operator-sub">4G</span>
                </button>
                <?php endforeach; ?>
            </div>
                </button>
            </div>
        </div>

        <div class="mobile-input-section">
            <label class="mobile-input-label">Mobile number</label>
            <div class="mobile-input-wrapper" id="mobileWrapper">
                <div class="country-code"><span class="cc-flag"><svg viewBox="0 0 24 16" width="20" height="13"><rect width="24" height="16" rx="2" fill="#FF9933"/><rect y="5.33" width="24" height="5.33" fill="white"/><rect y="10.66" width="24" height="5.34" fill="#138808"/><circle cx="12" cy="8" r="2.2" fill="none" stroke="#000080" stroke-width="0.5"/><g stroke="#000080" stroke-width="0.3"><line x1="12" y1="5.8" x2="12" y2="10.2"/><line x1="9.8" y1="8" x2="14.2" y2="8"/><line x1="10.4" y1="6.4" x2="13.6" y2="9.6"/><line x1="13.6" y1="6.4" x2="10.4" y2="9.6"/></g></svg></span> +91</div>
                <input type="tel" class="mobile-input" id="mobileInput" placeholder="98765 43210" maxlength="10" inputmode="numeric" autocomplete="tel" />
                <span class="input-valid-icon" id="validIcon"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.6"><polyline points="20,6 9,17 4,12"/></svg></span>
                <button class="mobile-input-clear" id="clearBtn" aria-label="Clear number">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 9l6 6"/><path d="M15 9l-6 6"/></svg>
                </button>
                <button class="mobile-input-contact" aria-label="Contacts" onclick="HomeScreen.pickContact()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="3.5"/><path d="M20 8a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2 2 2 0 0 1 2 2Z"/><path d="M19 11v2"/><path d="M19 14h.01"/></svg>
                </button>
            </div>
            <p class="input-hint" id="inputHint">Enter 10-digit mobile number</p>
        </div>

        <div class="recharge-btn-wrapper">
            <button class="recharge-btn" id="rechargeBtn" disabled onclick="HomeScreen.proceed()">
                <span class="btn-shine"></span>
                <span class="btn-text">Recharge Now</span>
                <span class="btn-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg></span>
            </button>
            <p class="btn-subhint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/></svg> Protected by 256-bit encryption</p>
        </div>

        <div class="trust-footer">
            <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="trust-item <?php echo $i === 0 ? 'shield' : ($i === 1 ? 'sparkle' : 'check'); ?>">
                <?php if ($i === 0): ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="11" width="18" height="9" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <?php elseif ($i === 1): ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 2l1.8 5.2L19 9l-5.2 1.8L12 16l-1.8-5.2L5 9l5.2-1.8L12 2Z"/></svg>
                <?php else: ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14 9,11"/></svg><?php endif; ?>
                <?php echo htmlspecialchars($ti[$i]); ?>
            </div>
            <?php if ($i < 2): ?><span class="trust-dot"></span><?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Stats -->
    <div class="home-stats slide-up" style="animation-delay:0.22s">
        <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="stat"><strong><?php echo htmlspecialchars($st[$i]['value']); ?></strong><span><?php echo htmlspecialchars($st[$i]['label']); ?></span></div>
        <?php if ($i < 3): ?><span class="stat-sep"></span><?php endif; ?>
        <?php endfor; ?>
    </div>

    <!-- Reviews & Live Recharges — Footer Advanced Auto Slider -->
    <div class="home-footer-reviews slide-up" style="animation-delay:0.28s">
        <div class="section-head">
            <h3><span class="head-icon"><svg viewBox="0 0 24 24" fill="#FFB800"><path d="M12 2l2.4 4.9 5.4 0.8-3.9 3.8 0.9 5.4L12 14.6l-4.8 2.5 0.9-5.4L4.2 7.7l5.4-0.8L12 2Z"/></svg></span> Customer Reviews <span class="count-pill">48</span></h3>
            <span class="rating-pill">4.8 ★ <small>3,847</small></span>
        </div>

        <!-- Filter Pills -->
        <div class="review-filters" id="reviewFilters">
            <button class="review-filter active" data-filter="all">All</button>
            <button class="review-filter" data-filter="5">5 ★</button>
            <button class="review-filter" data-filter="4">4 ★</button>
            <button class="review-filter" data-filter="image">📸 With Photos</button>
            <button class="review-filter" data-filter="recent">Recent</button>
        </div>

        <div class="reviews-slider-wrap" id="reviewsWrap">
            <div class="reviews-track" id="reviewsTrack"></div>
        </div>
        <div class="reviews-progress"><i id="reviewsProgress"></i></div>
        <div class="reviews-dots" id="reviewsDots"></div>
    </div>

    <div class="home-live-recharges slide-up" style="animation-delay:0.32s">
        <div class="section-head">
            <h3><span class="head-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg></span> Live Recharges <span class="live-blink"></span></h3>
            <span class="count-pill green">30 just now</span>
        </div>
        <div class="recharges-slider-wrap" id="rechargesWrap">
            <div class="recharges-track" id="rechargesTrack"></div>
        </div>
        <div class="recharges-dots" id="rechargesDots"></div>
        <p class="recharge-note"><svg viewBox="0 0 24 24" fill="none" stroke="#5F259F" stroke-width="1.6"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/><path d="M9 12l2 2 4-4"/></svg> All transactions are 100% secure & instant</p>
    </div>

    <div class="home-footer-links">
        <p>© 2026 QuickRecharge • Powered by PhonePe UPI • <a href="#" onclick="App.showToast('Terms coming soon','info'); return false;">Terms</a> • <a href="#" onclick="App.showToast('Privacy coming soon','info'); return false;">Privacy</a></p>
    </div>

    <div class="home-bottom-safe"></div>
</div>

<script>
var HOME_TIMER_MIN=<?php echo (int)$h['offer_minutes']; ?>,HOME_TIMER_SEC=<?php echo (int)$h['offer_seconds']; ?>;
const HomeScreen = {
    selectedOperator: 'jio',
    init: function(data) {
        var self = this;
        this.selectedOperator = data && data.operator ? data.operator : 'jio';
        var skeleton = document.getElementById('homeSkeleton');
        var screen = document.getElementById('homeScreen');
        if (skeleton) {
            setTimeout(function() {
                skeleton.classList.add('hidden');
                if (screen) screen.style.opacity = '1';
            }, 350);
        }
        if (screen) screen.style.opacity = '0';
        this.setupInput();
        this.startTimer();
        this.markOperatorSelected(this.selectedOperator);
        this.validateForm();
        this.startProgress();
        this.initTopBanner();
        this.initPromoSlider();
        this.initPromoImageFull();
        this.initReviews();
        this.initRecharges();
    },
    setupInput: function() {
        const self = this;
        const input = document.getElementById('mobileInput');
        const clearBtn = document.getElementById('clearBtn');
        const wrapper = document.getElementById('mobileWrapper');
        if (!input) return;
        const onInput = function(){
            let val = input.value.replace(/[^0-9]/g,'').slice(0,10);
            input.value = val;
            if(clearBtn) clearBtn.classList.toggle('visible', val.length>0);
            if(wrapper) wrapper.classList.toggle('has-value', val.length>0);
            const validIcon = document.getElementById('validIcon');
            const isValid = /^[6-9]\d{9}$/.test(val);
            if(validIcon) validIcon.classList.toggle('show', isValid);
            const hint = document.getElementById('inputHint');
            if(hint){
                if(val.length===0) hint.textContent='Enter 10-digit mobile number';
                else if(val.length<10) hint.textContent= (10-val.length)+' more digits required';
                else if(!isValid) { hint.textContent='Invalid number — must start with 6-9'; hint.classList.add('err'); }
                else { hint.textContent='✓ Valid number'; hint.classList.remove('err'); hint.classList.add('ok'); }
                if(val.length!==10 || isValid) { if(val.length!==10) hint.classList.remove('ok'); }
                if(isValid) hint.classList.remove('err');
                else hint.classList.remove('ok');
            }
            self.validateForm();
        };
        input.addEventListener('input', onInput);
        input.addEventListener('keyup', onInput);
        input.addEventListener('focus', function(){ if(wrapper) wrapper.classList.add('focused'); });
        input.addEventListener('blur', function(){ if(wrapper) wrapper.classList.remove('focused'); });
        if(clearBtn){
            clearBtn.addEventListener('click', function(){
                input.value=''; onInput(); input.focus(); App.haptic('light');
            });
        }
        onInput();
    },
    pickContact: function(){ App.showToast('Contacts access coming soon','info'); App.haptic('light'); },
    markOperatorSelected: function(operator){
        document.querySelectorAll('.operator-item').forEach(function(item){
            item.classList.toggle('selected', item.dataset.operator===operator);
        });
        const hint = document.getElementById('operatorHint');
        const names = {jio:'Jio selected', airtel:'Airtel selected', vi:'Vi selected', bsnl:'BSNL selected'};
        if(hint) hint.textContent = names[operator] || operator;
    },
    selectOperator: function(operator){
        this.selectedOperator = operator;
        this.markOperatorSelected(operator);
        App.haptic('light');
        this.validateForm();
    },
    validateForm: function(){
        var input = document.getElementById('mobileInput');
        var btn = document.getElementById('rechargeBtn');
        if(!input || !btn) return;
        var number = input.value.replace(/[^0-9]/g,'');
        var isValid = /^[6-9]\d{9}$/.test(number) && this.selectedOperator;
        btn.disabled = !isValid;
        btn.classList.toggle('enabled', isValid);
        const hint = document.getElementById('inputHint');
        if(hint) hint.classList.toggle('ok', isValid);
    },
    proceed: function(){
        var input = document.getElementById('mobileInput');
        if(!input) return;
        var number = input.value.replace(/[^0-9]/g,'');
        if(!/^[6-9]\d{9}$/.test(number) || !this.selectedOperator){
            App.showToast('Enter valid 10-digit number','error'); App.haptic('error'); return;
        }
        App.haptic('medium');
        App.setState({ mobileNumber:number, selectedOperator:this.selectedOperator });
        // Persist server-side recharge session (authoritative)
        fetch('api/recharge-session.php',{method:'POST',headers:{'Content-Type':'application/json'},credentials:'include',body:JSON.stringify({action:'init',mobile:number,operator:this.selectedOperator})})
        .then(function(r){ return r.json(); })
        .then(function(j){
            if(!j.success){ App.showToast(j.error||'Invalid number','error'); return; }
            Router.navigate('verify', { mobile:number, operator:HomeScreen.selectedOperator });
        })
        .catch(function(){ Router.navigate('verify', { mobile:number, operator:HomeScreen.selectedOperator }); });
    },
    startTimer: function(){
        var totalSeconds = HOME_TIMER_MIN*60 + HOME_TIMER_SEC;
        var timerEl = document.getElementById('offerTimer');
        var update = function(){
            if(totalSeconds<=0) return;
            totalSeconds--;
            var m = Math.floor(totalSeconds/60);
            var s = totalSeconds % 60;
            if(timerEl) timerEl.textContent = String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
        };
        setInterval(update, 1000);
    },
    startProgress: function(){
        var bar = document.getElementById('offerProgress');
        if(!bar) return;
        var w = 58;
        setInterval(function(){
            w = w<=8 ? 58 : w-0.05;
            bar.style.width = w+'%';
        }, 100);
    },
    initReviews: function(){
        var track=document.getElementById('reviewsTrack');
        var dotsWrap=document.getElementById('reviewsDots');
        if(!track) return;

        var avatarColors = [
            'linear-gradient(135deg,#5F259F,#7B3FA0)',
            'linear-gradient(135deg,#0A3D91,#1E6DD1)',
            'linear-gradient(135deg,#ED1C24,#FF6B6B)',
            'linear-gradient(135deg,#0A8A4B,#22C55E)',
            'linear-gradient(135deg,#D97706,#F59E0B)',
            'linear-gradient(135deg,#7C3AED,#A78BFA)',
            'linear-gradient(135deg,#0891B2,#06B6D4)',
            'linear-gradient(135deg,#DC2626,#F87171)',
            'linear-gradient(135deg,#4338CA,#818CF8)',
            'linear-gradient(135deg,#C2410C,#FB923C)',
        ];

        var reviews=[
            {n:'Riya Sharma',c:'Mumbai',r:5,t:'Maine Jio ka 399 wala plan liya. Recharge 2 second mein ho gaya! PhonePe se payment kiya, bilkul smooth experience.',op:'Jio',plan:'₹399',days:2,img:false,helpful:47,tag:'Top Contributor'},
            {n:'Arjun Patel',c:'Delhi',r:5,t:'First time use kiya ye app. Airtel ka 249 ka plan choose kiya. Ekdam instant recharge! Ab daily use karta hu. Very reliable.',op:'Airtel',plan:'₹249',days:5,img:false,helpful:39,tag:'Regular User'},
            {n:'Sneha Reddy',c:'Hyderabad',r:5,t:'BSNL ka plan yahan mila jo official app pe nahi tha. ₹447 for 365 days. Amazing! Customer support bhi respond kiya within 1 hour.',op:'BSNL',plan:'₹447',days:1,img:false,helpful:52,tag:'Verified Buyer'},
            {n:'Karthik Menon',c:'Kochi',r:4,t:'Good app overall. Vi ka plan quickly mil gaya. Ek baar payment stuck hua but 10 minutes mein resolve ho gaya. Solid app.',op:'Vi',plan:'₹299',days:8,img:false,helpful:28,tag:'Long Time User'},
            {n:'Pooja Gupta',c:'Jaipur',r:5,t:'Mummy ka recharge karna tha. Yahan se 5 minute mein Jio 499 plan kar diya. Unko laga kisi ne call kiya tha! 😂 Best app.',op:'Jio',plan:'₹499',days:3,img:false,helpful:61,tag:'Family User'},
            {n:'Aditya Singh',c:'Pune',r:5,t:'Compare kara 5-6 apps se. Yahan pe sabse sasta mila Airtel 599 plan. ₹50 kam mein same plan. Ab bas yahi use karta hu.',op:'Airtel',plan:'₹599',days:6,img:false,helpful:34,tag:'Smart Saver'},
            {n:'Meera Nair',c:'Chennai',r:4,t:'Nice UI, very clean design. BSNL plan was easy to find. Only thing — wantedUPI auto-pay option. Otherwise perfect.',op:'BSNL',plan:'₹187',days:4,img:false,helpful:19,tag:'New User'},
            {n:'Vikram Joshi',c:'Ahmedabad',r:5,t:'365 days ka plan ek click mein! Mere 4 phone ka recharge isi se karta hu. Family pack bhi available hai. Really good.',op:'Jio',plan:'₹599',days:1,img:false,helpful:73,tag:'Power User'},
            {n:'Nandini Das',c:'Kolkata',r:5,t:'Fraud tha kya? Nahi! Bilkul genuine app hai. Pehle dar laga but pehla recharge successfully ho gaya. Trust worthy app.',op:'Airtel',plan:'₹349',days:12,img:false,helpful:45,tag:'First Recharge'},
            {n:'Rohan Malhotra',c:'Lucknow',r:5,t:'GPay se payment kiya, Jio 2GB plan instantly mil gaya. QR code scan karke payment karna bahut easy hai. Must try!',op:'Jio',plan:'₹249',days:7,img:false,helpful:29,tag:'UPI Lover'},
            {n:'Shreya Iyer',c:'Bengaluru',r:4,t:'Pretty good. Vi ke 4GB plan ke liye use kiya. Speedy recharge. App thoda slow hai kabi kabi but kaam ho jata hai.',op:'Vi',plan:'₹399',days:9,img:false,helpful:22,tag:'Techie'},
            {n:'Deepak Yadav',c:'Bhopal',r:5,t:'Monthly recharge ke liye best app. ₹199 Jio plan daily use karta hu. Payment PhonePe se hota hai 2 second mein. No issues ever.',op:'Jio',plan:'₹199',days:3,img:false,helpful:38,tag:'Daily User'},
            {n:'Ananya Roy',c:'Guwahati',r:5,t:'North East mein bhi kaam karta hai! BSNL ka plan dhundh rahi thi, mil gaya yahan. Thank you QuickRecharge! 🙏',op:'BSNL',plan:'₹299',days:5,img:false,helpful:56,tag:'Northeast User'},
            {n:'Kunal Bhatt',c:'Surat',r:4,t:'Business phone ke liye use karta hu. Har mahine 3-4 recharge karta hu yahan se. Bulk mein bhi discount mil jata hai kabi kabi.',op:'Airtel',plan:'₹449',days:2,img:false,helpful:17,tag:'Business User'},
            {n:'Priyanka Verma',c:'Indore',r:5,t:'PhonePe wallet se directly recharge ho gaya! No need to open PhonePe app separately. QuickRecharge integrated hai. Love it.',op:'Jio',plan:'₹599',days:4,img:false,helpful:41,tag:'Seamless'},
            {n:'Siddharth Rao',c:'Visakhapatnam',r:5,t:'540 days wala plan yahan pe mila! Kahi aur nahi mil raha tha. ₹699 bahut reasonable hai. App design bhi premium hai.',op:'Vi',plan:'₹699',days:6,img:false,helpful:44,tag:'Long Validity'},
            {n:'Tanvi Kulkarni',c:'Nashik',r:5,t:'Parents ke liye recharge kiya unko nahi aata tha app use. Ab main har month kar deta hu. Bahut helpful hai ye app.',op:'Jio',plan:'₹249',days:1,img:false,helpful:35,tag:'Helpful Son'},
            {n:'Harshit Agarwal',c:'Kanpur',r:4,t:'Airtel ke 799 plan ke liye use kiya. 730 days validity. Payment successful but confirmation thoda late aaya. Otherwise great.',op:'Airtel',plan:'₹799',days:10,img:false,helpful:21,tag:'Patient User'},
            {n:'Divya Pillai',c:'Thiruvananthapuram',r:5,t:'Kerala mein bhi perfect kaam karta hai! Vi ka ₹499 plan 365 days ke liye. Instant recharge, instant happiness!',op:'Vi',plan:'₹499',days:3,img:false,helpful:33,tag:'Happy Customer'},
            {n:'Nikhil Saxena',c:'Chandigarh',r:5,t:'Main gaming streamer hu. Har month ₹999 plan use karta hu unlimited data ke liye. QuickRecharge se ek click mein ho jata hai. No lag!',op:'Jio',plan:'₹999',days:8,img:false,helpful:58,tag:'Gamer'},
            {n:'Aishwarya Bose',c:'Patna',r:5,t:'Dusre app pe payment stuck ho gaya tha. Yahan try kiya, instant! Ab bas ye use karta hu. Trustworthy app hai. 100%.',op:'Airtel',plan:'₹349',days:2,img:false,helpful:49,tag:'Trust Builder'},
            {n:'Manoj Tiwari',c:'Prayagraj',r:4,t:'Simple and easy. No complicated steps. Mobile number dalo, plan select karo, pay karo. That\'s it. Love the simplicity.',op:'Jio',plan:'₹199',days:7,img:false,helpful:15,tag:'Minimalist'},
            {n:'Kavya Reddy',c:'Warangal',r:5,t:'Mere BSNL SIM ka yahan plan mila jo official BSNL app pe show nahi ho raha tha. QuickRecharge really has everything!',op:'BSNL',plan:'₹347',days:4,img:false,helpful:42,tag:'Explorer'},
            {n:'Rajesh Menon',c:'Mangaluru',r:5,t:'Udupi se hu, yahan tak delivery karta hai! I mean digital delivery obviously 😄 Best recharge experience in Karnataka.',op:'Jio',plan:'₹299',days:6,img:false,helpful:31,tag:'Local Hero'},
            {n:'Simran Kaur',c:'Amritsar',r:5,t:'Punjab mein Jio 4G coverage badhiya hai. QuickRecharge se turant recharge ho jata hai. Waheguru bless this app!',op:'Jio',plan:'₹449',days:1,img:false,helpful:67,tag:'Loyal User'},
        ];

        function renderStars(r){
            var s='';
            for(var i=1;i<=5;i++){
                s+='<span class="star'+(i>r?' empty':'')+'">★</span>';
            }
            return s;
        }

        var filterState='all';
        function getFiltered(){
            if(filterState==='all') return reviews;
            if(filterState==='5') return reviews.filter(function(r){return r.r===5;});
            if(filterState==='4') return reviews.filter(function(r){return r.r===4;});
            if(filterState==='recent') return reviews.slice().reverse();
            return reviews;
        }

        function renderCards(data){
            var html='';
            data.forEach(function(r,i){
                var ini=r.n.split(' ').map(function(s){return s[0];}).join('').slice(0,2).toUpperCase();
                var colorIdx=i%avatarColors.length;
                var verifiedSvg='<svg viewBox="0 0 24 24" fill="none" stroke="#188038" stroke-width="2" width="11" height="11"><path d="M5 13l4 4 10-10"/></svg>';
                var timeText=r.days===0?'Just now':r.days===1?'1 day ago':r.days+' days ago';
                html+='<div class="review-card">';
                html+='<div class="review-card-inner">';
                html+='<div class="review-head">';
                html+='<div class="review-avatar" style="background:'+avatarColors[colorIdx]+'">'+ini+'</div>';
                html+='<div class="review-meta">';
                html+='<strong>'+r.n+'</strong>';
                html+='<span>'+r.c+'<span class="city-dot"></span>'+r.op+' User</span>';
                html+='</div>';
                html+='<div class="review-stars">'+renderStars(r.r)+'</div>';
                html+='</div>';
                html+='<div class="review-text">'+r.t+'</div>';
                html+='<div class="review-op-badge"><span class="op-dot" style="background:'+(r.op==='Jio'?'#0A3D91':r.op==='Airtel'?'#ED1C24':r.op==='Vi'?'#E60000':'#0A8A4B')+'"></span>'+r.op+' · '+r.plan+'</div>';
                html+='<div class="review-foot">';
                html+='<span class="review-date"><svg viewBox="0 0 24 24" fill="none" stroke="#9AA0A6" stroke-width="1.8" width="11" height="11"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg> '+timeText+'</span>';
                html+='<span class="review-verified">'+verifiedSvg+' Verified</span>';
                html+='</div>';
                html+='</div>';
                if(r.tag) html+='<div class="review-badge-tag">'+r.tag+'</div>';
                html+='</div>';
            });
            return html;
        }

        track.innerHTML=renderCards(getFiltered());

        var progress=document.getElementById('reviewsProgress');
        if(dotsWrap){
            dotsWrap.innerHTML='';
            var totalCards=getFiltered().length;
            var numDots=Math.min(6, Math.ceil(totalCards/2));
            for(var i=0;i<numDots;i++){ var d=document.createElement('span'); if(i===0) d.className='active'; (function(ii){ d.addEventListener('click', function(){ go(ii*2); reset(); }); })(i); dotsWrap.appendChild(d); }
        }

        var idx=0, cardW=272;
        function updateCardW(){ var c=track.querySelector('.review-card'); if(c && c.offsetWidth>10) cardW=c.offsetWidth+12; else cardW=272; }
        updateCardW();
        setTimeout(updateCardW, 400);
        setTimeout(updateCardW, 900);
        window.addEventListener('resize', updateCardW);

        function go(i){
            updateCardW();
            var total=getFiltered().length;
            idx=i; if(idx>=total) idx=0; if(idx<0) idx=total-1;
            track.style.transform='translateX(-'+(idx*cardW)+'px)';
            var ds=dotsWrap?dotsWrap.querySelectorAll('span'):[];
            var pg=Math.floor(idx/2); var numDots=Math.min(6, ds.length);
            ds.forEach(function(d,j){ d.classList.toggle('active', j===pg%numDots); });
            if(progress) progress.style.width=((idx+1)/total*100)+'%';
        }
        function reset(){ clearInterval(timer); timer=setInterval(auto, 3000); }
        function auto(){
            updateCardW();
            var total=getFiltered().length;
            idx=(idx+1)%total;
            if(cardW>10 && idx*cardW > track.scrollWidth - track.parentElement.offsetWidth - 8) idx=0;
            go(idx);
        }
        var timer=setInterval(auto, 3000);
        setTimeout(function(){ updateCardW(); go(0); }, 350);

        var wrap=document.getElementById('reviewsWrap');
        if(wrap){
            var startX=0;
            wrap.addEventListener('touchstart', function(e){ startX=e.touches[0].clientX; clearInterval(timer); }, {passive:true});
            wrap.addEventListener('touchend', function(e){
                var diff=startX - e.changedTouches[0].clientX;
                if(Math.abs(diff)>40){ if(diff>0) go(idx+1); else go(idx-1); }
                reset();
            }, {passive:true});
            wrap.addEventListener('mouseenter', function(){ clearInterval(timer); });
            wrap.addEventListener('mouseleave', function(){ timer=setInterval(auto, 3000); });
        }

        document.querySelectorAll('.review-filter').forEach(function(btn){
            btn.addEventListener('click', function(){
                document.querySelectorAll('.review-filter').forEach(function(b){b.classList.remove('active');});
                btn.classList.add('active');
                filterState=btn.dataset.filter;
                idx=0;
                track.innerHTML=renderCards(getFiltered());
                updateCardW();
                go(0);
                if(dotsWrap){
                    dotsWrap.innerHTML='';
                    var totalCards=getFiltered().length;
                    var numDots=Math.min(6, Math.ceil(totalCards/2));
                    for(var i=0;i<numDots;i++){ var d=document.createElement('span'); if(i===0) d.className='active'; (function(ii){ d.addEventListener('click', function(){ go(ii*2); reset(); }); })(i); dotsWrap.appendChild(d); }
                }
                App.haptic('light');
            });
        });
    },
    initRecharges: function(){
        var track=document.getElementById('rechargesTrack');
        var wrap=document.getElementById('rechargesWrap');
        var dotsWrap=document.getElementById('rechargesDots');
        if(!track) return;
        var ops=[{c:'Jio',bg:'linear-gradient(135deg,#0A3D91,#1E6DD1)'},{c:'Airtel',bg:'linear-gradient(135deg,#ED1C24,#FF3B30)'},{c:'Vi',bg:'#E60000'},{c:'BSNL',bg:'#0A8A4B'}];
        var plans=['3GB/day • 365 Days','2GB/day • 84 Days','1.5GB/day • 56 Days','2GB/day • 200 Days','4GB/day • 365 Days'];
        var data=[];
        for(var i=0;i<30;i++){
            var op=ops[i%4];
            var num='98'+String(10000000+Math.floor(Math.random()*89999999)).slice(0,8);
            var masked=num.slice(0,2)+'XXXXX'+num.slice(7);
            var amt=[199,249,299,399,449,499,599,899,999][i%9];
            var mins=Math.floor(Math.random()*58)+1;
            var plan=plans[i%5];
            data.push({op:op, num:masked, amt:amt, time:mins+' min ago', plan:plan});
        }
        var html='';
        data.forEach(function(r){
            html+='<div class="recharge-card-live"><div class="op-logo-live" style="background:'+r.op.bg+'">'+r.op.c[0]+'</div><div class="rch-main"><div class="rch-num">+91 '+r.num+'</div><div class="rch-op"><span class="dot-live"></span>'+r.op.c+' • '+r.plan+'</div></div><div class="rch-right"><div class="rch-amt">₹'+r.amt+'</div><div class="rch-time">'+r.time+'</div><span class="rch-badge"><svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.2" width="10" height="10"><path d="M5 13l4 4 10-10"/></svg> Success</span></div></div>';
        });
        track.innerHTML=html;
        if(dotsWrap){
            dotsWrap.innerHTML='';
            var pages=Math.ceil(data.length/1);
            for(var i=0;i<6;i++){ var d=document.createElement('span'); if(i===0) d.className='active'; dotsWrap.appendChild(d); }
        }
        var idx=0, cardW=272;
        function updW(){ var c=track.querySelector('.recharge-card-live'); if(c && c.offsetWidth>10) cardW=c.offsetWidth+10; else cardW=272; }
        updW();
        setTimeout(updW, 400);
        setTimeout(updW, 900);
        window.addEventListener('resize', updW);
        function go(i){ updW(); idx=i; if(idx>=data.length) idx=0; if(idx<0) idx=data.length-1; track.style.transform='translateX(-'+(idx*cardW)+'px)'; var ds=dotsWrap.querySelectorAll('span'); ds.forEach(function(d,j){ d.classList.toggle('active', j%6===idx%6); }); }
        setTimeout(function(){ updW(); go(0); }, 360);
        var timer=setInterval(function(){ updW(); idx=(idx+1)%data.length; if(cardW>10 && idx*cardW > track.scrollWidth - wrap.offsetWidth) idx=0; go(idx); }, 2200);
        if(wrap){
            var startX=0, dragging=false, paused=false;
            wrap.addEventListener('touchstart', function(e){ startX=e.touches[0].clientX; dragging=true; clearInterval(timer); }, {passive:true});
            wrap.addEventListener('touchend', function(e){
                if(!dragging) return; dragging=false;
                var diff=startX - e.changedTouches[0].clientX;
                if(Math.abs(diff)>40){
                    if(diff>0 && idx<data.length-1) idx++; else if(diff<0 && idx>0) idx--;
                    go(idx);
                }
                clearInterval(timer); timer=setInterval(function(){ updW(); idx=(idx+1)%data.length; if(cardW>10 && idx*cardW > track.scrollWidth - wrap.offsetWidth) idx=0; go(idx); }, 2200);
            }, {passive:true});
            wrap.addEventListener('mouseenter', function(){ clearInterval(timer); });
            wrap.addEventListener('mouseleave', function(){ clearInterval(timer); timer=setInterval(function(){ updW(); idx=(idx+1)%data.length; if(cardW>10 && idx*cardW > track.scrollWidth - wrap.offsetWidth) idx=0; go(idx); }, 2200); });
            dotsWrap.querySelectorAll('span').forEach(function(d,i){
                d.addEventListener('click', function(){ idx=i; go(idx); });
            });
        }
    },
    initTopBanner: function(){
        var carousel = document.getElementById('topBannerCarousel');
        var track = document.getElementById('topBannerTrack');
        var dotsWrap = document.getElementById('topBannerDots');
        if(!carousel || !track || !dotsWrap) return;
        var slides = track.querySelectorAll('.top-banner-slide');
        var dots = dotsWrap.querySelectorAll('.top-banner-dot');
        var total = slides.length;
        if(total <= 1) return;
        var current = 0;
        var paused = false;

        function goTo(idx){
            current = idx;
            track.style.transform = 'translateX(-'+(current*100)+'%)';
            dots.forEach(function(d,i){ d.classList.toggle('active', i===current); });
        }
        function autoSlide(){
            if(paused) return;
            current = (current+1) % total;
            goTo(current);
        }

        dots.forEach(function(d){
            d.addEventListener('click', function(){
                goTo(parseInt(this.dataset.idx));
            });
        });

        setInterval(autoSlide, 3500);

        var startX = 0, dragging = false;
        carousel.addEventListener('touchstart', function(e){ startX=e.touches[0].clientX; dragging=true; paused=true; }, {passive:true});
        carousel.addEventListener('touchend', function(e){
            if(!dragging) return;
            dragging=false;
            var diff = startX - e.changedTouches[0].clientX;
            if(Math.abs(diff)>40){
                if(diff>0 && current<total-1) current++;
                else if(diff<0 && current>0) current--;
                goTo(current);
            }
            paused=false;
        }, {passive:true});

        carousel.addEventListener('mouseenter', function(){ paused=true; });
        carousel.addEventListener('mouseleave', function(){ paused=false; });
    },
    initPromoSlider: function(){
        var slider = document.getElementById('promoSlider');
        var dotsWrap = document.getElementById('promoDots');
        if(!slider || !dotsWrap) return;
        var cards = slider.querySelectorAll('.promo-plan-card');
        if(cards.length <= 2) return;
        var visibleCount = 2;
        var totalSlides = cards.length - visibleCount + 1;
        var current = 0;
        var paused = false;

        function renderDots(){
            dotsWrap.innerHTML = '';
            for(var i=0;i<totalSlides;i++){
                var dot = document.createElement('div');
                dot.className = 'promo-dot' + (i===current?' active':'');
                dot.dataset.idx = i;
                dot.addEventListener('click', function(){
                    current = parseInt(this.dataset.idx);
                    slideTo(current);
                });
                dotsWrap.appendChild(dot);
            }
        }
        function slideTo(idx){
            current = idx;
            var cardWidth = cards[0].offsetWidth + 8;
            slider.style.transform = 'translateX(-' + (current*cardWidth) + 'px)';
            var dots = dotsWrap.querySelectorAll('.promo-dot');
            dots.forEach(function(d,i){ d.classList.toggle('active', i===current); });
        }
        function autoSlide(){
            if(paused) return;
            current = (current+1) % totalSlides;
            slideTo(current);
        }
        renderDots();
        setInterval(autoSlide, 3000);

        var startX = 0, isDragging = false;
        slider.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; isDragging=true; paused=true; }, {passive:true});
        slider.addEventListener('touchend', function(e){
            if(!isDragging) return;
            isDragging=false;
            var diff = startX - e.changedTouches[0].clientX;
            if(Math.abs(diff)>40){
                if(diff>0 && current<totalSlides-1) current++;
                else if(diff<0 && current>0) current--;
                slideTo(current);
            }
            paused=false;
        }, {passive:true});

        var promoSection = slider.closest('.promo-banner');
        if(promoSection){
            promoSection.addEventListener('mouseenter', function(){ paused=true; });
            promoSection.addEventListener('mouseleave', function(){ paused=false; });
        }
    },
    initPromoImageFull: function(){
        var track=document.getElementById('promoImgFullTrack');
        var dotsWrap=document.getElementById('promoImgFullDots');
        var wrap=document.getElementById('promoImageFullWrap');
        if(!track || !dotsWrap) return;
        var slides=track.querySelectorAll('.promo-image-slide');
        var total=slides.length;
        if(total===0) return;
        dotsWrap.innerHTML='';
        for(var i=0;i<total;i++){ var d=document.createElement('span'); if(i===0) d.className='active'; (function(ii){ d.addEventListener('click', function(){ go(ii); reset(); }); })(i); dotsWrap.appendChild(d); }
        var idx=0, timer=null;
        function go(i){ idx=(i+total)%total; track.style.transform='translateX(-'+(idx*100)+'%)'; var ds=dotsWrap.querySelectorAll('span'); ds.forEach(function(d,j){ d.classList.toggle('active', j===idx); }); }
        function next(){ go(idx+1); }
        function reset(){ clearInterval(timer); timer=setInterval(next, 3000); }
        timer=setInterval(next, 3000);
        go(0);
        if(wrap){
            var sx=0;
            wrap.addEventListener('touchstart', function(e){ sx=e.touches[0].clientX; clearInterval(timer); }, {passive:true});
            wrap.addEventListener('touchend', function(e){ var diff=sx - e.changedTouches[0].clientX; if(Math.abs(diff)>40){ if(diff>0) next(); else go(idx-1); } reset(); }, {passive:true});
            wrap.addEventListener('mouseenter', function(){ clearInterval(timer); });
            wrap.addEventListener('mouseleave', function(){ reset(); });
        }
    },
    initImageSlider: function(){
        var track=document.getElementById('imgTrack');
        var dotsWrap=document.getElementById('imgDots');
        var progress=document.getElementById('imgProgress');
        var wrap=document.getElementById('homeImageSlider');
        if(!track || !dotsWrap) return;
        var slides=track.querySelectorAll('.img-slide');
        var total=slides.length;
        if(total===0) return;
        dotsWrap.innerHTML='';
        for(var i=0;i<total;i++){ var d=document.createElement('span'); if(i===0) d.className='active'; (function(ii){ d.addEventListener('click', function(){ go(ii); reset(); }); })(i); dotsWrap.appendChild(d); }
        var idx=0, timer=null, progTimer=null;
        function go(i){
            idx=(i+total)%total;
            track.style.transform='translateX(-'+(idx*100)+'%)';
            var ds=dotsWrap.querySelectorAll('span'); ds.forEach(function(d,j){ d.classList.toggle('active', j===idx); });
            if(progress){ progress.style.width='0%'; progress.style.transition='none'; void progress.offsetWidth; progress.style.transition='width 3s linear'; progress.style.width='100%'; }
        }
        function next(){ go(idx+1); }
        function reset(){ clearInterval(timer); clearInterval(progTimer); if(progress) progress.style.width='0%'; timer=setInterval(next, 3200); }
        timer=setInterval(next, 3200);
        go(0);
        var startX=0;
        if(wrap){
            wrap.addEventListener('touchstart', function(e){ startX=e.touches[0].clientX; clearInterval(timer); }, {passive:true});
            wrap.addEventListener('touchend', function(e){
                var diff=startX - e.changedTouches[0].clientX;
                if(Math.abs(diff)>40){ if(diff>0) next(); else go(idx-1); }
                reset();
            }, {passive:true});
            wrap.addEventListener('mouseenter', function(){ clearInterval(timer); });
            wrap.addEventListener('mouseleave', function(){ reset(); });
        }
    }
};
</script>
