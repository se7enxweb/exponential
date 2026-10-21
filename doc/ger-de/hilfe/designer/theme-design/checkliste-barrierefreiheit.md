---
title: "Checkliste: Barrierefreiheit"
audience: designer
category: theme-design
order: 870
summary: Eine Checkliste zur Barrierefreiheit eines Designs: Seitenstruktur, Tastatur, Fokus, Kontrast, Bilder, Formulare, Bewegung, Zoom und Umbruch, dunkler Modus und erzwungene Farben sowie die automatische Prüfung ohne ernste Probleme.
keywords: Checkliste Barrierefreiheit, a11y-Checkliste, WCAG-Checkliste, Tastaturtest, Kontrastprüfung, axe, Screenreader-Test, Prüfung der Barrierefreiheit eines Designs, accessibility checklist, a11y checklist, WCAG checklist, keyboard test, contrast check, screen reader test, design accessibility review
type: help
---
# Checkliste: Barrierefreiheit

Diese Seite ist für Designer, die ein Design auf Barrierefreiheit prüfen. Gehen Sie sie für jeden Seitentyp durch: Startseite,
Artikel, Liste, Suchergebnisse, ein Formular, eine Fehlerseite, eine Layout-Seite.

## Inhalt

1. [Struktur](#struktur)
2. [Tastatur und Fokus](#tastatur-und-fokus)
3. [Optik](#optik)
4. [Inhalte, die das Design ausgibt](#inhalte-die-das-design-ausgibt)
5. [Formulare](#formulare)
6. [Automatische Prüfung](#automatische-prüfung)

## Struktur

- [ ] `lang` am `<html>`, `dir="rtl"` für Sprachen, die von rechts nach links geschrieben werden.
- [ ] Landmarks: Kopfbereich, Navigation (jede beschriftet), Hauptinhalt, Fußbereich ([Landmarks und Überschriften](../../../../eng-us/help/designers/theme-design/landmarks-and-headings.md)).
- [ ] Ein `h1`; Überschriften ohne übersprungene Ebenen.
- [ ] Ein Skip-Link als erstes fokussierbares Element, funktionierend ([Skip-Links](../../../../eng-us/help/designers/theme-design/skip-links.md)).
- [ ] Der Seitentitel nennt die Seite, dann die Website.

## Tastatur und Fokus

- [ ] Jeder Link, jeder Knopf und jedes Feld mit Tab erreichbar, in sinnvoller Reihenfolge ([Tastatur und Fokus](tastatur-und-fokus.md)).
- [ ] Fokus immer sichtbar, mit einem Kontrast von 3:1.
- [ ] Menüs, Dialoge, Slider funktionieren mit der Tastatur; Escape schließt; der Fokus kehrt zurück.
- [ ] Keine Tastaturfalle.

## Optik

- [ ] Textkontrast mindestens 4,5:1 (3:1 für großen Text), hell und dunkel ([Farbkontrast](farbkontrast.md)).
- [ ] Informationen nicht allein über Farbe (Fehler, Pflichtfelder, Links im Text).
- [ ] 200 % Zoom und 320 px Breite: nichts geht verloren, kein waagrechtes Scrollen der Seite ([Testen bei Zoomstufen](../../../../eng-us/help/designers/theme-design/testing-at-zoom-levels.md)).
- [ ] Bewegung beachtet `prefers-reduced-motion`; kein automatisches Abspielen ohne Pause ([Reduzierte Bewegung](../../../../eng-us/help/designers/theme-design/reduced-motion.md)).
- [ ] Erzwungene Farben (Windows-Kontrastmodus): Rahmen, Fokus und Symbole bleiben sichtbar.

## Inhalte, die das Design ausgibt

- [ ] Bilder: Alternativtext aus dem Bildfeld, `alt=""` für Dekoration.
- [ ] Knöpfe, die nur ein Symbol zeigen, haben einen Namen.
- [ ] Linktexte ergeben auch ohne Zusammenhang Sinn („Mehr über X lesen“, nicht „hier“).
- [ ] Eingebettete Videos haben Untertitel (eine Aufgabe der Redakteure; der Player muss sie zeigen).

## Formulare

- [ ] Jedes Feld hat eine sichtbare, damit verknüpfte Beschriftung ([Barrierefreie Formulare](barrierefreie-formulare.md)).
- [ ] Pflichtfelder im Text gekennzeichnet, nicht nur über Farbe.
- [ ] Fehler in einer Zusammenfassung und neben dem Feld aufgeführt; der Fokus wechselt zur Zusammenfassung.
- [ ] `autocomplete` an Feldern für persönliche Daten.

## Automatische Prüfung

- [ ] axe (Browsererweiterung oder ein Testskript) findet auf jedem Seitentyp **keine ernsten oder kritischen Probleme**, hell
  und dunkel.
- [ ] Keine Fehler in der Konsole des Browsers.

Automatische Prüfungen finden nur einen Teil der Probleme; die Tastatur- und Zoomprüfungen oben sind nicht optional.

## Verwandte Seiten

- [Barrierefreiheit: Grundlagen für Themes](barrierefreiheit-grundlagen-fuer-themes.md)
- [Checkliste: Start eines Themes](checkliste-theme-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
