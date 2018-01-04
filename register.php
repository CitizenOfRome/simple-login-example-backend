<?php
try {

    $inputJSON = file_get_contents('php://input');
    $inputData = json_decode($inputJSON);

    $password = password_hash($inputData -> password, PASSWORD_DEFAULT);
    $token = md5(uniqid($inputData -> email, true));

    $conn = require('./includes/getPDOConnection.php');

    // prepare sql and bind parameters
    $stmt = $conn->prepare("INSERT INTO users (email, password, token) 
    VALUES (:email, :password, :token)");
    $stmt->bindParam(':email', $inputData -> email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':token', $token);
    $success = $stmt->execute();

    echo json_encode(array(
        'message' => 'Registered successfully', 
        'success' => $success,
        'data' => array(
            'token' => $token,
            'id' => $conn->lastInsertId()
        )
    ));
} catch(PDOException $e) {
    echo json_encode(array(
        'message' => "Error: " . $e->getMessage(),
        'success' => false
    ));
}
