# drop_conversation_messages_table.php

```php id="jlwm17"
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('conversation_messages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};