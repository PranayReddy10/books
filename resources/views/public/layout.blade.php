<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Google Analytics (gtag.js) --}}
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-M689GHE19D"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-M689GHE19D');
  </script>

  {{-- ------- SEO ------- --}}
  <title>@yield('meta_title', 'JNTU Books — Free Engineering eBooks, Notes, Photos & Videos')</title>
  <meta name="description" content="@yield('meta_description', 'Read and download JNTU engineering eBooks, notes and study material. Browse student photos and videos. Free to browse, sign in to read.')">
  <meta name="keywords" content="@yield('meta_keywords', 'JNTU books, JNTUH, JNTUA, engineering ebooks, JNTU notes, JNTU timetables, JNTU study material, btech books, free engineering books')">
  <meta name="robots" content="@yield('meta_robots', 'index, follow, max-image-preview:large, max-snippet:-1')">
  <meta name="author" content="JNTU Books">
  <meta name="theme-color" content="#5b3df6">
  <link rel="canonical" href="@yield('canonical', url()->current())">

  {{-- Favicon / logo --}}
  <link rel="icon" href="{{ asset('site_assets/images/favicon.png') }}" type="image/png">
  <link rel="apple-touch-icon" href="{{ asset('site_assets/images/favicon.png') }}">

  {{-- Open Graph --}}
  <meta property="og:site_name" content="JNTU Books">
  <meta property="og:locale" content="en_IN">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:title" content="@yield('meta_title', 'JNTU Books')">
  <meta property="og:description" content="@yield('meta_description', 'Free engineering eBooks, notes, photos and videos for students.')">
  <meta property="og:image" content="@yield('og_image', getcong('app_logo') ? asset(getcong('app_logo')) : asset('upload/ebook_logo.png'))">
  <meta property="og:url" content="{{ url()->current() }}">

  {{-- Twitter --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('meta_title', 'JNTU Books')">
  <meta name="twitter:description" content="@yield('meta_description', 'Free engineering eBooks, notes, photos and videos for students.')">
  <meta name="twitter:image" content="@yield('og_image', getcong('app_logo') ? asset(getcong('app_logo')) : asset('upload/ebook_logo.png'))">

  {{-- Site-wide structured data: Organization + WebSite (helps logo + search box) --}}
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"Organization",
    "name":"JNTU Books",
    "url":"{{ url('/') }}",
    "logo":"{{ getcong('app_logo') ? asset(getcong('app_logo')) : asset('upload/ebook_logo.png') }}",
    "sameAs":[
      "https://www.instagram.com/jntu_books_updates/",
      "https://whatsapp.com/channel/0029Vb5uthA7NoZyvanZPr1J",
      "https://play.google.com/store/apps/details?id=com.jntuh.books"
    ]
  }
  </script>
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"WebSite",
    "name":"JNTU Books",
    "url":"{{ url('/') }}",
    "potentialAction":{
      "@type":"SearchAction",
      "target":"{{ url('/books') }}?q={search_term_string}",
      "query-input":"required name=search_term_string"
    }
  }
  </script>
  @yield('structured_data')

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    :root{
      --ink:#0e1030; --violet:#5b3df6; --violet-2:#8b6cff; --gold:#ffc24b;
      --paper:#f6f5ff; --card:#ffffff; --muted:#6b6f9c; --line:#e7e6fb;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',system-ui,sans-serif;color:var(--ink);background:var(--paper);line-height:1.55}
    a{color:inherit;text-decoration:none}
    h1,h2,h3,.display{font-family:'Sora',sans-serif;letter-spacing:-.02em}
    .wrap{max-width:1180px;margin:0 auto;padding:0 20px}
    img{max-width:100%;display:block}

    /* Nav */
    .nav{position:sticky;top:0;z-index:50;background:rgba(246,245,255,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
    .nav .wrap{display:flex;align-items:center;gap:24px;height:66px}
    .brand{font-family:'Sora';font-weight:800;font-size:20px;display:flex;align-items:center;gap:8px}
    .brand .dot{width:26px;height:26px;border-radius:8px;background:linear-gradient(135deg,var(--violet),var(--gold));display:inline-block;transform:rotate(8deg)}
    .nav a.link{color:var(--muted);font-weight:500;font-size:15px}
    .nav a.link:hover,.nav a.link.active{color:var(--ink)}
    .nav .spacer{flex:1}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:12px;font-weight:600;font-size:14px;border:none;cursor:pointer;transition:.18s}
    .btn-primary{background:var(--violet);color:#fff;box-shadow:0 8px 22px rgba(91,61,246,.28)}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(91,61,246,.36)}
    .btn-ghost{background:#fff;border:1px solid var(--line);color:var(--ink)}
    .btn-gold{background:var(--gold);color:#3a2a00}

    /* Footer */
    footer{margin-top:70px;background:var(--ink);color:#c9cbe8;padding:44px 0}
    footer .wrap{display:flex;flex-wrap:wrap;gap:30px;justify-content:space-between}
    footer a{color:#c9cbe8}footer a:hover{color:#fff}
    footer h4{color:#fff;font-family:'Sora';margin-bottom:12px;font-size:15px}
    footer .col{min-width:150px}
    .foot-bot{border-top:1px solid rgba(255,255,255,.12);margin-top:26px;padding-top:16px;font-size:13px;color:#8b8fbf}

    /* Book card */
    .book-card{background:var(--card);border-radius:16px;overflow:hidden;border:1px solid var(--line);transition:.22s;display:block}
    .book-card:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(20,16,60,.14)}
    .book-card .cover{aspect-ratio:3/4;background:#eceafd;overflow:hidden;position:relative}
    .book-card .cover img{width:100%;height:100%;object-fit:cover}
    /* generated gradient cover fallback */
    .pub-auto-cover{width:100%;height:100%;display:flex;align-items:center;justify-content:center;text-align:center;padding:16px;
      background:linear-gradient(150deg, var(--cc,#5b3df6), #12103a);color:#fff;font-family:'Sora';font-weight:700;font-size:15px;line-height:1.25}
    .cover-fallback-title{display:none}
    .cover-fallback .cover-fallback-title{display:flex;position:absolute;inset:0;align-items:center;justify-content:center;text-align:center;padding:16px;
      background:linear-gradient(150deg,#5b3df6,#12103a);color:#fff;font-family:'Sora';font-weight:700;font-size:15px;line-height:1.25}
    .book-card .meta{padding:12px 14px}
    .book-card .bt{font-family:'Sora';font-weight:600;font-size:15px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .book-card .bc{color:var(--muted);font-size:13px;margin-top:4px}
    .grid-books{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:18px}

    .pill{display:inline-block;padding:5px 12px;border-radius:999px;font-size:13px;font-weight:600;background:#fff;border:1px solid var(--line);color:var(--muted)}
    .pill.active{background:var(--violet);color:#fff;border-color:var(--violet)}

    @media(max-width:640px){.nav a.link.hide-sm{display:none}}
    @media(prefers-reduced-motion:reduce){*{transition:none!important;animation:none!important}}
  </style>
  @yield('head')
</head>
<body>

<nav class="nav">
  <div class="wrap">
    <a href="{{ route('public.home') }}" class="brand"><span class="dot"></span> JNTU Books</a>
    <a href="{{ route('public.books') }}" class="link hide-sm {{ request()->is('books*') ? 'active':'' }}">Books</a>
    <a href="{{ route('public.feed') }}" class="link hide-sm {{ request()->is('feed*') ? 'active':'' }}">Feed</a>
    <span class="spacer"></span>
    @auth
      <a href="{{ route('public.account') }}" class="btn btn-ghost"><i class="fa fa-user"></i> Account</a>
    @else
      <a href="{{ route('login') }}" class="link hide-sm">Sign in</a>
      <a href="{{ route('register') }}" class="btn btn-primary">Join free</a>
    @endauth
  </div>
</nav>

{{-- Smart app banner (Android) --}}
<div id="appBanner" style="display:none;background:#12103a;color:#fff;padding:10px 16px;align-items:center;gap:12px">
  <div class="wrap" style="display:flex;align-items:center;gap:12px;padding:0">
    <div style="width:38px;height:38px;border-radius:9px;background:linear-gradient(135deg,var(--violet),var(--gold));flex:none"></div>
    <div style="flex:1;line-height:1.2">
      <div style="font-family:'Sora';font-weight:700;font-size:14px">JNTU Books app</div>
      <div style="font-size:12px;color:#b9bce8">Faster reading, offline & the full feed.</div>
    </div>
    <button id="appBannerOpen" class="btn btn-gold" style="padding:8px 14px;font-size:13px">Open</button>
    <button id="appBannerClose" style="background:none;border:none;color:#8b8fbf;font-size:20px;cursor:pointer;line-height:1">&times;</button>
  </div>
</div>


@yield('content')

<footer>
  <div class="wrap">
    <div class="col" style="max-width:280px">
      <h4>JNTU Books</h4>
      <p style="font-size:14px">Free engineering eBooks, notes and study material — plus a student feed of photos and videos. Browse free; sign in to read.</p>
    </div>
    <div class="col">
      <h4>Explore</h4>
      <p><a href="{{ route('public.books') }}">Books</a></p>
      <p><a href="{{ route('public.feed') }}">Photo & Video Feed</a></p>
    </div>
    <div class="col">
      <h4>Account</h4>
      <p><a href="{{ route('login') }}">Sign in</a></p>
      <p><a href="{{ route('register') }}">Register</a></p>
    </div>
    <div class="col">
      <h4>Get the app</h4>
      <p style="font-size:14px">Read on the go with the JNTU Books Android app.</p>
      <a href="https://play.google.com/store/apps/details?id=com.jntuh.books" target="_blank" rel="noopener" style="display:inline-block;margin-top:8px">
        <img src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" alt="Get it on Google Play" style="height:46px">
      </a>
    </div>
    <div class="col">
      <h4>Follow us</h4>
      <p style="font-size:14px">Get updates, notes & timetables.</p>
      <div style="display:flex;gap:12px;margin-top:10px">
        <a href="https://www.instagram.com/jntu_books_updates/" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram" style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://whatsapp.com/channel/0029Vb5uthA7NoZyvanZPr1J" target="_blank" rel="noopener" aria-label="WhatsApp Channel" title="WhatsApp Channel" style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fa-brands fa-whatsapp"></i></a>
      </div>
    </div>
  </div>
  <div class="wrap foot-bot">© {{ date('Y') }} JNTU Books. All rights reserved.</div>
</footer>

@yield('scripts')

<script>
(function(){
  var PKG = 'com.jntuh.books';
  var PLAY = 'https://play.google.com/store/apps/details?id=' + PKG;
  var isAndroid = /Android/i.test(navigator.userAgent);
  var isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);

  // Try to open the app for the CURRENT page via Android App Links, else Play Store.
  // Once App Links + assetlinks.json are live, the https URL opens the app directly.
  window.openInApp = function(){
    var current = window.location.href;
    if (isAndroid) {
      // Android intent: open this https link in the app if it handles it, else Play Store.
      var host = window.location.host;
      var path = window.location.pathname + window.location.search;
      var intent = 'intent://' + host + path +
        '#Intent;scheme=https;package=' + PKG +
        ';S.browser_fallback_url=' + encodeURIComponent(PLAY) + ';end';
      window.location.href = intent;
    } else if (isIOS) {
      window.location.href = PLAY; // iOS: no app yet -> store/website
    } else {
      window.open(PLAY, '_blank');
    }
  };

  // Smart banner: show on Android unless dismissed this session.
  try {
    if (isAndroid && !sessionStorage.getItem('appBannerDismissed')) {
      var b = document.getElementById('appBanner');
      if (b) {
        b.style.display = 'block';
        document.getElementById('appBannerOpen').addEventListener('click', window.openInApp);
        document.getElementById('appBannerClose').addEventListener('click', function(){
          b.style.display = 'none';
          try { sessionStorage.setItem('appBannerDismissed','1'); } catch(e){}
        });
      }
    }
  } catch(e){}

  // Share button handler (native share where available, else copy link).
  window.shareThis = function(title){
    window.shareUrl(window.location.href, title);
  };

  // Share a specific URL (e.g. a single post's permalink from the feed).
  window.shareUrl = function(url, title){
    if (navigator.share) {
      navigator.share({ title: title || document.title, url: url }).catch(function(){});
    } else {
      navigator.clipboard.writeText(url).then(function(){
        var t = document.getElementById('shareToast');
        if (t){ t.style.display='block'; setTimeout(function(){ t.style.display='none'; }, 1800); }
      }).catch(function(){ prompt('Copy this link:', url); });
    }
  };
})();
</script>

<div id="shareToast" style="display:none;position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#12103a;color:#fff;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:600;z-index:200;box-shadow:0 10px 30px rgba(0,0,0,.3)">Link copied!</div>
</body>
</html>