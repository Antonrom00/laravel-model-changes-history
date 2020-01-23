<?php

namespace Antonrom\ModelChangesHistory\Tests\Unit;

use Antonrom\ModelChangesHistory\Models\Change;
use Antonrom\ModelChangesHistory\Tests\Models\TestModel;
use Antonrom\ModelChangesHistory\Tests\TestCase;

class HistoryStorageServiceTest extends TestCase
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var TestModel
     */
    protected $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table = config('model_changes_history.stores.database.table');
        $this->testModel = TestModel::create([
            'title' => 'Test title',
            'body'  => 'Test body',
        ]);
    }

    public function testRecordChangeCreate()
    {
        $this->assertDatabaseHas($this->table, [
            'model_type'  => get_class($this->testModel),
            'change_type' => Change::TYPE_CREATED,
        ]);
    }

    public function testRecordChangeUpdate()
    {
        $originalModel = clone $this->testModel;
        $this->testModel->update([
            'title' => 'Test title updated',
            'body'  => 'Test body updated',
        ]);

        $this->assertDatabaseHas($this->table, [
            'model_id'    => $this->testModel->id,
            'model_type'  => get_class($this->testModel),
            'change_type' => Change::TYPE_UPDATED,
            'changes'     => json_encode([
                'title' => [
                    'before' => $originalModel->title,
                    'after'  => $this->testModel->title,
                ],
                'body'  => [
                    'before' => $originalModel->body,
                    'after'  => $this->testModel->body,
                ],
            ]),
        ]);
    }

    public function testRecordChangeDelete()
    {
        $this->testModel->delete();
        $this->assertDatabaseHas($this->table, [
            'model_type'  => get_class($this->testModel),
            'change_type' => Change::TYPE_DELETED,
        ]);
    }

    public function testRecordChangeRestore()
    {
        $this->testModel->restore();
        $this->assertDatabaseHas($this->table, [
            'model_type'  => get_class($this->testModel),
            'change_type' => Change::TYPE_RESTORED,
        ]);
    }

    public function testRecordChangeForceDelete()
    {
        $this->testModel->forceDelete();
        $this->assertDatabaseHas($this->table, [
            'model_type'  => get_class($this->testModel),
            'change_type' => Change::TYPE_FORCE_DELETED,
        ]);
    }
}
