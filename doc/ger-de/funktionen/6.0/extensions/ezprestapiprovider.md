# ezprestapiprovider: die REST-Provider-Klassen (veraltet)

Diese Seite ist für Entwickler und Website-Administratoren, deren Installation `ezprestapiprovider` noch aktiviert.

> [!WARNING]
> **Veraltet.** REST API v1 und v2 werden von einer Extension bedient, [ezprestapi](ezprestapi.md) (1.2.6 oder neuer).
> `ezprestapiprovider` 6.0.4 (15. Oktober 2026) ist ihr letztes Release. Aktivieren Sie `ezprestapi` und entfernen Sie
> `ezprestapiprovider` aus `ActiveExtensions`; die Schritte stehen in
> [REST API v1 und v2: eine Extension](../../../anleitungen/rest-api-v1-und-v2.md#6-migration-von-ezprestapiprovider).

## Was sie jetzt ist

Eine Kompatibilitätsschicht über `ezprestapi`, damit alte Einstellungen und alter Code weiter funktionieren, während eine
Installation umzieht:

- `ezpRestApiProvider` erweitert `ezp7xRestApiProvider`, und `ezpRestContentController` erweitert
  `ezp7xRestContentController`, sodass sie die Routen von v1 und v2 und die Berechtigungsprüfungen von `ezprestapi`
  erhalten.
- Ihre `rest.ini` bildet die Präfixe `ezp` und `ezpl` auf `ezp7xRestApiProvider` ab, dieselben Werte, die `ezprestapi`
  setzt, sodass die Reihenfolge, in der die beiden aktiviert werden, keine Rolle mehr spielt.
- Sie liefert die fünf Klassen nicht mehr mit, die sie doppelt hatte (`ezpRestAtomController`, `ezpRestAtomView`,
  `ezpRestAtomDecorator`, `ezpRestContentModel`, `ezpRestApiViewController`); `ezprestapi` liefert sie, und der
  Autoload-Generator warnt nicht mehr vor doppelten Klassennamen.
- Ihr Composer-Paket verlangt `se7enxweb/ezprestapi ~1.2.6`.

Vor 6.0.4 registrierte sie ihren eigenen Provider, und wenn ihr `ProviderClass[ezp]` gewann (sie stand früher in
`ActiveExtensions` oder war allein aktiv, wie der Einrichtungsassistent sie früher aktivierte), antwortete jede Route
`/api/ezp/v2/...` mit 404, und die Lesezugriffe von v1 liefen ohne die Berechtigungsprüfungen. Die
[Tabelle der Aktivierungen](../../../anleitungen/rest-api-v1-und-v2.md#7-aktivierungskombinationen-vorher-und-seitdem) vergleicht
vorher und seitdem.

## Die Migration in Kürze

1. Behalten oder ergänzen Sie `ActiveExtensions[]=ezprestapi` in `settings/override/site.ini.append.php` und entfernen
   Sie `ActiveExtensions[]=ezprestapiprovider`.
2. Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`), erzeugen Sie die Extension-Autoloads neu
   (`php bin/php/ezpgenerateautoloads.php -e`), laden Sie PHP-FPM neu und starten Sie Velocity neu.
3. Ändern Sie eine Überschreibung `ProviderClass[ezp]=ezpRestApiProvider` in `rest.ini` auf `ezp7xRestApiProvider` und
   Code, der `ezpRestContentController` erweitert, so, dass er `ezp7xRestContentController` erweitert. Beides
   funktioniert über die Kompatibilitätsschicht weiter, solange sie installiert ist.

Für OAuth-Clients, Tokens, persönliche API-Schlüssel und die URLs von v1 ändert sich nichts.

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 6.0.0 | 29. Januar 2024 | Paketanbieter und -name auf se7enxweb umgestellt; Beschreibung aktualisiert. |
| 6.0.1, 6.0.2 | 27. und 30. September 2026 | Version, Lizenz und Website; die Beschreibung nennt Exponential. |
| 6.0.3 | 2. Oktober 2026 | Die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. |
| 6.0.4 | 15. Oktober 2026 | Veraltet, letztes Release: Provider und Inhalts-Controller erweitern die von ezprestapi; `rest.ini` bildet `ezp` und `ezpl` auf `ezp7xRestApiProvider` ab; die fünf doppelten Klassen und die Modelltests entfernt (sie liegen in ezprestapi); verlangt `se7enxweb/ezprestapi ~1.2.6`. |

## Verwandte Seiten

- [REST API v1 und v2: eine Extension](../../../anleitungen/rest-api-v1-und-v2.md)
- [ezprestapi](ezprestapi.md)
- [Chronik](../../../../eng-us/history/extensions/ezprestapiprovider.md) und [Versionshinweise](../../../../changelogs/extensions/ezprestapiprovider.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/ezprestapiprovider.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2023-12](../../../../eng-us/history/extensions/months/2023-12.md), [2024-01](../../../../eng-us/history/extensions/months/2024-01.md), [2026-03](../../../../eng-us/history/extensions/months/2026-03.md), [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
