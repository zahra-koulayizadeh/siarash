<?php require_once('../../../private/initialize.php'); ?>
<?php 
  if(is_post_request()) {
    $admin = [];
    $admin['first_name'] = $_POST['first_name'] ?? '';
    $admin['last_name'] = $_POST['last_name'] ?? '';
    $admin['email'] = $_POST['email'] ?? '';
    $admin['username'] = $_POST['username'] ?? '';
    $admin['password'] = $_POST['password'] ?? '';
    $admin['confirm_password'] = $_POST['confirm_password'] ?? '';

    $result = insert_admin($admin);
    if($result === true) {
      $_SESSION['message'] = "You successfully created a new admin.";
      $new_id = mysqli_insert_id($db);
      redirect_to(url_for('/staff/admins/show.php?id=' . h(u($new_id))));
    } else {
      $errors = $result;
    }
  }
?>

<?php $page_title = 'Create Admin'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <a class="back-link" href="<?php echo url_for('/staff/admins/index.php'); ?>">&laquo; Back To List</a>
  <h1>Creat Admin</h1>
  <?php echo display_errors($errors); ?>
  
  <form action="<?php echo url_for('/staff/admins/new.php'); ?>" method="post">
    <dl>
      <dt>First Name</dt>
      <dd><input type="text" name="first_name" id="first_name" value="<?php if(isset($admin['first_name'])) {echo $admin['first_name'];} ?>"></dd>
    </dl>

    <dl>
      <dt>Last Name</dt>
      <dd><input type="text" name="last_name" id="last_name" value="<?php if(isset($admin['last_name'])) {echo $admin['last_name'];} ?>"></dd>
    </dl>

    <dl>
      <dt>Email</dt>
      <dd><input type="email" name="email" id="email" value="<?php if(isset($admin['email'])) {echo $admin['email'];} ?>"></dd>
    </dl>

    <dl>
      <dt>Username</dt>
      <dd><input type="text" name="username" id="username" value="<?php if(isset($admin['username'])) {echo $admin['username'];} ?>"></dd>
    </dl>

    <dl>
      <dt>Password</dt>
      <dd><input type="password" name="password" id="password" value="<?php if(isset($admin['password'])) {echo $admin['password'];} ?>"></dd>
    </dl>

    <dl>
      <dt>Confirm Password</dt>
      <dd><input type="password" name="confirm_password" id="confirm_password" value="<?php if(isset($admin['confirm_password'])) {echo $admin['confirm_password'];} ?>"></dd>
    </dl>
    <p>Passwords should be at least 12 characters and include at least one uppercase letter, lowercase letter, number, and symbol.</p>
    <div id="operations">
      <input type="submit" value="Create Admin">
    </div>
  </form>


</div>
