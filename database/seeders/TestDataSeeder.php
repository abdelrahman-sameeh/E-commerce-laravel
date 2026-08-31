<?php

namespace Database\Seeders;

use App\Constants\UserRole;
use App\Models\Address;
use App\Models\Cart\Cart;
use App\Models\Cart\CartCoupon;
use App\Models\Cart\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Order\SubOrder;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductSubCategory;
use App\Models\SubCategory;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Users ────────────────────────────────────────────────────────

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name'  => 'System',
            'email'      => 'admin@test.com',
            'password'   => Hash::make('Ec1234sasa@#'),
            'role'       => UserRole::ADMIN,
        ]);

        $seller1 = User::create([
            'first_name' => 'Ahmed',
            'last_name'  => 'Electronics',
            'email'      => 'seller1@test.com',
            'password'   => Hash::make('Ec1234sasa@#'),
            'role'       => UserRole::SELLER,
        ]);

        $seller2 = User::create([
            'first_name' => 'Mohamed',
            'last_name'  => 'Fashion',
            'email'      => 'seller2@test.com',
            'password'   => Hash::make('Ec1234sasa@#'),
            'role'       => UserRole::SELLER,
        ]);

        $seller3 = User::create([
            'first_name' => 'Youssef',
            'last_name'  => 'Sports',
            'email'      => 'seller3@test.com',
            'password'   => Hash::make('Ec1234sasa@#'),
            'role'       => UserRole::SELLER,
        ]);

        $buyer1 = User::create([
            'first_name' => 'Ali',
            'last_name'  => 'Customer',
            'email'      => 'buyer1@test.com',
            'password'   => Hash::make('Ec1234sasa@#'),
            'role'       => UserRole::USER,
        ]);

        $buyer2 = User::create([
            'first_name' => 'Sara',
            'last_name'  => 'Customer',
            'email'      => 'buyer2@test.com',
            'password'   => Hash::make('Ec1234sasa@#'),
            'role'       => UserRole::USER,
        ]);

        $delivery = User::create([
            'first_name' => 'Omar',
            'last_name'  => 'Driver',
            'email'      => 'delivery@test.com',
            'password'   => Hash::make('Ec1234sasa@#'),
            'role'       => UserRole::DELIVERY,
        ]);

        // ─── 2. Addresses ────────────────────────────────────────────────────

        $addr1 = Address::create([
            'user_id'    => $buyer1->id,
            'country'    => 'EG',
            'city'       => 'Cairo',
            'street'     => '15 Tahrir Square, Downtown',
            'is_default' => true,
            'note'       => 'Next to the metro station',
            'latitude'   => 30.0444,
            'longitude'  => 31.2357,
        ]);

        Address::create([
            'user_id'    => $buyer1->id,
            'country'    => 'EG',
            'city'       => 'Giza',
            'street'     => '22 Pyramids Road',
            'is_default' => false,
            'note'       => 'Near the mall',
        ]);

        $addr3 = Address::create([
            'user_id'    => $buyer2->id,
            'country'    => 'EG',
            'city'       => 'Alexandria',
            'street'     => '5 Corniche Road',
            'is_default' => true,
        ]);

        // ─── 3. Categories & SubCategories ────────────────────────────────────

        $catElectronics = Category::create(['title' => 'Electronics']);
        $catFashion     = Category::create(['title' => 'Fashion']);
        $catSports      = Category::create(['title' => 'Sports & Outdoors']);

        $subPhones      = SubCategory::create(['title' => 'Phones',      'category_id' => $catElectronics->id]);
        $subLaptops     = SubCategory::create(['title' => 'Laptops',     'category_id' => $catElectronics->id]);
        $subHeadphones  = SubCategory::create(['title' => 'Headphones',  'category_id' => $catElectronics->id]);
        $subMenClothing = SubCategory::create(['title' => 'Men Clothing','category_id' => $catFashion->id]);
        $subShoes       = SubCategory::create(['title' => 'Shoes',       'category_id' => $catFashion->id]);
        $subFitness     = SubCategory::create(['title' => 'Fitness',     'category_id' => $catSports->id]);
        $subCycling     = SubCategory::create(['title' => 'Cycling',     'category_id' => $catSports->id]);

        // ─── 4. Products ──────────────────────────────────────────────────────

        // seller1 - Electronics
        $p1 = Product::create([
            'title'       => 'iPhone 15 Pro',
            'description' => 'Latest Apple iPhone with A17 chip and titanium frame.',
            'price'       => 1200.00,
            'quantity'    => 50,
            'seller_id'   => $seller1->id,
            'is_active'   => 1,
        ]);
        $p2 = Product::create([
            'title'       => 'Samsung Galaxy S24 Ultra',
            'description' => 'Flagship Samsung phone with AI features and S Pen.',
            'price'       => 950.00,
            'quantity'    => 30,
            'seller_id'   => $seller1->id,
            'is_active'   => 1,
        ]);
        $p3 = Product::create([
            'title'       => 'MacBook Pro M3',
            'description' => 'Apple MacBook Pro 14-inch with M3 Pro chip.',
            'price'       => 2400.00,
            'quantity'    => 15,
            'seller_id'   => $seller1->id,
            'is_active'   => 1,
        ]);
        $p4 = Product::create([
            'title'       => 'Sony WH-1000XM5',
            'description' => 'Industry-leading noise cancelling headphones.',
            'price'       => 350.00,
            'quantity'    => 100,
            'seller_id'   => $seller1->id,
            'is_active'   => 1,
        ]);

        // seller2 - Fashion
        $p5 = Product::create([
            'title'       => 'Nike Air Force 1',
            'description' => 'Classic white sneakers for everyday wear.',
            'price'       => 120.00,
            'quantity'    => 200,
            'seller_id'   => $seller2->id,
            'is_active'   => 1,
        ]);
        $p6 = Product::create([
            'title'       => 'Levi\'s 501 Original Jeans',
            'description' => 'Iconic straight fit jeans in dark wash.',
            'price'       => 80.00,
            'quantity'    => 150,
            'seller_id'   => $seller2->id,
            'is_active'   => 1,
        ]);
        $p7 = Product::create([
            'title'       => 'Polo Ralph Lauren T-Shirt',
            'description' => 'Cotton crew neck t-shirt with embroidered logo.',
            'price'       => 65.00,
            'quantity'    => 300,
            'seller_id'   => $seller2->id,
            'is_active'   => 1,
        ]);

        // seller3 - Sports
        $p8 = Product::create([
            'title'       => 'Adjustable Dumbbell Set 40kg',
            'description' => 'Space-saving adjustable dumbbells for home gym.',
            'price'       => 250.00,
            'quantity'    => 40,
            'seller_id'   => $seller3->id,
            'is_active'   => 1,
        ]);
        $p9 = Product::create([
            'title'       => 'Yoga Mat Premium',
            'description' => 'Extra thick non-slip yoga mat with carrying strap.',
            'price'       => 45.00,
            'quantity'    => 500,
            'seller_id'   => $seller3->id,
            'is_active'   => 1,
        ]);
        $p10 = Product::create([
            'title'       => 'Mountain Bike Pro X7',
            'description' => 'Full suspension mountain bike, 27.5-inch wheels.',
            'price'       => 800.00,
            'quantity'    => 10,
            'seller_id'   => $seller3->id,
            'is_active'   => 1,
        ]);

        // منتج غير نشط (للتست)
        $p11 = Product::create([
            'title'       => 'Old Product Discontinued',
            'description' => 'This product is no longer available.',
            'price'       => 99.00,
            'quantity'    => 0,
            'seller_id'   => $seller1->id,
            'is_active'   => 0,
        ]);

        // ─── 5. Product ↔ SubCategory ─────────────────────────────────────────

        ProductSubCategory::create(['product_id' => $p1->id,  'sub_category_id' => $subPhones->id]);
        ProductSubCategory::create(['product_id' => $p2->id,  'sub_category_id' => $subPhones->id]);
        ProductSubCategory::create(['product_id' => $p3->id,  'sub_category_id' => $subLaptops->id]);
        ProductSubCategory::create(['product_id' => $p4->id,  'sub_category_id' => $subHeadphones->id]);
        ProductSubCategory::create(['product_id' => $p5->id,  'sub_category_id' => $subShoes->id]);
        ProductSubCategory::create(['product_id' => $p6->id,  'sub_category_id' => $subMenClothing->id]);
        ProductSubCategory::create(['product_id' => $p7->id,  'sub_category_id' => $subMenClothing->id]);
        ProductSubCategory::create(['product_id' => $p8->id,  'sub_category_id' => $subFitness->id]);
        ProductSubCategory::create(['product_id' => $p9->id,  'sub_category_id' => $subFitness->id]);
        ProductSubCategory::create(['product_id' => $p10->id, 'sub_category_id' => $subCycling->id]);

        // ─── 6. Product Attributes ────────────────────────────────────────────

        ProductAttribute::create(['product_id' => $p1->id, 'key' => 'Color',   'value' => 'Natural Titanium']);
        ProductAttribute::create(['product_id' => $p1->id, 'key' => 'Storage', 'value' => '256GB']);
        ProductAttribute::create(['product_id' => $p1->id, 'key' => 'RAM',     'value' => '8GB']);

        ProductAttribute::create(['product_id' => $p2->id, 'key' => 'Color',   'value' => 'Phantom Black']);
        ProductAttribute::create(['product_id' => $p2->id, 'key' => 'Storage', 'value' => '512GB']);

        ProductAttribute::create(['product_id' => $p3->id, 'key' => 'RAM',     'value' => '18GB']);
        ProductAttribute::create(['product_id' => $p3->id, 'key' => 'Storage', 'value' => '512GB SSD']);
        ProductAttribute::create(['product_id' => $p3->id, 'key' => 'Screen',  'value' => '14-inch Liquid Retina XDR']);

        ProductAttribute::create(['product_id' => $p5->id, 'key' => 'Size',    'value' => '42']);
        ProductAttribute::create(['product_id' => $p5->id, 'key' => 'Color',   'value' => 'White']);

        ProductAttribute::create(['product_id' => $p6->id, 'key' => 'Size',    'value' => '32W/32L']);
        ProductAttribute::create(['product_id' => $p6->id, 'key' => 'Color',   'value' => 'Dark Wash']);

        ProductAttribute::create(['product_id' => $p10->id, 'key' => 'Frame',  'value' => 'Aluminum Alloy']);
        ProductAttribute::create(['product_id' => $p10->id, 'key' => 'Wheels', 'value' => '27.5 inch']);

        // ─── 7. Coupons ───────────────────────────────────────────────────────

        $coupon1 = Coupon::create([
            'code'        => 'ELECTRO20',
            'percentage'  => 20,
            'expire_date' => now()->addMonths(3)->toDateString(),
            'max_usage'   => 100,
            'used_count'  => 5,
            'seller_id'   => $seller1->id,
            'is_active'   => 1,
        ]);

        $coupon2 = Coupon::create([
            'code'        => 'FASHION15',
            'percentage'  => 15,
            'expire_date' => now()->addMonths(2)->toDateString(),
            'max_usage'   => 50,
            'used_count'  => 0,
            'seller_id'   => $seller2->id,
            'is_active'   => 1,
        ]);

        $coupon3 = Coupon::create([
            'code'        => 'SPORT10',
            'percentage'  => 10,
            'expire_date' => now()->addMonth()->toDateString(),
            'max_usage'   => 200,
            'used_count'  => 12,
            'seller_id'   => $seller3->id,
            'is_active'   => 1,
        ]);

        // كوبون منتهي (للتست)
        Coupon::create([
            'code'        => 'EXPIRED50',
            'percentage'  => 50,
            'expire_date' => now()->subDays(10)->toDateString(),
            'max_usage'   => 10,
            'used_count'  => 10,
            'seller_id'   => $seller1->id,
            'is_active'   => 0,
        ]);

        // ─── 8. Carts ─────────────────────────────────────────────────────────

        // buyer1 - فيها items من sellers مختلفين + كوبون
        $cart1 = Cart::create(['user_id' => $buyer1->id]);
        CartItem::create(['cart_id' => $cart1->id, 'product_id' => $p1->id, 'quantity' => 1]); // seller1
        CartItem::create(['cart_id' => $cart1->id, 'product_id' => $p4->id, 'quantity' => 2]); // seller1
        CartItem::create(['cart_id' => $cart1->id, 'product_id' => $p5->id, 'quantity' => 1]); // seller2
        CartItem::create(['cart_id' => $cart1->id, 'product_id' => $p9->id, 'quantity' => 3]); // seller3

        CartCoupon::create(['cart_id' => $cart1->id, 'coupon_id' => $coupon1->id]); // 20% على seller1

        // buyer2 - فيها items بسيطة
        $cart2 = Cart::create(['user_id' => $buyer2->id]);
        CartItem::create(['cart_id' => $cart2->id, 'product_id' => $p6->id, 'quantity' => 2]); // seller2
        CartItem::create(['cart_id' => $cart2->id, 'product_id' => $p8->id, 'quantity' => 1]); // seller3

        // ─── 9. Orders ───────────────────────────────────────────────────────

        // Order 1 - buyer1, processing
        $order1 = Order::create([
            'user_id'        => $buyer1->id,
            'subtotal'       => 1750.00,
            'discount'       => 0.00,
            'total_price'    => 1750.00,
            'phone'          => '01012345678',
            'address_id'     => $addr1->id,
            'status'         => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'card',
        ]);

        $sub1a = SubOrder::create([
            'order_id'    => $order1->id,
            'seller_id'   => $seller1->id,
            'subtotal'    => 950.00,
            'discount'    => 0.00,
            'total_price' => 950.00,
            'status'      => 'processing',
        ]);

        OrderItem::create([
            'sub_order_id'        => $sub1a->id,
            'product_id'          => $p2->id,
            'title'               => $p2->title,
            'slug'                => $p2->slug,
            'description'         => $p2->description,
            'cover_image'         => null,
            'price_at_purchase'   => 950.00,
            'quantity'            => 1,
            'created_at_snapshot' => $p2->created_at,
        ]);

        $sub1b = SubOrder::create([
            'order_id'    => $order1->id,
            'seller_id'   => $seller3->id,
            'subtotal'    => 800.00,
            'discount'    => 0.00,
            'total_price' => 800.00,
            'status'      => 'shipped',
        ]);

        OrderItem::create([
            'sub_order_id'        => $sub1b->id,
            'product_id'          => $p10->id,
            'title'               => $p10->title,
            'slug'                => $p10->slug,
            'description'         => $p10->description,
            'cover_image'         => null,
            'price_at_purchase'   => 800.00,
            'quantity'            => 1,
            'created_at_snapshot' => $p10->created_at,
        ]);

        // Order 2 - buyer2, completed
        $order2 = Order::create([
            'user_id'        => $buyer2->id,
            'subtotal'       => 265.00,
            'discount'       => 0.00,
            'total_price'    => 265.00,
            'phone'          => '01198765432',
            'address_id'     => $addr3->id,
            'status'         => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $sub2a = SubOrder::create([
            'order_id'    => $order2->id,
            'seller_id'   => $seller2->id,
            'subtotal'    => 265.00,
            'discount'    => 0.00,
            'total_price' => 265.00,
            'status'      => 'completed',
        ]);

        OrderItem::create([
            'sub_order_id'        => $sub2a->id,
            'product_id'          => $p7->id,
            'title'               => $p7->title,
            'slug'                => $p7->slug,
            'description'         => $p7->description,
            'cover_image'         => null,
            'price_at_purchase'   => 65.00,
            'quantity'            => 1,
            'created_at_snapshot' => $p7->created_at,
        ]);

        OrderItem::create([
            'sub_order_id'        => $sub2a->id,
            'product_id'          => $p5->id,
            'title'               => $p5->title,
            'slug'                => $p5->slug,
            'description'         => $p5->description,
            'cover_image'         => null,
            'price_at_purchase'   => 120.00,
            'quantity'            => 1,
            'created_at_snapshot' => $p5->created_at,
        ]);

        OrderItem::create([
            'sub_order_id'        => $sub2a->id,
            'product_id'          => $p6->id,
            'title'               => $p6->title,
            'slug'                => $p6->slug,
            'description'         => $p6->description,
            'cover_image'         => null,
            'price_at_purchase'   => 80.00,
            'quantity'            => 1,
            'created_at_snapshot' => $p6->created_at,
        ]);

        // Order 3 - buyer1, cancelled
        $order3 = Order::create([
            'user_id'        => $buyer1->id,
            'subtotal'       => 45.00,
            'discount'       => 0.00,
            'total_price'    => 45.00,
            'phone'          => '01012345678',
            'address_id'     => $addr1->id,
            'status'         => 'cancelled',
            'payment_status' => 'refunded',
            'payment_method' => 'wallet',
        ]);

        $sub3a = SubOrder::create([
            'order_id'    => $order3->id,
            'seller_id'   => $seller3->id,
            'subtotal'    => 45.00,
            'discount'    => 0.00,
            'total_price' => 45.00,
            'status'      => 'cancelled',
        ]);

        OrderItem::create([
            'sub_order_id'        => $sub3a->id,
            'product_id'          => $p9->id,
            'title'               => $p9->title,
            'slug'                => $p9->slug,
            'description'         => $p9->description,
            'cover_image'         => null,
            'price_at_purchase'   => 45.00,
            'quantity'            => 1,
            'created_at_snapshot' => $p9->created_at,
        ]);

        // ─── Done ─────────────────────────────────────────────────────────────

        $this->command->info('');
        $this->command->info('✅ All test data seeded!');
        $this->command->info('');

        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['admin',    'admin@test.com',    'password'],
                ['seller',   'seller1@test.com',  'password'],
                ['seller',   'seller2@test.com',  'password'],
                ['seller',   'seller3@test.com',  'password'],
                ['buyer',    'buyer1@test.com',   'password'],
                ['buyer',    'buyer2@test.com',   'password'],
                ['delivery', 'delivery@test.com', 'password'],
            ]
        );

        $this->command->info('');
        $this->command->table(
            ['Data', 'Count'],
            [
                ['Users',          7],
                ['Addresses',      3],
                ['Categories',     3],
                ['SubCategories',  7],
                ['Products',       11],
                ['Attributes',     14],
                ['Coupons',        4],
                ['Carts',          2],
                ['Cart Items',     6],
                ['Orders',         3],
                ['Sub Orders',     4],
                ['Order Items',    6],
            ]
        );
    }
}
