---
title: Beiträge zur Geschichte schreiben
audience: advocate
category: advocacy
order: 54
summary: Wie Sie Beiträge wie „Diesen Monat in der Geschichte von Exponential“ und andere Beiträge zur Geschichte aus der eigenen Chronik und dem Änderungsbuch des Projekts schreiben - einen Monat oder ein Thema wählen, Commits zitieren, erklären, was möglich wurde, den alten Namen richtig verwenden - mit einer Vorlage.
keywords: Geschichte, diesen Monat in der Geschichte, Chronik, Änderungsbuch, Commits, Jahrestag, Zeitleiste, Jahre von eZ Publish, Erzählen, Beiträge, Vorlage, history, this month in history, chronicle, ledger, commits, anniversary, timeline, storytelling, posts, template
type: help
---
# Beiträge zur Geschichte schreiben

Diese Seite ist für Fürsprecher, die Geschichten mögen. Exponential hat eine ungewöhnlich vollständige, öffentliche
Geschichte: Jede Änderung seit Dezember 2023 wird Monat für Monat in der Chronik erzählt und im Änderungsbuch
festgehalten, und die Historie des Repositorys reicht bis 2002 zurück. Beiträge zur Geschichte zeigen, dass das Projekt
lebt, und erklären, warum es Funktionen gibt.

## Inhalt

1. [Die Quellen](#die-quellen)
2. [Ideen für Beiträge](#ideen-für-beiträge)
3. [Wie Sie einen schreiben](#wie-sie-einen-schreiben)
4. [Die Vorlage](#die-vorlage)
5. [Regeln für die Geschichte](#regeln-für-die-geschichte)

## Die Quellen

| Quelle | Was sie Ihnen gibt |
|---|---|
| [Die Geschichte](../../../eng-us/history/README.md) | „Die Geschichte jeder Änderung ... seit Beginn der Ära von se7enxweb im Dezember 2023“, Monat für Monat |
| [Das Änderungsbuch](../../../eng-us/history/ledger/README.md) | Die vollständige Aufzeichnung der Commits pro Repository |
| [Das Ökosystem](../../../eng-us/history/ecosystem.md) | Änderungen in den verwandten Repositorys |
| [Wer ist 7x?, Fast dreißig Jahre](../wer-ist-7x.md#fast-dreißig-jahre-die-zeitleiste) | Die lange Zeitleiste |
| [Wer ist 7x?, Die Jahre von eZ Publish](../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021) | Die Jahre vor 2021 |
| [Zeitleiste](../wer-ist-die-exponential-foundation.md#zeitleiste) | Wichtige Daten mit Quellen |
| `git log` in einem Klon | Jeder Commit seit 2002 |

Jede Monatsseite beginnt mit **In short** und endet mit verwandten Seiten; jede Änderung trägt ihren Commit-Hash, den
Leser mit `git show <hash>` öffnen können ([Die Geschichte, Kurz gesagt](../../../eng-us/history/README.md#in-short)).

## Ideen für Beiträge

| Idee | Beginnen Sie bei |
|---|---|
| „Dieser Monat vor zwei Jahren“ | Der Monatsseite von vor zwei Jahren |
| „Wie SQLite zu Exponential kam“ | [Januar 2024](../../../eng-us/history/2024/2024-01a.md) |
| „Der Monat, in dem das Produkt seinen Namen bekam“ | [August 2025](../../../eng-us/history/2025/2025-08.md) |
| „Von PHP 7 zu PHP 8.5“ | [April 2025](../../../eng-us/history/2025/2025-04.md), [Dezember 2025](../../../eng-us/history/2025/2025-12.md) |
| „Die Geschichte von Velocity“ | [Chronik von Velocity](../../../eng-us/history/velocity/README.md) |
| „Ein Jahr Releases“ | [Wer ist 7x?, Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x) |
| „Der erste Beitrag von außen“ | [Dezember 2023](../../../eng-us/history/2023/2023-12.md) |

## Wie Sie einen schreiben

1. Lesen Sie die Monatsseite vollständig, dann die Commits, die Sie erwähnen werden (`git show <hash>`).
2. Wählen Sie drei Änderungen, die ein Leser heute noch nutzen kann.
3. Zu jeder: was sich geändert hat, warum es wichtig war, was man heute tun kann. Verlinken Sie die Funktionsseite.
4. Fügen Sie einen Satz Zusammenhang hinzu: was damals sonst im Projekt geschah.
5. Schließen Sie mit einem Link zur Monatsseite, damit Leser den Rest lesen können.

## Die Vorlage

```text
# This month in Exponential history: <Month Year>

<One sentence: what the month brought, from the "In short" of the month page.>

## <Change 1>
<What changed and why it mattered.> You can still use it: <feature page>. Commit: `<hash>`.

## <Change 2>
...

## Read more
The full month: <link to the month page>. Every change of that time is in the ledger: <link>.

<Disclosure line.>
```

## Regeln für die Geschichte

- **Verwenden Sie die Fakten der Chronik**, nicht Ihre Erinnerung an die Zeit.
- **Der alte Name nur in der Vergangenheitsform**: „als die Software noch eZ Publish hieß“
  ([Richtig über die Geschichte schreiben](richtig-ueber-die-geschichte-schreiben.md)).
- **Nennen Sie Menschen nur so, wie es die Quellen tun.** In der Geschichte und unter
  [Wer ist 7x?](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts) genannte Beitragende dürfen so genannt werden wie
  dort; fügen Sie keine privaten Details hinzu.
- **Keine Spekulation** darüber, warum jemand etwas getan hat, es sei denn, eine Quelle sagt es.
- **Respektieren Sie frühere Unternehmen.** Die Arbeit von eZ Systems ist Teil des Codes; sprechen Sie mit Respekt davon
  ([Andere Projekte und Unternehmen respektieren](andere-projekte-respektieren.md)).

## Verwandte Seiten

- [Gesprächspunkte: Langlebigkeit und Geschichte](gespraechspunkte-langlebigkeit-und-geschichte.md)
- [Über ein Release schreiben](ueber-ein-release-schreiben.md)
- [Die Geschichte von Exponential](../../../eng-us/history/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
