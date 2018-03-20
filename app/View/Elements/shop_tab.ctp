<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#tab_1" id="tab1" data-toggle="tab">レポート</a>
				</li>
				<li class="">
					<a href="#tab_2" id="tab2" data-toggle="tab">チームワーク分析</a>
				</li>
				<li class="">
					<a href="#tab_3" id="tab3" data-toggle="tab">タイプ別分析</a>
				</li>
				<li class="">
					<a href="#tab_4" id="tab4" data-toggle="tab">離職可能性分析</a>
				</li>
				<li class="">
					<a href="#tab_5" id="tab5" data-toggle="tab">ネットワーク分析</a>
				</li>			
			</ul>
			<div class="tab-content">
				<div id="tab_1" class="tab-pane active">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-thumbs-o-up"></i>&nbsp;チームサンクス数</h3>
						<code>総サンクス数：<?php echo number_format($sum); ?>件</code>
					</div>
					<div class="box-body">
						<div class="chart">
							<canvas id="areaChart" style="height:250px"></canvas>
						</div>
					</div>
				</div>
				<div id="tab_2" class="tab-pane "> 
					<div class="box-header with-border">
						<h3 class="box-title">3つの指標で測ったthanks!活用度からチームワークを評価します。</h3>
					</div>
					<div class="box-body">
						<div class="col-lg-12 col-md-12">
							<div class="col-lg-7 col-md-7">
								<div class="col-lg-12 col-md-12">
									<h4 class="box-title"><i class="fa fa-thumbs-o-up"></i>チームワーク指数 &nbsp<i class="fa fa-question-circle"></i></h4>	
								</div>
								<div class="col-lg-12 col-md-12">
									<div class="col-lg-8 col-md-8 text-center">
										<h1><?php echo $XYZ ;?></h1>
									</div>
									<div class="col-lg-4 col-md-4">
										<div class="col-lg-12 col-md-12">
											&nbsp
										</div>
										<div class="col-lg-12 col-md-12">
											&nbsp
										</div>
										<div class="col-lg-12 col-md-12">
											/100
										</div>
									</div>
								</div>
								<div class="col-lg-12 col-md-12 ">
									<div class="col-lg-12 col-md-12">
										<div class="col-lg-4 col-md-4">
											平均thanks!頻度
										</div>
										<div class="col-lg-1 col-md-1"><i class="fa fa-question-circle"></i>
										</div>
										<div class="col-lg-7 col-md-7 ">
											<div class="col-lg-6 col-md-6 text-center">
												<?php echo $X; ?>
											</div>
											<div class="col-lg-5 col-md-5">
												/5
											</div>
										</div>
									</div>
									<div class="col-lg-12 col-md-12">
										<div class="col-lg-4 col-md-4 ">
											起動アクティブ率
										</div>
										<div class="col-lg-1 col-md-1"><i class="fa fa-question-circle"></i>
										</div>
										<div class="col-lg-7 col-md-7 ">
											<div class="col-lg-6 col-md-6 text-center">
												<?php echo $Y; ?>
											</div>
											<div class="col-lg-5 col-md-5">
												/5
											</div>
										</div>
									</div>
									<div class="col-lg-12 col-md-12">
										<div class="col-lg-4 col-md-4">
											送信アクティブ率
										</div>
										<div class="col-lg-1 col-md-1"><i class="fa fa-question-circle"></i>
										</div>
										<div class="col-lg-7 col-md-7 ">
											<div class="col-lg-6 col-md-6 text-center">
												<?php echo $Z; ?>
											</div>
											<div class="col-lg-5 col-md-5">
												/5
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-5 col-md-5" >
								<div class="chart-responsive">
									<canvas id="radarChart"></canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				foreach ($data2 as $member_id => $res) {
					$n++;
					$name = $this->Html->Link($data[1][$member_id]['name'], array('action' => 'staffDetail', $shop_id, $member_id, $from, $to));
					switch ($res[5]) 
					{
						case 1: $type_success[] = $member_id; break;
						case 2: $type_warning[] = $member_id; break;
						case 3: $type_info[] = $member_id; break;
						case 4: $type_danger[] = $member_id; break;
					}
				}
				?>
				<div id="tab_3" class="tab-pane "> 
					<div class="box-header with-border">
						<h3 class="box-title">送受信数を元にメンバーを4つのタイプに分けマッピングします</h3>
					</div>
					<div class="box-body">
						<div class="col-lg-12 col-md-12">
							<div class="col-lg-7 col-md-7">
								<div class="box box-info">
									<div class="box-header with-border">
										<h3 class="box-title">クール<i class="fa fa-question-circle"></i></h3>
									</div>
									<div class="box-body">
										<div class="col-lg-12 col-md-12" >
											<?php 
												foreach($type_info as $var)
												{
											?>
											<div class="boxhinhanh text-center">
												<a href="<?php echo $this->Html->url(array('action' => 'staffDetail', $shop_id, $var, $from, $to)); ?>" class="linkhinhanh">
												<img class="hinhanh " border="1" height=50 width=50 src="/newshindan/img/avatar4.png">
												<BR>
												<?php
													echo $data[1][$var]['name'];
												?>
												</a>
											</div>
											<?php
												}
											?>
										</div>
									</div>									
								</div>
							</div>
							<div class="col-lg-5 col-md-5">
								<div class="box box-success">
									<div class="box-header with-border">
										<h3 class="box-title">人気者<i class="fa fa-question-circle"></i></h3>
									</div>
									<div class="box-body">
											<?php 
												foreach($type_success as $var)
												{	
											?>
											<div class="boxhinhanh text-center">
												<a href="<?php echo $this->Html->url(array('action' => 'staffDetail', $shop_id,$var, $from, $to)); ?>"
												class="linkhinhanh">
												<img class="hinhanh " border="1" height=50 width=50 src="
												<?php echo $data[1][$var]
												['profile_img_url'];
												?>.png">
												<BR>
												<?php
													echo $data[1][$var]['name'];
												?>
												</a>
											</div>
											<?php
												}
											?>
									</div>									
								</div>
							</div>
						</div>
						<div class="col-lg-12 col-md-12">
							<div class="col-lg-7 col-md-7">
								<div class="box box-danger">
									<div class="box-header with-border">
										<h3 class="box-title">孤立<i class="fa fa-question-circle"></i></h3>
									</div>
									<div class="box-body">
										<?php 
											foreach($type_danger as $var)
											{
										?>
										<div class="boxhinhanh text-center">
												<a href="<?php echo $this->Html->url(array('action' => 'staffDetail', $shop_id,$var, $from, $to)); ?>" class="linkhinhanh">
												<img class="hinhanh " border="1" height=50 width=50 src="
												<?php echo $data[1][$var]
												['profile_img_url'];
												?>.png">
												<BR>
												<?php
													echo $data[1][$var]['name'];
												?>
												</a>
											</div>
										<?php
											}
										?>
									</div>								
								</div>
							</div>
							<div class="col-lg-5 col-md-5">
								<div class="box box-warning">
									<div class="box-header with-border">
										<h3 class="box-title">世話焼き<i class="fa fa-question-circle"></i></h3>
									</div>
									<div class="box-body">
										<?php 
											foreach($type_warning as $var)
											{
										?>
										<div class="boxhinhanh text-center">
												<a href="<?php echo $this->Html->url(array('action' => 'staffDetail', $shop_id,$var, $from, $to)); ?>" class="linkhinhanh">
												<img class="hinhanh " border="1" height=50 width=50 src="
												<?php echo $data[1][$var]
												['profile_img_url'];
												?>.png">
												<BR>
												<?php
													echo $data[1][$var]['name'];
												?>
												</a>
											</div>
										<?php
											}
										?>
									</div>								
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="tab_4" class="tab-pane "> 
					<div class="box-header with-border">
						<h3 class="box-title">thanks!の活用度をスコア化して評価し、離職可能性を分析します</h3>
					</div>
					<div class="box-body">
						<div class="col-lg-12 col-md-12 text-center">
							<div class="col-lg-2 col-md-2">
								低
							</div>
							<div class="col-lg-8 col-md-8">
								離職可能性<i class="fa fa-question-circle"></i>
							</div>
							<div class="col-lg-2 col-md-2">
								低
							</div>
						</div>
							<div id="hbgReport" class="col-lg-12 col-md-12">
								<br>
							</div>
							<div id="bgReport" class="col-lg-12 col-md-12 text-center">
								a
							</div>
					</div>
				</div>
				<div id="tab_5" class="tab-pane "> 
					<div class="box-header with-border">
						<h3 class="box-title">チームの中心となるネットワークを表示します。アイコンをクリックするとそのユーザーを中心としたネットワークを再表示します。</h3>
					</div>
					<div class="box-body">
					</div>					
				</div>
			</div>
		</div>