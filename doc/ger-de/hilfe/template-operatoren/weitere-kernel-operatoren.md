---
title: Operatoren für Einstellungen, Module, Pakete und PDF
audience: developer, designer, app-creator
category: development
order: 350
summary: Die Kernel-Operatoren ezpreference, ezmodule, module_params und ezpackage und der Bibliotheksoperator pdf - was jeder liest, seine Parameter, was er zurückgibt, welche davon Seiteneffekte haben - mit Beispielen, auf alpha gerendert.
keywords: Benutzereinstellungen, Module, Pakete, PDF-Export, Siteaccess-Regeln, ezpreference, ezmodule, module_params, ezpackage, pdf, eZPreferences, eZModule::accessAllowed, SiteAccessRules, package, PDF export, eZPDF
type: help
---
# Operatoren für Einstellungen, Module, Pakete und PDF

Vier Operatoren aus `kernel/common/` und einer aus `lib/ezpdf/`, registriert in `kernel/common/eztemplateautoload.php`
und `lib/ezpdf/classes/eztemplateautoload.php` (ein Pfad von `[TemplateSettings] AutoloadPathList[]` in `site.ini`).

| Operator | Klasse und Datei |
|---|---|
| `ezpreference` | `eZKernelOperator`, `kernel/common/ezkerneloperator.php` |
| `ezmodule` | `eZModuleOperator`, `kernel/common/ezmoduleoperator.php` |
| `module_params` | `eZModuleParamsOperator`, `kernel/common/ezmoduleparamsoperator.php` |
| `ezpackage` | `eZPackageOperator`, `kernel/common/ezpackageoperator.php` |
| `pdf` | `eZPDF`, `lib/ezpdf/classes/ezpdf.php` |

Die Beispiele liefen auf der Kommandozeile als anonymer Benutzer.

## Inhalt

1. [Die Operatoren](#die-operatoren): [ezpreference](#ezpreference), [ezmodule](#ezmodule),
   [module_params](#module_params), [ezpackage](#ezpackage), [pdf](#pdf)

## Die Operatoren

### ezpreference

Generierte Seite: [ezpreference](../../../eng-us/examples/template-operators/ezpreference.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `name` | Zeichenkette | ja | `false` |

- **Eingabe:** keine. **Rückgabe:** die gespeicherte Einstellung dieses Namens des aktuellen Benutzers (eine
  Zeichenkette) oder `false`.

Der Operator ruft `eZPreferences::value( $name )` auf (`kernel/classes/ezpreferences.php`): Der Wert kommt aus der
Sitzung, wenn er dort für den angemeldeten Benutzer gespeichert wurde, sonst aus der Tabelle `ezpreferences`. Für den
anonymen Benutzer ist er immer `false`, ohne Abfrage. Einstellungen werden von der Ansicht `user/preferences` gesetzt (die
Schalter der Administration, die sich etwas merken), nicht von Templates. Der Compiler macht aus dem Aufruf einen direkten
Aufruf von `eZPreferences::value()` (`preferencesTransformation()`).

```tpl
1 [{ezpreference( 'admin_navigation_content' )}]
2 {ezpreference( 'admin_list_limit' )|get_type}
```

Ausgabe auf alpha:

```text
1 []
2 boolean[false]
```

### ezmodule

Generierte Seite: [ezmodule](../../../eng-us/examples/template-operators/ezmodule.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `uri` | Zeichenkette | nein | `false` |

- **Eingabe:** keine. **Rückgabe:** `true`, wenn das Modul in `module.ini` `[ModuleSettings] ModuleList[]` steht und der
  Siteaccess die Ansicht erlaubt, sonst `false`.

Der Operator prüft **nicht** die Richtlinien des Benutzers. `eZModule::accessAllowed()` (`lib/ezutils/classes/ezmodule.php`)
wendet die Siteaccess-Regeln aus `site.ini` `[SiteAccessRules] Rules[]` an und, mit `[ConfigurationSettings]
ManagedByDeployment=enabled`, die für die Bereitstellung gesperrten Ansichten. Deshalb ist `setup/info` unten für den
anonymen Benutzer erlaubt: Ob der Benutzer die Ansicht ausführen darf, entscheidet die eigene Richtlinienprüfung der
Ansicht. Verwenden Sie den Operator, um Links auf Ansichten zu verbergen, die ein Siteaccess abschaltet; für
Berechtigungen verwenden Sie `fetch( 'user', 'has_access_to', ... )`.

### module_params

Generierte Seite: [module_params](../../../eng-us/examples/template-operators/module_params.md).

- **Eingabe:** keine. **Parameter:** keine.
- **Rückgabe:** `$GLOBALS['eZRequestedModuleParams']`, ein Hash mit `module_name`, `function_name` (die Ansicht) und `parameters` (die benannten Ansichtsparameter), den `eZModule` beim Ausführen einer Ansicht speichert,
  oder `null`, wenn keine Ansicht lief (wie auf der Kommandozeile, Zeile 5 unten).

```tpl
1 content/view: {ezmodule( 'content/view' )|choose( 'no', 'yes' )}
2 setup/info: {ezmodule( 'setup/info' )|choose( 'no', 'yes' )}
3 user/login: {ezmodule( 'user/login' )|choose( 'no', 'yes' )}
4 nosuchmodule/view: {ezmodule( 'nosuchmodule/view' )|choose( 'no', 'yes' )}
5 module_params: {module_params()|get_type}
```

Ausgabe auf alpha:

```text
1 content/view: yes
2 setup/info: yes
3 user/login: yes
4 nosuchmodule/view: no
5 module_params: null
```

### ezpackage

Generierte Seite: [ezpackage](../../../eng-us/examples/template-operators/ezpackage.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class` | Zeichenkette | ja | `false` |
| `data` | Zeichenkette | nein | `false` |

- **Eingabe:** ein `eZPackage`-Objekt (aus `fetch( 'package', 'list' )` oder `fetch( 'package', 'item' )`).
- **Rückgabe:** ein Pfad, abhängig von `class`:

| `class` | Rückgabe |
|---|---|
| `thumbnail` | der Pfad der ersten Datei des Pakets vom Typ `thumbnail` oder `false` |
| `filepath` | der Pfad der Datei, deren Variablenname `data` ist; Fehler "No filepath found", wenn es keine gibt |
| `fileitempath` | der Pfad des in `data` angegebenen Dateieintrags |
| `documentpath` | der Pfad des Paketdokuments mit dem Namen `data`; Fehler "No documentpath found", wenn es keines gibt |
| `dirpath` | der Paketordner in seinem Repository |

Jeder andere Wert von `class` ergibt den Fehler "Unknown package operator name". Die Pfade sind relativ zum Wurzelverzeichnis
der Installation.

```tpl
{def $packages = fetch( 'package', 'list', hash( 'offset', 0, 'limit', 3 ) )}
{foreach $packages as $p}
{$p.name}: thumbnail={$p|ezpackage( 'thumbnail' )|wash} dir={$p|ezpackage( 'dirpath' )|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
slash_quotes: thumbnail= dir=var/storage/packages/local/slash_quotes
ezgmaplocation_extension: thumbnail= dir=var/storage/packages/ez-systems/ezgmaplocation_extension
ezstarrating_extension: thumbnail= dir=var/storage/packages/ez-systems/ezstarrating_extension
```

### pdf

Generierte Seite: [pdf](../../../eng-us/examples/template-operators/pdf.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `operation` | Zeichenkette | ja | leer |

Weitere Positionsparameter hängen von der Operation ab, meist ein Hash mit Optionen.

`eZPDF` steuert den PDF-Export (`content/pdf`, die `pdf`-View-Templates wie
`design/standard/templates/node/view/pdf.tpl`). Die meisten Operationen **geben nur einen Markup-Befehl** für die
PDF-Engine zurück, den das Template als Text sammelt; einige wenige wirken auf das PDF-Objekt:

| Operationen | Was sie tun |
|---|---|
| `toc`, `set_font`, `table`, `header`, `new_line`/`newline`, `new_page`/`newpage`, `image`, `anchor`, `link`, `strike`, `page_number`/`pageNumber`, `line`, `footer_block`, `header_block`, `footer`, `frame_header`, `frontpage`, `set_margin`, `keyword`, `create_index`, `ul`, `filled_circle`, `rectangle`, `filled_rectangle`, `text`, `text_box`, `text_frame` | geben eine Befehlszeichenkette wie `<C:callNewLine>` zurück; Standardwerte kommen aus `pdf.ini` |
| `create` | erzeugt das PDF-Objekt |
| `execute` | erzeugt das PDF-Objekt (Größe und Ausrichtung aus den Optionen oder aus `pdf.ini` `[PDFGeneral]`) und rendert den gesammelten Text hinein |
| `stream` | sendet das PDF an den Browser |
| `close` | schreibt das PDF über den Cluster-Datei-Handler in den als zweiten Parameter angegebenen Dateinamen |

Auf alpha nicht ausgeführt: `execute`, `stream` und `close` (sie rendern ein Dokument, senden Ausgabe oder schreiben eine
Datei). Die Operationen unten bauen nur Befehlszeichenketten:

```tpl
1 {pdf( 'new_line' )}
2 {pdf( 'new_page' )}
3 {pdf( 'strike', 'old price' )}
4 {pdf( 'anchor', hash( 'name', 'top' ) )}
5 {pdf( 'link', hash( 'url', 'https://exponential.earth/', 'text', 'Exponential' ) )}
6 {pdf( 'toc', hash( 'size', array( 12, 10 ), 'dots', 'true' ) )}
```

Ausgabe auf alpha:

```text
1 <C:callNewLine>
2 <C:callNewPage><C:callNewLine>
3 <c:strike>old price</c:strike>
4 <C:callAnchor:top:FitH:>
5 <c:alink:https%3A%2F%2Fexponential.earth%2F>Exponential</c:alink>
6 <C:callTOC:size:12,10:dots:true>
```

## Verwandte Seiten

- [PDF-Exporte](../../anleitungen/pdf-exporte.md)
- [Rollen- und Richtlinien-Operatoren](../../../eng-us/help/designers/templates/role-and-policy-operators.md)
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
