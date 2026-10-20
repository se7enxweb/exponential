---
title: "Die Kampagne des Vorjahres wiederverwenden"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1327
summary: Den Export des Vorjahres als Kopie importieren (--strategy=copy), die Texte ändern, es zuordnen.
keywords: wiederverwenden, Kampagne, als Kopie importieren, Strategie copy, Vorlage, reuse, campaign, import copy, strategy copy, template
type: help
---
# Die Kampagne des Vorjahres wiederverwenden

Die Kampagne dieses Jahres beginnt mit dem Layout des Vorjahres.

*Lebenszyklus: Pflegen, Beispiel 28 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie die Kopie im Editor, ändern Sie den Hero und die Listen, klicken Sie auf **Layout veröffentlichen** und ordnen Sie es der neuen Seite zu.

## Auf der Kommandozeile

1. `./console exp:layouts import campaign-2026.zip --strategy=copy --dry-run`, dann ohne `--dry-run`; ohne `--publish` bleibt die Kopie ein Entwurf.

## Welchen Weg Sie wählen

Eine Kopie, damit das Layout des Vorjahres unberührt bleibt.

## Prüfen

- Es gibt zwei Layouts; das alte ist unverändert.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../../../../eng-us/help/build-two-ways/export-and-import-layouts.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
