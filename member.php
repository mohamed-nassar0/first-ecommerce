<?php
session_start();

    if (isset($_GET['id']) && is_numeric($_GET['id'])){
        $the_member_id = $_GET['id'];
    } else {
        $the_member_id = 0;
    }

    // connect with db to show member data and check if this user exist or no
    include 'connect.php';

    $user_data = $conn->prepare('SELECT * FROM `users` WHERE `user_id` = ?');
    $user_data->execute(array($the_member_id));
    $count = $user_data->rowCount(); // get counter if in db exist or no
    $the_user_data = $user_data->fetch(); // get user data

    if ($count > 0) {
        
        // page title
        $page_name = $user_data->fetch()['user_name'] . ' Profile';

        include 'init.php';
if (isset($_SESSION['user_id'])) {

           $check_block_sysyem = $conn->prepare('SELECT
                                                        *
                                                 FROM
                                                        `block`
                                                 WHERE
                                                        `first_user` = ?
                                                 AND
                                                         `second_user` = ?
                                                 OR
                                                        `second_user` = ?
                                                 AND
                                                        `first_user` = ?');
            $check_block_sysyem->execute(array($_SESSION['user_id'], $the_member_id, $_SESSION['user_id'], $the_member_id));
if ($check_block_sysyem->rowCount() == 0) {
?>
<div class='container'>
<!-- start profile cover and profile picture -->
<div class='profile-cover' style='background:url("data/profile_cover/<?php echo 'cover.jpg'; ?>") center center'>
   <div class='profile-picture' style='background:url("data/profile_pictures/<?php echo $the_user_data['profile_picture']; ?>") center center'>
    </div>
</div>
<!-- end profile cover and profile picture -->

<!-- start profile data -->
<div class='profile-data'> <!-- start profile data div -->
    <div class='row'> <!-- start row div -->
        <div class='col-xs-12 col-sm-12 col-md-9 col-lg-9'>
            <h3><?php echo $the_user_data['user_name'];?></h3>
            <p><?php echo $the_user_data['email'];?></p>
            <p>Register Date: <?php echo $the_user_data['register_date'];?></p>
        </div>

        <!-- start block this member button -->
        <?php if (isset($_SESSION['user_id'])) {
            ?>
            <div class='col-xs-12 col-sm-12 col-md-3 col-lg-3'>
                <div class='profile-settings-block-btn'> <!-- start profile settings btn div -->
                    <a href='member.php?id=<?php echo $the_member_id . '&block_page=block' ?>'>Block</a>
                </div>
                <div class='fix'></div>
            </div> <!-- end profile settings btn div -->
        <?php
        // start block system 
        // here you can block another users
        if (isset($_GET['block_page']) && $_GET['block_page'] == 'block') {
            $errors = array();
            $check_block_system_from_me = $conn->prepare('SELECT * FROM `block` WHERE `first_user` = ? AND `second_user` = ?');
            $check_block_system_from_me->execute(array($_SESSION['user_id'], $the_member_id));
            if ($check_block_system_from_me->rowCount() > 0) {
                $errors['BLOCK_IS_EXIST'] = 'You Have Blocked This Member Before';
            }

            $check_block_system_from_him = $conn->prepare('SELECT * FROM `block` WHERE `first_user` = ? AND `second_user` = ?');
            $check_block_system_from_him->execute(array($the_member_id, $_SESSION['user_id']));
            if ($check_block_system_from_him->rowCount() > 0) {
                $errors['BLOCK_IS_EXIST'] = 'This Member Has Blocked You';
            }

            if (empty($errors)) {
                $block_this = $conn->prepare('INSERT INTO `block` (`first_user`, `second_user`, `date`) VALUES (?, ?)');
                $block_this->execute(array($_SESSION['user_id'], $the_member_id, date('Y-m-d h:i:s')));
                if ($block_this) {
                    redirect('block_list.php');
                }
            }


        }

        }
        ?>
        <!-- end block this member button -->

    </div> <!-- end row div -->
</div> <!-- end profile data div -->
<!-- end profile data -->

<!-- start my items -->
<div class='my-items'>
    <h3>Items</h3>
<?php
if (!empty(all_data('items', 'uploader = ' . $the_member_id))) {
    echo '<div class="row">'; // start row div
    foreach (all_data('items', 'uploader= ' . $the_member_id, 'item_id', 'DESC') as $item) {
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
    echo 'This Member Haven\'t Uploaded Items Yet';
}
?>
</div>
<!-- end my items -->

</div> <!-- end container div -->
<?php
} else {
    echo '<div class="container margin-top-40 alert alert-danger">You Cant See This Page</div>';
}


} // if isset session


// include footer files
include $template . 'footer.php';
    } else {

    }