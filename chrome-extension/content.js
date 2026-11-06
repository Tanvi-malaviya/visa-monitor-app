// US Visa Portal Auto-Fill Assistant Content Script
(function() {
    console.log('[Visa Auto-Fill Extension] Active on official portal:', location.href);

    let creds = null;
    const hash = window.location.hash;

    // 1. Detect credentials in URL hash: #autofill={"u":"email","p":"pass","fn":"...","ln":"...","mode":"login|signup"}
    if (hash && hash.includes('autofill=')) {
        try {
            const raw = hash.split('autofill=')[1].split('&')[0];
            creds = JSON.parse(decodeURIComponent(raw));
            sessionStorage.setItem('visa_last_autofill', JSON.stringify(creds));
            // Clean up the hash from the address bar for privacy
            history.replaceState(null, document.title, window.location.pathname + window.location.search);
        } catch(e) {
            console.warn('[Visa Auto-Fill Extension] Error parsing credentials from URL hash:', e);
        }
    }

    // 2. Fallback to session storage if refreshed
    if (!creds) {
        try {
            const saved = sessionStorage.getItem('visa_last_autofill');
            if (saved) creds = JSON.parse(saved);
        } catch(e) {}
    }

    if (!creds || !creds.u) {
        return;
    }

    console.log('[Visa Auto-Fill Extension] Auto-filling credentials for:', creds.u);

    // =========================================================================
    // FLOATING ASSISTANT CARD (Bottom-Right, Draggable, NEVER covers main design)
    // =========================================================================
    const card = document.createElement('div');
    card.id = 'visa-autofill-notification';
    card.style.cssText = `
        position: fixed;
        bottom: 22px;
        right: 24px;
        z-index: 9999999;
        width: 320px;
        box-sizing: border-box;
        background: rgba(15, 23, 42, 0.96);
        backdrop-filter: blur(10px);
        border: 1px solid #0284c7;
        border-radius: 12px;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.55), 0 0 16px rgba(2, 132, 199, 0.3);
        font-family: system-ui, -apple-system, sans-serif;
        overflow: hidden;
        transition: width 0.2s ease, opacity 0.2s ease;
    `;

    const applicantDisplayName = creds.fn ? (creds.fn + ' ' + (creds.ln || '')) : creds.u;

    card.innerHTML = `
        <!-- Drag Handle / Header -->
        <div id="visa-card-drag-handle" style="background:rgba(2,132,199,0.18);border-bottom:1px solid rgba(2,132,199,0.3);padding:7px 11px;display:flex;align-items:center;justify-content:space-between;cursor:grab;user-select:none;">
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:800;color:#38bdf8;">
                <span style="opacity:0.6;font-size:10px;">⋮⋮</span>
                <span>🤖 US Visa Assistant</span>
            </div>
            <div style="display:flex;align-items:center;gap:4px;">
                <button id="visa-card-minimize-btn" type="button" title="Minimize widget" style="background:none;border:none;color:#94a3b8;font-size:13px;font-weight:800;cursor:pointer;padding:1px 5px;border-radius:4px;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">−</button>
            </div>
        </div>

        <!-- Body Section -->
        <div id="visa-card-body" style="padding:10px 12px;display:flex;flex-direction:column;gap:8px;">
            <!-- Applicant & Timer -->
            <div style="font-size:11.5px;color:#94a3b8;display:flex;align-items:center;justify-content:space-between;gap:6px;">
                <span style="color:#e2e8f0;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:185px;" title="${applicantDisplayName}">👤 ${applicantDisplayName}</span>
                <div id="visa-countdown-container" style="display:flex;align-items:center;gap:4px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#38bdf8;flex-shrink:0;">
                    <span id="visa-timer-label">Timer:</span>
                    <strong id="visa-countdown-val" style="background:rgba(56,189,248,0.15);padding:1px 5px;border-radius:4px;color:#38bdf8;">--</strong>
                </div>
            </div>

            <!-- Live Status Pill & Message -->
            <div style="background:rgba(255,255,255,0.05);padding:6px 9px;border-radius:7px;display:flex;align-items:center;gap:7px;">
                <span id="visa-status-pill" style="padding:2px 6px;border-radius:4px;font-size:9.5px;font-weight:800;text-transform:uppercase;background:#0284c7;color:#ffffff;white-space:nowrap;flex-shrink:0;">INITIALIZING</span>
                <span id="visa-status-text" style="font-size:11px;color:#cbd5e1;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="Scanning login fields...">Scanning login fields...</span>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
                <button id="visa-btn-click-now" type="button" style="flex:1;padding:5px 8px;border-radius:6px;border:none;background:#0284c7;color:#ffffff;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;box-shadow:0 2px 6px rgba(2,132,199,0.4);">
                    <span>⚡</span><span>Click Now</span>
                </button>
                <button id="visa-toggle-click-btn" type="button" style="flex:1.2;padding:5px 8px;border-radius:6px;border:none;background:#ef4444;color:#ffffff;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;box-shadow:0 2px 6px rgba(239,68,68,0.4);">
                    <span>⏹</span><span>Pause Auto-Click</span>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(card);

    // Draggable Logic: Lets user freely drag the card anywhere on screen
    const dragHandle = document.getElementById('visa-card-drag-handle');
    (function makeDraggable(el, handle) {
        let p1 = 0, p2 = 0, p3 = 0, p4 = 0;
        handle.onmousedown = (e) => {
            if (e.target.tagName === 'BUTTON') return;
            e.preventDefault();
            p3 = e.clientX;
            p4 = e.clientY;
            document.onmouseup = () => {
                document.onmouseup = null;
                document.onmousemove = null;
                handle.style.cursor = 'grab';
            };
            document.onmousemove = (e2) => {
                e2.preventDefault();
                p1 = p3 - e2.clientX;
                p2 = p4 - e2.clientY;
                p3 = e2.clientX;
                p4 = e2.clientY;
                el.style.bottom = 'auto';
                el.style.right = 'auto';
                el.style.top = (el.offsetTop - p2) + 'px';
                el.style.left = (el.offsetLeft - p1) + 'px';
            };
            handle.style.cursor = 'grabbing';
        };
    })(card, dragHandle);

    // Minimize / Expand logic
    let isMinimized = false;
    const minimizeBtn = document.getElementById('visa-card-minimize-btn');
    const cardBody = document.getElementById('visa-card-body');
    if (minimizeBtn) {
        minimizeBtn.onclick = (e) => {
            e.preventDefault();
            isMinimized = !isMinimized;
            if (isMinimized) {
                cardBody.style.display = 'none';
                card.style.width = '200px';
                minimizeBtn.innerText = '+';
                minimizeBtn.title = 'Expand widget';
            } else {
                cardBody.style.display = 'flex';
                card.style.width = '320px';
                minimizeBtn.innerText = '−';
                minimizeBtn.title = 'Minimize widget';
            }
        };
    }

    // References to HUD elements
    const statusPill = document.getElementById('visa-status-pill');
    const statusText = document.getElementById('visa-status-text');
    const countdownVal = document.getElementById('visa-countdown-val');
    const toggleBtn = document.getElementById('visa-toggle-click-btn');
    const clickNowBtn = document.getElementById('visa-btn-click-now');

    function setHudState(type, pillText, messageText, timerText = null) {
        if (!statusPill || !statusText) return;

        statusPill.innerText = pillText;
        statusText.innerHTML = messageText;
        statusText.title = statusText.innerText;

        if (timerText !== null && countdownVal) {
            countdownVal.innerText = timerText;
        }

        switch(type) {
            case 'success':
                statusPill.style.background = '#10b981';
                statusPill.style.color = '#ffffff';
                card.style.borderColor = '#10b981';
                break;
            case 'error':
                statusPill.style.background = '#ef4444';
                statusPill.style.color = '#ffffff';
                card.style.borderColor = '#ef4444';
                break;
            case 'loading':
                statusPill.style.background = '#8b5cf6';
                statusPill.style.color = '#ffffff';
                card.style.borderColor = '#8b5cf6';
                break;
            case 'action_required':
                statusPill.style.background = '#f59e0b';
                statusPill.style.color = '#000000';
                card.style.borderColor = '#f59e0b';
                break;
            case 'paused':
                statusPill.style.background = '#64748b';
                statusPill.style.color = '#ffffff';
                card.style.borderColor = '#64748b';
                break;
            case 'submitting':
                statusPill.style.background = '#06b6d4';
                statusPill.style.color = '#ffffff';
                card.style.borderColor = '#06b6d4';
                break;
            default:
                statusPill.style.background = '#0284c7';
                statusPill.style.color = '#ffffff';
                card.style.borderColor = '#0284c7';
                break;
        }
    }

    // Audio click feedback
    function playClickTone() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(620, ctx.currentTime);
            gain.gain.setValueAtTime(0.06, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        } catch(e) {}
    }

    // Visual feedback directly on portal's Sign In button
    function highlightPortalButton(btn, attemptNum) {
        if (!btn) return;
        try {
            const origBoxShadow = btn.style.boxShadow;
            const origTransform = btn.style.transform;
            const origTransition = btn.style.transition;

            btn.style.transition = 'all 0.15s ease';
            btn.style.boxShadow = '0 0 0 4px #10b981, 0 0 25px rgba(16, 185, 129, 0.9)';
            btn.style.transform = 'scale(0.96)';

            const badge = document.createElement('div');
            badge.innerText = `🚀 Clicked (#${attemptNum})`;
            badge.style.cssText = `
                position: absolute;
                background: #10b981;
                color: #ffffff;
                font-size: 11px;
                font-weight: 800;
                padding: 3px 8px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                pointer-events: none;
                z-index: 9999999;
                transform: translateY(-28px);
                transition: all 0.35s ease;
                font-family: system-ui, sans-serif;
            `;

            if (btn.parentElement) {
                btn.parentElement.style.position = 'relative';
                btn.parentElement.appendChild(badge);
            }

            setTimeout(() => {
                btn.style.transform = origTransform || 'scale(1)';
                setTimeout(() => {
                    btn.style.boxShadow = origBoxShadow;
                    btn.style.transition = origTransition;
                    badge.style.opacity = '0';
                    badge.style.transform = 'translateY(-36px)';
                    setTimeout(() => badge.remove(), 400);
                }, 500);
            }, 150);
        } catch(e) {}
    }

    // Helper to find official portal Sign In / Login button
    function findSignInButton() {
        return document.getElementById('next') || 
               document.getElementById('continue') || 
               document.getElementById('signIn') ||
               document.getElementById('login') ||
               document.querySelector('button[id*="next" i], button[id*="continue" i], button[id*="login" i], button[id*="signin" i]') ||
               document.querySelector('button[type="submit"], input[type="submit"]') ||
               Array.from(document.querySelectorAll('button, a.btn, input[type="button"], input[type="submit"]')).find(b => {
                   const txt = (b.innerText || b.value || '').trim().toLowerCase();
                   return txt === 'sign in' || txt === 'login' || txt === 'next' || txt === 'continue' || txt === 'submit';
               });
    }

    // Inspect DOM for portal error messages
    function detectPortalError() {
        const selectors = [
            '.error.pageLevel',
            '.error.itemLevel',
            '#claimVerificationServerError',
            '#errorMessage',
            '.claimVerificationServerError',
            '.pageLevel p',
            '.itemLevel p',
            'div[role="alert"]',
            '.alert.alert-danger',
            '.text-danger',
            '.validation-summary-errors',
            '.field-validation-error',
            '.error-message'
        ];
        for (let sel of selectors) {
            const elements = document.querySelectorAll(sel);
            for (let el of elements) {
                if (el && el.offsetParent !== null) {
                    const txt = (el.innerText || el.textContent || '').trim();
                    if (txt && txt.length > 3 && !txt.toLowerCase().includes('script')) {
                        return txt;
                    }
                }
            }
        }
        return null;
    }

    // Check if portal is loading
    function isPortalBusy() {
        const spinners = document.querySelectorAll('.spinner, .working, #working, .pageContent.loading, #api.loading, .loading-indicator');
        for (let s of spinners) {
            if (s && s.offsetParent !== null) return true;
        }
        const btn = findSignInButton();
        if (btn && (btn.disabled || btn.getAttribute('aria-busy') === 'true' || btn.classList.contains('disabled') || btn.classList.contains('loading'))) {
            return true;
        }
        return false;
    }

    // Detect OTP prompt
    function detectOtpPrompt() {
        const otpInput = document.querySelector('#emailVerificationCode, #verificationCode, input[name*="code" i], input[name*="otp" i], input[id*="otp" i]');
        if (otpInput && otpInput.offsetParent !== null) {
            return 'Security / Verification code input is active.';
        }
        const allHeadings = Array.from(document.querySelectorAll('h1, h2, h3, h4, p, label')).find(el => {
            const txt = (el.innerText || '').toLowerCase();
            return (txt.includes('verification code') || txt.includes('security code') || txt.includes('verify your identity')) && el.offsetParent !== null;
        });
        if (allHeadings) {
            return allHeadings.innerText.trim();
        }
        return null;
    }

    // Detect Captcha
    function isCaptchaPresent() {
        const captcha = document.querySelector('iframe[src*="recaptcha"], iframe[src*="hcaptcha"], iframe[src*="challenges.cloudflare.com"], #arkose, div.g-recaptcha');
        return !!(captcha && captcha.offsetParent !== null);
    }

    // Detect Logged In state
    function isLoginSuccessful() {
        const url = window.location.href.toLowerCase();
        if (url.includes('/dashboard') || 
            url.includes('/applicant_details') || 
            url.includes('/appointment') || 
            url.includes('/manage') || 
            (url.includes('usvisascheduling.com') && !url.includes('signin') && !url.includes('authorize') && !url.includes('b2c'))) {
            return true;
        }
        const logout = Array.from(document.querySelectorAll('a, button, span')).find(el => {
            const txt = (el.innerText || '').trim().toLowerCase();
            return (txt === 'sign out' || txt === 'log out' || txt === 'logout' || txt === 'my account') && el.offsetParent !== null;
        });
        if (logout) return true;

        const userInput = document.querySelector('#signInName, input[name="Sign in name"], input[name="Email Address"], input[name="Email"], input[type="email"], input[type="text"]');
        const passInput = document.querySelector('#password, input[name="Password"], input[type="password"]');
        const signInBtn = findSignInButton();
        if (!userInput && !passInput && !signInBtn && !detectPortalError()) {
            return true;
        }
        return false;
    }

    // Human typing simulation
    async function typeSlowly(inputEl, text, minDelay = 40, maxDelay = 85) {
        if (!inputEl || !text) return;
        inputEl.focus();
        inputEl.value = '';
        
        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            inputEl.value += char;
            
            inputEl.dispatchEvent(new KeyboardEvent('keydown', { key: char, bubbles: true }));
            inputEl.dispatchEvent(new KeyboardEvent('keypress', { key: char, bubbles: true }));
            inputEl.dispatchEvent(new Event('input', { bubbles: true }));
            inputEl.dispatchEvent(new KeyboardEvent('keyup', { key: char, bubbles: true }));
            
            const delay = Math.floor(Math.random() * (maxDelay - minDelay + 1)) + minDelay;
            await new Promise(r => setTimeout(r, delay));
        }
        
        inputEl.dispatchEvent(new Event('change', { bubbles: true }));
    }

    let attempts = 0;
    const maxAttempts = 40;

    const timer = setInterval(() => {
        attempts++;

        // Mode 1: Sign In Detection & Fill
        const userInput = document.querySelector('#signInName, input[name="Sign in name"], input[name="Email Address"], input[name="Email"], input[type="email"], input[type="text"]');
        const passInput = document.querySelector('#password, input[name="Password"], input[type="password"]');

        if (userInput && passInput) {
            clearInterval(timer);

            (async () => {
                setHudState('default', 'TYPING USER', `⌨️ Typing username: <strong>${creds.u}</strong>...`, '--');
                userInput.style.outline = '2px solid #0284c7';
                await typeSlowly(userInput, creds.u, 45, 85);

                await new Promise(r => setTimeout(r, 450));

                setHudState('default', 'TYPING PASS', `🔒 Typing password for <strong>${creds.u}</strong>...`, '--');
                passInput.style.outline = '2px solid #10b981';
                await typeSlowly(passInput, creds.p || '', 50, 90);

                await new Promise(r => setTimeout(r, 400));

                // =========================================================================
                // EVERY 5 SECONDS LOGIN ENGINE WITH REAL-TIME FEEDBACK & ERROR MONITORING
                // =========================================================================
                let autoClickAttempts = 0;
                let isPaused = false;
                let countdownSeconds = 5;
                let countdownInterval = null;
                let lastClickTimestamp = null;

                if (toggleBtn) {
                    toggleBtn.onclick = (e) => {
                        e.preventDefault();
                        isPaused = !isPaused;
                        if (isPaused) {
                            toggleBtn.innerHTML = '<span>▶</span><span>Resume</span>';
                            toggleBtn.style.background = '#10b981';
                            setHudState('paused', 'PAUSED', '⏸ Auto-Click paused. Click manually or resume.', 'PAUSED');
                        } else {
                            toggleBtn.innerHTML = '<span>⏹</span><span>Pause Auto-Click</span>';
                            toggleBtn.style.background = '#ef4444';
                            countdownSeconds = 5;
                            setHudState('default', 'RESUMED', `▶ Next click in <strong>${countdownSeconds}s</strong>...`, `${countdownSeconds}s`);
                        }
                    };
                }

                if (clickNowBtn) {
                    clickNowBtn.onclick = (e) => {
                        e.preventDefault();
                        countdownSeconds = 5;
                        performLoginClick();
                    };
                }

                function performLoginClick() {
                    if (isPaused) return;

                    // 1. Check if login already succeeded
                    if (isLoginSuccessful()) {
                        clearInterval(countdownInterval);
                        setHudState('success', 'LOGGED IN', '🎉 <strong>Login Successful!</strong> Portal loaded.', 'DONE');
                        setTimeout(() => {
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 400);
                        }, 5000);
                        return;
                    }

                    // 2. Check if portal is currently busy / loading server response
                    if (isPortalBusy()) {
                        setHudState('loading', 'AUTHENTICATING', '🔄 <strong>Connecting to server...</strong> Please wait.', 'BUSY');
                        return;
                    }

                    // 3. Check for active OTP prompt
                    const otpMsg = detectOtpPrompt();
                    if (otpMsg) {
                        isPaused = true;
                        if (toggleBtn) {
                            toggleBtn.innerHTML = '<span>▶</span><span>Resume</span>';
                            toggleBtn.style.background = '#10b981';
                        }
                        setHudState('action_required', 'OTP REQUIRED', `🔐 <strong>Action Required:</strong> ${otpMsg}`, 'OTP');
                        return;
                    }

                    // 4. Check for active CAPTCHA
                    if (isCaptchaPresent()) {
                        setHudState('action_required', 'CAPTCHA', '🧩 <strong>Action Required:</strong> Please solve CAPTCHA on screen.', 'CAPTCHA');
                        return;
                    }

                    // 5. Restore inputs if portal wiped them on error
                    const currentUserInput = document.querySelector('#signInName, input[name="Sign in name"], input[name="Email Address"], input[name="Email"], input[type="email"], input[type="text"]');
                    const currentPassInput = document.querySelector('#password, input[name="Password"], input[type="password"]');

                    if (currentUserInput && (!currentUserInput.value || currentUserInput.value.trim() === '')) {
                        currentUserInput.value = creds.u;
                        currentUserInput.dispatchEvent(new Event('input', { bubbles: true }));
                        currentUserInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    if (currentPassInput && (!currentPassInput.value || currentPassInput.value.trim() === '')) {
                        currentPassInput.value = creds.p || '';
                        currentPassInput.dispatchEvent(new Event('input', { bubbles: true }));
                        currentPassInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    // 6. Find button and trigger click
                    const btn = findSignInButton();
                    if (btn) {
                        autoClickAttempts++;
                        lastClickTimestamp = Date.now();
                        playClickTone();
                        highlightPortalButton(btn, autoClickAttempts);

                        try {
                            btn.focus();
                            btn.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
                            btn.dispatchEvent(new MouseEvent('mouseup', { bubbles: true, cancelable: true }));
                            btn.click();
                            if (btn.form && typeof btn.form.requestSubmit === 'function') {
                                btn.form.requestSubmit(btn);
                            }
                        } catch(e) {
                            btn.click();
                        }

                        console.log(`[Visa Auto-Fill Extension] Login clicked (Attempt #${autoClickAttempts})`);
                        setHudState('submitting', 'SUBMITTING', `🚀 <strong>Clicked (#${autoClickAttempts})!</strong> Checking response...`, 'SENT');
                    } else {
                        setHudState('loading', 'WAITING', '⏳ Looking for Login button...', 'WAIT');
                    }
                }

                // Initial status
                setHudState('default', 'READY', `✓ Credentials entered! First click in <strong>${countdownSeconds}s</strong>...`, `${countdownSeconds}s`);

                // Continuous 1-second monitoring loop
                countdownInterval = setInterval(() => {
                    if (isPaused) return;

                    // Check if login succeeded
                    if (isLoginSuccessful()) {
                        clearInterval(countdownInterval);
                        setHudState('success', 'LOGGED IN', '🎉 <strong>Login Successful!</strong> Portal loaded.', 'DONE');
                        setTimeout(() => {
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 400);
                        }, 5000);
                        return;
                    }

                    // Check if error appeared
                    const errorMsg = detectPortalError();
                    if (errorMsg) {
                        setHudState('error', 'PORTAL ERROR', `❌ <strong>Error:</strong> "${errorMsg}"`, `Retry ${countdownSeconds}s`);
                    }

                    // Check if OTP appeared
                    const otpMsg = detectOtpPrompt();
                    if (otpMsg) {
                        isPaused = true;
                        if (toggleBtn) {
                            toggleBtn.innerHTML = '<span>▶</span><span>Resume</span>';
                            toggleBtn.style.background = '#10b981';
                        }
                        setHudState('action_required', 'OTP REQUIRED', `🔐 <strong>Action Required:</strong> ${otpMsg}`, 'PAUSED');
                        return;
                    }

                    // Check if portal is currently busy/spinning
                    if (isPortalBusy()) {
                        setHudState('loading', 'AUTHENTICATING', '🔄 <strong>Connecting to server...</strong> Please wait.', 'BUSY');
                        return;
                    }

                    // Countdown step
                    countdownSeconds--;

                    if (countdownSeconds > 0) {
                        if (!errorMsg) {
                            if (lastClickTimestamp && (Date.now() - lastClickTimestamp < 2000)) {
                                setHudState('submitting', 'SUBMITTING', `🚀 Clicked (#${autoClickAttempts})! Waiting for server...`, `${countdownSeconds}s`);
                            } else if (autoClickAttempts > 0) {
                                setHudState('default', `RETRY #${autoClickAttempts + 1}`, `ℹ️ Re-clicking in <strong>${countdownSeconds}s</strong>...`, `${countdownSeconds}s`);
                            } else {
                                setHudState('default', 'COUNTDOWN', `⏳ Submitting first login in <strong>${countdownSeconds}s</strong>...`, `${countdownSeconds}s`);
                            }
                        }
                    } else if (countdownSeconds <= 0) {
                        countdownSeconds = 5;
                        performLoginClick();
                    }
                }, 1000);

                window.addEventListener('beforeunload', () => {
                    clearInterval(countdownInterval);
                });

            })();
            return;
        }

        // Mode 2: Sign Up Detection & Fill
        const emailInput = document.querySelector('#email, input[name*="email" i]');
        if (emailInput && creds.mode === 'signup') {
            clearInterval(timer);

            (async () => {
                setHudState('default', 'SIGN UP', '⌨️ Filling Sign Up details slowly...', '--');
                emailInput.style.outline = '2px solid #10b981';
                await typeSlowly(emailInput, creds.u, 45, 80);

                const pwds = document.querySelectorAll('input[type="password"]');
                for (let p of pwds) {
                    await new Promise(r => setTimeout(r, 350));
                    p.style.outline = '2px solid #10b981';
                    await typeSlowly(p, creds.p || '', 45, 80);
                }

                const fn = document.querySelector('#givenName, input[name*="given" i]');
                if (fn && creds.fn) {
                    await new Promise(r => setTimeout(r, 350));
                    await typeSlowly(fn, creds.fn, 45, 80);
                }
                const ln = document.querySelector('#surname, input[name*="sur" i]');
                if (ln && creds.ln) {
                    await new Promise(r => setTimeout(r, 350));
                    await typeSlowly(ln, creds.ln, 45, 80);
                }

                setHudState('success', 'FILLED', '✓ Sign Up details auto-filled successfully!', 'DONE');
                setTimeout(() => {
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 400);
                }, 5000);
            })();
            return;
        }

        if (attempts >= maxAttempts) {
            clearInterval(timer);
            setHudState('default', 'READY', 'ℹ️ Portal ready. Please proceed with login.', '--');
            setTimeout(() => {
                card.remove();
            }, 4000);
        }
    }, 500);
})();
