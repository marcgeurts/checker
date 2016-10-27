<?php

use ClickNow\Checker\Action\ActionsCollection;
use Mockery as m;

class ActionsCollectionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testeAAA()
    {
        $command = m::mock('ClickNow\Checker\Command\Command');
        $command->shouldReceive('getPriorityAction')->andReturn();

        $actionsCollection = new ActionsCollection([$command]);
        $actionsCollection->sortByPriority($command);
    }

    public function testeAAAA()
    {
        $command = m::mock('ClickNow\Checker\Command\Command');
        $command->shouldReceive('canRunInContext')->andReturn();

        $context = m::mock('ClickNow\Checker\Context\ContextInterface');

        $actionsCollection = new ActionsCollection([$command]);
        $actionsCollection->filterByContext($command, $context);
    }
}
