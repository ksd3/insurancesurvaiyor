<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Image Upload</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #00008B;
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
  .container {
    width: 100%;
    max-width: 600px;
    padding: 20px;
    background-color: #333333;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  form {
    margin-bottom: 20px;
    padding: 20px;
  }
  #inputs {
    margin-bottom: 10px;
    display: flex;
    flex-wrap: wrap;
  }
  input[type="file"], input[type="text"], button[type="submit"], button[type="button"] {
    margin-right: 10px;
    margin-bottom: 5px;
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
    cursor: pointer;
  }
  input[type="file"], input[type="text"] {
    flex: 1;
    margin-right: 0;
    margin-bottom: 10px;
  }
  input[type="file"] {
    background-color: #444444;
    color: #ffffff;
  }
  input[type="file"]:hover, input[type="text"]:hover {
    background-color: #555555;
  }
  button[type="submit"], button[type="button"] {
    background-color: #888888;
    color: #ffffff;
  }
  button[type="submit"]:hover, button[type="button"]:hover {
    background-color: #999999;
  }
  #message {
    margin-top: 20px;
    text-align: center;
  }
  h2 {
    margin-top: 20px;
    text-align: center;
  }
  .image-container {
    margin-bottom: 20px;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #444444;
  }
  .image-container img {
    width: 100%;
    height: auto;
    display: block;
  }
  .analytics-button {
    display: block;
    margin-top: 10px;
    background-color: #888888;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    padding: 8px 15px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .analytics-button:hover {
    background-color: #999999;
  }

  @media screen and (max-width: 600px) {
    input[type="file"], input[type="text"], button[type="submit"], button[type="button"] {
      width: 100%;
      margin-right: 0;
    }
  }
  .logo {
      width: 30%;
      opacity: 1.0;
  }

  .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
  }
</style>
</head>
<body>
<div class="container">
  <form id="uploadForm" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="logo-container">
        <img class="logo" src="assurant-logo.png" alt="assurant-logo">
    </div>
    <div id="inputs">
        <input type="file" name="images[]" accept="image/*">
        <input type="text" name="texts[]" placeholder="Client Name">
        <input type="text" name="texts[]" placeholder="Date of Visit (YYYY-MM-DD)">
        <input type="text" name="texts[]" placeholder="Client ID">
        <button type="button" onclick="addFields()">Add More</button>
    </div>
    <button type="button" onclick="removeFields()">Remove Last</button>
    <button type="submit" name="submit">Submit</button>
  </form>

  <div id="message"></div>

  <?php

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $uploads_dir = 'uploads' . DIRECTORY_SEPARATOR;

      // Create the uploads folder if it doesn't exist
      if (!file_exists($uploads_dir)) {
          mkdir($uploads_dir, 0777, true); // Creates the folder recursively with full permissions
      }

      $data = array();

      foreach ($_FILES["images"]["error"] as $key => $error) {
          if ($error == UPLOAD_ERR_OK) {
              $tmp_name = $_FILES["images"]["tmp_name"][$key];
              $name = basename($_FILES["images"]["name"][$key]);
              $file_path = $uploads_dir . $name;
              move_uploaded_file($tmp_name, $file_path);
              $client_name = $_POST["texts"][($key * 3)]; // Index adjusted for multiple fields per image
              $date_of_visit = $_POST["texts"][($key * 3) + 1];
              $client_id = $_POST["texts"][($key * 3) + 2];
              $data[] = array("image" => $file_path, "client_name" => $client_name, "date_of_visit" => $date_of_visit, "client_id" => $client_id);
          }
      }

      $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
      file_put_contents("data.json", $json_data);

      echo "<script>document.getElementById('message').innerHTML = 'Data saved successfully!';</script>";

      if (!empty($data)) {
          echo '<h2>Uploaded Images and Text</h2>';
          displayImagesAndText($data);
          
          // Run the Python script
          runPythonScript();
      }
  }

  function displayImagesAndText($data) {
      foreach ($data as $item) {
          echo '<div class="image-container">';
          echo '<img src="' . $item["image"] . '" alt="Uploaded Image">';
          echo '<p>' . htmlspecialchars($item["client_name"]) . '</p>'; // Output text securely
          echo '<p>' . htmlspecialchars($item["date_of_visit"]) . '</p>';
          echo '<p>' . htmlspecialchars($item["client_id"]) . '</p>';
          echo '</div>';
      }
      echo '<button class="analytics-button" type="button" onclick="runPythonScript()">Run Analytics</button>';
  }

  function runPythonScript() {
      // Get the path to the Python script.
      $pythonScriptPath = "push_to_database.py";
      // Run the Python script.
      $command = "python3 $pythonScriptPath";
      exec($command, $output, $returnVar);

      if ($returnVar == 0) {
          echo "<script>console.log('Python script executed successfully.');</script>";
      } else {
          echo "<script>console.error('Failed to execute Python script.');</script>";
      }
  }
  ?>
</div>

<script>
  function addFields() {
    var inputs = document.getElementById('inputs');
    var newInput = document.createElement('div');
    newInput.innerHTML = '<input type="file" name="images[]" accept="image/*">' +
                         '<input type="text" name="texts[]" placeholder="Client Name">' +
                         '<input type="text" name="texts[]" placeholder="Date of Visit (YYYY-MM-DD)">' +
                         '<input type="text" name="texts[]" placeholder="Client ID">';
    inputs.appendChild(newInput);
  }

  function removeFields() {
    var inputs = document.getElementById('inputs');
    if (inputs.children.length > 1) {
      inputs.removeChild(inputs.lastChild);
    }
  }
</script>

</body>
</html>
