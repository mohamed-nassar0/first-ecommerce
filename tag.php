<?php
session_start();

$page_name = 'Tag ' . $_GET['tag'];
include 'init.php';
if (isset($_GET['tag']) && !empty($_GET['tag'])) {
    $tag = $_GET['tag'];
} else {
    $tag = 'emptyyyyy';
}
?>
<!-- start search box -->
<div class='container'>
    <div class='search-box'>
        <form method='get' action='tag.php' class='search-box'>
            <input type='text' name='tag' placeholder='Search' />
            <input type='submit' value='Search' />
        </form>
    </div>
</div>
<!-- end search box -->

<div class='margin-top-40'></div> <!-- to make margin top 40px -->
<?php

$all_items = all_data('items');
echo '<div class="container">'; // start container div
    echo '<div class="row">'; // start row div
    foreach ($all_items as $my_search_item) {
        if (in_array(strtolower($tag), explode(',', strtolower($my_search_item['tags'])))) {
            $exist = '';
            echo '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">';
                echo '<div class="item-box">';

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
    echo '<div class="container alert alert-danger">No Items Found</div>';
}




include $template . 'footer.php';
