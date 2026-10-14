<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Top Articles - GHRadio1';
$page_description = 'The most read articles in each category on GHRadio1.';

include 'includes/header.php';

$conn = getDB();

$categories = [];
$result = $conn->query("SELECT * FROM categories ORDER BY id ASC");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$category_articles = [];
foreach ($categories as $category) {
    $stmt = $conn->prepare("
        SELECT p.id, p.title, p.slug, p.featured_image, p.content, 
               p.views, p.likes, p.dislikes, p.created_at,
               u.username, u.firstname, u.lastname, u.profile_picture,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') AS comment_count
        FROM posts p
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.status = 'approved' AND p.category_id = ?
        ORDER BY p.views DESC, p.created_at DESC
        LIMIT 15
    ");
    $stmt->bind_param("i", $category['id']);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    if (!empty($rows)) {
        $category_articles[] = [
            'category' => $category,
            'articles' => $rows,
            'total_views' => array_sum(array_column($rows, 'views'))
        ];
    }
}

$total_articles = array_sum(array_map(fn($c) => count($c['articles']), $category_articles));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        
        <?php if (!empty($category_articles)): ?>
        <div class="flex flex-wrap gap-2 mt-4">
            <?php foreach ($category_articles as $catData): ?>
                <a href="#cat-<?php echo urlencode($catData['category']['slug']); ?>"
                   class="text-xs font-semibold bg-white border border-gray-200 hover:border-orange-300 hover:text-orange-600 text-gray-600 px-3 py-1.5 rounded-full transition">
                    <?php echo htmlspecialchars($catData['category']['name']); ?>
                    
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if (empty($category_articles)): ?>
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-newspaper text-3xl text-gray-300"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-700 mb-2">No articles yet</h2>
            <p class="text-gray-500 text-sm mb-6">Articles will appear here once they are published.</p>
            <a href="index.php" class="btn-primary inline-block px-6 py-2.5 rounded-full text-sm font-semibold">
                <i class="fas fa-home mr-2"></i> Back to Home
            </a>
        </div>
    <?php else: ?>

    
        <div class="space-y-12">
            <?php 
            $categoryNumber = 1;
            foreach ($category_articles as $catData): 
                $cat = $catData['category'];
                $articles = $catData['articles'];
                $isFirst = $categoryNumber === 1;
            ?>
                <section id="cat-<?php echo urlencode($cat['slug']); ?>" class="scroll-mt-24">
                    
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5 pb-3 border-b-2 border-orange-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br <?php echo $isFirst ? 'from-orange-500 to-orange-600' : 'from-gray-100 to-gray-200'; ?> rounded-xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-folder-open <?php echo $isFirst ? 'text-white' : 'text-gray-500'; ?> text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-extrabold text-gray-800 flex items-center gap-2">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                    <?php if ($isFirst): ?>
                                        <span class="text-[10px] uppercase bg-gradient-to-r from-orange-500 to-orange-600 text-white px-2 py-0.5 rounded-full font-bold tracking-wider">
                                            Top Stories
                                        </span>
                                    <?php endif; ?>
                                </h2>
                              
                            </div>
                        </div>
                        
                        <a href="category.php?slug=<?php echo urlencode($cat['slug']); ?>"
                           class="text-sm font-semibold text-orange-500 hover:text-orange-600 flex items-center gap-1">
                            View All <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <?php 
                        foreach ($articles as $index => $article): 
                            $rank = $index + 1;
                            $isTopThree = $rank <= 3;
                            $rankColors = [
                                1 => 'from-yellow-400 to-yellow-500 text-yellow-900',
                                2 => 'from-gray-300 to-gray-400 text-gray-700',
                                3 => 'from-orange-400 to-orange-500 text-white'
                            ];
                          
                        ?>
                            <article class="group bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:border-orange-200 hover:shadow-lg transition-all duration-300 flex flex-col relative">

                                <a href="article.php?slug=<?php echo urlencode($article['slug']); ?>" class="block relative overflow-hidden">
                                    <?php if (!empty($article['featured_image'])): ?>
                                        <img src="<?php echo SITE_URL; ?>uploads/posts/<?php echo htmlspecialchars($article['featured_image']); ?>"
                                             alt="<?php echo htmlspecialchars($article['title']); ?>"
                                             class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-500"
                                             loading="lazy">
                                    <?php else: ?>
                                        <div class="w-full h-44 bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                                            <i class="fas fa-newspaper text-orange-300 text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <!-- Content -->
                                <div class="p-4 flex-1 flex flex-col">

                                    <!-- Title -->
                                    <a href="article.php?slug=<?php echo urlencode($article['slug']); ?>" class="flex-1">
                                        <h3 class="font-bold text-gray-800 group-hover:text-orange-500 transition text-sm leading-snug line-clamp-2 mb-2">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </h3>
                                    </a>

                                    <!-- Excerpt -->
                                    <p class="text-gray-600 text-xs line-clamp-2 mb-3">
                                        <?php echo htmlspecialchars(substr(strip_tags($article['content']), 0, 100)) . '...'; ?>
                                    </p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Divider -->
                    <?php if ($categoryNumber < count($category_articles)): ?>
                        <div class="mt-12 flex items-center justify-center">
                            <div class="h-px bg-gradient-to-r from-transparent via-orange-200 to-transparent w-full max-w-md"></div>
                        </div>
                    <?php endif; ?>
                </section>
            <?php 
                $categoryNumber++;
            endforeach; 
            ?>
        </div>

        <!-- ============================================================
             BACK TO TOP
             ============================================================ -->
        <div class="mt-14 text-center">
            <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-full font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 transition">
                <i class="fas fa-arrow-up"></i>
                Back to Top
            </a>
        </div>

    <?php endif; ?>
</div>

<style>
/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth scroll for anchor links */
html {
    scroll-behavior: smooth;
}
</style>

<?php include 'includes/footer.php'; ?>