---
title: Barrierefreiheit testen
audience: website-creator, developer
category: website-creation
order: 610
summary: Eine praktische Testroutine für die Barrierefreiheit einer Exponential-Website - welche Seiten Sie testen, automatische Prüfungen, Durchgänge mit Tastatur und Screenreader, Zoom und Farbe, Befunde festhalten und nach dem Start weiter testen.
keywords: Barrierefreiheit testen, Barrierefreiheitstest, automatische Prüfung, Tastaturtest, Screenreader, Zoom, Kontrast, Regression, Website-Ersteller, accessibility testing, a11y test, axe, automatic checker, keyboard test, screen reader, zoom, contrast, WCAG, regression, website creator
type: help
---
# Barrierefreiheit testen

Diese Seite richtet sich an Website-Ersteller, die eine Website vor dem Start und nach Änderungen prüfen. Automatische
Werkzeuge finden einen Teil der Probleme; der Rest braucht einen Menschen mit Tastatur und Screenreader. Diese Routine
dauert für eine typische Website ein paar Stunden und findet die meisten Probleme.

## Inhalt

1. [Die Seiten auswählen](#die-seiten-auswählen)
2. [Automatische Prüfungen](#automatische-prüfungen)
3. [Durchgang mit der Tastatur](#durchgang-mit-der-tastatur)
4. [Durchgang mit dem Screenreader](#durchgang-mit-dem-screenreader)
5. [Zoom, Farbe und Bewegung](#zoom-farbe-und-bewegung)
6. [Befunde festhalten](#befunde-festhalten)
7. [Nach dem Start](#nach-dem-start)

## Die Seiten auswählen

Testen Sie eine Seite je Template, nicht jede Seite:

- die Startseite und jedes Layout einer Landingpage;
- eine Vollansicht jeder Klasse (Artikel, Veranstaltung, Produkt ...);
- eine Ordnerliste mit Seitenaufteilung;
- Suchergebnisse, mit und ohne Treffer;
- jedes Formular: Kontakt, Registrierung, Anmeldung, Newsletter, Kasse;
- Fehlerseiten (nicht gefunden, Zugriff verweigert);
- jeden Sprach-Siteaccess, einschließlich derer mit Schreibrichtung von rechts nach links.

## Automatische Prüfungen

Lassen Sie auf jeder Seite ein automatisches Prüfwerkzeug für Barrierefreiheit laufen (zum Beispiel eine
Browsererweiterung oder ein Kommandozeilenwerkzeug auf Basis von axe). Beheben Sie jeden gemeldeten Fehler; lesen Sie
die Warnungen. Lassen Sie es auch mit Ihren Farbschemata und bei Smartphone-Breite laufen. Automatische Werkzeuge finden
fehlende Alternativtexte, Beschriftungen, Kontrastprobleme und falsches ARIA, nicht aber, ob die Reihenfolge sinnvoll
ist.

## Durchgang mit der Tastatur

Legen Sie die Maus weg, und verwenden Sie Tab, Umschalt+Tab, Eingabe, Leertaste, Escape und die Pfeiltasten:

- [ ] Der Sprunglink erscheint beim ersten Tab und funktioniert.
- [ ] Jeder Link und jedes Bedienelement ist in einer sinnvollen Reihenfolge erreichbar.
- [ ] Der Fokus ist immer sichtbar.
- [ ] Menüs öffnen und schließen sich mit der Tastatur; Escape schließt Pop-ups und gibt den Fokus zurück.
- [ ] Nichts hält den Fokus gefangen (eingebettete Karten, Videoplayer, CAPTCHAs).

## Durchgang mit dem Screenreader

Mit einem Screenreader, den Ihr Betriebssystem anbietet:

- [ ] Der Seitentitel sagt, wo Sie sind.
- [ ] Landmarks und Überschriften ergeben eine brauchbare Gliederung.
- [ ] Bilder werden wie beabsichtigt beschrieben oder übersprungen.
- [ ] Formularfelder kündigen ihre Beschriftung, den Pflichtstatus und Fehler an.
- [ ] Sprachwechsel werden in der richtigen Sprache vorgelesen.

## Zoom, Farbe und Bewegung

- [ ] 200 % Zoom und 320 CSS-Pixel Breite: kein horizontales Scrollen, nichts abgeschnitten.
- [ ] Kontrast mindestens 4,5:1 für Text, 3:1 für großen Text und Bedienelemente.
- [ ] Informationen werden nicht allein durch Farbe vermittelt.
- [ ] Wenn reduzierte Bewegung angefordert ist, halten Slider und Animationen an.

## Befunde festhalten

| Seite | Problem | Erfolgskriterium | Schweregrad | Behebung | Verantwortlich |
|---|---|---|---|---|---|
| `<page>` | `<what happens>` | `<WCAG reference>` | `<blocker/major/minor>` | `<template or content>` | `<who>` |

Teilen Sie die Behebungen in Template-Arbeit (einmal, für jede Seite) und Inhaltsarbeit (Redaktion, Seite für Seite)
auf.

## Nach dem Start

- Lassen Sie die automatischen Prüfungen nach jeder Designänderung erneut laufen, als Teil Ihrer Release-Prüfungen
  ([Anwendungen testen](../anwendungsentwickler/anwendungen-testen.md)).
- Bringen Sie der Redaktion die Regeln für Inhalte bei
  ([Barrierefreiheit für Website-Ersteller](barrierefreiheit-fuer-website-ersteller.md#was-die-redaktion-steuert)).
- Geben Sie Besuchern eine Möglichkeit, Barrieren zu melden (ein Kontaktformular), und beantworten Sie die Meldungen.

## Verwandte Seiten

- [Barrierefreiheit für Website-Ersteller](barrierefreiheit-fuer-website-ersteller.md)
- [Checkliste für den Start](checkliste-fuer-den-start.md)
- [Qualitätstore und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
