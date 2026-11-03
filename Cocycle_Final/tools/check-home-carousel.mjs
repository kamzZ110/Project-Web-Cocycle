import { chromium } from '@playwright/test';
import { readFile } from 'node:fs/promises';
import assert from 'node:assert/strict';

const runtime = JSON.parse(await readFile(new URL('../storage/testing/runtime.json', import.meta.url)));
assert.match(runtime.database, /^cocycle_test_/);
const browser = await chromium.launch({ channel: 'msedge', headless: true });
const measurements = [];
try {
    for (const viewport of [{ width: 1440, height: 900 }, { width: 390, height: 844 }]) {
        const context = await browser.newContext({ viewport });
        const page = await context.newPage();
        await page.goto('http://127.0.0.1:8011/login');
        await page.getByPlaceholder('Email', { exact: true }).fill('customer@example.test');
        await page.getByPlaceholder('Password', { exact: true }).fill('CoCycleTest123!');
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.waitForURL('**/home');
        const slides = [];
        for (let index = 0; index < 3; index++) {
            const active = page.locator('#home .carousel-item.active img');
            await active.waitFor();
            slides.push(await active.evaluate(image => ({ alt: image.alt, height: image.getBoundingClientRect().height, width: image.getBoundingClientRect().width, loaded: image.naturalWidth > 0 })));
            if (index < 2) {
                const oldAlt = await active.getAttribute('alt');
                await page.locator('#home .carousel-control-next').click();
                await page.waitForFunction(previous => document.querySelector('#home .carousel-item.active img')?.alt !== previous, oldAlt);
                await page.waitForTimeout(700);
            }
        }
        assert.equal(new Set(slides.map(slide => Math.round(slide.height))).size, 1);
        assert.ok(slides.every(slide => slide.loaded && slide.height >= 220 && slide.height <= 400 && Math.round(slide.width) === viewport.width));
        assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth + 2), false);
        measurements.push({ viewport: viewport.width, slides });
        await context.close();
    }
} finally {
    await browser.close();
}
console.log(JSON.stringify(measurements, null, 2));
