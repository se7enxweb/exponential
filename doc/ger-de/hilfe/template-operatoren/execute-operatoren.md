---
title: Die Operatoren fetch und fetch_alias
audience: developer, designer, app-creator
category: development
order: 250
summary: fetch und fetch_alias aus eZTemplateExecuteOperator - wie ein Template über eZFunctionHandler und eZModuleFunctionInfo die function_definition.php eines Moduls erreicht, Parameter, Ergebnisse und Fehlschläge, was der Compiler mit einem konstanten fetch macht, mit Beispielen, die auf alpha ausgegeben wurden.
keywords: fetch, fetch_alias, Fetch-Funktion, Fetch-Alias, Daten lesen, Modul, function_definition.php, eZFunctionHandler, eZModuleFunctionInfo, function collection, fetchalias.ini, eZTemplateExecuteOperator, fetch function
type: help
---
# Die Operatoren fetch und fetch_alias

Templates lesen Daten mit `fetch`, das ein Operator ist, kein Funktions-Tag: Er gibt einen Wert zurück, den Sie mit
`def` zuweisen oder über die Pipe an andere Operatoren weitergeben. `fetch` und `fetch_alias` werden von
`eZTemplateExecuteOperator` (`lib/eztemplate/classes/eztemplateexecuteoperator.php`) über
`lib/eztemplate/classes/eztemplateautoload.php` registriert. Die Fetch-Funktionen selbst (content `node`, `list`,
`tree`, user `current_user` und so weiter) sind einzeln in der
[Referenz der Fetch-Funktionen](../../beispiele/fetch-funktionen/README.md) dokumentiert.

## Inhalt

1. [fetch](#fetch)
2. [fetch_alias](#fetch_alias)
3. [Wie ein fetch aufgelöst wird](#wie-ein-fetch-aufgelöst-wird)
4. [Wenn ein fetch fehlschlägt](#wenn-ein-fetch-fehlschlägt)
5. [Kompilierte fetch-Aufrufe](#kompilierte-fetch-aufrufe)
6. [Verwandte Seiten](#verwandte-seiten)

## fetch

### fetch

Ruft eine Funktion eines Moduls auf und gibt ihr Ergebnis zurück.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `module_name` | Zeichenkette, etwa `content`, `user`, `class`, `section` | ja | `false` |
| `function_name` | Zeichenkette, ein Schlüssel von `$FunctionList` des Moduls | ja | `false` |
| `function_parameters` | Hash der Parameter der Funktion | nein | `array()` |

Eingabe: keine. Gibt zurück, was die Funktion in den Schlüssel `result` ihrer Antwort legt: ein Objekt, eine Liste,
eine Zahl, oder `null`, wenn der Aufruf fehlschlägt. Generierte Referenz: [fetch](../../beispiele/template-operatoren/fetch.md).

Die Kategorie Recipes auf alpha (Knoten 144) und ihre Unterknoten:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 144 ) )
     $count = fetch( 'content', 'list_count', hash( 'parent_node_id', 144 ) )
     $first = fetch( 'content', 'list', hash( 'parent_node_id', 144,
                                               'limit', 3,
                                               'sort_by', array( 'name', true() ) ) )}
{$node.name|wash} (node {$node.node_id}, class {$node.class_identifier}) has {$count} children; the first three by name:

{foreach $first as $child}
- {$child.name|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
Recipes (node 144, class ng_category) has 18 children; the first three by name:
- Almond French Toast
- Avocado Kale Pesto Grilled Cheese
- Gluten-Free Shrimp Pad Thai with Tofu
```

Andere Module funktionieren genauso. Der Kommandozeilenlauf, der diese Seite ausgegeben hat, ist nicht angemeldet,
daher ist `current_user` der anonyme Benutzer:

```tpl
{def $user = fetch( 'user', 'current_user' )
     $classes = fetch( 'class', 'list', hash( 'class_filter', array( 'folder', 'ng_article' ) ) )}
Current user in this CLI run: {$user.login|wash} (logged in: {$user.is_logged_in|choose( 'no', 'yes' )})
Classes: {foreach $classes as $c}{$c.identifier} {/foreach}
```

Ausgabe auf alpha:

```text
Current user in this CLI run: anonymous (logged in: no)
Classes: folder ng_article
```

Der Parameter-Hash kann eine vorher gebaute Variable sein:

```tpl
{def $params = hash( 'parent_node_id', 144, 'limit', 2, 'sort_by', array( 'name', false() ) )}
{foreach fetch( 'content', 'list', $params ) as $k}{$k.name|wash}; {/foreach}
```

Ausgabe auf alpha:

```text
Zucchini Zoodles with Ground Turkey Tomato Sauce; Vibrant Guacamole;
```

## fetch_alias

### fetch_alias

Ruft einen fetch über einen kurzen Namen auf, der in `fetchalias.ini` definiert ist. Der Alias nennt das Modul, die
Funktion, welche Template-Parameter welchen Funktionsparametern entsprechen (`Parameter[]`) und feste Werte
(`Constant[]`); ein im Template angegebener Parameter hat Vorrang vor einer gleichnamigen Konstante, und Parameter, die
der Alias nicht zuordnet, werden unverändert weitergegeben (`eZFunctionHandler::executeAlias()`).

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `function_name` | Zeichenkette, ein Abschnitt von `fetchalias.ini` | ja | `false` |
| `function_parameters` | Hash | nein | `array()` |

Eingabe: keine. Gibt das Ergebnis des fetch hinter dem Alias zurück. Generierte Referenz:
[fetch_alias](../../beispiele/template-operatoren/fetch_alias.md). Der Kernel definiert acht Aliase (`object`,
`news_list`, `by_identifier`, `comments`, `children`, `children_count`, `subtree`, `reviews`); jeden Alias, seine
Zuordnung und wie Sie eigene hinzufügen, finden Sie unter [Fetch-Aliase](fetch-aliase.md).

```tpl
{def $kids = fetch_alias( 'children', hash( 'parent_node_id', 144, 'limit', 2, 'sort_by', array( 'name', true() ) ) )
     $n = fetch_alias( 'children_count', hash( 'parent_node_id', 144 ) )}
children_count: {$n}
children: {foreach $kids as $k}{$k.name|wash}; {/foreach}
```

Ausgabe auf alpha:

```text
children_count: 18
children: Almond French Toast; Avocado Kale Pesto Grilled Cheese;
```

## Wie ein fetch aufgelöst wird

1. `eZTemplateExecuteOperator::modify()` übergibt Modulname, Funktionsname und Parameter an
   `eZFunctionHandler::execute()` (`lib/ezutils/classes/ezfunctionhandler.php`).
2. `eZFunctionHandler::moduleFunctionInfo()` erzeugt je Modul und Anfrage ein `eZModuleFunctionInfo`
   (`lib/ezutils/classes/ezmodulefunctioninfo.php`), und `loadDefinition()` sucht
   `<module path>/<module>/function_definition.php` in jedem Pfad von `eZModule::globalPathList()`. Diese Pfade kommen
   aus `eZModule::activeModuleRepositories()`: `[ModuleSettings] ModuleRepositories` von `module.ini` (`kernel` und
   `kernel/private/modules`), danach der Ordner `modules/` jeder aktiven Erweiterung, die in `ExtensionRepositories`
   aufgeführt ist. Verwendet wird die **erste** gefundene Datei, und sie muss `$FunctionList` definieren.
3. `execute()` sucht die Funktion, prüft, dass sie eine `call_method` (Klasse und Methode) und `parameters` hat, und
   ruft die Methode der Function-Collection-Klasse mit den Parametern in der deklarierten Reihenfolge auf. Ein fehlender
   optionaler Parameter erhält seinen deklarierten Standardwert.
4. Die Methode gibt ein Array zurück; sein Schlüssel `result` ist das, was das Template erhält.

Eine solche Funktion schreiben: [Eine eigene Fetch-Funktion schreiben](../ersteller/anwendungsentwickler/eine-eigene-fetch-funktion-schreiben.md).

## Wenn ein fetch fehlschlägt

Ein unbekanntes Modul, eine unbekannte Funktion und ein fehlender Pflichtparameter ergeben im Template alle `null`. Die
Gründe („Cannot execute function ... no valid data“, „No such function“, „Missing parameter“) gehen über
`eZDebug::writeError()` in die Debug-Ausgabe, nicht in die eigene Fehlerliste des Templates, daher bleibt ein
fehlschlagender fetch auf der Seite stumm:

```tpl
unknown module: [{fetch( 'no_such_module', 'list' )|is_null|choose( 'not null', 'null' )}]
unknown function: [{fetch( 'content', 'no_such_function' )|is_null|choose( 'not null', 'null' )}]
missing parameter: [{fetch( 'content', 'node' )|is_null|choose( 'not null', 'null' )}]
```

Ausgabe auf alpha:

```text
unknown module: [null]
unknown function: [null]
missing parameter: [null]
```

Prüfen Sie das Ergebnis, bevor Sie es verwenden (`{if $node}` oder `is_null`), und sehen Sie sich während der
Entwicklung die Debug-Ausgabe an.

## Kompilierte fetch-Aufrufe

Sind Modul- und Funktionsname Konstanten, löst der Compiler (`fetchTransform()`) die Funktionsdefinition schon beim
Kompilieren auf und schreibt Code, der die Methode der Function Collection direkt aufruft, mit den Parametern in ihrer
deklarierten Reihenfolge, statt zur Laufzeit über `eZFunctionHandler` zu gehen. Der Methodenaufruf ist abgesichert:
Kann der laufende Prozess die Klasse oder Methode nicht laden, ist das Ergebnis `null`, und ein Fehler wird in die
Debug-Ausgabe geschrieben. Ein fetch mit variablem Modul- oder Funktionsnamen bleibt ein Aufruf zur Laufzeit. Alle
Beispiele auf dieser Seite ergaben auf alpha interpretiert und kompiliert dieselbe Ausgabe.

## Verwandte Seiten

- [Fetch-Funktionen: Daten in Templates lesen](../ersteller/anwendungsentwickler/fetch-funktionen.md)
- [Fetch-Grundlagen für Designer](../designer/vorlagen/fetch-grundlagen.md) und [Leistung von fetch](../designer/vorlagen/fetch-leistung.md)
- [Fetch-Aliase](fetch-aliase.md)
- [Die Referenz der Fetch-Funktionen (generiert)](../../beispiele/fetch-funktionen/README.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
