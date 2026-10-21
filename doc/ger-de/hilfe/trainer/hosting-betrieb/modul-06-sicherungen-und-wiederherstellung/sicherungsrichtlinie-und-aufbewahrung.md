---
title: "Lektion 6.2: Eine Sicherungsrichtlinie und Aufbewahrung"
audience: trainer, hosting, admin
category: training
order: 62
summary: Die Teilnehmenden schreiben anhand der Regeln aus den Quellen (Häufigkeit, Generationen, Ort, Verschlüsselung, Wiederherstellungstests) eine Sicherungsrichtlinie für einen Hosting-Tarif, gleichen sie mit der Aufbewahrung ab, die Exponential selbst einhält, und beschreiben sie Kunden, ohne zu viel zu versprechen. 35 Minuten.
keywords: hosting und betrieb, sicherungsrichtlinie, zeitplan, aufbewahrung, generationen, außer haus, verschlüsselung, point-in-time-recovery, wiederherstellungstest, aufbewahrung des audits, LiveDays, ArchiveDays, personenbezogene daten in sicherungen, hosting and operations, backup policy, retention, generations, off-site, encryption, restore test
type: help
---
# Lektion 6.2: Eine Sicherungsrichtlinie und Aufbewahrung

Befehle erstellen eine Sicherung; eine Richtlinie macht daraus einen Sicherungsdienst. Diese Lektion ist ein kurzer
Workshop: Die Teilnehmenden überführen die Regeln, die die Dokumentation vorgibt, in eine Richtlinientabelle für einen
Hosting-Tarif, gleichen sie mit der Aufbewahrung ab, die Exponential auf seine eigenen Einträge anwendet, und üben, die
Frage „Wie weit können Sie zurückgehen?“ anhand dieser Tabelle zu beantworten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 6: Sicherungen und Wiederherstellung](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Workshop) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Produktmanager, Systemadministratoren und Support-Leitungen |
| Voraussetzungen | [Lektion 6.1](was-gesichert-werden-muss.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Regeln nennen, die die Quellen als Untergrenze jeder Richtlinie setzen;
- die Entscheidungen einer Richtlinie treffen: Häufigkeit, Konsistenz, Generationen, Ort, Verschlüsselung,
  Point-in-Time-Recovery, wer wiederherstellen darf, Wiederherstellungstests;
- aufzählen, wann eine Sicherung außerhalb des Zeitplans erstellt wird (vor einem Upgrade, einer Paketinstallation,
  einem Wechsel des Datenbanksystems, einer Wiederherstellung);
- die Richtlinie mit den Einstellungen zur Aufbewahrung in Exponential vergleichen;
- erklären, warum personenbezogene Daten, die auf der Website gelöscht wurden, in älteren Sicherungen verbleiben
  können, und dass dies eine rechtliche Frage ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Sicherungszeitpläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md).

1. **Die Untergrenze** (3 Minuten). Eine Sicherung besteht aus Datenbank und Dateien im selben Moment; bewahren Sie
   Sicherungen getrennt vom Server auf und behalten Sie mehrere Generationen; sichern Sie vor jedem Upgrade; testen Sie
   mindestens einmal und nach jedem größeren Upgrade eine Wiederherstellung auf einem anderen Rechner; wöchentlich: Es
   gibt eine Sicherung aus den letzten sieben Tagen, und sie wurde einmal wiederhergestellt.
2. **Die Entscheidungen** (3 Minuten). Zeigen Sie die Tabelle der Entscheidungen. Die Dokumentation nennt keine Preise
   und keine eigenen festen Zahlen; jeder Wert ist die Wahl des Hosters.
3. **Aufbewahrung in Exponential** (2 Minuten). Das Audit-Protokoll behält Live-Dateien standardmäßig 90 Tage und
   Archive 730 Tage; das Einwilligungsprotokoll für E-Mails von Personen, die nicht mehr da sind, 1095 Tage; Archive von
   `404.log` und `security.log` werden nach 30 und 90 Tagen aufgeräumt. Eine Sicherung behält sie länger.
4. **Gelöschte Daten** (2 Minuten). Kopien in älteren Sicherungen behalten gelöschte personenbezogene Daten, bis sie
   ablaufen. Die Seite ist keine Rechtsberatung; der Hoster und jeder Kunde holen eigenen Rechtsrat ein.

### Häufige Fragen

**Bietet PostgreSQL Point-in-Time-Recovery?** Seine eigene Replikation und Point-in-Time-Recovery, eingerichtet nach der
PostgreSQL-Dokumentation, wenn ein Tarif eine Wiederherstellung zwischen zwei Dumps verspricht.

**Kann ein Kunde aus einem Panel wiederherstellen?** Ein Hoster kann das anbieten; eine Wiederherstellung durch einen
Kunden darf nie die Daten eines anderen Kunden überschreiben.

**Was sollte die Tarifbeschreibung sagen?** Die Richtlinientabelle mit den Werten des Hosters, damit der Support aus
derselben Tabelle antwortet, die der Kunde gesehen hat.

### Stolperfallen

- **Branchenzahlen erfinden.** Halten Sie sich an die Regeln aus den Quellen und die Werte, die die Gruppe festlegt;
  kennzeichnen Sie sie als die eigenen der Gruppe.
- **Den Workshop zur Rechtsberatung machen.** Benennen Sie die Frage, beantworten Sie sie nicht.
- **Richtlinien ohne Wiederherstellungstests.** Die Tabelle jeder Gruppe muss eine Zeile für Wiederherstellungstests
  haben.

## Praktische Übung

Arbeiten Sie in Gruppen zu zweit oder zu dritt, auf Papier oder in einem gemeinsamen Dokument.

1. Übernehmen Sie die Tabelle der Entscheidungen aus [Sicherungszeitpläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md#die-entscheidungen-einer-backup-richtlinie)
   und fügen Sie eine dritte Spalte „Unser Tarif“ hinzu.
2. Wählen Sie einen gedachten Tarif: Websites kleiner Unternehmen auf einem gemeinsam genutzten Rechner, SQLite, etwa
   fünf Redakteure pro Website.
3. Füllen Sie Häufigkeit, Konsistenz, Generationen und Ort aus.
4. Füllen Sie Verschlüsselung, Point-in-Time-Recovery und wer wiederherstellen darf aus.
5. Füllen Sie die Wiederherstellungstests aus: wie oft und auf welchem Rechner.
6. Fügen Sie eine Zeile „Sicherungen außerhalb des Zeitplans“ hinzu und listen Sie die vier Anlässe von der Seite auf.
7. Vergleichen Sie Ihre Generationen mit der mitgelieferten Aufbewahrung des Audit-Protokolls (90 und 730 Tage) und
   schreiben Sie einen Satz dazu, was das für eine Wiederherstellung ein Jahr zurück bedeutet.
8. Schreiben Sie die Antwort in zwei Sätzen, die der Support auf „Wie weit können Sie unsere Website zurück
   wiederherstellen?“ gibt.
9. Stellen Sie Ihre Tabelle der Gruppe in zwei Minuten vor.

## Erwartetes Ergebnis

- Jede Gruppe hat eine vollständige Tabelle mit einem Wert in jeder Zeile von „Unser Tarif“.
- Die Zeile für Wiederherstellungstests nennt eine Häufigkeit und einen anderen Rechner.
- Die Zeile „außerhalb des Zeitplans“ listet ein Upgrade, eine Paketinstallation, einen Wechsel des Datenbanksystems
  und eine Wiederherstellung auf.
- Die Antwort des Supports passt zu den Generationen in der Tabelle.
- Der Trainer prüft jede Tabelle auf die Regeln aus Schritt 1 der Hinweise für Trainer.

## Quiz

1. Nennen Sie zwei Regeln, die die Untergrenze jeder Sicherungsrichtlinie bilden.
2. Welcher Anlass erfordert keine Sicherung außerhalb des Zeitplans?
   A. ein Upgrade B. ein Wechsel des Datenbanksystems C. das Lesen der Logs D. eine Wiederherstellung
3. Das Audit-Protokoll behält Archive standardmäßig 730 Tage. Ihre Sicherungen reichen 30 Tage zurück. Was bedeutet
   das?
4. Warum können die Daten einer gelöschten Person nach der Löschung noch vorhanden sein?
5. Sagt die Dokumentation, wie viele Generationen ein Hoster behalten muss?

<details>
<summary>Antworten</summary>

1. Zwei beliebige von: Datenbank und Dateien im selben Moment; getrennt vom Server mit mehreren Generationen; vor jedem
   Upgrade; eine auf einem anderen Rechner getestete Wiederherstellung; wöchentlich eine aktuelle Sicherung, die
   mindestens einmal wiederhergestellt wurde.
2. **C.** Die vier Anlässe sind ein Upgrade, eine Paketinstallation, ein Wechsel des Datenbanksystems und eine
   Wiederherstellung.
3. Die Website selbst behält Audit-Archive länger als die Sicherungen; eine Wiederherstellung kann nur 30 Tage
   zurückgehen, aber die Archive auf der laufenden Website reichen weiter.
4. Ältere Sicherungen behalten sie, bis sie ablaufen ([Sicherungszeitpläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md#backups-und-gelöschte-personenbezogene-daten)).
5. Nein. Wie viele und wie lange, ist die Produktentscheidung des Hosters.

</details>

## Weiterführende Informationen

- [Sicherungszeitpläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)
- [Aufbewahrung von Logs und Daten](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)
- [Compliance und DSGVO](../../../hosting/sicherheit/compliance-und-dsgvo.md)
- [SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md): Dienste in einem Tarif beschreiben.

## Verwandte Seiten

- [Modul 6: Sicherungen und Wiederherstellung](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
