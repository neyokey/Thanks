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

# メンバーサンクス数(折れ線グラフ)
$labels = array();
$values = array(0 => array(), 1 => array());
$sum = array(0 => 0, 1 => 0);
foreach ($data[0] as $datetime => $nums) {
	$labels[] = '"'.date("n/j", $datetime).'"';
	$values[0][] = $nums[0];
	$values[1][] = $nums[1];
	$sum[0] += $nums[0];
	$sum[1] += $nums[1];
}

$val[0] = array(
	'labels' => implode(',', $labels),
	'data' => array(
		0 => implode(',', $values[0]),
		1 => implode(',', $values[1]),
	)
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
				label: "獲得サンクス数",
				fillColor: "rgba(220,220,220,0.2)",
				strokeColor: "rgba(220,220,220,1)",
				pointColor: "rgba(220,220,220,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(220,220,220,1)",
				data: [{$val[0]["data"][0]}]
			},
			{
				label: "送信サンクス数",
				fillColor: "rgba(151,187,205,0.2)",
				strokeColor: "#00a65a",
				pointColor: "#3b8bba",
				pointStrokeColor: "rgba(60,141,188,1)",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(60,141,188,1)",
				data: [{$val[0]["data"][1]}]
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
	areaChart.Line(areaChartData, areaChartOptions);
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
echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
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
	<?php echo $this->Form->end(); ?>

	<div class="col-lg-12 col-md-12">
		<!-- AREA CHART -->
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">メンバーサンクス数</h3>
				<code>総獲得サンクス数：<?php echo number_format($sum[0]); ?>件、総送信サンクス数：<?php echo number_format($sum[1]); ?>件</code>
			</div>
			<div class="box-body">
				<div class="chart">
					<canvas id="areaChart" style="height:250px"></canvas>
				</div>
			</div>
			<!-- /.box-body -->
			<div class="box-footer clearfix">
				<div class="box-tools pull-left">
					<?php echo $this->Html->Link('チームレポートを表示する', array('action' => 'shopDetail', $shop_id, $from, $to), array('class' => 'btn btn-default btn-sm')); ?>
				</div>
			</div>
		</div>
		<!-- /.box -->
	</div>
	<div class="col-lg-12 col-md-12">
		<!-- BAR CHART -->
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">他のメンバーとのサンクス数</h3>
			</div>
			<div class="box-body">
				<div class="chart">
					<?php echo $canvas2; ?>
				</div>
			</div>
			<!-- /.box-body -->
			<div class="box-footer clearfix">
				<div class="box-tools pull-left">
					<?php echo $this->Html->Link('チームレポートを表示する', array('action' => 'shopDetail', $shop_id, $from, $to), array('class' => 'btn btn-default btn-sm')); ?>
				</div>
			</div>
		</div>
		<!-- /.box -->
	</div>
</div>
