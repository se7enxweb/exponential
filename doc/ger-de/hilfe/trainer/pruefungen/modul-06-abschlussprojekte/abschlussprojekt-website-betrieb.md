---
title: "Lektion 6.3: Abschlussprojekt, eine Website betreiben"
audience: trainer, admin, hosting
category: training
order: 63
summary: Ein zweitägiges Abschlussprojekt für Administratoren und Hosting-Mitarbeiter, die Schulungs-Website einer erfundenen Organisation übernehmen, Rollen und Sektionen, Cron und Suche, eine Sicherung und eine getestete Wiederherstellung, ein Deployment und ein Wartungsfenster einrichten, zwei vom Trainer eingespielte Vorfälle bewältigen und dann ein Betriebshandbuch übergeben, bewertet mit einem Bewertungsraster aus sieben Kriterien. Zwei Tage plus Präsentationen.
keywords: prüfungen und praktische übungen, abschlussprojekt, website-betrieb, website-administration, hosting, rollen, sektionen, cronjobs, verzögerte indizierung, sicherung, wiederherstellungstest, deployment, wartung, vorfall, betriebshandbuch, bewertungsraster, assessments and practical exercises, capstone, site operations, site administration, roles, sections, delayed indexing, backup, restore test, deploy, maintenance, incident, runbook, rubric
type: help
---
# Lektion 6.3: Abschlussprojekt, eine Website betreiben

Zwei Tage lang sind die Teilnehmenden Administratoren der Website einer erfundenen Organisation, auf einer
Schulungsinstallation. Sie richten sie für die tägliche Arbeit ein, weisen nach, dass die Sicherungen funktionieren,
und bewältigen zwei Vorfälle, die der Trainer einspielt. Das abschließende Ergebnis ist ein Betriebshandbuch, dem ein
anderer Administrator folgen könnte.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Jeder Vorfall wird vom Trainer auf einer
> Schulungsinstallation eingespielt. Mail-Transport `file`; Testbenutzer unter `example.invalid`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 6: Abschlussprojekte](README.md) |
| Dauer | Zwei Tage: 15 Minuten Einweisung, etwa 10 Stunden Arbeit mit drei Zwischenständen, 15 Minuten Präsentation pro Teilnehmer, 1 Stunde Bewertung |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Teilnehmende am Ende von Website-Administration sowie Hosting und Betrieb (Suche optional) |
| Voraussetzungen | Diese Kurse; eine Schulungsinstallation auf SQLite und eine Shell pro Teilnehmer |

## Lernziele

Wer dieses Abschlussprojekt besteht, hat gezeigt, dass er Folgendes kann:

- einem Team begrenzten, getesteten Zugriff auf seinen Teil einer Website geben;
- Cron einplanen und die Suche aktuell halten;
- konsistent sichern und eine Wiederherstellung auf einer sicheren Kopie nachweisen;
- eine Änderung in der richtigen Reihenfolge in Betrieb nehmen und ein Wartungsfenster durchführen;
- einen Vorfall diagnostizieren, beheben und schriftlich festhalten.

## Hinweise für Trainer

### So führen Sie es durch

1. **Vorher**: eine SQLite-Schulungsinstallation pro Teilnehmer mit Inhalten, ohne installierte Crontab,
   `Transport=file`. Bereiten Sie zwei Vorfälle vor, die Sie an Tag 2 einspielen, beide umkehrbar, zum Beispiel:
   verzögerte Indizierung eingeschaltet, während Cron nicht läuft (neue Inhalte werden nicht gefunden), und eine Datei
   unter `var/`, die für den Benutzer der Website nicht mehr beschreibbar ist (ein Cache kann nicht geschrieben
   werden). Notieren Sie, wie Sie jeden eingespielt haben, damit Sie ihn rückgängig machen können.
2. **Zwischenstände**: Ende des Vormittags von Tag 1 (Rollen und Sektionen), Ende von Tag 1 (Cron, Suche, Sicherung),
   Mittag von Tag 2 (Wiederherstellungstest erledigt, erster Vorfall).
3. **Vorfälle**: Spielen Sie einen am Vormittag und einen nach dem Mittagessen von Tag 2 ein, jeweils mit einer kurzen
   „Benutzermeldung“ in einfachen Worten („Neue Artikel tauchen seit gestern nicht in der Suche auf“).
4. **Präsentation**: Die Teilnehmenden gehen das Betriebshandbuch durch und beheben live ein Problem, das der Trainer
   nennt.

### Stolperfallen

- **Vorfälle, die sich nicht rückgängig machen lassen.** Spielen Sie niemals etwas ein, das Daten löscht. Bleiben Sie
  bei Einstellungen und Berechtigungen.
- **Teilnehmende, die als root arbeiten.** Rechnen Sie an Tag 1 damit; der Vorfall mit den Berechtigungen zeigt,
  warum man es nicht tut.

## Praktische Übung

Die Aufgabenstellung, wie sie an die Teilnehmenden ausgeteilt wird.

*Sie sind die neue Administratorin oder der neue Administrator der Website der Hillside Community Library (eine
erfundene Bibliothek). Ihre Vorgängerin hat keine Notizen hinterlassen.*

1. Machen Sie eine Bestandsaufnahme: Siteaccesses, aktive Erweiterungen, Rollen, Sektionen, Cron, Mail-Transport,
   Suchmaschine. Schreiben Sie alles auf.
2. Das Veranstaltungsteam der Bibliothek soll nur den Bereich **Events** bearbeiten: Legen Sie die Sektion, die Rolle,
   die Gruppe und einen Testbenutzer unter `example.invalid` an und weisen Sie die Einschränkung nach.
3. Schreiben Sie die Crontab-Zeilen, die die Website braucht (die Teile default, frequent und infrequent), für den
   Benutzer der Website. Installieren Sie sie nicht auf dem Schulungsrechner; führen Sie jeden Teil einmal von Hand aus
   und notieren Sie das Ergebnis.
4. Sichern Sie die Datenbank und die Dateien in einem Wartungsfenster; stellen Sie sie in einem zweiten Verzeichnis
   als sichere Testkopie wieder her; prüfen Sie sie; schreiben Sie das einzeilige Wiederherstellungsprotokoll.
5. Nehmen Sie eine kleine Änderung, die Ihr Trainer Ihnen gibt (eine Einstellungsänderung oder ein Template), in der
   dokumentierten Reihenfolge in Betrieb und zeigen Sie, dass sie live ist.
6. Führen Sie ein geplantes Wartungsfenster von 15 Minuten mit einer Meldung durch und beenden Sie es.
7. Bewältigen Sie Vorfall 1 und Vorfall 2, wenn sie gemeldet werden: Finden Sie die Ursache, beheben Sie sie und
   schreiben Sie eine Vorfallnotiz (Symptom, Ursache, Behebung, Vorbeugung).
8. Schreiben Sie das Betriebshandbuch: tägliche, wöchentliche und monatliche Prüfungen; wie gesichert und
   wiederhergestellt wird; wie in Betrieb genommen wird; wer was darf.

## Erwartetes Ergebnis

- Die Notizen der Bestandsaufnahme, die durch den Testbenutzer nachgewiesene eingeschränkte Rolle, die Crontab-Zeilen
  mit den Läufen von Hand, eine Sicherung, eine getestete Wiederherstellung mit Protokoll, ein Deployment, ein
  Wartungsfenster in den letzten Änderungen, zwei Vorfallnotizen und ein Betriebshandbuch von einigen Seiten.
- Der Trainer prüft die Installation, die Kopie und die Notizen und verfolgt die Behebung live.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Bestandsaufnahme | Keine | Einige Punkte | Die meisten Punkte | Alle sieben Bereiche festgehalten | Notizen |
| Eingeschränkter Zugriff | Keiner | Rolle ohne Test | Getestet, Einschränkung undicht | Getestet und auf die Sektion beschränkt | Seite der Rolle, Testbenutzer |
| Cron und Suche | Nichts | Zeilen geschrieben, nicht ausgeführt | Ausgeführt, Suche nicht geprüft | Zeilen für den Benutzer der Website, jeder Teil ausgeführt, Suche aktuell | Notizen, Suche |
| Sicherung und Wiederherstellung (Pflicht) | Nichts | Nur Sicherung | Wiederhergestellt, Kopie nicht sicher gemacht | Konsistente Sicherung, sichere Kopie, Prüfungen, Protokoll | Kopie, Notizen |
| Deployment und Wartung | Nichts | Eines davon | Beides, Reihenfolge oder Meldung falsch | Beides, in der dokumentierten Reihenfolge, Wartung beendet | Notizen, letzte Änderungen der Wartung |
| Vorfälle | Keiner behoben | Einer behoben, keine Notiz | Beide behoben, Notizen dünn | Beide behoben, mit Ursache und Vorbeugung | Vorfallnotizen, die Installation |
| Betriebshandbuch | Keins | Bruchstücke | Brauchbar, Lücken | Ein anderer Administrator könnte ihm folgen | Das Betriebshandbuch |

**Bestanden**: 14 von 21 Punkten oder mehr und **Sicherung und Wiederherstellung** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach den Präsentationen.

1. Welcher Vorfall hat am längsten gedauert, und welches Log oder welche Seite hat die Ursache gefunden?
2. Warum werden die Crontab-Zeilen für den Benutzer der Website geschrieben, nicht für root?
3. Was würden Sie der Bibliothek über ihre Sicherungen sagen, mit welchem Nachweis?
4. Welchen Teil Ihres Betriebshandbuchs würden Sie zuerst mit einer Kollegin oder einem Kollegen testen?

<details>
<summary>Antworten</summary>

1. Eine offene Frage; gute Antworten nennen die Seite oder das Log, zum Beispiel die Seite der Such-Backends oder
   `var/log/error.log`.
2. Dateien, die root geschrieben hat, kann der Benutzer der Website später nicht ersetzen
   ([Beschreibbare Verzeichnisse und Berechtigungen](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md#einrichtung-1-ein-benutzer)).
3. Dass sie funktionieren, mit dem Wiederherstellungsprotokoll: Datum, Sicherung, Dauer der Wiederherstellung,
   Ergebnis, wer sie durchgeführt hat
   ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).
4. Eine offene Frage; der Abschnitt zur Wiederherstellung ist eine gute Antwort.

</details>

## Weiterführende Literatur

- [Website-Administratoren, praktische Aufgabe](../modul-03-administrationskurse/website-administratoren-praktische-aufgabe.md)
  und [Hosting und Betrieb, praktische Aufgabe](../modul-04-technische-kurse/hosting-betrieb-praktische-aufgabe.md)
- [Betriebshandbücher](../../../hosting/runbooks/README.md): Vorlagen für Vorfallnotizen.
- [Checkliste für den monatlichen Betrieb](../../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md)

## Verwandte Seiten

- [Modul 6: Abschlussprojekte](README.md)
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
