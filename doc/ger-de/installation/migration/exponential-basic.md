---
title: Exponential Basic und Exponential 6
audience: admin, developer, project-manager
category: install-upgrade
order: 24
summary: Eine Site zwischen Exponential Basic 2.4 (dem modernisierten klassischen 2.x-CMS) und Exponential 6 umziehen – zwei verschiedene Codebasen und Datenmodelle, die Inhalte werden also per Skript umgewandelt – mit den Basic-Tabellen, dem Artikel-Markup, Benutzern und Passwörtern, URLs, einem Plan Schritt für Schritt, dem Rückweg, der Prüfung und einer Checkliste.
keywords: Exponential Basic, Bildkatalog, Artikel-XML, Umwandlung, Migration, exponentialbasic, 2.4.0.3, 2.x, eZArticle_Article, image catalogue, eZUser_User, tech generator, article XML, conversion, migration
type: install
---
# Exponential Basic und Exponential 6

**Exponential Basic** (`se7enxweb/exponentialbasic`, neuestes Release `v2.4.0.3` vom 2026-08-18) modernisiert das
klassische CMS Exponential **2.x** für PHP 8 (`php ^8.2`). Es hat eigene Kernel-Module (`ezarticle`, `ezimagecatalogue`,
`ezfilemanager`, `ezforum`, `eztrade`, `ezcalendar`, `ezpoll`, `ezlink`, `ezurltranslator`, `ezuser` und weitere) auf
eigenen Tabellen. Seine README nennt als eines seiner Ziele „future portability (export to another cms)“ (künftige
Portierbarkeit, Export in ein anderes CMS).

Exponential 6 ist eine andere Codebasis mit einem anderen Inhaltsmodell (Klassen, Objekte, ein Inhaltsbaum). Es gibt
keine gemeinsame Datenbank und in keinem der beiden Repositorys einen Konverter zwischen ihnen; eine Migration ist eine
**per Skript ausgeführte Umwandlung der Inhalte**. Diese Seite zeigt Ihnen, wo die Daten liegen und wie sie sich
abbilden lassen.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Import- und Export-Werkzeuge](exponential-tools.md) ·
[Inhalte auf Exponential abbilden](inhaltsmodell-zuordnen.md)

## Inhalt

- [1. Was Basic Ihnen liefern kann](#1-was-basic-ihnen-liefern-kann)
- [2. Basic auf Exponential 6 abbilden](#2-basic-auf-exponential-6-abbilden)
- [3. Von Basic zu Exponential 6, Schritt für Schritt](#3-von-basic-zu-exponential-6-schritt-für-schritt)
- [4. Von Exponential 6 zu Basic](#4-von-exponential-6-zu-basic)
- [5. Prüfen, Rückweg, Risiken](#5-prüfen-rückweg-risiken)
- [6. Checkliste](#6-checkliste)

## 1. Was Basic Ihnen liefern kann

Ausgelesen aus dem Branch `main` von `se7enxweb/exponentialbasic`:

| Quelle | Was es ist | Verwendung |
|---|---|---|
| Die Datenbank | Tabellen pro Modul, definiert in `kernel/<module>/sql/<engine>/<module>.sql` (MySQL, PostgreSQL, SQLite, Informix) | die Hauptquelle: mit SQL auslesen |
| `bin/shell/php/ezpcsvimport.php` | eine CSV-Hilfsklasse (beliebiges Trennzeichen, Werte in Anführungszeichen, mehrzeilige Felder) | ein Hilfsmittel, wenn Sie den Export in Basic selbst schreiben |
| RSS der Artikel (`kernel/ezarticle/user/articlelistrss.php`) | eine RSS-Liste der Artikel | nur die neuesten Einträge; kein vollständiger Export |
| XML-RPC (`index_xmlrpc.php`, `kernel/<module>/xmlrpc/`) | Dienste für Artikel, Kategorien, Bilder und Dateien | Auslesen aus der Ferne; prüfen Sie, was jeder Aufruf zurückgibt, bevor Sie sich darauf verlassen |
| Die „Export“-Ansicht der Artikel (`kernel/ezarticle/admin/export.php`) | eine Ausgabe für Druck/Satz | kein Datenexport |
| Speicherverzeichnisse (`ezimagecatalogue/catalogue/`, die Dateien der Dateiverwaltung) | die Originale der Bilder und Dateien | mit dem Export kopieren |

Die Tabellen, auf die es für die meisten Sites ankommt:

| Tabelle | Enthält |
|---|---|
| `eZArticle_Article` | Artikel: `ID`, `Name`, `Contents` (das Artikel-Markup), `AuthorID`, `Created`, `Modified`, `Published`, `IsPublished`, `Keywords`, `TopicID`, `StartDate`, `StopDate`, `ImportID` |
| `eZArticle_Category`, `eZArticle_ArticleCategoryLink`, `eZArticle_ArticleCategoryDefinition` | den Kategoriebaum, die Kategorien eines Artikels und seine Hauptkategorie |
| `eZArticle_ArticleImageLink`, `eZArticle_ArticleFileLink`, `eZArticle_ArticleMediaLink` | Bilder, Dateien und Medien eines Artikels |
| `eZArticle_Attribute`, `eZArticle_AttributeValue`, `eZArticle_Type` | Artikeltypen mit zusätzlichen Attributen |
| `eZImageCatalogue_Image`, `eZImageCatalogue_Category`, `eZImageCatalogue_ImageCategoryLink` | die Bildbibliothek |
| `eZUser_User`, `eZUser_Group`, `eZUser_UserGroupLink`, `eZUser_Author` | Konten, Gruppen, Autoren |
| `eZURLTranslator_URL` | die eigenen URL-Übersetzungen der Site (alte Adressen) |
| `eZArticle_ArticlePermission`, `eZArticle_CategoryPermission` | Lese- und Schreibrechte pro Gruppe |

**Das Artikel-Markup.** `Contents` enthält ein XML-Dokument, das ein Generator geschrieben hat, zum Beispiel der
„tech“-Generator (`kernel/ezarticle/classes/eztechgenerator.php`): `<article><generator>tech</generator><intro>...</intro><body><page>...</page></body></article>`,
mit Inline-Tags wie `<bold>`, `<italic>`, `<underline>`, `<strike>` und Bildverweisen. Es gibt weitere Generatoren
(`ezsimplegenerator.php`, `ezezgenerator.php`, `ezqdomgenerator.php`); lesen Sie das Element `<generator>` jedes
Artikels aus und wandeln Sie pro Generator um.

## 2. Basic auf Exponential 6 abbilden

| Basic | Exponential 6 |
|---|---|
| Artikel (`eZArticle_Article`) | eine Klasse `article`: `title` (`ezstring`) aus `Name`, `intro` und `body` (`ezxmltext`) aus `<intro>` und `<body>` |
| Artikelseiten (`<page>`) | ein Textkörper mit Überschriften oder, bei langen Seiten, Unterobjekte pro Seite |
| Artikeltyp mit Attributen | eine Klasse pro Typ, ein Attribut pro `eZArticle_Attribute` |
| Kategoriebaum | Ordner im Inhaltsbaum; Hauptkategorie = Hauptort, weitere = zusätzliche Orte |
| Schlüsselwörter | `ezkeyword` oder `eztags` |
| Thema (Topic) | `eztags` oder eine Objektbeziehung zu Themenobjekten |
| `Published`, `Created`, `Modified` (Unix-Zeitstempel) | `published` und `modified` des Objekts |
| `StartDate`, `StopDate` | zeitgesteuertes Veröffentlichen und Verbergen per Workflow oder Cron, oder einfache `ezdatetime`-Attribute |
| Bild aus dem Bildkatalog | ein `image`-Objekt unterhalb eines Medienordners; Bildverknüpfungen der Artikel werden zu Beziehungen oder Einbettungen |
| Datei der Dateiverwaltung | ein `file`-Objekt |
| Forum, Umfrage, Kalender, Shop (trade) | jedes braucht eine eigene Entscheidung: Exponential hat kein Forum und keine Umfrage im Kernel; der Shop ist der des Kernels; archivieren Sie, was Sie nicht übernehmen |
| `eZUser_User` | ein `user`-Objekt in Benutzergruppen; `FirstName`, `LastName`, `Email`, `Login` |
| Gruppenrechte | Rollen mit Richtlinien, beschränkt auf Sektion oder Teilbaum |
| `eZURLTranslator_URL` und alte Artikeladressen | weiterleitende URL-Aliase oder Platzhalter ([Checkliste, 4](checkliste.md#4-urls-und-weiterleitungen)) |

**Passwörter.** `kernel/ezuser/classes/ezuser.php` von Basic speichert je nach Datenbank entweder das Format von
MySQLs `PASSWORD()` (`'*' . strtoupper( sha1( hex2bin( sha1( $password ) ) ) )`) oder `md5( $password )`.
Exponential 6 kennt `md5_password` (Typ 1, `md5( password )`) und `mysql` (Typ 4, berechnet mit der SQL-Funktion
`PASSWORD()`, die MySQL 8.0 entfernt hat). Daher gilt:

- MD5-Hashes aus Basic lassen sich als Typ 1 importieren; Exponential erzeugt bei der nächsten Anmeldung daraus einen
  Hash vom Typ 7.
- `PASSWORD()`-Hashes funktionieren als Typ 4 nur, solange die Exponential-Site auf einem Datenbankserver läuft, der
  `PASSWORD()` noch kennt (MariaDB ja, MySQL 8.0 nicht). Andernfalls planen Sie das Zurücksetzen der Passwörter ein.

Testen Sie beides an einer Kopie, bevor Sie den Benutzern versprechen, dass ihre Passwörter übernommen werden.

## 3. Von Basic zu Exponential 6, Schritt für Schritt

1. **Erfassen** Sie die verwendeten Module und zählen Sie die Zeilen pro Tabelle; listen Sie die verwendeten
   Artikelgeneratoren auf (`SELECT ... FROM eZArticle_Article` und das Element `<generator>` auslesen).
2. **Installieren Sie Exponential 6** und legen Sie die Klassen aus [2](#2-basic-auf-exponential-6-abbilden) an (halten
   Sie ihre Bezeichner stabil), außerdem die Ordner des Kategoriebaums, die Sprachen, Sektionen und Rollen.
3. **Export aus Basic**: ein Skript, das die Tabellen ausliest (SQL gegen eine Kopie der Basic-Datenbank) und pro
   Element eine JSON- oder CSV-Zeile schreibt, mit der ID der Quelle, der übergeordneten Kategorie, dem umgewandelten
   Textkörper und der alten Adresse.
4. **Wandeln Sie das Artikel-Markup** in die vereinfachte Eingabe von Exponential um (`<p>`, `<strong>`, `<em>`,
   `<a href>`, `<h1>`-`<h6>`, Listen, Tabellen): `<bold>` zu `<strong>`, `<italic>` zu `<em>`, Bildverweise zu
   Einbettungen der in Schritt 5 angelegten Bildobjekte.
5. **Importieren Sie zuerst die Medien** (Bildkatalog und Dateien), mit Remote-IDs wie `basic-image-<ID>`; über den
   ZIP-Import der Upload-Seite oder ein Importskript.
6. **Importieren Sie die Artikel** mit den Remote-IDs `basic-article-<ID>`: mit dem Importer von xrowextract und
   `--map`, wenn Ihr Export CSV/JSON in dessen Format ist, oder mit dem
   [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).
7. **Importieren Sie die Benutzer** in ihre Gruppen (Hash-Typen wie in [2](#2-basic-auf-exponential-6-abbilden)).
8. **Adressen**: für jede alte Adresse (aus `eZURLTranslator_URL` und den Modul-URLs wie
   `/article/view/<ID>/` und `/article/articleview/<ID>/`, `kernel/ezarticle/user/datasupplier.php`) ein
   weiterleitender eigener URL-Alias am neuen Knoten oder ein Platzhalter.
9. **Bauen Sie** den Suchindex und die URL-Aliase **neu auf**; führen Sie den Link-Checker und `exp:contentcheck` aus.
10. **Proben Sie** zweimal und stellen Sie dann um ([Checkliste](checkliste.md#8-umstellung)).

## 4. Von Exponential 6 zu Basic

Das Modell von Basic ist enger (Artikel, Bildkatalog, Dateien, Benutzer). Exportieren Sie aus Exponential mit
xrowextract ([Wechsel weg von Exponential](exponential-verlassen.md)): eine CSV/JSON pro Klasse, mit Objekt-ID,
Hauptknoten, übergeordnetem Knoten, URL-Alias und allen Attributen; wandeln Sie XmlText in einem Skript in das
Artikel-Markup von Basic um und fügen Sie die Daten über die eigenen Klassen von Basic in die Basic-Tabellen ein
(oder per SQL in eine Kopie, danach in der Administration von Basic geprüft). Klassen ohne Basic-Modul bleiben zurück
oder werden zu Artikeln. Passwort-Hashes vom Typ 7 (`password_hash()`) liest der Anmeldecode von Basic, wie oben
gezeigt, nicht: Planen Sie das Zurücksetzen ein.

## 5. Prüfen, Rückweg, Risiken

- Zahlen pro Modul und Kategorie vorher, pro Klasse und Ordner nachher; eine Stichprobe der 50 meistgelesenen Artikel
  im direkten Vergleich; alle Bilder dieser Artikel sichtbar; jede alte Adresse antwortet mit 301 auf die richtige Seite.
- Rückweg: Basic läuft bis zur Abnahme schreibgeschützt weiter; das Zurückschalten ist eine Änderung an DNS oder Proxy.

| Risiko | Gegenmaßnahme |
|---|---|
| Unbekannte Artikelgeneratoren | zuerst auflisten; pro Generator umwandeln; jeden testen |
| Im Markup referenzierte Bilder | zuerst die Medien importieren, IDs auf die neuen Objekte abbilden |
| Foren, Umfragen, Shop-Daten | pro Modul entscheiden: übernehmen, ersetzen oder archivieren |
| Passwörter | beide Hash-Formate testen; Zurücksetzen einplanen |
| Alte Adressen | die Weiterleitungstabelle aus dem Export erzeugen und automatisch testen |

## 6. Checkliste

- [ ] Module, Tabellen und Artikelgeneratoren erfasst.
- [ ] Klassen, Baum, Sprachen, Sektionen und Rollen in Exponential 6 angelegt.
- [ ] Exportskript liest eine Kopie der Basic-Datenbank; Markup umgewandelt und geprüft.
- [ ] Zuerst Medien, dann Artikel, dann Benutzer importiert, mit Remote-IDs; Beziehungen aufgelöst.
- [ ] Passwort-Strategie getestet; Benutzer informiert.
- [ ] Weiterleitungen für jede alte Adresse; Suche und Aliase neu aufgebaut.
- [ ] Zwei Proben, dann Umstellung; Basic bis zur Abnahme schreibgeschützt erhalten.

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
