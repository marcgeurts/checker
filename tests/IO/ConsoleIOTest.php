<?php

namespace ClickNow\Checker\IO;

use ClickNow\Checker\Exception\InvalidArgumentException;
use Mockery as m;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @group io
 * @covers \ClickNow\Checker\IO\ConsoleIO
 */
class ConsoleIOTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $io = new ConsoleIO(m::mock(InputInterface::class), $this->mockOutput());
        $this->assertInstanceOf(IOInterface::class, $io);
        $this->assertInstanceOf(SymfonyStyle::class, $io);
    }

    public function testIsInteractive()
    {
        $input = m::mock(InputInterface::class);
        $input->shouldReceive('isInteractive')->once()->andReturn(false);

        $io = new ConsoleIO($input, $this->mockOutput());
        $this->assertFalse($io->isInteractive());
    }

    public function testLog()
    {
        $output = $this->mockOutput();
        $output->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $output->shouldReceive('write')->twice()->andReturnNull();
        $output->shouldReceive('writeln')->once()->andReturnNull();

        $io = new ConsoleIO(m::mock(InputInterface::class), $output);
        $io->log('foo');
    }

    public function testLogIsNotVeryVerbose()
    {
        $output = $this->mockOutput();
        $output->shouldReceive('isVeryVerbose')->once()->andReturn(false);

        $io = new ConsoleIO(m::mock(InputInterface::class), $output);
        $io->log('foo');
    }

    public function testLogWithoutMessage()
    {
        $output = $this->mockOutput();
        $output->shouldReceive('isVeryVerbose')->once()->andReturn(true);

        $io = new ConsoleIO(m::mock(InputInterface::class), $output);
        $io->log('');
    }

    public function testReadyCommandInput()
    {
        $io = new ConsoleIO(m::mock(InputInterface::class), $this->mockOutput());
        $handle = $this->mockHandle('input');
        $this->assertEquals('input', $io->readCommandInput($handle));
    }

    public function testReadyCommandInputEmpty()
    {
        $io = new ConsoleIO(m::mock(InputInterface::class), $this->mockOutput());
        $handle = $this->mockHandle("\r\n\t\f");
        $this->assertEmpty($io->readCommandInput($handle));
    }

    public function testReadyCommandInputInvalid()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Expected a resource stream for reading the commandline input. Got `string`.'
        );

        $io = new ConsoleIO(m::mock(InputInterface::class), $this->mockOutput());
        $io->readCommandInput('string');
    }

    protected function mockOutput()
    {
        $output = m::mock(OutputInterface::class);
        $output->shouldReceive('getVerbosity')->once()->andReturn(OutputInterface::VERBOSITY_NORMAL);
        $output->shouldReceive('getFormatter')->once()->andReturn(m::spy(OutputFormatterInterface::class));

        return $output;
    }

    protected function mockHandle($content)
    {
        $handle = fopen('php://memory', 'a');
        fwrite($handle, $content);
        rewind($handle);

        return $handle;
    }
}
