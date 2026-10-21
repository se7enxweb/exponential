---
title: "Lektion 2.3: Den Suchindex neu aufbauen"
audience: trainer, admin, hosting
category: training
order: 23
summary: Die Teilnehmenden entscheiden, wann der ganze Suchindex neu aufgebaut werden muss, führen bin/php/updatesearchindex.php mit und ohne --clean auf ihrer Schulungsinstallation aus und lesen seine Ausgabe und seinen Audit-Eintrag. 30 Minuten.
keywords: suchkurs, neu indizieren, suchindex neu aufbauen, updatesearchindex.php, --clean, import, datenbankumzug, audit, data.index.rebuild, search course, reindex, rebuild search index, database move
type: help
---
# Lektion 2.3: Den Suchindex neu aufbauen

Manchmal passt der Index nicht mehr zu den Inhalten: nach einem Import, der direkt in die Datenbank geschrieben hat,
nach einem Datenbankumzug oder nach einer Änderung, welche Attribute durchsuchbar sind. Dann wird der ganze Index mit
einem Skript neu aufgebaut. Diese Lektion vermittelt, wann man es ausführt, wie, und wie man das tut, ohne einer
Live-Website zu schaden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 2: Indizierung](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren und Hosting-Mitarbeitende |
| Voraussetzungen | [Lektion 2.2](verzoegerte-indizierung-und-der-indexcontent-cronjob.md); ein Terminal im Wurzelverzeichnis der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- vier Situationen nennen, die einen vollständigen Neuaufbau verlangen;
- `php bin/php/updatesearchindex.php` mit `--clean` ausführen und erklären, was `--clean` vorher entfernt;
- die Ausgabe des Skripts lesen (*Number of objects to index*) und den Neuaufbau im Audit-Protokoll finden;
- einen Neuaufbau auf einer Live-Website planen: als Benutzer der Website, in einer ruhigen Stunde, mit dem richtigen
  Siteaccess.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Such-Backends](../../../hosting/grundlagen/such-backends.md#den-index-neu-aufbauen) und
[10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung).

1. **Wann** (3 Minuten). Nach einem Import oder einem Datenbankumzug; wenn Suchergebnisse falsch sind; nachdem
   Attribute durchsuchbar oder nicht durchsuchbar gemacht wurden (Lektion 2.4); auf MongoDB einmal nach der
   Installation, wenn die Cronjobs den Index noch nicht aufgebaut haben.
2. **Wie** (4 Minuten). Das Skript liest jedes veröffentlichte Objekt, jeweils 50, entfernt seinen alten Eintrag und
   indiziert es neu und schreibt nach jedem Stapel fest. Mit `--clean` leert es zuerst den ganzen Index (*Cleaning up
   search data*), sodass die Suche immer weniger von der Website findet, bis der Lauf abgeschlossen ist. Ohne
   `--clean` bleiben die alten Einträge auffindbar, während jedes Objekt ersetzt wird.
3. **Wo es festgehalten wird** (3 Minuten). Am Ende schreibt das Skript einen Audit-Eintrag `data.index.rebuild` mit
   der Anzahl der Objekte und ob `--clean` verwendet wurde. Zeigen Sie **Setup > Audit**, wenn die
   Schulungsinstallation Audits aufzeichnet.

### Häufige Fragen

**Sollte ich immer --clean verwenden?** Verwenden Sie es, wenn der Index Müll enthält: Objekte, die es nicht mehr
gibt, oder Wörter aus Attributen, die nicht mehr durchsuchbar sind. Auf einer Live-Website hält ein Lauf ohne
`--clean` die Suche während des Neuaufbaus funktionsfähig.

**Wie lange dauert es?** Das hängt von der Anzahl und Größe der Objekte ab. Führen Sie es einmal auf der
Schulungs-Website aus und notieren Sie die Zeit; testen Sie es bei einer Live-Website zuerst auf einer Kopie.

**Werden Entwürfe neu indiziert?** Nein, nur veröffentlichte Objekte.

**Mit ezfind?** Verwenden Sie das eigene Skript der Erweiterung; siehe Modul 4.

### Stolperfallen

- **Auf einem Server als root ausführen.** Das Skript kann Dateien anlegen, die der Webserver später nicht
  überschreiben kann. Führen Sie es als Benutzer der Website aus (`sudo -u <site user> php ...`);
  `--allow-root-user` ist nur für Schulungsrechner.
- **Der falsche Siteaccess.** Auf einer Installation mit mehreren Datenbanken wählt `-s <siteaccess>`, welche
  indiziert wird. Lassen Sie die Teilnehmenden ihn ausdrücklich nennen.
- **Einen Lauf mit --clean auf halbem Weg abbrechen.** Der Index ist dann unvollständig. Führen Sie ihn erneut bis zum
  Ende aus.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und ein Terminal in ihrem Wurzelverzeichnis. `<siteaccess>` ist Ihr
öffentlicher Siteaccess. Fügen Sie `--allow-root-user` nur hinzu, wenn Sie als root arbeiten.

1. Zählen Sie die veröffentlichten Objekte, die das Skript sehen wird: Führen Sie es ohne `--clean` aus und lesen Sie
   die erste Zeile.

   ```bash
   php bin/php/updatesearchindex.php -s <siteaccess>
   ```

2. Notieren Sie die Zahl nach *Number of objects to index* und wie lange der Lauf dauert (setzen Sie `time` vor den
   Befehl, wenn Ihre Shell es hat).
3. Suchen Sie in der Administration nach einem Wort aus einem Beispielartikel. Es wird gefunden.
4. Führen Sie den Neuaufbau mit `--clean` aus:

   ```bash
   php bin/php/updatesearchindex.php -s <siteaccess> --clean
   ```

5. Suchen Sie, während er läuft (auf einer größeren Website haben Sie einige Sekunden), in der Administration nach
   demselben Wort.
6. Suchen Sie erneut, wenn das Skript fertig ist.
7. Öffnen Sie **Setup > Audit**, wenn Ihre Schulungsinstallation Audits aufzeichnet, und suchen Sie den Eintrag für
   den Neuaufbau.
8. Schreiben Sie den Befehl auf, den Sie für eine Live-Website in ein Runbook schreiben würden, mit dem Benutzer der
   Website und dem Siteaccess.

## Erwartetes Ergebnis

- Die Schritte 1 und 4 geben *Number of objects to index: N* und den Fortschritt aus und enden ohne Fehler.
- Schritt 4 gibt vor dem Indizieren *Cleaning up search data* aus.
- Schritt 5 findet während des Laufs möglicherweise weniger Ergebnisse; Schritt 6 findet wieder alles.
- Schritt 7 zeigt einen Eintrag für den Neuaufbau (Aktion `data.index.rebuild`), wenn Audits aufgezeichnet werden.
- Die Runbook-Zeile aus Schritt 8 läuft als Benutzer der Website, nennt den Siteaccess und sagt, wann (eine ruhige
  Stunde).

## Quiz

1. Nennen Sie drei Situationen, die einen vollständigen Neuaufbau verlangen.
2. Was bewirkt `--clean`?
   A. entfernt Entwürfe B. leert den Index vor dem Indizieren C. leert alle Caches D. löscht Objekte im Papierkorb
3. Warum kann ein Neuaufbau ohne `--clean` auf einer Live-Website die bessere Wahl sein?
4. Welche Option wählt den Siteaccess, und warum ist das wichtig?
5. Wo wird ein Neuaufbau festgehalten?

<details>
<summary>Antworten</summary>

1. Drei beliebige aus: nach einem Import, nach einem Datenbankumzug, wenn Ergebnisse falsch sind, nachdem geändert
   wurde, welche Attribute durchsuchbar sind, einmal nach einer MongoDB-Installation.
2. **B.** „Remove all search data before beginning indexing“ (die Hilfe des Skripts).
3. Die Suche findet weiter die alten Einträge, während jedes Objekt ersetzt wird; mit `--clean` ist die Suche der
   Website bis zum Ende unvollständig.
4. `-s <siteaccess>`; es wählt die Einstellungen und damit die Datenbank, die indiziert wird.
5. Im Audit-Protokoll, als `data.index.rebuild`, mit der Anzahl der Objekte und ob `--clean` verwendet wurde.

</details>

## Weiterführende Informationen

- [Such-Backends](../../../hosting/grundlagen/such-backends.md#den-index-neu-aufbauen): der Neuaufbau aus Sicht eines
  Hosters.
- [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md): was das Audit aufzeichnet und wo man es liest.
- [Sicherungen: was gesichert werden muss](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md): Bei der eingebauten
  Suchmaschine wird der Index neu aufgebaut statt gesichert, da er in der Datenbank liegt.

## Verwandte Seiten

- [Modul 2: Indizierung](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
