---
title: "Tutorial: ein Theme für rechts nach links"
audience: designer
category: theme-design
order: 820
summary: Schritt für Schritt ein Theme auf Arabisch oder Hebräisch funktionsfähig machen: ein Siteaccess von rechts nach links zum Testen, dir am html-Element, das Stylesheet auf logische Eigenschaften umstellen, Spiegelregeln, Richtungssymbole umdrehen, Skripte, Schriften und prüfen, dass Seiten von links nach rechts sich nicht geändert haben.
keywords: Tutorial, rechts nach links, RTL-Theme, Arabisch, Hebräisch, dir rtl, logische Eigenschaften, Symbole spiegeln, RTL testen, bidirektional, tutorial, right to left, rtl theme, Arabic, Hebrew, logical properties, mirror icons, rtl testing, bidirectional
type: help
---
# Tutorial: ein Theme für rechts nach links

Dieses Tutorial ist für Designer, die ein vorhandenes Theme in Sprachen von rechts nach links funktionsfähig machen. Planen Sie für ein
mittelgroßes Theme einen halben Tag ein, das meiste davon für Schritt 3.

## Inhalt

1. [Schritt 1: ein Ort zum Testen](#schritt-1-ein-ort-zum-testen)
2. [Schritt 2: die Richtung am html-Element](#schritt-2-die-richtung-am-html-element)
3. [Schritt 3: logische Eigenschaften](#schritt-3-logische-eigenschaften)
4. [Schritt 4: Spiegelregeln und Symbole](#schritt-4-spiegelregeln-und-symbole)
5. [Schritt 5: Skripte und Schriften](#schritt-5-skripte-und-schriften)
6. [Schritt 6: vergleichen](#schritt-6-vergleichen)

## Schritt 1: ein Ort zum Testen

Machen Sie, bevor Sie etwas ändern, Screenshots der wichtigsten Seitentypen von links nach rechts; Sie vergleichen später damit.

Für einen Test von rechts nach links brauchen Sie einen Siteaccess (oder eine Test-Oberflächensprache), dessen Locale von rechts nach links
geht: die Locale-Dateien mit `[LanguageSettings] TextDirection=rtl` in `share/locale/` (zum Beispiel `heb-IL`, `ara-SA`). Bitten Sie Ihren
Administrator um einen Test-Siteaccess mit `[RegionalSettings] Locale=heb-IL` und Ihrem Design.

## Schritt 2: die Richtung am html-Element

```tpl
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Öffnen Sie den Test-Siteaccess: Text beginnt jetzt rechts; alles, was mit Flex und Grid gebaut ist, hat sich von selbst umgedreht; was falsch
bleibt, ist das, was physisches links und rechts verwendet hat.

## Schritt 3: logische Eigenschaften

Gehen Sie die Stylesheets durch und ersetzen Sie physische Eigenschaften durch logische ([Logische CSS-Eigenschaften](logische-css-eigenschaften.md)):

```bash
grep -n -E "(margin|padding|border)-(left|right)|(^|[^-])(left|right):|text-align: *(left|right)|float: *(left|right)" \
     extension/mytheme/design/mytheme/stylesheets/*.css
```

`margin-left` wird zu `margin-inline-start`, `left: 0` zu `inset-inline-start: 0`, `text-align: left` zu `text-align: start`.

## Schritt 4: Spiegelregeln und Symbole

Für das, was keine logische Form hat (Hintergrundpositionen, asymmetrische Kurzschreibweisen, waagrechte Schatten, Transformationen), fügen
Sie direkt nach dem Original eine Spiegelregel hinzu:

```css
.breadcrumb li + li::before { content: "›"; margin-inline: 0.5em; }
:where([dir="rtl"]) .breadcrumb li + li::before { content: "‹"; }
```

Drehen Sie Symbole um, die entlang der Zeile zeigen, und nichts anderes:

```css
:where([dir="rtl"]) .icon-arrow-next,
:where([dir="rtl"]) .icon-chevron { scale: -1 1; }
```

## Schritt 5: Skripte und Schriften

- Slider, Menüs außerhalb des Bildschirms, Ziehgriffe: Lesen Sie die Richtung (`getComputedStyle(document.documentElement).direction`) und
  tauschen Sie links und rechts in Berechnungen.
- Schriften: Stellen Sie sicher, dass Ihre Schrift oder der Ersatzstapel Arabisch oder Hebräisch abdeckt; ein Systemstapel ist ein sicherer
  Anfang.
- Von Redakteuren eingegebene Namen in übersetzten Sätzen: Isolieren Sie sie mit `<bdi>` ([Designs für rechts nach links](rechts-nach-links-designs.md)).

## Schritt 6: vergleichen

- Rechts nach links: Kopfbereich, Menüs, Brotkrumenpfade, Blättern, Formulare, Tabellen, Slider, Symbole.
- Links nach rechts: Vergleichen Sie mit den Screenshots aus Schritt 1. Logische Eigenschaften ändern von links nach rechts nichts; weicht eine
  Seite ab, war eine Umstellung falsch.
- Gehen Sie die [Checkliste für rechts nach links](checkliste-rechts-nach-links.md) durch.

## Verwandte Seiten

- [Designs für rechts nach links](rechts-nach-links-designs.md)
- [Logische CSS-Eigenschaften](logische-css-eigenschaften.md)
- [Checkliste: rechts nach links](checkliste-rechts-nach-links.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
