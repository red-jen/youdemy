<?php
// Ensure the session is started
require_once('../classes/user.inc.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user has the required role
function hasRole($requiredRole) {
    
     $SESSION = unserialize($_SESSION['user']);
    return $SESSION->getRole() === $requiredRole ? true : false;
}
?>

<nav class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo or Brand -->
            <div class="flex-shrink-0 flex items-center">
                <a href="index.php" class="text-white text-2xl font-bold hover:text-gray-200 transition duration-300">
                    Youdemy
                </a>
            </div>

            <!-- Nav Links -->
            <div class="hidden md:flex space-x-4 items-center">
                <a href="index.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                    Home
                </a>
                <a href="statistics.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                    Courses
                </a>

                <?php if (isset($_SESSION['user'])): ?>
                    <?php if (hasRole('student') ): ?>
                        <a href="my_courses.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                            My Courses
                        </a>
                    <?php endif; ?>

                    <?php if (hasRole('teacher')): ?>
                        <a href="creat_course.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                            Create Course
                        </a>
                    <?php endif; ?>

                    <?php if (hasRole('admin')): ?>
                   
                        <a href="dashbord.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                      Dash Board
                        </a>
                    <?php endif; ?>

                    <a href="profile.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        Profile
                    </a>
                    <a href="logout.php" class="text-white hover:bg-red-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        Login
                    </a>
                    <a href="signup.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        Register
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex md:hidden items-center">
                <button id="mobile-menu-button" class="text-white hover:text-gray-200 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="index.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                Home
            </a>
            <a href="courses.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                Courses
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (hasRole('student') || hasRole('teacher')): ?>
                    <a href="my_courses.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                        My Courses
                    </a>
                <?php endif; ?>

                <?php if (hasRole('teacher')): ?>
                    <a href="create_course.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                        Create Course
                    </a>
                <?php endif; ?>

                <?php if (hasRole('admin')): ?>
                    <a href="manage_users.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                        Manage Users
                    </a>
                    <a href="manage_categories.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                        Manage Categories
                    </a>
                    <a href="manage_tags.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                        Manage Tags
                    </a>
                <?php endif; ?>

                <a href="profile.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                    Profile
                </a>
                <a href="logout.php" class="text-white block hover:bg-red-600 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                    Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                    Login
                </a>
                <a href="register.php" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium transition duration-300">
                    Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Mobile Menu Script -->
<script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>