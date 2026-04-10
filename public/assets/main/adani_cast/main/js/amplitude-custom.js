// Amplitude.init({
//   songs: [
//     {
//       name: "حلقة بودكاست الأعمال 01",
//       artist: "تيموثي ك. أغيلار",
//       album: "بودكاست الأعمال",
//       url: "../{{asset('assets/main/img/audio/01.mp3')}}",
//       cover_art_url: "../{{asset('assets/main/img/audio/01.jpg')}}",
//       id: "d1",
//     },
//     {
//       name: "حلقة بودكاست التعليم 02",
//       artist: "دونا س. نيكولز",
//       album: "بودكاست التعليم",
//       url: "{{asset('assets/main/img/audio/02.mp3')}}",
//       cover_art_url: "{{asset('assets/main/img/audio/02.jpg')}}",
//       id: "d2",
//     },
//     {
//       name: "حلقة بودكاست الأعمال 03",
//       artist: "ألفين ك. بيفيريدج",
//       album: "بودكاست السفر",
//       url: "{{asset('assets/main/img/audio/03.mp3')}}",
//       cover_art_url: "{{asset('assets/main/img/audio/03.jpg')}}",
//       id: "d3",
//     },
//   ],
// });
//
// // playlist toggle
//
// $(".show-playlist").on("click", function () {
//   $("#playlist-container").toggle();
// });
//
// $(".close-playlist").on("click", function () {
//   $("#playlist-container").hide();
// });
//
// var songsToAdd = [
//   {
//     name: "العقلية في حياتنا الحلقة: 1",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/01.jpg')}}",
//     id: "n1",
//   },
//   {
//     name: "الخارج والبقاء على قيد الحياة الحلقة: 2",
//     artist: "جون هنري",
//     album: "السفر",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/02.jpg')}}",
//     id: "n2",
//   },
//   {
//     name: "تأسيس نادي للبودكاست الحلقة: 3",
//     artist: "دانيال لورادو",
//     album: "التكنولوجيا",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/03.jpg')}}",
//     id: "n3",
//   },
//   {
//     name: "تحسين الأعمال الحلقة: 4",
//     artist: "إدوارد مونسو",
//     album: "الأعمال",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/04.jpg')}}",
//     id: "n4",
//   },
//   {
//     name: "تقييم مجالاتك الحلقة: 5",
//     artist: "هولي لويل",
//     album: "النمو الشخصي",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/05.jpg')}}",
//     id: "n5",
//   },
//   {
//     name: "جعل البودكاست فريدًا الحلقة: 6",
//     artist: "ديبورا مايرز",
//     album: "الكوميديا",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/06.jpg')}}",
//     id: "n6",
//   },
//   {
//     name: "أفكار بودكاست الموسيقى الحلقة: 7",
//     artist: "تيري بيرك",
//     album: "الموسيقى",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/07.jpg')}}",
//     id: "n7",
//   },
//   {
//     name: "تعليم المهارات الشخصية الحلقة: 8",
//     artist: "أليكس فييلو",
//     album: "التعليم",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/show/08.jpg')}}",
//     id: "n8",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 1",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/01.jpg')}}",
//     id: "n9",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 2",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/02.jpg')}}",
//     id: "n10",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 3",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/03.jpg')}}",
//     id: "n11",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 4",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/04.jpg')}}",
//     id: "n12",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 5",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/05.jpg')}}",
//     id: "n13",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 6",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/06.jpg')}}",
//     id: "n14",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 7",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/07.jpg')}}",
//     id: "n15",
//   },
//   {
//     name: "العقلية في حياتنا الحلقة: 8",
//     artist: "ليندا كلارك",
//     album: "قصة حياة",
//     url: "{{asset('assets/main/img/audio/01.mp3')}}",
//     cover_art_url: "{{asset('assets/main/img/episode/08.jpg')}}",
//     id: "n16",
//   },
// ];
//
// $(".player-btn").on("click", function (e) {
//   if (Amplitude.getSongs().length === 0) {
//     $(".playlist-content").html("");
//   }
//
//   var songToAddIndex = $(this).attr("data-song-add");
//   var index = Amplitude.getSongs().findIndex(
//     (item) => item.id === songsToAdd[songToAddIndex].id
//   );
//   if (index === -1) {
//     var newIndex = Amplitude.addSong(songsToAdd[songToAddIndex]);
//     appendToSongDisplay(songsToAdd[songToAddIndex], newIndex);
//     Amplitude.playSongAtIndex(newIndex);
//     Amplitude.bindNewElements();
//     setPlayButtonView(songToAddIndex);
//   } else {
//     console.log("Already added in playlist!");
//   }
// });
//
// // flobal play/pause button view
// function setPlayButtonView(index) {
//   $("[data-song-add]").removeClass("active");
//   $("[data-song-add=" + index + "]").addClass("active");
// }
//
// // appends the song to the display
// function appendToSongDisplay(song, index) {
//   $(".playlist-content").append(`
//       <div class="playlist-item">
//         <div class="playlist-song amplitude-song-container amplitude-play-pause" data-amplitude-song-index="${index}">
//           <{{asset('assets/main/img src="${song.cover_art_url}"/>
//           <div class="playlist-song-meta">
//             <span class="playlist-song-name">${song.name}</span>
//             <span class="playlist-artist-album">${song.artist}</span>
//           </div>
//         </div>
//         <button type="button" class="playlist-remove" data-remove-id="${song.id}"><i class="far fa-xmark"></i></button>
//       </div>
//     `);
// }
//
// // playlist remove song
// $(".playlist-content").on("click", ".playlist-remove", function (e) {
//   e.stopPropagation();
//   var id = $(this).attr("data-remove-id");
//   var $item = $(this).closest(".playlist-item");
//   var index = Amplitude.getSongs().findIndex((song) => song.id === id);
//
//   if (index > -1) {
//     $item.remove();
//     Amplitude.removeSong(index);
//     if (Amplitude.getSongs().length === 0) {
//       $(".playlist-content")
//         .html(`<div class="col-sm-8 col-10 mx-auto mt-5 text-center">
//             <i class="far fa-music mb-3"></i>
//             <p>No songs, album or playlist are added on lineup.</p>
//             </div>`);
//     }
//   }
// });
//
// // for volume progress
// const audioPlayerContainer = document.getElementById("player-volume");
// const volumeSlider = document.getElementById("volume-slider");
// const volumeBtn = document.getElementById("amplitude-mute");
//
// const showRangeProgress = (rangeInput) => {
//   audioPlayerContainer.style.setProperty(
//     "--volume-before-width",
//     (rangeInput.value / rangeInput.max) * 100 + "%"
//   );
// };
//
// showRangeProgress(volumeSlider);
//
// volumeSlider.addEventListener("input", (e) => {
//   showRangeProgress(e.target);
// });
//
// volumeBtn.addEventListener("click", () => {
//   showRangeProgress(volumeSlider);
// });
//
// // player hide show
// $(".audio-player-hide").on("click", function () {
//   $(".audio-player").toggleClass("show");
//   $("#playlist-container").hide();
// });
