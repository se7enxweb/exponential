---
title: WordPress und Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 40
summary: Eine WordPress-Site nach Exponential 6 umziehen und zurück – der WXR-Export, WP-CLI und die REST-API, die Abbildung von Beiträgen, Seiten, Medien, Kategorien, Schlagwörtern, Autoren, Kommentaren und SEO-Feldern, HTML zu XmlText, Permalinks und Weiterleitungen, die Umstellung und der Rückweg über den WordPress Importer.
keywords: WordPress, WXR, WordPress eXtended RSS, wp export, WP-CLI, wp-json, REST-API, Application Passwords, Anwendungspasswörter, Beiträge, Seiten, Anhänge, Kategorien, Schlagwörter, Permalinks, Weiterleitungen, WordPress Importer, Migration, REST API, posts, pages, attachments, categories, tags, redirects, migration
type: install
---
# WordPress und Exponential

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Abbildung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## Inhalt

- [1. Was WordPress exportieren kann](#1-was-wordpress-exportieren-kann)
- [2. WordPress auf Exponential abbilden](#2-wordpress-auf-exponential-abbilden)
- [3. Von WordPress zu Exponential, Schritt für Schritt](#3-von-wordpress-zu-exponential-schritt-für-schritt)
- [4. Adressen und Weiterleitungen](#4-adressen-und-weiterleitungen)
- [5. Von Exponential zu WordPress](#5-von-exponential-zu-wordpress)
- [6. Risiken und Checkliste](#6-risiken-und-checkliste)
- [Quellen](#quellen)

## 1. Was WordPress exportieren kann

Laut der eigenen Dokumentation von WordPress:

| Werkzeug | Was Sie erhalten | Grenzen |
|---|---|---|
| **Werkzeuge > Daten exportieren** (englisch **Tools > Export**) | eine XML-Datei im Format „WordPress eXtended RSS“ oder WXR mit Beiträgen, Seiten, Custom Post Types, Kommentaren, benutzerdefinierten Feldern, Kategorien, Schlagwörtern, eigenen Taxonomien und Benutzern; Filter nach Status, Autor und Zeitraum | keine Site-Konfiguration, keine Dateien |
| **WP-CLI `wp export`** | dasselbe WXR, aufgeteilt in Dateien (`--dir`, `--max_file_size` in MB, Standard 15, `-1` für keine Grenze) | die Dokumentation von WP-CLI: „WXR files do not include site configuration (options) or the attachment files themselves“ (WXR-Dateien enthalten weder die Site-Konfiguration (Optionen) noch die Anhangsdateien selbst) |
| **REST-API** (`/wp-json/wp/v2/...`) | JSON zu Beiträgen, Seiten, Medien, Kategorien, Schlagwörtern, Benutzern und mehr; `per_page` 1 bis 100, Gesamtzahlen in den Headern `X-WP-Total` und `X-WP-TotalPages` | Authentifizierung mit Anwendungspasswörtern (Application Passwords, seit WordPress 5.6) oder einem Cookie mit Nonce |
| Das Verzeichnis `wp-content/uploads` | die Mediendateien selbst | vom Server kopieren |

Nutzen Sie WXR als eingefrorene Quelle des Imports (eine Datei, alle Inhalte, wiederholbar) und das Upload-Verzeichnis
für die Dateien. Nutzen Sie die REST-API, wenn Sie keine WXR-Datei bekommen können, oder für ein Delta der letzten Tage
vor der Umstellung.

Was WXR pro Eintrag enthält (die Elemente des Formats): den Titel, den Permalink (`<link>`), `<wp:post_id>`,
`<wp:post_name>` (den Slug), `<wp:post_type>`, `<wp:status>`, `<wp:post_parent>`, die Datumsangaben, `<content:encoded>` (das
HTML des Inhalts, einschließlich der Kommentare des Block-Editors), `<excerpt:encoded>`, die Kategorien und Schlagwörter, `<wp:postmeta>` (benutzerdefinierte
Felder, auch die von SEO-Plugins) und `<wp:comment>`. Anhänge sind Einträge vom Typ `attachment` mit ihrer URL.

## 2. WordPress auf Exponential abbilden

| WordPress | Exponential 6 |
|---|---|
| Beitrag | `article`-Objekt (oder `blog_post`) unterhalb eines Blog-Ordners |
| Seite (mit `post_parent`) | `folder`-/`article`-Objekte, deren Baum der Seitenhierarchie folgt |
| Custom Post Type | eine Klasse pro Typ, Attribute pro benutzerdefiniertem Feld |
| `post_title` | `title` (`ezstring`) |
| `post_content` (HTML, Block-Markup) | `body` (`ezxmltext`), umgewandelt: die Blockkommentare `<!-- wp:... -->` entfernen, auf die vereinfachte Eingabe reduzieren, Bilder zu Einbettungen |
| `post_excerpt` | `intro` (`ezxmltext`) oder `eztext` |
| Beitragsbild (Post-Meta `_thumbnail_id`) | ein `ezimage`-Attribut oder eine Relation zum Bildobjekt |
| Anhang | `image`- oder `file`-Objekt unterhalb eines Medienordners |
| Kategorie (hierarchisch) | Ordner (Hauptkategorie = Hauptort) oder `eztags` |
| Schlagwort | `ezkeyword` oder `eztags` |
| Autor | ein Benutzerobjekt; der Eigentümer des Objekts oder ein `ezauthor`-Attribut |
| Kommentar | Unterobjekte einer Kommentarklasse, oder archiviert |
| Benutzerdefinierte Felder (ACF und andere) | Attribute; sich wiederholende Gruppen werden zu Unterobjekten oder `ezmatrix` |
| Shortcodes | umgewandelt in Custom Tags oder Einbettungen, oder vor dem Import zu HTML gerendert |
| Titel und Beschreibung eines SEO-Plugins (Post-Meta) | `xrowmetadata`-Attribut |
| Status `draft`, `private`, `future` | unveröffentlichte Entwürfe, eine Sektion für Mitglieder oder ein Veröffentlichungsdatum |
| Menüs | der Inhaltsbaum und seine Sortierung; Menüs, die anderswohin verweisen, werden zu Links |
| Benutzer und Rollen | Benutzerobjekte in Gruppen; Rollen neu aufgebaut. WordPress speichert Passwörter in eigenen Hash-Formaten, die Exponential nicht lesen kann: Planen Sie das Zurücksetzen ein ([Abbildung, 6](inhaltsmodell-zuordnen.md#6-benutzer-gruppen-rollen-und-passwörter)) |

## 3. Von WordPress zu Exponential, Schritt für Schritt

1. **Bestandsaufnahme**: Post Types und ihre Anzahl (`wp post list --post_type=<type> --format=count`), Plugins, die Inhalte speichern
   (Page Builder, benutzerdefinierte Felder, SEO, Formulare), die Größe der Uploads, die Permalink-Struktur (Einstellungen > Permalinks).
2. **Legen Sie die Klassen** in Exponential und den Ordnerbaum an (Blog, Seiten, Medien).
3. **Export**: `wp export --dir=<dir> --max_file_size=-1` (oder Werkzeuge > Daten exportieren), und kopieren Sie `wp-content/uploads`.
4. **Umwandeln**: Ein Skript liest das WXR mit einem XML-Parser und schreibt pro Eintrag eine JSON-/CSV-Zeile mit der Remote-ID
   (`wp-<post_id>`), dem übergeordneten Eintrag, Titel, umgewandeltem Inhalt, Datumsangaben, Autor, Kategorien, Schlagwörtern, SEO-Feldern und dem alten Permalink.
   Page Builder, die das Layout in Post-Meta oder Shortcodes speichern, brauchen eine eigene Umwandlung oder als Ausweichlösung gerendertes HTML
   (die veröffentlichte Seite abrufen und den Inhaltsbereich übernehmen).
5. **Importieren Sie zuerst die Medien** (Anhänge als Bild-/Dateiobjekte mit `wp-<post_id>`; die Upload-Dateien über ihren Pfad),
   dann die Seiten in der Reihenfolge der Hierarchie, dann die Beiträge, dann die Kommentare. Nutzen Sie den Importer von xrowextract oder das
   [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).
6. **Interne Links umschreiben**: Links auf `/wp-content/uploads/...` und auf andere Beiträge werden zu Einbettungen oder Links auf die neuen
   Knoten (in einem zweiten Durchlauf über die Remote-ID aufgelöst).
7. **Weiterleitungen** ([4](#4-adressen-und-weiterleitungen)), **SEO**-Felder, **Suchindex**, **Sitemap**.
8. **Delta**: Exportieren Sie vor der Umstellung die seit dem Hauptexport geänderten Einträge (Datumsfilter des WXR oder die REST-API
   mit `modified_after`) und führen Sie den Import erneut aus; dank der Remote-IDs wird daraus eine Aktualisierung.
9. **Stellen Sie um** und beobachten Sie die 404-Liste ([Checkliste, 8](checkliste.md#8-umstellung)).

## 4. Adressen und Weiterleitungen

WordPress-Adressen folgen der Permalink-Struktur (`/%year%/%monthnum%/%postname%/`, `/%category%/%postname%/`,
`/?p=123` bei der Standardeinstellung „Einfach“), dazu kommen Archive für Kategorien, Schlagwörter, Autoren, Datumsangaben und Feeds.

| Alte Adresse | In Exponential |
|---|---|
| Permalinks von Beiträgen und Seiten | ein weiterleitender eigener URL-Alias auf dem Knoten, erzeugt aus `<link>` im WXR |
| `/category/<slug>/`, `/tag/<slug>/` | Aliase auf die Ordner- oder Schlagwortansicht |
| `/?p=123`, `/?page_id=45` | der Webserver, aus der Weiterleitungstabelle (Query-Strings erreichen URL-Aliase nie) |
| `/feed/`, `/category/<slug>/feed/` | ein RSS-Export von Exponential, über einen Alias |
| `/wp-content/uploads/...` | Weiterleitung auf die neuen Medienobjekte, oder die Dateien unter diesem Pfad belassen |
| `/wp-admin/`, `/wp-login.php` | 410 oder eine Weiterleitung auf die neue Anmeldung |

## 5. Von Exponential zu WordPress

1. Exportieren Sie mit xrowextract (CSV/JSON pro Klasse, mit URL-Alias, Remote-ID und jedem Attribut) und kopieren Sie das
   Speicherverzeichnis ([Exponential verlassen](exponential-verlassen.md)).
2. Wandeln Sie XmlText in HTML um (mit den Templates der Site rendern oder das XML transformieren) und schreiben Sie eine WXR-Datei: ein
   `<item>` pro Objekt mit `<wp:post_type>`, `<wp:post_name>` aus dem Alias, Kategorien aus dem Baum.
3. Importieren Sie mit dem **WordPress Importer**: Er importiert Beiträge, Seiten, Custom Post Types, Kommentare, Metadaten, Begriffe und
   Autoren, fragt Sie, welchem bestehenden oder neuen Benutzer jeder Autor zugeordnet werden soll, und kann „Dateianhänge herunterladen und importieren“
   (halten Sie die alten Medien-URLs während des Imports also erreichbar).
4. Leiten Sie die Exponential-Adressen auf die WordPress-Permalinks um (ein Weiterleitungs-Plugin oder der Webserver).
5. Benutzer: WordPress kann die Hashes von Exponential nicht prüfen; planen Sie das Zurücksetzen der Passwörter ein.

## 6. Risiken und Checkliste

| Risiko | Gegenmaßnahme |
|---|---|
| Inhalte aus Page Buildern | die Umwandlung zuerst an den komplexesten Seiten testen oder gerendertes HTML importieren |
| Im Text verbliebene Shortcodes | auflisten (`grep -o '\[[a-z_]*'`), jeden umwandeln oder rendern |
| Verlorene Beitragsbilder | `_thumbnail_id` ausdrücklich abbilden |
| Permalinks mit Query-Strings | Weiterleitungen im Webserver |
| Benutzer können sich nicht anmelden | Zurücksetzen der Passwörter vorab ankündigen |

- [ ] WXR-Export und Kopie der Uploads eingefroren; Anzahl pro Post Type notiert.
- [ ] Klassen und Baum angelegt; Abbildungstabelle geschrieben.
- [ ] Umwandlung an den 20 schwierigsten Einträgen getestet; Shortcodes behandelt.
- [ ] Medien, Seiten, Beiträge, Kommentare mit den Remote-IDs `wp-<id>` importiert; interne Links umgeschrieben.
- [ ] Weiterleitungstabelle erzeugt und getestet; Feeds und Uploads behandelt.
- [ ] SEO-Felder, Sitemap, Analytics; Delta-Import vor der Umstellung.

## Quellen

- WP-CLI `wp export`: <https://developer.wordpress.org/cli/commands/export/>
- Paginierung der REST-API: <https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/>
- Die Seite Werkzeuge > Daten exportieren: <https://wordpress.org/documentation/article/tools-export-screen/>

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
