---
title: "Prüfen, dass jedes Layout Kopf- und Fußzeile verknüpft"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1205
summary: Vor dem Livegang müssen die Zonen header und footer jedes Seitenlayouts mit Header / Footer verknüpft sein; exp:layouts list zeigt es.
keywords: Zonenverknüpfung, Kopfzeile, Fußzeile, Prüfung vor dem Livegang, exp:layouts list, Links to, zone linking, header, footer, launch check
type: help
---
# Prüfen, dass jedes Layout Kopf- und Fußzeile verknüpft

Kein Seitentyp geht ohne den Rahmen der Site live.

*Lebenszyklus: Livegang, Beispiel 6 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. In jedem Layout: Verknüpfungs-Schaltfläche (unten links) > **Zone verknüpfen** bei HEADER > *Header / Footer* > **Zone auswählen** bei dessen HEADER; dasselbe für FOOTER.

## Auf der Kommandozeile

1. `./console exp:layouts list` - die Spalte *Links to* zeigt für jedes Seitenlayout `fd0aa7af77a85a03bf1080018f5db1dc` (Header / Footer).

## Welchen Weg Sie wählen

Die Prüfung auf der Konsole deckt alle Layouts mit je einer Zeile ab.

## Prüfen

- Jedes nicht gemeinsame Layout der Site nennt Header / Footer unter *Links to*.

## Verwandte Seiten

- [Gemeinsame Layouts und Zonenverknüpfung](../../../../../eng-us/help/build-two-ways/shared-layouts-and-zone-linking.md)
- [Eine Kampagnenseite bauen, von Anfang bis Ende](../../../../../eng-us/help/build-two-ways/build-a-campaign-page.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
