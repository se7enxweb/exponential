---
title: Dokumentation als Code
audience: software-engineer, developer
category: software-engineering
order: 920
summary: Wie Exponential seine Dokumentation wie Code behandelt - Markdown-Dateien in doc/, mit dem Kernel versioniert, vom Hilfesystem in der Administration ausgeliefert, Front Matter, das Zielgruppen und Suche steuert, eine Prüfung von Links und Bildern auf der Kommandozeile, mit Erweiterungen ausgelieferte Dokumentation, Übersetzungen, die GNU FDL und die Regeln, um mit einer Änderung eine Seite zu schreiben.
keywords: Dokumentation als Code, Docs as Code, Hilfesystem, exphelp, exp:help check, Front Matter, Markdown, doc/, Linkprüfung, Zielgruppen, Dokumentation von Erweiterungen, GNU FDL, DITA, Übersetzungen, Softwareingenieur, documentation as code, help system, link check, audiences, extension documentation, translations
type: help
---
# Dokumentation als Code

Diese Seite richtet sich an Ingenieure, die mit ihrer Änderung Dokumentation schreiben oder die Dokumentation einer
Erweiterung pflegen. In Exponential liegt die Dokumentation im Repository, wird im selben Pull-Request wie der Code
geprüft, von einem Befehl kontrolliert und im Produkt gelesen. Die Funktionsseite ist [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md).

## Inhalt

1. [Wo die Dokumentation liegt](#wo-die-dokumentation-liegt)
2. [Wie sie ausgeliefert wird](#wie-sie-ausgeliefert-wird)
3. [Front Matter](#front-matter)
4. [Seiten prüfen](#seiten-prüfen)
5. [Dokumentation von Erweiterungen](#dokumentation-von-erweiterungen)
6. [Schreibregeln](#schreibregeln)
7. [Lizenz](#lizenz)

## Wo die Dokumentation liegt

| Art | Verzeichnis |
|---|---|
| Hilfeseiten nach Zielgruppe | `doc/eng-us/help/` (diese Seite liegt im Original in `doc/eng-us/help/software-engineers/`) |
| Funktionsseiten | `doc/eng-us/features/6.0/` |
| Upgrade-Hinweise | `doc/eng-us/bc/6.0/` |
| Anleitungen | `doc/eng-us/guides/` |
| Standards und Spezifikationen | `doc/eng-us/standards/`, `doc/eng-us/specifications/` |
| Installationsbuch | `doc/eng-us/install/` |
| Changelogs | `doc/changelogs/` |
| Andere Sprachen | `doc/ger-de/` und weitere Verzeichnisse aus `exphelp.ini [DocLanguages]` |

## Wie sie ausgeliefert wird

Das Hilfesystem zeigt jede **von Git verfolgte** Markdown- und Textdatei unter `doc/` in der Administration
(`/exphelp/viewer/<path>`), mit Überschriften, Inhaltsverzeichnis, Tabellen, hervorgehobenem Code, der Navigation ihres
Abschnitts und einem Link auf die Datei bei GitHub; eine Suche findet Seiten nach Titel, Überschriften und Text, mit Filtern
für Zielgruppe, Kategorie und Typ. Zum Lesen der Hilfe braucht man die Richtlinie `exphelp/read`. Eine Datei, die Git nicht
verfolgt, ist keine Seite; eine neue Seite erscheint also in der Hilfe, sobald sie committet ist.

## Front Matter

Hilfeseiten beginnen mit einem Block im YAML-Stil:

| Schlüssel | Zweck |
|---|---|
| `title` | der Seitentitel, zugleich die H1 |
| `audience` | eine oder mehrere Zielgruppen, die die Filter und die Karten des Dashboards steuern |
| `category` | die Kategorie in der Hilfe |
| `order` | die Reihenfolge innerhalb des Ordners |
| `summary` | die Beschreibung in Suchergebnissen und Listen |
| `keywords` | zusätzliche Suchbegriffe |
| `featured` | unter den hervorgehobenen Seiten gezeigt |
| `type` | die Art der Seite, zum Beispiel `help` |

## Seiten prüfen

```bash
./console exp:help check --below=help/software-engineers/   # links and images of the pages below a path
./console exp:help search "clear cache"                      # what readers will find
./console exp:help show guides/caches.md                     # the metadata and headings of one page
./console exp:help index --rebuild                           # rebuild the index
```

Die Prüfung meldet Fehler (ein Link auf eine fehlende Datei, ein Link, der die Installation verlässt, ein unsicheres
Schema, ein defektes Bild), Warnungen (ein Anker, den keine Überschrift hat, ein absoluter Link) und Hinweise (ein Link auf
eine Datei, die keine Hilfeseite ist). `--strict` lässt auch Warnungen scheitern. Verwenden Sie nur relative Links, und
verlinken Sie Anker auf Überschriften, die es gibt.

## Dokumentation von Erweiterungen

Die Hilfe zeigt die Dokumentation, die jede aktive Erweiterung mitbringt (ihr Verzeichnis `doc/`, README, CHANGELOG,
LICENSE), unter `/exphelp/extensions`, im selben Betrachter und in derselben Suche
([Hilfesystem für Erweiterungen](../erweiterungsentwicklung/teil-8-referenz/hilfesystem-fuer-erweiterungen.md),
[Ihre Erweiterung dokumentieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ihre-erweiterung-dokumentieren.md)).

## Schreibregeln

- Schreiben Sie, was es gibt. Prüfen Sie jede Aussage im Code, in den Einstellungen oder auf einer anderen Seite, und sagen
  Sie es, wo die Quellen keine Antwort geben.
- Nennen Sie das Produkt Exponential. Bezeichner wie `eZContentObject` bleiben, wie sie sind; der alte Produktname erscheint
  nur als Geschichte.
- Nur echte Befehle in Codeblöcken; keine Hostnamen, Konten oder Passwörter einer echten Installation.
- Amerikanisches Englisch in der Oberfläche und der englischen Dokumentation.
- Eine Verhaltensänderung erhält ihren Upgrade-Hinweis, eine Funktion ihre Seite, ein Release sein Changelog
  ([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).

Hilfethemen, die als Inhalte in der Administration gepflegt werden, folgen ihrem eigenen Stilleitfaden und ihren
Prüfzuständen ([Stilleitfaden der Hilfe](../../spezifikationen/6.0/hilfe-stilrichtlinien.md),
[Die Hilfe aktuell halten](../hilfethemen-pflegen.md)).

## Lizenz

Die Dokumentation ist frei unter der GNU Free Documentation License 1.3; der Code steht unter der GNU GPL, Version 2 oder
später ([Freie Dokumentation](../freie-dokumentation.md), [Standards für Dokumentation und Veröffentlichung](../../standards/dokumentation-und-veroeffentlichung.md)).

## Verwandte Seiten

- [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md)
- [Standards der Hilfe](../../spezifikationen/6.0/hilfestandards.md)
- [Beiträge an das Projekt](upstream-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
