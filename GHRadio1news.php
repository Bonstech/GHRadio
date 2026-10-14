<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/subscription-popup.php'; 

$page_title = 'GHRadio1 News';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$featuredPost = getFeaturedPost();

$trendingPosts = getTrendingPosts(5);

$latestPosts = getLatestPosts($limit, $offset);
$categories = getAllCategories();

include 'includes/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <div class="lg:col-span-2">
        <?php if ($featuredPost): ?>
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-gray-900 to-gray-800 shadow-xl h-[400px] md:h-[450px]">
                <div class="absolute inset-0 bg-black/50 z-10"></div>
                <?php if (!empty($featuredPost['featured_image'])): ?>
                    <img src="<?php echo SITE_URL; ?>uploads/posts/<?php echo htmlspecialchars($featuredPost['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($featuredPost['title']); ?>"
                         class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-orange-500 to-green-600 flex items-center justify-center">
                        <i class="fas fa-newspaper text-white/30 text-8xl"></i>
                    </div>
                <?php endif; ?>
                
                <div class="absolute inset-0 z-20 flex items-end">
                    <div class="w-full px-6 pb-8 md:px-8 md:pb-10">
                        <div class="inline-block bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-semibold mb-3">
                            <i class="fas fa-star mr-1"></i> Featured
                        </div>
                        
                        <?php if (!empty($featuredPost['category_name'])): ?>
                            <div class="inline-block bg-white/20 backdrop-blur-sm text-white px-2.5 py-0.5 rounded-full text-[10px] font-semibold mb-2 ml-2">
                                <?php echo htmlspecialchars($featuredPost['category_name']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white leading-tight mb-2 drop-shadow-lg line-clamp-2">
                            <?php echo htmlspecialchars($featuredPost['title']); ?>
                        </h2>
                        
                        <p class="text-sm text-white/80 mb-3 line-clamp-2 max-w-2xl hidden sm:block">
                            <?php echo htmlspecialchars(substr(strip_tags($featuredPost['content'] ?? ''), 0, 150)) . '...'; ?>
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-3 text-xs text-white/70">
                            <div class="flex items-center space-x-2">
                                <img src="<?php echo SITE_URL; ?>uploads/profiles/<?php echo !empty($featuredPost['profile_picture']) ? htmlspecialchars($featuredPost['profile_picture']) : 'default.png'; ?>" 
                                     alt="<?php echo htmlspecialchars($featuredPost['username'] ?? 'Anonymous'); ?>"
                                     class="w-6 h-6 rounded-full object-cover border border-white/30">
                                <span class="font-medium text-white/90 text-xs">
                                    <?php 
                                    $author_name = !empty($featuredPost['firstname']) && !empty($featuredPost['lastname']) 
                                        ? $featuredPost['firstname'] . ' ' . $featuredPost['lastname'] 
                                        : ($featuredPost['username'] ?? 'Anonymous');
                                    echo htmlspecialchars($author_name);
                                    ?>
                                </span>
                            </div>
                            <span>•</span>
                            <span><i class="far fa-calendar-alt mr-1"></i> <?php echo formatDate($featuredPost['created_at']); ?></span>
                        </div>
                        
                        <div class="mt-3">
                            <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($featuredPost['slug']); ?>" 
                               class="inline-flex items-center bg-white text-gray-900 px-5 py-2 rounded-full font-semibold hover:bg-gray-100 transition text-sm shadow-lg">
                                Read More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="absolute inset-0 z-10 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
            </div>
        <?php else: ?>
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-orange-600 via-orange-500 to-green-600 h-[400px] md:h-[450px]">
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="relative z-10 h-full flex items-center justify-center text-center px-6">
                    <div>
                        <div class="inline-block bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-semibold mb-4 text-white">
                            <i class="fas fa-broadcast-tower mr-2"></i> GHRadio1 News
                        </div>
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-4">
                            Welcome to <span class="text-yellow-300">GHRadio1</span>
                        </h1>
                        <p class="text-white/80 mb-6 max-w-2xl mx-auto">Your trusted source for breaking news, politics, entertainment, sports, and more.</p>
                        <a href="#latest" class="inline-block bg-white text-orange-600 px-6 py-2.5 rounded-full font-semibold hover:bg-gray-100 transition">
                            <i class="fas fa-newspaper mr-2"></i> Latest News
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-4 h-[400px] md:h-[450px] overflow-y-auto">
            <div class="flex items-center justify-between mb-4 sticky top-0 bg-white py-2 z-10 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-800 flex items-center">
                    <i class="fas fa-fire text-orange-500 mr-2"></i>
                    Trending Now
                </h3>
            </div>
            
            <div class="space-y-2">
                <?php if (empty($trendingPosts)): ?>
                    <p class="text-gray-500 text-sm text-center py-4">No trending posts this week.</p>
                <?php else: ?>
                    <?php foreach ($trendingPosts as $index => $post): ?>
                        <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($post['slug']); ?>" 
                           class="flex items-center gap-3 group hover:bg-orange-50 p-2 rounded-lg transition <?php echo $index === 0 ? 'bg-orange-50 border border-orange-200' : ''; ?>">
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-800 group-hover:text-orange-500 transition line-clamp-2 text-sm">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </h4>
                                
                            </div>
                            <?php if ($index < 3): ?>
                                <span class="text-[10px] font-bold <?php echo $index === 0 ? 'text-red-500' : ($index === 1 ? 'text-orange-500' : 'text-yellow-500'); ?>">
                                    <?php echo $index === 0 ? '🔥' : ($index === 1 ? '⭐' : '👏'); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <?php if ($index < 9): ?>
                            <div class="border-b border-gray-50"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="latest" class="mb-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-clock text-orange-500 mr-2"></i>
            Latest News
            <span class="ml-3 h-1 w-12 gh-radio-gradient rounded"></span>
        </h2>
        <a href="top-stories.php" class="text-orange-500 hover:text-orange-600 text-sm font-semibold flex items-center">
            More Stories <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <?php if (empty($latestPosts)): ?>
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <p class="text-gray-500">No posts found.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <?php foreach ($latestPosts as $post): ?>
                <article class="bg-white rounded-xl shadow-sm overflow-hidden card-hover border border-gray-100 hover:border-orange-200 flex flex-col">
                    <?php if (!empty($post['featured_image'])): ?>
                        <img src="<?php echo SITE_URL; ?>uploads/posts/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>"
                             class="w-full h-32 md:h-44 object-cover">
                    <?php else: ?>
                        <div class="w-full h-32 md:h-44 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <i class="fas fa-newspaper text-gray-400 text-3xl"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-3 md:p-4 flex-1 flex flex-col">
                        <div class="flex items-center gap-1.5 md:gap-2 text-[9px] md:text-[10px] text-gray-500 mb-2 flex-wrap">
                            <?php if (!empty($post['category_name'])): ?>
                                <span class="bg-orange-100 text-orange-600 px-1.5 md:px-2 py-0.5 rounded-full font-semibold">
                                    <?php echo htmlspecialchars($post['category_name']); ?>
                                </span>
                                <span class="hidden sm:inline">•</span>
                            <?php endif; ?>
                            <span><i class="far fa-clock"></i> <?php echo timeAgo($post['created_at']); ?></span>
                        </div>
                        
                        <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($post['slug']); ?>" class="flex-1">
                            <h3 class="font-bold text-gray-800 hover:text-orange-500 transition line-clamp-2 text-xs md:text-sm mb-2">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                        </a>
                        
                        <p class="text-gray-600 line-clamp-2 text-[10px] md:text-xs hidden sm:block">
                            <?php echo htmlspecialchars(substr(strip_tags($post['content'] ?? ''), 0, 100)) . '...'; ?>
                        </p>
                        
                        <div class="flex items-center justify-between mt-2 md:mt-3 pt-2 border-t border-gray-100">
                            <div class="flex items-center space-x-2 md:space-x-3 text-gray-400 text-[9px] md:text-[10px]">
                                <span><i class="far fa-comment"></i> <?php echo $post['comment_count'] ?? 0; ?></span>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <?php if ($page > 1): ?>
            <div class="mt-8 flex justify-center">
                <a href="?page=<?php echo $page - 1; ?>" class="btn-primary px-6 py-2 rounded-full text-sm font-semibold">
                    <i class="fas fa-chevron-left mr-2"></i> Previous
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>


<style>
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

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #F97316;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #ea580c;
}

@media (max-width: 640px) {
    .h-\[400px\],
    .h-\[450px\] {
        height: auto;
        min-height: 300px;
    }
    
    .grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .grid-cols-4 {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>