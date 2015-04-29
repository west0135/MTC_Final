<?php

require_once "general/Collections.class.php";

class AtaProgramCollection extends Collections
{
	public function __construct()
	{
	  parent::__construct(BaseAta_program_category, BaseAta_program);
	}
	
	public function getList($postArray=NULL)
	{
	  return $this->selectItems();
	}
	
	public function getCategorySchema()
	{
		return $this->category_schema_array;
	}
	
	public function getItemSchema()
	{
		return $this->items_schema_array;
	}
	
	public function getCategoryPrimaryKeyName()
	{
		return $this->category_primary_key_name;
	}
	
	public function getItemPrimaryKeyName()
	{
		return $this->item_primary_key_name;
	}
}
