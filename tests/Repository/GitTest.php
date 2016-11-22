<?php

namespace ClickNow\Checker\Repository;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Repository;
use Mockery as m;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @group repository
 * @covers \ClickNow\Checker\Repository\Git
 */
class GitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gitonomy\Git\Repository|\Mockery\MockInterface
     */
    protected $repository;

    /**
     * @var \ClickNow\Checker\Repository\Git
     */
    protected $git;

    protected function setUp()
    {
        $this->repository = m::mock(Repository::class);
        $this->git = new Git($this->repository);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testHooks()
    {
        $this->assertClassHasStaticAttribute('hooks', Git::class);
        $this->assertInternalType('array', Git::$hooks);
    }

    public function testGetRegisteredFiles()
    {
        $this->repository->shouldReceive('run')->with('ls-files')->once()->andReturn('file1.txt'.PHP_EOL.'file2.txt');
        $result = $this->git->getRegisteredFiles();

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file1.txt', $result[0]->getPathname());
        $this->assertInstanceOf(SplFileInfo::class, $result[1]);
    }

    public function testGetChangedFiles()
    {
        $file1 = m::mock(File::class);
        $file2 = m::mock(File::class);
        $file3 = m::mock(File::class);

        $file1->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file2->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(true);
        $file3->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);

        $file1->shouldReceive('isRename')->withNoArgs()->once()->andReturn(false);
        $file2->shouldReceive('isRename')->withNoArgs()->never();
        $file3->shouldReceive('isRename')->withNoArgs()->once()->andReturn(true);

        $file1->shouldReceive('getNewName')->withNoArgs()->never();
        $file2->shouldReceive('getNewName')->withNoArgs()->never();
        $file3->shouldReceive('getNewName')->withNoArgs()->once()->andReturn('file3.txt');

        $file1->shouldReceive('getName')->withNoArgs()->once()->andReturn('file1.txt');
        $file2->shouldReceive('getName')->withNoArgs()->never();
        $file3->shouldReceive('getName')->withNoArgs()->never();

        $diff = m::mock(Diff::class);
        $diff->shouldReceive('getFiles')->withNoArgs()->once()->andReturn([$file1, $file2, $file3]);

        $this->repository->shouldReceive('getWorkingCopy->getDiffStaged')->withNoArgs()->once()->andReturn($diff);
        $result = $this->git->getChangedFiles(null);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file1.txt', $result[0]->getPathname());
        $this->assertNull($result[1]);
        $this->assertInstanceOf(SplFileInfo::class, $result[2]);
        $this->assertSame('file3.txt', $result[2]->getPathname());
    }

    public function testGetChangedFilesFromRawDiff()
    {
        $rawDiff = 'diff --git a/file.txt b/file.txt
new file mode 100644
index 0000000000000000000000000000000000000000..9766475a4185a151dc9d56d614ffb9aaea3bfd42
--- /dev/null
+++ b/file.txt
@@ -0,0 +1 @@
+content
';
        $result = $this->git->getChangedFiles($rawDiff);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file.txt', $result[0]->getPathname());
    }
}
