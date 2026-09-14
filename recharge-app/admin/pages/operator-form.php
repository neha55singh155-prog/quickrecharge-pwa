<?php
$isEdit = $action === 'edit';
$op = null;
if ($isEdit) {
    $op = $store->getOperator($_GET['id'] ?? 0);
    if (!$op) { header('Location: ?page=operators'); exit; }
}

$uploadDir = dirname(__DIR__, 2) . '/uploads/operators';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opData = [
        'name' => $_POST['name'] ?? '',
        'code' => strtolower(trim($_POST['code'] ?? '')),
        'color' => $_POST['color'] ?? 'linear-gradient(135deg,#6C2BFF,#8B5DFF)',
        'active' => isset($_POST['active']),
        'order' => (int)($_POST['order'] ?? 1),
    ];

    if (!empty($_POST['existing_image'])) {
        $opData['image'] = $_POST['existing_image'];
    }

    if (!empty($_FILES['operator_image']['name'])) {
        $file = $_FILES['operator_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (in_array($ext, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
            $filename = 'op_' . ($opData['code'] ?: time()) . '.' . $ext;
            $filepath = $uploadDir . '/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $opData['image'] = 'uploads/operators/' . $filename;
            }
        }
    }

    if ($isEdit) $opData['id'] = (int)$_GET['id'];
    $store->saveOperator($opData);
    ob_end_clean();
    header('Location: ?page=operators&saved=1');
    exit;
}
?>

<div class="topbar">
<div>
<h1><?php echo $isEdit ? 'Edit Operator' : 'Add Operator'; ?></h1>
<div class="breadcrumb"><a href="?page=operators" style="color:#6C2BFF;text-decoration:none">Operators</a> / <?php echo $isEdit ? 'Edit' : 'Add'; ?></div>
</div>
</div>

<div class="form-card" style="max-width:700px">
<form method="POST" enctype="multipart/form-data">
<div class="form-row">
<div>
<label class="form-label">Operator Name</label>
<input type="text" name="name" value="<?php echo htmlspecialchars($op['name'] ?? ''); ?>" placeholder="e.g. Jio" required>
</div>
<div>
<label class="form-label">Code (unique identifier)</label>
<input type="text" name="code" value="<?php echo htmlspecialchars($op['code'] ?? ''); ?>" placeholder="e.g. jio" required <?php echo $isEdit ? 'readonly style="background:#F4F5F7"' : ''; ?>>
</div>
</div>

<div class="form-row">
<div>
<label class="form-label">Color (CSS gradient)</label>
<input type="text" name="color" value="<?php echo htmlspecialchars($op['color'] ?? 'linear-gradient(135deg,#6C2BFF,#8B5DFF)'); ?>" placeholder="linear-gradient(135deg,#0A3D91,#1E6DD1)">
<p style="font-size:11px;color:#6B6B6B;margin-top:4px">Used when no image is uploaded</p>
</div>
<div>
<label class="form-label">Display Order</label>
<input type="number" name="order" min="1" max="99" value="<?php echo $op['order'] ?? 1; ?>">
</div>
</div>

<div style="margin-bottom:20px">
<label class="form-label">Operator Logo / Image</label>
<div style="display:flex;align-items:flex-start;gap:16px">
<div style="flex:1">
<input type="file" name="operator_image" accept="image/*" style="padding:10px;border:2px dashed #E5E5E5;border-radius:8px;width:100%;cursor:pointer" onchange="previewImage(this)">
<p style="font-size:11px;color:#6B6B6B;margin-top:6px">JPG, PNG, GIF, WebP, SVG — Max 2MB</p>
</div>
<div id="imagePreview" style="width:80px;height:80px;border-radius:16px;border:2px solid #E5E5E5;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#F4F5F7;flex-shrink:0">
<?php if (!empty($op['image'])): ?>
<img src="<?php echo htmlspecialchars($op['image']); ?>" style="width:100%;height:100%;object-fit:cover">
<?php else: ?>
<span style="font-size:11px;color:#6B6B6B;text-align:center;padding:4px">No image</span>
<?php endif; ?>
</div>
</div>
<?php if (!empty($op['image'])): ?>
<input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($op['image']); ?>">
<?php endif; ?>
</div>

<div style="margin-bottom:20px;display:flex;align-items:center;gap:10px">
<input type="checkbox" name="active" id="activeCheck" <?php echo empty($op['active']) && !$isEdit ? '' : 'checked'; ?>>
<label for="activeCheck" style="font-size:14px;cursor:pointer">Active (visible in app)</label>
</div>

<div style="display:flex;gap:10px;padding-top:8px">
<button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Operator' : 'Add Operator'; ?></button>
<a href="?page=operators" class="btn btn-outline">Cancel</a>
</div>
</form>
</div>

<script>
function previewImage(input) {
    var preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
