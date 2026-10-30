---
title: twig.ini [TwigSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TwigSettings] in twig.ini: LibraryPath, AutoReload, Debug, StrictVariables, Autoescape, TemplateSuffixList, WhileLoopLimit, FunctionList ..."
keywords: Einstellung, twig.ini, TwigSettings, INI, setting, LibraryPath, AutoReload, Debug, StrictVariables, Autoescape, TemplateSuffixList, WhileLoopLimit, FunctionList, ParameterOperatorList, Profile, Security, SecurityAllowedTagList, SecurityAllowedFilterList, SecurityAllowedFunctionList, SecurityAllowedMethodList, SecurityAllowedPropertyList
type: generated
generated: exp:help:examples
---
# twig.ini [TwigSettings]

Der Block `[TwigSettings]` von `settings/twig.ini`, mit 16 Einstellung(en).

Bearbeiten Sie `settings/twig.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/twig.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/twig.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/twig.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LibraryPath

> Woher Twig 3 geladen wird, wenn der Composer-Autoload der Installation kein twig/twig  
> hat: ein Verzeichnis mit vendor/autoload.php (ein Composer-Verzeichnis, das twig/twig  
> verlangt) oder das Paket twig/twig selbst (das Verzeichnis mit src/Environment.php).  
> Relativ zum Installationsverzeichnis oder absolut.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
LibraryPath=
*/ ?>
```

## AutoReload

> auto\_reload von Twig: Ein geändertes .twig wird neu kompiliert. disabled spart auf  
> Produktionssystemen eine Dateiprüfung je Template (leeren Sie nach einem Deploy die  
> Template-Caches).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
AutoReload=enabled
*/ ?>
```

## Debug

> Der Debug-Modus von Twig (die Funktion dump(), Debug-Informationen im kompilierten Code).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Debug=disabled
*/ ?>
```

## StrictVariables

> disabled: Eine undefinierte Variable oder ein undefiniertes Attribut druckt nichts, wie in einem .tpl.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
StrictVariables=disabled
*/ ?>
```

## Autoescape

> disabled: Nichts wird maskiert, wenn das Template es nicht sagt (|wash), wie in einem  
> .tpl, sodass umgewandelte Templates drucken, was das .tpl druckte. Ein Siteaccess kann  
> es für handgeschriebene Templates einschalten (html); siehe die Sicherheitsanleitung.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Autoescape=disabled
*/ ?>
```

## TemplateSuffixList

> Dateiendungen von Templates, die Twig rendert (expTemplateEngines prüft die längste  
> zuerst). Dieselbe Liste wie site.ini \[TemplateBridgeSettings\] EngineSuffixList\[\].  

Standard: eine Liste mit 2: `.twig`, `.html.twig`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "TemplateSuffixList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TemplateSuffixList[]
TemplateSuffixList[]=.twig
TemplateSuffixList[]=.html.twig
*/ ?>
```

## WhileLoopLimit

> Die meisten Runden, die eine umgewandelte {while}- oder {do}-Schleife läuft ({% expwhile %});  
> eine Schleife, die sie erreicht, endet mit einem Debug-Hinweis.  

Standard: `10000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
WhileLoopLimit=10000
*/ ?>
```

## FunctionList

> Template-Funktionen, die als Twig-Funktionen mit einem Hash benannter Parameter aufrufbar  
> sind, {{ attribute\_view\_gui({attribute: a}) }}. Jede andere Template-Funktion ohne  
> Kinder wird ebenfalls gebrückt; Funktionen mit Kindern werden zu Tags  
> ({% explblock name='x' %}...{% endexplblock %}).  

Standard: eine Liste mit 11: `attribute_view_gui`, `attribute_edit_gui`, `attribute_result_gui`, `attribute_pdf_gui`, `node_view_gui`, `content_view_gui`, `content_version_view_gui`, `class_attribute_view_gui`, `class_attribute_edit_gui`, `related_view_gui`, `shop_account_view_gui`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "FunctionList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FunctionList[]
FunctionList[]=attribute_view_gui
FunctionList[]=attribute_edit_gui
FunctionList[]=attribute_result_gui
FunctionList[]=attribute_pdf_gui
FunctionList[]=node_view_gui
FunctionList[]=content_view_gui
FunctionList[]=content_version_view_gui
FunctionList[]=class_attribute_view_gui
FunctionList[]=class_attribute_edit_gui
FunctionList[]=related_view_gui
FunctionList[]=shop_account_view_gui
*/ ?>
```

## ParameterOperatorList

> Operatoren ohne Template-Hinweise, die nur ihre Parameter lesen, nie ihre Eingabe  
> (zusätzlich zu site.ini \[TemplateBridgeSettings\] ParameterOperatorList\[\], der Liste für  
> jede Engine).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "ParameterOperatorList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ParameterOperatorList[]
*/ ?>
```

## Profile

> enabled (und eingeschaltete Debug-Ausgabe): Die Zähler der Debug-Leiste zeigen, wohin die  
> Twig-Zeit einer Seite geht (Renderings, Laden kompilierter Templates, Operatoren,  
> Template-Funktionen, Includes, Verpacken der Werte).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Profile=disabled
*/ ?>
```

## Security

> Die Sandbox (Twigs SandboxExtension mit der Richtlinie von expTwigSecurity).  
> enabled: Ein Template darf nur die hier erlaubten Tags, Filter und Funktionen benutzen;  
> keine Methode eines Objekts kann aufgerufen und keine Eigenschaft gelesen werden (Werte  
> des Template-Systems werden über ihre Attribute gelesen, was erlaubt ist).  
> Getrennt von den ohne sie kompilierten Templates kompiliert (<cache>/twig/compile-secure).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Security=enabled
*/ ?>
```

## SecurityAllowedTagList

> Erlaubte Tags (leer: Twigs eigene Tags unten und jedes exp\*-Tag von lib/twig).  

Standard: eine Liste mit 16: `if`, `for`, `set`, `apply`, `block`, `extends`, `include`, `embed`, `macro`, `import`, `from`, `use` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "SecurityAllowedTagList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityAllowedTagList[]
SecurityAllowedTagList[]=if
SecurityAllowedTagList[]=for
SecurityAllowedTagList[]=set
SecurityAllowedTagList[]=apply
SecurityAllowedTagList[]=block
SecurityAllowedTagList[]=extends
SecurityAllowedTagList[]=include
SecurityAllowedTagList[]=embed
SecurityAllowedTagList[]=macro
SecurityAllowedTagList[]=import
SecurityAllowedTagList[]=from
SecurityAllowedTagList[]=use
SecurityAllowedTagList[]=with
SecurityAllowedTagList[]=verbatim
SecurityAllowedTagList[]=guard
SecurityAllowedTagList[]=types
*/ ?>
```

## SecurityAllowedFilterList

> Twig-Filter, die zusätzlich zu jedem gebrückten Operator erlaubt sind.  

Standard: eine Liste mit 31: `abs`, `batch`, `capitalize`, `column`, `date`, `default`, `e`, `escape`, `first`, `format`, `join`, `json_encode` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "SecurityAllowedFilterList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityAllowedFilterList[]
SecurityAllowedFilterList[]=abs
SecurityAllowedFilterList[]=batch
SecurityAllowedFilterList[]=capitalize
SecurityAllowedFilterList[]=column
SecurityAllowedFilterList[]=date
SecurityAllowedFilterList[]=default
SecurityAllowedFilterList[]=e
SecurityAllowedFilterList[]=escape
SecurityAllowedFilterList[]=first
SecurityAllowedFilterList[]=format
SecurityAllowedFilterList[]=join
SecurityAllowedFilterList[]=json_encode
SecurityAllowedFilterList[]=keys
SecurityAllowedFilterList[]=last
SecurityAllowedFilterList[]=length
SecurityAllowedFilterList[]=lower
SecurityAllowedFilterList[]=merge
SecurityAllowedFilterList[]=nl2br
SecurityAllowedFilterList[]=number_format
SecurityAllowedFilterList[]=raw
# ... 11 weitere in der ausgelieferten Datei
*/ ?>
```

## SecurityAllowedFunctionList

> Twig-Funktionen, die zusätzlich zu jedem gebrückten Operator und jeder Template-Funktion erlaubt sind.  

Standard: eine Liste mit 8: `attribute`, `block`, `cycle`, `max`, `min`, `parent`, `random`, `range`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "SecurityAllowedFunctionList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityAllowedFunctionList[]
SecurityAllowedFunctionList[]=attribute
SecurityAllowedFunctionList[]=block
SecurityAllowedFunctionList[]=cycle
SecurityAllowedFunctionList[]=max
SecurityAllowedFunctionList[]=min
SecurityAllowedFunctionList[]=parent
SecurityAllowedFunctionList[]=random
SecurityAllowedFunctionList[]=range
*/ ?>
```

## SecurityAllowedMethodList

> Erlaubte Methoden als Klasse::methode (standardmäßig keine: Werte werden über Attribute gelesen).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "SecurityAllowedMethodList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityAllowedMethodList[]
*/ ?>
```

## SecurityAllowedPropertyList

> Erlaubte Eigenschaften als Klasse::eigenschaft (standardmäßig keine).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# ein leeres "SecurityAllowedPropertyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityAllowedPropertyList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von twig.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/twig.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
