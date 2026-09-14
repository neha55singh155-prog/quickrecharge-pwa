<!-- S008 Success Screen -->
<div class="success-screen">
    <!-- Confetti Container -->
    <div class="confetti-container" id="confettiContainer"></div>

    <!-- Content -->
    <div class="success-content">
        <!-- Animation -->
        <div class="success-animation">
            <div class="success-circle">
                <svg class="success-checkmark" id="successCheck" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
            </div>
            <div class="success-rings">
                <div class="success-ring"></div>
                <div class="success-ring"></div>
                <div class="success-ring"></div>
            </div>
        </div>

        <!-- Info -->
        <div class="success-info">
            <h1 class="success-title">Recharge Successful!</h1>
            <p class="success-subtitle">Your mobile has been recharged successfully</p>
        </div>

        <!-- Details Card -->
        <div class="success-card">
            <div class="success-card-row">
                <span class="success-card-label">Mobile Number</span>
                <span class="success-card-value" id="successMobile">+91 98765 43210</span>
            </div>
            <div class="success-card-row">
                <span class="success-card-label">Operator</span>
                <span class="success-card-value" id="successOperator">Jio Prepaid</span>
            </div>
            <div class="success-card-row">
                <span class="success-card-label">Plan</span>
                <span class="success-card-value" id="successPlan">2GB/day Unlimited</span>
            </div>
            <div class="success-card-row">
                <span class="success-card-label">Amount</span>
                <span class="success-card-value amount" id="successAmount">₹299</span>
            </div>
            <div class="success-card-row">
                <span class="success-card-label">Order ID</span>
                <span class="success-card-value" id="successOrderId">ORD000000</span>
            </div>
            <div class="success-card-row">
                <span class="success-card-label">Transaction ID</span>
                <span class="success-card-value" id="successTxnId">TXN123456789</span>
            </div>
            <div class="success-card-row">
                <span class="success-card-label">Status</span>
                <span class="success-card-value success">Completed</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="success-actions">
        <button class="success-btn-primary" onclick="SuccessScreen.goHome()">
            Done
        </button>
        <button class="success-btn-secondary" onclick="SuccessScreen.rechargeAgain()">
            Recharge Again
        </button>
    </div>
</div>

<!-- Success Screen Script -->
<script>
const SuccessScreen = {
    init: function(data) {
        // Release wake lock
        App.keepWake(false);

        // Display details
        this.displayDetails(data);
        
        // Trigger animations
        this.startAnimations();
        
        // Create confetti
        this.createConfetti();
    },
    
    displayDetails: function(data) {
        const state = App.getState();
        const operatorNames = {
            'jio': 'Jio Prepaid',
            'airtel': 'Airtel Prepaid',
            'vi': 'Vi Prepaid',
            'bsnl': 'BSNL Prepaid'
        };
        
        const mobileEl = document.getElementById('successMobile');
        const operatorEl = document.getElementById('successOperator');
        const planEl = document.getElementById('successPlan');
        const amountEl = document.getElementById('successAmount');
        const orderIdEl = document.getElementById('successOrderId');
        const txnIdEl = document.getElementById('successTxnId');
        
        if (mobileEl) mobileEl.textContent = '+91 ' + App.formatMobile(state.mobileNumber || '9876543210');
        if (operatorEl) operatorEl.textContent = operatorNames[state.selectedOperator] || 'Jio Prepaid';
        if (planEl && data.plan) planEl.textContent = (data.plan.data||'') + (data.plan.calls === 'Unlimited' ? ' Unlimited' : '');
        if (amountEl && data.plan) amountEl.textContent = '₹' + data.plan.amount;
        if (orderIdEl && data.orderId) orderIdEl.textContent = data.orderId;
        if (txnIdEl && data.transactionId) txnIdEl.textContent = data.transactionId;
        // Only reachable after backend SUCCESS — never from a frontend claim
        if (data && data.verified === false) {
            var sub = document.querySelector('.success-subtitle');
            if (sub) sub.textContent = 'Awaiting backend verification';
        }
    },
    
    startAnimations: function() {
        // Animate checkmark
        setTimeout(() => {
            const check = document.getElementById('successCheck');
            if (check) check.classList.add('animate');
        }, 300);
        
        App.haptic('medium');
    },
    
    createConfetti: function() {
        const container = document.getElementById('confettiContainer');
        if (!container) return;
        
        const colors = ['#6C2BFF', '#8B5DFF', '#22C55E', '#F59E0B', '#EF4444'];
        
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = `${Math.random() * 100}%`;
            confetti.style.animationDelay = `${Math.random() * 0.5}s`;
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            
            if (Math.random() > 0.5) {
                confetti.style.borderRadius = '50%';
            }
            
            container.appendChild(confetti);
        }
        
        // Clean up after animation
        setTimeout(() => {
            container.innerHTML = '';
        }, 3500);
    },
    
    goHome: function() {
        App.haptic('light');
        Router.navigate('home');
    },
    
    rechargeAgain: function() {
        App.haptic('light');
        Router.navigate('home');
    }
};
</script>
