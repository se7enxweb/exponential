---
title: "Thema: jeder Pull Request wird übernommen"
audience: press
category: press-media
order: 91
summary: Ein Themenbeitrag über die Beitragsrichtlinie von Exponential - jeder Pull Request wird übernommen, nachdem sein Autor ihn über die Prüfrunden hinweg zusammengefasst und auf main rebased hat, und die Historie von main wird nach dem Merge nie umgeschrieben - wie das mit Sicherheit und Stabilität zusammengeht, mit Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Pull Requests, Beiträge, Merge-Richtlinie, Open-Source-Governance, Mitwirkende, Code-Review, Community, CONTRIBUTING, Inklusion, story, pull requests, contributions, merge policy, open source governance, contributors, code review, community, inclusion
type: help
---
# Thema: jeder Pull Request wird übernommen

Diese Seite ist für Journalisten, die über die Steuerung von Open-Source-Projekten berichten. Viele Projekte lehnen die
meisten Beiträge von außen ab; Exponential dokumentiert die gegenteilige Richtlinie.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Jeder Pull Request wird übernommen. Solange er offen ist, prüft 7x ihn in vielen Runden, und sein Autor fasst ihn
zusammen (Squash) und rebased ihn auf `main`, wie es die Prüfungen verlangen; sobald er übernommen ist, wird die
Historie von `main` nie umgeschrieben, und spätere Korrekturen kommen als neue Pull Requests
([Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst), [CI](../../../eng-us/bc/6.0/ci.md)).
Beiträge werden unter derselben GPL-Lizenz veröffentlicht (CONTRIBUTING.md). Seit Dezember 2023 kommen Pull Requests
von 7x, von Entwicklern wie ekked und fwoldt und von Unternehmen wie Netgen und Mugo Web; in der gesamten Historie
erscheinen 1.039 übernommene Pull Requests ([Exponential in Zahlen](../../../eng-us/help/press/exponential-by-the-numbers.md)).

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Jeder Pull Request wird übernommen | [Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst) |
| Solange ein Pull Request offen ist, fasst sein Autor ihn zusammen und rebased ihn auf `main`, wie es die Prüfungen verlangen; nach dem Merge wird `main` nie umgeschrieben, und spätere Korrekturen kommen als neue Pull Requests | ebenda; [Code reviews](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md#code-reviews) |
| Die Testsuite läuft bei jedem Pull Request | [CI](../../../eng-us/bc/6.0/ci.md) |
| Beiträge stehen unter der GPL v2 oder später | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| Code-Beiträge zum Kernel brauchen vor dem Merge eine unterzeichnete Contributor Licensing Agreement (CLA); mit Unterzeichnung und Einreichung übertragen Mitwirkende das Urheberrecht an 7x und geben die Kontrolle ab: 7x darf einen Beitrag auf jede Weise verwenden, ändern, neu lizenzieren und verbreiten, ohne sie zu fragen oder zu bezahlen, und sie können ihn nicht zurückziehen; die eigenen Erweiterungen der Anwender sind nicht betroffen | [Übertragung des Urheberrechts (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla) |
| 1.039 übernommene Pull Requests in der Historie (18. Oktober 2026) | [Exponential in Zahlen](../../../eng-us/help/press/exponential-by-the-numbers.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Keine Code-Prüfung“ | 7x prüft jeden Pull Request in vielen Runden, und sein Autor überarbeitet ihn, fasst ihn zusammen und rebased ihn vor dem Merge; die CI-Tests laufen bei jedem Pull Request |
| „Alles kommt ins Release“ | Probleme werden vor dem Merge im Pull Request behoben oder danach in neuen Pull Requests |
| „Tausende Mitwirkende“ | Zählen Sie aus der Historie, mit Datum |
| „Mitwirkende behalten das Urheberrecht an ihrem Code“ | Das tun sie nicht: Mitwirkende am Kernel übertragen das Urheberrecht unter der CLA an 7x; 7x darf ihn neu lizenzieren und verbreiten, ohne sie zu fragen, und sie können ihn nicht zurückziehen |

## Blickwinkel und Ideen für Überschriften

- „Ein Projekt, das zu jedem Beitrag Ja sagt, und wie es stabil bleibt.“
- „Ihr erster Pull Request wird übernommen: eine offene Tür für Neulinge.“

## Verwandte Seiten

- [Thema: die Community](thema-community.md)
- [Einen ersten Beitrag begleiten](../fuersprecher/einen-ersten-beitrag-begleiten.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
