<?php 
session_start();
// page name
$page_name = 'Admin Login Page';

// no nav bar in this page
$no_nav = '';

// include init file
include 'init.php';

// check if the user are logged in or no
if (isset($_SESSION['user_id'])) {
	redirect("index.php");
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if (isset($_POST['login'])) { // start login
		$login_id 		 = filter_var($_POST['login_id'], FILTER_SANITIZE_EMAIL);
		$password 		 = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
		$errors 		 = array();

		$select_data = $conn->prepare("SELECT * FROM
												`users` 
										WHERE 
												`email` = ?");

			$select_data->execute(array($login_id));
			$select_user_email = $select_data->rowCount();
			$user_data = $select_data->fetch();

		if (empty($login_id)) {

				$errors['EMAIL'] = 'Enter Your Email';

			} else {

				if (filter_var($login_id, FILTER_VALIDATE_EMAIL) == FALSE) {

					$errors['EMAIL'] = 'Email Not Valid!';
					
				} else {
			
					if (($select_user_email == 0)) {
			
						$errors['EMAIL'] = 'Email Address Not Registered';
						
					} else {
			
						if (password_verify($password, $user_data['password']) == FALSE) {

							$errors['PASSWORD'] = 'Wrong Password';
			
						}

					}
			
				} 
			} // end line 45
			

			if (empty($errors)) {
				$_SESSION['user_id'] = $user_data['user_id'];
				redirect('index.php');
			}

	} // end login


	if (isset($_POST['register'])) { 
	// start register data from form to data base to check this user are registerd or no

		$first_name		  = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
		$last_name 		  = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
		$email     		  = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		$password 		  = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
		$retype_password  = filter_var($_POST['retype_password'], FILTER_SANITIZE_STRING);
		$full_name 	      = $first_name . ' ' . $last_name;
		$errors    		  = array();

		// check first name input field
		if (strlen($first_name) < 3) {
			$errors['FIRST_NAME'] = 'Your Name Must Be 3 Letters Or More';
		}

		if (strlen($first_name) > 15) {
			$errors['FIRST_NAME'] = 'Your Name Must Be 15 Letters Or Less';
		}

		if (empty($first_name)) {
			$errors['FIRST_NAME'] = 'Please Enter Your Name';
		}

		// check last name input field
		if (strlen($last_name) < 3) {
			$errors['LAST_NAME'] = 'Your Last Name Must Be 3 Letters Or More';
		}

		if (strlen($last_name) > 15) {
			$errors['LAST_NAME'] = 'Your Last Name Must Be 15 Letters Or Less';
		}

		if (empty($last_name)) {
			$errors['LAST_NAME'] = 'Please Enter Your Last Name';
		}

		// check email address input field
		if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) {
			$errors['REGISTER_EMAIL'] = 'Email Address Is Not Valid';
		}

		if (empty($email)) {
			$errors['REGISTER_EMAIL'] = 'Enter Your Email';
		}

		if (check_object('users', 'email', $email) > 0) {
			$errors['REGISTER_EMAIL'] = 'Email Address Used Before, Try Another One';
		}

		if (!empty($password) && !empty($retype_password)) {

			if ($password != $retype_password) {
				$errors['REGISTER_PASSWORD'] = 'Password Not Match';
			} else {

				if (strlen($password) < 8) {
					$errors['REGISTER_PASSWORD'] = 'Password Must Be 8 Letters Or More';
				} else {
					$password_to_insert_to_db = password_hash($password, PASSWORD_DEFAULT);

				}

			}

		} else {
			$errors['EMPTY_PASSWORD'] = 'Complete Your Password';
			if (empty($password)) {
				$the_empty_password_1 = '';
			}
			if (empty($retype_password)) {
				$the_empty_password_2 = '';
			}
		}

		if (empty($errors)) {
			// in no errors in register form send data to db
			$insert_user_data_to_db = $conn->prepare('INSERT INTO `users` (`user_name`, `email`, `password`, `admin`, `profile_picture`, `cover_picture`, `active`, `register_date`) VALUES (?,?,?,?,?,?,?,?)');
			$insert_user_data_to_db->execute(array($full_name, $email, $password_to_insert_to_db, 0, 'img.png', 'cover.jpg', 0, date('Y-m-d h:i:s')));
			if ($insert_user_data_to_db) {
				setcookie('WELCOME_MESSAGE', 'welcome message', time() + 600, '/', '127.0.0.1');
				redirect('login.php');
			}

		}
	} // end register


} // end SERVER['REQUEST_METHOD']
?>
<!-- start login anad register form -->
<div class='container'>
	<div class='login-and-register-boxes'>

	<div class='login-and-register-buttons'>
		<button class='login'>Login</button> OR
		<button class='register'>Register</button>
	</div>
	<?php 
	if (isset($_COOKIE['WELCOME_MESSAGE'])) {
		echo '<div class="alert alert-success welcome-message-in-login">Congratulations You Can Login Now</div>';
	}
	?>
	<!-- start login form -->
			<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' class='login-form'>

			<!-- Emaill Address Field -->
			<input type='text' name='login_id'  placeholder='user name or emali' class='form-control' />
			<?php 
				if (isset($errors['EMAIL'])) {
					echo '<div class="alert alert-danger">' . $errors['EMAIL'] . '</div>';
				}
			?>

			<!-- Password Field -->
			<input type='password' name='password' placeholder='password' class='form-control' />
			<?php 
				if (isset($errors['PASSWORD'])) {
					echo '<div class="alert alert-danger">' . $errors['PASSWORD'] . '</div>';
				}
			?>	

			<input type='submit' name='login' value='login' class='btn btn-danger btn-block' />
		</form>
	<!-- end login form -->

	<!-- start register form -->
		<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' class='register-form'>

			<!-- First Name Field -->
			<input type='text' name='first_name'  placeholder='First Name' class='form-control' />
			<?php 
				if (isset($errors['FIRST_NAME'])) {
					echo '<div class="alert alert-danger">' . $errors['FIRST_NAME'] . '</div>';
				}
			?>

			<!-- Last Name Field -->
			<input type='text' name='last_name'  placeholder='Last Name' class='form-control' />
			<?php 
				if (isset($errors['LAST_NAME'])) {
					echo '<div class="alert alert-danger">' . $errors['LAST_NAME'] . '</div>';
				}
			?>

				<!-- Email Address Field -->
				<input type='email' name='email'  placeholder='Email Address' class='form-control' />
			<?php
				if (isset($errors['REGISTER_EMAIL'])) {
					echo '<div class="alert alert-danger">' . $errors['REGISTER_EMAIL'] . '</div>';
				}
			?>

			<!-- Password Field -->
			<input type='password' name='password' placeholder='Password' class='form-control' />
			<?php 			
				if (isset($errors['EMPTY_PASSWORD']) && isset($the_empty_password_1)) {
					echo '<div class="alert alert-danger">' . $errors['EMPTY_PASSWORD'] . '</div>';
				}
			?>

			<!-- Retype Password Field -->
			<input type='password' name='retype_password' placeholder='Retype Password' class='form-control' />
			<?php 
				if (isset($errors['REGISTER_PASSWORD'])) {
					echo '<div class="alert alert-danger">' . $errors['REGISTER_PASSWORD'] . '</div>';
				}

				if (isset($errors['EMPTY_PASSWORD']) && isset($the_empty_password_2)) {
					echo '<div class="alert alert-danger">' . $errors['EMPTY_PASSWORD'] . '</div>';
				}
			?>

			<input type='submit' name='register' value='Register' class='btn btn-primary btn-block' />
		</form>
	<!-- end register form -->
	</div> <!-- <<< end container div -->
</div> <!-- <<< end login-and-register-boxes div -->
<!-- end login anad register form -->
<?php
// include footer file
include $template . 'footer.php';