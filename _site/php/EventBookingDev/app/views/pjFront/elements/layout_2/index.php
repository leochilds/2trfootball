<div id="phpevtcal_container_<?php echo $index;?>" class="ebcal-container">
	<?php
	if($show_header == 1)
	{ 
		?>
		<div id="phpevtcal_header_<?php echo $index;?>" class="ebcal-category">
			<?php
			if($show_cats == 1)
			{ 
				?>
				<select id="phpevtcal_category_<?php echo $index;?>" class="ebcal-field">
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
			?>
		</div>
		<?php
	} 
	?>
	<div id="phpevtcal_content_<?php echo $index;?>"></div>
</div>