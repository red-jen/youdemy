<?php
session_start();
require_once 'classes/classes.php';

$visitor = new Visitor();
$searchResults = [];
$allCourses = Visitor::viewCourseCatalog();

if (isset($_GET['search'])) {
    $searchResults = $visitor->searchCourses($_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youdemy - Learn Anything, Anytime</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .course-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Hero Section -->
    <header class="relative bg-gradient-to-r from-blue-600 to-indigo-800 text-white">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold">Youdemy</div>
            <div class="space-x-4">
                <?php if (!isset($_SESSION['user'])): ?>
                    <a href="pages/login.php" class="hover:text-gray-300">Login</a>
                    <a href="pages/signup.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100">Sign Up</a>
                <?php else: ?>
                    <a href="pages/creat_course.php">creat post</a>
                    <a href="dashboard.php" class="hover:text-gray-300">Dashboard</a>
                    <a href="pages/logout.php" class="hover:text-gray-300">Logout</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="container mx-auto px-6 py-20">
            <div class="text-center">
                <h1 class="text-5xl font-bold mb-4">Unlock Your Potential</h1>
                <p class="text-xl mb-8">Learn from industry experts and transform your career</p>
                
                <!-- Search Bar -->
                <form action="" method="GET" class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input 
                            type="search" 
                            name="search" 
                            placeholder="Search for courses..."
                            class="w-full relative z-40 px-6 py-4 text-gray-700 bg-white rounded-full focus:outline-none focus:shadow-outline text-lg"
                        >
                        <button type="submit" class="absolute right-0 top-0 mt-4 mr-6">
                            <i class="fas fa-search text-gray-500 text-xl"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Wave Effect -->
        <div class="absolute bottom-0 w-full">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 220">
                <path fill="#F9FAFB" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <!-- Features Section -->
        <section class="grid md:grid-cols-3 gap-8 mb-20">
            <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Expert Teachers</h3>
                <p class="text-gray-600">Learn from industry professionals with real-world experience</p>
            </div>
            <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Flexible Learning</h3>
                <p class="text-gray-600">Study at your own pace, anytime and anywhere</p>
            </div>
            <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-certificate text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Certification</h3>
                <p class="text-gray-600">Earn certificates upon course completion</p>
            </div>
        </section>

        <!-- Courses Section -->
        <section>
            <h2 class="text-3xl font-bold mb-8">
                <?php echo isset($_GET['search']) ? 'Search Results' : 'Featured Courses'; ?>
            </h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                $displayCourses = !empty($searchResults) ? $searchResults : $allCourses;
                foreach ($displayCourses as $course): 
                ?>
                <div class="course-card bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="relative">
                        <!-- Placeholder image using a gradient -->
                        <div class="bg-gradient-to-r from-blue-400 to-indigo-500 h-48"></div>
                        <div class="absolute top-4 right-4 bg-yellow-400 text-xs font-bold px-2 py-1 rounded-full">
                            NEW
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-users mr-2"></i>500+ students
                            </span>
                            <a href="course-details.php?id=<?php echo $course['id']; ?>" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($displayCourses)): ?>
                <div class="text-center py-20">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-2xl font-semibold mb-2">No courses found</h3>
                    <p class="text-gray-600">Try adjusting your search terms or browse our course catalog</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Call to Action -->
    <section class="bg-gradient-to-r from-purple-600 to-indigo-800 text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-4">Start Learning Today</h2>
            <p class="text-xl mb-8">Join thousands of students already learning on Youdemy</p>
            <a href="signup.php" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                Get Started
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">Youdemy</h4>
                    <p class="text-gray-400">Empowering learners worldwide with quality education.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Categories</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Development</a></li>
                        <li><a href="#" class="hover:text-white">Business</a></li>
                        <li><a href="#" class="hover:text-white">Design</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-blue-400"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-pink-400"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-blue-400"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Youdemy. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>