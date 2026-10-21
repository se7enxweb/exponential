---
title: "Modul 1: Siteaccesses für mehrere Websites"
audience: trainer, admin, developer
category: training
order: 10
summary: Das erste Modul von Multisite und Seitenpakete. Die Teilnehmenden verstehen Siteaccesses und die Ladereihenfolge der Einstellungen, planen eine Multisite, fügen ihrer Schulungsinstallation eine zweite Website hinzu und geben ihr einen Sprach-Siteaccess. 3 Stunden 35 Minuten.
keywords: multisite und seitenpakete, modul 1, siteaccess, zweite website, sprach-siteaccess, multisite and site packages, module 1, AvailableSiteAccessList, RelatedSiteAccessList, second site, RootNode, language siteaccess, SiteLanguageList, exp:ini
type: help
---
# Modul 1: Siteaccesses für mehrere Websites

Jedes spätere Modul baut auf diesem auf. Die Teilnehmenden lernen zuerst, die Siteaccesses einer Installation und die
Reihenfolge zu lesen, in der Einstellungsdateien geladen werden, planen dann eine Multisite auf Papier und bauen
schließlich ihre eigene Schulungsinstallation zu einer Installation mit zwei Websites und einer zweiten Sprache aus.
Die hier angelegten Siteaccesses `site_b` und `site_b_ger` werden in den Modulen 2 bis 4 wieder verwendet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Dauer | 3 Stunden 35 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 1.1: Was ein Siteaccess ist](was-ein-siteaccess-ist.md) | 45 Minuten | Die Siteaccesses, die Zuordnungsregeln und die Ladereihenfolge mit `exp:ini` lesen |
| [Lektion 1.2: Mehrere Websites auf einer Installation planen](mehrere-websites-planen.md) | 40 Minuten | Eine Multisite für einen Auftrag planen und zwischen einer und mehreren Installationen entscheiden |
| [Lektion 1.3: Schritt für Schritt eine zweite Website hinzufügen](eine-zweite-website-hinzufuegen.md) | 60 Minuten | Einen Wurzelordner und den Siteaccess `site_b` anlegen, ihn registrieren, `/site_b` erreichen |
| [Lektion 1.4: Ein Siteaccess pro Sprache](ein-siteaccess-pro-sprache.md) | 50 Minuten | `site_b_ger` mit eigenen Sprachen hinzufügen und ein Element übersetzen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person, installiert mit `php bin/php/console exp:install` (URL-Zuordnung, die
  Voreinstellung), mit Shell-Zugang und einem Administratorkonto. Wie man eine solche Übungsumgebung aufbaut:
  [Lektion 2.3 von Train the Trainer](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/one-install-per-learner.md).
- Die Teilnehmenden können `php bin/php/console` im Wurzelverzeichnis der Installation als der Benutzer ausführen,
  der die Website ausliefert; wenn sie als root arbeiten müssen, sagen Sie ihnen, dass sie `--allow-root-user`
  hinzufügen sollen.
- Wissen Sie, wie jede Website der Übungsumgebung ausgeliefert wird (der eingebaute Server, Apache oder Velocity) und
  unter welcher Adresse, zum Beispiel `http://127.0.0.1:8087`; zeigen Sie bei Velocity einmal
  `php bin/php/console exp:velocity restart`.
- Drucken oder verteilen Sie den Auftrag aus Lektion 1.2.
- Proben Sie die Lektionen 1.3 und 1.4 einmal auf einer frischen Installation der Übungsumgebung und notieren Sie die
  Knoten-ID, die der Ordner dort bekommen hat.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 1.1 | 45 |
| Lektion 1.2 | 40 |
| Pause | 10 |
| Lektion 1.3 | 60 |
| Pause | 10 |
| Lektion 1.4 | 50 |
| **Gesamt** | **215** |

## Verwandte Seiten

- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
