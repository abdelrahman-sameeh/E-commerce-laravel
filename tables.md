users

- id (PK)
- first_name
- last_name
- email (UNIQUE)
- password
- created_at
- phone
- role => "admin", "owner", "user", "delivery"

---

products

- id (PK)
- title
- slug (UNIQUE)
- description
- cover_image
- price
- quantity
- owner_id (FK -> user.id)
- is_active
- created_at

---

product_pictures

- id (pk)
- product_id (FK products.id)
- picture

---

coupons

- id (PK)
- code (UNIQUE)
- percentage
- expire_date
- max_usage
- used_count
- owner_id (FK -> user.id)
- is_active

---

addresses

- id (PK)
- user_id (FK -> users.id)
- city
- street
- country

---

cart_items

- id (PK)
- cart_id (FK -> carts.id)
- product_id (FK -> products.id)
- owner_id (FK -> owners.id)
- quantity
- price_at_add_time

---

cart_coupons

- id (PK)
- cart_id (FK -> carts.id)
- owner_id (FK -> users.id)
- coupon_id (FK -> coupons.id)

---

orders

- id (PK)
- user_id (FK -> users.id)
- owner_id (FK -> owners.id)
- subtotal
- discount
- total_price
- coupon_id (FK -> coupons.id, NULL)
- phone
- address_id (FK -> addresses.id)
- status
- payment_status
- created_at

---

order_items

- id (PK)
- order_id (FK orders.id)
- product_id (optional, nullable)
- title
- slug
- description
- cover_image
- price_at_purchase
- quantity
- created_at_snapshot

---

order_item_pictures

- id (PK)
- order_item_id (FK -> order_items.id)
- picture
