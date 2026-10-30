---
title: smarty.ini [SmartySettings]
audience: developer, admin
category: examples
summary: "The settings of [SmartySettings] in smarty.ini: LibraryPath, CompileCheck, Debugging, FunctionList, ParameterOperatorList, Security, SecurityAllowedTagList, SecurityDisabledTagList ..."
keywords: smarty.ini, SmartySettings, INI, setting, LibraryPath, CompileCheck, Debugging, FunctionList, ParameterOperatorList, Security, SecurityAllowedTagList, SecurityDisabledTagList, SecurityAllowedModifierList, SecurityDisabledModifierList, SecurityDisabledSpecialVariableList, SecurityConstants, SecurityStaticClassList, SecurityStreamList, SecurityObjectAccess, SecurityTrustedDirList
type: generated
generated: exp:help:examples
---
# smarty.ini [SmartySettings]

The block `[SmartySettings]` of `settings/smarty.ini`, with 16 setting(s).

Never edit `settings/smarty.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/smarty.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/smarty.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/smarty.ini.append.php` in an extension (read when the extension is active).

## LibraryPath

> Where Smarty 5 is, when it is not installed with Composer (smarty/smarty ^5) in the  
> installation's vendor/: the directory that holds src/Smarty.php, relative to the  
> installation root or absolute. Empty: Composer's autoload only.  

Default: empty.

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

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
CompileCheck=enabled
*/ ?>
```

## Debugging

> enabled: Smarty's debug console.  

Default: `disabled`.

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

Default: a list of 11: `attribute_view_gui`, `attribute_edit_gui`, `attribute_result_gui`, `attribute_pdf_gui`, `node_view_gui`, `content_view_gui`, `content_version_view_gui`, `class_attribute_view_gui`, `class_attribute_edit_gui`, `related_view_gui`, `shop_account_view_gui`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "FunctionList[]" first replaces the shipped list; without it the items are added
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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "ParameterOperatorList[]" first replaces the shipped list; without it the items are added
ParameterOperatorList[]
*/ ?>
```

## Security

> Smarty security policy for .stpl templates: enabled or disabled. Enabled, a template is compiled with the  
> lists below and refused at compile time when it uses something they do not allow. A template compiled  
> with the policy is kept apart from one compiled without it (<cache dir>/smarty/compile-secure).  
> See doc/eng-us/guides/smarty-templates-security.md.  

Default: `enabled`.

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

Default: a list of 32: `if`, `elseif`, `else`, `foreach`, `foreachelse`, `for`, `forelse`, `while`, `section`, `sectionelse`, `break`, `continue` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityAllowedTagList[]" first replaces the shipped list; without it the items are added
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
# ... 12 more in the shipped file
*/ ?>
```

## SecurityDisabledTagList

> Tags never allowed, Smarty's or Exponential's: {eval} (runs a string as a template), {fetch} (reads a file  
> or a URL), {math} (evaluates an expression), {debug} (shows every variable), {config\_load} (reads  
> configuration files), {html\_image} (reads image files).  

Default: a list of 6: `eval`, `fetch`, `math`, `debug`, `config_load`, `html_image`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityDisabledTagList[]" first replaces the shipped list; without it the items are added
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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityAllowedModifierList[]" first replaces the shipped list; without it the items are added
SecurityAllowedModifierList[]
*/ ?>
```

## SecurityDisabledModifierList

> Modifiers never allowed, Smarty's or Exponential's operators.  

Default: a list of 1: `debug_print_var`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityDisabledModifierList[]" first replaces the shipped list; without it the items are added
SecurityDisabledModifierList[]
SecurityDisabledModifierList[]=debug_print_var
*/ ?>
```

## SecurityDisabledSpecialVariableList

> $smarty.<name> special variables a template may not read: the request and the environment  
> ($smarty.get, .post, .cookies, .session, .server, .env, .request), PHP constants ($smarty.const) and the  
> template object ($smarty.template\_object, which reaches the Smarty instance and its settings).  

Default: a list of 9: `get`, `post`, `cookies`, `session`, `server`, `env`, `request`, `const`, `template_object`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityDisabledSpecialVariableList[]" first replaces the shipped list; without it the items are added
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

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
SecurityConstants=disabled
*/ ?>
```

## SecurityStaticClassList

> Classes whose static methods and properties a template may use ({Foo::bar()}); empty: none.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityStaticClassList[]" first replaces the shipped list; without it the items are added
SecurityStaticClassList[]
*/ ?>
```

## SecurityStreamList

> Stream wrappers a template may be loaded from ({include file='php://...'}); empty: none. Template files  
> themselves are no stream.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityStreamList[]" first replaces the shipped list; without it the items are added
SecurityStreamList[]
*/ ?>
```

## SecurityObjectAccess

> Methods and properties of objects ({$node->expSmartyObject()->remove()}): enabled or disabled. Disabled, a  
> template that uses -> in a tag is refused; templates read attributes as $node.name, which stays allowed.  

Default: `disabled`.

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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/smarty.ini.append.php
[SmartySettings]
# an empty "SecurityTrustedDirList[]" first replaces the shipped list; without it the items are added
SecurityTrustedDirList[]
*/ ?>
```

## Related

- [All blocks of smarty.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/smarty.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
