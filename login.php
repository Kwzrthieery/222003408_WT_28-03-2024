<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bityeartwo2024");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password_from_db = $row["password"];
        
        if (password_verify($password, $hashed_password_from_db)) {
            // Successful login, retrieve all user data
            $user_id = $row["id"];
            $sql = "SELECT * FROM user WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();

            // Display user data in a table on a new page
            echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>User Data</title>
                    </head>
                    <body>
                        <h2>User Data</h2>
                        <table border='1'>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Telephone</th>
                                <th>Password</th>
                                <th>Creation Date</th>
                            </tr>
                            <tr>
                                <td>{$user_data['id']}</td>
                                <td>{$user_data['firstname']}</td>
                                <td>{$user_data['lastname']}</td>
                                <td>{$user_data['username']}</td>
                                <td>{$user_data['email']}</td>
                                <td>{$user_data['telephone']}</td>
                                <td>{$user_data['password']}</td>
                                <td>{$user_data['creationdate']}</td>
                            </tr>
                        </table>
                    </body>
                    </html>";
            exit();
        } else {
            echo "Invalid username or password";
        }
    } else {
        echo "User not found";
    }
}
$conn->close();
?>
