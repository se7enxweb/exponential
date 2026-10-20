---
title: "Gesprächspunkte: Langlebigkeit und Geschichte"
audience: advocate
category: advocacy
order: 24
summary: Wie Sie die lange Geschichte von Exponential als Stärke nutzen - eine seit 1999 gepflegte Codebasis, eine öffentliche Geschichte seit 2002, die Verantwortung von 7x seit 2023, die Foundation seit 2026 - mit belegten Punkten, der Zeitleiste und den ehrlichen Grenzen zur Zukunft.
keywords: Gesprächspunkte, Langlebigkeit, Geschichte, Zeitleiste, Nachhaltigkeit, Jahre von eZ Publish, 7x, Brookins Consulting, Exponential Foundation, Releases, Änderungsbuch, Zukunft, Überleben, talking points, longevity, history, timeline, sustainability, releases, ledger, future, survival
type: help
---
# Gesprächspunkte: Langlebigkeit und Geschichte

Diese Seite ist für Fürsprecher, denen die Frage begegnet „Wird es das noch geben?“, und für diejenigen, die gern die
Geschichte erzählen. Die Geschichte von Exponential ist lang und öffentlich. Nutzen Sie sie als Beleg, nicht als
Nostalgie, und versprechen Sie nie die Zukunft.

## Inhalt

1. [Die Punkte](#die-punkte)
2. [Eine Zeitleiste zum Zeigen](#eine-zeitleiste-zum-zeigen)
3. [Wie Sie den alten Namen einordnen](#wie-sie-den-alten-namen-einordnen)
4. [Die ehrlichen Grenzen](#die-ehrlichen-grenzen)
5. [Machen Sie es konkret](#machen-sie-es-konkret)

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „Der Copyright-Hinweis beginnt 1998 und 1999; die Codebasis wird seit mehr als 25 Jahren entwickelt.“ | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| „Die Historie des Repositorys reicht bis zum 2. September 2002 zurück, und jeder kann sie lesen.“ | [Wer ist 7x?, 7x in Zahlen](../wer-ist-7x.md#7x-in-zahlen) |
| „Exponential hat es über sein Lebensende 2021 hinaus geschafft und überlebt.“ (im Original wörtlich zitiert) | [Wer ist 7x?, Kurz gesagt](../wer-ist-7x.md#kurz-gesagt) |
| „7x hat 2023 die Leitung übernommen und seit dem 1. Januar 2024 fünfzehn Releases 6.0.x veröffentlicht.“ | [Wer ist 7x?, Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x) |
| „Eine gemeinnützige Organisation, die Exponential Foundation, wurde im März 2026 gegründet, um das Projekt zu betreiben.“ | [Was die Foundation ist](../wer-ist-die-exponential-foundation.md#was-die-foundation-ist) |
| „Jede Änderung seit Dezember 2023 wird Monat für Monat in der Geschichte erzählt und in Änderungsbüchern festgehalten.“ | [Die Geschichte](../../../en/us/history/README.md), [Dokumentation](../wer-ist-die-exponential-foundation.md#dokumentation) |
| „Die GPL bedeutet, dass der Code jedes Unternehmen überleben kann. Er hat seinen ursprünglichen Hersteller schon einmal überlebt.“ | [Mit Einwänden umgehen, Langlebigkeit](../geschaeft/einwandbehandlung.md#langlebigkeit-gibt-es-das-in-zehn-jahren-noch) |

## Eine Zeitleiste zum Zeigen

| Wann | Was | Quelle |
|---|---|---|
| 1999 bis 2014 | Copyright-Jahre von eZ Systems AS im Code | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| 2002 | Ältester Commit im Repository | [7x in Zahlen](../wer-ist-7x.md#7x-in-zahlen) |
| 2006 | eZpedia startet | [Zeitleiste](../wer-ist-die-exponential-foundation.md#zeitleiste) |
| 2021 | Die Plattform erreicht das Ende ihrer Lebensdauer; die Codebasis lebt weiter | ebenda |
| Dezember 2023 | Das Projekt von 7x startet und übernimmt die Codebasis | ebenda |
| 1. Januar 2024 | 6.0.0 | ebenda |
| August 2025 | Das Produkt wird zu Exponential | ebenda |
| 19. März 2026 | Die Exponential Foundation wird gegründet | ebenda |
| 7. Juni 2026 | 6.0.14 | ebenda |
| September und Oktober 2026 | Velocity kommt hinzu; Exponential Intelligence und die Hilfe in der Linie 6.0.15 | ebenda |

Die vollständige Tabelle steht in [Wer ist die Exponential Foundation?, Zeitleiste](../wer-ist-die-exponential-foundation.md#zeitleiste);
die lange Geschichte in [Wer ist 7x?](../wer-ist-7x.md#fast-dreißig-jahre-die-zeitleiste).

## Wie Sie den alten Namen einordnen

- Sagen Sie: „Die Codebasis wurde viele Jahre unter dem Namen eZ Publish entwickelt, einem Produkt, das sein Hersteller
  nach fast achtzehn Jahren eingestellt hat. Exponential hat sie fortgeführt.“
- Sagen Sie nicht: „Exponential ist eZ Publish“, „das neue eZ Publish“ oder irgendetwas, das eine heutige Verbindung zu
  eZ Systems oder Ibexa nahelegt.

Details: [Richtig über die Geschichte schreiben](richtig-ueber-die-geschichte-schreiben.md).

## Die ehrlichen Grenzen

- **Niemand kann die Zukunft versprechen.** „Die bisherige Geschichte und die Lizenz geben Ihnen Möglichkeiten; niemand
  kann garantieren, was geschieht.“
- **Governance und Budget der Foundation sind in den Quellen nicht dokumentiert.**
- **Alter ist allein keine Qualität.** Verbinden Sie die Geschichte mit den aktuellen Belegen: Releases, Tests,
  Sicherheitsprozess.

## Machen Sie es konkret

Bieten Sie dem Zuhörer eine Prüfung in fünf Minuten an:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git log --reverse --format='%ad %an: %s' | head -3   # the oldest changes
git tag -l 'v6.0.*' --sort=version:refname          # the 6.0.x releases
git log -5 --format='%ad %s'                        # this week's changes
```

Oder verweisen Sie auf einen Monat in [der Geschichte](../../../en/us/history/README.md) und lassen Sie lesen, was sich geändert hat.

## Verwandte Seiten

- [Die Geschichte von Exponential in zwei Minuten](die-geschichte-in-zwei-minuten.md)
- [Einwand: „Lebt es noch?“](einwand-lebt-es-noch.md)
- [Beiträge zur Geschichte schreiben](beitraege-zur-geschichte-schreiben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
