import { chromium } from '@playwright/test';
import assert from 'node:assert/strict';
import { readFile, writeFile } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';

const base = 'http://127.0.0.1:8011';
const runtime = JSON.parse(await readFile(new URL('../storage/testing/runtime.json', import.meta.url)));
assert.match(runtime.database, /^cocycle_test_/);
const browser = await chromium.launch({ channel: 'msedge', headless: true });
const results = [];
try {
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
    const page = await context.newPage();
    page.on('dialog', dialog => dialog.accept());
    page.on('pageerror', error => results.push(`page error: ${error.message}`));
    async function login(email, landing) {
        await page.goto(`${base}/login`);
        await page.getByPlaceholder('Email', { exact: true }).fill(email);
        await page.getByPlaceholder('Password', { exact: true }).fill('CoCycleTest123!');
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.waitForURL(`**${landing}`);
    }

    await login('customer@example.test', '/home');
    await page.goto(`${base}/marketplace`);
    assert.ok(await page.locator('.add-cart-direct').count(), 'catalog has no purchasable products');
    await page.locator('.add-cart-direct').first().click();
    await page.goto(`${base}/cart`);
    assert.ok(await page.locator('tbody tr').count(), 'product did not reach customer cart');
    let updateResponse = page.waitForResponse(response => response.url().includes('/cart/update'));
    await page.locator('.update-qty[data-type="plus"]').first().click();
    await updateResponse;
    await page.waitForLoadState('networkidle');
    assert.equal(await page.locator('.qty-input').first().inputValue(), '2');
    updateResponse = page.waitForResponse(response => response.url().includes('/cart/update'));
    await page.locator('.update-qty[data-type="minus"]').first().click();
    await updateResponse;
    await page.waitForLoadState('networkidle');
    assert.equal(await page.locator('.qty-input').first().inputValue(), '1');
    await page.locator('#checkout-btn').click();
    await page.waitForTimeout(500);
    await page.reload();
    assert.match(await page.locator('body').innerText(), /Keranjangmu kosong/);
    results.push('catalog -> cart quantity update -> checkout');

    await page.goto(`${base}/donation`);
    assert.ok(await page.locator('form[action*="/donation/add"] button[type="submit"]').count(), 'donation catalog is empty');
    await page.locator('form[action*="/donation/add"] button[type="submit"]').first().click();
    await page.goto(`${base}/donation/cart`);
    assert.ok(await page.locator('.item-row').count(), 'donation item did not reach cart');
    await page.locator('button[type="submit"]').first().click();
    await page.waitForURL('**/donation/impact');
    assert.match(await page.locator('body').innerText(), /Pelaporan Dampak/);
    results.push('donation catalog -> donation cart -> impact');

    await page.goto(`${base}/chat`);
    await page.locator('#searchInput').fill('Browser Partner');
    const searchResponse = await page.waitForResponse(response => response.url().includes('/chat/search'));
    const contacts = await searchResponse.json();
    assert.ok(contacts.length, 'contact search returned no partner');
    await page.locator('.chat-item').first().click();
    await page.locator('#msgInput').fill('Pesan uji browser');
    const sent = page.waitForResponse(response => response.url().includes('/chat/send'));
    await page.locator('#btnSend').click();
    const sendResponse = await sent;
    assert.equal(sendResponse.status(), 200, `chat send failed: ${await sendResponse.text()}`);
    const sendData = await sendResponse.json();
    assert.equal(sendData.data.message, 'Pesan uji browser');
    await page.reload();
    await page.locator('.chat-item').first().click();
    await page.waitForFunction(() => document.querySelector('#messageArea')?.innerText.includes('Pesan uji browser'));
    results.push('customer chat contact search -> send');

    await login('partner@example.test', '/partner/home');
    await page.goto(`${base}/partner/products/create`);
    await page.locator('[name="name"]').fill('Produk Browser CoCycle');
    await page.locator('[name="category"]').selectOption('Snacks');
    await page.locator('[name="price"]').fill('12500');
    await page.locator('[name="stock"]').fill('3');
    await page.locator('[name="status"]').selectOption('active');
    await page.locator('#productForm button[type="submit"]').click();
    await page.waitForURL('**/partner/products');
    assert.match(await page.locator('body').innerText(), /Produk Browser CoCycle/);
    results.push('partner create product');

    await page.goto(`${base}/partner/orders`);
    assert.match(await page.locator('body').innerText(), /Preparing/);
    await page.getByRole('button', { name: 'Set Ready' }).first().click();
    await page.waitForTimeout(400);
    await page.getByRole('button', { name: 'Complete' }).first().waitFor();
    await page.getByRole('button', { name: 'Complete' }).first().click();
    await page.waitForTimeout(400);
    assert.match(await page.locator('body').innerText(), /Completed/);
    results.push('partner order Ready -> Completed');

    await page.goto(`${base}/partner/chat`);
    await page.locator('#searchInput').fill('Browser Customer');
    const partnerSearch = await page.waitForResponse(response => response.url().includes('/partner/chat/search'));
    const partnerContacts = await partnerSearch.json();
    assert.ok(partnerContacts.some(contact => contact.id === runtime.customer_id), 'partner cannot find customer');
    await page.locator('.chat-item').first().click();
    const partnerMessages = await page.waitForResponse(response => response.url().includes('/partner/chat/messages'));
    const received = await partnerMessages.json();
    assert.ok(received.some(message => message.message === 'Pesan uji browser'), `chat history missing: ${JSON.stringify(received)}`);
    assert.match(await page.locator('#messageArea').innerText(), /Pesan uji browser/);
    results.push('partner chat receives customer message');
    await context.close();
} finally {
    await writeFile(fileURLToPath(new URL('../storage/testing/browser/flows.json', import.meta.url)), JSON.stringify(results, null, 2));
    await browser.close();
}
console.log(JSON.stringify(results, null, 2));
