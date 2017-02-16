<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use Mockery as m;

/**
 * @group  runner
 * @covers \ClickNow\Checker\Runner\ConfigRunner
 */
class ConfigRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Config\Checker|\Mockery\MockInterface
     */
    protected $checker;

    /**
     * @var \ClickNow\Checker\Runner\ConfigRunner|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configRunner;

    protected function setUp()
    {
        $this->checker = m::mock(Checker::class);
        $this->configRunner = $this->getMockForTrait(ConfigRunner::class, [$this->checker]);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCanRunIn()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getName')->withNoArgs()->twice()->andReturn('foo');

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getRunner->getName')->withNoArgs()->times(3)->andReturn('bar');

        $this->assertTrue($this->configRunner->canRunInContext($runner, $context));

        $this->configRunner->setCanRunIn(false);
        $this->assertFalse($this->configRunner->canRunInContext($runner, $context));

        $this->configRunner->setCanRunIn(['foo']);
        $this->assertTrue($this->configRunner->canRunInContext($runner, $context));

        $this->configRunner->setCanRunIn(['bar']);
        $this->assertTrue($this->configRunner->canRunInContext($runner, $context));

        $this->configRunner->setCanRunIn(['foobar']);
        $this->assertFalse($this->configRunner->canRunInContext($runner, $context));
    }

    public function testProcessTimeout()
    {
        $this->checker->shouldReceive('getProcessTimeout')->withNoArgs()->once()->andReturn(30);
        $this->assertSame(30.0, $this->configRunner->getProcessTimeout());

        $this->configRunner->setProcessTimeout(null);
        $this->assertSame(0.0, $this->configRunner->getProcessTimeout());

        $this->configRunner->setProcessTimeout(60);
        $this->assertSame(60.0, $this->configRunner->getProcessTimeout());
    }

    public function testProcessAsyncWait()
    {
        $this->checker->shouldReceive('getProcessAsyncWait')->withNoArgs()->once()->andReturn(10);
        $this->assertSame(10, $this->configRunner->getProcessAsyncWait());

        $this->configRunner->setProcessAsyncWait(null);
        $this->assertSame(0, $this->configRunner->getProcessAsyncWait());

        $this->configRunner->setProcessAsyncWait(20);
        $this->assertSame(20, $this->configRunner->getProcessAsyncWait());
    }

    public function testProcessAsyncLimit()
    {
        $this->checker->shouldReceive('getProcessAsyncLimit')->withNoArgs()->once()->andReturn(1000);
        $this->assertSame(1000, $this->configRunner->getProcessAsyncLimit());

        $this->configRunner->setProcessAsyncLimit(null);
        $this->assertSame(0, $this->configRunner->getProcessAsyncLimit());

        $this->configRunner->setProcessAsyncLimit(2000);
        $this->assertSame(2000, $this->configRunner->getProcessAsyncLimit());
    }

    public function testStopOnFailure()
    {
        $this->checker->shouldReceive('isStopOnFailure')->withNoArgs()->once()->andReturn(true);
        $this->assertTrue($this->configRunner->isStopOnFailure());

        $this->configRunner->setStopOnFailure(null);
        $this->assertFalse($this->configRunner->isStopOnFailure());

        $this->configRunner->setStopOnFailure(true);
        $this->assertTrue($this->configRunner->isStopOnFailure());
    }

    public function testIgnoreUnstagedChanges()
    {
        $this->checker->shouldReceive('isIgnoreUnstagedChanges')->withNoArgs()->once()->andReturn(true);
        $this->assertTrue($this->configRunner->isIgnoreUnstagedChanges());

        $this->configRunner->setIgnoreUnstagedChanges(null);
        $this->assertFalse($this->configRunner->isIgnoreUnstagedChanges());

        $this->configRunner->setIgnoreUnstagedChanges(true);
        $this->assertTrue($this->configRunner->isIgnoreUnstagedChanges());
    }

    public function testStrict()
    {
        $this->checker->shouldReceive('isStrict')->withNoArgs()->once()->andReturn(true);
        $this->assertTrue($this->configRunner->isStrict());

        $this->configRunner->setStrict(null);
        $this->assertFalse($this->configRunner->isStrict());

        $this->configRunner->setStrict(true);
        $this->assertTrue($this->configRunner->isStrict());
    }

    public function testProgress()
    {
        $this->checker->shouldReceive('getProgress')->withNoArgs()->once()->andReturn('list');
        $this->assertSame('list', $this->configRunner->getProgress());

        $this->configRunner->setProgress('bar');
        $this->assertSame('bar', $this->configRunner->getProgress());

        $this->configRunner->setProgress('');
        $this->assertEmpty($this->configRunner->getProgress());
    }

    public function testSkipSuccessOutput()
    {
        $this->checker->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(true);
        $this->assertTrue($this->configRunner->isSkipSuccessOutput());

        $this->configRunner->setSkipSuccessOutput(null);
        $this->assertFalse($this->configRunner->isSkipSuccessOutput());

        $this->configRunner->setSkipSuccessOutput(true);
        $this->assertTrue($this->configRunner->isSkipSuccessOutput());
    }

    public function testMessage()
    {
        $this->checker->shouldReceive('getMessage')->with('foo')->once()->andReturnNull();
        $this->assertNull($this->configRunner->getMessage('foo'));

        $this->checker->shouldReceive('getMessage')->with('failed')->once()->andReturn('ERROR');
        $this->assertSame('ERROR', $this->configRunner->getMessage('failed'));

        $this->configRunner->setMessage(['successfully' => 'OK']);
        $this->assertSame('OK', $this->configRunner->getMessage('successfully'));
    }
}
