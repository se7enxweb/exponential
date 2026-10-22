---
title: "Checkliste zur Design-Qualitätssicherung"
audience: theme-designer
category: theme-design
order: 38
summary: Eine Checkliste für Theme-Designer, um ein gebautes Exponential-Theme gegen das Design zu prüfen - auf einem Entwicklungs- oder Vorschau-Siteaccess, Seitentyp für Seitentyp, hell und dunkel, in drei Breiten, mit echten Inhalten und Stressfällen - und wie Sie melden, was durchfällt.
keywords: Theme-Designer, Design-QA, Qualitätssicherung, Checkliste, visuelle QA, Testen, hell und dunkel, Zoom, Stressfälle, Vorschau-Siteaccess, Fehlerbericht, theme designer, design QA, quality assurance, checklist, visual QA, testing, light and dark, stress content, preview siteaccess, bug report
type: help
---
# Checkliste zur Design-Qualitätssicherung

Diese Seite ist für Theme-Designer, die das gebaute Theme vor dem Review und vor dem Start gegen ihr Design prüfen. Sie
ist eine Checkliste für die **Design**-Qualität der Seiten. Die technischen Checklisten des Projekts (Barrierefreiheit,
Performance, CSP, Dunkelmodus, von rechts nach links, Start eines Themes) sind getrennt und gelten weiterhin; Links
stehen am Ende jedes Abschnitts. Arbeiten Sie auf einem Entwicklungs- oder Vorschau-Siteaccess, nie auf der
Live-Website.

## Inhalt

1. [Vorbereitung](#vorbereitung)
2. [Für jeden Seitentyp](#für-jeden-seitentyp)
3. [Der Rahmen](#der-rahmen)
4. [Inhaltsansichten](#inhaltsansichten)
5. [Listen, Suche und Blöcke](#listen-suche-und-blöcke)
6. [Besondere Seiten](#besondere-seiten)
7. [Grundlagen](#grundlagen)
8. [Melden](#melden)

## Vorbereitung

- [ ] Ein Entwicklungs- oder Vorschau-Siteaccess mit dem Theme ([Ein Design auf einem Siteaccess in der Vorschau ansehen](../designer/theme-design/design-vorschau-auf-einem-siteaccess.md)).
- [ ] Kommentare mit Template-Pfaden eingeschaltet, um in Berichten Templates zu nennen ([Werkzeuge für Theme-Designer](werkzeuge-fuer-theme-designer.md)).
- [ ] Die Testobjekte der Stressfälle sind angelegt ([Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md#die-stressfälle)).
- [ ] Eine Liste von Adressen: eine pro Klasse und Ansicht, dazu Suche, Anmeldung, Fehler, ein leerer Ordner.
- [ ] Ein Redakteurskonto zum Testen mit einer Adresse unter `example.invalid`.

## Für jeden Seitentyp

Prüfen Sie jede Adresse in dieser Matrix:

| | Hell | Dunkel |
|---|---|---|
| 1440 px | [ ] | [ ] |
| 960 px bei 200 % Zoom | [ ] | [ ] |
| 375 px | [ ] | [ ] |

Und auf jeder:

- [ ] Entspricht dem Mockup in Layout, Schrift, Abständen und Farbe, oder die Abweichung ist vereinbart.
- [ ] Kein horizontales Scrollen der Seite.
- [ ] Nur Tastatur: Jedes interaktive Element ist erreichbar, der Fokus immer sichtbar.
- [ ] Keine weiße Karte und kein weißer Kasten auf dunklem Hintergrund im Dunkelmodus.
- [ ] Einstellung für reduzierte Bewegung an: Nichts bewegt sich, was sich nicht bewegen soll.

## Der Rahmen

- [ ] Kopf- und Fußbereich wie gestaltet in jeder Breite; Menü für kleine Bildschirme geöffnet und geschlossen.
- [ ] Aktueller Bereich im Hauptmenü markiert; aktuelle Seite zuletzt in der Brotkrümelnavigation.
- [ ] Zustände angemeldet und abgemeldet.
- [ ] Sprachumschalter (falls vorhanden): jede Sprache in sich selbst benannt, die aktuelle markiert.
- [ ] Der Sprunglink erscheint beim ersten Tab.
- [ ] Logo hell und dunkel korrekt.

Siehe [Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md).

## Inhaltsansichten

- [ ] Vollansicht jeder Klasse mit allen Feldern ausgefüllt und mit jedem optionalen Feld leer.
- [ ] Längste Schlagzeile und längstes Wort brechen sauber um.
- [ ] Formatierter Text "mit allem": jede Überschriftenebene, Listen, Tabellen, Zitate, Einbettungen, Custom-Tags.
- [ ] Bilder: richtiger Alias, richtiger Zuschnitt, Alternativtext vorhanden, keine Verzerrung; hochgeladene Hochformate
      und Panoramen.
- [ ] Daten und Zahlen für die Locale des Siteaccess formatiert.

Siehe [Ansichtsmodi gestalten](ansichtsmodi-gestalten.md) und [Für Redakteure gestalten](fuer-redakteure-gestalten.md).

## Listen, Suche und Blöcke

- [ ] Ordnerlisten mit gemischten Klassen lesen sich als eine Liste.
- [ ] Karten mit und ohne Bild; ganze Karte klickbar mit einem Link für Screenreader.
- [ ] Seitennavigation auf der ersten, einer mittleren und der letzten Seite und auf einem Telefon.
- [ ] Suche mit Ergebnissen, einem Ergebnis, vielen Ergebnissen und keinem.
- [ ] Jeder Ansichtstyp von Layoutblöcken in voller Breite und in einer schmalen Zone; ein Eintrag; eine leere Sammlung
      hinterlässt keine Lücke.

Siehe [Inhaltslisten und Karten gestalten](inhaltslisten-und-karten-gestalten.md),
[Suchergebnisse gestalten](suchergebnisse-gestalten.md), [Layoutblöcke entwerfen](layoutbloecke-gestalten.md).

## Besondere Seiten

- [ ] Nicht gefunden, Zugriff verweigert (abgemeldet, mit dem Anmeldeformular, und angemeldet), allgemeiner Fehler.
- [ ] Anmelden, Passwort vergessen, Registrierung (falls eingeschaltet), Bestätigungsseiten.
- [ ] Formulare: Fehlerzusammenfassung und Meldungen pro Feld; Bestätigungsseite.
- [ ] Leerer Ordner und leere Liste verwandter Inhalte.

Siehe [Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md), [Formulare gestalten](formulare-gestalten.md),
[Anmelde- und Benutzerseiten gestalten](anmelde-und-benutzerseiten-gestalten.md).

## Grundlagen

- [ ] Jede Farbe, Größe, jeder Abstand und Radius in den Stylesheets ist ein Token; jeder Farb-Token hat einen dunklen
      Wert.
- [ ] Textkontrast mindestens 4,5:1 in beiden Themes; Fokus und Rahmen von Bedienelementen mindestens 3:1.
- [ ] Typografische Skala und Abstandsskala wie festgelegt verwendet; keine verirrten Werte.
- [ ] Interaktive Zustände wie in der Zustandsmatrix ([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md)).
- [ ] Seitenbudget eingehalten: Anfragen, keine Anfragen an Dritte vor der Einwilligung, LCP und CLS
      ([Für Performance-Budgets gestalten](fuer-performance-budgets-gestalten.md)).
- [ ] Die [Styleguide-Seite](eine-styleguide-seite-bauen.md) entspricht dem [Komponenteninventar](komponenteninventar.md).

Führen Sie dann die Checklisten des Projekts aus: [Barrierefreiheit](../designer/theme-design/checkliste-barrierefreiheit.md),
[Dunkelmodus](../designer/theme-design/checkliste-dunkelmodus.md), [Performance](../designer/theme-design/checkliste-leistung.md),
[CSP](../designer/theme-design/checkliste-csp.md), [Von rechts nach links](../designer/theme-design/checkliste-rechts-nach-links.md).

## Melden

Ein Problem pro Bericht, mit:

1. der Adresse, der Breite, hell oder dunkel und der Zoomstufe;
2. was Sie sehen und was das Design erwartet (Link auf das Mockup);
3. dem Template, das es gezeichnet hat, aus den Kommentaren mit Template-Pfaden;
4. einem Bildschirmfoto, aus dem alle personenbezogenen Daten entfernt sind;
5. der Priorität: blockiert den Start, sollte behoben werden, später.

## Verwandte Seiten

- [Checkliste für das Theme-Design-Review](checkliste-theme-design-review.md)
- [Checkliste zur Übergabe](checkliste-zur-uebergabe.md)
- [Checkliste: Start eines Themes](../designer/theme-design/checkliste-theme-start.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
