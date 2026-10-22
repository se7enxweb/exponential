---
title: Die Symfony Legacy Bridge für Framework-Entwickler
audience: web-framework-developer, developer
category: web-frameworks
order: 410
summary: Wie Exponential 6 und Symfony als eine Anwendung laufen - das Legacy-Bridge-Bundle, das den Kernel von Exponential 6 in einer Symfony-Plattforminstallation lädt, was jede Seite übernimmt, der Siteaccess legacy_admin, die Konsolenbefehle, Twig aus Legacy-Templates aufrufen mit ngsymfonytools, welche Bridge-Linie zu welcher Symfony-Version passt, und die Grenzen.
keywords: Symfony, Legacy Bridge, legacy bridge, legacy-bridge, EzPublishLegacyBundle, legacy_admin, legacy_mode, exponential:legacy:script, ngsymfonytools, site-legacy-bundle, Twig, zwei Kernel, dual kernel, Exponential Platform Legacy, Nexus, Web-Framework-Entwickler, web framework developer
type: help
---
# Die Symfony Legacy Bridge für Framework-Entwickler

Diese Seite richtet sich an Symfony-Entwickler, die Exponential 6 und ihren Symfony-Code in einer Anwendung haben
möchten. Die **Legacy Bridge** ist ein Symfony-Bundle, das den Kernel von Exponential 6 in einer
Symfony-Plattforminstallation lädt; eine einzige Webanwendung liefert dann die Legacy-Administration und -Templates
sowie den Symfony-Stack aus, auf derselben Datenbank
([Legacy Bridge](../../funktionen/6.0/legacy-bridge.md), [Symfony und die Legacy Bridge](../ersteller/anwendungsentwickler/symfony-und-die-legacy-bridge.md)).
Sie ist kein Weg, Symfony zu einer eigenständigen Installation von Exponential 6 hinzuzufügen: Der Host ist ein
Symfony-Plattformprojekt.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Welche Seite was übernimmt](#welche-seite-was-übernimmt)
3. [Welche Linie für welches Symfony](#welche-linie-für-welches-symfony)
4. [Über die Grenze hinweg arbeiten](#über-die-grenze-hinweg-arbeiten)
5. [Grenzen](#grenzen)

## Die Bausteine

| Paket | Rolle |
|---|---|
| `se7enxweb/legacy-bridge` | das Bundle (`eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle`), das den Legacy-Kernel startet |
| `se7enxweb/ibexa-legacy-bridge` | die Portierung für die Generation Ibexa 4.6 |
| `se7enxweb/site-legacy-bundle` | Bindeglied für Sites im Netgen-Stil: Legacy-Mapper, Templating, Event-Listener |
| `se7enxweb/ngsymfonytools` | eine Legacy-Erweiterung mit Template-Operatoren, mit denen ein `.tpl` Twig einbinden oder einen Symfony-Controller rendern kann |
| `se7enxweb/exponential-legacy-installer` | der Composer-Installer für die Legacy-Seite |

Die Legacy-Seite wird über einen Siteaccess namens `legacy_admin` erreicht, der mit `legacy_mode: true` konfiguriert ist
([Spezifikation des Legacy-Bridge-Bundles](../../spezifikationen/6.0/legacy-bridge-bundle.md)).

## Welche Seite was übernimmt

| Bleibt in Exponential 6 (Legacy) | Wird von Symfony erledigt |
|---|---|
| Inhaltsklassen, Rollen und Richtlinien, die Legacy-Administration | öffentliches Rendering mit Twig, Routen, Controller |
| `.tpl`-Templates und Legacy-Erweiterungen | Bundles, Dienste, Dependency Injection |
| Workflows, Cronjob-Teile, Legacy-Module | REST und GraphQL der Plattform, Symfony-Konsole |

Beide lesen dieselben Inhalte: Was Redakteure in der Legacy-Administration anlegen, erscheint im Twig-Frontend.
Innerhalb der Bridge kann der Legacy-Web-Kernel den Service-Container von Symfony erhalten
(`ezpKernelWeb::hasServiceContainer()`).

## Welche Linie für welches Symfony

| Bridge-Linie | Plattform | Symfony | PHP |
|---|---|---|---|
| `master` (`v2.1.x`) | eZ Platform 2.5 LTS | 3.4 | 8.x mit Korrekturen |
| `3.x` | eZ Platform 3.3 | 5.4 | `^8.0` |
| `4.x` | Ibexa-4.6-kompatible Installationen | 5.4 | `^8.0` |
| `5.x` | Platform v5 | 7.4 | `^8.4` |

Details, Tags und Voraussetzungen: [Welche Version für welche Plattform](../../funktionen/6.0/legacy-bridge.md#welche-version-für-welche-plattform).

## Über die Grenze hinweg arbeiten

- **Ein Legacy-Skript mit den Einstellungen der Plattform ausführen:**
  `php bin/console exponential:legacy:script bin/php/ezcache.php --clear-id=content --siteaccess=legacy_admin`
  (die Namen `exponential:legacy:*` gibt es ab den auf der Funktionsseite genannten Linien und Tags; ältere Linien
  kennen nur die Namen `ezpublish:*`) ([Konsolenbefehle](../../funktionen/6.0/legacy-bridge.md#konsolenbefehle)).
- **Twig aus einem Legacy-Template:** Aktivieren Sie `ngsymfonytools` und verwenden Sie dessen Operatoren
  ([Twig aus einem Legacy-Template aufrufen](../../funktionen/6.0/plattform-site-bundles.md#twig-aus-einem-legacy-template-aufrufen)).
- **Caches:** Standardmäßig leert die Bridge den Persistenz-Cache der Plattform, wenn der Legacy-Cache für
  Inhaltsansichten geleert wird, sowie bei `cache:clear` (`ez_publish_legacy.clear_all_spi_cache_from_legacy` und
  `clear_all_spi_cache_on_symfony_clear_cache`).
- **Gemeinsame Logik:** Halten Sie Geschäftsregeln in einfachen PHP-Klassen, die beide Seiten aufrufen können.

## Grenzen

- Das Upstream-Projekt erklärte, dass die Bridge unter eZ Platform 3.x nicht unterstützt wird, weil das Schema
  auseinanderlaufen würde; die Zweige von se7enxweb behalten das Legacy-Schema für die oben genannten Plattform-Kernel bei.
- Zwei Stacks bedeuten zwei Sätze von Caches, Templates und Deployments, die bei jedem Release betrieben und getestet
  werden müssen.
- Nicht jedes Plattformprodukt enthält den Legacy-Kernel: Exponential Platform DXP enthält ihn nicht
  ([Die Plattformprodukte](die-plattformprodukte.md)).

## Verwandte Seiten

- [Exponential und ein Framework kombinieren](exponential-und-ein-framework-kombinieren.md)
- [Migration von eZ Platform und Ibexa](../../installation/16-migration-von-ez-platform-und-ibexa.md)
- [Forks von Plattformpaketen und umbenannte Befehle](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
