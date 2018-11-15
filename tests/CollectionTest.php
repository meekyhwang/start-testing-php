<?php
use \PHPUnit\Framework\TestCase;

class CollectionTest extends \PHPUnit\Framework\TestCase {
	//remove
	/**
	 * @var Collection
	 */
	private $emptyCollection;
	/**
	 * @var Collection
	 */
	private $oneCollection;
	/**
	 * @var Collection
	 */
	private $manyCollection;
	

	public function setUp(){
		$this->emptyCollection = new Collection();
		
		$this->oneCollection = new Collection();
		$this->oneCollection->add('something');

		$this->manyCollection = new Collection();
		$this->manyCollection->add(1);
		$this->manyCollection->add('two');
		$this->manyCollection->add("3.0");
	}

	public function testIsEmpty(){
		$this->assertTrue( $this->emptyCollection->isEmpty());
		$this->assertFalse( $this->oneCollection->isEmpty());
	}
	public function testSize(){
		$this->assertSame(0, $this->emptyCollection->size());
		$this->assertSame(1, $this->oneCollection->size());
		$this->assertSame(3, $this->manyCollection->size());		
	}

	public function testContains(){
		$this->assertFalse( $this->emptyCollection->contains('anything'));
		$this->assertTrue( $this->oneCollection->contains('something'));
		$this->assertTrue( $this->manyCollection->contains('two'));
		$this->assertFalse( $this->manyCollection->contains('1'));
	}

	public function testRemove(){
		$collection = new Collection();

		$collection->add(1);
		$collection->add('two');
		$this->assertSame( 2, $collection->size());
		
		$collection->remove('1');
		$this->assertSame( 2, $collection->size());
		$this->assertTrue($collection->contains('two'));
		$this->assertTrue($collection->contains(1));

		$collection->remove(1);
		$this->assertSame( 1, $collection->size());
		$this->assertTrue($collection->contains('two'));
		$this->assertFalse($collection->contains(1));
	}
}