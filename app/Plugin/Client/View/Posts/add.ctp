<h1>Add Post</h1>
<?php
echo $this->Form->create('Post');
echo $this->Form->input('title',array('type' => 'text'));
echo $this->Form->input('body', array('type' => 'text'));
echo $this->Form->end('Save Post');
?>