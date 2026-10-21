---
title: Formate für den Datenexport beim Ausstieg oder Lieferantenwechsel
audience: government, project-manager, admin, developer
category: public-sector
order: 40
summary: Jeder Weg, Daten aus einer Exponential-Website zu holen, und das Format, das er schreibt - das Website-Archiv von xrowextract, CSV/XML/JSON je Klasse, Pakete (.ezpkg), Datenbank-Dumps, Speicherdateien, Layouts, Formulareingaben, Einwilligungsnachweise, das Audit-Protokoll und REST - mit den Befehlen und dem Empfänger, zu dem jedes Format passt.
keywords: Datenexport, Exportformate, Website-Archiv, xrowextract, CSV, JSON, XML, ezpkg, Pakete, Datenbank-Dump, ezsqldumpschema, Speicher, Export von Layouts, bccie, Einwilligungsprotokoll, Audit-Export, REST, Portabilität, data export, export formats, site archive, database dump, storage, layouts export, consent log, audit export, portability
type: help
---
# Formate für den Datenexport beim Ausstieg oder Lieferantenwechsel

Diese Seite richtet sich an Projektleitungen, die die Exportformate in einem Vertrag oder Ausstiegsplan benennen müssen,
und an die Administratoren, die sie erzeugen. Jedes Werkzeug und jedes Format unten ist in
[Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md) und
[Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md#2-export-aus-exponential) dokumentiert.
Die Befehle werden im Wurzelverzeichnis der Installation ausgeführt.

## Inhalt
1. [Welcher Export für welchen Empfänger](#welcher-export-für-welchen-empfänger)
2. [Das Website-Archiv](#das-website-archiv)
3. [Einzelne Klassen](#einzelne-klassen)
4. [Pakete](#pakete)
5. [Die Datenbank](#die-datenbank)
6. [Dateien](#dateien)
7. [Layouts, Formulare, Einwilligung und Audit](#layouts-formulare-einwilligung-und-audit)
8. [Inhalte über HTTP](#inhalte-über-http)
9. [Was im Vertrag zu benennen ist](#was-im-vertrag-zu-benennen-ist)

## Welcher Export für welchen Empfänger

| Empfänger | Braucht | Exporte |
|---|---|---|
| Neuer Hoster oder Lieferant, dieselbe Software | die ganze Installation | Datenbank-Dump, Dateien, Einstellungen, Code ([Wechsel zu einem anderen Anbieter](wechsel-zu-einem-anderen-anbieter.md)) |
| Ein anderes Exponential oder Exponential Platform Legacy | Klassen und Inhalte | Pakete, dazu das oben Genannte |
| Ein anderes CMS | Inhalte, Dateien, Adressen | Website-Archiv, Dateien, Adressliste ([Wechsel zu einem anderen System](wechsel-zu-einem-anderen-system.md)) |
| Ihr Archiv oder Ihre Schriftgutverwaltung | eine dauerhafte Kopie | Website-Archiv, Datenbank-Dump, Dateien, Audit-Bündel |
| Datenschutzbeauftragte | Einwilligungs- und Zugriffsnachweise | CSV der Einwilligungen, Audit-Export je Benutzer |

## Das Website-Archiv

```bash
./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>
```

- Je Klasse eine CSV-Datei mit Objekt-ID, Remote-ID, Hauptknoten, Elternknoten, URL-Alias, Daten und jedem Attribut.
- Eine `manifest.json`, die das Archiv beschreibt, und eine `README.txt`.
- ZIP, TAR.GZ, TAR.BZ2 oder TAR.XZ.
- **Passwort-Hashes sind nie enthalten.**

Das ist der portabelste Export: einfaches CSV, das Tabellenkalkulationen, Skripte und die meisten Importprogramme lesen.

## Einzelne Klassen

```bash
./console ext:xrowextract:csv --class=<identifier> --node=<id> --output=<file>
```

Schreibt auch XML und JSON, mit einem typisierten Manifest (Datentyp, Format, Sprache je Spalte). Optionen wie
`--list-classes`, `--list-columns` und `--preview=10` helfen, vor dem Export hineinzusehen. `./console exp:ezcsvexport`
schreibt für einen Teilbaum eine CSV-Datei je Klasse, mit `--storage-dir` für Dateien.

## Pakete

`php ezpm.php export` oder `./console ext:xrowextract:package --export` schreiben `.ezpkg`-Pakete mit Klassen und
Inhalten, für eine andere Exponential-Installation. Was ein Paket enthält, steht unter [Pakete](../../../../anleitungen/pakete.md).

## Die Datenbank

- `./console exp:ezsqldumpschema --output-types=all ...` schreibt Schema und Daten im neutralen Format von Exponential
  oder als SQL, für jede der unterstützten Engines.
- Oder das eigene Dump-Werkzeug der Datenbank ([Datenbanksicherungen je Engine](../../../hosting/betrieb/datenbank-backups-je-engine.md)).
- `sevenx_dse`, der Datenbankeditor der Administration, exportiert einzelne Tabellen als SQL, CSV oder TSV.

Ein Datenbank-Dump ist die vollständigste Kopie, aber nur für jemanden nützlich, der Exponential betreibt oder sein
Schema liest. Rich Text in Feldern vom Typ `ezxmltext` wird im XML von Exponential gespeichert: Rendern oder
transformieren Sie ihn für ein anderes System
([Exponential verlassen, Abschnitt 3](../../../../installation/migration/exponential-verlassen.md#3-rich-text)).

## Dateien

Bilder, Dokumente und Medien liegen in `var/storage/` und `var/<site>/storage/`: die Originale. Kopieren Sie sie bei
jedem Export, der auf Dateien verweist. In einem Datenbank-Cluster führen Sie zuerst `exp:clusterize -u` aus, damit die
Dateien auf der Festplatte liegen.

## Layouts, Formulare, Einwilligung und Audit

| Daten | Befehl | Format |
|---|---|---|
| Layouts (Exponential Layouts) | `./console exp:layouts export --all --output=<file.zip>` | ZIP-Übertragungsdatei |
| Formulareingaben | `./console ext:bccie:export --object=<id> --format=csv` | CSV oder SYLK |
| Nachweise der E-Mail-Einwilligung | `./console exp:mailconsent` | CSV |
| Audit-Protokoll | `./console exp:audit export --format=jsonl\|csv\|bundle --out=<path>` | JSON Lines, CSV oder ein Bündel mit `records.jsonl` und einer signierten `manifest.json` ([Audit-Leitfaden](../../../../bc/6.0/audit.md)) |

## Inhalte über HTTP

Die REST API v1 und v2 liefern Knoten, Unterelemente und Felder als JSON, sodass ein anderes System Inhalte über HTTP
abrufen kann ([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)). Version 2 kann Rich Text als XHTML
liefern.

## Was im Vertrag zu benennen ist

```text
On request, and at the end of the contract, the Supplier delivers within <n> working days:
- a site archive of all classes (xrowextract, <ZIP|TAR.XZ>) with manifest;
- a full database dump in <engine dump format | Exponential neutral format>;
- the storage directories of every site;
- layouts, form submissions, consent records and the audit trail export with signed manifest;
- settings and keys, transferred privately;
- the source of all custom code in Git with history.
```

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Vorlage für einen Ausstiegsplan](vorlage-fuer-einen-ausstiegsplan.md)
- [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
