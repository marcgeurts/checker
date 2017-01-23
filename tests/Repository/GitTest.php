use Symfony\Component\Filesystem\Filesystem;
    /**
     * @var \Symfony\Component\Filesystem\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

        $this->filesystem = m::mock(Filesystem::class);
        $this->git = new Git($this->repository, $this->filesystem);
        $file2->shouldReceive('isRename')->withNoArgs()->once()->andReturn(true);
        $file3->shouldReceive('isRename')->withNoArgs()->once()->andReturn(false);
        $file2->shouldReceive('getNewName')->withNoArgs()->once()->andReturn('file2.txt');
        $file3->shouldReceive('getNewName')->withNoArgs()->never();
        $file3->shouldReceive('getName')->withNoArgs()->once()->andReturn('file3.txt');

        $file1->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file2->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file3->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(true);

        $this->filesystem->shouldReceive('exists')->with('file1.txt')->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with('file2.txt')->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with('file3.txt')->never();
        $this->assertInstanceOf(SplFileInfo::class, $result[1]);
        $this->assertSame('file2.txt', $result[1]->getPathname());
    }

    public function testGetChangedFilesNonExistingFiles()
    {
        $file = m::mock(File::class);
        $file->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file->shouldReceive('isRename')->withNoArgs()->once()->andReturn(false);
        $file->shouldReceive('getNewName')->withNoArgs()->never();
        $file->shouldReceive('getName')->withNoArgs()->once()->andReturn('file.txt');

        $this->filesystem->shouldReceive('exists')->with('file.txt')->once()->andReturn(false);

        $diff = m::mock(Diff::class);
        $diff->shouldReceive('getFiles')->withNoArgs()->once()->andReturn([$file]);

        $this->repository->shouldReceive('getWorkingCopy->getDiffStaged')->withNoArgs()->once()->andReturn($diff);
        $result = $this->git->getChangedFiles(null);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(0, $result);
        $this->filesystem->shouldReceive('exists')->with('file.txt')->once()->andReturn(true);
