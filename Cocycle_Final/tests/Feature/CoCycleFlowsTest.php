<?php

namespace Tests\Feature;

use App\Models\{User, Store, Product, Cart, Order, FoodItem, Donation, Message};
use Illuminate\Support\Facades\{Artisan, DB, Hash, Notification, Password};
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CoCycleFlowsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('These tests require isolated SQLite :memory:.');
        }
        Artisan::call('migrate', ['--force' => true]);
    }

    private function account(string $role = 'customer'): User
    {
        $suffix = bin2hex(random_bytes(4));
        return User::create(['username' => 'test'.$suffix, 'email' => "$suffix@example.test", 'phone' => '0812345678',
            'password' => 'TestPass123!', 'role' => $role, 'nama_toko' => $role === 'partner' ? 'Test Store' : null]);
    }

    private function product(User $partner, array $attributes = []): Product
    {
        $store = $partner->store()->firstOrCreate([], ['store_name' => 'Test Store', 'status' => 'active']);
        return $store->products()->create(array_merge(['name' => 'Same product name', 'category' => 'Snacks',
            'price' => 150000, 'stock' => 5, 'image' => 'assets/default_product.png', 'status' => 'active'], $attributes));
    }

    public function test_all_pages_render_for_correct_role_and_empty_data(): void
    {
        foreach (['/', '/login', '/register', '/register?role=partner', '/forgot-password'] as $path) $this->get($path)->assertOk();
        $this->actingAs($this->account());
        foreach (['/home', '/marketplace', '/cart', '/donation', '/donation/cart', '/donation/impact', '/donation/levels', '/chat'] as $path) {
            $this->get($path)->assertOk();
        }
        $partner = $this->account('partner');
        $this->actingAs($partner);
        foreach (['/partner/home', '/partner/products', '/partner/products/create', '/partner/orders', '/partner/profile', '/partner/analysis', '/partner/chat', '/partner/withdrawal'] as $path) {
            $this->get($path)->assertOk();
        }
        $this->assertDatabaseHas('stores', ['user_id' => $partner->id]);
    }

    public function test_registration_login_logout_and_partner_store(): void
    {
        foreach (['customer', 'partner'] as $role) {
            $this->post('/register', ['username' => $role, 'email' => "$role@example.test", 'phone' => '08123',
                'address' => 'Test address', 'password' => 'TestPass123!', 'role_type' => $role, 'nama_toko' => 'New Store'])->assertRedirect('/login');
            $user = User::where('email', "$role@example.test")->firstOrFail();
            $this->assertSame($role === 'partner', $user->store()->exists());
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
            $this->post('/login', ['email' => $user->email, 'password' => 'TestPass123!'])->assertRedirect($role === 'partner' ? '/partner/home' : '/home');
            $this->assertAuthenticatedAs($user);
            $this->post('/logout')->assertRedirect('/');
            $this->assertGuest();
        }
        $this->post('/register', ['username' => 'customer', 'email' => 'customer@example.test'])->assertSessionHasErrors();
    }

    public function test_roles_and_store_ownership_are_enforced(): void
    {
        $customer = $this->account();
        $partner = $this->account('partner');
        $foreign = $this->product($partner);
        $this->actingAs($customer)->get('/partner/products')->assertRedirect('/home')->assertSessionHas('role_notice');
        $this->actingAs($customer)->post('/partner/products/store', [])->assertRedirect('/home')->assertSessionHas('role_notice');
        $other = $this->account('partner');
        $this->actingAs($other)->get("/partner/products/{$foreign->id}/edit")->assertNotFound();
        $this->delete("/partner/products/{$foreign->id}/delete")->assertNotFound();
        $this->assertNotSame($other->fresh()->store->id, $partner->store->id);
    }

    public function test_starter_products_are_owned_once_and_checkout_reaches_the_selected_partner(): void
    {
        $service = app(\App\Services\PartnerStarterCatalog::class);
        $first = $this->account('partner'); $second = $this->account('partner');
        $a = $service->initialize($first); $b = $service->initialize($second);
        $this->assertSame(7, $a->products()->count());
        $this->assertSame(7, $b->products()->count());
        $this->assertNotSame($a->id, $b->id);
        $service->initialize($first);
        $this->assertSame(7, $a->products()->count());
        $product = $b->products()->firstOrFail();
        $originalStock = $product->stock;
        $this->actingAs($this->account());
        $catalog = $this->get('/marketplace')->assertOk()->getContent();
        $this->assertSame(7, substr_count($catalog, 'class="card h-100 product-card view-btn w-100 shadow-sm"'));
        $this->assertStringContainsString('value="'.$product->id.'"', $catalog);
        $this->postJson('/cart/add', ['product_id' => $product->id])->assertOk();
        $this->get('/cart')->assertSee('Toko:')->assertSee($b->store_name);
        $this->postJson('/cart/checkout')->assertOk()->assertJsonPath('message', 'Pesanan berhasil dikirim ke: '.$b->store_name);
        $order = Order::firstOrFail();
        $this->assertEquals($b->id, $order->store_id);
        $this->assertEquals($originalStock - 1, $product->fresh()->stock);
        $this->actingAs($second)->get('/partner/orders/'.$order->id)->assertOk();
        $this->actingAs($first)->get('/partner/orders/'.$order->id)->assertNotFound();
        $a->products()->delete();
        $service->initialize($first);
        $this->assertSame(0, $a->products()->count(), 'Deleted starter products must not return on login.');
        $existing = $this->account('partner');
        $custom = $this->product($existing);
        $service->initialize($existing);
        $this->assertSame(1, $existing->store()->first()->products()->count());
        $this->assertEquals(150000, $custom->fresh()->price);
    }

    public function test_partner_opening_customer_cart_receives_clear_account_switch_message(): void
    {
        $partner = $this->account('partner');
        $this->actingAs($partner)->get('/cart')->assertRedirect('/partner/home')->assertSessionHas('role_notice');
        $this->get('/partner/home')->assertOk()->assertSee('Halaman ini hanya untuk customer');
        $this->postJson('/cart/checkout')->assertForbidden()->assertJsonPath('message', 'Halaman ini hanya untuk customer. Sesi browser ini sedang memakai akun partner.');
    }

    public function test_customer_owned_legacy_products_are_preserved_but_not_purchasable(): void
    {
        $legacy = $this->product($this->account(), ['name' => 'Legacy orphan product']);
        $customer = $this->account();
        $this->actingAs($customer)->get('/marketplace')->assertOk()->assertDontSee('Legacy orphan product');
        $this->postJson('/cart/add', ['product_id' => $legacy->id])->assertUnprocessable();
        Cart::create(['user_id' => $customer->id, 'product_id' => $legacy->id, 'product_name' => $legacy->name,
            'product_price' => $legacy->price, 'product_image' => $legacy->image, 'quantity' => 1]);
        $this->get('/cart')->assertOk()->assertSee('Item dari toko lama belum terhubung ke partner');
        $this->postJson('/cart/checkout')->assertUnprocessable()->assertJsonValidationErrors('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $legacy->id, 'stock' => 5]);
    }

    public function test_login_ignores_intended_page_for_a_different_role(): void
    {
        $customer = $this->account();
        $partner = $this->account('partner');

        $this->get('/partner/orders')->assertRedirect('/login');
        $this->post('/login', ['email' => $customer->email, 'password' => 'TestPass123!'])->assertRedirect('/home');
        $this->post('/logout');

        $this->get('/marketplace')->assertRedirect('/login');
        $this->post('/login', ['email' => $partner->email, 'password' => 'TestPass123!'])->assertRedirect('/partner/home');
        $this->post('/logout');

        $this->get('/cart')->assertRedirect('/login');
        $this->post('/login', ['email' => $customer->email, 'password' => 'TestPass123!'])->assertRedirect('/cart');
    }

    public function test_cart_prices_stock_store_grouping_and_order_status(): void
    {
        $customer = $this->account(); $partner = $this->account('partner'); $other = $this->account('partner');
        $one = $this->product($partner); $two = $this->product($other, ['price' => 25000]);
        $this->actingAs($customer);
        foreach ([$one, $two] as $product) $this->postJson('/cart/add', ['product_id' => $product->id, 'price' => 1])->assertOk();
        $cart = Cart::where('product_id', $one->id)->firstOrFail();
        $this->postJson('/cart/update', ['cart_id' => $cart->id, 'qty' => -1])->assertUnprocessable();
        $this->postJson('/cart/update', ['cart_id' => $cart->id, 'qty' => 6])->assertUnprocessable();
        $this->postJson('/cart/update', ['cart_id' => $cart->id, 'qty' => 2])->assertOk();
        $one->update(['price' => 160000]);
        $this->postJson('/cart/checkout')->assertOk();
        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseCount('cart', 0);
        $order = Order::where('store_id', $one->store_id)->firstOrFail();
        $this->assertEquals(320000, $order->total_price);
        $this->assertEquals(3, $one->fresh()->stock);
        $this->postJson('/cart/checkout')->assertUnprocessable();
        $this->actingAs($other)->get("/partner/orders/{$order->id}")->assertNotFound();
        $this->actingAs($partner)->get("/partner/orders/{$order->id}")->assertOk();
        $this->postJson('/partner/orders/update-status', ['order_id' => $order->id, 'status' => 'Ready'])->assertOk();
        $this->postJson('/partner/orders/update-status', ['order_id' => $order->id, 'status' => 'Completed'])->assertOk();
        $this->assertNotNull($order->fresh()->completed_at);
        $this->assertEquals(288000, (float) $partner->fresh()->balance);
        $this->assertDatabaseCount('partner_wallet_transactions', 1);
        $this->postJson('/partner/orders/update-status', ['order_id' => $order->id, 'status' => 'Completed'])->assertOk();
        $this->assertEquals(288000, (float) $partner->fresh()->balance, 'An order must only credit the partner once.');
        $this->postJson('/partner/orders/update-status', ['order_id' => $order->id, 'status' => 'Cancelled'])->assertUnprocessable();
    }

    public function test_partner_wallet_reconciles_legacy_completed_orders_and_withdrawal_cancel_restores_balance(): void
    {
        $partner = $this->account('partner');
        $customer = $this->account();
        $store = $partner->store()->firstOrCreate([], ['store_name' => 'Test Store', 'status' => 'active']);
        $order = Order::create([
            'store_id' => $store->id,
            'user_id' => $customer->id,
            'invoice_number' => 'LEGACY-WALLET-1',
            'total_price' => 100000,
            'status' => 'Completed',
        ]);

        $this->actingAs($partner)->get('/partner/withdrawal')->assertOk()->assertSee('90.000');
        $this->assertEquals(90000, (float) $partner->fresh()->balance);
        $this->assertDatabaseHas('partner_wallet_transactions', ['order_id' => $order->id, 'type' => 'income', 'amount' => 90000]);

        $this->get('/partner/withdrawal')->assertOk();
        $this->assertDatabaseCount('partner_wallet_transactions', 1);

        $this->post('/partner/withdrawal', [
            'amount' => 25000,
            'bank_name' => 'Bank Demo',
            'account_name' => 'Partner Test',
            'account_number' => '1234567890',
        ])->assertRedirect('/partner/withdrawal')->assertSessionHas('success');
        $this->assertEquals(65000, (float) $partner->fresh()->balance);
        $withdrawal = \App\Models\PartnerWalletTransaction::where('user_id', $partner->id)->where('type', 'withdrawal')->firstOrFail();
        $this->assertSame('pending', $withdrawal->status);

        $this->post('/partner/withdrawal/'.$withdrawal->id.'/cancel')->assertRedirect('/partner/withdrawal');
        $this->assertEquals(90000, (float) $partner->fresh()->balance);
        $this->assertSame('cancelled', $withdrawal->fresh()->status);
        $this->post('/partner/withdrawal/'.$withdrawal->id.'/cancel')->assertNotFound();
        $this->assertEquals(90000, (float) $partner->fresh()->balance);
        $this->post('/partner/withdrawal', ['amount' => 90001, 'bank_name' => 'Test Bank',
            'account_name' => 'Test', 'account_number' => 'TEST-ONLY'])->assertSessionHasErrors('amount');
        $this->assertEquals(90000, (float) $partner->fresh()->balance);
    }

    public function test_checkout_failure_is_atomic_and_cancellation_restores_stock_once(): void
    {
        $user = $this->account(); $partner = $this->account('partner');
        $product = $this->product($partner);
        $this->actingAs($user)->postJson('/cart/add', ['product_id' => $product->id])->assertOk();
        $product->update(['stock' => 0]);
        $this->postJson('/cart/checkout')->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0); $this->assertDatabaseCount('cart', 1);
        $product->update(['stock' => 5]);
        $this->postJson('/cart/checkout')->assertOk();
        $order = Order::firstOrFail();
        $this->actingAs($partner);
        $this->postJson('/partner/orders/update-status', ['order_id' => $order->id, 'status' => 'Ready'])->assertOk();
        $this->get('/partner/orders/'.$order->id)->assertOk()->assertSee('Batalkan Pesanan')->assertSee('PESANAN SELESAI');
        for ($i = 0; $i < 2; $i++) $this->postJson('/partner/orders/update-status', ['order_id' => $order->id, 'status' => 'Cancelled'])->assertOk();
        $this->assertEquals(5, $product->fresh()->stock);
        $this->assertNotNull($order->fresh()->cancelled_at);
    }

    public function test_product_crud_validation_and_profile(): void
    {
        $partner = $this->account('partner'); $this->actingAs($partner);
        $data = ['name' => 'Fresh Bread', 'category' => 'Bakery & Pastry', 'price' => 150000, 'stock' => 2, 'status' => 'active'];
        $this->post('/partner/products/store', array_merge($data, ['stock' => -1]))->assertSessionHasErrors('stock');
        $this->post('/partner/products/store', $data)->assertRedirect('/partner/products');
        $product = Product::where('store_id', $partner->fresh()->store->id)->where('name', 'Fresh Bread')->firstOrFail();
        $this->assertEquals(150000, $product->price);
        $this->assertNotEmpty($product->image);
        $this->get("/partner/products/{$product->id}/edit")->assertOk();
        $this->post("/partner/products/{$product->id}/update", array_merge($data, ['name' => 'Updated Bread']))->assertRedirect();
        $this->post('/partner/profile', ['name' => 'Updated Store', 'email' => 'store@example.test', 'phone' => '123', 'address' => 'Address'])->assertRedirect();
        $this->assertDatabaseHas('stores', ['user_id' => $partner->id, 'store_name' => 'Updated Store']);
        $this->delete("/partner/products/{$product->id}/delete")->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $customer = $this->account(); $this->actingAs($customer);
        $this->postJson('/profile/update', ['username' => 'updated', 'phone' => '123', 'fullname' => 'Test Name', 'address' => 'Test'])->assertOk();
        $this->getJson('/profile/data')->assertJsonPath('fullname', 'Test Name');
    }

    public function test_chat_search_send_receive_and_isolation(): void
    {
        $customer = $this->account(); $partner = $this->account('partner'); $other = $this->account('partner');
        $this->actingAs($partner)->getJson('/partner/chat/search?q='.$customer->username)->assertOk()->assertJsonPath('0.id', $customer->id);
        $this->postJson('/partner/chat/send', ['receiver_id' => $customer->id, 'message' => '<script>Hello</script>'])->assertOk();
        $this->actingAs($customer)->getJson('/chat/messages?contact_id='.$partner->id)->assertJsonPath('0.message', '<script>Hello</script>')->assertJsonPath('0.is_me', false);
        $this->postJson('/chat/send', ['receiver_id' => $partner->id, 'message' => 'Reply'])->assertOk();
        $this->actingAs($partner)->getJson('/partner/chat/contacts')->assertJsonPath('0.last_message', 'Reply');
        $this->actingAs($other)->getJson('/partner/chat/messages?contact_id='.$customer->id)->assertExactJson([]);
    }

    public function test_donation_validation_checkout_and_impact(): void
    {
        $this->actingAs($this->account());
        $food = FoodItem::create(['store_name' => 'Test Store', 'name' => 'Bread', 'quantity_available' => 4, 'unit' => 'pcs', 'handling_fee' => 3000, 'status' => 'available']);
        $this->post('/donation/add', ['id' => $food->id, 'price' => 1])->assertRedirect();
        $this->assertEquals(3000, session('donation_cart')[0]['price']);
        $this->post('/donation/checkout', ['community' => 'Unknown'])->assertSessionHasErrors('community');
        $this->post('/donation/checkout', ['community' => 'Greenpeace Indonesia', 'qty' => [0 => -1]])->assertSessionHasErrors();
        $food->update(['status' => 'donated']);
        $this->post('/donation/checkout', ['community' => 'Greenpeace Indonesia'])->assertSessionHasErrors();
        $this->assertDatabaseCount('donations', 0);
        $food->update(['status' => 'available']);
        $this->post('/donation/checkout', ['community' => 'Greenpeace Indonesia', 'qty' => [0 => 2]])->assertRedirect('/donation/impact');
        $this->assertDatabaseHas('donations', ['total_amount' => 6000]);
        $this->assertDatabaseHas('impact_trackings', ['waste_saved_kg' => 1, 'co2_saved_kg' => 0.4]);
        $this->assertEquals(2, $food->fresh()->quantity_available);
        $this->get('/donation/impact')->assertOk()->assertSee('Greenpeace Indonesia');
    }

    public function test_reset_token_is_single_use_and_login_works(): void
    {
        Notification::fake();
        $user = $this->account();
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
        $token = Password::createToken($user);
        $data = ['email' => $user->email, 'token' => $token, 'password' => 'Changed123!', 'password_confirmation' => 'Changed123!'];
        $this->get('/reset-password/'.$token.'?email='.urlencode($user->email))->assertOk();
        $this->post('/reset-password', $data)->assertRedirect('/login');
        $this->assertTrue(Hash::check('Changed123!', $user->fresh()->password));
        $this->post('/reset-password', $data)->assertSessionHasErrors();
        $this->post('/login', ['email' => $user->email, 'password' => 'Changed123!'])->assertRedirect('/home');
    }
}
