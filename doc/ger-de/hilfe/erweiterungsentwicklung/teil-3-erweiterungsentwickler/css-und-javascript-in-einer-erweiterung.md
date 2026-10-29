---
title: CSS und JavaScript in einer Erweiterung
audience: developer, app-creator, designer
category: extension-development
order: 230
summary: Stylesheets und Skripte so laden, wie der Kernel es erwartet - Listen in design.ini, ezscript_require und ezcss_require, der Packer und sein Cache, CSP-taugliche Skripte, keine Versionsparameter.
keywords: CSS, JavaScript, Stylesheet, Skript, Packer, Data-Attribute, css, javascript, stylesheet, script, design.ini, FrontendCSSFileList, BackendCSSFileList, FrontendJavaScriptList, BackendJavaScriptList, ezscript_require, ezcss_require, ezcss_load, ezscript_load, packer, ezjscore, data attributes, CSP
type: help
---
# CSS und JavaScript in einer Erweiterung

Teil 3, Kapitel 23. Aussehen und Verhalten einer Erweiterung im Browser stammen aus Dateien in ihrem Design:
`stylesheets/` und `javascript/`. Sie werden über Einstellungen geladen und vom **ezjscore-Packer** gebündelt, nie
direkt in Templates geschrieben. Dieses Kapitel zeigt die Wege, sie zu laden, und die Regeln, die eine Content Security
Policy vorgibt.

## Dateien

```text
extension/mysite_hello/design/standard/stylesheets/mysite_hello.css
extension/mysite_hello/design/standard/javascript/mysite_hello.js
extension/mysite_hello/design/admin/stylesheets/mysite_hello_admin.css
```

## Auf jeder Seite laden: Listen in design.ini

`extension/mysite_hello/settings/design.ini.append.php` (die Erweiterung `expqueue` macht genau das für ihre
Admin-Dateien):

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

[StylesheetSettings]
FrontendCSSFileList[]=mysite_hello.css
BackendCSSFileList[]=mysite_hello_admin.css

[JavaScriptSettings]
FrontendJavaScriptList[]=mysite_hello.js

*/ ?>
```

| Liste | Geladen von |
|---|---|
| `FrontendCSSFileList[]`, `FrontendJavaScriptList[]` | Website-Designs (ihr Pagelayout ruft `ezcss_load` und `ezscript_load` mit diesen Listen auf) |
| `BackendCSSFileList[]`, `BackendJavaScriptList[]` | die Designs der Administration |
| `CSSFileList[]`, `JavaScriptList[]` | veraltet: beide; verwenden Sie die zwei Listen oben |
| `AdminComponentCSSFileList[]` | die eigenen gemeinsamen Komponenten der Administration; Erweiterungen ergänzen stattdessen `BackendCSSFileList[]`, wie `design.ini` sagt |

Die Dateinamen sind Pfade unterhalb von `stylesheets/` oder `javascript/` einer Designbasis; der Packer findet sie über
die Designkette.

## Auf einer einzelnen Seite laden: require

Wenn nur ein Template eine Datei braucht, fordern Sie sie dort an:

```text
{ezscript_require( 'mysite_hello.js' )}
{ezcss_require( 'mysite_hello.css' )}
```

Der Kopf der Seite gibt die angeforderten Dateien zusammen mit den übrigen aus (`ezscript_load` und `ezcss_load` im
Pagelayout sammeln sie ein). Die Erweiterungen `cjw_newsletter` und `cjw_themes_jumper` verwenden das für Skripte, die
nur einige Seiten brauchen.

## Der Packer

ezjscore fasst die aufgeführten Dateien pro Seitentyp zu einem Stylesheet und einem Skript zusammen, komprimiert sie,
legt sie im Cache ab und gibt ihnen einen Namen mit der Änderungszeit (`[Packer] AppendLastModifiedTime=enabled` in
`ezjscore.ini`), sodass Browser nach einer Änderung eine neue Kopie holen. Die Folgen:

- **Hängen Sie nie `?v=...` an einen Asset-Link an.** Die Zeit im Namen erledigt das bereits.
- Leeren Sie nach dem Ändern eines Stylesheets oder Skripts den Packer-Cache und den Template-Block-Cache (das
  Admin-Pagelayout hält den Link in einem Cache-Block):
  `php bin/php/ezcache.php --clear-id=ezjscore-packer,template-block` oder `./console exp:velocity deploy --packer`.
- Während der Entwicklung liefert `[TemplateSettings] DevelopmentMode=enabled` (oder `[eZJSCore] Packer=disabled` in
  `ezjscore.ini`) die Dateien einzeln aus, was das Debuggen erleichtert.

## Skripte, die unter einer Content Security Policy funktionieren

Die Administration sendet eine Content Security Policy, die Skripte nur aus Dateien der Website erlaubt (und eine Nonce
pro Anfrage). Deshalb:

| Nicht erlaubt | Stattdessen |
|---|---|
| `<script>...</script>` in einem Template | eine Datei in `javascript/`, geladen wie oben |
| `onclick="..."`, `onsubmit="..."`, jedes `on...=` | ein `data-*`-Attribut und ein Listener in Ihrer Datei |
| `href="javascript:..."` | eine Schaltfläche mit einem `data-*`-Attribut |
| `eval()`, `new Function()`, Timer mit Zeichenketten | normale Funktionen |
| `style="..."`, `<style>` | eine Klasse, gestaltet in Ihrem Stylesheet |

`expqueue_confirm.js` der Erweiterung `expqueue` ist ein vollständiges Beispiel: Das Formular des Templates trägt
`data-a4-confirm="<question>"`, und das Skript, geladen über `BackendJavaScriptList[]`, wartet auf `submit`:

```javascript
(function ( window, document ) {
    'use strict';
    document.addEventListener( 'submit', function ( e ) {
        var form = e.target;
        if ( !form || !form.hasAttribute || !form.hasAttribute( 'data-a4-confirm' ) )
            return;
        if ( !window.confirm( form.getAttribute( 'data-a4-confirm' ) ) )
            e.preventDefault();
    } );
})( window, document );
```

(Gekürzt gegenüber der echten Datei, die zusätzlich dem eigenen Handler von admin4 den Vortritt lässt.) Werte gelangen
über Data-Attribute vom Template zum Skript, und zwar maskiert: `data-name="{$name|wash}"`.

## jQuery und expui

Die Administration verwendet jQuery 4 und den Namensraum `Exp` der Erweiterung `expui`; YUI wurde entfernt. admin4 und
admin4l laden keines davon auf jeder Seite: Ein Skript in `BackendJavaScriptList[]` läuft ohne sie, und ein Template,
dessen Skript jQuery oder ein `exp::`-Modul braucht, fordert es an, außerhalb jedes cache-block
(`{ezscript_require( array( 'exp::core::shared', 'exp::io', 'myextension.js' ) )}`); ein jQuery-Plugin, das die älteren
Admin-Designs auf jeder Seite brauchen, gehört in `ClassicBackendJavaScriptList[]`. Verwenden Sie diese, statt eine
eigene Kopie einer Bibliothek mitzubringen; siehe
[admin4 und admin4l ohne jQuery](../../../funktionen/6.0/jquery4-und-entfernung-von-yui.md#admin4-und-admin4l-ohne-jquery),
[Die JavaScript-API von expui](../teil-8-referenz/expui-javascript-api.md)
und [Vom alten jQuery zu jQuery 4](../teil-4-aktualisieren/altes-jquery-zu-jquery-4.md).

## Admin-Seiten gestalten

Verwenden Sie in der Administration die gemeinsamen Komponentenklassen (`.a4-card`, `.a4-btn`, `.a4-table` ...) und
die Design-Tokens, bevor Sie eigenes CSS schreiben; siehe [Admin-Seiten im admin4-Stil](admin-seiten-im-admin4-stil.md).

## Verwandte Seiten

- Zurück: [Ein Design in einer Erweiterung](design-in-einer-erweiterung.md)
- Weiter: [Ajax mit ezjscore-Serverfunktionen](ajax-mit-ezjscore-serverfunktionen.md)
- [Content Security Policy für Autoren von Erweiterungen](content-security-policy-fuer-erweiterungen.md)
- [Theme-Assets, der Packer und CSP](../teil-6-theme-entwickler/theme-assets-der-packer-und-csp.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
