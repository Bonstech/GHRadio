<?php
// search.php
require_once 'config/database.php';
require_once 'includes/functions.php';

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : null;
$date = isset($_GET['date']) ? sanitize($_GET['date']) : null;
$author = isset($_GET['author']) ? sanitize($_GET['author']) : null;

$results = [];
$search_performed = false;

if (!empty($query) || $category || $date || $author) {
    $search_performed = true;
    $results = searchPosts($query, $category, $date, $author);
}

$page_title = 'Search Results';
include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Search Results
        <?php if ($search_performed): ?>
            <span class="text-sm font-normal text-gray-500 ml-2">
                Found <?php echo count($results); ?> articles
            </span>
        <?php endif; ?>
    </h1>
    
    <?php if (!$search_performed): ?>
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Enter a search term above to find articles.</p>
        </div>
    <?php elseif (empty($results)): ?>
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <i class="fas fa-exclamation-circle text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No articles found matching your search criteria.</p>
            <a href="index.php" class="btn-primary inline-block mt-4 px-6 py-2 rounded-full text-sm font-semibold">
                <i class="fas fa-home mr-2"></i> Go Home
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($results as $post): ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden card-hover">
                    <div class="md:flex">
                        <?php if ($post['featured_image']): ?>
                            <div class="md:w-1/4">
                                <img src="<?php echo SITE_URL; ?>uploads/posts/<?php echo $post['featured_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     class="w-full h-48 md:h-full object-cover">
                            </div>
                        <?php endif; ?>
                        <div class="p-6 <?php echo $post['featured_image'] ? 'md:w-3/4' : 'w-full'; ?>">
                            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                                <?php if ($post['category_name']): ?>
                                    <span class="gh-radio-text font-semibold"><?php echo $post['category_name']; ?></span>
                                    <span>•</span>
                                <?php endif; ?>
                                <span><?php echo timeAgo($post['created_at']); ?></span>
                                <span>•</span>
                                <span><i class="far fa-eye"></i> <?php echo number_format($post['views']); ?></span>
                            </div>
                            
                            <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo $post['slug']; ?>">
                                <h3 class="text-xl font-bold text-gray-800 hover:text-orange-500 transition mb-2">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </h3>
                            </a>
                            
                            <p class="text-gray-600 line-clamp-2">
                                <?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 150)) . '...'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>