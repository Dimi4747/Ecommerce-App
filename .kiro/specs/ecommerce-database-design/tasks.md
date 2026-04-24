# Implementation Plan: E-Commerce Database Schema

## Overview

This implementation plan creates a comprehensive Laravel database schema for an e-commerce application including products, categories, variants, orders, cart, payments, coupons, and reviews. The implementation follows Laravel conventions with migrations in dependency order, Eloquent models with relationships, factories for test data generation, and seeders for populating the database.

## Tasks

- [ ] 1. Create Phase 1 migrations for independent tables
  - [x] 1.1 Extend users table with e-commerce fields
    - Add phone, date_of_birth, role columns to existing users table
    - Add soft deletes to users table
    - _Requirements: 4.1, 13.4_
  
  - [x] 1.2 Create categories table migration
    - Create categories table with name, slug, description, parent_id, sort_order
    - Add self-referencing foreign key for hierarchical structure
    - Add indexes on slug
    - _Requirements: 1.2, 1.3, 14.1-14.4_
  
  - [ ] 1.3 Create products table migration
    - Create products table with name, slug, sku, description, base_price, status, average_rating, review_count, currency_code
    - Add soft deletes for order history preservation
    - Add indexes on sku, status, and composite (status, created_at)
    - Add full-text index on name and description
    - _Requirements: 1.1, 1.7, 1.8, 11.1, 11.2, 20.1, 20.4_
  
  - [ ] 1.4 Create product_attributes table migration
    - Create product_attributes table with name, slug, type
    - _Requirements: 2.2, 14.1-14.4_
  
  - [ ] 1.5 Create coupons table migration
    - Create coupons table with code, description, type, value, usage limits, validity period
    - Add unique index on code
    - Add check constraints for usage_count <= usage_limit
    - _Requirements: 9.1, 9.2, 12.2, 14.1-14.4_

- [ ] 2. Create Phase 2 migrations for first-level dependencies
  - [ ] 2.1 Create addresses table migration
    - Create addresses table with user_id foreign key and address fields
    - Add type enum (shipping, billing, both) and is_default flag
    - Add index on user_id
    - _Requirements: 7.1, 7.2, 7.3, 14.1-14.4_
  
  - [ ] 2.2 Create product_variants table migration
    - Create product_variants table with product_id foreign key, sku, price, stock_quantity, low_stock_threshold, track_inventory, is_default, variant_attributes JSON
    - Add unique index on sku
    - Add index on product_id
    - _Requirements: 2.1, 2.5, 2.6, 3.1, 3.6, 14.1-14.4_
  
  - [ ] 2.3 Create product_images table migration
    - Create product_images table with product_id foreign key, image_path, alt_text, sort_order, is_primary
    - Add index on product_id
    - _Requirements: 1.5, 14.1-14.4_
  
  - [ ] 2.4 Create product_attribute_values table migration
    - Create product_attribute_values table with product_attribute_id foreign key, value, display_value, color_hex, display_order
    - Add index on product_attribute_id
    - _Requirements: 2.3, 14.1-14.4_
  
  - [ ] 2.5 Create carts table migration
    - Create carts table with user_id (nullable), session_id (nullable), expires_at
    - Add indexes on user_id and session_id
    - Add foreign key with SET NULL on user deletion
    - _Requirements: 4.1, 4.2, 4.3, 4.7, 11.7, 14.1-14.4_
  
  - [ ] 2.6 Create orders table migration
    - Create orders table with user_id, order_number, status, financial columns (subtotal, tax_amount, shipping_amount, discount_amount, total_amount), currency_code, coupon_id, notes
    - Add unique index on order_number
    - Add indexes on user_id, status, and composite (status, created_at)
    - Add foreign key on user_id with RESTRICT to preserve orders
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.6, 5.7, 11.4, 11.5, 11.6, 14.1-14.4, 19.1, 19.2_

- [ ] 3. Create Phase 3 migrations for second-level dependencies
  - [ ] 3.1 Create category_product pivot table migration
    - Create category_product pivot table linking categories and products
    - Add composite index and unique constraint on (category_id, product_id)
    - _Requirements: 1.4, 11.3, 14.1-14.4_
  
  - [ ] 3.2 Create product_variant_attributes pivot table migration
    - Create product_variant_attributes pivot table linking variants to attribute values
    - Add composite index on (product_variant_id, product_attribute_value_id)
    - _Requirements: 2.4, 14.1-14.4_
  
  - [ ] 3.3 Create cart_items table migration
    - Create cart_items table with cart_id, product_variant_id foreign keys, quantity, price_at_addition
    - Add composite index and unique constraint on (cart_id, product_variant_id)
    - Add check constraint for quantity > 0
    - _Requirements: 4.2, 4.5, 11.9, 12.4, 14.1-14.4_
  
  - [ ] 3.4 Create order_items table migration
    - Create order_items table with order_id, product_variant_id foreign keys, quantity, unit_price, subtotal, product_snapshot JSON
    - Add index on order_id
    - Add foreign key on product_variant_id with RESTRICT
    - Add check constraint for quantity > 0
    - _Requirements: 5.2, 5.5, 12.4, 14.1-14.4, 18.6_
  
  - [ ] 3.5 Create order_addresses table migration
    - Create order_addresses table with order_id foreign key, type enum, and all address fields
    - Add index on order_id
    - _Requirements: 7.5, 7.6, 14.1-14.4_
  
  - [ ] 3.6 Create order_status_history table migration
    - Create order_status_history table with order_id, old_status, new_status, notes, changed_by_user_id
    - Add index on order_id
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 14.1-14.4_
  
  - [ ] 3.7 Create payments table migration
    - Create payments table with order_id foreign key, payment_method, transaction_id, amount, currency_code, status, gateway_response JSON, card_last_four, card_brand
    - Add unique index on transaction_id
    - Add indexes on order_id and transaction_id
    - Add check constraint for amount >= 0
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 12.5, 14.1-14.4, 19.1, 19.2_
  
  - [ ] 3.8 Create inventory_transactions table migration
    - Create inventory_transactions table with product_variant_id, quantity_change, quantity_after, transaction_type, polymorphic reference (reference_id, reference_type), notes, user_id
    - Add indexes on product_variant_id and (reference_type, reference_id)
    - _Requirements: 3.2, 3.3, 3.4, 14.1-14.4_
  
  - [ ] 3.9 Create coupon_usage table migration
    - Create coupon_usage table with coupon_id, order_id, user_id foreign keys, discount_amount
    - Add indexes on coupon_id and user_id
    - _Requirements: 9.3, 9.4, 14.1-14.4_
  
  - [ ] 3.10 Create product_reviews table migration
    - Create product_reviews table with product_id, user_id foreign keys, rating, title, comment, is_verified_purchase, is_approved
    - Add index on product_id
    - Add unique composite constraint on (product_id, user_id)
    - Add check constraint for rating between 1 and 5
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 11.8, 12.2, 12.3, 14.1-14.4_

- [ ] 4. Checkpoint - Verify migrations execute successfully
  - Run `php artisan migrate` to verify all migrations execute without errors
  - Test rollback with `php artisan migrate:rollback` to verify down() methods work
  - Ensure all tests pass, ask the user if questions arise

- [ ] 5. Create Eloquent models for all tables
  - [ ] 5.1 Create Product model with relationships and soft deletes
    - Define fillable fields, casts, and soft deletes trait
    - Add relationships: categories (belongsToMany), variants (hasMany), images (hasMany), reviews (hasMany)
    - Add accessor for primary image
    - _Requirements: 1.1, 1.7, 13.4_
  
  - [ ] 5.2 Create Category model with self-referencing relationship
    - Define fillable fields and casts
    - Add relationships: parent (belongsTo), children (hasMany), products (belongsToMany)
    - _Requirements: 1.2, 1.3_
  
  - [ ] 5.3 Create ProductVariant model with relationships
    - Define fillable fields and casts
    - Add relationships: product (belongsTo), cartItems (hasMany), orderItems (hasMany), inventoryTransactions (hasMany), attributeValues (belongsToMany)
    - _Requirements: 2.1, 2.6_
  
  - [ ] 5.4 Create ProductAttribute and ProductAttributeValue models
    - Define fillable fields and relationships
    - ProductAttribute hasMany ProductAttributeValue
    - ProductAttributeValue belongsTo ProductAttribute and belongsToMany ProductVariant
    - _Requirements: 2.2, 2.3_
  
  - [ ] 5.5 Create ProductImage model with relationship
    - Define fillable fields and belongsTo product relationship
    - _Requirements: 1.5_
  
  - [ ] 5.6 Create Cart model with relationships and accessors
    - Define fillable fields, casts (expires_at as datetime)
    - Add relationships: user (belongsTo), items (hasMany)
    - Add accessors: getTotal() and getItemCount()
    - _Requirements: 4.1, 4.2_
  
  - [ ] 5.7 Create CartItem model with relationships
    - Define fillable fields and casts
    - Add relationships: cart (belongsTo), productVariant (belongsTo)
    - _Requirements: 4.2, 4.5_
  
  - [ ] 5.8 Create Order model with relationships and status tracking
    - Define fillable fields and casts for decimal amounts
    - Add relationships: user (belongsTo), items (hasMany), statusHistory (hasMany), addresses (hasMany), shippingAddress (hasOne), billingAddress (hasOne), payments (hasMany), coupon (belongsTo)
    - Add model event to create OrderStatusHistory when status changes
    - _Requirements: 5.1, 5.6, 6.2_
  
  - [ ] 5.9 Create OrderItem model with relationships
    - Define fillable fields and casts
    - Add relationships: order (belongsTo), productVariant (belongsTo)
    - _Requirements: 5.2, 5.5_
  
  - [ ] 5.10 Create OrderStatusHistory model
    - Define fillable fields
    - Add relationships: order (belongsTo), changedByUser (belongsTo User)
    - _Requirements: 6.1, 6.3_
  
  - [ ] 5.11 Create OrderAddress model
    - Define fillable fields
    - Add relationship: order (belongsTo)
    - _Requirements: 7.6_
  
  - [ ] 5.12 Create Address model with relationship
    - Define fillable fields
    - Add relationship: user (belongsTo)
    - _Requirements: 7.1, 7.2_
  
  - [ ] 5.13 Create Payment model with relationship
    - Define fillable fields and casts
    - Add relationship: order (belongsTo)
    - _Requirements: 8.1, 8.6_
  
  - [ ] 5.14 Create Coupon model with relationships
    - Define fillable fields and casts for dates
    - Add relationships: orders (hasMany), couponUsage (hasMany)
    - _Requirements: 9.1, 9.2_
  
  - [ ] 5.15 Create CouponUsage model with relationships
    - Define fillable fields and casts
    - Add relationships: coupon (belongsTo), order (belongsTo), user (belongsTo)
    - _Requirements: 9.3_
  
  - [ ] 5.16 Create ProductReview model with relationships
    - Define fillable fields and casts
    - Add relationships: product (belongsTo), user (belongsTo)
    - _Requirements: 10.1, 10.5_
  
  - [ ] 5.17 Create InventoryTransaction model with polymorphic relationship
    - Define fillable fields and casts
    - Add relationships: productVariant (belongsTo), reference (morphTo), user (belongsTo)
    - _Requirements: 3.2, 3.4_
  
  - [ ] 5.18 Extend User model with e-commerce relationships
    - Add relationships: addresses (hasMany), carts (hasMany), orders (hasMany), productReviews (hasMany), couponUsage (hasMany)
    - Add soft deletes trait
    - _Requirements: 4.1, 13.4_

- [ ] 6. Checkpoint - Verify model relationships work
  - Test model relationships using tinker or simple test scripts
  - Ensure all tests pass, ask the user if questions arise

- [ ] 7. Create factory files for all models
  - [ ] 7.1 Create ProductFactory with states
    - Generate realistic name, slug, sku (format: PRD-####-???), description, base_price, status, currency_code
    - Add active() state to set status to 'active'
    - Add withVariants(count) state to create product with variants and null base_price
    - _Requirements: 16.1, 16.3_
  
  - [ ] 7.2 Create CategoryFactory
    - Generate name, slug, description, sort_order
    - Support parent_id for hierarchical categories
    - _Requirements: 16.1_
  
  - [ ] 7.3 Create ProductVariantFactory
    - Generate sku (format: VAR-####-???), price, stock_quantity, low_stock_threshold, track_inventory, is_default, variant_attributes JSON
    - _Requirements: 16.1, 16.5_
  
  - [ ] 7.4 Create ProductAttributeFactory and ProductAttributeValueFactory
    - ProductAttributeFactory: generate name, slug, type
    - ProductAttributeValueFactory: generate value, display_value, color_hex, display_order
    - _Requirements: 16.1_
  
  - [ ] 7.5 Create ProductImageFactory
    - Generate image_path, alt_text, sort_order, is_primary
    - _Requirements: 16.1_
  
  - [ ] 7.6 Create CartFactory and CartItemFactory
    - CartFactory: generate user_id (nullable), session_id (nullable), expires_at
    - CartItemFactory: generate quantity, price_at_addition
    - _Requirements: 16.1_
  
  - [ ] 7.7 Create OrderFactory with total calculation
    - Generate user_id, order_number (format: ORD-YYYY-#####), status, subtotal, tax_amount, shipping_amount, discount_amount
    - Calculate total_amount = subtotal + tax_amount + shipping_amount - discount_amount
    - Add withItems(count) state to create order with order items
    - _Requirements: 16.1, 16.4_
  
  - [ ] 7.8 Create OrderItemFactory
    - Generate quantity, unit_price, calculate subtotal, create product_snapshot JSON
    - _Requirements: 16.1_
  
  - [ ] 7.9 Create AddressFactory
    - Generate type, first_name, last_name, company, address fields, phone, is_default
    - _Requirements: 16.1_
  
  - [ ] 7.10 Create PaymentFactory
    - Generate payment_method, transaction_id, amount, currency_code, status, gateway_response JSON, card_last_four, card_brand
    - _Requirements: 16.1_
  
  - [ ] 7.11 Create CouponFactory
    - Generate code, description, type, value, usage limits, validity period, is_active
    - _Requirements: 16.1_
  
  - [ ] 7.12 Create ProductReviewFactory
    - Generate rating (1-5), title, comment, is_verified_purchase, is_approved
    - _Requirements: 16.1_
  
  - [ ] 7.13 Create InventoryTransactionFactory
    - Generate quantity_change, quantity_after, transaction_type, notes
    - _Requirements: 16.1_

- [ ] 8. Checkpoint - Verify factories generate valid data
  - Test each factory in tinker to ensure valid data generation
  - Ensure all tests pass, ask the user if questions arise

- [ ] 9. Create seeder files for test data
  - [ ] 9.1 Create UserSeeder
    - Create 10 test users with varied roles
    - Use UserFactory
    - _Requirements: 15.4_
  
  - [ ] 9.2 Create CategorySeeder
    - Create 5 categories with hierarchical relationships (parent-child)
    - Use CategoryFactory
    - _Requirements: 15.2_
  
  - [ ] 9.3 Create ProductAttributeSeeder
    - Create Color, Size, Material attributes with multiple values
    - Use ProductAttributeFactory and ProductAttributeValueFactory
    - _Requirements: 15.8_
  
  - [ ] 9.4 Create ProductSeeder
    - Create 20 products with active status
    - Attach 1-3 random categories to each product
    - Create 2-4 variants per product with first variant as default
    - Create 3-5 images per product with first image as primary
    - Link variants to attribute values
    - Use ProductFactory with withVariants state
    - _Requirements: 15.3, 15.8_
  
  - [ ] 9.5 Create CouponSeeder
    - Create 5 active coupons with different types (percentage, fixed_amount, free_shipping)
    - Use CouponFactory
    - _Requirements: 15.6_
  
  - [ ] 9.6 Create AddressSeeder
    - Create 2-3 addresses per user with one marked as default
    - Use AddressFactory
    - _Requirements: 15.4_
  
  - [ ] 9.7 Create CartSeeder
    - Create active carts for 5 random users with 2-4 items each
    - Use CartFactory and CartItemFactory
    - _Requirements: 15.8_
  
  - [ ] 9.8 Create OrderSeeder
    - Create 15 orders with various statuses
    - Create 2-5 order items per order
    - Create shipping and billing addresses for each order
    - Create payment record for each order
    - Create order status history entries
    - Apply coupons to 30% of orders
    - Use OrderFactory with withItems state
    - _Requirements: 15.5, 15.8_
  
  - [ ] 9.9 Create ProductReviewSeeder
    - Create reviews for 50% of products (10 products)
    - Mark reviews as verified purchase if user has ordered the product
    - Set 80% of reviews as approved
    - Use ProductReviewFactory
    - _Requirements: 15.7_
  
  - [ ] 9.10 Create DatabaseSeeder orchestrator
    - Call all seeders in dependency order: User, Category, ProductAttribute, Product, Coupon, Address, Cart, Order, ProductReview
    - _Requirements: 15.1, 15.8_

- [ ] 10. Checkpoint - Verify seeders populate database correctly
  - Run `php artisan db:seed` to verify all seeders execute without errors
  - Verify data integrity and relationships in database
  - Ensure all tests pass, ask the user if questions arise

- [ ] 11. Create integration tests for migrations and constraints
  - [ ]* 11.1 Write migration tests
    - Test that all tables are created with correct columns
    - Test that indexes are created correctly
    - Test that foreign keys have correct cascade behavior
    - _Requirements: 14.6_
  
  - [ ]* 11.2 Write database constraint tests
    - Test unique constraints on sku, order_number, coupon code
    - Test check constraints on rating (1-5), quantity (> 0), prices (>= 0)
    - Test foreign key cascade behaviors (CASCADE, SET NULL, RESTRICT)
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 12.7_
  
  - [ ]* 11.3 Write model relationship tests
    - Test all Eloquent relationships work correctly
    - Test eager loading prevents N+1 queries
    - Test order status history is created automatically on status change
    - _Requirements: 6.2_

- [ ] 12. Create business logic tests
  - [ ]* 12.1 Write order total calculation tests
    - Test that order total equals subtotal + tax + shipping - discount
    - Test order total validation in model events
    - _Requirements: 5.6_
  
  - [ ]* 12.2 Write cart total calculation tests
    - Test cart total is sum of (price_at_addition * quantity) for all items
    - Test cart item count is sum of quantities
    - _Requirements: 4.5_
  
  - [ ]* 12.3 Write coupon usage tests
    - Test coupon usage_count increments when applied
    - Test coupon usage limits are enforced
    - _Requirements: 9.4, 9.5_
  
  - [ ]* 12.4 Write inventory transaction tests
    - Test inventory transactions record stock changes correctly
    - Test quantity_after matches actual stock_quantity
    - _Requirements: 3.2_

- [ ] 13. Create factory and seeder tests
  - [ ]* 13.1 Write factory validation tests
    - Test each factory generates valid data with correct formats
    - Test factory states work correctly (active products, withVariants, withItems)
    - Test order factory calculates totals correctly
    - _Requirements: 16.1, 16.3, 16.4, 16.5, 16.7_
  
  - [ ]* 13.2 Write seeder validation tests
    - Test DatabaseSeeder creates all required data (10 users, 5 categories, 20 products, 15 orders, 5 coupons)
    - Test ProductSeeder creates products with all relationships (variants, images, categories)
    - Test seeded data has correct structure (default variants, primary images, hierarchical categories)
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7_

- [ ] 14. Final checkpoint - Complete testing and validation
  - Run full test suite with `php artisan test`
  - Verify all migrations, models, factories, and seeders work correctly
  - Ensure database schema matches design specifications
  - Ensure all tests pass, ask the user if questions arise

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Migrations must be created in dependency order to satisfy foreign key constraints
- All monetary values use DECIMAL(10,2) for precision
- Soft deletes are used on products and users to preserve order history
- JSON columns are used for flexible data (variant_attributes, product_snapshot, gateway_response)
- Indexes are strategically placed on frequently queried columns
- Factory states enable flexible test data generation
- Seeders create realistic test data with proper relationships
- Testing focuses on schema validation, constraints, relationships, and business logic (no property-based tests for IaC)
