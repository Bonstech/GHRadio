<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';
$category = null;

if (!empty($slug)) {
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
}

if (!$category) {
    header('Location: index.php');
    exit;
}

$page_title = $category['name'];

$posts = [];
$conn = getDB();
$stmt = $conn->prepare("
    SELECT p.*, u.username, u.profile_picture, u.bio,
           c.name AS category_name,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') AS comment_count
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'approved'
      AND p.category_id = ?
      AND p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ORDER BY p.created_at DESC
    LIMIT 50
");
$stmt->bind_param("i", $category['id']);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">

    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white">
                <i class="fas fa-folder-open text-sm"></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                <?php echo htmlspecialchars($category['name']); ?>
            </h1>
        </div>
       
    </div>

    <?php if (empty($posts)): ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-3xl text-gray-300"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-700 mb-2">No recent articles</h2>
            <p class="text-gray-500 text-sm mb-6">No articles in this category within the last 30 days.</p>
            <a href="GHRadio1news.php" class="btn-primary inline-block px-6 py-2.5 rounded-full text-sm font-semibold">
                <i class="fas fa-home mr-2"></i> Back to Home
            </a>
        </div>
    <?php else: ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($posts as $post): ?>
                <article class="bg-white rounded-xl shadow-sm overflow-hidden card-hover border border-gray-100 hover:border-orange-200 flex flex-col">

                    <!-- Featured Image -->
                    <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($post['slug']); ?>" class="block">
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?php echo SITE_URL; ?>uploads/posts/<?php echo htmlspecialchars($post['featured_image']); ?>"
                                 alt="<?php echo htmlspecialchars($post['title']); ?>"
                                 class="w-full h-48 object-cover"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                                <i class="fas fa-newspaper text-orange-300 text-4xl"></i>
                            </div>
                        <?php endif; ?>
                    </a>

                    <div class="p-4 flex-1 flex flex-col">

                        <div class="mb-2">
                            <span class="inline-block bg-orange-100 text-orange-600 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
                                <?php echo htmlspecialchars($post['category_name'] ?? $category['name']); ?>
                            </span>
                        </div>

                        <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo urlencode($post['slug']); ?>" class="flex-1">
                            <h3 class="font-bold text-gray-800 hover:text-orange-500 transition text-base leading-snug line-clamp-2 mb-2">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                        </a>

                        <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                            <?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 110)) . '...'; ?>
                        </p>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">
                            
                            <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">
                                <i class="far fa-clock"></i> <?php echo timeAgo($post['created_at']); ?>
                            </span>
                        </div>

                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>