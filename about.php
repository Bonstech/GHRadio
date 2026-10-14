<?php
Require_once 'config/database.php';
$stream_url = "https://stream.zeno.fm/dzgdmrn0c5zuv";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More information about GHRadio1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="GHRadio1" />
    <link rel="manifest" href="/site.webmanifest" />
</head>
<body>
    <header class="bg-gradient-to-r from-[#096C62] to-[#E9B138] text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="index.php">
                <div href="index.php" class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center mr-3">
                    <i href="index.php"><img class="rounded-full" src="assets/images/GHRadiologo.jpg" alt="logoimage"></i>
                </div>
                </a>
                <span class="text-xl font-bold"><p>GHRadio1</p><p class="ml-2 text-sm">Real Music Power</p></span>
                
            </div>
            
            <nav class="hidden md:flex space-x-8">
                <!--<a href="#" class="hover:text-purple-300 transition">About</a>-->
                <a href="contact.php" class="hover:text-purple-300 transition">Contact</a>
                <a href="schedules.php" class="hover:text-purple-300 transition">Schedules</a>
            </nav>
            
            <button class="md:hidden focus:outline-none" id="menu-toggle">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        
        <div class="md:hidden hidden bg-gray-800 py-2 px-4" id="mobile-menu">
            <a href="contact.php" class="block py-2 hover:text-purple-300 transition">Contact</a>
            <a href="schedules.php" class="block py-2 hover:text-purple-300 transition">Schedules</a>
        </div>
    </header>


    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-center text-gray-800 mb-6">
            Learn More About GHRadio1
        </h1>
        
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md p-6 sm:p-8 md:p-10">
            <p class="text-base sm:text-lg md:text-xl text-gray-700 leading-relaxed sm:leading-loose text-center">
                Welcome to GHRadio1, your premier online radio station broadcasting from Kumasi, Ghana. Our mission is to educate, entertain, and globally promote Ghanaian culture through the power of authentic Ghanaian music. With a focus on highlife, Afrobeat, and contemporary sounds, we connect the world to the rich musical heritage of Ghana. Our engaging programs are designed to resonate with both local listeners and the Ghanaian diaspora.
            </p>
            
            <p class="mt-6 text-base sm:text-lg md:text-xl text-gray-700 leading-relaxed sm:leading-loose text-center">
                At GHRadio1, we are committed to offering a diverse range of shows that celebrate Ghana's vibrant culture, while also serving as a platform for discussions on entertainment and social issues. Join us as we project the real music power of Ghana to the world.
            </p>
            
            <p class="mt-6 text-base sm:text-lg text-gray-700 font-medium text-center">
                Stay connected with us through our website and social media for the latest updates and live streaming.
            </p>
        </div>
    </div>
    
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        document.addEventListener('DOMContentLoaded', function() {
        const player = document.getElementById('radioPlayer');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const playIcon = playPauseBtn.querySelector('i');
        
        function updateButton() {
            if (player.paused) {
                playIcon.className = 'fas fa-play text-white';
            } else {
                playIcon.className = 'fas fa-pause text-white';
            }
        }
        
        playPauseBtn.addEventListener('click', function() {
            if (player.paused) {
                player.play();
            } else {
                player.pause();
            }
            updateButton();
        });
    
    player.addEventListener('play', updateButton);
    player.addEventListener('pause', updateButton);
    player.addEventListener('loadstart', updateButton);
    
    updateButton();
});
    </script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>