---
title: Passt Exponential?
audience: adopter, businesses, project-manager, customer
category: adoption
order: 11
summary: Signale, dass Exponential zu den Anforderungen eines Anwenders passt, und Signale, dass es vielleicht nicht passt, auf Grundlage der Funktionen, die die README aufführt, der unterstützten Plattformen und der Produktfamilie - mit den Seiten, um jedes Signal zu prüfen.
keywords: Eignung, passt, Anforderungen, Inhaltsmodell, Multisite, Mehrsprachigkeit, Shop, Rechte, Workflows, REST-API, Symfony, Signale, fit, suitability, requirements, content model, multisite, multilingual, shop, permissions, workflows, REST API, signals
type: help
---
# Passt Exponential?

## Inhalt

1. [Signale, dass es passt](#signale-dass-es-passt)
2. [Signale, genauer hinzusehen](#signale-genauer-hinzusehen)
3. [Wenn ein anderes Produkt der Familie besser passt](#wenn-ein-anderes-produkt-der-familie-besser-passt)

## Signale, dass es passt

| Sie brauchen | Exponential hat | Quelle |
|---|---|---|
| Strukturierte Inhalte nach eigenem Entwurf | "User defined content classes and objects", ein "fully customizable and extendable content model" (selbst definierte Inhaltsklassen und Objekte, ein vollständig anpassbares und erweiterbares Inhaltsmodell) | `README.md` |
| Mehrere Websites oder Sprachen aus einer Installation | "Multi-site support", "Advanced multi-lingual support" | `README.md` |
| Einen Onlineshop | "Advanced e-Commerce Stores (Built in)", einen eingebauten Shop | `README.md` |
| Bereiche nur für Mitarbeitende und Freigaben | Rollenbasierte Rechte, "Content approval and scheduled publication" (Freigabe von Inhalten und zeitgesteuertes Veröffentlichen) | `README.md` |
| Apps oder andere Systeme, die Inhalte lesen | "A headless CRUD REST API" | `README.md` |
| Redakteure, die Seiten gestalten | Exponential Layouts | [Der Layout-Editor](../../layout-editor.md) |
| Hilfe beim Schreiben und Übersetzen | Exponential Intelligence, nur Entwürfe | [expai](../../../funktionen/6.0/erweiterungen/expai.md) |
| Geringe laufende Kosten und wenige bewegliche Teile | Keine Lizenzgebühr; SQLite ohne Datenbankserver | [Wofür Sie nicht bezahlen](../../unternehmen/kosten/wofuer-sie-nicht-bezahlen.md) |

## Signale, genauer hinzusehen

| Situation | Was Sie prüfen |
|---|---|
| Ihr Team kennt nur Symfony und Twig | Die auf Symfony basierende Platform-Linie passt vielleicht besser ([Ein Produkt auswählen](../../oekosystem/produkte/ein-produkt-auswaehlen.md)) |
| Sie müssen Microsoft SQL Server verwenden | Kein Treiber in 6.0.x; "known to be possible with additional work", also mit zusätzlicher Arbeit bekanntermaßen möglich ([Datenbankunterstützung](../../datenbankunterstuetzung.md)) |
| Ihr Hoster erlaubt keine langlebigen Prozesse | Verwenden Sie Apache oder LiteSpeed statt Velocity ([Webserver und Engines](../../oekosystem/php-stack/webserver-und-engines.md)) |
| Sie brauchen eine Funktion, die Sie in der Hilfe nicht finden | Gehen Sie davon aus, dass es sie nicht gibt, bis ein Machbarkeitsnachweis sie zeigt; prüfen Sie die Erweiterungen ([Eine Erweiterung finden](../../oekosystem/pakete/eine-erweiterung-finden.md)) |
| Sie brauchen garantierte Antwortzeiten | Planen Sie bezahlten Support von 7x oder einem Partner ein ([Langlebigkeit und Unterstützung](../entscheiden/langlebigkeit-und-unterstuetzung.md)) |

## Wenn ein anderes Produkt der Familie besser passt

Zur Familie gehören Exponential Basic für sehr kleine Websites und die Platform-Linie (Legacy, Nexus, DXP) für
Symfony-Projekte ([Produkte](../../oekosystem/produkte/README.md)). Dieser Bereich beschreibt die Einführung von
Exponential 6; lesen Sie für die Platform-Linie deren eigene Dokumentation unter <https://platform.exponential.earth>.

## Verwandte Seiten

- [Exponential bewerten](README.md)
- [Für wen Exponential ist](../../geschaeft/fuer-wen-exponential-ist.md)
- [Funktionshöhepunkte für Kunden](../../geschaeft/funktionshighlights-fuer-kunden.md)
- [Welche Art von Website Sie haben](../../kunden/typen/welcher-kundentyp-sind-sie.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
