<?php
function googleHeadTags():string{return <<<'HTML'
<meta name="p:domain_verify" content="ff316b686eb47fa5273380c6b95b7c10"/>
<script>
// Keep analytics events queued while the main experience finishes painting.
window.btdAfterMainPaint=function(run){
 function schedule(){requestAnimationFrame(function(){requestAnimationFrame(function(){if(window.scheduler&&scheduler.postTask)scheduler.postTask(run,{priority:'background'});else if('requestIdleCallback' in window)requestIdleCallback(run,{timeout:1500});else setTimeout(run,0);});});}
 function ready(){var image=document.querySelector('.hero-background,.detail-media,.article-hero');if(image&&image.decode)image.decode().catch(function(){}).then(schedule);else schedule();}
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',ready,{once:true});else ready();
};
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5PC5WSNL');</script>
<!-- End Google Tag Manager -->
<meta name="google-site-verification" content="Bj5Vqti4rz1PGZWvcalAkATE_jxt9FbrUhj_9vVuBxw" />
<!-- Google tag (gtag.js) -->
<script> btdAfterMainPaint(function(){if(document.querySelector('script[src*="gtag/js?id=G-0E3Z4TFE1C"]'))return;var s=document.createElement('script');s.async=true;s.src='https://www.googletagmanager.com/gtag/js?id=G-0E3Z4TFE1C';document.head.appendChild(s);}); </script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-0E3Z4TFE1C');
</script>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
btdAfterMainPaint(function(){s.parentNode.insertBefore(t,s);})}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1650116410113483');
fbq('track', 'PageView');
</script>
<!-- End Meta Pixel Code -->
HTML;}
function trackingBodyTags():string{return <<<'HTML'
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5PC5WSNL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- Meta Pixel (noscript) -->
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1650116410113483&amp;ev=PageView&amp;noscript=1"
/></noscript>
<!-- End Meta Pixel (noscript) -->
HTML;}
