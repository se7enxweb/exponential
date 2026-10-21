---
title: "Modul 3: Import- und Exportwerkzeuge"
audience: trainer, admin, developer
category: training
order: 30
summary: Das Werkzeugmodul des Migrationskurses - das richtige Import- oder Exportwerkzeug wählen, mit xrowextract importieren, mit Probeläufen und Abgleich über die Remote-ID, und ein eigenes Importskript auf der Kernel-API schreiben. Drei Lektionen, 2 Stunden 45 Minuten.
keywords: migration zu exponential, import, export, pakete, importskript, remote-id, probelauf, modul, migrating to exponential, xrowextract, ezcsvimport, packages, import script, ezexec, remote id, dry run, module
type: help
---
# Modul 3: Import- und Exportwerkzeuge

Ist die Zuordnung geschrieben, müssen die Inhalte hinein. Dieses Modul vermittelt die Werkzeuge, die Exponential
bereits mitbringt, die Arbeitsweise, die einen Import wiederholbar macht, und für Entwickler das Gerüst eines eigenen
Importskripts für Quellen, die kein Werkzeug liest.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Dauer | 2 Stunden 45 Minuten |
| Lektionen | 3 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [3.1 Das richtige Import- oder Exportwerkzeug wählen](das-richtige-werkzeug-waehlen.md) | 45 Minuten | Mit xrowextract Klassen und Exportspalten auflisten; fünf Quellen ihren Werkzeugen zuordnen |
| [3.2 Importieren mit xrowextract: Probeläufe und wiederholbare Importe](importieren-mit-xrowextract.md) | 60 Minuten | Eine Klasse exportieren, einen Import als Probelauf und echt ausführen und nachweisen, dass er nichts dupliziert |
| [3.3 Ein eigenes Importskript](ein-eigenes-importskript.md) | 60 Minuten | Das Gerüst aus der Dokumentation mit drei Elementen zweimal ausführen und Aktualisierungen statt Duplikaten sehen |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation pro Person mit Shell-Zugang; prüfen Sie, dass
      `php bin/php/console ext:xrowextract:csv --list-classes --node=2` darauf funktioniert (xrowextract installiert
      und aktiv).
- [ ] Jede Person hat einen Kursordner mit mindestens einem Artikel und kennt dessen Knoten-ID (Lektionen 3.2 und
      3.3).
- [ ] Ein einfacher Texteditor auf den Schulungsrechnern; kein Tabellenkalkulationsprogramm für die CSV-Datei.
- [ ] Lektion 3.3 ist für Entwickler. Wer nicht entwickelt, kann Lektion 3.2 mit einer zweiten Klasse wiederholen
      oder Abschnitt 2 der [Werkzeugseite](../../../../installation/migration/exponential-tools.md) zu Exporten lesen.
- [ ] Führen Sie Lektion 3.3 einmal selbst durch; notieren Sie den Sprachcode und die Bezeichner der Klasse
      **Folder** Ihres Schulungs-Seitenpakets.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 3.1 | 45 |
| Lektion 3.2 | 60 |
| Pause | 15 |
| Lektion 3.3 | 60 |
| Gesamt | 180 (2 Stunden 45 Minuten Lektionen, zuzüglich einer Pause von 15 Minuten) |

## Verwandte Seiten

- [Migration zu Exponential: Kursübersicht](../README.md)
- [Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md): die Seite, auf
  der dieses Modul aufbaut.
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
