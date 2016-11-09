<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Exception\InvalidArgumentException;
use ClickNow\Checker\Util\FilesCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * @group process
 * @covers \ClickNow\Checker\Process\ArgumentsCollection
 */
class ArgumentsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Process\ArgumentsCollection
     */
    protected $argumentsCollection;

    protected function setUp()
    {
        $this->argumentsCollection = new ArgumentsCollection();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->argumentsCollection);
    }

    public function testForExecutable()
    {
        $result = ArgumentsCollection::forExecutable('foo');

        $this->assertInstanceOf(ArgumentsCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame('foo', $result->first());
    }

    public function testAddOptionalArgument()
    {
        $this->argumentsCollection->addOptionalArgument('--item=%s', null);
        $this->assertSame([], $this->argumentsCollection->getValues());

        $this->argumentsCollection->addOptionalArgument('--item=%s', 'value');
        $this->assertSame(['--item=value'], $this->argumentsCollection->getValues());
    }

    public function testAddOptionalArgumentWithSeparatedValue()
    {
        $this->argumentsCollection->addOptionalArgumentWithSeparatedValue('--item', null);
        $this->assertSame([], $this->argumentsCollection->getValues());

        $this->argumentsCollection->addOptionalArgumentWithSeparatedValue('--item', 'value');
        $this->assertSame(['--item', 'value'], $this->argumentsCollection->getValues());
    }

    public function testAddOptionalCommaSeparatedArgument()
    {
        $this->argumentsCollection->addOptionalCommaSeparatedArgument('--item=%s', []);
        $this->assertSame([], $this->argumentsCollection->getValues());

        $this->argumentsCollection->addOptionalCommaSeparatedArgument('--item=%s', [1, 2]);
        $this->assertSame(['--item=1,2'], $this->argumentsCollection->getValues());

        $this->argumentsCollection->clear();
        $this->argumentsCollection->addOptionalCommaSeparatedArgument('--item=%s', [1, 2], '|');
        $this->assertSame(['--item=1|2'], $this->argumentsCollection->getValues());
    }

    public function testAddArgumentArray()
    {
        $this->argumentsCollection->addArgumentArray('--item=%s', [1, 2]);
        $this->assertSame(['--item=1', '--item=2'], $this->argumentsCollection->getValues());
    }

    public function testAddArgumentArrayWithSeparatedValue()
    {
        $this->argumentsCollection->addArgumentArrayWithSeparatedValue('--item', [1, 2]);
        $this->assertSame(['--item', 1, '--item', 2], $this->argumentsCollection->getValues());
    }

    public function testAddSeparatedArgumentArray()
    {
        $this->argumentsCollection->addSeparatedArgumentArray('--item', []);
        $this->assertSame([], $this->argumentsCollection->getValues());

        $this->argumentsCollection->addSeparatedArgumentArray('--item', [1, 2]);
        $this->assertSame(['--item', 1, 2], $this->argumentsCollection->getValues());
    }

    public function testAddRequiredArgument()
    {
        $this->argumentsCollection->addRequiredArgument('--item=%s', 'value');
        $this->assertSame(['--item=value'], $this->argumentsCollection->getValues());

        $this->setExpectedException(InvalidArgumentException::class, 'The argument `--item=%s` is required.');
        $this->argumentsCollection->addRequiredArgument('--item=%s', null);
    }

    public function testAddFiles()
    {
        $this->argumentsCollection->addFiles($this->mockFiles());
        $this->assertSame(['file1.txt', 'file2.txt'], $this->argumentsCollection->getValues());
    }

    public function testAddCommaSeparatedFiles()
    {
        $this->argumentsCollection->addCommaSeparatedFiles($this->mockFiles());
        $this->assertSame(['file1.txt,file2.txt'], $this->argumentsCollection->getValues());
    }

    protected function mockFiles()
    {
        $files = m::mock(FilesCollection::class);
        $files->shouldReceive('getAllPaths')->withNoArgs()->once()->andReturn(['file1.txt', 'file2.txt']);

        return $files;
    }
}
