<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="<?= \dirp\app::asset('css/dirp.css') ?>">

		<? if($css): ?>
			<? foreach($css as $sheet): ?>
				<link rel="stylesheet" type="text/css" href="<?= $sheet ?>">
			<? endforeach ?>
		<? endif ?>

		<? if($head): ?>
			<? foreach($head as $thing): ?>
				<?= $thing ?>
			<? endforeach ?>
		<? endif ?>

		<title>dirp : <?= $title ?></title>
	</head>
	<body>
		<div id="outer-wrapper">
		<div id="main-wrapper">
			<div id="navigation">
				<? foreach(\dirp\addon\event::fire('rendernav', array('links' => array()))->links as $link => $path): ?>
					<a class="button" href="<?= $path ?>"><?= $link ?></a>
				<? endforeach ?>
			</div>
			<div id="header">
				<h1> <img alt="icon" src="<?= \dirp\app::asset('img/' . $icon) ?>"> <?= $title ?></h1>
			</div>
			<div id="content">
				<?= $body ?>
			</div>
			<div id="panels">
				<? if($panels): ?>
					<? foreach($panels as $p_title => $p_body): ?>
						<div class="panel">
							<div class="header">
								<img alt="panel" src="<?= \dirp\app::asset('img/plugin.png') ?>">
								<?= $p_title ?: 'Unnamed Panel' ?>
							</div>
							<div class="content">
								<?= $p_body ?: '<em>&lt;crickets&gt;</em>' ?>
							</div>
						</div>
					<? endforeach ?>
				<? endif ?>
			</div>
		</div>
		</div>

		<div id="footer">
			powered by
			<a class="button" style="margin-right:0" href="http://github.com/filp/dirp">
				<img src="<?= \dirp\app::asset('img/folder_heart2.png') ?>">
				dirp
			</a>
		</div>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
		<!-- <script type="text/javascript" src="<?= \dirp\app::asset('js/dirp.js') ?>"></script> -->
		<? if($js): ?>
			<? foreach($js as $script): ?>
				<script type="text/javascript" src="<?= $script ?>"></script>
			<? endforeach ?>
		<? endif ?>
	</body>
</html>
