<?php
session_start();
$page_name = 'Categories';

include 'init.php';
?>

<!-- start search box -->
<div class='container'>
    <div class='search-box'>
        <form method='get' action='categories.php' class='search-box'>
            <input type='text' name='name' placeholder='Search' />
            <input type='submit' value='Search' />
        </form>
    </div>
</div>
<!-- end search box -->

<?php

if (isset($_GET['name']) && !empty($_GET['name'])) {
    $search_category_name = '`category_name` LIKE "%' . $_GET['name'] . '%" ';
} else {
    $search_category_name = NULL;
}

$all_categories = all_data('categories', $search_category_name, 'category_name', 'ASC');

if (!empty($all_categories)) {
    echo '<div class="container">'; // start container div
        foreach ($all_categories as $category) {
            echo '<a class="category-link-btn" href="category.php?id=' . $category['category_id'] . '">';
                echo '<div class="category-box">'; // start category box
                    echo $category['category_name'];
                echo '</div>';    
            echo '</a>';
        }
    echo '</div>'; // end container div
} else {
    echo '<div class="container margin-top-40 alert alert-danger">No Categories Found</div>';
}




include $template . 'footer.php';