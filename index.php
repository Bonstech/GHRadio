<?php

include 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/subscription-popup.php'; 

$conn = getDB();

$trendingPosts = getTrendingPosts(5);

$latestArticles = [];
$latestResult = $conn->query("
    SELECT id, title, slug, featured_image, created_at 
    FROM posts 
    WHERE status = 'approved' 
    ORDER BY created_at DESC 
    LIMIT 3
");
if ($latestResult && $latestResult->num_rows > 0) {
    while ($row = $latestResult->fetch_assoc()) {
        $row['category_name'] = 'News'; // Add default category
        $latestArticles[] = $row;
    }
}


$stream_url = "https://stream.zeno.fm/dzgdmrn0c5zuv";
$ogTitle = "GHRadio1";
$ogDescription = "Are you looking for the best online radio in Ghana and beyond? Look no further, GHRadio1 is always available for you";
$ogUrl = "https://www.ghradio1.com/";
$ogImage = "https://www.ghradio1.com/GHRadiologo.jpg";
$ogType = "website";
$ogSiteName = "GHRadio1";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GHRadio1 - Real Music Power!</title>
    
    <!-- Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($ogTitle) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($ogDescription) ?>" />
    <meta property="og:url" content="<?= htmlspecialchars($ogUrl) ?>" />
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>" />
    <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>" />
    <meta property="og:site_name" content="<?= htmlspecialchars($ogSiteName) ?>" />
    
    <!-- Favicons -->
   <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="GHRadio1" />
    <link rel="manifest" href="/site.webmanifest" />
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-X9GFKHWSDC"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-X9GFKHWSDC');
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
       
        * { box-sizing: border-box; }
        
        body {
            background: #000;
            color: #fff;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(1.8); }
        }
        
        .soundwave {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 40px;
        }

        .btn-primary {
            background: #ea580c;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #ea580c;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(249, 115, 22, 0.3);
        }
        
        .pulse {
            width: 6px;
            background: linear-gradient(to top, #E79F04, #9b59b6);
            border-radius: 3px;
            animation: pulse 1.2s ease infinite;
        }
        .pulse:nth-child(1) { animation-delay: 0.0s; height: 15px; }
        .pulse:nth-child(2) { animation-delay: 0.2s; height: 25px; }
        .pulse:nth-child(3) { animation-delay: 0.4s; height: 35px; }
        .pulse:nth-child(4) { animation-delay: 0.3s; height: 30px; }
        .pulse:nth-child(5) { animation-delay: 0.1s; height: 20px; }
        .pulse:nth-child(6) { animation-delay: 0.5s; height: 40px; }
        .pulse:nth-child(7) { animation-delay: 0.15s; height: 25px; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .carousel-height {
            height: 350px;
        }
        @media (min-width: 768px) { .carousel-height { height: 400px; } }
        @media (min-width: 1024px) { .carousel-height { height: 450px; } }
        
        .carousel-item {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            position: absolute;
            inset: 0;
        }
        .carousel-item.active {
            opacity: 1;
            z-index: 10;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .volume-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            background: #333;
            border-radius: 999px;
            cursor: pointer;
        }
        .volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #E79F04;
            cursor: pointer;
            box-shadow: 0 0 10px #E79F04;
        }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #096C62, #E9B138); border-radius: 4px; }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        
        .mobile-menu {
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .mobile-menu.open {
            display: block;
            max-height: 500px;
        }
        @media (max-width: 768px) {
            .mobile-menu { display: block; }
        }
        
        @media (max-width: 768px) {
            .soundwave { height: 30px; gap: 3px; }
            .pulse { width: 4px; }
        }
    </style>
</head>
<body>
    
   
    <header class="sticky top-0 z-50 bg-gradient-to-r from-[#096C62] to-[#E9B138] shadow-lg backdrop-blur-sm bg-opacity-95">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-purple-600 p-0.5">
                            <div class="w-full h-full rounded-full bg-black overflow-hidden">
                                <img class="w-full h-full object-cover" src="assets/images/GHRadiologo.jpg" alt="GHRadio1 Logo">
                            </div>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-black"></div>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">GHRadio1</h1>
                        <p class="text-xs text-white/80">Real Music Power</p>
                    </div>
                </div>
                
                <!-- Desktop  -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="about.php" target="_blank" class="font-medium hover:text-white/80 transition relative group">
                        About
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
                    </a>
                    <a href="contact.php" target="_blank" class="font-medium hover:text-white/80 transition relative group">
                        Contact
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
                    </a>
                    <a href="GHRadio1news.php" target="_blank" class="font-medium hover:text-white/80 transition relative group">
                        News
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
                    </a>
                    <a href="schedules.php" target="_blank" class="font-medium hover:text-white/80 transition relative group">
                        Schedules
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
                    </a>
                </nav>
                
                <!-- Mobile -->
                <button class="md:hidden text-2xl focus:outline-none" id="menu-toggle" aria-label="Toggle menu">
                    <i class="fas fa-bars" id="menu-icon"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile  -->
        <div class="md:hidden mobile-menu bg-gray-900 border-t border-white/10" id="mobile-menu">
            <div class="container mx-auto px-4 py-3">
                <a href="about.php" target="_blank" class="block py-2 hover:bg-white/10 px-3 rounded transition">About</a>
                <a href="contact.php" target="_blank" class="block py-2 hover:bg-white/10 px-3 rounded transition">Contact</a>
                <a href="GHRadio1news.php" target="_blank" class="block py-2 hover:bg-white/10 px-3 rounded transition">News</a>
                <a href="schedules.php" target="_blank" class="block py-2 hover:bg-white/10 px-3 rounded transition">Schedules</a>
            </div>
        </div>
    </header>

    
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        
        <section class="mb-12">
            <div class="max-w-5xl mx-auto">
                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl border border-white/10">
                    <div class="h-2 bg-gradient-to-r from-orange-500 via-purple-500 to-[#E9B138]"></div>
                    <div class="p-6 bg-gradient-to-br from-gray-900 to-black">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-orange-500 to-purple-600 p-0.5 shadow-lg">
                                    <div class="w-full h-full rounded-xl bg-gray-800 overflow-hidden">
                                        <img id="Program-image" class="w-full h-full object-cover" src="assets/images/GHRadiologo.jpg" alt="Album cover">
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-300">Currently Playing</p>
                                    <p class="text-lg font-bold text-white">Live Stream</p>
                                    <div class="flex items-center gap-4 mt-1">
                                        <p class="text-xs text-gray-400">Show: <span id="current-schedule" class="text-orange-400 font-medium">REAL MUSIC POWER</span></p>
                                        <p class="text-xs text-gray-400">Host: <span id="Host-Name" class="text-orange-400 font-medium">GHRadio1</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="soundwave">
                                <div class="pulse"></div>
                                <div class="pulse"></div>
                                <div class="pulse"></div>
                                <div class="pulse"></div>
                                <div class="pulse"></div>
                                <div class="pulse"></div>
                                <div class="pulse"></div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                            <div class="flex items-center space-x-4">
                                <button id="skip-backward" 
                                        class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white transition flex items-center justify-center"
                                        aria-label="Restart stream"
                                        title="Restart from beginning">
                                    <i class="fas fa-step-backward"></i>
                                </button>
                                
                                <button id="play-button" 
                                        class="w-16 h-16 rounded-full bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white transition flex items-center justify-center shadow-lg"
                                        aria-label="Play / Pause">
                                    <i class="fas fa-play text-2xl" id="play-icon"></i>
                                </button>
                                
                                <button id="skip-forward" 
                                        class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white transition flex items-center justify-center"
                                        aria-label="Jump to live"
                                        title="Jump to live">
                                    <i class="fas fa-step-forward"></i>
                                </button>
                            </div>
                                                        <div class="flex items-center space-x-6">
                                <div class="relative group">
                                    <button id="volume-btn" class="text-2xl text-white hover:text-orange-400 transition">
                                        <i id="volume-icon" class="fas fa-volume-up"></i>
                                    </button>
                                    <div id="volume-control" class="hidden absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 bg-gray-800 rounded-xl shadow-xl p-4 w-40 border border-gray-700 group-hover:block">
                                        <input type="range" id="volume-slider" min="0" max="1" step="0.01" value="0.7" class="volume-slider">
                                        <div class="flex justify-between text-xs text-gray-400 mt-2">
                                            <span>0%</span>
                                            <span>100%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <button id="like-btn" class="text-2xl text-white hover:text-red-500 transition">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <span id="like-count" class="text-white font-medium">8310</span>
                                </div>
                                
                                <button id="share-button" class="text-2xl text-white hover:text-orange-400 transition">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div id="player-status" class="text-center text-sm text-gray-300 mt-4 hidden">Stopped</div>
                    </div>
                </div>
            </div>
            
            <audio id="radio-player" preload="auto" crossorigin="anonymous" style="display: none;">
                <source id="radio-source" src="https://stream.zeno.fm/ghradio1" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </section>
        
       
        <section class="mb-16">
            <div class="flex items-center justify-between mb-8">
               
                    <a href="GHRadio1news.php" target="_blank" class="inline-block bg-[#ea580c] px-6 py-2 rounded-full font-semibold hover:bg-orange-700 transition">
                             More latest news <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i>
                    </a>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <?php if (!empty($latestArticles)): ?>
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl carousel-height">
                            <div class="carousel-container relative w-full h-full">
                                <?php foreach ($latestArticles as $index => $article): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                        <?php if (!empty($article['featured_image'])): ?>
                                            <img src="uploads/posts/<?php echo htmlspecialchars(basename($article['featured_image'])); ?>" 
                                                 alt="<?php echo htmlspecialchars_decode(htmlspecialchars($article['title'])); ?>" 
                                                 class="w-full h-full object-cover" />
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gradient-to-br from-indigo-900 via-purple-800 to-amber-700 flex items-center justify-center">
                                                <span class="text-white text-4xl font-black tracking-wider">GHRadio1</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent flex items-end">
                                            <div class="p-5 md:p-7 text-white w-full">
                                                <span class="text-[10px] font-bold uppercase tracking-wider bg-gradient-to-r from-amber-400 to-purple-500 px-3 py-1 rounded-full inline-block mb-3">
                                                    <?php echo htmlspecialchars($article['category_name'] ?? 'News'); ?>
                                                </span>
                                                <h3 class="text-xl md:text-2xl lg:text-3xl font-bold leading-tight line-clamp-2">
                                                    <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($article['slug']); ?>" 
                                                       class="hover:underline decoration-amber-300 underline-offset-4 transition hover:text-amber-300">
                                                        <?php echo htmlspecialchars_decode(htmlspecialchars($article['title'])); ?>
                                                    </a>
                                                </h3>
                                                <p class="text-white/50 text-xs mt-2 flex items-center gap-2">
                                                    <i class="far fa-clock"></i> 
                                                    <?php echo date('M j, Y', strtotime($article['created_at'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($latestArticles) > 1): ?>
                                <button class="carousel-prev absolute left-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white w-9 h-9 rounded-full flex items-center justify-center backdrop-blur-sm transition z-20 border border-white/10">
                                    <i class="fas fa-chevron-left text-sm"></i>
                                </button>
                                <button class="carousel-next absolute right-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white w-9 h-9 rounded-full flex items-center justify-center backdrop-blur-sm transition z-20 border border-white/10">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </button>
                            <?php endif; ?>

                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                                <?php foreach ($latestArticles as $index => $article): ?>
                                    <button class="carousel-indicator w-2 h-2 rounded-full bg-white/40 transition-all <?php echo $index === 0 ? 'bg-white scale-125' : ''; ?>" data-index="<?php echo $index; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="glass-card rounded-2xl p-10 text-center">
                            <i class="fas fa-newspaper text-4xl text-white/20 mb-4"></i>
                            <p class="text-white/40 text-lg">No articles yet. Stay tuned!</p>
                            <p class="text-white/20 text-sm mt-2">Articles will appear here once published.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="glass-card rounded-2xl p-6 border border-white/5">
                    <h4 class="text-lg font-bold text-white/90 mb-4 flex items-center">
                        <i class="fas fa-fire text-orange-400 mr-2"></i> Trending Now
                    </h4>
                    <?php if (!empty($trendingPosts)): ?>
                        <div class="space-y-3">
                            <?php foreach ($trendingPosts as $article): ?>
                                <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($article['slug']); ?>" 
                                   class="flex items-center gap-3 group hover:bg-white/5 p-2 rounded-lg transition">
                                    <?php if (!empty($article['featured_image'])): ?>
                                        <img src="uploads/posts/<?php echo htmlspecialchars(basename($article['featured_image'])); ?>" 
                                             alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                             class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-900 to-amber-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-newspaper text-white/30"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-white group-hover:text-amber-400 transition line-clamp-2">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </p>
                                       
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-white/40 text-sm">No trending articles yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        
        <section class="max-w-4xl mx-auto mb-12">
            <div class="glass-card rounded-2xl p-6 text-center border border-white/10">
                <h3 class="text-2xl font-bold mb-4 bg-gradient-to-r from-orange-500 to-purple-600 bg-clip-text text-transparent">
                    Support GHRadio1
                </h3>
                <p class="text-gray-300 mb-6">Help us keep the music playing and the content flowing</p>
                
                <button id="payment-toggle" class="px-8 py-3 bg-gradient-to-r from-[#096C62] to-[#E9B138] text-white rounded-full font-semibold hover:shadow-lg hover:scale-105 transition transform">
                    Donate
                </button>
                
                <div id="payment-options" class="hidden mt-8 flex flex-col md:flex-row justify-center gap-8">
                    <!-- PayPal -->
                    <a href="https://www.paypal.me/ghradio1" target="_blank" class="group flex flex-col items-center p-4 rounded-xl hover:bg-white/5 transition">
                        <svg width="64px" height="64px" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="20" fill="#0070BA"></circle>
                            <path d="M32.3305 18.0977C32.3082 18.24 32.2828 18.3856 32.2542 18.5351C31.2704 23.5861 27.9046 25.331 23.606 25.331H21.4173C20.8916 25.331 20.4486 25.7127 20.3667 26.2313L19.2461 33.3381L18.9288 35.3527C18.8755 35.693 19.1379 36 19.4815 36H23.3634C23.8231 36 24.2136 35.666 24.286 35.2127L24.3241 35.0154L25.055 30.3772L25.1019 30.1227C25.1735 29.6678 25.5648 29.3338 26.0245 29.3338H26.6051C30.3661 29.3338 33.3103 27.8068 34.1708 23.388C34.5303 21.5421 34.3442 20.0008 33.393 18.9168C33.1051 18.59 32.748 18.3188 32.3305 18.0977Z" fill="white" fill-opacity="0.6"></path>
                            <path d="M31.3009 17.6871C31.1506 17.6434 30.9955 17.6036 30.8364 17.5678C30.6766 17.5328 30.5127 17.5018 30.3441 17.4748C29.754 17.3793 29.1074 17.334 28.4147 17.334H22.5676C22.4237 17.334 22.2869 17.3666 22.1644 17.4254C21.8948 17.5551 21.6944 17.8104 21.6459 18.1229L20.402 26.0013L20.3662 26.2311C20.4481 25.7126 20.8911 25.3308 21.4168 25.3308H23.6055C27.9041 25.3308 31.2699 23.5851 32.2537 18.5349C32.2831 18.3854 32.3078 18.2398 32.33 18.0975C32.0811 17.9655 31.8115 17.8525 31.5212 17.7563C31.4496 17.7324 31.3757 17.7094 31.3009 17.6871Z" fill="white" fill-opacity="0.8"></path>
                            <path d="M21.6461 18.1231C21.6946 17.8105 21.895 17.5552 22.1646 17.4264C22.2879 17.3675 22.4239 17.3349 22.5678 17.3349H28.4149C29.1077 17.3349 29.7542 17.3803 30.3444 17.4757C30.513 17.5027 30.6768 17.5338 30.8367 17.5687C30.9957 17.6045 31.1508 17.6443 31.3011 17.688C31.3759 17.7103 31.4498 17.7334 31.5222 17.7564C31.8125 17.8527 32.0821 17.9664 32.331 18.0976C32.6237 16.231 32.3287 14.9601 31.3194 13.8093C30.2068 12.5424 28.1986 12 25.629 12H18.169C17.6441 12 17.1963 12.3817 17.1152 12.9011L14.0079 32.5969C13.9467 32.9866 14.2473 33.3381 14.6402 33.3381H19.2458L20.4022 26.0014L21.6461 18.1231Z" fill="white"></path>
                        </svg>
                        <span class="mt-2 text-white group-hover:text-[#E9B138] transition">PayPal</span>
                    </a>
                    
                    <a href="payment.html" target="blank" class="group flex flex-col items-center p-4 rounded-xl hover:bg-white/5 transition">
                        <svg fill="#ffffff" width="64px" height="54px" viewBox="0 0 210 256" xmlns="http://www.w3.org/2000/svg">
                            <path d="M174.55,144.5L174.55,144.5c-1.4-4-5-6.5-9.2-6.5H159V23c0-11.708-9.292-21-21-21H25C12.57,2,2,12.57,2,25v183 c0,11.9,10.95,22,22.75,22l114.213,0c1.207,0,2.27,0.984,2.18,2.188c-0.095,1.266-1.153,1.812-2.393,1.812h-45.5L128,254h80 L174.55,144.5z M82.05,220.2c-3.199,0-5.599-2.399-5.6-5.598c-0.001-3.045,2.557-5.602,5.602-5.602 c3.199,0.001,5.598,2.401,5.598,5.6C87.55,217.8,85.25,220.2,82.05,220.2z M144,138h-19.65c-5.3,0-9.8,4.7-9.8,10l0,0 c0,5.3,4.5,10,9.8,10h19.8v42H18V31h126V138z M80.636,134.85c23.663,0,43-19.21,43-43s-19.21-43-43-43S37.763,68.188,37.763,91.977 C37.763,115.64,56.973,134.85,80.636,134.85z M78.261,95.497c-7.888-2.756-12.807-6.743-12.807-13.824 c0-6.531,4.325-11.662,12.255-13.273v-7.082h5.937v6.743c4.919,0,8.481,1.018,11.11,2.417l-2.417,8.481 c-1.951-0.806-5.131-2.163-9.499-2.163s-6.743,2.163-6.743,4.325c0,2.968,2.756,4.113,8.693,6.531 c8.1,2.968,11.874,7.082,11.874,13.824c0,6.743-4.113,11.874-12.807,13.824v6.743h-5.937v-6.531 c-5.131,0-10.305-1.357-12.807-2.756l2.163-8.693c2.756,1.357,7.082,2.968,11.662,2.968c4.919,0,7.336-2.163,7.336-5.131 S83.944,97.702,78.261,95.497z"></path>
                        </svg>
                        <span class="mt-2 text-white group-hover:text-[#E9B138] transition">Mobile Money</span>
                    </a>
                </div>
            </div>
        </section>
    </main>
    
    
    <a href="<?php echo SITE_URL; ?>login.php" 
       target="_blank"
       class="fixed bottom-6 right-6 z-50 flex items-center bg-[#ea580c] text-white px-4 py-3 rounded-full shadow-lg hover:bg-orange-700 transition group hover:scale-105"
       id="whatsappFloat">
        <span class="text-2xl font-semibold">Login</span>
    </a>
    
    <script src="scriptt.js?v=2"></script>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            
            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    mobileMenu.classList.toggle('open');
                    
                    if (menuIcon) {
                        menuIcon.classList.toggle('fa-bars');
                        menuIcon.classList.toggle('fa-times');
                    }
                });
            }
            
            document.addEventListener('click', function(e) {
                if (mobileMenu && menuToggle) {
                    if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                        mobileMenu.classList.remove('open');
                        if (menuIcon) {
                            menuIcon.classList.add('fa-bars');
                            menuIcon.classList.remove('fa-times');
                        }
                    }
                }
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (mobileMenu && mobileMenu.classList.contains('open')) {
                        mobileMenu.classList.remove('open');
                        if (menuIcon) {
                            menuIcon.classList.add('fa-bars');
                            menuIcon.classList.remove('fa-times');
                        }
                    }
                }
            });
        });
        
        
        document.addEventListener('DOMContentLoaded', function() {
            const carouselItems = document.querySelectorAll('.carousel-item');
            const indicators = document.querySelectorAll('.carousel-indicator');
            const prevBtn = document.querySelector('.carousel-prev');
            const nextBtn = document.querySelector('.carousel-next');
            
            if (!carouselItems.length) return;
            
            let currentIndex = 0;
            let autoPlayInterval;
            
            function showSlide(index) {
                carouselItems.forEach(item => item.classList.remove('active'));
                carouselItems[index].classList.add('active');
                
                indicators.forEach(indicator => {
                    indicator.classList.remove('bg-white', 'scale-125');
                    indicator.classList.add('bg-white/50');
                });
                if (indicators[index]) {
                    indicators[index].classList.remove('bg-white/50');
                    indicators[index].classList.add('bg-white', 'scale-125');
                }
                currentIndex = index;
            }
            
            function nextSlide() {
                let nextIndex = (currentIndex + 1) % carouselItems.length;
                showSlide(nextIndex);
            }
            
            function prevSlide() {
                let prevIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
                showSlide(prevIndex);
            }
            
            function startAutoPlay() {
                autoPlayInterval = setInterval(nextSlide, 5000);
            }
            
            function stopAutoPlay() {
                clearInterval(autoPlayInterval);
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    stopAutoPlay();
                    nextSlide();
                    startAutoPlay();
                });
            }
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    stopAutoPlay();
                    prevSlide();
                    startAutoPlay();
                });
            }
            
            indicators.forEach(indicator => {
                indicator.addEventListener('click', () => {
                    stopAutoPlay();
                    let index = parseInt(indicator.getAttribute('data-index'));
                    showSlide(index);
                    startAutoPlay();
                });
            });
            
            const carousel = document.querySelector('.carousel-container');
            if (carousel) {
                carousel.addEventListener('mouseenter', stopAutoPlay);
                carousel.addEventListener('mouseleave', startAutoPlay);
            }
            
            startAutoPlay();
        });
        
        
        document.addEventListener('DOMContentLoaded', () => {
            const player = document.getElementById('radio-player');
            if (player) {
                player.play().catch(err => console.log('Autoplay blocked:', err));
            }
        });
        
        document.addEventListener('click', () => {
            const player = document.getElementById('radio-player');
            if (player && player.muted) {
                player.muted = false;
                player.play();
            }
        }, { once: true });
        
        const volumeBtn = document.getElementById('volume-btn');
        const volumeControl = document.getElementById('volume-control');
        
        if (volumeBtn && volumeControl) {
            volumeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                volumeControl.classList.toggle('hidden');
            });
            
            document.addEventListener('click', (e) => {
                if (!volumeControl.contains(e.target) && !volumeBtn.contains(e.target)) {
                    volumeControl.classList.add('hidden');
                }
            });
        }
        
        
        const paymentToggle = document.getElementById('payment-toggle');
        const paymentOptions = document.getElementById('payment-options');
        
        if (paymentToggle && paymentOptions) {
            paymentToggle.addEventListener('click', function() {
                paymentOptions.classList.toggle('hidden');
            });
        }
    </script>
   
</body>

</html>
 <?php require_once 'includes/footer.php'; ?>