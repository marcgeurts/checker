<?php

namespace ClickNow\Checker\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * @group result
 * @covers \ClickNow\Checker\Result\ResultsCollection
 */
class ResultsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Result\ResultsCollection
     */
    protected $resultsCollection;

    protected function setUp()
    {
        $this->resultsCollection = new ResultsCollection();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->resultsCollection);
    }

    public function testIsSuccessfully()
    {

    }

    public function testIsFailed()
    {

    }

    public function testFilterBySkipped()
    {

    }

    public function testFilterBySuccess()
    {

    }

    public function testFilterByWarning()
    {

    }

    public function testFilterByError()
    {

    }

    public function testGetAllMessages()
    {

    }
}
