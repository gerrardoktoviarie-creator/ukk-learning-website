document.addEventListener('DOMContentLoaded', function() {
    // User Profile Dropdown
    const userProfile = document.querySelector('.user-profile');
    const profileDropdown = document.querySelector('.user-profile-dropdown');
    
    if (userProfile && profileDropdown) {
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
    }
    
    // Sound effect for buttons
    const buttons = document.querySelectorAll('.btn-mapel, .btn-submit');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            playSound();
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            filterMapels(this.value);
        });
    }
});

function filterMapels(searchTerm) {
    const mapelCards = document.querySelectorAll('.mapel-card');
    
    mapelCards.forEach(card => {
        const mapelName = card.querySelector('.mapel-name').textContent.toLowerCase();
        const mapelDesc = card.querySelector('.mapel-description').textContent.toLowerCase();
        
        if (mapelName.includes(searchTerm.toLowerCase()) || mapelDesc.includes(searchTerm.toLowerCase())) {
            card.style.display = '';
            card.style.animation = 'fadeIn 0.3s ease-in';
        } else {
            card.style.display = 'none';
        }
    });
}

function playSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 600;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.15);
    } catch(e) {
        console.log('Audio not supported');
    }
}