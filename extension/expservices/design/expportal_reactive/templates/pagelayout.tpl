<!DOCTYPE html>
{* expportal_reactive: the whole portal is rendered client-side from the expservices calls
   (ezjscore/call/exp<domain>::<method>). This template only boots it: page frame, accessible landmarks, the
   configuration the scripts read from data-attributes of <body>, jQuery 4 through ezjscore, then the reactive modules
   (vdom, store, components, effects, app). The module result is ignored on purpose; the address is the #/route. *}
<html lang="en" data-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>{ezini( 'Portal', 'SiteTitle', 'expportal.ini' )|wash}</title>
    <script>try {ldelim} var t = localStorage.getItem('expportal-theme'); if (t) document.documentElement.setAttribute('data-theme', t); {rdelim} catch (e) {ldelim}{rdelim}</script>
    <link rel="stylesheet" href={'stylesheets/portal.css'|ezdesign}>
    {ezscript_load( array( 'ezjsc::jquery' ) )}
</head>
<body class="portal"
      data-base="{'/'|ezurl( 'no' )}"
      data-call="{'ezjscore/call'|ezurl( 'no' )}"
      data-title="{ezini( 'Portal', 'SiteTitle', 'expportal.ini' )|wash}"
      data-news="{ezini( 'Portal', 'NewsNode', 'expportal.ini' )}"
      data-shop="{ezini( 'Portal', 'ShopNode', 'expportal.ini' )}"
      data-forums="{ezini( 'Portal', 'ForumsNode', 'expportal.ini' )}"
      data-media="{ezini( 'Portal', 'MediaNode', 'expportal.ini' )}"
      data-pagesize="{ezini( 'Portal', 'PageSize', 'expportal.ini' )}"
      data-design="expportal_reactive">
<a class="skip" href="#main">Skip to content</a>
<header class="site-header" role="banner">
    <a class="brand" href="#/">{ezini( 'Portal', 'SiteTitle', 'expportal.ini' )|wash}</a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav">Menu</button>
    <nav id="nav" class="nav" aria-label="Main"></nav>
    <form class="search" role="search" id="search-form">
        <label class="sr" for="search-q">Search</label>
        <input id="search-q" name="q" type="search" placeholder="Search" autocomplete="off">
        <button type="submit">Search</button>
    </form>
    <div class="tools">
        <a class="basket-link" href="#/basket">Basket <span id="basket-count" class="badge">0</span></a>
        <span id="account-slot"></span>
        <button class="theme-toggle" type="button" aria-label="Switch light or dark theme">Theme</button>
    </div>
</header>
<main id="main" tabindex="-1" role="main"><p class="state">Loading</p><noscript><p class="state">This portal needs JavaScript.</p></noscript></main>
<footer class="site-footer" role="contentinfo"><p>Exponential portal, a client of the expservices calls. <span id="service-status"></span></p></footer>
<div id="live" class="sr" aria-live="polite" role="status"></div>
{* core.js (api, services, util, router, theme) and portal.css come from expportal_jquery through the design fallback *}
<script src={'javascript/portal/core.js'|ezdesign}></script>
<script src={'javascript/reactive/vdom.js'|ezdesign}></script>
<script src={'javascript/reactive/store.js'|ezdesign}></script>
<script src={'javascript/reactive/components.js'|ezdesign}></script>
<script src={'javascript/reactive/effects.js'|ezdesign}></script>
<script src={'javascript/reactive/app.js'|ezdesign}></script>
</body>
</html>
