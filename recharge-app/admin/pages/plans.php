<?php
$opFilter = isset($_GET['op']) ? $_GET['op'] : 'all';
$plans = $store->getPlans($opFilter);
?>

<div class="topbar">
<div>
<h1>Manage Plans</h1>
<div class="breadcrumb">Add, edit or delete recharge plans</div>
</div>
</div>

<div class="table-wrap">
<div class="table-header">
<h2>All Plans (<?php echo count($plans); ?>)</h2>
<div class="table-actions">
<div class="filter-bar">
<a href="?page=plans&op=all" class="filter-btn <?php echo $opFilter==='all'?'active':''; ?>">All</a>
<a href="?page=plans&op=jio" class="filter-btn <?php echo $opFilter==='jio'?'active':''; ?>">Jio</a>
<a href="?page=plans&op=airtel" class="filter-btn <?php echo $opFilter==='airtel'?'active':''; ?>">Airtel</a>
<a href="?page=plans&op=vi" class="filter-btn <?php echo $opFilter==='vi'?'active':''; ?>">Vi</a>
<a href="?page=plans&op=bsnl" class="filter-btn <?php echo $opFilter==='bsnl'?'active':''; ?>">BSNL</a>
</div>
<a href="?page=plans&action=add" class="btn btn-primary btn-sm">+ Add Plan</a>
</div>
</div>
<table>
<thead>
<tr><th>ID</th><th>Operator</th><th>Title</th><th>Price</th><th>Old Price</th><th>Validity</th><th>Data</th><th>Badge</th><th>Order</th><th>Status</th><th>Actions</th></tr>
</thead>
<tbody>
<?php if (empty($plans)): ?>
<tr><td colspan="11" class="empty">No plans found</td></tr>
<?php else: foreach ($plans as $p): ?>
<tr>
<td>#<?php echo $p['id']; ?></td>
<td><span class="badge badge-purple"><?php echo strtoupper($p['operator']); ?></span></td>
<td><strong><?php echo htmlspecialchars($p['title'] ?? ''); ?></strong></td>
<td><strong>&#8377;<?php echo $p['amount']; ?></strong></td>
<td><?php echo !empty($p['old_price']) ? '<s style="color:#999">&#8377;'.htmlspecialchars((string)$p['old_price']).'</s>' : '-'; ?></td>
<td><?php echo htmlspecialchars($p['validity']); ?></td>
<td><?php echo htmlspecialchars($p['data']); ?></td>
<td><?php echo !empty($p['badge']) ? '<span class="badge badge-yellow">'.htmlspecialchars($p['badge']).'</span>' : '-'; ?></td>
<td><?php echo (int)($p['sort_order'] ?? 0); ?></td>
<td><?php echo !empty($p['active']) ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-red">Inactive</span>'; ?></td>
<td>
<a href="?page=plans&action=edit&id=<?php echo (int)$p['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
<form method="POST" action="?page=plans&action=delete&id=<?php echo (int)$p['id']; ?>" style="display:inline" onsubmit="return confirm('Delete this plan?')">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_csrf_token'] ?? ''); ?>">
<button type="submit" class="btn btn-danger btn-sm">Del</button>
</form>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
