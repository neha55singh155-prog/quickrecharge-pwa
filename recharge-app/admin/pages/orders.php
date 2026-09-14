<?php
$txnFilter = isset($_GET['status']) ? preg_replace('/[^a-z]/', '', $_GET['status']) : 'all';
// Admin verification actions (admin session only — index.php guards ?page=)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['txn_action'], $_POST['txn_id'])) {
    $txnId = preg_replace('/[^a-zA-Z0-9\-]/', '', trim($_POST['txn_id']));
    $action = $_POST['txn_action'];
    $utr = preg_replace('/[^A-Za-z0-9]/', '', trim($_POST['utr'] ?? ''));
    if ($action === 'approve') {
        $store->updateTransactionStatus($txnId, 'SUCCESS', $utr);
        $store->addLog('verification.admin_success', ['transaction_id' => $txnId, 'utr' => $utr]);
    } elseif ($action === 'reject') {
        $store->updateTransactionStatus($txnId, 'FAILED', $utr);
        $store->addLog('verification.admin_failed', ['transaction_id' => $txnId]);
    }
    header('Location: ?page=orders&status=' . urlencode($txnFilter));
    exit;
}
$txns = $store->getTransactions($txnFilter);
?>

<div class="topbar">
<div>
<h1>Transactions</h1>
<div class="breadcrumb">All payment transactions and recharge attempts</div>
</div>
</div>

<div class="table-wrap">
<div class="table-header">
<h2>All Transactions (<?php echo count($txns); ?>)</h2>
<div class="table-actions">
<div class="filter-bar">
<a href="?page=orders" class="filter-btn <?php echo $txnFilter==='all'?'active':''; ?>">All</a>
<a href="?page=orders&status=initiated" class="filter-btn <?php echo $txnFilter==='initiated'?'active':''; ?>">Initiated</a>
<a href="?page=orders&status=pending" class="filter-btn <?php echo $txnFilter==='pending'?'active':''; ?>">Pending</a>
<a href="?page=orders&status=success" class="filter-btn <?php echo $txnFilter==='success'?'active':''; ?>">Success</a>
<a href="?page=orders&status=failed" class="filter-btn <?php echo $txnFilter==='failed'?'active':''; ?>">Failed</a>
</div>
</div>
</div>
<p style="font-size:12px;color:#6B6B6B;padding:0 16px 12px">Verify customer UPI payments against your bank statement (match Transaction ID + UTR), then Approve or Reject. Only admin approval marks SUCCESS.</p>
<table>
<thead>
<tr><th>Transaction ID</th><th>Date</th><th>Mobile</th><th>Operator</th><th>Plan</th><th>Amount</th><th>UPI ID</th><th>Method</th><th>UTR</th><th>Status</th><th>Verify</th></tr>
</thead>
<tbody>
<?php if (empty($txns)): ?>
<tr><td colspan="11" class="empty">No transactions found</td></tr>
<?php else: foreach ($txns as $t): ?>
<tr>
<td><strong style="font-size:12px"><?php echo htmlspecialchars($t['transaction_id']); ?></strong></td>
<td style="font-size:12px;color:#6B6B6B"><?php echo htmlspecialchars($t['created_at']); ?></td>
<td style="font-size:12px">+91 <?php echo htmlspecialchars($t['mobile']); ?></td>
<td><span class="badge badge-purple"><?php echo htmlspecialchars(strtoupper($t['operator'])); ?></span></td>
<td style="font-size:11px;color:#6B6B6B">#<?php echo (int)($t['plan_id'] ?? 0); ?></td>
<td><strong>&#8377;<?php echo number_format((float)($t['plan_amount'] ?? $t['amount'] ?? 0), 0); ?></strong></td>
<td style="font-size:11px;color:#6B6B6B"><?php echo htmlspecialchars($t['upi_id'] ?? $t['merchant_upi_id'] ?? ''); ?></td>
<td style="font-size:12px"><?php echo ucfirst(htmlspecialchars($t['payment_method'])); ?></td>
<td style="font-size:11px"><?php echo htmlspecialchars($t['upi_reference'] ?? $t['reference_id'] ?? '—'); ?></td>
<td>
<?php
$statusClass = 'badge-yellow';
if ($t['status'] === 'SUCCESS') $statusClass = 'badge-green';
elseif ($t['status'] === 'FAILED') $statusClass = 'badge-red';
elseif ($t['status'] === 'INITIATED') $statusClass = 'badge-purple';
?>
<span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($t['status']); ?></span>
</td>
<td>
<?php if (in_array($t['status'], ['INITIATED', 'PENDING'], true)): ?>
<form method="POST" style="display:flex;gap:4px;align-items:center">
<input type="hidden" name="txn_id" value="<?php echo htmlspecialchars($t['transaction_id']); ?>">
<input type="text" name="utr" placeholder="UTR (optional)" value="<?php echo htmlspecialchars($t['upi_reference'] ?? ''); ?>" style="width:90px;padding:6px 8px;font-size:11px;border:1px solid #E5E5E5;border-radius:8px">
<button type="submit" name="txn_action" value="approve" class="btn btn-primary" style="padding:6px 10px;font-size:11px">Approve</button>
<button type="submit" name="txn_action" value="reject" class="btn btn-outline" style="padding:6px 10px;font-size:11px">Reject</button>
</form>
<?php else: ?>
<span style="font-size:11px;color:#999">—</span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
