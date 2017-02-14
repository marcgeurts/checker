<?php

namespace ClickNow\Checker\IO;

use Mockery as m;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @group  io
 * @covers \ClickNow\Checker\IO\ConsoleIO
 */
class ConsoleIOTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface|\Mockery\MockInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface|\Mockery\MockInterface
     */
    protected $output;

    /**
     * @var \ClickNow\Checker\IO\ConsoleIO
     */
    protected $consoleIO;

    public function setUp()
    {
        $this->input = m::mock(InputInterface::class);

        $verbosity = OutputInterface::VERBOSITY_NORMAL;
        $formatter = m::spy(OutputFormatterInterface::class);

        $this->output = m::mock(OutputInterface::class);
        $this->output->shouldReceive('getVerbosity')->withNoArgs()->atLeast()->once()->andReturn($verbosity);
        $this->output->shouldReceive('getFormatter')->withNoArgs()->atLeast()->once()->andReturn($formatter);

        $this->consoleIO = new ConsoleIO($this->input, $this->output);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(IOInterface::class, $this->consoleIO);
        $this->assertInstanceOf(SymfonyStyle::class, $this->consoleIO);
    }

    public function testIsInteractive()
    {
        $this->input->shouldReceive('isInteractive')->withNoArgs()->once()->andReturn(false);

        $this->assertFalse($this->consoleIO->isInteractive());
    }

    public function testLog()
    {
        $this->output->shouldReceive('isVerbose')->withNoArgs()->once()->andReturn(true);
        $this->output->shouldReceive('write')->withAnyArgs()->twice()->andReturnNull();
        $this->output->shouldReceive('writeln')->with(' foo', m::any())->once()->andReturnNull();

        $this->consoleIO->log('foo');
    }

    public function testLogIsNotVeryVerbose()
    {
        $this->output->shouldReceive('isVerbose')->withNoArgs()->once()->andReturn(false);
        $this->output->shouldReceive('write')->withAnyArgs()->never();
        $this->output->shouldReceive('writeln')->withAnyArgs()->never();

        $this->consoleIO->log('foo');
    }

    public function testLogWithoutMessage()
    {
        $this->output->shouldReceive('isVerbose')->withNoArgs()->once()->andReturn(true);
        $this->output->shouldReceive('write')->withAnyArgs()->never();
        $this->output->shouldReceive('writeln')->withAnyArgs()->never();

        $this->consoleIO->log('');
    }

    public function testWarning()
    {
        $this->output->shouldReceive('isDecorated')->withNoArgs()->once()->andReturn(true);
        $this->output->shouldReceive('write')->withAnyArgs()->twice()->andReturnNull();
        $this->output->shouldReceive('writeln')->withAnyArgs()->once()->andReturnNull();

        $this->consoleIO->warning('warning');
    }

    public function testSuccessText()
    {
        $this->output->shouldReceive('write')->withAnyArgs()->once()->andReturnNull();
        $this->output->shouldReceive('writeln')->with('/success/', m::any())->once()->andReturnNull();

        $this->consoleIO->successText('success');
    }

    public function testWarningText()
    {
        $this->output->shouldReceive('write')->withAnyArgs()->once()->andReturnNull();
        $this->output->shouldReceive('writeln')->with('/warning/', m::any())->once()->andReturnNull();

        $this->consoleIO->warningText('warning');
    }

    public function testErrorText()
    {
        $this->output->shouldReceive('write')->withAnyArgs()->once()->andReturnNull();
        $this->output->shouldReceive('writeln')->with('/error/', m::any())->once()->andReturnNull();

        $this->consoleIO->errorText('error');
    }

    public function testReadyCommandInput()
    {
        $diff = <<<eod
diff --git file1.php file2.php
index 372bf10b74013301cfb4bf0e8007d208bb813363..d95f50da4a02d3d203bda1f3cb94e29d4f0ef481 100644
--- file1.php
+++ file2.php
@@ -2,3 +2,4 @@
 
 
 'something';
+'ok';

eod;

        $this->assertSame($diff, $this->consoleIO->readCommandInput($this->mockHandle($diff)));
        $this->assertSame('input', $this->consoleIO->readCommandInput($this->mockHandle('input')));
        $this->assertEmpty($this->consoleIO->readCommandInput($this->mockHandle("\r\n\t\f")));
        $this->assertNull($this->consoleIO->readCommandInput(null));
    }

    /**
     * Mock handle.
     *
     * @param string $content
     *
     * @return resource
     */
    protected function mockHandle($content)
    {
        $handle = fopen('php://memory', 'a');
        fwrite($handle, $content);
        rewind($handle);

        return $handle;
    }
}
