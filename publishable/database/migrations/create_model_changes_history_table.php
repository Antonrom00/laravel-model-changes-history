<?php

use Antonrom\ModelChangesHistory\Models\Change;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelChangesHistoryTable extends Migration
{
    protected $tableName;

    public function __construct()
    {
        $this->connection = config('model_changes_history.stores.database.connection', null);
        $this->tableName  = config('model_changes_history.stores.database.table', 'model_changes_history');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('model_id');
            $table->string('model_type');

            $table->json('before_changes')->nullable();
            $table->json('after_changes')->nullable();

            $table->json('changes')->nullable();

            $table->enum('change_type', [
                Change::TYPE_CREATED,
                Change::TYPE_UPDATED,
                Change::TYPE_DELETED,
                Change::TYPE_RESTORED,
                Change::TYPE_FORCE_DELETED,
            ]);

            $table->string('changer_type')->nullable();
            $table->unsignedBigInteger('changer_id')->nullable();

            $table->json('stack_trace')->nullable();

            $table->timestamp(Change::CREATED_AT);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop($this->tableName);
    }
}