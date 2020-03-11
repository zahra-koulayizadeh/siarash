<?php
  require_once('../../../private/initialize.php');
  if(!isset($_GET['id'])) {
    redirect_to(url_for('/staff/admins/index.php'));
  }
  $id = $_GET['id'] ?? '';
  if(is_post_request()) {
    $result = delete_admin($id);
    $_SESSION['message'] = "You successfully deleted the admin.";
    redirect_to(url_for('/staff/admins/index.php'));
  } else {
    $admin = find_admin_by_id($id);
  }
?>

<?php
  $page_title = "Delete Admin";
  include(SHARED_PATH . '/staff_header.php');
?>

<div id="content">
  <div class="admin delete">
    <a href="<?php echo url_for('/staff/admins/index.php'); ?>">&laquo; Back to List</a>
    <h1>Delete Admin</h1>
    <p>Are you sure you want to delete this admin?</p>
    <p><?php echo $admin['username']; ?></p>
    <form action="<?php echo url_for('staff/admins/delete.php?id=' . h(u($admin['id']))); ?>" method="post">
      <div id="operations">
        <input type="submit" value="Delete Admin">
      </div>
    </form>
  </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php');