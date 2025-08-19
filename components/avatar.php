<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// mock data (กรณีคุณมี session จริงก็ใช้ session ได้เลย)
$avatarList = [
    'https://mdbcdn.b-cdn.net/img/new/avatars/2.webp',
    'https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
];

// ถ้าต้องการให้สุ่มใหม่ทุกครั้งที่โหลดหน้า
// unset($_SESSION['avatar']);

$_SESSION['avatar']   = $_SESSION['avatar'] ?? $avatarList[array_rand($avatarList)];
$_SESSION['username'] = $_SESSION['username'] ?? 'JohnDoe';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'John Doe';
$_SESSION['address']  = $_SESSION['address'] ?? '';

$avatar   = htmlspecialchars($_SESSION['avatar']);
$username = htmlspecialchars($_SESSION['username']);
$fullName = htmlspecialchars($_SESSION['full_name']);
$address  = htmlspecialchars($_SESSION['address']);
?>

<nav class="navbar navbar-expand-lg ">
  <div class="container-fluid">
    <ul class="navbar-nav ms-auto">
      <!-- Avatar Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center"
           href="#" id="navbarDropdownMenuLink"
           role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?php echo $avatar; ?>" 
               class="rounded-circle"
               height="32"
               width="32"
               alt="<?php echo $username; ?>"
               style="object-fit: cover;" />
          <span class="ms-2 fw-bold text-light"><?php echo $username; ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
          <li class="px-3 py-2 ">
            <strong><?php echo $fullName; ?></strong><br>
            <small>@<?php echo $username; ?></small><br>
            <small><?php echo $address; ?></small>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="profile.php">👤 My Profile</a></li>
          <li><a class="dropdown-item" href="settings.php">⚙️ Settings</a></li>
          <li><a class="dropdown-item" href="logout.php">🚪 Logout</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
