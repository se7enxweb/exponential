---
title: "Modul 1: Die Website ausliefern"
audience: trainer, hosting, admin
category: training
order: 10
summary: Das erste Modul von Hosting und Betrieb, wie eine Anfrage beantwortet wird, Apache und nginx mit PHP-FPM, PHP-FPM-Pools und ihre Feinabstimmung, OPcache und APCu. Vier Lektionen, 2 Stunden 45 Minuten.
keywords: hosting und betrieb, modul 1, die website ausliefern, Apache, nginx, PHP-FPM, OPcache, APCu, routing, htaccess_root, hosting and operations, module 1, serving the site
type: help
---
# Modul 1: Die Website ausliefern

Bevor jemand eine Exponential-Website gut betreiben kann, muss er wissen, was sie ausliefert. Dieses Modul gibt die
Landkarte (vier Wege, eine Website auszuliefern, und eine gemeinsame Routing-Liste) und vermittelt dann die
traditionelle Einrichtung mit Apache oder nginx und PHP-FPM, die PHP-Werte und Zeitlimits eines Pools und den
Opcode-Cache, der PHP schnell macht. Velocity, der empfohlene Server, hat ein eigenes Modul als Nächstes.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Dauer | 2 Stunden 45 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |
| Form | Praxisorientiert; eine virtuelle Linux-Maschine mit root pro Person |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [1.1 Wie eine Anfrage beantwortet wird](wie-eine-anfrage-beantwortet-wird.md) | 40 Min. | Die Server vergleichen und die Routing-Regeln mit vier Anfragen nachweisen |
| [1.2 Apache und nginx mit PHP-FPM](apache-und-nginx-mit-php-fpm.md) | 45 Min. | Einen virtuellen Host schreiben, sicher neu laden, Dateieigentümer korrigieren |
| [1.3 PHP-FPM-Pools und Feinabstimmung](php-fpm-pools-und-feinabstimmung.md) | 45 Min. | Pool-Werte setzen, Zeitlimits und APCu prüfen, ein Neuladen vorab ansehen |
| [1.4 OPcache und APCu](opcache-und-apcu.md) | 35 Min. | OPcache-Einstellungen prüfen und das Profil des Opcode-Caches lesen |

## Dieses Modul vorbereiten

- Eine virtuelle Linux-Maschine pro Person mit root, Apache (oder nginx), PHP-FPM, `curl` und einer mit `exp:install`
  angelegten Schulungsinstallation ([Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md)).
- Beide Befehlslisten bereit für Systeme im Debian-Stil (`apache2`, `a2enmod`) und im Red-Hat-Stil (`httpd`), falls der
  Kurs gemischt ist.
- Proben Sie jede Übung auf demselben Betriebssystem und mit derselben PHP-Version, die die Teilnehmenden verwenden.
- Prüfen Sie, dass `./console exp:setup:status` auf jeder Schulungsinstallation läuft: Lektion 1.2 kopiert `.htaccess`
  mit `exp:setup:htaccess` ([.htaccess und config.php von der Konsole einrichten](../../../../anleitungen/setup-dateien-von-der-konsole.md)).
- Drucken Sie das Routing-Diagramm aus [Kapitel 8, Abschnitt 8.2](../../../../installation/08-die-website-ausliefern.md#82-wie-eine-anfrage-beantwortet-wird)
  als Handout aus.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 1.1 (40 Min.) |
| 0:40 | Lektion 1.2 (45 Min.) |
| 1:25 | Pause (15 Min.) |
| 1:40 | Lektion 1.3 (45 Min.) |
| 2:25 | Lektion 1.4 (35 Min.) |
| 3:00 | Ende (2 Std. 45 Min. Lektionen plus eine Pause) |

## Verwandte Seiten

- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Webserver und Engines](../../../hosting/server/README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
