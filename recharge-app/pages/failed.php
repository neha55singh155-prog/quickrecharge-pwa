<!-- S009 Failed Screen -->
<div class="failed-screen">
    <!-- Content -->
    <div class="failed-content">
        <!-- Animation -->
        <div class="failed-animation">
            <div class="failed-circle">
                <svg class="failed-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="failed-rings">
                <div class="failed-ring"></div>
                <div class="failed-ring"></div>
            </div>
        </div>

        <!-- Info -->
        <div class="failed-info">
            <h1 class="failed-title">Payment Failed</h1>
            <p class="failed-subtitle">We couldn't process your payment</p>
        </div>

        <!-- Details Card -->
        <div class="failed-card">
            <div class="failed-card-row">
                <span class="failed-card-label">Mobile Number</span>
                <span class="failed-card-value" id="failedMobile">+91 98765 43210</span>
            </div>
            <div class="failed-card-row">
                <span class="failed-card-label">Operator</span>
                <span class="failed-card-value" id="failedOperator">Jio Prepaid</span>
            </div>
            <div class="failed-card-row">
                <span class="failed-card-label">Plan</span>
                <span class="failed-card-value" id="failedPlan">2GB/day Unlimited</span>
            </div>
            <div class="failed-card-row">
                <span class="failed-card-label">Amount</span>
                <span class="failed-card-value amount" id="failedAmount">₹299</span>
            </div>
        </div>

        <!-- Error Box -->
        <div class="failed-error-box">
            <svg class="failed-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="failed-error-text">
                <div class="failed-error-title">Error Details</div>
                <div class="failed-error-message" id="failedError">Payment declined by your bank. Please check your account balance or try a different payment method.</div>
            </div>
        </div>

        <!-- Help -->
        <p class="failed-help">
            Need help? <a href="#" onclick="FailedScreen.contactSupport()">Contact Support</a>
        </p>
    </div>

    <!-- Actions -->
    <div class="failed-actions">
        <button class="failed-btn-primary" onclick="FailedScreen.retry()">
            Try Again
        </button>
        <button class="failed-btn-secondary" onclick="FailedScreen.goHome()">
            Go to Home
        </button>
    </div>
</div>

<!-- Failed Screen Script -->
<script>
const FailedScreen = {
    init: function(data) {
        // Release wake lock
        App.keepWake(false);

        // Display details
        this.displayDetails(data);
    },
    
    displayDetails: function(data) {
        const state = App.getState();
        const operatorNames = {
            'jio': 'Jio Prepaid',
            'airtel': 'Airtel Prepaid',
            'vi': 'Vi Prepaid',
            'bsnl': 'BSNL Prepaid'
        };
        
        const mobileEl = document.getElementById('failedMobile');
        const operatorEl = document.getElementById('failedOperator');
        const planEl = document.getElementById('failedPlan');
        const amountEl = document.getElementById('failedAmount');
        const errorEl = document.getElementById('failedError');
        
        if (mobileEl) mobileEl.textContent = '+91 ' + App.formatMobile(state.mobileNumber || '9876543210');
        if (operatorEl) operatorEl.textContent = operatorNames[state.selectedOperator] || 'Jio Prepaid';
        if (planEl && data.plan) planEl.textContent = `${data.plan.data} Unlimited`;
        if (amountEl && data.plan) amountEl.textContent = `₹${data.plan.amount}`;
        if (errorEl && data.error) errorEl.textContent = data.error;
        
        App.haptic('error');
    },
    
    retry: function() {
        App.haptic('light');
        Router.navigate('checkout', {
            plan: App.getState().selectedPlan,
            mobile: App.getState().mobileNumber,
            operator: App.getState().selectedOperator
        });
    },
    
    goHome: function() {
        App.haptic('light');
        Router.navigate('home');
    },
    
    contactSupport: function() {
        App.haptic('light');
        App.showToast('Support: support@rechargeapp.com', 'info');
    }
};
</script>
