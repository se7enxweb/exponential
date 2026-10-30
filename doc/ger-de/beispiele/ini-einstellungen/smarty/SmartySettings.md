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

> Where Smarty 5 is, when it is not installed with Composer (smarty/smarty ^5) in the  
> installation's vendor/: the directory that holds src/Smarty.php, relative to the  
> installation root or absolute. Empty: Composer's autoload only.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
LibraryPath=
*/ ?>
```

## CompileCheck

> enabled: Smarty recompiles a .stpl when it changed. disabled: never checks (production;  
> clear the template cache after a template change).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
CompileCheck=enabled
*/ ?>
```

## Debugging

> enabled: Smarty's debug console.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
Debugging=disabled
*/ ?>
```

## FunctionList

> Exponential template functions callable as Smarty tags with named parameters,  
> e.g. {attribute\_view\_gui attribute=$node.data\_map.body}  

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

> Template operators that always take their arguments as parameters and never read the value they are  
> applied to, for operators without template hints (the bridge cannot see that from the operator). With it,  
> {myop('a', 'b')} calls the operator with the parameters 'a' and 'b', as myop( 'a', 'b' ) does in a .tpl;  
> without it, 'a' would be its input. The kernel's own (ezhttp, ezhttp\_hasvariable, has\_role, topmenu ...) are  
> built in. Read once per process: a change needs a deploy (a restart of the PHP workers).  

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

> Smarty security policy for .stpl templates: enabled or disabled. Enabled, a template is compiled with the  
> lists below and refused at compile time when it uses something they do not allow. A template compiled  
> with the policy is kept apart from one compiled without it (<cache dir>/smarty/compile-secure).  
> See doc/eng-us/guides/smarty-templates-security.md.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
Security=enabled
*/ ?>
```

## SecurityAllowedTagList

> Smarty's own tags a template may use; empty: all of them except SecurityDisabledTagList. The tags of  
> Exponential's template functions ({attribute\_view\_gui}, {expinclude}, {expcacheblock} ...) and the  
> functions a template defines with {function} are always allowed unless they are disabled below.  
> A closing tag ({/if}) follows its opening tag.  

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

> Tags never allowed, Smarty's or Exponential's: {eval} (runs a string as a template), {fetch} (reads a file  
> or a URL), {math} (evaluates an expression), {debug} (shows every variable), {config\_load} (reads  
> configuration files), {html\_image} (reads image files).  

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

> Smarty's own modifiers a template may use; empty: all of them except SecurityDisabledModifierList.  
> Exponential's operators ({$text|wash}, {concat($a, $b)}, {fetch('content', 'node', ...)}) are always  
> allowed unless they are disabled below.  

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

> Modifiers never allowed, Smarty's or Exponential's operators.  

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

> $smarty.<name> special variables a template may not read: the request and the environment  
> ($smarty.get, .post, .cookies, .session, .server, .env, .request), PHP constants ($smarty.const) and the  
> template object ($smarty.template\_object, which reaches the Smarty instance and its settings).  

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

> PHP constants in templates ({if $x == E\_ALL}, {$smarty.const.X}): enabled or disabled.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
SecurityConstants=disabled
*/ ?>
```

## SecurityStaticClassList

> Classes whose static methods and properties a template may use ({Foo::bar()}); empty: none.  

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

> Stream wrappers a template may be loaded from ({include file='php://...'}); empty: none. Template files  
> themselves are no stream.  

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

> Methods and properties of objects ({$node->expSmartyObject()->remove()}): enabled or disabled. Disabled, a  
> template that uses -> in a tag is refused; templates read attributes as $node.name, which stays allowed.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
SecurityObjectAccess=disabled
*/ ?>
```

## SecurityTrustedDirList

> Directories, besides the template directories of the design bases, that templates may be loaded from  
> ({include file=...}, {exp\_smarty\_render( '<file>' )}), relative to the installation root or absolute.  

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
