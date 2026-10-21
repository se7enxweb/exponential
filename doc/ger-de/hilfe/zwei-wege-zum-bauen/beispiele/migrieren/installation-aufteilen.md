---
title: "Eine Installation in zwei Sites aufteilen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1523
summary: Die Layouts des Teilbaums der Site exportieren und auf der neuen Installation nach deren Inhalten importieren.
keywords: Site aufteilen, Export, Import, Teilbaum, Migration, split site, export, import, subtree, migration
type: help
---
# Eine Installation in zwei Sites aufteilen

Bold Agency zieht auf eine eigene Installation um.

*Lebenszyklus: Migrieren, Beispiel 24 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Entfernen Sie die Zuordnungen von Bold Agency auf der alten Installation, sobald die neue live ist.

## Auf der Kommandozeile

1. `./console exp:layouts export --only=<Bold Agency identifiers> --output=bold.zip` (ihre gemeinsamen Layouts kommen mit).
2. Auf der neuen Installation, nach den Inhalten: Probelauf, Import (mit `--publish` leert er den Seiten-Cache selbst).

## Welchen Weg Sie wählen

Bezeichner machen die Auswahl genau.

## Prüfen

- Die neue Installation zeigt die Seiten von Bold Agency mit ihren Layouts.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
