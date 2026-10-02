<!DOCTYPE html>
{* admin4 sign-in page: one calm form, nothing else to read. On wide windows the form on the left and a brand panel
   on the right; on phones and narrow windows only the form, full width, large fields, no brand panel.
   Its own markup (body.a4-login-page, no #page/#header/#columns), so none of the old admin login styles reach it.
   The form itself is the module result (user/login.tpl), shown through page_mainarea.tpl as before. *}
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="robots" content="noindex, nofollow">
    {def $admin_theme = ezpreference( 'admin_theme' )}
    {cache-block keys=array( $navigation_part.identifier, $ui_context, $ui_component, $admin_theme, $access_type )}
    {include uri='design:page_head.tpl'}
    {include uri='design:page_head_style.tpl'}
    {include uri='design:page_head_script.tpl'}
    {/cache-block}
</head>
<body class="a4-login-page">

<main class="a4-login">
    <section class="a4-login-side">
        <header class="a4-login-top">
            <a class="a4-login-logo" href={'/'|ezurl} aria-label="Exponential"></a>
        </header>

        <div class="a4-login-box">
            {include uri="design:page_mainarea.tpl"}
        </div>

        <footer class="a4-login-legal">
            {include uri="design:page_login_copyright.tpl"}
        </footer>
    </section>

    <aside class="a4-login-brand" aria-hidden="true">
        <div class="a4-login-brand-inner">
            <p class="a4-login-brand-kicker">Exponential {fetch( 'setup', 'version' )|explode( '.' )|extract_left( 2 )|implode( '.' )}</p>
            <p class="a4-login-brand-title">{'Content, sites and layouts, managed in one place.'|i18n( 'design/admin/user/login' )}</p>
            <p class="a4-login-brand-sub">{ezini( 'SiteSettings', 'SiteName' )|wash}</p>
        </div>
    </aside>
</main>

{* This comment will be replaced with actual debug report (if debug is on). *}
<!--DEBUG_REPORT-->
</body>
</html>
