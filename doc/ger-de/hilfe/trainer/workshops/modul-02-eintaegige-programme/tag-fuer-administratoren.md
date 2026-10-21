---
title: "Agenda 2.2: Der Tag für Administratoren"
audience: trainer, admin
category: training
order: 22
summary: Ein eintägiger Workshop für neue Website-Administratoren, eine Website installieren, Benutzer und Rollen, Sektionen, Caches, Cronjobs, Logs, Systeminformationen und die Upgrade-Prüfung, alles einmal auf der eigenen Installation geübt. 6 Stunden Einheiten.
keywords: ein tag, administratoren, workshop-agenda, rollen, richtlinien, caches, cronjobs, logs, systeminformationen, upgrade-prüfung, installation, one day, administrators, workshop agenda, roles, policies, system information, upgrade check, install
type: help
---
# Agenda 2.2: Der Tag für Administratoren

Neue Administratoren müssen wissen, wo alles ist und wie sie die Routineaufgaben sicher erledigen. Dieser Tag gibt jeder
Person ihre eigene Installation und geht die Aufgaben durch, die sie jede Woche erledigen wird, mit der Anleitung zu
jeder.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Workshops und Bootcamps](../README.md) |
| Modul | [Modul 2: Eintägige Agenden](README.md) |
| Dauer | 6 Stunden Einheiten (9:00 bis 16:30 mit Pausen und Mittagspause) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Neue Website-Administratoren, 4 bis 12 Teilnehmende |
| Voraussetzungen | Ein Laptop pro Person, der die [Anforderungen](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/requirements-for-a-training-machine.md) erfüllt; Grundkenntnisse der Kommandozeile |

## Lernziele

Am Ende des Tages können die Teilnehmenden:

- eine Schulungs-Website installieren und ausliefern und sich sicher anmelden;
- eine Rolle mit eingeschränkten Richtlinien anlegen und einer Gruppe zuweisen;
- den richtigen Cache für eine Änderung leeren und die Logs lesen;
- die Cronjobs von Hand ausführen und die Systeminformationen und die Upgrade-Prüfung finden.

## Die Agenda

| Zeit | Block | Verwendet | Kontrollpunkt |
|---|---|---|---|
| 9:00 | Begrüßung; installieren und ausliefern (60 Min.) | [Eine Installation pro Person](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/one-install-per-learner.md), [Die Websites der Übungsumgebung ausliefern](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md) | Website ausgeliefert; Passwort geändert |
| 10:00 | Der Rundgang durch die Administration für Administratoren (30 Min.) | [Ein Rundgang durch die Administration](../../../rundgang-durch-die-administration.md) | Bereich Setup geöffnet |
| 10:30 | Pause (15 Min.) | | |
| 10:45 | Benutzer, Gruppen, Rollen und Richtlinien (75 Min.) | [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md) | Eine Rolle „News editors“, einer Gruppe zugewiesen |
| 12:00 | Mittagspause (45 Min.) | | |
| 12:45 | Sektionen (30 Min.) | [Sektionen](../../../../anleitungen/sektionen.md) | Eine Sektion angelegt und zugewiesen |
| 13:15 | Caches und Logs (60 Min.) | [Caches](../../../../anleitungen/caches.md), [Der Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md) | Ein Cache unter **Setup > Caches** geleert; `error.log` geöffnet |
| 14:15 | Pause (15 Min.) | | |
| 14:30 | Cronjobs (40 Min.) | [Cronjobs](../../../../anleitungen/cronjobs.md) | Cronjobs einmal von Hand ausgeführt |
| 15:10 | Systeminformationen und Upgrade-Prüfung (35 Min.) | [Systeminformationen](../../../../anleitungen/systeminformationen.md), [Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) | Version und PHP abgelesen; Upgrade-Prüfung geöffnet |
| 15:45 | Abschluss: was wöchentlich zu tun ist; Rückmeldung (45 Min.) | [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md) | Wöchentliche Checkliste geschrieben |
| 16:30 | Ende | | |

Streichpunkte, in dieser Reihenfolge: der Block zu Sektionen (in die Nachbereitung verschieben); die Upgrade-Prüfung.

## Hinweise für Trainer

- **Proben Sie die Installation auf jedem Rechnertyp**, den die Teilnehmenden mitbringen.
- **Rollen sind das Herzstück des Tages.** Lesen Sie eine Rolle auf ihrer Seite als Sätze, bevor Sie eine anlegen.
- **Caches**: Vermitteln Sie „den kleinsten Cache, der die Änderung abdeckt“ aus
  [Fehlerbehebung, 12.12](../../../../installation/12-fehlerbehebung.md#1212-caches-und-änderungen-die-nicht-sichtbar-werden).
- **Nach dem Tag** verweisen Sie auf den vollständigen Kurs Website-Administration.

## Praktische Übung: das Abschlussprojekt des Tages

Auf der eigenen Installation der Person:

1. Legen Sie eine Benutzergruppe „Newsroom“ und darin einen Benutzer mit einer Adresse unter `example.invalid` an.
2. Legen Sie die Rolle „News editors“ mit `content/read`, `content/create` und `content/edit`, eingeschränkt auf einen
   Teilbaum, an und weisen Sie sie „Newsroom“ zu.
3. Melden Sie sich als der neue Benutzer an und prüfen Sie, was er tun kann; melden Sie sich ab.
4. Leeren Sie den Template-Cache unter **Setup > Caches** und suchen Sie den Eintrag dazu in den Logs oder im
   Audit-Protokoll, falls Ihre Installation ihn zeigt.
5. Schreiben Sie die wöchentliche Checkliste: Logs, Cronjobs, Caches, Updates.

## Erwartetes Ergebnis

- Die Rollenseite von „News editors“ zeigt ihre Richtlinien als Sätze und die Zuweisung an „Newsroom“.
- Der neue Benutzer kann nur im gewählten Teilbaum bearbeiten.
- Eine wöchentliche Checkliste mit vier oder mehr Punkten.

## Quiz (am Ende des Tages)

1. Was bewirkt eine Rolle, bevor sie zugewiesen ist?
2. Eine Template-Änderung erscheint nicht. Welchen Cache leeren Sie?
3. Wo lesen Sie das Fehlerlog im Browser?
4. Warum weist man Rollen Gruppen zu?

<details>
<summary>Antworten</summary>

1. Nichts ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)).
2. Den Template-Cache: `php bin/php/ezcache.php --clear-tag=template` oder unter **Setup > Caches**.
3. **Setup > Logdateien** ([Der Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md)).
4. Jedes Mitglied, jetzt und später, bekommt die Rolle.

</details>

## Weiterführende Informationen

- [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Anmeldung mit zwei Faktoren](../../../../anleitungen/zwei-faktor-anmeldung.md)

## Verwandte Seiten

- [Modul 2: Eintägige Agenden](README.md)
- [Workshops und Bootcamps: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
