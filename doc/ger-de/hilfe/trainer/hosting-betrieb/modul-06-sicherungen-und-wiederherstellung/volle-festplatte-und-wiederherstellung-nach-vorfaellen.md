---
title: "Lektion 6.4: Eine volle Festplatte und Wiederherstellungen bei einer Störung"
audience: trainer, hosting, admin
category: training
order: 64
summary: Die Teilnehmenden geben mit dem Runbook für eine volle Festplatte sicher Speicherplatz frei (was entfernt werden darf und was nie) und treffen die Entscheidungen des Runbooks für die Wiederherstellung aus einer Sicherung bei einem Datenverlust und einer Kompromittierung. 40 Minuten.
keywords: hosting und betrieb, volle festplatte, runbook, du, exp:logs prune, truncate, komprimieren, caches leeren, storage nie löschen, aus sicherung wiederherstellen, kompromittierte website, entscheidungen bei störungen, hosting and operations, disk full, restore from backup, compromised site, incident decisions
type: help
---
# Lektion 6.4: Eine volle Festplatte und Wiederherstellungen bei einer Störung

Zwei Störungen landen auf dem Tisch des Sicherungsteams: eine Festplatte, die vollgelaufen ist, und ein Kunde, der die
Website von gestern zurück braucht. Beides geht schief, wenn jemand in Eile löscht oder wiederherstellt. Diese Lektion
probt das Runbook für eine volle Festplatte an einer Schulungsinstallation und die Entscheidungen des Runbooks für die
Wiederherstellung als Planspielübung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 6: Sicherungen und Wiederherstellung](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Mitarbeitende in Rufbereitschaft und Systemadministratoren |
| Voraussetzungen | [Lektion 6.3](wiederherstellen-und-wiederherstellungstests.md); [Lektion 5.2](../modul-05-monitoring-und-logs/log-viewer-und-rotation.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- herausfinden, was auf einer vollen Festplatte gewachsen ist, innerhalb und außerhalb der Installation;
- sagen, was entfernt werden darf (Caches, Log-Archive) und was nie (`var/storage`, `settings/`, die SQLite-Dateien);
- mit dem Cache-Befehl und `exp:logs` statt mit `rm` Speicherplatz freigeben;
- die vier Fragen des Runbooks für die Wiederherstellung (welche Sicherung, wie viel, was geht verloren, wo) für einen
  Datenverlust und für eine Kompromittierung beantworten;
- den beschädigten Zustand aufbewahren, bevor sie darüber wiederherstellen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Runbook: volle Festplatte](../../../hosting/runbooks/festplatte-voll.md) und
[Runbook: aus einer Sicherung wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md).

1. **Symptome** (2 Minuten). Uploads schlagen fehl, Veröffentlichen schlägt fehl, Seiten zeigen Fehler, Logs wachsen
   nicht mehr, die Datenbank verweigert Schreibvorgänge.
2. **Es finden** (3 Minuten). `df -h`, `du -sh var/* | sort -h`, dann außerhalb der Installation: Sicherungen auf
   derselben Festplatte, das Datenverzeichnis des Datenbankservers, Systemlogs, PHP-Sitzungsdateien.
3. **Entfernen und nie** (4 Minuten). Zeigen Sie die Tabelle: Caches über den Cache-Befehl; Log-Archive über
   `exp:logs --prune`; aktuelle Logs über `exp:logs --truncate` geleert, nicht gelöscht; `var/storage`, `settings/` und
   die SQLite-Dateien nie von Hand. Ein Log, das um Megabytes pro Minute wächst, ist ein Symptom: Finden Sie heraus,
   warum.
4. **Entscheidungen zur Wiederherstellung** (6 Minuten). Welche Sicherung (bei einer Kompromittierung eine von vor der
   ersten Änderung des Angreifers), alles oder ein Teil, was geht verloren (sagen Sie es dem Kunden, bevor Sie
   beginnen), an Ort und Stelle oder zuerst auf einem zweiten Rechner. Bewahren Sie den beschädigten Zustand auf.

### Häufige Fragen

**Kann ich `var/cache` mit `rm` löschen?** Verwenden Sie den Cache-Befehl; er kennt die Pfade jeder Website.

**Die SQLite-Datei `-wal` ist riesig.** Sie schrumpft bei einem Checkpoint; `VACUUM` braucht selbst freien
Speicherplatz. Lesen Sie Kapitel 9, bevor Sie etwas ausführen.

**Genügt nach einer Kompromittierung eine Wiederherstellung?** Nein. Das Runbook [Kompromittierte Website](../../../hosting/runbooks/kompromittierte-website.md)
kommt zuerst; die Sicherung muss älter sein als die erste Änderung, und die Lücke muss geschlossen werden.

### Stolperfallen

- **Aktuelle Logdateien löschen** zerstört ihre Berechtigungen für den anderen Schreiber. Verwenden Sie `--truncate`.
- **Die Festplatte einer Person absichtlich füllen.** Tun Sie es nicht; lesen Sie die Größen und verwenden Sie
  Probeläufe.
- **Die Planspielübung als Theorie behandeln.** Bitten Sie jede Gruppe, genau die Sicherungsdatei zu nennen, die sie
  wählen würde.

## Praktische Übung

Teil A verwendet Ihre eigene Schulungsinstallation, als Benutzer der Website. Teil B ist eine Planspielübung in
Gruppen.

1. Prüfen Sie das Dateisystem: `df -h .`.
2. Suchen Sie die größten Verzeichnisse in `var/`: `du -sh var/* | sort -h | tail -10`.
3. Zeigen Sie die Log-Archive und ihre Größen: `php bin/php/console exp:logs --archives`.
4. Sehen Sie sich ein Aufräumen vorab an: `php bin/php/console exp:logs --prune --dry-run`.
5. Zeigen Sie, welche Logs am schnellsten wachsen: `php bin/php/console exp:logs --list`.
6. Notieren Sie für jedes Verzeichnis aus Schritt 2 anhand der Tabelle des Runbooks, ob es aufgeräumt werden darf und
   mit welchem Befehl.
7. Teil B, Szenario 1: Eine Redakteurin hat vor zwei Tagen versehentlich einen ganzen Bereich entfernt und es heute
   bemerkt. Beantworten Sie die vier Fragen aus *Zuerst entscheiden* im Runbook für die Wiederherstellung.
8. Teil B, Szenario 2: Die Website eines Kunden wurde von einem Angreifer verändert; die erste unbekannte Änderung steht
   vor fünf Tagen im Audit-Protokoll. Beantworten Sie dieselben vier Fragen und nennen Sie das Runbook, das Sie zuerst
   öffnen.
9. Jede Gruppe liest ihre Antworten im Raum vor.

## Erwartetes Ergebnis

- Schritt 6: Caches und Log-Archive als mit dem Cache-Befehl und `exp:logs` entfernbar markiert; `var/storage`,
  `settings/` und das SQLite-Verzeichnis als „nie“ markiert.
- Schritt 7: Die Gruppe prüft zuerst Papierkorb und Versionen, wählt dann die neueste Sicherung von vor der Entfernung,
  stellt auf einem zweiten Rechner wieder her und holt den Bereich zurück, und sie sagt dem Kunden, was bei einer
  vollständigen Wiederherstellung verloren ginge.
- Schritt 8: eine Sicherung von vor der ersten Änderung, zuerst das Runbook für eine kompromittierte Website, der
  beschädigte Zustand für die Untersuchung aufbewahrt.
- Der Trainer prüft die Antworten zu beiden Szenarien anhand des Runbooks.

## Quiz

1. Was davon darf bei einer vollen Festplatte entfernt werden?
   A. `var/storage` B. `settings/override` C. rotierte Log-Archive D. die SQLite-Datei `-wal`
2. Warum werden aktuelle Logs mit `exp:logs --truncate` geleert und nicht gelöscht?
3. Welche Sicherung stellen Sie nach einer Kompromittierung wieder her?
4. Warum bewahren Sie den beschädigten Zustand vor einer Wiederherstellung auf?
5. Nennen Sie zwei Orte außerhalb der Installation, die Sie bei einer vollen Festplatte prüfen.

<details>
<summary>Antworten</summary>

1. **C.** Über `exp:logs --prune` ([Runbook: volle Festplatte](../../../hosting/runbooks/festplatte-voll.md#was-entfernt-werden-darf-und-was-nie)).
2. Leeren behält die Dateien und ihre Berechtigungen; ein gelöschtes Log wird möglicherweise mit dem falschen
   Eigentümer neu angelegt.
3. Eine von vor der ersten Änderung des Angreifers.
4. Er kann gebraucht werden, um nach der Sicherung veröffentlichte Inhalte zurückzuholen oder um zu untersuchen.
5. Zwei beliebige von: Sicherungen auf derselben Festplatte, das Datenverzeichnis des Datenbankservers, Systemlogs,
   PHP-Sitzungsdateien.

</details>

## Weiterführende Informationen

- [Runbook: volle Festplatte](../../../hosting/runbooks/festplatte-voll.md)
- [Runbook: aus einer Sicherung wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Runbook: kompromittierte Website](../../../hosting/runbooks/kompromittierte-website.md)
- [Inhaltsverlauf](../../../../anleitungen/inhaltsverlauf.md): Versionen vor einer Wiederherstellung.

## Verwandte Seiten

- [Modul 6: Sicherungen und Wiederherstellung](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
