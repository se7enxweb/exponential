# Was eine neue Installation mitbringt: die Standard-Auslieferung der Extensions

Diese Seite ist für alle, die Exponential installieren und wissen wollen, welche Extensions damit kommen und wie sich eine
hinzufügen oder weglassen lässt. Die `composer.json` von Exponential tut mehr, als PHP-Anforderungen aufzulisten: Sie
entscheidet, welche Extensions mit `composer create-project` kommen. Ab Dezember 2023 haben die Betreuer diese Liste Paket
für Paket aufgebaut.

## Die aktuelle Liste ansehen

```bash
grep -n '"se7enxweb/' composer.json
```

Der erste Block der Ausgabe ist `suggest`, der zweite `require`. Die aktuellen Abschnitte `require` und `suggest` von
`composer.json` sind maßgeblich; die Zeitleiste unten sagt Ihnen, wann jedes Teil hinzukam.

## Wie es funktioniert

- Jede Extension ist ein Composer-Paket des Anbieters `se7enxweb`. Die Versionsbedingungen verwenden `~`, was die letzte
  Stelle freigibt: `~6.0.8` nimmt `6.0.9` und `6.0.10` an, aber nicht `6.1.0`.
- `require` wird mit dem Produkt installiert. `suggest` ist nur ein Hinweis, den Composer ausgibt; ein vorgeschlagenes
  Paket installieren Sie selbst mit `composer require se7enxweb/<name>`.
- Die PHP-Anforderung ist `^8.1 || ^8.2 ...` (siehe [Unterstützung für PHP 8](../../bc/6.0/php8.md)).
- Im Dezember 2023 stellte schon das erste Release die Quellen von Paketnamen Dritter (`...-ls` und `...-ls-extension`) auf
  `se7enxweb/<name>` um. Im Januar 2024 wurde jedes Paket umbenannt, sodass kein Trick mit `replace` von Composer nötig
  ist.

## Eine Extension hinzufügen

1. Installieren Sie sie:

   ```bash
   composer require se7enxweb/ezflow
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

2. Aktivieren Sie sie in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ezflow
   ```

Eine aus einem Website-Paket erstellte Installation aktiviert die Extensions ihres Pakets für Sie.

## Eine weglassen

1. Entfernen Sie sie aus `ActiveExtensions` und leeren Sie die Caches.
2. Führen Sie `composer remove se7enxweb/<name>` aus.

Wenn Sie mit Git-Checkouts der Extensions statt mit Composer installiert haben, führen Sie nicht `composer update` aus:
Es ersetzt die Checkouts.

## Zeitleiste der Auslieferung (`composer.json` im Wurzelverzeichnis)

| Wann | Release-Linie | Änderung |
|---|---|---|
| 14.-23. Dez. 2023 | 6.0.0 | Repository als Produkt von 7x gekennzeichnet; Anforderungen zeigen auf `se7enxweb`; `ezgmaplocation`, `ezstarrating`, `ezwt` auf Versionen gesetzt, die sich sauber installieren |
| 7. Jan. 2024 | 6.0.1 | `bcgooglesitemaps` (Sitemaps), `swark`, `owsimpleoperator` (zusätzliche Template-Operatoren), `bcwebsitestatistics` (Webstatistik und Google Analytics); `ezmultiupload` auf ein Release mit wichtigen Korrekturen angehoben |
| 23.-29. Jan. 2024 | 6.0.1 | `ezpaypal`, `ezodf`, `ezautosave`, `ezie`, `ezflow`, `ezdemo` und `ezwebin` umbenannt oder als vollwertige Pakete hinzugefügt; `ezoracle` und `ezauthorize` vorgeschlagen; `xrowmetadata` vorgeschlagen |
| Feb. 2024 | 6.0.2 | `bccie`, `xrowextract` (Inhaltsexport) hinzugefügt |
| März 2024 | 6.0.3 | Datentyp-Extensions `enhancedezbinaryfile`, `enhancedselection2` und `birthday` hinzugefügt; `bcurlaliaswithdash` vorgeschlagen; `xrowmetadata` wird zur Anforderung |
| 11. Aug. 2024 | 6.0.4 | Anti-Spam-Extensions `recaptcha` und `hcaptcha` hinzugefügt |
| 31. Aug. - 5. Sep. 2024 | 6.0.4 | `sevenx_valkey` (Unterstützung für Valkey/Redis) hinzugefügt und im selben Release nach `suggest` verschoben, damit Installationen ohne Redis-Server nicht scheitern |
| 1. und 18. Okt. 2024 | 6.0.5, 6.0.6 | `ezownerchange` und `git_manager` vorgeschlagen |
| 2. Nov. 2024 | 6.0.6 | `ezupdate` hinzugefügt |
| 13. Mai 2025 | 6.0.9 | `ezprestapi` 1.2 hinzugefügt: Inhalte über eine REST-API anlegen, lesen, aktualisieren und löschen (seit 1.2.6 die eine Extension für REST API v1 und v2; die `composer.json` im Wurzelverzeichnis von 6.0.15 verlangt `ezprestapi ~1.2.6` und nicht mehr `ezprestapiprovider`, siehe [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)) |
| 13. Aug. 2025 | 6.0.10 | `sevenx_themes_simple` hinzugefügt, damit das Standard-Theme immer ausgeliefert wird |

Einige Einträge wechselten später zwischen `suggest` und `require`: Heute stehen `ezoracle` und `git_manager` in
`require`, während `ezauthorize`, `ezownerchange`, `bcurlaliaswithdash` und `sevenx_valkey` weiterhin nur Vorschläge
sind. Seitdem ist die Liste um das Layout-System, den Newsletter, die Tags-Extension und weitere gewachsen.

## Verwandte Seiten

- [Extensions, Themes und Pakete](erweiterungen/README.md), [Extension-Liste: sortieren, untersuchen und herunterladen](extension-liste-und-downloads.md), [Ladereihenfolge und sicheres Speichern](ladereihenfolge-der-extensions.md)
- [Extension-Metadaten: `ezinfo.php` und `extension.xml`](../../spezifikationen/6.0/extension-metadaten.md)
- [Das Produkt heißt Exponential](umbenennung-in-exponential.md), [Unterstützung für PHP 8](../../bc/6.0/php8.md)
- Changelogs, die die Menge der Extensions geändert haben: [6.0.0](../../../changelogs/6.0/6.0.0.md), [6.0.1](../../../changelogs/6.0/6.0.1.md), [6.0.2](../../../changelogs/6.0/6.0.2.md), [6.0.3](../../../changelogs/6.0/6.0.3.md), [6.0.4](../../../changelogs/6.0/6.0.4.md), [6.0.5](../../../changelogs/6.0/6.0.5.md), [6.0.6](../../../changelogs/6.0/6.0.6.md), [6.0.9](../../../changelogs/6.0/6.0.9.md), [6.0.10](../../../changelogs/6.0/6.0.10.md)
- Chronik: [Dezember 2023](../../geschichte/2023/2023-12.md), [Januar 2024, erste Hälfte](../../geschichte/2024/2024-01a.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md), [Februar 2024](../../geschichte/2024/2024-02.md), [März 2024](../../geschichte/2024/2024-03.md), [Juni 2024](../../geschichte/2024/2024-06.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
