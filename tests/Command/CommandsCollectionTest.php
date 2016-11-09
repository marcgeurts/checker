<?php

namespace ClickNow\Checker\Command;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @group command
 * @covers \ClickNow\Checker\Command\CommandsCollection
 */
class CommandsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\CommandsCollection
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
