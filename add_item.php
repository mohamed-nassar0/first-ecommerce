<?php
session_start();


if (isset($_SESSION['user_id'])) {

    // page title
    $page_name = 'Add New Item';

    // include initialize [header, navbar, connection, routes]
    include 'init.php'; 

    // start check item data and insert it to db
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $item_name     = filter_var($_POST['item-name'], FILTER_SANITIZE_STRING);
        $description   = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $tags          = filter_var($_POST['tags'], FILTER_SANITIZE_STRING);
        $category      = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
        $errors        = array();
    
        // check item name field
        if (strlen($item_name) < 5) {
            $errors['ITEM_NAME'] = 'Item Name Must Be 5 Letters Or More';
        }
    
        if (empty($item_name)) {
            $errors['ITEM_NAME'] = 'Item Name Cannot Be Empty';
        }
        
        // check category field
        if (!is_numeric($category)) {
            $errors['CATEGORY'] = 'You Have Choosen Wrong Value';
        } else {
            // get all categories id
            foreach (all_data('categories') as $cat) {
                $categories[] = $cat['category_id'];
            } 
            if (!in_array($category, $categories)) {
                $errors['CATEGORY'] = 'This Category Not Valid';
            }
        }
    
        if (empty($errors)) { // if no errors insert data 
            $insert_category = $conn->prepare('INSERT INTO `items` (`item_name`, `description`, `date`, `uploader`, `category`, `tags`, `item_picture`)
                                               VALUES (?, ?, ?, ?, ?, ?, ?)');
            $insert_category->execute(array($item_name, $description, date('Y-m-d h:i:s'), $_SESSION['user_id'], $category, $tags, 'image.jpg'));
            
            if ($insert_category) {
                redirect('index.php');
            }

        }
    
    } // end insert item in db

    // select user data and show add new item form if the user account is active
    $select_user_data = select_object('users', 'user_id', $_SESSION['user_id']);

    if ($select_user_data['active'] == 1) {
?>
<!-- start add new item form -->
<div class='add-item'>
    <div class='container'>
        <h1 class='h1'>Add New Item</h1>
        <form method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>

            <!-- item name input field -->
            <input type='text' name='item-name' class='form-control' placeholder='Item Name' />
            <?php 
                if (isset($errors['ITEM_NAME'])) {
                    echo '<div class="alert alert-danger">' . $errors['ITEM_NAME'] . '</div>';
                }
            ?>
            
            <!-- item description input field -->
            <textarea name='description' class='form-control' placeholder='Description'></textarea>
            
             <!-- item tag input field -->
           <p>  use [,] to add 2 or more tags for Example tag1,tag2,tag3,etc</p>
             <textarea name='tags' class='form-control' placeholder='Tags'></textarea>      

            <!-- item category input field -->
            <select name='category' class='form-control'>
            <?php 
                foreach (all_data('categories') as $category) {
                    echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
                }
            ?>
            </select>
            <?php 
                if (isset($errors['CATEGORY'])) {
                    echo '<div class="alert alert-danger">' . $errors['CATEGORY'] . '</div>';
                }
            ?>

            <!-- submit button to send data from form to the page -->
            <input type='submit' value='Add Item'  class='btn btn-info' />
        
        </form>
    </div>
</div>
<!-- end add new item form -->
<?php
    } else { // if user account is not active
        echo '<div class="container margin-top-40 alert alert-info">You Can Post Any 
             Item Until Administrator Active Your Accout</div>';
    }



    include $template . 'footer.php'; // include footer file
} else {
    header('location:index.php');
    exit;
}