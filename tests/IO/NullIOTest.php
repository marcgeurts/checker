<?php

namespace ClickNow\Checker\IO;

use Symfony\Component\Console\Output\NullOutput;

/**
 * @group io
 * @covers \ClickNow\Checker\IO\NullIO
 */
class NullIOTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\NullIO
     */
    protected $io;

    protected function setUp()
    {
        $this->io = new NullIO();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(IOInterface::class, $this->io);
        $this->assertInstanceOf(NullOutput::class, $this->io);
    }

    public function testIsInteractive()
    {
        $this->assertFalse($this->io->isInteractive());
    }

    public function testCreateProgressBar()
    {
        $this->io->createProgressBar();
    }

    public function testLog()
    {
        $this->io->log('');
    }

    public function testTitle()
    {
        $this->io->title('');
    }

    public function testSection()
    {
        $this->io->section('');
    }

    public function testListing()
    {
        $this->io->listing([]);
    }

    public function testText()
    {
        $this->io->text('');
    }

    public function testSuccess()
    {
        $this->io->success('');
    }

    public function testError()
    {
        $this->io->error('');
    }

    public function testWarning()
    {
        $this->io->warning('');
    }

    public function testNote()
    {
        $this->io->note('');
    }

    public function testCaution()
    {
        $this->io->caution('');
    }

    public function testTable()
    {
        $this->io->table([], []);
    }

    public function testAsk()
    {
        $this->assertEmpty($this->io->ask(''));
    }

    public function testAskHidden()
    {
        $this->assertEmpty($this->io->askHidden(''));
    }

    public function testConfirm()
    {
        $this->assertFalse($this->io->confirm(''));
    }

    public function testChoice()
    {
        $this->assertEmpty($this->io->choice('', []));
    }

    public function testNewLine()
    {
        $this->io->newLine();
    }

    public function testProgressStart()
    {
        $this->io->progressStart();
    }

    public function testProgressAdvance()
    {
        $this->io->progressAdvance();
    }

    public function testProgressFinish()
    {
        $this->io->progressFinish();
    }
}
