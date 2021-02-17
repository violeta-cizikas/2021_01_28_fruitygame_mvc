<?php
require APPROOT . '/views/inc/header.php'; ?>

<!-- jumbotron - demonstracine bootstrap'o klase -->
<div class="jumbotron jumbotron-fluid">
	<div class="container">
		<h1>Register</h1>
		<!-- kuriama forma --> 
		<form method="post">
			<div class="form-group">
				<input name="nickname" type="text" class="form-control <?php echo (!empty($data['nicknameErr'])) ? 'is-invalid' : ''; ?>" placeholder="Nickname">
				<span class='invalid-feedback'><?php echo $data['nicknameErr'] ?></span>
			</div>

			<div class="form-group">
				<input name="password" type="password" class="form-control <?php echo (!empty($data['passwordErr'])) ? 'is-invalid' : ''; ?>" placeholder="Password">
				<span class='invalid-feedback'><?php echo $data['passwordErr'] ?></span>
			</div>

			<div class="form-group">
				<input name="confirmPassword" type="password" class="form-control <?php echo (!empty($data['confirmPasswordErr'])) ? 'is-invalid' : ''; ?>" placeholder="Confirm password">
				<span class='invalid-feedback'><?php echo $data['confirmPasswordErr'] ?></span>
			</div>

			<button type="submit" class="btn btn-primary">Register</button>
		</form>
	</div>
</div>

<!-- footeryje uzdaromas html'as ir body -->
<?php require APPROOT . '/views/inc/footer.php';