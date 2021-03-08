<?php
session_start();

// check link
if (isset($_GET['id']) && is_numeric($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 0;
}

// connect with db to check this category exist or no
include 'connect.php';

// select this category from db
$check_category = $conn->prepare('SELECT * FROM `categories` WHERE `category_id` = ?');
$check_category->execute(array($id));

if ($check_category->rowCount() > 0) { // if this category exist show the page

    // Page Title
    $page_name = 'Category ' . $check_category->fetch()['category_name'];

    // include Initialize [header, connect with db, navbar, routes, etc];
    include 'init.php';

    // select all items exist in ths category
    $all_data_in_this_category = all_data('items', 'category = ' . $id, 'item_name', 'ASC');
    
    // if there are items print 
    if (!empty($all_data_in_this_category)) {
        echo '<div class="margin-top-40"></div>'; // make margin top 40 px
        echo '<div class="container">'; // start container div
            echo '<div class="row">'; // start row div
            foreach ($all_data_in_this_category as $item) {
                echo '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">';
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
        echo '</div>'; // end container div

        include $template . 'footer.php';
    } else {
        echo '<div class="container margin-top-40 alert alert-danger">No Items Found</div>';
    }

} else {
    echo '<div class="container margin-top-40 alert alert-danger">No Items Found</div>';
}







