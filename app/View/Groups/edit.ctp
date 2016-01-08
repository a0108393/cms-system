<?php 

?>
<div class="group form">
<?php 	
	echo $this->Form->create('Group'); 
	echo $this->Form->input('name');
	echo $this->Form->submit('Submit', array(
		'class' => 'btn btn-primary',
		'div' =>  'form-group',
		'before' => '<div class="col-lg-offset-2 col-lg-10">',
		'after' => $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default')) . '</div>',
	));
	echo $this->Form->end(); 
?>
</div>