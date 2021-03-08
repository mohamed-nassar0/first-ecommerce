<?php
session_start();

// check search letter in link
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = filter_var($_GET['search'], FILTER_SANITIZE_STRING);
} else {
    $search = 'empty';
}

$page_name = 'Search [ ' . filter_var($search, FILTER_SANITIZE_STRING) . ' ]';
include 'init.php';

?>
<!-- start search box -->
<div class='container'>
    <div class='search-box'>
        <form method='get' action='search.php' class='search-box'>
            <input type='text' name='search' placeholder='Search' />
            <input type='submit' value='Search' />
        </form>

    </div>
</div>
<!-- end search box -->

<?php
$search_data_row_count = all_data('items', '`item_name` LIKE "%' . $search . '%" ', NULL, NULL, 'rowCount');

if ($search_data_row_count > 0) {
    echo '<div class="margin-top-40">'; // start margin top 40
    echo '<div class="container">'; // start container div
        echo '<div class="row">'; // start row div
        foreach (all_data('items', 'item_name LIKE "%' . $search . '%" ', 'item_id', 'DESC') as $item) {
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
    echo '</div>'; // end margin top 40 div
} else {
    echo '<div class="container margin-top-40 alert alert-danger">Not Items Found</div>';
}


include $template . 'footer.php';