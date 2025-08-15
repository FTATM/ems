
<?php

include '../config/connect.php';
// fetch data in table 
function fetchUsers($conn) {
    // Edit delete
    $result = $conn->query("SELECT * FROM users WHERE is_deleted = 0") ;
    $users = [] ;

    while ($rows = $result->fetch_assoc()) {
        $users[] = $rows ;
    }
    return $users;
}
$users = fetchUsers($conn);
echo json_encode($users, JSON_UNESCAPED_UNICODE);




