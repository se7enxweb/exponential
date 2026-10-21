---
title: Von Exponential wegmigrieren
audience: admin, developer, project-manager
category: install-upgrade
order: 60
summary: Alles, was Exponential 6 exportieren kann, wenn Sie eine Site auf ein anderes System umziehen – xrowextract-Site-Archive mit Manifesten, CSV/XML/JSON pro Klasse, Pakete, Layouts, Datenbank-Dumps, Dateien, Benutzer, URL-Aliase für Weiterleitungen, Formulardaten und Einwilligungsnachweise – und ein Plan für die Übergabe der Inhalte.
keywords: Export, verlassen, Ausstieg, Datenportabilität, xrowextract, Site-Archiv, Manifest, CSV, JSON, XML, ezpkg, ezsqldumpschema, Speicher, URL-Aliase, Weiterleitungen, Benutzer, bccie, Einwilligungsprotokoll, export, leave, exit, data portability, site archive, storage, URL aliases, redirects, users, consent log
type: install
---
# Von Exponential wegmigrieren

Ihre Inhalte gehören Ihnen. Exponential speichert sie in einem offenen Datenbankschema und in gewöhnlichen Dateien und hat Werkzeuge,
die sie in einfachen Formaten ausgeben. Diese Seite listet sie auf und gibt einen Plan vor; den Rest bestimmt der Import des Zielsystems.
Anleitungen für bestimmte Ziele: [WordPress](wordpress.md#5-von-exponential-zu-wordpress),
[Drupal](drupal.md#5-von-exponential-zu-drupal), [Joomla](joomla.md#5-von-exponential-zu-joomla),
[Shopify](shopify.md#4-von-exponential-zu-shopify), [Contentful](contentful.md#4-von-exponential-zu-contentful),
[Website-Baukästen](website-baukaesten.md#5-von-exponential-zu-einem-baukasten) und innerhalb der Produktfamilie
[Platform Legacy](exponential-platform-legacy.md), [Nexus](exponential-platform-nexus.md),
[DXP](exponential-platform-dxp.md).

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Werkzeuge](exponential-tools.md#2-export-aus-exponential)

## Inhalt

- [1. Was Sie exportieren sollten](#1-was-sie-exportieren-sollten)
- [2. Der vollständige Export, Schritt für Schritt](#2-der-vollständige-export-schritt-für-schritt)
- [3. Rich Text](#3-rich-text)
- [4. Adressen für die Weiterleitungen](#4-adressen-für-die-weiterleitungen)
- [5. Benutzer und personenbezogene Daten](#5-benutzer-und-personenbezogene-daten)
- [6. Checkliste](#6-checkliste)

## 1. Was Sie exportieren sollten

| Daten | Werkzeug | Format |
|---|---|---|
| Alle Inhalte einer Site, selbstbeschreibend | xrowextract-Site-Archiv: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>` | pro Klasse ein CSV (Objekt-ID, Remote-ID, Hauptknoten, übergeordneter Knoten, URL-Alias, Datumsangaben, jedes Attribut), `manifest.json`, `README.txt`; ZIP oder TAR.GZ/BZ2/XZ. Passwort-Hashes werden nie exportiert |
| Eine Klasse, gefiltert | `./console ext:xrowextract:csv --class=<identifier> --node=<id> --output=<file>` (auch XML und JSON) | mit einem typisierten Manifest (Datentyp, Format, Sprache pro Spalte) |
| Klassen und Inhalte für ein anderes Exponential oder Exponential Platform Legacy | Pakete (`php ezpm.php export`, `ext:xrowextract:package --export`) | `.ezpkg` |
| Layouts | `./console exp:layouts export --all --output=<file.zip>` | ZIP-Transferdatei |
| Bilder, Dateien, Medien | das Speicherverzeichnis `var/<site>/storage/` | Originale; die Pfade stehen im Export |
| Die gesamte Datenbank | `./console exp:ezsqldumpschema --output-types=all ...` oder das eigene Dump-Werkzeug der Datenbank | neutrales Format oder SQL |
| Formulareinsendungen (Informationssammlung) | `./console ext:bccie:export --object=<id> --format=csv` | CSV oder SYLK |
| Nachweise über E-Mail-Einwilligungen | `./console exp:mailconsent` (Export als CSV) | CSV |
| Audit-Protokoll | `./console exp:audit` | siehe [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md) |
| Einzelne Tabellen, durch einen Administrator | `sevenx_dse` | SQL, CSV, TSV |
| Inhalte über HTTP | REST v1/v2 | JSON |

## 2. Der vollständige Export, Schritt für Schritt

1. **Frieren Sie** die Bearbeitung ein (oder notieren Sie die Uhrzeit für einen späteren Delta-Export).
2. **Site-Archiv** mit xrowextract, alle Klassen, in dem Format, das das Ziel am besten liest (CSV für Tabellenkalkulationen und die meisten
   Importer, JSON für Skripte).
3. **Speicher**: Kopieren Sie `var/<site>/storage/` (bei einem Datenbank-Cluster vorher die Dateien des Clusters mit `exp:clusterize -u`).
4. **Datenbank-Dump**, als Archiv, auf das Sie zurückgreifen können.
5. **Layouts**, wenn das Ziel sie nutzen kann (ein anderes Exponential oder ein Skript).
6. **Adressliste** ([4](#4-adressen-für-die-weiterleitungen)).
7. **Prüfen Sie** das Archiv: Öffnen Sie das Manifest, gleichen Sie die Zeilen pro Klasse mit den Anzahlen in der Administration ab, öffnen Sie eine Stichprobe der Dateien.

## 3. Rich Text

`ezxmltext`-Attribute werden im XML-Format von Exponential gespeichert. Das Ziel braucht HTML (oder Markdown). Zwei Wege:

- **Rendern**: Der REST-v2-Aufruf mit `OutputFormat=xhtml` oder die eigenen Templates der Site liefern das HTML, das die Besucher gesehen haben.
- **Transformieren**: Wandeln Sie das XML (Absatz, Überschrift, Link, Einbettung, Tabelle, Custom Tags) in einem Skript in HTML um; Einbettungen
  werden zu Bild-Tags oder Links auf die exportierten Dateien.

## 4. Adressen für die Weiterleitungen

Der Haupt-URL-Alias jedes Knotens steht im Site-Archiv (eine Spalte pro Objekt). Andere Aliase (eigene Aliase, andere
Sprachen) und die Platzhalter stehen in der Datenbank (`ezurlalias_ml`, `ezurlwildcard`). Erstellen Sie daraus die Weiterleitungstabelle:
alte Adresse auf die Adresse desselben Eintrags im neuen System, zugeordnet über die Remote-ID oder die Objekt-ID. Richten Sie sie im
neuen System ein, bevor das DNS umgestellt wird.

## 5. Benutzer und personenbezogene Daten

- Das Site-Archiv enthält nie Passwort-Hashes. Die Benutzer vergeben im Zielsystem neue Passwörter (versenden Sie Mails zum Zurücksetzen), es sei denn,
  das Ziel kann Standardwerte von PHPs `password_hash()` prüfen: Dann lässt sich ein Datenbankexport der `ezuser`-Hashes vom Typ 7
  verwenden. Entscheiden Sie das mit Ihrer Datenschutzberatung.
- Exportieren Sie nur die personenbezogenen Daten, die das Ziel braucht; bewahren Sie den Rest in einem zugriffsgeschützten Archiv auf oder löschen Sie ihn,
  wie es Ihre Aufbewahrungsregeln vorsehen.

## 6. Checkliste

- [ ] Einfrieren oder Delta-Plan; Site-Archiv mit allen Klassen; Manifeste geprüft.
- [ ] Speicher kopiert; Datenbank-Dump archiviert; Layouts exportiert, falls nützlich.
- [ ] Rich Text gerendert oder transformiert; Einbettungen zu Dateien aufgelöst.
- [ ] Weiterleitungstabelle aus URL-Aliasen und Platzhaltern; im Zielsystem eingerichtet.
- [ ] Benutzer und personenbezogene Daten nach den vereinbarten Regeln behandelt.
- [ ] Alte Site schreibgeschützt behalten, bis die neue abgenommen ist.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../eng-us/README-LICENSE.md)).
