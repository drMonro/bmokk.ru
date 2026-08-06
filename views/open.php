<?
	$content = get_content('oi', true);
	$i = isset($_GET['i']) ? $_GET['i'] : 0;
?>

<div class="col-12">
	<h2><?=explode(' - ',$content[$i]['name'])[1]?></h2>
</div>

<div class="col my-2">
	<div class="row">
		<div class="col-md-8">
			<?=$content[$i]['description']?>
		</div>
		<div class="col-md-4 my-2">
			<?
			$i = 0;
			foreach ($content as $c) {
				?>
					<a class="open-i" href="/?oi&i=<?=$i?>">
						<div class="card news">
							<div class="card-body">
								<i class="fa fa-info-circle" aria-hidden="true"></i><span style="padding-left: 8px;"><?=explode(' - ',$c['name'])[1]?></span>
							</div>
						</div>
					</a>
				<?
				$i++;
			}
			?>
		</div>
	</div>
</div>