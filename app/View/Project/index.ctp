<?php

?>
<a class="btn" href="<?php echo Router::url(array('controller' => 'Project', 'action' => 'add'), true); ?>">Add</a>
<a class="btn" href="<?php echo Router::url(array('controller' => 'Project', 'action' => 'edit'), true); ?>">Edit</a>
<a class="btn" href="<?php echo Router::url(array('controller' => 'Project', 'action' => 'delete'), true); ?>">Delete</a>