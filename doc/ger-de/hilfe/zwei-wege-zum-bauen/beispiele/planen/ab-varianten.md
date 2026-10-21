---
title: "A/B-Varianten einer Seite planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1018
summary: Die Bedingung Abfrageparameter gibt es, aber der Resolver merkt sich seine Antwort pro Pfad, sodass eine Adresse nicht zwei Layouts zugleich zeigen kann; planen Sie Varianten unter zwei Adressen.
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

1. Auf einer Kopie getestet: eine Zuordnung *Pfadpräfix `showcase` + Abfrageparameter `variant`* (Priorität 3000) und die normale Showcase-Zuordnung. Die zuerst angefragte Adresse entschied für die nächste Stunde über das Layout von `/showcase` und `/showcase?variant=b` (in beiden Fällen 114 Blöcke oder in beiden Fällen 5).
2. Planen Sie also zwei Seiten mit eigener Adresse (zum Beispiel `/offer-a` und `/offer-b`), jede mit einer Zuordnung Ort oder Pfad auf ihr eigenes Layout, und teilen Sie den Verkehr außerhalb auf (Kampagnenlinks, Newsletter-Hälften).

## Mit Templates

1. Zwei Full-View-Templates, über `override.ini` pro Knoten oder Remote-ID ausgewählt, sind der Weg über Templates zum selben Ziel.

## Welchen Weg Sie wählen

Getrennte Adressen halten Seiten-Caches und Resolver korrekt.

## Prüfen

- Jede Varianten-Adresse zeigt in einem privaten Fenster ihr eigenes Layout.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Bekanntes Verhalten des Editors](../../bekanntes-verhalten-des-editors.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
