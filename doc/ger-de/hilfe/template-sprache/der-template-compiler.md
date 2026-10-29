---
title: Der Template-Compiler
audience: developer, designer, app-creator
category: development
order: 400
summary: Wie eZTemplateCompiler ein gelesenes Template in PHP umwandelt - die Pipeline (Transformation der Knoten, statische Optimierungen, Zusammenfassen, Erzeugen von PHP), Name und Ordner der kompilierten Datei, die Hinweise von Operatoren und Funktionen, mit denen ein Operator zu Inline-PHP wird, der Rückfall processOperator, DevelopmentMode, die Einstellungen - mit erzeugtem Code einer Entwicklungsinstallation und einem echten Unterschied zwischen kompilierter und interpretierter Ausgabe.
keywords: Template-Compiler, eZTemplateCompiler, TemplateCompile, kompilierte Templates, var/cache/template/compiled, operatorTemplateHints, functionTemplateHints, element-transformation, processOperator, DevelopmentMode, eztc, CompileComments, choose, template compiler, compiled templates
type: help
---
# Der Template-Compiler

Mit `[TemplateSettings] TemplateCompile=enabled` (der Standard in `settings/site.ini` und der Wert auf einer Entwicklungsinstallation) wird ein Template nicht bei
jeder Anfrage interpretiert. Beim ersten Gebrauch wandelt `eZTemplateCompiler` (`lib/eztemplate/classes/eztemplatecompiler.php`) seinen
gelesenen Baum in eine PHP-Datei um, und spätere Anfragen binden diese Datei ein. Diese Seite zeigt, wie, mit Code, den der Compiler auf einer Entwicklungsinstallation
erzeugt hat.

## Inhalt

1. [Wann ein Template kompiliert wird](#wann-ein-template-kompiliert-wird)
2. [Die Pipeline](#die-pipeline)
3. [Wie das erzeugte PHP aussieht](#wie-das-erzeugte-php-aussieht)
4. [Hinweise: wie ein Operator zu PHP wird](#hinweise-wie-ein-operator-zu-php-wird)
5. [Kompiliert und interpretiert sind nicht immer gleich](#kompiliert-und-interpretiert-sind-nicht-immer-gleich)
6. [Wohin die Dateien kommen und wann sie neu gebaut werden](#wohin-die-dateien-kommen-und-wann-sie-neu-gebaut-werden)
7. [Einstellungen](#einstellungen)
8. [Verwandte Seiten](#verwandte-seiten)

## Wann ein Template kompiliert wird

`eZTemplate::fetch()` bittet die Ressource (`design:` oder `file:`), das Template zu laden. Die Datei-Ressource
(`lib/eztemplate/classes/eztemplatefileresource.php`) fragt zuerst `eZTemplateCompiler::hasCompiledTemplate()`, ob eine kompilierte Datei
existiert, die mindestens so neu ist wie die Template-Datei. Wenn ja, wird das Template nicht einmal gelesen: Die Engine führt
`eZTemplateCompiler::executeCompilation()` aus, das die PHP-Datei einbindet. Wenn nicht, wird das Template gelesen und, wenn Kompilieren
erlaubt ist, kompiliert und dann ausgeführt. Scheitert das Ausführen der kompilierten Datei, lädt die Engine das Template neu und interpretiert
als letzten Ausweg den gelesenen Baum (`eZTemplate::reloadAfterFailedCompiledTemplate()`).

Das Kompilieren wird übersprungen, wenn `TemplateCompile` nicht `enabled` ist, wenn das Caching der Engine ausgeschaltet ist
(`$tpl->setIsCachingAllowed( false )`, was der Renderer der Beispiele dieser Seiten nutzt) oder wenn eine Anfrage mit dem Hinweis markiert ist,
nicht zu cachen (`$GLOBALS['eZSiteBasics']['no-cache-adviced']`).

## Die Pipeline

`eZTemplateCompiler::compileTemplate()` führt diese Schritte am gelesenen Baum aus:

1. **Transformation der Knoten** (`processNodeTransformation`): Jede Funktion und jeder Operator mit Hinweisen kann seinen Knoten durch
   einfachere Knoten oder durch PHP-Code ersetzen (siehe unten).
2. **Statische Optimierungen** (`processStaticOptimizations`): Zur Kompilierzeit bekannte Werte werden einmal berechnet.
3. **Zusammenfassen der Knoten** (`processNodeCombining`): Benachbarte Textknoten werden verbunden.
4. Die Angaben zur Position (Datei, Zeile, Spalte, in Fehlermeldungen genutzt) werden bei `CompileNodePlacements=disabled` verworfen.
5. **Erzeugen von PHP** (`generatePHPCode`), geschrieben mit `eZPHPCreator`.

`CompileIncludeNodeTree[]` (`original`, `transformed`, `static`, `combined`, `final`) fügt der erzeugten Datei die Zwischenbäume hinzu, für die
Arbeit am Compiler selbst.

## Wie das erzeugte PHP aussieht

Dieses kleine Template wurde auf einer Entwicklungsinstallation in einen Arbeitsordner kompiliert (die Einstellung `compilation-directory` des Compilers zeigte
woandershin, der Cache der Website blieb also unberührt) und auf beide Arten ausgeführt:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
<h1>{$node.name|wash}</h1>
{if $node.children_count|gt( 0 )}{$node.children_count} children{else}no children{/if}

{'compiled'|upcase}
```

Ausgabe:

```text
<h1>Why Should You Choose Strength Training?</h1>no children
COMPILED
```

Beide Läufe ergaben dieselbe Ausgabe. Auszüge aus den 135 erzeugten Zeilen:

```php
<?php
// URI:       file:var/tmp/examples/tm-compiler/01-small.tpl
// Filename:  var/tmp/examples/tm-compiler/01-small.tpl
// Timestamp: 1792402671 (Mon Oct 19 2:37:51 PDT 2026)
$tpl->Level++;
if ( $tpl->Level > 40 )
{
$text = $tpl->MaxLevelWarning;$tpl->Level--;
return;
}

// def $node
unset( $var );
if ( class_exists( 'eZContentFunctionCollection' ) && method_exists( 'eZContentFunctionCollection', 'fetchContentNode' ) )
    $var = call_user_func_array( array( new eZContentFunctionCollection(), 'fetchContentNode' ),
      array_values( array(     'node_id' => 96,
    'node_path' => false,
    'language_code' => false,
    'remote_id' => false ) ) );
...
$var = htmlspecialchars( (string) $var1, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
...
$if_cond = eZTemplateLogicOperator::compareCounts( 'gt', $if_cond1, 0 );
...
$var = 'COMPILED' ;
```

Was der Compiler getan hat:

- `fetch( 'content', 'node', ... )` wurde zu einem direkten Aufruf der PHP-Methode der Fetch-Funktion,
  `eZContentFunctionCollection::fetchContentNode()`, mit den aus `function_definition.php` ergänzten Parametern (die Transformation des
  Operators fetch in `eZTemplateExecuteOperator`).
- `|wash` wurde zu `htmlspecialchars()`.
- `gt( 0 )` wurde zum Aufruf eines statischen Helfers des Logikoperators.
- `'compiled'|upcase` wurde zur Kompilierzeit berechnet: Das PHP enthält die Konstante `'COMPILED'`.
- Der Zugriff auf Attribute (`$node.name`) läuft über `compiledFetchAttribute()`, eine Funktion der gemeinsamen `common.php` im selben Ordner,
  die `hasAttribute()` und `attribute()` aufruft, genau wie der Interpreter.
- Die Prüfung von `Level` stoppt ausufernde Rekursion: Mehr als 40 verschachtelte Templates (`$tpl->MaxLevel`) enden mit einer Warnung.

## Hinweise: wie ein Operator zu PHP wird

Eine Operatorklasse kann ihre Operatoren dem Compiler in `operatorTemplateHints()` beschreiben. Aus `eZTemplateStringOperator`
(`lib/eztemplate/classes/eztemplatestringoperator.php`):

```php
$this->UpcaseName => array( 'parameters' => false, 'element-transformation-func' => 'phpMapTransformation' ),
$this->ShortenName => array( 'parameters' => 3, 'element-transformation-func' => 'customMapTransformation' ),
$this->WashName => array( 'parameters' => 1, 'element-transformation-func' => 'washTransformation' ),
...
$hints[$operator]['input'] = true;
$hints[$operator]['output'] = true;
$hints[$operator]['element-transformation'] = true;
$hints[$operator]['transform-parameters'] = true;
```

| Hinweis | Bedeutung |
|---|---|
| `input`, `output` | der Operator nimmt den durch die Pipe kommenden Wert / erzeugt einen Wert |
| `parameters` | wie viele Parameter er nimmt (`false`: keine, `true`: beliebig viele) |
| `element-transformation` | der Operator kann sich selbst in PHP-Code umwandeln |
| `element-transformation-func` | die Methode, die diesen Code zurückgibt |
| `transform-parameters` | seine Parameter werden vor ihm kompiliert |
| `input-as-parameter` | der durch die Pipe kommende Wert wird als erster Parameter übergeben (`always` oder true) |
| `static` | das Ergebnis hängt nur von konstanten Parametern ab und darf zur Kompilierzeit berechnet werden |

Ein Operator **ohne** Hinweise funktioniert kompiliert trotzdem: Der Compiler erzeugt einen Aufruf von `$tpl->processOperator( ... )`, der zur
Laufzeit `modify()` des Operators ausführt, wie es der Interpreter täte. Hinweise machen es nur schneller. Template-Funktionen haben denselben
Mechanismus in `functionTemplateHints()` und `templateNodeTransformation()`; `cache-block` zum Beispiel wird zu einem Aufruf von
`eZTemplateCacheBlock::handle()` kompiliert. Wie Sie einen eigenen Operator schreiben, steht in
[Template-Operatoren schreiben](../ersteller/anwendungsentwickler/template-operatoren-schreiben.md).

## Kompiliert und interpretiert sind nicht immer gleich

Der Transformationscode ist eine zweite Umsetzung jedes Operators, die beiden können also voneinander abweichen. Das Ausführen jedes Beispiels
dieses Abschnitts auf beide Arten auf einer Entwicklungsinstallation fand einen Fall: `choose` mit einer leeren Zeichenkette als Index.

```tpl
{def $empty_image = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.additional_image}
has_content returns {$empty_image.has_content|get_type}
choose: [{$empty_image.has_content|choose( 'no', 'yes' )}]
cond:   [{cond( $empty_image.has_content, 'yes', 'no' )}]
```

Ausgabe:

```text
has_content returns string[0]
choose: [no]
cond:   [no]
```

Das ist die interpretierte Ausgabe. Kompiliert ergab dasselbe Template `choose: []`. Der Interpreter (`eZTemplateLogicOperator::modify()`)
macht aus jedem nicht numerischen Index über seinen Wahrheitswert 0 oder 1, also wählt `''` den Wert `'no'`. Der kompilierte Code ist ein
PHP-`switch ( $index ) { case 0: ...; case 1: ... }`, und PHP 8 hält `''` nicht für gleich `0`, also passt kein Fall, und das Ergebnis ist leer.
`has_content` eines leeren Felds `ezimage` gibt `''` zurück (das `teaser_intro` desselben Artikels, ein `ezxmltext`, gibt `false` zurück, was
beide Modi gleich behandeln). Nutzen Sie für solche Prüfungen `cond()` oder `{if}`, die in beiden Modi übereinstimmen, wie die letzte Zeile
zeigt.

## Wohin die Dateien kommen und wann sie neu gebaut werden

- Ordner: `var/<site>/cache/template/compiled/` (`eZSys::cacheDirectory()` plus `template/compiled`), oder
  `[TemplateSettings] SharedCompiledTemplatesDir`, wenn `ShareCompiledTemplates=enabled` gesetzt ist.
- Name: `<template name>-<md5>.php`. Der md5 wird aus dem Schlüssel des Templates gebildet plus dem internen Zeichensatz, der Locale, ob volle
  URLs genutzt werden, dem Siteaccess, einem eigenen Pagelayout, der Index-Datei und, wenn ein angemeldeter Benutzer eine andere Oberfläche
  gewählt hat (UI-Umschaltung), dieser Wahl (`eZTemplateCompiler::compilationFilename()`). Ein Template kann also viele kompilierte Dateien
  haben, eine pro Siteaccess und Sprache. Mit `ShareCompiledTemplates` zählen nur der Schlüssel und die Locale.
- Neu gebaut, wenn die Template-Datei neuer ist als die kompilierte Datei. Aus einem kompilierten Template eingebundene Templates werden nur
  auf ihr Vorhandensein geprüft, außer `DevelopmentMode=enabled` fügt auch dort eine Prüfung der Änderungszeit hinzu (der erzeugte Code zum
  Einbinden vergleicht `filemtime()` beider Dateien). Mit `DevelopmentMode=disabled`, wie auf einer Entwicklungsinstallation, leeren Sie den Template-Cache, nachdem Sie
  ein eingebundenes Template geändert haben.
- Geleert mit `php bin/php/ezcache.php --clear-id=template` oder der Schaltfläche **Template-Cache** der Cache-Seite; siehe
  [Template-Caches](template-caches.md).
- Vorab kompiliert mit `php bin/php/eztc.php` (siehe [eztc](../../beispiele/befehle/eztc.md)), damit die ersten Besucher nicht für das
  Kompilieren bezahlen.

## Einstellungen

Alle in `site.ini [TemplateSettings]`, Werte auf dem Siteaccess `site` einer Entwicklungsinstallation:

```tpl
TemplateCompile:  {ezini( 'TemplateSettings', 'TemplateCompile' )}
DevelopmentMode:  {ezini( 'TemplateSettings', 'DevelopmentMode' )}
TemplateCache:    {ezini( 'TemplateSettings', 'TemplateCache' )}
NodeTreeCaching:  {ezini( 'TemplateSettings', 'NodeTreeCaching' )}
ViewCaching:      {ezini( 'ContentSettings', 'ViewCaching' )}
CachedViewModes:  {ezini( 'ContentSettings', 'CachedViewModes' )}
```

Ausgabe:

```text
TemplateCompile:  enabled
DevelopmentMode:  disabled
TemplateCache:    enabled
NodeTreeCaching:  disabled
ViewCaching:      enabled
CachedViewModes:  full;sitemap;pdf;googlesitemap
```

| Einstellung | Wirkung |
|---|---|
| `TemplateCompile` | Templates zu PHP kompilieren |
| `DevelopmentMode` | zusätzliche Prüfungen der Änderungszeit für eingebundene Templates |
| `TemplateOptimization` | weitere Optimierungen kompilierter Templates |
| `CompileComments` | Kommentare im erzeugten PHP |
| `CompileNodePlacements` | Datei/Zeile/Spalte für Fehlermeldungen behalten |
| `CompileExecution` | `disabled`: die Dateien erzeugen, aber trotzdem interpretieren |
| `CompileAlwaysGenerate` | auch kompilieren, wenn eine kompilierte Datei existiert |
| `CompileAccumulators`, `CompileTimingPoints` | Debug-Akkumulatoren und Zeitmesspunkte im Code |
| `CompileResourceFallback` | Rückfallcode für die Behandlung von Ressourcen, zur Fehlersuche im Compiler |
| `ShareCompiledTemplates`, `SharedCompiledTemplatesDir` | ein kompilierter Satz für alle Siteaccesses |
| `TemplateCompression` | kompilierte Templates gzip-komprimiert speichern |
| `NodeTreeCaching` | den gelesenen Knotenbaum von Templates cachen |

## Verwandte Seiten

- [Template-Caches](template-caches.md)
- [Kompilierung und Cache von Templates](../designer/vorlagen/template-kompilierung-und-cache.md)
- [Fehler und Fehlersuche](fehler-und-fehlersuche.md)
- [Das Objektmodell in Templates](das-objektmodell-in-templates.md)
- [eztc](../../beispiele/befehle/eztc.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
