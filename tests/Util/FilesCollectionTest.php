<?php

namespace ClickNow\Checker\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @group util
 * @covers \ClickNow\Checker\Util\FilesCollection
 */
class FilesCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $tempFile;

    /**
     * @var \ClickNow\Checker\Util\FilesCollection
     */
    protected $filesCollection;

    protected function setUp()
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'phpunit');
        $this->filesCollection = new FilesCollection();
    }

    protected function tearDown()
    {
        unlink($this->tempFile);

        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->filesCollection);
    }

    public function testFilterByName()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getFilename')->withNoArgs()->once()->andReturn('file.png');
        $file2->shouldReceive('getFilename')->withNoArgs()->once()->andReturn('file.php');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterByName('*.php');

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testFilterByNotName()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getFilename')->withNoArgs()->once()->andReturn('file.php');
        $file2->shouldReceive('getFilename')->withNoArgs()->once()->andReturn('file.png');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterByNotName('*.php');

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testFilterByByPath()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getRelativePathname')->withNoArgs()->once()->andReturn('path1/file.php');
        $file2->shouldReceive('getRelativePathname')->withNoArgs()->once()->andReturn('path2/file.png');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterByPath('path2');

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testFilterByNotByPath()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getRelativePathname')->withNoArgs()->once()->andReturn('path2/file.php');
        $file2->shouldReceive('getRelativePathname')->withNoArgs()->once()->andReturn('path1/file.png');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterByNotPath('path2');

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testFilterByExtensions()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);
        $file3 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getFilename')->withNoArgs()->once()->andReturn('file.php');
        $file2->shouldReceive('getFilename')->withNoArgs()->once()->andReturn('file.png');
        $file3->shouldReceive('getFilename')->withNoArgs()->once()->andReturn('file.js');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);
        $this->filesCollection->add($file3);

        $result = $this->filesCollection->filterByExtensions(['php', 'js']);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($file1, $result[0]);
        $this->assertNull($result[1]);
        $this->assertSame($file3, $result[2]);
    }

    public function testFilterByExtensionsEmpty()
    {
        $this->filesCollection->add(m::mock(SplFileInfo::class));
        $result = $this->filesCollection->filterByExtensions([]);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertEmpty($result);
    }

    public function testFilterBySize()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('isFile')->withNoArgs()->twice()->andReturn(true);
        $file2->shouldReceive('isFile')->withNoArgs()->twice()->andReturn(true);

        $file1->shouldReceive('getSize')->withNoArgs()->twice()->andReturn(16 * 1024);
        $file2->shouldReceive('getSize')->withNoArgs()->twice()->andReturn(8 * 1024);

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterBySize('>= 4K')->filterBySize('<= 10K');

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testFilterByDate()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getRealPath')->withNoArgs()->once()->andReturn($this->tempFile);
        $file2->shouldReceive('getRealPath')->withNoArgs()->once()->andReturn($this->tempFile);

        $file1->shouldReceive('getMTime')->withNoArgs()->once()->andReturn(strtotime('-5 days'));
        $file2->shouldReceive('getMTime')->withNoArgs()->once()->andReturn(strtotime('-4 hours'));

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterByDate('since yesterday');

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testFilterByClosure()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getPathname')->withNoArgs()->once()->andReturn('file.png');
        $file2->shouldReceive('getPathname')->withNoArgs()->once()->andReturn('file.php');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterByClosure(function (SplFileInfo $file) {
            return $file->getPathname() === 'file.php';
        });

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testFilterByFileList()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getPathname')->withNoArgs()->once()->andReturn('file.png');
        $file2->shouldReceive('getPathname')->withNoArgs()->twice()->andReturn('file.php');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->filterByFileList(new \ArrayIterator([$file2]));

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]);
        $this->assertSame($file2, $result[1]);
    }

    public function testGetAllPaths()
    {
        $file1 = m::mock(SplFileInfo::class);
        $file2 = m::mock(SplFileInfo::class);

        $file1->shouldReceive('getPathname')->withNoArgs()->once()->andReturn('file.php');
        $file2->shouldReceive('getPathname')->withNoArgs()->once()->andReturn('file.png');

        $this->filesCollection->add($file1);
        $this->filesCollection->add($file2);

        $result = $this->filesCollection->getAllPaths();

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertSame(['file.php', 'file.png'], $result);
    }
}
