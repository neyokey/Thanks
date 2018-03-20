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
	areaChart.Line(areaChartData, areaChartOptions);
});

 
EOF;

$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
foreach ($data3 as $category_id => $_data3) {
	$js .= <<<EOF
$(function () {
	$('#stamp-data-table{$category_id}').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[3, 'desc']],
		'displayLength' : 10
	});
});

EOF;
}
$js .= <<<EOF
$(function () {
	$('#stamp-data-table-best').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[3, 'desc']],
		'displayLength' : 10
	});
});

EOF;


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
$charts[3] = array();
$n = 0;
foreach ($data3 as $category_id => $_data3) {
	$x = 0;
	foreach ($_data3['data'] as $res) {
		$x += $res['nums'];
	}
	$charts[3][] = <<<EOF
		{
			value: {$x},
			color: "#{$colors[$n][0]}",
			highlight: "#{$colors[$n][0]}",
			label: "{$_data3["name"]}"
		}
EOF;
	$n++;
}
$charts[3] = implode(',', $charts[3]);
$js .= <<<EOF
$(function () {
	//-------------
	//- PIE CHART -
	//-------------
	// Get context with jQuery - using jQuery's .get() method.
	var pieOptions = {
		segmentShowStroke: true,
		segmentStrokeColor: "#fff",
		segmentStrokeWidth: 2,
		percentageInnerCutout: 50, // This is 0 for Pie charts
		animationSteps: 100,
		animationEasing: "easeOutBounce",
		animateRotate: true,
		animateScale: false,
		responsive: true,
		maintainAspectRatio: true,
		legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
	};
	var pieChartCanvas3 = $("#pieChart3").get(0).getContext("2d");
	var pieChart3 = new Chart(pieChartCanvas3);
	var PieData3 = [{$charts[3]}];
	pieChart3.Doughnut(PieData3, pieOptions);
});

EOF;


echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
<div class="row">
	<?php echo $this->Form->create('TrnThanks'); ?>
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body">
				<div class="row">
					<div class="col-lg-3 col-md-3">
						<?php echo $this->Form->input('status', array('type' => 'select', 'class' => 'form-control', 'label' => '利用状態', 'options' => $status, 'empty' => 'ALL', 'div' => array('class' => 'form-group'))); ?>
					</div>
					<div class="col-lg-3 col-md-3">
						<?php echo $this->Form->input('trial_flg', array('type' => 'select', 'class' => 'form-control', 'label' => '利用形態', 'options' => $trialFlgs, 'empty' => 'ALL', 'div' => array('class' => 'form-group'))); ?>
					</div>
					<div class="col-lg-6 col-md-6">
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

<?php
$box_body = '';
$shop_num = 0;
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
$n = 0;
$x = count($colors) - 1;
foreach ($shops as $agency_id => $agencies) {
	if ($agencyId !== null) {
		if ($agency_id != $agencyId) {
			continue;
		}
	}
	foreach ($agencies['data'] as $chain_id => $chains) {
		if ($chainId !== null) {
			if ($chain_id != $chainId) {
				continue;
			}
		}
		foreach ($chains['data'] as $shop_id => $shop_name) {
			if ($n > $x) {
				$key = 0;
				$n = 0;
			} else {
				$key = $n;
				$n++;
			}
			$class = 'fa fa-circle-o '.$colors[$key][1];
			$box_body .= '<div class="col-shop-list col-md-4 col-sm-6"><i class="'.$class.'">&nbsp;</i>'.$this->Html->Link($shop_name, '/report/shopDetail/'.$shop_id.'/'.$from.'/'.$to).'</div>';
			$shop_num++;
		}
	}
}
?>
	<div class="col-lg-12 col-md-12">
		<div class="box box-danger">
			<div class="box-header with-border">
				<h3 class="box-title">集計チーム一覧</h3>
				<code><?php echo 'チーム数：'.$shop_num.'件、メンバー数：'.$member_num.'人'; ?></code>
			</div>
			<div class="box-body">
				<div class="row fontawesome-icon-list">
					<?php echo $box_body; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-12 col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<?php
				$n = 0;
				foreach ($data3 as $category_id => $res) {
					$n++;
					$class = $n == 1 ? 'active' : '';
					echo '<li class="'.$class.'"><a href="#tab_'.$n.'" data-toggle="tab">'.$res['name'].'</a></li>';
				}
				?>
			</ul>
			<div class="tab-content">
				<?php
				$n = 0;
				foreach ($data3 as $category_id => $res) {
					$n++;
					$class = $n == 1 ? 'tab-pane active' : 'tab-pane';
					echo '<div id="tab_'.$n.'" class="'.$class.'">';
				?>
					<h4 class="page-header"><i class="fa fa-th"></i>&nbsp;カテゴリごとのスタンプ利用率</h4>
					<table cellpadding="0" cellspacing="0" id="<?php echo 'stamp-data-table'.$category_id; ?>" class="table table-bordered table-hover dataTable">
					<thead>
					<tr>
						<th>Id</th>
						<th>スタンプ名</th>
						<th>スタンプ画像</th>
						<th style="width: 110px;">利用数</th>
						<th style="width: 110px;">利用割合</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($res['data'] as $stamp_id => $res2) { ?>
					<tr>
						<td><?php echo $stamp_id; ?></td>
						<td><?php echo $res2['stamp_name']; ?></td>
						<td><?php echo $this->Html->Image(Configure::read('IMG_URL').$res2['image_url'], array('class' => 'img-responsive', 'style' => 'width: 20%;')); ?></td>
						<td><?php echo '<span class="hide">'.sprintf("%05d", $res2['nums']).'</span>'.number_format($res2['nums']).'回'; ?></td>
						<td><?php echo sprintf("%02d", $res2['rate']).'％'; ?></td>
					</tr>
					<?php } ?>
					</tbody>
					</table>
				<?php
					echo '</div>';
				}
				?>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-th"></i>&nbsp;全体のサンクス利用数</h3>
			</div>
			<div class="box-body table-responsive no-padding">
				<table cellpadding="0" cellspacing="0" id="stamp-data-table-best" class="table table-bordered table-hover dataTable">
					<thead>
					<tr>
						<th>Id</th>
						<th>スタンプ名</th>
						<th>スタンプ画像</th>
						<th>利用数</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($data4['BEST'] as $stamp_id => $res) { ?>
					<tr>
						<td><?php echo $stamp_id; ?></td>
						<td><?php echo $res['stamp_name']; ?></td>
						<td><?php echo $this->Html->Image(Configure::read('IMG_URL').$res['image_url'], array('class' => 'img-responsive', 'style' => 'width: 40%;')); ?></td>
						<td><?php echo '<span class="hide">'.sprintf("%05d", $res['nums']).'</span>'.number_format($res['nums']).'回'; ?></td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6">
		<div class="box box-danger">
			<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-th"></i>&nbsp;カテゴリの割合</h3>
			</div>
			<div class="box-body">
				<canvas id="pieChart3" style="height:250px"></canvas>
			</div>
		</div>
	</div>
</div>
