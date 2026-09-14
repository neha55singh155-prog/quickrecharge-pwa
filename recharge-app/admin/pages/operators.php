<?php
$operators = $store->getOperators();
?>

<div class="topbar">
<div>
<h1>Network Providers</h1>
<div class="breadcrumb">Manage operators — add, edit, upload logos</div>
</div>
</div>

<div class="table-wrap">
<div class="table-header">
<h2>All Operators (<?php echo count($operators); ?>)</h2>
<a href="?page=operators&action=add" class="btn btn-primary btn-sm">+ Add Operator</a>
</div>
<table>
<thead>
<tr><th>Preview</th><th>Name</th><th>Code</th><th>Color</th><th>Image</th><th>Status</th><th>Order</th><th>Actions</th></tr>
</thead>
<tbody>
<?php if (empty($operators)): ?>
<tr><td colspan="8" class="empty">No operators found</td></tr>
<?php else: foreach ($operators as $op): ?>
<tr>
<td>
<?php if (!empty($op['image'])): ?>
<img src="<?php echo htmlspecialchars($op['image']); ?>" alt="<?php echo htmlspecialchars($op['name']); ?>" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1px solid #E5E5E5">
<?php else: ?>
<div style="width:40px;height:40px;border-radius:10px;background:<?php echo $op['color'] ?? 'linear-gradient(135deg,#6C2BFF,#8B5DFF)'; ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px">
<?php echo strtoupper(substr($op['name'], 0, 2)); ?>
</div>
<?php endif; ?>
</td>
<td><strong><?php echo htmlspecialchars($op['name']); ?></strong></td>
<td><code style="background:#F4F5F7;padding:2px 8px;border-radius:4px;font-size:12px"><?php echo htmlspecialchars($op['code']); ?></code></td>
<td>
<div style="width:60px;height:24px;border-radius:6px;background:<?php echo htmlspecialchars($op['color'] ?? '#6C2BFF'); ?>;border:1px solid #E5E5E5"></div>
</td>
<td>
<?php if (!empty($op['image'])): ?>
<span class="badge badge-green">Uploaded</span>
<?php else: ?>
<span class="badge badge-yellow">No Image</span>
<?php endif; ?>
</td>
<td>
<?php echo !empty($op['active']) ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-red">Inactive</span>'; ?>
</td>
<td><?php echo (int)($op['order'] ?? 0); ?></td>
<td>
<a href="?page=operators&action=edit&id=<?php echo (int)$op['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
<form method="POST" action="?page=operators&action=delete&id=<?php echo (int)$op['id']; ?>" style="display:inline" onsubmit="return confirm('Delete this operator?')">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_csrf_token'] ?? ''); ?>">
<button type="submit" class="btn btn-danger btn-sm">Del</button>
</form>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
