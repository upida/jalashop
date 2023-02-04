<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTriggerPurchaseOrderToProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER tr_purchase_order_details_add_entity_to_products AFTER INSERT ON purchase_order_details 
            FOR EACH ROW
            UPDATE products
                SET quantity = quantity + NEW.quantity
            WHERE sku = NEW.sku
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_purchase_order_details_add_entity_to_products`');
    }
}
