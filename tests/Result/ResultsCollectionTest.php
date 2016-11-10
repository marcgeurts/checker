<?php

namespace ClickNow\Checker\Result;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * @group result
 * @covers \ClickNow\Checker\Result\ResultsCollection
 */
class ResultsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\CommandInterface|\Mockery\MockInterface
     */
    protected $command;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \ClickNow\Checker\Action\ActionInterface|\Mockery\MockInterface
     */
    protected $action;

    /**
     * @var \ClickNow\Checker\Result\ResultsCollection
     */
    protected $resultsCollection;

    protected function setUp()
    {
        $this->command = m::mock(CommandInterface::class);
        $this->context = m::mock(ContextInterface::class);
        $this->action = m::mock(ActionInterface::class);
        $this->resultsCollection = new ResultsCollection();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->resultsCollection);
    }

    public function testContainsResult()
    {
        $result1 = m::mock(ResultInterface::class);
        $result2 = m::mock(ResultInterface::class);

        $this->resultsCollection->add($result1);
        $this->resultsCollection->add($result2);

        $results = $this->resultsCollection->toArray();
        $this->assertCount(2, $this->resultsCollection);
        $this->assertSame($result1, $results[0]);
        $this->assertSame($result2, $results[1]);
    }

    public function testIsSuccessfullyIfItContainsOnlyResultPassed()
    {
        $this->resultsCollection->add(Result::success($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::skipped($this->command, $this->context, $this->action));

        $this->assertTrue($this->resultsCollection->isSuccessfully());
    }

    public function testIsFailedIfItContainsAnyResultError()
    {
        $this->resultsCollection->add(Result::success($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::error($this->command, $this->context, $this->action, 'ERROR'));

        $this->assertTrue($this->resultsCollection->isFailed());
    }

    public function testIfResultIsEmpty()
    {
        $this->assertFalse($this->resultsCollection->isSuccessfully());
        $this->assertFalse($this->resultsCollection->isFailed());
    }

    public function testFilterByStatus()
    {
        $this->resultsCollection->add(Result::skipped($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::success($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::warning($this->command, $this->context, $this->action, 'WARNING'));
        $this->resultsCollection->add(Result::error($this->command, $this->context, $this->action, 'ERROR'));

        $this->assertCount(4, $this->resultsCollection);
        $this->assertCount(1, $this->resultsCollection->filterBySkipped());
        $this->assertCount(1, $this->resultsCollection->filterBySuccess());
        $this->assertCount(1, $this->resultsCollection->filterByWarning());
        $this->assertCount(1, $this->resultsCollection->filterByError());
    }

    public function testGetAllMessages()
    {
        $this->resultsCollection->add(Result::skipped($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::success($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::warning($this->command, $this->context, $this->action, 'WARNING'));
        $this->resultsCollection->add(Result::error($this->command, $this->context, $this->action, 'ERROR'));

        $result = $this->resultsCollection->getAllMessages();
        $this->assertCount(2, $result);
        $this->assertSame(['WARNING', 'ERROR'], $result);
    }

    public function testGetAllMessagesEmpty()
    {
        $this->resultsCollection->add(Result::skipped($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::success($this->command, $this->context, $this->action));

        $this->assertEmpty($this->resultsCollection->getAllMessages());
    }

    public function testGetAllMessagesInResultsCollectionEmpty()
    {
        $this->assertEmpty($this->resultsCollection->getAllMessages());
    }
}
