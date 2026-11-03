import { chromium } from '@playwright/test';
import { readFile } from 'node:fs/promises';
import assert from 'node:assert/strict';

const runtime = JSON.parse(await readFile(new URL('../storage/testing/runtime.json', import.meta.url)));
assert.match(runtime.database, /^cocycle_test_/);
const base = 'http://127.0.0.1:8011';
const browser = await chromium.launch({ channel: 'msedge', headless: true });
try {
    for (const scenario of [
        { destination: '/partner/orders', email: 'customer@example.test', home: '/home' },
        { destination: '/marketplace', email: 'partner@example.test', home: '/partner/home' },
        { destination: '/cart', email: 'customer@example.test', home: '/cart' },
    ]) {
        const context = await browser.newContext();
        const page = await context.newPage();
        await page.goto(base + scenario.destination);
        await page.waitForURL('**/login');
        await page.getByPlaceholder('Email', { exact: true }).fill(scenario.email);
        await page.getByPlaceholder('Password', { exact: true }).fill('CoCycleTest123!');
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.waitForURL(`**${scenario.home}`);
        if (scenario.email === 'customer@example.test') {
            const response = await page.goto(base + '/partner/products');
            assert.equal(response.status(), 403);
        }
        console.log(`${scenario.destination} -> ${scenario.home}: OK`);
        await context.close();
    }
} finally {
    await browser.close();
}
