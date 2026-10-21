# eztags: Tags, eine Taxonomie für Inhalte

Diese Seite ist für Website-Entwickler, die Inhalte klassifizieren, und für Redakteure, die sie verschlagworten. `eztags`
(in der Administrationsoberfläche seit 2.3.5 als **Tags** angezeigt) fügt einen Tag-Datentyp hinzu, einen Tag-Baum, den
Sie in der Administrationsoberfläche verwalten, und eine Tag-Ansicht mit eigenen Seiten. Es kann mehr als der Datentyp
`ezkeyword` des Kernels:

- eine **Baumhierarchie** von Tags, mit Synonymen;
- Tags in der Administrationsoberfläche hinzufügen, bearbeiten, verschieben, zusammenführen und löschen (oberes Menü
  **Tags**, `/tags/dashboard`);
- Verschlagworten im Bearbeitungsformular des Objekts mit **Autovervollständigung**, Vorschlägen und dem Hinzufügen neuer
  Tags an Ort und Stelle;
- eine Tag-Ansicht (`/tags/view/...`) wie die Inhaltsansicht, mit dem aktuellen Tag in `$tag`;
- einen **erweiterten Attributfilter** für Fetches `content/list` und `content/tree`;
- geschlossene (vordefinierte) und offene (Benutzer-Tags) Klassifikation oder eine Kombination aus beiden, später
  umschaltbar.

## Wann Sie es verwenden

- Um `ezkeyword` zu ersetzen.
- Um eine geschlossene Klassifikation zu ersetzen, die auf `ezselection` oder `ezobjectrelation(list)` aufgebaut ist:
  bessere Eingabe, einfachere Pflege.
- Um dynamische Seiten aus verschlagworteten Inhalten zu erstellen.

Der eigene Ordner `doc/` der Extension hat eine Installationsanleitung, eine Benutzungsanleitung, Hinweise zur
Aktualisierung, ein Benutzerhandbuch und den Changelog. Diese Seite behandelt, was die Releases von Exponential 6 (2.3.1
bis 2.4.11) geändert haben.

## Verwenden

1. Öffnen Sie **Tags** in der Administrationsoberfläche (`/tags/dashboard`) und bauen Sie den Tag-Baum auf.
2. Fügen Sie unter **Setup > Klassen** einer Klasse ein Tags-Attribut hinzu und wählen Sie seine Bearbeitungsansicht
   (Standard, Auswählen oder Baum).
3. Bearbeiten Sie ein Objekt und tippen Sie in das Tags-Feld: Die Autovervollständigung schlägt vorhandene Tags vor.
4. Öffnen Sie `/tags/view/<tag>`, um alles zu sehen, was damit verschlagwortet ist.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `eztags.ini` | `GeneralSettings` | `URLPrefix` | `tags/view` | URL-Präfix einer Tag-Seite |
| `eztags.ini` | `GeneralSettings` | `ShowOldStyleChildrenList` | `disabled` | Auf dem Server ausgegebene Liste der Kinder statt der Tabelle |
| `eztags.ini` | `GeneralSettings` | `DefaultAlwaysAvailable` | `false` | Neue Tags sind in jeder Sprache verfügbar |
| `eztags.ini` | `GeneralSettings` | `TagCloudOverSolr` | `enabled` | Die Tag-Wolke aus Solr bauen, wenn verfügbar |
| `eztags.ini` | `GeneralSettings` | `MaxResults` | `24` | Anzahl der Ergebnisse der Autovervollständigung |
| `eztags.ini` | `GeneralSettings` | `AutoCompleteType` | `start` | Auf den Anfang oder einen beliebigen Teil passen |
| `eztags.ini` | `TreeMenu` | `ToolTips`, `MaxTags`, `AutoopenCurrentTag`, `MenuPersistence` | `enabled`, `100`, `enabled`, `enabled` | Tag-Baum in der Administrationsoberfläche |
| `eztags.ini` | `SearchSettings` | `SearchLimit`, `IndexSynonyms`, `IncludeSynonyms`, `IndexParentTags` | `15`, `enabled`, `disabled`, `disabled` | Indizierung und Suche |
| `eztags.ini` | `EditSettings` | `AvailableViews[]` | Default, Select, Tree | Bearbeitungsansichten des Tag-Felds |

Richtlinien, Modul `tags`: `read`, `dashboard`, `id`, `view`, `add` (nach Tag eingeschränkt), `addsynonym`, `edit`,
`editsynonym`, `delete`, `deletesynonym` und weitere.

## Was sich in den Releases von Exponential 6 geändert hat

### Funktioniert auf jeder Datenbank

- **SQLite** (2.3.2, 2.3.3): Die Funktion `MOD()` und ein `ORDER BY` nach einem `UNION` waren nicht portabel; jetzt sind
  sie `%` und Positionsverweise. Ein SQLite-Schema (Entsprechung von `share/db_schema.dba` mit `eztags_keyword` und
  Absicherungen `IF NOT EXISTS`) lässt den Export des Tags-Pakets auf dem SQLite-Backend funktionieren.
- **MongoDB** (2.3.4): Tags wurden als nichts ausgegeben, weil jeder Lesezugriff auf einen Tag `eztags` mit
  `eztags_keyword` verknüpft und MongoDB keinen Join hat; der Treiber antwortete mit einem leeren Ergebnis. Ein Zweig für
  MongoDB liest die beiden Collections getrennt und ordnet sie zu, unter Beibehaltung der Übersetzungsregeln
  (Hauptsprache, wenn gefragt, sonst eine ausdrückliche Locale, sonst die Sprachpriorität der Website mit der
  Hauptsprache des Tags als letztem Ausweg). `EZTagsObject::fetchList()`, `fetchListCount()` und
  `createFromAttribute()` des Datentyps sind verzweigt; MySQL und SQLite behalten die ursprünglichen Anweisungen.
- **Oracle und PostgreSQL** (2.4.5): Nachschlagen von Tags nach Hauptübersetzung nahm ein Bit mit `%`, was Oracle
  ablehnt, sodass verschlagwortete Inhalte ihre Tags verloren; jetzt verwenden sie `eZDB::bitAnd()`. Der Filter des
  Tag-Baums und die Tag-Suche nach Teilbaum verglichen mit einem in doppelte Anführungszeichen gesetzten Muster, was
  außerhalb von MySQL ein Bezeichner ist; Muster stehen in einfachen Anführungszeichen, und die Variante „und“ des
  Baumfilters, der das schließende Anführungszeichen seines Musters fehlte, funktioniert auf jeder Datenbank.

### Administrationsoberfläche

- Eintrag im oberen Menü, Titel des Reiters und Tooltip sagen **Tags** (2.3.5, 2.4.1) statt „eZ Tags“.
- Die Administration wird in jedem Admin-Design ausgegeben (2.4.8). eZ Tags lieferte seine Admin-Templates nur in
  `design/admin2` mit, sodass Dashboard, Tag-Seiten, Formulare, linkes Menü und Knotenreiter in den Designs `admin` und
  `admin3` leer waren. `design/admin` trägt jetzt pro Template von admin2 ein kleines Template, das das Original aus
  admin2 einbindet, sodass es eine Kopie zu pflegen gibt.
- Die Bearbeitungsseite des Tag-Felds funktioniert in Designs, die die `FrontendJavaScriptList` von eZ Tags nicht laden
  (2.4.8): Das Template des Felds verlangt seine eigenen Skripte und Stile, sodass sich Tags auf jedem Website-Design
  bearbeiten lassen.
- Jeder sichtbare Text ist ein Übersetzungstext mit Deutsch; Reiter und Tooltip sind übersetzt (2.4.2, 2.4.3).

### jQuery 4 und kein YUI (2.4.7, 2.4.8, 2.4.9)

- jsTree 3.3.17 ersetzt den älteren Build. Tag-Feld, modaler Dialog, Reiter Übersetzungen und der Filter der
  Kinderliste verwenden `.on()`, `.off()`, `.trigger()`, `.prop()`, `Array.isArray` und `Function.prototype.bind`,
  sodass die Admin-Seiten der Tags unter jQuery 4 ohne Warnungen von jQuery Migrate funktionieren.
- Die Tabelle der Kinder eines Tags läuft auf `exp::datatable` von Exponential UI (`$.fn.eZTagsChildrenExp`), und die
  DataTable von YUI 2 ist in 2.4.9 weg: `design.ini` fügt nicht mehr jeder Admin-Seite `ezjsc::yui2` hinzu.
  `$.fn.eZTagsChildren` bleibt als alter Name derselben Tabelle, und das Template `eztags_children_yui.tpl` heißt jetzt
  `eztags_children_table.tpl`. Die Extension benötigt `se7enxweb/expui ^1.0.0.1`. Siehe
  [Entfernung von YUI](../../../bc/6.0/yui-entfernung.md).

### Auslieferung

`ezinfo.php` (2.4.6) gibt Name, Version, Copyright, Lizenz und Website neben `extension.xml` an, sodass die Info-Seite und
die Aktualisierungsprüfungen aus jeder der beiden Dateien dasselbe Release lesen. Die Einstiegsdateien tragen über den
ursprünglichen Köpfen einen Kopf von 7x und der Exponential Foundation (2.4.10), und die Kommandozeilenskripte,
Cronjob-Teile und Modulansichten sind Klassen, die die Dateien aufrufen (2.4.10, 2.4.11); siehe
[Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md).

## Verwandte Seiten

- [xrowextract](xrowextract.md): exportiert Tags über seine Handler für Schlagwörter und Tags und einen erweiterten Filter für eztags
- [MongoDB-Datenbankunterstützung](../mongodb-datenbankunterstuetzung.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/eztags.md) und [Versionshinweise](../../../../changelogs/extensions/eztags.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/eztags.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-04](../../../geschichte/erweiterungen/monate/2026-04.md), [2026-08](../../../geschichte/erweiterungen/monate/2026-08.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
