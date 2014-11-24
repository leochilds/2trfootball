<?php
if(!empty($tpl['event_arr']))
{
	foreach($tpl['event_arr'] as $v){
		$event_title = '';
		if(!empty($v['start_time'])){
			$event_time = pjUtil::formatTime($v['start_time'], 'H:i:s', $tpl['option_arr']['o_time_format']);
			$event_title = $event_time . ', ' . $v['event_title'];
		}else{
			$event_title = $v['event_title'];
		}
		?>
		<div id="phpevtcal_event_box_<?php echo $v['id']?>" class="phpevtcal-event-box">
			<div class="phpevtcal-detail-heading">
				<label class="phpevtcal-detail-date"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($v['event_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']);?></label>
			</div>
			<div class="phpevtcal-detail-content">
				<label class="phpevtcal-event-title"><?php echo stripslashes($event_title)?></label>
				<div class="phpevtcal-detail-cate"><?php echo stripslashes($v['category'])?></div>
				<div class="phpevtcal-detail-desc"><?php echo stripslashes($v['description']);?></div>
			</div>
		</div>
		<?php
	} 
	$page = isset($_GET['page']) ? $_GET['page'] : 1 ;
	$pages = $tpl['pages'];
	?>
	<div id="phpevtcal_pagination">
		<?php
		if($page < $pages)
		{
			?><a href="javascript:void(0);" class="phpevtcal-paging phpevtcal-paging-next" rev="<?php echo $page + 1;?>"></a><?php
		}else{
			?><a href="javascript:void(0);" class="phpevtcal-paging-next"></a><?php
		}
		if($page > 1)
		{
			?><a href="javascript:void(0);" class="phpevtcal-paging phpevtcal-paging-prev" rev="<?php echo $page - 1;?>"></a><?php
		}else{
			?><a href="javascript:void(0);" class="phpevtcal-paging-prev"></a><?php
		}
		?>
	</div>
	<?php
}else{
	
}
?>