<?
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<div class="col-12">
	<?
	if ($id < 1) {
		echo '<h2>Новости</h2>';
	}
		$main_news = get_news();

		if (count($main_news) == 0) {
			exit('Пока нет новостей...');
		}

		foreach ($main_news as $n) {
			if ($id > 0 && $id != $n['id']){
				continue;
			}
	?>
		<div class="card news">
			<div class="card-body">
				<div class="card-title phone">
					<?=$n['title']?>
				</div>
				<div class="mb-3">
					<?=$n['description'];?>
				</div>
			</div>
			<?if (!empty($n['date_new'])) {?><span class="news-date color_grey"><?=date("d.m.Y",strtotime($n['date_new']))?></span><?}?>
		</div>
	<?
		}
	?>
				<div class="mb-3 text-center">
					<span class="btn btn-dark" onclick="window.location.href ='/<?=($id > 0) ? '?news' : ''?>'">
						<i class="fa fa-arrow-left" aria-hidden="true"></i> <?=($id > 0) ? 'Все новости' : 'На главную'?>
					</span>
				</div>
</div>
