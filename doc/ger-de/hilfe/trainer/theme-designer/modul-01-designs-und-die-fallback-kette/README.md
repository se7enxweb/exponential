---
title: "Modul 1: Designs und die Fallback-Kette"
audience: trainer, designer, developer
category: training
order: 10
summary: Das erste Modul von Theme-Design. Die Teilnehmenden finden die Designs einer Installation, lesen die Design-Kette eines Siteaccess, finden das Template hinter jedem Markup und unterscheiden Website-Designs von Administrationsdesigns. 3 Stunden 10 Minuten.
keywords: theme-design, modul 1, design, fallback-kette, SiteDesign, AdditionalSiteDesignList, DesignExtensions, template-pfadkommentare, admin4, administrationsdesign, theme design, module 1, fallback chain, template path comments, administration design
type: help
---
# Modul 1: Designs und die Fallback-Kette

Alle späteren Module bauen auf einer Idee auf: Ein Siteaccess hat eine Kette von Designs, und die erste Datei, die
entlang dieser Kette gefunden wird, gewinnt. Dieses Modul macht die Idee greifbar. Die Teilnehmenden sehen sich die
Designs auf der Festplatte an, lesen die Kette aus den Einstellungen, belegen ihre Lesart mit Template-Pfadkommentaren
und lernen, worin sich die Administrationsdesigns von den Website-Designs unterscheiden. Nichts in diesem Modul ändert
das Aussehen einer Seite; es geht ums Lesen vor dem Schreiben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Dauer | 3 Stunden 10 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Einsteiger |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 1.1: Was ein Design ist und wo es liegt](was-ein-design-ist.md) | 45 Minuten | Die Designs auf der Festplatte auflisten, ihre Ordner und die Zeilen `DesignExtensions[]` lesen |
| [Lektion 1.2: Die Design-Kette eines Siteaccess lesen](die-kette-eines-siteaccess-lesen.md) | 45 Minuten | Die Kette mit `exp:ini where` und `get` lesen, vorhersagen, welches `pagelayout.tpl` verwendet wird |
| [Lektion 1.3: Welches Template hat dieses Markup geschrieben?](welches-template-dieses-markup-geschrieben-hat.md) | 45 Minuten | Template-Pfadkommentare einschalten, Dateien im Seitenquelltext finden, die Kommentare wieder ausschalten |
| [Lektion 1.4: Website-Designs und Administrationsdesigns](website-designs-und-admin-designs.md) | 45 Minuten | Die Kette der Administration lesen, das Administrationsdesign für die eigene Sitzung umschalten |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Teilnehmerin oder Teilnehmer mit Shell-Zugang im Stammverzeichnis der Installation,
  zum Beispiel installiert mit `php bin/php/console exp:install` ([7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md));
  siehe [Modul 2: Die Übungsumgebung aufbauen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/README.md) von Train
  the Trainer.
- Jede Installation im Browser erreichbar (zum Beispiel mit `php bin/php/console exp:velocity start`).
- Ein Administratorkonto pro Person für Lektion 1.4; die Rolle braucht die Richtlinie `uiswitch/design`, wenn die
  Teilnehmenden die Administrationsdesigns ausprobieren sollen.
- Führen Sie jeden Befehl des Moduls einmal auf einer Kopie der Schulungsinstallation aus und notieren Sie den Namen
  eines öffentlichen Siteaccess und den des Siteaccess der Administration; schreiben Sie beide an die Tafel.
- Pro Person ein ausgedrucktes Blatt mit zwei leeren Spalten, „öffentliche Kette“ und „Kette der Administration“.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 1.1 | 45 |
| Lektion 1.2 | 45 |
| Pause | 10 |
| Lektion 1.3 | 45 |
| Lektion 1.4 | 45 |
| **Gesamt** | **190** |

## Verwandte Seiten

- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
