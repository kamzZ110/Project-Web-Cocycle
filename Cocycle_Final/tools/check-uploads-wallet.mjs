import { chromium, expect } from '@playwright/test';
import assert from 'node:assert/strict';
import { readFile, writeFile } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';

const runtime = JSON.parse(await readFile(new URL('../storage/testing/runtime.json', import.meta.url)));
assert.match(runtime.database, /^cocycle_test_[a-zA-Z0-9_]+$/);
const base = 'http://127.0.0.1:8011';
const suffix = Date.now();
const imagePath = fileURLToPath(new URL('../public/assets/default_profile.png', import.meta.url));
const browser = await chromium.launch({ channel: 'msedge', headless: true });
const checks = [], errors = [];
let passed = false;
async function newAccount(role, width) {
  const context = await browser.newContext({viewport:{width,height:950}});
  const page = await context.newPage();
  page.on('pageerror', e=>errors.push(e.message));
  page.on('dialog', d=>d.accept());
  const username = `upload${role}${suffix}`, email = `${username}@example.test`;
  await page.goto(`${base}/register?role=${role}`);
  for (const [name,value] of Object.entries({username,email,phone:'08123456789',address:'Alamat pengujian',password:'CoCycleTest123!'})) {
    await page.locator(`[name="${name}"]`).fill(value);
  }
  if (role === 'partner') await page.locator('[name="nama_toko"]').fill(`Upload Store ${suffix}`);
  await page.getByRole('button',{name:'Continue',exact:true}).click();
  await page.waitForURL('**/login');
  await page.getByPlaceholder('Email',{exact:true}).fill(email);
  await page.getByPlaceholder('Password',{exact:true}).fill('CoCycleTest123!');
  await page.getByRole('button',{name:'Continue',exact:true}).click();
  await page.waitForURL(role==='partner'?'**/partner/home':'**/home');
  return page;
}
try {
  const customer = await newAccount('customer',390);
  await customer.locator('#profileBtn').click();
  await customer.getByText('Edit Profile',{exact:true}).first().click();
  await expect(customer.locator('#editProfileModal')).toBeVisible();
  const uploaded = customer.waitForResponse(r=>r.url().endsWith('/profile/photo'));
  await customer.locator('#modalProfilePicInput').setInputFiles(imagePath);
  const response = await uploaded;
  assert.equal(response.status(),200);
  const photo = (await response.json()).profile_pic;
  await expect(customer.locator('#profileBtn img')).toHaveAttribute('src',photo,{timeout:10000});
  await expect(customer.locator('#profileBtn img')).toBeVisible();
  assert.equal(await customer.locator('#profileBtn img').evaluate(img=>img.complete && img.naturalWidth>0),true);
  checks.push('customer photo upload and reload on mobile');

  const partner = await newAccount('partner',1440);
  await partner.goto(`${base}/partner/profile`);
  const logoPost = partner.waitForResponse(r=>r.url().endsWith('/partner/profile') && r.request().method()==='POST');
  await partner.locator('[name="logo_image"]').setInputFiles(imagePath);
  assert.equal((await logoPost).status(),302);
  await expect(partner.getByAltText('Logo Toko')).toBeVisible();
  assert.equal(await partner.getByAltText('Logo Toko').evaluate(img=>img.complete && img.naturalWidth>0),true);
  checks.push('partner logo upload and persisted image');

  const productName = `Upload product ${suffix}`;
  await partner.goto(`${base}/partner/products/create`);
  for (const [name,value] of Object.entries({name:productName,price:'10000',stock:'3',description:'Produk sementara untuk pengujian'})) {
    await partner.locator(`[name="${name}"]`).fill(value);
  }
  await partner.locator('[name="category"]').selectOption('Snacks');
  await partner.locator('[name="image"]').setInputFiles(imagePath);
  await partner.getByRole('button',{name:'Simpan Produk'}).click();
  await partner.waitForURL('**/partner/products');
  await expect(partner.getByText(productName,{exact:true})).toBeVisible();
  await customer.goto(`${base}/marketplace?search=${encodeURIComponent(productName)}`);
  await expect(customer.locator('.product-card')).toHaveCount(1);
  assert.equal(await customer.locator('.product-card img').evaluate(img=>img.complete && img.naturalWidth>0),true);
  checks.push('partner product image upload visible in customer catalog');
  const added = customer.waitForResponse(r=>r.url().endsWith('/cart/add'));
  await customer.locator('.add-cart-direct').click();
  assert.equal((await added).status(),200);
  await customer.goto(`${base}/cart`);
  const checkout = customer.waitForResponse(r=>r.url().endsWith('/cart/checkout'));
  await customer.locator('#checkout-btn').click();
  assert.equal((await checkout).status(),200);
  await partner.goto(`${base}/partner/orders`);
  await partner.getByRole('link',{name:'Detail',exact:true}).click();
  const ready = await Promise.all([
    partner.waitForResponse(r=>r.url().includes('/partner/orders/update-status')),
    partner.getByRole('button',{name:'SET READY',exact:true}).click(),
  ]);
  assert.equal(ready[0].status(),200);
  await expect(partner.getByRole('button',{name:'PESANAN SELESAI',exact:true})).toBeVisible();
  const completed = partner.waitForResponse(r=>r.url().includes('/partner/orders/update-status'));
  await partner.getByRole('button',{name:'PESANAN SELESAI',exact:true}).click();
  assert.equal((await completed).status(),200);
  await expect(partner.getByText('Pesanan ini sudah completed.',{exact:false})).toBeVisible();
  await partner.goto(`${base}/partner/withdrawal`);
  await expect(partner.locator('.saldo-amount')).toHaveText('Rp 9.000');
  await partner.reload();
  await expect(partner.locator('.saldo-amount')).toHaveText('Rp 9.000');
  checks.push('completed 10000 order credits 9000 once');
  for (const [name,value] of Object.entries({amount:'4000',bank_name:'Bank Uji',account_name:'Akun Uji',account_number:'TEST-ONLY-123'})) {
    await partner.locator(`[name="${name}"]`).fill(value);
  }
  await partner.getByRole('button',{name:'Ajukan Penarikan'}).click();
  await expect(partner.locator('.saldo-amount')).toHaveText('Rp 5.000');
  await expect(partner.locator('.trans-item').getByText('Menunggu diproses',{exact:false})).toBeVisible();
  await partner.getByRole('button',{name:'Batalkan',exact:true}).click();
  await expect(partner.locator('.saldo-amount')).toHaveText('Rp 9.000');
  await expect(partner.getByRole('button',{name:'Batalkan',exact:true})).toHaveCount(0);
  checks.push('withdrawal reserves 4000 and cancellation restores funds');
  await partner.setViewportSize({width:390,height:844});
  assert.equal(await partner.evaluate(()=>document.documentElement.scrollWidth <= innerWidth),true,'wallet mobile overflow');
  assert.deepEqual(errors,[]);
  passed = true;
  console.log(JSON.stringify({passed:true,checks}));
} finally {
  await writeFile(new URL('../storage/testing/uploads-wallet-results.json',import.meta.url),JSON.stringify({passed,date:new Date().toISOString(),database:runtime.database,checks,errors},null,2));
  await browser.close();
}
