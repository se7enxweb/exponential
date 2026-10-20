---
title: Von Inline-CSS zu Stylesheets
audience: developer, designer
category: extension-development
order: 160
summary: Verschieben Sie die style-Blöcke und style-Attribute einer alten Erweiterung in Stylesheet-Dateien, die über design.ini geladen werden, damit der Packer sie bündelt und eine Content Security Policy mit style-src self sie nicht verwirft.
keywords: Inline-CSS, style-Attribut, style-Block, Stylesheet, Packer, Upgrade, inline CSS, style attribute, style block, stylesheet, BackendCSSFileList, FrontendCSSFileList, CSSFileList, packer, CSP, style-src, upgrade
type: help
---
# Von Inline-CSS zu Stylesheets

Teil 4, Kapitel 16. Alte Templates sind voller `style="..."`-Attribute und `<style>`-Blöcke. Eine Content Security
Policy mit `style-src 'self'` verwirft beide: Die Seite funktioniert noch, sieht aber falsch aus. Sie sind außerdem
schwer zu pflegen, weil dieselbe Regel in viele Templates kopiert wird, und sie können dem dunklen Modus der
Administration nicht folgen. Dieses Kapitel verschiebt sie in Stylesheet-Dateien.

## Sie finden

```bash
cd extension/myoldext
grep -rnoE 'style\s*=\s*"[^"]*"' --include='*.tpl' design/ | sed 's/.*style=//' | sort | uniq -c | sort -rn | head -20
grep -rln '<style' --include='*.tpl' design/
```

Der erste Befehl zählt jeden unterschiedlichen `style`-Wert. Meist machen wenige Werte den größten Teil aus, und jeder
davon wird zu einer Klasse.

## Ein echtes Beispiel

se7enxweb/AdminAid, Commit e5a665c, „The class view carries no inline CSS; its flag uses the shared a4-flag class“:

```text
before:
<img src="{$language_code|flag_icon}" alt="{$language_code}" style="vertical-align: middle;" />

after:
<img src="{$language_code|flag_icon}" alt="{$language_code}" class="a4-flag" />
```

`a4-flag` ist eine Klasse der gemeinsamen Komponenten der Administration (`components.css`), daher brauchte die
Erweiterung kein eigenes CSS. Sehen Sie immer zuerst dort nach: Auf einer Administrationsseite gibt es für die meisten
Inline-Styles alter Erweiterungen bereits eine gemeinsame Klasse. Siehe
[admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md).

## Die Schritte für Ihre eigenen Styles

### 1. Das Stylesheet anlegen

Legen Sie es im Design der Erweiterung unter `stylesheets/` ab, mit einem Namen, der nicht mit dem einer anderen
Erweiterung kollidieren kann:

```text
extension/myoldext/design/standard/stylesheets/myoldext.css
```

Verwenden Sie `design/standard` für Styles, die in jedem Design gebraucht werden, und `design/admin` (oder das
Administrationsdesign, auf das Sie abzielen) für Styles, die nur die Administration betreffen.

### 2. Jeden Inline-Wert in eine Klasse verwandeln

```text
before:
<td style="width: 30%; white-space: nowrap;">...</td>
<div style="display:none" id="myoldext-advanced">...</div>

after:
<td class="myoldext-label">...</td>
<div id="myoldext-advanced" hidden>...</div>
```

```css
.myoldext-label { width: 30%; white-space: nowrap; }
```

Zwei Tipps aus diesem Beispiel:

- **Aus `display: none` wird `hidden`.** Das Attribut `hidden` ist kein CSS, die Policy rührt es also nicht an, und
  Skripte schalten es mit `element.hidden = false` um.
- **Stellen Sie jeder Klasse den Namen der Erweiterung voran** (`myoldext-`). Kurze Namen wie `.label` oder `.box`
  kollidieren mit den Regeln des Kernels und anderer Erweiterungen.

### 3. Werte, die eigentlich Daten sind

Manche Inline-Styles tragen einen Wert aus dem Inhalt: eine Breite in Prozent, eine von einem Redakteur gewählte
Farbe, einen Fortschrittsbalken. Diese können keine feste Klasse sein. Möglichkeiten:

| Wert | Lösung |
|---|---|
| Einige bekannte Werte (links, zentriert, rechts) | eine Klasse pro Wert: `myoldext-align-{$align\|wash}` |
| Ein Prozentwert in Stufen | Klassen pro Stufe (`myoldext-w-10` ... `myoldext-w-100`), gerundet |
| Alles andere | ein `data-*`-Attribut und ein paar Zeilen in Ihrer Skriptdatei, die den Style daraus setzen (Skripte dürfen unter der Policy `element.style` setzen; blockiert wird nur das Attribut im Markup) |
| Ein echtes `<progress>` oder `<meter>` | das HTML-Element mit seinem Attribut `value`, kein CSS nötig |

### 4. Das Stylesheet laden

Registrieren Sie es in `settings/design.ini.append.php` der Erweiterung:

```ini
<?php /*

[StylesheetSettings]
# frontend designs
FrontendCSSFileList[]=myoldext.css
# administration designs
BackendCSSFileList[]=myoldext.css

*/ ?>
```

`CSSFileList[]`, das alte Erweiterungen verwenden, funktioniert weiterhin, ist aber in der `design.ini` des Kernels
zugunsten der beiden obigen Listen als veraltet markiert. Dateien in diesen Listen werden vom Packer von ezjscore
zusammen mit den übrigen Styles der Seite gebündelt und kosten daher keine zusätzliche Anfrage.

> **Hinweis**
> `AdminComponentCSSFileList[]` im selben Abschnitt ist die Liste des Kernels für seine eigenen
> Komponenten-Stylesheets der Administration. Der Kommentar in `design.ini` sagt es deutlich: „An extension adds its
> file to BackendCSSFileList, not here.“

### 5. Die Caches leeren und nachsehen

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-id=template,template-block
```

Laden Sie die Seite dann im Browser neu und umgehen Sie dabei seinen Cache (ein harter Reload). Der Link zum
gepackten Stylesheet ist in manchen Designs Teil der gecachten Seitenausgabe, deshalb wird auch der
Template-Block-Cache geleert.

### 6. `<style>`-Blöcke

Ein `<style>`-Block in einem Template wandert in dieselbe Datei, wobei seine Selektoren das Präfix erhalten, falls sie
es noch nicht haben. Wenn der Block nur auf einer Seite ausgegeben wurde, laden seine Regeln jetzt auf jeder Seite:
Für ein paar Zeilen ist das in Ordnung, und es ist der Grund, die Selektoren spezifisch zu halten.

## Ihre Arbeit prüfen

- Die Suchen oben auf dieser Seite finden nichts (oder nur Werte, die Sie bewusst behalten haben, mit einem
  Kommentar).
- Die Seite sieht im Browser genauso aus wie vorher, bei Administrationsseiten im hellen und im dunklen Modus.
- Die Browser-Konsole zeigt keine Zeilen „Refused to apply inline style“.

## Verwandte Seiten

- Zurück: [CSP: javascript:-Links, eval und String-Timer](csp-javascript-links-und-eval.md)
- Weiter: [Von YUI zu expui](yui-zu-expui.md)
- [admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md)
- [Die Administration gestalten](../../../bc/6.0/admin4-gestaltung.md)
- [CSS und JavaScript in einer Erweiterung](../teil-3-erweiterungsentwickler/css-und-javascript-in-einer-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
