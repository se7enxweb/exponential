---
title: "Modul 3: Einstellungen, Logs und Caches pro Website"
audience: trainer, admin, developer
category: training
order: 30
summary: Das dritte Modul von Multisite und Seitenpakete. Die Teilnehmenden verschieben die Einstellungen einer Website in eine Website-Erweiterung, schreiben Einstellungen für alle Siteaccesses einer Website und geben einer Website eigene Logs und einen eigenen INI-Cache. 3 Stunden 35 Minuten.
keywords: multisite und seitenpakete, modul 3, website-erweiterung, logs pro website, caches pro website, ini-cache, multisite and site packages, module 3, site extension, exp:ini move-all, extension settings/override, override__group, UseGlobalLogDir, LogDir, LogVarDir, CacheVarDir, ExpiryDir, INICacheDir
type: help
---
# Modul 3: Einstellungen, Logs und Caches pro Website

Laufen mehrere Websites, kommen betriebliche Fragen: Wo liegt die Konfiguration jeder Website, wie gelten
Einstellungen für eine Website und nicht für die anderen, und wie werden Logs und Caches getrennt gehalten? Dieses
Modul verschiebt die zweite Website der Übungsumgebung in eine eigene Erweiterung und trennt dann ihre Einstellungen,
Logs und ihren INI-Cache von der Hauptwebsite.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Dauer | 3 Stunden 35 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 3.1: Eine Website in einer eigenen Erweiterung](eine-website-in-einer-eigenen-erweiterung.md) | 55 Minuten | Die Einstellungen von `site_b` und `site_b_ger` mit `exp:ini move-all` in die Erweiterung `site_clubb` verschieben |
| [Lektion 3.2: Einstellungen für jeden Siteaccess einer Website](einstellungen-fuer-jeden-siteaccess-einer-website.md) | 45 Minuten | Das `settings/override/` der Erweiterung schreiben und mit `exp:ini where` nachweisen, dass nur ihre Websites es lesen |
| [Lektion 3.3: Logs pro Website](logs-pro-website.md) | 45 Minuten | `site_b` ein eigenes Log-Verzeichnis geben und seine Zeile in `404.log` finden |
| [Lektion 3.4: Caches und der INI-Cache pro Website](caches-und-der-ini-cache-pro-website.md) | 50 Minuten | `site_b` einen eigenen INI-Cache geben, ihn allein leeren, Caches im Arbeitsspeicher planen |

## Dieses Modul vorbereiten

- Alle Teilnehmenden haben die Module 1 und 2 auf ihrer eigenen Schulungsinstallation abgeschlossen.
- Die Teilnehmenden können als der Benutzer, der die Website ausliefert, Dateien unter `extension/` und `var/` anlegen
  (prüfen Sie den Eigentümer einmal: Von root angelegte Dateien kann der Webserver später nicht ändern).
- Proben Sie Lektion 3.1 in einer frischen Übungsumgebung: Notieren Sie die genauen Ablehnungsmeldungen, die Ihre
  Version ausgibt, damit Sie sie zeigen können.
- Wenn die Übungsumgebungen auf Exponential Velocity laufen, planen Sie nach der Einstellungsänderung jeder Lektion
  einen Neustart ein (`php bin/php/console exp:velocity restart`).
- Das Dateisystem im Arbeitsspeicher aus Lektion 3.4 wird nur auf Papier geplant; hängen Sie auf einem gemeinsamen
  Schulungsserver nichts ein.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 3.1 | 55 |
| Lektion 3.2 | 45 |
| Pause | 10 |
| Lektion 3.3 | 45 |
| Pause | 10 |
| Lektion 3.4 | 50 |
| **Gesamt** | **215** |

## Verwandte Seiten

- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
