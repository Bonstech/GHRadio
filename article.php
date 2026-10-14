<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/subscription-popup.php'; 


error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: index.php');
    exit;
}

$post = getPostBySlug($_GET['slug']);

if (!$post) {
    header('Location: index.php');
    exit;
}

try {
    recordArticleView($post['id']);
} catch (Exception $e) {
    error_log("Error recording view: " . $e->getMessage());
}

$post = getPostBySlug($_GET['slug']);


$comments = getComments($post['id']);


$unique_views = getUniqueViews($post['id']);

function getRelatedArticles($category_id, $post_id, $limit = 4) {
    $conn = getDB();
    $stmt = $conn->prepare("
        SELECT p.*, u.username, u.firstname, u.lastname, u.profile_picture,
        c.name as category_name,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') as comment_count
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ? AND p.id != ? AND p.status = 'approved'
        ORDER BY p.created_at DESC, p.views DESC
        LIMIT ?
    ");
    $stmt->bind_param("iii", $category_id, $post_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$related_articles = getRelatedArticles($post['category_id'], $post['id']);

$page_title = $post['title'];
$page_description = strip_tags(substr($post['content'], 0, 160));
$page_description = str_replace(["\n", "\r"], ' ', $page_description);
$page_description = trim($page_description);

$share_image = !empty($post['featured_image']) 
    ? SITE_URL . 'uploads/posts/' . $post['featured_image'] 
    : SITE_URL . 'assets/images/GHRadiologo.jpg';

$og_title = $post['title'] . ' - GHRadio1';
$og_description = $page_description;
$og_url = SITE_URL . 'article.php?slug=' . $post['slug'];
$og_image = $share_image;
$og_type = 'article';
$og_site_name = 'GHRadio1';

$GLOBALS['og_title'] = $og_title;
$GLOBALS['og_description'] = $og_description;
$GLOBALS['og_url'] = $og_url;
$GLOBALS['og_image'] = $og_image;
$GLOBALS['og_type'] = $og_type;
$GLOBALS['og_site_name'] = $og_site_name;

include 'includes/header.php';
?>

<article class="bg-white rounded-xl shadow-sm overflow-hidden max-w-5xl mx-auto">
    
    <?php if ($post['featured_image']): ?>
        <div class="relative px-4 md:px-6 pt-4 md:pt-6">
            <div class="relative rounded-xl overflow-hidden">
                <img src="<?php echo SITE_URL; ?>uploads/posts/<?php echo $post['featured_image']; ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                     class="w-full h-[250px] md:h-[400px] lg:h-[450px] object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent p-4 md:p-6">
                    <div class="max-w-4xl mx-auto">
                        <div class="inline-block bg-orange-500 px-3 py-1 rounded-full text-white text-xs md:text-sm font-semibold mb-2">
                            <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                        </div>
                        <h1 class="text-xl md:text-3xl lg:text-4xl font-extrabold text-white leading-tight">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="px-4 md:px-6 pt-4 md:pt-6">
            <div class="max-w-4xl mx-auto">
                <div class="inline-block bg-orange-500 px-3 py-1 rounded-full text-white text-sm font-semibold mb-3">
                    <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                </div>
                <h1 class="text-2xl md:text-4xl font-extrabold text-gray-800">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="px-4 md:px-6 py-4 md:py-6 border-b">
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center space-x-4">
                    <img src="<?php echo SITE_URL; ?>uploads/profiles/<?php echo $post['profile_picture'] ?? 'default.png'; ?>" 
                         alt="<?php echo $post['username'] ?? 'Anonymous'; ?>"
                         class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover flex-shrink-0">
                    <div>
                        <p class="font-bold text-gray-800 text-sm md:text-base">
                            By
                            <?php 
                            echo htmlspecialchars(
                                (!empty($post['firstname']) && !empty($post['lastname'])) 
                                    ? $post['firstname'] . ' ' . $post['lastname'] 
                                    : ($post['username'] ?? 'Anonymous')
                            );
                            ?>
                        </p>
                       
                        <div class="flex flex-wrap items-center gap-2 md:gap-4 text-xs md:text-sm text-gray-500 mt-0.5">
                            <span><i class="far fa-calendar-alt"></i> <?php echo formatDate($post['created_at']); ?></span>
                            <span class="hidden xs:inline">•</span>
                            <span><i class="fas fa-users"></i> <?php echo number_format($unique_views); ?>views</span>
                            <span class="hidden xs:inline">•</span>
                            <span><i class="far fa-clock"></i> <?php echo timeAgo($post['created_at']); ?></span>
                        </div>
                    </div>
                </div>
                
              <div class="flex items-center space-x-2 flex-shrink-0">
                  <div class="font-semibold text-xl">Share:</div>
                    <?php 
                    $share_url = SITE_URL . 'article.php?slug=' . $post['slug'];
                    $share_title = urlencode($post['title'] . ' - GHRadio1');
                    $share_image_encoded = urlencode($share_image);
                    ?>
                    <button onclick="shareArticle('whatsapp', '<?php echo $share_url; ?>', '<?php echo $share_title; ?>', '<?php echo $share_image_encoded; ?>')" 
                            class="w-12 h-12 md:w-14 md:h-14 bg-green-500 text-white rounded-full hover:bg-green-600 transition flex items-center justify-center text-xs md:text-sm">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                    <button onclick="shareArticle('facebook', '<?php echo $share_url; ?>', '<?php echo $share_title; ?>', '<?php echo $share_image_encoded; ?>')" 
                            class="w-12 h-12 md:w-14 md:h-14 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition flex items-center justify-center text-xs md:text-sm">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button onclick="shareArticle('twitter', '<?php echo $share_url; ?>', '<?php echo $share_title; ?>', '<?php echo $share_image_encoded; ?>')" 
                            class="w-12 h-12 md:w-14 md:h-14 bg-blue-400 text-white rounded-full hover:bg-blue-500 transition flex items-center justify-center text-xs md:text-sm">
                        <i class="fab fa-twitter"></i>
                    </button>
                    <button onclick="shareArticle('linkedin', '<?php echo $share_url; ?>', '<?php echo $share_title; ?>', '<?php echo $share_image_encoded; ?>')" 
                            class="w-12 h-12 md:w-14 md:h-14 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition flex items-center justify-center text-xs md:text-sm">
                        <i class="fab fa-linkedin-in"></i>
                    </button>
                    <button onclick="copyToClipboard('<?php echo $share_url; ?>')" 
                            class="w-12 h-12 md:w-14 md:h-14 bg-gray-500 text-white rounded-full hover:bg-gray-600 transition flex items-center justify-center text-xs md:text-sm">
                        <i class="fas fa-link"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
<!-- ============================================================
     AI AUDIO READER
     ============================================================ -->
    <div id="audioReader" class="my-4 w-full">
        <div class="bg-gradient-to-br from-orange-50 via-amber-50 to-green-50 rounded-2xl border border-orange-200 shadow-sm overflow-hidden">
            
            <!-- Main Control Bar -->
            <div class="flex items-center gap-2 p-2.5">
                
                <!-- Play Button -->
                <button id="audioPlayBtn" 
                        onclick="toggleAudioReader()"
                        aria-label="Listen to article"
                        class="flex-shrink-0 w-11 h-11 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white flex items-center justify-center shadow-md active:scale-95 transition-all duration-200 relative">
                    <i id="audioIcon" class="fas fa-headphones text-base"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white animate-pulse"></span>
                </button>

                <!-- Info / Progress Area -->
                <div class="flex-1 min-w-0">
                    
                    <!-- Status Line -->
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-[11px] font-semibold text-gray-700 truncate" id="audioTitle">
                            Listen to this article
                        </span>
                        <span class="text-[10px] text-gray-500 flex-shrink-0" id="audioStatus">Ready</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9px] text-gray-400 font-mono w-8 text-center" id="currentTime">0:00</span>
                        <div class="flex-1 relative group cursor-pointer" id="progressBarWrap">
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div id="progressFill" 
                                    class="bg-gradient-to-r from-orange-400 to-orange-600 h-1.5 rounded-full transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                        </div>
                        <span class="text-[9px] text-gray-400 font-mono w-8 text-center" id="totalTime">0:00</span>
                    </div>
                </div>

                <!-- Speed Menu Button -->
                <div class="relative flex-shrink-0">
                    <button onclick="toggleSpeedMenu()" 
                            class="text-[10px] font-bold text-gray-600 bg-white/80 hover:bg-white px-2 py-1 rounded-full border border-gray-200 hover:border-orange-300 transition whitespace-nowrap"
                            aria-label="Playback speed">
                        <span id="speedDisplay">1x</span>
                    </button>
                    <div id="speedMenu" 
                        class="hidden absolute right-0 top-full mt-1 bg-white rounded-lg shadow-xl border border-gray-100 p-1 w-20 z-50">
                        <button onclick="setSpeed(0.75)" class="block w-full text-left px-2 py-1 text-[10px] hover:bg-orange-50 rounded">0.75x</button>
                        <button onclick="setSpeed(1)" class="block w-full text-left px-2 py-1 text-[10px] hover:bg-orange-50 rounded font-bold text-orange-600">1x</button>
                        <button onclick="setSpeed(1.25)" class="block w-full text-left px-2 py-1 text-[10px] hover:bg-orange-50 rounded">1.25x</button>
                        <button onclick="setSpeed(1.5)" class="block w-full text-left px-2 py-1 text-[10px] hover:bg-orange-50 rounded">1.5x</button>
                        <button onclick="setSpeed(2)" class="block w-full text-left px-2 py-1 text-[10px] hover:bg-orange-50 rounded">2x</button>
                    </div>
                </div>

                <!-- Stop Button -->
                <button onclick="stopAudioReader()" 
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition"
                        aria-label="Stop">
                    <i class="fas fa-stop text-xs"></i>
                </button>
            </div>
        </div>
    </div>
            <div class="article-content p-4 prose prose-lg max-w-none" style="background: white; color: #1a1a2e;">
                <?php 
                $content = processImageTags($post['content']);
                echo $content;
                ?>
            </div>
        </div>
        
        <?php if ($post['source_website']): ?>
            <div class="max-w-3xl mx-auto mt-4 text-sm text-gray-500 text-center">
                <i class="fas fa-newspaper"></i> 
                <span class="font-medium text-gray-700"><?php echo htmlspecialchars($post['source_website']); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($post['bio']): ?>
            <div class="max-w-3xl mx-auto mt-10 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center space-x-4">
                    <img src="<?php echo SITE_URL; ?>uploads/profiles/<?php echo $post['profile_picture'] ?? 'default.png'; ?>" 
                         alt="<?php echo htmlspecialchars($post['username'] ?? 'Anonymous'); ?>"
                         class="w-14 h-14 md:w-16 md:h-16 rounded-full object-cover">
                    <div>
                        <h4 class="font-bold text-gray-800"><?php 
                        echo htmlspecialchars(
                            (!empty($post['firstname']) && !empty($post['lastname'])) 
                                ? $post['firstname'] . ' ' . $post['lastname'] 
                                : ($post['username'] ?? 'Anonymous')
                        );
                        ?>
                        </h4>
                        <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($post['bio']); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="px-4 md:px-6 py-6 md:py-8">
        <div class="max-w-3xl mx-auto">
            <button onclick="toggleComments()" 
                    id="commentsToggleBtn"
                    class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition border border-gray-200">
                <div class="flex items-center space-x-3">
                    <i class="far fa-comments text-xl text-orange-500"></i>
                    <span class="font-semibold text-gray-800">Comments</span>
                    <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full text-sm font-semibold">
                        <?php echo count($comments); ?>
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="commentsToggleIcon"></i>
                </div>
            </button>
            
            <div id="commentsContent" class="mt-6 hidden">
                <div class="mb-8 bg-gray-50 rounded-xl p-4 md:p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Leave a Comment</h4>
                    <form action="comment.php" method="POST" class="space-y-3">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <input type="hidden" name="slug" value="<?php echo $post['slug']; ?>">
                        
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="text" name="commenter_name" placeholder="Your Name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                                <input type="email" name="commenter_email" placeholder="Your Email" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                            </div>
                        <?php endif; ?>
                        
                        <textarea name="comment" rows="3" placeholder="Write your comment..." required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition resize-y"></textarea>
                        
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg font-semibold">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Comment
                        </button>
                    </form>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($comments)): ?>
                        <p class="text-gray-500 text-center py-8">No comments yet. Be the first to comment!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="flex space-x-3 border-b pb-4">
                                <img src="<?php echo SITE_URL; ?>uploads/profiles/<?php echo !empty($comment['profile_picture']) ? $comment['profile_picture'] : 'default-avatar.png'; ?>"  
                                     alt="<?php echo htmlspecialchars($comment['username'] ?? $comment['commenter_name']); ?>"
                                     class="w-10 h-10 rounded-full object-cover">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($comment['username'] ?? $comment['commenter_name']); ?>
                                        </span>
                                        <span class="text-xs text-gray-500"><?php echo timeAgo($comment['created_at']); ?></span>
                                    </div>
                                    <p class="text-gray-700 mt-1"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($related_articles)): ?>
    <div class="px-4 md:px-6 py-6 md:py-8 border-b">
        <div class="max-w-4xl mx-auto">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-link text-orange-500 mr-2"></i>
                You may also like
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($related_articles as $related): ?>
                    <a href="<?php echo SITE_URL; ?>article.php?slug=<?php echo $related['slug']; ?>" 
                       class="group bg-gray-50 rounded-xl p-4 hover:bg-orange-50 hover:shadow-lg transition-all duration-300 border border-transparent hover:border-orange-200">
                        <h4 class="font-semibold text-gray-800 group-hover:text-orange-500 transition line-clamp-2 text-sm">
                            <?php echo htmlspecialchars($related['title']); ?>
                        </h4>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</article>

<a href="<?php echo getWhatsAppChannel(); ?>" 
   target="_blank"
   class="fixed bottom-6 right-6 z-50 flex items-center bg-green-500 text-white px-4 py-3 rounded-full shadow-lg hover:bg-green-600 transition group hover:scale-105"
   id="whatsappFloat">
    <i class="fab fa-whatsapp text-xl mr-2"></i>
    <span class="text-sm font-semibold">Join WhatsApp Channel</span>
</a>

<style>

    
    img {
        -webkit-user-drag: none;
        user-drag: none;
    }

#whatsappFloat {
    animation: bounceIn 0.6s ease;
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
}

#whatsappFloat:hover {
    box-shadow: 0 12px 35px rgba(37, 211, 102, 0.5);
}

@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

#whatsappFloat::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 9999px;
    background: rgba(37, 211, 102, 0.2);
    animation: pulse-ring 2s infinite;
    z-index: -1;
}

@keyframes pulse-ring {
    0% { transform: scale(1); opacity: 0.5; }
    100% { transform: scale(1.3); opacity: 0; }
}

.article-content {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #1a1a2e;
}

.article-content p {
    margin-bottom: 1.75rem;
    line-height: 1.8;
}

.article-content h2 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-top: 2.5rem;
    margin-bottom: 1.25rem;
    color: #1a1a2e;
}

.article-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #1a1a2e;
}

.article-content h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-top: 1.75rem;
    margin-bottom: 0.75rem;
    color: #1a1a2e;
}

.article-content ul, 
.article-content ol {
    margin: 1.25rem 0 1.75rem 2rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content blockquote {
    border-left: 4px solid #F97316;
    padding-left: 1.5rem;
    margin: 1.75rem 0;
    font-style: italic;
    color: #4a4a6a;
    background: #f8f9fa;
    padding: 1.25rem 1.5rem;
    border-radius: 0.5rem;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.75rem;
}

.article-content figure {
    margin: 2rem 0;
}

.article-content figure img {
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
}

.article-content figcaption {
    text-align: center;
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.75rem;
    font-style: italic;
}

.article-content a {
    color: #F97316;
    text-decoration: underline;
    transition: color 0.2s ease;
}

.article-content a:hover {
    color: #ea580c;
}

.article-content strong {
    font-weight: 700;
    color: #1a1a2e;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 640px) {
    .article-content {
        font-size: 1rem;
    }
    
    .article-content h2 {
        font-size: 1.5rem;
    }
    
    .article-content h3 {
        font-size: 1.25rem;
    }
    
    .article-content p {
        margin-bottom: 1.25rem;
    }
    
    .article-content figure {
        margin: 1.5rem 0;
    }
}

#commentsContent {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* ============================================================
   AI AUDIO READER STYLES
   ============================================================ */
#audioReader {
    /* Fits any container */
}

#audioPlayBtn.playing {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}

#audioPlayBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

#progressBarWrap:hover #progressFill {
    height: 8px;
}

#speedMenu {
    animation: menuSlide 0.2s ease;
}

@keyframes menuSlide {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Reading highlight for current paragraph */
.audio-reading-active {
    background: linear-gradient(to right, rgba(249, 115, 22, 0.08), transparent);
    border-left: 3px solid #F97316;
    padding-left: 12px;
    margin-left: -15px;
    transition: all 0.3s ease;
    border-radius: 4px;
}

/* Mobile optimizations */
@media (max-width: 480px) {
    #audioPlayBtn {
        width: 40px;
        height: 40px;
    }
    #audioPlayBtn i {
        font-size: 14px;
    }
    #audioTitle {
        font-size: 10px;
    }
}

/* Text to be read */
.article-content {
    scroll-behavior: smooth;
}
</style>

<script>


function shareArticle(platform, url, title, image) {
    const decodedTitle = decodeURIComponent(title);
    const decodedImage = decodeURIComponent(image || '');
    
    let shareText = decodedTitle + ' - GHRadio1';
    
    let shareUrl = '';
    switch(platform) {
        case 'facebook':
            shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url) + 
                       '&quote=' + encodeURIComponent(shareText);
            break;
        case 'twitter':
            shareUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + 
                       '&text=' + encodeURIComponent(shareText);
            break;
        case 'whatsapp':
            shareUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(shareText + ' - ' + url);
            break;
        case 'linkedin':
            shareUrl = 'https://www.linkedin.com/sharing/share-offscreen/?url=' + encodeURIComponent(url);
            break;
        case 'telegram':
            shareUrl = 'https://t.me/share/url?url=' + encodeURIComponent(url) + 
                       '&text=' + encodeURIComponent(shareText);
            break;
        default:
            alert('Share platform not supported');
            return;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=500');
    }
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link copied to clipboard! 📋');
        }).catch(() => {
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    showNotification('Link copied to clipboard! 📋');
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-50 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 translate-x-full';
    notification.innerHTML = '<div class="flex items-center space-x-3"><i class="fas fa-check-circle text-green-400"></i><span>' + message + '</span></div>';
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-0');
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}


let lastScrollTop = 0;
window.addEventListener('scroll', function() {
    const floatBtn = document.getElementById('whatsappFloat');
    if (!floatBtn) return;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (scrollTop > lastScrollTop && scrollTop > 300) {
        floatBtn.style.transform = 'translateY(100px)';
        floatBtn.style.opacity = '0';
    } else {
        floatBtn.style.transform = 'translateY(0)';
        floatBtn.style.opacity = '1';
    }
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
});


function toggleComments() {
    const content = document.getElementById('commentsContent');
    const icon = document.getElementById('commentsToggleIcon');
    const text = document.getElementById('commentsToggleText');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
        text.textContent = 'Hide Comments';
        localStorage.setItem('commentsOpen', 'true');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
        text.textContent = 'Show Comments';
        localStorage.setItem('commentsOpen', 'false');
    }
}


    

let speechSynthesis = window.speechSynthesis;
let currentUtterance = null;
let isPlaying = false;
let isPaused = false;
let progressInterval = null;
let currentSpeed = 1;
let isAutoScroll = true;

const playBtn = document.getElementById('audioPlayBtn');
const playIcon = document.getElementById('audioIcon');
const progressFill = document.getElementById('progressFill');
const currentTimeEl = document.getElementById('currentTime');
const totalTimeEl = document.getElementById('totalTime');
const statusEl = document.getElementById('audioStatus');
const statusDot = document.getElementById('statusDot');
const wordCountEl = document.getElementById('wordCount');
const wordCountShort = document.getElementById('wordCountShort');
const speedDisplay = document.getElementById('speedDisplay');
const speedMenu = document.getElementById('speedMenu');
const speedToggle = document.getElementById('speedToggle');
const autoScrollBtn = document.getElementById('autoScrollBtn');
const autoScrollLabel = document.getElementById('autoScrollLabel');


function getArticleText() {
    const articleContent = document.querySelector('.article-content');
    if (!articleContent) return '';
    
    const clone = articleContent.cloneNode(true);
    clone.querySelectorAll('figure, img, .my-8, .rounded-xl, .shadow-md, .my-6, figure').forEach(el => {
        el.remove();
    });
    
    let text = clone.textContent || '';
    text = text.replace(/\s+/g, ' ');
    text = text.replace(/\.\.\./g, '… ');
    text = text.replace(/\n/g, ' ');
    text = text.replace(/\s+([.,!?;:])/g, '$1 ');
    text = text.replace(/([.,!?;:])\s*/g, '$1 ');
    text = text.trim();
    
    const wordCount = text.split(/\s+/).length;
    if (wordCountEl) wordCountEl.textContent = wordCount.toLocaleString() + ' words';
    if (wordCountShort) wordCountShort.textContent = wordCount.toLocaleString();
    
    return text;
}


function createNaturalUtterance(text) {
    const sentences = text.match(/[^.!?]+[.!?]+/g) || [text];
    let processedText = '';
    sentences.forEach((sentence, index) => {
        processedText += sentence.trim();
        if (index < sentences.length - 1) {
            processedText += '... ';
        }
    });
    const paragraphs = text.split(/\n\s*\n/);
    if (paragraphs.length > 1) {
        processedText = paragraphs.join('... ... ');
    }
    return processedText;
}

function toggleAudio() {
    if (isPlaying) {
        pauseAudio();
    } else {
        playAudio();
    }
}

function playAudio() {
    const text = getArticleText();
    if (!text) {
        updateStatus('No content', 'error');
        return;
    }
    
    if (isPaused && currentUtterance) {
        speechSynthesis.resume();
        isPaused = false;
        isPlaying = true;
        updatePlayButton(true);
        updateStatus('Playing...', 'playing');
        startProgressTracking();
        return;
    }
    
    stopAudio();
    
    const naturalText = createNaturalUtterance(text);
    currentUtterance = new SpeechSynthesisUtterance(naturalText);
    
    const voices = speechSynthesis.getVoices();
    const preferredVoices = ['Google UK English Female', 'Google US English Female', 'Samantha', 'Alex', 'Victoria'];
    let selectedVoice = null;
    for (const preferred of preferredVoices) {
        const found = voices.find(v => v.name.includes(preferred));
        if (found) { selectedVoice = found; break; }
    }
    if (selectedVoice) {
        currentUtterance.voice = selectedVoice;
    } else if (voices.length > 0) {
        const englishVoice = voices.find(v => v.lang.startsWith('en'));
        if (englishVoice) currentUtterance.voice = englishVoice;
    }
    
    currentUtterance.rate = currentSpeed;
    currentUtterance.pitch = 1;
    currentUtterance.volume = 1;
    currentUtterance.lang = 'en-US';
    
    currentUtterance.onstart = function() {
        isPlaying = true;
        isPaused = false;
        updatePlayButton(true);
        updateStatus('Playing...', 'playing');
        startProgressTracking();
    };
    
    currentUtterance.onpause = function() {
        isPaused = true;
        isPlaying = false;
        updatePlayButton(false);
        updateStatus('Paused', 'paused');
        clearInterval(progressInterval);
    };
    
    currentUtterance.onresume = function() {
        isPaused = false;
        isPlaying = true;
        updatePlayButton(true);
        updateStatus('Playing...', 'playing');
    };
    
    currentUtterance.onend = function() {
        isPlaying = false;
        isPaused = false;
        updatePlayButton(false);
        updateStatus('Finished', 'finished');
        progressFill.style.width = '100%';
        clearInterval(progressInterval);
    };
    
    currentUtterance.onerror = function(event) {
        console.error('Speech error:', event);
        isPlaying = false;
        isPaused = false;
        updatePlayButton(false);
        updateStatus('Error', 'error');
        clearInterval(progressInterval);
    };
    
    speechSynthesis.speak(currentUtterance);
}

function pauseAudio() {
    if (speechSynthesis.speaking && !isPaused) {
        speechSynthesis.pause();
        isPaused = true;
        isPlaying = false;
        updatePlayButton(false);
        updateStatus('Paused', 'paused');
        clearInterval(progressInterval);
    }
}

function stopAudio() {
    speechSynthesis.cancel();
    isPlaying = false;
    isPaused = false;
    updatePlayButton(false);
    updateStatus('Ready', 'idle');
    progressFill.style.width = '0%';
    clearInterval(progressInterval);
    currentUtterance = null;
}


function changeSpeed(speed) {
    currentSpeed = speed;
    speedDisplay.textContent = speed === 1 ? '1x' : speed + 'x';
    
    document.querySelectorAll('#speedMenu button').forEach(btn => {
        btn.classList.remove('font-semibold', 'text-orange-600');
        if (parseFloat(btn.textContent.replace('x', '')) === speed) {
            btn.classList.add('font-semibold', 'text-orange-600');
        }
    });
    
    if (isPlaying || isPaused) {
        const wasPlaying = isPlaying;
        stopAudio();
        if (wasPlaying) setTimeout(playAudio, 200);
    }
}


function toggleAutoScroll() {
    isAutoScroll = !isAutoScroll;
    if (isAutoScroll) {
        autoScrollLabel.textContent = 'Auto';
        autoScrollBtn.classList.remove('border-orange-500', 'bg-orange-50');
        autoScrollBtn.classList.add('border-gray-200', 'text-gray-600');
    } else {
        autoScrollLabel.textContent = 'Off';
        autoScrollBtn.classList.add('border-orange-500', 'bg-orange-50');
        autoScrollBtn.classList.remove('border-gray-200', 'text-gray-600');
    }
}


function startProgressTracking() {
    clearInterval(progressInterval);
    
    const text = getArticleText();
    const wordCount = text.split(/\s+/).length;
    const estimatedDuration = (wordCount / 150) * (1 / currentSpeed) * 60;
    
    let elapsed = 0;
    totalTimeEl.textContent = formatTime(estimatedDuration);
    
    progressInterval = setInterval(() => {
        elapsed += 1;
        const progress = Math.min((elapsed / estimatedDuration) * 100, 100);
        progressFill.style.width = progress + '%';
        currentTimeEl.textContent = formatTime(elapsed);
        
        if (isAutoScroll) {
            const paragraphs = document.querySelectorAll('.article-content p');
            if (paragraphs.length > 0) {
                const scrollProgress = progress / 100;
                const index = Math.floor(scrollProgress * (paragraphs.length - 1));
                if (paragraphs[index]) {
                    paragraphs[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
        
        if (progress >= 100) {
            clearInterval(progressInterval);
        }
    }, 1000);
}


function updatePlayButton(playing) {
    if (playing) {
        playIcon.className = 'fas fa-pause text-base';
    } else {
        playIcon.className = 'fas fa-play text-base ml-0.5';
    }
}

function updateStatus(status, state) {
    statusEl.textContent = status;
    const colors = {
        playing: 'bg-green-500',
        paused: 'bg-yellow-500',
        idle: 'bg-gray-300',
        finished: 'bg-blue-500',
        error: 'bg-red-500'
    };
    statusDot.className = 'w-1.5 h-1.5 rounded-full ' + (colors[state] || 'bg-gray-300');
}

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
}


document.getElementById('progressBar').addEventListener('click', function(e) {
    const rect = this.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width;
    const progress = x * 100;
    progressFill.style.width = progress + '%';
    
    if (isPlaying || isPaused) {
        const wasPlaying = isPlaying;
        stopAudio();
        if (wasPlaying) setTimeout(playAudio, 200);
    }
});


speedToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    speedMenu.classList.toggle('hidden');
});

document.addEventListener('click', function(e) {
    if (!speedToggle.contains(e.target) && !speedMenu.contains(e.target)) {
        speedMenu.classList.add('hidden');
    }
});


document.addEventListener('keydown', function(e) {
    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        if (e.key === ' ' || e.key === 'Space') {
            e.preventDefault();
            toggleAudio();
        }
    }
});


if (speechSynthesis.onvoiceschanged !== undefined) {
    speechSynthesis.onvoiceschanged = function() {};
}


document.addEventListener('DOMContentLoaded', function() {
    updateStatus('Ready', 'idle');
    getArticleText();
    console.log('🎧 Audio player loaded. Press Space to play/pause.');
});


document.addEventListener('DOMContentLoaded', function() {
    const commentsOpen = localStorage.getItem('commentsOpen');
    if (commentsOpen === 'true') {
        const content = document.getElementById('commentsContent');
        const icon = document.getElementById('commentsToggleIcon');
        const text = document.getElementById('commentsToggleText');
        if (content && icon && text) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
            text.textContent = 'Hide Comments';
        }
    }
    
    setTimeout(loadVoices, 1000);
});

window.toggleAudio = toggleAudio;
window.stopAudio = stopAudio;
window.playAudio = playAudio;
window.pauseAudio = pauseAudio;
window.shareArticle = shareArticle;
window.copyToClipboard = copyToClipboard;
window.handleReaction = handleReaction;
window.toggleComments = toggleComments;

console.log('✅ Article page loaded successfully!');
console.log('📢 Audio player: Press Ctrl+Shift+P to play/pause');
console.log('💬 Comments: Click to expand/collapse');
console.log('📤 Share: Click share buttons');

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    
    document.addEventListener('selectstart', function(e) {
        e.preventDefault();
        return false;
    });
    
    document.addEventListener('copy', function(e) {
        e.preventDefault();
        return false;
    });
    
    document.addEventListener('cut', function(e) {
        e.preventDefault();
        return false;
    });
    
    document.addEventListener('paste', function(e) {
        e.preventDefault();
        return false;
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && (e.key === 'c' || e.key === 'u' || e.key === 's' || e.key === 'p')) {
            e.preventDefault();
            return false;
        }
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>