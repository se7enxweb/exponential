---
title: Die Geschichtsseiten als Quelle
audience: press
category: press-media
order: 113
summary: Wie Reporter die Geschichte von Exponential nutzen - die Chronik Monat für Monat, das Ledger jedes Commits und die Seite zur Abdeckung -, um eine Änderung zu datieren, ihren Zusammenhang zu finden und sie zu zitieren, mit den Grenzen dessen, was die Geschichte beweisen kann.
keywords: Geschichte, Chronik, Monatsseiten, Ledger, Abdeckung, Commits, Daten, Zusammenhang, Zitieren, Ökosystem, Erweiterungen, Velocity, history, chronicle, month pages, coverage, commits, dates, context, citation, ecosystem, extensions
type: help
---
# Die Geschichtsseiten als Quelle

Diese Seite ist für Journalisten und Autoren langer Texte, die Zusammenhänge suchen: wann sich etwas geändert hat,
warum und was es möglich gemacht hat. Das Repository enthält eine geschriebene Geschichte jeder Änderung seit Dezember
2023. Sie ist eine der stärksten Quellen, die Sie haben, weil jede Aussage darin einen Commit nennt, den jeder öffnen
kann.

## Inhalt

1. [Was die Geschichte ist](#was-die-geschichte-ist)
2. [Die drei Teile](#die-drei-teile)
3. [Sie in einem Beitrag verwenden](#sie-in-einem-beitrag-verwenden)
4. [Wie Sie sie zitieren](#wie-sie-sie-zitieren)
5. [Was die Geschichte nicht beweisen kann](#was-die-geschichte-nicht-beweisen-kann)

## Was die Geschichte ist

Die [Geschichte von Exponential](../../../eng-us/history/README.md) beschreibt sich selbst als „the story of every change made to
Exponential and to the repositories around it since the se7enxweb era began in December 2023“. Sie beginnt mit dem
Monat, in dem „a dormant code base changed hands“ ([Dezember 2023](../../../eng-us/history/2023/2023-12.md)). Für die Jahre davor
verwenden Sie [Wer ist 7x?, Die eZ-Publish-Jahre, 1999 bis 2021](../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021).

## Die drei Teile

| Teil | Was er ist | Am besten für |
|---|---|---|
| [Die Chronik](../../../eng-us/history/README.md#the-chronicle-month-by-month) | Eine Seite pro Monat (arbeitsreiche Monate in zwei geteilt), jede beginnt mit „In short“ | Zusammenhang und die Geschichte eines Zeitraums |
| [Das Ledger](../../../eng-us/history/ledger/README.md) | „the complete, machine-made record of every commit“, mit Datum, Art, Änderung, Dateien und dem Release-Tag, der sie zuerst enthielt | Genaue Daten und welches Release eine Änderung enthält |
| [Die Seite zur Abdeckung](../../../eng-us/history/coverage.md) | Der Beleg, dass jede Änderung erklärt, als klein aufgeführt oder als ohne Nutzen für Anwender gekennzeichnet ist | Zeigen, dass nichts ausgelassen wurde |

Neben der Hauptchronik gibt es Chroniken für das [Plattform-Ökosystem](../../../eng-us/history/ecosystem.md), die
[Erweiterungen](../../../eng-us/history/extensions/README.md) und [Velocity](../../../eng-us/history/velocity/README.md).

## Sie in einem Beitrag verwenden

| Ihre Frage | Wo Sie nachsehen | Beispiel |
|---|---|---|
| Wann hat das Produkt seinen Namen bekommen? | Die Monatsseite | „The product was renamed to Exponential on 12 August 2025“ ([August 2025](../../../eng-us/history/2025/2025-08.md)) |
| Wann hat der Fork begonnen? | Die erste Monatsseite | „The first outside change of the fork was merged on 11 December 2023 (upstream pull request #40)“ ([Geschichte](../../../eng-us/history/README.md)) |
| Wann kam eine Technologie hinzu? | Die Liste „story so far“ | „June 2026: MongoDB 8 as a database, and 6.0.14“ |
| Wie umfangreich war ein Zeitraum? | Die eigene Zählung des Monats | Der August 2026 brachte „149 changes to the root repository on the 6.0.15 line, and no release tag“ ([August 2026](../../../eng-us/history/2026/2026-08.md)) |
| Welcher Commit war es? | Der Commit-Hash auf der Monatsseite | `git show <hash>` in einem Klon |

Jede Monatsseite endet mit verwandten Seiten: den Funktionsseiten, den Spezifikationen, den Hinweisen zum Upgrade und
dem Changelog des Releases. Folgen Sie ihnen, um eine Funktion richtig zu beschreiben.

## Wie Sie sie zitieren

```text
According to the project's documented history (doc/eng-us/history, "August 2025"), the product was renamed to
Exponential on 12 August 2025. https://github.com/se7enxweb/exponential/blob/main/doc/eng-us/history/2025/2025-08.md
```

- Nennen Sie die Monatsseite, nicht nur „die Geschichte“.
- Wenn Sie eine Zählung von einer Monatsseite drucken, nennen Sie den Monat und sagen Sie, was gezählt wurde
  (Änderungen an welchem Repository).
- Setzen Sie Sätze der Monatsseiten in Anführungszeichen; sie sind freie Dokumentation und dürfen mit Link zitiert
  werden ([Freie Dokumentation](../freie-dokumentation.md)).

## Was die Geschichte nicht beweisen kann

| Die Geschichte zeigt | Sie zeigt nicht |
|---|---|
| Was wann in welchem Repository geändert wurde | Wie viele Websites die Änderung nutzen |
| Das Release, das eine Änderung zuerst enthielt | Wann eine bestimmte Website aktualisiert wurde |
| Das Konto, das einen Commit gemacht hat | Wer die Person hinter einem Konto ist, sofern das keine öffentliche Seite sagt |
| Zählungen von Änderungen | Aufwand, Geld oder aufgewendete Stunden |

Zwei Warnungen:

- **Release-Status.** Eine Monatsseite kann Arbeit an der Entwicklungslinie beschreiben. Die Hauptnachricht des Oktober
  2026 lautet „no release tag; they belong to the 6.0.15 line“. Machen Sie aus der Arbeit eines Monats kein Release
  ([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)).
- **Upstream-Geschichte.** Geforkte Repositorys tragen ältere Geschichte mit sich, die das Team von se7enxweb nicht
  gemacht hat. Die Seite „Wer ist 7x?“ zählt „6,556 ledger changes in 53 repositories since December 2023, 1,037 of them
  made by the se7enxweb team, the rest upstream history that the forks carry along“ ([Wer ist 7x?](../wer-ist-7x.md#verantwortung-von-einer-ruhenden-codebasis-zu-exponential-6)).
  Schreiben Sie nicht alles davon 7x zu.

## Verwandte Seiten

- [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md)
- [Daten prüfen](daten-pruefen.md)
- [Richtig über die Geschichte schreiben](../fuersprecher/richtig-ueber-die-geschichte-schreiben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
