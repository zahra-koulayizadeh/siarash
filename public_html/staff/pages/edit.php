<?php 
require_once('../../../private/initialize.php');
require_login();
?>

<?php 
  if(!isset($_GET['id'])) { //id is required
    redirect_to(url_for('/staff/pages/index.php'));
  }
  $id = $_GET['id'];

  if(is_post_request()){
    $page = [];
    $page['id'] = $id;
    $page['menu_name'] = $_POST['menu_name'] ?? '';
    $page['subject_id'] = $_POST['subject_id'] ?? '';
    $page['last_position'] = $_POST['last_position'] ?? '';
    $page['position'] = $_POST['position'] ?? '';
    $page['visible'] = $_POST['visible'] ?? '';
    $page['content'] = $_POST['content'] ?? '';

    $result = update_page($page) ;
    if( $result === true) {
      $_SESSION['message'] = "You successfully updated the page.";
      redirect_to(url_for('/staff/pages/show.php?id=' . u($page['id'])));
    } else {
      $errors = $result;
    }

  } else { // if it is not a post request
    $page = find_page_by_id($id);
  }

// to show number of position
$page_count = count_pages_by_subject_id($page['subject_id']);

?>

<?php $page_title = 'Create Page'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <a class="back-link" href="<?php echo url_for('/staff/subjects/show.php?id=' . h(u($page['subject_id']))); ?>">&laquo; Back to Subject Page</a>

  <h1>Edit Page</h1>
  <?php echo display_errors($errors); ?>
  <form action="<?php echo url_for('/staff/pages/edit.php?id=' . h(u($id))); ?>" method="post">
    <dl>
      <dt>Menu Name</dt>
      <dd><input type="text" name="menu_name" value="<?php echo h($page['menu_name']); ?>"></dd>
    </dl>

    <dl>
      <dt>Subject</dt>
      <dd>
        <select name="subject_id">
        <?php
          $subject_set = find_all_subjects();
          while($subject = mysqli_fetch_assoc($subject_set)) {
            echo "<option value=\"" . h($subject['id']) . "\"";
            if($page["subject_id"] == $subject['id']) {
              echo " selected";
            }
            echo ">" . h($subject['menu_name']) . "</option>";
          }
          mysqli_free_result($subject_set);
        ?>
        </select>
      </dd>
    </dl>
    
    <dl>
      <dt>Position</dt>
        <dd>
          <select name="position" id="position">
            <?php
              for($i=1; $i<=$page_count; $i++) {
                echo "<option value=\"{$i}\"";
                if($i == $page['position']) {
                  echo " selected";
                }
                echo ">{$i}</option>";
              }
            ?>
          </select>
        </dd>
    </dl>
    
    <input type="hidden" name="last_position" value="<?php echo $page['position']; ?>">

    <dl>
      <dt>Visible</dt>
      <dd>
        <input type="hidden" name="visible" value="0">
        <input type="checkbox" name="visible" <?php if($page['visible'] == "1") { echo 'checked'; } ?>  value="1">
      </dd>
    </dl>

    <dl>
        <dt>Content</dt>
        <dd>
          <textarea name="content" cols="60" rows="10"><?php echo h($page['content']); ?></textarea>
        </dd>
      </dl>

    <div id="operations">
      <input type="submit" value="Edit Page">
    </div>
  </form>
</div>