---
title: "Die Übertragung von Layouts von Staging in die Produktion planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1027
summary: Auf einer Staging-Installation bauen, dann die Layouts exportieren und importieren; Inhalte werden über Remote-ID und Pfad zugeordnet.
keywords: Staging, Produktion, Export, Import, Remote-ID, Deployment, Planung, staging, production, export, import, remote ID, deployment, planning
type: help
---
# Die Übertragung von Layouts von Staging in die Produktion planen

Layouts erreichen die Produktion, ohne zweimal gebaut zu werden.

*Lebenszyklus: Planen, Beispiel 28 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Dasselbe über **Layouts > Exportieren** und **Layouts > Importieren** in der Administration.

## Auf der Kommandozeile

1. Auf Staging: `./console exp:layouts export --only=<identifiers> --output=release.zip`.
2. Auf der Produktion: `./console exp:layouts import release.zip --dry-run`, dann mit der gewählten `--strategy` und `--publish`.
3. Von Hand ist nichts zu leeren: Seit explayouts_core 1.0.10 leert der Import mit `--publish` den Seiten-Cache selbst.

## Welchen Weg Sie wählen

Planen Sie zuerst die Inhalte (dieselben Remote-IDs auf beiden, zum Beispiel aus demselben Paket), dann die Layouts.

## Prüfen

- Der Probelauf auf der Produktion zeigt `new` oder `changed`, nicht `conflicting`.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
