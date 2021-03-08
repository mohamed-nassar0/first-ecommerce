<?php
session_Start();

if (isset($_SESSION['user_id'])) {

      // page title
      $page_name = 'Contact With Us';

      // include Initialize
      include 'init.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // start check and insert message to db
        $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
        $errors  = array();

        if (empty($message)) {
            $errors['MESSAGE'] = 'Please Enter Your Message';
        }

        if (empty($errors)) {
            // insert message
            $insert_message = $conn->prepare("INSERT INTO `messages` (`from_user`, `message`, `date`) VALUES (?, ?, ?)");
            $insert_message->execute(array($_SESSION['user_id'], $message, date('Y-m-d h:i:s')));

            // if done 
            if ($insert_message) {
                redirect('index.php');
            }
        }
    } // end line 6
    
?>
<div class='container contact-with-us'>  
    <h1>contact with us</h1>
    <form method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <textarea class='form-control' name='message' placeholder='Enter Your Message Here'></textarea>
        <?php 
            if (isset($errors['MESSAGE'])) {
                echo '<div class="alert alert-danger">' . $errors['MESSAGE'] . '</div>';
            }
        ?>
        <input type='submit' value='Send Message' class='btn btn-primary' />
    </form>
</div>

<?php
    // include footer file
    include $template . 'footer.php';
} else {
    header('location:index.php');
    exit;
}