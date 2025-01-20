<?php
session_start();
require_once('../classes/teacher.inc.php');
require_once('../classes/course.inc.php');
require_once('../classes/enrollement.inc.php');

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user']) || !unserialize($_SESSION['user'])->hasRole('teacher')) {
    header('Location: login.php');
    exit();
}

$teacher = unserialize($_SESSION['user']);
$message = '';

// Handle course modification
if (isset($_POST['action']) && $_POST['action'] == 'modify') {
    $course = Course::getById($_POST['course_id']);
    if ($course && $course['teacherid'] == $teacher->getId()) {
        $success = new Course(
            $_POST['title'],
            $_POST['description'],
            $course['content'],
            $course['categoryId'],
            $teacher->getId(),
            $course['file']
        );
        $success->setId($_POST['course_id']);
        $success->save();
        
        $message = $success ? "Course updated successfully!" : "Failed to update course.";
    }
}

// Handle enrollment cancellation
if (isset($_POST['action']) && $_POST['action'] == 'cancel_enrollment') {
    $result = Enrollment::cancelEnrollment($_POST['course_id'], $_POST['student_id']);
    $message = $result['message'];
}
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $course = Course::getById($_POST['course_id']);
    if ($course && $course['teacherid'] == $teacher->getId()) {
        $success = new Course( $course['title'],
        $course['description'],
        $course['content'],
        $course['categoryId'],
        $teacher->getId(),
        $course['file']);
        $success->setId($_POST['course_id']);
    $result =  $success->delete($_POST['course_id']);
    $message = "deleted sucsessfully";
}
}

// Get teacher's courses and statistics
$courseStats = $teacher->viewCourseStatistics();
$totalCourses = $teacher->numcourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Statistics</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include('navbar.inc.php'); ?>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Course Statistics</h1>
            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                Total Courses: <?php echo $totalCourses; ?>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Course Statistics Overview -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Course Enrollment Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($courseStats as $stat): ?>
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                        <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($stat['title']); ?></h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Enrolled Students:</p>
                                <p class="text-2xl font-bold text-blue-600"><?php echo $stat['total_students']; ?></p>
                            </div>
                            <a href="#course-<?php echo $stat['id']; ?>" class="text-blue-500 hover:text-blue-700">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Detailed Course Management -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Course Details and Management</h2>
            
            <?php foreach ($courseStats as $stat): ?>
                <div id="course-<?php echo $stat['id']; ?>" class="bg-white p-6 rounded-lg shadow mb-4">
                    <h3 class="font-bold text-xl mb-4"><?php echo htmlspecialchars($stat['title']); ?></h3>
                    
                    <!-- Course Modification Form -->
                    <form action="" method="POST" class="mb-6">
                        <input type="hidden" name="action" value="modify">
                        <input type="hidden" name="course_id" value="<?php echo $stat['id']; ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2">Title</label>
                                <input type="text" name="title" 
                                       value="<?php echo htmlspecialchars($stat['title']); ?>" 
                                       class="w-full p-2 border rounded">
                            </div>
                            <div>
                                <label class="block mb-2">Description</label>
                                <textarea name="description" 
                                          class="w-full p-2 border rounded h-24"><?php echo htmlspecialchars($stat['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mt-4">
                            Update Course
                        </button>
                    </form>
                    <form action="" method="POST" class="mb-6">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="course_id" value="<?php echo $stat['id']; ?>">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mt-4">
                            delete Course
                        </button>
                    </form>

                    <!-- Enrolled Students List -->
                    <div class="mt-6">
                        <h4 class="font-bold text-lg mb-3">Enrolled Students</h4>
                        <?php 
                        $enrolledStudents = Enrollment::getEnrolledStudents($stat['id']);
                        if ($enrolledStudents): 
                        ?>
                            <div class="space-y-2">
                                <?php foreach ($enrolledStudents as $student): ?>
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                        <div>
                                            <span class="font-medium"><?php echo htmlspecialchars($student['name']); ?></span>
                                            <span class="text-gray-500 text-sm ml-2"><?php echo htmlspecialchars($student['email']); ?></span>
                                            <span class="text-gray-400 text-sm ml-2">
                                                Enrolled: <?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?>
                                            </span>
                                        </div>
                                        <form action="" method="POST" class="inline">
                                            <input type="hidden" name="action" value="cancel_enrollment">
                                            <input type="hidden" name="course_id" value="<?php echo $stat['id']; ?>">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <button type="submit" 
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                                Cancel Enrollment
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">No students enrolled yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
</body>
</html>