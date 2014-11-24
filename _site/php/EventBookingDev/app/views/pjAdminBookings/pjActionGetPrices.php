<?php
if(count($tpl['price_arr']) > 0)
{
	foreach($tpl['price_arr'] as $v)
	{
		?>
		<p>
			<label class="title80"><?php echo stripslashes($v['title']);?></label>
			<select name="price_<?php echo $v['id']; ?>" lang="<?php echo $v['price'];?>" class="pj-price pj-form-field w60">
				<?php
				$max = intval($v['available']) - intval($v['cnt_booked']);
				$max = (int) $max < 1 ? 0 : $max;
				foreach (range(0, $max) as $i)
				{
					?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
				}
				?>
			</select>
			&nbsp;x&nbsp;<?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?>
		</p>
		<?php
	}
}
?>
<span class="block overflow">
	<input type="hidden" id="customer_people" name="customer_people" class="required" value="" />
</span>
