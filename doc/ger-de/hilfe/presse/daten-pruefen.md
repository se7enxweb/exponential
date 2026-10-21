---
title: Daten prüfen
audience: press
category: press-media
order: 133
summary: Wie Sie Daten zu Exponential prüfen - Veröffentlichungsdaten aus Git-Tags, Daten der Geschichte aus dem Commit-Log und den Geschichtsseiten, die Daten der Organisationen von ihren eigenen Seiten - dazu die Fallen bei Daten (Gründungs- gegenüber Urheberrechtsjahren, Idee gegenüber Start, Tag gegenüber Ankündigung, Zeitzonen) und wie Sie „heute“ schreiben.
keywords: Daten, Daten prüfen, Veröffentlichungsdatum, Datum des Tags, Datum des Commits, Geschichte, Gründungsdatum, Zeitleiste, Zeitzone, Stand, heute, dates, verify dates, release date, tag date, commit date, history, founding date, timeline, time zone, as of, today
type: help
---
# Daten prüfen

Diese Seite ist für Reporter und Faktenprüfer. Die Daten rund um Exponential sind gut festgehalten, aber mehrere davon
werden leicht verwechselt. Diese Seite zeigt, wo jede Art von Datum steht und wie Sie es prüfen.

## Inhalt

1. [Wo jedes Datum steht](#wo-jedes-datum-steht)
2. [Ein Veröffentlichungsdatum prüfen](#ein-veröffentlichungsdatum-prüfen)
3. [Fallen bei Daten](#fallen-bei-daten)
4. [„Heute“ schreiben](#heute-schreiben)

## Wo jedes Datum steht

| Datum | Primäre Quelle |
|---|---|
| Ein Release | Sein Git-Tag; [Release-Übersicht](../../../eng-us/release-overview) |
| Eine Änderung | Der Commit; die Monatsseite in [der Geschichte](../../../eng-us/history/README.md) |
| Eine Funktion in 6.0.15 | Die datierten Abschnitte des [Changelogs 6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Die Umbenennung | <https://exponential.earth/about/project> (11. August 2025); [August 2025](../../../eng-us/history/2025/2025-08.md) (12. August 2025) |
| Die Foundation | <https://foundation.exponential.earth/> (19. März 2026) |
| Die Etappen von 7x | [7x in drei Etappen: 2014, 2022, 2023](7x-in-drei-etappen.md) |
| Angelegte Repositorys | Die GitHub-API, zitiert auf [Wer ist 7x?](../wer-ist-7x.md#fast-dreißig-jahre-die-zeitleiste) |

## Ein Veröffentlichungsdatum prüfen

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
git log -1 --format='%ad' --date=short v6.0.14     # the date of the tagged commit
git for-each-ref --format='%(refname:short) %(creatordate:short)' 'refs/tags/v6.0.*' --sort=version:refname
```

Das Datum des Tags und eine spätere Ankündigung können Tage auseinanderliegen. Das Veröffentlichungsdatum ist das des
Tags; die Ankündigung hat ihr eigenes Datum.

## Fallen bei Daten

| Falle | Richtige Lesart |
|---|---|
| 1998 im Urheberrechtsvermerk | Das erste Jahr eines Urheberrechtsvermerks, kein Gründungsdatum |
| 1999 | Das erste Urheberrechtsjahr von eZ Systems AS im Code |
| 2001, 2014, 2022, 2023 für 7x | Gründung als Brookins Consulting, die Idee, der Beginn des Projekts, der Start |
| 11. oder 12. August 2025 für die Umbenennung | Zuerst die Website, am nächsten Tag das Repository; „August 2025“ deckt beides ab |
| Ein Datum im Changelog 6.0.15 | Wann eine Funktion in die Entwicklung kam, kein Veröffentlichungsdatum |
| Daten im Pressekit | Das Kit ist auf Juni 2026 datiert; es ist älter als neuere Releases ([Das Pressekit](das-pressekit.md)) |
| Zeitzonen | Commits tragen den Zeitversatz ihres Autors; bei einem Tag nahe Mitternacht nennen Sie den Monat oder prüfen den Versatz |

## „Heute“ schreiben

„Derzeit“, „das neueste“ und „heute“ veralten schnell. Schreiben Sie das Datum, an dem Sie geprüft haben:

```text
"As of 18 October 2026, the newest release is 6.0.14."
"6.0.15 was in development when this article was published."
```

## Verwandte Seiten

- [Zeitleiste für Reporter](zeitleiste-fuer-reporter.md)
- [Zahlen prüfen](zahlen-pruefen.md)
- [Leitfaden zum Faktencheck](leitfaden-faktenpruefung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
