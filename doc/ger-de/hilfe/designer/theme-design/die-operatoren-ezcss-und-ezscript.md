---
title: Die Operatoren ezcss und ezscript
audience: designer, developer
category: theme-design
order: 220
summary: Referenz der acht Packer-Operatoren von ezjscore (ezcss, ezcss_require, ezcss_load, ezcssfiles, ezscript, ezscript_require, ezscript_load, ezscriptfiles) mit jedem Parameter, Standardwert und Verhalten.
keywords: ezcss, ezcss_load, ezcss_require, ezcssfiles, ezscript, ezscript_load, ezscript_require, ezscriptfiles, pack_level, media, ignore_loaded, Operatorreferenz, Operatoren von ezjscore, operator reference, ezjscore operators
type: help
---
# Die Operatoren ezcss und ezscript

Diese Seite ist eine Referenz für Designer und Entwickler. Alle acht Operatoren sind in der Klasse `ezjscPackerTemplateFunctions` in
`extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` definiert und in `extension/ezjscore/autoloads/eztemplateautoload.php`
registriert.

## Inhalt

1. [Signaturen](#signaturen)
2. [require, load und einfach: der Unterschied](#require-load-und-einfach-der-unterschied)
3. [Packstufen](#packstufen)
4. [Dateinamen](#dateinamen)
5. [Beispiele](#beispiele)

## Signaturen

```text
ezscript[_require|_load]( array|string scripts
                          [, string type='text/javascript'
                          [, string language=''
                          [, string charset='utf-8'
                          [, int pack_level=2 ]]]] )

ezcss[_require|_load]( array|string css_files
                       [, string media='all'
                       [, string type='text/css'
                       [, string rel='stylesheet'
                       [, string charset=''
                       [, int pack_level=3 ]]]]] )

ezscriptfiles( array|string scripts [, int pack_level=2 [, bool ignore_loaded=false ]] )
ezcssfiles( array|string css_files [, int pack_level=3 [, bool ignore_loaded=false ]] )
```

(aus dem Kopfkommentar der Klasse; `charset` bei Stylesheets ist veraltet, weil es kein gültiges HTML ist.) Arrays dürfen
verschachtelt sein; sie werden abgeflacht, sodass sich `ezini()`-Listen innerhalb eines Arrays übergeben lassen.

## require, load und einfach: der Unterschied

| Aufruf | Bevor ein _load lief | Nachdem ein _load lief |
|---|---|---|
| `ezcss_require` / `ezscript_require` | merkt sich die Dateien (in der persistent variable der View), gibt nichts aus | gibt die noch nicht ausgegebenen Dateien aus |
| `ezcss_load` / `ezscript_load` | gibt seine Dateien plus alle gemerkten aus, markiert die Art als geladen | gibt die noch nicht ausgegebenen Dateien aus |
| `ezcss` / `ezscript` | gibt seine Dateien aus | gibt seine Dateien aus |
| `ezcssfiles` / `ezscriptfiles` | gibt die URLs seiner Dateien zurück (ohne die schon ausgegebenen, außer mit `ignore_loaded`) | ebenso |

Weil die gemerkten Dateien in der persistent variable der Inhaltsansicht stehen, liefert auch eine Vollansicht aus dem View-Cache ihre
angeforderten Dateien an das Pagelayout.

## Packstufen

| Stufe | Wirkung |
|---|---|
| 0 | aus: jede Datei wird einzeln ausgeliefert |
| 1 | Dateien zu einer zusammengeführt |
| 2 | zusammengeführt, Leerraum und Kommentare entfernt (JavaScript- und CSS-Optimierer aus `ezjscore.ini [eZJSCore]`) |
| 3 | wie 2 (veraltet) |

Die Stufe wird auf 0 gezwungen, wenn `site.ini [TemplateSettings] DevelopmentMode=enabled` gilt, außer `ezjscore.ini [eZJSCore] Packer`
ist gesetzt (auf `enabled`, `disabled` oder eine Stufe von 0 bis 3). Stylesheets: Relative `url()`-Pfade werden beim Packen in absolute
umgeschrieben, sodass Bilder und Schriften weiter laden.

## Dateinamen

- `'main.css'`: gefunden in `stylesheets/` der Design-Kaskade (`javascript/` für Skripte).
- `'sub/file.css'`: ein Unterordner.
- `'/extension/x/design/y/stylesheets/file.css'`: Ein führendes `/` bedeutet einen Pfad ab der Wurzel der Installation, außerhalb des
  üblichen Unterordners.
- `'ezjsc::jquery'`, `'ezjsc::jqueryUI'`: Generatoren von ezjscore (`[ezjscServer_ezjsc]` in `ezjscore.ini`); ein Name mit `::` ruft
  eine Serverfunktion auf, statt eine Datei zu lesen.

Eine Datei, die nicht gefunden wird, wird mit einer Warnung in der Debug-Ausgabe übersprungen („Could not find: ...“), nicht mit einem
Fehler.

## Beispiele

```tpl
{* all stylesheets of the design, plus whatever views required *}
{ezcss_load( array( 'base.css', 'components.css' ) )}

{* merged but not minified: keeps vendor CSS byte-identical *}
{ezcss_load( array( 'vendor.css' ), 'all', 'text/css', 'stylesheet', '', 1 )}

{* jQuery and the site script at the end of the body *}
{ezscript_load( array( 'ezjsc::jquery', 'site.js' ) )}

{* deferred bundle *}
{foreach ezscriptfiles( array( 'late.js' ), 2, true() ) as $url}<script defer src="{$url}"></script>{/foreach}
```

> **Hinweis**
> Das Design starter ruft `ezcss_load( ..., 'all', 'text/css', 'stylesheet', 1 )` mit fünf Argumenten auf. An der Signatur gemessen ist
> das fünfte `charset`, sodass die Packstufe auf ihrem Standard bleibt. Übergeben Sie alle sechs Argumente (mit `''` für charset), wenn
> Sie die Stufe setzen möchten.

## Verwandte Seiten

- [Stylesheets laden](stylesheets-laden.md)
- [JavaScript laden](javascript-laden.md)
- [Der Packer](der-packer.md)
- [ezjscore](../../../funktionen/6.0/erweiterungen/ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
