<?php
$uploadDir = dirname(__DIR__, 2) . '/uploads/upi';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
$saveError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vpaCheck = trim($_POST['upi_id_text'] ?? '');
    if (!preg_match('/^[\w.\-]{2,256}@[a-zA-Z]{2,64}$/', $vpaCheck)) {
        $saveError = 'Invalid UPI ID "' . $vpaCheck . '". Enter your REAL UPI ID from your UPI app (e.g. 9876543210@paytm, yourname@okhdfc, yourname@ybl). Placeholder IDs will be rejected by UPI apps at scan time.';
    } else {
    $checkout = [
        'hero_title' => $_POST['hero_title'] ?? 'Secure Checkout',
        'hero_sub' => $_POST['hero_sub'] ?? 'UPI • Verified • Fast',
        'upi_id' => $_POST['upi_id_text'] ?? 'merchant@upi',
        'merchant_name' => $_POST['merchant_name'] ?? 'QuickRecharge',
        'merchant_display_name' => $_POST['merchant_display_name'] ?? 'QuickRecharge Store',
        'currency' => $_POST['currency'] ?? 'INR',
        'description' => $_POST['description'] ?? 'Mobile Recharge',
        'qr_image' => $_POST['existing_qr'] ?? '',
        'upi_apps' => [],
        'security_text' => $_POST['security_text'] ?? '',
        'security_sub' => $_POST['security_sub'] ?? '',
        'summary_title' => $_POST['summary_title'] ?? 'Recharge Summary',
        'summary_sub' => $_POST['summary_sub'] ?? '',
        'trust_items' => [],
        'qr_expiry_minutes' => max(1, min(30, (int)($_POST['qr_expiry_minutes'] ?? 5))),
    ];

    if (!empty($_FILES['qr_image']['name'])) {
        $file = $_FILES['qr_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (in_array($ext, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
            $filename = 'qr_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                $checkout['qr_image'] = 'uploads/upi/' . $filename;
            }
        }
    }

    if (!empty($_POST['remove_qr'])) {
        $checkout['qr_image'] = '';
    }

    for ($i = 0; $i < 4; $i++) {
        $logoPath = $_POST['existing_logo'][$i] ?? '';
        if (!empty($_FILES['upi_logo']['name'][$i])) {
            $file = $_FILES['upi_logo'];
            if ($file['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'][$i], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp','svg'];
                if (in_array($ext, $allowed) && $file['size'][$i] <= 5*1024*1024) {
                    $filename = 'logo_' . ($i+1) . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'][$i], $uploadDir . '/' . $filename)) {
                        $logoPath = 'uploads/upi/' . $filename;
                    }
                }
            }
        }
        if (!empty($_POST['remove_logo'][$i])) {
            $logoPath = '';
        }
        $checkout['upi_apps'][] = [
            'id' => $_POST['upi_id'][$i] ?? '',
            'name' => $_POST['upi_name'][$i] ?? '',
            'desc' => $_POST['upi_desc'][$i] ?? '',
            'badge' => $_POST['upi_badge'][$i] ?? '',
            'color' => $_POST['upi_color'][$i] ?? '#333333',
            'logo' => $logoPath,
            'active' => isset($_POST['upi_active'][$i]),
        ];
    }
    for ($i = 0; $i < 3; $i++) {
        $checkout['trust_items'][] = $_POST['trust_item'][$i] ?? '';
    }
    $store->saveCheckout($checkout);
    header('Location: ?page=payment-settings&saved=1');
    exit;
    } // end VPA-valid save block
}

$ck = $store->getCheckout();
$apps = $ck['upi_apps'] ?? [];
while (count($apps) < 4) $apps[] = ['id'=>'','name'=>'','desc'=>'','badge'=>'','color'=>'#333333','active'=>true];
$ti = $ck['trust_items'] ?? [];
while (count($ti) < 3) $ti[] = '';
?>

<div class="topbar">
<div>
<h1>Payment Settings</h1>
<div class="breadcrumb">Configure UPI payment — apps, merchant details, QR code</div>
</div>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="toast toast-success" id="saveToast">Payment settings saved!</div>
<script>setTimeout(function(){var t=document.getElementById('saveToast');if(t)t.remove()},3000)</script>
<?php endif; ?>
<?php if (!empty($saveError)): ?>
<div class="toast" id="saveError" style="background:#FEE2E2;color:#B91C1C;border:1px solid #FECACA;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px"><?php echo htmlspecialchars($saveError); ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<!-- Hero Section -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#5F259F">Checkout Header</h3>
<div class="form-row">
<div>
<label class="form-label">Title</label>
<input type="text" name="hero_title" value="<?php echo htmlspecialchars($ck['hero_title'] ?? ''); ?>">
</div>
<div>
<label class="form-label">Subtitle</label>
<input type="text" name="hero_sub" value="<?php echo htmlspecialchars($ck['hero_sub'] ?? ''); ?>">
</div>
</div>
</div>

<!-- UPI Details -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#00BAF2">UPI Payment Details</h3>
<p style="font-size:13px;color:#6B6B6B;margin-bottom:16px">This UPI ID is used for all payments. Merchant name appears in UPI apps.</p>
<div class="form-row">
<div>
<label class="form-label">UPI ID (VPA)</label>
<input type="text" name="upi_id_text" value="<?php echo htmlspecialchars($ck['upi_id'] ?? 'merchant@upi'); ?>" placeholder="e.g. yourname@upi" required>
<p style="font-size:11px;color:#6B6B6B;margin-top:4px">Example: 9876543210@paytm, yourname@oksbi</p>
</div>
<div>
<label class="form-label">Merchant Name (Internal)</label>
<input type="text" name="merchant_name" value="<?php echo htmlspecialchars($ck['merchant_name'] ?? 'QuickRecharge'); ?>" placeholder="e.g. QuickRecharge">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Display Name (shown in UPI apps)</label>
<input type="text" name="merchant_display_name" value="<?php echo htmlspecialchars($ck['merchant_display_name'] ?? $ck['merchant_name'] ?? 'QuickRecharge Store'); ?>" placeholder="e.g. QuickRecharge Store">
<p style="font-size:11px;color:#6B6B6B;margin-top:4px">This is what users see when paying</p>
</div>
<div>
<label class="form-label">Payment Description</label>
<input type="text" name="description" value="<?php echo htmlspecialchars($ck['description'] ?? 'Mobile Recharge'); ?>" placeholder="e.g. Mobile Recharge">
</div>
</div>
<div class="form-row" style="max-width:200px">
<div>
<label class="form-label">Currency</label>
<select name="currency">
<option value="INR" <?php echo ($ck['currency'] ?? 'INR') === 'INR' ? 'selected' : ''; ?>>INR (₹)</option>
</select>
</div>
<div>
<label class="form-label">QR Expiry (minutes)</label>
<input type="number" name="qr_expiry_minutes" min="1" max="30" value="<?php echo (int)($ck['qr_expiry_minutes'] ?? 5); ?>">
<p style="font-size:11px;color:#6B6B6B;margin-top:4px">Dynamic QR validity (1–30 min)</p>
</div>
</div>

<div style="margin-top:16px">
<label class="form-label">QR Code Image (optional)</label>
<div style="display:flex;align-items:flex-start;gap:16px">
<div style="flex:1">
<input type="file" name="qr_image" accept="image/*" style="padding:10px;border:2px dashed #E5E5E5;border-radius:8px;width:100%;cursor:pointer" onchange="previewQR(this)">
<p style="font-size:11px;color:#6B6B6B;margin-top:6px">Upload your UPI QR code — JPG, PNG, SVG (Max 5MB)</p>
<p style="font-size:11px;color:#6C2BFF;margin-top:2px">If uploaded, users see this static QR. If not, dynamic QR is generated with correct amount.</p>
</div>
<div id="qrPreview" style="width:100px;height:100px;border-radius:12px;border:2px solid #E5E5E5;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#F4F5F7;flex-shrink:0">
<?php if (!empty($ck['qr_image'])): ?>
<img src="<?php echo htmlspecialchars($ck['qr_image']); ?>" style="width:100%;height:100%;object-fit:contain">
<?php else: ?>
<span style="font-size:11px;color:#6B6B6B;text-align:center;padding:4px">No QR image</span>
<?php endif; ?>
</div>
</div>
<?php if (!empty($ck['qr_image'])): ?>
<input type="hidden" name="existing_qr" value="<?php echo htmlspecialchars($ck['qr_image']); ?>">
<div style="margin-top:8px"><label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;color:#EF4444"><input type="checkbox" name="remove_qr" value="1"> Remove current QR image</label></div>
<?php endif; ?>
</div>
</div>

<!-- UPI Apps -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#5F259F">UPI Apps</h3>
<p style="font-size:13px;color:#6B6B6B;margin-bottom:16px">Toggle each UPI app on/off and upload logos. At least one must remain enabled.</p>
<?php for ($i = 0; $i < 4; $i++): ?>
<?php $a = $apps[$i]; ?>
<div style="background:#F8F9FA;border-radius:10px;padding:16px;margin-bottom:12px">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
<strong style="font-size:13px"><?php echo htmlspecialchars($a['name'] ?: 'Method ' . ($i + 1)); ?></strong>
<label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer">
<input type="checkbox" name="upi_active[<?php echo $i; ?>]" value="1" <?php echo !empty($a['active']) ? 'checked' : ''; ?>>
Enabled
</label>
</div>
<div class="form-row">
<div>
<label class="form-label">App ID</label>
<input type="text" name="upi_id[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($a['id']); ?>" placeholder="e.g. phonepe">
</div>
<div>
<label class="form-label">App Name</label>
<input type="text" name="upi_name[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($a['name']); ?>" placeholder="e.g. PhonePe">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Description</label>
<input type="text" name="upi_desc[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($a['desc']); ?>" placeholder="e.g. Recommended • Fastest">
</div>
<div>
<label class="form-label">Badge Text</label>
<input type="text" name="upi_badge[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($a['badge']); ?>" placeholder="e.g. Popular">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Brand Color</label>
<input type="color" name="upi_color[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($a['color'] ?? '#333333'); ?>" style="height:40px;padding:4px;cursor:pointer">
</div>
<div></div>
</div>
<div style="margin-top:12px">
<label class="form-label">App Logo</label>
<div style="display:flex;align-items:flex-start;gap:12px">
<div style="flex:1">
<input type="file" name="upi_logo[<?php echo $i; ?>]" accept="image/*" style="padding:10px;border:2px dashed #E5E5E5;border-radius:8px;width:100%;cursor:pointer" onchange="previewLogo(this,<?php echo $i; ?>)">
<p style="font-size:11px;color:#6B6B6B;margin-top:4px">Upload PNG/SVG logo — Max 5MB</p>
</div>
<div id="logoPreview<?php echo $i; ?>" style="width:56px;height:56px;border-radius:12px;border:2px solid #E5E5E5;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#FFF;flex-shrink:0">
<?php if (!empty($a['logo'])): ?>
<img src="<?php echo htmlspecialchars($a['logo']); ?>" style="width:100%;height:100%;object-fit:contain">
<?php else: ?>
<span style="font-size:18px"><?php echo $a['id']==='phonepe'?'💜':($a['id']==='gpay'?'🔵':($a['id']==='paytm'?'💙':'⬜')); ?></span>
<?php endif; ?>
</div>
</div>
<?php if (!empty($a['logo'])): ?>
<input type="hidden" name="existing_logo[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($a['logo']); ?>">
<div style="margin-top:6px"><label style="font-size:11px;display:flex;align-items:center;gap:4px;cursor:pointer;color:#EF4444"><input type="checkbox" name="remove_logo[<?php echo $i; ?>]" value="1"> Remove logo</label></div>
<?php endif; ?>
</div>
</div>
<?php endfor; ?>
</div>

<!-- Security & Summary -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#5F259F">Security & Summary</h3>
<div class="form-row">
<div>
<label class="form-label">Security Title</label>
<input type="text" name="security_text" value="<?php echo htmlspecialchars($ck['security_text'] ?? ''); ?>">
</div>
<div>
<label class="form-label">Security Subtitle</label>
<input type="text" name="security_sub" value="<?php echo htmlspecialchars($ck['security_sub'] ?? ''); ?>">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Summary Title</label>
<input type="text" name="summary_title" value="<?php echo htmlspecialchars($ck['summary_title'] ?? ''); ?>">
</div>
<div>
<label class="form-label">Summary Subtitle</label>
<input type="text" name="summary_sub" value="<?php echo htmlspecialchars($ck['summary_sub'] ?? ''); ?>">
</div>
</div>
</div>

<!-- Trust Footer -->
<div class="form-card" style="margin-bottom:16px">
<h3 style="font-size:15px;margin-bottom:16px;color:#5F259F">Trust Footer</h3>
<?php for ($i = 0; $i < 3; $i++): ?>
<div style="margin-bottom:12px">
<label class="form-label">Item <?php echo $i + 1; ?></label>
<input type="text" name="trust_item[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($ti[$i]); ?>" placeholder="e.g. Secure">
</div>
<?php endfor; ?>
</div>

<!-- Save -->
<div style="display:flex;gap:10px;padding:8px 0 20px">
<button type="submit" class="btn btn-primary" style="padding:12px 32px;font-size:15px">Save All Changes</button>
<a href="?page=dashboard" class="btn btn-outline" style="padding:12px 24px">Cancel</a>
</div>
</form>

<script>
function previewQR(input) {
    var preview = document.getElementById('qrPreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function previewLogo(input, idx) {
    var preview = document.getElementById('logoPreview' + idx);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
