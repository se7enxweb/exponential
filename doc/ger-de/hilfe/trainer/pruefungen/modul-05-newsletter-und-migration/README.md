---
title: "Modul 5: Die Kurse Newsletter und Migration prüfen"
audience: trainer, editor, admin, developer
category: training
order: 50
summary: Schriftliche Tests mit belegten Lösungsschlüsseln und praktische Aufgaben mit Bewertungsrastern für die Kurse Newsletter mit CJW Newsletter und Migration zu Exponential, mit einer Newsletter-Aufgabe, die niemals Mails versendet. Etwa 4 Stunden 45 Minuten, wenn alle vier durchgeführt werden.
keywords: prüfungen und praktische übungen, newsletter, migration, schriftlicher test, praktische aufgabe, bewertungsraster, datei-transport, abbildung des inhaltsmodells, weiterleitungen, remote-id, assessments and practical exercises, newsletters, cjw_newsletter, written test, practical task, rubric, file transport, example.invalid, content model mapping, redirects, remote id
type: help
---
# Modul 5: Die Kurse Newsletter und Migration prüfen

Dieses Modul enthält die Prüfungen für zwei Spezialkurse: Newsletter mit CJW Newsletter und die Migration zu
Exponential. Die Newsletter-Aufgabe durchläuft einen vollständigen Zyklus, ohne eine einzige Mail zu versenden; die
Migrationsaufgabe ist eine Planungsaufgabe mit einem kleinen, wiederholbaren Import.

> **Hinweis**
> Dies sind die eigenen Prüfungen des Trainers, keine offizielle Zertifizierung. Newsletter-Übungen verwenden eine
> Schulungsinstallation, deren Mail-Transporte in Dateien schreiben, und ausschließlich Testabonnenten unter
> `example.invalid`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Dauer | 4 Stunden 45 Minuten für alle vier; Newsletter-Paar 2 Stunden 5 Minuten, Migrations-Paar 2 Stunden 40 Minuten |
| Lektionen | 4 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [5.1 Newsletter, schriftlicher Test](newsletter-schriftlicher-test.md) | 40 Min. | Dreizehn Fragen zu Listen, Opt-in, Cron, Transporten und Bounces |
| [5.2 Newsletter, praktische Aufgabe](newsletter-praktische-aufgabe.md) | 85 Min. | Ein vollständiger Zyklus in Dateien: abonnieren, bestätigen, per Cron versenden, abbestellen, sperren |
| [5.3 Migration zu Exponential, schriftlicher Test](migration-schriftlicher-test.md) | 45 Min. | Vierzehn Fragen zu Migrationswegen, Abbildung, Weiterleitungen, Generalprobe und Rückfallplan |
| [5.4 Migration zu Exponential, praktische Aufgabe](migration-praktische-aufgabe.md) | 115 Min. | Bestandsaufnahme, Abbildung, Weiterleitungen, Plan und ein wiederholbarer Import über die Remote-ID |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation pro Teilnehmer mit `Transport=file`, ohne Crontab und für die Lektionen 5.1 und 5.2
      mit aktiver Newsletter-Erweiterung, einem Newsletter-System und einer Liste.
- [ ] Alle drei Newsletter-Einstellungen `TransportMethod*` vom Trainer vor der Sitzung auf `file` gesetzt
      ([10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
- [ ] Für Lektion 5.4: pro Teilnehmer eine statische Beispiel-Website mit 10 bis 20 Seiten (für den Kurs erstellt,
      ohne personenbezogene Daten) und das Import-Werkzeug Ihres Kurses installiert.
- [ ] Ausgedruckte Fragen und Bewertungsraster, die Lösungsschlüssel getrennt aufbewahrt.

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 bis 0:40 | Lektion 5.1 |
| 0:40 bis 2:05 | Lektion 5.2 |
| 2:05 bis 2:50 | Lektion 5.3 |
| 2:50 bis 4:45 | Lektion 5.4 |
| Gesamt | 4 Stunden 45 Minuten, ohne Pausen |

## Verwandte Seiten

- [Modul 1: Prüfungen gestalten](../modul-01-pruefungen-gestalten/README.md)
- [Eine Prüfungssitzung durchführen](../modul-01-pruefungen-gestalten/eine-pruefung-durchfuehren.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
