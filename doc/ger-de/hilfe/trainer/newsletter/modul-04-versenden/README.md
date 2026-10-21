---
title: "Modul 4: Versand"
audience: trainer, editor, admin
category: training
order: 40
summary: Vorschauen und Testversand, eine Ausgabe planen und versenden, die Cronjobs und Konsolenbefehle, einen Versand abbrechen, das Archiv und Statistiken - alles auf einer Schulungsinstallation, die jede Mail in eine Datei schreibt. 3 Stunden 15 Minuten.
keywords: newsletter mit cjw newsletter, versand, testversand, newsletter versenden, cronjob, konsole, warteschlange, abbrechen, archiv, statistiken, newsletters with cjw newsletter, sending, test send, send newsletter, console, queue, abort, archive, statistics
type: help
---
# Modul 4: Versand

In diesem Modul wird gesendet. Die Teilnehmenden testen eine Ausgabe, senden sie an ihre erfundenen Abonnenten,
arbeiten als Administratoren die Warteschlange von Hand ab, brechen einen Versand ab und lesen, was gesendet wurde.
Jede Mail ist eine Datei in `var/log/mail` und jede Adresse liegt unter `example.invalid`; der Trainer prüft beides
vor jeder Lektion.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Dauer | 3 Stunden 15 Minuten |
| Lektionen | 4 |
| Niveau | Einsteiger bis Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [4.1 Vorschauen und Testversand](vorschauen-und-testversand.md) | 45 Minuten | Eine Ausgabe in der Vorschau ansehen, einen Test senden, die Testdatei lesen |
| [4.2 Eine Ausgabe planen und versenden](eine-ausgabe-planen-und-versenden.md) | 60 Minuten | Einen Versand planen, die Warteschlange beobachten, die Mail-Dateien zählen |
| [4.3 Cronjobs und Konsolenbefehle](cronjobs-und-konsolenbefehle.md) | 45 Minuten | Die Warteschlange zuerst mit einem Probelauf abarbeiten, `status` lesen, Crontab-Zeilen schreiben |
| [4.4 Einen Versand abbrechen, das Archiv und Statistiken](abbrechen-das-archiv-und-statistiken.md) | 45 Minuten | Einen Versand abbrechen, das Archiv und einen Bericht lesen |

## Dieses Modul vorbereiten

- [ ] Am Kurstag in der Einstellungsansicht: `TransportMethodCronjob`, `TransportMethodPreview` und
      `TransportMethodDirectly` sind `file`; `site.ini [MailSettings] Transport` ist `file`.
- [ ] Jede Abonnenten-, Testempfänger- und Testgruppenadresse in der Datenbank liegt unter `example.invalid`.
      Durchsuchen Sie die Seite **Benutzer** nach allem anderen und entfernen oder sperren Sie es vor dem Kurs.
- [ ] Während des Moduls führt kein Cron die Newsletter-Teile auf der gemeinsamen Schulungsinstallation aus (der
      Trainer startet die Warteschlange), damit die Übungen dann stattfinden, wenn der Trainer es sagt.
- [ ] Shell-Zugang für Administratoren (Lektion 4.3), als Benutzer des Webservers.
- [ ] Optional für Lektion 4.4: ein Versand auf einer vorbereiteten Installation mit eingeschaltetem Tracking und
      einigen erfassten Öffnungen und Klicks, um einen Bericht mit Zahlen zu zeigen.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 bis 0:45 | Lektion 4.1 |
| 0:45 bis 1:45 | Lektion 4.2 |
| 1:45 bis 2:30 | Lektion 4.3 (Redakteure, die die Shell nicht nutzen, folgen am Beamer) |
| 2:30 bis 3:15 | Lektion 4.4 |

Planen Sie zusätzlich eine Pause von 15 Minuten nach Lektion 4.2 ein.

## Verwandte Seiten

- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
