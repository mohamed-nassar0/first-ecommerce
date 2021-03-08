<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $page_name = 'Edit Profile';
    include 'init.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
$user_name      = filter_var($_POST['user-name'], FILTER_SANITIZE_STRING);
$email          = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$new_password_1 = filter_var($_POST['new-password-1'], FILTER_SANITIZE_STRING);
$new_password_2 = filter_var($_POST['new-password-2'], FILTER_SANITIZE_STRING);
$old_password   = filter_var($_POST['old-password'], FILTER_SANITIZE_STRING);
$errors         = array();

// get admin data to check the old password correct or no
$admin_password = select_object('users', 'user_id', $_SESSION['user_id']);

// validate user name
if (strlen($user_name) < 6) {
    $errors['USER_NAME'] = 'User Name Must Be 6 Letters Or More';
}

if (empty($user_name)) {
    $errors['USER_NAME'] = 'User Name Cannot Be Empty';
}

// validate email
if (empty($email)) {
    $errors['EMAIL'] = 'Email Address Cannot Be Empty';
}

if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) {
    $errors['EMAIL'] = 'Email Address Isn\'t Valid';
}

if (check_object('users', 'email', $email . ' AND `user_id` != ' . $_SESSION['user_id']) > 0) {
    $errors['EMAIL'] = 'Email Address Used Before , Try Another One';
}

// validate new password
if (empty($new_password_1) && empty($new_password_2)) {

     $password = $admin_password['password'];
    
} else {

    if ($new_password_1 != $new_password_2) {
        $errors['PASSWORD'] = 'Password Not Match';
    } else {
        if (strlen($new_password_1) < 8) {
        $errors['PASSWORD'] = 'Password Must Be 8 Letters Or More';
        } else {
            $password = password_hash($new_password_1, PASSWORD_DEFAULT);
        }
    }

}

// validate old password to save data
if (!password_verify($old_password, $admin_password['password'])) {
    $errors['OLD_PASSWORD'] = 'Wrong Password';
}
if(empty($old_password)) {
    $errors['OLD_PASSWORD'] = 'Please Enter Your Password';
}

// update data
if (empty($errors)) {
    $update_data = $conn->Prepare('UPDATE
                                        `users`
                                   SET
                                        `user_name` = ?,
                                        `email` = ?,
                                        `password` = ?
                                   WHERE
                                        `user_id` = ?');

    $update_data->execute(array($user_name, $email, $password, $_SESSION['user_id']));

    if ($update_data) {
        redirect('profile.php');
    }

}

}

//if (isset($_SERVER['HTTP_REFERER'])){
// get page name in link
//$get_page_name = @strtolower(end(explode('/', $_SERVER['HTTP_REFERER'])));


// do cookie here value = profile.php for 10min
// get data to show in form fields
$admin_data = select_object('users', 'user_id', $_SESSION['user_id']);
?>
<h1 class='h1'>edit your profile</h1>   
<div class='container'>
    <form method='post' class='edit-profile-form'>
        <p>User Name</p>
        <!-- edit user name field -->
        <input type='text' name='user-name' placeholder='User Name' class='form-control' autocomplete='off' value='<?php echo $admin_data['user_name'];?>' />
        <?php 
            if (isset($errors['USER_NAME'])) {
                echo '<div class="alert alert-danger">' . $errors['USER_NAME'] . '</div>';
            }
        ?>

        <p>Email</p>
        <!-- edit email field -->
        <input type='text' name='email' placeholder='Email' class='form-control' value='<?php echo $admin_data['email'];?>' />
        <?php 
            if (isset($errors['EMAIL'])) {
                echo '<div class="alert alert-danger">' . $errors['EMAIL'] . '</div>';
            }
        ?>

        <p>Password</p>
        <!-- 2 fields for new password and retype new password -->
        <input type='password' name='new-password-1' placeholder='New Password' class='form-control' />
        <input type='password' name='new-password-2' placeholder='Retype New Password' class='form-control' />
        <?php 
            if (isset($errors['PASSWORD'])) {
                echo '<div class="alert alert-danger">' . $errors['PASSWORD'] . '</div>';
            }
        ?>

        <hr>
        <p>Old Password</p>
        <!-- old password to save data -->
        <input type='password' name='old-password' placeholder='Old Password' class='form-control' />
        <?php 
            if (isset($errors['OLD_PASSWORD'])) {
                echo '<div class="alert alert-danger">' . $errors['OLD_PASSWORD'] . '</div>';
            }
        ?>

    <input type='submit' value='Save' class='btn btn-info'>
    </form>
</div>

<?php



    include $template . 'footer.php';
} else {
    header('location:index.php');
    exit;
}