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

    public function testIsNotSuccessfullyIfResultIsEmpty()
    {
        $this->assertFalse($this->resultsCollection->isSuccessfully());
    }

    public function testIsNotFailedIfResultIsEmpty()
    {
        $this->assertFalse($this->resultsCollection->isFailed());
    }

    public function testFilterByStatus()
    {
        $resultSkipped = Result::skipped($this->command, $this->context, $this->action);
        $resultSuccess = Result::success($this->command, $this->context, $this->action);
        $resultWarning = Result::warning($this->command, $this->context, $this->action, 'WARNING');
        $resultError = Result::error($this->command, $this->context, $this->action, 'ERROR');

        $this->resultsCollection->add($resultSkipped);
        $this->resultsCollection->add($resultSuccess);
        $this->resultsCollection->add($resultWarning);
        $this->resultsCollection->add($resultError);
        $this->assertCount(4, $this->resultsCollection);

        $skipped = $this->resultsCollection->filterBySkipped();
        $this->assertCount(1, $skipped);
        $this->assertSame($resultSkipped, $skipped[0]);
        $this->assertNull($skipped[1]);
        $this->assertNull($skipped[2]);
        $this->assertNull($skipped[3]);

        $success = $this->resultsCollection->filterBySuccess();
        $this->assertCount(1, $success);
        $this->assertNull($success[0]);
        $this->assertSame($resultSuccess, $success[1]);
        $this->assertNull($success[2]);
        $this->assertNull($success[3]);

        $warning = $this->resultsCollection->filterByWarning();
        $this->assertCount(1, $warning);
        $this->assertNull($warning[0]);
        $this->assertNull($warning[1]);
        $this->assertSame($resultWarning, $warning[2]);
        $this->assertNull($warning[3]);

        $error = $this->resultsCollection->filterByError();
        $this->assertCount(1, $error);
        $this->assertNull($error[0]);
        $this->assertNull($error[1]);
        $this->assertNull($error[2]);
        $this->assertSame($resultError, $error[3]);
    }

    public function testGetAllMessages()
    {
        $this->resultsCollection->add(Result::skipped($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::success($this->command, $this->context, $this->action));
        $this->resultsCollection->add(Result::warning($this->command, $this->context, $this->action, 'WARNING'));
        $this->resultsCollection->add(Result::error($this->command, $this->context, $this->action, 'ERROR'));

        $result = $this->resultsCollection->getAllMessages();

        $this->assertCount(2, $result);
        $this->assertSame([2 => 'WARNING', 3 => 'ERROR'], $result);
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
