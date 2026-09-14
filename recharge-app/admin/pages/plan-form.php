<?php
$isEdit = $action === 'edit';
$plan = null;
if ($isEdit) {
    $plan = $store->getPlan($_GET['id'] ?? 0);
    if (!$plan) { header('Location: ?page=plans'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planData = [
        'operator' => $_POST['operator'],
        'title' => $_POST['title'] ?? '',
        'amount' => (float)$_POST['amount'],
        'old_price' => (float)($_POST['old_price'] ?? 0),
        'validity' => $_POST['validity'],
        'data' => $_POST['data'],
        'calls' => $_POST['calls'],
        'sms' => $_POST['sms'],
        'category' => $_POST['category'],
        'badge' => $_POST['badge'],
        'active' => isset($_POST['active']),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'benefits' => $_POST['benefits'] ?? '',
    ];
    if ($isEdit) $planData['id'] = (int)$_GET['id'];
    $store->savePlan($planData);
    ob_end_clean();
    header('Location: ?page=plans&op=' . $planData['operator']);
    exit;
}
?>

<div class="topbar">
<div>
<h1><?php echo $isEdit ? 'Edit Plan' : 'Add New Plan'; ?></h1>
<div class="breadcrumb"><a href="?page=plans" style="color:#6C2BFF;text-decoration:none">Plans</a> / <?php echo $isEdit ? 'Edit' : 'Add'; ?></div>
</div>
</div>

<div class="form-card" style="max-width:700px">
<form method="POST">
<div class="form-row">
<div>
<label class="form-label">Operator</label>
<select name="operator" required>
<option value="jio" <?php echo ($plan['operator']??'')==='jio'?'selected':''; ?>>Jio</option>
<option value="airtel" <?php echo ($plan['operator']??'')==='airtel'?'selected':''; ?>>Airtel</option>
<option value="vi" <?php echo ($plan['operator']??'')==='vi'?'selected':''; ?>>Vi</option>
<option value="bsnl" <?php echo ($plan['operator']??'')==='bsnl'?'selected':''; ?>>BSNL</option>
</select>
</div>
<div>
<label class="form-label">Plan Title</label>
<input type="text" name="title" value="<?php echo htmlspecialchars($plan['title']??''); ?>" placeholder="e.g. Jio 499 Unlimited">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Price (₹)</label>
<input type="number" name="amount" step="1" min="1" value="<?php echo $plan['amount']??''; ?>" required>
</div>
<div>
<label class="form-label">Old Price (₹) <span style="font-weight:400;color:#999">— optional</span></label>
<input type="number" name="old_price" step="1" min="0" value="<?php echo $plan['old_price']??''; ?>" placeholder="e.g. 599">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Validity</label>
<input type="text" name="validity" value="<?php echo htmlspecialchars($plan['validity']??''); ?>" placeholder="e.g. 84 Days" required>
</div>
<div>
<label class="form-label">Data</label>
<input type="text" name="data" value="<?php echo htmlspecialchars($plan['data']??''); ?>" placeholder="e.g. 2GB/day" required>
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Calls</label>
<input type="text" name="calls" value="<?php echo htmlspecialchars($plan['calls']??'Unlimited'); ?>" required>
</div>
<div>
<label class="form-label">SMS</label>
<input type="text" name="sms" value="<?php echo htmlspecialchars($plan['sms']??''); ?>" placeholder="e.g. 100/day" required>
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Category</label>
<select name="category" required>
<option value="popular" <?php echo ($plan['category']??'')==='popular'?'selected':''; ?>>Popular</option>
<option value="unlimited" <?php echo ($plan['category']??'')==='unlimited'?'selected':''; ?>>Unlimited</option>
<option value="data" <?php echo ($plan['category']??'')==='data'?'selected':''; ?>>Data</option>
<option value="talktime" <?php echo ($plan['category']??'')==='talktime'?'selected':''; ?>>Talktime</option>
</select>
</div>
<div>
<label class="form-label">Badge</label>
<input type="text" name="badge" value="<?php echo htmlspecialchars($plan['badge']??''); ?>" placeholder="e.g. HOT, NEW, VIP">
</div>
</div>
<div class="form-row">
<div>
<label class="form-label">Sort Order</label>
<input type="number" name="sort_order" min="0" value="<?php echo $plan['sort_order']??0; ?>">
<p style="font-size:11px;color:#6B6B6B;margin-top:4px">Lower = shows first</p>
</div>
<div class="form-full" style="display:flex;align-items:center;gap:10px;padding-top:24px">
<input type="checkbox" name="active" id="activeCheck" <?php echo empty($plan['active']) && !$isEdit ? '' : 'checked'; ?>>
<label for="activeCheck" style="font-size:14px;cursor:pointer">Active (visible in app)</label>
</div>
</div>
<div style="grid-column:1/-1">
<label class="form-label">Free Benefits <span style="font-weight:400;color:#999">— one per line</span></label>
<textarea name="benefits" rows="3" style="padding:10px 14px;border:1px solid #E5E5E5;border-radius:8px;font-size:14px;width:100%;resize:vertical" placeholder="JioHotstar Premium&#10;Google Gemini&#10;JioTV"><?php echo htmlspecialchars($plan['benefits']??''); ?></textarea>
<p style="font-size:11px;color:#6B6B6B;margin-top:4px">Enter one benefit per line. Leave empty for no benefits.</p>
</div>
<div style="display:flex;gap:10px;padding-top:16px">
<button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Plan' : 'Add Plan'; ?></button>
<a href="?page=plans" class="btn btn-outline">Cancel</a>
</div>
</form>
</div>
