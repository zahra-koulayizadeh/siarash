<?php require_once('../../../private/initialize.php'); ?>
<?php 
  // id must set in url
  if(!isset($_GET['id'])) {
    redirect_to(url_for('/staff/admins/index.php'));
  }
  $id = $_GET['id'] ?? '0' ; // maybe user just typed :public/staff/admins/show.php?id
  $admin = find_admin_by_id($id);
?>
<?php $page_title = 'Show Admin'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?> 
<div id="content">
  <a class="back-link" href="<?php echo url_for('/staff/admins/index.php'); ?>">&laquo;Back to List</a>
  <div class="admin show">
    <h1>Admin: <?php echo $admin['first_name'] . ' ' . $admin['last_name']; ?></h1>
    <dl>
      <dt>Firstname</dt>
      <dd><?php echo $admin['first_name']; ?></dd>
    </dl>
    <dl>
      <dt>Lastname</dt>
      <dd><?php echo $admin['last_name']; ?></dd>
    </dl>
    <dl>
      <dt>Email</dt>
      <dd><?php echo $admin['email']; ?></dd>
    </dl>
    <dl>
      <dt>Username</dt>
      <dd><?php echo $admin['username']; ?></dd>
    </dl>
  </div>
</div>
<?php include(SHARED_PATH . '/staff_footer.php'); ?>