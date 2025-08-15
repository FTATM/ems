
<?php

include '../config/connect.php';
// fetch data in table 
function fetchUsers($conn) {
    $result = $conn->query("SELECT * FROM users") ;
    $users = [] ;

    while ($rows = $result->fetch_assoc()) {
        $users[] = $rows ;
    }
    return $users;
}
$users = fetchUsers($conn);
echo json_encode($users, JSON_UNESCAPED_UNICODE);




