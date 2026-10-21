---
title: "Bootcamp 3.2: Das dreitägige Administratoren-Bootcamp"
audience: trainer, admin
category: training
order: 32
summary: Drei Tage für alle, die den Betrieb einer Exponential-Website übernehmen, Tag 1 Installation, Benutzer und Rollen, Tag 2 Sektionen, Einstellungen, Caches, Cronjobs und Logs, Tag 3 Sicherheit, Zwei-Faktor-Anmeldung, Sicherungen, Upgrades und eine Störfallübung.
keywords: drei tage, bootcamp, administratoren, rollen, sektionen, einstellungen, caches, cronjobs, logs, sicherheit, zwei-faktor, sicherungen, upgrades, störfallübung, three days, administrators, roles, sections, settings, security, two-factor, backups, incident drill
type: help
---
# Bootcamp 3.2: Das dreitägige Administratoren-Bootcamp

Ein Administrator muss eine Website sicher, schnell und aktuell halten und sie wiederherstellen können, wenn etwas
kaputtgeht. Drei Tage bieten Raum für jede Routineaufgabe und am Ende für eine Störfallübung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Workshops und Bootcamps](../README.md) |
| Modul | [Modul 3: Dreitägige Bootcamps](README.md) |
| Dauer | 3 Tage mit je 6 Stunden Einheiten |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, 4 bis 12 Teilnehmende |
| Voraussetzungen | Ein Laptop pro Teilnehmer, der die [Anforderungen](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/requirements-for-a-training-machine.md) erfüllt; Grundkenntnisse der Kommandozeile |

## Lernziele

Am Ende des Bootcamps können die Teilnehmenden:

- eine Installation einrichten, ausliefern und zurücksetzen;
- Benutzer, Gruppen, Rollen, Richtlinien und Sektionen verwalten;
- Einstellungen lesen und ändern, die richtigen Caches leeren, Cronjobs ausführen und Logs lesen;
- eine Website härten, die Zwei-Faktor-Anmeldung einschalten, sichern, wiederherstellen und aktualisieren;
- einen Störfall von der Fehlerreferenz bis zur Behebung bearbeiten.

## Der Ablauf

| Tag | Vormittag (3 Std.) | Nachmittag (3 Std.) |
|---|---|---|
| 1 | Installieren und ausliefern ([Lektion 2.3](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/one-install-per-learner.md), [2.4](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md)); [Ein Rundgang durch die Administration](../../../rundgang-durch-die-administration.md) | Benutzer, Gruppen, Rollen und Richtlinien ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)) |
| 2 | Sektionen ([Sektionen](../../../../anleitungen/sektionen.md)); Einstellungen und INI ([Einstellungsseite](../../../../anleitungen/einstellungsseite.md), [exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md)) | Caches ([Caches](../../../../anleitungen/caches.md)), Cronjobs ([Cronjobs](../../../../anleitungen/cronjobs.md)), Logs ([Der Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md)) |
| 3 | Sicherheit ([Sicherheitshärtung](../../../sicherheitshaertung.md)), Zwei-Faktor-Anmeldung ([Zwei-Faktor-Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)), Sicherungen | Upgrades ([Upgrades](../../../../anleitungen/aktualisieren.md), [Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)); Störfallübung |

Pausen: jeden Tag 15 Minuten am Vormittag und am Nachmittag, 45 Minuten Mittagspause.

## Hinweise für Trainer

- **Jeder Teilnehmer hat drei Tage lang eine eigene Installation**; halten Sie eine Referenzkopie für schnelles
  Zurücksetzen bereit ([Lektion 2.6](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).
- **Die Störfallübung** (Tag 3, letzte 90 Minuten): Der Trainer bereitet vorab auf jeder Installation einen Fehler vor
  (zum Beispiel ein Cache-Verzeichnis, das root gehört, oder einen eingeschalteten Wartungsmodus); die Teilnehmenden
  finden und beheben ihn mit der [Fehlerbehebung](../../../../installation/12-fehlerbehebung.md).
- **Upgrades** auf einer Schulungsinstallation: Aktualisieren Sie auf ein neueres Release derselben Versionslinie nach
  [Mit Git aktualisieren](../../../mit-git-aktualisieren.md), wenn die Gruppe mit Git-Klonen arbeitet.

## Praktische Übung: das Abschlussprojekt des Bootcamps

1. Richten Sie eine Gruppe „Newsroom“ mit einer eingeschränkten Rolle und einer eigenen Sektion ein.
2. Schalten Sie die Zwei-Faktor-Anmeldung für einen Testbenutzer ein, wie in der Anleitung beschrieben.
3. Sichern Sie die Installation (Datenbankdatei und `var/storage`), ändern Sie etwas, stellen Sie sie wieder her und
   leeren Sie die Caches.
4. Finden und beheben Sie in der Störfallübung den vorbereiteten Fehler und schreiben Sie eine dreizeilige
   Störfallnotiz: Symptom, Ursache, Behebung.

## Erwartetes Ergebnis

- Rolle und Sektion funktionieren für den Testbenutzer.
- Die Wiederherstellung hat den früheren Zustand zurückgebracht.
- Die Störfallnotiz nennt Symptom, Ursache und Behebung mit dem verwendeten Befehl.

## Quiz (Ende des Bootcamps)

1. Was leert `--clear-tag=ini`, das `--clear-id=ini` stehen lässt?
2. Wo findet man eine Fehlerreferenz `ERR-...`?
3. Eine Seite zeigt nach einer fehlgeschlagenen Installation die Wartungsseite. Was führen Sie aus?
4. Warum gehören die 2FA-Richtlinien in die Rollen, die sich anmelden?

<details>
<summary>Antworten</summary>

1. Den globalen INI-Cache ([Fehlerbehebung, 12.12](../../../../installation/12-fehlerbehebung.md#1212-caches-und-änderungen-die-nicht-sichtbar-werden)).
2. In `var/log/error.log` und seinen rotierten Dateien oder unter **Setup > Logdateien**.
3. `php bin/php/maintenance.php status`, dann `off`, nachdem die Ursache behoben ist.
4. Damit Benutzer ihren zweiten Faktor selbst einrichten und bestätigen können ([Zwei-Faktor-Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)).

</details>

## Weiterführende Informationen

- [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Upgrades](../../../../installation/11-aktualisieren.md)

## Verwandte Seiten

- [Modul 3: Dreitägige Bootcamps](README.md)
- [Workshops und Bootcamps: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
