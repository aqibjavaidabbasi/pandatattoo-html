<?php
/* Analytics — see TRACKING.md. Order matters: GA4 first, then GTM. */
$ga4_id = env('GA4_ID', 'G-YBBGK3PZCZ');
$gtm_id = env('GTM_ID', 'GTM-M3WW4NT7');
?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga4_id) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?= e($ga4_id) ?>');
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= e($gtm_id) ?>');</script>
<!-- End Google Tag Manager -->
