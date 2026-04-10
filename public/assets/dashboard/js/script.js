
// document.addEventListener('DOMContentLoaded', function () { }

document.addEventListener('DOMContentLoaded', function() {
    // document.addEventListener('alpine:init', function() {

    // alert('f');
        cropper = $('#contain').cropimage( {
            image: 'http://127.0.0.1:8000/assets/dashboard/images/logo.png',
            imgFormat: 'auto',
            minWidth: 100,
            minHeight: 100,
            circleCrop: false,
            zoomable: true,
            background: 'transparent',
            inBoundGrid: true,
            outBoundColor: 'none',
            noBorder: false
        } )

        $('.button-crop').on('click', function(){
            // Get the cropped image source URL
            const blobDataURL = cropper.getImage('PNG') // JPEG, PNG, ...
            if( !blobDataURL ) return

            // Callback with cropped image's blob generated URL
            $('#move-stats').html('<h3>Cropped Image</h3><img style="margin:10% auto;" src="'+ blobDataURL +'">')
        })

        $('.button-reset-crop').on('click', function(){
            cropper.reset()
        })
    // });
});
// $(function(){
    // Initiate cropper
// })
// $(function(){
//
//     cropper = Cropper( {
//         image: 'http://127.0.0.1:8000/assets/dashboard/images/logo.png',
//         imgFormat: 'auto',
//         minWidth: 100,
//         minHeight: 100,
//         circleCrop: false,
//         zoomable: true,
//         background: 'transparent',
//         inBoundGrid: true,
//         outBoundColor: 'none',
//         noBorder: false
//     } ,'#contain',
//
//     $('.button-crop').on('click', function(){
//         const blobDataURL = cropper.getImage('PNG') // JPEG, PNG, ...
//         if( !blobDataURL ) return
//         $('#move-stats').html('<h3>Cropped Image</h3><img style="margin:10% auto;" src="'+ blobDataURL +'">')
//     }));
// })
