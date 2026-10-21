---
title: "Lektion 3.1: Website-Administratoren, schriftlicher Test"
audience: trainer, admin
category: training
order: 31
summary: Ein schriftlicher Test mit vierzehn Fragen für das Ende des Kurses Website-Administration, zu Rollen und Richtlinien, Sektionen, Caches, Cronjobs, Workflows, Wartungsmodus und Audit, mit einem Lösungsschlüssel, in dem jede Antwort eine Anleitung zitiert. 30 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, website-administration, schriftlicher test, rollen, richtlinien, zuweisung mit einschränkung, sektionen, caches, cronjobs, workflows, freigabe, wartungsmodus, audit, assessments and practical exercises, site administration, written test, roles, policies, assign with limitation, sections, approval, maintenance mode
type: help
---
# Lektion 3.1: Website-Administratoren, schriftlicher Test

Dieser Test schließt den Kurs Website-Administration ab. Er prüft die Begriffe, die Administratoren brauchen, um eine
Website sicher und lauffähig zu halten: wer was tun darf, wie Inhalte für Berechtigungen gruppiert werden, welcher
Cache zu leeren ist, warum Cron wichtig ist, wie eine Freigabe funktioniert und wie man die Website vom Netz nimmt und
wieder zurückholt.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die zitierten Seiten beschreiben das
> Design Admin 4; prüfen Sie die Beschriftungen auf Ihrer Schulungsinstallation.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 3: Die Administrationskurse prüfen](README.md) |
| Dauer | 45 Minuten (5 Einweisung, 30 Test, 10 Besprechung der Antworten) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Website-Administration |
| Voraussetzungen | Der Kurs Website-Administration; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- Rollen, Richtlinien, Einschränkungen und Zuweisungen erklären und gefährliche Richtlinien erkennen;
- erklären, wofür eine Sektion verwendet wird und warum neue Sektionen für Besucher unsichtbar sind;
- den richtigen Cache zum Leeren wählen und wissen, wo sich Velocity unterscheidet;
- erklären, warum der Workflow-Cronjob für Freigaben nötig ist;
- den Wartungsmodus beschreiben und den Weg zurück, wenn die Administration nicht erreichbar ist.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 30 Minuten.
2. Gehen Sie die Antworten mit den zitierten Anleitungen auf dem Projektor durch.
3. Bewerten Sie mit der Regel unter **Bewertung**.

### Häufige Fragen

**Manche Funktionen gibt es in unserer Schulungsversion nicht.** Entfernen Sie die Frage; prüfen Sie nicht, was die
Installation nicht zeigen kann.

**Gehört die Kommandozeile dazu?** Die Fragen 8 und 13 nennen Befehle. Ist Ihr Kurs im Browser geblieben, akzeptieren
Sie den Weg über den Browser als Antwort.

### Stolperfallen

- **Standardsektionen.** Frage 5 nennt die Sektionen einer neuen Installation. Ein Seitenpaket kann weitere hinzufügen;
  die fünf sind trotzdem vorhanden.
- **Velocity.** Haben Ihre Teilnehmenden Velocity nie verwendet, streichen Sie Frage 9.

## Der Test

1. Was ist der Unterschied zwischen einer Rolle und einer Richtlinie?
2. Sie haben eine Rolle angelegt, aber niemand hat ihre Rechte. Welcher Schritt fehlt?
3. Warum weisen Sie eine Rolle lieber einer Benutzergruppe zu als einzelnen Benutzern?
4. Eine Rolle hat eine Richtlinie für das Modul **role**. Warum kennzeichnet die Rollenliste sie als Risiko?
5. Nennen Sie die fünf Sektionen einer neuen Installation.
6. Sie haben Inhalte in eine neue Sektion verschoben, und Besucher sehen sie nicht mehr. Warum?
7. Wann kann eine Sektion entfernt werden?
8. Die Template-Änderung einer Redakteurin wird nicht angezeigt. Welchen Cache-Tag leeren Sie zuerst, über
   **Setup > Caches** oder in der Shell?
9. Sie haben die INI-Caches geleert, aber eine von Velocity bereitgestellte Website verwendet noch die alte
   Einstellung. Was ist zusätzlich nötig?
10. Inhalte wurden vor Stunden freigegeben und sind immer noch ausstehend. Welchen Cronjob prüfen Sie?
11. Was bewirkt **Abbrechen...** bei einem wartenden Workflow-Prozess? Werden Inhalte gelöscht?
12. Ihre Website ist im Wartungsmodus. Welchen Statuscode erhalten die Besucher, und können Sie die Administration
    noch erreichen?
13. Die Administration ist während der Wartung nicht erreichbar, und nichts anderes funktioniert. Was beendet die
    Wartung?
14. Wo sehen Sie, wer die Wartung eingeschaltet und wer eine Sektion geändert hat?

<details>
<summary>Antworten</summary>

1. Eine Rolle ist eine Sammlung von Richtlinien; eine Richtlinie erlaubt ihren Benutzern, ein Modul oder eine Funktion
   eines Moduls zu verwenden, möglicherweise eingeschränkt
   ([Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md#kurz-gesagt)).
2. Sie muss Benutzern oder Gruppen zugewiesen werden; eine Rolle bewirkt nichts, bis sie zugewiesen ist
   ([Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md#kurz-gesagt)).
3. Jedes Mitglied der Gruppe, jetzt und künftig, erhält die Rolle
   ([Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).
4. Ihre Benutzer können Rollen ändern und sich so jeden Zugriff selbst geben
   ([Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md#kurz-gesagt)).
5. Standard, Users, Media, Setup und Design
   ([Sektionen](../../../../anleitungen/sektionen.md#kurz-gesagt)).
6. Die Rolle Anonymous darf nur Standard lesen; Inhalte in einer neuen Sektion sind für Besucher verborgen, bis eine
   Richtlinie sie erlaubt ([Sektionen](../../../../anleitungen/sektionen.md#kurz-gesagt)).
7. Nur wenn nichts sie verwendet: kein Objekt (einschließlich Entwürfen und archivierten), keine Richtlinie mit einer
   Sektionseinschränkung, die sie nennt, keine Rollenzuweisung, die auf sie eingeschränkt ist
   ([Sektionen](../../../../anleitungen/sektionen.md#kurz-gesagt)).
8. Die Template-Caches, über den Tag `template` (**Clear content / template / INI caches…** oder
   `php bin/php/ezcache.php --clear-tag=template --allow-root-user`)
   ([Caches](../../../../anleitungen/caches.md#5-in-einer-shell)).
9. Velocity neu starten, denn es hält Einstellungen im Speicher
   ([Caches](../../../../anleitungen/caches.md#kurz-gesagt)).
10. Den Workflow-Cronjob (`workflow.php`, standardmäßig im Teil *frequent*); ohne ihn werden freigegebene Inhalte nie
    veröffentlicht ([Workflows](../../../../anleitungen/workflows.md#kurz-gesagt)).
11. Der Workflow hält an, eine ausstehende Version wird wieder zum Entwurf, eine Freigabeanfrage wird geschlossen, und
    der Prozess wird entfernt. Es werden keine Inhalte gelöscht
    ([Workflows](../../../../anleitungen/workflows.md#kurz-gesagt)).
12. `503 Service Unavailable` mit `Retry-After`; die Administration unter `/admin` bleibt erreichbar
    ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md#kurz-gesagt)).
13. Das Löschen der Zustandsdatei `var/maintenance.json`
    ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md#kurz-gesagt)).
14. Im Audit-Protokoll: die Wartung als `system.maintenance.change`, Sektionen als `content.section.change`
    ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md#kurz-gesagt),
    [Sektionen](../../../../anleitungen/sektionen.md#5-eine-sektion-anlegen)).

</details>

## Bewertung

- Ein Punkt pro Frage; Frage 5 braucht für den Punkt alle fünf Namen (einen halben Punkt für vier).
- Bestanden: 10 von 14 Punkten oder mehr, mit den Fragen 4 und 10 richtig: Die eine betrifft die Sicherheit, die
  andere ist der häufigste Fehler der Art „meine Inhalte hängen fest“.
- Geben Sie Feedback wie in [Lektion 1.6](../modul-01-pruefungen-gestalten/rueckmeldung-zu-ergebnissen-geben.md).

## Weiterführende Literatur

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md)
- [Sektionen](../../../../anleitungen/sektionen.md) und [Caches](../../../../anleitungen/caches.md)
- [Workflows](../../../../anleitungen/workflows.md) und [Wartungsmodus](../../../../anleitungen/wartungsmodus.md)

## Verwandte Seiten

- [Lektion 3.2: Website-Administratoren, praktische Aufgabe](website-administratoren-praktische-aufgabe.md)
- [Modul 3: Die Administrationskurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
