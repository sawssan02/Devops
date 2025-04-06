<html>
    <head>
        <title>SUPMIT</title>
    </head>

    <body>
        <h1>Student Checking App</h1>
        <ul>
            <form action="" method="POST">
            <!--<label>Enter student name:</label><br />
            <input type="text" name="" placeholder="Student Name" required/>
            <br /><br />-->
            <button type="submit" name="submit">List Student</button>
            </form>

            <?php
              if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['submit']))
              {
                $username = getenv('USERNAME');
                $password = getenv('PASSWORD');
                if (empty($username)) $username = 'fake_username';
                if (empty($password)) $password = 'fake_password';

                $context = stream_context_create(array(
                  "http" => array(
                    "header" => "Authorization: Basic " . base64_encode("$username:$password"),
                  )));

                $url = 'http://13.61.3.10:5000/supmit/api/v1.0/get_student_ages';
                $list = file_get_contents($url, false, $context);
                
                if ($list === false) {
                    echo "Error: Unable to fetch data from the API.";
                    exit;
                }
                
                $list = json_decode($list, true);
                if ($list === null) {
                    echo "Error: Invalid JSON response.";
                    exit;
                }

                echo "<p style='color:red; font-size: 20px;'>This is the list of the student with age</p>";
                
                if (isset($list["student_ages"]) && is_array($list["student_ages"])) {
                    foreach($list["student_ages"] as $key => $value) {
                        echo "- $key is $value years old <br>";
                    }
                } else {
                    echo "No student ages found.";
                }
              }
            ?>
        </ul>
    </body>
</html>
