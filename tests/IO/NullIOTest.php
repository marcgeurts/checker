<?php

namespace ClickNow\Checker\IO;

use Symfony\Component\Console\Output\NullOutput;

/**
 * @group  io
 * @covers \ClickNow\Checker\IO\NullIO
 */
class NullIOTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\NullIO
     */
    protected $nullIO;

    protected function setUp()
    {
        $this->nullIO = new NullIO();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(IOInterface::class, $this->nullIO);
        $this->assertInstanceOf(NullOutput::class, $this->nullIO);
    }

    public function testIsInteractive()
    {
        $this->assertFalse($this->nullIO->isInteractive());
    }

    public function testCreateProgressBar()
    {
        $this->nullIO->createProgressBar();
    }

    public function testLog()
    {
        $this->nullIO->log('');
    }

    public function testTitle()
    {
        $this->nullIO->title('');
    }

    public function testSection()
    {
        $this->nullIO->section('');
    }

    public function testListing()
    {
        $this->nullIO->listing([]);
    }

    public function testText()
    {
        $this->nullIO->text('');
    }

    public function testSuccess()
    {
        $this->nullIO->success('');
    }

    public function testError()
    {
        $this->nullIO->error('');
    }

    public function testWarning()
    {
        $this->nullIO->warning('');
    }

    public function testNote()
    {
        $this->nullIO->note('');
    }

    public function testCaution()
    {
        $this->nullIO->caution('');
    }

    public function testTable()
    {
        $this->nullIO->table([], []);
    }

    public function testAsk()
    {
        $this->assertEmpty($this->nullIO->ask(''));
    }

    public function testAskHidden()
    {
        $this->assertEmpty($this->nullIO->askHidden(''));
    }

    public function testConfirm()
    {
        $this->assertFalse($this->nullIO->confirm(''));
    }

    public function testChoice()
    {
        $this->assertEmpty($this->nullIO->choice('', []));
    }

    public function testNewLine()
    {
        $this->nullIO->newLine();
    }

    public function testProgressStart()
    {
        $this->nullIO->progressStart();
    }

    public function testProgressAdvance()
    {
        $this->nullIO->progressAdvance();
    }

    public function testProgressFinish()
    {
        $this->nullIO->progressFinish();
    }
}
