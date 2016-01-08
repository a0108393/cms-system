<?php
	$option = array();
	for($i = date("Y"); $i > (date("Y") - 20); $i--){
		$option[$i] = $i;
	}
	echo $this->Form->create(null,array('type' => 'get'));
	echo $this->Form->input('month_from', array(
		'options' => Configure::read('marketing.list_month'),
		'class'=>'select-ui-primary',
		'default'=> (isset($_GET['month_from'])) ? $_GET['month_from'] : 1
	));
	echo $this->Form->input('month_to', array(
		'options' => Configure::read('marketing.list_month'),
		'class'=>'select-ui-primary',
		'default'=> (isset($_GET['month_to'])) ? $_GET['month_to'] : 12
	));
	echo $this->Form->input('select_year', array(
		'options' => $option,
		'class'=>'select-ui-primary',
		'default'=> (isset($_GET['select_year'])) ? $_GET['select_year'] : date("Y",time())
	));
	echo $this->Form->end('Filter');
?>
<script type="text/javascript">
$(function () {
        $('#container').highcharts({
            title: {
                text: 'Link Report Chart - Month view',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: [<?php echo $data['category'];?>]
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
            series: [<?php echo $data['series'];?>],
			chart: {
				renderTo: 'container',
				events: {
					load: function(event) {
						var content_width = parseInt($('#container').width() - 67);
						var count_series = parseInt(<?php echo $data['count_series'];?>);
						one_column = content_width/count_series;
						extra = 0.5 * one_column;
						// Draw the flow chart
						var ren = this.renderer,
							colors = Highcharts.getOptions().colors,
							rightArrow = ['M', 0, 0, 'L', 100, 0, 'L', 95, 5, 'M', 100, 0, 'L', 95, -5],
							leftArrow = ['M', 100, 0, 'L', 0, 0, 'L', 5, 5, 'M', 0, 0, 'L', 5, -5];
						
						<?php if(count($data['events']) > 0){
							foreach($data['events'] as $k => $item) {
								$start_month = date("m",strtotime($item['Event']['start_date']));
								$finish_month = date("m",strtotime($item['Event']['end_date']));
								if(date("Y",strtotime($item['Event']['start_date'])) != $data['curr_year']){
									$start_month = 1;
								}
								if(date("Y",strtotime($item['Event']['end_date'])) != $data['curr_year']){
									$finish_month = 12;
								}
								if($start_month == $finish_month) {
									$start_month = $start_month - 0.3;
									$finish_month = $finish_month + 0.3;
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
    });
    

		</script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>