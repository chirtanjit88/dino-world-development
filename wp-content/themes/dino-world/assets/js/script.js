jQuery(document).ready(function($) {
    $(".navbar-toggler").click(function() {
        $(".navbar-toggler").toggleClass("active-show");
    });

    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
            delay: 50
        });
    }
});

// Refresh AOS on window load and when images finish rendering
window.addEventListener('load', function() {
    if (typeof AOS !== 'undefined') {
        AOS.refresh();
    }
});

// Hero Banner Video Transition:
// Shows banner image on load, starts video playback, and replaces image after 2 seconds once video is ready.
(function initHeroBanner() {
    const image = document.getElementById("hero-img");
    const video = document.getElementById("hero-video");

    if (!video || !image) return;

    video.muted = true;

    let minimumTimePassed = false;
    let videoIsReady = false;
    let transitionCompleted = false;

    function revealVideo() {
        if (transitionCompleted) return;
        if (minimumTimePassed && videoIsReady) {
            transitionCompleted = true;
            image.classList.add("fade-out");
        }
    }

    const triggerPlay = () => {
        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    videoIsReady = true;
                    revealVideo();
                })
                .catch((error) => {
                    console.warn("Autoplay deferred:", error);
                    const resumeOnInteraction = () => {
                        video.play().then(() => {
                            videoIsReady = true;
                            revealVideo();
                        }).catch(() => {});
                    };
                    window.addEventListener("click", resumeOnInteraction, { once: true });
                    window.addEventListener("touchstart", resumeOnInteraction, { once: true });
                    window.addEventListener("scroll", resumeOnInteraction, { once: true });
                });
        }
    };

    video.addEventListener("playing", () => {
        videoIsReady = true;
        revealVideo();
    });

    if (video.readyState >= 2) {
        triggerPlay();
    } else {
        video.addEventListener("canplay", triggerPlay, { once: true });
        triggerPlay();
    }

    // Exactly 2 seconds initial image display
    setTimeout(function () {
        minimumTimePassed = true;
        revealVideo();
    }, 2000);
})();



if ( document.querySelector('.splide') && typeof Splide !== 'undefined' ) {
    var splide = new Splide( '.splide', {
      perPage: 3,
      gap    : '1rem',
      breakpoints: {
        992: {
          perPage: 2,
          gap    : '.7rem',
          height : '20rem',
        },
         768: {
          perPage: 2,
          gap    : '.7rem',
          height : '25rem',
        },
        475: {
          perPage: 1,
          gap    : '.7rem',
          height : '15rem',
        },
        375: {
          perPage: 1,
          gap    : '.7rem',
          height : '20rem',
        },
      },
    } );

    splide.mount();
}

 

document.addEventListener('DOMContentLoaded', function () {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const searchInputs = document.querySelectorAll('.blog-search-input, #searchInput');

    let currentCat = 'all';
    let searchKeyword = '';

    function applyFilters() {
        const blogCols = document.querySelectorAll('.blog-col');
        blogCols.forEach(col => {
            const catAttr = (col.getAttribute('data-cat') || '').trim();
            const cats = catAttr ? catAttr.split(/\s+/) : [];
            const title = (col.getAttribute('data-title') || '').toLowerCase();

            const catMatch = (currentCat === 'all' || cats.indexOf(currentCat) !== -1);
            const searchMatch = !searchKeyword || title.indexOf(searchKeyword) !== -1;

            if (catMatch && searchMatch) {
                col.classList.remove('d-none');
                col.style.removeProperty('display');
            } else {
                col.classList.add('d-none');
                col.style.setProperty('display', 'none', 'important');
            }
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCat = this.getAttribute('data-filter') || 'all';
            applyFilters();
        });
    });

    searchInputs.forEach(input => {
        input.addEventListener('input', function () {
            searchKeyword = this.value.trim().toLowerCase();
            applyFilters();
        });
    });
});