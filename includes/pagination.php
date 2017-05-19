<?php

//This is a helper class to make paginating records easy.

class Pagination extends DatabaseObject {
		
	// These are the three inital values you will need to use the pagination functionality		
	public $current_page;
	public $per_page;
	public $total_count;
	
	public function __construct($page=1, $per_page=20, $total_count=0){
		$this->current_page = (int)$page;
		$this->per_page = (int)$per_page;
		$this->total_count = (int)$total_count;
	}
	
	public function offset() {
		//Assuming 20 items per page:
		//Page 1 has an offset of 0 (1-1) * 20
		//Page 2 has an offset of 20 (2-1) * 20
		// In other words, page 2 starts with item 21
		// offset = $per_page * ($current_page - 1)
		return ($this->current_page -1) * $this->per_page;
	}
	
	public function low_offset() {
		//Assuming 20 items per page:
		//Page 1 has an offset of 0 (1-1) * 20
		//Page 2 has an offset of 20 (2-1) * 20
		// In other words, page 2 starts with item 21
		
		$low_offset = $this->offset()+1;
		return $low_offset;
	}
	
	public function high_offset() {
		//Assuming 20 items per page:
		//Page 1 has an offset of 0 (1-1) * 20
		//Page 2 has an offset of 20 (2-1) * 20
		// In other words, page 2 starts with item 21
		
		$low_offset = $this->offset() + $this->per_page;
		return $low_offset;
	}
	
	public function total_pages() {
		return ceil($this->total_count/$this->per_page);
	}
	
	public function previous_page(){
		$previous_page .= $this->current_page - 1;
		
		return $previous_page;
	}

	public function next_page(){
		$next_page .= $this->current_page + 1;
		
		return $next_page;
	}
	
	public function has_previous_page(){
		$prev_page = $this->current_page - 1;	
		return $prev_page >= 1 ? true:false;
	}
	
	public function has_next_page(){
		$nxt_page = $this->current_page + 1;	
		return $nxt_page <= $this->total_pages() ? true : false;
	}
	
	public function last_item($page_items_count){
		if($page_items_count == $this->high_offset()){
			return $this->high_offset();
		}  elseif($page_items_count < $this->high_offset()){
			return $page_items_count + $this->offset();
		}
	}
}

?>