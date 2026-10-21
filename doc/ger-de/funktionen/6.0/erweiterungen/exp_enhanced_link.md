# exp_enhanced_link: der erweiterte Link-Datentyp

Diese Seite ist für Website-Entwickler, die Netgen-basierte Inhaltsklassen oder Demodaten installieren.
`exp_enhanced_link` („Exponential Enhanced Link“) stellt den Feldtyp `ngenhancedlink` der Netgen-Pakete für
Exponential 6 bereit, sodass Inhaltsklassen und Demodaten, die ihn verwenden, installiert und ausgegeben werden können.

## Was ein Attribut enthält

- ein **internes** Ziel (eine Inhalts- oder Platzierungs-ID) oder eine **externe** URL;
- einen **Zielmodus**: `link`, `link_new_tab`, `embed` oder `modal`;
- eine optionale **Beschriftung** und ein optionales URL-**Suffix**.

Der Wert wird als JSON in `data_text` mit einer Referenz in `sort_key_string` gespeichert, was dem Speicherformat von
Netgen entspricht. Der Datentyp implementiert `fromString()` und `toString()` für Paketimport und -export, unterstützt
die Suchindizierung (`metaData()`, `sortKey()`, `title()`) und bringt einfache Templates zum Bearbeiten und Anzeigen im
Admin mit.

## Einrichten

Er benötigt die PHP-Erweiterung `json`. Führen Sie nach dem Aktivieren der Extension aus:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Fügen Sie dann unter **Setup > Klassen** einer Klasse ein Attribut vom Typ erweiterter Link hinzu.

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.0.0 | 7. August 2026 | Erster Import. |
| 1.0.1 | | Eine `object_serialize_map` deklariert `data_text` als `text`, sodass Link-Werte beim Exportieren, Duplizieren oder Kopieren von Inhalten erhalten bleiben (vorher gingen sie bei der Serialisierung verloren). |
| 1.0.2 | | `serializeContentClassAttribute()` und `unserializeContentClassAttribute()` erhalten die Klasseneinstellung `data_text5`, wenn Klassen mit dem Paketsystem exportiert und importiert werden. |
| 1.0.3, 1.0.4 | | Übersetzungen mit Deutsch; die Info-Seite nennt die Extension „Exponential Enhanced Link“ (sie zeigte den Verzeichnisnamen). |

## Verwandte Seiten

- [Chronik](../../../geschichte/erweiterungen/exp_enhanced_link.md) und [Versionshinweise](../../../../changelogs/extensions/exp_enhanced_link.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/exp_enhanced_link.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-08](../../../geschichte/erweiterungen/monate/2026-08.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
