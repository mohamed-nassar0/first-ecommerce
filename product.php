<?php
session_start();


// check user id exist in link and is number or no
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 0;
}

// connect with Db
include 'connect.php';
// check if this item Exist in db
$chek_category_exist_or_no = $conn->prepare('SELECT * FROM `items` WHERE `item_id` = ?');
$chek_category_exist_or_no->execute(array($id));

if ($chek_category_exist_or_no->rowCount() > 0) {
 
// Page title
$page_name = $chek_category_exist_or_no->fetch()['item_name'];
include 'init.php'; // include initialize [Db, Routes, NavBar, Header, etc]

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
        $user    = $_SESSION['user_id'];
        $item    = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $date    = date('Y-m-d h:i:s');
        $errors  = array();

        if (empty($comment)) {
            $errors['COMMENT'] = 'Comment Field Cannot Be Empty';
        }

        if (empty($errors)) {
            $insert_the_comment = $conn->prepare('INSERT INTO `comments` (`comment`, `item`, `user`, `date`) VALUES (?, ?, ?, ?)');
            $insert_the_comment->execute(array($comment, $item, $user, $date));
            if ($insert_the_comment) {
                redirect('product.php?id=' . $id);
            }
        }

    }
}

// get product data 
$product_data = select_object('items', 'item_id', $id);

?>
<div class='container'> <!-- start container div -->
    <div class='product'> <!-- start product div -->
        <div class='row'> <!-- start row div -->

            <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>
                    <h3><?php echo $product_data['item_name']; ?></h3>
                    <p><?php echo nl2br($product_data['description']); ?></p>
                    <p class='category-and-uploader'>Uploader: <span><?php echo select_object('users', 'user_id', $product_data['uploader'])['user_name'];?></span></p>
                    <p  class='category-and-uploader'>Category: <span><?php echo select_object('categories', 'category_id', $product_data['category'])['category_name'];?></span></p>
                    <p class='date'><?php echo $product_data['date']; ?></p>
            <?php if (!empty($product_data['tags'])) {
                echo '<h4>Tags:</h4>';
                // tags from strgin to array then print it
                $tags_from_string_to_array = explode(',', $product_data['tags']);
                for ($number_of_tags =0;$number_of_tags<=count($tags_from_string_to_array) - 1;$number_of_tags++) {
                    echo '<a class="btn" href="tag.php?tag=' . $tags_from_string_to_array[$number_of_tags] . '">' . $tags_from_string_to_array[$number_of_tags] . '</a>';
                }
                } else {
                    echo '<p>No Tags</p>';
                } ?>
            </div>
            
            <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>
                 <img src='data/uploads/item_pictures/<?php echo $product_data['item_picture'];?>' alt='Product Picture' />
            </div>

        </div> <!-- end row -->
<?php 
if (isset($_SESSION['user_id'])) {
    $check_user_active_or_no = select_object('users', 'user_id', $_SESSION['user_id']);
    if ($check_user_active_or_no['active'] == 1) {
        ?>
    <!-- start add comment form -->
    <form method='post' action='<?php echo $_SERVER['PHP_SELF'] . '?id=' . $product_data['item_id']; ?>' class='comment-form'>
        <textarea type='text' name='comment' placeholder='leave your comment' ></textarea>
        <input type='submit' value='OK' />
        <?php 
            if (isset($errors['COMMENT'])) {
                    echo '<div class="alert alert-danger">' . $errors['COMMENT'] . '</div>';
            }
        ?>
    </form>
<!-- end add comment form -->
<?php 
    } else {
        echo '<p class="danger-message">You Cant Post Comment Until Administrator Active Your Account</p>';
    }

} else {
/* here if no session user_id 
   Add Comment Form Will Not Appear And Show This Message Instead   
*/
echo '<p>Sorry, But You are not Registered To Post Your Comment <br><a class="wrong-link" href="Login.php"> Login </a> And Try Again</p>';
}

$all_comments = all_data('comments', 'item = '  . $id, 'comment_id', 'DESC');

if (!empty($all_comments)) {

    foreach ($all_comments as $comments) {
        echo '<div class="comment-box">';
        echo '<p class="user-name"><a href="member.php?id=' . $comments['user'] . '">' . select_object('users', 'user_id', $comments['user'])['user_name'] . '</a></p>';

        echo '<p class="the-comment">' . nl2br($comments['comment']) . '</p>';
        echo '<p class="the-date">' . $comments['date'] . '</p>';

        echo '</div>'; // end comment box div
        echo '<div></div>';
    }

} else {
    echo '<p class="no-comment">No Comments!</p>';
}

?>

    </div> <!-- end product div -->
</div> <!-- end container div -->
<?php

include $template . 'footer.php';
} else {
    // when no item in data base
    echo '<div class="container margin-top-40 alert alert-danger">Not Item Found , Please Check URL And Try Again</div>';
}
