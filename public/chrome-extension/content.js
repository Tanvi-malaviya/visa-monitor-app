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

    // Visual notification banner on top of the real portal
    const banner = document.createElement('div');
    banner.id = 'visa-autofill-notification';
    banner.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:9999999;background:linear-gradient(135deg,#0284c7,#10b981);color:#ffffff;text-align:center;padding:10px 16px;font-family:system-ui,-apple-system,sans-serif;font-size:14px;font-weight:600;box-shadow:0 4px 15px rgba(0,0,0,0.35);transition:all 0.3s ease;';
    banner.innerHTML = '🤖 <strong>Visa Assistant Extension:</strong> Auto-filling credentials for <u>' + (creds.fn ? (creds.fn + ' (' + creds.u + ')') : creds.u) + '</u>...';
    document.body.appendChild(banner);

    let attempts = 0;
    const maxAttempts = 40; // 20s

    const timer = setInterval(() => {
        attempts++;

        // Mode 1: Sign In Detection & Fill
        const userInput = document.querySelector('#signInName, input[name="Sign in name"], input[name="Email Address"], input[name="Email"], input[type="email"], input[type="text"]');
        const passInput = document.querySelector('#password, input[name="Password"], input[type="password"]');

        if (userInput && passInput) {
            clearInterval(timer);

            // Type username
            userInput.value = creds.u;
            userInput.dispatchEvent(new Event('input', { bubbles: true }));
            userInput.dispatchEvent(new Event('change', { bubbles: true }));
            userInput.style.outline = '2px solid #0284c7';

            // Type password
            passInput.value = creds.p || '';
            passInput.dispatchEvent(new Event('input', { bubbles: true }));
            passInput.dispatchEvent(new Event('change', { bubbles: true }));
            passInput.style.outline = '2px solid #10b981';

            banner.innerHTML = '✓ <strong>Visa Assistant Extension:</strong> Credentials auto-filled! Submitting Sign In...';

            setTimeout(() => {
                let signInBtn = document.getElementById('next') || document.getElementById('continue') || document.querySelector('button[type="submit"], input[type="submit"]');
                if (!signInBtn) {
                    signInBtn = Array.from(document.querySelectorAll('button')).find(b => (b.innerText || '').toLowerCase().includes('sign in'));
                }
                if (signInBtn) {
                    signInBtn.click();
                    banner.innerHTML = '✓ <strong>Sign In Submitted!</strong> Welcome to official portal.';
                    setTimeout(() => {
                        banner.style.opacity = '0';
                        setTimeout(() => banner.remove(), 400);
                    }, 4000);
                }
            }, 600);
            return;
        }

        // Mode 2: Sign Up Detection & Fill
        const emailInput = document.querySelector('#email, input[name*="email" i]');
        if (emailInput && creds.mode === 'signup') {
            clearInterval(timer);
            emailInput.value = creds.u;
            emailInput.dispatchEvent(new Event('input', { bubbles: true }));
            emailInput.dispatchEvent(new Event('change', { bubbles: true }));
            emailInput.style.outline = '2px solid #10b981';

            document.querySelectorAll('input[type="password"]').forEach(p => {
                p.value = creds.p || '';
                p.dispatchEvent(new Event('input', { bubbles: true }));
                p.dispatchEvent(new Event('change', { bubbles: true }));
                p.style.outline = '2px solid #10b981';
            });

            const fn = document.querySelector('#givenName, input[name*="given" i]');
            if (fn && creds.fn) {
                fn.value = creds.fn;
                fn.dispatchEvent(new Event('input', { bubbles: true }));
            }
            const ln = document.querySelector('#surname, input[name*="sur" i]');
            if (ln && creds.ln) {
                ln.value = creds.ln;
                ln.dispatchEvent(new Event('input', { bubbles: true }));
            }

            banner.innerHTML = '✓ <strong>Visa Assistant Extension:</strong> Sign Up details auto-filled!';
            setTimeout(() => {
                banner.style.opacity = '0';
                setTimeout(() => banner.remove(), 400);
            }, 5000);
            return;
        }

        if (attempts >= maxAttempts) {
            clearInterval(timer);
            banner.innerHTML = 'ℹ️ <strong>Visa Assistant:</strong> Portal ready. Please proceed with login.';
            setTimeout(() => banner.remove(), 4000);
        }
    }, 500);
})();

