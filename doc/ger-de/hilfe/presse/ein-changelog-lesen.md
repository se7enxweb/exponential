---
title: Ein Changelog lesen
audience: press
category: press-media
order: 102
summary: Wie eine Changelog-Seite von Exponential aufgebaut ist - Kopf, In short, Upgrading, Added, Updated, Removed, Renamed, Commit-Hashes, die nicht einzeln beschriebenen Änderungen - und wie Sie ihre Zeilen in genaue Sätze verwandeln, ohne sie zu übertreiben.
keywords: Changelog, Release Notes, lesen, Added, Updated, Removed, Renamed, Commit, Hash, In short, Upgrade, Übersetzung, einfache Sprache, changelog, release notes, reading, commit, hash, upgrading, translation, plain language
type: help
---
# Ein Changelog lesen

Diese Seite ist für Reporter und Creator, die zum ersten Mal ein Changelog von Exponential öffnen. Die Changelogs sind
für Menschen geschrieben, die Websites betreiben; sie sind deshalb genau, aber dicht. Hier steht, wie jeder Teil
funktioniert und wie Sie ihn in einfache Sprache übertragen, ohne etwas hinzuzufügen, das die Seite nicht sagt.

## Inhalt

1. [Die Teile einer Changelog-Seite](#die-teile-einer-changelog-seite)
2. [Commit-Hashes](#commit-hashes)
3. [Von der Changelog-Zeile zum Satz](#von-der-changelog-zeile-zum-satz)
4. [Wörter, die Sorgfalt brauchen](#wörter-die-sorgfalt-brauchen)
5. [Die Entwicklungslinie lesen](#die-entwicklungslinie-lesen)

## Die Teile einer Changelog-Seite

Nehmen Sie [das Changelog von 6.0.14](../../../changelogs/6.0/6.0.14.md) als Muster. Jede Seite eines erschienenen
6.0.x-Releases hat dieselben Teile.

| Teil | Beispiel aus 6.0.14 | Was er Ihnen sagt |
|---|---|---|
| Kopf | „Released 2026-06-07. Tag `v6.0.14`.“ | Das Datum und den Tag; zitieren Sie beides |
| Für wen sie gedacht ist | „Read this page if you want to run on MongoDB ...“ | Die Zielgruppe, für die das Release am wichtigsten ist |
| In short | „MongoDB 8 is a database next to MySQL, PostgreSQL and SQLite ...“ | Die Schlagzeile, vom Projekt geschrieben; die sicherste Zusammenfassung zum Zitieren |
| Upgrading | „No action needed for MySQL, PostgreSQL and SQLite sites.“ | Was ein Website-Betreiber tun muss |
| Added | Neue Dateien, Funktionen, Skripte | Etwas, das es vorher nicht gab |
| Updated | Änderungen und Korrekturen am Bestehenden | Verbesserungen, einschließlich Fehlerkorrekturen |
| Removed, Renamed | Entferntes oder Umbenanntes | Mögliche Arbeit für Website-Betreiber |
| Not listed above | „12 changes of this release bring no separate user benefit ...“ | Änderungen, die gezählt, aber nicht beschrieben werden |
| Related pages | Vorheriges und nächstes Release, Monatsseiten der Geschichte | Wo Sie die Geschichte lesen |

Die vier Arten Added, Updated, Removed und Renamed stammen aus den Regeln für Commit-Nachrichten in
[CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), die auch `Fixed:` nennen; die
Changelogs fassen Korrekturen unter Updated zusammen (siehe die Übersicht der [Release Notes der Erweiterungen](../../../changelogs/extensions/README.md):
„Updated (fixes included)“).

## Commit-Hashes

Jede Zeile endet mit einem oder mehreren kurzen Hashes in Backticks, zum Beispiel `af5761fdd8`. Ein Hash bezeichnet die
genaue Änderung in der Git-Historie. Sie müssen ihn nicht drucken, aber er erlaubt Ihnen oder Ihren Lesern, eine
Behauptung zu prüfen:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git show --stat af5761fdd8
```

Auf GitHub öffnet sich dieselbe Änderung unter `https://github.com/se7enxweb/exponential/commit/<hash>`. Wenn Sie einen
Hash drucken, kopieren Sie ihn; tippen Sie ihn nie aus dem Gedächtnis.

## Von der Changelog-Zeile zum Satz

| Changelog-Zeile (6.0.14) | Genauer Satz | Übertreibung, die Sie vermeiden sollten |
|---|---|---|
| „The setup wizard has a database name field, to authenticate against a specific database.“ | „Das Installationsprogramm kann sich jetzt mit einer benannten Datenbank verbinden.“ | „Die Installation wurde neu gebaut.“ |
| „The PostgreSQL setup wizard can be run again on an existing database.“ | „Das Installationsprogramm für PostgreSQL kann auf einer bestehenden Datenbank erneut ausgeführt werden.“ | „Die Unterstützung für PostgreSQL ist neu.“ (Das war sie nicht.) |
| „Default installations use package version 6.0.14 instead of 6.0.10.“ | „Neue Installationen starten mit dem Paket 6.0.14.“ | „Alle werden automatisch aktualisiert.“ |

Faustregeln:

- Behalten Sie den Umfang: Wenn die Zeile eine Datenbank nennt, verallgemeinern Sie nicht auf alle.
- Behalten Sie die Zeitform: Eine Korrektur bringt etwas zum Funktionieren; sie beweist nicht, dass es überall kaputt
  war.
- Verwenden Sie für Zusammenfassungen am besten den Wortlaut von „In short“; zitieren Sie ihn mit Anführungszeichen und
  Link ([Richtlinie für Zitate](richtlinie-fuer-zitate.md)).

## Wörter, die Sorgfalt brauchen

| Wort in einem Changelog | Was es hier bedeutet |
|---|---|
| Updated | Geändert oder korrigiert; nicht automatisch eine neue Funktion |
| Deprecated | Funktioniert noch, soll aber wegfallen; noch nicht entfernt |
| Removed | Ab dieser Version nicht mehr vorhanden |
| Hardening | Verbesserungen der Sicherheit (Härtung); beschreiben Sie sie wie das Changelog, nie als „jetzt sicher“ ([Über Sicherheitskorrekturen berichten](../../../eng-us/help/press/covering-security-fixes.md)) |
| Required packages | Die Versionen anderer Pakete, die der Kernel braucht; Änderungen dort können für Entwickler eine Nachricht sein |
| Behavior change notes | Seiten unter `doc/eng-us/bc/6.0/`, die erklären, was beim Upgrade zu prüfen ist |

## Die Entwicklungslinie lesen

Das [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md) ist anders: Es beginnt mit „Not released yet“, ist nach
Zeitabschnitten statt nach Arten gegliedert und ist lang („four months of work“, vier Monate Arbeit). Lesen Sie die
Liste „How this page is organised“ am Anfang und wählen Sie einen Zeitabschnitt. Über alles darauf muss als „in
Entwicklung“ berichtet werden ([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)).

## Verwandte Seiten

- [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md)
- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Ein Release selbst überprüfen](ein-release-selbst-pruefen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
