<?php
session_start();

if (isset($_SESSION['user_id']) && $_SESSION['admin'] == 1) {
$page_name = 'Tag ' . $_GET['tag'];
include 'init.php';
if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];
} else {
    $tag = '';
}

$all_items = all_data('items');
echo '<div class="container">'; // start container div
    echo '<div class="row">'; // start row div
    foreach ($all_items as $my_search_item) {
        if (in_array(strtolower($tag), explode(',', strtolower($my_search_item['tags'])))) {
            $exist = '';
            echo '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">';
                echo '<div class="item-box">';

                echo '<div class="control-item">';
                    echo '<a class="btn btn-info" href="items.php?page=edit&id=' . $my_search_item['item_id'] . '">Edit</a>';
                    echo '<a class="btn btn-danger" href="items.php?page=delete&id=' . $my_search_item['item_id'] . '">Delete</a>';
                echo '</div>'; // end control-item div  

                echo '<img src="data/uploads/item_pictures/' . $my_search_item['item_picture'] . '" alt="Item Picture" />';    
                echo '<h3><a href="product.php?id=' . $my_search_item['item_id'] . '">' . $my_search_item['item_name'] . '</a></h3>';
                echo '<p class="date">' . $my_search_item['date'] .'</p>';
                echo '<p>' . substr($my_search_item['description'], 0, 50);
                        
                if (strlen($my_search_item['description']) >= 50) {
                    echo '...';
                }
                        echo '</p>';
                    echo '</div>'; // end item-box div
                echo '</div>';
        }
    }
    echo '</div>'; // end row div
echo '</div>'; // end container div
if (!isset($exist)) {
    echo 'No Results Exist';
}


foreach (all_data('items', 'uploader= ' . $_SESSION['user_id'], 'item_id', 'DESC') as $item) {
   
    
}



include $template . 'footer.php';
} else {
    header('location:../index.php');
    exit;
}