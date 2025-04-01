document.addEventListener("DOMContentLoaded", function () {
    const mainVideo = document.getElementById("mainVideo");
    const miniPlayer = document.getElementById("miniPlayer");
    const miniVideo = document.getElementById("miniVideo");
    const playPauseMini = document.getElementById("playPauseMini");
    const expandMini = document.getElementById("expandMini");
    const closeMini = document.getElementById("closeMini");
    const playButton = document.getElementById("playButton");
    const videoOverlay = document.getElementById("videoOverlay");
    const progressBar = document.getElementById("miniProgressBar");
    let hasPlayed = false;
    let lastMiniTime = 0;

    mainVideo.addEventListener("loadeddata", () => console.log("Video loaded successfully."));

    // Auto-play when visible on screen
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !hasPlayed) {
                mainVideo.play().catch(console.error);
            }
        });
    }, { threshold: 0.5 });

    observer.observe(mainVideo);

    playButton.addEventListener("click", () => {
        if (mainVideo.paused) {
            mainVideo.currentTime = 0;
            mainVideo.play().catch(console.error);
            hasPlayed = true;
        } else {
            mainVideo.pause();
        }
    });

    mainVideo.addEventListener("play", () => {
        videoOverlay.style.opacity = "0";
        videoOverlay.style.pointerEvents = "none";
    });

    mainVideo.addEventListener("pause", () => {
        videoOverlay.style.opacity = "1";
        videoOverlay.style.pointerEvents = "auto";
    });

    mainVideo.addEventListener("fullscreenchange", () => {
        mainVideo.controls = !!document.fullscreenElement;
    });

    // Show mini-player when scrolling past the video
    window.addEventListener("scroll", () => {
        const videoRect = mainVideo.getBoundingClientRect();
        const shouldShowMini = videoRect.bottom <= 0 && !mainVideo.paused;

        miniPlayer.style.display = shouldShowMini ? "block" : "none";

        if (shouldShowMini) {
            miniVideo.src = mainVideo.currentSrc;
            miniVideo.currentTime = lastMiniTime;
            miniVideo.play();
        }
    });

    playPauseMini.addEventListener("click", () => {
        if (miniVideo.paused) {
            miniVideo.play();
            playPauseMini.innerHTML = '<i class="fas fa-pause"></i>';
        } else {
            miniVideo.pause();
            playPauseMini.innerHTML = '<i class="fas fa-play"></i>';
        }
    });

    expandMini.addEventListener("click", () => {
        miniPlayer.style.display = "none";
        window.scrollTo({ top: 0, behavior: "smooth" });
        mainVideo.currentTime = miniVideo.currentTime;
        mainVideo.play();
    });

    closeMini.addEventListener("click", () => {
        miniPlayer.style.opacity = "0";
        setTimeout(() => {
            miniPlayer.style.display = "none";
        }, 300);
        
        lastMiniTime = miniVideo.currentTime;
        miniVideo.pause();

        // **Pause the main video when closing mini-player**
        mainVideo.pause();
    });

    miniVideo.addEventListener("timeupdate", () => {
        progressBar.style.width = (miniVideo.currentTime / miniVideo.duration) * 100 + "%";
    });

    mainVideo.parentElement.addEventListener("mouseenter", () => {
        if (mainVideo.paused) showOverlay(true);
    });

    mainVideo.parentElement.addEventListener("mouseleave", () => showOverlay(false));

    function showOverlay(visible) {
        if (!mainVideo.paused) return;
        videoOverlay.style.opacity = visible ? "1" : "0";
        videoOverlay.style.pointerEvents = visible ? "auto" : "none";
    }
});
