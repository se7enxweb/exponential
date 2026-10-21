# ezxmlexport: geplante XML-Exporte

Diese Seite ist für Websites, die `ezxmlexport` noch einsetzen, und für alle, die ein Exportwerkzeug auswählen.
`ezxmlexport` („eZ XML Export“) exportiert Inhalte nach einem Zeitplan als XML und kann das Ergebnis per FTP
ausliefern.

## Was es bietet

Das Modul `xmlexport` hat diese Ansichten:

| Ansicht | Zweck |
|---|---|
| `edit`, `view`, `delete` | einen Export festlegen, anzeigen und entfernen |
| `runningexports` | laufende Exporte ansehen |
| `createxmlschema` | das XML-Schema erzeugen |
| `ftptest` | die FTP-Auslieferung testen |
| `relaunchftptransfert` | eine fehlgeschlagene Übertragung neu starten |

Es hat außerdem einen Cronjob und eine Kommandozeile. Version 1.3.0 machte es mit dem Design admin2 kompatibel.

## Status

In der Zeit von Exponential 6 erhielt das Repository nur Paketmetadaten und Informationen zur Finanzierung (Dezember
2023 bis März 2026); der Code ist die Linie 1.3.

Für neue Arbeiten verwenden Sie [xrowextract](xrowextract.md). Es exportiert nach einem Zeitplan als CSV, JSON, XML
und Inhaltspakete und liefert per SFTP, FTPS, S3, WebDAV und HTTP aus.

## Verwandte Seiten

- [xrowextract](xrowextract.md)
- [Chronik](../../../geschichte/erweiterungen/ezxmlexport.md) und [Versionshinweise](../../../../changelogs/extensions/ezxmlexport.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezxmlexport.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2023-12](../../../geschichte/erweiterungen/monate/2023-12.md), [2024-01](../../../geschichte/erweiterungen/monate/2024-01.md), [2026-03](../../../geschichte/erweiterungen/monate/2026-03.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
