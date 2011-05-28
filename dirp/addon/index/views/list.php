<? if($crumbs): ?>
	<div id="crumb-container">
		<span id="crumbs">
			<img alt="breadcrumb" src="<?= \dirp\app::asset('img/folders.png') ?>">
			<a class="crumb startcrumb" href="?dir=/">root</a>
			<span class="crumbspacer">/</span>
			<? $crumbcount = 0 ?>
			<? foreach($crumbs as $crumb => $path): ?>
				<? if(empty($crumb)): continue; endif; ?>
				<a class="crumb" href="<?= $path ?>"><?= $crumb ?></a>
				<? if($crumbcount < count($crumbs) - 1): ?>
					<span class="crumbspacer">/</span>
				<? endif; $crumbcount++ ?>

			<? endforeach ?>
		</span>
	</div>
<? endif ?>
<? if( $body_override ) : ?>
	<?= $body_override ?>
<? elseif( (array) $files): ?>
	<div id="file-list">
		<? foreach($files as $file): ?>
			
			<? if(!$file->visible): continue; endif ?>

			<? if($file->isdir): ?>
				<a href="<?= $root . '?dir=' . rawurlencode($relative .'/' . $file->name) ?>">
			<? else: ?>
				<a href="<?=  $root . \dirp\file::to_path($filesroot, rawurlencode(trim($relative, '/')) ?: null, rawurlencode($file->name)) ?>">
			<? endif ?>
					<div class="file <?= $file->isdir ? 'folder' : '' ?>">
						<img alt="icon" class="fileicon" alt="icon" src="<?= \dirp\app::asset('img/'.$file->icon) ?>">
						<span class="filename"><?= $file->displayname ?: $file->name ?></span>
						<? if($file->size): ?>
							<span class="filesize"><?= $file->get_pretty_size() ?></span>
						<? endif ?>
						<? if($file->mtime): ?>
							<span class="filemodified"><?= date('g:i:s a', $file->mtime) ?></span>
						<? endif ?>
						<? if($file->sub): ?>
							<div class="sub">
								<div class="content">
									<?= $file->sub ?>
								</div>
							</div>
						<? endif ?>
					</div>
				</a>
			<? $files_count++ //hackety hack ?>
		<? endforeach ?>
	</div>
<? endif ?>
<? if(!$files_count): ?>
	<div class="message">
		<img src="<?= \dirp\app::asset('img/page_look.png') ?>" alt="uhhhh">
		There's not a single file in sight!
	</div>
<? endif ?>