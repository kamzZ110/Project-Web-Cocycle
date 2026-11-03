import { chromium } from '@playwright/test';
import assert from 'node:assert/strict';
import { readFile, writeFile } from 'node:fs/promises';
const runtime = JSON.parse(await readFile(new URL('../storage/testing/runtime.json', import.meta.url)));
assert.match(runtime.database, /^cocycle_test_/);
const base = 'http://127.0.0.1:8011';
const browser = await chromium.launch({ channel: 'msedge', headless: true });
const suffix = Date.now();
const email = `starter${suffix}@example.test`;
const store = `Starter Store ${suffix}`;
const errors = [];
try {
  const partner = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
  partner.on('pageerror', e => errors.push(e.message));
  await partner.goto(`${base}/register?role=partner`);
  for (const [name, value] of Object.entries({username:`starter${suffix}`,email,phone:'0812345678',address:'Alamat uji',nama_toko:store,password:'CoCycleTest123!'})) {
    await partner.locator(`[name="${name}"]`).fill(value);
  }
  await partner.getByRole('button', {name:'Continue',exact:true}).click();
  await partner.waitForURL('**/login');
  async function login(page, address, target) {
    await page.goto(`${base}/login`);
    await page.getByPlaceholder('Email', {exact:true}).fill(address);
    await page.getByPlaceholder('Password', {exact:true}).fill('CoCycleTest123!');
    await page.getByRole('button', {name:'Continue',exact:true}).click();
    await page.waitForURL(`**${target}`);
  }
  await login(partner, email, '/partner/home');
  await partner.goto(`${base}/partner/products`);
  assert.equal(await partner.locator('.btn-ubah').count(),7);
  await partner.reload();
  assert.equal(await partner.locator('.btn-ubah').count(),7);
  const customer = await browser.newPage({viewport:{width:390,height:844}});
  customer.on('dialog', d=>d.accept());
  customer.on('pageerror', e=>errors.push(e.message));
  await login(customer,'customer@example.test','/home');
  await customer.goto(`${base}/marketplace?resto=${encodeURIComponent(store)}`);
  assert.equal(await customer.locator('.add-cart-direct').count(),7);
  assert.deepEqual(await customer.locator('.product-card img').evaluateAll(imgs=>imgs.filter(i=>!i.complete || i.naturalWidth===0).map(i=>i.src)),[]);
  assert.deepEqual(errors,[], 'JavaScript errors before adding product');
  const added = customer.waitForResponse(r=>r.url().endsWith('/cart/add'));
  await customer.locator('.add-cart-direct').first().click();
  assert.equal((await added).status(),200);
  await customer.goto(`${base}/cart`);
  const checked = customer.waitForResponse(r=>r.url().endsWith('/cart/checkout'));
  await customer.locator('#checkout-btn').click();
  assert.equal((await checked).status(),200);
  await partner.goto(`${base}/partner/orders`);
  const detail = partner.getByRole('link',{name:'Detail',exact:true});
  assert.equal(await detail.count(),1);
  const orderPath = await detail.getAttribute('href');
  await detail.click();
  assert.match(await partner.locator('body').innerText(),/Red Sweet Pepper/);
  const other = await browser.newPage();
  await login(other,'partner@example.test','/partner/home');
  assert.equal((await other.goto(orderPath)).status(),404);
  assert.deepEqual(errors,[]);
  await writeFile(new URL('../storage/testing/starter-catalog-results.json',import.meta.url),JSON.stringify({passed:true,database:runtime.database,checks:['partner registration creates 7 products','reload does not duplicate','customer mobile catalog filters new store','checkout visible to correct partner','other partner cannot access order'],errors},null,2));
  console.log('PASS: registration, 7 owned products, no duplicates, mobile customer checkout, partner receipt, cross-store denial.');
} finally {console.log(JSON.stringify({errors})); await browser.close();}
