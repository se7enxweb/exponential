---
title: Stylesheets und Skripte
audience: website-creator, developer
category: website-creation
order: 140
summary: Fügen Sie Ihr CSS und JavaScript auf die Art von Exponential hinzu - Listen in design.ini, ezcss_load und ezscript_load, der Packer von ezjscore, Cache-Busting und eine Website, die für eine Content Security Policy bereit bleibt.
keywords: Stylesheet, CSS, JavaScript, Skripte, Packer, Cache, Website-Ersteller, stylesheet, scripts, design.ini, CSSFileList, JavaScriptList, ezcss_load, ezscript_load, ezjscore, packer, jQuery, cache, CSP, website creator
type: help
---
# Stylesheets und Skripte

Diese Seite richtet sich an Website-Ersteller, die einem Design ihr eigenes CSS und JavaScript hinzufügen. In
Exponential werden diese Dateien nicht von Hand im Pagelayout verlinkt: Sie werden in `design.ini` aufgeführt und vom
Packer von `ezjscore` geladen, der sie zusammenfasst, minimiert und jeder Version einen eigenen Dateinamen gibt.

## Inhalt

1. [Die Listen in design.ini](#die-listen-in-designini)
2. [Sie im Pagelayout laden](#sie-im-pagelayout-laden)
3. [Der Packer](#der-packer)
4. [Nach einer Änderung](#nach-einer-änderung)
5. [Skripte, die unter einer Content Security Policy funktionieren](#skripte-die-unter-einer-content-security-policy-funktionieren)
6. [Checkliste](#checkliste)

## Die Listen in design.ini

In Ihrer Design-Erweiterung, `settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=mysite_design

[StylesheetSettings]
CSSFileList[]=main.css
CSSFileList[]=print.css

[JavaScriptSettings]
JavaScriptList[]=ezjsc::jquery
JavaScriptList[]=main.js
*/ ?>
```

| Einstellung | Enthält |
|---|---|
| `[StylesheetSettings] CSSFileList[]` | Stylesheets, gefunden in `stylesheets/` der Design-Kette |
| `[StylesheetSettings] FrontendCSSFileList[]` | weitere Stylesheets für die öffentliche Website |
| `[JavaScriptSettings] JavaScriptList[]` | Skripte, gefunden in `javascript/` der Design-Kette |
| `[JavaScriptSettings] FrontendJavaScriptList[]` | weitere Skripte für die öffentliche Website |

`ezjsc::jquery` ist das eigene jQuery von ezjscore, Sie liefern also keine Kopie mit. Das mitgelieferte Theme simple ist
ein echtes Beispiel: `extension/sevenx_themes_simple/settings/design.ini.append.php`.

## Sie im Pagelayout laden

```text
{ezcss_load( array( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
                    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
...
{ezscript_load( array( ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                       ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
```

Ein Template, das für sich selbst eine weitere Datei braucht, fordert sie mit `{ezcss_require( 'gallery.css' )}` oder
`{ezscript_require( 'gallery.js' )}` an; `ezcss_load`/`ezscript_load` im Pagelayout bindet sie dann ein. Die vier
Funktionen sind in `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` definiert.

## Der Packer

Der Packer wird in `extension/ezjscore/settings/ezjscore.ini` eingestellt:

| Einstellung | Bedeutung |
|---|---|
| `[eZJSCore] Packer` | folgt normalerweise `[TemplateSettings] DevelopmentMode`; setzen Sie `disabled` oder eine Stufe von `0` bis `3`, um ihn zu erzwingen |
| `[Packer] AppendLastModifiedTime=enabled` | die Änderungszeit der Datei ist Teil des Namens der gepackten Datei, sodass Browser nach einer Änderung eine neue Kopie holen |
| `[Packer] CustomHosts[.css]`, `[.js]` | gepackte Dateien von einem anderen Host ausliefern |
| `JavaScriptOptimizer[]`, `CssOptimizer[]` | die Minimierer |

Mit eingeschaltetem Packer verlinkt die Seite eine zusammengefasste Datei je Liste; ausgeschaltet (Entwicklung) jede
Datei einzeln, was die Fehlersuche erleichtert.

## Nach einer Änderung

Weil gepackte Dateien und die Cache-Blöcke des Pagelayouts sich die alten Dateinamen merken, braucht eine Änderung an
einem Stylesheet:

```bash
php bin/php/ezcache.php --clear-id=ezjscore-packer --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=template-block --allow-root-user
```

Laden Sie die Seite dann vollständig neu (Hard Reload). Hängen Sie kein `?v=...` an Links an; die Änderungszeit im
Dateinamen erledigt das bereits.

## Skripte, die unter einer Content Security Policy funktionieren

Die mitgelieferte Policy erlaubt, wenn Sie sie für eine Website einschalten, nur Skript- und Stylesheet-Dateien der
Website selbst (`script-src 'self'`, `style-src 'self'`): kein Inline-Code in `<script>`, keine Attribute `on...=`,
kein `style=""` ([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)). Schreiben Sie die Website
von Anfang an so:

| Statt | Schreiben Sie |
|---|---|
| `<a onclick="openGallery()">` | `<a class="js-gallery">` und `addEventListener` in `main.js` |
| `<script>var root = "...";</script>` | einen Datenblock `<script type="application/json">`, den `main.js` liest ([Das Pagelayout](das-pagelayout.md#skripte-ohne-inline-code)) |
| `<div style="width: 40%">` | eine CSS-Klasse oder ein `data-*`-Attribut, das Ihr Skript liest |
| ein Skript von einem anderen Host | eine Kopie in Ihrem Design oder `AdditionalSources[]=script-src https://<host>` in `[ContentSecurityPolicy]` |

## Checkliste

- [ ] Jede CSS- und JS-Datei ist in `design.ini` aufgeführt, keine ist von Hand verlinkt.
- [ ] Keine Inline-Skripte, keine Attribute `on...` und kein `style=""` in Ihren Templates.
- [ ] Der Packer ist für den Produktivbetrieb eingeschaltet (`DevelopmentMode` aus).
- [ ] Nach einer Änderung: Packer-, Template- und Template-Block-Caches geleert, Hard Reload.
- [ ] Skripte und Schriften von Drittanbietern sind auf Datenschutz geprüft und in der Policy erlaubt.

## Verwandte Seiten

- [Das Pagelayout](das-pagelayout.md)
- [ezjscore](../../../../eng-us/features/6.0/extensions/ezjscore.md)
- [jQuery 4 und die Entfernung von YUI](../../../../eng-us/features/6.0/jquery4-and-yui-removal.md)
- [Content Security Policy für Anwendungen](../anwendungsentwickler/content-security-policy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
