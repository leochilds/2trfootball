<?php
if($_GET['view_mode'] == 'monthly'){
	include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_3/modes/monthly.php';
}else if($_GET['view_mode'] == 'calendar'){
	include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_3/modes/calendar.php';
}else if($_GET['view_mode'] == 'list'){
	include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_3/modes/list.php';
}
?>