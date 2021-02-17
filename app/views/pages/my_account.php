<?php
require APPROOT . '/views/inc/header.php'; ?>

<!-- jumbotron - demonstracine bootstrap'o klase -->
<div class="jumbotron jumbotron-fluid">
	<div class="container">
		<h1>Hello, <?php echo $data['user']['nickname']?></h1>
		<p id="userBalance">Your balance: $<?php echo $data['user']['balance']?></p>
		<!-- kuriama forma / prirasomas metodas ir name / buttonai pakeiciami i inputus+name(nes apdorojant forma negalima suzinoti, kuris mygtukas buvo paspaustas vartotojo su pele, o, kai nauojami inputai - galima suzinoti pgl name)-->
		<!-- pasirasius scripta, - is formos istrinamas method="post" -->
		<form>
			<div class="form-group">
				<!-- pridedamas php, span, id -->
				<input id='sum' name="sum" type="text" class="form-control <?php echo (!empty($data['sumErr'])) ? 'is-invalid' : ''; ?>" placeholder="Enter sum">
				<span class='invalid-feedback'><?php echo $data['sumErr'] ?></span>
			</div>

			<!-- uzdedami onclick atributai ant abieju input'u, ko pasekoje paspaudus juos pele, bus iskvieciama JS f-ja -->
			<input onclick="submitForm(event, 'deposit')" name="deposit" type="submit" class="btn btn-primary" value='Add deposit'>
			<input onclick="submitForm(event, 'withdraw')" name="withdraw" type="submit" class="btn btn-primary" value='Withdraw'>
		</form>
	</div>

	<!-- pinigų pridėjimo/nuėmimo vieta be refresh'o -->
	<script>
		function submitForm(event, buttonName) {
			event.preventDefault();
			// naujas kintamasis ir duomenys siunciami i bekenda
			let formDataForBackend = {
				//sum inputo name
				sum: document.getElementById('sum').value,
				deposit: buttonName == 'deposit',
			};
			// window.location - dabartinis narsykles adresas
			fetch(window.location, {
				method:'POST',
				// paima data objekta, pavercia i json ir fetchas sius json kaip body http uzklausos
				body: JSON.stringify(formDataForBackend),
			}) 
			.then(function (renponse) {
				return renponse.json();
			})
			.then(function ($jsonDataFromBackend) {
				// istraukiamas input'as
				let sumInput = document.getElementById('sum');

				// tikrinama ar backend'as grazino klaidu
				if($jsonDataFromBackend.sumErr){
					// rodomas klaidos pranesimas
					// ant input'o uzdedama klase, kuri bootstrape'e reiskia kad inputas turi klaidu
					 sumInput.className += ' is-invalid';
					// istraukiamas html span elementas, skirtas 'sum' laukelio klaidoms rodyti
					let errorSpan = document.getElementsByClassName('invalid-feedback')[0];
					// irasome i span klaidos pranesima is backend'o
					errorSpan.innerHTML = $jsonDataFromBackend.sumErr;
				} else {
					// jeigu backendas negrazino klaidos
					// reikia panaikinti 'is-invalid' klase ant input'o
					sumInput.classList.remove('is-invalid');
					// istraukiamas elementas pagal id, kuris naudojamas rodyti user balansa
					let userBalance = document.getElementById('userBalance');
					// pakeiciama zinute su nauju balansu is backendo
					userBalance.innerHTML = `Your balance: $${ $jsonDataFromBackend.user.balance }`;
				}
			});    
		}
	</script>
</div>

<!-- footeryje uzdaromas html'as ir body -->
<?php require APPROOT . '/views/inc/footer.php';