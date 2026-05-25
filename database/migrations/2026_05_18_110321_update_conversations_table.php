# update_conversations_table.php

```php id="jlwm16"
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {

            // =====================================
            // 🏷 CURRENT CATEGORY
            // =====================================
            $table->string('current_category')
                ->nullable()
                ->after('title');

            // =====================================
            // 🧠 CURRENT PROBLEM
            // =====================================
            $table->text('current_problem')
                ->nullable()
                ->after('current_category');

            // =====================================
            // 🎫 TICKET CREATED
            // =====================================
            $table->boolean('ticket_created')
                ->default(false)
                ->after('current_problem');

            // =====================================
            // 🎟 CURRENT TICKET ID
            // =====================================
            $table->string('current_ticket_id')
                ->nullable()
                ->after('ticket_created');

            // =====================================
            // 📊 CONVERSATION STATUS
            // =====================================
            $table->string('conversation_status')
                ->default('active')
                ->after('current_ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {

            $table->dropColumn([

                'current_category',

                'current_problem',

                'ticket_created',

                'current_ticket_id',

                'conversation_status'
            ]);
        });
    }
};