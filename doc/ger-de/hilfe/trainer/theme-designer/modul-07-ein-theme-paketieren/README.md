---
title: "Modul 7: Ein Theme paketieren"
audience: trainer, designer, developer
category: training
order: 70
summary: Das siebte Modul von Theme-Design. Die Teilnehmenden vervollständigen die Metadaten ihres Themes, aktivieren es sicher und in der richtigen Reihenfolge, exportieren es als .ezpkg und stellen es mit einer composer.json unter Git. 3 Stunden 10 Minuten.
keywords: theme-design, modul 7, theme-erweiterung, ezinfo.php, extension.xml, Setup Extensions, ActiveExtensions, ladereihenfolge, extends, ezpkg, ezpm, Composer, composer.json, git, paketieren, theme design, module 7, theme extension, loading order, packaging
type: help
---
# Modul 7: Ein Theme paketieren

Ein Theme nützt anderen erst, wenn es sich installieren, erkennen und aktualisieren lässt. In diesem Modul machen die
Teilnehmenden aus `training_theme` etwas, das sie weitergeben könnten: Metadaten, die in jeder Datei übereinstimmen,
eine README, die erklärt, wie man das Theme einschaltet, eine klare Trennung zwischen den Einstellungen des Themes und
denen der Installation, einen Export als `.ezpkg` und ein eigenes Git-Repository mit einer `composer.json`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Dauer | 3 Stunden 10 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 7.1: Die Metadaten des Themes](metadaten-der-theme-erweiterung.md) | 45 Minuten | `ezinfo.php`, `extension.xml` und `LICENSE` in Übereinstimmung bringen; das Theme auf der Seite „Über“ finden |
| [Lektion 7.2: Ein Theme aktivieren und die Ladereihenfolge](aktivierung-und-ladereihenfolge.md) | 45 Minuten | Unter Setup > Extensions mit Überprüfung deaktivieren und aktivieren; `SiteDesign` dokumentieren; einen Block `<extends>` notieren |
| [Lektion 7.3: Ein Composer-Paket oder ein .ezpkg](composer-oder-ezpkg.md) | 45 Minuten | Den Lieferweg wählen, das Theme mit `ezpm.php` exportieren, das Archiv untersuchen, den Export wieder abschalten |
| [Lektion 7.4: Ein Theme unter Git, mit einer composer.json](ein-theme-unter-git.md) | 45 Minuten | Im Ordner der Erweiterung ein Repository anlegen, mit Präfixen committen, `composer.json` schreiben und validieren |

## Dieses Modul vorbereiten

- Alle Teilnehmenden arbeiten auf ihrer eigenen Installation: Die Lektionen 7.2 und 7.3 ändern `ActiveExtensions[]`
  und `package.ini`.
- `xmllint` (die Werkzeuge von libxml2), Git und nach Möglichkeit Composer auf den Computern der Teilnehmenden oder dem
  Schulungsserver installiert.
- `ezpm.php` im Stammverzeichnis der Installation in der Übungsumgebung; führen Sie `php ezpm.php help` einmal auf einer
  Kopie der Übungsumgebung aus.
- Vereinbaren Sie mit der Gruppe einen erfundenen Composer-Vendor-Namen für den Kurs (Lektion 7.4) und eine
  Kurs-E-Mail-Adresse unter `example.invalid` für Git-Commits.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 7.1 | 45 |
| Lektion 7.2 | 45 |
| Pause | 10 |
| Lektion 7.3 | 45 |
| Lektion 7.4 | 45 |
| **Gesamt** | **190** |

## Verwandte Seiten

- [Modul 6: Icons](../modul-06-icons/README.md)
- [Theme-Erweiterungen: hier beginnen](../../../designer/erweiterungen/theme-erweiterungen-hier-beginnen.md)
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
