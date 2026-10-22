---
title: "Eine standardkonforme Website: Markup, Stil und Struktur"
audience: designer, builder, developer
category: books
order: 560
summary: Das Plädoyer des Buchs für Webstandards und seine Planungsmethode für die Website eines Instituts (Seitentypen werden zu Datenklassen), aktualisiert von XHTML 1.0 und CSS 2 auf HTML5, barrierefreies Markup und das Inhaltsmodell von Exponential 6.
keywords: Webstandards, HTML, CSS, Barrierefreiheit, Bandbreite, Semantik, Validierung, Seitentypen, Datenklassen, Fallstudie, Templates, Buch Learning, web standards, accessibility, bandwidth, semantics, validation, page types, data classes, case study, templates, Learning book
type: help
---
# Eine standardkonforme Website: Markup, Stil und Struktur

Das letzte Kapitel des Buchs ist eine Fallstudie: eine Website für ein Universitätsinstitut, gebaut mit Templates, die
sauberes Markup erzeugen. Seine Werkzeuge (XHTML 1.0, CSS 2, eine Browserliste, die mit Netscape 4 beginnt) sind
Geschichte. Seine Argumentation und seine Planungsmethode sind es nicht.

## Warum Standards

Die vier Gründe des Buchs, in heutigen Worten:

- **Barrierefreiheit.** Semantisches Markup (Überschriften, die Überschriften sind, Listen, die Listen sind, Tabellen nur
  für Daten, Textalternativen für Bilder) ist das, worauf Bildschirmleser und Tastaturen angewiesen sind.
  [Verpflichtender Alternativtext](../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)
  hilft Ihnen, das einzuhalten.
- **Bandbreite.** Darstellung in einem gemeinsamen Stylesheet statt in jeder Seite bedeutet kleinere Seiten und ein
  Stylesheet, das der Browser behält.
- **Zukunftssicherheit.** Seiten, die dem Standard folgen, funktionieren auch in Browsern, die es noch nicht gibt;
  Seiten, die von den Eigenheiten eines Browsers abhängen, nicht.
- **Leichte Wartung.** An einer Stelle ändert sich das Aussehen der ganzen Website.

Der heutige Standard ist HTML (der Living Standard, „HTML5“) für Markup und CSS für Stil, geprüft mit den Validatoren
des W3C (`validator.w3.org`). Streben Sie gültiges Markup an und sehen Sie sich die Seite ohne Stylesheet an: Sie sollte
sich in der richtigen Reihenfolge lesen.

## Warum das System hilft

Das Buch nennt zwei Vorteile, die der Aufteilung des Standards selbst entsprechen: **Darstellung durch Templates vom
Code getrennt** und **modulare, wiederverwendbare Inhaltsstrukturen**. In einem Template heißt das: Das Markup gehört
Ihnen, einmal in den Templates des Designs geschrieben ([Grundsätze der Templates](grundsaetze-der-templates.md)); Inhalte kommen
aus den Attributen des Objekts, sodass kein Markup in den Inhalten gespeichert wird, außer in [Rich Text](rich-text-tags.md),
wo die Tags semantisch sind (Überschriften, Listen, Tabellen, Einbettungen) und die Templates sie in das Markup
verwandeln, das Sie gewählt haben. Stil gehört in die Stylesheets des Designs, aus `design.ini` geladen, nie inline
(siehe [Die Design-Kette](../erweiterungsentwicklung/teil-2-builder/die-design-kette.md)); Skript gehört in Dateien, weil die
Content Security Policy der Website Inline-Skripte blockiert.

## Die Struktur planen: Seitentypen werden zu Datenklassen

Die Methode der Fallstudie funktioniert für jede Website und ist der Teil, den Sie behalten sollten. Führen Sie jede
Seite der geplanten Website auf und schreiben Sie daneben, welche Art von Seite sie ist: eine **einfache Seite** (ein
Titel und Text), eine **Liste** einer Art von Dingen oder eine **Seite, die eigene Daten braucht**. Die Seiten, die eigene
Daten brauchen, werden zu Inhaltsklassen. Bei der Website des Instituts kam Folgendes heraus:

| Art der Seite | Wurde zu |
|---|---|
| Anfahrt, Was ist Geomatik?, Industrie, Übersicht der Lehrveranstaltungen | eine Klasse, **Einfache Seite** (und die Startseite wählt einige davon zur Anzeige aus) |
| Veröffentlichungen, nach Jahr gruppiert | Klasse **Veröffentlichung** |
| Stellenangebote (Promotion, Forschung, sonstige) | Klasse **Stellenangebot** |
| Forschungsbereiche mit Projekten | Klasse **Projekt** |
| Mitarbeitende, Studierende, nach Kategorien | Klasse **Person**, in Kategorieordnern |
| Ausgründungen und Links | Klasse **Link** |
| Nachrichten | Klasse **Nachricht** (Stellenangebote erscheinen hier ebenfalls) |

Zwei Dinge, die das Buch über den Vorgang anmerkt, sind es wert, übernommen zu werden: Die Struktur wurde während der
Planung **einfacher** (der Nachrichtenbereich, zuerst kompliziert, endete als eine Klasse und eine Liste), und die
**Startseite** wurde aus den anderen Seiten heraus entworfen, indem sie eine aktuelle Nachricht, ein hervorgehobenes
Projekt und einige Schnelllinks aus den Bereichen darunter übernimmt; in Templates sind das Abrufe
([Inhalte abrufen](inhalte-in-templates-abrufen.md)) in einem Startseiten-Template, das das Template des Ordners
überschreibt ([Templates überschreiben](templates-ueberschreiben.md)).

Machen Sie die Übung zuerst auf Papier ([Eine Beispielsite planen](eine-beispielsite-planen.md)); legen Sie dann die
Klassen an ([Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md)); schreiben Sie dann die Templates.

Hier enden die Kapitel des Buchs Learning in dieser Hilfe. [Zurück zum Anfang](README.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 9, „Creating a Standards-Compliant eZ publish Site“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: Standards von XHTML und CSS 2 auf HTML und die heutige Praxis aktualisiert; die Planungsmethode beibehalten.
