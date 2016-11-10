<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\IO\IOInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Mockery as m;

/**
 * @group subscriber
 * @covers \ClickNow\Checker\Subscriber\ReportSubscriber
 */
class ReportSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \ClickNow\Checker\Console\Helper\PathsHelper|\Mockery\MockInterface
     */
    protected $paths;

    /**
     * @var \ClickNow\Checker\Subscriber\ReportSubscriber
     */
    protected $reportSubscriber;

    protected function setUp()
    {
        $this->io = m::mock(IOInterface::class);
        $this->reportSubscriber = new ProgressSubscriber($this->io);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->reportSubscriber);
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', ReportSubscriber::getSubscribedEvents());
        $this->assertCount(4, ReportSubscriber::getSubscribedEvents());
    }

    public function onReportSuccessWithMessage()
    {

    }

    public function onReportSuccessWithoutMessage()
    {

    }

    public function onReportSuccessAndWarning()
    {

    }

    public function onReportWithSkippedSuccess()
    {

    }

    public function onReportErrorWithMessage()
    {

    }

    public function onReportErrorWithoutMessage()
    {

    }

    public function onReportErrorAndWarning()
    {

    }
}
