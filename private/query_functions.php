<?php
  function find_all_subjects($options=[]) {
    global $db;

    $visible = $options['visible'] ?? false; 
    $sql = "SELECT * FROM subjects ";
    if($visible) {
      $sql .= "WHERE visible = true ";
    }
    $sql .= "ORDER BY position ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }

  function find_subject_by_id($id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;
  
    $sql = "SELECT * FROM subjects ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "'";
    if($visible) {
      $sql .= "AND visible = true";
    }
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $subject = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $subject;
  }

  function validate_subject($subject) {
    $errors = [];

    // menu_name
    if(is_blank($subject['menu_name'])) {
      $errors[] = "Name cannot be blank.";
    } elseif(!has_length($subject['menu_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "Name must be between 2 and 255 characters.";
    }

    // position
    // Make sure we are working with an integer
    $postion_int = (int) $subject['position'];
    if($postion_int <= 0) {
      $errors[] = "Position must be greater than zero.";
    }
    if($postion_int > 999) {
      $errors[] = "Position must be less than 999.";
    }

    // visible
    // Make sure we are working with a string
    $visible_str = (string) $subject['visible'];
    if(!has_inclusion_of($visible_str, ["0","1"])) {
      $errors[] = "Visible must be true or false.";
    }

    return $errors;
  }

  function shift_subject_positions($start_pos, $end_pos, $current_id=0) {
    global $db;
    $sql = "UPDATE subjects ";
    if($start_pos == 0) {
      // new item, +1 to items greater than $end_pos
      
      $sql .= "SET position = position+1 " ;
      $sql .= "WHERE position >='" . db_escape($db, $end_pos) . "' "; 
    } elseif($end_pos == 0) {
      // delete item, -1 from items greater than $start_pos
      $sql .= "SET position = position-1 " ;
      $sql .= "WHERE position >'" . db_escape($db, $start_pos) . "' ";
    } elseif($start_pos < $end_pos) {
      // move later, -1 from items between (including $end_pos)
      $sql .= "SET position = position-1 " ;
      $sql .= "WHERE position <='" . db_escape($db, $end_pos) . "' ";
      $sql .= "AND position >'" . db_escape($db, $start_pos) . "' ";
    } elseif($start_pos > $end_pos) {
      // move earlier, +1 to items between (including $end_pos)
      $sql .= "SET position = position+1 " ;
      $sql .= "WHERE position >='" . db_escape($db, $end_pos) . "' ";
      $sql .= "AND position <'" . db_escape($db, $start_pos) . "' ";
    } else { // if $start_pos = end_pos no shift needed.
      return true;
    }
    // Exclude the current_id in the SQL WHERE clause
    $sql .= "AND id!='" . db_escape($db, $current_id) . "'";

    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      // update positions failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function insert_subject($subject) {
    global $db;

    $errors = validate_subject($subject);
    if(!empty($errors)) {
      return $errors;
    }

    shift_subject_positions(0, $subject['position'], 0); 

    $sql = "INSERT INTO subjects ";
    $sql .= "(menu_name, position, visible) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $subject['menu_name']) . "',";
    $sql .= "'" . db_escape($db, $subject['position']) . "',";
    $sql .= "'" . db_escape($db, $subject['visible']) . "'";
    $sql .= ")";

    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result){
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function update_subject($subject) {
    global $db;

    $errors = validate_subject($subject);
    if(!empty($errors)) {
      return $errors;
    }

    shift_subject_positions($subject['last_position'], $subject['position'], $subject['id']); 


    $sql = "UPDATE subjects SET ";
    $sql .= "menu_name='" . db_escape($db, $subject['menu_name']) . "', ";
    $sql .= "position='" . db_escape($db, $subject['position']) . "', ";
    $sql .= "visible='" . db_escape($db, $subject['visible']) . "' ";
    $sql .= "WHERE id='" . db_escape($db, $subject['id']) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }

  }

  function delete_subject($subject) {
    global $db;

    shift_subject_positions($subject['position'], 0,  $subject['id']); 

    $sql = "DELETE FROM subjects ";
    $sql .= "WHERE id='" . db_escape($db, $subject['id']) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function find_subject_ids() {
    global $db;
    $subject_ids = [];
    $sql = "SELECT DISTINCT id FROM subjects";
    $result = mysqli_query($db, $sql);
    while ($arr = mysqli_fetch_assoc($result)) {
      $subject_ids[] = $arr['id'];
    };
    return $subject_ids;
  }

  //########## Pages ##########//

  function find_all_pages() {
    global $db;
    $sql = "SELECT * FROM pages ";
    $sql .= "ORDER BY subject_id, position ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }

  function find_page_by_id($id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT * FROM pages ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "'";
    if($visible) {
      $sql .= "AND visible = true ";
    }

    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $page = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $page;
  }

  function validate_page($page) {
    $errors = [];

    // menu_name
    if(is_blank($page['menu_name'])) {
      $errors[] = "Name cannot be blank.";
    } elseif(!has_length($page['menu_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "Name must be between 2 and 255 characters.";
    }
    $current_id = $page['id'] ?? '0';
    if(!has_unique_page_menu_name($page['menu_name'], $current_id)) {
      $errors[] = "Menu name must be unique.";
    }

    
    $subject_ids = find_subject_ids();
    if(is_blank($page['subject_id'])) {
      $errors[] = "Subject ID cannot be blank.";
    } elseif(!has_inclusion_of($page['subject_id'], $subject_ids)) {
      $errors[] = "The subject id you had choosed is not in the database";
    }

    if(!has_length($page['position'], ['min' => 0, 'max' => 4])){
      $errors[] = "Position must be between 1 and 999.";
    }
    
    if(!has_inclusion_of($page['visible'], [0, 1])) {
      $errors[] = "Visible must be either true or false";
    }

    return $errors;

  }

  function shift_page_positions($start_pos, $end_pos, $subject_id ,$current_id=0) {
    global $db;
    $sql = "UPDATE pages ";
    if($start_pos == 0) {
      // new item, +1 to items greater than $end_pos
      
      $sql .= "SET position = position+1 " ;
      $sql .= "WHERE position >='" . db_escape($db, $end_pos) . "' "; 
      
    } elseif($end_pos == 0) {
      // delete item, -1 from items greater than $start_pos
      $sql .= "SET position = position-1 " ;
      $sql .= "WHERE position >'" . db_escape($db, $start_pos) . "' ";
      
    } elseif($start_pos < $end_pos) {
      // move later, -1 from items between (including $end_pos)
      $sql .= "SET position = position-1 " ;
      $sql .= "WHERE position <='" . db_escape($db, $end_pos) . "' ";
      $sql .= "AND position >'" . db_escape($db, $start_pos) . "' ";
      
    } elseif($start_pos > $end_pos) {
      // move earlier, +1 to items between (including $end_pos)
      $sql .= "SET position = position+1 " ;
      $sql .= "WHERE position >='" . db_escape($db, $end_pos) . "' ";
      $sql .= "AND position <'" . db_escape($db, $start_pos) . "' ";
      
    } else { // if $start_pos = end_pos no shift needed.
      return true;
    }
    // Exclude the current_id in the SQL WHERE clause
    $sql .= "AND id!='" . db_escape($db, $current_id) . "'";
    $sql .= "AND subject_id ='" . db_escape($db, $subject_id) . "' ";

    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      // update positions failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function insert_page($page) {
    global $db;

    $errors = validate_page($page);
    if(!empty($errors)) {
      return $errors;
    }

    shift_page_positions(0, $page['position'], $page['subject_id']); 


    $sql = "INSERT INTO pages (";
    $sql .= "subject_id, menu_name, position, visible, content) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $page['subject_id']) . "', ";
    $sql .= "'" . db_escape($db, $page['menu_name']) . "', ";
    $sql .= "'" . db_escape($db, $page['position']) . "', ";
    $sql .= "'" . db_escape($db, $page['visible']) . "', ";
    $sql .= "'" . db_escape($db, $page['content']) . "' ";
    $sql .= ")"; 

    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function update_page($page) {
    global $db;

    $errors = validate_page($page);
    if(!empty($errors)) {
      return $errors;
    }

    shift_page_positions($page['last_position'], $page['position'], $page['subject_id'] ,$page['id']); 

    $sql = "UPDATE pages SET ";
    $sql .= "subject_id='" . db_escape($db, $page['subject_id']) . "', "; 
    $sql .= "menu_name='" . db_escape($db, $page['menu_name']) . "', "; 
    $sql .= "position='" . db_escape($db, $page['position']) . "', "; 
    $sql .= "visible='" . db_escape($db, $page['visible']) . "', "; 
    $sql .= "content='" . db_escape($db, $page['content']) . "' "; 
    $sql .= "WHERE id='" . db_escape($db, $page['id']) . "' ";
    $sql .= "LIMIT 1";
  
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function delete_page($page) {
    global $db;

    shift_page_positions($page['position'], 0, $page['subject_id'], $page['id']); 

    $sql = "DELETE FROM pages ";
    $sql .= "WHERE id='" . db_escape($db, $page['id']) . "'";
  
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function find_pages_by_subject_id($subject_id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT * FROM pages ";
    $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "'";
    if($visible) {
      $sql .= "AND visible = true ";
    }
    $sql .= "ORDER BY position ASC";

    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }

  function count_pages_by_subject_id($subject_id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT COUNT(id) FROM pages ";
    $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "'";
    if($visible) {
      $sql .= "AND visible = true ";
    }
    $sql .= "ORDER BY position ASC";

    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);
    $count = $row[0];
    return $count;
  }

  //##########Admins##########//

  function find_all_admins() {
    global $db;
    $sql = "SELECT * FROM admins ";
    $sql .= "ORDER BY last_name ASC, first_name ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }

  function validate_admin($admin, $options=[]) {
    $errors =[];

    $password_required = $options['password_required'] ?? true;

    // Firstname
    if(is_blank($admin['first_name'])) {
      $errors[] = "First name cannot be blank.";
    } elseif(!has_length($admin['first_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "First name must be between 2 and 255 characters.";
    }

    // Lastname
    if(is_blank($admin['last_name'])) {
      $errors[] = "Last name cannot be blank.";
    } elseif(!has_length($admin['last_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "Last name must be between 2 and 255 characters.";
    }

    // Email
    if(is_blank($admin['email'])) {
      $errors[] = "Email cannot be blank.";
    } elseif(!has_valid_email_format($admin['email'])) {
      $errors[] = "Please enter a valid email.";
    } elseif(!has_length($admin['email'], ['max' => 255])) {
      $errors[] = "Email must have maximum 255 characters";
    }

    // Username
    if(is_blank($admin['username'])) {
      $errors[] = "Username cannot be blank.";
    } elseif(!has_length($admin['username'], ['min' => 8, 'max' => 255])) {
      $errors[] = "Username must be between 8 and 255 characters.";
    }
    
    $current_id = $admin['id'] ?? '0';
    if(!has_unique_admin_username($admin['username'], $current_id)) {
      $errors[] = "Username must be unique.";
    }

    if($password_required) {
      // Password
      if(is_blank($admin['password'])) {
        $errors[] = "Password cannot be blank.";
      } elseif(!has_length($admin['password'], ['min' => 12, 'max' => 255])) {
        $errors[] = "Password must be between 12 and 255 characters.";
      } elseif (!preg_match('/[A-Z]/', $admin['password'])) {
        $errors[] = "Password must contain at least 1 uppercase letter.";
      } elseif (!preg_match('/[a-z]/', $admin['password'])) {
        $errors[] = "Password must contain at least 1 lowercase letter.";
      } elseif (!preg_match('/[0-9]/', $admin['password'])) {
        $errors[] = "Password must contain at least 1 number.";
      } elseif (!preg_match('/[^A-Za-z0-9\s]/', $admin['password'])) {
        $errors[] = "Password must contain at least 1 symbol.";
      }
  
      // Confirm Password
      if(is_blank($admin['confirm_password'])) {
        $errors[] = "Confirm Password cannot be blank.";
      } elseif($admin['confirm_password'] !== $admin['password'] ) {
        $errors[] = "Passwords must match.";
      }
    }

    return $errors;
  }

  function insert_admin($admin) {
    global $db;

    $errors = validate_admin($admin);
    if(!empty($errors)) {
      return $errors;
    }

    $hashed_password = password_hash($admin['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO admins (";
    $sql .= "first_name, last_name, email, username, hashed_password) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $admin['first_name']) . "', ";
    $sql .= "'" . db_escape($db, $admin['last_name']) . "', ";
    $sql .= "'" . db_escape($db, $admin['email']) . "', ";
    $sql .= "'" . db_escape($db, $admin['username']) . "', ";
    $sql .= "'" . db_escape($db, $hashed_password) . "' ";
    $sql .= ")"; 

    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function find_admin_by_id($id) {
    global $db;

    $sql = "SELECT * FROM admins ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "'";

    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $admin = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $admin;
  }

  function find_admin_by_username($username) {
    global $db;

    $sql = "SELECT * FROM admins ";
    $sql .= "WHERE username='" . db_escape($db, $username) . "'";

    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $admin = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $admin;
  }

  function update_admin($admin) {
    global $db;

    $password_sent = !is_blank($admin['password']);

    $errors = validate_admin($admin, ['password_required' => $password_sent]);
    if(!empty($errors)) {
      return $errors;
    }

    $hashed_password = password_hash($admin['password'], PASSWORD_BCRYPT);

    $sql = "UPDATE admins SET ";
    $sql .= "first_name='" . db_escape($db, $admin['first_name']) . "', ";
    $sql .= "last_name='" . db_escape($db, $admin['last_name']) . "', ";
    $sql .= "email='" . db_escape($db, $admin['email']) . "', ";
    if($password_sent) {
      $sql .= "hashed_password='" . db_escape($db, $hashed_password) . "', ";
    }
    $sql .= "username='" . db_escape($db, $admin['username']) . "' ";
    $sql .= "WHERE id='" . db_escape($db, $admin['id']) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function delete_admin($id) {
    global $db;

    $sql = "DELETE FROM admins ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "'";

    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

?>