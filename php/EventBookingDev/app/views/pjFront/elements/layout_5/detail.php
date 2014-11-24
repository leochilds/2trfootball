<?php
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
			<a class="phpevtcal-detail-close" href="javascript:void(0);" rev="<?php echo $v['id']?>"></a>
		</div>
		<label class="phpevtcal-event-title"><?php echo stripslashes($event_title)?></label>
		<div class="phpevtcal-detail-cate"><?php echo stripslashes($v['category'])?></div>
		<div class="phpevtcal-detail-desc"><?php echo stripslashes($v['description']);?></div>
	</div>
	<?php
} 
?>