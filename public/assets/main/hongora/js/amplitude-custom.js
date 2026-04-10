// استرجاع البيانات من localStorage إذا كانت موجودة
const savedData = JSON.parse(localStorage.getItem("audioPlayerData")) || {
  songs: [],
  currentIndex: 0,
  currentTime: 0,
  volume: 50,
};

// دالة عرض التنبيه (Toast)
function showToast(message) {
  let $toast = $('<div class="toast-notification">' + message + '</div>');
  $toast.css({
    position: 'fixed',
    bottom: '20px',
    right: '20px',
    background: 'rgba(0,0,0,0.7)',
    color: '#fff',
    padding: '10px 20px',
    'border-radius': '4px',
    'z-index': 10000,
    display: 'none'
  });
  $('body').append($toast);
  $toast.fadeIn(400, function(){
    setTimeout(function(){
      $toast.fadeOut(400, function(){
        $toast.remove();
      });
    }, 3000);
  });
}

// دالة نسخ النص للحافظة (fallback للمشاركة)
function copyTextToClipboard(text) {
  var dummy = document.createElement("textarea");
  document.body.appendChild(dummy);
  dummy.value = text;
  dummy.select();
  document.execCommand("copy");
  document.body.removeChild(dummy);
}

// تهيئة Amplitude بالأغاني المحفوظة أو الأساسية
Amplitude.init({
  songs: savedData.songs.length > 0 ? savedData.songs : [
    {
      name: "حلقة يا حب البرغل ",
      artist: "عمار أبو حسين",
      album: "بودكاست اللويح",
      url: "./img/audio/01.mp3",
      cover_art_url: "./img/podcast/1.webp",
      id: "d1",
    },
    {
      name: "حلقة المطب الأول ",
      artist: "عمار أبو حسين",
      album: "بودكاست مطبات ",
      url: "./img/audio/02.mp3",
      cover_art_url: "./img/podcast/2.webp",
      id: "d2",
    },
    {
      name: "حلقة دوزان مرة تانية",
      artist: "عمار أبو حسين",
      album: "بودكاست اللويح",
      url: "./img/audio/03.mp3",
      cover_art_url: "./img/podcast/3.webp",
      id: "d3",
    },
  ],
  callbacks: {
    initialized: function () {
      // عند تحميل الصفحة لو فيه بيانات محفوظة
      if (savedData.songs.length > 0) {
        Amplitude.setSongIndex(savedData.currentIndex);
        Amplitude.skipTo(savedData.currentTime);
      }
      Amplitude.setVolume(savedData.volume);
      showRangeProgress(volumeSlider);
    },
    play: function () {
      saveState();
    },
    pause: function () {
      saveState();
    },
    stop: function () {
      saveState();
    },
    song_change: function () {
      saveState();
    },
  },
});

// دالة لحفظ حالة المشغل في localStorage
function saveState() {
  const currentIndex = Amplitude.getActiveIndex();
  const currentTime = Amplitude.getSongPlayedSeconds();
  const songs = Amplitude.getSongs();
  const volume = Amplitude.getVolume();

  const state = {
    songs,
    currentIndex,
    currentTime,
    volume,
  };
  localStorage.setItem("audioPlayerData", JSON.stringify(state));
}

// عند الضغط على زر إظهار/إخفاء قائمة التشغيل
$(".show-playlist").on("click", function () {
  $("#playlist-container").toggle();
});
$(".close-playlist").on("click", function () {
  $("#playlist-container").hide();
});

// الحلقات المتاحة للإضافة
var songsToAdd = [
  {
    name: "العقلية في حياتنا الحلقة: 1",
    artist: "ليندا كلارك",
    album: "قصة حياة",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/01.jpg",
    id: "n1",
  },
  {
    name: "الخارج والبقاء على قيد الحياة الحلقة: 2",
    artist: "جون هنري",
    album: "السفر",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/02.jpg",
    id: "n2",
  },
  {
    name: "تأسيس نادي للبودكاست الحلقة: 3",
    artist: "دانيال لورادو",
    album: "التكنولوجيا",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/03.jpg",
    id: "n3",
  },
  {
    name: "تحسين الأعمال الحلقة: 4",
    artist: "إدوارد مونسو",
    album: "الأعمال",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/04.jpg",
    id: "n4",
  },
  {
    name: "تقييم مجالاتك الحلقة: 5",
    artist: "هولي لويل",
    album: "النمو الشخصي",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/05.jpg",
    id: "n5",
  },
  {
    name: "جعل البودكاست فريدًا الحلقة: 6",
    artist: "ديبورا مايرز",
    album: "الكوميديا",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/06.jpg",
    id: "n6",
  },
  {
    name: "أفكار بودكاست الموسيقى الحلقة: 7",
    artist: "تيري بيرك",
    album: "الموسيقى",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/07.jpg",
    id: "n7",
  },
  {
    name: "تعليم المهارات الشخصية الحلقة: 8",
    artist: "أليكس فييلو",
    album: "التعليم",
    url: "img/audio/01.mp3",
    cover_art_url: "img/show/08.jpg",
    id: "n8",
  },
];

// عند الضغط على زر التشغيل الخارجي لإضافة حلقة من البطاقات
$(".player-btn").on("click", function (e) {
  if (Amplitude.getSongs().length === 0) {
    $(".playlist-content").html("");
  }

  var songToAddIndex = $(this).attr("data-song-add");
  var index = Amplitude.getSongs().findIndex(
    (item) => item.id === songsToAdd[songToAddIndex].id
  );

  if (index === -1) {
    var newIndex = Amplitude.addSong(songsToAdd[songToAddIndex]);
    appendToSongDisplay(songsToAdd[songToAddIndex], newIndex);
    Amplitude.playSongAtIndex(newIndex);
    Amplitude.bindNewElements();
    setPlayButtonView(songToAddIndex);
    saveState();
    showToast("تمت إضافة الحلقة");
  } else {
    Amplitude.playSongAtIndex(index);
    setPlayButtonView(songToAddIndex);
  }
});

// تحديث مظهر زر التشغيل بعد إضافة أو اختيار حلقة
function setPlayButtonView(index) {
  $("[data-song-add]").removeClass("active");
  $("[data-song-add=" + index + "]").addClass("active");
}

// إضافة حلقة جديدة لقائمة التشغيل في الـ DOM، مع زر مشاركة ودعم draggable
function appendToSongDisplay(song, index) {
  $(".playlist-content").append(`
    <div class="playlist-item" draggable="true" data-song-id="${song.id}">
      <div class="playlist-song amplitude-song-container amplitude-play-pause" data-amplitude-song-index="${index}">
        <img src="${song.cover_art_url}" alt="غلاف الحلقة"/>
        <div class="playlist-song-meta">
          <span class="playlist-song-name">${song.name}</span>
          <span class="playlist-artist-album">${song.artist}</span>
        </div>
      </div>
      <button type="button" class="playlist-remove" data-remove-id="${song.id}">
        <i class="far fa-xmark"></i>
      </button>
      <button type="button" class="playlist-share" data-song-id="${song.id}">
        <i class="fa-solid fa-share-alt"></i>
      </button>
    </div>
  `);
}

// عند الضغط على زر حذف الحلقة من قائمة التشغيل
$(".playlist-content").on("click", ".playlist-remove", function (e) {
  e.stopPropagation();

  var id = $(this).attr("data-remove-id");
  var $item = $(this).closest(".playlist-item");
  var index = Amplitude.getSongs().findIndex((song) => song.id === id);

  if (index > -1) {
    $item.remove();
    Amplitude.removeSong(index);
    saveState();
    showToast("تم حذف الحلقة");

    if (Amplitude.getSongs().length === 0) {
      $(".playlist-content").html(`
        <div class="col-sm-8 col-10 mx-auto mt-5 text-center">
          <i class="far fa-music mb-3"></i>
          <p>No songs, album or playlist are added on lineup.</p>
        </div>`);
    }
  }
});

// حدث لمشاركة الحلقة عند الضغط على زر المشاركة
$(".playlist-content").on("click", ".playlist-share", function(e) {
  e.stopPropagation();
  var songId = $(this).attr("data-song-id");
  var song = Amplitude.getSongs().find(item => item.id === songId);
  if(song) {
    if(navigator.share) {
      navigator.share({
        title: song.name,
        text: "استمع للحلقة: " + song.name,
        url: window.location.href + "?song=" + song.id
      }).then(() => {
        showToast("تم مشاركة الحلقة!");
      }).catch((error) => {
        showToast("خطأ في المشاركة: " + error);
      });
    } else {
      const shareLink = window.location.href + "?song=" + song.id;
      copyTextToClipboard(shareLink);
      showToast("تم نسخ رابط المشاركة للحلقة!");
    }
  }
});

// دعم سحب وإفلات إعادة ترتيب العناصر في قائمة التشغيل
var draggedItem = null;
$(".playlist-content").on("dragstart", ".playlist-item", function(e){
  draggedItem = this;
  $(this).addClass("dragging");
});
$(".playlist-content").on("dragover", ".playlist-item", function(e){
  e.preventDefault();
});
$(".playlist-content").on("drop", ".playlist-item", function(e){
  e.preventDefault();
  if(draggedItem !== this) {
    if($(this).index() > $(draggedItem).index()){
      $(this).after(draggedItem);
    } else {
      $(this).before(draggedItem);
    }
    updateSongsOrder();
  }
});
$(".playlist-content").on("dragend", ".playlist-item", function(e){
  $(this).removeClass("dragging");
  draggedItem = null;
});

// تحديث ترتيب الأغاني في Amplitude بناءً على ترتيب العناصر الجديدة
function updateSongsOrder(){
  var newSongsOrder = [];
  $(".playlist-item").each(function(){
    var songId = $(this).attr("data-song-id");
    var song = Amplitude.getSongs().find(item => item.id === songId);
    if(song){
      newSongsOrder.push(song);
    }
  });
  // تحديث الترتيب في Amplitude (باستخدام الخاصية الداخلية)
  Amplitude._songs = newSongsOrder;
  Amplitude.bindNewElements();
  saveState();
  showToast("تم إعادة ترتيب القائمة بنجاح");
}

// تحكم في مستوى الصوت: تحديث العرض وإعدادات Amplitude
const audioPlayerContainer = document.getElementById("player-volume");
const volumeSlider = document.getElementById("volume-slider");
const volumeBtn = document.getElementById("amplitude-mute");

const showRangeProgress = (rangeInput) => {
  audioPlayerContainer.style.setProperty(
    "--volume-before-width",
    (rangeInput.value / rangeInput.max) * 100 + "%"
  );
};

volumeSlider.value = savedData.volume || 50;
Amplitude.setVolume(volumeSlider.value);

volumeSlider.addEventListener("input", (e) => {
  Amplitude.setVolume(e.target.value);
  showRangeProgress(e.target);
  saveState();
});

volumeBtn.addEventListener("click", () => {
  showRangeProgress(volumeSlider);
  saveState();
});

// عند الضغط على زر إخفاء المشغل (لتوسيع أو تصغير العرض)
$(".audio-player-hide").on("click", function () {
  $(".audio-player").toggleClass("show");
  $("#playlist-container").hide();
});
