---
title: Sitemaps
audience: website-creator, admin
category: website-creation
order: 390
summary: XML-Sitemaps für Suchmaschinen mit bcgooglesitemaps oder xrowmetadata erzeugen - die Cronjob-Teile, die Einstellungen, mehrsprachige und Multisite-Sitemaps, die Datei ausliefern und in robots.txt eintragen.
keywords: Sitemap, XML-Sitemap, Sitemap-Cronjob, Suchmaschinen, mehrsprachige Sitemap, Website-Ersteller, sitemap, XML sitemap, bcgooglesitemaps, xrowmetadata, googlesitemaps, sitemap cronjob, robots.txt, search engines, multilingual sitemap, website creator
type: help
---
# Sitemaps

Diese Seite richtet sich an Website-Ersteller, die Suchmaschinen eine Liste aller Seiten geben. Exponential erzeugt
Sitemaps mit einer von zwei Erweiterungen; beide laufen als Cronjob-Teile und schreiben eine Datei, die der Webserver
ausliefert.

## Inhalt

1. [Eine Erweiterung wählen](#eine-erweiterung-wählen)
2. [bcgooglesitemaps](#bcgooglesitemaps)
3. [Sitemaps mit xrowmetadata](#sitemaps-mit-xrowmetadata)
4. [Ausliefern und eintragen](#ausliefern-und-eintragen)
5. [Checkliste](#checkliste)

## Eine Erweiterung wählen

| | `bcgooglesitemaps` | `xrowmetadata` |
|---|---|---|
| Teil der Distribution | ja ([Funktion robots.txt](../../../../eng-us/features/6.0/robots-txt.md)) | ja |
| Cronjob-Teile | `googlesitemaps`, `googlesitemapsmultilingual` | `sitemap`, `archivesitemap`, `newssitemap`, `mobilesitemap` |
| Wählen, wenn | Sie eine einfache Sitemap wollen | Sie xrowmetadata bereits für Seitenmetadaten verwenden |

## bcgooglesitemaps

Nach [bcgooglesitemaps](../../../../eng-us/features/6.0/extensions/bcgooglesitemaps.md):

```bash
php runcronjobs.php googlesitemaps
php runcronjobs.php googlesitemapsmultilingual
```

Einstellungen in `settings/override/bcgooglesitemaps.ini.append.php`:

| Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `BCGoogleSitemapSettings` | `SitemapRootNodeID` | `2` | Startknoten |
| `BCGoogleSitemapSettings` | `Protocol` | `https` | Schema, das in die Adressen geschrieben wird |
| `BCGoogleSitemapSettings` | `Filename`, `Filesuffix`, `Path` | `sitemap`, `.xml`, leer | die Datei |
| `Classes` | `ClassFilterType`, `ClassFilterArray[]` | `exclude`, leer | Klassen einschließen oder ausschließen |
| `NodeSettings` | `Main_Node_Only` | `false` | nur Hauptplatzierungen (vermeidet Duplikate) |
| `NodeSettings` | `ExcludeNodes`, `ExcludedNodeIDs[]` | `disabled`, leer | Teilbäume auslassen |

Schließen Sie Klassen aus, die keine Seiten sind (Bilder, Dateien, Banner), und setzen Sie `Main_Node_Only=true`, wenn
Inhalte mehrere Platzierungen haben.

## Sitemaps mit xrowmetadata

```bash
php runcronjobs.php sitemap
```

schreibt Dateien unter `var/storage/sitemap/<siteaccess>/` (Namen in `xrowsitemap.ini`) und gibt jeden Dateinamen aus;
die Rewrite-Regeln müssen sie durchlassen (`RewriteRule ^sitemap[^/]*\.xml - [L]`)
([xrowmetadata, Sitemaps](../../../../eng-us/features/6.0/extensions/xrowmetadata.md#sitemaps)).

## Ausliefern und eintragen

1. Führen Sie den Teil einmal aus, und prüfen Sie, ob die Datei existiert und Ihre Seiten auflistet.
2. Nehmen Sie den Teil in den Cron auf, zum Beispiel nächtlich ([Cronjobs](../../../anleitungen/cronjobs.md)).
3. Stellen Sie sicher, dass der Webserver (oder Velocity) die Datei ausliefert, statt sie an `index.php` weiterzugeben.
4. Tragen Sie sie in `robots.txt` ein:

   ```text
   Sitemap: https://www.example.com/sitemap.xml
   ```

5. Reichen Sie sie in den Webmaster-Werkzeugen der Suchmaschinen ein.

## Checkliste

- [ ] Nur echte Seiten sind aufgeführt, jede einmal.
- [ ] Versteckte Inhalte und Inhalte nur für Mitglieder sind nicht aufgeführt (testen Sie mit einer Seite, von der Sie
  wissen, dass sie eingeschränkt ist).
- [ ] Die Adressen verwenden den öffentlichen Host und `https`.
- [ ] Jeder Sprach-Siteaccess hat seine Sitemap.
- [ ] Der Cron erzeugt sie neu.

## Verwandte Seiten

- [robots.txt für Website-Ersteller](robots-txt.md)
- [SEO-Grundlagen](seo-grundlagen.md)
- [Cronjobs für Anwendungen](../anwendungsentwickler/cronjobs-fuer-anwendungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
