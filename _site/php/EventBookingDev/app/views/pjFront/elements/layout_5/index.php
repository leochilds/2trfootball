<div id="phpevtcal_container">
	<?php
	if($show_header == 1)
	{ 
		?>
		<div id="phpevtcal_header">
			<?php
			if($show_cats == 1)
			{ 
				?>
				<select id="phpevtcal_category" class="phpevtcal-select">
					<option value="0">-- <?php __('front_label_choose');?> --</option>
					<?php
					foreach($tpl['category_arr'] as $v)
					{
						?><option value="<?php echo $v['id']?>"><?php echo $v['category']?></option><?php
					} 
					?>
				</select>
				<?php
			} 
			if($show_icons == 1)
			{
				?>
				<div id="phpevtcal_menu">
					<?php
					if($tpl['option_arr']['o_enable_monthly_view'] == 'Yes')
					{ 
						?><a class="phpevtcal-view-mode phpevtcal-monthly" href="javascript:void(0);" rev="monthly"></a><?php
					} 
					?>
					<a class="phpevtcal-view-mode phpevtcal-calendar" href="javascript:void(0);" rev="calendar"></a>
					<?php
					if($tpl['option_arr']['o_enable_list_view'] == 'Yes')
					{ 
						?><a class="phpevtcal-view-mode phpevtcal-list" href="javascript:void(0);" rev="list"></a><?php
					} 
					?>
				</div>
				<?php
			} 
			?>
		</div>
		<?php
	} 
	?>
	<div class="phpevtcal-round-container">
		<div class="phpevtcal-round-container-top">
			<div class="left"></div>
			<div class="right"></div>
			<div class="middle">&nbsp;</div>
		</div>
		<div id="phpevtcal_content"></div>
		<div class="phpevtcal-round-container-bottom">
			<div class="left"></div>
			<div class="right"></div>
			<div class="middle">&nbsp;</div>
		</div>
	</div>
</div>