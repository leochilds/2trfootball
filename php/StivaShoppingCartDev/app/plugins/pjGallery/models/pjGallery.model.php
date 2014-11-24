<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjGalleryApp.model.php';
class pjGalleryModel extends pjGalleryAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_gallery';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'hash', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'mime_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'small_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'small_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'small_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'small_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'medium_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'medium_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'medium_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'large_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'large_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'large_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'large_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'source_size', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'source_width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'source_height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'alt', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'watermark', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'sort', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('title');
	
	public static function factory($attr=array())
	{
		return new pjGalleryModel($attr);
	}
	
	public function pjActionSetup()
	{
		$field_arr = array(
			0 => array('plugin_gallery_alt', 'Gallery plugin / ALT'),
			1 => array('plugin_gallery_watermark_position', 'Gallery plugin / Watermark position'),
			2 => array('plugin_gallery_position', 'Gallery plugin / Position'),
			3 => array('plugin_gallery_image_settings', 'Gallery plugin / Image settings'),
			4 => array('plugin_gallery_confirmation_multi', 'Gallery plugin / Delete all confirmation'),
			5 => array('plugin_gallery_confirmation_single', 'Gallery plugin / Delete image confirmation'),
			6 => array('plugin_gallery_delete_confirmation', 'Gallery plugin / Delete confirmation'),
			7 => array('plugin_gallery_compression_note', 'Gallery plugin / Compression note'),
			8 => array('plugin_gallery_btn_delete', 'Gallery plugin / Button Delete'),
			9 => array('plugin_gallery_btn_cancel', 'Gallery plugin / Button Cancel'),
			10 => array('plugin_gallery_btn_save', 'Gallery plugin / Button Save'),
			11 => array('plugin_gallery_btn_set_watermark', 'Gallery plugin / Set watermark'),
			12 => array('plugin_gallery_btn_clear_current', 'Gallery plugin / Clear current one'),
			13 => array('plugin_gallery_btn_compress', 'Gallery plugin / Button Compress'),
			14 => array('plugin_gallery_btn_recreate', 'Gallery plugin / Button Recreate'),
			15 => array('plugin_gallery_top_left', 'Gallery plugin / Top Left'),
			16 => array('plugin_gallery_top_center', 'Gallery plugin / Top Center'),
			17 => array('plugin_gallery_bottom_left', 'Gallery plugin / Bottom Left'),
			18 => array('plugin_gallery_bottom_right', 'Gallery plugin / Bottom Right'),
			19 => array('plugin_gallery_bottom_center', 'Gallery plugin / Bottom Center'),
			20 => array('plugin_gallery_center_left', 'Gallery plugin / Center Left'),
			21 => array('plugin_gallery_center_right', 'Gallery plugin / Center Right'),
			22 => array('plugin_gallery_center_center', 'Gallery plugin / Center Center'),
			23 => array('plugin_gallery_top_right', 'Gallery plugin / Top Right'),
			24 => array('plugin_gallery_empty_result', 'Gallery plugin / Empty result set'),
			25 => array('plugin_gallery_move', 'Gallery plugin / Move'),
			26 => array('plugin_gallery_edit', 'Gallery plugin / Edit'),
			27 => array('plugin_gallery_delete', 'Gallery plugin / Delete'),
			28 => array('plugin_gallery_resize', 'Gallery plugin / Resize'),
			29 => array('plugin_gallery_rotate', 'Gallery plugin / Rotate'),
			30 => array('plugin_gallery_watermark', 'Gallery plugin / Watermark'),
			31 => array('plugin_gallery_compression', 'Gallery plugin / Compression'),
			32 => array('plugin_gallery_delete_all', 'Gallery plugin / Delete All'),
			33 => array('plugin_gallery_originals', 'Gallery plugin / Originals'),
			34 => array('plugin_gallery_thumbs', 'Gallery plugin / Thumbs'),
			35 => array('plugin_gallery_photos', 'Gallery plugin / photos'),
			36 => array('plugin_gallery_upload', 'Gallery plugin / Upload'),
			37 => array('plugin_gallery_recreate', 'Gallery plugin / Recreate from original'),
			38 => array('plugin_gallery_original', 'Gallery plugin / Original'),
			39 => array('plugin_gallery_preview', 'Gallery plugin / Preview'),
			40 => array('plugin_gallery_thumb', 'Gallery plugin / Thumb'),
			41 => array('plugin_gallery_btn_back', 'Gallery plugin / Button Back'),
			42 => array('plugin_gallery_resize_body', 'Gallery plugin / Resize Notice body'),
			43 => array('plugin_gallery_resize_title', 'Gallery plugin / Resize Notice title')
		);
		
		$multi_arr = array(
			0 => array('ALT'),
			1 => array('Watermark position'),
			2 => array('Position'),
			3 => array('Image settings'),
			4 => array('Are you sure you want to delete all images?'),
			5 => array('Are you sure you want to delete selected image?'),
			6 => array('Delete confirmation'),
			7 => array('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur id consectetur magna. Nulla facilisi. Sed id dolor ante.'),
			8 => array('Delete'),
			9 => array('Cancel'),
			10 => array('Save'),
			11 => array('Set watermark'),
			12 => array('Clear current one'),
			13 => array('Compress'),
			14 => array('Re-create thumbs'),
			15 => array('Top Left'),
			16 => array('Top Center'),
			17 => array('Bottom Left'),
			18 => array('Bottom Right'),
			19 => array('Bottom Center'),
			20 => array('Center Left'),
			21 => array('Center Right'),
			22 => array('Center Center'),
			23 => array('Top Right'),
			24 => array('No images uploaded yet.'),
			25 => array('Move'),
			26 => array('Edit'),
			27 => array('Delete'),
			28 => array('Resize/Crop'),
			29 => array('Rotate'),
			30 => array('Watermark'),
			31 => array('Compression'),
			32 => array('Delete All'),
			33 => array('Originals'),
			34 => array('Thumbs'),
			35 => array('photos'),
			36 => array('Upload'),
			37 => array('re-create from original'),
			38 => array('Original'),
			39 => array('Preview'),
			40 => array('Thumb'),
			41 => array('&laquo; Back'),
			42 => array('Move the outer parts of the rectangular and/or position it over the image to change framing, aspect ratio or accentuate an object.'),
			43 => array('Crop Image')
		);
		
		$pjFieldModel = pjFieldModel::factory();
		$pjMultiLangModel = pjMultiLangModel::factory();
		pjObject::import('Model', 'pjLocale:pjLocale');
		$locale_arr = pjLocaleModel::factory()->findAll()->getDataPair('id', 'id');
		
		foreach ($field_arr as $key => $field)
		{
			$insert_id = $pjFieldModel->reset()->setAttributes(array(
				'key' => $field[0],
				'type' => !isset($field[2]) ? 'backend' : $field[2],
				'label' => $field[1],
				'source' => 'plugin'
			))->insert()->getInsertId();
			if ($insert_id !== false && (int) $insert_id > 0)
			{
				foreach ($locale_arr as $locale)
				{
					$pjMultiLangModel->reset()->setAttributes(array(
						'foreign_id' => $insert_id,
						'model' => 'pjField',
						'locale' => $locale,
						'field' => 'title',
						'content' => $multi_arr[$key][0],
						'source' => 'plugin'
					))->insert();
				}
			}
		}
	}
}
?>