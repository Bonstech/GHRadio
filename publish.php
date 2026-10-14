<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$title = trim($_POST['title'] ?? '');
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to publish an article.';
    header('Location: login.php');
    exit;
}

// Get user data
$user = getUserById($_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$is_admin = isset($user['role']) && $user['role'] === 'admin';
$categories = getAllCategories();

$error = '';
$success = '';
$uploaded_images_map = [];


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = intval($_POST['category_id'] ?? 0);
    $author_name = sanitize($_POST['author_name'] ?? '');
    $source_name = sanitize($_POST['source_name'] ?? '');
    $status = $is_admin ? 'approved' : 'pending';
    
    if (empty($title) || empty($content) || empty($category_id)) {
        $error = 'Title, content, and category are required.';
    } else {
        $conn = getDB();
        $slug = createSlug($title);
        
        $stmt = $conn->prepare("SELECT id FROM posts WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $slug = $slug . '-' . time();
        }
        
        $featured_image = '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir(__DIR__ . '/uploads/posts/')) {
                mkdir(__DIR__ . '/uploads/posts/', 0777, true);
            }
            $upload_result = uploadFile($_FILES['featured_image'], __DIR__ . '/uploads/posts/');
            if ($upload_result['success']) {
                $featured_image = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        $uploaded_images = [];
        $image_mapping = [];
        
        if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
            $total_files = count($_FILES['additional_images']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $original_name = $_FILES['additional_images']['name'][$i];
                    $file = [
                        'name' => $original_name,
                        'type' => $_FILES['additional_images']['type'][$i],
                        'tmp_name' => $_FILES['additional_images']['tmp_name'][$i],
                        'error' => $_FILES['additional_images']['error'][$i],
                        'size' => $_FILES['additional_images']['size'][$i]
                    ];
                    $upload_result = uploadFile($file, __DIR__ . '/uploads/posts/');
                    if ($upload_result['success']) {
                        $new_name = $upload_result['filename'];
                        $uploaded_images[] = $new_name;
                        $image_mapping[$original_name] = $new_name;
                    }
                }
            }
        }
        
        if (empty($error)) {
            $processed_content = $content;
            foreach ($image_mapping as $original => $new) {
                $processed_content = str_replace('[image:' . $original . ']', '[image:' . $new . ']', $processed_content);
            }
            
            $author_id = $_SESSION['user_id'];
            
            $stmt = $conn->prepare("
                INSERT INTO posts (title, slug, content, featured_image, author_id, author_name, category_id, source_website, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("sssssiiss", 
                $title, $slug, $processed_content, $featured_image, $author_id, $author_name, $category_id, $source_name, $status
            );
            
            if ($stmt->execute()) {
                $post_id = $conn->insert_id;
                
                foreach ($uploaded_images as $index => $filename) {
                    $stmt_img = $conn->prepare("INSERT INTO post_images (post_id, image_path, position) VALUES (?, ?, ?)");
                    $position = $index;
                    $stmt_img->bind_param("isi", $post_id, $filename, $position);
                    $stmt_img->execute();
                }
                
                if ($is_admin) {
                    $success = 'Article published successfully!';
                } else {
                    $success = 'Article submitted for approval. You will be notified when it\'s published.';
                }
                
                $_POST = [];
                $_FILES = [];
            } else {
                $error = 'Failed to publish article: ' . $conn->error;
            }
        }
    }
}

$page_title = 'Publish Article';
include 'includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-pen-fancy text-orange-500 mr-2"></i>
            Publish Article
        </h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo htmlspecialchars($success); ?>
                <?php if (!$is_admin): ?>
                    <p class="mt-2 text-sm">Your article is pending admin approval.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data" id="publishForm">
            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Article Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" required 
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                       placeholder="Enter article title"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition bg-white">
            </div>
            
            <!-- Category -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>
                <select name="category_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition bg-white">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Author Name (Optional)</label>
                    <input type="text" name="author_name" 
                           value="<?php echo isset($_POST['author_name']) ? htmlspecialchars($_POST['author_name']) : ''; ?>"
                           placeholder="Leave blank to use your username"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition bg-white">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Source Name (Optional)</label>
                    <input type="text" name="source_name" 
                           value="<?php echo isset($_POST['source_name']) ? htmlspecialchars($_POST['source_name']) : ''; ?>"
                           placeholder="e.g., BBC News, CNN, Ghana Web"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition bg-white">
                    <p class="text-xs text-gray-500 mt-1">Enter the name of the original source</p>
                </div>
            </div>
            
            <!-- Featured Image -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Featured Image</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP (Max 5MB)</p>
                        </div>
                        <input type="file" name="featured_image" accept="image/*" class="hidden" id="featuredImage">
                    </label>
                </div>
                <div id="featuredPreview" class="mt-2"></div>
                <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x630px</p>
            </div>
            
            <!-- Additional Images -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Images</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-images text-3xl text-gray-400 mb-2"></i>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> multiple images</p>
                            <p class="text-xs text-gray-500">You can select multiple images at once</p>
                        </div>
                        <input type="file" name="additional_images[]" accept="image/*" multiple class="hidden" id="additionalImages">
                    </label>
                </div>
                <div id="additionalPreview" class="mt-2 flex flex-wrap gap-2"></div>
                <p class="text-xs text-gray-500 mt-1">Upload images and use the tags below to insert them into your article</p>
            </div>
            
            <!-- Content Editor -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Article Content <span class="text-red-500">*</span>
                </label>
                
                <!-- Toolbar -->
                <div class="border border-gray-300 rounded-t-lg bg-gray-50 p-2 flex flex-wrap gap-1">
                    <button type="button" onclick="formatText('bold')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm font-bold bg-white" title="Bold">B</button>
                    <button type="button" onclick="formatText('italic')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm italic bg-white" title="Italic">I</button>
                    <button type="button" onclick="formatText('underline')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm underline bg-white" title="Underline">U</button>
                    <button type="button" onclick="formatText('strikeThrough')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm line-through bg-white" title="Strikethrough">S</button>
                    <span class="w-px h-6 bg-gray-300 mx-1"></span>
                    <button type="button" onclick="formatText('insertUnorderedList')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm bg-white" title="Bullet List">
                        <i class="fas fa-list-ul"></i>
                    </button>
                    <button type="button" onclick="formatText('insertOrderedList')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm bg-white" title="Numbered List">
                        <i class="fas fa-list-ol"></i>
                    </button>
                    <span class="w-px h-6 bg-gray-300 mx-1"></span>
                    <button type="button" onclick="formatText('justifyLeft')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm bg-white" title="Align Left">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <button type="button" onclick="formatText('justifyCenter')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm bg-white" title="Align Center">
                        <i class="fas fa-align-center"></i>
                    </button>
                    <button type="button" onclick="formatText('justifyRight')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm bg-white" title="Align Right">
                        <i class="fas fa-align-right"></i>
                    </button>
                    <span class="w-px h-6 bg-gray-300 mx-1"></span>
                    <button type="button" onclick="formatText('createLink')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm bg-white" title="Insert Link">
                        <i class="fas fa-link"></i>
                    </button>
                    <button type="button" onclick="formatText('unlink')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm bg-white" title="Remove Link">
                        <i class="fas fa-unlink"></i>
                    </button>
                    <span class="w-px h-6 bg-gray-300 mx-1"></span>
                    <button type="button" onclick="formatText('formatBlock', 'h2')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm font-bold bg-white">H2</button>
                    <button type="button" onclick="formatText('formatBlock', 'h3')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm font-bold bg-white">H3</button>
                    <button type="button" onclick="formatText('formatBlock', 'h4')" class="px-3 py-1 hover:bg-gray-200 rounded text-sm font-bold bg-white">H4</button>
                    <span class="w-px h-6 bg-gray-300 mx-1"></span>
                    <button type="button" onclick="clearFormatting()" class="px-3 py-1 hover:bg-gray-200 rounded text-sm text-red-600 bg-white" title="Clear Formatting">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                </div>
                
                <!-- Content Editor -->
                <div class="border border-t-0 border-gray-300 rounded-b-lg overflow-hidden" style="background: white;">
                    <div id="editor" contenteditable="true" 
                         class="w-full px-4 py-3 min-h-[400px] focus:outline-none bg-white prose prose-sm max-w-none"
                         style="line-height: 1.8; font-size: 16px; background: white; color: #1a1a2e;">
                        <?php echo isset($_POST['content']) ? $_POST['content'] : ''; ?>
                    </div>
                    <textarea name="content" id="content" style="display:none;"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>
                
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">
                    <span><i class="fas fa-info-circle"></i> Use the toolbar to format your text</span>
                    <span>•</span>
                    <span>Click image tags below to insert them at cursor position</span>
                    <span>•</span>
                    <span>Use <strong>Ctrl+B</strong> for bold, <strong>Ctrl+I</strong> for italic</span>
                </div>
            </div>
            
            <!-- Image Tags Help -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="font-semibold text-blue-800 mb-2"><i class="fas fa-info-circle"></i> Available Images:</h4>
                <p class="text-sm text-blue-700 mb-2">Click any tag below to insert it at the cursor position in your article:</p>
                <div id="imageTagsList" class="mt-2 text-sm flex flex-wrap gap-2"></div>
            </div>

           
            
            <!-- Submit Buttons -->
            <div class="flex flex-wrap items-center gap-4 pt-4 border-t">
                <button type="submit" class="btn-primary px-8 py-3 rounded-lg font-semibold text-lg flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> 
                    <?php echo $is_admin ? 'Publish Now' : 'Submit for Approval'; ?>
                </button>
                <!--<button type="button" onclick="previewArticle()" class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition flex items-center gap-2">
                    //<i class="fas fa-eye"></i> Preview
                </button>-->
                <a href="index.php" class="text-gray-600 hover:text-gray-800 transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b p-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Article Preview</h3>
                <button onclick="closePreview()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6 preview-content prose prose-lg max-w-none" style="background: white; color: #1a1a2e;">
                <!-- Preview content will be inserted here -->
            </div>
        </div>
    </div>
</div>

<script>
// SITE_URL for JavaScript
window.SITE_URL = '<?php echo SITE_URL; ?>';

// Format text function
function formatText(command, value = null) {
    const editor = document.getElementById('editor');
    editor.focus();
    
    if (command === 'createLink') {
        const url = prompt('Enter the URL:', 'https://');
        if (url) {
            document.execCommand('createLink', false, url);
        }
        return;
    }
    
    if (command === 'formatBlock' && value) {
        document.execCommand('formatBlock', false, value);
        return;
    }
    
    document.execCommand(command, false, value);
    updateContent();
}

// Clear formatting
function clearFormatting() {
    const editor = document.getElementById('editor');
    editor.focus();
    document.execCommand('removeFormat', false, null);
    updateContent();
}

// Update hidden textarea with editor content
function updateContent() {
    const editor = document.getElementById('editor');
    const textarea = document.getElementById('content');
    textarea.value = editor.innerHTML;
}

// Featured image preview
document.getElementById('featuredImage').addEventListener('change', function(e) {
    const preview = document.getElementById('featuredPreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.innerHTML = `
                <div class="relative inline-block mt-2">
                    <img src="${ev.target.result}" alt="Featured Preview" class="h-32 w-auto rounded-lg object-cover border">
                    <button type="button" onclick="this.parentElement.remove(); document.getElementById('featuredImage').value = '';" 
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                </div>
            `;
        };
        reader.readAsDataURL(this.files[0]);
    }
});

// Additional images preview with tags
document.getElementById('additionalImages').addEventListener('change', function(e) {
    const preview = document.getElementById('additionalPreview');
    const list = document.getElementById('imageTagsList');
    preview.innerHTML = '';
    list.innerHTML = '';
    
    const files = Array.from(this.files);
    
    if (files.length === 0) {
        list.innerHTML = '<p class="text-sm text-gray-500">No images uploaded yet.</p>';
        return;
    }
    
    files.forEach((file, index) => {
        // Show preview
        const reader = new FileReader();
        reader.onload = function(ev) {
            const div = document.createElement('div');
            div.className = 'relative inline-block';
            div.innerHTML = `
                <img src="${ev.target.result}" alt="Preview ${index}" class="h-20 w-auto rounded-lg object-cover border">
                <span class="absolute bottom-0 right-0 bg-black bg-opacity-60 text-white text-xs px-1 rounded">${index + 1}</span>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
        
        // Create tag button with original filename - FIXED: Added type="button"
        const tag = `[image:${file.name}]`;
        const tagButton = document.createElement('button');
        tagButton.type = 'button'; // THIS IS THE FIX - prevents form submission
        tagButton.className = 'bg-white px-3 py-2 rounded border border-blue-300 text-blue-700 hover:bg-blue-50 text-sm font-mono';
        tagButton.innerHTML = `<i class="fas fa-image mr-1"></i> ${file.name}`;
        tagButton.title = 'Click to insert this image at cursor position';
        tagButton.onclick = function(e) {
            e.preventDefault(); // Prevent any default action
            insertImageTagByName(file.name);
        };
        list.appendChild(tagButton);
    });
    
    // Add instruction
    const instruction = document.createElement('p');
    instruction.className = 'text-xs text-gray-500 w-full mt-2';
    instruction.textContent = 'Click any image name above to insert it at the cursor position in your article.';
    list.appendChild(instruction);
});

// Insert image tag by filename
function insertImageTagByName(filename) {
    const editor = document.getElementById('editor');
    editor.focus();
    const tag = `[image:${filename}]`;
    document.execCommand('insertHTML', false, tag);
    updateContent();
    showNotification('Image tag inserted!');
}

// Preview article
function previewArticle() {
    updateContent();
    const content = document.getElementById('content').value;
    const title = document.querySelector('input[name="title"]').value || 'Untitled Article';
    const modal = document.getElementById('previewModal');
    const previewContent = modal.querySelector('.preview-content');
    
    // Process image tags
    let previewHtml = content;
    const imageMatches = previewHtml.match(/\[image:([^\]]+)\]/g);
    if (imageMatches) {
        imageMatches.forEach(match => {
            const filename = match.replace('[image:', '').replace(']', '');
            const imageHtml = `
                <div class="my-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-image text-yellow-600 text-2xl"></i>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Image placeholder</p>
                            <p class="text-xs text-yellow-600">Filename: ${filename}</p>
                            <p class="text-xs text-yellow-600">Image will appear after publishing</p>
                        </div>
                    </div>
                </div>
            `;
            previewHtml = previewHtml.replace(match, imageHtml);
        });
    }
    
    // Add title
    previewContent.innerHTML = `
        <h1 class="text-3xl font-bold mb-6">${title}</h1>
        <div style="background: white; color: #1a1a2e; line-height: 1.8;">${previewHtml}</div>
    `;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close preview
function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Show notification
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

// Auto-save content on change
document.getElementById('editor').addEventListener('input', updateContent);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'b':
                e.preventDefault();
                formatText('bold');
                break;
            case 'i':
                e.preventDefault();
                formatText('italic');
                break;
            case 'u':
                e.preventDefault();
                formatText('underline');
                break;
            case 's':
                e.preventDefault();
                document.getElementById('publishForm').submit();
                break;
        }
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateContent();
    document.getElementById('imageTagsList').innerHTML = '<p class="text-sm text-gray-500">Upload images to see tags here.</p>';
});

// Close modal on click outside
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreview();
    }
});

// Escape key to close preview
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreview();
    }
});

// Paste handler to remove black background
document.getElementById('editor').addEventListener('paste', function(e) {
    e.preventDefault();
    
    const text = (e.originalEvent || e).clipboardData.getData('text/plain');
    const html = (e.originalEvent || e).clipboardData.getData('text/html');
    
    if (text) {
        document.execCommand('insertText', false, text);
    } else if (html) {
        const temp = document.createElement('div');
        temp.innerHTML = html;
        temp.querySelectorAll('*').forEach(el => {
            el.removeAttribute('style');
            el.removeAttribute('class');
            el.removeAttribute('bgcolor');
        });
        document.execCommand('insertHTML', false, temp.innerHTML);
    }
    
    updateContent();
});
</script>

<style>
/* Editor styles */
#editor {
    min-height: 400px;
    line-height: 1.8;
    background: white !important;
    color: #1a1a2e !important;
}

#editor:focus {
    outline: none;
}

#editor * {
    background: transparent !important;
    color: #1a1a2e !important;
}

#editor p {
    margin-bottom: 1.5rem;
    background: transparent !important;
}

#editor h2 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    background: transparent !important;
}

#editor h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 1.25rem;
    margin-bottom: 0.75rem;
    background: transparent !important;
}

#editor h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    background: transparent !important;
}

#editor ul, #editor ol {
    margin-left: 2rem;
    margin-bottom: 1rem;
    background: transparent !important;
}

#editor li {
    margin-bottom: 0.25rem;
    background: transparent !important;
}

#editor blockquote {
    border-left: 4px solid #F97316;
    padding-left: 1rem;
    margin: 1rem 0;
    color: #4a4a6a;
    background: #f8f9fa !important;
    padding: 1rem;
    border-radius: 0.5rem;
}

#editor img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 0.5rem 0;
}

/* Bullet list styles */
#editor ul {
    list-style-type: disc !important;
    padding-left: 1.5rem !important;
}

#editor ol {
    list-style-type: decimal !important;
    padding-left: 1.5rem !important;
}

#editor ul li, #editor ol li {
    margin-bottom: 0.25rem;
}

/* Preview styles */
.preview-content {
    line-height: 1.8;
    background: white !important;
    color: #1a1a2e !important;
}

.preview-content * {
    background: transparent !important;
    color: #1a1a2e !important;
}

.preview-content h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.preview-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.preview-content p {
    margin-bottom: 1.5rem;
}

.preview-content ul, .preview-content ol {
    margin-left: 2rem;
    margin-bottom: 1.5rem;
}

.preview-content li {
    margin-bottom: 0.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>