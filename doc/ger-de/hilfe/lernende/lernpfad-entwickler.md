---
title: "Lernpfad: Entwickler"
audience: student
category: learning
order: 7
summary: Ein schrittweiser Lernpfad für angehende Exponential-Entwickler, aufgebaut aus vorhandenen Hilfeseiten und Anleitungen - eine Entwicklungsinstallation aus Git-Klonen, das Inhaltsmodell, die Template-Sprache, Einstellungen, Fetch-Funktionen, Erweiterungen und Module, die RAD-Werkzeuge, die REST API, Tests und Beitragen -, mit einer Übungsaufgabe und einer Prüfung für jeden Schritt.
keywords: lernpfad, entwickler, PHP, git, template-sprache, erweiterung, modul, inhaltsklasse, datentyp, REST API, RAD, tests, beitragen, schwerpunkt, learning path, developer, template language, extension, module, content class, datatype, contributing, track
type: help
---
# Lernpfad: Entwickler

Dieser Pfad ist für Lernende, die mit PHP auf Exponential aufbauen werden: Erweiterungen, Module, Templates,
Inhaltsklassen und Integrationen. Sie sollten Grundkenntnisse in PHP haben und ein Terminal bedienen können. Git
lernen Sie unterwegs. Schließen Sie zuerst den [Redakteurpfad](lernpfad-redakteur.md) ab; er zeigt Ihnen, wofür der
Code, den Sie schreiben, da ist.

## Inhalt

1. [Die Schritte](#die-schritte)
2. [Weiterführend](#weiterführend)
3. [Wohin der Pfad als Nächstes führt](#wohin-der-pfad-als-nächstes-führt)

## Die Schritte

### Schritt 1: Eine Entwicklungsinstallation aus Git-Klonen (2 Stunden)

- Lesen: [Mit Git entwickeln](../mit-git-entwickeln.md), Schritte 1 und 2,
  [Eine Entwicklungsinstallation einrichten](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md).
- Üben: [Aus Git-Klonen zum Lernen installieren](aus-git-klonen-zum-lernen-installieren.md).
- Prüfen: Was gibt Ihnen `git switch -c my-site`, und warum bedeutet „HEAD detached“ in einer Erweiterung nicht, dass
  etwas kaputt ist?

### Schritt 2: Den Code lesen, den Sie haben (1 Stunde)

- Lesen: [Den Code lesen, den Sie haben](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/den-vorhandenen-code-lesen.md),
  [Was eine Erweiterung ist](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md).
- Üben: Verwenden Sie `git log --oneline -10 -- <file>` und `git blame <file>` an einer Kernel-Datei
  ([Mit Git entwickeln](../mit-git-entwickeln.md#herausfinden-warum-und-wann)).

### Schritt 3: Das Inhaltsmodell (2 Stunden)

- Lesen: [Das Inhaltsmodell verstehen](das-inhaltsmodell-verstehen.md),
  [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md), Abschnitte 1 bis 3.
- Üben: [Projekt 1](projekt-01-eine-eigene-inhaltsklasse.md).
- Prüfen: Was ist der Unterschied zwischen einem Inhaltsobjekt und einem Knoten?

### Schritt 4: Die Template-Sprache (4 Stunden)

- Lesen: [Die Template-Sprache lernen](die-template-sprache-lernen.md),
  [Templates und Design](../../anleitungen/templates-und-design.md), Abschnitte 1 bis 6.
- Üben: [Projekt 3](projekt-03-ein-template-override.md).
- Prüfen: Warum verwendet `{$node.name|wash}` den Operator `wash`?

### Schritt 5: Einstellungen, Siteaccesses und die Konsole (2 Stunden)

- Lesen: [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md),
  [Geltungsbereiche von Einstellungen und ihre Reihenfolge](../erweiterungsentwicklung/teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md),
  [Der Befehl exp:ini](../erweiterungsentwicklung/teil-2-builder/der-befehl-exp-ini.md).
- Üben: Lesen Sie eine Einstellung mit `exp:ini where` und erklären Sie, welche Datei gewinnt.

### Schritt 6: Erweiterungen und Module (4 Stunden)

- Lesen: [Erweiterungen](../../anleitungen/extensions.md), Abschnitt 6.1, [RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md).
- Üben: [Projekt 8](projekt-08-ihre-erste-erweiterung.md).
- Prüfen: Welche Zugriffskontrolle hat eine Modul-View ohne Eintrag `'functions'`? (Keine: Jeder, der die Website
  erreichen kann, kann sie öffnen, wie die Anleitung zu Erweiterungen warnt.)

### Schritt 7: Die REST API (2 Stunden)

- Lesen: [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md), [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md),
  Abschnitte 1 bis 3.
- Üben: [Projekt 7](projekt-07-inhalte-ueber-die-rest-api-lesen.md).
- Prüfen: Welche Versionen beantworten Lesezugriffe und welche Schreibzugriffe?

### Schritt 8: Suche und Rollen aus der Sicht von Entwicklern (2 Stunden)

- Lesen: [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md#für-entwickler), den
  Kurs [Suche](../trainer/suche/README.md) der Trainer, Modul 1.
- Üben: [Projekt 6](projekt-06-eine-suche-die-das-richtige-findet.md).

### Schritt 9: Qualität und Beitragen (2 Stunden)

- Lesen: [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md), [Coding-Standards](../../standards/programmierstandards.md),
  [Code beitragen](../unterstuetzer/code-beitragen.md), [Als Lernende beitragen](als-lernende-beitragen.md).
- Üben: [Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md).
- Prüfen: Mit welchen fünf Wörtern darf eine Commit-Betreffzeile beginnen? (`Added:`, `Updated:`, `Fixed:`,
  `Removed:`, `Renamed:`, wie `CONTRIBUTING.md` sie aufführt.)

## Weiterführend

| Thema | Seiten |
|---|---|
| Das gesamte Handbuch für Entwickler | [Erweiterungsentwicklung](../erweiterungsentwicklung/README.md), [Lesepfade](../erweiterungsentwicklung/lesepfade.md) |
| Die Template-Sprache in der Tiefe | [Template-Sprache](../template-sprache/README.md), [Template-Operatoren](../template-operatoren/README.md) |
| Workflows und Ereignisse | [Workflows](../../anleitungen/workflows.md) |
| Eine Erweiterung veröffentlichen | [Erweiterungen](../../anleitungen/extensions.md#7-die-extension-veröffentlichen), [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md) |
| Code aktualisieren | [Mit Git aktualisieren](../mit-git-aktualisieren.md) |

## Wohin der Pfad als Nächstes führt

- Lernprojekte: [1](projekt-01-eine-eigene-inhaltsklasse.md), [3](projekt-03-ein-template-override.md),
  [6](projekt-06-eine-suche-die-das-richtige-findet.md), [7](projekt-07-inhalte-ueber-die-rest-api-lesen.md),
  [8](projekt-08-ihre-erste-erweiterung.md), [Abschlussprojekt](projekt-10-abschlussprojekt-eine-vollstaendige-kleine-website.md).
- Mit einem Trainer: [Entwickler](../trainer/entwickler/README.md), 13 Module.
- Beitragen: [Als Lernende beitragen](als-lernende-beitragen.md).

## Verwandte Seiten

- [Git als Lernende nutzen](git-als-lernende-nutzen.md)
- [Git-Kurzreferenz für Lernende](git-kurzreferenz-fuer-lernende.md)
- [Lernpfad: Designer](lernpfad-designer.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
