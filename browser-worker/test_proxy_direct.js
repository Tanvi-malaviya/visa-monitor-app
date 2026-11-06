const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true, channel: 'chrome' });
    const context = await browser.newContext();
    const page = await context.newPage();

    page.on('console', msg => console.log('PAGE LOG:', msg.type(), msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));
    page.on('requestfailed', req => console.log('REQ FAILED:', req.url(), req.failure()?.errorText));
    page.on('response', resp => {
        if (resp.status() >= 400) {
            console.log('HTTP ERROR:', resp.status(), resp.url());
        }
    });

    // Login
    await page.goto('http://127.0.0.1:8002/login');
    await page.fill('input[name=email]', 'admin@sathwara.com');
    await page.fill('input[name=password]', 'admin123');
    await page.click('button[type=submit]');
    await page.waitForTimeout(2000);

    console.log('Current URL after login:', page.url());

    // Navigate to customers page
    await page.goto('http://127.0.0.1:8002/admin/customers');
    await page.waitForTimeout(2000);

    // Click the button to open modal
    console.log('Opening modal...');
    await page.click('button:has-text("Auto-Fill & Book")');
    await page.waitForTimeout(5000);

    // Take screenshot of the page
    await page.screenshot({ path: 'C:/Users/DELL/.gemini/antigravity-ide/scratch/modal_actual.png' });
    console.log('Saved modal_actual.png');

    // Also navigate directly to portal-proxy to see what it renders
    const proxyPage = await context.newPage();
    proxyPage.on('console', msg => console.log('PROXY LOG:', msg.type(), msg.text()));
    proxyPage.on('pageerror', err => console.log('PROXY ERROR:', err.message));
    proxyPage.on('requestfailed', req => console.log('PROXY REQ FAILED:', req.url(), req.failure()?.errorText));

    console.log('Navigating directly to portal-proxy...');
    await proxyPage.goto('http://127.0.0.1:8002/admin/portal-proxy', { waitUntil: 'networkidle' }).catch(e => console.log('goto err:', e.message));
    await proxyPage.waitForTimeout(3000);
    await proxyPage.screenshot({ path: 'C:/Users/DELL/.gemini/antigravity-ide/scratch/proxy_direct.png' });
    console.log('Saved proxy_direct.png');

    await browser.close();
})();
