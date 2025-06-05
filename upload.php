<?php
if (isset($_POST['submit'])) {
    // Database connection
    include 'db.php'; 

    // Get form data
    $name = $_POST['name'];
    
    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_type = $_FILES['profile_picture']['type'];
        
        // Check if file is an image (Optional: check mime type)
        if (in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
            // Define the upload directory
            $upload_dir = 'uploads/'; // Make sure this folder exists and has write permissions
            $file_path = $upload_dir . basename($file_name);
            
            // Move the uploaded file to the desired folder
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Save the file path to the database
                $sql = "INSERT INTO employees (name, profile_picture) VALUES ('$name', '$file_path')";
                if ($conn->query($sql) === TRUE) {
                    echo "Employee added successfully!";
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "Failed to upload the image.";
            }
        } else {
            echo "Only image files are allowed.";
        }
    } else {
        echo "No file uploaded or file error.";
    }
}
?>
