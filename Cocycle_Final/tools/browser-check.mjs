import { chromium } from '@playwright/test';
import { fileURLToPath } from 'node:url';
import { mkdir, writeFile, readFile } from 'node:fs/promises';
import assert from 'node:assert/strict';

const base = 'http://127.0.0.1:8011';
const runtime = JSON.parse(await readFile(new URL('../storage/testing/runtime.json', import.meta.url)));
assert.match(runtime.database, /^cocycle_test_/);
const output = new URL('../storage/testing/browser/', import.meta.url);
await mkdir(output, { recursive: true });
const browser = await chromium.launch({ channel: 'msedge', headless: true });
const results = [];
try {
    for (const viewport of [{width:1440,height:1000}, {width:390,height:844}]) {
        const context = await browser.newContext({viewport});
        const page = await context.newPage();
        let errors = [];
        page.on('pageerror', error => errors.push(error.message));
        page.on('dialog', dialog => dialog.accept());
        async function inspect(path, label) {
            errors = [];
            const response = await page.goto(base + path, {waitUntil:'networkidle'});
            const info = await page.evaluate(() => ({
                title: document.title,
                overflow: document.documentElement.scrollWidth > innerWidth + 2,
                brokenImages: [...document.images].filter(i => i.getAttribute('src') && i.getBoundingClientRect().width > 0 && !i.naturalWidth).map(i => i.getAttribute('src')),
                forms: [...document.forms].map(f => ({action:f.getAttribute('action'), fields:[...f.elements].map(e=>({name:e.name,type:e.type,text:e.tagName==='BUTTON'?e.textContent.trim():undefined}))})),
                overflowElements: [...document.querySelectorAll('body *')].filter(e => { const r=e.getBoundingClientRect(); return r.width>0 && (r.right>innerWidth+2 || r.left < -2); }).slice(0,10).map(e=>({tag:e.tagName,className:typeof e.className==='string'?e.className:'',left:Math.round(e.getBoundingClientRect().left),right:Math.round(e.getBoundingClientRect().right),width:Math.round(e.getBoundingClientRect().width)})),
            }));
            const result = {label, viewport:viewport.width, status:response.status(), url:page.url(), errors:[...errors], ...info};
            results.push(result);
            await page.screenshot({path:fileURLToPath(new URL(`${viewport.width}-${label}.png`, output)), fullPage:true});
            assert.equal(response.status(), 200, label);
        }
        for (const [path,label] of [['/','landing'],['/login','login'],['/register','register'],['/register?role=partner','register-partner'],['/forgot-password','forgot']]) await inspect(path,label);
        async function login(email) {
            await page.goto(base+'/login');
            await page.getByPlaceholder('Email', {exact:true}).fill(email);
            await page.getByPlaceholder('Password', {exact:true}).fill('CoCycleTest123!');
            await page.getByRole('button', {name:'Continue', exact:true}).click();
            await page.waitForURL(/\/(home|partner\/home)$/);
        }
        await login('customer@example.test');
        for (const [path,label] of [['/home','customer-home'],['/marketplace','marketplace'],['/cart','cart-empty'],['/donation','donation'],['/donation/cart','donation-cart-empty'],['/donation/impact','impact'],['/donation/levels','levels'],['/chat','customer-chat']]) await inspect(path,label);
        await login('partner@example.test');
        for (const [path,label] of [['/partner/home','partner-home'],['/partner/products','products'],['/partner/products/create','create-product'],['/partner/orders','orders-empty'],['/partner/profile','partner-profile'],['/partner/analysis','analysis'],['/partner/chat','partner-chat'],['/partner/withdrawal','withdrawal']]) await inspect(path,label);
        await context.close();
    }
} finally {
    await writeFile(new URL('report.json',output), JSON.stringify(results,null,2));
    await browser.close();
}
console.log(JSON.stringify(results.map(({label,viewport,status,overflow,brokenImages,errors})=>({label,viewport,status,overflow,brokenImages,errors})),null,2));
