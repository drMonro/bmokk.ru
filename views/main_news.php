<?
	$main_news = get_news(2);

	if (count($main_news) == 0) {
		exit('Пока нет новостей...');
	}

	foreach ($main_news as $n) {
?>
	<div class="card news">
		<div class="card-body">
			<div class="card-title phone">
				<?=$n['title']?>
			</div>
			<div class="mb-3">
					<?
					$c =150;
						if (strlen($n['description']) > $c){
							$n['description'] = mb_substr($n['description'], 0, $c).' <span class="color_grey"><a href="/?news&id='.$n['id'].'">читать полностью ...</a></span>';
						}
						echo $n['description'];
					?>
			</div>
		</div>
		<?if (!empty($n['date_new'])) {?><span class="news-date color_grey"><?=date("d.m.Y",strtotime($n['date_new']))?></span><?}?>
	</div>
<?
	}
?>
