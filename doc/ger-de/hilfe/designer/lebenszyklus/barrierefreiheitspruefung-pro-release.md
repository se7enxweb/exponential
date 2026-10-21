---
title: "Eine Prüfung der Barrierefreiheit bei jedem Release"
audience: designer
category: theme-lifecycle
order: 140
summary: Eine wiederholbare Prüfung der Barrierefreiheit vor jedem Theme-Release - axe auf der Seitenliste bei drei Breiten in hell und dunkel, ein Durchgang mit der Tastatur, Zoom, Kontrast - Ergebnisse festhalten und ernste Probleme zuerst beheben, wie es die Themes von 7x für WCAG 2.2 AA getan haben.
keywords: Prüfung der Barrierefreiheit, a11y, axe, WCAG 2.2 AA, Kontrast, Tastatur, Zoom, Release-Routine, ernste Probleme, accessibility audit, contrast, keyboard, release routine, serious issues
type: help
---
# Eine Prüfung der Barrierefreiheit bei jedem Release

Diese Seite ist für Designer, die ein Theme betreuen. Barrierefreiheit verschlechtert sich leise: eine neue Farbe, ein
neuer Icon-Link ohne Namen. Eine kurze Prüfung pro Release fängt das ab. Wie Sie barrierefrei gestalten, erklären
[Grundlagen der Barrierefreiheit für Themes](../theme-design/barrierefreiheit-grundlagen-fuer-themes.md) und
[Checkliste: Barrierefreiheit](../theme-design/checkliste-barrierefreiheit.md).

## Die Routine

1. **Automatische Prüfung** mit axe (Browsererweiterung oder ein Headless-Lauf) auf jeder Seite Ihrer Seitenliste, bei
   1440 px, 960 px mit 200 % Zoom und 375 px, im hellen und dunklen Modus. Ziel: keine ernsten oder kritischen Probleme.
2. **Durchgang mit der Tastatur**: Gehen Sie mit der Tabulatortaste durch die Startseite, einen Artikel und ein
   Formular. Der Fokus ist immer sichtbar; nichts hält ihn fest.
3. **Zoom**: Bei 200 % fließt der Inhalt ohne waagerechtes Scrollen um ([Testen bei Zoomstufen](../theme-design/testen-bei-zoomstufen.md)).
4. **Kontrast** neuer Farben: Text mindestens 4,5:1 ([Farbkontrast](../theme-design/farbkontrast.md)).
5. **Namen**: Jeder Link und jede Schaltfläche hat einen zugänglichen Namen, jedes Bild einen passenden Alternativtext.

## Festhalten und beheben

| Festhalten | Beispiel |
|---|---|
| Datum, Theme-Version, Werkzeug und Version | 2026-10-17, 0.1.5, axe |
| Geprüfte Seiten und Breiten | 12 Seiten, 3 Breiten, hell und dunkel |
| Befunde nach Schweregrad | 0 ernste, 2 mittlere |
| Was behoben wurde | Die Links in der Fußzeile sind 24 px hoch |

Das Changelog von `cjw_themes_jumper` 0.1.5 ist ein Beispiel dafür, wie man das Ergebnis meldet: Es nennt die bestandenen
Prüfungen (Kontrast, Zielgröße und Linknamen, axe, WCAG 2.2 AA) und was geändert wurde, um sie zu bestehen.

## Verwandte Seiten

- [Visuelle Regressionsprüfungen](visuelle-regressionspruefungen.md)
- [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
