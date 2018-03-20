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

# 店舗サンクス数(折れ線グラフ)
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
	areaChart.Line(areaChartData, areaChartOptions);
});

EOF;
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
				<h3 class="box-title">総サンクス数</h3>
				<code>総サンクス数：<?php echo number_format($sum); ?>件</code>
			</div>
			<div class="box-body">
				<div class="chart">
					<canvas id="areaChart" style="height:250px"></canvas>
				</div>
			</div>
			<!-- /.box-body -->
		</div>
		<!-- /.box -->
	</div>
	<div class="col-lg-6 col-md-6">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">サンクス利用頻度のトップ10</h3>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>企業名</th>
					<th>店舗名</th>
					<th>thanks数/人・週</th>
					<th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data[1] as $shop_id => $res): ?>
				<tr>
					<td><?php echo $this->Html->Link($chains[$res['chain_id']], '/account/companyView/'.$res['chain_id']); ?>&nbsp;</td>
					<td><?php echo $this->Html->Link($res['aname'], '/account/shopView/'.$shop_id); ?>&nbsp;</td>
					<td><?php echo '<span class="label '.$res['label'].'">'.sprintf('%.1f', round($res['num'], 1)).'</span>'; ?>&nbsp;</td>
					<td class="actions">
						<?php echo $this->Html->Link('レポート', '/report/shopDetail/'.$shop_id.'/'.$from.'/'.$to, array('class' => 'btn btn-info btn-sm')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">サンクス利用頻度のワースト10</h3>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>企業名</th>
					<th>店舗名</th>
					<th>thanks数/人・週</th>
					<th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data[2] as $shop_id => $res): ?>
				<tr>
					<td><?php echo $this->Html->Link($chains[$res['chain_id']], '/account/companyView/'.$res['chain_id']); ?>&nbsp;</td>
					<td><?php echo $this->Html->Link($res['aname'], '/account/shopView/'.$shop_id); ?>&nbsp;</td>
					<td><?php echo '<span class="label '.$res['label'].'">'.sprintf('%.1f', round($res['num'], 1)).'</span>'; ?>&nbsp;</td>
					<td class="actions">
						<?php echo $this->Html->Link('レポート', '/report/shopDetail/'.$shop_id.'/'.$from.'/'.$to, array('class' => 'btn btn-info btn-sm')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
