<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $existing = $store->getHomeContent();

    $home = [
        'app_name' => $_POST['app_name'] ?? 'QuickRecharge',
        'app_tagline' => $_POST['app_tagline'] ?? 'Fast • Secure • Instant',
        'offer_title' => $_POST['offer_title'] ?? 'Special Offer Ends In',
        'offer_minutes' => (int)($_POST['offer_minutes'] ?? 9),
        'offer_seconds' => (int)($_POST['offer_seconds'] ?? 48),
        'promo_brand' => $_POST['promo_brand'] ?? 'PhonePe',
        'promo_title' => $_POST['promo_title'] ?? 'BEST SAVINGS OFFERS',
        'promo_subtitle' => $_POST['promo_subtitle'] ?? '',
        'promo_hot_deal' => $_POST['promo_hot_deal'] ?? '',
        'promo_plans' => [],
        'stats' => [],
        'trust_items' => [],
        'top_banner_slides' => $existing['top_banner_slides'] ?? [],
    ];

    for ($i = 0; $i < count($_POST['promo_price'] ?? []); $i++) {
        $home['promo_plans'][] = [
            'price' => (float)($_POST['promo_price'][$i] ?? 0),
            'data' => $_POST['promo_data'][$i] ?? '',
            'validity' => $_POST['promo_validity'][$i] ?? '',
            'calls' => $_POST['promo_calls'][$i] ?? 'Unlimited',
            'featured' => isset($_POST['promo_featured'][$i]),
            'ribbon' => $_POST['promo_ribbon'][$i] ?? '',
        ];
    }

    for ($i = 0; $i < 4; $i++) {
        $home['stats'][] = [
            'value' => $_POST['stat_value'][$i] ?? '',
            'label' => $_POST['stat_label'][$i] ?? '',
        ];
    }

    for ($i = 0; $i < 3; $i++) {
        $home['trust_items'][] = $_POST['trust_item'][$i] ?? '';
    }

    $store->saveHomeContent($home);
    header('Location: ?page=home-content&saved=1');
    exit;
}

if (isset($_GET['add_slide'])) {
    $slides = $store->getTopBannerSlides();
    $maxId = 0;
    foreach ($slides as $s) { if (($s['id'] ?? 0) > $maxId) $maxId = $s['id']; }
    $slides[] = [
        'id' => $maxId + 1,
        'title' => $_POST['slide_title'] ?? 'New Slide',
        'subtitle' => $_POST['slide_subtitle'] ?? '',
        'bg_color' => $_POST['slide_bg_color'] ?? '#5F259F',
        'accent_color' => $_POST['slide_accent_color'] ?? '#FFD54F',
        'icon' => $_POST['slide_icon'] ?? '🎁',
        'active' => true,
        'order' => count($slides) + 1,
    ];
    $store->saveTopBannerSlides($slides);
    header('Location: ?page=home-content&slide_added=1');
    exit;
}

if (isset($_GET['delete_slide'])) {
    $delId = (int)$_GET['delete_slide'];
    $slides = $store->getTopBannerSlides();
    $slides = array_values(array_filter($slides, fn($s) => $s['id'] != $delId));
    $store->saveTopBannerSlides($slides);
    header('Location: ?page=home-content&slide_deleted=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['save_slides'])) {
    $slides = $store->getTopBannerSlides();
    foreach ($slides as &$s) {
        foreach ($slides as $check) {
            if ($check['id'] == $s['id']) {
                $s['title'] = $_POST['slide_title'][$s['id']] ?? $s['title'];
                $s['subtitle'] = $_POST['slide_subtitle'][$s['id']] ?? $s['subtitle'];
                $s['bg_color'] = $_POST['slide_bg_color'][$s['id']] ?? $s['bg_color'];
                $s['accent_color'] = $_POST['slide_accent_color'][$s['id']] ?? $s['accent_color'];
                $s['icon'] = $_POST['slide_icon'][$s['id']] ?? $s['icon'];
                $s['active'] = isset($_POST['slide_active'][$s['id']]);
                $s['order'] = (int)($_POST['slide_order'][$s['id']] ?? $s['order']);
                break;
            }
        }
    }
    unset($s);
    $store->saveTopBannerSlides($slides);
    header('Location: ?page=home-content&slides_saved=1');
    exit;
}

$home = $store->getHomeContent();
$topSlides = $store->getTopBannerSlides();
?>

<div class="topbar">
<div>
<h1>Home Page Content</h1>
<div class="breadcrumb">Edit your app's home page — changes appear instantly</div>
</div>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="toast toast-success" id="saveToast">Home content saved successfully!</div>
<script>setTimeout(function(){var t=document.getElementById('saveToast');if(t)t.remove()},3000)</script>
<?php endif; ?>

<form method="POST">

<!-- Header Section -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#6C2BFF">Header</h3>
<div class="form-row">
<div>
<label class="form-label">App Name</label>
<input type="text" name="app_name" value="<?php echo htmlspecialchars($home['app_name'] ?? ''); ?>">
</div>
<div>
<label class="form-label">Tagline</label>
<input type="text" name="app_tagline" value="<?php echo htmlspecialchars($home['app_tagline'] ?? ''); ?>">
</div>
</div>
</div>

<!-- Top Banner Slides -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#ED1C24">Top Banner Carousel</h3>
<p style="font-size:12px;color:#666;margin-bottom:14px">Auto-sliding promotional banners shown below the header. Max 6 slides.</p>

<?php if (isset($_GET['slide_added'])): ?><div style="background:#DCFCE7;color:#166534;padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:12px">Slide added!</div><?php endif; ?>
<?php if (isset($_GET['slide_deleted'])): ?><div style="background:#FEF3C7;color:#92400E;padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:12px">Slide deleted.</div><?php endif; ?>
<?php if (isset($_GET['slides_saved'])): ?><div style="background:#DCFCE7;color:#166534;padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:12px">Slides saved!</div><?php endif; ?>

<?php if (!empty($topSlides)): ?>
<form method="POST" action="?page=home-content&save_slides" style="margin-bottom:16px">
<?php foreach ($topSlides as $s): ?>
<div style="background:#F8F9FA;border-radius:10px;padding:14px;margin-bottom:10px;border:1px solid #E5E7EB">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
<strong style="font-size:13px">Slide #<?php echo $s['id']; ?> — <?php echo htmlspecialchars($s['icon'] ?? ''); ?> <?php echo htmlspecialchars($s['title']); ?></strong>
<div style="display:flex;align-items:center;gap:8px">
<label style="font-size:11px;display:flex;align-items:center;gap:4px"><input type="checkbox" name="slide_active[<?php echo $s['id']; ?>]" value="1" <?php echo !empty($s['active']) ? 'checked' : ''; ?>> Active</label>
<a href="?page=home-content&delete_slide=<?php echo $s['id']; ?>" style="color:#EF4444;font-size:11px;text-decoration:none" onclick="return confirm('Delete this slide?')">Delete</a>
</div>
</div>
<div class="form-row">
<div><label class="form-label">Title</label><input type="text" name="slide_title[<?php echo $s['id']; ?>]" value="<?php echo htmlspecialchars($s['title']); ?>"></div>
<div><label class="form-label">Subtitle</label><input type="text" name="slide_subtitle[<?php echo $s['id']; ?>]" value="<?php echo htmlspecialchars($s['subtitle']); ?>"></div>
</div>
<div class="form-row">
<div><label class="form-label">Icon (emoji)</label><input type="text" name="slide_icon[<?php echo $s['id']; ?>]" value="<?php echo htmlspecialchars($s['icon']); ?>" placeholder="e.g. 💰"></div>
<div><label class="form-label">Sort Order</label><input type="number" name="slide_order[<?php echo $s['id']; ?>]" value="<?php echo $s['order']; ?>" min="1" max="10"></div>
</div>
<div class="form-row">
<div><label class="form-label">BG Color</label><input type="color" name="slide_bg_color[<?php echo $s['id']; ?>]" value="<?php echo htmlspecialchars($s['bg_color']); ?>" style="height:38px"></div>
<div><label class="form-label">Accent Color</label><input type="color" name="slide_accent_color[<?php echo $s['id']; ?>]" value="<?php echo htmlspecialchars($s['accent_color']); ?>" style="height:38px"></div>
</div>
</div>
<?php endforeach; ?>
<button type="submit" class="btn btn-primary" style="margin-bottom:10px">Save Slides</button>
</form>
<?php else: ?>
<p style="font-size:12px;color:#999;margin-bottom:12px">No slides yet. Add one below.</p>
<?php endif; ?>

<?php if (count($topSlides) < 6): ?>
<form method="POST" action="?page=home-content&add_slide" style="background:#EFF6FF;border-radius:10px;padding:14px;border:1px dashed #93C5FD">
<h4 style="font-size:13px;margin-bottom:10px;color:#2563EB">Add New Slide</h4>
<div class="form-row">
<div><label class="form-label">Title</label><input type="text" name="slide_title" placeholder="e.g. Cashback Offer" required></div>
<div><label class="form-label">Subtitle</label><input type="text" name="slide_subtitle" placeholder="e.g. Get 10% cashback"></div>
</div>
<div class="form-row">
<div><label class="form-label">Icon</label><input type="text" name="slide_icon" placeholder="e.g. 🔥" value="🎁"></div>
<div></div>
</div>
<div class="form-row">
<div><label class="form-label">BG Color</label><input type="color" name="slide_bg_color" value="#5F259F" style="height:38px"></div>
<div><label class="form-label">Accent Color</label><input type="color" name="slide_accent_color" value="#FFD54F" style="height:38px"></div>
</div>
<button type="submit" class="btn btn-primary" style="margin-top:8px;padding:8px 20px;font-size:13px">Add Slide</button>
</form>
<?php endif; ?>
</div>

<!-- Offer Timer Section -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#F59E0B">Offer Timer</h3>
<div class="form-row">
<div class="form-full">
<label class="form-label">Timer Title</label>
<input type="text" name="offer_title" value="<?php echo htmlspecialchars($home['offer_title'] ?? ''); ?>">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Minutes</label>
<input type="number" name="offer_minutes" min="0" max="999" value="<?php echo $home['offer_minutes'] ?? 9; ?>">
</div>
<div>
<label class="form-label">Seconds</label>
<input type="number" name="offer_seconds" min="0" max="59" value="<?php echo $home['offer_seconds'] ?? 48; ?>">
</div>
</div>
</div>

<!-- Promo Banner Section -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#6C2BFF">Promo Banner</h3>
<div class="form-row">
<div>
<label class="form-label">Brand Name</label>
<input type="text" name="promo_brand" value="<?php echo htmlspecialchars($home['promo_brand'] ?? ''); ?>">
</div>
<div>
<label class="form-label">Badge Title</label>
<input type="text" name="promo_title" value="<?php echo htmlspecialchars($home['promo_title'] ?? ''); ?>">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Subtitle</label>
<input type="text" name="promo_subtitle" value="<?php echo htmlspecialchars($home['promo_subtitle'] ?? ''); ?>">
</div>
<div>
<label class="form-label">Hot Deal Text</label>
<input type="text" name="promo_hot_deal" value="<?php echo htmlspecialchars($home['promo_hot_deal'] ?? ''); ?>">
</div>
</div>

<h4 style="font-size:13px;margin:16px 0 12px;color:#333">Promo Plans (3 cards)</h4>
<?php for ($i = 0; $i < 3; $i++): ?>
<?php $pp = $home['promo_plans'][$i] ?? ['price'=>'','data'=>'','validity'=>'','calls'=>'Unlimited','featured'=>false,'ribbon'=>'']; ?>
<div style="background:#F8F9FA;border-radius:10px;padding:16px;margin-bottom:12px">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
<strong style="font-size:13px">Card <?php echo $i + 1; ?></strong>
<label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer">
<input type="checkbox" name="promo_featured[<?php echo $i; ?>]" value="1" <?php echo !empty($pp['featured']) ? 'checked' : ''; ?>>
Featured (highlighted)
</label>
</div>
<div class="form-row">
<div>
<label class="form-label">Price (₹)</label>
<input type="number" name="promo_price[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($pp['price']); ?>">
</div>
<div>
<label class="form-label">Ribbon Text</label>
<input type="text" name="promo_ribbon[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($pp['ribbon']); ?>" placeholder="e.g. MOST PICKED">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Data</label>
<input type="text" name="promo_data[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($pp['data']); ?>" placeholder="e.g. 2GB/DAY">
</div>
<div>
<label class="form-label">Validity</label>
<input type="text" name="promo_validity[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($pp['validity']); ?>" placeholder="e.g. 84 DAYS">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Calls</label>
<input type="text" name="promo_calls[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($pp['calls']); ?>">
</div>
<div></div>
</div>
</div>
<?php endfor; ?>
</div>

<!-- Stats Section -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#16A34A">Stats Bar</h3>
<?php for ($i = 0; $i < 4; $i++): ?>
<?php $s = $home['stats'][$i] ?? ['value'=>'','label'=>'']; ?>
<div class="form-row" style="margin-bottom:12px">
<div>
<label class="form-label">Stat <?php echo $i + 1; ?> — Value</label>
<input type="text" name="stat_value[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($s['value']); ?>" placeholder="e.g. 4.8★">
</div>
<div>
<label class="form-label">Stat <?php echo $i + 1; ?> — Label</label>
<input type="text" name="stat_label[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($s['label']); ?>" placeholder="e.g. App rating">
</div>
</div>
<?php endfor; ?>
</div>

<!-- Trust Footer -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#6C2BFF">Trust Footer</h3>
<?php for ($i = 0; $i < 3; $i++): ?>
<div style="margin-bottom:12px">
<label class="form-label">Item <?php echo $i + 1; ?></label>
<input type="text" name="trust_item[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($home['trust_items'][$i] ?? ''); ?>" placeholder="e.g. Protected">
</div>
<?php endfor; ?>
</div>

<!-- Save Button -->
<div style="display:flex;gap:10px;padding:8px 0 20px">
<button type="submit" class="btn btn-primary" style="padding:12px 32px;font-size:15px">Save All Changes</button>
<a href="?page=dashboard" class="btn btn-outline" style="padding:12px 24px">Cancel</a>
</div>

</form>
