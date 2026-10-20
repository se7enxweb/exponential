# bcgooglesitemaps: XML-Sitemaps für Google

Diese Seite ist für Website-Betreiber, die möchten, dass Suchmaschinen alle öffentlichen Seiten finden.
`bcgooglesitemaps` erzeugt als Cronjob eine Sitemap (Protokoll `sitemap.xml`) Ihrer öffentlichen Inhalte. Sie schreibt
eine Datei pro Siteaccess, meist `sitemap_<siteaccess>.xml` (die Namen werden in der INI-Datei festgelegt), und hat eine
mehrsprachige Variante.

## Verwenden

Führen Sie sie einmal im Wurzelverzeichnis der Installation aus:

```bash
php runcronjobs.php googlesitemaps
php runcronjobs.php googlesitemapsmultilingual
```

Prüfen Sie, dass die Sitemap-Datei geschrieben wurde, und tragen Sie denselben Befehl dann in cron ein, damit sie aktuell
bleibt.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `bcgooglesitemaps.ini` | `BCGoogleSitemapSettings` | `SitemapRootNodeID` | `2` | Startknoten der Sitemap |
| `bcgooglesitemaps.ini` | `BCGoogleSitemapSettings` | `Protocol` | `https` | In die URLs geschriebenes Protokoll |
| `bcgooglesitemaps.ini` | `BCGoogleSitemapSettings` | `Filename`, `Filesuffix`, `Path` | `sitemap`, `.xml`, leer | Wo die Datei liegt und wie sie heißt |
| `bcgooglesitemaps.ini` | `Classes` | `ClassFilterType`, `ClassFilterArray[]` | `exclude`, leer | Klassen ein- oder ausschließen |
| `bcgooglesitemaps.ini` | `NodeSettings` | `Main_Node_Only` | `false` | Nur Hauptknoten |
| `bcgooglesitemaps.ini` | `NodeSettings` | `ExcludeNodes`, `ExcludedNodeIDs[]` | `disabled`, leer | Teilbäume ausschließen. Standardmäßig ausgeschaltet (1.1.6), um mit Websites kompatibel zu bleiben, die nie Ausschlüsse hatten |

Tragen Sie Ihre Werte in `settings/override/bcgooglesitemaps.ini.append.php` ein.

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.1.6 | 7. Januar 2024 | Neue Einstellung `ExcludeNodes`; die Abhängigkeit vom Paket von ezsystems durch das von 7x ersetzt; die Lizenzbedingungen erlauben einen Lizenzwechsel auf eine neuere Version; das Cronjob-Werkzeug wurde für die Aufnahme in Exponential geforkt. |
| 1.1.6.1 | | Lizenz in `composer.json` klargestellt. |
| 1.1.6.2 bis 1.1.6.4 | 27. September bis 2. Oktober 2026 | Die Lizenz wird vollständig genannt; die Beschreibung nennt Exponential; Kommandozeilenskripte und Modulansichten sind Klassen, die die Dateien aufrufen; Einstiegsdateien tragen einen Kopf von 7x und der Exponential Foundation; Befehle führen eine Beschreibung. |

Für umfassendere SEO (Titel, Beschreibungen, Open-Graph-Bilder, News- und Mobil-Sitemaps) siehe
[xrowmetadata](../../../../eng-us/features/6.0/extensions/xrowmetadata.md).

## Verwandte Seiten

- [xrowmetadata](../../../../eng-us/features/6.0/extensions/xrowmetadata.md)
- [Cronjobs in der Konsole](../../../../eng-us/features/6.0/cronjobs-console.md)
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronik](../../../../eng-us/history/extensions/bcgooglesitemaps.md) und [Versionshinweise](../../../../changelogs/extensions/bcgooglesitemaps.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/bcgooglesitemaps.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2024-01](../../../../eng-us/history/extensions/months/2024-01.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
