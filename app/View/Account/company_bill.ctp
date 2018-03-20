<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
				<div class="box-tools pull-right">
					<ul class="pagination pagination-sm inline">
						<li><?php echo $this->Html->Link('«前年', array('action' => 'companyBill', $id, ($year - 1))) ?></li>
						<li><?php echo $this->Html->Link('翌年»', array('action' => 'companyBill', $id, ($year + 1))) ?></li>
					</ul>
				</div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th>&nbsp;</th>
						<th>利用店舗数</th>
						<th>支払金額</th>
						<th>締日</th>
						<th>支払期日</th>
					</tr>
					<?php
					foreach ($data['Bill'] as $res) {
						echo <<<EOF
						<tr>
							<th>{$res['value']}</th>
							<td>{$res['coun']}件</td>
							<td>{$res['pric']}円</td>
							<td>{$res['dy1']}</td>
							<td>{$res['dy2']}</td>
						</tr>

EOF;
					}
					?>
				</table>
			</div>
			<div class="box-footer clearfix">
				<?php echo $this->Html->Link('企業詳細へ戻る', array('action' => 'companyView', $id), array('class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
