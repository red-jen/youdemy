<?php
session_start();
require_once('../classes/config.inc.php');
require_once('../classes/admin.inc.php');
require_once('../classes/category.inc.php');
require_once('../classes/tags.inc.php');


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$admin = unserialize($_SESSION['user']);
if ($admin->getRole() !== 'admin') {
    header('Location: index.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'updateStatus':
                $result = $admin->updateUserStatus($_POST['userId'], $_POST['status']);
                $message = $result['message'];
                break;
            case 'deleteUser':
                $result = $admin->deleteUser($_POST['userId']);
                $message = $result['message'];
                break;
            case 'addCategory':
                $result = $admin->addCategory($_POST['categoryName']);
                $message = $result ? 'Category added successfully' : 'Failed to add category';
                break;
            case 'deleteCategory':
                $result = $admin->deleteCategory($_POST['categoryId']);
                $message = $result['message'];
                break;
            case 'addTag':
                $result = $admin->addTag($_POST['tagName']);
                $message = $result ? 'Tag added successfully' : 'Failed to add tag';
                break;
            case 'deleteTag':
                $result = $admin->deleteTag($_POST['tagId']);
                $message = $result ? 'Tag deleted successfully' : 'Failed to delete tag';
                break;
                case 'bulkAddTags':
                    $tagNames = explode("#", $_POST['tagNames']);
                    foreach($tagNames as $tag ){
                          $result = $admin->addTag($tag);
                    }
                   
                    $message = $result['message'];
                    break;
        }
    }
}


$users = $admin->getAllUsers();
$categories = Category::getAll();
$tags = Tag::getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'navbar.inc.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <?php if (isset($message)): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- User Management Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6">User Management</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($user['name']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($user['role']); ?></td>
                            <td class="px-6 py-4">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="updateStatus">
                                    <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" 
                                            class="rounded border-gray-300 text-sm">
                                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                        <option value="pending" <?php echo $user['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    </select>

                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="deleteUser">
                                    <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category Management Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Category Management</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Add Category Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Add New Category</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="addCategory">
                        <div class="flex gap-4">
                            <input type="text" name="categoryName" required
                                   class="flex-1 rounded border-gray-300"
                                   placeholder="Category Name">
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Add
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Category List -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Existing Categories</h3>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($categories as $category): ?>
                        <li class="py-3 flex justify-between items-center">
                            <span><?php echo htmlspecialchars($category['name']); ?></span>
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="deleteCategory">
                                <input type="hidden" name="categoryId" value="<?php echo $category['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
   





        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Tag Management</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Add Tag Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Add New Tag</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="addTag">
                        <div class="flex gap-4">
                            <input type="text" name="tagName" required
                                   class="flex-1 rounded border-gray-300"
                                   placeholder="Tag Name">
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Add
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tag List -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Existing Tags</h3>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($tags as $tag): ?>
                        <li class="py-3 flex justify-between items-center">
                            <span><?php echo htmlspecialchars($tag['name']); ?></span>
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="deleteTag">
                                <input type="hidden" name="tagId" value="<?php echo $tag['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>




        <!-- Global Statistics Section -->
<?php 
$globalStats = $admin->getGlobalStatistics();
?>
<div class="mb-12">
    <h2 class="text-2xl font-bold mb-6">Global Statistics</h2>
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Courses -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-2">Total Courses</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $globalStats['totalCourses']; ?></p>
        </div>
        
        <!-- Most Popular Course -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-2">Most Popular Course</h3>
            <?php if ($globalStats['mostPopularCourse']): ?>
            <p class="text-lg font-medium"><?php echo htmlspecialchars($globalStats['mostPopularCourse']['title']); ?></p>
            <p class="text-sm text-gray-600"><?php echo $globalStats['mostPopularCourse']['student_count']; ?> students</p>
            <?php else: ?>
            <p class="text-gray-500">No enrollments yet</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Courses by Category -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Courses by Category</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($globalStats['coursesByCategory'] as $category): ?>
            <div class="border rounded p-4">
                <h4 class="font-medium"><?php echo htmlspecialchars($category['category']); ?></h4>
                <p class="text-2xl font-bold text-blue-600"><?php echo $category['count']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Teachers -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Top 3 Teachers</h3>
        <div class="space-y-4">
            <?php foreach ($globalStats['topTeachers'] as $index => $teacher): ?>
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <span class="text-2xl font-bold mr-2">#<?php echo $index + 1; ?></span>
                    <span class="font-medium"><?php echo htmlspecialchars($teacher['teacher_name']); ?></span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600"><?php echo $teacher['course_count']; ?> courses</p>
                    <p class="text-sm text-gray-600"><?php echo $teacher['total_students']; ?> students</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Bulk Tag Addition -->
<div class="mb-12">
    <h2 class="text-2xl font-bold mb-6">Bulk Tag Addition</h2>
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST">
            <input type="hidden" name="action" value="bulkAddTags">
            <div class="mb-4">
                <label class="block mb-2">Add Multiple Tags</label>
                <textarea name="tagNames" 
                          class="w-full p-2 border rounded"
                          rows="4"
                          placeholder="Enter tags, one per line"></textarea>
            </div>
            <button type="submit" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add Tags
            </button>
        </form>
    </div>
</div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Youdemy. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>