---
title: Der Kernel von Exponential 6 im Ökosystem
audience: ecosystem, developer, admin, partner
category: ecosystem
order: 11
summary: Der Kernel von Exponential 6 als Mittelpunkt des Ökosystems - was er ist, seine wichtigsten Funktionen, wie die README sie aufführt, die Release-Linie 6.0 und ihre Unterstützung, wie er mit Composer installiert wird und was darauf aufbaut.
keywords: Exponential 6, Kernel, CMS, CMF, Inhaltsmodell, Shop, Multisite, Mehrsprachigkeit, REST-API, se7enxweb/exponential, 6.0.14, 6.0.15, Composer, kernel, content model, shop, multisite, multilingual, REST API
type: help
---
# Der Kernel von Exponential 6 im Ökosystem

## Inhalt

1. [Was er ist](#was-er-ist)
2. [Wichtigste Funktionen](#wichtigste-funktionen)
3. [Die Release-Linie](#die-release-linie)
4. [Ihn installieren](#ihn-installieren)
5. [Was darauf aufbaut](#was-darauf-aufbaut)

## Was er ist

"Exponential is a professional PHP application framework with advanced CMS (content management system) functionality.
As a CMS its most notable feature is its fully customizable and extendable content model", Exponential ist also ein
professionelles PHP-Anwendungsframework mit fortgeschrittenen CMS-Funktionen, dessen bemerkenswerteste Eigenschaft
sein vollständig anpassbares und erweiterbares Inhaltsmodell ist (`README.md`). Es wird als Composer-Paket
`se7enxweb/exponential` veröffentlicht. Sein Code war einst der Code von eZ Publish, das sein Hersteller eingestellt hat
([Exponential 6, der Kernel](../../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel)).

## Wichtigste Funktionen

Die "Main Exponential features" der README umfassen: selbst definierte Inhaltsklassen und Objekte,
Versionsverwaltung, Mehrsprachigkeit, eine eingebaute Suchmaschine, ein rollenbasiertes Rechtesystem, Freigabe von
Inhalten und zeitgesteuertes Veröffentlichen, mehrere Websites, eingebauten E-Commerce, Workflows, eine
Template-Engine, eine Headless-REST-API, eine Abstraktionsschicht für Datenbanken, Clustering und eine Plugin-API. Ein
Rundgang für Käufer: [Funktionsrundgang](../../kunden/nutzen/rundgang-durch-die-funktionen.md).

## Die Release-Linie

| Fakt | Quelle |
|---|---|
| 6.0.0 erschien am 1. Januar 2024 | [Zeitleiste](../../wer-ist-die-exponential-foundation.md#zeitleiste) |
| 6.0.14 erschien am 7. Juni 2026; 6.0.15 ist auf `main` in Entwicklung | ebenda |
| 6.0 ist die gepflegte Linie; Korrekturen werden mit dem nächsten Release 6.0.x veröffentlicht | `SECURITY.md` |
| Ein veröffentlichtes Tag wird nie geändert | `SECURITY.md` |
| PHP 7.4 bis 8.5 werden unterstützt | `README.md`, [PHP-Versionen](../php-stack/php-versionen.md) |

Die Linie 6.0.15 bringt unter vielem anderen Velocity, das Design admin4, einen Audit-Trail, Exponential Layouts,
Exponential Intelligence, Inhaltsjobs, die LoveStack-Funktionen von Mugo Web und das Hilfesystem
([Exponential 6, der Kernel](../../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel)).

## Ihn installieren

"Exponential 6.0.x specifically requires the php package manager called Composer to be installed and run the command
`composer install`", Exponential 6.0.x verlangt also ausdrücklich, dass Composer installiert ist und der Befehl
`composer install` ausgeführt wird (`README.md`). Das Installationsbuch behandelt jeden Weg:
[Einleitung](../../../installation/01-einfuehrung.md), [Den Code holen](../../../installation/03-den-code-beschaffen.md),
[Installation mit einem Befehl](../../../funktionen/6.0/installation-mit-einem-befehl.md).

## Was darauf aufbaut

- Die mitgelieferten Erweiterungen ([Die mitgelieferten Erweiterungen](../pakete/die-mitgelieferten-erweiterungen.md)).
- Velocity, das ihn ausliefert ([Exponential Velocity](exponential-velocity.md)).
- Exponential Platform Legacy und Nexus, die ihn neben Symfony enthalten ([Die Platform-Linie](die-platform-linie.md)).
- Websites, Seitenpakete, Themes, Apps und Integrationen, die Community und Partner bauen.

## Verwandte Seiten

- [Produkte](README.md)
- [Was Exponential ist (Projektleitung)](../../fuehrung/was-exponential-ist.md)
- [Erste Schritte](../../erste-schritte.md)
- [Die Geschichte von Exponential](../../../geschichte/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
