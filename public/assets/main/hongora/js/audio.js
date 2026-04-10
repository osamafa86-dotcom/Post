(function () {
    "use strict";
  
    /***************************************/
    /**      1. تهيئة بيانات البودكاست   **/
    /***************************************/
    const podcastData = [
      {
        name: "دروس أونلاين",
        artist: "أحمد أبو زيد",
        url: "../img/audio/01.mp3",
        cover_art_url: "../img/podcast/1.webp",
      },
      {
        name: "بودكاست جديد",
        artist: "حنجرة",
        url: "../img/audio/02.mp3",
        cover_art_url: "../img/podcast/2.webp",
      },
      // يمكنك إضافة المزيد من عناصر البودكاست هنا
    ];
  
    // تأكد من وجود بيانات في المصفوفة
    if (!Array.isArray(podcastData) || podcastData.length === 0) {
      console.error("لا توجد أي بيانات بودكاست صالحة!");
      return; // الخروج مبكرًا لعدم وجود بيانات
    }
  
    /***********************************************/
    /**  2. جلب العناصر من الـ DOM والتحقق منها  **/
    /***********************************************/
    const audio = document.getElementById("audio");
    const coverArt = document.getElementById("cover-art"); // أو ".cover-art" إذا كنت تريد استخدام querySelector
    const podcastName = document.getElementById("podcast-name");
    const artistName = document.getElementById("artist-name");
  
    const playBtn = document.getElementById("play");
    const pauseBtn = document.getElementById("pause");
    const prevBtn = document.getElementById("prev");
    const nextBtn = document.getElementById("next");
  
    const progressRange = document.getElementById("progress");
    const volumeRange = document.getElementById("volume");
    const volumeIcon = document.getElementById("volume-icon");
  
    // تحقق من أن جميع العناصر موجودة
    if (
      !audio ||
      !coverArt ||
      !podcastName ||
      !artistName ||
      !playBtn ||
      !pauseBtn ||
      !prevBtn ||
      !nextBtn ||
      !progressRange ||
      !volumeRange ||
      !volumeIcon
    ) {
      console.error("يجب التأكد من وجود جميع العناصر المطلوبة في صفحة HTML!");
      return; // الخروج مبكرًا لعدم توفر العناصر
    }
  
    /***************************************/
    /**    3. دوال المساعدة (Helper)      **/
    /***************************************/
  
    /**
     * دالة لتحديث واجهة المستخدم بناءً على الأغنية النشطة في Amplitude
     */
    function updateUI() {
      const currentSong = Amplitude.getActiveSongMetadata();
  
      // إذا وجدنا بيانات للأغنية النشطة
      if (currentSong) {
        coverArt.src = currentSong.cover_art_url || "default-cover.jpg";
        podcastName.textContent = currentSong.name || "Unknown Podcast";
        artistName.textContent = currentSong.artist || "Unknown Artist";
      }
  
      // تبديل زرّي التشغيل والإيقاف بناءً على حالة المشغّل
      if (audio.paused) {
        pauseBtn.style.display = "none";
        playBtn.style.display = "inline-block";
      } else {
        playBtn.style.display = "none";
        pauseBtn.style.display = "inline-block";
      }
    }
  
    /**
     * دالة لتحميل بيانات أول بودكاست في العناصر المرئية (بدون تشغيل الصوت)
     */
    function loadFirstPodcast() {
      // إذا كانت توجد بيانات على الأقل
      const firstPodcast = podcastData[0];
      coverArt.src = firstPodcast.cover_art_url;
      podcastName.textContent = firstPodcast.name;
      artistName.textContent = firstPodcast.artist;
    }
  
    /***************************************/
    /**        4. تهيئة Amplitude         **/
    /***************************************/
    function initAmplitude() {
      // تهيئة Amplitude
      Amplitude.init({
        songs: podcastData,
        audio_element: audio,
        callbacks: {
          song_change: function () {
            updateUI();
          },  play: function () {
            // يشغّل تلقائياً عند بدء تشغيل الصوت
            updateUI();
          },
          pause: function () {
            // يشغّل تلقائياً عند إيقاف الصوت
            updateUI();
          },
        
          initialized: function () {
            // تشغيل أول بودكاست وإيقافه مؤقتًا (مجرد إعداد)
            Amplitude.playSongAtIndex(0);
            Amplitude.pause();
            updateUI();
          },
        },
      });
    }
  
    /***************************************/
    /**         5. إعداد الأحداث         **/
    /***************************************/
    function addEventListeners() {
      // زر التشغيل
      playBtn.addEventListener("click", function () {
        Amplitude.play();
        updateUI();

      });
  
      // زر الإيقاف المؤقت
      pauseBtn.addEventListener("click", function () {
        Amplitude.pause();
        updateUI();

      });
  
      // السابق
      prevBtn.addEventListener("click", function () {
        Amplitude.prev();
        updateUI();

      });
  
      // التالي
      nextBtn.addEventListener("click", function () {
        Amplitude.next();
        updateUI();

      });
  
      // التحكم في مستوى الصوت
      volumeRange.addEventListener("input", function () {
        const newVolume = parseFloat(this.value) / 100;
        Amplitude.setVolume(newVolume);
  
        // إذا كان في وضع كتم، قم بإلغاء الكتم
        if (isMuted) {
          Amplitude.setMute(false);
          isMuted = false;
          volumeIcon.classList.remove("fa-volume-mute");
          volumeIcon.classList.add("fa-volume-up");
        }
      });
  
      // زر الكتم
      volumeIcon.addEventListener("click", function () {
        toggleMute();
      });
  
      // تحريك شريط التقدم (Seek Bar)
      progressRange.addEventListener("input", function () {
        let percentage = parseFloat(this.value);
        Amplitude.setSongPlayedPercentage(percentage);
      });
  
      // تحديث شريط التقدم أثناء التشغيل
      audio.addEventListener("timeupdate", function () {
        if (audio.duration) {
          let progressPercent = (audio.currentTime / audio.duration) * 100;
          progressRange.value = progressPercent;
        }
      });
  
      // الانتقال تلقائيًا للمقطع التالي بعد انتهاء الحالي
      audio.addEventListener("ended", function () {
        Amplitude.next();
      });
    }
  
    /***************************************/
    /**     6. منطق الكتم (Mute Logic)    **/
    /***************************************/
    let isMuted = false;
  
    function toggleMute() {
      if (!isMuted) {
        // كتم الصوت
        Amplitude.setMute(true);
        isMuted = true;
        volumeIcon.classList.remove("fa-volume-up");
        volumeIcon.classList.add("fa-volume-mute");
      } else {
        // إلغاء الكتم
        Amplitude.setMute(false);
        isMuted = false;
        volumeIcon.classList.remove("fa-volume-mute");
        volumeIcon.classList.add("fa-volume-up");
      }
    }
  
    /***************************************/
    /**       7. بدء تشغيل المشغّل       **/
    /***************************************/
    function initializePlayer() {
      loadFirstPodcast(); // تحميل أول بودكاست في الواجهة
      initAmplitude();    // تهيئة مشغل Amplitude
      addEventListeners(); // ضبط الأحداث
    }
  
    // عند تحميل الصفحة بالكامل
    window.addEventListener("load", initializePlayer);
  })();
  