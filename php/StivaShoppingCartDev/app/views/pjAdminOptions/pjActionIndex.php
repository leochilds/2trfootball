<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	include_once dirname(__FILE__) . '/elements/submenu.php';
	
	switch (@$_GET['tab'])
	{
		case 6:
			pjUtil::printNotice(@$titles['AO26'], @$bodies['AO26']);
			include dirname(__FILE__) . '/elements/terms.php';
			break;
		case 5:
			pjUtil::printNotice(@$titles['AO25'], @$bodies['AO25'], false);
			include dirname(__FILE__) . '/elements/confirmation.php';
			break;
		case 4:
			pjUtil::printNotice(@$titles['AO24'], @$bodies['AO24']);
			include dirname(__FILE__) . '/elements/taxes.php';
			break;
		case 3:
			pjUtil::printNotice(@$titles['AO23'], @$bodies['AO23']);
			include dirname(__FILE__) . '/elements/tab.php';
			break;
		case 2:
			pjUtil::printNotice(@$titles['AO22'], @$bodies['AO22']);
			include dirname(__FILE__) . '/elements/tab.php';
			break;
		case 1:
			pjUtil::printNotice(@$titles['AO21'], @$bodies['AO21']);
			include dirname(__FILE__) . '/elements/tab.php';
			break;
		default:
			include dirname(__FILE__) . '/elements/tab.php';
	}
}
?>