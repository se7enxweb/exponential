---
title: SEO-Prüfungen
audience: customer, editor, businesses
category: customer-success
order: 16
summary: Was ein Website-Betreiber prüft, damit Suchmaschinen die Website finden und verstehen - was Exponential bereits tut (lesbare Adressen, Weiterleitungen nach Verschiebungen, robots.txt, Feeds), was die Redaktion beeinflusst, worum Sie den Website-Ersteller bitten und wo Sie nach Problemen suchen.
keywords: SEO, Suchmaschinenoptimierung, Titel, Beschreibungen, URL-Aliase, Weiterleitungen, robots.txt, Sitemap, Metadaten, Auffindbarkeit, Website-Betreiber, search engine optimization, titles, descriptions, URL aliases, redirects, sitemap, metadata, findability, site owner
type: help
---
# SEO-Prüfungen

Diese Seite ist für den Betreiber und die Marketingleitung, die möchten, dass die Website gefunden wird. Das meiste,
was Suchmaschinen brauchen, ist eingebaut oder eine Erweiterung entfernt; der Rest ist gutes Schreiben durch die
Redaktion. Die Checkliste für Website-Ersteller ist [SEO-Grundlagen für Exponential-Websites](../../../../en/us/help/creators/website-creators/seo-basics.md);
diese Seite ist die Sicht des Betreibers: was zu prüfen ist, wer was behebt und wie oft. Die monatliche Routine ist die
[Monatliche SEO-Checkliste](monatliche-seo-checkliste.md).

## Inhalt

1. [Was die Software bereits tut](#was-die-software-bereits-tut)
2. [Was davon abhängt, wie die Website gebaut wurde](#was-davon-abhängt-wie-die-website-gebaut-wurde)
3. [Was die Redaktion beeinflusst](#was-die-redaktion-beeinflusst)
4. [Wo Sie nach Problemen suchen](#wo-sie-nach-problemen-suchen)
5. [Wer was behebt](#wer-was-behebt)

## Was die Software bereits tut

Aus [SEO-Grundlagen für Exponential-Websites](../../../../en/us/help/creators/website-creators/seo-basics.md):

| Bedarf | Wie |
|---|---|
| Lesbare Adressen aus den Namen der Inhalte | URL-Aliase, eingebaut |
| Alte Adressen funktionieren nach Umbenennen oder Verschieben weiter | Verlaufs-Aliase leiten weiter, eingebaut |
| Eigene kurze Adressen und Muster | globale Aliase und Wildcards, eingebaut |
| `robots.txt` | im Web-Wurzelverzeichnis ausgeliefert |
| Feeds | RSS-Exporte, eingebaut ([RSS- und Podcast-Feeds](../wachstum/rss-und-podcast-feeds.md)) |
| Schnelle Seiten | Caches und der Response-Cache von Velocity |

## Was davon abhängt, wie die Website gebaut wurde

Fragen Sie Ihren Website-Ersteller oder Partner einmal, und schreiben Sie die Antworten auf:

| Frage | Warum | Lesen Sie |
|---|---|---|
| Hat jede Seite ihren eigenen Titel und ihre eigene Beschreibung? Welches Feld wird zur Beschreibung? | der Ausschnitt in den Suchergebnissen | [Seitenmetadaten und Teilen in sozialen Netzwerken](../../../../en/us/help/creators/website-creators/metadata-and-social-sharing.md) |
| Gibt es eine XML-Sitemap, per Cron neu erzeugt, in `robots.txt` aufgeführt? | hilft Suchmaschinen, jede Seite zu finden | [XML-Sitemaps](../../../../en/us/help/creators/website-creators/sitemaps.md) |
| Geben Inhaltselemente mit mehreren Orten eine kanonische Adresse aus? | eine Adresse pro Seite | [SEO-Grundlagen](../../../../en/us/help/creators/website-creators/seo-basics.md) |
| Gibt es einen einzigen Hostnamen, mit HTTPS, und werden die anderen weitergeleitet? | keine doppelten Websites | [Live gehen: Hosts, HTTPS und Siteaccesses](../../../../en/us/help/creators/website-creators/going-live-hosts-https.md) |
| Hat jede Sprache ihre eigene Adresse? | Suchmaschinen zeigen die richtige Sprache | [Sprachumschalter und Sprach-URLs](../../../../en/us/help/creators/website-creators/language-switcher-and-language-urls.md) |

## Was die Redaktion beeinflusst

Redakteure schreiben, was Suchmaschinen lesen: Titel, Einleitungen, Alternativtexte und Linktexte
([SEO-Grundlagen](../../../../en/us/help/creators/website-creators/seo-basics.md)).

- [ ] **Titel** kurz und klar. Der Name eines Elements wird zu seinem Titel und zu seiner Adresse.
- [ ] **Einleitung oder Beschreibung** ausgefüllt: Sie wird zur Beschreibung, wenn kein Metadatenfeld ausgefüllt ist.
- [ ] **Überschriften** in der richtigen Reihenfolge, eine Hauptüberschrift pro Seite.
- [ ] **Linktexte**, die sagen, wohin sie führen („Öffnungszeiten“, nicht „hier klicken“).
- [ ] **Alternativtexte** an jedem Bild ([Richtlinien für Bilder und Dateien](richtlinien-fuer-bilder-und-dateien.md)).
- [ ] **Umbenennen und verschieben Sie ohne Sorge**; alte Adressen leiten weiter. **Entfernen** ist etwas anderes:
      Entscheiden Sie, wohin die Besucher geleitet werden ([URLs, Weiterleitungen und verschobene Seiten](urls-weiterleitungen-und-verschobene-seiten.md)).

## Wo Sie nach Problemen suchen

| Sehen Sie nach in | Es sagt Ihnen | Quelle |
|---|---|---|
| **Setup > Suchstatistiken** | wonach Besucher auf Ihrer Website suchen und was sie nicht finden (standardmäßig aus) | [Website-Suche und wonach Besucher suchen](website-suche-und-wonach-besucher-suchen.md) |
| **Setup > URL-Verwaltung**, Filter ungültig | kaputte ausgehende Links | [URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md) |
| Kaputte Beziehungen | Seiten, die auf etwas Entferntes oder Verborgenes verweisen | [Die Inhaltsprüfungen](../../../../en/us/features/6.0/content-checks.md) |
| Die Webmaster-Tools der Suchmaschinen | Indexierungs- und Crawl-Fehler | [SEO-Grundlagen](../../../../en/us/help/creators/website-creators/seo-basics.md) |
| Das Fehlerlog nach einem Relaunch | alte Adressen, die ins Leere laufen | [Weiterleitungen und verschobene Inhalte](../../../../en/us/help/creators/website-creators/redirects-and-moved-content.md) |

## Wer was behebt

| Problem | Wer |
|---|---|
| Schwache Titel, fehlende Beschreibungen oder Alternativtexte | Redaktion |
| Eine entfernte Seite ohne Weiterleitung | ein Redakteur mit Zugriff auf URL-Aliase, oder der Administrator |
| Keine Sitemap, kein kanonischer Link, mehrere Hostnamen | Website-Ersteller oder Partner |
| Langsame Seiten | Hoster oder Partner ([Leistung und Caching](../../../../en/us/help/creators/website-creators/performance-and-caching.md)) |

## Verwandte Seiten

- [Monatliche SEO-Checkliste](monatliche-seo-checkliste.md)
- [URLs, Weiterleitungen und verschobene Seiten](urls-weiterleitungen-und-verschobene-seiten.md)
- [SEO-Grundlagen für Exponential-Websites](../../../../en/us/help/creators/website-creators/seo-basics.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
