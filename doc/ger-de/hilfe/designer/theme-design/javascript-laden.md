---
title: JavaScript laden
audience: designer
category: theme-design
order: 210
summary: Wie die Skripte eines Designs mit ezscript_load und ezscriptfiles auf die Seite kommen, die Skriptlisten in design.ini, Skripte, die ein einzelnes Template anfordert, verzögerte Bündel, Konfiguration als JSON-Daten statt Inline-Code und jQuery über ezjsc::jquery.
keywords: JavaScript, Skripte, ezscript_load, ezscript_require, ezscriptfiles, JavaScriptList, FrontendJavaScriptList, BackendJavaScriptList, defer, Skriptreihenfolge, ezjsc::jquery, Inline-Skript, JSON-Konfiguration, javascript, scripts, script order, inline script, json config
type: help
---
# JavaScript laden

Diese Seite ist für Designer, die einem Design Verhalten hinzufügen: einen Menüschalter, einen Slider, ein Cookie-Banner. Sie
beantwortet: welcher Operator Skripte ausgibt, wie Sie sie verzögern, wie ein Template ein Skript nur bei Bedarf anfordert und wie Sie
einem Skript Einstellungen ohne Inline-Code übergeben.

## Inhalt

1. [Die Operatoren](#die-operatoren)
2. [Im Pagelayout](#im-pagelayout)
3. [Verzögerte Skripte](#verzögerte-skripte)
4. [Ein Skript für eine Art von Seite](#ein-skript-für-eine-art-von-seite)
5. [Skriptlisten in design.ini](#skriptlisten-in-designini)
6. [Einem Skript Einstellungen übergeben](#einem-skript-einstellungen-übergeben)
7. [Regeln](#regeln)

## Die Operatoren

Aus `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`:

| Operator | Tut |
|---|---|
| `ezscript_load( files, type, language, charset, pack_level )` | gibt `<script>`-Tags für die angegebenen Dateien aus plus jede mit `ezscript_require` vorgemerkte Datei |
| `ezscript_require( files )` | merkt Dateien für das nächste `ezscript_load` vor (gibt sie sofort aus, wenn es schon lief) |
| `ezscript( files, ... )` | gibt Tags nur für die angegebenen Dateien aus |
| `ezscriptfiles( files, pack_level, ignore_loaded )` | gibt gepackte URLs zurück, sodass Sie das `<script>`-Tag selbst schreiben |

Dateien sind relativ zu `javascript/` und werden über die Design-Kaskade gefunden. Die Standard-Packstufe ist 2 (zusammengeführt und
Leerraum entfernt). Ein Name mit `::` ist ein Generator, keine Datei: `ezjsc::jquery` gibt jQuery aus der lokalen Kopie aus, die in
`ezjscore.ini [eZJSCore] LocalScripts[]` konfiguriert ist (jQuery 4.0.0 mit jQuery Migrate 4.0.2 in dieser Version).

## Im Pagelayout

Am Ende des Body, damit die Seite gezeichnet wird, bevor Skripte laufen:

```tpl
    {ezscript_load( array( 'ezjsc::jquery', 'mytheme.js' ) )}
</body>
```

Skripte, die vor dem ersten Zeichnen laufen müssen (zum Beispiel ein Theme-Umschalter, der eine Klasse am `<html>` setzt), gehören als
kleine eigene Dateien in den Kopf.

## Verzögerte Skripte

`ezscript_load` schreibt einfache `<script src>`-Tags. Für `defer` fragen Sie nach den URLs und schreiben das Tag selbst:

```tpl
{foreach ezscriptfiles( array( 'gallery.js', 'consent.js' ), 2, true() ) as $script_url}
<script defer src="{$script_url}"></script>
{/foreach}
```

`true()` als dritter Parameter (`ignore_loaded`) hält dieses Bündel unabhängig von schon ausgegebenen Dateien. Das Design media macht
genau das für seine nicht kritischen Skripte (`pagelayout/head/script.tpl`).

## Ein Skript für eine Art von Seite

```tpl
{* override/templates/full/gallery.tpl *}
{ezscript_require( array( 'gallery.js' ) )}
```

Das `ezscript_load` des Pagelayouts gibt es aus. Seiten ohne Galerie laden es nie.

## Skriptlisten in design.ini

`settings/design.ini [JavaScriptSettings]`:

| Einstellung | Gedacht für |
|---|---|
| `FrontendJavaScriptList[]` | öffentliche Designs |
| `BackendJavaScriptList[]` | jede Seite der Administrationsdesigns (der Kernel führt `tools/ezjsselection.js`, `ezadmin_menubar.js` ... auf); nur Skripte, die ohne jQuery laufen, da admin4 und admin4l jQuery nur dort laden, wo ein Template es anfordert |
| `ClassicBackendJavaScriptList[]` | jede Seite der älteren Administrationsdesigns (`admin`, `admin3`), nach jQuery und `BackendJavaScriptList[]`; admin4 liest sie nicht |
| `JavaScriptList[]` | ältere Liste für beide (DEPRECATED zugunsten der beiden oben) |

Ein admin4-Template, dessen Skript jQuery oder ein Exponential-UI-Modul braucht, fordert es selbst an, außerhalb jedes
cache-block: `{ezscript_require( array( 'ezjsc::jquery', 'myadmin.js' ) )}` (siehe
[admin4 und admin4l ohne jQuery](../../../funktionen/6.0/jquery4-und-entfernung-von-yui.md#admin4-und-admin4l-ohne-jquery)).

Wie bei Stylesheets zählt eine Liste nur, wenn Ihr Pagelayout sie liest:

```tpl
{ezscript_load( array( 'mytheme.js', ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
```

## Einem Skript Einstellungen übergeben

Geben Sie keine Werte in ein Inline-Skript aus. Verwenden Sie einen JSON-Datenblock, den der Browser nicht ausführt und eine Content
Security Policy nicht blockiert, oder `data-*`-Attribute:

```tpl
<script type="application/json" id="mytheme-config">{json_encode( hash(
    'searchUrl', '/content/search'|ezurl( 'no' ),
    'closeLabel', 'Close'|i18n( 'design/mytheme' ) ) )}</script>
```

```js
// javascript/mytheme.js
const config = JSON.parse(document.getElementById('mytheme-config').textContent);
```

`json_encode` ist ein Operator von ezjscore. Das Design media übergibt so seine Texte zur Video-Einwilligung.

## Regeln

- Kein Inline-`<script>` mit Code und keine `onclick=""`-Attribute ([Kein Inline-JavaScript](kein-inline-javascript.md)).
- Kein `?v=` an Skript-URLs; die Dateinamen des Packers ändern sich mit dem Inhalt.
- Ziehen Sie kleine, seitenspezifische Dateien mit `ezscript_require` einem großen Bündel auf jeder Seite vor.
- Testen Sie mit offener Konsole des Browsers: Ein Fehler stoppt ein ganzes gepacktes Bündel.

## Verwandte Seiten

- [Stylesheets laden](stylesheets-laden.md)
- [jQuery in Designs](jquery-in-designs.md)
- [Der Packer](der-packer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
