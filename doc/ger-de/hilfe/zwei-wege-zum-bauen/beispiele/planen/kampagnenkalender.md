---
title: "Kampagnen mit Start- und Enddatum planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1017
summary: Eine Zuordnung kann eine Zeit-Bedingung (from, to) haben; planen Sie Kampagnen-Layouts damit und wissen Sie, dass sich der Resolver Antworten bis zu einer Stunde merkt.
keywords: Kampagne, Zeitbedingung, Startdatum, Enddatum, saisonal, Zuordnung, Resolver-Cache, CacheTTL, campaign, time condition, start date, end date, seasonal, mapping, resolver cache
type: help
---
# Kampagnen mit Start- und Enddatum planen

Ein Kampagnen-Layout, das eine Seite von einem Datum bis zu einem anderen übernimmt.

*Lebenszyklus: Planen, Beispiel 18 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Neue Zuordnung** bietet die Bedingung **Zeit** neben Siteaccess, Klasse, Abfrageparameter und Routenparameter.
2. Der Resolver prüft die Zeit gegen `from` und `to` und merkt sich die Antwort dann pro Pfad und Siteaccess für `[ResolverSettings] CacheTTL` (auf dieser Installation 3600 Sekunden).

## Mit Templates

1. Mit Templates ist es eine Bedingung im Template (`currentdate()` gegen ein Datum) und ein Leeren des Cache beim Umschalten.

## Welchen Weg Sie wählen

Planen Sie den Wechsel mit bis zu einer Stunde Überschneidung, oder schalten Sie zu dem Zeitpunkt, an dem es geschehen muss, von Hand um (die neue Zuordnung aktivieren, die alte deaktivieren).

## Prüfen

- Die Kampagnen-Zuordnung hat eine höhere Priorität als die normale.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
