---
title: "Modul 5: Caches"
audience: trainer, admin
category: training
order: 50
summary: Das fünfte Modul von Website-Administration, mit dem, was die Caches enthalten, dem Leeren in der Administration und auf der Kommandozeile, dem Vorabfüllen und den Caches vor PHP. Fünf Lektionen, 2 Stunden 55 Minuten.
keywords: website-administration, modul 5, caches, cache-verwaltung, ezcache.php, exp:cache, vorabfüllen, antwort-cache von velocity, http-cache, statischer cache, site administration, module 5, cache management, preload, velocity response cache, http cache, static cache
type: help
---
# Modul 5: Caches

Caches machen Exponential schnell, und veraltete Caches verursachen die meisten Anrufe der Art „Meine Änderung wird
nicht angezeigt“. In diesem Modul lernen die Teilnehmenden, die Caches zu lesen, bevor sie sie leeren, im Browser und
im Terminal das Kleinste zu leeren, das ein Problem behebt, eine Website nach dem Leeren wieder aufzuwärmen und mit
den Caches umzugehen, die antworten, bevor PHP es tut.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Dauer | 2 Stunden 55 Minuten (fünf Lektionen) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene bis Experten |
| Format | Praktisch; eine Schulungsinstallation pro Person, ausgeliefert von Velocity, ein Terminal, ein privates Browserfenster |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [5.1 Was die Caches enthalten](was-die-caches-enthalten.md) | 35 Min. | Die Seite Caches, IDs, Tags und die letzten Leerungen lesen |
| [5.2 Caches in der Administration leeren](caches-in-der-administration-leeren.md) | 35 Min. | Ausgewählte Caches und einen Tag leeren, auch aus der rechten Spalte |
| [5.3 Caches auf der Kommandozeile leeren](caches-auf-der-kommandozeile-leeren.md) | 35 Min. | `ezcache.php` und `exp:cache` mit `--dry-run` verwenden |
| [5.4 Caches vorab füllen](caches-vorladen.md) | 35 Min. | Probelauf, Vorabfüllen, defekte Links, der Shell-Befehl |
| [5.5 Die Caches vor PHP](caches-vor-php.md) | 35 Min. | Antwort-, HTTP- und statischen Cache vergleichen, Header lesen |

## Dieses Modul vorbereiten

- Die Schulungsinstallationen aus Modul 1, ausgeliefert von Velocity (`php bin/php/console exp:velocity start`), damit
  der Antwort-Cache vorhanden ist.
- Die `SiteURL` der Übungsumgebung muss zu der Adresse passen, unter der sie ausgeliefert wird, sonst fragt das
  Vorabfüllen in Lektion 5.4 die falsche Adresse ab. Prüfen Sie es vor dem Kurs mit einem Probelauf.
- `curl` auf der Kommandozeile für Lektion 5.5 und die PHP-Erweiterung curl für das Vorabfüllen.
- Vereinbaren Sie auf einem gemeinsamen Schulungsserver, wer welchen Cache leeren darf: Jedes Leeren macht die Website
  für alle langsamer.

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 | Lektion 5.1 (35 Min.) |
| 0:35 | Lektion 5.2 (35 Min.) |
| 1:10 | Pause (15 Min.) |
| 1:25 | Lektion 5.3 (35 Min.) |
| 2:00 | Lektion 5.4 (35 Min.) |
| 2:35 | Pause (10 Min.) |
| 2:45 | Lektion 5.5 (35 Min.) |
| 3:20 | Ende (2 Std. 55 Min. Unterricht plus 25 Min. Pausen) |

## Verwandte Seiten

- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
