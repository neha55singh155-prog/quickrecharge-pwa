<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Recharge App</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#6C2BFF 0%,#4A1DB5 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
.login-box{background:#fff;border-radius:16px;padding:40px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.login-box h1{font-size:24px;color:#141414;text-align:center;margin-bottom:8px}
.login-box p{text-align:center;color:#6B6B6B;margin-bottom:30px;font-size:14px}
.form-group{margin-bottom:20px}
.form-group label{display:block;font-size:13px;font-weight:600;color:#333;margin-bottom:6px}
.form-group input{width:100%;padding:12px 16px;border:2px solid #E5E5E5;border-radius:10px;font-size:15px;transition:.2s}
.form-group input:focus{outline:none;border-color:#6C2BFF}
.login-btn{width:100%;padding:14px;background:linear-gradient(135deg,#6C2BFF,#8B5DFF);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;transition:.2s}
.login-btn:hover{transform:translateY(-1px);box-shadow:0 4px 15px rgba(108,43,255,.4)}
.error{background:#FEF2F2;border:1px solid #FCA5A5;color:#DC2626;padding:10px;border-radius:8px;font-size:13px;margin-bottom:20px;text-align:center}
.logo{text-align:center;margin-bottom:20px}
.logo svg{width:60px;height:60px}
</style>
</head>
<body>
<div class="login-box">
<div class="logo">
<svg viewBox="0 0 60 60" fill="none"><circle cx="30" cy="30" r="30" fill="url(#g)"/><path d="M30 15l3.5 11h11.5l-9.3 6.8 3.5 10.7L30 37.5l-9.2 6 3.5-10.7L15 26h11.5z" fill="#fff"/><defs><linearGradient id="g" x1="0" y1="0" x2="60" y2="60"><stop stop-color="#6C2BFF"/><stop offset="1" stop-color="#8B5DFF"/></linearGradient></defs></svg>
</div>
<h1>Admin Panel</h1>
<p>Recharge App Management</p>
<?php if (!empty($error)): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if (isset($_GET['expired'])): ?><div class="error">Session expired. Please login again.</div><?php endif; ?>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_csrf_token'] ?? ''); ?>">
<div class="form-group"><label>Username</label><input type="text" name="username" required placeholder="Enter username" autocomplete="username"></div>
<div class="form-group"><label>Password</label><input type="password" name="password" required placeholder="Enter password" autocomplete="current-password"></div>
<button type="submit" class="login-btn">Login</button>
</form>
</div>
</body>
</html>
