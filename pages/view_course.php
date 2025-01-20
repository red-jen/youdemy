<?php
session_start();

require_once('../classes/config.inc.php');
require_once('../classes/category.inc.php');
require_once('../classes/tags.inc.php');
require_once('../classes/course.inc.php');
require_once('../classes/user.inc.php');
require_once('../classes/student.inc.php');
 require('../classes/classes.php');


// Get course ID from URL
$courseId = $_GET['id'] ?? null;
// if (!$courseId) {
//     header('Location: index.php');
//     exit;
// }

// Get course details
$course = Visitor::getCourseDetails($courseId);
// if (!$course) {
//     header('Location: index.php');
//     exit;
// }

// Check if user is logged in and is a student
$isStudent = false;
$isEnrolled = false;
if (isset($_SESSION['user'])) {
    $user = unserialize($_SESSION['user']);
    $isStudent = ($user->getRole() === 'student');
    
    if ($isStudent) {
        $result = $user->enrollInCourse($courseId);
        if($result['success'] === false ){
            $isEnrolled = true;
        }
    }
}

// Handle enrollment
if (isset($_POST['enroll']) && $isStudent && !$isEnrolled) {
    $result = $user->enrollInCourse($courseId);
    if ($result) {
        $isEnrolled = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.inc.php'; ?>

    <!-- Course Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-800 text-white py-16">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl">
                <h1 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($course['title']); ?></h1>
                <div class="flex items-center space-x-4 mb-6">
                    <span class="flex items-center">
                        <i class="fas fa-user-tie mr-2"></i>
                        <?php echo htmlspecialchars($course['teacher_name']); ?>
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-users mr-2"></i>
                        <?php echo $course['enrolled_students']; ?> students
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-folder mr-2"></i>
                        <?php echo htmlspecialchars($course['category_name']); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12">
        <div class="grid md:grid-cols-3 gap-12">
            <!-- Course Information -->
            <div class="md:col-span-2">
                <!-- About This Course -->
                <div class="bg-white rounded-xl shadow-md p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-4">About This Course</h2>
                    <p class="text-gray-600 mb-6">
                        <?php echo nl2br(htmlspecialchars($course['description'])); ?>
                    </p>
                    
                    <?php if (!empty($course['tags'])): ?>
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">Tags:</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($course['tags'] as $tag): ?>
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                    <?php echo htmlspecialchars($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Course Content -->
                <div class="bg-white rounded-xl shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-4">Course Content</h2>
                    <div class="prose max-w-none">
                        <?php echo nl2br(htmlspecialchars($course['content'])); ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                    <?php if ($isStudent && !$isEnrolled): ?>
                        <form method="POST" action="">
                            <button type="submit" name="enroll" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Enroll Now
                            </button>
                        </form>
                    <?php elseif ($isStudent && $isEnrolled): ?>
                        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">
                            <i class="fas fa-check-circle mr-2"></i>
                            You are enrolled in this course
                        </div>
                        <a href="content.php?id=<?php echo $courseId; ?>" 
                           class="block w-full bg-blue-600 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Go to Course Content
                        </a>
                    <?php elseif (!isset($_SESSION['user'])): ?>
                        <div class="text-center">
                            <p class="mb-4 text-gray-600">Sign up to enroll in this course</p>
                            <a href="pages/signup.php" class="block w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Sign Up Now
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Instructor Info -->
                    <div class="mt-8">
                        <h3 class="font-semibold mb-4">About the Instructor</h3>
                        <div class="flex items-center mb-4">
                            <div class="bg-gray-200 w-12 h-12 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-tie text-gray-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="font-semibold"><?php echo htmlspecialchars($course['teacher_name']); ?></div>
                                <div class="text-sm text-gray-600"><?php echo htmlspecialchars($course['teacher_email']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Youdemy. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>