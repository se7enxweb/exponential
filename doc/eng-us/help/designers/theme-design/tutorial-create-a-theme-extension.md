---
title: "Tutorial: create a theme extension"
audience: designer, developer
category: theme-design
order: 770
summary: Build a complete theme extension from an empty folder, step by step: folders, metadata, design registration, a siteaccess that uses the design, a CSP-safe pagelayout, a stylesheet with tokens and dark mode, a script, an override, translations, activation, caches and the checks before you call it done.
keywords: tutorial, create theme, new theme extension, theme from scratch, design extension, step by step, pagelayout, DesignExtensions, siteaccess design, first theme, mytheme
featured: true
type: help
---
# Tutorial: create a theme extension

This tutorial is for designers who build their first Exponential theme. At the end you have an extension `mytheme`
with a design `mytheme`, used by one siteaccess, with a pagelayout, a stylesheet with light and dark tokens, a script
file, one override and translatable texts. Every file is shown in full. Allow about two hours.

You need: shell access to a development installation (never a live site), and someone who may change installation
settings (an administrator), for step 7.

## Contents

1. [Step 1: the folders](#step-1-the-folders)
2. [Step 2: metadata](#step-2-metadata)
3. [Step 3: register the design](#step-3-register-the-design)
4. [Step 4: the pagelayout](#step-4-the-pagelayout)
5. [Step 5: stylesheet and script](#step-5-stylesheet-and-script)
6. [Step 6: an override](#step-6-an-override)
7. [Step 7: a siteaccess that uses the design](#step-7-a-siteaccess-that-uses-the-design)
8. [Step 8: activate and clear caches](#step-8-activate-and-clear-caches)
9. [Step 9: check it](#step-9-check-it)
10. [Next steps](#next-steps)

## Step 1: the folders

```bash
cd /path/to/exponential
mkdir -p extension/mytheme/settings \
         extension/mytheme/design/mytheme/templates/pagelayout \
         extension/mytheme/design/mytheme/override/templates/full \
         extension/mytheme/design/mytheme/stylesheets \
         extension/mytheme/design/mytheme/javascript \
         extension/mytheme/design/mytheme/images \
         extension/mytheme/translations/ger-DE
```

Name rules: lowercase, letters, digits and underscores; a prefix of your own so it never collides with another theme
([Naming your theme extension](../extensions/naming-your-theme-extension.md)).

## Step 2: metadata

`extension/mytheme/extension.xml`:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<extension>
  <name>mytheme</name>
  <summary>My theme</summary>
  <version>1.0.0</version>
  <license>GPL-2.0-or-later</license>
  <maintainers>
    <maintainer>Your name</maintainer>
  </maintainers>
</extension>
```

`extension/mytheme/ezinfo.php`:

```php
<?php
class mythemeInfo
{
    static function info()
    {
        return array(
            'Name'      => 'My theme',
            'Version'   => '1.0.0',
            'Copyright' => 'Copyright (C) 2026 Your name',
            'License'   => 'GNU General Public License v2.0 (or any later version)',
        );
    }
}
```

These two files are what the administration's About page reads. Versions in both must always agree
([ezinfo.php](../extensions/ezinfo-php.md), [extension.xml](../extensions/extension-xml.md)).

## Step 3: register the design

`extension/mytheme/settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=mytheme
*/ ?>
```

From now on `extension/mytheme/design/mytheme/` is a design base for the design `mytheme`
([Design extensions](design-extensions.md)).

## Step 4: the pagelayout

`extension/mytheme/design/mytheme/templates/pagelayout.tpl`:

```tpl
{def $mt_node = false()}
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.node_id ) )}
    {set $mt_node = fetch( 'content', 'node', hash( 'node_id', $module_result.content_info.node_id ) )}
{/if}
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{if $mt_node}{$mt_node.name|wash} - {/if}{$site.title|wash}</title>
    {ezscript( array( 'theme-guard.js' ) )}
    {ezcss_load( array( 'tokens.css', 'mytheme.css' ) )}
</head>
<body>
<a class="skip-link" href="#main">{'Skip to main content'|i18n( 'design/mytheme' )}</a>
{include uri='design:pagelayout/header.tpl'}
<main id="main" tabindex="-1">
{$module_result.content}
</main>
{include uri='design:pagelayout/footer.tpl'}
{ezscript_load( array( 'mytheme.js' ) )}
</body>
</html>
{undef $mt_node}
```

`templates/pagelayout/header.tpl`:

```tpl
<header class="site-header">
    <a class="site-name" href={'/'|ezurl}>{$site.title|wash}</a>
    <button type="button" class="theme-toggle" id="theme-toggle">{'Color mode'|i18n( 'design/mytheme' )}</button>
</header>
```

`templates/pagelayout/footer.tpl`:

```tpl
<footer class="site-footer">
    <p>{'Powered by Exponential'|i18n( 'design/mytheme' )}</p>
</footer>
```

No `<style>`, no inline script: this pagelayout runs under a strict Content Security Policy
([CSP-safe design](csp-safe-design.md)).

## Step 5: stylesheet and script

`stylesheets/tokens.css`: copy the starter set of [Design tokens](design-tokens.md) (light values on `:root`, dark
values on `:root[data-theme="dark"]`).

`stylesheets/mytheme.css`:

```css
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; color: var(--site-ink); background: var(--site-surface); font-family: var(--site-font); line-height: 1.5; }
a { color: var(--site-accent); }
.site-header, .site-footer, main { max-width: 72rem; margin-inline: auto; padding: var(--site-space-3); }
.site-header { display: flex; justify-content: space-between; align-items: center; }
.skip-link { position: fixed; inset-inline-start: 0.5rem; inset-block-start: 0.5rem; transform: translateY(-200%);
             background: var(--site-card); color: var(--site-ink); padding: var(--site-space-2) var(--site-space-3); }
.skip-link:focus { transform: none; }
:where(a, button):focus-visible { outline: 3px solid var(--site-focus); outline-offset: 2px; }
```

`javascript/theme-guard.js`: the guard from [The theme toggle and the pre-paint guard](theme-toggle-pre-paint-guard.md).

`javascript/mytheme.js`:

```js
(function () {
  var b = document.getElementById('theme-toggle');
  if (!b || !window.mythemeTheme) return;
  b.addEventListener('click', function () {
    var order = { light: 'dark', dark: 'system', system: 'light' };
    window.mythemeTheme.save(order[document.documentElement.getAttribute('data-theme-choice')] || 'light');
  });
})();
```

## Step 6: an override

Give articles their own full view. `extension/mytheme/settings/override.ini.append.php` (active for every siteaccess
that has the extension; for one siteaccess only, put it in `settings/siteaccess/<name>/` of the extension):

```ini
<?php /* #?ini charset="utf-8"?
[mytheme_full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
*/ ?>
```

`design/mytheme/override/templates/full/article.tpl`:

```tpl
<article class="article">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.intro.has_content}
        <div class="article-intro">{attribute_view_gui attribute=$node.data_map.intro}</div>
    {/if}
    {attribute_view_gui attribute=$node.data_map.body}
</article>
```

Use the attribute identifiers of your installation's article class ([Override basics](../templates/override-ini-basics.md)).

## Step 7: a siteaccess that uses the design

Ask your administrator to create (or let you create on your development installation) a siteaccess `mytheme_site`,
listed in `AvailableSiteAccessList[]`, with `settings/siteaccess/mytheme_site/site.ini.append.php` holding, besides the
usual site settings:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard

[ExtensionSettings]
ActiveAccessExtensions[]=mytheme
```

See [Previewing a design on a siteaccess](previewing-a-design-on-a-siteaccess.md).

## Step 8: activate and clear caches

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

On installations that run Velocity, `./console exp:velocity deploy` does both and reloads PHP.

## Step 9: check it

- [ ] `/mytheme_site/` shows your header, the content, your footer.
- [ ] An article shows `full/article.tpl` (turn on `ShowTemplatePathComments` to see it).
- [ ] The color mode button switches light, dark, system; reloading keeps the choice; no white flash.
- [ ] Tab once: the skip link appears.
- [ ] `./console exp:eztemplatecheck extension/mytheme/design/mytheme/templates/` reports every file valid.
- [ ] No errors in the browser console; no `<style>` or inline script in the page source.

## Next steps

- Translate the texts: [Tutorial: internationalize a theme](tutorial-internationalize-a-theme.md).
- Put the extension under Git and give it a `composer.json`: [Theme extensions: start here](../extensions/theme-extensions-start-here.md).
- Run the [theme launch checklist](checklist-theme-launch.md).

## Related pages

- [Design extensions](design-extensions.md)
- [The pagelayout](the-pagelayout.md)
- [What a theme extension is](../extensions/what-a-theme-extension-is.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
