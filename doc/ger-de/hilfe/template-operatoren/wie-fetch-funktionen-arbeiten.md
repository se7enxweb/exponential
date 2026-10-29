---
title: Wie Fetch-Funktionen arbeiten
audience: developer, app-creator
category: development
order: 700
summary: Was geschieht, wenn ein Template fetch() aufruft - eZFunctionHandler, eZModuleFunctionInfo, die function_definition.php des Moduls und seine Klasse für die Funktionssammlung -, das Format der Definition, Ergebnisse und Fehler, die Suchreihenfolge der Modul-Repositories und wie eine Erweiterung ein Fetch-Modul hinzufügt, die Fetch-Funktionen eines Kernel-Moduls erweitert oder ersetzt, jeweils auf einer Entwicklungsinstallation gerendert.
keywords: Fetch-Funktionen, Fetch-Funktion, Funktionssammlung, Modul-Repositories, Fetch-Funktion überschreiben, Fetch-Funktion erweitern, fetch, fetch function, function_definition.php, FunctionList, call_method, function collection, eZFunctionHandler, eZModuleFunctionInfo, module repositories, ExtensionRepositories, override fetch function, extend fetch function
type: help
---
# Wie Fetch-Funktionen arbeiten

`fetch( module, function, hash( ... ) )` ist der Weg, auf dem ein Template Daten liest: Inhalte, Benutzer, Sections,
Ihre eigenen Tabellen. Diese Seite verfolgt einen Aufruf durch den Code und zeigt, wie eine Erweiterung
Fetch-Funktionen hinzufügt oder ändert. Die Parameter jeder Fetch-Funktion dieser Installation stehen in der
[Referenz der Fetch-Funktionen](../../beispiele/fetch-funktionen/README.md); eine erste eigene Fetch-Funktion wird
Schritt für Schritt in [Eine eigene Fetch-Funktion schreiben](../ersteller/anwendungsentwickler/eine-eigene-fetch-funktion-schreiben.md)
geschrieben.

## Inhalt

1. [Der Weg eines Aufrufs](#der-weg-eines-aufrufs)
2. [function_definition.php](#function_definitionphp)
3. [Die Funktionssammlung](#die-funktionssammlung)
4. [Wenn etwas nicht stimmt](#wenn-etwas-nicht-stimmt)
5. [Wo Module gefunden werden](#wo-module-gefunden-werden)
6. [Ein Fetch-Modul in einer Erweiterung hinzufügen](#ein-fetch-modul-in-einer-erweiterung-hinzufügen)
7. [Die Fetch-Funktionen eines Kernel-Moduls erweitern](#die-fetch-funktionen-eines-kernel-moduls-erweitern)
8. [Sie ersetzen, und warum man es lassen sollte](#sie-ersetzen-und-warum-man-es-lassen-sollte)
9. [Kompilierte Templates](#kompilierte-templates)

## Der Weg eines Aufrufs

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $count = fetch( 'content', 'list_count', hash( 'parent_node_id', 89 ) )}
{$node.name|wash}: {$count} children
```

Ausgabe:

```text
Fit &amp; Healthy: 16 children
```

1. Der Template-Operator `fetch` (`eZTemplateExecuteOperator::modify()`, `lib/eztemplate/classes/eztemplateexecuteoperator.php`)
   ruft `eZFunctionHandler::execute( 'content', 'node', array( 'node_id' => 89 ) )` auf.
2. `eZFunctionHandler::moduleFunctionInfo( 'content' )` (`lib/ezutils/classes/ezfunctionhandler.php`) erzeugt einmal
   je Anfrage ein `eZModuleFunctionInfo` für das Modul und hält es in `$GLOBALS['eZGlobalModuleFunctionList']`.
3. `eZModuleFunctionInfo::loadDefinition()` (`lib/ezutils/classes/ezmodulefunctioninfo.php`) sucht in jedem
   Modul-Repository nach `<repository>/content/function_definition.php` und bindet die **erste** gefundene Datei ein.
   Diese Datei setzt `$FunctionList`.
4. `execute()` sucht `node` in `$FunctionList`, baut die Argumentliste aus den `parameters` der Definition in deren
   Reihenfolge auf (Ihr Wert, sonst der `default`, sonst `null`; ein fehlender erforderlicher Parameter bricht den
   Aufruf ab) und ruft die `call_method` `class::method` an einer Instanz der Klasse auf.
5. Die Methode gibt `array( 'result' => ... )` zurück; das Template erhält das `result`.

## function_definition.php

Die Datei `kernel/content/function_definition.php` des Kernels zeigt mit dem Eintrag `node` das Format:

```php
$FunctionList['node'] = array( 'name' => 'node',
                               'operation_types' => array( 'read' ),
                               'call_method' => array( 'class' => 'eZContentFunctionCollection',
                                                       'method' => 'fetchContentNode' ),
                               'parameter_type' => 'standard',
                               'parameters' => array( array( 'name' => 'node_id',
                                                             'type' => 'integer',
                                                             'required' => false,
                                                             'default' => false ),
                                                      array( 'name' => 'node_path',
                                                             'type' => 'string',
                                                             'required' => false,
                                                             'default' => false ),
                                                      // ...
                                                    ) );
```

| Schlüssel | Bedeutung |
|---|---|
| `name` | der Funktionsname, wie er in `fetch()` verwendet wird |
| `operation_types` | `read` bei jeder Fetch-Funktion im Kernel; dient der Dokumentation |
| `call_method` | `class` und `method`, die die Arbeit erledigen; die Klasse wird mit `new` erzeugt und muss per Autoload ladbar sein |
| `parameter_type` | `standard` |
| `parameters` | in der Reihenfolge der Argumente der Methode: `name`, `type`, `required`, `default` |

Das Template übergibt Parameter **nach Namen** in einem Hash; die Definition ordnet sie den Argumenten der Methode
**nach Position** zu. `type` wird nicht geprüft; ein Parameter vom `type` `array` ist für
[Fetch-Aliase](fetch-aliase.md) wichtig, die solche Konstanten an `;` aufteilen.

## Die Funktionssammlung

Nach Konvention enthält eine Klasse je Modul alle seine Fetch-Funktionen, die "Funktionssammlung"
(`eZContentFunctionCollection` in `kernel/content/ezcontentfunctioncollection.php`). Jede Methode gibt ein Array
zurück:

- `array( 'result' => $value )` bei Erfolg: Das Template erhält `$value`;
- `array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) )`, wenn das
  Gesuchte nicht existiert oder nicht gelesen werden darf: Das Template erhält `null`.

Prüfen Sie Berechtigungen in der Methode (`canRead()` an Knoten und Objekten oder die Richtlinien des Benutzers): Ein
Template kann jede Fetch-Funktion mit beliebigen Parametern aufrufen, und die Sammlungen des Kernels filtern nach den
Zugriffsrechten des aktuellen Benutzers.

## Wenn etwas nicht stimmt

Fetch hält die Seite nie an. Jedes Problem wird in die Debug-Ausgabe geschrieben, und das Template erhält `null`:

```tpl
[{fetch( 'nosuchmodule', 'list', hash() )}]
```

Ausgabe:

```text
[]
```

(`Missing function definition file for module: nosuchmodule` und
`Cannot execute function 'list' in module 'nosuchmodule', no valid data`.)

```tpl
{def $result = fetch( 'content', 'nosuchfunction', hash() )}
{if is_null( $result )}no result{/if}
```

Ausgabe:

```text
no result
```

(`No such function 'nosuchfunction' in module 'content'`.)

```tpl
{def $result = fetch( 'content', 'node', hash() )}
{if is_null( $result )}no result{else}{$result.name|wash}{/if}
```

Ausgabe:

```text
no result
```

`content/node` hat keinen erforderlichen Parameter; dieser Aufruf erreicht also
`eZContentFunctionCollection::fetchContentNode()`, das ohne Knoten-ID oder Pfad keinen Knoten zurückgibt. Fehlt bei
einer Funktion ein erforderlicher Parameter, ruft `eZModuleFunctionInfo::executeClassMethod()` die Methode gar nicht
erst auf, und die Debug-Ausgabe nennt den fehlenden Parameter. Prüfen Sie Fetch-Ergebnisse mit `is_null()` oder
`is_set()`, bevor Sie sie verwenden.

## Wo Module gefunden werden

Die Modul-Repositories stammen aus `module.ini` `[ModuleSettings]`: `ModuleRepositories[]` (die Verzeichnisse `kernel`
und `kernel/private/modules` des Kernels) und für jede in `ExtensionRepositories[]` aufgeführte und aktive Erweiterung
deren Ordner `modules`. `eZModule::activeModuleRepositories()` (`lib/ezutils/classes/ezmodule.php`) führt die beiden
Listen zusammen und **kehrt sie um**; Erweiterungen werden also vor dem Kernel durchsucht, und von den Erweiterungen
zuerst die, die in `ExtensionRepositories[]` zuletzt steht. Im Siteaccess `site` einer Entwicklungsinstallation beginnt die
Suchreihenfolge mit `extension/sevenx_alpha_settings/modules`, `extension/expui/modules`, `extension/ezjscore/modules`,
... und endet mit `kernel/private/modules` und `kernel`; 28 Module haben Fetch-Funktionen, 21 davon im Kernel. Keine
Erweiterung auf einer Entwicklungsinstallation hat eine `function_definition.php` für ein Kernel-Modul.

## Ein Fetch-Modul in einer Erweiterung hinzufügen

Ein Modul, das nur Fetch-Funktionen anbietet, braucht keine Ansichten: Ein Ordner mit einer `function_definition.php`
genügt.

```
extension/myextension/modules/c11demo/function_definition.php
extension/myextension/classes/c11demofunctioncollection.php
extension/myextension/settings/module.ini.append.php
```

`module.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=myextension

*/ ?>
```

Die Definition (`modules/c11demo/function_definition.php`):

```php
<?php
$FunctionList = array();
$FunctionList['children_by_class'] = array(
    'name' => 'children_by_class',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'C11DemoFunctionCollection',
                            'method' => 'fetchChildrenByClass' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'parent_node_id',
                                  'type' => 'integer',
                                  'required' => true ),
                           array( 'name' => 'limit',
                                  'type' => 'integer',
                                  'required' => false,
                                  'default' => 50 ) ) );
```

Die Sammlung (`classes/c11demofunctioncollection.php`, danach `php bin/php/ezpgenerateautoloads.php -e`):

```php
class C11DemoFunctionCollection
{
    /**
     * The children of a node, counted per class identifier: array( identifier => count ), largest first.
     */
    public function fetchChildrenByClass( $parentNodeID, $limit )
    {
        $parent = eZContentObjectTreeNode::fetch( (int)$parentNodeID );
        if ( !$parent instanceof eZContentObjectTreeNode || !$parent->canRead() )
            return array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) );

        $counts = array();
        foreach ( $parent->subTree( array( 'Depth' => 1, 'Limit' => (int)$limit ) ) as $child )
        {
            $identifier = $child->attribute( 'class_identifier' );
            $counts[$identifier] = ( $counts[$identifier] ?? 0 ) + 1;
        }
        arsort( $counts );
        return array( 'result' => $counts );
    }
}
```

Auf einer Entwicklungsinstallation nicht als installierte Erweiterung ausgeführt: Das Skript, das die Beispiele dieses und der beiden nächsten
Abschnitte gerendert hat, stellte im Speicher ein Modul-Repository aus einem Arbeitsordner vor die anderen
(`eZModule::setGlobalPathList()`, nur für seinen eigenen Prozess) und lud die Klassen aus einem Arbeitsordner.

```tpl
{foreach fetch( 'c11demo', 'children_by_class', hash( 'parent_node_id', 89 ) ) as $identifier => $count}
{$identifier}: {$count}

{/foreach}
```

Ausgabe:

```text
ng_landing_page: 5
ng_topic: 4
ng_article: 2
ng_category: 1
ng_contact_form: 1
ng_container: 1
ng_site_info: 1
folder: 1
```

## Die Fetch-Funktionen eines Kernel-Moduls erweitern

Weil nur die erste für einen Modulnamen gefundene `function_definition.php` gelesen wird, kann eine Erweiterung
`content` oder `section` keine einzelne Fetch-Funktion hinzufügen, indem sie eine Datei mit nur dieser Funktion
mitliefert: Sie würde alle Funktionen des Kernels verdecken. Binden Sie die Datei des Kernels ein und ergänzen Sie
deren Liste:

```php
<?php
/**
 * Extends the kernel's section fetch functions instead of replacing them: the first function_definition.php
 * found for a module is the only one read, so this file includes the kernel's and adds to its list.
 */
include 'kernel/section/function_definition.php';

$FunctionList['c11_identifiers'] = array(
    'name' => 'c11_identifiers',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'C11DemoSectionFunctionCollection',
                            'method' => 'fetchIdentifiers' ),
    'parameter_type' => 'standard',
    'parameters' => array() );
```

(`extension/myextension/modules/section/function_definition.php`; `C11DemoSectionFunctionCollection::fetchIdentifiers()`
gibt die sortierten Bezeichner von `eZSection::fetchList()` zurück.) Sowohl `section/list` des Kernels als auch die
neue Funktion funktionieren:

```tpl
{def $sections = fetch( 'section', 'list' )
     $identifiers = fetch( 'section', 'c11_identifiers' )}
section/list: {$sections|count} sections
section/c11_identifiers: {$identifiers|implode( ', ' )}
```

Ausgabe:

```text
section/list: 10 sections
section/c11_identifiers: , , design, help_internal, help_public, media, sample_collaboration, setup, standard, users
```

(Zwei Sections auf einer Entwicklungsinstallation haben einen leeren Bezeichner, daher die beiden führenden Kommas.) Der Pfad im include ist
relativ zum Wurzelverzeichnis der Installation, dem Arbeitsverzeichnis jeder Anfrage und jedes Skripts von
Exponential.

Trotzdem ist eine Funktion mit einem eigenen Modulnamen (`fetch( 'myext', ... )`) die bessere Wahl: Sie kann nicht mit
einer künftigen Kernel-Funktion gleichen Namens kollidieren und braucht keine Kopie des Verhaltens einer Kernel-Datei.

## Sie ersetzen, und warum man es lassen sollte

Dieselbe Datei ohne das `include` ersetzt die Fetch-Funktionen von section des Kernels in jedem Siteaccess, in dem die
Erweiterung aktiv ist:

```tpl
{def $sections = fetch( 'section', 'list' )
     $identifiers = fetch( 'section', 'c11_identifiers' )}
section/list: {$sections|count} sections
section/c11_identifiers: {$identifiers|implode( ', ' )}
```

Ausgabe:

```text
section/list: 0 sections
section/c11_identifiers: , , design, help_internal, help_public, media, sample_collaboration, setup, standard, users
```

`section/list` gibt jetzt nichts mehr zurück; jedes Template, das es verwendet, auch in der Administration, geht
stillschweigend kaputt. Wenn Sie ändern müssen, was eine Fetch-Funktion des Kernels zurückgibt, behalten Sie ihren
Namen und ihre Parameter, binden Sie die Datei des Kernels ein und ersetzen Sie nur die `call_method` dieses Eintrags
durch eine Unterklasse der Kernel-Sammlung, die für die Fälle, die Sie nicht ändern, `parent::` aufruft. Prüfen Sie es
bei jedem Kernel-Upgrade erneut.

## Kompilierte Templates

Bei `fetch()`-Aufrufen, deren Modul- und Funktionsnamen Konstanten sind, liest der Compiler
(`eZTemplateExecuteOperator::fetchTransform()`) die Funktionsdefinition zur Kompilierzeit und schreibt einen direkten
Aufruf der Sammlungsmethode in das kompilierte Template, abgesichert durch `class_exists()`/`method_exists()`. Zwei
Folgen:

- Leeren Sie nach dem Ändern einer `function_definition.php` (Parameter, Reihenfolge, `call_method`) den Template-Cache
  (`php bin/php/ezcache.php --clear-tag=template --allow-root-user`), sonst rufen kompilierte Templates weiter die alte
  Methode mit der alten Argumentreihenfolge auf;
- eine Definition, die zur Kompilierzeit nicht geladen werden kann (fehlende Klasse), lässt den Compiler auf einen
  Aufruf zur Laufzeit zurückfallen.

Alle Beispiele auf dieser Seite lieferten auf einer Entwicklungsinstallation interpretiert und kompiliert (in einen Arbeitsordner) dasselbe
Ergebnis.

## Verwandte Seiten

- [Fetch-Aliase](fetch-aliase.md)
- [Execute-Operatoren](execute-operatoren.md): die Operatoren `fetch` und `fetch_alias`
- [Referenz der Fetch-Funktionen](../../beispiele/fetch-funktionen/README.md)
- [Eine eigene Fetch-Funktion schreiben](../ersteller/anwendungsentwickler/eine-eigene-fetch-funktion-schreiben.md)
- [Fetch-Funktionen: Daten in Templates lesen](../ersteller/anwendungsentwickler/fetch-funktionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
