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

$cakeDescription = __d('cake_dev', 'Thanks!');
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
	<!-- Right side column. Contains the navbar and content of the page -->
	<div class="content-wrapper">
		<?php
		$flg = isset($ContentHeaderFlg) ? $ContentHeaderFlg : true;
		if ($flg === true) {
			echo '<section class="content-header"><h1>Thanks!</h1></section>';
		}
		?>

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