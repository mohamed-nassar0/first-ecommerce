<?php
session_Start();

if (isset($_SESSION['user_id'])) {

//page title
$page_name = 'Members Search';
include 'init.php';
?>
<!-- 
= In This Page User Can Search Another Members 
=  Search By User Name
-->

<!-- start search box -->
<div class='container'>
    <div class='search-box'>
        <form method='get' action='members.php' class='search-box'>
            <input type='text' name='name' placeholder='Search' />
            <input type='submit' value='Search' />
        </form>
    </div>
</div>
<!-- end search box -->
<div class='container'>
    <?php 
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $user = filter_var($_GET['name'], FILTER_SANITIZE_STRING);
    } else {
        $user = 'empty value';
    }
   

    $all_users_data = all_data('users', '`user_name` LIKE "%' . $user . '%"', 'user_id', 'ASC');
    
    if (!empty($all_users_data)) {
        foreach ($all_users_data as $member) {
            echo '<div class="members-box">';
                echo '<img src="data/profile_pictures/' . $member['profile_picture'] . '" alt="Member Image" />';
                echo '<p><a href="member.php?id=' . $member['user_id'] . '">' . $member['user_name'] . '</a></p>';
            echo '</div>';
        }

    } else {
        echo '<div class="alert alert-danger margin-top-40">Not Members Found</div>';
    }
    
    ?>

</div>

<?php

// include footer file
include $template . 'footer.php';

} else {
    header('location:login.php');
    exit;
}
