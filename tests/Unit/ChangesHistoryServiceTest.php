<?php

namespace Antonrom\ModelChangesHistory\Tests\Unit;

use Antonrom\ModelChangesHistory\Facades\ChangesHistory;
use Antonrom\ModelChangesHistory\Models\Change;
use Antonrom\ModelChangesHistory\Tests\fixtures\TestModel;
use Antonrom\ModelChangesHistory\Tests\TestCase;

class ChangesHistoryServiceTest extends TestCase
{
    public function testCreateChange()
    {
        $testModel = TestModel::make([
            'title' => 'Test title',
            'body'  => 'Test body',
        ]);

        $change = ChangesHistory::createChange(Change::TYPE_CREATED, $testModel);

        $this->assertEquals(Change::TYPE_CREATED, $change->change_type);
        $this->assertEquals(get_class($testModel), $change->model_type);
        $this->assertEquals(collect(), $change->changes);
        $this->assertNull($change->changer_type);
    }
}
