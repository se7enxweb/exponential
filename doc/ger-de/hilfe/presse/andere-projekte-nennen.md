---
title: Netgen, Mugo Web und CJW Network nennen
audience: press, advocate
category: press-media
order: 35
summary: Welche Teile von Exponential auf der Open-Source-Arbeit anderer Unternehmen aufbauen - das Layout-Konzept und AdminUI von Netgen, der LoveStack von Mugo Web, Newsletter und request-shield von CJW Network, der Qbix-Server, Zeta Components -, wie das Projekt sie nennt und wie Berichte sie nennen sollten.
keywords: Nennung, Quellenangabe, Urheber nennen, Netgen, Netgen Layouts, AdminUI, Mugo Web, LoveStack, CJW Network, cjw_newsletter, request-shield, Qbix, Zeta Components, AdminNeo, Open-Source-Nennung, credit, attribution, open source credit
type: help
---
# Netgen, Mugo Web und CJW Network nennen

Diese Seite ist für Journalisten und Creator, die über Funktionen von Exponential schreiben, die in der
Open-Source-Arbeit eines anderen Unternehmens begannen. Das Projekt nennt diese Unternehmen mit Namen; gute
Berichterstattung tut dasselbe.

## Inhalt

1. [Wer was gebaut hat](#wer-was-gebaut-hat)
2. [Wie das Projekt die Nennung formuliert](#wie-das-projekt-die-nennung-formuliert)
3. [Wie Sie es schreiben](#wie-sie-es-schreiben)
4. [Was Sie nicht andeuten sollten](#was-sie-nicht-andeuten-sollten)

## Wer was gebaut hat

| Unternehmen oder Projekt | Was Exponential verwendet | Quelle |
|---|---|---|
| **Netgen** | Exponential Layouts bringt "the Netgen Layouts concept to the Exponential kernel"; die Distribution Nexus baut auf Netgen Media Site und Netgen Layouts auf; AdminUI ist "a feature complete port of the legendary Netgen AdminUI"; das Repository `se7enxweb/exponential` ist ein GitHub-Fork von `netgen/ezpublish-legacy` | README; [Beitragende und Freunde](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts) |
| **Mugo Web** | Die LoveStack-Verbesserungen, in 6.0.15 neu gebaut "with full credit to Mugo Web" | README, „Exponential Distribution Integration: LoveStack“ |
| **CJW Network** | `cjw_newsletter`; eine CJW-Variante von Nexus; request-shield, dokumentiert als Begleiter vor dem HTTP-Cache ("a project of CJW Network ... not of the Foundation") | [Begleitprojekte](../wer-ist-die-exponential-foundation.md#begleitprojekte) |
| **Qbix** | Exponential Velocity "began as a fork of the Qbix web server engine" | README von Velocity; [Exponential Velocity](../wer-ist-7x.md#exponential-velocity) |
| **Zeta Components** | Die Bibliotheken des Kernels; 7x pflegt Forks und hat Sicherheits-Pull-Requests an das Upstream-Projekt geschickt | [Der PHP-Stack, auf dem es steht](../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt) |
| **AdminNeo** | Der Datenbank-Quelltexteditor `sevenx_dse` | [sevenx_dse](../../../eng-us/features/6.0/extensions/sevenx_dse.md) |
| **eZ Systems** und Hunderte von Beitragenden | Die Codebasis selbst, 1999 bis 2021 | COPYRIGHT.md; [Die Jahre von eZ Publish](../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021) |

## Wie das Projekt die Nennung formuliert

- "with full credit to Mugo Web" (README).
- "brings the Netgen Layouts concept to the Exponential kernel" (README).
- "It began as a fork of the Qbix web server engine, and the two projects have since drifted far apart" (README von Velocity).

## Wie Sie es schreiben

```text
„Exponential Layouts, ein Seitenbaukasten, der das Konzept von Netgen Layouts auf Exponential überträgt, ...“
„Die Linie 6.0.15 baut die LoveStack-Verbesserungen von Mugo Web neu, mit Nennung des Unternehmens.“
„Velocity begann als Fork des Qbix-Webservers und hat sich seitdem eigenständig entwickelt.“
```

## Was Sie nicht andeuten sollten

| Vermeiden Sie | Warum |
|---|---|
| „Der Seitenbaukasten von Netgen für Exponential“ | Exponential Layouts ist eine Portierung des Konzepts mit eigenem Namen und eigenem Code |
| „von Netgen gebaut“ für Exponential | Netgen hat Pull Requests beigetragen, und seine Arbeit wurde portiert; 7x leitet Exponential |
| „Mugo Web schließt sich Exponential an“ | Die Quellen dokumentieren die Wiederverwendung von Open-Source-Arbeit, keine Geschäftsbeziehung |
| „request-shield ist Teil von Exponential“ | Es ist ein Projekt von CJW Network, dokumentiert als Begleiter |
| Jede Empfehlung durch diese Unternehmen | Keine ist dokumentiert; bitten Sie sie um eine Stellungnahme, wenn Ihr Beitrag sie braucht |

## Verwandte Seiten

- [Marke und Namen für Medien](marke-und-namen-fuer-medien.md)
- [Thema: Exponential Layouts, der Seitenbaukasten](thema-exponential-layouts.md)
- [Andere Projekte und Unternehmen respektieren](../fuersprecher/andere-projekte-respektieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
