<?php
session_start();
require_once('../classes/teacher.inc.php');
require_once('../classes/course.inc.php');
require_once('../classes/statistique.inc.php');

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user']) || !unserialize($_SESSION['user'])->hasRole('teacher')) {
    header('Location: login.php');
    exit();
}

$teacher = unserialize($_SESSION['user']);
$message = '';

// Handle course modification
if (isset($_POST['action']) && $_POST['action'] == 'modify') {
    $courseId = $_POST['course_id'];
    $course = new Course(
        $_POST['title'],
        $_POST['description'],
        $_POST['content'],
        $_POST['category_id'],
        $teacher->getId(),
        $_POST['file_path']
    );
    if ($course->save()) {
        $message = "Course updated successfully!";
    } else {
        $message = "Failed to update course.";
    }
}

// Handle enrollment cancellation
if (isset($_POST['action']) && $_POST['action'] == 'cancel_enrollment') {
    $db = new Database();
    $conn = $db->connect();
    $stmt = $conn->prepare("DELETE FROM enrollment WHERE courseId = ? AND studentId = ?");
    if ($stmt->execute([$_POST['course_id'], $_POST['student_id']])) {
        $message = "Enrollment cancelled successfully!";
    } else {
        $message = "Failed to cancel enrollment.";
    }
}

// Get teacher's courses and statistics
$courseStats = $teacher->viewCourseStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include('navbar.inc.php'); ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Course Management</h1>
        
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Course Statistics -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Course Statistics</h2><?php echo $teacher->numcourses();?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($courseStats as $stat): ?>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-lg mb-2"><?php echo $stat['title']; ?></h3>
                        <p>Total Students: <?php echo $stat['total_students']; ?></p>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Course Management -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Manage Courses</h2>
            <?php
            $db = new Database();
            $conn = $db->connect();
            $stmt = $conn->prepare("
                SELECT c.*, GROUP_CONCAT(e.studentId) as enrolled_students 
                FROM course c 
                LEFT JOIN enrollment e ON c.id = e.courseId 
                WHERE c.teacherId = ? 
                GROUP BY c.id
            ");
            $stmt->execute([$teacher->getId()]);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php foreach ($courses as $course): ?>
                <div class="bg-white p-6 rounded-lg shadow mb-4">
                    <h3 class="font-bold text-xl mb-4"><?php echo$course['title']; ?></h3>
                    
                    <!-- Course Modification Form -->
                    <form action="" method="POST" class="mb-4">
                        <input type="hidden" name="action" value="modify">
                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2">Title</label>
                                <input type="text" name="title" value="<?php echo $course['title']; ?>" 
                                       class="w-full p-2 border rounded">
                            </div>
                            <div>
                                <label class="block mb-2">Description</label>
                                <textarea name="description" class="w-full p-2 border rounded"><?php echo $course['description']; ?></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">
                            Update Course
                        </button>
                    </form>

                    <!-- Enrolled Students -->
                    <?php if ($course['enrolled_students']): ?>
                        <div class="mt-4">
                            <h4 class="font-bold mb-2">Enrolled Students</h4>
                            <?php
                            $studentIds = explode(',', $course['enrolled_students']);
                            $placeholders = str_repeat('?,', count($studentIds) - 1) . '?';
                            $stmt = $conn->prepare("SELECT id, name FROM user WHERE id IN ($placeholders)");
                            $stmt->execute($studentIds);
                            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            
                            <div class="space-y-2">
                                <?php foreach ($students as $student): ?>
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                        <span><?php echo $student['name']; ?></span>
                                        <form action="" method="POST" class="inline">
                                            <input type="hidden" name="action" value="cancel_enrollment">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                                                Cancel Enrollment
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="mt-4 text-gray-500">No students enrolled yet.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
</body>
</html>