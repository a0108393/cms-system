<h1>
<?php 
    switch ($this->action) {
        case 'add':
            echo __('Add Channel');
            break;
        case 'edit':
            echo __('Edit Channel');
            break;
        default:
            echo __('View Channel');
            break;
    }
?>
</h1>
<?php
    echo $this->Form->create('Channel');
    if($this->action != 'view') {
	    echo $this->Form->input('name');
	    echo $this->Form->input('id', array('type' => 'hidden'));
    	echo $this->Form->end('Save Channel');
    }
    else {
    	echo $this->Form->input('name', array('disabled' => true));
    	echo $this->Form->end();
    }
?>