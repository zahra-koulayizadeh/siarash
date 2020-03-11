<?php
require_once('../../../private/initialize.php');
require_login();

if(!isset($_GET['id'])) { //id is required for determination
  redirect_to(url_for('/staff/pages/index.php'));
}
$id = $_GET['id'];
$page = find_page_by_id($id); // find the page we are going to delete

if(is_post_request()) { // if user pressed submit button
  $result = delete_page($page);
  if($result === true) {
    $_SESSION['message'] = "You successfully deleted the page.";
    redirect_to(url_for('/staff/subjects/show.php?id=' . h(u($page['subject_id'])))); // if it was successfull redirect to subject's list.
  } else {
    $errors = $result;
  }
}
?>

<?php $page_title = 'Delete Page'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <a class="back-link" href="<?php echo url_for('/staff/subjects/show.php?id=' . h(u($page['subject_id']))); ?>">&laquo; Back to Subject Page</a>

  <div class="page delete">
    <h1>Delete Page</h1>
    <?php echo display_errors($errors); ?>
    <p>Are you sure you want to delete this page?</p>
    <p class="item"><?php echo h($page['menu_name']); ?></p>

    <form action="<?php echo url_for('/staff/pages/delete.php?id=' . h(u($page['id']))); ?>" method="post">
      <div id="operations">
        <input type="submit" name="commit" value="Delete page" />
      </div>
    </form>
  </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>