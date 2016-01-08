<ul>
<?php 
if( count( $affiliate ) > 0 ){
	foreach ($affiliate as $key => $value){ ?>
		<li value="<?php echo $key; ?>"><?php echo str_replace($keyup,'<b style="color:red;">'.$keyup.'</b>',$value); ?></li>
<?php }} ?>
</ul>