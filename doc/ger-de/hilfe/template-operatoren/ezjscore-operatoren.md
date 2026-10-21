---
title: Die Operatoren von ezjscore im Detail
audience: developer, designer, app-creator
category: development
order: 400
summary: Jeder Operator der Erweiterung ezjscore - die Packer-Operatoren ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles, die Encoder json_encode, xml_encode, node_encode sowie has_access_to_limitation - mit allen Parametern, dem Mechanismus der persistenten Variablen hinter require und load, den Packstufen und auf alpha gerenderten Beispielen.
keywords: ezjscore, Packer, Packstufe, persistente Variable, Stylesheets, JavaScript, Zugriffsrechte, ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles, json_encode, xml_encode, node_encode, has_access_to_limitation, packer, pack_level, persistent_variable
type: help
---
# Die Operatoren von ezjscore im Detail

Die Erweiterung `ezjscore` (im Kernel-Repository enthalten, `extension/ezjscore/`) registriert in
`extension/ezjscore/autoloads/eztemplateautoload.php` zwölf Operatoren; ihre `settings/site.ini.append.php` fügt die Erweiterung zu
`[TemplateSettings] ExtensionAutoloadPath[]` hinzu. Sie existieren nur, solange ezjscore eine aktive Erweiterung ist, was jedes
mitgelieferte Design voraussetzt. Für die Sicht des Designers lesen Sie zuerst
[ezjscore template operators](../designer/vorlagen/ezjscore-template-operatoren.md) und
[The ezcss and ezscript operators](../designer/theme-design/die-operatoren-ezcss-und-ezscript.md); diese Seite dokumentiert jeden
Parameter.

| Klasse | Datei | Operatoren |
|---|---|---|
| `ezjscPackerTemplateFunctions` | `autoloads/ezjscpackertemplatefunctions.php` | die acht Packer-Operatoren |
| `ezjscEncodingTemplateFunctions` | `autoloads/ezjscencodingtemplatefunctions.php` | `json_encode`, `xml_encode`, `node_encode` |
| `ezjscAccessTemplateFunctions` | `autoloads/ezjscaccesstemplatefunctions.php` | `has_access_to_limitation` |

## Inhalt

1. [Packstufen und Dateinamen](#packstufen-und-dateinamen)
2. [Die Packer-Operatoren](#die-packer-operatoren): [ezscript](#ezscript), [ezcss](#ezcss), [ezscriptfiles](#ezscriptfiles),
   [ezcssfiles](#ezcssfiles), [ezscript_require](#ezscript_require), [ezcss_require](#ezcss_require),
   [ezscript_load](#ezscript_load), [ezcss_load](#ezcss_load)
3. [Die Encoder](#die-encoder): [json_encode](#json_encode), [xml_encode](#xml_encode), [node_encode](#node_encode)
4. [Zugriff](#zugriff): [has_access_to_limitation](#has_access_to_limitation)

## Packstufen und Dateinamen

Alle Packer-Operatoren übergeben ihre Dateiliste an `ezjscPacker` (`extension/ezjscore/classes/ezjscpacker.php`).

- Ein Dateiname wird wie bei `ezdesign` im Ordner `javascript/` bzw. `stylesheets/` jeder Design-Basis gesucht. Ein Name wie
  `ezjsc::jquery` ist ein Aufruf einer Serverfunktion (`ezjscServer`-Funktionen, die in der `ezjscore.ini` aufgeführt sind), deren
  Ergebnis eine Liste von Dateien ist; Arrays innerhalb der Liste werden eingeebnet.
- `pack_level` 0 verlinkt die Dateien so, wie sie sind. Oberhalb von 0 werden die Dateien im Cache-Ordner
  (`var/<site>/cache/public/`) zu einer Datei zusammengefügt, und höhere Stufen minimieren sie zusätzlich. `ezjscore.ini`
  `[eZJSCore] Packer=disabled` erzwingt 0, eine Zahl an dieser Stelle ersetzt jedes `pack_level`; ist `Packer` nicht gesetzt,
  erzwingt `[TemplateSettings] DevelopmentMode=enabled` den Wert 0. Auf alpha gilt `Packer=enabled`, also entscheidet der Parameter.
- Beim Packen wird die zusammengefügte Datei geschrieben, wenn eine Liste zum ersten Mal verwendet wird. Die Beispiele auf dieser
  Seite verwenden `pack_level` 0, damit sie nichts schreiben.
- An die Links wird keine Versionsabfrage angehängt; eine geänderte Datei erhält einen neuen gepackten Namen, weil der Name die
  Zeitstempel der Dateien enthält.

```tpl
Packer setting: [{ezini( 'eZJSCore', 'Packer', 'ezjscore.ini' )}]
```

Ausgabe auf alpha:

```text
Packer setting: [enabled]
```

## Die Packer-Operatoren

### ezscript

Generierte Seite: [ezscript](../../beispiele/template-operatoren/ezscript.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `script_array` | Array | ja | |
| `type` | Zeichenkette | nein | `text/javascript` |
| `language` | Zeichenkette | nein | leer (nur auf Wunsch, kein gültiges HTML5) |
| `charset` | Zeichenkette | nein | `utf-8` |
| `pack_level` | Ganzzahl | nein | 2 |

- **Eingabe:** keine. **Gibt zurück:** `<script>`-Tags (`ezjscPacker::buildJavascriptTag()`). Der Operator berücksichtigt nicht,
  was zuvor angefordert oder geladen wurde.

### ezcss

Generierte Seite: [ezcss](../../beispiele/template-operatoren/ezcss.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `css_array` | Array | ja | |
| `media` | Zeichenkette | nein | `all` |
| `type` | Zeichenkette | nein | `text/css` |
| `rel` | Zeichenkette | nein | `stylesheet` |
| `charset` | Zeichenkette | nein | leer (veraltet) |
| `pack_level` | Ganzzahl | nein | 3 |

- **Eingabe:** keine. **Gibt zurück:** `<link>`-Tags (`ezjscPacker::buildStylesheetTag()`); `media` entfällt, wenn es `all` ist.

### ezscriptfiles

Generierte Seite: [ezscriptfiles](../../beispiele/template-operatoren/ezscriptfiles.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `script_array` | Array | ja | |
| `pack_level` | Ganzzahl | nein | 2 |
| `ignore_loaded` | Boolean | nein | `false` |

- **Gibt zurück:** die Liste der Adressen statt Tags. Ohne `ignore_loaded` werden nur die Dateien zurückgegeben (und vermerkt), die
  auf dieser Seite noch nicht angefordert oder geladen wurden.

### ezcssfiles

Generierte Seite: [ezcssfiles](../../beispiele/template-operatoren/ezcssfiles.md).

Dasselbe für Stylesheets: `css_array`, `pack_level` (Standard 3), `ignore_loaded`.

```tpl
1 {ezcssfiles( array( 'core.css', 'debug.css' ), 0, true() )|implode( ' ' )}
2 {ezscriptfiles( array( 'ezjsc::jquery' ), 0, true() )|implode( ' ' )}
3 {ezcss( array( 'core.css' ), 'screen', 'text/css', 'stylesheet', '', 0 )}
4 {ezscript( array( 'ezjsc::jquery' ), 'text/javascript', '', 'utf-8', 0 )}
```

Ausgabe auf alpha:

```text
1 /extension/ezwebin/design/ezwebin/stylesheets/core.css /design/standard/stylesheets/debug.css
2  /extension/expui/design/standard/lib/jquery/jquery-4.0.0.min.js /extension/expui/design/standard/lib/jquery/jquery-migrate-4.0.2.js
3 <link rel="stylesheet" href="/extension/ezwebin/design/ezwebin/stylesheets/core.css" media="screen">

4 <script src="/extension/expui/design/standard/lib/jquery/jquery-4.0.0.min.js"></script>
<script src="/extension/expui/design/standard/lib/jquery/jquery-migrate-4.0.2.js"></script>
```

### ezscript_require

Generierte Seite: [ezscript_require](../../beispiele/template-operatoren/ezscript_require.md).

Die Parameter von `ezscript`. Solange `ezscript_load` auf der Seite noch nicht gelaufen ist, gibt der Operator nichts aus und hängt
die Dateien an `js_files` in der **persistenten Variablen** an, sodass ein View-Template ein Skript anfordern kann, das der
Pagelayout später ausgibt. Nachdem `ezscript_load` gelaufen ist, gibt er Tags für die noch nicht geladenen Dateien aus.

### ezcss_require

Generierte Seite: [ezcss_require](../../beispiele/template-operatoren/ezcss_require.md).

Dasselbe für Stylesheets (`css_files`), mit den Parametern von `ezcss`.

### ezscript_load

Generierte Seite: [ezscript_load](../../beispiele/template-operatoren/ezscript_load.md).

Die Parameter von `ezscript`, wobei `script_array` optional ist. Der erste Aufruf auf einer Seite stellt seine eigenen Dateien
**vor** die angeforderten, gibt Tags für alle aus und markiert die Skripte als geladen; spätere Aufrufe geben nur Dateien aus, die
noch nicht geladen sind.

### ezcss_load

Generierte Seite: [ezcss_load](../../beispiele/template-operatoren/ezcss_load.md).

Dasselbe für Stylesheets, mit den Parametern von `ezcss`, wobei `css_array` optional ist.

Die vermerkten Listen liegen in der persistenten Variablen: in einem View-Template `$persistent_variable`, im Pagelayout
`$module_result.content_info.persistent_variable` (`ezjscPackerTemplateFunctions::setPersistentArray()`), sodass sie den
View-Cache überdauern. Eine persistente Variable, die kein Hash ist, wird mit dem Fehler „persistent_variable was not an array“
zurückgesetzt. Die folgende Reihenfolge zeigt den ganzen Ablauf in einem Template:

```tpl
{def $persistent_variable = hash()}
1 require: [{ezcss_require( array( 'debug.css' ) )}]
2 load: {ezcss_load( array( 'core.css' ), 'all', 'text/css', 'stylesheet', '', 0 )}
3 remembered: {$persistent_variable.css_files|implode( ', ' )}
4 require after load: {ezcss_require( array( 'pagelayout.css' ), 'all', 'text/css', 'stylesheet', '', 0 )}
5 again: [{ezcss_require( array( 'pagelayout.css' ), 'all', 'text/css', 'stylesheet', '', 0 )}]
```

Ausgabe auf alpha:

```text
1 require: []
2 load: <link rel="stylesheet" href="/extension/ezwebin/design/ezwebin/stylesheets/core.css">
<link rel="stylesheet" href="/design/standard/stylesheets/debug.css">

3 remembered: core.css, debug.css
4 require after load: <link rel="stylesheet" href="/extension/ezwebin/design/ezwebin/stylesheets/pagelayout.css">

5 again: []
```

Zeile 5 ist leer, weil `pagelayout.css` bereits in Zeile 4 ausgegeben wurde.

## Die Encoder

### json_encode

Generierte Seite: [json_encode](../../beispiele/template-operatoren/json_encode.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `hash` | Hash | ja | leeres Array |

- **Eingabe:** nicht verwendet: Übergeben Sie den Wert als Parameter. **Gibt zurück:** das Ergebnis von PHPs `json_encode()` für
  diesen Wert. Objekte ohne öffentliche Eigenschaften werden zu `{}`; übergeben Sie Hashes und Arrays.

Das Ergebnis ist JSON und nicht für HTML maskiert: Innerhalb eines Blocks `<script type="application/json">` ist das genau
richtig; in einem Attribut maskieren Sie es mit wash.

### xml_encode

Generierte Seite: [xml_encode](../../beispiele/template-operatoren/xml_encode.md).

Derselbe Parameter. **Gibt zurück:** ein XML-Dokument mit einem Element `<root>` und einem Element pro Schlüssel
(`ezjscAjaxContent::xmlEncode()`; Listeneinträge werden zu Elementen `<child>`).

### node_encode

Generierte Seite: [node_encode](../../beispiele/template-operatoren/node_encode.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `node` | Objekt | ja | |
| `params` | Hash | nein | leer |
| `type` | Zeichenkette | nein | `json` |

- **Eingabe:** keine. **Gibt zurück:** einen Knoten, ein Inhaltsobjekt oder eine Liste davon, vereinfacht durch
  `ezjscAjaxContent::simplify()`: JSON für `type` `json`, XML für `xml`, das Array selbst für jeden anderen Wert.
- **params:** `dataMap` (einzuschließende Attributbezeichner, `array( 'all' )` für alle), `dataMapType` (Filter nach Datentyp),
  `fetchPath`, `fetchSection`, `fetchChildrenCount`, `loadImages` mit `imageSizes` und `imagePreGenerateSizes` (Standard `small`),
  `imageDataTypes`, `fetchNodeFunction`.

Mit `loadImages` werden die aufgeführten Bildgrößen erzeugt, wenn sie noch nicht existieren, wodurch Bilddateien geschrieben werden:
Fordern Sie nur Aliase an, die die Bilder bereits haben.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
1 {json_encode( hash( 'title', $node.name, 'children', $node.children_count, 'tags', array( 'a', 'b' ) ) )}
2 {xml_encode( hash( 'title', $node.name, 'id', $node.node_id ) )}
{def $data = node_encode( $node, hash( 'dataMap', array( 'title' ) ), false() )}
3 node_encode as array: {foreach $data as $k => $v}{$k}{delimiter} {/delimiter}{/foreach}

4 node_encode as JSON: {node_encode( $node, hash(), 'json' )|count_chars} characters, starts {node_encode( $node, hash(), 'json' )|extract_left( 40 )}
```

Ausgabe auf alpha:

```text
1 {"title":"Privacy Policy","children":0,"tags":["a","b"]}
2 <?xml version="1.0" encoding="UTF-8"?>
<root><title>Privacy Policy</title><id>90</id></root>3 node_encode as array: name id contentobject_id contentobject_remote_id contentobject_state main_node_id version modified published section_id current_language owner_id class_id class_name path_identification_string translations initial_language can_edit class_identifier is_container node_id parent_node_id node_remote_id url_alias url depth priority hidden_status_string is_hidden is_invisible path children_count image_attributes data_map
4 node_encode as JSON: 838 characters, starts {"name":"Privacy Policy","id":88,"conten
```

## Zugriff

### has_access_to_limitation

Generierte Seite: [has_access_to_limitation](../../beispiele/template-operatoren/has_access_to_limitation.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `module` | Zeichenkette | ja | leer |
| `function` | Zeichenkette | ja | leer |
| `limitations` | Array (Hash) | ja | leer |
| `debug` | Boolean | nein | `false` |

- **Eingabe:** keine. **Gibt zurück:** `true` oder `false` für den aktuellen Benutzer.

Zuerst entscheidet `hasAccessTo()`: `yes` und `no` werden unverändert zurückgegeben. Bei Zugriff `limited` werden die Werte aller
Richtlinien pro übergebenem Limitierungsschlüssel zusammengeführt; der Aufruf ergibt `true`, wenn jeder übergebene Wert unter den
zusammengeführten Werten ist (`Subtree` und `User_Subtree` passen, wenn Ihre Pfadzeichenkette einen der erlaubten Pfade enthält).
Limitierungsschlüssel der Richtlinien, die Sie nicht übergeben haben, werden ignoriert (mit `debug` in der Debug-Ausgabe vermerkt).
Auf alpha liest die anonyme Rolle die Sektionen 1, 3 und 67, daher wird Sektion 2 abgelehnt:

```tpl
{def $access = fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'read' ) )}
anonymous content/read: {$access|choose( 'no', 'yes' )}
1 section 1: {has_access_to_limitation( 'content', 'read', hash( 'Section', 1 ) )|choose( 'no', 'yes' )}
2 section 3: {has_access_to_limitation( 'content', 'read', hash( 'Section', 3 ) )|choose( 'no', 'yes' )}
3 section 2: {has_access_to_limitation( 'content', 'read', hash( 'Section', 2 ) )|choose( 'no', 'yes' )}
4 content/edit: {has_access_to_limitation( 'content', 'edit', hash( 'Section', 1 ) )|choose( 'no', 'yes' )}
```

Ausgabe auf alpha:

```text
anonymous content/read: yes
1 section 1: yes
2 section 3: yes
3 section 2: no
4 content/edit: no
```

## Verwandte Seiten

- [ezjscore-Template-Operatoren für Designer](../designer/vorlagen/ezjscore-template-operatoren.md)
- [JSON-Endpunkte mit ezjscore](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md)
- [Die persistente Variable](../designer/vorlagen/die-persistent-variable.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
