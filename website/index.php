<html>
    <head>
        <title>SUPMIT</title>
    </head>

    <body>
        <h1>Student Checking App</h1>
        <ul>
            <form action="" method="POST">
                <button type="submit" name="submit">List Student</button>
            </form>

            <?php
              if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['submit'])) {
                  // Hardcoded credentials for the API authentication
                  $username = 'root';
                  $password = 'root';

                  // Prepare the context for HTTP request with basic authentication header
                  $context = stream_context_create(array(
                    "http" => array(
                      "header" => "Authorization: Basic " . base64_encode("$username:$password")
                    )
                  ));

                  $url = 'http://13.61.3.10:5000/supmit/api/v1.0/get_student_ages';

                  // Attempt to fetch the data from the API
                  $response = @file_get_contents($url, false, $context);

                  // Check if the request was successful
                  if ($response === FALSE) {
                      echo "<p style='color:red;'>Error: Unable to fetch data from the API.</p>";
                  } else {
                      // Decode the JSON response from the API
                      $list = json_decode($response, true);

                      // Check if the data contains the student ages
                      if (isset($list["student_ages"]) && is_array($list["student_ages"])) {
                          echo "<p style='color:red; font-size: 20px;'>This is the list of the student with age:</p>";
                          foreach ($list["student_ages"] as $key => $value) {
                              echo "- $key is $value years old <br>";
                          }
                      } else {
                          echo "<p style='color:red;'>No student data found.</p>";
                      }
                  }
              }
            ?>
        </ul>
    </body>
</html>
