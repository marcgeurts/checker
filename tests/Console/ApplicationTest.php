<?php

namespace ClickNow\Checker\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @group console
 * @covers \ClickNow\Checker\Console\Application
 * @runTestsInSeparateProcesses
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Tester\ApplicationTester
     */
    protected $applicationTester;

    protected function setUp()
    {
        $application = new Application();
        $application->setAutoExit(false);

        $this->applicationTester = new ApplicationTester($application);
    }

    public function testName()
    {
        $this->applicationTester->run([]);

        $this->assertRegExp('/'.Application::APP_NAME.'/', $this->applicationTester->getDisplay());
    }

    public function testVersion()
    {
        $this->applicationTester->run(['--version']);

        $this->assertRegExp('/'.Application::APP_VERSION.'/', $this->applicationTester->getDisplay());
    }

    public function testConfig()
    {
        $this->applicationTester->run(['--config' => 'foo']);

        $this->assertTrue($this->applicationTester->getInput()->hasParameterOption('--config'));
        $this->assertSame('foo', $this->applicationTester->getInput()->getParameterOption('--config'));
    }

    public function testConfigShortcut()
    {
        $this->applicationTester->run(['-c' => 'foo']);

        $this->assertTrue($this->applicationTester->getInput()->hasParameterOption('-c'));
        $this->assertSame('foo', $this->applicationTester->getInput()->getParameterOption('-c'));
    }

    public function testVerbose()
    {
        $this->applicationTester->run([], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        $this->assertTrue($this->applicationTester->getOutput()->isVerbose());
    }
}
