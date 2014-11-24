<!DOCTYPE html>
<html>
	<head>
		<title>Event Booking Calendar Preview</title>
	<head>
	<body>
		<div style="padding-left: 60px;overflow: hidden;">
		<link href="<?php echo PJ_INSTALL_URL; ?>/index.php?controller=pjFront&action=pjActionLoadCss<?php echo !empty($_GET['cssfile']) ? '&cssfile=' . $_GET['cssfile'] : null; ?>" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>/index.php?controller=pjFront&action=pjActionLoadJs"></script>
		<script type="text/javascript" src="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionLoad&layout=<?php echo $_GET['layout'];?>&view=<?php echo $_GET['view']?>&icons=<?php echo $_GET['icons']?>&cats=<?php echo $_GET['cats']?>&cid=<?php echo $_GET['cid']?>"></script>
		</div>
	</body>
</html>