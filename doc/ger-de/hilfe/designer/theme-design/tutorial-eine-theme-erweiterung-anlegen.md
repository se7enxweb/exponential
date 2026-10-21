---
title: "Tutorial: eine Theme-Erweiterung anlegen"
audience: designer, developer
category: theme-design
order: 770
summary: Eine vollständige Theme-Erweiterung Schritt für Schritt aus einem leeren Ordner bauen: Ordner, Metadaten, Registrierung des Designs, ein Siteaccess, der das Design verwendet, ein CSP-sicheres Pagelayout, ein Stylesheet mit Token und dunklem Modus, ein Skript, ein Override, Übersetzungen, Aktivierung, Caches und die Prüfungen, bevor Sie es fertig nennen.
keywords: Tutorial, Theme anlegen, neue Theme-Erweiterung, Theme von Grund auf, Design-Erweiterung, Schritt für Schritt, Pagelayout, DesignExtensions, Design eines Siteaccess, erstes Theme, mytheme, tutorial, create theme, new theme extension, theme from scratch, design extension, step by step, siteaccess design, first theme
featured: true
type: help
---
# Tutorial: eine Theme-Erweiterung anlegen

Dieses Tutorial ist für Designer, die ihr erstes Exponential-Theme bauen. Am Ende haben Sie eine Erweiterung `mytheme` mit einem Design
`mytheme`, das ein Siteaccess verwendet, mit einem Pagelayout, einem Stylesheet mit hellen und dunklen Token, einer Skriptdatei, einem Override
und übersetzbaren Texten. Jede Datei wird vollständig gezeigt. Planen Sie etwa zwei Stunden ein.

Sie brauchen: Shell-Zugang zu einer Entwicklungsinstallation (nie einer Live-Website) und jemanden, der Einstellungen der Installation ändern
darf (einen Administrator), für Schritt 7.

## Inhalt

1. [Schritt 1: die Ordner](#schritt-1-die-ordner)
2. [Schritt 2: Metadaten](#schritt-2-metadaten)
3. [Schritt 3: das Design registrieren](#schritt-3-das-design-registrieren)
4. [Schritt 4: das Pagelayout](#schritt-4-das-pagelayout)
5. [Schritt 5: Stylesheet und Skript](#schritt-5-stylesheet-und-skript)
6. [Schritt 6: ein Override](#schritt-6-ein-override)
7. [Schritt 7: ein Siteaccess, der das Design verwendet](#schritt-7-ein-siteaccess-der-das-design-verwendet)
8. [Schritt 8: aktivieren und Caches leeren](#schritt-8-aktivieren-und-caches-leeren)
9. [Schritt 9: prüfen](#schritt-9-prüfen)
10. [Nächste Schritte](#nächste-schritte)

## Schritt 1: die Ordner

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

Namensregeln: Kleinbuchstaben, Buchstaben, Ziffern und Unterstriche; ein eigenes Präfix, damit er nie mit einem anderen Theme kollidiert
([Ihre Theme-Erweiterung benennen](../erweiterungen/ihre-theme-erweiterung-benennen.md)).

## Schritt 2: Metadaten

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

Diese beiden Dateien liest die Seite About der Administration. Die Versionen in beiden müssen immer übereinstimmen
([ezinfo.php](../erweiterungen/ezinfo-php.md), [extension.xml](../erweiterungen/extension-xml.md)).

## Schritt 3: das Design registrieren

`extension/mytheme/settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=mytheme
*/ ?>
```

Ab jetzt ist `extension/mytheme/design/mytheme/` eine Design-Basis für das Design `mytheme` ([Design-Erweiterungen](design-erweiterungen.md)).

## Schritt 4: das Pagelayout

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

Kein `<style>`, kein Inline-Skript: Dieses Pagelayout läuft unter einer strengen Content Security Policy ([CSP-sicheres Design](csp-sicheres-design.md)).

## Schritt 5: Stylesheet und Skript

`stylesheets/tokens.css`: Kopieren Sie den Startsatz aus [Design-Token](design-tokens.md) (helle Werte auf `:root`, dunkle Werte auf
`:root[data-theme="dark"]`).

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

`javascript/theme-guard.js`: der Schutz aus [Der Theme-Umschalter und der Schutz vor dem ersten Zeichnen](theme-umschalter-und-pre-paint-guard.md).

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

## Schritt 6: ein Override

Geben Sie Artikeln eine eigene Vollansicht. `extension/mytheme/settings/override.ini.append.php` (aktiv für jeden Siteaccess, der die
Erweiterung hat; für nur einen Siteaccess legen Sie es in `settings/siteaccess/<name>/` der Erweiterung):

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

Verwenden Sie die Attributbezeichner der Artikelklasse Ihrer Installation ([Grundlagen der Overrides](../templates/override-grundlagen.md)).

## Schritt 7: ein Siteaccess, der das Design verwendet

Bitten Sie Ihren Administrator, einen Siteaccess `mytheme_site` anzulegen (oder legen Sie ihn auf Ihrer Entwicklungsinstallation selbst
an), aufgeführt in `AvailableSiteAccessList[]`, mit `settings/siteaccess/mytheme_site/site.ini.append.php`, die neben den üblichen
Website-Einstellungen enthält:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard

[ExtensionSettings]
ActiveAccessExtensions[]=mytheme
```

Siehe [Ein Design auf einem Siteaccess in der Vorschau ansehen](design-vorschau-auf-einem-siteaccess.md).

## Schritt 8: aktivieren und Caches leeren

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Auf Installationen mit Velocity erledigt `./console exp:velocity deploy` beides und lädt PHP neu.

## Schritt 9: prüfen

- [ ] `/mytheme_site/` zeigt Ihren Kopfbereich, den Inhalt, Ihren Fußbereich.
- [ ] Ein Artikel zeigt `full/article.tpl` (schalten Sie `ShowTemplatePathComments` ein, um es zu sehen).
- [ ] Der Knopf für den Farbmodus wechselt zwischen hell, dunkel, System; Neuladen behält die Auswahl; kein weißes Aufblitzen.
- [ ] Einmal Tab: Der Skip-Link erscheint.
- [ ] `./console exp:eztemplatecheck extension/mytheme/design/mytheme/templates/` meldet jede Datei als gültig.
- [ ] Keine Fehler in der Konsole des Browsers; kein `<style>` und kein Inline-Skript im Seitenquelltext.

## Nächste Schritte

- Übersetzen Sie die Texte: [Tutorial: ein Theme internationalisieren](tutorial-ein-theme-internationalisieren.md).
- Stellen Sie die Erweiterung unter Git und geben Sie ihr eine `composer.json`: [Theme-Erweiterungen: hier beginnen](../erweiterungen/theme-erweiterungen-hier-beginnen.md).
- Gehen Sie die [Checkliste zum Start eines Themes](checkliste-theme-start.md) durch.

## Verwandte Seiten

- [Design-Erweiterungen](design-erweiterungen.md)
- [Das Pagelayout](das-pagelayout.md)
- [Was eine Theme-Erweiterung ist](../erweiterungen/was-eine-theme-erweiterung-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
