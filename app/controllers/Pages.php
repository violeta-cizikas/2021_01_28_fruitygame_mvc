<?php 
//////////////////////////////////////////////////
// sukuriama kontrolerio klase, nes naudojamas MVC
// extends - paveldimumas(praplecia)
class Pages extends Controller
{
	// userModel aktyvavimas 
	// deklaravimas, kad klase turi property
	private $userModel;
	// construct - ypatingas metodas vadinamas kontruktoriumi, nes, kai klase kuriama su raktazodziu new - iskvieciamas konstruktorius
	// konstruktorius naudojamas nustatyti pradinems klases property reiksmems
	public function __construct()
	{   
		// naudojamas metodas $this->model, kuris paveldetas is tevines klases Controller
		// ir sis metodas grazina Model, ir tuomet tas Model irasomas i sios klases property $this->userModel
		$this->userModel = $this->model('User');
	}
	///////////////////////////////////////////////
	// metodas index atsakingas uz psl atvaizdavima
	public function index()
	{
		// sukuriami duomenys, kad juos paduoti views
		$data = ['title' => 'Welcome to FRUITYGAME!!!'];
		// uzkraunamas views
		$this->view('pages/index', $data);
	}

	///////////////////////////////////////////////
	// kuriamas metodas, kuris atsakingas uz my_account psl
	public function my_account()
	{   // tikrinama ar vartotojas sprisijunges
		if (isset($_SESSION['user'])) {
			// apdorojama forma
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {               
				// vietoje $_POST atkoduojamas body kaip json, norint, kad pridėjimo-nuėmimo vieta butu be refresh
				// file_get_contents - php f-ja, kuri grazina failo turini
				$json = file_get_contents('php://input');
				// json_decode - paima JSON koduotą eilutę ir paverčia ją PHP kintamuoji
				$jsonData = json_decode($json, true);
				// sanitize Post Array
				// $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

				// sukuriama data 
				// trim istrina spaces'us is string'o pradzios arba galo
				// intval f-ja string'a pavercia i skaiciu
				$data = [
					'sum' => intval(trim($jsonData['sum'])),
					// laukelio sum klaidos / is pradziu tuscias, nes klaidu nera
					'sumErr' => '',
					// views'as nuluztu, jei data masyve nebutu user
					'user' => $_SESSION['user'],
				];

				// tikrinama, kad suma butu ne maziau 50
				if ($data['sum'] < 50) {
					// zinutes pranesimas
					$data['sumErr'] = 'Sum must be 50 or more';
					// tinkama suma
				} else {
					// tikrinama ar buvo paspausta deposit
					// tikrinama ar egzistuoja property 'deposit' $jsonData masyve ir ar jis yra truthy
					if (isset($jsonData['deposit']) && $jsonData['deposit']) {
						// pridedamas depozitas i nauja kintamaji (vartotojo balansa)
						// veliau bus irasoma i DB ir i sesija
						$newBalance = $_SESSION['user']['balance'] + $data['sum'];
						// atnaujinamas balansas vartojo DB
						$this->userModel->updateBalance($_SESSION['user']['nickname'], $newBalance);
						// irasomas naujas balansas i sesijos vidu
						$_SESSION['user']['balance'] = $newBalance;
						// kad psl iskart rodytu su nauju balansu
						$data['user']['balance'] = $newBalance;
					} else {
						// negalima nuimti daugiau nei turima
						if ($data['sum'] > $_SESSION['user']['balance']) {
							// zinutes pranesimas
							$data['sumErr'] = 'Can not withdraw more than you have';
							// tinkama suma isimti is saskaitos
						} else {
							$newBalance = $_SESSION['user']['balance'] - $data['sum'];
							$this->userModel->updateBalance($_SESSION['user']['nickname'], $newBalance);
							$_SESSION['user']['balance'] = $newBalance;
							$data['user']['balance'] = $newBalance;
						}
					}
				} 
				// header - http protokolo dalis
				// Content-Type nurodo narsyklei, kad turinio tipas - json
				header('Content-Type: application/json'); 
				// atspausdinami rezultatai json formatu
				echo json_encode($data);
				// sustabdomas php veikimas, nes jau visi rezultatai grazinti - tam, kad pries tai nepradedu rodyti views'o
				die();                
				// pirmakart atsidarant - taip pat reikia user'io, kad matytusi vardas ir balansas          
			} else {
				$data = ['user' => $_SESSION['user']];
			}
		// jei vartotojas neprisijunges - nukreipiama i login
		} else {
			redirect('/users/login');
			// grizta is f-jos ir si f-ja nebevyks toliau
			return;
		}
		// load the view
		$this->view('pages/my_account', $data);
	}

	/////////////////////////////////////////////////
	// kuriamas metodas, kuris atsakingas uz play psl
	public function play()
	{
		// zaidimo psl logika
		// ar prisijunges
		if (isset($_SESSION['user'])) {
			// ar apdorojama forma
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// sanitize Post Array
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				// create data 
				// trim istrina spaces'us is stringo pradzios arba galo
				// intval f-ja stringa pavercia i skaiciu
				$data = [
					'sum' => intval(trim($_POST['sum'])),
					'sumErr' => '',
					// views'as nuluztu, jei data masyve nebutu user
					'user' => $_SESSION['user'],
				];
				
				// minimali statymo suma 5
				if ($data['sum'] < 5) {
					$data['sumErr'] = 'Sum must be 5 or more';
					// jei suma didesne uz balansa
				} else if ($data['sum'] > $_SESSION['user']['balance']) {
					$data['sumErr'] = 'Can not bet more than you have';
					// jei suma tinkama - vyksta statymas
				} else {
					// pasikeites balansas po statymo
					$newBalance = $_SESSION['user']['balance'] - $data['sum'];                    
					// kintamasis nurodantis, kad vyks zaidimas
					$data['play'] = true;
					// korteliu atsitiktine tvarka generavimas
					$possibleCards = ['apple.png', 'grape.png', 'pineapple.png'];
					$cards = [];
					for($i = 0; $i < 9; $i++) {
						// su count - gaunamas masyvo ilgis
						$cardIndex = rand(0, count($possibleCards) - 1);
						$cards[] = $possibleCards[$cardIndex];
					} 
					// kuriamas kintamasis 
					$data['winAmount'] = 0;

					// JACKPOT!!!
					if ($cards[0] === $cards[1] && $cards[0] === $cards[2]
						&& $cards[3] === $cards[4] && $cards[3] === $cards[5]
						&& $cards[6] === $cards[7] && $cards[6] === $cards[8]){

						$newBalance += $data['sum'] * 3 * 5 * 3;
						$data['winAmount'] += $data['sum'] * 3 * 5 * 3;
						$data['win'] = true;

					} else {

						// pirmos eilutes laimejimu patikrinimas
						if ($cards[0] === $cards[1] && $cards[0] === $cards[2]) {
							$newBalance += $data['sum'] * 3;
							$data['winAmount'] += $data['sum'] * 3;
							$data['win'] = true;
						}

						// tikrinama ar vidurines linijos korteles vienodos
						if ($cards[3] === $cards[4] && $cards[3] === $cards[5]) {
							// vidurines linijos laimejimo atveju - padauginama is 3
							$newBalance += $data['sum'] * 5;
							$data['winAmount'] += $data['sum'] * 5;
							// laimejimo atveju
							$data['win'] = true;
						}
						// trecios eilutes laimejimu patikrinimas
						if ($cards[6] === $cards[7] && $cards[6] === $cards[8]) {
							$newBalance += $data['sum'] * 3;
							$data['winAmount'] += $data['sum'] * 3;
							$data['win'] = true;
						} 
					}

					$this->userModel->updateBalance($_SESSION['user']['nickname'], $newBalance);
					$_SESSION['user']['balance'] = $newBalance;
					$data['user']['balance'] = $newBalance;
					$data['cards'] = $cards;
				}
				$this->view('pages/play', $data);
			  // apdorojamas get metodas  
			} else {
				$data = ['user' => $_SESSION['user']];
				// load the view
				$this->view('pages/play', $data);
			}
		  // nukreipimas i login, jei neprisijunges  
		} else {
			redirect('/users/login');
		}      
	}
}
