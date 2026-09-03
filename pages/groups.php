<?php
// หน้า groups.php ถูกยุบรวมเข้ากับ locations.php (stepper 2 ขั้น)
// คงไฟล์นี้ไว้เป็น redirect เพราะ checkSession() ใน components/session.php ยังชี้มาที่นี่
header("Location: ../pages/locations.php?step=2");
exit();
