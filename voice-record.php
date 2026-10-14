<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Voice Message - GHRadio1';
include 'includes/header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-microphone text-orange-500 mr-2"></i>
            Send Voice Message
        </h1>
        
        <p class="text-gray-600 mb-6">Record a voice message and send it to GHRadio1 admin.</p>
        
        <div id="recordingStatus" class="hidden mb-4 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-700">
            <i class="fas fa-circle text-red-500 animate-pulse mr-2"></i>
            Recording... <span id="timerDisplay">00:00</span>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-6 mb-6">
            <div class="flex flex-col items-center justify-center gap-4">
                <div id="waveContainer" class="hidden flex items-center justify-center gap-1 h-16 mb-2">
                    <?php for ($i = 1; $i <= 20; $i++): ?>
                        <div class="wave-bar bg-orange-500 rounded-full" 
                             style="width: 4px; height: <?php echo rand(10, 40); ?>px; animation-delay: <?php echo $i * 0.05; ?>s;"></div>
                    <?php endfor; ?>
                </div>
                
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <button id="startRecordBtn" 
                            class="px-6 py-3 bg-red-500 text-white rounded-full font-semibold hover:bg-red-600 transition flex items-center gap-2 text-sm">
                        <i class="fas fa-microphone"></i> Start Recording
                    </button>
                    
                    <button id="stopRecordBtn" 
                            class="px-6 py-3 bg-gray-500 text-white rounded-full font-semibold hover:bg-gray-600 transition flex items-center gap-2 text-sm hidden">
                        <i class="fas fa-stop"></i> Stop
                    </button>
                    
                    <button id="playRecordBtn" 
                            class="px-6 py-3 bg-green-500 text-white rounded-full font-semibold hover:bg-green-600 transition flex items-center gap-2 text-sm hidden">
                        <i class="fas fa-play"></i> Play
                    </button>
                    
                    <button id="sendRecordBtn" 
                            class="px-6 py-3 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition flex items-center gap-2 text-sm hidden">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                    
                    <button id="clearRecordBtn" 
                            class="px-6 py-3 bg-gray-300 text-gray-700 rounded-full font-semibold hover:bg-gray-400 transition flex items-center gap-2 text-sm hidden">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
            </div>
        </div>
        
        <div id="audioPreview" class="hidden mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-600 mb-2">Recording preview:</p>
            <audio id="audioPlayer" controls class="w-full"></audio>
            <p class="text-xs text-gray-400 mt-1" id="audioInfo"></p>
        </div>
        
        <form id="voiceMessageForm" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="audio_data" id="audioData">
            <input type="hidden" name="audio_duration" id="audioDuration">
            <input type="hidden" name="file_size" id="fileSize">
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Your Name *</label>
                        <input type="text" name="user_name" id="userName" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 transition"
                               placeholder="Enter your full name">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Your Email *</label>
                        <input type="email" name="user_email" id="userEmail" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 transition"
                               placeholder="Enter your email address">
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </p>
                </div>
            <?php endif; ?>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Message (Optional)</label>
                <textarea name="message" id="voiceMessage" rows="3" 
                          placeholder="Add a message along with your voice recording..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 transition resize-y"></textarea>
            </div>
            
            <div id="formStatus" class="hidden p-3 rounded-lg"></div>
            
            <button type="submit" id="submitFormBtn" 
                    class="w-full btn-primary px-6 py-3 rounded-lg font-semibold text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-paper-plane mr-2"></i> Send Voice Message
            </button>
        </form>
    </div>
    
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-4xl text-green-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Voice Message Sent!</h3>
                <p class="text-gray-600 mb-6">Your voice message has been sent successfully.</p>
                <button onclick="closeSuccessModal()" class="btn-primary px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-check mr-2"></i> Done
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes waveAnimation {
    0%, 100% { transform: scaleY(0.5); }
    50% { transform: scaleY(1); }
}

.wave-bar {
    animation: waveAnimation 0.8s ease-in-out infinite;
    transform-origin: bottom;
}

#waveContainer .wave-bar:nth-child(odd) {
    animation-duration: 0.6s;
}
#waveContainer .wave-bar:nth-child(3n) {
    animation-duration: 1s;
}
#waveContainer .wave-bar:nth-child(5n+2) {
    animation-duration: 0.7s;
}
</style>

<script>


let mediaRecorder = null;
let audioChunks = [];
let audioBlob = null;
let isRecording = false;
let timerInterval = null;
let seconds = 0;

const startBtn = document.getElementById('startRecordBtn');
const stopBtn = document.getElementById('stopRecordBtn');
const playBtn = document.getElementById('playRecordBtn');
const sendBtn = document.getElementById('sendRecordBtn');
const clearBtn = document.getElementById('clearRecordBtn');
const audioPreview = document.getElementById('audioPreview');
const audioPlayer = document.getElementById('audioPlayer');
const audioInfo = document.getElementById('audioInfo');
const recordingStatus = document.getElementById('recordingStatus');
const timerDisplay = document.getElementById('timerDisplay');
const waveContainer = document.getElementById('waveContainer');
const formStatus = document.getElementById('formStatus');
const submitBtn = document.getElementById('submitFormBtn');

if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    startBtn.disabled = true;
    startBtn.textContent = 'Not supported';
    startBtn.classList.add('bg-gray-400');
}

startBtn.addEventListener('click', async function() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        
        mediaRecorder.ondataavailable = event => audioChunks.push(event.data);
        
        mediaRecorder.onstop = () => {
            audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(audioBlob);
            audioPlayer.src = audioUrl;
            
            const duration = seconds;
            const size = Math.round(audioBlob.size / 1024);
            audioInfo.textContent = `Duration: ${formatTime(duration)} | Size: ${size} KB`;
            document.getElementById('audioDuration').value = duration;
            document.getElementById('fileSize').value = audioBlob.size;
            
            audioPreview.classList.remove('hidden');
            playBtn.classList.remove('hidden');
            sendBtn.classList.remove('hidden');
            clearBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('audioData').value = e.target.result;
            };
            reader.readAsDataURL(audioBlob);
        };
        
        mediaRecorder.start();
        isRecording = true;
        seconds = 0;
        
        startBtn.classList.add('hidden');
        stopBtn.classList.remove('hidden');
        recordingStatus.classList.remove('hidden');
        waveContainer.classList.remove('hidden');
        playBtn.classList.add('hidden');
        sendBtn.classList.add('hidden');
        clearBtn.classList.add('hidden');
        audioPreview.classList.add('hidden');
        formStatus.classList.add('hidden');
        
        timerInterval = setInterval(() => {
            seconds++;
            timerDisplay.textContent = formatTime(seconds);
        }, 1000);
        
    } catch (err) {
        alert('Unable to access microphone. Please allow microphone access.');
    }
});

stopBtn.addEventListener('click', function() {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;
        clearInterval(timerInterval);
        recordingStatus.classList.add('hidden');
        waveContainer.classList.add('hidden');
        stopBtn.classList.add('hidden');
        mediaRecorder.stream.getTracks().forEach(track => track.stop());
    }
});

playBtn.addEventListener('click', function() {
    if (audioPlayer.paused) {
        audioPlayer.play();
        playBtn.innerHTML = '<i class="fas fa-pause"></i> Pause';
    } else {
        audioPlayer.pause();
        playBtn.innerHTML = '<i class="fas fa-play"></i> Play';
    }
});

clearBtn.addEventListener('click', function() {
    audioChunks = [];
    audioBlob = null;
    audioPlayer.src = '';
    audioPreview.classList.add('hidden');
    playBtn.classList.add('hidden');
    sendBtn.classList.add('hidden');
    clearBtn.classList.add('hidden');
    startBtn.classList.remove('hidden');
    seconds = 0;
    timerDisplay.textContent = '00:00';
    document.getElementById('audioData').value = '';
    document.getElementById('audioDuration').value = '';
    document.getElementById('fileSize').value = '';
    formStatus.className = 'hidden';
});


document.getElementById('voiceMessageForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!audioBlob) {
        showFormStatus('Please record a voice message first.', 'error');
        return;
    }
    
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    if (!isLoggedIn) {
        const userName = document.getElementById('userName').value.trim();
        const userEmail = document.getElementById('userEmail').value.trim();
        
        if (!userName || !userEmail) {
            showFormStatus('Please enter your name and email.', 'error');
            return;
        }
        if (!userEmail.includes('@')) {
            showFormStatus('Please enter a valid email address.', 'error');
            return;
        }
    }
    
    const formData = new FormData(this);
    formData.append('audio_data', document.getElementById('audioData').value);
    formData.append('audio_duration', document.getElementById('audioDuration').value);
    formData.append('file_size', document.getElementById('fileSize').value);
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
    showFormStatus('Sending your voice message...', 'info');
    
    try {
        const response = await fetch('send-voice-message.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
            this.reset();
            clearBtn.click();
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Send Voice Message';
            formStatus.className = 'hidden';
        } else {
            showFormStatus(result.message || 'Failed to send.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Send Voice Message';
        }
    } catch (error) {
        showFormStatus('Error: ' + error.message, 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Send Voice Message';
    }
});



function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
}

function showFormStatus(message, type) {
    const classes = {
        'success': 'bg-green-50 border-green-200 text-green-700',
        'error': 'bg-red-50 border-red-200 text-red-700',
        'info': 'bg-blue-50 border-blue-200 text-blue-700'
    };
    
    formStatus.className = 'p-3 rounded-lg border ' + (classes[type] || classes.info);
    formStatus.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + ' mr-2"></i> ' + message;
    formStatus.classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('successModal').classList.remove('flex');
}

audioPlayer.addEventListener('play', function() {
    playBtn.innerHTML = '<i class="fas fa-pause"></i> Pause';
});

audioPlayer.addEventListener('pause', function() {
    playBtn.innerHTML = '<i class="fas fa-play"></i> Play';
});

audioPlayer.addEventListener('ended', function() {
    playBtn.innerHTML = '<i class="fas fa-play"></i> Play';
});

document.getElementById('successModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSuccessModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?>