# ezmultiupload: viele Dateien auf einmal hochladen

Diese Seite ist für Redakteure, die viele Bilder oder Dateien auf einmal hinzufügen, und für Administratoren, die es
einrichten. `ezmultiupload` („eZ Multiupload LS“) fügt Ordnern und Galerien **Mehrere Dateien hochladen** hinzu.
Wählen Sie mehrere Dateien aus oder ziehen Sie sie hinein, und jede wird unter dem aktuellen Knoten zu einem
Inhaltsobjekt der passenden Klasse (Bilder in einer Galerie, Dateien in einem Ordner).

## Verwenden

1. Öffnen Sie einen Ordner oder eine Galerie.
2. Klicken Sie auf **Mehrere Dateien hochladen** (die Schaltfläche der Website-Werkzeugleiste kommt von [ezwt](ezwt.md);
   die Ansicht ist `ezmultiupload/upload`).
3. Wählen Sie die Dateien aus oder ziehen Sie sie hinein. Jede Datei zeigt ein Vorschaubild, einen Zähler und eine
   Meldung, wenn sie gespeichert ist.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `ezmultiupload.ini` | `MultiUploadSettings` | `AvailableClasses[]` | folder, gallery | Klassen, die das Hochladen anbieten |
| `ezmultiupload.ini` | `MultiUploadSettings` | `AvailableSubtreeNode[]` | leer | Auf Teilbäume beschränken |
| `ezmultiupload.ini` | `MultiUploadSettings` | `MultiuploadHandlers[]` | leer | Zusätzliche Handler |
| `ezmultiupload.ini` | `FileTypeSettings_folder` | `FileType[]` | `*.*` | In einem Ordner angenommene Dateitypen |
| `ezmultiupload.ini` | `FileTypeSettings_gallery` | `FileType[]` | `*.jpg`, `*.png`, `*.gif`, `*.flv`, `*.mp4`, `*.mov` (und in Großbuchstaben) | In einer Galerie angenommene Dateitypen |

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 6.0.5 | 1. Oktober 2026 | Die Upload-Seite läuft auf `exp::upload` von Exponential UI, wenn `expui` aktiv ist (Meldungen in `Exp.dialog`), mit dem Uploader von YUI als Rückfall. Verglichen mit der YUI-Version in den Designs admin, admin2 und admin3 unter Velocity und PHP-FPM stellt jede Datei dieselbe Anfrage mit denselben Feldern, Vorschaubildern, Zählern und Meldungen. Nach einem **Abbrechen** werden die nächsten Dateien hochgeladen, während YUI nichts mehr hochlud, bis die Seite neu geladen wurde. Acht neue Texte, ins Deutsche übersetzt. Benötigt `se7enxweb/expui ^1.0.0.1`. |
| 6.0.6 | | **Der Uploader von YUI 3 ist entfernt**: `upload.tpl` behält nur seinen Code für Exponential UI (`exp::io`, `exp::dialog`, `exp::upload`), und `design/standard/javascript/ezmultiupload.js` ist entfernt. |
| 6.0.7, 6.0.8 | | Klassen und Befehlshelfer, Kopf- und Copyright-Hinweise, englische und deutsche Übersetzungen. |

**Hinweis zur Aktualisierung:** Wenn Sie `upload.tpl` überschreiben, stellen Sie es auf die Module von Exponential UI
um. Siehe [Entfernung von YUI](../../../bc/6.0/yui-entfernung.md).

## Verwandte Seiten

- [ezwt](ezwt.md)
- [jQuery 4 und die Entfernung von YUI](../jquery4-und-entfernung-von-yui.md)
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Chronik](../../../geschichte/erweiterungen/ezmultiupload.md) und [Versionshinweise](../../../../changelogs/extensions/ezmultiupload.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezmultiupload.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Monat: 2026-10 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
