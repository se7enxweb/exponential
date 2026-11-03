# Datentypen: welche die Inhalte nutzen und welche sie beschädigen

Diese Anleitung erklärt die Datentyp-Seiten der Administrationsoberfläche (Setup > Datentypen,
`/class/datatype/list`, und ein Datentyp, `/class/datatype/view/<Bezeichner>`): was ein Datentyp ist, woher jeder
kommt, wie viel der Inhalte ihn nutzt und wie Sie Inhalte finden und reparieren, deren Datentyp nichts mehr
registriert.

Sie richtet sich an Administratoren und Entwickler, die das Inhaltsmodell pflegen. Die Seite liest nur: Sie ändert
nichts. Jede Angabe unten wurde am 3. November 2026 auf dem Demonstrationsserver am Code geprüft
(`kernel/private/classes/views/class/datatype.php` und `kernel/classes/expdatatypeinventory.php`).

[Anleitungen](README.md) · Siehe auch: [Klassengruppen](klassengruppen.md),
[Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md),
[Alle Datentypen](../beispiele/datentypen/README.md)

## Kurz gesagt

- Ein **Datentyp** speichert und bearbeitet eine Art von Wert eines Klassenattributs: eine Textzeile (`ezstring`),
  ein Bild (`ezimage`), eine Relation (`ezobjectrelationlist`). Die meisten bringt der Kernel mit, Extensions fügen
  weitere hinzu (`eztags`, `ezgmaplocation` ...).
- Die Liste zeigt jeden Datentyp, den `content.ini [DataTypeSettings] AvailableDataTypes[]` registriert, gruppiert
  nach Anbieter: erst der Kernel, dann jede Extension. Jede Zeile nennt PHP-Klasse und Datei, wie viele Klassen,
  Klassenattribute, Objekte und Objektattribute ihn nutzen, und seine Merkmale.
- Ein Datentyp, den die Datenbank nutzt, den aber nichts mehr registriert, ist **Nicht registriert**, rot, in einer
  eigenen Gruppe. Die Inhalte damit lassen sich nicht anzeigen, bearbeiten oder veröffentlichen, bis der Datentyp
  zurück ist oder die Klasse geändert wurde.
- Die Seite eines Datentyps zeigt seine Einstellungsspalten, seine Templates in dieser Administration und in der
  Standard-Site, die Klassen und Attribute, die ihn nutzen, mit ihren Objekten, seine Inhalte, seine Tabellen
  und seine Hilfeseite.

Sie öffnen sie unter Setup > Datentypen (Gruppe "Inhaltsmodell", neben Klassen) oder mit **Datentypen** auf der Seite
der Klassengruppen (`/class/grouplist`). Sie braucht dieselben Rechte wie die Klassenseiten (`class/*`).

## 1. Die Übersicht

| Zahl | Was sie zählt |
|---|---|
| Registriert | Datentypen aus `AvailableDataTypes[]`, deren Datei gefunden und deren Klasse erzeugt wurde |
| In Gebrauch | Registrierte Datentypen mit mindestens einem Klassen- oder Objektattribut |
| Nicht verwendet | Registrierte Datentypen, die keine Klasse und kein Objekt nutzt |
| Extensions mit Datentypen | Extensions mit mindestens einem registrierten Datentyp |
| Klassenattribute | Attribute der definierten Klassen (Version 0), aller Datentypen |
| Objektattribute | Attribute der aktuellen Version jedes veröffentlichten Objekts, eines je Sprache |
| Nicht registriert | Datentypen in `ezcontentclass_attribute` oder `ezcontentobject_attribute`, die nichts registriert (nur wenn es welche gibt) |
| Eingetragen, Datei fehlt | Datentypen aus `AvailableDataTypes[]`, die kein Datentyp-Verzeichnis enthält (nur wenn es welche gibt) |

Die Zahlen sind zwei gruppierte Abfragen, eine über `ezcontentclass_attribute`, eine über `ezcontentobject_attribute`
verbunden mit `ezcontentobject`. Auf einer großen Datenbank dauert die zweite einige Sekunden (2,5 s auf der 3 GB
großen SQLite-Datenbank des Demonstrationsservers mit 200.000 Objektattributen), daher werden beide **fünf Minuten**
in einer Datei des Cache-Verzeichnisses gehalten. Der Kopf sagt, wann gezählt wurde; **Neu zählen** zählt sofort
(`/(refresh)/1`).

## 2. Filter und Suche

- **Zeigen**: Alle, In Gebrauch, Nicht verwendet, Nicht registriert. Es sind Ansichtsparameter:
  `/class/datatype/list/(filter)/unused`, `/(filter)/orphans`, `/(filter)/used`. "Nicht verwendet" listet auch die
  Datentypen, deren Datei fehlt.
- **Bereitgestellt von**: Alle Anbieter, Kern, jede Extension, Nicht registriert: `/(group)/kernel`, `/(group)/eztags`,
  `/(group)/orphans`. Beides lässt sich kombinieren: `/class/datatype/list/(filter)/unused/(group)/kernel`.
- **Datentyp suchen** (mit JavaScript): sucht in Bezeichner, Name, PHP-Klasse, Extension und Datei der
  gezeigten Zeilen.
- **Sortieren** (mit JavaScript): ein Klick auf eine Spaltenüberschrift sortiert ihre Tabelle; Zahlen beginnen mit
  der größten, ein zweiter Klick kehrt die Reihenfolge um.

Ohne JavaScript funktionieren die Filter weiter (es sind Links), und jede Zeile steht in der Reihenfolge der
Bezeichner.

## 3. Eine Zeile

| Spalte | Was sie sagt |
|---|---|
| Datentyp | Der Bezeichner (ein Link auf seine Seite), sein Name in der Sprache der Oberfläche und sein Zustand: Nicht registriert, Eingetragen, Datei fehlt, Nicht verwendet |
| PHP-Klasse und Datei | Die Klasse, die er mit `eZDataType::register()` registriert hat, und die Datei, die sie definiert |
| Klassen, Klassenattribute | Definierte Klassen und ihre Attribute dieses Datentyps |
| Objekte, Objektattribute | Veröffentlichte Objekte (aktuelle Version) mit einem solchen Attribut und diese Attribute (jede Sprache zählt) |
| Merkmale | Was der Datentyp über sich selbst sagt |

Die Merkmale:

| Merkmal | Aus | Bedeutung |
|---|---|---|
| Übersetzbar | `isTranslatable()` | Jede Übersetzung hat ihren eigenen Wert |
| Durchsuchbar | `isIndexable()` | Die Suche indiziert seine Werte |
| Sammelt Informationen | `isInformationCollector()` | Er kann Informationen von Besuchern sammeln (Formulare) |
| Sortierbar | `sortKeyType()` | Listen lassen sich nach ihm sortieren (der Tooltip nennt den Typ des Sortierschlüssels) |
| Verknüpfungen | `isRelationType()` | Seine Werte verweisen auf andere Objekte |
| Aus Text | `isSimpleStringInsertionSupported()` | Er lässt sich aus einer einfachen Zeichenkette füllen (Import, REST) |
| Aus Datei | `isHTTPFileInsertionSupported()`, `isRegularFileInsertionSupported()` | Er lässt sich aus einer Datei füllen |
| Pakete | `serialize_supported` | Die Einstellungen seiner Klassenattribute gehen in Pakete |

## 4. Ein Datentyp

`/class/datatype/view/<Bezeichner>`, zum Beispiel `/class/datatype/view/ezimage`:

- **Über diesen Datentyp**: Bezeichner, Name, Anbieter (der Kernel oder die Extension), PHP-Klasse, Datei, ob
  `AvailableDataTypes[]` ihn listet, die Merkmale, die Spalten seines Werts (`data_text`, `data_int`, `sort_key_*`
  ...), die Spalten der Einstellungen seiner Klassenattribute (`data_int1` ... `data_text5`) und seine Tabellen
  (`ezimagefile` für `ezimage`). Spalten und Tabellen werden durch Lesen des Codes gefunden, mit derselben Heuristik
  wie die erzeugten [Datentyp-Seiten](../beispiele/datentypen/README.md): ein Hinweis, kein Versprechen.
- **Templates**: `content/datatype/view`, `edit`, `collect`, `result/info` und `class/datatype/view`, `edit` des
  Datentyps, jeweils mit der Datei, die die Designkette zuerst findet, für diese Administration und für den
  Standard-Siteaccess (`site.ini [SiteSettings] DefaultAccess`). Override-Templates, die nach Klasse oder Attribut
  greifen, sind hier nicht berücksichtigt; sie gehen diesen vor.
- **Klassen, die ihn verwenden**: jede Klasse (ein Link auf class/view) mit ihren Attributen dieses Datentyps, ihren
  Objekten und Objektattributen.
- **Klassenattribute**: jedes Attribut mit seiner Klasse, seinen Merkmalen (Erforderlich, Durchsuchbar, Übersetzbar,
  Sammelt Informationen) und den gesetzten Einstellungsspalten (`data_int1=600` ...), mit Objekten und
  Objektattributen.
- **Inhalte**: jedes Objekt dieser Klassen, das Sie lesen dürfen, einmal (sein Hauptknoten), mit Klasse, Sektion,
  Sprachen, Eigentümer und Daten. Sortieren Sie mit den Spaltenköpfen nach Änderung (Standard, neueste zuerst),
  Veröffentlichung, Name oder Klasse (ein zweiter Klick kehrt die Reihenfolge um), zeigen Sie 10, 25, 50 oder 100
  pro Seite und beschränken Sie die Liste auf eine Klasse. Die Auswahl steht in der Adresse, zum Beispiel
  `/class/datatype/view/ezimage/(sort)/name/(order)/asc/(limit)/50/(class)/5/(offset)/50`; die Anzahl gilt fünf
  Minuten.
- **Hilfeseite**: die erzeugte Seite des Datentyps im Hilfezentrum, wo es eine gibt.

## 5. Nicht registriert: Inhalte reparieren, deren Datentyp fehlt

Ein Klassenattribut behält den Bezeichner seines Datentyps (`data_type_string`) in der Datenbank. Wird eine
Extension, die einen Datentyp angeboten hat, deaktiviert oder entfernt, oder listet ihre `content.ini.append.php` ihn
nicht mehr, bleibt der Bezeichner, und der Kernel kann den Datentyp nicht erzeugen: Die Objekte der Klasse lassen
sich nicht anzeigen, bearbeiten oder veröffentlichen.

1. Öffnen Sie `/class/datatype/list/(filter)/orphans`. Der Hinweis nennt, wie viele Klassen- und Objektattribute
   einen solchen Datentyp haben.
2. Öffnen Sie den Datentyp. **Klassen, die ihn verwenden** nennt die Klassen; **PHP-Klasse** und **Datei** sind leer.
3. Aktivieren Sie entweder die Extension, die ihn anbietet (Setup > Extensions), und prüfen Sie, dass ihre
   `settings/content.ini.append.php` ihn in `[DataTypeSettings] AvailableDataTypes[]` und ihr Verzeichnis in
   `ExtensionDirectories[]` einträgt, dann leeren Sie den INI-Cache; oder bearbeiten Sie jede Klasse und entfernen
   das Attribut (seine Werte gehen mit) oder verschieben die Inhalte in eine Klasse ohne es.
4. **Neu zählen**: Die Zeile ist aus "Nicht registriert" verschwunden.

"Eingetragen, Datei fehlt" ist der umgekehrte Fall: Eine Einstellung listet einen Datentyp, dessen Datei kein
Datentyp-Verzeichnis enthält. Nichts nutzt ihn; entfernen Sie ihn aus `AvailableDataTypes[]` oder aktivieren Sie
seine Extension.

## 6. Datenbanken

Die Zählung läuft auf jeder Datenbank, die der Kernel unterstützt. Auf SQLite, MySQL, PostgreSQL und Oracle sind es
dieselben zwei Anweisungen mit `GROUP BY data_type_string` über `eZDB::arrayQuery()` (die Spaltennamen werden in
jeder Schreibweise gelesen, da Oracle sie groß zurückgibt). Auf MongoDB sind es Aggregations-Pipelines (`$group` mit
`$addToSet`; die Objektattribute mit einem `$lookup` auf `ezcontentobject`), da die SQL-Übersetzung des Treibers nur
SELECTs auf eine Tabelle ohne `GROUP BY` beantwortet.

## Referenzen

- Modul und Ansicht: `kernel/class/module.php` (Ansicht `datatype`, Parameter `Action`, `DataTypeString`),
  `kernel/class/datatype.php`, `kernel/private/classes/views/class/datatype.php`.
- Daten: `kernel/classes/expdatatypeinventory.php` (`registry()`, `usage()`, `build()`, `filter()`,
  `classAttributes()`, `templates()`, `readSource()`); Cache-Datei `class-datatype-*.json` im Cache-Verzeichnis, fünf
  Minuten.
- Templates: `design/admin4/templates/class/datatype_list.tpl`, `datatype_view.tpl`, `datatype_flags.tpl` (dieselben
  Dateien in `design/admin/templates/class/`); Skript `javascript/page/class_datatypes.js`; die Sortier-Schaltflächen
  in `stylesheets/modules/structure.css` (`.a4-class-datatypes-sort`).
- Menü: `settings/menu.ini` `Links[datatypes]`, `GroupLinks_content_model[]`; Kontexthilfe
  `settings/exphelp.ini [ContextHelp] Map[class/datatype]`.
- Tests: `tests/tests/kernel/classes/expDataTypeInventoryTest.php` (ohne Datenbank),
  `tests/tests/kernel/classes/expDataTypeInventorySqliteTest.php` (SQLite im Speicher).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
