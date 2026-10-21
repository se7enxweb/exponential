# enhancedezbinaryfile: ein Dateidatentyp mit Informationssammlung

Diese Seite ist für Website-Entwickler, die Formulare brauchen, an die Besucher eine Datei anhängen.
`enhancedezbinaryfile` („Enhanced eZBinary File Type“) ist ein Datentyp zum Hochladen von Dateien, im Klasseneditor als
**Enhanced File** angezeigt, gebaut für die **Informationssammlung** (Formulare, die Besucher ausfüllen).

Er unterscheidet sich in drei Punkten vom `ezbinaryfile` des Kernels:

- Er bewahrt hochgeladene Dateien in einem Download-Ordner auf, mit einer Obergrenze;
- er versendet gesammelte Informationen als mehrteilige E-Mail, wenn eine Datei angehängt ist;
- er bietet einen Template-Operator, der prüft, ob die Datei noch vorhanden ist.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `module.ini` | `RemoveFiles` | `MaxFiles` | `25` | Anzahl der hochgeladenen Dateien, die auf dem Server aufbewahrt werden. Der benötigte Speicherplatz beträgt höchstens `MaxFiles` mal `upload_max_filesize`. `0` bewahrt eine unbegrenzte Anzahl auf, was für eine Website mit anonymen Uploads nicht ratsam ist. |
| `module.ini` | `RemoveFiles` | `DownloadPath` | `original/attachments` | Wo Dateien aufbewahrt werden. Schlägt eine E-Mail fehl, werden die Dateien unter `var/<siteaccess>/storage/original/collected/<filetype>` gespeichert. |

## In einem Template verwenden

Prüfen Sie eine Datei mit dem Operator `filecheck`, bevor Sie auf sie verlinken:

```
{if eq($attribute.data_text|filecheck,true)}...{/if}
```

Die mehrteilige E-Mail wird nur versendet, wenn es einen Anhang gibt.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Änderung |
|---|---|
| 4.4.0, 4.4.1 (Januar 2024, 27. September 2026) | `composer.json`; die Extension gibt ihre Version, Lizenz und Website an. |
| 4.4.2 | Jeder sichtbare Text ist ein Übersetzungstext, mit Deutsch. „%filename successfully uploaded.“ ist eine einzige Meldung statt des Dateinamens mit einem angehängten Satzteil. |
| 4.4.3 | Die Info-Seite nennt die Extension „Enhanced eZBinary File Type“ (sie zeigte den Verzeichnisnamen); die Beschreibung nennt Exponential. |
| 4.4.4 | Die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. |

## Verwandte Seiten

- [Chronik](../../../geschichte/erweiterungen/enhancedezbinaryfile.md) und [Versionshinweise](../../../../changelogs/extensions/enhancedezbinaryfile.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/enhancedezbinaryfile.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Monat: 2026-09 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-09.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
