<?php 
require_once('../../private/initialize.php'); //Include static strings in require and include 
require_login();?>
<!-- We use dot dot here because we should first require initialize.php then use defined constants like SHARED_PATH -->

<?php $page_title = 'Staff Menu'; /* variables are available inside included files */ 
?> 
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <div id="main-menu">
    <h2>Main Menu</h2>
    <ul>
      <!-- We should use relative url -->
      <li><a href="<?php echo url_for('/staff/subjects/index.php'); ?>">Subjects</a></li>
      <li><a href="<?php echo url_for('/staff/admins/index.php'); ?>">Admin</a></li>
    </ul>
  </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>


