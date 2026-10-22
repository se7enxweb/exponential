---
title: Code als Community-Mitglied beitragen
audience: community, developer, supporter
category: community
order: 24
summary: Der Ablauf eines Code-Beitrags zum Exponential-Kernel aus Sicht eines Community-Mitglieds - Fork, Branch, Coding-Standards und Namenspräfixe, Commit-Nachrichten mit Präfix, das Dateimanifest, Tests, das CLA, der Pull Request und die Regel zu Squash und Rebase.
keywords: Code, Pull Request, Fork, Branch, Commit-Nachricht, Added Updated Fixed Removed Renamed, Manifest, filelist.md5, make hooks, Coding-Standards, PSR, exp-Präfix, Tests, CLA, Squash, Rebase, commit message, coding standards, exp prefix, tests
type: help
---
# Code als Community-Mitglied beitragen

Code-Beiträge gehen als GitHub-Pull-Requests an das Kernel-Repository
[se7enxweb/exponential](https://github.com/se7enxweb/exponential). Die verbindlichen Regeln stehen in
`CONTRIBUTING.md`; diese Seite ordnet sie in der Reihenfolge, in der Sie ihnen begegnen.

## Inhalt

1. [Bevor Sie Code schreiben](#bevor-sie-code-schreiben)
2. [Während Sie Code schreiben](#während-sie-code-schreiben)
3. [Bevor Sie den Pull Request eröffnen](#bevor-sie-den-pull-request-eröffnen)
4. [Der Pull Request](#der-pull-request)
5. [Code, der kein Kernel-Code ist](#code-der-kein-kernel-code-ist)

## Bevor Sie Code schreiben

1. **Suchen Sie nach einem Issue.** Durchsuchen Sie den Tracker; behebt Ihre Änderung ein bekanntes Issue, nennen Sie
   es. Für eine größere Funktion eröffnen Sie zuerst ein Issue und beschreiben die Idee, damit Sie nichts bauen, das
   nicht gemergt werden kann. Tun Sie dasselbe, wenn das Verhalten, das Sie ändern möchten, gewollt oder
   auslegungsfähig sein könnte: Eine solche [negative Funktion](../../projekt/unterstuetzung-erhalten.md#negative-funktionen)
   ist etwas, worauf sich andere Websites verlassen; die Änderung braucht daher eine klare Begründung, funktioniert
   am besten als Option statt als neuer Standard und kann mehrere Review-Runden brauchen, bevor sie angenommen wird.
2. **Forken und klonen**: `git clone https://github.com/your-username/exponential.git`, dann `composer install`
   (`CONTRIBUTING.md`, „Getting Started“).
3. **Installieren Sie die Hooks** mit `make hooks`: Der pre-commit-Hook prüft das Manifest, die PHP-Syntax und den
   Code-Stil; der commit-msg-Hook prüft die Nachricht.
4. **Branch anlegen**: `git checkout -b feature/your-feature`.

## Während Sie Code schreiben

| Regel | Quelle |
|---|---|
| Folgen Sie den PSR-Standards und den [Coding-Standards](../../../standards/programmierstandards.md) des Projekts | `CONTRIBUTING.md`, „Making Changes“ |
| Benennen Sie neuen Code nach [der Präfixregel](../../../standards/namenspraefixe-sevenx-und-exp.md): Neuer Kernel-Code verwendet `exp`; vorhandene Bezeichner wie `eZContentObject` oder `eZINI` werden nie umbenannt | ebenda |
| Templates und Stylesheets der Administrationsdesigns folgen [Die Administration gestalten](../../../bc/6.0/admin4-gestaltung.md): kein `<style>`-Block und kein `style=""`-Attribut, gemeinsame Komponenten und Tokens | ebenda |
| Fügen Sie Tests hinzu, die ohne Ihre Änderung fehlschlagen und mit ihr bestehen | `CONTRIBUTING.md`, „Code Reviews“ |
| Eine Änderung pro Pull Request: „Eine Korrektur und ein Refactoring sind zwei Pull Requests“ | ebenda |

## Bevor Sie den Pull Request eröffnen

- **Commit-Nachrichten** beginnen mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: `, zum Beispiel
  `Fixed: The redirect keeps the query string on the own host` (`CONTRIBUTING.md`).
- **Das Manifest**: „Jeder Commit, der eine Datei hinzufügt, ändert oder entfernt, aktualisiert ihre Zeile in
  `share/filelist.md5` (`make manifest-fix`)“.
- **Die Tests bestehen**, und die Dokumentation ist aktualisiert, wenn sich Verhalten geändert hat.
- **Ihr CLA liegt** bei 7x **vor** ([Das CLA erklärt](das-cla-erklaert.md)).

Die Prüfungen, die die CI ausführt, beschreibt [Qualitätsprüfungen](../../../funktionen/6.0/qualitaetspruefungen.md).

## Der Pull Request

1. Pushen Sie Ihren Branch in Ihren Fork und eröffnen Sie einen Pull Request gegen `main`.
2. Füllen Sie die Vorlage aus und kreuzen Sie das CLA-Kästchen an.
3. Rechnen Sie mit Review-Runden: „7x führt deutlich mehr Review-Runden als üblich durch, bevor es einen Commit oder
   Pull Request annimmt, manchmal Hunderte von Überarbeitungen“ (`CONTRIBUTING.md`, „Code Reviews“).
4. **Squashen und rebasen** Sie Ihren Pull Request auf `main`, solange er offen ist; force-pushen Sie nur in Ihren
   eigenen Branch.
5. Nach dem Mergen wird die Historie von `main` nie umgeschrieben; eine spätere Korrektur ist ein neuer Pull Request.

[Reviews geben und erhalten](reviews-geben-und-erhalten.md) enthält Ratschläge für die Review-Runden.

## Code, der kein Kernel-Code ist

| Ihr Code | Wohin er geht | CLA? |
|---|---|---|
| Eine Korrektur an einer Erweiterung mit eigenem Repository | Die Pull Requests dieses Repositorys | Fragen Sie den Maintainer; das CLA deckt Code ab, „den 7x“ in das Projekt „aufnehmen soll“ |
| Ihre eigene Erweiterung, Ihr Theme oder Seitenpaket | Ihr eigenes Repository, Projects, Packagist | Nein ([Erweiterungen und Themes teilen](erweiterungen-und-themes-teilen.md)) |
| Eine Korrektur an einer Bibliothek, von der Exponential abhängt | Das eigene Projekt der Bibliothek | Dessen Regeln |

## Verwandte Seiten

- [Als Community-Mitglied mitwirken](README.md)
- [Code beitragen (Unterstützer)](../../unterstuetzer/code-beitragen.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Mit Pull Requests zurückgeben](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)
- [Umgangsformen bei Pull Requests](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/pull-request-etikette.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
