const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true, channel: 'chrome' });
    const context = await browser.newContext();
    const page = await context.newPage();

    page.on('console', msg => console.log('PAGE LOG:', msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));
    page.on('requestfailed', req => console.log('REQ FAILED:', req.url(), req.failure()?.errorText));

    // Login first
    await page.goto('http://127.0.0.1:8002/login');
    await page.fill('input[name=email]', 'admin@visamonitor.com');
    await page.fill('input[name=password]', 'password');
    await page.click('button[type=submit]');
    await page.waitForTimeout(2000);

    console.log('Logged in URL:', page.url());

    await page.goto('http://127.0.0.1:8002/admin/customers');
    await page.waitForTimeout(2000);

    console.log('Clicking Auto-Fill & Book button...');
    await page.evaluate(() => {
        openThirdPartyPortal(4, 'Tanvi Malaviya', 'tanvi.sathwarainfotech@gmail.com', 'Pass@Tanvi2026', 'FEFEFEFE');
    });

    await page.waitForTimeout(6000);

    const frames = page.frames();
    console.log('Total frames:', frames.length);
    for (let i = 0; i < frames.length; i++) {
        const f = frames[i];
        console.log(`Frame ${i} URL:`, f.url());
        try {
            const content = await f.content();
            console.log(`Frame ${i} content length:`, content.length);
            console.log(`Frame ${i} preview:`, content.substring(0, 300).replace(/\n/g, ' '));
        } catch (e) {
            console.log(`Frame ${i} content error:`, e.message);
        }
    }

    await page.screenshot({ path: 'C:/Users/DELL/.gemini/antigravity-ide/scratch/test_iframe_result.png' });
    console.log('Screenshot saved to scratch/test_iframe_result.png');
    await browser.close();
})();
