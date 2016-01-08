<?php
	if(!empty($data['SaveReport'])) {
		echo '<ul>';
		foreach($data['SaveReport'] as $k => $item) {
			switch ($item['SaveReport']['type_report']) {
				case 'day_view':
					$link = $this->Html->link($item['SaveReport']['title'], array('plugin' => 'marketing', 'controller' => 'reports', 'action' => 'links', '?' => array('type_report' => $item['SaveReport']['type_report'], 'start_date' => formatDate($item['SaveReport']['start_date']), 'end_date' =>  formatDate($item['SaveReport']['end_date']) )));
					break;
				case 'day_of_week':
					$link = $this->Html->link($item['SaveReport']['title'], array('plugin' => 'marketing', 'controller' => 'reports', 'action' => 'links', '?' => array('type_report' => $item['SaveReport']['type_report'], 'day_of_week' => formatDate($item['SaveReport']['start_date']))));
					break;
				default:
					$link = $this->Html->link($item['SaveReport']['title'], array('plugin' => 'marketing', 'controller' => 'reports', 'action' => 'links', '?' => array('type_report' => $item['SaveReport']['type_report'], 'time_of_day' => formatDate($item['SaveReport']['start_date']))));
					break;
			}
			echo '<li>' . $link  . $this->Html->gridAction('trash', array('action' => 'delete', $item['SaveReport']['id']), $item, __('Are you sure?')) .'</li>';
		}
		echo '</ul>';
	}
	echo $this->Form->create(null,array('type' => 'get'));
	echo $this->Form->input('type_report', array(
		'options' => Configure::read('marketing.type_report'),
		'div'=>array('class'=>'form-group channels'),
		'class'=>'select-ui-primary',
		'id'=>'type_report',
		'default' => (isset($_GET['type_report'])) ? ($_GET['type_report']) : 'day_view',
		// 'onChange'=>'push_adv_link($(this))',
		'after'=>'<img id="loading" src="'.$this->base.'/images/loading.gif'.'" /> </div>'
	));
	echo $this->Form->inputDatepicker('start_date', array(
		'id' => 'start_date', 
		'div' => array(
			'class' => 'form-group select_date',
			'style' => ((isset($_GET['type_report']) && $_GET['type_report'] == 'day_view') || !isset($_GET['type_report'])) ? 'display:block;' : 'display: none;'
		),
		'default'=> (isset($_GET['start_date'])) ? ($_GET['start_date']) : formatDate(date('Y-m-d'))
	));
	echo $this->Form->inputDatepicker('end_date', array(
		'id' => 'end_date', 
		'div' => array(
			'class' => 'form-group select_date',
			'style' => ((isset($_GET['type_report']) && $_GET['type_report'] == 'day_view') || !isset($_GET['type_report'])) ? 'display:block;' : 'display: none;'
		),
		'default'=> (isset($_GET['end_date'])) ? ($_GET['end_date']) : formatDate(date('Y-m-d', strtotime("+7 day", time())))
	));
	echo $this->Form->inputDatepicker('day_of_week', array(
		'id' => 'day_of_week',
		'div' => array(
			'class' => 'form-group day_of_week',
			'style' => (isset($_GET['type_report']) && $_GET['type_report'] == 'day_of_week') ? 'display:block;' : 'display: none;'
		),
		'default'=> (isset($_GET['day_of_week'])) ? ($_GET['day_of_week']) : formatDate(date('Y-m-d')),
		'datepicker_setup' => array(
			'calendarWeeks' => true
		)
	));
	echo $this->Form->inputDatepicker('time_of_day', array(
		'id' => 'time_of_day',
		'div' => array(
			'class' => 'form-group time_of_day',
			'style' => (isset($_GET['type_report']) && $_GET['type_report'] == 'time_of_day') ? 'display:block;' : 'display: none;'
		),
		'default'=> (isset($_GET['time_of_day'])) ? ($_GET['time_of_day']) : formatDate(date('Y-m-d'))
	));
	echo $this->Form->submit('Filter');
	
?>

<script type="text/javascript">
<?php if(!empty($data['category'])): ?>
$(function () {
	$('#container').highcharts({	
		title: {
			text: '<?php echo $data['title']; ?>',
			x: -20 //center
		},
		subtitle: {
			text: '',
			x: -20
		},
		xAxis: {
			categories: [<?php echo $data['category'] ?>]
		},
		yAxis: {
			title: {
				text: 'No of visits'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			valueSuffix: 'click'
		},
		legend: {
			layout: 'vertical',
			align: 'center',
			verticalAlign: 'middle',
			borderWidth: 0
		},
		series: [<?php echo $data['series'] ?>],
		chart: {
			renderTo: 'container',
			events: {
				load: function(event) {
					var content_width = parseInt($('#container').width() - 50);
					console.log(content_width);
					var count_series = parseInt(<?php echo $data['count_series'];?>);
					one_column = content_width/count_series;
					extra = 0.5 * one_column;
					// Draw the flow chart
					var ren = this.renderer,
						colors = Highcharts.getOptions().colors,
						rightArrow = ['M', 0, 0, 'L', 100, 0, 'L', 95, 5, 'M', 100, 0, 'L', 95, -5],
						leftArrow = ['M', 100, 0, 'L', 0, 0, 'L', 5, 5, 'M', 0, 0, 'L', 5, -5];
					
					<?php if(count($data['events']) > 0){
						$data['curr_year'] = 2014;
						foreach($data['events'] as $k => $item) {
							if(strtotime($item['Event']['start_date']) >= $data['start_date']) {
								$start_month = 1 +  (strtotime($item['Event']['start_date']) - $data['start_date'])/86400;
							}
							else {
								$start_month = 1;
							}
							if($data['end_date'] >= strtotime($item['Event']['end_date'])) {
								$finish_month = $data['count_series'] - ($data['end_date'] - strtotime($item['Event']['end_date']))/86400;
							}
							else {
								$finish_month = $data['count_series'];
							}
							if($start_month == $finish_month) {
								// $start_month = $start_month - 0.3;
								// $finish_month = $finish_month + 0.3;
							}
					?>
						/*render 2 node*/
						ren.circle(55 + (<?php echo  $finish_month; ?> * one_column - extra), <?php echo  (35 + (int)($k*2 .'0')); ?>, 4).attr({
							fill: colors[<?php echo  $k; ?>],
							stroke: colors[<?php echo  $k; ?>],
							'stroke-width': 1
						}).add();
						ren.circle(55 + (<?php echo  $start_month; ?> * one_column - extra), <?php echo  (35 + (int)($k*2 .'0')); ?>, 4).attr({
							fill: colors[<?php echo  $k; ?>],
							stroke: colors[<?php echo  $k; ?>],
							'stroke-width': 1
						}).add();
						
						// render line
						ren.path(['M', (<?php echo  $start_month; ?> * one_column - extra), 0, 'L', (<?php echo  $finish_month; ?> * one_column - extra), 0])
						.attr({
							'stroke-width': 5
							, stroke: colors[<?php echo  $k; ?>],
							zIndex: 9
							// , dashstyle: 'dash'
						}).translate(55, <?php echo  (35 + (int)($k*2 .'0')); ?>)
						.on('mouseover', function() {//mouseover show box
							ren.label('<?php echo $item['Event']['name'].'<br />'; if(date('Y',strtotime($item['Event']['start_date'])) != $data['curr_year']){ echo 'Older';} else { echo date('j M Y',strtotime($item['Event']['start_date']));} echo ' - '; if(date('Y',strtotime($item['Event']['end_date'])) != $data['curr_year']){ echo 'Continue';} else { echo date('j M Y',strtotime($item['Event']['end_date']));}?>', (<?php echo  ((int)$start_month + 0.5); ?> * one_column - extra), <?php echo  ((int)($k*2 .'0') + 45); ?>)
								.attr({
									fill: 'white',
									stroke: colors[<?php echo  $k; ?>],
									'stroke-width': 2,
									padding: 5,
									r: 5,
									id: 'box-event-<?php echo $item['Event']['id'];?>',
									zIndex: 10
								})
								.css({
									color: colors[<?php echo  $k; ?>],
									//'text-anchor': 'middle',
									fontWeight: 'bold'
								})
								.add()
								.shadow(true);
						})
						.on('mouseout', function() {
							$("#box-event-<?php echo $item['Event']['id'];?>").remove();
						})
						.add();
					<?php
						}
					}
					?>
				}
			}
		}
	});
	//event change type_report
	
});
<?php else: ?>
$(document).ready(function() {
	$('#container').html('<h2><?php echo __("No data");?></h2>');
});
<?php endif; ?>
$(document).ready(function() {
	$('select#type_report').change(function() {
		console.log($(this).find('option:selected').val());
		if( $(this).find('option:selected').val() == 'day_view') {
			$('.time_of_day').css('display', 'none');
			$('.day_of_week').css('display', 'none');
			$('.select_date').css('display', 'block');
		}
		else if( $(this).find('option:selected').val() == 'day_of_week') {
			
			$('.day_of_week').css('display', 'block');
			$('.select_date').css('display', 'none');
			$('.time_of_day').css('display', 'none');
			// $('.day_of_week').show();
		}
		else {
			$('.select_date').css('display', 'none');
			$('.day_of_week').css('display', 'none');
			$('.time_of_day').css('display', 'block');
		}
	});
});
</script>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<br />
<br />
<?php 
if(!empty($data['category'])) {
	echo  $this->Form->input('title');
	echo $this->Form->submit('Save', array(
		'class' => 'btn btn-success',
		'name' => 'save_filter'
	));
	// echo '<input type="submit" class="btn btn-success" name="save_filter" value="'. __('Save') .'" />';
}
echo $this->Form->end(); 
?>