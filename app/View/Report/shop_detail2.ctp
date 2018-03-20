<?php
## 集計用JS
$this->Html->css('plugins/daterangepicker/daterangepicker.css', array('inline' => false));
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js', array('inline' => false));
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/locale/ja.js', array('inline' => false));
$this->Html->script('plugins/daterangepicker/daterangepicker.js', array('inline' => false));

$js = <<<EOF
$(function () {
	//Date range picker
	$('#reservation').daterangepicker({
		showDropdowns: false,
		opens: 'left',
		locale: {
			format: 'YYYY/MM/DD',
			applyLabel: '選択',
			cancelLabel: 'クリア',
			fromLabel: '開始日',
			toLabel: '終了日',
			weekLabel: 'W',
			customRangeLabel: '自分で指定',
			daysOfWeek: moment.weekdaysMin(),
			monthNames: moment.monthsShort(),
			firstDay: moment.localeData()._week.dow
		}
	});
});


EOF;


## グラフ用JS
$this->Html->script('plugins/chartjs/Chart.min.js', array('inline' => false));
$val = array();

# チームサンクス数(折れ線グラフ)
$labels = array();
$values = array();
$sum = 0;
foreach ($data[0] as $datetime => $nums) {
	$labels[] = '"'.date("n/j", $datetime).'"';
	$values[] = $nums;
	$sum += $nums;
}
$val[0] = array(
	'labels' => implode(',', $labels),
	'data' => implode(',', $values)
);

$js .= <<<EOF
$(function () {
	//--------------
	//- AREA CHART -
	//--------------

	// Get context with jQuery - using jQuery's .get() method.
	var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
	// This will get the first returned node in the jQuery collection.
	var areaChart = new Chart(areaChartCanvas);

	var areaChartData = {
		labels: [{$val[0]["labels"]}],
		datasets: [
			{
				label: "サンクス数",
				fillColor: "rgba(151,187,205,0.2)",
				strokeColor: "#00a65a",
				pointColor: "#3b8bba",
				pointStrokeColor: "rgba(60,141,188,1)",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(60,141,188,1)",
				data: [{$val[0]["data"]}]
			}
		]
	};

	var areaChartOptions = {
		//Boolean - If we should show the scale at all
		showScale: true,
		//Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines: false,
		//String - Colour of the grid lines
		scaleGridLineColor: "rgba(0,0,0,.05)",
		//Number - Width of the grid lines
		scaleGridLineWidth: 1,
		//Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		//Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines: true,
		//Boolean - Whether the line is curved between points
		bezierCurve: true,
		//Number - Tension of the bezier curve between points
		bezierCurveTension: 0.3,
		//Boolean - Whether to show a dot for each point
		pointDot: false,
		//Number - Radius of each point dot in pixels
		pointDotRadius: 4,
		//Number - Pixel width of point dot stroke
		pointDotStrokeWidth: 1,
		//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius: 20,
		//Boolean - Whether to show a stroke for datasets
		datasetStroke: true,
		//Number - Pixel width of dataset stroke
		datasetStrokeWidth: 2,
		//Boolean - Whether to fill the dataset with a color
		datasetFill: true,
		//String - A legend template
		legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
		//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio: true,
		//Boolean - whether to make the chart responsive to window resizing
		responsive: true
	};

	//Create the line chart
	//areaChart.Line(areaChartData, areaChartOptions);
	areaChart = new Chart(areaChartCanvas).Line(areaChartData,areaChartOptions); 

	//--------------
	//- Radar CHART -
	//--------------

	// Get context with jQuery - using jQuery's .get() method.
	var radarChartCanvas = $("#radarChart").get(0).getContext("2d");
	radarChartCanvas.canvas.width = 400;
	radarChartCanvas.canvas.height = 300;
	// This will get the first returned node in the jQuery collection.

	var radarChartData = {
		labels: ["平均thanks!頻度", "送信アクティブ率", "起動アクティブ率"],
		datasets: [
			{
			    label: "チームワーク指数",
			    fillColor : "rgba(88,214,141,0.5)",
			    data: [$X,$Z,$Y]
			 }
		]
	};
	var radarChartOptions = {
		angleShowLineOut : false,
		angleLineColor : "rgba(88,214,141,1)",	
		pointDot : false,
	};
	

	$('#tab2').on('shown.bs.tab', function (e) {
        areaChart.destroy();
       	radarChart1 = new Chart(radarChartCanvas).Radar(radarChartData,radarChartOptions);
    });

    $('#tab1').on('shown.bs.tab', function (e) {
        radarChart1.destroy();
        areaChart = new Chart(areaChartCanvas).Line(areaChartData,areaChartOptions);    
    });
    $('#tab3').on('shown.bs.tab', function (e) {
    	if(typeof radarChart1 != 'undefined')
    		radarChart1.destroy(); 
        areaChart.destroy();     
    });
    $('#tab4').on('shown.bs.tab', function (e) {
    	if(typeof radarChart1 != 'undefined')
    		radarChart1.destroy();  
        areaChart.destroy();     
    });
    $('#tab5').on('shown.bs.tab', function (e) {
    	if(typeof radarChart1 != 'undefined')
    		radarChart1.destroy();  
        areaChart.destroy();     
    });

});


EOF;


# メンバーごとのサンクス数(棒グラフ)
$canvas2 = '';
$charts = array();
$n = 0;
$x = 8;
$max = 0;
foreach ($data[1] as $res) {
	$n++;
	if ($n == 1) {
		$labels = array();
		$values = array(0 => array(), 1 => array());
	}
	$labels[] = '"'.$res['name'].'"';
	$values[0][] = $res['thanks_receives'];
	$values[1][] = $res['thanks_sends'];
	if ($n >= $x) {
		$charts[] = array(
			'labels' => $labels,
			'values' => $values
		);
		$n = 0;
	}
	if ($res['thanks_receives'] > $max) {
		$max = $res['thanks_receives'];
	}
	if ($res['thanks_sends'] > $max) {
		$max = $res['thanks_sends'];
	}
}
if ($n != 0) {
	$z = count($labels);
	if ($z != $x) {
		for ($n=0; $n<($x-$z); $n++) {
			$labels[] = '"　"';
			$values[0][] = null;
			$values[1][] = null;
		}
	}
	$charts[] = array(
		'labels' => $labels,
		'values' => $values
	);
}

$x = ceil($max / 50);
switch ($x) {
	case 1:
		$scaleSteps = 5;
		$scaleStepWidth = 10;
		break;
	case 2:
		$scaleSteps = 10;
		$scaleStepWidth = 10;
		break;
	case 3:
		$scaleSteps = 5;
		$scaleStepWidth = 30;
		break;
	default:
		$scaleSteps = $x;
		$scaleStepWidth = 50;
		break;
}
foreach ($charts as $key => $res) {
	$val[1] = array(
		'labels' => implode(',', $res['labels']),
		'data' => array(
			0 => implode(',', $res['values'][0]),
			1 => implode(',', $res['values'][1])
		)
	);
	$id = 'barChart'.$key;
	$name = 'barChartData'.$key;
	$canvas2 .= '<canvas id="'.$id.'" style="height:230px"></canvas>';
	$js .= <<<EOF
$(function () {
	//-------------
	//- BAR CHART -
	//-------------

	var {$name} = {
		labels: [{$val[1]["labels"]}],
		datasets: [
			{
				label: "獲得サンクス数",
				fillColor: "rgba(210, 214, 222, 1)",
				strokeColor: "rgba(210, 214, 222, 1)",
				pointColor: "rgba(210, 214, 222, 1)",
				pointStrokeColor: "#c1c7d1",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(220,220,220,1)",
				data: [{$val[1]["data"][0]}]
			},
			{
				label: "送信サンクス数",
				fillColor: "rgba(60,141,188,0.9)",
				strokeColor: "rgba(60,141,188,0.8)",
				pointColor: "#3b8bba",
				pointStrokeColor: "rgba(60,141,188,1)",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(60,141,188,1)",
				data: [{$val[1]["data"][1]}]
			}
		]
	};

	var barChartCanvas = $("#{$id}").get(0).getContext("2d");
	var barChart = new Chart(barChartCanvas);
	var barChartData = {$name};
	barChartData.datasets[1].fillColor = "#00a65a";
	barChartData.datasets[1].strokeColor = "#00a65a";
	barChartData.datasets[1].pointColor = "#00a65a";
	var barChartOptions = {
		//Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
		scaleBeginAtZero: true,
		//Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines: true,
		//String - Colour of the grid lines
		scaleGridLineColor: "rgba(0,0,0,.05)",
		//Number - Width of the grid lines
		scaleGridLineWidth: 1,
		//Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		//Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines: true,
		//Boolean - If there is a stroke on each bar
		barShowStroke: true,
		//Number - Pixel width of the bar stroke
		barStrokeWidth: 2,
		//Number - Spacing between each of the X value sets
		barValueSpacing: 5,
		//Number - Spacing between data sets within X values
		barDatasetSpacing: 1,
		//String - A legend template
		legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
		//Boolean - whether to make the chart responsive
		responsive: true,
		maintainAspectRatio: true,
		scaleOverride: true,
		scaleSteps : {$scaleSteps},
		scaleStepWidth : {$scaleStepWidth},
		scaleStartValue : 0
	};

	barChartOptions.datasetFill = false;
	barChart.Bar(barChartData, barChartOptions);
});


EOF;
}

$colors = array(
	array('dd4b39', 'text-red'),
	array('f39c12', 'text-yellow'),
	array('00c0ef', 'text-aqua'),
	array('0073b7', 'text-blue'),
#	array('111111', 'text-black'),
	array('3c8dbc', 'text-light-blue'),
	array('00a65a', 'text-green'),
	array('d2d6de', 'text-gray'),
	array('001f3f', 'text-navy'),
	array('39cccc', 'text-teal'),
	array('3d9970', 'text-olive'),
	array('01ff70', 'text-lime'),
	array('ff851b', 'text-orange'),
	array('f012be', 'text-fuchsia'),
	array('605ca8', 'text-purple'),
	array('d81b60', 'text-maroon'),
);
$charts = array(0 => array(), 1 => array(), 2 => array());
$n = 0;
$x = count($colors) - 1;
foreach ($data[1] as $id => $res) {
	if ($n > $x) {
		$key = 0;
		$n = 0;
	} else {
		$key = $n;
		$n++;
	}
	$charts[0][] = <<<EOF
		{
			value: {$res["thanks_receives"]},
			color: "#{$colors[$key][0]}",
			highlight: "#{$colors[$key][0]}",
			label: "{$res["name"]}"
		}
EOF;
	$charts[1][] = <<<EOF
		{
			value: {$res["thanks_sends"]},
			color: "#{$colors[$key][0]}",
			highlight: "#{$colors[$key][0]}",
			label: "{$res["name"]}"
		}
EOF;
	$charts[2][] = '<div class="col-md-2 col-sm-3"><i class="fa fa-circle-o '.$colors[$key][1].'"></i>&nbsp;'.$this->Html->Link($res["name"], array('action' => 'staffDetail', $shop_id, $id, $from, $to)).'</div>';
}
$charts[0] = implode(',', $charts[0]);
$charts[1] = implode(',', $charts[1]);
$charts[2] = implode('', $charts[2]);

# メンバーのサンクス割合
$js .= <<<EOF
$(function () {
	//-------------
	//- PIE CHART -
	//-------------
	// Get context with jQuery - using jQuery's .get() method.
	var pieOptions = {
		//Boolean - Whether we should show a stroke on each segment
		segmentShowStroke: true,
		//String - The colour of each segment stroke
		segmentStrokeColor: "#fff",
		//Number - The width of each segment stroke
		segmentStrokeWidth: 2,
		//Number - The percentage of the chart that we cut out of the middle
		percentageInnerCutout: 50, // This is 0 for Pie charts
		//Number - Amount of animation steps
		animationSteps: 100,
		//String - Animation easing effect
		animationEasing: "easeOutBounce",
		//Boolean - Whether we animate the rotation of the Doughnut
		animateRotate: true,
		//Boolean - Whether we animate scaling the Doughnut from the centre
		animateScale: false,
		//Boolean - whether to make the chart responsive to window resizing
		responsive: true,
		// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio: true,
		//String - A legend template
		legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
	};
	var pieChartCanvas0 = $("#pieChart0").get(0).getContext("2d");
	var pieChart0 = new Chart(pieChartCanvas0);
	var PieData0 = [{$charts[0]}];
	pieChart0.Doughnut(PieData0, pieOptions);

	var pieChartCanvas1 = $("#pieChart1").get(0).getContext("2d");
	var pieChart1 = new Chart(pieChartCanvas1);
	var PieData1 = [{$charts[1]}];
	pieChart1.Doughnut(PieData1, pieOptions);
});


EOF;


$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$js .= <<<EOF
$(function () {
	$('#staff-data-table').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[2, 'asc']],
		'displayLength' : 50
	});
});

EOF;

echo $this->Html->css('plugins/report/tab.css');
echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
	<?php echo $this->Form->end(); ?>

<div class="row">
	<?php echo $this->Form->create('TrnThanks'); ?>
	<div class="col-md-6">
		<div class="box box-info">
			<div class="box-body">
				<div class="form-group">
					<label>集計期間</label>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						<?php echo $this->Form->input('reservation', array('id' => 'reservation', 'type' => 'text', 'class' => 'form-control pull-right', 'div' => false, 'label' => false, 'readonly' => true)); ?>
						<div class="input-group-btn">
							<?php echo $this->Form->submit('再集計', array('class' => 'btn btn-info pull-right', 'div' => false)); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12 col-md-12">
		<?php echo $this->element('shop_tab',
			array('sum' => $sum,
				  'n' => $n
				));
		?>
	</div>	
	<div class="col-lg-12 col-md-12">
		<!-- BAR CHART -->
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-thumbs-o-up"></i>&nbsp;メンバーごとのサンクス数</h3>
			</div>
			<div class="box-body">
				<div class="chart">
					<?php echo $canvas2; ?>
				</div>
			</div>
			<!-- /.box-body -->
		</div>
		<!-- /.box -->
	</div>
	<div class="col-lg-12 col-md-12">
		<!-- DONUT CHART -->
		<div class="box box-danger">
			<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-thumbs-o-up"></i>&nbsp;メンバーのサンクス割合</h3>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<p class="text-center">獲得サンクス数</p>
						<div class="chart-responsive">
							<canvas id="pieChart0" style="height:180px"></canvas>
						</div>
					</div>
					<div class="col-lg-6 col-md-6">
						<p class="text-center">送信サンクス数</p>
						<div class="chart-responsive">
							<canvas id="pieChart1" style="height:180px"></canvas>
						</div>
					</div>
					<div class="col-lg-12 col-md-12">
						<div class="row fontawesome-icon-list">
							<?php echo $charts[2]; ?>
						</div>
					</div>
				</div>
			</div>
			<!-- /.box-body -->
		</div>
	</div>
	<div class="col-lg-12 col-md-12">
		<div class="box box-danger">
			<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-thumbs-o-up"></i>&nbsp;Thanks人物分析</h3>
			</div>
			<div class="box-body">
				<table id="staff-data-table" class="table table-bordered">
					<thead>
					<tr>
						<th>No.</th>
						<th>メンバー名</th>
						<th>タイプ</th>
						<th>離職可能性</th>
						<th>獲得サンクス数</th>
						<th>送信サンクス数</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$n = 0;
					foreach ($data2 as $member_id => $res) {
						$n++;
						$name = $this->Html->Link($data[1][$member_id]['name'], array('action' => 'staffDetail', $shop_id, $member_id, $from, $to));
						switch ($res[5]) {
							case 1: $type = '<span class="label label-success">'.$res[6].'</span>'; break;
							case 2: $type = '<span class="label label-warning">'.$res[6].'</span>'; break;
							case 3: $type = '<span class="label label-info">'.$res[6].'</span>'; break;
							case 4: $type = '<span class="label label-danger">'.$res[6].'</span>'; break;
						}
						switch ($res[7]) {
							case 1: $turnover_type = '<span class="label label-info">'.$res[8].'</span>'; break;
							case 2: $turnover_type = '<span class="label label-warning">'.$res[8].'</span>'; break;
							case 3: $turnover_type = '<span class="label label-danger">'.$res[8].'</span>'; break;
						}
						echo <<<EOF
					<tr>
						<td>{$member_id}</td>
						<td>{$name}</td>
						<td><span class="hide">{$res[5]}</span>{$type}</td>
						<td><span class="hide">{$res[7]}</span>{$turnover_type}</td>
						<td>{$res[0]}</td>
						<td>{$res[1]}</td>
					</tr>

EOF;
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
