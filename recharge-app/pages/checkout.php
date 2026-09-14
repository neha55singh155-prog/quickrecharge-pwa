<?php
$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';
$store = new DataStore($basePath . '/admin/data');
$ck = $store->getCheckout();
$ck = array_merge([
    'hero_title' => 'Secure Checkout',
    'hero_sub' => 'UPI • Verified • Fast',
    'upi_id' => 'merchant@upi',
    'merchant_name' => 'QuickRecharge',
    'merchant_display_name' => 'QuickRecharge Store',
    'currency' => 'INR',
    'description' => 'Mobile Recharge',
    'qr_image' => '',
    'upi_apps' => [],
    'security_text' => '100% Secure Payments',
    'security_sub' => 'Your payment is protected with UPI and bank-level security',
    'summary_title' => 'Recharge Summary',
    'summary_sub' => 'Please check your details before payment',
    'trust_items' => ['Secure', 'Instant Recharge', 'Trusted by Millions'],
    'qr_expiry_minutes' => 5,
], $ck);
// Only the 4 allowed UPI methods, honoring admin enable/disable
$allowedIds = ['phonepe', 'gpay', 'paytm', 'qr'];
$apps = array_values(array_filter($ck['upi_apps'], function ($a) use ($allowedIds) {
    return !empty($a['active']) && in_array($a['id'], $allowedIds, true);
}));
$ti = $ck['trust_items'];
while (count($ti) < 3) $ti[] = '';
?>
<!-- S006 Secure Checkout — Premium UPI Only (server-verified amounts) -->
<div class="ck-screen">

    <!-- Background Waves -->
    <div class="ck-bg">
        <div class="ck-wave ck-wave-1"></div>
        <div class="ck-wave ck-wave-2"></div>
        <div class="ck-wave ck-wave-3"></div>
    </div>

    <!-- Header -->
    <div class="ck-header slide-up">
        <div class="ck-header-icon">
            <svg viewBox="0 0 24 24" fill="white" width="22" height="22"><path d="M12 2l2.2 5.2 5.8 0.5-4.4 3.7 1.3 5.6L12 13.5 7.1 17l1.3-5.6L4 7.7l5.8-0.5L12 2Z"/></svg>
        </div>
        <div class="ck-header-text">
            <h1 class="ck-header-title"><?php echo htmlspecialchars($ck['hero_title']); ?></h1>
            <p class="ck-header-sub"><?php echo htmlspecialchars($ck['hero_sub']); ?></p>
        </div>
        <button class="ck-back-btn" onclick="Router.goBack()" aria-label="Go back">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"/></svg>
        </button>
    </div>

    <div class="ck-scroll-body">

        <!-- Number Card -->
        <div class="ck-card ck-number-card slide-up" style="animation-delay:0.04s">
            <div class="ck-number-left">
                <div class="ck-op-logo" id="ckOperatorLogo">
                    <span id="ckOpLetter">J</span>
                </div>
                <div class="ck-number-info">
                    <span class="ck-number-label">Recharge for</span>
                    <span class="ck-number-value" id="ckNumber">+91 ••••• •••••</span>
                </div>
            </div>
            <button class="ck-change-btn" onclick="Router.navigate('home')">Change</button>
        </div>

        <!-- Recharge Summary (backend-verified) -->
        <div class="ck-card ck-summary-card slide-up" style="animation-delay:0.06s">
            <div class="ck-sum-header">
                <div>
                    <h2 class="ck-sum-title"><?php echo htmlspecialchars($ck['summary_title']); ?></h2>
                    <p class="ck-sum-sub"><?php echo htmlspecialchars($ck['summary_sub']); ?></p>
                </div>
                <span class="ck-verified-pill" id="ckVerifiedPill">Verified</span>
            </div>
            <div class="ck-sum-loading" id="ckSumLoading">Loading plan from server…</div>
            <div class="ck-sum-error" id="ckSumError" style="display:none"></div>
            <div class="ck-sum-rows" id="ckSumRows" style="display:none">
                <div class="ck-sum-row"><span>Operator</span><strong id="ckSumOperator">—</strong></div>
                <div class="ck-sum-row"><span>Mobile</span><strong id="ckSumMobile">—</strong></div>
                <div class="ck-sum-row"><span>Plan</span><strong id="ckSumPlan">—</strong></div>
                <div class="ck-sum-row"><span>Validity</span><strong id="ckSumValidity">—</strong></div>
                <div class="ck-sum-row ck-sum-total"><span>Total Amount</span><strong id="ckSumAmount">—</strong></div>
                <div class="ck-sum-row"><span>Status</span><strong class="ck-status-ok">Verified ✓</strong></div>
            </div>
        </div>

        <!-- UPI Section -->
        <div class="ck-card ck-upi-card slide-up" style="animation-delay:0.08s">
            <div class="ck-upi-header">
                <h2 class="ck-upi-title">Pay via UPI</h2>
                <p class="ck-upi-sub">Choose your preferred UPI app</p>
            </div>
            <div class="ck-upi-list" id="ckUpiList">
                <?php if (empty($apps)): ?>
                <p style="font-size:13px;color:#EF4444">UPI payments are temporarily disabled. Please contact support.</p>
                <?php endif; ?>
                <?php foreach ($apps as $idx => $a): ?>
                <button class="ck-upi-item" data-app="<?php echo htmlspecialchars($a['id']); ?>" onclick="CheckoutScreen.pay('<?php echo htmlspecialchars($a['id']); ?>')" style="animation-delay:<?php echo 0.1 + ($idx * 0.04); ?>s">
                    <div class="ck-upi-icon-wrap">
                        <?php if (!empty($a['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($a['logo']); ?>" alt="<?php echo htmlspecialchars($a['name']); ?>" style="width:100%;height:100%;object-fit:contain;border-radius:14px">
                        <?php elseif ($a['id'] === 'phonepe'): ?>
                        <div class="ck-upi-icon ck-icon-phonepe">
                            <svg viewBox="0 0 48 48" width="28" height="28"><circle cx="24" cy="24" r="24" fill="#5F259F"/><text x="24" y="30" text-anchor="middle" font-family="sans-serif" font-size="18" font-weight="800" fill="white">\u092A</text></svg>
                        </div>
                        <?php elseif ($a['id'] === 'gpay'): ?>
                        <div class="ck-upi-icon ck-icon-gpay">
                            <svg viewBox="0 0 48 48" width="28" height="28"><circle cx="24" cy="24" r="24" fill="#fff" stroke="#E0E0E0" stroke-width="1"/><text x="14" y="30" font-family="sans-serif" font-size="16" font-weight="700" fill="#4285F4">G</text><text x="24" y="30" font-family="sans-serif" font-size="12" font-weight="600" fill="#5F6368">Pay</text></svg>
                        </div>
                        <?php elseif ($a['id'] === 'paytm'): ?>
                        <div class="ck-upi-icon ck-icon-paytm">
                            <svg viewBox="0 0 48 48" width="28" height="28"><circle cx="24" cy="24" r="24" fill="#002970"/><text x="24" y="30" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="800" fill="white">Paytm</text></svg>
                        </div>
                        <?php else: ?>
                        <div class="ck-upi-icon ck-icon-qr">
                            <svg viewBox="0 0 48 48" width="28" height="28"><circle cx="24" cy="24" r="24" fill="#1A1A1A"/><text x="24" y="22" text-anchor="middle" font-family="sans-serif" font-size="9" font-weight="700" fill="white">UPI</text><text x="24" y="34" text-anchor="middle" font-family="sans-serif" font-size="7" font-weight="600" fill="#AAA">QR</text></svg>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="ck-upi-text">
                        <span class="ck-upi-name"><?php echo htmlspecialchars($a['name']); ?></span>
                        <span class="ck-upi-desc"><?php echo htmlspecialchars($a['desc']); ?></span>
                    </div>
                    <div class="ck-upi-right">
                        <?php if (!empty($a['badge'])): ?>
                        <span class="ck-upi-badge"><?php echo htmlspecialchars($a['badge']); ?></span>
                        <?php endif; ?>
                        <div class="ck-upi-chevron">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,18 15,12 9,6"/></svg>
                        </div>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- QR Inline Section (dynamic only) -->
        <div class="ck-qr-inline" id="ckQrInline" style="display:none">
            <div class="ck-qr-inline-card">
                <div class="ck-qr-inline-header">
                    <div class="ck-qr-inline-title-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/><line x1="21" y1="14" x2="21" y2="14.01"/><line x1="14" y1="21" x2="14" y2="21.01"/><line x1="21" y1="21" x2="21" y2="21.01"/></svg>
                        <span class="ck-qr-inline-title">Scan & Pay</span>
                    </div>
                    <button class="ck-qr-inline-close" id="ckQrCloseBtn" onclick="CheckoutScreen.closeQR()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18"/><path d="M6 6l18 18"/></svg>
                    </button>
                </div>

                <!-- QR Display Area -->
                <div class="ck-qr-display" id="ckQrDisplay">
                    <div class="ck-qr-amount-badge">
                        <span>Amount:</span>
                        <strong id="ckQrAmount">—</strong>
                    </div>
                    <div class="ck-qr-code-wrap" id="ckQrCode"></div>
                    <div class="ck-qr-detail"><span class="ck-qr-label">Pay to:</span> <span id="ckQrMerchant">—</span></div>
                    <div class="ck-qr-detail"><span class="ck-qr-label">Order:</span> <span id="ckQrOrder">—</span></div>
                    <button class="ck-qr-paid-btn" id="ckQrPaidBtn" onclick="CheckoutScreen.qrPaid()">I Have Paid — Verify</button>
                </div>

                <!-- Countdown Timer -->
                <div class="ck-qr-timer-wrap" id="ckQrTimerWrap">
                    <div class="ck-qr-timer-bar">
                        <div class="ck-qr-timer-fill" id="ckQrTimerFill"></div>
                    </div>
                    <div class="ck-qr-timer-text">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                        <span>Timer: <strong id="ckQrTimerValue">--:--</strong></span>
                    </div>
                </div>

                <!-- Expired Overlay -->
                <div class="ck-qr-expired" id="ckQrExpired" style="display:none">
                    <div class="ck-qr-expired-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                    </div>
                    <p class="ck-qr-expired-text">QR Code Expired</p>
                    <p class="ck-qr-expired-sub">This QR is no longer valid.</p>
                    <button class="ck-qr-regenerate-btn" id="ckQrRegenBtn" onclick="CheckoutScreen.regenerateQR()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="23,4 23,10 17,10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Generate New QR
                    </button>
                </div>

                <p class="ck-qr-hint">Scan this QR using any UPI app</p>
            </div>
        </div>

        <!-- Security Badge -->
        <div class="ck-card ck-security slide-up" style="animation-delay:0.16s">
            <div class="ck-sec-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9,12 11,14 15,10" stroke="#7C3AED" stroke-width="2"/></svg>
            </div>
            <div class="ck-sec-text">
                <strong><?php echo htmlspecialchars($ck['security_text']); ?></strong>
                <span><?php echo htmlspecialchars($ck['security_sub']); ?></span>
            </div>
        </div>

        <div style="height:24px"></div>
    </div>

    <!-- App Not Installed Fallback Modal -->
    <div class="ck-fallback-modal" id="ckFallbackModal">
        <div class="ck-fallback-card">
            <div class="ck-fallback-icon" id="ckFallbackIcon"></div>
            <h3 class="ck-fallback-title" id="ckFallbackTitle">App Not Available</h3>
            <p class="ck-fallback-msg" id="ckFallbackMsg">This UPI app is not available on this device.</p>
            <p class="ck-fallback-sub">Please try another UPI option below:</p>
            <div class="ck-fallback-options" id="ckFallbackOptions"></div>
            <button class="ck-fallback-close" onclick="CheckoutScreen.closeFallback()">Cancel</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function(){
var CK_APPS = <?php echo json_encode(array_values($apps)); ?>;

function isAndroid(){ return /Android/i.test(navigator.userAgent||''); }
function isIOS(){ return /iPhone|iPad|iPod/i.test(navigator.userAgent||''); }

window.CheckoutScreen = {
    plan:null, serverAmount:0, serverTxn:null, serverUpi:null,
    _appOpened:false, _fallbackTimer:null, _lastApp:null,
    qrInstance:null, _qrTimer:null, _qrRemaining:0, _qrExpiry:0, _qrTxnId:null, _qrTotal:300,
    _planId:0,

    init:function(data){
        data = data||{};
        var self=this;
        this.closeQR(true);
        this._appOpened=false;
        this._planId = parseInt(data.plan_id||'',10) || parseInt((data.plan&&data.plan.id)||'',10) || 0;
        this.plan = null; this.serverAmount = 0;
        // Resolve authoritative session from backend (never trust frontend amount)
        fetch('api/recharge-session.php')
        .then(function(r){ return r.json(); })
        .then(function(sess){
            var s = (sess&&sess.data)||{};
            var pid = self._planId || parseInt(s.plan_id||'0',10);
            if(!pid || !s.mobile){ self.showSummaryError('Session expired. Please start again from Home.'); return; }
            self._planId = pid;
            App.setState({mobileNumber:s.mobile, selectedOperator:s.operator, selectedPlan:s.plan});
            self.plan = s.plan; self.serverAmount = parseFloat(s.amount||0);
            self.renderSummary(s);
        })
        .catch(function(){ self.showSummaryError('Network error. Please check your connection and retry.'); });

        var onVis = function(){ if(self._appOpened){ self._onReturnFromApp(); } };
        window.addEventListener('pageshow', onVis);
        window.addEventListener('focus', onVis);
    },

    renderSummary:function(s){
        var p = s.plan||{};
        var names={jio:'JIO',airtel:'Airtel',vi:'Vi',bsnl:'BSNL'};
        var cols={jio:'#0A3D91',airtel:'#ED1C24',vi:'#E60000',bsnl:'#00A651'};
        var short={jio:'Jio',airtel:'A',vi:'Vi',bsnl:'BSNL'};
        var op=(s.operator||'jio').toLowerCase();
        var mob=s.mobile||'';
        document.getElementById('ckNumber').textContent='+91 '+App.formatMobile(mob);
        var logo=document.getElementById('ckOperatorLogo'); if(logo) logo.style.background=cols[op]||cols.jio;
        var letter=document.getElementById('ckOpLetter'); if(letter) letter.textContent=short[op]||'J';
        document.getElementById('ckSumLoading').style.display='none';
        document.getElementById('ckSumError').style.display='none';
        document.getElementById('ckSumRows').style.display='';
        document.getElementById('ckSumOperator').textContent=names[op]||op;
        document.getElementById('ckSumMobile').textContent='+91 '+App.formatMobile(mob);
        document.getElementById('ckSumPlan').textContent=(p.data||'')+' • '+(p.calls||'');
        document.getElementById('ckSumValidity').textContent=p.validity||'—';
        document.getElementById('ckSumAmount').textContent='₹'+s.amount;
    },

    showSummaryError:function(msg){
        document.getElementById('ckSumLoading').style.display='none';
        var e=document.getElementById('ckSumError');
        e.style.display=''; e.textContent=msg;
        App.showToast(msg,'error');
    },

    createServerTransaction:function(app){
        var st=App.getState();
        return fetch('api/create-order.php',{method:'POST',headers:{'Content-Type':'application/json'},
            body:JSON.stringify({mobile:st.mobileNumber, plan_id:this._planId, operator:st.selectedOperator, upi_app:app})})
        .then(function(r){ return r.json().then(function(j){ return {http:r.status, body:j}; }); })
        .then(function(res){
            if(!res.body.success) throw new Error(res.body.error||'Transaction creation failed');
            return res.body.data;
        });
    },

    pay:function(app){
        var self=this;
        App.haptic('medium');
        if(!this._planId){ App.showToast('Please select a plan first','error'); return; }
        if(app==='qr'){ this.showQR(); return; }
        App.showToast('Creating secure payment request…','info');
        this.createServerTransaction(app).then(function(d){
            self.serverTxn=d; self.serverAmount=parseFloat(d.amount);
            self._appOpened=true; self._lastApp=app;
            self._startFallbackTimer(app);
            var uri=d.app_uri;
            // Platform rule: Android uses app intent; iOS/web use generic UPI link w/ fallback
            if(!isAndroid() && app!=='qr'){
                // iOS browsers block custom schemes — still attempt, fallback modal covers failure
                uri=d.upi_uri;
            }
            window.location.href=uri;
        }).catch(function(e){ App.showToast(e.message,'error'); });
    },

    _startFallbackTimer:function(app){
        var self=this;
        clearTimeout(this._fallbackTimer);
        this._fallbackTimer=setTimeout(function(){
            if(self._appOpened){ self._appOpened=false; self.showFallback(app); }
        },2500);
    },

    _onReturnFromApp:function(){
        var self=this;
        clearTimeout(this._fallbackTimer);
        if(!this._appOpened) return;
        this._appOpened=false;
        var d=this.serverTxn;
        if(!d){ return; }
        Router.navigate('payment',{transaction_id:d.transaction_id, upiApp:this._lastApp});
    },

    showFallback:function(app){
        App.haptic('light');
        var map={phonepe:'PhonePe',gpay:'Google Pay',paytm:'Paytm'};
        var appName=map[app]||app;
        document.getElementById('ckFallbackTitle').textContent=appName+' Not Available';
        document.getElementById('ckFallbackMsg').textContent=appName+' is not available on this device.';
        var colors={phonepe:'#5F259F',gpay:'#4285F4',paytm:'#00BAF2'};
        document.getElementById('ckFallbackIcon').innerHTML='<div style="width:64px;height:64px;border-radius:18px;background:'+(colors[app]||'#333')+';display:flex;align-items:center;justify-content:center;margin:0 auto 16px;"><span style="color:#fff;font-weight:800;font-size:20px">'+appName.charAt(0)+'</span></div>';
        var optDiv=document.getElementById('ckFallbackOptions');
        optDiv.innerHTML='';
        var self=this;
        CK_APPS.forEach(function(a){
            if(a.id===app||a.id==='qr') return;
            var btn=document.createElement('button');
            btn.className='ck-fallback-opt';
            btn.textContent='Pay with '+a.name;
            btn.onclick=function(){ self.closeFallback(); self.pay(a.id); };
            optDiv.appendChild(btn);
        });
        var qrBtn=document.createElement('button');
        qrBtn.className='ck-fallback-opt ck-fallback-qr';
        qrBtn.textContent='Pay via UPI QR Code';
        qrBtn.onclick=function(){ self.closeFallback(); self.showQR(); };
        optDiv.appendChild(qrBtn);
        document.getElementById('ckFallbackModal').classList.add('active');
    },

    closeFallback:function(){
        clearTimeout(this._fallbackTimer);
        document.getElementById('ckFallbackModal').classList.remove('active');
    },

    showQR:function(){
        var self=this;
        App.haptic('light');
        if(!this._planId){ App.showToast('Please select a plan first','error'); return; }
        document.getElementById('ckQrInline').style.display='block';
        document.getElementById('ckQrExpired').style.display='none';
        document.getElementById('ckQrDisplay').style.display='';
        if(document.getElementById('ckQrTimerWrap')) document.getElementById('ckQrTimerWrap').style.display='';
        document.getElementById('ckQrCode').innerHTML='<div style="padding:40px;text-align:center;color:#6B6B6B;font-size:13px;">Generating secure QR…</div>';
        // Every QR display creates a NEW backend transaction (fresh reference + expiry)
        this.createServerTransaction('qr').then(function(data){
            self.serverTxn=data; self.serverAmount=parseFloat(data.amount);
            self._qrTxnId=data.transaction_id;
            self._qrTotal=parseInt(data.expires_in,10);
            self._qrExpiry=Date.now()+(self._qrTotal*1000);
            self._qrRemaining=self._qrTotal;
            document.getElementById('ckQrAmount').textContent='₹'+data.amount;
            document.getElementById('ckQrOrder').textContent=data.transaction_id;
            document.getElementById('ckQrMerchant').textContent=data.merchant_name;
            self.renderQRCode(data);
            self.startTimer();
        }).catch(function(e){
            document.getElementById('ckQrCode').innerHTML='<div style="padding:40px;text-align:center;color:#EF4444;font-size:13px;">'+e.message+'</div>';
        });
    },

    renderQRCode:function(data){
        var c=document.getElementById('ckQrCode');
        c.innerHTML='<div style="padding:40px;text-align:center;color:#6B6B6B;font-size:13px;">Generating QR…</div>';
        // ALWAYS dynamic — never a static uploaded image
        var self=this, attempts=0;
        function draw(){
            attempts++;
            if(typeof QRCode!=='undefined'){
                try{
                    c.innerHTML='';
                    self.qrInstance=new QRCode(c,{text:data.upi_uri,width:200,height:200,colorDark:'#1A1A1A',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.M});
                    return;
                }catch(e){ /* fall through to retry/fallback */ }
            }
            if(attempts<3){
                self.ensureQrLib(function(){ setTimeout(draw, 400); });
            } else {
                // Last-resort fallback: show tappable UPI link + copy so payment is never blocked
                c.innerHTML='';
                var msg=document.createElement('div');
                msg.style.cssText='padding:16px;text-align:center;font-size:12.5px;color:#6B6B6B;max-width:220px;';
                msg.innerHTML='QR library failed to load.<br><a href="'+data.upi_uri.replace(/"/g,'&quot;')+'" style="color:#7C3AED;font-weight:700;">Tap here to pay ₹'+data.amount+'</a><br><button id="ckCopyUri" style="margin-top:10px;padding:10px 16px;border:1.5px solid #E5E5E5;border-radius:10px;background:#F8F8F8;font-family:Poppins,sans-serif;font-size:12px;font-weight:700;color:#333;cursor:pointer;">Copy UPI Link</button>';
                c.appendChild(msg);
                var btn=document.getElementById('ckCopyUri');
                if(btn) btn.onclick=function(){ App.copyToClipboard(data.upi_uri); };
            }
        }
        draw();
    },

    // Dynamically load QR library if the global one failed (offline CDN etc.)
    _qrLibLoading:false,
    ensureQrLib:function(cb){
        if(typeof QRCode!=='undefined' || this._qrLibLoading){ if(cb)cb(); return; }
        this._qrLibLoading=true;
        var urls=[
            'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js',
            'https://unpkg.com/qrcodejs@1.0.0/qrcode.min.js'
        ];
        var i=0;
        function next(){
            if(i>=urls.length || typeof QRCode!=='undefined'){ if(cb)cb(); return; }
            var s=document.createElement('script');
            s.src=urls[i++];
            s.onload=function(){ if(cb)cb(); };
            s.onerror=function(){ next(); };
            document.head.appendChild(s);
            setTimeout(next, 3500); // don't hang if neither load nor error fires
        }
        next();
    },

    qrPaid:function(){
        if(!this._qrTxnId){ App.showToast('Generate a QR first','error'); return; }
        Router.navigate('payment',{transaction_id:this._qrTxnId, upiApp:'qr'});
    },

    startTimer:function(){
        var self=this;
        clearInterval(this._qrTimer);
        this._updateTimerDisplay();
        this._qrTimer=setInterval(function(){
            var diff=Math.max(0,Math.ceil((self._qrExpiry-Date.now())/1000));
            self._qrRemaining=diff;
            self._updateTimerDisplay();
            if(diff<=0){
                clearInterval(self._qrTimer); self._qrTimer=null;
                self.onQRExpired();
            }
        },250);
    },

    _updateTimerDisplay:function(){
        var rem=this._qrRemaining;
        var min=Math.floor(rem/60), sec=rem%60;
        var tv=document.getElementById('ckQrTimerValue');
        if(tv) tv.textContent=(min<10?'0':'')+min+':'+(sec<10?'0':'')+sec;
        var fill=document.getElementById('ckQrTimerFill');
        if(fill){
            var total=this._qrTotal||rem||1;
            var pct=Math.max(0,(rem/total)*100);
            fill.style.width=pct+'%';
            fill.style.background=pct<20?'#EF4444':pct<50?'#F59E0B':'#7C3AED';
        }
    },

    onQRExpired:function(){
        App.haptic('heavy');
        document.getElementById('ckQrDisplay').style.display='none';
        if(document.getElementById('ckQrTimerWrap')) document.getElementById('ckQrTimerWrap').style.display='none';
        document.getElementById('ckQrExpired').style.display='';
        this.qrInstance=null;
        var self=this;
        fetch('api/verify-payment.php',{method:'POST',headers:{'Content-Type':'application/json'},
            body:JSON.stringify({transaction_id:this._qrTxnId,status:'FAILED'})}).catch(function(){});
    },

    regenerateQR:function(){
        App.haptic('medium');
        document.getElementById('ckQrExpired').style.display='none';
        document.getElementById('ckQrDisplay').style.display='';
        if(document.getElementById('ckQrTimerWrap')) document.getElementById('ckQrTimerWrap').style.display='';
        this.showQR();
    },

    closeQR:function(silent){
        clearInterval(this._qrTimer);
        this._qrTimer=null; this._qrRemaining=0; this._qrExpiry=0;
        var inline=document.getElementById('ckQrInline');
        if(inline) inline.style.display='none';
        var exp=document.getElementById('ckQrExpired'); if(exp) exp.style.display='none';
        var disp=document.getElementById('ckQrDisplay'); if(disp) disp.style.display='';
        var tw=document.getElementById('ckQrTimerWrap'); if(tw) tw.style.display='';
        var c=document.getElementById('ckQrCode'); if(c) c.innerHTML='';
        this.qrInstance=null;
    }
};
})();
</script>
