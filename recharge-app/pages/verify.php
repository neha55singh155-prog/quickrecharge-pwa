<!-- S003 Verify — PhonePe Exact FIXED -->
<div class="verify-screen">
    <div class="verify-phase" id="verifyPhase1">
        <div class="verify-card">
            <div class="verify-badge-top"><svg viewBox="0 0 24 24" fill="none" stroke="#5F259F" stroke-width="1.8"><path d="M12 22s7-3.5 7-9V7l-7-3-7 3v6c0 5.5 7 9 7 9Z"/><path d="M9 12l2 2 4-4"/></svg> PhonePe Secure Verification</div>
            <div class="verify-anim-box">
                <div class="verify-anim-square"></div>
                <div class="verify-anim-inner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M15.8 15.2l1.4 1.4a1.2 1.2 0 0 1-1.7 1.7l-1.4-1.4a7 7 0 0 1-5.6-5.6L7.1 9.9a1.2 1.2 0 0 1 1.7-1.7l1.4 1.4a1.6 1.6 0 0 1 0 2.2l-0.7 0.7a4 4 0 0 0 2.8 2.8l0.7-0.7a1.6 1.6 0 0 1 2.2 0Z"/></svg>
                </div>
            </div>
            <h2 class="verify-card-title">Verifying your number</h2>
            <p class="verify-card-sub" id="verifySubtitle">Validating mobile number...</p>
            <div class="verify-number-chip"><span class="dot-blue"></span><span id="verifyNumberChip">+91 85745 76476</span></div>
            <div class="verify-progress-wrap">
                <div class="verify-progress-bar"><div class="verify-progress-fill" id="verifyProgress"></div></div>
                <span class="verify-progress-pct" id="verifyProgressPct">0%</span>
            </div>
            <div class="verify-steps-row" id="verifyStepsRow">
                <div class="verify-step-col"><span class="verify-step-icon done" id="vIcon1">✓</span><span class="verify-step-label done">Validate</span></div>
                <div class="verify-step-col"><span class="verify-step-icon pending" id="vIcon2">2</span><span class="verify-step-label" id="vLabel2">Operator</span></div>
                <div class="verify-step-col"><span class="verify-step-icon pending" id="vIcon3">3</span><span class="verify-step-label" id="vLabel3">Plans</span></div>
            </div>
        </div>
    </div>
    <div class="verify-phase hidden" id="verifyPhase2">
        <div class="verify-card">
            <div class="verify-success-wrap">
                <div class="verify-success-circle"><svg class="verify-check" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M6 12l4 4 8-8"/></svg></div>
            </div>
            <h2 class="verify-card-title success">Number Verified!</h2>
            <p class="verify-card-sub">Loading best plans for <strong id="verifyNumberSuccess">+91 85745 76476</strong></p>
            <div class="verify-number-chip" style="background:rgba(34,197,94,0.08); border-color:rgba(34,197,94,0.15); color:#16A34A; margin-top:10px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg> Verified by PhonePe</div>
            <div class="verify-dots"><span></span><span></span><span></span></div>
        </div>
    </div>
</div>
<script>
const VerifyScreen={
    init:function(data){
        this.data=data||{};
        const number=this.data.mobile||'8574576476';
        const op=this.data.operator||'jio';
        const fmt='+91 '+App.formatMobile(number);
        const chip=document.getElementById('verifyNumberChip'); if(chip) chip.textContent=fmt;
        const el=document.getElementById('verifyNumberSuccess'); if(el) el.textContent=fmt;
        document.getElementById('verifyPhase1').classList.remove('hidden');
        document.getElementById('verifyPhase2').classList.add('hidden');
        this.animateProgress(1550, ()=>{
            document.getElementById('verifyPhase1').classList.add('hidden');
            document.getElementById('verifyPhase2').classList.remove('hidden');
            App.haptic('medium');
            setTimeout(()=> Router.navigate('plans',{mobile:number, operator:op}), 900);
        });
    },
    animateProgress:function(duration, cb){
        const bar=document.getElementById('verifyProgress');
        const pct=document.getElementById('verifyProgressPct');
        const sub=document.getElementById('verifySubtitle');
        const i1=document.getElementById('vIcon1'), i2=document.getElementById('vIcon2'), i3=document.getElementById('vIcon3');
        const l2=document.getElementById('vLabel2'), l3=document.getElementById('vLabel3');
        const steps=[
            {t:0, text:'Validating mobile number...', pct:0},
            {t:300, text:'Detecting operator...', pct:28},
            {t:700, text:'Checking plan eligibility...', pct:58},
            {t:1050, text:'Fetching available plans...', pct:84},
            {t:1380, text:'Almost done...', pct:100},
        ];
        steps.forEach(s=>{
            setTimeout(()=>{
                if(bar) bar.style.width=s.pct+'%';
                if(pct) pct.textContent=s.pct+'%';
                if(sub) sub.textContent=s.text;
                // steps icons
                if(s.pct>=28){ if(i2){ i2.textContent='✓'; i2.className='verify-step-icon done'; } if(l2) l2.className='verify-step-label done'; }
                if(s.pct>=58){ if(i3){ i3.textContent='✓'; i3.className='verify-step-icon done'; } if(l3) l3.className='verify-step-label done'; }
                if(s.pct>0) App.haptic('light');
            }, s.t);
        });
        setTimeout(cb, duration);
    }
};
</script>
