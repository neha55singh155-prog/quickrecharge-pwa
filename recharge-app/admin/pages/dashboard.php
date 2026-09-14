<div class="topbar">
<div>
<h1>Dashboard</h1>
<div class="breadcrumb">Welcome back, Admin</div>
</div>
<div style="font-size:13px;color:#6B6B6B"><?php echo date('l, F j, Y'); ?></div>
</div>

<div class="cards">
<div class="stat-card purple">
<div class="label">Total Plans</div>
<div class="value"><?php echo $stats['total_plans']; ?></div>
<div class="sub">Across all operators</div>
</div>
<div class="stat-card green">
<div class="label">Revenue</div>
<div class="value">&#8377;<?php echo number_format($stats['total_revenue']); ?></div>
<div class="sub">Total earnings</div>
</div>
<div class="stat-card orange">
<div class="label">Total Orders</div>
<div class="value"><?php echo $stats['total_orders']; ?></div>
<div class="sub"><?php echo $stats['success_orders']; ?> successful</div>
</div>
<div class="stat-card red">
<div class="label">Pending</div>
<div class="value"><?php echo $stats['pending_orders']; ?></div>
<div class="sub"><?php echo $stats['failed_orders']; ?> failed</div>
</div>
</div>

<div class="table-wrap">
<div class="table-header">
<h2>Recent Orders</h2>
<a href="?page=orders" class="btn btn-outline btn-sm">View All</a>
</div>
<table>
<thead>
<tr><th>#ID</th><th>Mobile</th><th>Operator</th><th>Amount</th><th>Status</th><th>Date</th></tr>
</thead>
<tbody>
<?php
$orders = $store->getOrders();
$recent = array_slice($orders, 0, 5);
if (empty($recent)):
?>
<tr><td colspan="6" class="empty">No orders yet</td></tr>
<?php else: foreach ($recent as $o): ?>
<tr>
<td><strong>#<?php echo (int)$o['id']; ?></strong></td>
<td><?php echo htmlspecialchars($o['mobile']); ?></td>
<td><span class="badge badge-purple"><?php echo htmlspecialchars(strtoupper($o['operator'])); ?></span></td>
<td><strong>&#8377;<?php echo (float)$o['amount']; ?></strong></td>
<td>
<?php if ($o['status']==='success'): ?><span class="badge badge-green">Success</span>
<?php elseif ($o['status']==='pending'): ?><span class="badge badge-yellow">Pending</span>
<?php else: ?><span class="badge badge-red">Failed</span><?php endif; ?>
</td>
<td><?php echo date('M d, H:i', strtotime($o['created'])); ?></td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
