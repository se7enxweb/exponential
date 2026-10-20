---
title: Wix, Squarespace, GoDaddy und Exponential
audience: admin, developer, project-manager, sales-marketing
category: install-upgrade
order: 45
summary: Eine Site von den gehosteten Baukästen Wix, Squarespace und GoDaddy Website Builder nach Exponential 6 umziehen und von Exponential zu ihnen – was jeder Baukasten laut seinem eigenen Hilfe-Center exportieren kann, was von der Live-Site abgelesen werden muss, URL-Weiterleitungen auf beiden Seiten und ein Plan auf Grundlage eines Crawls.
keywords: Wix, Squarespace, GoDaddy, Websites + Marketing, Website-Baukasten, Baukasten, Export, WordPress-XML, CSV, CMS-Sammlungen, URL Redirect Manager, URL mappings, Weiterleitungen, Crawl, Migration, site builder, export, WordPress XML, CMS collections, crawl, migration
type: install
---
# Wix, Squarespace, GoDaddy und Exponential

Gehostete Website-Baukästen halten eine Site auf ihrer eigenen Plattform. Was Sie mitnehmen können, ist begrenzt und
unterscheidet sich stark zwischen ihnen; planen Sie ein, einen Teil der Inhalte von der Live-Site abzulesen.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Statische HTML-Sites](statisches-html.md) (die Crawl-Methode) ·
[Werkzeuge](exponential-tools.md)

## Inhalt

- [1. Was jeder Baukasten exportieren kann](#1-was-jeder-baukasten-exportieren-kann)
- [2. Die Crawl-Methode für das, was sich nicht exportieren lässt](#2-die-crawl-methode-für-das-was-sich-nicht-exportieren-lässt)
- [3. Abbildung und Import](#3-abbildung-und-import)
- [4. Adressen und Weiterleitungen](#4-adressen-und-weiterleitungen)
- [5. Von Exponential zu einem Baukasten](#5-von-exponential-zu-einem-baukasten)
- [6. Checkliste](#6-checkliste)
- [Quellen](#quellen)

## 1. Was jeder Baukasten exportieren kann

### Wix

Laut dem Hilfe-Center von Wix:

- **Kein Export der Site.** Eine Wix-Site „muss auf den Servern von Wix gehostet und betrieben werden“ („needs to be
  hosted and operated on Wix's servers“), wegen der proprietären Technik.
- **Blog**: „Derzeit ist es nicht möglich, Blogbeiträge auf andere Plattformen zu exportieren“ („Currently, it is not
  possible to export blog posts to other platforms“; ein Funktionswunsch auf Seiten von Wix).
- **CMS-Sammlungen**: *More Actions > Export to CSV* (alle Felder, die gefilterten Einträge oder nur die Struktur; UTF-8;
  Zeiten in UTC+0).
- Für manche Daten gibt es **REST-APIs**, zum Beispiel zum Abfragen von Blogbeiträgen (`POST https://www.wixapis.com/v3/posts/query`,
  `cursorPaging` bis 100, `fieldsets` wie `RICH_CONTENT`, `URL`, `SEO`). Der Inhalt kommt als Knotenstruktur des
  Rich-Content-Formats von Wix, nicht als HTML, und braucht einen eigenen Konverter.
- Shop-Produkte lassen sich exportieren (Wix hat dazu einen Artikel; für diese Seite nicht geöffnet).

### Squarespace

Laut dem Hilfe-Center von Squarespace exportiert *Settings > Import/Export* in eine **WordPress-XML**-Datei mit den
Layoutseiten, **einer** Blogseite mit ihren Beiträgen (bis zu 1000 Kommentare pro Beitrag) sowie Text-, Bild- und
Galerieinhalten. **Nicht exportiert** werden: Alben, Cover, Indexseiten, Kalender, Portfolios, Shopseiten; seitenspezifische Header, Footer und Seitenleisten;
weitere Blogseiten; Dropdowns; Audio-, Video- und Produktblöcke; Entwürfe; Stileinstellungen und eigenes CSS. Für Produkte gibt es
einen separaten CSV-Export (bis zu 10.000; ohne Zusatzinformationen, Bewertungen, hervorgehobene Bilder, Alternativtext und Variantenbilder).

Das WordPress-XML ist WXR: Wandeln Sie es wie in der [WordPress-Anleitung](wordpress.md#3-von-wordpress-zu-exponential-schritt-für-schritt) um.

### GoDaddy Website Builder (Websites + Marketing)

Im Hilfe-Center von GoDaddy wurde kein offizieller Export der Site oder der Inhalte gefunden. Die eingebaute Sicherung „sichert nur die
Abschnitte, Texte und Einstellungen Ihrer Site“ („only backs up your site's sections, text and settings“) und umfasst
keine Blogs, Produkte, Termine oder Bilder; der Artikel beschreibt das Wiederherstellen einer Sicherung, nicht das Herunterladen.
Für Produkte des Online-Shops gibt es einen CSV-Export (ein Hilfeartikel existiert; nicht geöffnet).
Eine API und ein Import von Weiterleitungen wurden nicht geprüft. Planen Sie ein, die Site von den Live-Seiten abzulesen
([2](#2-die-crawl-methode-für-das-was-sich-nicht-exportieren-lässt)).

## 2. Die Crawl-Methode für das, was sich nicht exportieren lässt

1. **Erfassen Sie jede Adresse**: die Sitemap des Baukastens (`/sitemap.xml` bei den meisten), dazu ein Crawl, dazu die
   meistbesuchten Seiten aus der Webanalyse.
2. **Rufen Sie jede Seite ab** mit einem Crawler, den Sie selbst steuern, rücksichtsvoll (mit begrenzter Rate), und speichern Sie das HTML.
3. **Extrahieren Sie den Inhaltsbereich** je Seitentyp mit CSS-Selektoren: Titel, Inhalt, Bilder, Datumsangaben, Autor. Baukästen
   verpacken Inhalte in generiertes Markup; schreiben Sie einen Extraktor pro Seitentemplate und testen Sie ihn an einer Stichprobe.
4. **Laden Sie die Bilder herunter**, in ihrer größten Auflösung und mit ihrem Alternativtext.
5. **Schreiben Sie JSON-Zeilen** mit der alten Adresse als Remote-ID (`page-<hash of address>`) und importieren Sie dann wie bei
   jeder anderen Quelle ([Werkzeuge](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)).

Prüfen Sie die Nutzungsbedingungen des Baukastens, bevor Sie Ihre eigene Site in großem Umfang crawlen, und tun Sie es, solange Sie das
Konto noch haben.

## 3. Abbildung und Import

| Inhalt im Baukasten | Exponential 6 |
|---|---|
| Seiten | `folder`-/`article`-Objekte in einem Baum, der der Navigation der Site folgt |
| Blogbeiträge | `article`-Objekte unterhalb eines Blog-Ordners; Schlagwörter zu `ezkeyword` oder `eztags` |
| Einträge von CMS-Sammlungen (CSV von Wix) | eine Klasse pro Sammlung; der Importer von xrowextract liest das CSV mit `--map` |
| Galerien | ein Galerie-Ordner mit Bildobjekten |
| Produkte | die Produktklasse des Kernel-Shops oder Inhalte, die auf einen externen Shop verlinken |
| Formulare | Informationssammlung; frühere Einsendungen archiviert |
| SEO-Titel und -Beschreibung | `xrowmetadata` |

Das Design wird nie exportiert: Planen Sie die Templates der neuen Site von Anfang an ein.

## 4. Adressen und Weiterleitungen

- Erstellen Sie die Weiterleitungstabelle aus der Adressliste von [2](#2-die-crawl-methode-für-das-was-sich-nicht-exportieren-lässt).
- In Exponential: weiterleitende eigene URL-Aliase, Platzhalter für regelmäßige Muster, der Webserver für Query-Strings
  ([Checkliste, 4](checkliste.md#4-urls-und-weiterleitungen)).
- Lassen Sie die Baukasten-Site online, bis das DNS umgestellt ist; danach tragen nur noch Ihre Weiterleitungen die alten Links.

## 5. Von Exponential zu einem Baukasten

| Ziel | Weg hinein | Weiterleitungen |
|---|---|---|
| Wix | CMS-Sammlungen können CSV importieren (aus dem Export von xrowextract); Seiten und Blogbeiträge von Hand oder über die APIs | **SEO > URL Redirect Manager > Import redirects**: ein CSV mit zwei Spalten, höchstens 500 Weiterleitungen pro Datei |
| Squarespace | die eigenen Importoptionen (prüfen Sie diese für Ihren Tarif); sonst von Hand | **URL mappings**: Zeilen der Form `/old -> /new 301`, ein Platzhalter `[name]`, bis zu 400 KB (etwa 2.500 Zeilen) |
| GoDaddy | von Hand (kein Import wurde geprüft) | nicht geprüft |

Exportieren Sie mit xrowextract ([Exponential verlassen](exponential-verlassen.md)) und behalten Sie die Speicherdateien für die Bilder.

## 6. Checkliste

- [ ] Was der Baukasten exportieren kann, ist exportiert (CSV, WordPress-XML); alles andere steht auf der Crawl-Liste.
- [ ] Adressliste vollständig (Sitemap, Crawl, Webanalyse).
- [ ] Extraktoren pro Seitentemplate geschrieben und getestet; Bilder in voller Größe mit Alternativtext heruntergeladen.
- [ ] Mit Remote-IDs importiert; Templates gebaut; Weiterleitungen getestet.
- [ ] Baukasten-Konto bis nach der Umstellung behalten.

## Quellen

- Wix, eine Site exportieren: <https://support.wix.com/en/article/exporting-or-embedding-your-wix-site-elsewhere>
- Wix, Blogbeiträge exportieren: <https://support.wix.com/en/article/wix-blog-request-exporting-blog-posts-to-other-platforms>
- Wix, API zum Abfragen von Beiträgen: <https://dev.wix.com/docs/rest/business-solutions/blog/posts-stats/query-posts>
- Squarespace, Ihre Site exportieren: <https://support.squarespace.com/hc/en-us/articles/206566687-Exporting-your-site>
- Squarespace, URL mappings: <https://support.squarespace.com/hc/en-us/articles/205815308-URL-mappings>
- Squarespace, Produkte exportieren: <https://support.squarespace.com/hc/en-us/articles/360000694948-Export-products-to-a-csv>
- GoDaddy, die Website sichern: <https://www.godaddy.com/help/back-up-my-website-32228>

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
