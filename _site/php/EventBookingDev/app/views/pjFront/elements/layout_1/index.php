<div id="phpevtcal_container_<?php echo $index; ?>" class="phpevtcal-container">
	<?php
	if($show_header == 1)
	{ 
		?>
		<div id="phpevtcal_header_<?php echo $index; ?>" class="phpevtcal-header">
			<?php
			if($show_cats == 1)
			{ 
				?>
				<select id="phpevtcal_category_<?php echo $index; ?>" class="phpevtcal-select ebc-float-left">
					<option value="0">-- <?php __('front_label_choose');?> --</option>
					<?php
					foreach($tpl['category_arr'] as $v)
					{
						if(isset($_GET['cid']))
						{
							if($_GET['cid'] > 0 && $v['id'] == $_GET['cid'])
							{
								?><option value="<?php echo $v['id']?>" selected="selected"><?php echo $v['category']?></option><?php
							}else{
								?><option value="<?php echo $v['id']?>"><?php echo $v['category']?></option><?php
							}
						}else{
							?><option value="<?php echo $v['id']?>"><?php echo $v['category'];?></option><?php
						}
					} 
					?>
				</select>
				<?php
			} 
			if($show_icons == 1)
			{
				?>
				<div id="phpevtcal_menu_<?php echo $index; ?>" class="phpevtcal-menu">
					<?php
					if($tpl['option_arr']['o_enable_monthly_view'] == 'Yes')
					{ 
						?><a class="phpevtcal-view-mode phpevtcal-monthly" href="javascript:void(0);" rev="monthly" title="<?php __('front_label_monthly_view');?>"></a><?php
					} 
					?>
					<a class="phpevtcal-view-mode phpevtcal-calendar" href="javascript:void(0);" rev="calendar" title="<?php __('front_label_calendar_view');?>"></a>
					<?php
					if($tpl['option_arr']['o_enable_list_view'] == 'Yes')
					{ 
						?><a class="phpevtcal-view-mode phpevtcal-list" href="javascript:void(0);" rev="list" title="<?php __('front_label_list_view');?>"></a><?php
					} 
					?>
				</div>
				<?php
			} 
			?>
		</div>
		<br/>
		<?php
	} 
	?>
	<div id="phpevtcal_content_<?php echo $index; ?>"></div>
</div>