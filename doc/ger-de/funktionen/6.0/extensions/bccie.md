# bccie: gesammelte Informationen nach CSV oder Excel exportieren

Diese Seite ist für Website-Betreiber, die Formularantworten sammeln (Kontaktformulare, Umfragen) und sie in einer
Tabellenkalkulation brauchen. `bccie` („BC CIE“, seit 1.1.4 im Admin-Menü als **CIE** angezeigt) exportiert die
**gesammelten Informationen** von Inhaltsobjekten in **CSV**- oder **SYLK (Excel)**-Dateien. Sie können aus der
Administrationsoberfläche auf Abruf exportieren oder nach Zeitplan mit seinen Cronjob-Teilen. Der ursprüngliche Autor ist
Brookins Consulting (2006 bis 2017, auf Grundlage der Arbeit eines früheren Mitwirkenden).

## Verwenden

1. Öffnen Sie den Reiter **CIE** in der Administrationsoberfläche (`/bccie/overview`; die Rolle braucht `bccie/read`). Die Startseite zeigt die Formulare, die Informationen gesammelt haben, wie viele Antworten jedes hat, die letzten Exporte und die gefundenen Probleme (zum Beispiel einen geplanten Export, der ein Objekt nennt, das nicht existiert).
2. Öffnen Sie ein Formular mit **Export**. Wählen Sie die Felder, den Datumsbereich, das Format (CSV oder SYLK (Excel)), das Trennzeichen und den Zeichensatz. Ein falsches Datum oder gar kein Feld wird mit einer Meldung neben dem Formular abgelehnt.
3. **Do export** lädt die Datei herunter. Ein Formular mit mehr als `DirectExportLimit` Antworten (2000) wird mit **Run in the background** exportiert: Die Seite zeigt den Fortschritt und eine Schaltfläche **Herunterladen**, wenn die Datei fertig ist.
4. Um die Antworten eines Formulars zu entfernen, verwenden Sie **Ausgewählte entfernen** in der Liste oder **Remove all collected information** auf der Exportseite; beide fragen vorher nach und brauchen die Richtlinie `bccie/remove`. Exportieren Sie die Daten, bevor Sie sie entfernen.

Sicherheit in Tabellenkalkulationen: Eine Antwort, die mit `=`, `+`, `-` oder `@` beginnt, wird mit einem führenden
einfachen Anführungszeichen geschrieben, sodass Excel sie nicht als Formel ausführt. Text ist UTF-8; für Excel unter
Windows wählen Sie `utf8bom`, unter macOS `utf16le`.

### Aus der Konsole und cron

```bash
./console ext:bccie:status
./console ext:bccie:export --object=123 --format=sylk --dry-run
./console ext:bccie:export --cron          # the scheduled export of cie.ini
./console ext:bccie:purge --object=123 --before=2026-01-01 --yes
```

Für geplante Exporte setzen Sie `Collection[]`, `Directory` und die Bereichseinstellungen unten und tragen die
Cronjob-Teile in cron ein (`php runcronjobs.php exportcsv`); die Dateien werden nach `var/export` geschrieben. Die
Referenz ist die [Spezifikation](../../../spezifikationen/6.0/bccie.md).

## Einstellungen

Alle Schlüssel stehen in `cie.ini`, Block `CieSettings`.

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `Debug`, `Log`, `LogDebug` | `disabled`, `var/log/cie.log`, `disabled` | Debug-Ausgabe und Protokolldatei |
| `Directory` | `var/export` | Wohin die Exporte aus cron geschrieben werden |
| `ExportLimitedRange`, `DateRangeToExport` | `disabled`, `7` | Nur die letzten N Tage exportieren |
| `RemoveExported` | `disabled` | Gesammelte Informationen nach dem Export löschen |
| `Collection[]`, `ExcludeAttributeID[]` | leer | Einschränken, was exportiert wird |
| `CsvFormat`, `CsvSeparator`, `SylkFormat`, `SylkSeparator` | `csv`, `;`, `sylk`, `;` | Formate und Trennzeichen |
| `ExportZeroToEmptyString` | `disabled` | 0 als leere Zelle schreiben |
| `ExportExecutionTimeLimit` | `180` | Sekunden |
| `ExportUsingDaysCalcualation` | `disabled` | Bereich nach Tagen (Schlüssel so geschrieben, wie ausgeliefert) |
| `DisplayLeaveEmptyOption` | `enabled` | Die Option „leer lassen“ zeigen |
| `ExportFileName` | `bccie_cie_export-` | Präfix des Dateinamens |

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.1.4 | 20. September 2026 | Der Name im oberen Menü ist **CIE** statt „BC CIE Export“. |
| 1.1.5 bis 1.1.7 | | Die Lizenz wird vollständig genannt (GPL v2 oder später); jeder sichtbare Text ist ein Übersetzungstext mit Deutsch, einschließlich der Exportoption „SYLK (Excel)“; Reiter, Tooltip und Name des Navigationsteils haben deutsche Übersetzungen. |
| 1.1.8 | 29. September 2026 | Das Öffnen des Exports ohne gültiges Formular antwortet mit der Seite „nicht verfügbar“ des Kernels statt mit HTTP 500. Die Ansichten `export` und `doexport` endeten mit `EZ_ERROR_KERNEL_NOT_AVAILABLE`, einer Konstante, die der Kernel nicht mehr definiert, sodass PHP 8 mit einem Fehler wegen einer undefinierten Konstante abbrach; sie verwenden jetzt `eZError::KERNEL_NOT_AVAILABLE`. |
| 1.1.12 | 4. Oktober 2026 | Eine Dashboard-Startseite; eine Exportseite, die ihre Eingaben prüft; große Exporte im Hintergrund mit Fortschritt und Download; Konsolenbefehle `ext:bccie:export`, `status` und `purge`; die Cronjob-Teile auf demselben Runner neu geschrieben. Behoben: Jeder Datentyp wurde vom Basis-Handler geschrieben (PHP 8 ruft keinen nach der Klasse benannten Konstruktor auf); Zeichen außerhalb von Latin-1 wurden zu Fragezeichen; Tabellenformeln in Antworten liefen in Excel; ein doppeltes Anführungszeichen zerbrach eine CSV-Zelle; der SYLK-Export verlor seine erste Sammlung und seinen Kopf; der Kopf passte nicht zu den Spalten; das Entfernen von Antworten brauchte nur die Leserichtlinie (jetzt braucht es `bccie/remove`). |
| 1.1.9 bis 1.1.11 | 30. September bis 2. Oktober 2026 | Die Beschreibung nennt Exponential; Befehle und Cronjob-Teile sind Klassen, die die Dateien aufrufen, jeweils mit einer Beschreibung; englische und deutsche Übersetzungen für jeden Text, den die Administrationsoberfläche unübersetzt zeigte. |

## Verwandte Seiten

- [Spezifikation](../../../spezifikationen/6.0/bccie.md): Ansichten, Richtlinien, Dateien, Befehle, Einstellungen
- [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md): das neuere Exportwerkzeug mit eigenen Handlern; [birthday](birthday.md) bringt einen Handler dafür mit
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronik](../../../../eng-us/history/extensions/bccie.md) und [Versionshinweise](../../../../changelogs/extensions/bccie.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/bccie.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
