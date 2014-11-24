<?php
switch ($_GET['layout']) {
	case 'layout_1':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/bookingsummary.php';
		break;
	case 'layout_2':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_2/bookingsummary.php';
		break;
	case 'layout_3':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_3/bookingsummary.php';
		break;
	case 'layout_4':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_4/bookingsummary.php';
		break;
	case 'layout_5':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_5/bookingsummary.php';
		break;
	default:
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/bookingsummary.php';
		break;
}
?>