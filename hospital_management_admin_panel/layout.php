<?php
// Uncomment to enable session-based authentication
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get the current page filename
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <!-- Sidebar -->
    <div class="flex h-screen">
        <aside class="w-48 h-screen">
            <div class="w-48 h-full fixed bg-white text-gray-500">
                <div class="px-8 py-3">
                    <img src="https://i.postimg.cc/jSvW9XfQ/output-onlinepngtools-2.png" alt="Logo" class="w-20 h-20 object-contain">
                </div>
                <nav>
                    <ul>
                        <li>
                            <a href="index.php" class="group ">
                                <p class="px-8 py-3 hover:bg-indigo-500 hover:text-white <?php echo ($currentPage == 'index.php') ? 'bg-indigo-500 text-white' : ''; ?>">Dashboard</p>
                            </a>
                        </li>
                        <li>
                            <a href="patients.php" class="group">
                                <p class="px-8 py-3 hover:bg-indigo-500 hover:text-white <?php echo ($currentPage == 'patients.php') ? 'bg-indigo-500 text-white' : ''; ?>">Patients</p>
                            </a>
                        </li>
                        <li>
                            <a href="doctors.php" class="group">
                                <p class="px-8 py-3 hover:bg-indigo-500 hover:text-white <?php echo ($currentPage == 'doctors.php') ? 'bg-indigo-500 text-white' : ''; ?>">Doctors</p>
                            </a>
                        </li>
                        <li>
                            <a href="appointments.php" class="group">
                                <p class="px-8 py-3 hover:bg-indigo-500 hover:text-white <?php echo ($currentPage == 'appointments.php') ? 'bg-indigo-500 text-white' : ''; ?>">Appointments</p>
                            </a>
                        </li>
                        <li>
                            <a href="add_lab_test.php" class="group">
                                <p class="px-8 py-3 hover:bg-indigo-500 hover:text-white <?php echo ($currentPage == 'add_lab_test.php') ? 'bg-indigo-500 text-white' : ''; ?>">Add Lab Test</p>
                            </a>
                        </li>
                        <li>
                            <a href="upload_lab_report.php" class="group ">
                                <p class="px-8 py-3 hover:bg-indigo-500 hover:text-white <?php echo ($currentPage == 'upload_lab_report.php') ? 'bg-indigo-500 text-white' : ''; ?>">Upload Report</p>
                            </a>
                        </li>
                        <li>
                            <a href="logout.php" class="group">
                                <p class="px-8 py-3 hover:bg-indigo-500 hover:text-white">Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 bg-gray-100">
            <header class="bg-white py-4 px-4 flex justify-between items-center">
                <h1 class="text-xl font-bold"><?php echo isset($pageTitle) ? $pageTitle : "Admin Panel"; ?></h1>
                <div>
                    <img src="https://i.postimg.cc/jSvW9XfQ/output-onlinepngtools-2.png" alt="Logo" class="w-10 h-10 object-contain border rounded-full">
                </div>
            </header>
            <div class="p-4">
                <?php
                // Display dynamic content based on the page
                echo isset($content) ? $content : "Welcome to the Admin Panel!";
                ?>
            </div>
        </main>
    </div>

</body>

</html>
