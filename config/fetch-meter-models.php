<?php
// Meter models (device profiles), with how many registers each one maps and
// how many meters are using it — the two numbers that tell you at a glance
// whether a profile is finished and whether it is safe to delete.
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$sql = "SELECT mm.id, mm.name, mm.brand, mm.meter_type_id, mm.note,
               mt.name AS meter_type_name,
               (SELECT COUNT(*) FROM register_map rm
                 WHERE rm.model_id = mm.id AND rm.is_deleted = 0) AS register_count,
               (SELECT COUNT(*) FROM meter m
                 WHERE m.model_id = mm.id AND m.is_deleted = 0) AS meter_count
        FROM meter_model mm
        JOIN meter_type mt ON mt.id = mm.meter_type_id
        WHERE mm.is_deleted = 0
        ORDER BY mt.name, mm.brand, mm.name";

$rows = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['id'] = (int) $row['id'];
        $row['meter_type_id'] = (int) $row['meter_type_id'];
        $row['register_count'] = (int) $row['register_count'];
        $row['meter_count'] = (int) $row['meter_count'];
        $rows[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $rows]);

$conn->close();
