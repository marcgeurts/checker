<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Exception;
use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Repository;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group  subscriber
 * @covers \ClickNow\Checker\Subscriber\StashUnstagedChangesSubscriber
 */
class StashUnstagedChangesSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \Gitonomy\Git\Repository|\Mockery\MockInterface
     */
    protected $repository;

    /**
     * @var \ClickNow\Checker\Subscriber\StashUnstagedChangesSubscriber
     */
    protected $stashUnstagedChangesSubscriber;

    protected function setUp()
    {
        $this->io = m::mock(IOInterface::class);
        $this->repository = m::mock(Repository::class);
        $this->stashUnstagedChangesSubscriber = new StashUnstagedChangesSubscriber($this->io, $this->repository);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->stashUnstagedChangesSubscriber);
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', StashUnstagedChangesSubscriber::getSubscribedEvents());
        $this->assertCount(4, StashUnstagedChangesSubscriber::getSubscribedEvents());
    }

    public function testStashIsNotEnabled()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isIgnoreUnstagedChanges')->withNoArgs()->once()->andReturn(false);

        $this->repository->shouldReceive('getWorkingCopy')->withNoArgs()->never();
        $this->stashUnstagedChangesSubscriber->saveStash($this->mockEvent($runner));
    }

    public function testStashWithoutFiles()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isIgnoreUnstagedChanges')->withNoArgs()->once()->andReturn(true);

        $diff = $this->mockDiff();
        $this->repository->shouldReceive('getWorkingCopy->getDiffPending')->withNoArgs()->once()->andReturn($diff);

        $this->stashUnstagedChangesSubscriber->saveStash($this->mockEvent($runner));
    }

    public function testStashSuccessfully()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isIgnoreUnstagedChanges')->withNoArgs()->once()->andReturn(true);

        $diff = $this->mockDiff(['file.txt']);

        $this->repository->shouldReceive('getWorkingCopy->getDiffPending')->withNoArgs()->once()->andReturn($diff);
        $this->repository->shouldReceive('run')->with('stash', m::contains('save'))->once()->andReturnNull();
        $this->repository->shouldReceive('run')->with('stash', m::contains('pop'))->once()->andReturnNull();

        $this->io->shouldReceive('warning')->withAnyArgs()->twice()->andReturnNull();
        $this->io->shouldReceive('errorText')->withAnyArgs()->never();

        $this->stashUnstagedChangesSubscriber->saveStash($this->mockEvent($runner));
        $this->stashUnstagedChangesSubscriber->popStash();
    }

    public function testStashWithSaveFailed()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isIgnoreUnstagedChanges')->withNoArgs()->once()->andReturn(true);

        $diff = $this->mockDiff(['file.txt']);

        $this->repository->shouldReceive('getWorkingCopy->getDiffPending')->withNoArgs()->once()->andReturn($diff);
        $this->repository->shouldReceive('run')->with('stash', m::contains('save'))->once()->andThrow(Exception::class);
        $this->repository->shouldReceive('run')->with('stash', m::contains('pop'))->never();

        $this->io->shouldReceive('warning')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('errorText')->withAnyArgs()->once()->andReturnNull();

        $this->stashUnstagedChangesSubscriber->saveStash($this->mockEvent($runner));
        $this->stashUnstagedChangesSubscriber->popStash();
    }

    public function testStashWithPopFailed()
    {
        $this->setExpectedException(RuntimeException::class);

        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isIgnoreUnstagedChanges')->withNoArgs()->once()->andReturn(true);

        $diff = $this->mockDiff(['file.txt']);

        $this->repository->shouldReceive('getWorkingCopy->getDiffPending')->withNoArgs()->once()->andReturn($diff);
        $this->repository->shouldReceive('run')->with('stash', m::contains('save'))->once()->andReturnNull();
        $this->repository->shouldReceive('run')->with('stash', m::contains('pop'))->once()->andThrow(Exception::class);

        $this->io->shouldReceive('warning')->withAnyArgs()->twice()->andReturnNull();
        $this->io->shouldReceive('errorText')->withAnyArgs()->never();

        $this->stashUnstagedChangesSubscriber->saveStash($this->mockEvent($runner));
        $this->stashUnstagedChangesSubscriber->popStash();
    }

    /**
     * Mock diff.
     *
     * @param array $files
     *
     * @return \Gitonomy\Git\Diff\Diff|\Mockery\MockInterface
     */
    protected function mockDiff(array $files = [])
    {
        $diff = m::mock(Diff::class);
        $diff->shouldReceive('getFiles')->withNoArgs()->once()->andReturn($files);

        return $diff;
    }

    /**
     * Mock event.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return \ClickNow\Checker\Event\RunnerEvent|\Mockery\MockInterface
     */
    protected function mockEvent(RunnerInterface $runner)
    {
        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getRunner')->withNoArgs()->once()->andReturn($runner);

        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext')->withNoArgs()->once()->andReturn($context);

        return $runnerEvent;
    }
}
