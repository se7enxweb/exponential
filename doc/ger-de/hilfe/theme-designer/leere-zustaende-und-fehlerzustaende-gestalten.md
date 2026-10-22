---
title: "Leere Zustände und Fehlerzustände gestalten"
audience: theme-designer
category: theme-design
order: 21
summary: Wie Theme-Designer die Momente gestalten, in denen eine Exponential-Website nichts zu zeigen hat oder etwas schiefgegangen ist - leere Ordner und Blöcke, fehlende Bilder, keine Suchergebnisse, Seiten für "nicht gefunden" und "Zugriff verweigert", Formularfehler und das gemeinsame Muster für alle.
keywords: Theme-Designer, leerer Zustand, Fehlerzustand, nicht gefunden, 404, Zugriff verweigert, Fehlerseite, Kernel-Fehler, leerer Ordner, keine Ergebnisse, fehlendes Bild, Fehler-Templates, theme designer, empty state, error state, not found, access denied, error page, kernel errors, empty folder, no results, missing image, error templates
type: help
---
# Leere Zustände und Fehlerzustände gestalten

Diese Seite ist für Theme-Designer, die möchten, dass auch die Randfälle einer Website gestaltet aussehen.
Inhaltsverwaltete Websites erzeugen ständig leere Zustände und Fehlerzustände: ein neuer Ordner ohne Unterelemente, ein
Block, dessen Abfrage nichts findet, ein Link auf eine Seite, die verschoben wurde, ein Besucher, der eine Seite nicht
sehen darf. Die beteiligten Templates sind in [Fehler-Templates](../designer/vorlagen/fehler-templates.md)
dokumentiert; diese Seite handelt davon, wie sie aussehen sollten.

## Inhalt

1. [Ein Muster für alle](#ein-muster-für-alle)
2. [Leere Inhalte](#leere-inhalte)
3. [Fehlende Bilder und optionale Felder](#fehlende-bilder-und-optionale-felder)
4. [Fehlerseiten](#fehlerseiten)
5. [Nicht gefunden](#nicht-gefunden)
6. [Zugriff verweigert](#zugriff-verweigert)
7. [Fehler in Formularen](#fehler-in-formularen)
8. [Ein Inventar der Zustände](#ein-inventar-der-zustände)

## Ein Muster für alle

Bauen Sie eine Komponente für den **leeren Zustand** und eine **Meldungskomponente** (Hinweis), und verwenden Sie sie
überall ([Komponenteninventar](komponenteninventar.md)). Jede beantwortet drei Fragen in einfachen Worten:

1. Was ist passiert? ("Dieser Ordner enthält noch keine Artikel.")
2. Warum, wenn es hilft? ("Möglicherweise müssen Sie sich anmelden, um diese Seite zu sehen.")
3. Was kann ich jetzt tun? Ein Link oder eine Schaltfläche.

Halten Sie den Ton ruhig und die Sprache einfach. Kein Humor, der in der Übersetzung scheitert, keine Schuldzuweisung,
keine Fehlercodes als Haupttext.

## Leere Inhalte

| Situation | Was Besucher sehen | Was das Design tun sollte |
|---|---|---|
| Ein Ordner ohne Unterelemente | die Vollansicht des Ordners mit einer leeren Liste | ein kurzer Satz oder gar nichts, wenn der Ordner einen eigenen Text hat |
| Ein Layoutblock, dessen Sammlung leer ist | ein leerer Block | der Block sollte nichts ausgeben, damit sich die Zone schließt |
| Eine Liste verwandter Inhalte ohne Verknüpfungen | eine leere Überschrift | keine Überschrift, kein Kasten |
| Eine auf nichts gefilterte Liste | eine leere Liste | sagen, welcher Filter das verursacht hat und wie man ihn entfernt |

Die übliche Regel für Besucher: **leere Container verbergen**. Ein Kasten mit Überschrift und nichts darin sieht kaputt
aus. Eine Meldung über leere Inhalte ist nur dort richtig, wo der Besucher nach etwas gefragt hat (eine Suche, ein
Filter, eine Liste, die er absichtlich geöffnet hat).

Redakteure sind die Ausnahme: In der Vorschau eines leeren Blocks müssen sie sehen, dass der Block da ist. Fragen Sie
den Entwickler, ob Block-Templates einen Hinweis nur in der Vorschau des Redakteurs zeigen können
([Layouts für Designer](../designer/theme-design/layouts-fuer-designer.md)).

## Fehlende Bilder und optionale Felder

Jedes optionale Feld braucht ein Design ohne es ([Für Redakteure gestalten](fuer-redakteure-gestalten.md#regel-2-jedes-optionale-feld-hat-ein-design-für-leer)).
Bevorzugen Sie besonders bei Bildern eine Variante nur mit Text gegenüber einem grauen Platzhalter; ein Raster aus
gleichen grauen Kästen sagt Besuchern nichts.

## Fehlerseiten

Wenn der Kernel eine Seite nicht zeigen kann, gibt er ein Fehler-Template innerhalb Ihres Rahmens aus. Die Templates
sind nach dem Fehlercode des Kernels nummeriert, unter `error/kernel/` (der Kernel liefert `1.tpl` bis `6.tpl` und
`20.tpl` bis `22.tpl`); die Hilfe für Designer listet auf, welche Nummer was bedeutet
([Fehler-Templates](../designer/vorlagen/fehler-templates.md#die-templates-und-ihre-nummern)). Gestalten Sie sie als
normale Seiten:

- den Rahmen mit Kopfbereich, Navigation und Fußbereich, damit Besucher weitermachen können;
- eine klare Überschrift und ein oder zwei Sätze;
- das Suchfeld;
- Links zur Startseite und zu den Hauptbereichen.

## Nicht gefunden

Die Seite "nicht gefunden" ist die meistbesuchte Fehlerseite. Gestalten Sie sie so, dass Besucher wieder Tritt fassen,
denn viele kommen nach einer Umstrukturierung über alte Links: Eine Suche, in die die Wörter aus der Adresse schon
eingetragen sind, ist eine nützliche Idee, braucht aber einen Entwickler, der sie baut
([Fehler-Templates](../designer/vorlagen/fehler-templates.md#eine-hilfreiche-seite-nicht-gefunden)).

## Zugriff verweigert

Zwei verschiedene Fälle brauchen zwei verschiedene Meldungen:

- **Nicht angemeldet**: Der Inhalt ist nach der Anmeldung vielleicht sichtbar. Mit den mitgelieferten
  Fehlereinstellungen erhält ein anonymer Besucher, dem der Zugriff verweigert wird, die Fehlerseite mit eingebettetem
  Anmeldeformular; gestalten Sie die Seite für verweigerten Zugriff und das Anmeldeformular also zusammen
  ([Anmelde- und Benutzerseiten gestalten](anmelde-und-benutzerseiten-gestalten.md)).
- **Angemeldet, aber nicht berechtigt**: Sagen Sie das klar und bieten Sie einen Weg zurück an. Fordern Sie den
  Besucher nicht auf, sich erneut anzumelden.

## Fehler in Formularen

Validierungsfehler sind ein Zustand des Formulars, keine Fehlerseite: eine Zusammenfassung oben mit Links zu jedem Feld
und eine Meldung an jedem Feld ([Formulare gestalten](formulare-gestalten.md)).

## Ein Inventar der Zustände

Nehmen Sie diese in Ihre Mockup-Liste und Ihre [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md) auf:

- [ ] Leerer Ordner, leerer Block, leere Liste verwandter Inhalte, leere gefilterte Liste.
- [ ] Karte und Vollansicht ohne Bild und ohne jedes optionale Feld.
- [ ] Keine Suchergebnisse.
- [ ] Nicht gefunden, Zugriff verweigert (abgemeldet und angemeldet) und eine allgemeine Fehlerseite.
- [ ] Formular mit Fehlern.
- [ ] Alle hell und dunkel, in drei Breiten.

## Verwandte Seiten

- [Fehler-Templates](../designer/vorlagen/fehler-templates.md)
- [Suchergebnisse gestalten](suchergebnisse-gestalten.md#keine-ergebnisse)
- [Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
