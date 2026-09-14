<?php
// Security headers for admin panel
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Recharge App</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#F4F5F7;color:#141414;display:flex;min-height:100vh}

.sidebar{width:240px;background:#fff;border-right:1px solid #E5E5E5;padding:20px 0;position:fixed;height:100vh;overflow-y:auto}
.sidebar-brand{padding:0 20px 20px;border-bottom:1px solid #E5E5E5;display:flex;align-items:center;gap:10px}
.sidebar-brand svg{width:32px;height:32px}
.sidebar-brand h2{font-size:16px;font-weight:700;color:#6C2BFF}
.sidebar-nav{padding:12px 0}
.sidebar-nav a{display:flex;align-items:center;gap:12px;padding:10px 20px;color:#6B6B6B;text-decoration:none;font-size:14px;font-weight:500;transition:.15s}
.sidebar-nav a:hover{background:#F4F5F7;color:#141414}
.sidebar-nav a.active{background:linear-gradient(135deg,#6C2BFF,#8B5DFF);color:#fff;border-radius:0}
.sidebar-nav a svg{width:20px;height:20px;flex-shrink:0}
.sidebar-bottom{position:absolute;bottom:0;width:100%;padding:12px 20px;border-top:1px solid #E5E5E5}
.sidebar-bottom a{display:flex;align-items:center;gap:10px;padding:8px 0;color:#EF4444;text-decoration:none;font-size:13px;font-weight:500}

.main{margin-left:240px;flex:1;padding:24px;min-height:100vh}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
.topbar h1{font-size:22px;font-weight:700}
.topbar .breadcrumb{font-size:13px;color:#6B6B6B}

.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:#fff;border-radius:12px;padding:20px;border:1px solid #E5E5E5}
.stat-card .label{font-size:12px;color:#6B6B6B;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.stat-card .value{font-size:28px;font-weight:700}
.stat-card .sub{font-size:12px;color:#6B6B6B;margin-top:4px}
.stat-card.purple .value{color:#6C2BFF}
.stat-card.green .value{color:#16A34A}
.stat-card.orange .value{color:#F59E0B}
.stat-card.red .value{color:#EF4444}

.table-wrap{background:#fff;border-radius:12px;border:1px solid #E5E5E5;overflow:hidden}
.table-header{padding:16px 20px;border-bottom:1px solid #E5E5E5;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.table-header h2{font-size:16px;font-weight:600}
.table-actions{display:flex;gap:8px;align-items:center}
.btn{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:.15s;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.btn-primary{background:linear-gradient(135deg,#6C2BFF,#8B5DFF);color:#fff}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 2px 8px rgba(108,43,255,.3)}
.btn-sm{padding:6px 12px;font-size:12px}
.btn-success{background:#16A34A;color:#fff}
.btn-danger{background:#EF4444;color:#fff}
.btn-warning{background:#F59E0B;color:#fff}
.btn-outline{background:transparent;border:1px solid #E5E5E5;color:#6B6B6B}
.btn-outline:hover{border-color:#6C2BFF;color:#6C2BFF}

table{width:100%;border-collapse:collapse}
th{text-align:left;padding:12px 20px;font-size:12px;color:#6B6B6B;text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:1px solid #E5E5E5;background:#FAFAFA}
td{padding:12px 20px;font-size:13px;border-bottom:1px solid #F0F0F0}
tr:last-child td{border-bottom:none}
tr:hover td{background:#FAFAFA}

.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-green{background:#DCFCE7;color:#16A34A}
.badge-yellow{background:#FEF3C7;color:#D97706}
.badge-red{background:#FEE2E2;color:#DC2626}
.badge-purple{background:#F3E8FF;color:#6C2BFF}
.badge-blue{background:#DBEAFE;color:#2563EB}

.filter-bar{display:flex;gap:8px;flex-wrap:wrap}
.filter-btn{padding:6px 14px;border:1px solid #E5E5E5;border-radius:20px;font-size:12px;cursor:pointer;background:#fff;transition:.15s}
.filter-btn.active{background:#6C2BFF;color:#fff;border-color:#6C2BFF}
.filter-btn:hover{border-color:#6C2BFF}

input[type="text"],input[type="number"],select,textarea{padding:10px 14px;border:1px solid #E5E5E5;border-radius:8px;font-size:14px;width:100%;transition:.15s}
input:focus,select:focus,textarea:focus{outline:none;border-color:#6C2BFF;box-shadow:0 0 0 3px rgba(108,43,255,.1)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.form-full{grid-column:1/-1}
.form-label{display:block;font-size:13px;font-weight:600;color:#333;margin-bottom:6px}
.form-card{background:#fff;border-radius:12px;border:1px solid #E5E5E5;padding:24px}

.empty{text-align:center;padding:40px;color:#6B6B6B}
.empty svg{width:48px;height:48px;margin-bottom:12px;opacity:.4}

.toast{position:fixed;top:20px;right:20px;padding:12px 20px;border-radius:8px;color:#fff;font-size:13px;font-weight:600;z-index:9999;animation:slideIn .3s ease}
.toast-success{background:#16A34A}
.toast-error{background:#EF4444}
@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}
</style>
</head>
<body>
<aside class="sidebar">
<div class="sidebar-brand">
<svg viewBox="0 0 32 32" fill="none"><circle cx="16" cy="16" r="16" fill="url(#sg)"/><path d="M16 8l2 6h6l-5 3.5 2 6-5-3.5-5 3.5 2-6-5-3.5h6z" fill="#fff"/><defs><linearGradient id="sg" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6C2BFF"/><stop offset="1" stop-color="#8B5DFF"/></linearGradient></defs></svg>
<h2>Recharge Admin</h2>
</div>
<nav class="sidebar-nav">
<a href="?page=dashboard" class="<?php echo $currentPage==='dashboard'?'active':''; ?>">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
Dashboard
</a>
<a href="?page=home-content" class="<?php echo $currentPage==='home-content'?'active':''; ?>">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
Home Content
</a>
<a href="?page=plans" class="<?php echo $currentPage==='plans'?'active':''; ?>">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
Plans
</a>
<a href="?page=operators" class="<?php echo $currentPage==='operators'?'active':''; ?>">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
Operators
</a>
<a href="?page=settings" class="<?php echo $currentPage==='settings'?'active':''; ?>">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
Settings
</a>
<a href="?page=payment-settings" class="<?php echo $currentPage==='payment-settings'?'active':''; ?>">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
Payment Settings
</a>
<a href="?page=transactions" class="<?php echo $currentPage==='transactions'?'active':''; ?>">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
Transactions
</a>
</nav>
<div class="sidebar-bottom">
<a href="?page=logout">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
Logout
</a>
</div>
</aside>
<main class="main">
