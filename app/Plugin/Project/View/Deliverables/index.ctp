<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
</head>



<body>



<div class="container">
  <h2>Deliverable Dashboard</h2>
  <ul class="nav nav-tabs" >
    <li class="active"><a data-toggle="tab" href="#home">Pending Tasks</a></li>
    <li><a data-toggle="tab" href="#menu1">Details</a></li>

  </ul>

  <div class="tab-content">
  
    <div id="home" class="tab-pane fade in active">

      <div class="top-btn-group">

<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<?php

	echo '<table class="table table-striped" border="1">';
	
		$tableHeaders = $this->Html->tableHeaders(array(
		__('ID.'),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('assignment', __('Assignment')),
		$this->Paginator->sort('date', __('Date')),
	    $this->Paginator->sort('deadline', __('Deadline')),
		$this->Paginator->sort('project_name', __('Project Name')),
		$this->Paginator->sort('deliverable_name', __('Deliverable Name'))
	));
	
	
	echo '<thead>';

	echo $tableHeaders;
	echo '</thead>';

	echo '</table>';


?>

<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>
    </div>
	
	
	
	
	
	
	
	
	
    </div>
	
    <div id="menu1" class="tab-pane fade">
    <div id="home" class="tab-pane fade in active">

      <div class="top-btn-group">

<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<?php
    echo '<button type="button" style="float: right;">View Project Description</button>';
    echo '<table>';
    echo '<h4>project brief info here</h4>';
	echo '<button type="button">Add Deliverables</button>';
	echo '<table class="table table-striped" border="1">';
	
		
	echo '<thead>';

	echo '<h4>deliverable info</h4>';
	echo '</thead>';

	echo '</table>';


?>


    </div>
	
	
	
	
	
	
	
	
	
    </div>
	
 
	
  </div>
</div>

<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/56a89d80e251f00d7430ddd0/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->



</body>