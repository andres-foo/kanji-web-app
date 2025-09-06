<?php

header('Content-Type: application/json');



// is post
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Set HTTP status code to "Method Not Allowed"
    $response = [
        'status' => 'error',
        'message' => 'Only POST requests are allowed.'
    ];
    echo json_encode($response);
}

// grab data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$literal = $data['literal'];
$group = $data['group'];



// has data
if (empty($literal) || empty($group)) {
    $response = [
        'status' => 'error',
        'message' => 'Missing required parameters: group or literal.'
    ];
    echo json_encode($response);
    exit;
}



// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

$sql = "UPDATE kanjis SET component_group = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);

$results = $stmt->execute([$group, $literal]);
if (!$results) {
    $response = [
        'status' => 'Error',
        'message' => 'Could not update'
    ];
    echo json_encode($response);
    exit;
}

$response = [
    'status' => 'Success',
    'message' => 'Alrighty'
];
echo json_encode($response);
exit;
