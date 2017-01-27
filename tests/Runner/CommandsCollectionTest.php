<?php

namespace ClickNow\Checker\Runner;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @group  runner
 * @covers \ClickNow\Checker\Runner\CommandsCollection
 */
class CommandsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Runner\CommandsCollection
     */
    protected $commandsCollection;

    protected function setUp()
    {
        $this->commandsCollection = new CommandsCollection();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->commandsCollection);
    }
}
