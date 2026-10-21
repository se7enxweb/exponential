---
title: "Modul 6: Websites mit dem Kickstarter und exp:install installieren"
audience: trainer, admin, developer
category: training
order: 60
summary: Das letzte Modul von Multisite und Seitenpakete. Die Teilnehmenden vergleichen die Installationsmethoden, installieren eine Multisite mit exp:install und mit dem Kickstarter, installieren sicher neu und erzeugen Einstellungen neu und bauen in einem Abschlussprojekt eine Multisite von Grund auf. 5 Stunden 5 Minuten.
keywords: multisite und seitenpakete, modul 6, kickstarter, installationsmethoden, neuinstallation, einstellungen neu erzeugen, abschlussprojekt, multisite and site packages, module 6, exp:install, kickstart.ini, install methods, --print, --dry-run, --force, DatabaseAction, reinstall, regenerate settings, capstone
type: help
---
# Modul 6: Websites mit dem Kickstarter und exp:install installieren

Jede Multisite beginnt mit einer Installation, und viele werden mehr als einmal installiert: Testsysteme,
Staging-Kopien, Übungsumgebungen. Dieses Modul vermittelt die beiden Installer für die Kommandozeile, `exp:install`
und den Kickstarter, den sicheren Weg zur Neuinstallation und zum Neuerzeugen von Einstellungen und endet mit einem
Abschlussprojekt, in dem jede Person selbstständig eine Multisite in einem leeren Verzeichnis aufbaut.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Dauer | 5 Stunden 5 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 6.1: Eine Installer-Engine, drei Oberflächen](eine-engine-drei-frontends.md) | 40 Minuten | Die Methoden vergleichen und eine pro Situation wählen |
| [Lektion 6.2: Eine Multisite mit exp:install installieren](installieren-mit-exp-install.md) | 55 Minuten | Nach `--print` und `--dry-run` mit Host-Zuordnung installieren, drei Hosts testen |
| [Lektion 6.3: Der Kickstarter und kickstart.ini](der-kickstarter-und-kickstart-ini.md) | 55 Minuten | `kickstart.ini` schreiben und prüfen, mit Port-Zuordnung installieren |
| [Lektion 6.4: Neu installieren, zurücksetzen und Einstellungen neu erzeugen](neu-installieren-und-einstellungen-neu-erzeugen.md) | 45 Minuten | Sichern, mit `remove` zurücksetzen, mit `skip` neu erzeugen |
| [Lektion 6.5: Abschlussprojekt, eine Multisite von Grund auf](abschlussprojekt-eine-multisite-von-grund-auf.md) | 90 Minuten | Allein eine Multisite mit zwei Marken aufbauen und sie mit der Checkliste prüfen |

## Dieses Modul vorbereiten

- Jede Person braucht drei frische Code-Verzeichnisse (Lektionen 6.2, 6.3 und 6.5), vorbereitet wie in
  [Train the Trainer, Lektion 2.2](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md),
  und eine Möglichkeit, sie auf freien Ports auszuliefern ([Lektion 2.4](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md)).
- Der Rechner braucht Netzwerkzugang zum Paketindex für die Standard-Seitenpakete, oder die Pakete sind bereits
  importiert (eine Offline-Installation); testen Sie vor dem Kurs ein `exp:install --dry-run`.
- Installationen dauern jeweils einige Minuten; staffeln Sie sie auf einem gemeinsamen Server und führen Sie sie mit
  niedriger Priorität aus.
- Für die Sicherung in Lektion 6.4 muss `sqlite3` installiert sein.
- Drucken Sie den Auftrag und die Checkliste des Abschlussprojekts aus Lektion 6.5 aus.
- Erinnern Sie die Teilnehmenden daran, dass `kickstart.ini`, die Zusammenfassung und `var/log/initial-admin-password`
  Passwörter enthalten: Nichts davon gehört in einen Chat, auf Screenshots oder in gemeinsame Ordner.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 6.1 | 40 |
| Lektion 6.2 | 55 |
| Pause | 10 |
| Lektion 6.3 | 55 |
| Lektion 6.4 | 45 |
| Pause | 10 |
| Lektion 6.5 | 90 |
| **Gesamt** | **305** |

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
