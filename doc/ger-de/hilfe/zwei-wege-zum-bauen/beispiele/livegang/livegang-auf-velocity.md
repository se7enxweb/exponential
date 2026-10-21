---
title: "Livegang auf Exponential Velocity"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1223
summary: Auf Velocity brauchen Änderungen an Einstellungen exp:velocity restart (wie exp:ini sagt); planen Sie es in die Schritte des Livegangs ein.
keywords: Velocity, Livegang, Neustart, Einstellungen, Worker, launch, restart, settings, workers
type: help
---
# Livegang auf Exponential Velocity

Am Tag des Livegangs geänderte Einstellungen wirken auch auf einer Site, die Velocity ausliefert.

*Lebenszyklus: Livegang, Beispiel 24 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Das Veröffentlichen von Layouts braucht keinen Neustart.

## Auf der Kommandozeile

1. Führen Sie, nachdem `exp:ini` geschrieben hat, `./console exp:velocity restart` aus, wie sein Hinweis sagt: *Velocity workers keep the settings of their warm-up*.

## Welchen Weg Sie wählen

Fassen Sie Änderungen an Einstellungen zusammen und starten Sie einmal neu.

## Prüfen

- Das geänderte Menü erscheint unter der Velocity-Adresse.

## Verwandte Seiten

- [Fehlerbehebung beim Kopfmenü](../../kopfmenue-fehlerbehebung.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
