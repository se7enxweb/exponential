---
title: "Projektdateien: README, Sicherheitsrichtlinie und Mitwirken in der Hilfe"
audience: admin, developer
category: project-files
order: 60
summary: README.md, SECURITY.md, CONTRIBUTING.md, COPYRIGHT.md und LICENSE.md des Repositorys sowie die README-Dateien seiner Verzeichnisse sind Seiten der Hilfe. Wo Sie sie finden, wie Links zwischen ihnen und der Dokumentation funktionieren und was nie angezeigt wird.
keywords: README, SECURITY, CONTRIBUTING, COPYRIGHT, LICENSE, Projektdateien, Repository, Wurzelverzeichnis, Markdown, git, project files
type: help
---
# Projektdateien: README, Sicherheitsrichtlinie und Mitwirken in der Hilfe

Das Repository von Exponential hat außerhalb des Dokumentationsverzeichnisses einige Dateien, die jeder Administrator
und Entwickler kennen sollte: das [README](../../../README.md), das das Projekt vorstellt, die
[Sicherheitsrichtlinie](../../../SECURITY.md), die sagt, wie Sie eine Sicherheitslücke melden, die
[Richtlinien für Mitwirkende](../../../CONTRIBUTING.md), den [Copyright-Hinweis](../../../COPYRIGHT.md) und die
[Lizenz](../../../LICENSE.md). Die Hilfe zeigt sie als eigene Seiten, sodass Sie sie in der Administrationsoberfläche
lesen können, ohne zu GitHub zu gehen.

## Wo Sie sie finden

- **Alle Hilfeseiten** (das Inhaltsverzeichnis) und **der Seitenbaum** neben jeder Hilfeseite: Jede von ihnen ist eine
  Seite oben im Baum, neben den Hauptordnern, unter ihrem eigenen Titel (der des README ist der Name des Projekts). Sie
  liegen in keinem Ordner, und die Brotkrumennavigation über einer von ihnen lautet Hilfe > ihr Titel.
- **Das Dashboard**: die Kategorie **Projektdateien**.
- **Die Suche**: Sie werden wie jede Seite gefunden; wählen Sie die Kategorie „Projektdateien“ oder den Typ
  „Projektdatei“, um nur sie zu sehen.

Ihre Adressen beginnen mit `project/`, gefolgt vom Pfad der Datei im Repository:
`/exphelp/viewer/project/README.md`, `/exphelp/viewer/project/SECURITY.md`. Die `README.md`-Dateien der
Kommandozeilenskripte (`project/bin/...`), der Designs und des Kernels werden ebenfalls angezeigt, wenn diese
Verzeichnisse eine haben.

Die Dokumentation der Extensions gehört nicht dazu: Sie hat ihre eigene Kategorie, **Extensions**. Tests, die Dateien
verborgener Verzeichnisse wie `.github/` (die Vorlage für Pull Requests) und alles andere, was nicht als Dokumentation
gelesen werden soll, werden hier nie angezeigt.

## Links zwischen ihnen und der Dokumentation

Die Dateien verlinken einander und die Dokumentation mit relativen Links, wie auf GitHub, und die Hilfe folgt ihnen: Ein
Link aus dem README auf eine Anleitung öffnet die Anleitung in der Hilfe, und ein Link von einer Hilfeseite auf das
README öffnet das README hier. Jede Seite hat außerdem „Auf GitHub bearbeiten“, das dieselbe Datei im Repository öffnet.

## In anderen Sprachen

Diese Dateien sind auf Englisch geschrieben und werden, wie die Versionshinweise, nicht übersetzt. Wenn Sie die Hilfe in
einer anderen Sprache lesen, werden sie auf Englisch angezeigt, mit einem Hinweis, der das sagt.

## Lizenz

Die Projektdateien sind Teil der Software, nicht der Dokumentation: Sie werden mit Exponential unter dessen Lizenz
verbreitet, der GNU General Public License, Version 2 oder jede spätere Version. Der Hinweis unter jeder von ihnen sagt
das und verlinkt die Lizenz. Die Dokumentationsseiten selbst sind freie Dokumentation (siehe
[Freie Dokumentation](freie-dokumentation.md)).

## Was nie angezeigt wird

Nur Dateien, die im Repository committet sind, werden angezeigt. Eine Datei, die jemand ins Installationsverzeichnis
gelegt hat, ohne sie zu committen (eine private Notiz, eine lokale Kopie einer Seite), wird nie angezeigt, nie von der
Suche gefunden und nie verlinkt, gleich, was die Einstellungen sagen. Dateien an Orten, die git ignoriert, Dateien
außerhalb des Installationsverzeichnisses, verborgene Dateien und Verzeichnisse (`.github/`), Tests, alles unterhalb
von `extension/` und alles, was nicht Markdown ist, werden ebenfalls nie angezeigt.

Für Administratoren, die ändern möchten, welche Dateien angezeigt werden, und für die vollständige Liste der Regeln,
siehe [Das Hilfesystem](../funktionen/6.0/hilfesystem.md#projektdateien) (`exphelp.ini [ProjectFiles]`).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
