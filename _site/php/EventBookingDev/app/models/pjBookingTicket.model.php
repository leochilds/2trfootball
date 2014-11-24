<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingTicketModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings_tickets';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'int', 'default' => '0'),
		array('name' => 'ticket_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'unit_price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'is_used', 'type' => 'enum', 'default' => 'F')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjBookingTicketModel($attr);
	}
}
?>