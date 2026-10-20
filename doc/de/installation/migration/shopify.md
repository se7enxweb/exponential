---
title: Shopify und Exponential
audience: admin, developer, project-manager, sales-marketing
category: install-upgrade
order: 44
summary: Inhalte zwischen Shopify und Exponential 6 übertragen – die Produkt-CSV, die Admin GraphQL API, was Shopify nicht exportiert (Blogbeiträge und Seiten haben keine CSV, Bilder nur als URLs), die CSV der URL-Weiterleitungen, Zuordnung und Vorgehen Schritt für Schritt, in beide Richtungen.
keywords: Shopify, CSV, Admin GraphQL API, X-Shopify-Access-Token, Produkte, Blogbeiträge, Seiten, URL-Weiterleitungen, Migration, products, blog posts, pages, URL redirects, migration
type: install
---
# Shopify und Exponential

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Zuordnung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## 1. Was Shopify exportieren kann

Laut dem Hilfe-Center und der Entwicklerdokumentation von Shopify:

| Quelle | Was Sie erhalten | Grenzen |
|---|---|---|
| **CSV-Exporte** | Produkte, Kunden, Bestellungen, Geschenkkartencodes, Rabattcodes, Finanzdaten; „A CSV file with UTF-8 encoding is the only file format“ (eine CSV-Datei in UTF-8 ist das einzige Dateiformat) | Produktbilder sind nicht in der Datei, nur URLs, die auf den Shop zeigen (sie funktionieren nur, solange er öffentlich bleibt) |
| **Blogbeiträge und Seiten** | keine CSV: Shopify selbst rät, sie zu kopieren und einzufügen | lesen Sie sie stattdessen über die Admin API |
| **Admin GraphQL API** | `POST https://{store}.myshopify.com/admin/api/<version>/graphql.json`, Header `X-Shopify-Access-Token`; Cursor-Paginierung (`first`/`after`, `pageInfo { hasNextPage endCursor }`), höchstens 250 pro Anfrage | höchstens 25.000 Objekte lassen sich paginieren; darüber empfiehlt Shopify Bulk-Operationen |
| **URL-Weiterleitungen** (Content > Menus > URL redirects) | CSV-Export und -Import mit den Spalten „Redirect from“ und „Redirect to“ | bis zu 100.000 Weiterleitungen (20.000.000 bei Plus) |
| Theme, Einstellungen, Menüs, Apps | nicht als Inhalt exportierbar | werden von Hand neu aufgebaut |

## 2. Abbildung auf Exponential

| Shopify | Exponential 6 |
|---|---|
| Blogartikel (Text als HTML, Tags, Autor, Bild, SEO) | `article` mit `ezxmltext`-Text, `ezkeyword`/`eztags`, Eigentümer oder `ezauthor`, Bildbeziehung, `xrowmetadata` |
| Seite | `article`/`folder` |
| Produkt (Titel, Beschreibung als HTML, Bilder, Optionen, Varianten) | eine Produktklasse (Kernel-Shop: `ezprice`/`ezmultiprice`, Optionen) oder Inhalt, der auf den Shopify-Shop verlinkt |
| Kollektion | Ordner oder `eztags` |
| Kunden, Bestellungen | bleiben in Shopify oder in dessen Archiv (personenbezogene Daten) |
| Metafelder | Attribute |

## 3. Schritt für Schritt (Shopify zu Exponential)

1. Entscheiden Sie: nur Inhalte (der Shop bleibt bei Shopify, Exponential ist die Content-Site) oder auch der Katalog.
2. Exportieren Sie die Produkte als CSV; lesen Sie Blogs, Artikel und Seiten über die Admin GraphQL API (eine eigene
   App mit Lese-Scopes im Shop); laden Sie jedes Bild über seine URL herunter, **solange der Shop noch öffentlich ist**.
3. Wandeln Sie das HTML um, importieren Sie Medien, Kollektionen, Produkte, Artikel und Seiten mit Remote-IDs
   (`shopify-<gid>`), mit dem Importer von xrowextract oder dem [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).
4. Exportieren Sie die URL-Weiterleitungen von Shopify und die Pfade von Produkten (`/products/<handle>`), Kollektionen
   (`/collections/<handle>`), Seiten (`/pages/<handle>`) und Artikeln (`/blogs/<blog>/<handle>`); leiten Sie jeden auf
   seinen neuen Knoten weiter ([Checkliste, 4](checkliste.md#4-urls-und-weiterleitungen)).
5. Testen, proben, umstellen.

## 4. Von Exponential zu Shopify

Exportieren Sie mit xrowextract ([Migration weg von Exponential](exponential-verlassen.md)). Produkte: der Produkt-CSV-Import
von Shopify. Artikel und Seiten: über die Admin GraphQL API (keine CSV). Weiterleitungen: der CSV-Import der
URL-Weiterleitungen, aus den URL-Aliasen von Exponential.

## 5. Checkliste

- [ ] Umfang entschieden; Shopify-Shop öffentlich gehalten, bis jedes Bild heruntergeladen ist.
- [ ] Produkte per CSV, Artikel und Seiten per API; Anzahlen geprüft.
- [ ] Remote-IDs; HTML umgewandelt; Kollektionen zugeordnet.
- [ ] Weiterleitungstabelle für jeden Handle-Pfad; getestet.

## Quellen

- URL-Weiterleitungen: <https://help.shopify.com/en/manual/online-store/menus-and-links/url-redirect>
- Produkte exportieren: <https://help.shopify.com/en/manual/products/import-export/export-products>
- Einen Shop duplizieren (was kopiert wird und was nicht): <https://help.shopify.com/en/manual/shopify-admin/duplicate-store>
- GraphQL-Paginierung: <https://shopify.dev/docs/api/usage/pagination-graphql>

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
