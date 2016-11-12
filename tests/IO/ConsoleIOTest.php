<?php

namespace ClickNow\Checker\IO;

use ClickNow\Checker\Exception\InvalidArgumentException;
use Mockery as m;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @group io
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
        $this->output->shouldReceive('getVerbosity')->withNoArgs()->once()->andReturn($verbosity);
        $this->output->shouldReceive('getFormatter')->withNoArgs()->once()->andReturn($formatter);

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
        $this->output->shouldReceive('isVeryVerbose')->withNoArgs()->once()->andReturn(true);
        $this->output->shouldReceive('write')->withAnyArgs()->twice()->andReturnNull();
        $this->output->shouldReceive('writeln')->with(' foo', BufferedOutput::OUTPUT_NORMAL)->once()->andReturnNull();

        $this->consoleIO->log('foo');
    }

    public function testLogIsNotVeryVerbose()
    {
        $this->output->shouldReceive('isVeryVerbose')->withNoArgs()->once()->andReturn(false);
        $this->output->shouldNotReceive('write');
        $this->output->shouldNotReceive('writeln');

        $this->consoleIO->log('foo');
    }

    public function testLogWithoutMessage()
    {
        $this->output->shouldReceive('isVeryVerbose')->withNoArgs()->once()->andReturn(true);
        $this->output->shouldNotReceive('write');
        $this->output->shouldNotReceive('writeln');

        $this->consoleIO->log('');
    }

    public function testReadyCommandInput()
    {
        $handle = $this->mockHandle('input');
        $this->assertSame('input', $this->consoleIO->readCommandInput($handle));
    }

    public function testReadyCommandInputEmpty()
    {
        $handle = $this->mockHandle("\r\n\t\f");
        $this->assertEmpty($this->consoleIO->readCommandInput($handle));
    }

    public function testReadyCommandInputInvalid()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Expected a resource stream for reading the commandline input. Got `string`.'
        );

        $this->consoleIO->readCommandInput('string');
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
