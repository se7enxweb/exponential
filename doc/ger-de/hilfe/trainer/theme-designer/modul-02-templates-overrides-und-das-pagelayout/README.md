---
title: "Modul 2: Templates, Overrides und das Pagelayout"
audience: trainer, designer, developer
category: training
order: 20
summary: Das zweite Modul von Theme-Design. Die Teilnehmenden legen mit dem Assistenten für Design-Erweiterungen ihre eigene Theme-Erweiterung an, erweitern ihr Pagelayout, schreiben einen Override und legen die aufwendigen Teile des Rahmens in Cache-Blöcke. 4 Stunden 5 Minuten.
keywords: theme-design, modul 2, assistent für design-erweiterungen, theme-erweiterung, pagelayout, override, override.ini, template-editor, cache-block, eztemplatecheck, theme design, module 2, design extension wizard, theme extension, Template Editor
type: help
---
# Modul 2: Templates, Overrides und das Pagelayout

Ab diesem Modul bauen die Teilnehmenden. Alle legen mit dem Assistenten für Design-Erweiterungen die Theme-Erweiterung
`training_theme` an und schalten sie für ihren öffentlichen Siteaccess ein. Sie erweitern ihr Pagelayout um einen
Breadcrumb, einen Sprunglink und eine Begrüßung, fügen einen Override für die Zeilenansicht von Artikeln hinzu und
lernen, wo Cache-Blöcke helfen und wo sie die Daten eines Besuchers an einen anderen verraten. Jedes spätere Modul
arbeitet in dieser Erweiterung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Dauer | 4 Stunden 5 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Einsteiger bis Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 2.1: Ihre eigene Design-Erweiterung](ihre-eigene-design-erweiterung.md) | 60 Minuten | `training_theme` mit dem Assistenten anlegen, aktivieren, `SiteDesign` für einen Siteaccess setzen |
| [Lektion 2.2: Das Pagelayout, der Rahmen Ihrer Website](das-pagelayout.md) | 60 Minuten | Einen Sprunglink, einen Breadcrumb und eine Begrüßung hinzufügen; Templates mit `eztemplatecheck.php` prüfen |
| [Lektion 2.3: Overrides mit override.ini](overrides-mit-override-ini.md) | 60 Minuten | Den Override des Assistenten lesen, einen Zeilen-Override für Artikel schreiben, ihn im Template-Editor finden |
| [Lektion 2.4: Cache-Blöcke in einem Design](cache-bloecke-in-einem-design.md) | 45 Minuten | Das Menü zwischenspeichern, einen Block provozieren und reparieren, dessen Schlüssel den Benutzer vergessen |

## Dieses Modul vorbereiten

- Die eigene Schulungsinstallation jeder Person, mit einem Administratorkonto mit `setup/setup` (für den Assistenten
  unter **Setup > RAD**) und Shell-Zugang im Stammverzeichnis der Installation.
- Veröffentlichte Inhalte auf der Schulungs-Website: mindestens ein Ordner unterhalb der obersten Ebene, der zwei oder
  mehr Artikel mit einem **Intro** auflistet (die Demo-Inhalte des Pakets `sevenx_multisite` haben solche; siehe
  [Das Seitenpaket für eine Schulung wählen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/das-seitenpaket-fuer-den-kurs-waehlen.md)).
- Führen Sie den Assistenten einmal auf einer Kopie der Übungsumgebung aus und prüfen Sie seine `page_head.tpl`: Wenn
  der Titel `implode_key` verwendet, bereiten Sie die drei Änderungen aus
  [Die Teile des Design-Assistenten](../../../../../eng-us/help/designers/extensions/design-wizard-parts-reference.md#three-edits-the-generated-head-and-layout-need)
  vor.
- Wenn die Übungsumgebung von Velocity bereitgestellt wird, schreiben Sie den Befehl zum Neustart an die Tafel:
  `php bin/php/console exp:velocity restart`.
- Ein privates Browserfenster auf jedem Computer der Teilnehmenden für Lektion 2.4.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 2.1 | 60 |
| Pause | 10 |
| Lektion 2.2 | 60 |
| Lektion 2.3 | 60 |
| Pause | 10 |
| Lektion 2.4 | 45 |
| **Gesamt** | **245** |

Die Lektionszeit beträgt 3 Stunden 45 Minuten. Legen Sie bei einem eintägigen Kurs
die Mittagspause nach Lektion 2.2.

## Verwandte Seiten

- [Modul 1: Designs und die Fallback-Kette](../modul-01-designs-und-die-fallback-kette/README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
