<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Recent Articles - GHRadio1';
$page_description = 'Stay updated with the latest articles from GHRadio1. All articles published within the last 15 days, organized by category.';

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 flex items-center gap-3">
            <i class="fas fa-clock text-orange-500"></i>
            Recent Articles
        </h1>
        <p class="text-gray-600 mt-2">
            Articles published within the last 15 days, organized by category.
            <span class="text-sm text-gray-400 ml-2" id="articleCount"></span>
        </p>
        <div class="flex flex-wrap items-center gap-3 mt-3 text-sm text-gray-500">
            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full">
                <i class="far fa-calendar-alt mr-1"></i> Last 15 days
            </span>
            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full">
                <i class="fas fa-tags mr-1"></i> Grouped by category
            </span>
            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full">
                <i class="fas fa-newspaper mr-1"></i> Latest first
            </span>
        </div>
    </div>

    <?php
    // Get all categories
    $categories = getAllCategories();
    
    // Get articles published in the last 15 days
    $conn = getDB();
    $recent_articles = [];
    $total_articles = 0;
    
    foreach ($categories as $category) {
        $stmt = $conn->prepare("
            SELECT p.*, 
                   u.username, 
                   u.firstname, 
                   u.lastname, 
                   u.profile_picture,
                   c.name as category_name,
                   c.slug as category_slug,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') as comment_count
            FROM posts p 
            LEFT JOIN users u ON p.author_id = u.id 
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ? 
              AND p.status = 'approved' 
              AND p.created_at >= DATE_SUB(NOW(), INTERVAL 15 DAY)
            ORDER BY p.created_at DESC
        ");
        $stmt->bind_param("i", $category['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $category_articles = $result->fetch_all(MYSQLI_ASSOC);
        if (!empty($category_articles)) {
            $recent_articles[$category['name']] = [
                'category' => $category,
                'articles' => $category_articles
            ];
            $total_articles += count($category_articles);
        }
    }
    ?>

    <?php if (empty($recent_articles)): ?>
        <!-- No Articles Found -->
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-newspaper text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No Recent Articles</h3>
            <p class="text-gray-500">There are no articles published in the last 15 days.</p>
            <p class="text-sm text-gray-400 mt-2">Check back soon for new content!</p>
            <a href="index.php" class="btn-primary inline-block mt-4 px-6 py-2 rounded-full text-sm font-semibold">
                <i class="fas fa-home mr-2"></i> Go to Homepage
            </a>
        </div>
    <?php else: ?>
        <!-- Display Articles by Category -->
        <div class="space-y-10">
            <?php foreach ($recent_articles as $category_name => $data): ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <!-- Category Header -->
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-folder-open text-white text-xl"></i>
                            <h2 class="text-xl font-bold text-white">
                                <?php echo htmlspecialchars($category_name); ?>
                            </h2>
                            <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full">
                                <?php echo count($data['articles']); ?> articles
                            </span>
                        </div>
                        <a href="category.php?slug=<?php echo urlencode($data['category']['slug']); ?>" 
                           class="text-white/80 hover:text-white text-sm font-medium flex items-center gap-1">
                            View All <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    
                    <!-- Articles List -->
                    <div class="p-4 space-y-3">
                        <?php foreach ($data['articles'] as $index => $article): ?>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-lg hover:bg-gray-50 transition group <?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'; ?>">
                                <!-- Left: Title & Meta -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($article['slug']); ?>" 
                                           class="text-gray-800 hover:text-orange-500 font-medium text-sm md:text-base transition group-hover:underline">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </a>
                                        <?php if ($article['is_featured']): ?>
                                            <span class="bg-yellow-100 text-yellow-700 text-[10px] px-2 py-0.5 rounded-full font-semibold">
                                                <i class="fas fa-star mr-0.5"></i> Featured
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <img src="<?php echo SITE_URL; ?>uploads/profiles/<?php echo !empty($article['profile_picture']) ? htmlspecialchars($article['profile_picture']) : 'default.png'; ?>" 
                                                 alt="<?php echo htmlspecialchars($article['username'] ?? 'Anonymous'); ?>"
                                                 class="w-4 h-4 rounded-full object-cover">
                                            <?php 
                                            $author_name = !empty($article['firstname']) && !empty($article['lastname']) 
                                                ? $article['firstname'] . ' ' . $article['lastname'] 
                                                : ($article['username'] ?? 'Anonymous');
                                            echo htmlspecialchars($author_name);
                                            ?>
                                        </span>
                                        <span>•</span>
                                        <span><i class="far fa-calendar-alt"></i> <?php echo timeAgo($article['created_at']); ?></span>
                                        <span>•</span>
                                        <span><i class="far fa-eye"></i> <?php echo number_format($article['views'] ?? 0); ?></span>
                                        <span>•</span>
                                        <span><i class="far fa-comment"></i> <?php echo $article['comment_count'] ?? 0; ?></span>
                                        <?php if ($article['source_website']): ?>
                                            <span>•</span>
                                            <span class="text-gray-400">
                                                <i class="fas fa-newspaper"></i> <?php echo htmlspecialchars($article['source_website']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Right: Read Time & Action -->
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <i class="far fa-clock"></i>
                                        <?php 
                                        $word_count = str_word_count(strip_tags($article['content']));
                                        $read_time = ceil($word_count / 200);
                                        echo $read_time . ' min read';
                                        ?>
                                    </span>
                                    <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($article['slug']); ?>" 
                                       class="text-orange-500 hover:text-orange-600 text-sm">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Stats Footer -->
        <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span><i class="fas fa-newspaper text-orange-500"></i> <?php echo $total_articles; ?> total articles</span>
                <span>•</span>
                <span><i class="fas fa-tags text-blue-500"></i> <?php echo count($recent_articles); ?> categories</span>
                <span>•</span>
                <span><i class="fas fa-calendar-alt text-green-500"></i> Last 15 days</span>
            </div>
            <a href="index.php" class="text-orange-500 hover:text-orange-600 text-sm font-medium flex items-center gap-1">
                View all articles <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- Auto-refresh Notice -->
        <div class="mt-4 text-center text-xs text-gray-400">
            <i class="fas fa-sync-alt mr-1"></i> 
            Articles older than 15 days are automatically removed from this list.
            <span class="block text-gray-300 mt-1">Last updated: <?php echo date('F j, Y g:i A'); ?></span>
        </div>
    <?php endif; ?>
</div>

<style>
/* Hover effects */
.group:hover .group-hover\:underline {
    text-decoration: underline;
}

/* Smooth transitions */
.transition {
    transition: all 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .space-y-10 {
        gap: 1.5rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>