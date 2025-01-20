<?php
// course-content.php
session_start();
require_once('../classes/config.inc.php');
require_once('../classes/course.inc.php');
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

$courseId = $_GET['id'] ?? null;
if (!$courseId) {
    header('Location: index.php');
    exit;
}

$course = Course::getById($courseId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - Course Content</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include 'navbar.inc.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Course Title and Navigation -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($course['title']); ?></h1>
                <nav class="text-sm breadcrumbs">
                    <a href="index.php" class="text-blue-600 hover:underline">Courses</a>
                    <span class="mx-2">›</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($course['title']); ?></span>
                </nav>
            </div>

            <!-- Course Content -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-semibold mb-4">Course Materials</h2>
                
                <!-- Course Description -->
                <div class="prose max-w-none mb-8">
                    <?php echo nl2br(htmlspecialchars($course['description'])); ?>
                </div>

                <!-- Course Content -->
                <div class="prose max-w-none mb-8">
                    <h3 class="text-xl font-semibold mb-4">Course Content</h3>
                    <?php echo nl2br(htmlspecialchars($course['content'])); ?>
                </div>

                <!-- Course File -->
                <?php if ($course['file']): ?>
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-xl font-semibold mb-4">Course Materials</h3>
                        <?php
                        $fileExtension = strtolower(pathinfo($course['file'], PATHINFO_EXTENSION));
                        if (in_array($fileExtension, ['mp4', 'webm'])): ?>
                            <div class="aspect-w-16 aspect-h-9">
                                <video controls class="w-full rounded-lg shadow">
                                    <source src="<?php echo $course['file']; ?>" type="video/<?php echo $fileExtension; ?>">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        <?php elseif ($fileExtension === 'pdf'): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <a href="<?php echo $course['file']; ?>" 
                                   class="flex items-center text-blue-600 hover:underline"
                                   target="_blank">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    View Course PDF
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <a href="<?php echo $course['file']; ?>" 
                                   class="flex items-center text-blue-600 hover:underline"
                                   download>
                                    <i class="fas fa-file mr-2"></i>
                                    Download Course Material
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>