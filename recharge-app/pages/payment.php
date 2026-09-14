<!-- S007 Payment Verify Screen (server-verified — no fake success) -->
<div class="payment-screen">
    <!-- Header -->
    <header class="payment-header">
        <button class="payment-back" onclick="PaymentScreen.cancel()" aria-label="Go back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15,18 9,12 15,6"/>
            </svg>
        </button>
        <h1 class="payment-header-title">Payment</h1>
    </header>

    <!-- Content -->
    <div class="payment-content">
        <!-- Animation — UPI -->
        <div class="payment-animation">
            <div class="payment-circle">
                <svg class="payment-icon" viewBox="0 0 64 64" fill="none">
                    <rect width="64" height="64" rx="16" fill="white" stroke="#EDE9FF"/>
                    <text x="32" y="36" text-anchor="middle" font-family="Poppins" font-size="18" font-weight="800" fill="#5F259F">UPI</text>
                    <text x="32" y="48" text-anchor="middle" font-family="Poppins" font-size="7" font-weight="600" fill="#7B3FA0" id="payAppLabel">PhonePe</text>
                </svg>
            </div>
            <div class="payment-ring"></div>
            <div class="payment-ring"></div>
            <div class="payment-upi-badge" id="payUpiBadge"><svg width="12" height="12" viewBox="0 0 24 24" fill="white"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/></svg> Secure</div>
        </div>

        <!-- Info -->
        <div class="payment-info">
            <h2 class="payment-title" id="paymentTitle">Verifying Payment</h2>
            <p class="payment-subtitle" id="paymentSubtitle">Waiting for backend confirmation — do not close this screen</p>
            <div class="payment-amount">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span class="payment-amount-value" id="paymentAmount">₹—</span>
            </div>
        </div>

        <!-- Order ID -->
        <div style="text-align:center;margin-bottom:16px">
            <span style="font-size:11px;color:#6B6B6B">Transaction ID:</span>
            <span style="font-size:12px;font-weight:700;color:#5F259F;margin-left:4px" id="payOrderId">-</span>
        </div>

        <!-- Progress Bar -->
        <div class="payment-progress">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-text">
                <span id="progressPercent">0%</span>
                <span id="progressTime">Verifying…</span>
            </div>
        </div>

        <!-- Steps -->
        <div class="payment-steps">
            <div class="payment-step active" id="payStep1">
                <div class="payment-step-dot">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                </div>
                <span class="payment-step-text">Payment initiated</span>
            </div>
            <div class="payment-step" id="payStep2">
                <div class="payment-step-dot">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                </div>
                <span class="payment-step-text">Waiting for UPI confirmation</span>
            </div>
            <div class="payment-step" id="payStep3">
                <div class="payment-step-dot">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                </div>
                <span class="payment-step-text">Backend verification</span>
            </div>
            <div class="payment-step" id="payStep4">
                <div class="payment-step-dot">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                </div>
                <span class="payment-step-text">Recharge confirmation</span>
            </div>
        </div>

        <!-- UTR form: user pastes 12-digit UPI reference after paying -->
        <div id="payUtrWrap" style="margin:16px 0;display:none">
            <div style="background:rgba(255,255,255,.75);border:1px solid #EDE9FF;border-radius:16px;padding:16px">
                <label style="font-size:13px;font-weight:700;color:#1A1A1A">UPI Reference / UTR <span style="color:#999;font-weight:500">(optional, speeds up verification)</span></label>
                <div style="display:flex;gap:8px;margin-top:10px">
                    <input id="payUtrInput" type="text" inputmode="text" placeholder="e.g. 423456789012" style="flex:1;padding:12px 14px;border:1.5px solid #E5E5E5;border-radius:12px;font-size:14px;font-family:Poppins,sans-serif" maxlength="32">
                    <button onclick="PaymentScreen.submitUtr()" style="padding:12px 18px;background:linear-gradient(135deg,#5F259F,#7B3FA0);color:#fff;border:none;border-radius:12px;font-family:Poppins,sans-serif;font-size:13px;font-weight:700;cursor:pointer">Submit</button>
                </div>
                <p style="font-size:11.5px;color:#6B6B6B;margin-top:8px">Find it in your UPI app payment history after paying.</p>
            </div>
        </div>

        <!-- Cancel -->
        <div class="payment-cancel">
            <button class="payment-cancel-btn" onclick="PaymentScreen.cancel()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Cancel Payment
            </button>
        </div>
    </div>
</div>

<!-- Payment Screen Script -->
<script>
const PaymentScreen = {
    steps: ['payStep1', 'payStep2', 'payStep3', 'payStep4'],
    txnId: null,
    upiApp: null,
    plan: null,
    _pollTimer: null,
    _pollCount: 0,

    init: function(data) {
        data = data||{};
        this.txnId = data.transaction_id || data.orderId || null;
        this.upiApp = data.upiApp || 'upi';
        this.plan = data.plan || App.getState().selectedPlan || null;
        this._pollCount = 0;

        var appNames = { phonepe: 'PhonePe', gpay: 'Google Pay', paytm: 'Paytm', qr: 'UPI' };
        var titleEl = document.getElementById('paymentTitle');
        var appLabelEl = document.getElementById('payAppLabel');
        var badgeEl = document.getElementById('payUpiBadge');
        var orderIdEl = document.getElementById('payOrderId');
        if (titleEl) titleEl.textContent = 'Paying via ' + (appNames[this.upiApp] || 'UPI');
        if (appLabelEl) appLabelEl.textContent = appNames[this.upiApp] || 'UPI';
        if (badgeEl) badgeEl.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="white"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/></svg> ' + (appNames[this.upiApp] || 'UPI') + ' Secure';
        if (orderIdEl) orderIdEl.textContent = this.txnId || '-';

        if (!this.txnId) {
            App.showToast('No transaction found. Please start again.','error');
            Router.navigate('checkout',{});
            return;
        }

        App.keepWake(true);
        this.markStep(0);
        this.updateProgress(15, 'Payment request created…');
        // Report PENDING (never SUCCESS from frontend)
        this.reportPending();
        this.startPolling();
        var uw = document.getElementById('payUtrWrap');
        if (uw) uw.style.display = '';
    },

    reportPending: function(){
        var self=this;
        fetch('api/verify-payment.php',{method:'POST',headers:{'Content-Type':'application/json'},
            body:JSON.stringify({transaction_id:this.txnId,status:'PENDING'})}).catch(function(){});
    },

    submitUtr: function(){
        var inp = document.getElementById('payUtrInput');
        var utr = inp ? inp.value.trim() : '';
        if(!utr){ App.showToast('Enter your UPI reference','error'); return; }
        var self=this;
        fetch('api/verify-payment.php',{method:'POST',headers:{'Content-Type':'application/json'},
            body:JSON.stringify({transaction_id:this.txnId,status:'PENDING',upi_reference:utr})})
        .then(function(r){ return r.json(); })
        .then(function(j){
            if(j.success){ App.showToast('Reference submitted — verifying…','success'); self.pollOnce(); }
            else App.showToast(j.error||'Submit failed','error');
        })
        .catch(function(){ App.showToast('Network error','error'); });
    },

    startPolling: function(){
        var self=this;
        clearInterval(this._pollTimer);
        this._pollTimer = setInterval(function(){ self.pollOnce(); }, 3000);
        this.pollOnce();
    },

    pollOnce: function(){
        var self=this;
        this._pollCount++;
        fetch('api/transaction-status.php?transaction_id='+encodeURIComponent(this.txnId))
        .then(function(r){ return r.json(); })
        .then(function(j){
            if(!j.success){ self.onFailed(j.error||'Transaction not found'); return; }
            var d=j.data;
            var amtEl=document.getElementById('paymentAmount');
            if(amtEl) amtEl.textContent='₹'+d.amount;
            if(d.status==='SUCCESS'){ self.onSuccess(d); }
            else if(d.status==='FAILED'||d.status==='CANCELLED'){ self.onFailed('Your payment could not be verified.'); }
            else if(d.status==='EXPIRED'){ self.onExpired(); }
            else {
                var pct=Math.min(90, 20+self._pollCount*5);
                self.updateProgress(pct, d.status==='PENDING' ? 'Payment received — verifying with provider…' : 'Waiting for UPI confirmation…');
                if(self._pollCount===2) self.markStep(1);
                if(self._pollCount===4) self.markStep(2);
            }
        })
        .catch(function(){ self.updateProgress(30,'Network issue — retrying…'); });
    },

    markStep: function(i){
        for(var k=0;k<this.steps.length;k++){
            var el=document.getElementById(this.steps[k]);
            if(!el) continue;
            el.classList.remove('active','completed');
            if(k<i) el.classList.add('completed');
            else if(k===i) el.classList.add('active');
        }
    },

    updateProgress: function(percent, text) {
        const fillEl = document.getElementById('progressFill');
        const percentEl = document.getElementById('progressPercent');
        const timeEl = document.getElementById('progressTime');
        const subtitleEl = document.getElementById('paymentSubtitle');
        if (fillEl) fillEl.style.width = percent + '%';
        if (percentEl) percentEl.textContent = percent + '%';
        if (timeEl) timeEl.textContent = text;
        if (subtitleEl) subtitleEl.textContent = text;
    },

    onSuccess: function(d){
        clearInterval(this._pollTimer);
        this.updateProgress(100, 'Payment verified!');
        this.markStep(4);
        App.keepWake(false);
        var self=this;
        setTimeout(function(){
            Router.navigate('success', {
                plan: self.plan || {amount:d.amount},
                orderId: d.transaction_id,
                transactionId: d.transaction_id,
                upiApp: self.upiApp,
                verified: true
            });
        }, 600);
    },

    onFailed: function(msg){
        clearInterval(this._pollTimer);
        App.keepWake(false);
        Router.navigate('failed', {
            plan: this.plan,
            orderId: this.txnId,
            error: msg
        });
    },

    onExpired: function(){
        clearInterval(this._pollTimer);
        App.keepWake(false);
        Router.navigate('failed', {
            plan: this.plan,
            orderId: this.txnId,
            error: 'Payment session expired. Please create a new payment request.'
        });
    },

    cancel: function() {
        var self=this;
        App.haptic('light');
        App.keepWake(false);
        clearInterval(this._pollTimer);
        if (this.txnId) {
            fetch('api/verify-payment.php',{method:'POST',headers:{'Content-Type':'application/json'},
                body:JSON.stringify({transaction_id:this.txnId,status:'CANCELLED'})}).catch(function(){});
        }
        Router.navigate('home');
    }
};
</script>
