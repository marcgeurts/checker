<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Exception\ActionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ActionNotFoundException;
use Mockery as m;

/**
 * @group  runner
 * @covers \ClickNow\Checker\Runner\ActionRunner
 */
class ActionRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Runner\ActionRunner|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionRunner;

    /**
     * @var \ClickNow\Checker\Runner\ActionInterface|\Mockery\MockInterface
     */
    protected $action;

    protected function setUp()
    {
        $this->actionRunner = $this->getMockForTrait(ActionRunner::class, [new ActionsCollection()]);

        $this->action = m::mock(ActionInterface::class);
        $this->action->shouldReceive('getName')->withNoArgs()->andReturn('bar');
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetActionsIsEmpty()
    {
        $actions = $this->actionRunner->getActions();

        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertEmpty($actions);
    }

    public function testAddAction()
    {
        $this->actionRunner->addAction($this->action);
        $actions = $this->actionRunner->getActions();

        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertCount(1, $actions);
        $this->assertSame($this->action, $actions->first());
    }

    public function testAddActionThrowsWhenActionHasAlreadyBeenAdded()
    {
        $this->setExpectedException(ActionAlreadyRegisteredException::class, 'Action `bar` already registered.');

        $this->actionRunner->addAction($this->action);
        $this->actionRunner->addAction($this->action);
    }

    public function testGetActionMetadataDefault()
    {
        $this->actionRunner->addAction($this->action);
        $metadata = $this->actionRunner->getActionMetadata($this->action);

        $this->assertInternalType('array', $metadata);
        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertSame(['priority' => 0, 'blocking' => true], $metadata);
        $this->assertSame(0, $this->actionRunner->getActionPriority($this->action));
        $this->assertTrue($this->actionRunner->isActionBlocking($this->action));
    }

    public function testGetActionMetadataNotFound()
    {
        $this->setExpectedException(ActionNotFoundException::class, 'Action `bar` was not found.');

        $this->actionRunner->getActionMetadata($this->action);
    }

    public function testSetActionMetadataPriority()
    {
        $this->actionRunner->addAction($this->action, ['metadata' => ['priority' => 100]]);
        $metadata = $this->actionRunner->getActionMetadata($this->action);

        $this->assertInternalType('array', $metadata);
        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertSame(['priority' => 100, 'blocking' => true], $metadata);
        $this->assertSame(100, $this->actionRunner->getActionPriority($this->action));
        $this->assertTrue($this->actionRunner->isActionBlocking($this->action));
    }

    public function testSetActionMetadataBlocking()
    {
        $this->actionRunner->addAction($this->action, ['metadata' => ['blocking' => false]]);
        $metadata = $this->actionRunner->getActionMetadata($this->action);

        $this->assertInternalType('array', $metadata);
        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertSame(['priority' => 0, 'blocking' => false], $metadata);
        $this->assertSame(0, $this->actionRunner->getActionPriority($this->action));
        $this->assertFalse($this->actionRunner->isActionBlocking($this->action));
    }

    public function testGetActionConfig()
    {
        $this->actionRunner->addAction($this->action, ['foo' => 'bar']);
        $config = $this->actionRunner->getActionConfig($this->action);

        $this->assertInternalType('array', $config);
        $this->assertSame(['foo' => 'bar'], $config);
    }

    public function testGetActionConfigEmpty()
    {
        $this->actionRunner->addAction($this->action);
        $config = $this->actionRunner->getActionConfig($this->action);

        $this->assertInternalType('array', $config);
        $this->assertEmpty($config);
    }

    public function testGetActionConfigNotFound()
    {
        $this->setExpectedException(ActionNotFoundException::class, 'Action `bar` was not found.');

        $this->actionRunner->getActionConfig($this->action);
    }
}
