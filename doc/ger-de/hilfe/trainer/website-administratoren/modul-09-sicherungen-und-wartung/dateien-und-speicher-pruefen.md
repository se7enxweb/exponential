---
title: "Lektion 9.4: Dateien und Speicher prüfen"
audience: trainer, admin
category: training
order: 94
summary: Die Teilnehmenden finden Inhalte, deren Dateien fehlen, listen verwaiste Dateien im Speicher auf, verschieben eine in die Quarantäne und wieder zurück und prüfen, dass die eigenen Dateien der Installation mit dem Release übereinstimmen. 40 Minuten.
keywords: website-administration, fehlende dateien, ungültige relationen, fehlende dateien in var/site/storage, speicherdateien, verwaiste dateien, quarantäne, exp:content-check, dateikonsistenz, verifyfiles.sh, filelist.md5, site administration, missing files, broken relations, storage files, orphan files, quarantine, file consistency
type: help
---
# Lektion 9.4: Dateien und Speicher prüfen

Nach einer Wiederherstellung, einem Serverumzug oder einer Aufräumaktion auf der Festplatte zählen drei Fragen:
Existiert noch jedes Bild und jede Datei, die der Inhalt verwendet, welche Dateien im Speicher verwendet kein Inhalt
mehr, und wurden eigene Dateien der Installation geändert? Exponential beantwortet jede davon mit einer Prüfung, die
nichts ändert, bis Sie es verlangen. Diese Lektion verwendet alle drei.

> **Hinweis**
> Die Prüfungen lesen nur. Verwaiste Dateien in die Quarantäne zu verschieben lässt sich rückgängig machen; das Löschen
> aus der Quarantäne nicht. Löschen Sie im Kurs nichts.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 9: Sicherungen und Wartung](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | [Lektion 9.3](sicherung-und-wiederherstellungstest.md); ein Administratorkonto und eine Shell auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Prüfung auf fehlende Dateien unter **Setup > Ungültige Relationen** ausführen und ihre Zustände lesen;
- dieselbe Prüfung in einer Shell mit `exp:content-check --missing-files` ausführen;
- **Setup > Speicherdateien** prüfen lassen, die verwaisten Dateien lesen und eine davon in die Quarantäne und wieder
  zurück verschieben;
- die Dateien der Installation mit `verifyfiles.sh` gegen die Liste des Release prüfen;
- sagen, welche Prüfung nach einer Wiederherstellung, einem Serverumzug und einem Upgrade auszuführen ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Fehlende Dateien und Bildvarianten](../../../../anleitungen/fehlende-dateien.md),
[Die Inhaltsprüfungen](../../../../../eng-us/features/6.0/content-checks.md) (die Abschnitte „Speicherdateien“ und „Quarantäne“)
und [Prüfung der Dateikonsistenz](../../../../../eng-us/features/6.0/file-consistency-check.md).

1. **Fehlende Dateien** (4 Minuten). Ein Ziel unter **Setup > Ungültige Relationen**: Haken Sie
   **Fehlende Dateien in var/site/storage** an (angezeigt wird der Name Ihres Speicherverzeichnisses) und führen Sie
   die Prüfung aus. Jede Zeile nennt das Objekt, das Feld, die Version, den erwarteten Pfad und den Zustand:
   **Datei fehlt**, **Variante fehlt**, **Variante veraltet** oder **Variante nie erstellt**. Eine Bildvariante wird
   erst erstellt, wenn ein Bild zum ersten Mal in ihr angezeigt wird, deshalb ist *nie erstellt* normal.
2. **Speicherdateien** (4 Minuten). **Setup > Speicherdateien** durchsucht `var/<site>/storage` und listet
   **Verwaiste Dateien** (kein Inhalt verwendet sie, einschließlich alter Versionen und des Papierkorbs) und
   **Dateiprobleme** (falscher Eigentümer oder falsche Rechte, leere Dateien, Namen, die sich nur in der Groß- und
   Kleinschreibung unterscheiden). Eine Datei, die jünger als ein Tag ist, ist nie verwaist.
3. **Die Quarantäne** (3 Minuten). Verwaiste Dateien werden nach `var/<site>/storage-quarantine/<date>/` verschoben,
   in einem Manifest verzeichnet und lassen sich wiederherstellen. Aus dem Speicher selbst wird nie etwas gelöscht.
4. **Dateikonsistenz** (3 Minuten). `bash bin/shell/verifyfiles.sh` vergleicht jede Datei mit der
   `share/filelist.md5` des Release; der Exit-Status 0 bedeutet, dass alle übereinstimmen. Führen Sie es im
   Dokumentenstamm aus.
5. **Welche Prüfung wann** (1 Minute). Nach einer Wiederherstellung oder einem Umzug: fehlende Dateien. Beim
   Aufräumen: Speicherdateien. Nach einem Upgrade oder einem Vorfall: Dateikonsistenz.

### Häufige Fragen

**Wer darf die Prüfungen ausführen?** Wer die Richtlinien des Moduls `contentcheck` hat; auf einer neuen Installation
hat sie nur Administrator. Jede Liste zeigt nur, was `content/read` des Benutzers erlaubt.

**Warum ist ein Bild einer alten Version nicht verwaist?** Es wird im Verlauf noch angezeigt und mit dieser Version
wiederhergestellt.

**Die Konsistenzprüfung listet Dateien, die ich absichtlich geändert habe.** Sie listet jede Datei, die vom Release
abweicht. Eine direkt vorgenommene Änderung gehört stattdessen in eine Erweiterung oder ein Override; die Lektion zu
Upgrades (Modul 10) kommt darauf zurück.

### Stolperfallen

- **Einen großen Speicher im Browser durchsuchen.** Die Prüfung läuft in Schritten; verwenden Sie auf einer großen
  Website die Kommandozeile.
- **Dateien einer Website in Quarantäne verschieben, die aus einer älteren Sicherung wiederhergestellt wurde.** Führen
  Sie zuerst die Prüfung auf fehlende Dateien aus; eine Datei kann verwaist aussehen, wenn ihr Inhalt nicht
  wiederhergestellt wurde.
- **`verifyfiles.sh` im falschen Verzeichnis ausführen.** Die Pfade in der Liste sind relativ zum Dokumentenstamm.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, im Browser und in einem Terminal im Wurzelverzeichnis der
Installation.

1. Öffnen Sie **Setup > Ungültige Relationen**. Klicken Sie auf **Ganzer Baum**, haken Sie
   **Fehlende Dateien in var/site/storage** an (der Name kann abweichen) und führen Sie die Prüfung aus. Lesen Sie die
   Befunde.
2. Führen Sie in einem Terminal dieselbe Prüfung aus:

   ```bash
   php bin/php/console exp:content-check --missing-files --node=2
   ```

3. Machen Sie absichtlich eine Datei kaputt: Suchen Sie das Original eines Schulungsbildes unter
   `var/<site>/storage/images` und benennen Sie es mit der Endung `.bak` um (`mv <file> <file>.bak`).
4. Führen Sie Schritt 2 erneut aus und suchen Sie die Zeile **missing** für dieses Bild. Benennen Sie die Datei zurück
   und führen Sie die Prüfung noch einmal aus.
5. Öffnen Sie **Setup > Speicherdateien** und klicken Sie auf **Erneut prüfen**. Lesen Sie **Letzte Prüfung**, dann
   **Verwaiste Dateien**: Anzahl und Größe.
6. Gibt es verwaiste Dateien, haken Sie eine an und klicken Sie auf
   **Ausgewählte Dateien in die Quarantäne verschieben**; lesen Sie die Bestätigung, bevor Sie bestätigen.
7. Suchen Sie die Datei in der Liste der Quarantäne und stellen Sie sie an ihrem Platz wieder her.
8. Prüfen Sie die Dateien der Installation gegen das Release:

   ```bash
   bash bin/shell/verifyfiles.sh
   echo "exit status: $?"
   ```

9. Schreiben Sie zu jeder Prüfung einen Satz: wann Sie sie auf einer echten Website ausführen würden.

## Erwartetes Ergebnis

- Schritt 1 zeigt die Befunde, auf einer gesunden Schulungs-Website keine.
- Schritt 4 listet das umbenannte Bild als fehlend; nach dem Zurückbenennen ist es aus der Liste verschwunden.
- Schritt 5 zeigt die Prüfung mit Summen; Schritt 7 legt die Datei aus der Quarantäne wieder dorthin, wo sie war.
- Schritt 8 endet mit dem Exit-Status 0 oder listet Dateien, die vom Release abweichen (eine Schulungsinstallation, die
  aus einem Entwicklungszweig gebaut wurde, kann einige zeigen).
- Der Trainer prüft, dass alle Teilnehmenden die Datei aus Schritt 3 und die Datei aus Schritt 6 wiederhergestellt
  haben.

![Speicherdateien: Dateien, die nichts verwendet, und Dateien mit falschem Eigentümer oder falschen Rechten](../../../../../eng-us/help/images/trainers/sa-storage.webp)

## Quiz

1. Welcher Zustand der Prüfung auf fehlende Dateien ist normal und kein Problem?
   A. **Datei fehlt** B. **Variante fehlt** C. **Variante veraltet** D. **Variante nie erstellt**
2. Wohin legt **Ausgewählte Dateien in die Quarantäne verschieben** eine Datei, und kann sie zurückkommen?
3. Welcher Befehl prüft, ob die eigenen Dateien der Installation mit dem Release übereinstimmen?
4. Eine Datei wurde vor einer Stunde hochgeladen, und noch verwendet kein Inhalt sie. Wird sie als verwaist
   aufgeführt?
5. Sie haben eine Website aus einer Sicherung wiederhergestellt. Welche Prüfung führen Sie zuerst aus?

<details>
<summary>Antworten</summary>

1. **D.** Eine Bildvariante wird erst erstellt, wenn ein Bild zum ersten Mal in ihr angezeigt wird
   ([Fehlende Dateien](../../../../anleitungen/fehlende-dateien.md#1-die-prüfung-öffnen)).
2. Nach `var/<site>/storage-quarantine/<date>/`, in einem Manifest verzeichnet; sie lässt sich an ihrem Platz
   wiederherstellen ([Quarantäne](../../../../../eng-us/features/6.0/content-checks.md#quarantine)).
3. `bash bin/shell/verifyfiles.sh`, ausgeführt im Dokumentenstamm.
4. Nein. Eine Datei, die jünger als ein Tag ist, ist nie verwaist.
5. Die Prüfung auf fehlende Dateien: Existiert jede Datei, die der Inhalt verwendet?

</details>

## Weiterführende Literatur

- [Fehlende Dateien und Bildvarianten](../../../../anleitungen/fehlende-dateien.md): die Befunde lesen und beheben.
- [Die Inhaltsprüfungen](../../../../../eng-us/features/6.0/content-checks.md): nicht referenzierte Objekte, ungültige
  Relationen, Speicherdateien und die Quarantäne.
- [Prüfung der Dateikonsistenz](../../../../../eng-us/features/6.0/file-consistency-check.md).
- [Runbook: Festplatte voll](../../../hosting/runbooks/festplatte-voll.md): wenn der Speicher die Festplatte füllt.

## Verwandte Seiten

- [Modul 9: Sicherungen und Wartung](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
