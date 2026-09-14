/**
 * App State Manager — Premium
 * Central state management for the application
 */

const App = {
    state: {
        currentScreen: 'splash',
        user: null,
        selectedOperator: null,
        selectedPlan: null,
        currentOrder: null,
        mobileNumber: '',
        isLoading: false,
    },

    elements: {
        app: null,
    },

    _toastTimer: null,

    init: function() {
        this.elements.app = document.getElementById('app');
        this.checkSession();
        this.initSplash();
        this.injectPremiumToastStyles();
    },

    checkSession: function() {
        var savedUser = localStorage.getItem('recharge_user');
        if (savedUser) {
            try { this.state.user = JSON.parse(savedUser); }
            catch (e) { localStorage.removeItem('recharge_user'); }
        }
    },

    initSplash: function() {
        Router.navigate('home');
    },

    setState: function(newState) {
        this.state = Object.assign({}, this.state, newState);
    },

    getState: function() {
        return Object.assign({}, this.state);
    },

    setUser: function(user) {
        this.state.user = user;
        localStorage.setItem('recharge_user', JSON.stringify(user));
    },

    clearUser: function() {
        this.state.user = null;
        localStorage.removeItem('recharge_user');
    },

    showLoading: function() {
        this.state.isLoading = true;
        var loader = document.getElementById('global-loader');
        if (loader) loader.classList.add('show');
    },

    hideLoading: function() {
        this.state.isLoading = false;
        var loader = document.getElementById('global-loader');
        if (loader) loader.classList.remove('show');
    },

    formatCurrency: function(amount) {
        return '\u20B9' + parseFloat(amount).toFixed(0);
    },

    formatMobile: function(mobile) {
        if (!mobile || mobile.length !== 10) return mobile;
        return mobile.slice(0, 5) + ' ' + mobile.slice(5);
    },

    validateMobile: function(mobile) {
        var cleaned = mobile.replace(/\D/g, '');
        return cleaned.length === 10 && /^[6-9]/.test(cleaned);
    },

    generateOrderId: function() {
        var ts = Date.now().toString(36);
        var rand = Math.random().toString(36).substring(2, 8);
        return (ts + rand).toUpperCase().substring(0, 12);
    },

    apiCall: async function(endpoint, data, method) {
        data = data || {};
        method = method || 'POST';
        try {
            var options = {
                method: method,
                headers: { 'Content-Type': 'application/json' },
            };
            if (method === 'POST' && Object.keys(data).length > 0) {
                options.body = JSON.stringify(data);
            }
            var response = await fetch('api/' + endpoint, options);
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { status: 'error', message: 'Network error. Please try again.' };
        }
    },

    /* ===== Premium Toast System ===== */
    injectPremiumToastStyles: function() {
        if (document.getElementById('app-toast-styles')) return;
        var style = document.createElement('style');
        style.id = 'app-toast-styles';
        style.textContent = '\
            .app-toast-container{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:99999;display:flex;flex-direction:column;gap:8px;pointer-events:none;width:calc(100% - 32px);max-width:390px;}\
            .app-toast{display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:16px;font-family:Poppins,sans-serif;font-size:13px;font-weight:600;color:white;pointer-events:auto;\
                animation:toastIn 0.35s cubic-bezier(0.175,0.885,0.32,1.275) both;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);\
                box-shadow:0 8px 32px rgba(0,0,0,0.18);opacity:0;}\
            .app-toast.show{opacity:1;}\
            .app-toast.removing{animation:toastOut 0.3s ease both;}\
            .app-toast-icon{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}\
            .app-toast-icon svg{width:18px;height:18px;}\
            .app-toast-text{flex:1;line-height:1.4;}\
            .app-toast-close{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.15);border:none;cursor:pointer;flex-shrink:0;}\
            .app-toast-close svg{width:14px;height:14px;color:white;}\
            .app-toast.success{background:linear-gradient(135deg,rgba(34,197,94,0.92),rgba(22,163,74,0.92));}\
            .app-toast.success .app-toast-icon{background:rgba(255,255,255,0.2);}\
            .app-toast.error{background:linear-gradient(135deg,rgba(239,68,68,0.92),rgba(220,38,38,0.92));}\
            .app-toast.error .app-toast-icon{background:rgba(255,255,255,0.2);}\
            .app-toast.info{background:linear-gradient(135deg,rgba(95,37,159,0.92),rgba(123,63,160,0.92));}\
            .app-toast.info .app-toast-icon{background:rgba(255,255,255,0.2);}\
            .app-toast.warning{background:linear-gradient(135deg,rgba(245,158,11,0.92),rgba(217,119,6,0.92));}\
            .app-toast.warning .app-toast-icon{background:rgba(255,255,255,0.2);}\
            @keyframes toastIn{0%{opacity:0;transform:translateY(-20px) scale(0.9);}100%{opacity:1;transform:translateY(0) scale(1);}}\
            @keyframes toastOut{0%{opacity:1;transform:translateY(0) scale(1);}100%{opacity:0;transform:translateY(-20px) scale(0.9);}}\
        ';
        document.head.appendChild(style);
    },

    showToast: function(message, type) {
        type = type || 'info';
        var self = this;

        var container = document.querySelector('.app-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'app-toast-container';
            document.body.appendChild(container);
        }

        var icons = {
            success: '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
            error: '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            info: '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
            warning: '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
        };

        var toast = document.createElement('div');
        toast.className = 'app-toast ' + type;
        toast.innerHTML = '<div class="app-toast-icon">' + (icons[type] || icons.info) + '</div><div class="app-toast-text">' + message + '</div><button class="app-toast-close" onclick="this.closest(\'.app-toast\').remove()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';

        container.appendChild(toast);
        requestAnimationFrame(function() { toast.classList.add('show'); });

        setTimeout(function() {
            toast.classList.add('removing');
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    },

    haptic: function(type) {
        if ('vibrate' in navigator) {
            switch(type) {
                case 'light': navigator.vibrate(10); break;
                case 'medium': navigator.vibrate(20); break;
                case 'heavy': navigator.vibrate(30); break;
                case 'error': navigator.vibrate([50, 30, 50]); break;
                case 'success': navigator.vibrate([10, 50, 10]); break;
            }
        }
    },

    /* ===== MOBILE APP FEATURES ===== */
    initMobile: function() {
        this.initSwipeBack();
        this.initPreventZoom();
        this.initStatusBar();
        this.initKeepWake();
        this.initOrientation();
        this.initNetworkStatus();
    },

    // Swipe from left edge to go back
    initSwipeBack: function() {
        var self = this;
        var startX = 0;
        var startY = 0;
        var swiping = false;
        var indicator = document.getElementById('swipe-back');
        var threshold = 50;

        document.addEventListener('touchstart', function(e) {
            if (e.touches[0].clientX < 20) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                swiping = true;
            }
        }, { passive: true });

        document.addEventListener('touchmove', function(e) {
            if (!swiping) return;
            var dx = e.touches[0].clientX - startX;
            var dy = Math.abs(e.touches[0].clientY - startY);

            if (dy > 30) { swiping = false; return; }

            if (dx > 0 && dx < 120) {
                if (indicator) indicator.classList.add('active');
            }
        }, { passive: true });

        document.addEventListener('touchend', function(e) {
            if (!swiping) return;
            swiping = false;
            if (indicator) indicator.classList.remove('active');

            var endX = e.changedTouches[0].clientX;
            var dx = endX - startX;

            if (dx > threshold && Router.canGoBack()) {
                self.haptic('light');
                Router.goBack();
            }
        }, { passive: true });
    },

    // Prevent pinch zoom on inputs
    initPreventZoom: function() {
        document.addEventListener('gesturestart', function(e) { e.preventDefault(); });
        document.addEventListener('gesturechange', function(e) { e.preventDefault(); });

        // Set font-size 16px on inputs to prevent iOS zoom
        var style = document.createElement('style');
        style.textContent = 'input[type="tel"], input[type="text"], input[type="number"], input[type="email"], input[type="password"], textarea, select { font-size: 16px !important; }';
        document.head.appendChild(style);
    },

    // Status bar color for Android
    initStatusBar: function() {
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            // Update theme color based on screen
            document.addEventListener('click', function() {
                var current = Router.currentScreen;
                var colors = {
                    'home': '#F5F0FF',
                    'verify': '#F5F0FF',
                    'plans': '#5F259F',
                    'plan-detail': '#F5F0FF',
                    'checkout': '#5F259F',
                    'payment': '#F5F0FF',
                    'success': '#F5F0FF',
                    'failed': '#F5F0FF',
                };
                if (colors[current]) meta.setAttribute('content', colors[current]);
            });
        }
    },

    // Keep screen on during payment
    _wakeLock: null,
    keepWake: function(on) {
        if ('wakeLock' in navigator) {
            if (on) {
                navigator.wakeLock.request('screen').then(function(lock) {
                    App._wakeLock = lock;
                }).catch(function() {});
            } else if (this._wakeLock) {
                this._wakeLock.release();
                this._wakeLock = null;
            }
        }
    },

    // Prevent rotation
    initOrientation: function() {
        if (screen.orientation && screen.orientation.lock) {
            screen.orientation.lock('portrait').catch(function() {});
        }
    },

    // Network status
    _isOnline: navigator.onLine,
    initNetworkStatus: function() {
        var self = this;
        window.addEventListener('online', function() {
            self._isOnline = true;
            self.showToast('You are back online', 'success');
        });
        window.addEventListener('offline', function() {
            self._isOnline = false;
            self.showToast('No internet connection', 'error');
        });
    },

    checkNetwork: function() {
        if (!this._isOnline) {
            this.showToast('Please check your internet connection', 'error');
            return false;
        }
        return true;
    },

    // Keep awake during payment
    initKeepWake: function() {
        // Will be called by payment screen
    },

    /* ===== PWA INSTALL ===== */
    installPWA: function() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(choice) {
                if (choice.outcome === 'accepted') {
                    App.showToast('App installed successfully!', 'success');
                }
                deferredPrompt = null;
                document.getElementById('install-banner').classList.remove('show');
            });
        }
    },

    dismissInstall: function() {
        localStorage.setItem('install_dismissed', '1');
        document.getElementById('install-banner').classList.remove('show');
    },

    /* ===== SHARE / CONTACT ===== */
    shareRecharge: function(data) {
        if (navigator.share) {
            navigator.share({
                title: 'QuickRecharge',
                text: 'I just recharged ₹' + (data ? data.amount : '') + ' on QuickRecharge!',
                url: window.location.href
            }).catch(function() {});
        }
    },

    // Copy to clipboard
    copyToClipboard: function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                App.showToast('Copied!', 'success');
            });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            App.showToast('Copied!', 'success');
        }
    }
};

window.App = App;
