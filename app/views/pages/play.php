<?php
require APPROOT . '/views/inc/header.php'; ?>

<!-- jumbotron - demonstracine bootstrap'o klase -->
<div class="jumbotron jumbotron-fluid jumbotron-play">
	<div class="container playContainer">
		<h1>Hello, <?php echo $data['user']['nickname']?></h1>
		<p>Your balance: $<?php echo $data['user']['balance']?></p>
		<!-- kuriama forma -->
		<!-- pridedamas metodas post, name, php, span -->
		<form method='post'>
			<div class="form-group">
				<input name='sum' type="text" class="form-control <?php echo (!empty($data['sumErr'])) ? 'is-invalid' : ''; ?>" placeholder="Bet sum">
				<span class='invalid-feedback'><?php echo $data['sumErr'] ?></span>
			</div>

			<button type="submit" class="btn btn-primary">Play</button>
		</form>
	</div>

	<!-- zaidimas -->
	<div class="container">

		<!-- laimejimo pranesimas-->
		<?php if (isset($data['win'])) :?>
			<div class="winAnimationContainer">
				<h1 class="winAnimation">YOU WIN $<?php echo $data['winAmount']?> !!!</h1>
			</div>
			<!-- 64 _ laimejimo garso efektas --> 
			<audio autoplay loop><source src="<?php echo URLROOT?>/audio/Jackpot Sound Effect.mp3" type="audio/mpeg"></audio>
			<script>gimmick('body')</script>
		<?php endif; ?>

		<div class="gameContainer">
			<!-- php -->
			<?php if (isset($data['play'])) :?>
			<?php foreach ($data['cards'] as $key => $card) :?>
				<div class="gameCard"><img src="<?php echo URLROOT . '/img/' . $card ?>"/></div> 
			<?php endforeach; ?>  
			<?php else: ?>
				<!-- kai uzverstos korteles -->
				<!--arba su for ciklu, kad nerasyti 9 kartus --> 
				<div class="gameCard"></div>
				<div class="gameCard"></div>
				<div class="gameCard"></div>
				<div class="gameCard"></div>
				<div class="gameCard"></div>
				<div class="gameCard"></div>
				<div class="gameCard"></div>
				<div class="gameCard"></div>
				<div class="gameCard"></div>
			<?php endif; ?>  
		</div>
	</div>
</div>

<!-- footeryje uzdaromas html'as ir body -->
<?php require APPROOT . '/views/inc/footer.php';