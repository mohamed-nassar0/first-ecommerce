<?php
session_start();

if (isset($_SESSION['user_id'])) {
// page title
$page_name = 'Block List';

// include initialize [haeder, nav, connection, routes]
include 'init.php';
$block_list = all_data('block', '`first_user` = ' . $_SESSION['user_id'], 'date', 'DESC');

if (!empty($block_list)) {
    echo '<h1 class="h1">block list</h1>';

    echo '<div class="margin-top-40">'; // start margin top 40 div
    
    echo '<div class="container">'; // start container div
    foreach ($block_list as $list) {
        $select_user_data = select_object('users', 'user_id', $list['second_user']); 
        echo '<div class="block-list">'; // start block list div
            echo '<img src="data/profile_pictures/' . $select_user_data['profile_picture'] . '" /> <p>' . $select_user_data['user_name'] . '</p>';
       
        echo '<div class="control">'; // start control div
            echo '<a href="link.php" class="btn btn-info">Unblock</a>';
        echo '</div>'; // end control div

        echo '</div>'; // end block list div
    }
    echo '</div>'; // end contaienr div
    echo '</div>'; // end margin top 40 div
} else {
    echo '<div class="container margin-top-40 alert">List Is Empty</div>';
}



// include footer file
include $template . 'footer.php';
} else {
    header('location:index.php');
    exit;
}