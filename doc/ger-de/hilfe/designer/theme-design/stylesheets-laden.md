---
title: Stylesheets laden
audience: designer
category: theme-design
order: 200
summary: Wie das CSS eines Designs auf die Seite kommt: der Operator ezcss_load, die Listen in design.ini (CSSFileList, FrontendCSSFileList, BackendCSSFileList, AdminComponentCSSFileList), Stylesheets, die ein Inhalts-Template anfordert, Reihenfolge, media und der Packer.
keywords: Stylesheets, CSS, CSS laden, ezcss_load, ezcss_require, ezcss, CSSFileList, FrontendCSSFileList, BackendCSSFileList, AdminComponentCSSFileList, design.ini, StylesheetSettings, CSS-Reihenfolge, stylesheets, css, load css, css order
featured: true
type: help
---
# Stylesheets laden

Diese Seite ist für Designer, die einem Design ein Stylesheet hinzufügen oder sich fragen, warum eines nicht auf der Seite ist. Sie
beantwortet: welcher Operator Stylesheets ausgibt, wo die Dateilisten stehen, in welcher Reihenfolge Dateien ankommen und wie ein
einzelnes Inhalts-Template ein zusätzliches Stylesheet anfordern kann.

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [ezcss_load im Pagelayout](#ezcss_load-im-pagelayout)
3. [Dateilisten in design.ini](#dateilisten-in-designini)
4. [Ein Stylesheet nur für eine Art von Seite](#ein-stylesheet-nur-für-eine-art-von-seite)
5. [Reihenfolge und Spezifität](#reihenfolge-und-spezifität)
6. [Media und Druck](#media-und-druck)
7. [Was Sie nicht tun sollten](#was-sie-nicht-tun-sollten)

## Die kurze Antwort

1. Legen Sie die Datei in `design/<yourdesign>/stylesheets/` Ihrer Design-Erweiterung ab.
2. Nennen Sie sie im Aufruf `ezcss_load( array( ... ) )` im Kopf Ihres Pagelayouts oder in einer `design.ini`-Liste, die Ihr Pagelayout
   liest.
3. Leeren Sie die Caches und laden Sie hart neu. Die Datei erreicht den Browser zusammen mit den anderen gepackt ([Der Packer](der-packer.md)).

## ezcss_load im Pagelayout

Die Erweiterung ezjscore stellt die Operatoren bereit (`extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`):

| Operator | Tut |
|---|---|
| `ezcss_load( files, media, type, rel, charset, pack_level )` | gibt `<link>`-Tags für die angegebenen Dateien aus **plus** jede bis dahin mit `ezcss_require` vorgemerkte Datei |
| `ezcss_require( files )` | merkt Dateien für das nächste `ezcss_load` vor; nachdem es lief, gibt es sie sofort aus |
| `ezcss( files, ... )` | gibt Tags für die angegebenen Dateien aus, sonst nichts |
| `ezcssfiles( files, pack_level, ignore_loaded )` | gibt die gepackten URLs statt Tags zurück |

Standard für `media` ist `all`, für `pack_level` 3. Pfade sind relativ zu `stylesheets/` und werden über die Design-Kaskade gefunden;
`'mytheme.css'` findet also `design/<any design in the chain>/stylesheets/mytheme.css`, und `'explayouts_standard/grid.css'` findet eine
Datei in einem Unterordner.

Der Kopf des Designs media (`pagelayout/head/style.tpl`):

```tpl
{ezcss_load( array( 'inter.css',
                    'explayouts_standard/grid.css',
                    'index.css',
                    'shop.css',
                    'exp-mobile-layout.css' ) )}
```

(gekürzt). Verwenden Sie im Pagelayout `ezcss_load`, nicht `ezcss`: Nur `_load` gibt aus, was andere Templates angefordert haben.

## Dateilisten in design.ini

`settings/design.ini [StylesheetSettings]` definiert Listen, die ein Pagelayout mit `ezini()` lesen kann:

| Einstellung | Gedacht für |
|---|---|
| `FrontendCSSFileList[]` | öffentliche Designs (eine Erweiterung fügt hier ihr Stylesheet hinzu) |
| `BackendCSSFileList[]` | Administrationsdesigns |
| `AdminComponentCSSFileList[]` | die gemeinsamen Komponenten und Modul-Stylesheets der Administration (`components.css`, `modules/setup.css` ...) |
| `CSSFileList[]` | die ältere Liste für beide, aus Kompatibilitätsgründen behalten (DEPRECATED zugunsten der beiden oben) |

Eine Liste zählt nur, wenn das Kopf-Template des Designs sie liest. Das `design/standard/templates/pagelayout.tpl` des Kernels und das
Design simple lesen `CSSFileList` und `FrontendCSSFileList`; das `page_head_style.tpl` von admin4 liest `AdminComponentCSSFileList` und
`BackendCSSFileList`; das Design media nennt seine Dateien selbst. Wenn andere Erweiterungen Ihrem Design Stylesheets hinzufügen können
sollen, lesen Sie die Listen:

```tpl
{ezcss_load( array( 'mytheme.css',
                    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
```

und eine Erweiterung fügt dann in ihrer eigenen `settings/design.ini.append.php` eine Zeile hinzu:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=myextension.css
```

## Ein Stylesheet nur für eine Art von Seite

Ein Vollansichts-Template, das zusätzliches CSS braucht, fordert es an; das `ezcss_load` des Pagelayouts gibt es aus:

```tpl
{* override/templates/full/event.tpl *}
{ezcss_require( array( 'event-calendar.css' ) )}
<article class="event">...</article>
```

Das funktioniert, weil die Ansicht vor dem Pagelayout läuft. Die Liste reist in der persistent variable und übersteht daher auch den
View-Cache. Der Besucher lädt die Datei nur auf Seiten, die sie brauchen; auf allen anderen Seiten kostet sie nichts.

## Reihenfolge und Spezifität

- Dateien kommen in der Reihenfolge des Arrays heraus; mit `ezcss_require` vorgemerkte Dateien kommen nach dem Array.
- Spätere Dateien gewinnen bei gleicher Spezifität. Setzen Sie Basisstile zuerst, dann Komponenten, dann seitenspezifische Dateien,
  dann Korrekturen.
- Doppelte werden verworfen: Eine zweimal angeforderte Datei wird einmal ausgegeben.

## Media und Druck

Der zweite Parameter ist das Attribut `media`:

```tpl
{ezcss_load( array( 'mytheme.css' ) )}
{ezcss( array( 'print.css' ), 'print' )}
```

Ein Druck-Stylesheet ist oft einfacher in der Hauptdatei, mit `@media print { ... }` ([Druck-Stylesheets](druck-stylesheets.md)).

## Was Sie nicht tun sollten

- **Keine `<style>`-Blöcke oder `style=""`** in Templates ([Kein Inline-CSS](kein-inline-css.md)).
- **Kein von Hand geschriebenes `<link>` auf `/extension/.../stylesheets/x.css`**: Es umgeht die Kaskade und den Packer.
- **Keine `?v=`-Abfragen**, um ein Neuladen zu erzwingen ([Keine Versionsabfragen an Asset-Links](keine-versionsparameter-an-asset-links.md)).
- **Keine CDN-Stylesheets** ohne Grund: Fremde Hosts verzögern das erste Zeichnen und müssen von der Content Security Policy der Website
  erlaubt werden ([Externe Ressourcen und CDNs](externe-ressourcen-und-cdns.md)).

## Verwandte Seiten

- [JavaScript laden](javascript-laden.md)
- [Die Operatoren ezcss und ezscript](die-operatoren-ezcss-und-ezscript.md)
- [Der Packer](der-packer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
