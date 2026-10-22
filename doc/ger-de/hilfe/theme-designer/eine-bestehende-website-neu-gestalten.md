---
title: "Eine bestehende Website neu gestalten"
audience: theme-designer
category: theme-design
order: 41
summary: Wie Theme-Designer eine laufende Exponential-Website neu gestalten - das aktuelle Design und seine Overrides prüfen, entscheiden, was bleibt, Tokens in ein altes Theme einführen, das neue Design neben dem alten auf einem Vorschau-Siteaccess bauen und ohne Überraschungen umschalten.
keywords: Theme-Designer, Neugestaltung, Redesign, Auffrischung, Bestandsprüfung, bestehende Website, Overrides, altes Theme, Tokens, Vorschau-Siteaccess, Migration, Umschalten, schrittweise, theme designer, redesign, refresh, audit, existing site, legacy theme, preview siteaccess, switch over, incremental
type: help
---
# Eine bestehende Website neu gestalten

Diese Seite ist für Theme-Designer, die eine Website neu gestalten sollen, die bereits mit Exponential läuft. Eine
Neugestaltung ist in einer Hinsicht leichter als eine neue Website (die Inhalte existieren, Sie können also vom ersten
Tag an mit ihnen gestalten) und in einer anderen schwerer (Jahre an Overrides, Sonderfällen und Gewohnheiten der
Redakteure existieren auch). Diese Seite ist ein Plan für die Designseite; das Upgrade der Software selbst ist ein
eigenes Projekt für den Entwickler.

## Inhalt

1. [Prüfen, bevor Sie zeichnen](#prüfen-bevor-sie-zeichnen)
2. [Entscheiden, was bleibt](#entscheiden-was-bleibt)
3. [Neues Design oder aufgefrischtes Design](#neues-design-oder-aufgefrischtes-design)
4. [Tokens in ein altes Theme einführen](#tokens-in-ein-altes-theme-einführen)
5. [Neben dem Live-Design bauen](#neben-dem-live-design-bauen)
6. [Redakteure und die Neugestaltung](#redakteure-und-die-neugestaltung)
7. [Umschalten](#umschalten)

## Prüfen, bevor Sie zeichnen

Sammeln Sie mit dem Entwickler:

1. **Die Design-Kaskade** jedes Siteaccess: welche Designs, in welcher Reihenfolge ([Designs und Siteaccesses](../designer/theme-design/designs-und-siteaccesses.md)).
2. **Die Overrides**: jede Regel in der `override.ini` des Designs; sie sagt Ihnen, welche Klasse, welcher Knoten und
   welche Sektion ein eigenes Template hat. Der Template-Editor der Administration listet sie pro Template auf
   ([Der Template-Editor](../designer/vorlagen/der-template-editor.md)).
3. **Die Inhalte**: Klassen, Felder und wie sie wirklich verwendet werden ([Ihr erster Tag als Theme-Designer](ihr-erster-tag-als-theme-designer.md)).
4. **Statistiken, wenn der Kunde sie teilt**: welche Seiten besucht werden und welche nie.
5. **Probleme**: Barrierefreiheit, Dunkelmodus, Performance, kleine Bildschirme, bekannte Beschwerden.

Der Vergleich der Overrides der Website mit den aktuellen Standard-Templates zeigt, wo das alte Design abgedriftet ist
([Ihre Overrides mit den Standard-Templates vergleichen](../designer/lebenszyklus/overrides-mit-dem-standard-vergleichen.md)).

## Entscheiden, was bleibt

Entscheiden Sie für jeden Override und jede besondere Seite: unverändert behalten, neu gestalten oder entfernen.
Overrides für Seiten, die niemand besucht, sind Kosten ohne Nutzen. Was sich zu behalten lohnt, ist oft unsichtbar: URLs,
die Inhaltsstruktur und die Gewohnheiten der Redakteure. Eine Änderung des Baums ändert Adressen, was Suchmaschinen und
Links betrifft; das ist eine Entscheidung über Inhalte, keine über das Design.

## Neues Design oder aufgefrischtes Design

| | Auffrischung | Neues Design |
|---|---|---|
| Templates | behalten, neu gestaltet | neu geschrieben |
| Inhaltsmodell | unverändert | kann sich ändern |
| Risiko | gering | höher |
| Typischer Umfang | Tokens, Typografie, Farbe, Komponenten, Dunkelmodus | alles in [Vom Mockup zum Theme](vom-mockup-zum-theme.md) |

Eine Auffrischung kann einen großen Teil des sichtbaren Gewinns zu einem Bruchteil des Risikos liefern, besonders wenn
das alte Theme Tokens erhält.

## Tokens in ein altes Theme einführen

1. Listen Sie jeden unterschiedlichen Wert für Farbe, Schriftgröße, Abstand und Radius in den alten Stylesheets auf.
2. Fassen Sie fast gleiche Werte zusammen und geben Sie jeder Gruppe einen Rollennamen
   ([Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md), [Typografische Skala](typografische-skala.md),
   [Abstände und Raster](abstaende-und-raster.md)).
3. Schreiben Sie die Token-Datei, mit dunklen Werten ([Design-Tokens](../designer/theme-design/design-tokens.md)).
4. Ersetzen Sie wörtliche Werte durch Tokens, Stylesheet für Stylesheet, und prüfen Sie die Seiten nach jedem.
5. Verschieben Sie dabei alle Inline-Stile aus Templates in Klassen
   ([Inline-Skripte und -Stile entfernen](../designer/lebenszyklus/inline-skripte-und-styles-entfernen.md)).

Danach sind Dunkelmodus und spätere Markenänderungen größtenteils Arbeit an Tokens
([Tutorial: Dunkelmodus ergänzen](../designer/theme-design/tutorial-dunkelmodus-hinzufuegen.md)).

## Neben dem Live-Design bauen

Bauen Sie das neue Design als neue Design-Erweiterung und liefern Sie es auf einem **Vorschau-Siteaccess** mit den
Live-Inhalten aus, während Besucher das alte behalten
([Ein Design auf einem Siteaccess in der Vorschau ansehen](../designer/theme-design/design-vorschau-auf-einem-siteaccess.md)).
Prüfen Sie dort mit Redakteuren und dem Kunden ([Mit Kunden und Reviews arbeiten](mit-kunden-und-reviews-arbeiten.md)).
Bearbeiten Sie nie das Live-Design an Ort und Stelle; lassen Sie das alte Design bis zum Umschalten unberührt.

## Redakteure und die Neugestaltung

- Sagen Sie Redakteuren früh, was sich für sie ändert: neue Ansichtstypen für Blöcke, neue Bildformate, neue Hinweise an
  den Feldern.
- Fragen Sie sie, welche Inhalte heute am schlechtesten aussehen; das ist meist der beste Stresstest.
- Planen Sie das Aufräumen der Inhalte (fehlende Alternativtexte, übergroße Bilder, leere Einleitungen) als Aufgabe mit
  einer verantwortlichen Person, nicht als Hoffnung.

## Umschalten

Das Umschalten ist eine Änderung der Einstellungen, die der Entwickler oder Administrator vornimmt: Der Siteaccess
erhält die neue Liste der Designs. Davor:

- [ ] [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md) auf dem Vorschau-Siteaccess bestanden.
- [ ] Caches eingeplant: Der Entwickler leert nach dem Umschalten die betroffenen Caches ([Caching für Designer](../designer/theme-design/caching-fuer-designer.md)).
- [ ] Alte Overrides, die nicht mehr gebraucht werden, zur späteren Entfernung aufgelistet.
- [ ] Ein Weg zurück vereinbart: Das alte Design bleibt installiert, bis das neue eine Weile gelaufen ist.

## Verwandte Seiten

- [Tutorial: ein statisches HTML-Design portieren](../designer/theme-design/tutorial-ein-statisches-html-design-portieren.md)
- [Visuelle Regressionsprüfungen](../designer/lebenszyklus/visuelle-regressionspruefungen.md)
- [Inkompatible Änderungen und die nächste Hauptversion](../designer/lebenszyklus/inkompatible-aenderungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
