/**
 * Router — Premium SPA Navigation
 * With page transition loader, haptic feedback, and smooth animations
 */

const Router = {
    screens: {
        'splash': { file: 'pages/splash.php', css: 'assets/css/splash.css' },
        'home': { file: 'pages/home.php', css: 'assets/css/home.css' },
        'verify': { file: 'pages/verify.php', css: 'assets/css/verify.css' },
        'plans': { file: 'pages/plans.php', css: 'assets/css/plans.css' },
        'checkout': { file: 'pages/checkout.php', css: 'assets/css/checkout.css' },
        'payment': { file: 'pages/payment.php', css: 'assets/css/payment.css' },
        'success': { file: 'pages/success.php', css: 'assets/css/success.css' },
        'failed': { file: 'pages/failed.php', css: 'assets/css/failed.css' },
    },

    history: [],
    currentScreen: null,
    _loading: false,
    _loaderEl: null,
    _progressEl: null,
    _progressPct: 0,
    _progressTimer: null,

    navigate: function(screenId, data) {
        if (this._loading) return;
        data = data || {};

        var screen = this.screens[screenId];
        if (!screen) {
            console.error('Screen not found:', screenId);
            return;
        }

        if (this.currentScreen) {
            this.history.push(this.currentScreen);
        }

        this.currentScreen = screenId;
        App.setState({ currentScreen: screenId });
        this.loadScreen(screen, screenId, data);
    },

    loadScreen: async function(screen, screenId, data) {
        var self = this;
        var app = document.getElementById('app');

        this._loading = true;
        this.showPageLoader();

        try {
            var response = await fetch(screen.file);
            var html = await response.text();

            this.loadCSS(screen.css);

            await this.simulateProgress(200 + Math.random() * 300);

            var scriptContents = [];
            var scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/gi;
            var match;
            while ((match = scriptRegex.exec(html)) !== null) {
                scriptContents.push(match[1]);
            }
            var cleanHtml = html.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');

            app.innerHTML = cleanHtml;
            app.classList.remove('screen-transitioning');
            app.classList.add('screen-enter');

            this.hidePageLoader();

            scriptContents.forEach(function(script) {
                var el = document.createElement('script');
                el.textContent = script;
                document.body.appendChild(el);
                document.body.removeChild(el);
            });

            this.initScreenScripts(screenId, data);
            this._loading = false;

            setTimeout(function() {
                app.classList.remove('screen-enter');
            }, 350);

        } catch (error) {
            console.error('Error loading screen:', error);
            this.hidePageLoader();
            app.innerHTML = this.getErrorHTML();
            app.classList.remove('screen-transitioning');
            this._loading = false;
        }
    },

    showPageLoader: function() {
        var loader = document.getElementById('page-transition-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'page-transition-loader';
            loader.innerHTML = '<div class="ptl-bar"><div class="ptl-fill"></div></div><div class="ptl-dots"><span></span><span></span><span></span></div>';
            document.body.appendChild(loader);
        }
        var fill = loader.querySelector('.ptl-fill');
        if (fill) fill.style.width = '0%';
        loader.classList.add('active');
        this._loaderEl = loader;
        this._progressEl = loader.querySelector('.ptl-fill');
        this._progressPct = 0;
    },

    hidePageLoader: function() {
        var self = this;
        var loader = this._loaderEl;
        if (!loader) return;

        if (this._progressEl) this._progressEl.style.width = '100%';

        setTimeout(function() {
            loader.classList.remove('active');
            var fill = loader.querySelector('.ptl-fill');
            if (fill) {
                setTimeout(function() { fill.style.width = '0%'; }, 400);
            }
        }, 150);
    },

    simulateProgress: function(duration) {
        var self = this;
        var fill = this._progressEl;
        if (!fill) return Promise.resolve();

        return new Promise(function(resolve) {
            var steps = [
                { t: 0, w: '15%' },
                { t: duration * 0.2, w: '40%' },
                { t: duration * 0.5, w: '65%' },
                { t: duration * 0.8, w: '85%' },
                { t: duration, w: '95%' },
            ];
            steps.forEach(function(s) {
                setTimeout(function() {
                    if (fill) fill.style.width = s.w;
                }, s.t);
            });
            setTimeout(resolve, duration);
        });
    },

    loadCSS: function(cssPath) {
        var existing = document.querySelector('link[href="' + cssPath + '"]');
        if (existing) return;
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = cssPath;
        document.head.appendChild(link);
    },

    initScreenScripts: function(screenId, data) {
        switch(screenId) {
            case 'home':
                if (typeof HomeScreen !== 'undefined') HomeScreen.init(data);
                break;
            case 'verify':
                if (typeof VerifyScreen !== 'undefined') VerifyScreen.init(data);
                break;
            case 'plans':
                if (typeof PlansScreen !== 'undefined') PlansScreen.init(data);
                break;
            case 'checkout':
                if (typeof CheckoutScreen !== 'undefined') CheckoutScreen.init(data);
                break;
            case 'payment':
                if (typeof PaymentScreen !== 'undefined') PaymentScreen.init(data);
                break;
            case 'success':
                if (typeof SuccessScreen !== 'undefined') SuccessScreen.init(data);
                break;
            case 'failed':
                if (typeof FailedScreen !== 'undefined') FailedScreen.init(data);
                break;
        }
    },

    goBack: function() {
        if (this.history.length > 0) {
            var previousScreen = this.history.pop();
            this.currentScreen = previousScreen;
            this.loadScreen(this.screens[previousScreen], previousScreen, {});
        } else {
            this.navigate('home');
        }
    },

    replace: function(screenId, data) {
        data = data || {};
        var screen = this.screens[screenId];
        if (!screen) {
            console.error('Screen not found:', screenId);
            return;
        }
        this.currentScreen = screenId;
        App.setState({ currentScreen: screenId });
        this.loadScreen(screen, screenId, data);
    },

    getErrorHTML: function() {
        return '<div class="screen"><div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:80vh;text-align:center;padding:20px;"><div style="width:72px;height:72px;border-radius:20px;background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center;margin-bottom:20px;"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><h2 style="font-size:20px;font-weight:700;color:#141414;margin-bottom:8px;">Something went wrong</h2><p style="font-size:14px;color:#6B6B6B;margin-bottom:24px;">Please try again later</p><button onclick="Router.navigate(\'home\')" style="display:inline-flex;align-items:center;gap:8px;padding:14px 32px;background:linear-gradient(135deg,#5F259F,#7B3FA0);color:white;font-family:Poppins,sans-serif;font-size:15px;font-weight:600;border:none;border-radius:20px;cursor:pointer;box-shadow:0 4px 16px rgba(95,37,159,0.3);">Go Home</button></div></div>';
    },

    canGoBack: function() {
        return this.history.length > 0;
    }
};

window.Router = Router;
