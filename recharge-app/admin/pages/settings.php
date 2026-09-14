<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'app_name' => $_POST['app_name'] ?? '',
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
        'min_recharge' => $_POST['min_recharge'] ?? '10',
        'max_recharge' => $_POST['max_recharge'] ?? '10000',
        'support_email' => $_POST['support_email'] ?? '',
        'UPI_ID' => $_POST['UPI_ID'] ?? '',
    ];
    $store->saveSettings($settings);
    header('Location: ?page=settings&saved=1');
    exit;
}
$settings = $store->getSettings();
?>

<div class="topbar">
<div>
<h1>Settings</h1>
<div class="breadcrumb">App configuration</div>
</div>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="toast toast-success" id="saveToast">Settings saved!</div>
<script>setTimeout(function(){var t=document.getElementById('saveToast');if(t)t.remove()},3000)</script>
<?php endif; ?>

<div class="form-card" style="max-width:600px">
<form method="POST">
<div style="margin-bottom:20px">
<label class="form-label">App Name</label>
<input type="text" name="app_name" value="<?php echo htmlspecialchars($settings['app_name'] ?? ''); ?>">
</div>
<div style="margin-bottom:20px">
<label class="form-label">Support Email</label>
<input type="text" name="support_email" value="<?php echo htmlspecialchars($settings['support_email'] ?? ''); ?>">
</div>
<div style="margin-bottom:20px">
<label class="form-label">UPI ID</label>
<input type="text" name="UPI_ID" value="<?php echo htmlspecialchars($settings['UPI_ID'] ?? ''); ?>">
</div>
<div class="form-row">
<div>
<label class="form-label">Min Recharge (&#8377;)</label>
<input type="number" name="min_recharge" value="<?php echo htmlspecialchars($settings['min_recharge'] ?? '10'); ?>">
</div>
<div>
<label class="form-label">Max Recharge (&#8377;)</label>
<input type="number" name="max_recharge" value="<?php echo htmlspecialchars($settings['max_recharge'] ?? '10000'); ?>">
</div>
</div>
<div style="margin-bottom:20px;display:flex;align-items:center;gap:10px;padding-top:8px">
<input type="checkbox" name="maintenance_mode" id="maintCheck" <?php echo ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : ''; ?>>
<label for="maintCheck" style="font-size:14px;cursor:pointer">Enable Maintenance Mode</label>
</div>
<button type="submit" class="btn btn-primary">Save Settings</button>
</form>
</div>

<div class="form-card" style="max-width:600px;margin-top:16px">
<h3 style="font-size:15px;margin-bottom:12px">Admin Credentials</h3>
<div style="padding:12px;background:#F4F5F7;border-radius:8px;font-size:13px">
<div style="margin-bottom:6px"><strong>Username:</strong> admin</div>
<div><strong>Password:</strong> <em>Change in admin/index.php using password_hash()</em></div>
<p style="font-size:11px;color:#6B6B6B;margin-top:8px">To change password, run: <code>php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"</code> and update ADMIN_PASS_HASH in admin/index.php</p>
</div>
</div>
