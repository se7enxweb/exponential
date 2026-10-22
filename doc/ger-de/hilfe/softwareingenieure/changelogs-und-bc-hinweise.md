---
title: Changelogs und Upgrade-Hinweise
audience: software-engineer, developer
category: software-engineering
order: 620
summary: Wo Exponential festhält, was sich geändert hat und was eine Installation dagegen tun muss - die Changelog-Seiten je Version und je Erweiterung, die Abschnitte Added, Updated, Removed und Renamed, die Upgrade-Hinweise in doc/eng-us/bc/ mit ihrer Tabelle In short, die Checklisten je Zeitraum und das Geschichtsbuch - und wie Sie einen Eintrag für Ihre eigene Änderung schreiben.
keywords: Changelog, Release Notes, Upgrade-Hinweise, Rückwärtskompatibilität, bc, doc/changelogs, Added, Updated, Removed, Renamed, Tabelle In short, Verhaltensänderungen, Geschichtsbuch, Softwareingenieur, changelog, release notes, upgrade notes, backward compatibility, behavior changes, history ledger
type: help
---
# Changelogs und Upgrade-Hinweise

Diese Seite richtet sich an Ingenieure, die die Aufzeichnung einer Änderung schreiben, und an jene, die sie vor einem
Upgrade lesen. Exponential führt zwei Aufzeichnungen mit unterschiedlichen Aufgaben: Das Changelog sagt, **was** sich
geändert hat, die Upgrade-Hinweise sagen, **was dagegen zu tun ist**.

## Inhalt

1. [Wo die Aufzeichnungen liegen](#wo-die-aufzeichnungen-liegen)
2. [Das Changelog](#das-changelog)
3. [Upgrade-Hinweise](#upgrade-hinweise)
4. [Die Aufzeichnung Ihrer Änderung schreiben](#die-aufzeichnung-ihrer-änderung-schreiben)
5. [Die Aufzeichnung vor einem Upgrade lesen](#die-aufzeichnung-vor-einem-upgrade-lesen)

## Wo die Aufzeichnungen liegen

| Aufzeichnung | Ort | Beispiel |
|---|---|---|
| Kernel-Changelog je Version | `doc/changelogs/<series>/<version>.md` | [6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Changelogs der Erweiterungen | `doc/changelogs/extensions/<name>.md` | [Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md) |
| Upgrade-Hinweise je Änderung | `doc/eng-us/bc/<series>/` | [Vertrauenswürdige Proxys](../../bc/6.0/vertrauenswuerdige-proxys.md) |
| Checklisten je Zeitraum | `doc/eng-us/bc/6.0/behaviour-changes-*.md` | [Verhaltensänderungen vom 1. und 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md) |
| Funktionsseiten | `doc/eng-us/features/6.0/` | [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md) |

Ältere Serien bis zurück zu 2.9 werden in `doc/changelogs/` für die Geschichte aufbewahrt.

## Das Changelog

- Eine Seite je Release; Einträge gruppiert als **Added**, **Updated**, **Removed** und **Renamed** (die Seite für 6.0.15
  zusätzlich nach Zeitraum, weil die Linie lang ist).
- Jede Seite beginnt mit **Upgrading**, das die Upgrade-Hinweise in der Reihenfolge verlinkt, in der sie zu lesen sind.
- Einträge werden aus den Betreffzeilen der Commits zusammengestellt, die dieselben Präfixe verwenden (`Added:`,
  `Updated:`, `Fixed:`, `Removed:`, `Renamed:`); Korrekturen erscheinen im Changelog von Velocity unter Updated.
- Eine Seite für eine Version in Entwicklung sagt das in ihrer ersten Zeile ("Not released yet").

## Upgrade-Hinweise

Jede Änderung, die von einer Installation eine Handlung verlangt oder sich ungefragt anders verhält, erhält eine Seite in
`doc/eng-us/bc/<series>/`. Eine Seite beginnt mit einer Tabelle **In short**:

| Zeile | Beantwortet |
|---|---|
| What changed | das Verhalten davor und danach |
| Who is affected | welche Installationen, Erweiterungen oder Benutzer |
| How to check | einen Befehl oder eine Seite, die zeigt, ob Sie betroffen sind |
| How to fix | was zu tun ist oder wie Sie es bewusst rückgängig machen |

Änderungen, die viele Bereiche betreffen, werden je Zeitraum mit einer Reihenfolge für die Bereitstellung und einer
Checklistentabelle gesammelt. Siehe [Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md), was als
solche Änderung zählt.

## Die Aufzeichnung Ihrer Änderung schreiben

1. Schreiben Sie die Betreffzeile des Commits mit dem richtigen Präfix; der Text sagt warum und was
   ([Git-Arbeitsablauf](git-arbeitsablauf.md)).
2. Fügen Sie den Eintrag der Changelog-Seite der Version in Entwicklung hinzu, unter dem passenden Abschnitt.
3. Muss eine Installation handeln, schreiben Sie den Upgrade-Hinweis mit den vier Zeilen und verlinken Sie ihn aus dem
   Abschnitt Upgrading des Changelogs.
4. Eine neue Einstellung erhält ihren Abschnitt auf der Funktionsseite und eine Zeile im Changelog.
5. Nennen Sie das Produkt Exponential, und lassen Sie Bezeichner wie `eZINI` unverändert.
6. Prüfen Sie die Seiten mit `exp:help check` ([Dokumentation als Code](dokumentation-als-code.md)).

## Die Aufzeichnung vor einem Upgrade lesen

Lesen Sie den Abschnitt Upgrading jeder Version, die Sie durchlaufen, dann die Checkliste jedes Zeitraums in der
angegebenen Reihenfolge, dann die Changelogs der Erweiterungen, die Sie verwenden. Die Upgrade-Anleitung führt durch
([Aktualisieren](../../anleitungen/aktualisieren.md)).

## Verwandte Seiten

- [Changelogs, für Maintainer von Erweiterungen](../erweiterungsentwicklung/teil-7-veroeffentlichen/changelogs.md)
- [Commit-Nachrichten](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md)
- [Release Engineering](release-engineering.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
