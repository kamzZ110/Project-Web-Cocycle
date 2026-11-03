import { chromium } from '@playwright/test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';

const runtime = JSON.parse(await readFile(new URL('../storage/testing/runtime.json', import.meta.url)));
assert.match(runtime.database, /^cocycle_test_[a-zA-Z0-9_]+$/);
const base = 'http://127.0.0.1:8011';
const suffix = Date.now().toString();
const password = 'CoCycleTest123!';
const partnerName = `Partner Audit ${suffix}`;
const customerName = `CustomerAudit${suffix}`;
const browser = await chromium.launch({ channel: 'msedge', headless: true });
const errors = [];
const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
const page = await context.newPage();
page.on('pageerror', error => errors.push(error.message));
page.on('dialog', dialog => dialog.accept());

async function register(role, username, store) {
  const email = `${username}@example.test`;
  await page.goto(`${base}/register?role=${role}`);
  for (const [name, value] of Object.entries({ username, email, phone: '08123456789', address: 'Alamat uji', password })) {
    await page.locator(`[name="${name}"]`).fill(value);
  }
  if (role === 'partner') await page.locator('[name="nama_toko"]').fill(store);
  await page.getByRole('button', { name: 'Continue', exact: true }).click();
  await page.waitForURL('**/login');
  return email;
}
async function login(email, target) {
  await page.goto(`${base}/login`);
  await page.getByPlaceholder('Email', { exact: true }).fill(email);
  await page.getByPlaceholder('Password', { exact: true }).fill(password);
  await page.getByRole('button', { name: 'Continue', exact: true }).click();
  await page.waitForURL(`**${target}`);
}
async function logout() {
  await page.locator('form[action$="/logout"] button').first().click();
  await page.waitForURL(`${base}/`);
}

try {
  const partnerEmail = await register('partner', `partner${suffix}`, partnerName);
  await login(partnerEmail, '/partner/home');
  await page.goto(`${base}/cart`);
  await page.waitForURL('**/partner/home');
  await page.getByRole('alert').getByText('Keranjang hanya untuk akun customer', { exact: false }).waitFor();
  await page.getByRole('button', { name: 'Keluar untuk ganti akun' }).click();
  const customerEmail = await register('customer', customerName);
  await login(customerEmail, '/home');
  await page.goto(`${base}/marketplace`);
  const choice = page.locator('.store-choice').filter({ has: page.locator('option', { hasText: partnerName }) }).first();
  await choice.waitFor();
  const card = choice.locator('xpath=ancestor::div[contains(concat(" ", normalize-space(@class), " "), " product-card ")]');
  const productName = await card.locator('.card-title').innerText();
  assert.equal(await page.locator('.product-card').filter({ has: page.locator('.card-title', { hasText: productName }) }).count(), 1);
  await choice.selectOption({ label: partnerName });
  const added = page.waitForResponse(response => response.url().endsWith('/cart/add'));
  await card.locator('.add-cart-direct').click();
  assert.equal((await added).status(), 200);
  await page.goto(`${base}/cart`);
  await page.getByText(`Toko: ${partnerName}`).waitFor();
  const checked = page.waitForResponse(response => response.url().endsWith('/cart/checkout'));
  const confirmation = page.waitForEvent('dialog', { predicate: dialog => dialog.message().includes('Pesanan berhasil') });
  await page.locator('#checkout-btn').click();
  const checkout = await checked;
  assert.equal(checkout.status(), 200);
  assert.match((await confirmation).message(), new RegExp(partnerName));
  // Logging in as the partner in a separate browser context mirrors separate sessions.
  const partnerContext = await browser.newContext();
  const partnerPage = await partnerContext.newPage();
  await partnerPage.goto(`${base}/login`);
  await partnerPage.getByPlaceholder('Email', { exact: true }).fill(partnerEmail);
  await partnerPage.getByPlaceholder('Password', { exact: true }).fill(password);
  await partnerPage.getByRole('button', { name: 'Continue', exact: true }).click();
  await partnerPage.waitForURL('**/partner/home');
  await partnerPage.goto(`${base}/partner/orders`);
  await partnerPage.getByText(customerName, { exact: false }).first().waitFor();
  assert.deepEqual(errors, []);
  await partnerContext.close();
  console.log('PASS: grouped listing, seller selection, cart, checkout, selected partner order, and cross-role cart message.');
} finally {
  await context.close();
  await browser.close();
}
