<!-- S004 Select Plan — Ultra Advanced Premium V3 -->
<div class="plans-screen">
    <div class="plans-bg-orb orb-1"></div>
    <div class="plans-bg-orb orb-2"></div>
    <div class="plans-bg-orb orb-3"></div>

    <!-- Header — unified with Home -->
    <header class="plans-header">
        <button class="plans-back-btn" onclick="Router.goBack()" aria-label="Go back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15,18 9,12 15,6"/></svg>
        </button>
        <div class="plans-header-center">
            <h1 class="plans-header-title">Select Plan</h1>
            <p class="plans-header-subtitle" id="plansOperator"><span class="op-dot"></span> Jio Prepaid • 5G Ready</p>
        </div>
        <button class="plans-header-action" id="plansSearchToggle" aria-label="Search plans" onclick="PlansScreen.toggleSearch()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="M16.6 16.6L21 21"/></svg>
        </button>
        <button class="plans-header-action" aria-label="Filters" onclick="PlansScreen.toggleFilterSheet()">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.9"><path d="M4 7h16"/><path d="M8 12h8"/><path d="M11 17h2"/><circle cx="14.5" cy="7" r="2.2" fill="white" stroke="white"/><circle cx="9.5" cy="12" r="2.2" fill="white" stroke="white"/></svg>
        </button>
    </header>

    <!-- Search -->
    <div class="plans-search-wrap" id="plansSearchWrap">
        <div class="plans-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M16.6 16.6L21 21"/></svg>
            <input id="plansSearchInput" type="text" placeholder="Search ₹199, 2GB, 84 days, Hotstar..." oninput="PlansScreen.onSearch(this.value)" />
            <button class="plans-search-clear" onclick="PlansScreen.clearSearch()" aria-label="Clear search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></button>
        </div>
        <div class="plans-search-tags">
            <button onclick="PlansScreen.quickSearch('199')">₹199</button>
            <button onclick="PlansScreen.quickSearch('2GB')">2GB/day</button>
            <button onclick="PlansScreen.quickSearch('84 Days')">84 Days</button>
            <button onclick="PlansScreen.quickSearch('HOT')">🔥 HOT</button>
            <button onclick="PlansScreen.quickSearch('Unlimited')">Unlimited</button>
        </div>
    </div>

    <!-- Info Bar -->
    <div class="plans-info-bar" id="plansInfoBar">
        <div class="plans-info-left">
            <div class="plans-info-avatar" id="plansAvatar">J</div>
            <div class="plans-info-text">
                <div class="plans-info-number" id="plansNumber">+91 98765 43210</div>
                <div class="plans-info-operator" id="plansOperatorName">Jio Prepaid Plans • <span class="info-5g">5G • HD Voice</span></div>
            </div>
        </div>
        <div class="plans-info-actions">
            <span class="plans-secure-pill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/><path d="M9 12l10 0 0 0"/><polyline points="9,12 11,14 15,10" style="display:none"/></svg> Secure</span>
            <button class="plans-info-change" onclick="Router.navigate('home')">Change <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l15-6-6-6"/></svg></button>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="plans-toolbar" id="plansToolbar">
        <div class="plans-toolbar-left">
            <span class="plans-count" id="plansCount">0 plans</span>
            <span class="plans-dot"></span>
            <span class="plans-offers"><svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.9"><path d="M20 12V8H6a2 2 0 0 1 2-2h12v4Z"/><path d="M20 12v4H6a2 2 0 0 0 2 2h12v-4Z"/><path d="M12 8v8"/></svg> Cashback</span>
        </div>
        <div class="plans-toolbar-right">
            <div class="plans-sort-wrap">
                <select id="plansSort" class="plans-sort-select" onchange="PlansScreen.onSort(this.value)">
                    <option value="recommended">Recommended</option>
                    <option value="price_low">Price: Low → High</option>
                    <option value="price_high">Price: High → Low</option>
                    <option value="validity">Validity: Long → Short</option>
                    <option value="data">Data: High → Low</option>
                </select>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="plans-view-toggle" role="group" aria-label="View toggle">
                <button class="active" data-view="list" onclick="PlansScreen.setView('list')" aria-label="List view"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M8 6h12"/><path d="M8 12h12"/><path d="M8 18h12"/><circle cx="4" cy="6" r="1.6" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1.6" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1.6" fill="currentColor" stroke="none"/></svg></button>
                <button data-view="grid" onclick="PlansScreen.setView('grid')" aria-label="Grid view"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg></button>
            </div>
        </div>
    </div>

    <!-- Sticky Category + Chips wrapper -->
    <div class="plans-sticky-wrap" id="plansSticky">
        <div class="plans-categories" id="planCategories">
            <button class="plan-category-tab active" data-category="popular" onclick="PlansScreen.filterCategory('popular')">
                <span class="tab-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.8 5.7 6.2.9-4.5 4.3 1 6.2L12 15.7l-5.5 2.9 1-6.2L3 8.6l6.2-.9L12 2Z"/></svg></span>
                Popular <span class="tab-count" data-count="popular">0</span>
            </button>
            <button class="plan-category-tab" data-category="unlimited" onclick="PlansScreen.filterCategory('unlimited')">
                <span class="tab-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M13 2L4 14h7l-1 8 9-12h-7l1-8Z"/></svg></span>
                Unlimited <span class="tab-count" data-count="unlimited">0</span>
            </button>
            <button class="plan-category-tab" data-category="data" onclick="PlansScreen.filterCategory('data')">
                <span class="tab-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><ellipse cx="12" cy="8" rx="7" ry="4"/><path d="M5 8v5c0 2.2 3.1 4 7 4s7-1.8 7-4V8"/><path d="M5 12c0 2.2 3.1 4 7 4s7-1.8 7-4"/></svg></span>
                Data <span class="tab-count" data-count="data">0</span>
            </button>
            <button class="plan-category-tab" data-category="talktime" onclick="PlansScreen.filterCategory('talktime')">
                <span class="tab-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M22 16.9v2a2 2 0 0 1-2.1 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.2 3.1 2 2 0 0 1 4.1 1h2a2 2 0 0 1 2 1.7c.2.9.5 1.9.8 2.8a2 2 0 0 1-.5 2.1L8 8.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.8.8A2 2 0 0 1 22 16.9Z"/></svg></span>
                Talktime <span class="tab-count" data-count="talktime">0</span>
            </button>
            <button class="plan-category-tab" data-category="all" onclick="PlansScreen.filterCategory('all')">
                <span class="tab-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.4"/><rect x="14" y="3" width="7" height="7" rx="1.4"/><rect x="14" y="14" width="7" height="7" rx="1.4"/><rect x="3" y="14" width="7" height="7" rx="1.4"/></svg></span>
                All <span class="tab-count" data-count="all">0</span>
            </button>
        </div>
        <div class="plans-chips" id="plansChips">
            <button class="chip active" data-chip="all" onclick="PlansScreen.filterPrice('all')">All</button>
            <button class="chip" data-chip="under199" onclick="PlansScreen.filterPrice('under199')">Under ₹199</button>
            <button class="chip" data-chip="199-399" onclick="PlansScreen.filterPrice('199-399')">₹199–399</button>
            <button class="chip" data-chip="400-799" onclick="PlansScreen.filterPrice('400-799')">₹400–799</button>
            <button class="chip" data-chip="800plus" onclick="PlansScreen.filterPrice('800plus')">₹800+</button>
            <button class="chip chip-accent" data-chip="long" onclick="PlansScreen.filterPrice('long')">✦ Long validity</button>
        </div>
    </div>

    <!-- Featured Rail (only popular) -->
    <div class="plans-featured-rail" id="plansFeaturedRail" aria-label="Featured plans">
        <!-- injected via JS -->
    </div>

    <!-- Compare bar -->
    <div class="plans-compare-bar" id="plansCompareBar">
        <div class="compare-left">
            <span class="compare-icon"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.9"><rect x="3" y="3" width="7" height="7" rx="1.4"/><rect x="14" y="3" width="7" height="7" rx="1.4"/><rect x="14" y="14" width="7" height="7" rx="1.4"/><rect x="3" y="14" width="7" height="7" rx="1.4"/></svg></span>
            <div>
                <div class="compare-title"><span id="compareCount">0</span> plans selected</div>
                <div class="compare-sub">Compare to find best value</div>
            </div>
        </div>
        <div class="compare-actions">
            <button class="compare-clear" onclick="PlansScreen.clearCompare()">Clear</button>
            <button class="compare-go" onclick="PlansScreen.goCompare()">Compare</button>
        </div>
    </div>

    <!-- Plans List -->
    <div class="plans-list list-view" id="plansList"></div>

    <!-- Trust -->
    <div class="plans-trust">
        <span><svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.9"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/><path d="M9 12l11 0"/><polyline points="9,12 11,14 15,10"/></svg> Secure Payment</span>
        <span class="dot"></span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="#6C2BFF" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Instant</span>
        <span class="dot"></span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="1.9"><path d="M12 2l2.8 5.7 6.2.9-4.5 4.3 1 6.2L12 15.7 5.5 18.6l1-6.2L3 8.6l6.2-.9L12 2Z"/></svg> 2M+ Users</span>
    </div>
</div>

<!-- Filter Sheet -->
<div class="plans-sheet-overlay" id="plansSheetOverlay" onclick="PlansScreen.toggleFilterSheet()"></div>
<div class="plans-sheet" id="plansSheet">
    <div class="sheet-handle"></div>
    <h3>Filters & Sorting</h3>
    <div class="sheet-section">
        <label>Sort by</label>
        <div class="sheet-sorts">
            <button data-sort="recommended" class="active" onclick="PlansScreen.onSort('recommended'); PlansScreen.toggleFilterSheet()">Recommended</button>
            <button data-sort="price_low" onclick="PlansScreen.onSort('price_low'); PlansScreen.toggleFilterSheet()">Price Low → High</button>
            <button data-sort="price_high" onclick="PlansScreen.onSort('price_high'); PlansScreen.toggleFilterSheet()">Price High → Low</button>
            <button data-sort="validity" onclick="PlansScreen.onSort('validity'); PlansScreen.toggleFilterSheet()">Longest Validity</button>
            <button data-sort="data" onclick="PlansScreen.onSort('data'); PlansScreen.toggleFilterSheet()">Most Data</button>
        </div>
    </div>
    <div class="sheet-section">
        <label>Price range</label>
        <div class="sheet-chips">
            <button onclick="PlansScreen.filterPrice('all'); PlansScreen.toggleFilterSheet()">All</button>
            <button onclick="PlansScreen.filterPrice('under199'); PlansScreen.toggleFilterSheet()">Under ₹199</button>
            <button onclick="PlansScreen.filterPrice('199-399'); PlansScreen.toggleFilterSheet()">₹199-399</button>
            <button onclick="PlansScreen.filterPrice('800plus'); PlansScreen.toggleFilterSheet()">₹800+</button>
        </div>
    </div>
    <button class="sheet-close" onclick="PlansScreen.toggleFilterSheet()">Done</button>
</div>

<script>
const PlansScreen = {
    plans: [], filteredPlans: [],
    currentCategory: 'popular', currentPrice: 'all', currentSort: 'recommended', currentView: 'list',
    searchQuery: '', compareIds: new Set(), _searchOpen:false, _sheetOpen:false,
    init: function(data){
        const numberEl=document.getElementById('plansNumber');
        if(numberEl && data.mobile) numberEl.textContent='+91 '+App.formatMobile(data.mobile);
        const operatorEl=document.getElementById('plansOperator');
        const operatorNameEl=document.getElementById('plansOperatorName');
        const avatarEl=document.getElementById('plansAvatar');
        if(data.operator){
            const names={'jio':'Jio Prepaid','airtel':'Airtel Prepaid','vi':'Vi Prepaid','bsnl':'BSNL Prepaid'};
            const short={'jio':'J','airtel':'A','vi':'Vi','bsnl':'B'};
            const colors={'jio':'linear-gradient(135deg,#0A3D91,#1E6DD1)','airtel':'linear-gradient(135deg,#ED1C24,#FF3B30)','vi':'linear-gradient(135deg,#CC0000,#FF1A1A)','bsnl':'linear-gradient(135deg,#0A8A4B,#22C55E)'};
            if(operatorEl) operatorEl.innerHTML='<span class="op-dot"></span> '+(names[data.operator]||data.operator)+' • 5G Ready';
            if(operatorNameEl) operatorNameEl.innerHTML=(names[data.operator]+' Plans • ')+'<span class="info-5g">5G • HD Voice</span>';
            if(avatarEl){ avatarEl.textContent=short[data.operator]||'J'; avatarEl.style.background=colors[data.operator]||colors['jio']; }
        }
        this.bindSticky();
        this.loadPlans(data.operator);
    },
    bindSticky: function(){
        const sticky=document.getElementById('plansSticky');
        if(!sticky) return;
        let ticking=false;
        const onScroll=()=>{
            if(!ticking){ requestAnimationFrame(()=>{ sticky.classList.toggle('is-stuck', window.scrollY>120); ticking=false; }); ticking=true; }
        };
        window.addEventListener('scroll', onScroll, {passive:true});
        document.getElementById('app')?.addEventListener('scroll', onScroll, {passive:true});
    },
    loadPlans: async function(operator){
        this.showLoading();
        try{
            var resp = await fetch('api/get-plans.php?operator='+encodeURIComponent(operator||'jio'),{credentials:'include'});
            var json = await resp.json();
            if(!json.success) throw new Error(json.error||'Failed to load plans');
            this.plans=(json.data.plans||[]).map(function(p){ return PlansScreen.normalizePlan(p, operator); });
            this.updateCounts();
            this.filterPlans();
        }catch(e){ console.error(e); App.showToast(e.message||'Failed to load plans','error');
            var c=document.getElementById('plansList'); if(c) c.innerHTML=PlansScreen.getEmptyHTML();
        }
    },
    normalizePlan: function(p, operator){
        var validity = p.validity || '';
        var days = 0;
        var m = /([\d.]+)\s*(year|yr|month|day)/i.exec(validity);
        if(m){ var n=parseFloat(m[1]); var u=m[2].toLowerCase();
            days = u[0]==='y' ? Math.round(n*365) : u[0]==='m' ? Math.round(n*30) : Math.round(n); }
        var dataGb = 0;
        var dm = /([\d.]+)\s*gb/i.exec(p.data||'');
        if(dm) dataGb = parseFloat(dm[1]);
        return {
            id: p.id, amount: parseFloat(p.amount), validity: validity, days: days,
            data: p.data||'-', dataGb: dataGb, calls: p.calls||'Unlimited', sms: p.sms||'-',
            category: p.category||'popular', badge: p.badge||'', title: p.title||'',
            old_price: p.old_price||'', benefits: p.benefits||'', popular: (p.category==='popular'),
            ott: p.ott||[], highlight: p.highlight||'',
            _op: (p.operator||operator||'jio').toLowerCase()
        };
    },
    updateCounts: function(){
        const cats=['popular','unlimited','data','talktime'];
        const all=this.plans.length;
        const aEl=document.querySelector('[data-count="all"]'); if(aEl) aEl.textContent=all;
        cats.forEach(c=>{
            const n=this.plans.filter(p=>p.category===c).length;
            const el=document.querySelector('[data-count="'+c+'"]'); if(el) el.textContent=n;
        });
    },
    // Plans are database-driven via api/get-plans.php — no hard-coded prices.
    toggleSearch: function(){ this._searchOpen=!this._searchOpen; document.getElementById('plansSearchWrap').classList.toggle('open', this._searchOpen); if(this._searchOpen) setTimeout(()=>document.getElementById('plansSearchInput').focus(),80); },
    quickSearch: function(v){ const i=document.getElementById('plansSearchInput'); if(i) i.value=v; this.onSearch(v); },
    clearSearch: function(){ const i=document.getElementById('plansSearchInput'); if(i) i.value=''; this.onSearch(''); },
    toggleFilterSheet: function(){ this._sheetOpen=!this._sheetOpen; document.getElementById('plansSheet').classList.toggle('open', this._sheetOpen); document.getElementById('plansSheetOverlay').classList.toggle('show', this._sheetOpen); },
    setView: function(v){ this.currentView=v; document.querySelectorAll('.plans-view-toggle button').forEach(b=>b.classList.toggle('active', b.dataset.view===v)); document.getElementById('plansList').className='plans-list '+v+'-view'; App.haptic('light'); },
    onSort: function(v){ this.currentSort=v; const sel=document.getElementById('plansSort'); if(sel) sel.value=v; document.querySelectorAll('.sheet-sorts button').forEach(b=> b.classList.toggle('active', b.dataset.sort===v)); App.haptic('light'); this.filterPlans(); },
    onSearch: function(v){ this.searchQuery=(v||'').toLowerCase().trim(); this.filterPlans(); },
    filterPrice: function(p){ this.currentPrice=p; document.querySelectorAll('.plans-chips .chip').forEach(c=>c.classList.toggle('active', c.dataset.chip===p)); App.haptic('light'); this.filterPlans(); },
    filterCategory: function(category){
        this.currentCategory=category;
        document.querySelectorAll('.plan-category-tab').forEach(tab=> tab.classList.toggle('active', tab.dataset.category===category));
        App.haptic('light'); this.filterPlans();
    },
    filterPlans: function(){
        let list=[...this.plans];
        if(this.currentCategory!=='all') list=list.filter(p=>p.category===this.currentCategory);
        if(this.currentPrice==='under199') list=list.filter(p=>p.amount<199);
        else if(this.currentPrice==='199-399') list=list.filter(p=>p.amount>=199 && p.amount<=399);
        else if(this.currentPrice==='400-799') list=list.filter(p=>p.amount>=400 && p.amount<=799);
        else if(this.currentPrice==='800plus') list=list.filter(p=>p.amount>=800);
        else if(this.currentPrice==='long') list=list.filter(p=>p.days>=84);
        if(this.searchQuery){
            const q=this.searchQuery;
            list=list.filter(p=> String(p.amount).includes(q) || p.data.toLowerCase().includes(q) || p.validity.toLowerCase().includes(q) || (p.badge&&p.badge.toLowerCase().includes(q)) || p.category.includes(q) || (p.highlight&&p.highlight.toLowerCase().includes(q)) || (p.ott&&p.ott.join(' ').toLowerCase().includes(q)));
        }
        if(this.currentSort==='price_low') list.sort((a,b)=>a.amount-b.amount);
        else if(this.currentSort==='price_high') list.sort((a,b)=>b.amount-a.amount);
        else if(this.currentSort==='validity') list.sort((a,b)=>b.days-a.days);
        else if(this.currentSort==='data') list.sort((a,b)=>b.dataGb-a.dataGb);
        else list.sort((a,b)=> (b.popular?1:0)-(a.popular?1:0) || a.amount-b.amount);
        this.filteredPlans=list;
        const countEl=document.getElementById('plansCount');
        if(countEl) countEl.textContent=list.length+' plan'+(list.length!==1?'s':'')+(this.searchQuery?' for "'+this.searchQuery+'"':'');
        this.renderFeatured();
        this.renderPlans();
    },
    renderFeatured: function(){
        const rail=document.getElementById('plansFeaturedRail');
        if(!rail) return;
        const show = this.currentCategory==='popular' && this.currentPrice==='all' && !this.searchQuery;
        if(!show){ rail.innerHTML=''; rail.classList.remove('show'); return; }
        const featured=this.plans.filter(p=>p.highlight).slice(0,4);
        if(!featured.length){ rail.innerHTML=''; rail.classList.remove('show'); return; }
        rail.classList.add('show');
        rail.innerHTML='<div class="rail-head"><span><svg viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M12 2l2.8 5.7 6.2.9-4.5 4.3 1 6.2L12 15.7l-5.5 2.9 1-6.2L3 8.6l6.2-.9L12 2Z"/></svg> Featured for you</span><button onclick="PlansScreen.filterCategory(\'all\')">View all →</button></div><div class="rail-scroll">'+featured.map(p=>`
            <div class="rail-card" onclick="PlansScreen.selectPlan(${p.id})">
                <div class="rail-badge">${p.highlight}</div>
                <div class="rail-amount">₹${p.amount}<span> • ${p.validity}</span></div>
                <div class="rail-meta">${p.data} • ${p.calls}</div>
                <div class="rail-ott">${(p.ott||[]).slice(0,2).join(' • ')||'Value Pack'}</div>
            </div>
        `).join('')+'</div>';
    },
    renderPlans: function(){
        const container=document.getElementById('plansList');
        if(!container) return;
        if(this.filteredPlans.length===0){ container.innerHTML=this.getEmptyHTML(); return; }
        container.innerHTML=this.filteredPlans.map((plan,i)=> this.getPlanCardHTML(plan,i)).join('');
    },
    getOttIcon: function(name){
        const n=(name||'').toLowerCase();
        if(n.includes('hotstar')) return `<div class="ott-app-icon" style="background:linear-gradient(135deg,#0A3D91,#7B3FA0,#FF2E8A)"><svg viewBox="0 0 24 24" fill="white"><path d="M12 2l2.2 5.2 5.8 0.5-4.4 3.7 1.3 5.6L12 13.5 7.1 17l1.3-5.6L4 7.7l5.8-0.5L12 2Z"/></svg></div>`;
        if(n.includes('gemini')) return `<div class="ott-app-icon" style="background:white; border:1px solid #EDE9FF"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 5.5L20 12l-5.5 2.5L12 20l-2.5-5.5L4 12l5.5-2.5L12 2Z" fill="url(#gG)"/><defs><linearGradient id="gG" x1="0" y1="0" x2="24" y2="24"><stop offset="0%" stop-color="#4285F4"/><stop offset="35%" stop-color="#EA4335"/><stop offset="70%" stop-color="#FBBC05"/><stop offset="100%" stop-color="#34A853"/></linearGradient></defs></svg></div>`;
        if(n.includes('jiotv')) return `<div class="ott-app-icon" style="background:#FF1A1A"><svg viewBox="0 0 24 24" fill="white"><path d="M19.7 8.3c-.3-1-1.2-1.7-2.3-1.7H6.6c-1.1 0-2 0.7-2.3 1.7L2 16c0.3 1 1.2 1.7 2.3 1.7h10.8c1.1 0 2-0.7 2.3-1.7L19.7 8.3Z"/><path d="M10 13.5l5-2.5-5-2.5v5Z" fill="#FF1A1A" stroke="white" stroke-width="0.6"/><rect x="8" y="15.8" width="8" height="1.6" rx="0.8" fill="white"/></svg></div>`;
        if(n.includes('sony') || n.includes('liv')) return `<div class="ott-app-icon" style="background:linear-gradient(180deg,#0A1834,#3A1A8A)"><span style="font-size:7px; font-weight:800; color:white; letter-spacing:0.5px;">SONY</span><span style="font-size:11px; font-weight:800; color:#FFD700; margin-top:-2px;">liv</span></div>`;
        if(n.includes('netflix')) return `<div class="ott-app-icon" style="background:#E50914"><span style="color:white; font-weight:800; font-size:14px;">N</span></div>`;
        if(n.includes('prime')) return `<div class="ott-app-icon" style="background:#00A8E1"><span style="color:white; font-weight:700; font-size:9px;">prime video</span></div>`;
        if(n.includes('cinema') || n.includes('jiocinema')) return `<div class="ott-app-icon" style="background:linear-gradient(135deg,#FF6B35,#FF2E8A)"><span style="color:white; font-size:7px; font-weight:800;">JioCinema</span></div>`;
        return `<div class="ott-app-icon" style="background:#F5F0FF; border:1px solid #EDE9FF"><span style="font-size:8px; font-weight:700; color:#5F259F;">${name.slice(0,4).toUpperCase()}</span></div>`;
    },
    getPlanCardHTML: function(plan, index){
        const perDay=(plan.amount/plan.days).toFixed(1);
        const badgeClass = (plan.badge==='NEW') ? 'new' : (plan.badge==='HOT' ? 'hot' : (plan.badge ? 'neutral' : 'hidden'));
        const badgeHTML=plan.badge?'<span class="plan-card-badge '+badgeClass+'">'+ (plan.badge==='HOT'?'🔥 ':'') +plan.badge+'</span>':'';
        const isCompared=this.compareIds.has(plan.id);
        // True 5G design mapping
        const opForBadge = (plan._op||'jio').toLowerCase();
        const opPlanLabel = opForBadge==='jio' ? 'Jio PLAN' : opForBadge==='airtel' ? 'Airtel PLAN' : opForBadge==='vi' ? 'Vi PLAN' : 'BSNL PLAN';
        const opLogoClass = opForBadge;
        const opLogoText = opForBadge==='jio' ? 'Jio' : opForBadge==='airtel' ? 'airtel' : opForBadge==='vi' ? 'Vi' : 'BSNL';
        const title = plan.amount>=400 && plan.days>=300 ? 'True 5G Plan' : (plan.category==='data' ? 'Data Booster' : (plan.category==='talktime' ? 'Talktime Plan' : 'True 5G Plan'));
        const sub = plan.data==='-' ? 'Voice • SMS' : (plan.popular ? 'More Data  •  More Entertainment' : 'High Speed  •  Unlimited Calls');
        const oldPrice = plan.popular && plan.amount<500 ? '₹'+(plan.amount+500) : '';
        // OTT grid — show up to 4, rest in +N
        const ottList = (plan.ott && plan.ott.length) ? plan.ott : ['JioHotstar','Google Gemini','JioTV','Sony LIV'];
        const visibleOtt = ottList.slice(0,4);
        const extraOtt = ottList.length>4 ? ottList.length-4 : 0;
        // For demo, force 4 as in image if ott less than 4, pad with defaults
        let ottGrid = visibleOtt;
        if(ottGrid.length<4){
            const defaults=['JioHotstar','Google Gemini','JioTV','Sony LIV'];
            ottGrid = [...ottGrid, ...defaults].slice(0,4);
        }
        return `
            <div class="plan-card true5g ${plan.popular?'is-popular':''} slide-up" style="animation-delay:${index*0.04}s" onclick="PlansScreen.selectPlan(${plan.id})">
                <div class="true-top">
                    ${plan.popular ? '<span class="badge-pop">✦ POPULAR</span>' : '<span></span>'}
                    <span class="badge-plan">${opPlanLabel}</span>
                </div>
                <div class="true-brand">
                    <div class="true-logo ${opLogoClass}">${opLogoText}</div>
                    <div class="true-title-wrap">
                        <div class="true-title">${title}</div>
                        <div class="true-sub">${sub}</div>
                    </div>
                </div>
                <div class="true-price-row">
                    <div class="true-price">
                        <span class="now">₹${plan.amount}</span>
                        ${oldPrice?'<span class="old">'+oldPrice+'</span>':''}
                    </div>
                    <div class="true-unlimited">
                        <span class="inf">∞</span>
                        <span>Unlimited<br><b>True 5G Data</b></span>
                    </div>
                </div>
                <div class="true-stats">
                    <div class="t-stat">
                        <span class="t-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#5F259F" stroke-width="1.7" stroke-linecap="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 3v3"/><path d="M16 3v3"/><path d="M3 10h18"/></svg></span>
                        <span class="t-label">Validity</span>
                        <span class="t-value">${plan.validity}</span>
                    </div>
                    <div class="t-stat">
                        <span class="t-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#5F259F" stroke-width="1.7"><circle cx="12" cy="12" r="3.5"/><path d="M12 2.5a9.5 9.5 0 0 1 0 19"/><path d="M2.5 12a9.5 9.5 0 0 1 19 0"/></svg></span>
                        <span class="t-label">Data</span>
                        <span class="t-value">${plan.data}</span>
                    </div>
                    <div class="t-stat">
                        <span class="t-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#5F259F" stroke-width="1.7"><path d="M15.8 15.2l1.4 1.4a1.2 1.2 0 0 1-1.7 1.7l-1.4-1.4a7 7 0 0 1-5.6-5.6L7.1 9.9a1.2 1.2 0 0 1 1.7-1.7l1.4 1.4a1.6 1.6 0 0 1 0 2.2l-0.7 0.7a4 4 0 0 0 2.8 2.8l0.7-0.7a1.6 1.6 0 0 1 2.2 0Z"/></svg></span>
                        <span class="t-label">Voice</span>
                        <span class="t-value">${plan.calls}</span>
                    </div>
                    <div class="t-stat">
                        <span class="t-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#5F259F" stroke-width="1.7"><rect x="3" y="5" width="18" height="13" rx="3"/><path d="M7 10h10"/><path d="M7 14h6"/></svg></span>
                        <span class="t-label">SMS</span>
                        <span class="t-value">${plan.sms}</span>
                    </div>
                </div>
                <div class="true-free-head">
                    <span class="gift"><svg viewBox="0 0 24 24" fill="#5F259F"><path d="M12 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/><path d="M8 9a3 3 0 0 0-3 3c0 1 0.5 1.5 1 2l4 4 4-4c0.5-0.5 1-1 1-2a3 3 0 0 0-3-3 2.5 2.5 0 0 0-2 1 2.5 2.5 0 0 0-2-1Z" fill="none" stroke="#5F259F" stroke-width="1.4"/><rect x="3" y="12" width="18" height="8" rx="2" fill="#5F259F"/><path d="M12 12v8" stroke="white" stroke-width="1.4"/></svg></span>
                    FREE WITH THIS PLAN
                    <button class="more-btn" onclick="event.stopPropagation(); PlansScreen.showOtt(${plan.id})">+${extraOtt?extraOtt:3} More <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 18l6-6-6-6"/></svg></button>
                </div>
                <div class="true-ott-grid">
                    ${ottGrid.map((o,i)=>{
                        const labels={
                            'JioHotstar':['JioHotstar','Movies, Cricket','& Originals'],
                            'Google Gemini':['Google Gemini','18 Months Pro','Membership'],
                            'JioTV':['JioTV','1000+ Live','TV Channels'],
                            'Sony LIV':['Sony LIV','TV Shows','& Series'],
                        };
                        const lab = labels[o] || [o,'Included','Benefit'];
                        return `<div class="ott-item">
                            ${this.getOttIcon(o)}
                            <div class="ott-name">${lab[0]}</div>
                            <div class="ott-desc">${lab[1]}<br>${lab[2]}</div>
                        </div>`;
                    }).join('')}
                </div>
                <button class="true-cta" onclick="event.stopPropagation(); PlansScreen.selectPlan(${plan.id})">Recharge for ₹${plan.amount} <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M5 12h13"/><path d="M12 6l6 6-6 6"/></svg></button>
                <div class="true-compare-row" onclick="event.stopPropagation()">
                    <label class="plan-compare"><input type="checkbox" ${isCompared?'checked':''} onchange="PlansScreen.toggleCompare(${plan.id}, this.checked)" /><span class="cbox"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.6"><path d="M5 13l4 4 10-10"/></svg></span> Compare</label>
                    <span class="perday">₹${perDay}/day • Valid for ${plan.validity}</span>
                </div>
            </div>
        `;
    },
    toggleCompare: function(id, checked){
        if(checked){ if(this.compareIds.size>=3){ App.showToast('Max 3 plans','error'); event.target.checked=false; return; } this.compareIds.add(id); }
        else this.compareIds.delete(id);
        this.updateCompareBar(); App.haptic('light');
    },
    updateCompareBar: function(){
        const bar=document.getElementById('plansCompareBar');
        const cnt=document.getElementById('compareCount');
        if(cnt) cnt.textContent=this.compareIds.size;
        if(bar) bar.classList.toggle('show', this.compareIds.size>0);
    },
    clearCompare: function(){ this.compareIds.clear(); this.updateCompareBar(); this.renderPlans(); },
    goCompare: function(){
        if(this.compareIds.size<2){ App.showToast('Select at least 2','error'); return; }
        App.showToast('Compare: '+[...this.compareIds].map(id=> '₹'+(this.plans.find(p=>p.id===id)?.amount)).join(' vs '),'success');
    },
    showOtt: function(id){ const p=this.plans.find(x=>x.id===id); if(!p) return; App.showToast((p.ott||[]).join(' • ')||'OTT benefits included','info'); },
    getEmptyHTML: function(){
        return `
            <div class="plans-empty">
                <div class="plans-empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7"/><path d="M16.6 16.6L21 21"/><path d="M8 11h6"/></svg></div>
                <h3 class="plans-empty-title">No plans found</h3>
                <p class="plans-empty-text">Try different category, price or search keyword</p>
                <button class="plans-empty-btn" onclick="PlansScreen.clearSearch(); PlansScreen.filterCategory('all'); PlansScreen.filterPrice('all')">Clear all filters</button>
            </div>`;
    },
    showLoading: function(){
        const container=document.getElementById('plansList');
        if(!container) return;
        // Featured shimmer + cards shimmer — PhonePe style
        let h=`<div class="shimmer-card" style="padding:20px; text-align:center; margin-bottom:2px;">
            <div style="width:56px;height:56px;border-radius:16px;margin:0 auto 14px;background:linear-gradient(90deg,#F0EAFF,#EDE9FF,#F5F0FF);background-size:400% 100%;animation:shimmer 1.35s infinite linear;"></div>
            <div class="skeleton-line" style="width:60%;height:10px;margin:0 auto;"></div>
            <div class="skeleton-line" style="width:40%;height:8px;margin:8px auto 0;"></div>
        </div>`;
        for(let i=0;i<4;i++) h+=`<div class="plan-skeleton"><div class="sk-top"><div class="skeleton-line short"></div><div class="skeleton-line badge"></div></div><div class="skeleton-line long"></div><div class="sk-grid"><div class="skeleton-line"></div><div class="skeleton-line"></div><div class="skeleton-line"></div></div><div class="skeleton-line" style="width:100%;height:8px;margin-top:14px;"></div></div>`;
        container.innerHTML=h;
        const cnt=document.getElementById('plansCount'); if(cnt) cnt.textContent='Loading plans...';
    },
    selectPlan: async function(planId){
        const plan=this.plans.find(p=>p.id===planId); if(!plan) return;
        App.haptic('medium');
        try{
            var resp = await fetch('api/recharge-session.php',{method:'POST',headers:{'Content-Type':'application/json'},credentials:'include',body:JSON.stringify({action:'select-plan',plan_id:planId})});
            var json = await resp.json();
            if(!json.success) throw new Error(json.error||'Plan unavailable');
            plan.amount = parseFloat(json.data.amount);
            App.setState({selectedPlan:plan});
            Router.navigate('checkout',{plan_id:planId, mobile:App.getState().mobileNumber, operator:App.getState().selectedOperator});
        }catch(e){ App.showToast(e.message,'error'); }
    }
};
</script>
