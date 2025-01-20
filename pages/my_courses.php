<?php
session_start();

require_once('../classes/config.inc.php');
require_once('../classes/user.inc.php');
require_once('../classes/student.inc.php');

// Check if user is logged in and is a student
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = unserialize($_SESSION['user']);
if ($user->getRole() !== 'student') {
    header('Location: index.php');
    exit;
}

// Get enrolled courses
$enrolledCourses = $user->viewMyCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enrolled Courses - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.inc.php'; ?>
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-800 text-white py-12">
        <div class="container mx-auto px-6">
            <h1 class="text-3xl font-bold">My Enrolled Courses</h1>
            <p class="mt-2">Welcome back, <?php echo htmlspecialchars($user->getName()); ?></p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <?php if (empty($enrolledCourses)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <i class="fas fa-book-open text-4xl text-gray-400 mb-4"></i>
                <h2 class="text-xl font-semibold mb-2">No Courses Yet</h2>
                <p class="text-gray-600 mb-4">You haven't enrolled in any courses yet.</p>
                <a href="index.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Browse Courses
                </a>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($enrolledCourses as $course): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-2">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </h2>
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                <?php echo htmlspecialchars($course['description']); ?>
                            </p>
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="fas fa-user-tie mr-2"></i>
                                <span><?php echo htmlspecialchars($course['teacher_name']); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <a href="course-content.php?id=<?php echo $course['id']; ?>" 
                                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    Go to Course
                                </a>
                                <a href="view_course.php?id=<?php echo $course['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Youdemy. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>