---
title: "A/B-Varianten einer Seite planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1018
summary: Eine Bedingung Abfrageparameter gibt einer Adresse ein zweites Layout: Seit explayouts 1.4.42 bekommen /showcase und /showcase?variant=b bei jeder Anfrage ihr eigenes Layout.
keywords: A/B-Test, Variante, Abfrageparameter, Resolver-Cache, zwei URLs, Experiment, A/B test, variant, query parameter, resolver cache, two URLs, experiment
type: help
---
# A/B-Varianten einer Seite planen

Zwei Versionen einer Landingpage vergleichen.

*Lebenszyklus: Planen, Beispiel 19 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Legen Sie neben der normalen Showcase-Zuordnung eine Zuordnung *Pfadpräfix `showcase` + Abfrageparameter `variant`* (Priorität 3000) an. Seit explayouts 1.4.42 merkt sich der Resolver keine Antwort, die von einer Bedingung Abfrageparameter abhängt; `/showcase` und `/showcase?variant=b` bekommen also bei jeder Anfrage ihr eigenes Layout. Auf der Live-Installation geprüft: Eine Zuordnung mit dem Abfrageparameter `p643b=b` an einem Artikel zeigte mit `?p643b=b` `layout_4` und ohne ihn das normale `layout_2`, auf Apache und auf Velocity, in jeder Reihenfolge der Anfragen.
2. Teilen Sie den Verkehr außerhalb auf (Kampagnenlinks, Newsletter-Hälften). Der Seiten-Cache behält keine Adresse, deren Query einen Parameter außerhalb von `[HttpCacheSettings] QueryStringParameters` hat; die Variante wird also bei jedem Aufruf dargestellt.

## Mit Templates

1. Zwei Full-View-Templates, über `override.ini` pro Knoten oder Remote-ID ausgewählt, sind der Weg über Templates zum selben Ziel.

## Welchen Weg Sie wählen

Eine Adresse mit Abfrageparameter für einen Test; zwei Seiten mit eigener Adresse (zum Beispiel `/offer-a` und `/offer-b`), wenn beide Varianten aus dem Seiten-Cache kommen sollen.

## Prüfen

- Jede Varianten-Adresse zeigt in einem privaten Fenster ihr eigenes Layout.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Bekanntes Verhalten des Editors](../../bekanntes-verhalten-des-editors.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
