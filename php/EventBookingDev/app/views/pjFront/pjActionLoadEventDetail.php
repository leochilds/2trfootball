<?php
switch ($_GET['layout']) {
	case 'layout_1':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/detail.php';
		break;
	case 'layout_2':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_2/detail.php';
		break;
	case 'layout_3':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_3/detail.php';
		break;
	case 'layout_4':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_4/detail.php';
		break;
	case 'layout_5':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_5/detail.php';
		break;
	default:
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/detail.php';
		break;
}
?>