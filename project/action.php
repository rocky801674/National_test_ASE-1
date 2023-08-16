<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
// echo $_POST;die;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $phone_number = $_POST["phone_number"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    // Simple validation
    $errors = array();

    if (empty($full_name)) {
        $errors[] = "Full Name is required.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone_number)) {
        $errors[] = "Phone Number must be a 10-digit number.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }

    if (empty($message)) {
        $errors[] = "Message is required.";
    }



    if (count($errors) === 0) {
        // Connect to the database (replace with your database credentials)
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $dbname = "test";

        $conn = new mysqli($hostname, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare data for insertion
        $full_name = $conn->real_escape_string($full_name);
        $phone_number = $conn->real_escape_string($phone_number);
        $email = $conn->real_escape_string($email);
        $subject = $conn->real_escape_string($subject);
        $message = $conn->real_escape_string($message);

        // Insert the data into the database
        $sql = "INSERT INTO contact_form_data (full_name, phone_number, email, subject, message, ip_address, submission_time) VALUES ('$full_name', '$phone_number', '$email', '$subject', '$message', '" . $_SERVER['REMOTE_ADDR'] . "', NOW())";

        if ($conn->query($sql) === TRUE) {
            echo "Form submitted successfully!";

            // Send email notification to site owner
            $to = "csumanta777@gmail.com"; // Replace with the actual owner's email address
            $subject = "Test Form Submission";
            $message = "A new Test form submission has been received:\n\n";
            $message .= "Full Name: $full_name\n";
            $message .= "Phone Number: $phone_number\n";
            $message .= "Email: $email\n";
            $message .= "Subject: $subject\n";
            $message .= "Message: $message\n";

            // Replace the following lines with your email sending code using PHPMailer or similar library
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'Csumanta777@gmail.com'; // Replace with your Gmail address
                $mail->Password   = 'fpaljqjzaxgmxnsb'; // Replace with your Gmail password or an app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('csumanta777@gmail.com', 'Sumanta Chakraborty');
                $mail->addAddress('test@techsolvitservices.com', 'Tanmoy Mondal');

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Testing Sucessfully Submited with one problem';
                $mail->Body    = $message;

                $mail->send();
                echo 'Email sent successfully';
            } catch (Exception $e) {
                echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }

    // ...

} else {
    // Redirect back to the form if accessed directly
    header("Location: index.html");
    exit();
}
