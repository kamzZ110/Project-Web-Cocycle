import {chromium} from '@playwright/test';
import {mkdir,writeFile} from 'node:fs/promises';
import {fileURLToPath} from 'node:url';
const base='http://127.0.0.1:8012';
const folder=new URL('../storage/testing/reference-browser/',import.meta.url);
await mkdir(folder,{recursive:true});
const browser=await chromium.launch({channel:'msedge',headless:true});
const results=[];
try {
  for(const viewport of [{width:1440,height:1000},{width:390,height:844}]) {
    const context=await browser.newContext({viewport});
    const page=await context.newPage();
    page.on('dialog',d=>d.accept());
    async function inspect(path,label) {
      const res=await page.goto(base+path,{waitUntil:'networkidle'});
      const data=await page.evaluate(()=>({title:document.title,overflow:document.documentElement.scrollWidth>innerWidth+2,headings:[...document.querySelectorAll('h1,h2,h3,h4,h5')].map(x=>x.textContent.trim()).slice(0,15)}));
      results.push({path,label,viewport:viewport.width,status:res.status(),...data});
      await page.screenshot({path:fileURLToPath(new URL(`${viewport.width}-${label}.png`,folder)),fullPage:true});
    }
    for(const [path,label] of [['/','landing'],['/backend/login.php','login'],['/backend/SignUp.php','register'],['/backend/SignUp.php?role=partner','register-partner']]) await inspect(path,label);
    async function login(email) {
      await page.goto(base+'/backend/login.php');
      await page.getByPlaceholder('Email',{exact:true}).fill(email);
      await page.getByPlaceholder('Password',{exact:true}).fill('CoCycleTest123!');
      await page.getByRole('button',{name:'Continue',exact:true}).click();
      await page.waitForURL(/homepage_[12]\.php/);
    }
    await login('customer@example.test');
    for(const [path,label] of [['/backend/homepage_1.php','customer-home'],['/explore/katalog.php','marketplace'],['/explore/cart.php','cart-empty'],['/donation/index.php','donation'],['/donation/checkout.php','donation-cart-empty'],['/donation/impact.php','impact'],['/donation/levels.php','levels']]) await inspect(path,label);
    await login('partner@example.test');
    for(const [path,label] of [['/partner/homepage_2.php','partner-home'],['/partner/produk.php','products'],['/partner/tambah_produk.php','create-product'],['/partner/riwayat.php','orders-empty'],['/partner/profil_partner.php','partner-profile'],['/partner/analisis.php','analysis'],['/partner/chat.php','partner-chat'],['/partner/tarik_dana.php','withdrawal']]) await inspect(path,label);
    await context.close();
  }
}finally{await writeFile(new URL('report.json',folder),JSON.stringify(results,null,2));await browser.close();}
console.log(JSON.stringify(results.map(({label,viewport,status,overflow})=>({label,viewport,status,overflow})),null,2));
