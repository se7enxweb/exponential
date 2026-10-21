---
title: "Modul 1: Die Entwicklungsinstallation"
audience: trainer, developer
category: training
order: 10
summary: Das erste Modul des Kurses Entwickler. Die Teilnehmenden finden sich in einer Installation zurecht, bauen mit exp:install eine Entwicklungsinstallation aus Git-Klonen auf, schalten die Debug-Ausgabe ein und führen ihr erstes Skript mit geladenem Kernel aus. 3 Stunden 25 Minuten.
keywords: entwickler, modul 1, entwicklungsinstallation, git clone, exp:install, debug-ausgabe, logs, ezexec, ordnerübersicht, developers course, module 1, development installation, debug output, folder map
type: help
---
# Modul 1: Die Entwicklungsinstallation

Jedes spätere Modul braucht eine Installation, in der die Teilnehmenden etwas kaputt machen und sehen können, was
schiefgelaufen ist. Dieses Modul baut sie auf: die Ordnerübersicht und die zwei Regeln des Kurses, eine Installation
aus Git-Klonen, sichtbare Fehler und ein erstes Skript, das mit dem Kernel spricht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 3 Stunden 25 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Einsteiger |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 1.1: Die Landkarte einer Installation für Entwickler](die-landkarte-fuer-entwickler.md) | 45 Minuten | Die Ordner durchgehen, eine Adresse und einen Klassennamen bis zu ihren Dateien verfolgen |
| [Lektion 1.2: Eine Entwicklungsinstallation mit Git](eine-entwicklungsinstallation-mit-git.md) | 60 Minuten | Klonen, `composer install`, `exp:install`, `exp:velocity start`, anmelden |
| [Lektion 1.3: Debug-Ausgabe und Logs](debug-ausgabe-und-logs.md) | 45 Minuten | Die Debug-Ausgabe mit `exp:ini` einschalten, den Bericht lesen, die Logs verfolgen |
| [Lektion 1.4: Ein Skript mit geladenem Kernel](ein-skript-mit-geladenem-kernel.md) | 45 Minuten | `extension/mysite_hello` anlegen, ein Skript mit `ezexec.php` ausführen |

## Dieses Modul vorbereiten

- Alle Teilnehmenden brauchen einen Rechner (oder einen eigenen Ordner auf einem gemeinsamen Server) mit PHP,
  Composer 2 und Git 2.28 oder neuer. Prüfen Sie die Versionen anhand der [Anforderungen](../../../../installation/02-voraussetzungen.md).
- Bei einer langsamen oder volumenbegrenzten Verbindung bereiten Sie auf jedem Rechner einen fertigen Klon mit
  bereits ausgeführtem `composer install` vor; die Teilnehmenden beginnen Lektion 1.2 dann beim Installationsschritt.
- Legen Sie den Port für jede Person fest, wenn sich mehrere einen Server teilen (`[PHPServerSettings] Port` in
  `settings/velocity.ini`, standardmäßig 8087), und schreiben Sie die Ports an die Tafel.
- Proben Sie das ganze Modul einmal auf einem sauberen Rechner; notieren Sie, wie lange der Klon und
  `composer install` dauern.
- Keine echten E-Mails: Entwicklungsinstallationen dürfen keine E-Mails an echte Adressen verschicken.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 1.1 | 45 |
| Lektion 1.2 | 60 |
| Pause | 10 |
| Lektion 1.3 | 45 |
| Lektion 1.4 | 45 |
| **Gesamt** | **205** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
