---
title: Metadaten und Teilen in sozialen Medien
audience: website-creator, developer
category: website-creation
order: 380
summary: Jeder Seite einen eigenen Titel, eine Beschreibung, Schlüsselwörter und ein Open-Graph-Bild geben - mit der Erweiterung xrowmetadata oder eigenen Feldern - und sie sicher im Pagelayout ausgeben.
keywords: Metadaten, Meta-Beschreibung, Titel, Schlüsselwörter, Teilen in sozialen Medien, Pagelayout, Website-Ersteller, metadata, meta description, title, keywords, Open Graph, og:image, Twitter card, social sharing, xrowmetadata, metadata operator, pagelayout, website creator
type: help
---
# Metadaten und Teilen in sozialen Medien

Diese Seite richtet sich an Website-Ersteller, die wollen, dass jede Seite ihren eigenen Titel, ihre eigene
Beschreibung und ihr eigenes Bild zum Teilen trägt. Ab Werk gibt ein Design websiteweite Werte aus (`SiteName`,
`SiteDescription`; siehe [Ihre Website markengerecht gestalten](ihre-website-markengerecht-gestalten.md)). Werte pro Seite kommen aus der
Erweiterung `xrowmetadata` oder aus Feldern, die Sie Klassen hinzufügen.

## Inhalt

1. [Zwei Wege](#zwei-wege)
2. [Mit xrowmetadata](#mit-xrowmetadata)
3. [Mit eigenen Feldern](#mit-eigenen-feldern)
4. [Open Graph und Bilder zum Teilen](#open-graph-und-bilder-zum-teilen)
5. [Checkliste](#checkliste)

## Zwei Wege

| | `xrowmetadata` | Eigene Felder |
|---|---|---|
| Die Redaktion füllt aus | ein Metadatenfeld pro Objekt: Titel, Schlüsselwörter, Beschreibung, Open-Graph-Bild | gewöhnliche Felder (`meta_description`, `share_image`) |
| Standardwerte | ein Standard-Open-Graph-Bild pro Klasse | Ihr Template entscheidet |
| Sitemaps | vier Sitemap-Cronjobs enthalten | eigene Erweiterung |
| Wählen, wenn | Sie ein vollständiges Paket wollen | Sie nur ein oder zwei Felder wollen |

## Mit xrowmetadata

Aus [xrowmetadata](../../../funktionen/6.0/erweiterungen/xrowmetadata.md):

1. Fügen Sie den Klassen, die beschrieben werden sollen, den Datentyp für Metadaten hinzu (**Setup > Klassen**).
2. Geben Sie ihn im Pagelayout aus:

   ```text
   {def $meta = metadata( $module_result.node_id )}
   {if $meta}
       {if $meta.title}<title>{$meta.title|wash}</title>{/if}
       {if $meta.description}<meta name="description" content="{$meta.description|wash}">{/if}
   {else}
       <title>{$site.title|wash}</title>
   {/if}
   {undef $meta}
   ```

3. Führen Sie den Sitemap-Cronjob aus und melden Sie die Sitemap an ([Sitemaps](sitemaps.md)).

## Mit eigenen Feldern

Fügen Sie ein Feld `meta_description` (`eztext`) hinzu und verwenden Sie die Einleitung, wenn es leer ist:

```text
{if is_set( $module_result.node_id )}
  {def $page = fetch( 'content', 'node', hash( 'node_id', $module_result.node_id ) )
       $desc = ''}
  {if and( is_set( $page.data_map.meta_description ), $page.data_map.meta_description.has_content )}
    {set $desc = $page.data_map.meta_description.content}
  {elseif and( is_set( $page.data_map.intro ), $page.data_map.intro.has_content )}
    {set $desc = $page.data_map.intro.content.output.output_text|striptags|shorten( 160 )}
  {/if}
  {if $desc}<meta name="description" content="{$desc|wash}">{/if}
  {undef $page $desc}
{/if}
```

Setzen Sie das in den Cache-Block des Pagelayouts, dessen Schlüssel die Adresse ist, sonst läuft der Fetch bei jeder
Anfrage.

## Open Graph und Bilder zum Teilen

Soziale Netzwerke lesen `og:title`, `og:description`, `og:image` und `og:url`. Das Theme simple gibt bereits
`og:title`, `og:site_name` und ein `og:image` aus dem Design aus
([Ihre Website markengerecht gestalten](ihre-website-markengerecht-gestalten.md#meta-tags)). Für ein Bild pro Seite verwenden Sie
`og_image` von `xrowmetadata` (mit einem Standard pro Klasse) oder eine eigene Bildrelation und geben einen Alias
passender Größe mit der vollständigen Adresse aus (`|ezroot( , 'full' )` oder der Hostname):

```text
<meta property="og:image" content="{$image.url|ezroot( no, 'full' )}">
```

## Checkliste

- [ ] Jeder Seitentyp hat einen Titel und eine Beschreibung, mit einem sinnvollen Fallback.
- [ ] Werte werden mit `|wash` ausgegeben.
- [ ] Bilder zum Teilen sind absolute URLs eines Alias, der für Vorschauen groß genug ist.
- [ ] Die Metadaten liegen in einem Cache-Block mit der Seite als Schlüssel.
- [ ] Die Redaktion weiß, welches Feld zur Beschreibung wird.

## Verwandte Seiten

- [SEO-Grundlagen](seo-grundlagen.md)
- [Ihre Website markengerecht gestalten](ihre-website-markengerecht-gestalten.md)
- [Das Pagelayout](das-pagelayout.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
