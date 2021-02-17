<?php
//////////////////////////////////////////////////
// sukuriama kontrolerio klase Users
class Users extends Controller
{
	private $userModel;

	public function __construct()
	{
		$this->userModel = $this->model('User');
	}

	//////////////////////////////////////////////////
	// kuriamas register
	public function register()
	{
		// echo 'Register in progress';
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// sanitize Post Array
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

			// create data 
			$data = [
				'nickname'      => trim($_POST['nickname']),
				'password'  => trim($_POST['password']),
				'confirmPassword' => trim($_POST['confirmPassword']),
				'nicknameErr'      => '',
				'passwordErr'  => '',
				'confirmPasswordErr' => '',
			];

			// Validate nickname 
			if (empty($data['nickname'])) {
				// empty field
				$data['nicknameErr'] = "Please enter Your Nickname";
			} else {
				// check if nickname already exists
				if ($this->userModel->findUserByNickname($data['nickname'])) {
					$data['nicknameErr'] = "Nickname already taken";
				}
			}

			if (empty($data['password'])) {
				$data['passwordErr'] = "Please enter Your Password";
			} elseif (strlen($data['password']) < 4) {
				$data['passwordErr'] = "Please enter atleast 4 symbols";
			}

			if ($data['password'] != $data['confirmPassword']) {
				$data['confirmPasswordErr'] = "Passwords should match";
			}

			// if there is no erros
			if (empty($data['nicknameErr']) && empty($data['passwordErr']) && empty($data['confirmPasswordErr'])) {

				// hash password // safe way to store pass (pvz. atsitiktinis simboliu rinkinys: '$2y$10$3R/vOrPgB.z.1gVjvavn1OrsYZDRsHC1ROK0t0S78Fx7fccxW5rMy')
				$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

				if ($this->userModel->register($data)) {
					// success user added 
					// set flash msg
					flash('register_success', 'You have registered successfully');
					// header("Location: " . URLROOT . "/users/login");
					redirect('/users/login');
				} else {
					die('Something went wrong in adding user to db');
				}

			} else {
				// set flash msg
				flash('register_fail', 'please check the form', 'alert alert-danger');
				// load view with errors 
				$this->view('pages/register', $data);
			}

		} else {
			// kuriamas $data, nes i view bus paduodami atvaizduojami duomenys
			$data = [];
			// view() uzkrauna html turini
			$this->view('pages/register', $data);
		}    
	}

	//////////////////////////////////////////////////
	// kuriamas login
	public function login()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// sanitize Post Array
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

			// create data 
			// trim istrina spaces'us is stringo pradzios arba galo
			$data = [
				'nickname' => trim($_POST['nickname']),
				'password' => trim($_POST['password']),
				'nicknameErr' => '',
				'passwordErr' => '',
			];

			// rezultatas irasomas i kintamaji
			$user = $this->userModel->findUserByNickname($data['nickname']);
			// patikrinimas ar user nerastas
			if ($user === false) {
				$data['nicknameErr'] = "User does not exist";
			} else {

				// ar sutampa paswordai, jei sutampa - php laikinai nukilinamas
				// password_verify - tikrina ar su'hash'intas password'as DB sutampa su ivestu password'u
				if (password_verify($data['password'], $user['password'])) {
					// die('ok');
					// isimenama, kad vartotojas yra prisijunges
					$_SESSION['user'] = $user;
					// nurodomas adresas kur nukreipiama
					redirect('/pages/my_account');
				// jei nesutampa - klaidos pranesimas
				} else {
					$data['passwordErr'] = "Password incorrect";
				}
			}
		} else {
			// create some data to load into view
			$data = [];
		}        
		// view() - uzkrauna html turini
		$this->view('pages/login', $data);
	}

	//////////////////////////////////////////////////
	// kuriamas logout
	public function logout()
	{
		$_SESSION['user'] = null;
		// sesijos istrynimas
		session_destroy();
		// nukreipimas
		redirect('/users/login');
	}
}