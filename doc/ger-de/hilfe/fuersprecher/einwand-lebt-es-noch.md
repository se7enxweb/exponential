---
title: "Einwand: „Lebt es noch?“"
audience: advocate
category: advocacy
order: 31
summary: Wie Sie „Ist Exponential nicht tot?“ oder „Ist das nicht das eingestellte Produkt?“ mit prüfbaren Belegen beantworten - Releases, Commits, CI, die Geschichte, die Organisationen dahinter - dazu Antwortvorlagen und was Sie nicht sagen sollten.
keywords: Einwand, tot, verlassen, eingestellt, Ende der Lebensdauer, gepflegt, Releases, Aktivität, eZ Publish, lebt noch, Belege, Geschichte, objection, dead, abandoned, discontinued, end of life, maintained, releases, activity, still alive, evidence, history
type: help
---
# Einwand: „Lebt es noch?“

Diese Seite ist für Fürsprecher, die hören: „Ist das nicht tot?“, „Wurde eZ Publish nicht eingestellt?“ oder „Als ich
zuletzt geschaut habe, passierte nichts.“ Es ist der häufigste Zweifel und der am leichtesten zu beantwortende, denn
die Belege sind öffentlich und jeder kann sie in Minuten prüfen.

## Inhalt

1. [Woher der Zweifel kommt](#woher-der-zweifel-kommt)
2. [Die Belege](#die-belege)
3. [Lassen Sie es prüfen](#lassen-sie-es-prüfen)
4. [Antwortvorlagen](#antwortvorlagen)
5. [Sagen Sie nicht](#sagen-sie-nicht)

## Woher der Zweifel kommt

Der Zweifel ist berechtigt. Die Codebasis wurde viele Jahre unter dem Namen eZ Publish entwickelt, und ihr Hersteller
hat dieses Produkt nach fast achtzehn Jahren eingestellt
([Exponential 6](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel)). Die eigenen Quellen des Projekts
sagen: „Die Plattform, die ursprünglich als eZ Publish bekannt war, erreichte 2021 das Ende ihrer Lebensdauer. Wir
haben weitergemacht“ ([Zeitleiste](../wer-ist-die-exponential-foundation.md#zeitleiste)). Wer in diesen Jahren aufgehört
hat, das Projekt zu verfolgen, hat allen Grund zu fragen.

## Die Belege

| Beleg | Quelle |
|---|---|
| 6.0.0 am 1. Januar 2024 bis 6.0.14 am 7. Juni 2026: fünfzehn Releases, 6.0.15 in Entwicklung | [Wer ist 7x?, Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x) |
| „6556 Änderungen im Änderungsbuch in 53 Repositorys seit Dezember 2023“ | [Mit Einwänden umgehen, Wird es gepflegt?](../geschaeft/einwandbehandlung.md#wird-es-gepflegt) |
| Tests laufen bei jedem Push und Pull Request, auf PHP 8.1 bis 8.5 | [CI](../../bc/6.0/ci.md), [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| Was die aktuelle Linie hinzufügt: Velocity, admin4, Prüfprotokoll, Layouts, Exponential Intelligence, die Hilfe | [Änderungsprotokoll 6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Jeder Monat Arbeit wird in der Geschichte erzählt | [Die Geschichte](../../geschichte/README.md) |
| Ein Unternehmen leitet es (7x), und eine gemeinnützige Organisation betreibt es (die Exponential Foundation, gegründet 2026) | [Die Foundation und 7x](../wer-ist-die-exponential-foundation.md#die-foundation-und-7x) |
| Pull Requests kommen von mehreren Organisationen und werden übernommen | [Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst) |

## Lassen Sie es prüfen

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git tag -l 'v6.0.*' --sort=version:refname   # the releases
git log -10 --format='%ad %s'                 # the latest changes, with dates
```

Oder ohne Terminal: die [Releases auf GitHub](https://github.com/se7enxweb/exponential/releases) und der jüngste Monat
der [Geschichte](../../geschichte/README.md).

## Antwortvorlagen

```text
SHORT (social media)
It's very much alive: fifteen 6.0.x releases since Jan 2024, 6.0.15 in development, tests on every push.
Releases: https://github.com/se7enxweb/exponential/releases

FORUM
Fair question - the code base was developed for years as eZ Publish, which its creator discontinued.
7x took over leadership in 2023, and since 1 January 2024 there have been fifteen 6.0.x releases
(6.0.0 to 6.0.14), with 6.0.15 in development. Every month is documented in the history pages in doc/eng-us/history,
and you can check the tags yourself: git tag -l 'v6.0.*' --sort=version:refname
Since 2026 a nonprofit, the Exponential Foundation, operates the project.

IN PERSON
"The original vendor stopped, but the code is free, so it carried on. There have been fifteen releases
since January 2024 - I can show you the list on my phone."
```

## Sagen Sie nicht

- „Es ist lebendiger denn je.“ Vage; nennen Sie stattdessen die Fakten.
- „eZ Publish ist jetzt Exponential von eZ Systems.“ Falsch: keine Verbindung zu eZ Systems oder Ibexa
  ([Wer ist 7x?, Kurz gesagt](../wer-ist-7x.md#kurz-gesagt)).
- „Tausende Unternehmen nutzen es noch.“ Keine Quelle zählt Benutzer.
- „Jeden Monat ein Release.“ Nennen Sie die tatsächlichen Daten.

## Verwandte Seiten

- [Gesprächspunkte: Langlebigkeit und Geschichte](gespraechspunkte-langlebigkeit-und-geschichte.md)
- [Die Geschichte von Exponential in zwei Minuten](die-geschichte-in-zwei-minuten.md)
- [Einwände als Fürsprecher beantworten](einwaende-beantworten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
