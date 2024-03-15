<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Image Upload</title>
</head>
<body>
<form id="uploadForm" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <div id="inputs">
    <input type="file" name="images[]" accept="image/*">
    <input type="text" name="texts[]">
    <button type="button" onclick="addFields()">Add More</button>
  </div>
  <button type="button" onclick="removeFields()">Remove Last</button>
  <button type="submit" name="submit">Submit</button>
</form>

<div id="message"></div>

<script>
function addFields() {
  var inputs = document.getElementById('inputs');
  var newInput = document.createElement('div');
  newInput.innerHTML = '<input type="file" name="images[]" accept="image/*">' +
                       '<input type="text" name="texts[]">';
  inputs.appendChild(newInput);
}

function removeFields() {
  var inputs = document.getElementById('inputs');
  if (inputs.children.length > 1) {
    inputs.removeChild(inputs.lastChild);
  }
}
</script>

  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $data = array();
      $uploads_dir = 'uploads' . DIRECTORY_SEPARATOR;

      // Create the uploads folder if it doesn't exist
      if (!file_exists($uploads_dir)) {
          mkdir($uploads_dir, 0777, true); // Creates the folder recursively with full permissions
      }

      foreach ($_FILES["images"]["error"] as $key => $error) {
          if ($error == UPLOAD_ERR_OK) {
              $tmp_name = $_FILES["images"]["tmp_name"][$key];
              $name = basename($_FILES["images"]["name"][$key]);
              $file_path = $uploads_dir . $name;
              move_uploaded_file($tmp_name, $file_path);
              $data[] = array("image" => $file_path, "text" => $_POST["texts"][$key]);
          }
      }

      $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
      file_put_contents("data.json", $json_data);

      echo "<script>document.getElementById('message').innerHTML = 'Data saved successfully!';</script>";
  }
  ?>
</body>
</html>
