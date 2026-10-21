---
title: Websites mit Schreibrichtung von rechts nach links
audience: website-creator, admin
category: website-creation
order: 270
summary: Eine öffentliche Website auf Arabisch, Hebräisch, Persisch oder in einer anderen Sprache mit Schreibrichtung von rechts nach links bauen - woher die Richtung kommt, dir auf der Seite und an Inhalten setzen, logische CSS-Eigenschaften und wie Sie testen.
keywords: von rechts nach links, Schreibrichtung, Textrichtung, Arabisch, Hebräisch, Persisch, Urdu, logische Eigenschaften, Website-Ersteller, right to left, RTL, Arabic, Hebrew, Persian, dir, text direction, TextDirection, text_direction, is_right_to_left, logical properties, bidi, website creator
type: help
---
# Websites mit Schreibrichtung von rechts nach links

Diese Seite richtet sich an Website-Ersteller, die eine öffentliche Website oder eine Sprache davon in einer Schrift
mit Schreibrichtung von rechts nach links bauen. Die Administration dreht sich bei Oberflächensprachen von rechts nach
links von selbst um ([Sprachen von rechts nach links in der Administration](../../../funktionen/6.0/rechts-nach-links.md));
ein öffentliches Design tut, was seine Templates und Stylesheets vorgeben, deshalb zeigt diese Seite, was Sie
ergänzen müssen.

## Inhalt

1. [Woher die Richtung kommt](#woher-die-richtung-kommt)
2. [Die Seite](#die-seite)
3. [Inhalte in einer Sprache von rechts nach links](#inhalte-in-einer-sprache-von-rechts-nach-links)
4. [Stylesheets](#stylesheets)
5. [Testen](#testen)

## Woher die Richtung kommt

Die einzige Quelle ist die Locale-Datei: `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`. 28
Locale-Dateien tragen diesen Eintrag; die Liste steht unter [Sprachen in Exponential](../../../sprachen.md). Der Kernel
stellt ihn bereit:

| Wo | Was |
|---|---|
| `$site.text_direction` | `rtl` oder `ltr` für die aktuelle Oberflächen-Locale des Siteaccess |
| `$code\|locale().is_right_to_left` | für einen beliebigen Locale-Code in einem Template |
| `eZLocale::textDirectionOf( 'heb-IL' )` | dasselbe in PHP |

## Die Seite

Geben Sie die Richtung in Ihrem Pagelayout am Element `<html>` aus, so wie es die Pagelayouts der Administration tun:

```text
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Die Einstellung `Locale` des Siteaccess bestimmt `$site.text_direction`, ein arabischer Siteaccess mit `Locale=ara-SA`
erhält also `dir="rtl"`, ein englischer nichts.

## Inhalte in einer Sprache von rechts nach links

Eine Seite kann Inhalte in einer anderen Richtung als die Oberfläche zeigen, zum Beispiel ein arabisches Zitat auf einer
englischen Seite. `design/standard/templates/parts/exp_content_direction.tpl` gibt für eine Inhalts-Locale von rechts
nach links ` lang="..." dir="rtl"` aus und für eine von links nach rechts nichts; binden Sie es an dem Element ein, das
den Inhalt umschließt. Die Online-Editoren wechseln bei Texten von rechts nach links ihre Richtung, sodass die Redaktion
in der richtigen Richtung schreibt.

## Stylesheets

| Statt | Schreiben Sie |
|---|---|
| `margin-left`, `padding-right` | `margin-inline-start`, `padding-inline-end` |
| `left: 0`, `right: 0` | `inset-inline-start: 0`, `inset-inline-end: 0` |
| `text-align: left` | `text-align: start` |
| `float: left` | Flex- oder Grid-Layouts, die der Richtung folgen |
| Pfeilsymbole, die "vorwärts" zeigen | spiegeln Sie sie mit `[dir="rtl"] .icon-forward { transform: scaleX(-1); }` |

Mit logischen Eigenschaften dient ein Stylesheet beiden Richtungen; es gibt keine `-rtl.css`-Kopie zu pflegen.

## Testen

- [ ] Öffnen Sie den Siteaccess mit Schreibrichtung von rechts nach links: `<html dir="rtl">` ist vorhanden, Spalten und
  Menüs sind gespiegelt.
- [ ] Gemischter Text (Zahlen, lateinische Wörter in arabischem Text) liest sich richtig.
- [ ] Formulare: Beschriftungen, Eingabefelder und Fehlermeldungen richten sich an der Anfangsseite aus.
- [ ] Symbole, die entlang der Zeile zeigen, zeigen in die richtige Richtung.
- [ ] Die Websites mit Schreibrichtung von links nach rechts sehen genau wie vorher aus.
- [ ] Prüfen Sie auch bei geringer Breite; Menüs, die zusammenklappen, gehen oft als Erstes kaputt.

## Verwandte Seiten

- [Eine mehrsprachige Website bauen](mehrsprachige-websites.md)
- [Barrierefreiheit für Website-Ersteller](barrierefreiheit-fuer-website-ersteller.md)
- [Stylesheets und Skripte](stylesheets-und-skripte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
