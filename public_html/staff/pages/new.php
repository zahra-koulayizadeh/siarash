<?php 
  require_once('../../../private/initialize.php');
  require_login();
?>

<?php 

  if(is_post_request()) {
    $page = [];
    $page['menu_name'] = $_POST['menu_name'] ?? '';
    $page['subject_id'] = $_POST['subject_id'] ?? '';
    $page['position'] = $_POST['position'] ?? '';
    $page['visible'] = $_POST['visible'] ?? '';
    $page['content'] = $_POST['content'] ?? '';

    $result = insert_page($page);
    if($result === true) {
      $_SESSION['message'] = "You successfully created a new page.";
      $new_id = mysqli_insert_id($db);
      redirect_to(url_for('/staff/pages/show.php?id=' . $new_id));
    } else { // inserting failed
      $errors = $result ;
    }

  } else { // if it is not a post request
    $page = [];
    $page['subject_id'] = $_GET['subject_id'] ?? '1';
  }

$page_count = count_pages_by_subject_id($page['subject_id']) + 1;
?>

<?php $page_title = 'Create Page'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a class="action" href="<?php echo url_for('/staff/subjects/show.php?id=' . h(u($page['subject_id']))); ?>">&laquo; Back to Subject Page</a>

  <div class="page new">
    <h1>Create Page</h1>
    <?php echo display_errors($errors); ?>
    <form action="<?php echo url_for('/staff/pages/new.php'); ?>" method="post">
      <dl>
        <dt>Menu Name</dt>
        <dd><input type="text" name="menu_name" value="<?php echo isset($page['menu_name']) ? h($page['menu_name']) : ''; ?>"></dd>
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
                  if($i == $page_count) {
                    echo " selected";
                  }
                  echo ">{$i}</option>";
                }
              ?>
            </select>
          </dd>
      </dl>

      <dl>
        <dt>Visible</dt>
        <dd>
          <input type="hidden" name="visible" value="0">
          <input type="checkbox" name="visible" value="1">
        </dd>
      </dl>
      
      <dl>
        <dt>Content</dt>
        <dd> <textarea name="content" id="content" cols="60" rows="10"><?php echo isset($page['content']) ? h($page['content']) : ''; ?></textarea> </dd>
      </dl>

      <div id="operations">
        <input type="submit" value="Create Page">
      </div>

    </form>

  </div>
  
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>