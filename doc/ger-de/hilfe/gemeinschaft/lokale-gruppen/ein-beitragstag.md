---
title: Ein Beitragstag
audience: community, advocate, trainer, developer
category: community
order: 72
summary: Wie eine lokale Gruppe oder ein Unternehmen einen Beitragstag für Exponential durchführt - Aufgaben und Testinstallationen vorbereiten, ein Zeitplan, Neulinge mit erfahrenen Mitgliedern zusammenbringen, die Regeln, denen jeder Beitrag folgt, und die Nacharbeit, damit die Arbeit gemergt wird.
keywords: Beitragstag, Sprint, Hackathon, gemeinsamer Beitrag, Aufgaben, Pull Requests, Übersetzungen, Hilfeseiten, Testinstallation, Paare, Nacharbeit, contribution day, hackathon, group contribution, tasks, pairing, follow-up
type: help
---
# Ein Beitragstag

Ein Beitragstag sind einige Stunden, in denen eine Gruppe gemeinsam echte Beiträge leistet: Bestätigungen von Fehlern,
Übersetzungen, Korrekturen von Hilfeseiten, kleine Pull Requests.

## Inhalt

1. [Vor dem Tag](#vor-dem-tag)
2. [Zeitplan](#zeitplan)
3. [Aufgabenliste](#aufgabenliste)
4. [Regeln für jeden Beitrag](#regeln-für-jeden-beitrag)
5. [Nach dem Tag](#nach-dem-tag)

## Vor dem Tag

- Sammeln Sie 10 bis 20 kleine Aufgaben, die sich jeweils in ein oder zwei Stunden abschließen lassen (siehe
  [Aufgabenliste](#aufgabenliste)).
- Bitten Sie die Teilnehmenden, ein GitHub-Konto anzulegen und für Code eine Testinstallation aus einem Git-Klon auf
  SQLite einzurichten ([Mit Git entwickeln](../../mit-git-entwickeln.md)).
- Informieren Sie Teilnehmende, die Kernel-Code senden werden, vorab über das CLA; Unterschreiben und Zustellen
  brauchen Zeit ([Das CLA erklärt](../mitwirken/das-cla-erklaert.md)).
- Nennen Sie eine Ansprechperson für Probleme ([Sicherheit und Datenschutz in der Community](sicherheit-und-datenschutz-in-der-community.md)).

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 | Begrüßung, Verhaltenskodex, wie der Tag abläuft |
| 0:15 | Hilfe bei der Einrichtung; Paare bilden (eine erfahrene, eine neue Person) |
| 0:45 | Arbeit an den Aufgaben |
| 2:45 | Jedes Paar zeigt, was es getan hat |
| 3:15 | Offene Pull Requests und Issues werden für die Nacharbeit aufgelistet |

## Aufgabenliste

| Art | Beispiel | Seite |
|---|---|---|
| Fehler bestätigen | Drei offene Issues auf einer frischen Installation nachstellen | [Issues nachstellen und bestätigen](../../fuersprecher/issues-nachstellen-und-bestaetigen.md) |
| Übersetzungen | Die unübersetzten Meldungen eines Bildschirms fertigstellen | [Übersetzungen beitragen](../mitwirken/uebersetzungen-beitragen.md) |
| Hilfeseiten | Einer Hilfeseite Schritt für Schritt folgen und korrigieren, was falsch ist | [Dokumentation und Hilfeseiten beitragen](../mitwirken/dokumentation-und-hilfeseiten-beitragen.md) |
| Paketierung | Einer Erweiterung, der eine `composer.json` fehlt, eine hinzufügen, ein Anliegen der Roadmap | [Roadmap](../../projekt/roadmap.md) |
| Code | Eine kleine Korrektur mit einem Test | [Code beitragen](../mitwirken/code-beitragen.md) |
| Tests | `main` auf einer anderen PHP-Version oder Datenbank installieren | [Releases testen](../mitwirken/releases-testen.md) |

## Regeln für jeden Beitrag

- Commit-Nachrichten mit Präfix und die Manifestzeile (`CONTRIBUTING.md`).
- Keine Geheimnisse oder personenbezogenen Daten in Issues oder Pull Requests.
- KI-gestützte Arbeit wird von der Person geprüft, die sie einreicht, und gekennzeichnet, wenn ein wesentlicher Teil
  generiert wurde (`CODE_OF_CONDUCT.md`).
- Eine Änderung pro Pull Request.

## Nach dem Tag

- Pull Requests erhalten nach dem Tag Review-Runden; vereinbaren Sie, wer sich jeweils darum kümmert.
- Veröffentlichen Sie eine kurze Notiz über das Erreichte, ohne Namen, sofern die Menschen nicht zugestimmt haben.
- Danken Sie den Teilnehmenden öffentlich, sobald die Arbeit gemergt ist.

Größere, mehrtägige Veranstaltungen: [Einen Beitragssprint vorschlagen](../../fuersprecher/einen-beitrags-sprint-vorschlagen.md).

## Verwandte Seiten

- [Lokale Gruppen und Community-Veranstaltungen](README.md)
- [Wege mitzuwirken](../mitwirken/wege-mitzuwirken.md)
- [Mentoring](../anderen-helfen/mentoring.md)
- [Ablauf eines praktischen Workshops](../../fuersprecher/ablaufplan-praxis-workshop.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
