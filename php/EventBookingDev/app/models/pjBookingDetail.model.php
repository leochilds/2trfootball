<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingDetailModel extends pjAppModel
{
	protected $table = 'bookings_details';
	
	protected $schema = array(
		array('name' => 'booking_id', 'type' => 'int', 'default' => '0'),
		array('name' => 'price_id', 'type' => 'int', 'default' => '0'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'unit_price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cnt', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjBookingDetailModel($attr);
	}
}
?>