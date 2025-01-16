<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Course - YouDemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
</head>
<body class="bg-gray-50">
    <?php
 
    include('../classes/config.inc.php');
    include('../classes/category.inc.php');
    include('../classes/tags.inc.php');
    include('../classes/course.inc.php');
    include('../classes/teacher.inc.php');

    session_start();
    
    // Check if user is logged in and is a teacher
    // if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    //     header('Location: login.php');
    //     exit();
    // }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $teacher = unserialize($_SESSION['user']);
        $teacher->addCourse(array("title"=> $_POST['title'],
       "description"=> $_POST['description'],
        "content"=> $_POST['content'],
        "category" => $_POST['category'],
        $_POST['tags']));
           
    

       // Add tags if selected
        if (isset($_POST['tags']) && is_array($_POST['tags'])) {
            foreach ($_POST['tags'] as $tagId) {
                $course->addTCag($tagId);
            }
        }

        if ($course->save()) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    Course created successfully!
                  </div>';
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    Error creating course.
                  </div>';
        }
    }

    // Get categories and tags for the form
    $categories = Category::getAll();
    $tags = Tag::getAll();
    ?>

    <div class="max-w-2xl mx-auto p-8">
        <h1 class="text-3xl font-bold mb-8">Create New Course</h1>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Course Title</label>
                <input type="text" name="title" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" rows="6" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tags</label>
                <div class="mt-2 space-y-2">
                    <?php foreach ($tags as $tag): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="tags[]" 
                                   value="<?php echo htmlspecialchars($tag['id']); ?>"
                                   class="rounded border-gray-300">
                            <label class="ml-2 text-sm text-gray-600">
                                <?php echo $tag['name']; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Create Course
                </button>
            </div>
        </form>
    </div>
</body>
</html>