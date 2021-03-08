<?php
session_start();
$page_name = 'Ecommerce Shop';

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
// Show All Item In Main Page
if (all_data('items', NULL, NULL, NULL, 'rowCount') > 0) {
    echo '<div class="margin-top-40">'; // start margin top 40
    echo '<div class="container">'; // start container div
        echo '<div class="row">'; // start row div
        foreach (all_data('items', NULL, 'item_id', 'DESC') as $item) {
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
    echo '<div class="alert alert-danger container margin-top-40">No Items Found</div>';
}







include $template . 'footer.php';