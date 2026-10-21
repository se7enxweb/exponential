---
title: Ein Rundgang durch das Theme simple
audience: designer
category: theme-design
order: 110
summary: Das Design simple (Erweiterung sevenx_themes_simple), das leichte öffentliche Standarddesign: wie sein Pagelayout die Seitendaten von ezwebin und einen Cache-Block verwendet, wie es CSS und Skripte aus design.ini lädt, seine Menüs und was Sie ändern sollten, wenn Sie darauf aufbauen.
keywords: Theme simple, sevenx_themes_simple, Design simple, Standarddesign, ezpagedata, cache-block, CSSFileList, JavaScriptList, Menü-Templates, Aufklappmenü, template_look, pagedesign, simple theme, simple design, default design, menu templates, dropdown menu
type: help
---
# Ein Rundgang durch das Theme simple

Diese Seite ist für Designer, die das Design simple der Erweiterung `sevenx_themes_simple` (Composer-Paket `se7enxweb/sevenx_themes_simple`)
verwenden oder anpassen, das leichte und responsive öffentliche Design einer Standardinstallation. Sie beantwortet: wie das Design
aufgebaut ist, wie es seine Dateien lädt und worauf Sie achten sollten, wenn Sie darauf aufbauen. Die eigene Seite der Erweiterung ist
[sevenx_themes_simple](../../../../eng-us/features/6.0/extensions/sevenx_themes_simple.md).

## Inhalt

1. [Worauf es aufbaut](#worauf-es-aufbaut)
2. [Das Pagelayout](#das-pagelayout)
3. [Stylesheets und Skripte aus design.ini](#stylesheets-und-skripte-aus-designini)
4. [Menüs](#menüs)
5. [Overrides](#overrides)
6. [Was Sie modernisieren sollten, wenn Sie darauf aufbauen](#was-sie-modernisieren-sollten-wenn-sie-darauf-aufbauen)

## Worauf es aufbaut

- Die Inhaltsklassen der ezwebin-Familie (Artikel, Blog, Ordner, Galerie, Produkt, Veranstaltung ...), die die Erweiterung als
  `.ezpkg`-Pakete in `packages/` mitliefert.
- Der Operator `ezpagedata` der Erweiterung `ezwebin`, der Fakten über die Seite sammelt (CSS-Klassen, das Objekt „template look“, den
  aktuellen Knoten).
- `ezjscore` zum Packen und `ezjsc::jquery` für jQuery.

## Das Pagelayout

`design/simple/templates/pagelayout.tpl`:

1. definiert den Warenkorb, den Rollen-Hash des Benutzers und den Wurzelknoten aus den Einstellungen;
2. öffnet einen `{cache-block}` mit den Schlüsseln URI (mit dem Fehlertyp für Fehlerseiten), ob der Warenkorb leer ist, die Objekt-id des
   Benutzers und einen optionalen `$extra_cache_key`;
3. liest darin `ezpagedata()` und das Objekt `template_look` (das Objekt „page design“, das Redakteure in der Administration bearbeiten,
   mit Feldern wie dem Logo und **Hide powered by**);
4. bindet `page_head.tpl`, `page_head_style.tpl`, `page_head_script.tpl`, den Kopfbereich und die Menüs, den Inhalt und den Fußbereich ein.

Der Cache-Block macht den Rahmen günstig: Er wird einmal pro URI und Benutzer gerendert und dann aus dem Cache gelesen. Die Regeln zu den
Schlüsseln stehen in [Cache-block](../templates/cache-block.md).

## Stylesheets und Skripte aus design.ini

Anders als das Design media liest simple seine Dateilisten aus den Einstellungen. `page_head_style.tpl`:

```tpl
{ezcss_load( array( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
                    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
```

und `page_head_script.tpl`:

```tpl
{ezscript_load( array( ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                       ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
```

mit den Listen in `extension/sevenx_themes_simple/settings/design.ini.append.php`:

```ini
[StylesheetSettings]
CSSFileList[]=websitetoolbar.css
CSSFileList[]=libs/fontawesome/css/all.min.css
CSSFileList[]=magnific-popup.css
CSSFileList[]=main.css
CSSFileList[]=main.res.css
[JavaScriptSettings]
JavaScriptList[]=ezjsc::jquery
JavaScriptList[]=jquery.magnific-popup.js
JavaScriptList[]=main.js
```

Ein Design auf simple fügt also mit einer Zeile in seiner eigenen `design.ini.append.php` ein Stylesheet hinzu:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=mytheme.css
```

Es wird nach den Dateien von simple ausgegeben, sodass Ihre Regeln bei gleicher Spezifität gewinnen.

## Menüs

`templates/menu/` enthält mehrere Menü-Templates (`flat_top.tpl`, `double_top.tpl`, `dropdown.tpl`, `multi-dropdown.tpl`, `topbar.tpl`,
`social-media.tpl`). `page_header.tpl` bindet `menu/dropdown.tpl` ein; die anderen Möglichkeiten stehen als auskommentierte Einbindungen
da, sodass ein Menüwechsel eine Änderung von einer Zeile in einer Kopie von `page_header.tpl` in Ihrem Design ist. Menüs holen die Kinder
des Wurzelknotens; siehe [Menüs und Navigation](../templates/menues-und-navigation.md).

Der Ordner enthält auch Überbleibsel von Editoren (Dateien `*.backup`, `*.save`, `#...#`). Das Design verwendet sie nicht; kopieren Sie sie
nicht.

## Overrides

`design/simple/override/templates/` hat `embed/`, `full/`, `line/`, `view/` und `article_full_page.tpl`; die Regeln stehen in
`settings/siteaccess/site/override.ini.append.php` und `settings/siteaccess/sevenx_site_user/` der Erweiterung.

## Was Sie modernisieren sollten, wenn Sie darauf aufbauen

Das Design simple ist älter als die aktuellen Regeln für die Administration. Wenn Sie darauf ein neues öffentliches Design bauen, bedenken
Sie:

- **Externe Schriften.** Das Pagelayout lädt Inter von `fonts.googleapis.com` mit `preconnect`-Hinweisen. Eine selbst gehostete Kopie im
  `fonts/` Ihres Designs ist schneller und braucht keine Anfrage an Dritte (das Design media tut das, siehe
  [Schriften in einem Design](schriften-in-einem-design.md)).
- **Bedingte Kommentare für den Internet Explorer.** Die Body-Klassen `<!--[if IE ...]>` bedienen Browser, die nicht mehr unterstützt
  werden. Ein neues Design braucht sie nicht.
- **Font Awesome.** Es bringt eine große Symbolschrift mit; ein SVG-Symbolsatz, der nur dort verwendet wird, wo er gebraucht wird, ist
  leichter ([Symbole in einem Design](icons-in-einem-design.md)).
- **Content Security Policy.** Prüfen Sie die Templates des Designs auf Inline-Skripte und -Stile, bevor Sie eine Richtlinie für die
  Website einschalten ([CSP-sicheres Design](csp-sicheres-design.md)).

## Verwandte Seiten

- [Die Themes, die mit Exponential geliefert werden](themes-die-mit-exponential-kommen.md)
- [Das Design ezwebin als Grundlage](ezwebin-als-grundlage.md)
- [Stylesheets laden](stylesheets-laden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
