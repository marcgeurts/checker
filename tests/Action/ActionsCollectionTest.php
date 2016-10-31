<?php

use ClickNow\Checker\Action\ActionsCollection;
use Mockery as m;

class ActionsCollectionTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testSortOnPriority()
    {
        $action1 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action2 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action3 = m::mock('ClickNow\Checker\Action\ActionInterface');

        $command = m::mock('ClickNow\Checker\Command\CommandInterface');
        $command->shouldReceive('getPriorityAction')->once()->with($action1)->andReturn(100);
        $command->shouldReceive('getPriorityAction')->once()->with($action2)->andReturn(200);
        $command->shouldReceive('getPriorityAction')->once()->with($action3)->andReturn(100);

        $actionsCollection = new ActionsCollection([$action1, $action2, $action3]);
        $result = $actionsCollection->sortByPriority($command);
        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(3, $result);

        $actions = $result->toArray();
        $this->assertSame($action2, $actions[0]);
        $this->assertSame($action1, $actions[1]);
        $this->assertSame($action3, $actions[2]);
    }

    public function testFilterByContext()
    {
        $action1 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action2 = m::mock('ClickNow\Checker\Action\ActionInterface');

        $action1->shouldReceive('canRunInContext')->once()->andReturn(true);
        $action2->shouldReceive('canRunInContext')->once()->andReturn(false);

        $command = m::mock('ClickNow\Checker\Command\CommandInterface');
        $context = m::mock('ClickNow\Checker\Context\ContextInterface');

        $actionsCollection = new ActionsCollection([$action1, $action2]);
        $result = $actionsCollection->filterByContext($command, $context);
        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(1, $result);

        $actions = $result->toArray();
        $this->assertSame($action1, $actions[0]);
    }
}
