<?php

class Collection{
	private $size = 0;
	private $items =[];
	public function __construct(){}

	public function isEmpty(){	
		return $this->size() === 0;
	}
	public function add($item){
		$this->items[] = $item;
	}
	public function remove($item){
		$index = array_search($item, $this->items, true);
		if($index !== false){
			unset($this->items[$index]);
		}
	}
	public function size(){
		return count($this->items);
	}
	public function contains(string $string){
		return in_array($string, $this->items, true);
	}
}

