# ezpaypal: Zahlungs-Gateway für PayPal

Diese Seite ist für Shop-Betreiber, die Zahlungen mit PayPal annehmen. `ezpaypal` ist ein Zahlungs-Gateway für das
Shop- und Workflow-System. Es verwendet die **NVP**-API von PayPal (Name-Wert-Paare), die mit Weiterleitungen arbeitet:
Der Kunde wird zum Bezahlen zu PayPal geschickt, und PayPal ruft die Benachrichtigungs-URL der Website auf
(`/paypal/notify_url`). Die Extension stellt das Workflow-Ereignis bereit, das die Zahlung startet, und das Modul
`paypal`.

## Einrichten

1. Aktivieren Sie die Extension.
2. Legen Sie `settings/override/paypal.ini.append.php` an und setzen Sie mindestens `Business` (Ihr
   PayPal-Geschäftskonto).
3. Testen Sie mit dem Servernamen der PayPal-Sandbox in `ServerName`, bevor Sie live gehen.
4. Fügen Sie das PayPal-Workflow-Ereignis dem Bestell-Workflow des Shops hinzu.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `paypal.ini` | `ServerSettings` | `ServerName`, `ServerPort`, `RequestURI` | `https://www.paypal.com`, `443`, `/cgi-bin/webscr` | Wohin der Kunde geschickt wird |
| `paypal.ini` | `PaypalSettings` | `MaxDescriptionLength` | `127` | Längste übermittelte Bestellbeschreibung |
| `paypal.ini` | `PaypalSettings` | `Business` | leer | Das PayPal-Geschäftskonto (Ihre Adresse) |
| `paypal.ini` | `PaypalSettings` | `NoNote`, `NoteLabel`, `PageStyle`, `BackgroundColor`, `LogoURI` | `0`, leer, leer, `0`, leer | Aussehen der PayPal-Seite |
| `paypal.ini` | `OrderSettings` | `PaymentMadeOrderStatusID` | `1000` | Bestellstatus, der gesetzt wird, wenn die Zahlung erfolgt ist |

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.2.0 | 24. Januar 2024 | Paket- und Finanzierungskonfiguration in `composer.json`; mit Composer installierbar. |
| 1.2.1 | 27. September 2026 | `ezinfo.php` hat ein statisches `info()` und gibt Version, Lizenz und Website an, sodass die Info-Seite sie liest. |
| 1.2.2, 1.2.3 | | Die Beschreibung nennt Exponential; Kommandozeilenskripte und Modulansichten sind Klassen, die die Dateien aufrufen; Einstiegsdateien tragen einen Kopf von 7x und der Exponential Foundation; die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. Das Zahlungsverhalten ist unverändert. |

Das README der Extension beschreibt noch ihre ursprüngliche Doppellizenzierung; die Releases von Exponential stehen
unter GPL v2 oder später.

## Verwandte Seiten

- [Bestellbelege](../bestellbelege.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ezpaypal.md) und [Versionshinweise](../../../../changelogs/extensions/ezpaypal.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezpaypal.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Monat: 2026-09 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-09.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
