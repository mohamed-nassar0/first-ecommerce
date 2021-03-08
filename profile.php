<?php
session_start();

if (isset($_SESSION['user_id'])) {
$page_name = 'My Profile';
include 'init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

// change cover system
$input_name         = $_FILES['change-cover'];
$picture_name       = $input_name['name'];
$picture_tmp_name   = $input_name['tmp_name'];
$picture_error_code = $input_name['error'];
$picture_size       = $input_name['size'];
$errors             = array();
$allowed_extension  = array('jpg', 'jpeg', 'png', 'gif');


if (!in_array(strtolower(@end(explode('.', $picture_name))), $allowed_extension)) {
    $errors[] = 'Picture Not Valid';
}

if (empty($picture_name)) {
    $errors[] = 'Please Choose Your Picture';
}

if ($picture_size > 1048576) {
    $errors[] = 'Picture Size Must Be Less Than 1MB';
}

if (empty($errors)) {

    $random_picture_name = 'ecommerce_' . strtolower(select_object('users', 'user_id', $_SESSION['user_id'])['user_name']) . '_' . rand(1, 100000000) . '.' . strtolower(end(explode('.', $picture_name)));

    $insert_cover = $conn->prepare('UPDATE `users` SET `cover_picture` = ? WHERE `user_id` = ?');

    $insert_cover->execute(array($random_picture_name, $_SESSION['user_id']));

    move_uploaded_file($picture_tmp_name, 'data/profile_cover/' . $random_picture_name);

    if (insert_cover) {
        redirect('profile.php');
    }

}



} // end change cover system

?>
<div class='container'>

<!-- start profile cover and profile picture -->
<div class='profile-cover' style='background:url("data/profile_cover/<?php echo select_object('users', 'user_id', $_SESSION['user_id'])['cover_picture']; ?>") center center'>
   <div class='profile-picture' style='background:url("data/profile_pictures/<?php echo select_object('users', 'user_id', $_SESSION['user_id'])['profile_picture']; ?>") center center'>
    </div>
 <i class='fa fa-exchange'></i>
</div>
<!-- end profile cover and profile picture -->

<!-- start change cover box -->
<div class='change-cover'>
    <p>Change Your Cover Picture</p>
    <form method='post' action='<?php echo $_SERVER['PHP_SELF'];?>' enctype='multipart/form-data'>
        <input type='file' name='change-cover' />
        <input type='submit' value='Change Cover' class='btn btn-info' />
        <i class='fa fa-close'></i>
    </form>

</div> <!-- end container div -->
<!-- end change cover box -->

<!-- start errors div -->
<!-- end errors div -->


<!-- start profile data -->
<div class='profile-data'> <!-- start profile data div -->
    <div class='row'> <!-- start row div -->
        <div class='col-xs-12 col-sm-12 col-md-9 col-lg-9'>
            <h3><?php echo select_object('users', 'user_id', $_SESSION['user_id'])['user_name'];?></h3>
            <p><?php echo select_object('users', 'user_id', $_SESSION['user_id'])['email'];?></p>
            <p>Register Date: <?php echo select_object('users', 'user_id', $_SESSION['user_id'])['register_date'];?></p>
        </div>

        <div class='col-xs-12 col-sm-12 col-md-3 col-lg-3'>
            <div class='profile-settings-btn'> <!-- start profile settings btn div -->
                <button>Edit Profile</button>
                <ul>
                <a href='edit_profile.php'> <li>Edit Profile</li></a>
                <a href='block_list.php'>  <li>Block List</li></a>
                </ul>
            </div> <!-- end profile settings btn div -->
            <div class='fix'></div>
        </div> <!-- end col-xs-12 col-sm-12 -->
    </div> <!-- end row div -->
</div> <!-- end profile data div -->
<!-- end profile data -->


<?php
if (isset($errors)) {
    echo '<div class="error-box">';
        for ($number = 0; $number < count($errors); $number++) {
            echo '<div class="alert alert-danger">' . $errors[$number] . '</div>';
        }
        echo '<i class="fa fa-close"></i>';
    echo '</div>';
}
?>



<!-- start my items -->
<div class='my-items'>
    <h3>My Items</h3>
<?php 
if (!empty(all_data('items', 'uploader = ' . $_SESSION['user_id']))) {
    echo '<div class="row">'; // start row div

    $select_all_items = $conn->prepare('SELECT items.*, users.* FROM `items` INNER JOIN `users` ON items.uploader = users.user_id WHERE `user_id` = ?');
    $select_all_items->execute(array($_SESSION['user_id']));


    foreach ($select_all_items->fetchAll() as $item) {
        echo '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">';
            echo '<div class="item-box">';

                echo '<img src="data/uploads/item_pictures/' . $item['item_picture'] . '" alt="Item Picture" />';    
                echo '<h3><a href="product.php?id=' . $item['item_id'] . '">' . $item['item_name'] . '</a></h3>';
                echo '<p class="date">' . $item['date'] .'</p>';

                echo '<p>' . substr($item['description'], 0, 50);
                 
                if (strlen($item['description']) >= 50) {
                    echo '...';
                }
                echo '</p>';
            echo '</div>'; // end item-box div
        echo '</div>';
    }
    echo '</div>'; // end row div

} else {
    echo 'You Haven\'t Uploaded Items Yet';
}
?>

</div>

<!-- end my items -->


</div> <!-- end container div -->

<?php
// include footer file
include $template . 'footer.php';
} else {
    header('location:../index.php');
    exit;
}