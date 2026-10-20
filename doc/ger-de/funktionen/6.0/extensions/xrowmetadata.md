# xrowmetadata: Seitentitel, Beschreibungen, Open Graph und Sitemaps

Diese Seite ist für Website-Entwickler, denen Suchmaschinen und Linkvorschauen wichtig sind. `xrowmetadata` („Xrow Meta
Data“) ist der einfache SEO-Baustein von Exponential:

- Er gibt jedem Inhaltsobjekt ein bearbeitbares Attribut **Metadaten**: Seitentitel, Schlüsselwörter, Beschreibung,
  kanonischer Link und, seit 1.3.6, ein **Open-Graph-Bild**;
- er erstellt mit Cronjobs **XML-Sitemaps**.

## Verwenden

1. Fügen Sie den Metadaten-Datentyp den Klassen hinzu, die Sie beschreiben wollen (**Setup > Klassen**).
2. Geben Sie die Metadaten in Ihrem Seitenlayout aus (siehe das Beispiel).
3. Führen Sie den Sitemap-Cronjob aus und tragen Sie die Sitemap in `robots.txt` ein.

## Beispiel: die Metadaten in Ihrem Design ausgeben

```
{def $meta = metadata( $module_result.node_id ) }
{if $meta}
    {if $meta.title}<title>{$meta.title|wash}</title>{/if}
    {if $meta.keywords}<meta name="keywords" content="{$meta.keywords|implode(',')|wash}">{/if}
    {if $meta.description}<meta name="description" content="{$meta.description|wash}">{/if}
{else}
    <title>{$site_title}</title>
    {foreach $site.meta as $key => $item}
        <meta name="{$key|wash}" content="{$item|wash}">
    {/foreach}
{/if}
```

## Sitemaps

Vier Cronjob-Teile schreiben Sitemaps für jeden Siteaccess: `sitemap`, `archivesitemap`, `newssitemap` und
`mobilesitemap`.

```bash
php runcronjobs.php sitemap
```

Der Befehl gibt den Namen jeder geschriebenen Datei aus (seit 1.4.4; vorher scheiterte er am Dateiobjekt). Die Dateien
werden unter `var/storage/sitemap/<siteaccess>/` geschrieben, zum Beispiel `urlset_standard_<siteaccess>.xml`; die Namen
werden in `xrowsitemap.ini` festgelegt. Die News-Sitemap hört still auf, wenn der News-Teilbaum leer ist.

Dann:

1. Sorgen Sie dafür, dass Ihre Rewrite-Regeln das XML durchlassen: `RewriteRule ^sitemap[^/]*\.xml - [L]`.
2. Tragen Sie die Sitemap in `robots.txt` ein:

```
Sitemap: https://www.example.com/sitemaps/index
```

## Open-Graph-Bild

Seit 1.3.6 und 1.3.7 tragen die Metadaten `og_image`, Breite, Höhe, Alternativtext und Typ.

- Wählen Sie im Bearbeitungsformular des **Objekts** das Bild mit derselben Oberfläche zum Auswählen und Entfernen wie
  bei einer Objektbeziehung. Der Wert wird in `data_text` gespeichert.
- Legen Sie im **Klassen**-Attribut ein **Standard**-Open-Graph-Bild fest, das Objekte ohne eigenes verwenden (der
  Standard `data_text5`, mit Rückfall auf `data_int4`). `xrowMetaDataFunctions` greift auf diesen Standard zurück, wenn
  ein Objekt keinen Wert hat.
- Die Bearbeitungsformulare von Klasse und Objekt zeigen das gewählte Bild eingebettet unter dem Namensfeld und eine
  Meldung „keine Beziehung“, wenn keines gewählt ist.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.3.5 bis 1.3.7 | Januar 2024 bis August 2026 | README in Markdown; das Open-Graph-Bild. |
| 1.4.0 | 22. September 2026 | Modulansichten sind auf einem dauerhaften Worker (Velocity) sicher; siehe [Verhaltensänderungen](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md#1-persistent-php-workers-module-views-no-longer-declare-at-file-level-without-a-guard). |
| 1.4.1, 1.4.2 | | Die Extension gibt ihre Version, Lizenz und Website an; die Info-Seite nennt sie „Xrow Meta Data“; die Beschreibung nennt Exponential. |
| 1.4.3 | 1. Oktober 2026 | Ein vollständiger englischer Übersetzungskatalog (`translations/eng-US/translation.ts`; vorher wurde auf einem englischen Siteaccess jeder Text in der Debug-Ausgabe als fehlende Übersetzung gemeldet); Deutsch für den kanonischen Link, das Open-Graph-Bild und die Seitentexte; das Kontrollkästchen „mehr“ des Metadatenfelds verwendet jQuery 4 (`.on()`). |
| 1.4.4 | 2. Oktober 2026 | Befehle und Cronjob-Teile führen eine Beschreibung; Kommandozeilenskripte und Modulansichten sind Klassen, die die Dateien aufrufen ([Einzelheiten](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)). |

## Verwandte Seiten

- [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md): Der Export hat einen Spalten-Handler für diesen Datentyp
- [bcgooglesitemaps](bcgooglesitemaps.md): einfache Sitemaps
- [robots.txt](../robots-txt.md)
- [Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md)
- [Chronik](../../../../eng-us/history/extensions/xrowmetadata.md) und [Versionshinweise](../../../../changelogs/extensions/xrowmetadata.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/xrowmetadata.md)
- Monate: [2026-08](../../../../eng-us/history/extensions/months/2026-08.md), [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
