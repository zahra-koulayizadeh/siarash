<?php require_once('../../../private/initialize.php'); ?>
<?php
  $admins_set = find_all_admins();
?>
<?php $page_title = 'Admins'; ?> 
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <div class="admins listing"></div>
  <h1>Admins</h1>
  <div class="actions">
      <a class="action" href="<?php echo url_for('/staff/admins/new.php'); ?>">Create New Admin</a>
  </div>

  <table class="list">
    <tr>
      <th>ID</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Email</th>
      <th>Username</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>

    <?php while($admin = mysqli_fetch_assoc($admins_set)) { ?>
      <tr>
        <td><?php echo $admin['id']; ?></td>
        <td><?php echo $admin['first_name']; ?></td>
        <td><?php echo $admin['last_name']; ?></td>
        <td><?php echo $admin['email']; ?></td>
        <td><?php echo $admin['username']; ?></td>
        <td><a href="<?php echo url_for('staff/admins/show.php?id=' . h(u($admin['id']))) ?>">View</a></td>
        <td><a href="<?php echo url_for('staff/admins/edit.php?id=' . h(u($admin['id']))) ?>">Edit</a></td>
        <td><a href="<?php echo url_for('staff/admins/delete.php?id=' . h(u($admin['id']))); ?>">Delete</a></td>
      </tr>
    <?php } //while $admin ?>
  </table>
  <?php
    mysqli_free_result($admins_set);
  ?>
</div>
<?php include(SHARED_PATH . '/staff_footer.php'); ?>