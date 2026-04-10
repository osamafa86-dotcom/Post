@extends('components.layouts.dashboard.app')


<html>
<head>
    <link href="{{ asset('assets/dashboard/css/cropimage.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('assets/dashboard/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/cropimage.js') }}"></script>
    <script>
        $(function(){
            // Initiate cropper
            cropper = $('#contain').cropimage( {
                image: '{{$image}}',
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
        })
    </script>
    {{--    <script src="{{ asset('assets/dashboard/js/script.js') }}"></script>--}}
</head>

<body>
<div id="move-stats" style="position:absolute;left:2%;top:5%;padding:10px;box-shadow:0 0 8px 2px rgba(100, 100, 100, .4);font-family:tahoma;"></div>
<div id="contain" style="position:absolute;width:44%;height:65%;left:24%;top:15%;box-shadow:0 0 8px 2px rgba(100, 100, 100, .4);"></div>

<button class="button-crop" style="position:absolute;left:14%;top:15%;box-shadow:0 0 8px 2px rgba(100, 100, 100, .4);">Done</button>
<button class="button-reset-crop">Reset</button>
</body>
</html>


