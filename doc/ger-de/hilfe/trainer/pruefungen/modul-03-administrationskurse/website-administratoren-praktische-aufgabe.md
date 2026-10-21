---
title: "Lektion 3.2: Website-Administratoren, praktische Aufgabe"
audience: trainer, admin
category: training
order: 32
summary: Eine praktische Aufgabe von 60 Minuten für das Ende des Kurses Website-Administration, eine Sektion und eine eingeschränkte Rolle anlegen, sie einer Gruppe zuweisen, die Einschränkung mit einem Testbenutzer nachweisen, eine Cache-Gruppe leeren, einen Cronjob-Teil ausführen und die Schulungs-Website vom Netz nehmen und zurückholen, bewertet mit einem Bewertungsraster aus sechs Kriterien. 85 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, website-administration, praktische aufgabe, bewertungsraster, neue sektion, neue rolle, neue richtlinie, zuweisung mit einschränkung, testbenutzer, example.invalid, caches, cronjobs, wartungsmodus, assessments and practical exercises, site administration, practical task, rubric, new section, new role, new policy, assign with limitation, test user, maintenance mode
type: help
---
# Lektion 3.2: Website-Administratoren, praktische Aufgabe

Eine echte Administrationsaufgabe in einer Stunde: Ein neues Team braucht einen eigenen Bereich der Website und eine
Rolle, mit der es dort bearbeiten darf und nirgendwo sonst. Die Teilnehmenden richten das ein, weisen mit einem
Testkonto nach, dass es funktioniert, und erledigen drei Routineaufgaben: einen Cache, einen Cronjob und ein
Wartungsfenster.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Alle Teilnehmenden arbeiten als
> Administrator auf ihrer eigenen Schulungsinstallation. Testbenutzer erhalten Adressen unter `example.invalid`; der
> Mail-Transport ist `file`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 3: Die Administrationskurse prüfen](README.md) |
| Dauer | 85 Minuten (5 Einweisung, 60 Aufgabe, 20 Bewertung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Website-Administration |
| Voraussetzungen | [Lektion 3.1](website-administratoren-schriftlicher-test.md); ein Administratorkonto auf der eigenen Schulungsinstallation der Teilnehmenden |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- eine Sektion anlegen und ihr Inhalte zuweisen;
- eine Rolle mit eingeschränkten Richtlinien anlegen und einer Gruppe zuweisen;
- eine Berechtigung mit einem eigenen Testkonto nachweisen;
- gezielt eine Cache-Gruppe leeren;
- einen Cronjob-Teil aus der Administration ausführen;
- eine Website mit einer Meldung vom Netz nehmen und zurückholen.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: eine Schulungsinstallation pro Teilnehmer, frisch installiert, mit dem Mail-Transport auf `file`. Die
   Teilnehmenden melden sich als Administrator der Installation an.
2. **Einweisung**: Teilen Sie die Schritte und das Bewertungsraster aus. 60 Minuten. Die Anleitungen dürfen geöffnet
   sein.
3. **Bewertung**: Lesen Sie auf jeder Installation **Setup > Sektionen**, die Seite der Rolle, die Gruppe des
   Testbenutzers, die letzten Läufe auf der Cronjob-Seite und die letzten Änderungen auf der Wartungsseite (die letzten
   acht Umschaltungen werden aufgeführt).

### Häufige Fragen

**Eine Teilnehmerin sperrt sich mit dem Wartungsmodus aus.** Der Wartungsmodus lässt die Administration erreichbar,
wenn er über die Seite eingeschaltet wird. Ist eine Teilnehmerin trotzdem ausgesperrt, holt
`php bin/php/maintenance.php off` die Website zurück. Bewerten Sie den Schritt und geben Sie die Zeit zurück, wenn der
Fehler nicht bei ihr lag.

**Dürfen die Teilnehmenden die Shell statt des Browsers verwenden?** Ja, wo das Ergebnis dasselbe ist.

### Stolperfallen

- **Testen mit dem Administratorkonto.** Administratoren sind nicht durch Sektionen eingeschränkt; der Test braucht den
  Testbenutzer.
- **Den Rollenentwurf vergessen.** Nichts ändert sich bis **Speichern**. Wer den Editor ohne Speichern geschlossen
  hat, hat keine Rolle.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Verwenden Sie Ihre eigene Schulungsinstallation, angemeldet
als Administrator.

1. Drücken Sie unter **Setup > Sektionen** auf **Neue Sektion**. Nennen Sie sie `Press area`; behalten Sie den
   vorgeschlagenen Bezeichner und den Navigationsteil **Inhaltsstruktur**. Drücken Sie **Sektion anlegen**.
2. Erstellen Sie unterhalb der Inhaltswurzel einen **Folder** mit dem Namen `Press` und weisen Sie ihn mit allem
   darunter der Sektion `Press area` zu (**Inhalt zuweisen** auf der Seite der Sektion oder in den Details des
   Ordners).
3. Legen Sie unterhalb der Benutzer eine Benutzergruppe `Press team` an.
4. Drücken Sie unter **Rollen und Richtlinien** auf **Neue Rolle**, nennen Sie sie `Press editors` und fügen Sie mit
   **Neue Richtlinie** Richtlinien hinzu: `content` `read` eingeschränkt auf die Sektion `Press area`; `content`
   `create` und `content` `edit` eingeschränkt auf die Sektion `Press area`. Drücken Sie **Speichern**.
5. Drücken Sie auf der Seite der Rolle **Zuweisen** und wählen Sie die Gruppe `Press team`.
6. Legen Sie in `Press team` einen Benutzer `press01` mit der Adresse `press01@example.invalid` und einem Passwort an,
   das Sie notieren.
7. Melden Sie sich in einem privaten Browserfenster als `press01` an. Erstellen Sie einen Artikel in `Press`. Versuchen
   Sie dann, ein Element außerhalb von `Press` zu bearbeiten. Notieren Sie, was Sie sehen. Melden Sie sich ab.
8. Leeren Sie unter **Setup > Caches** nur die Gruppe **Templates**. Notieren Sie die Meldung, die folgt.
9. Führen Sie unter **Setup > Cronjobs** den Teil, der `workflow.php` enthält (standardmäßig der Teil *frequent*), mit
   **Teil starten** aus und beobachten Sie die **Ausgabe**.
10. Nehmen Sie unter **Setup > Wartung** die Website mit der Meldung `Training maintenance, back in 15 minutes`, einer
    erwarteten Dauer von 15 Minuten und mit Zugang für sich selbst vom Netz; setzen Sie das Häkchen zur Bestätigung
    und drücken Sie **Website offline nehmen**. Öffnen Sie die öffentliche Website im privaten Fenster: Notieren Sie,
    was sie zeigt. Drücken Sie dann **Website wieder online bringen**.

## Erwartetes Ergebnis

- **Setup > Sektionen** führt `Press area` mit Objekten auf; der Ordner `Press` zeigt diese Sektion in seinen Details.
- Die Rolle `Press editors` hat drei Richtlinien, die auf `Press area` eingeschränkt sind, und ist `Press team`
  zugewiesen.
- `press01` hat einen Artikel in `Press` erstellt und konnte außerhalb nicht bearbeiten.
- Die Cache-Meldung nannte die Gruppe Templates; die Cronjob-Seite führt den Lauf auf; die letzten Änderungen auf der
  Wartungsseite zeigen eine Umschaltung offline und eine online, durch die Teilnehmerin.
- Der Trainer prüft jede Seite in der Administration und bittet die Teilnehmenden, Schritt 7 zu erklären.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Sektion | Keine | Angelegt, keine Inhalte zugewiesen | Inhalte zugewiesen, nicht der ganze Teilbaum | Sektion angelegt, Ordner und Teilbaum zugewiesen | **Setup > Sektionen**, die Details des Ordners |
| Rolle und Richtlinien | Keine Rolle gespeichert | Rolle gespeichert, Richtlinien uneingeschränkt | Eingeschränkt, eine Funktion fehlt | read, create, edit auf die Sektion eingeschränkt | Die Seite der Rolle |
| Zuweisung | Nicht zugewiesen | Einem einzelnen Benutzer zugewiesen | Der Gruppe zugewiesen, Testbenutzer anderswo | Gruppe zugewiesen, Testbenutzer darin | Seite der Rolle, die Gruppe |
| Nachweis mit Testbenutzer (Pflicht) | Kein Test | Mit dem Administrator getestet | Getestet, Ergebnis nicht erklärt | Mit `press01` getestet, beide Ergebnisse erklärt | Die Notiz der Teilnehmenden, der Artikel |
| Routineaufgaben | Keine | Eines von Cache, Cronjob | Beides, eines nicht wie verlangt | Nur die Gruppe Templates, der richtige Teil ausgeführt | Cache-Meldung, letzte Cronjob-Läufe |
| Wartung | Nicht erledigt | Offline, nicht wieder online | Erledigt, Meldung oder Dauer fehlt | Meldung, Dauer, eigener Zugang, wieder online | Letzte Änderungen der Wartung |

**Bestanden**: 12 von 18 Punkten oder mehr und **Nachweis mit Testbenutzer** mit 2 oder 3. Ohne Test weiß niemand, ob
eine Rolle funktioniert.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Warum konnte das Administratorkonto die Rolle nicht nachweisen?
2. Besucher können `Press` nicht sehen. Ist das hier richtig, und was würden Sie hinzufügen, wenn sie es sollten?
3. Warum nur die Gruppe Templates leeren und nicht alle Caches?
4. Was wäre mit Freigaben geschehen, wenn der Teil *frequent* nie gelaufen wäre?

<details>
<summary>Antworten</summary>

1. Die Richtlinien von Administratoren sind nicht durch Sektionen eingeschränkt, daher ist alles erlaubt
   ([Sektionen](../../../../anleitungen/sektionen.md#5-eine-sektion-anlegen)).
2. Ja, für einen Pressebereich im Aufbau; für Besucher eine Richtlinie für die Rolle Anonymous, die Sektion zu lesen
   ([Sektionen](../../../../anleitungen/sektionen.md#kurz-gesagt)).
3. Alles zu leeren kostet Zeit, während die Caches neu aufgebaut werden; leeren Sie, was die Änderung braucht
   ([Caches](../../../../anleitungen/caches.md)).
4. Freigegebene Inhalte würden nie veröffentlicht ([Workflows](../../../../anleitungen/workflows.md#kurz-gesagt)).

</details>

## Weiterführende Literatur

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md#3-eine-rolle-mit-eingeschränkten-richtlinien-anlegen):
  der Richtlinien-Assistent.
- [Sektionen](../../../../anleitungen/sektionen.md#5-eine-sektion-anlegen): das Sektionsformular.
- [Cronjobs](../../../../anleitungen/cronjobs.md) und [Wartungsmodus](../../../../anleitungen/wartungsmodus.md).

## Verwandte Seiten

- [Lektion 3.1: Website-Administratoren, schriftlicher Test](website-administratoren-schriftlicher-test.md)
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
