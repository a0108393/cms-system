<?php

?>
<a class="btn" href="<?php echo Router::url(array('controller' => 'Client', 'action' => 'add'), true); ?>">Add</a>
<a class="btn" href="<?php echo Router::url(array('controller' => 'Client', 'action' => 'edit'), true); ?>">Edit</a>
<a class="btn" href="<?php echo Router::url(array('controller' => 'Client', 'action' => 'delete'), true); ?>">Delete</a>
<a class="btn" href="<?php echo $this->webroot; ?>">Home</a>