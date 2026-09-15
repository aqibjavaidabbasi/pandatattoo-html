<?php $meta_pixel_id = env('META_PIXEL_ID', '2148964251981412'); ?>
<!-- Meta Pixel Code -->
<script>!function(b,e,f,g,a,c,d){b.fbq||(a=b.fbq=function(){a.callMethod?a.callMethod.apply(a,arguments):a.queue.push(arguments)},b._fbq||(b._fbq=a),a.push=a,a.loaded=!0,a.version="2.0",a.queue=[],c=e.createElement(f),c.async=!0,c.src=g,d=e.getElementsByTagName(f)[0],d.parentNode.insertBefore(c,d))}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");fbq("init","<?= e($meta_pixel_id) ?>");fbq("track","PageView");</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?= e($meta_pixel_id) ?>&ev=PageView&noscript=1"></noscript>
<!-- End Meta Pixel Code -->
