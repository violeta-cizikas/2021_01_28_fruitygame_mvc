<?php
require APPROOT . '/views/inc/header.php'; ?>

<div>
    <div class="container">
		<!-- echo $data['title'] - reiskia ateina is controler -->
		<div class="winAnimationContainer winAnimationContainerWithMargin">
        	<h1 class="display-3 text-center text-warning winAnimation"><?php echo $data['title'] ?></h1>
    	</div>       
    </div>
</div>

<audio autoplay loop><source src="<?php echo URLROOT?>/audio/Jackpot Sound Effect.mp3" type="audio/mpeg"></audio>

<script>gimmick('body')</script>
	
<?php require APPROOT . '/views/inc/footer.php';

