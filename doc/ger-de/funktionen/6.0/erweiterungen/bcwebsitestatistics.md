# bcwebsitestatistics: Google-Analytics-Tags und Bestellverfolgung

Diese Seite ist für Website-Betreiber, die Besucherzahlen und Shop-Umsätze mit Google Analytics messen.
`bcwebsitestatistics` („BC Website Statistics“) fügt den Seiten das Analytics-Skript hinzu und meldet über den
Workflow-Typ `ezreceipt` Shop-Bestellungen.

## Bevor Sie beginnen

- Die Website muss aus dem Internet erreichbar sein.
- Sie brauchen ein eingerichtetes Website-Profil in Google Analytics.

## Einrichten

1. Aktivieren Sie die Extension.
2. Legen Sie `settings/override/bcwebsitestatistics.ini.append.php` mit Ihren eigenen Werten an (siehe Tabelle). Setzen
   Sie immer `Urchin`: Der mitgelieferte Wert ist ein Beispiel, nicht Ihrer.
3. Um Bestellungen zu melden, setzen Sie `OrderSubmit=enabled` und fügen Sie den Workflow-Typ `ezreceipt` dem Workflow
   des Shops hinzu.
4. Leeren Sie den INI-Cache und öffnen Sie eine Seite; ihr Quelltext enthält das Analytics-Skript.

## Einstellungen

| Datei | Block | Schlüssel | Mitgelieferter Wert | Bedeutung |
|---|---|---|---|---|
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `OrderSubmit` | `disabled` | Bestellungen melden |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `PageSubmit` | `enabled` | Seitenaufrufe melden |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `Urchin` | eine Beispiel-Tracking-ID | **Setzen Sie Ihre eigene** Analytics-ID |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `HostName`, `ShopName` | `disabled`, ein Platzhalter als Shopname | Gemeldete Host- und Shopnamen |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `Script`, `SecureScript`, `SecureTagManagerScript` | Adressen der Google-Skripte | Eingebundene Skripte |

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.0.4, 1.0.5 | Januar 2024 | `composer.json` auf das Paket von 7x umgestellt; Abhängigkeit vom Repository von ezsystems ersetzt; Version aktualisiert. |
| 1.0.6 | 18. Juli 2026 | Die erzeugten Skript-Tags für Analytics (gtag) tragen die veralteten Attribute `async`, `type=text/javascript` und `language=Javascript` nicht mehr (HTML5-Validierung). |
| 1.0.7 bis 1.0.9 | 27. bis 30. September 2026 | `ezinfo.php` hat ein statisches `info()` und gibt Version, Lizenz und Website an; jeder sichtbare Text ist ein Übersetzungstext mit Deutsch; die Beschreibung nennt Exponential. |

## Verwandte Seiten

- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/bcwebsitestatistics.md) und [Versionshinweise](../../../../changelogs/extensions/bcwebsitestatistics.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/bcwebsitestatistics.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-07](../../../geschichte/erweiterungen/monate/2026-07.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
