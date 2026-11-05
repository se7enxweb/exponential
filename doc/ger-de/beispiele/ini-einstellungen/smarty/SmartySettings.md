---
title: smarty.ini [SmartySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SmartySettings] in smarty.ini: LibraryPath, CompileCheck, Debugging, FunctionList, ParameterOperatorList, Security, SecurityAllowedTagList, SecurityDisabledTagList ..."
keywords: Einstellung, smarty.ini, SmartySettings, INI, setting, LibraryPath, CompileCheck, Debugging, FunctionList, ParameterOperatorList, Security, SecurityAllowedTagList, SecurityDisabledTagList, SecurityAllowedModifierList, SecurityDisabledModifierList, SecurityDisabledSpecialVariableList, SecurityConstants, SecurityStaticClassList, SecurityStreamList, SecurityObjectAccess, SecurityTrustedDirList
type: generated
generated: exp:help:examples
---
# smarty.ini [SmartySettings]

Der Block `[SmartySettings]` von `settings/smarty.ini`, mit 16 Einstellung(en).

Bearbeiten Sie `settings/smarty.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/smarty.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/smarty.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/smarty.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LibraryPath

> Wo Smarty 5 liegt, wenn es nicht mit Composer (smarty/smarty ^5) im  
> vendor/ der Installation installiert ist: das Verzeichnis, das src/Smarty.php enthält, relativ zum  
> Wurzelverzeichnis der Installation oder absolut. Leer: nur das Autoload von Composer.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
LibraryPath=
*/ ?>
```

## CompileCheck

> enabled: Smarty kompiliert eine .stpl neu, wenn sie sich geändert hat. disabled: prüft nie (Produktivbetrieb;  
> leeren Sie nach einer Template-Änderung den Template-Cache).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
CompileCheck=enabled
*/ ?>
```

## Debugging

> enabled: die Debug-Konsole von Smarty.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
Debugging=disabled
*/ ?>
```

## FunctionList

> Exponential-Template-Funktionen, die als Smarty-Tags mit benannten Parametern aufrufbar sind,  
> z. B. {attribute\_view\_gui attribute=$node.data\_map.body}  

Standard: eine Liste mit 11: `attribute_view_gui`, `attribute_edit_gui`, `attribute_result_gui`, `attribute_pdf_gui`, `node_view_gui`, `content_view_gui`, `content_version_view_gui`, `class_attribute_view_gui`, `class_attribute_edit_gui`, `related_view_gui`, `shop_account_view_gui`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
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

> Template-Operatoren, die ihre Argumente immer als Parameter nehmen und nie den Wert lesen, auf den sie  
> angewendet werden, für Operatoren ohne Template-Hinweise (die Brücke kann das dem Operator nicht ansehen). Damit  
> ruft {myop('a', 'b')} den Operator mit den Parametern 'a' und 'b' auf, wie myop( 'a', 'b' ) in einer .tpl;  
> ohne es wäre 'a' seine Eingabe. Die eigenen des Kernels (ezhttp, ezhttp\_hasvariable, has\_role, topmenu ...) sind  
> eingebaut. Einmal je Prozess gelesen: Eine Änderung braucht ein Deploy (einen Neustart der PHP-Worker).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "ParameterOperatorList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ParameterOperatorList[]
*/ ?>
```

## Security

> Smarty-Sicherheitsrichtlinie für .stpl-Templates: enabled oder disabled. Eingeschaltet wird ein Template mit den  
> Listen unten kompiliert und beim Kompilieren abgelehnt, wenn es etwas verwendet, das sie nicht erlauben. Ein Template, das  
> mit der Richtlinie kompiliert wurde, wird getrennt von einem ohne sie kompilierten aufbewahrt (<cache dir>/smarty/compile-secure).  
> Siehe doc/eng-us/guides/smarty-templates-security.md.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
Security=enabled
*/ ?>
```

## SecurityAllowedTagList

> Die eigenen Tags von Smarty, die ein Template verwenden darf; leer: alle außer SecurityDisabledTagList. Die Tags der  
> Template-Funktionen von Exponential ({attribute\_view\_gui}, {expinclude}, {expcacheblock} ...) und die  
> Funktionen, die ein Template mit {function} definiert, sind immer erlaubt, solange sie unten nicht abgeschaltet sind.  
> Ein schließender Tag ({/if}) folgt seinem öffnenden Tag.  

Standard: eine Liste mit 32: `if`, `elseif`, `else`, `foreach`, `foreachelse`, `for`, `forelse`, `while`, `section`, `sectionelse`, `break`, `continue` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityAllowedTagList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityAllowedTagList[]
SecurityAllowedTagList[]=if
SecurityAllowedTagList[]=elseif
SecurityAllowedTagList[]=else
SecurityAllowedTagList[]=foreach
SecurityAllowedTagList[]=foreachelse
SecurityAllowedTagList[]=for
SecurityAllowedTagList[]=forelse
SecurityAllowedTagList[]=while
SecurityAllowedTagList[]=section
SecurityAllowedTagList[]=sectionelse
SecurityAllowedTagList[]=break
SecurityAllowedTagList[]=continue
SecurityAllowedTagList[]=capture
SecurityAllowedTagList[]=ldelim
SecurityAllowedTagList[]=rdelim
SecurityAllowedTagList[]=nocache
SecurityAllowedTagList[]=block
SecurityAllowedTagList[]=extends
SecurityAllowedTagList[]=include
SecurityAllowedTagList[]=function
# ... 12 weitere in der ausgelieferten Datei
*/ ?>
```

## SecurityDisabledTagList

> Tags, die nie erlaubt sind, die von Smarty oder von Exponential: {eval} (führt einen String als Template aus), {fetch} (liest eine Datei  
> oder eine URL), {math} (wertet einen Ausdruck aus), {debug} (zeigt jede Variable), {config\_load} (liest  
> Konfigurationsdateien), {html\_image} (liest Bilddateien).  

Standard: eine Liste mit 6: `eval`, `fetch`, `math`, `debug`, `config_load`, `html_image`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityDisabledTagList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityDisabledTagList[]
SecurityDisabledTagList[]=eval
SecurityDisabledTagList[]=fetch
SecurityDisabledTagList[]=math
SecurityDisabledTagList[]=debug
SecurityDisabledTagList[]=config_load
SecurityDisabledTagList[]=html_image
*/ ?>
```

## SecurityAllowedModifierList

> Die eigenen Modifikatoren von Smarty, die ein Template verwenden darf; leer: alle außer SecurityDisabledModifierList.  
> Die Operatoren von Exponential ({$text|wash}, {concat($a, $b)}, {fetch('content', 'node', ...)}) sind immer  
> erlaubt, solange sie unten nicht abgeschaltet sind.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityAllowedModifierList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityAllowedModifierList[]
*/ ?>
```

## SecurityDisabledModifierList

> Modifikatoren, die nie erlaubt sind, die von Smarty oder Operatoren von Exponential.  

Standard: eine Liste mit 1: `debug_print_var`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityDisabledModifierList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityDisabledModifierList[]
SecurityDisabledModifierList[]=debug_print_var
*/ ?>
```

## SecurityDisabledSpecialVariableList

> Spezialvariablen $smarty.<name>, die ein Template nicht lesen darf: die Anfrage und die Umgebung  
> ($smarty.get, .post, .cookies, .session, .server, .env, .request), PHP-Konstanten ($smarty.const) und das  
> Template-Objekt ($smarty.template\_object, das die Smarty-Instanz und ihre Einstellungen erreicht).  

Standard: eine Liste mit 9: `get`, `post`, `cookies`, `session`, `server`, `env`, `request`, `const`, `template_object`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityDisabledSpecialVariableList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityDisabledSpecialVariableList[]
SecurityDisabledSpecialVariableList[]=get
SecurityDisabledSpecialVariableList[]=post
SecurityDisabledSpecialVariableList[]=cookies
SecurityDisabledSpecialVariableList[]=session
SecurityDisabledSpecialVariableList[]=server
SecurityDisabledSpecialVariableList[]=env
SecurityDisabledSpecialVariableList[]=request
SecurityDisabledSpecialVariableList[]=const
SecurityDisabledSpecialVariableList[]=template_object
*/ ?>
```

## SecurityConstants

> PHP-Konstanten in Templates ({if $x == E\_ALL}, {$smarty.const.X}): enabled oder disabled.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
SecurityConstants=disabled
*/ ?>
```

## SecurityStaticClassList

> Klassen, deren statische Methoden und Eigenschaften ein Template verwenden darf ({Foo::bar()}); leer: keine.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityStaticClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityStaticClassList[]
*/ ?>
```

## SecurityStreamList

> Stream-Wrapper, aus denen ein Template geladen werden darf ({include file='php://...'}); leer: keine. Template-Dateien  
> selbst sind kein Stream.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityStreamList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityStreamList[]
*/ ?>
```

## SecurityObjectAccess

> Methoden und Eigenschaften von Objekten ({$node->expSmartyObject()->remove()}): enabled oder disabled. Abgeschaltet wird ein  
> Template abgelehnt, das -> in einem Tag verwendet; Templates lesen Attribute als $node.name, was erlaubt bleibt.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
SecurityObjectAccess=disabled
*/ ?>
```

## SecurityTrustedDirList

> Verzeichnisse, neben den Template-Verzeichnissen der Design-Basen, aus denen Templates geladen werden dürfen  
> ({include file=...}, {exp\_smarty\_render( '<file>' )}), relativ zum Wurzelverzeichnis der Installation oder absolut.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# ein leeres "SecurityTrustedDirList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityTrustedDirList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von smarty.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/smarty.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
