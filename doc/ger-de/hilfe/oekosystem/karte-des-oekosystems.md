---
title: Die Karte des Ökosystems von Exponential
audience: ecosystem, developer, partner, project-manager
category: ecosystem
order: 3
summary: Wie die Teile des Ökosystems von Exponential zusammenhängen - von PHP und Composer über den Kernel, Velocity und die mitgelieferten Erweiterungen bis zur Platform-Linie, den Websites und ihren Anwendern - mit dem, was wovon abhängt und wo jeder Teil dokumentiert ist.
keywords: Karte des Ökosystems, Architektur, Abhängigkeiten, Kernel, Velocity, Erweiterungen, Platform, Nexus, DXP, Legacy Bridge, Composer, Packagist, Websites, Anwender, ecosystem map, architecture, dependencies, kernel, extensions, legacy bridge, sites, users
type: help
---
# Die Karte des Ökosystems von Exponential

## Inhalt

1. [Die Schichten](#die-schichten)
2. [Was wovon abhängt](#was-wovon-abhängt)
3. [Zwei Familien: Legacy-Kernel und Symfony-Plattform](#zwei-familien-legacy-kernel-und-symfony-plattform)
4. [Wo der Code liegt](#wo-der-code-liegt)

## Die Schichten

| Schicht | Teile | Seite |
|---|---|---|
| 1. Sprache und Werkzeuge | PHP 7.4 und neuer, Composer, Packagist, Git | [PHP-Versionen](php-stack/php-versionen.md), [Composer und Packagist](pakete/composer-und-packagist.md) |
| 2. Bibliotheken | Zeta Components (Kernel); Symfony, Twig, Doctrine (Platform-Linie) | [Upstream-Projekte und Forks](php-stack/upstream-projekte-und-forks.md) |
| 3. Kernel | Exponential 6 (`se7enxweb/exponential`) | [Der Kernel von Exponential 6](produkte/exponential-6-kernel.md) |
| 4. Auslieferung | Velocity, FrankenPHP, der eingebaute Server von PHP, Apache oder nginx mit PHP-FPM | [Webserver und Engines](php-stack/webserver-und-engines.md) |
| 5. Speicherung | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Datenbanken](php-stack/datenbanken.md) |
| 6. Erweiterungen | 66 mitgelieferte Pakete von se7enxweb, darunter Layouts und Intelligence; Erweiterungen anderer | [Die mitgelieferten Erweiterungen](pakete/die-mitgelieferten-erweiterungen.md) |
| 7. Designs und Themes | Admin-Designs, AdminUI, Themes für Websites | [Themes und Designs](pakete/themes-und-designs.md) |
| 8. Websites | Seitenpakete, individuelle Websites, Integrationen, Apps über die REST-API | [Seitenpakete und der Paketserver](pakete/seitenpakete-und-der-paketserver.md) |
| 9. Anwender | Betreiber, Redakteure, Mitglieder, Besucher | [Anwender im Ökosystem](personen/anwender-im-oekosystem.md) |

## Was wovon abhängt

```text
your site  ->  extensions and designs  ->  Exponential 6 kernel  ->  Zeta Components  ->  PHP
                                              ^
                    Velocity serves it        |  legacy bridge
                                              |
                     Exponential Platform Legacy / Nexus (Symfony + the kernel)
                     Exponential Platform 3.2.9 / DXP     (Symfony only, no kernel)
```

- Eine Erweiterung hängt von der Kernel-Version ab, für die sie geschrieben wurde; prüfen Sie ihre `composer.json`
  ([Kompatibilität und Abhängigkeiten](pakete/kompatibilitaet-und-abhaengigkeiten.md)).
- Velocity liefert PHP-Anwendungen aus und wird seit 6.0.15 von der `composer.json` des Kernels verlangt
  ([Exponential Velocity](produkte/exponential-velocity.md)).
- Legacy und Nexus betreiben den Kernel über die Legacy Bridge neben der Symfony-Plattform; DXP und Platform 3.2.9
  enthalten den Kernel nicht ([Die Platform-Linie](produkte/die-platform-linie.md)).

## Zwei Familien: Legacy-Kernel und Symfony-Plattform

| | Kernel von Exponential 6 | Symfony-Platform-Linie |
|---|---|---|
| Framework | Ein eigenes (CMF), Templates in seiner Template-Sprache | Symfony, Twig-Templates |
| Pakete | `se7enxweb/exponential` und Erweiterungen | Die Distributionen Platform, Legacy, Nexus, DXP und ihre Bundles |
| Kombinierbar | Ja: Legacy und Nexus enthalten ihn | Ja, über die Legacy Bridge |
| Übersicht der Repositorys | [Wer ist 7x?](../wer-ist-7x.md#das-ökosystem-der-erweiterungen) | [Das Ökosystem der Exponential-Plattform](../../geschichte/oekosystem.md) |

## Wo der Code liegt

- Alle Repositorys: <https://github.com/se7enxweb>.
- Alle Composer-Pakete: der Anbieter `se7enxweb` auf Packagist.
- Die Änderungsgeschichte von 53 Repositorys seit Dezember 2023, mit den Ledgers: [Das Ökosystem der
  Exponential-Plattform](../../geschichte/oekosystem.md).

## Verwandte Seiten

- [Ökosystem: hier beginnen](oekosystem-hier-beginnen.md)
- [Produkte](produkte/README.md)
- [Pakete](pakete/README.md)
- [Der PHP-Stack](php-stack/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
