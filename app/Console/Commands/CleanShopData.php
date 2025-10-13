<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanApplicationData extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:clean-data';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Truncates all business-related data (orders, products, vendors, etc.) safely.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Absolute first step: Confirmation
        if (!$this->confirm('!!! WARNING !!! This will permanently delete all orders, products, vendors, and customer transactional data. This cannot be undone. Have you taken a database backup?')) {
            $this->info('Operation cancelled by user.');
            return 1;
        } else {


        $this->info('Disabling foreign key checks...');
        Schema::disableForeignKeyConstraints();

        // This list is carefully ordered. Child tables are deleted before parent tables.
        $tablesToTruncate = [
            // --- Order & Refund Related (Children First) ---
            'order_status_histories',
            'order_details',
            'order_expected_delivery_histories',
            'order_status_histories',
            'order_transactions',
            'refund_requests',
            'refund_transactions',
            'refund_statuses',
            'orders',

            // --- Product & Cart Related ---
            'carts',
            'digital_product_otp_verifications',
            'digital_product_variations',
            'flash_deal_products',
            'product_compares',
            'soft_credentials',
            'stock_clearance_products',
            'product_seos',
            'product_stocks',
            'product_tag', // Pivot table
            'reviews',
            'review_replies',
            'stock_clearance_setups',
            'wishlists',
            'products',
            'tags',
             
            // --- Delivery Man Related ---
            'delivery_histories',
            'delivery_man_locations',
            'deliveryman_notifications',
            'delivery_man_transactions',
            'deliveryman_wallets',
            'delivery_men',
           
            // --- Seller / Vendor Related ---
            'seller_wallet_histories',
            'seller_wallets',
            'shop_followers',
            'shops',
            'sellers',

             // --- Customer Transactional Data ---
            'customer_wallet_histories',
            'customer_wallets',
            'loyalty_point_transactions',
            'wallet_transactions', // General wallet transactions
            'transactions',      // General transactions
            

            
            // --- General Business Data & Logs ---
            
            'deal_of_the_days',
            'feature_deals',
            'flash_deals',
            'most_demandeds',
            'notifications', 'notification_messages', 'notification_seens',
            'paytabs_invoices',
            'payment_requests',
            'restock_products', 'restock_product_customers',
            'subscriptions',
            'support_tickets',
            'support_ticket_convs',
            'withdrawal_requests',
            'guest_users',
            'search_functions',


        ];

        // We are now executing the commands directly.
            foreach ($tablesToTruncate as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->warn("Truncated `{$table}` table.");
                } else {
                    $this->line("Table `{$table}` does not exist, skipping.");
                }
            }
            // --- END OF CORRECTION ---

            $this->info('Re-enabling foreign key checks...');
            Schema::enableForeignKeyConstraints();

            $this->info('All shop data has been successfully cleaned!');
        }
        
        return 0;
    }
}