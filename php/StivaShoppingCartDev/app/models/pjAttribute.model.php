<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAttributeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'attributes';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'parent_id', 'type' => 'int', 'default' => ':NULL'),
		//array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'hash', 'type' => 'varchar', 'default' => ':NULL')
	);
	
	protected $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}
}
?>