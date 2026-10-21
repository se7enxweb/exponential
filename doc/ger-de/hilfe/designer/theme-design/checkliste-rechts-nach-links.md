---
title: "Checkliste: rechts nach links"
audience: designer
category: theme-design
order: 910
summary: Eine Checkliste für rechts nach links in einem Design: dir am html-Element, logische Eigenschaften, Spiegelregeln, welche Symbole sich umdrehen, Inhalte in der anderen Richtung, isolierte Namen, Skripte, die messen, Schriften und unveränderte Seiten von links nach rechts.
keywords: RTL-Checkliste, Checkliste rechts nach links, Arabisch, Hebräisch, Prüfung logischer Eigenschaften, gespiegelte Symbole, Bidi, RTL-Prüfung, rtl checklist, right to left checklist, Arabic, Hebrew, logical properties check, mirrored icons, bidi, rtl review
type: help
---
# Checkliste: rechts nach links

Diese Seite ist für Designer, die ein Design in Sprachen prüfen, die von rechts nach links geschrieben werden. Hintergrund:
[Designs für rechts nach links](rechts-nach-links-designs.md).

## Inhalt

1. [Rahmen](#rahmen)
2. [CSS](#css)
3. [Text und Inhalte](#text-und-inhalte)
4. [Verhalten](#verhalten)
5. [Keine Rückschritte](#keine-rückschritte)

## Rahmen

- [ ] `dir="rtl"` am `<html>` nur für Sprachen von rechts nach links (aus `$site.text_direction`).
- [ ] `lang` passt zur Sprache.
- [ ] Seitenspalten, Elemente des Kopfbereichs und der Skip-Link erscheinen auf der gespiegelten Seite.

## CSS

- [ ] Keine physischen Eigenschaften `left`/`right` mehr für das Layout ([Logische CSS-Eigenschaften](logische-css-eigenschaften.md)).
- [ ] Spiegelregeln (`:where([dir="rtl"])`) für Floats, Hintergrundpositionen, asymmetrische Kurzschreibweisen, Schatten,
  Transformationen.
- [ ] Nur Richtungssymbole umgedreht (Pfeile, Chevrons, Carets, Antworten, Abmelden); Uhren, Häkchen, Logos nicht.
- [ ] Von Redakteuren gewählte Ausrichtung (`text-left` und ähnliche) nicht gespiegelt.

## Text und Inhalte

- [ ] Inhalt in einer Sprache von links nach rechts innerhalb einer Seite von rechts nach links (und umgekehrt) trägt eigenes
  `lang` und `dir`.
- [ ] Von Redakteuren eingegebene Namen in übersetzten Sätzen mit `<bdi>` oder `dir="auto"` isoliert.
- [ ] Die Schrift oder ihr Ersatz deckt die Schrift der Sprache ab; die Zeilenhöhe passt dazu.
- [ ] Zahlen, Daten und Telefonnummern lesen sich korrekt.

## Verhalten

- [ ] Slider bewegen sich in Leserichtung; Knöpfe für zurück und weiter sitzen auf den richtigen Seiten.
- [ ] Menüs außerhalb des Bildschirms öffnen von der Anfangsseite.
- [ ] Ziehgriffe und Größenänderer funktionieren.
- [ ] Die Pfeiltasten links und rechts folgen in Widgets der sichtbaren Richtung.

## Keine Rückschritte

- [ ] Seiten von links nach rechts sind unverändert (vergleichen Sie Screenshots von vorher und nachher).

## Verwandte Seiten

- [Tutorial: ein Theme für rechts nach links](tutorial-ein-rechts-nach-links-theme.md)
- [Sprachen von rechts nach links in der Administration](../../../funktionen/6.0/rechts-nach-links.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
