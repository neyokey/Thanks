<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright		Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link			http://cakephp.org CakePHP(tm) Project
 * @package			app.View.Layouts
 * @since			CakePHP(tm) v 0.10.0.1076
 * @license			http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'thanks!: 管理画面');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription.':'.$this->fetch('title'); ?>
	</title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

	<?php
	echo $this->Html->meta('icon');

//	echo $this->Html->css('cake.generic');

	echo $this->Html->css('bootstrap.min');
	echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css');
	echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css');
	echo $this->Html->css('AdminLTE');
	echo $this->Html->css('skins/_all-skins.min');

	echo $this->fetch('meta');
	echo $this->fetch('css');
	?>
</head>
<body class="skin-green sidebar-mini wysihtml5-supported">
<div class="wrapper">

	<header class="main-header">
		<!-- Logo -->
		<?php echo $this->Html->Link('<span class="logo-mini"><b>39</b>s</span><span class="logo-lg"><b>t</b>hanks!</span>', '/', array('class' => 'logo', 'escape' => false)); ?>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top">
			<!-- Sidebar toggle button-->
			<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
				<span class="sr-only">Toggle navigation</span>
			</a>
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<!-- User Account: style can be found in dropdown.less -->
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?php echo $this->Html->Image('app_icon_ios_1024-1024_a.png', array('alt' => 'User Image', 'class' => 'user-image')); ?>
							<span class="hidden-xs"><?php echo $userSession['aname']; ?></span>
						</a>
						<ul class="dropdown-menu">
							<!-- User image -->
							<li class="user-header">
								<?php echo $this->Html->Image('app_icon_ios_1024-1024_a.png', array('alt' => 'User Image', 'class' => 'user-image')); ?>
								<p>
									<?php echo $userSession['aname']; ?>
									<small><?php echo 'Member since '.date('M. Y', strtotime($userSession['insert_time'])); ?></small>
								</p>
							</li>
							<!-- Menu Footer-->
							<li class="user-footer">
								<div class="pull-right">
									<?php echo $this->Html->Link('Sign out', array('controller' => 'pages', 'action' => 'logout'), array('class' => 'btn btn-default btn-flat')); ?>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	</header>

	<!-- Left side column. contains the logo and sidebar -->
	<aside class="main-sidebar">
		<!-- sidebar: style can be found in sidebar.less -->
		<section class="sidebar">
			<ul class="sidebar-menu">
				<li>
					<?php echo $this->Html->Link('<i class="fa fa-home"></i><span>Home</span>', '/', array('escape' => false)); ?> 
				</li>
				<li class="<?php echo $this->name == 'Account' ? 'treeview active' : 'treeview'; ?>">
					<a href="#">
						<i class="fa fa-building-o"></i>
						<span>アカウント</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<?php
						if ($userSession['acc_grant'] == 0) {
							echo '<li>'.$this->Html->Link('<i class="fa fa-angle-double-right"></i>元売', array('controller' => 'account', 'action' => 'master'), array('escape' => false)).'</li>';
						}
						if ($userSession['acc_grant'] <= 1) {
							echo '<li>'.$this->Html->Link('<i class="fa fa-angle-double-right"></i>代理店', array('controller' => 'account', 'action' => 'agency'), array('escape' => false)).'</li>';
						}
						if ($userSession['acc_grant'] <= 2) {
							echo '<li>'.$this->Html->Link('<i class="fa fa-angle-double-right"></i>企業', array('controller' => 'account', 'action' => 'company'), array('escape' => false)).'</li>';
						}
						echo '<li>'.$this->Html->Link('<i class="fa fa-angle-double-right"></i>チーム', array('controller' => 'account', 'action' => 'shop'), array('escape' => false)).'</li>';
						echo '<li>'.$this->Html->Link('<i class="fa fa-angle-double-right"></i>メンバー', array('controller' => 'account', 'action' => 'staff'), array('escape' => false)).'</li>';
						?>
					</ul>
				</li>
				<li class="<?php echo $this->name == 'Report' ? 'treeview active' : 'treeview'; ?>">
					<a href="#">
						<i class="fa fa-newspaper-o"></i>
						<span>レポート</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<?php
						if ($userSession['acc_grant'] != 3) {
							echo '<li>'.$this->Html->Link('<i class="fa fa-angle-double-right"></i>トータルレポート', array('controller' => 'report', 'action' => 'detail'), array('escape' => false)).'</li>';
						}
						?>
						<li><?php echo $this->Html->Link('<i class="fa fa-angle-double-right"></i>チームレポート', array('controller' => 'report', 'action' => 'shop'), array('escape' => false)); ?></li>
						<li><?php echo $this->Html->Link('<i class="fa fa-angle-double-right"></i>メンバーレポート', array('controller' => 'report', 'action' => 'staff'), array('escape' => false)); ?></li>
						<li><?php
						if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
						switch ($userSession['acc_grant']) {
							case 0:
							case 1:
								$action = 'stamp';
								break;
							case 2:
								$action = 'stampCompany';
								break;
							case 3:
								$action = 'stampShop';
								break;
						}
						echo $this->Html->Link('<i class="fa fa-angle-double-right"></i>ポイントレポート', array('controller' => 'report', 'action' => $action), array('escape' => false));
						}
						?></li>
					</ul>
				</li>
				<?php if ($userSession['acc_grant'] == 0) { ?>
				<li class="<?php echo $this->name == 'Stamp' ? 'treeview active' : 'treeview'; ?>">
					<a href="#">
						<i class="fa fa-fw fa-commenting"></i>
						<span>スタンプ</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><?php echo $this->Html->Link('<i class="fa fa-angle-double-right"></i>スタンプ管理', array('controller' => 'stamp', 'action' => 'index'), array('escape' => false)); ?></li>
					</ul>
				</li>
				<li class="<?php echo $this->name == 'Bot' ? 'treeview active' : 'treeview'; ?>">
					<a href="#">
						<i class="fa fa-fw fa-android"></i>
						<span>Bot</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><?php echo $this->Html->Link('<i class="fa fa-angle-double-right"></i>Bot管理', array('controller' => 'bot', 'action' => 'index'), array('escape' => false)); ?></li>
					</ul>
				</li>
				<li class="<?php echo $this->name == 'Push' ? 'treeview active' : 'treeview'; ?>">
					<a href="#">
						<i class="fa fa-fw fa-bell-o"></i>
						<span>Push通知</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><?php echo $this->Html->Link('<i class="fa fa-angle-double-right"></i>Push通知予約', array('controller' => 'push', 'action' => 'add'), array('escape' => false)); ?></li>
						<li><?php echo $this->Html->Link('<i class="fa fa-angle-double-right"></i>Push通知管理', array('controller' => 'push', 'action' => 'index'), array('escape' => false)); ?></li>
					</ul>
				</li>
				<?php if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) { ?>
				<li class="<?php echo $this->name == 'Point' ? 'treeview active' : 'treeview'; ?>">
					<a href="#">
						<i class="fa fa-fw fa-amazon"></i>
						<span>Point交換申請</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<?php
						foreach ($PointExchanges as $item_id => $res) {
							$ext = $res['num'] > 0 ? '<span class="pull-right-container"><small class="label pull-right bg-green">'.$res['num'].'</small></span>' : '';
							echo '<li>'.$this->Html->Link('<i class="fa fa-angle-double-right"></i><span>'.$res['name'].'</span>'.$ext, array('controller' => 'point', 'action' => 'index', $item_id), array('escape' => false)).'<li>';
						}
						?>
					</ul>
				</li>
				<?php } ?>
				<?php } ?>
			</ul>
		</section>
		<!-- /.sidebar -->
	</aside>

	<!-- Right side column. Contains the navbar and content of the page -->
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				<?php echo $title; ?>
				<small>Control panel</small>
			</h1>
			<ol class="breadcrumb">
				<li><?php echo $this->Html->Link('<i class="fa fa-dashboard"></i> Home', '/', array('escape' => false)); ?></li>
				<li class="active"><?php echo $this->fetch('title'); ?></li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</section>
		<!-- /.content -->
	</div>
	<!-- /.right-side -->
</div>
<!-- ./wrapper -->

	<?php
	echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js');
	echo $this->Html->script('bootstrap.min');
	echo $this->Html->script('AdminLTE/app');

	echo $this->fetch('script');
	?>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
</body>
</html>