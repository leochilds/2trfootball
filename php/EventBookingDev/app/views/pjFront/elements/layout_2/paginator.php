<div class="ebcal-paging-container">
	<?php
	if (isset($tpl['paginator']))
	{		
		$page = 1 ;
		
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}
		?>
		<ul class="ebcal-paginator">
			<?php
			$stages = 3;
			$lastpage = $tpl['paginator']['pages'];
								
			if ($page > 1)
			{
				?><li><a class="ebcal-prev ebcal-page-clickable" href="#" rev="<?php echo $page - 1; ?>">&lsaquo;</a></li><?php
			}
			?><?php 
			if ($lastpage < 7 + ($stages * 2))
			{
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
					{
						?><li><a href="javascript:void(0);" class="current"><?php echo $counter; ?></a></li><?php
					}else{
						?><li><a class="ebcal-page-clickable" href="#" rev="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
					}
				}
			} else if ($lastpage > 5 + ($stages * 2)){
				if($page < 1 + ($stages * 2))		
				{
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
					{
						if ($counter == $page){
							?><li><a href="javascript:void(0);" class="current"><?php echo $counter; ?></a></li><?php
						}else{
							?><li><a class="ebcal-page-clickable" href="#" rev="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
						}	
					}
					?>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li><a href="#" class="ebcal-page-clickable" rev="<?php echo $lastpage - 1; ?>"><?php echo $lastpage - 1; ?></a></li>
					<li><a href="#" class="ebcal-page-clickable" rev="<?php echo $lastpage; ?>"><?php echo $lastpage; ?></a></li>
					<?php
				}else if($lastpage - ($stages * 2) > $page && $page > ($stages * 2)){
					?>
					<li><a href="#" class="ebcal-page-clickable" rev="1">1</a></li>
					<li><a href="#" class="ebcal-page-clickable" rev="2">2</a></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<?php
					for ($counter = $page - $stages; $counter <= $page + $stages; $counter++){
						if ($counter == $page)
						{
							?><li><a href="javascript:void(0);" class="current"><?php echo $counter; ?></a></li><?php
						}else{
							?><li><a class="ebcal-page-clickable" href="#" rev="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
						}
					}
					?>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li><a href="#" class="ebcal-page-clickable" rev="<?php echo $lastpage - 1; ?>"><?php echo $lastpage - 1; ?></a></li>
					<li><a href="#" class="ebcal-page-clickable" rev="<?php echo $lastpage; ?>"><?php echo $lastpage; ?></a></li>
					<?php
				}else{
					?>
					<li><a href="#" class="ebcal-page-clickable" rev="1">1</a></li>
					<li><a href="#" class="ebcal-page-clickable" rev="2">2</a></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<li class="dot"><span>.</span></li>
					<?php
					for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
						{
							?><li><a href="javascript:void(0);" class="current"><?php echo $counter; ?></a></li><?php
						}else{
							?><li><a class="ebcal-page-clickable" href="#" rev="<?php echo $counter; ?>"><?php echo $counter; ?></a></li><?php
						}
					}
				}	
			}
			if ($page < $counter - 1){
				 ?><li><a class="ebcal-next ebcal-page-clickable" href="#" rev="<?php echo $page + 1; ?>">&rsaquo;</a></li><?php
			}
			?>
		</ul>
		<?php
	} 
	?>
</div>