---
title: "Modul 6: Von den Symfony-basierten Plattformen"
audience: trainer, admin, developer
category: training
order: 60
summary: Das Modul für Websites, die eZ Platform oder Ibexa betreiben - die Generationen der Quelle und die beiden Ziele, Weg A zu Exponential Platform Nexus derselben Generation und Weg B hinunter zum Legacy-Kernel von Exponential 6 oder zu Platform Legacy. Drei Lektionen, 2 Stunden 30 Minuten.
keywords: migration zu exponential, weg a, weg b, paketwechsel, legacy-bridge, modul, migrating to exponential, eZ Platform, Ibexa, Symfony, Exponential Platform Nexus, Exponential Platform Legacy, path A, path B, package swap, ezsqldiff, legacy bridge, module
type: help
---
# Modul 6: Von den Symfony-basierten Plattformen

Websites auf eZ Platform oder Ibexa haben in der Exponential-Familie zwei Ziele. Dieses Modul vermittelt, wie man die
Generation der Quelle erkennt, wie man zwischen den Exponential-Platform-Distributionen derselben Generation und dem
Legacy-Kernel von Exponential 6 wählt und wie jeder Weg geplant wird. Es folgt Kapitel 16 des Installationshandbuchs.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Dauer | 2 Stunden 30 Minuten |
| Lektionen | 3 |
| Niveau | Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [6.1 Von eZ Platform oder Ibexa: zwei Ziele und wie man wählt](zwei-ziele-und-die-wahl.md) | 45 Minuten | Generationen erkennen; passende Linien von Nexus und Platform Legacy finden; für zwei Websites einen Weg wählen |
| [6.2 Weg A: zu Exponential Platform Nexus](weg-a-zu-nexus.md) | 45 Minuten | Einen Umzug von 4.6 planen: Paketwechsel, Befehlsnamen, Landing Pages, Überprüfung |
| [6.3 Weg B: hinunter zum Legacy-Kernel von Exponential 6](weg-b-hinunter-zum-legacy-kernel.md) | 60 Minuten | Über Feldtypen entscheiden; die sechs Datenbankschritte mit `ezsqldiff.php` in der richtigen Richtung schreiben |

## Dieses Modul vorbereiten

- [ ] Lesen Sie Kapitel 16 einmal vollständig; gleichen Sie die Versionslinien aus Kapitel 16.1 am Kurstag mit dem
      Migrations-Hub ab.
- [ ] Eine Schulungsinstallation pro Person mit Shell-Zugang, für die nur lesenden Befehle aus Lektion 6.3.
- [ ] Die Szenarien der Lektionen 6.2 und 6.3 ausgedruckt oder in einem gemeinsamen Dokument.
- [ ] Die Teilnehmenden sollten für Lektion 6.2 Grundlagen von Composer und Symfony kennen; in gemischten Gruppen
      arbeitet ein Symfony-Entwickler mit einem Administrator zusammen.
- [ ] Es wird keine Nexus- oder Platform-Installation benötigt: Das Modul ist ein Planungsmodul. Wenn Sie eine zeigen
      möchten, verwenden Sie einen Starter auf einem separaten Rechner und richten Sie ihn nie auf eine echte
      Datenbank.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 6.1 | 45 |
| Lektion 6.2 | 45 |
| Pause | 15 |
| Lektion 6.3 | 60 |
| Gesamt | 165 (2 Stunden 30 Minuten Lektionen, zuzüglich einer Pause von 15 Minuten) |

## Verwandte Seiten

- [Migration zu Exponential: Kursüberblick](../README.md)
- [Kapitel 16: Migration von eZ Platform und Ibexa](../../../../installation/16-migration-von-ez-platform-und-ibexa.md):
  das Kapitel, auf dem dieses Modul aufbaut.
- [Kapitel 17: Migrationsreferenz](../../../../installation/17-migrationsreferenz.md): die Nachschlagetabellen für beide
  Wege.
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
