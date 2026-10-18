---
title: Patterns in kernel/classes/ini
audience: developer
category: standards
order: 494
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/ini with its design patterns.
keywords: kernel/classes/ini, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/ini

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/ini` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (32)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expIniAction` | interface | [Interface (contract)](interface.md) | `kernel/classes/ini/actions/expiniaction.php:15` | One action of the exp:ini command (get, set, add, rem, ...). |
| `expIniActionActions` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionactions.php:14` | exp:ini actions: the actions of the command, the built-in ones and those extensions register in ini.ini [IniCommandSettings] Actions[], with their al… |
| `expIniActionAdd` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionadd.php:14` | exp:ini add: appends a value to an array (Variable[]=value) in one scope's file, creating the file and the block when they are missing (unless --no-c… |
| `expIniActionBase` | abstract class implements expIniAction | [Template Method / abstract base class](template-method.md) | `kernel/classes/ini/actions/expiniactionbase.php:15` | What the built-in exp:ini actions share: the three describing methods from class constants, the printing of a value as INI lines, and the small check… |
| `expIniActionClear` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionclear.php:13` | exp:ini clear: writes the array reset line Variable[] in one scope's file, so the values the files loaded before it give are dropped and only what fo… |
| `expIniActionCopy` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactioncopy.php:15` | exp:ini copy: copies a setting from one scope's file to another's: a plain variable or a hash entry is set to the same value, a whole array is writte… |
| `expIniActionGet` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionget.php:14` | exp:ini get: the value in effect (every file merged in load order, for the current siteaccess or the one of -s) or, with a scope, the value that scop… |
| `expIniActionList` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionlist.php:14` | exp:ini list: the blocks of an INI file, or the variables of one block, in effect (every file merged, for the current siteaccess) or as one scope's f… |
| `expIniActionMove` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionmove.php:16` | exp:ini move: moves one block (with its comments and blank lines, in order) or every block of a file from one scope to another, typically from settin… |
| `expIniActionMoveAll` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionmoveall.php:15` | exp:ini move-all: moves every INI file of a scope (or those --files names) into another scope, block by block, one transaction per file as exp:ini mo… |
| `expIniActionRegistry` | class | [Factory and static factory methods](factory.md), [Registry](registry.md) | `kernel/classes/ini/actions/expiniactionregistry.php:22` | The actions of the exp:ini command and the scope providers of the settings editor, read from ini.ini [IniCommandSettings]: |
| `expIniActionRem` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionrem.php:14` | exp:ini rem (alias remove): removes a variable from one scope's file, or one value of an array, or one entry of a hash. |
| `expIniActionScopes` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionscopes.php:14` | exp:ini scopes: the scopes this installation has (global, default, every siteaccess, every extension and the extension siteaccess directories, and th… |
| `expIniActionSet` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionset.php:14` | exp:ini set: sets a plain variable (Variable=value) or a hash entry (Variable[key]=value) in one scope's file, creating the file and the block when t… |
| `expIniActionToggle` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactiontoggle.php:14` | exp:ini toggle: flips a switch in one scope's file the way the setting writes it: enableddisabled, truefalse, yesno, onoff, 10 (the case is kept). |
| `expIniActionWhere` | class extends expIniActionBase | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/ini/actions/expiniactionwhere.php:14` | exp:ini where: every file that sets a variable, in the order eZINI loads them (the last one wins for a plain value, arrays add up unless a file reset… |
| `expIniCommand` | class | [Command (console, scripts, cronjobs)](command.md), [Utility (static helper)](utility.md) | `kernel/classes/ini/actions/expinicommand.php:15` | The exp:ini command without its script: splitting the command line, the help texts, finding the action in the registry, running it with an expIniComm… |
| `expIniCommandContext` | class | – | `kernel/classes/ini/actions/expinicommandcontext.php:22` | What an exp:ini action is handed: the arguments after the action's name, the options, the output (text, or one JSON object with --json), the settings… |
| `expIniCoreScopeProvider` | class implements expIniScopeProvider | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ini/expinicorescopeprovider.php:16` | global (settings/override), default (settings/, the shipped defaults, not writable by policy) and one scope per siteaccess: every directory in settin… |
| `expIniEditor` | class | [Factory and static factory methods](factory.md), [Registry](registry.md) | `kernel/classes/ini/expinieditor.php:27` | The INI editor. |
| `expIniException` | class extends Exception | [Exception hierarchy](exception.md), [Factory and static factory methods](factory.md) | `kernel/classes/ini/expiniexception.php:15` | An error of the INI editor. |
| `expIniExtensionScopeProvider` | class implements expIniScopeProvider | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ini/expiniextensionscopeprovider.php:14` | extension: for every directory in extension/ (active or not; the label says which) and extension::siteaccess: for every extension//settings/siteacces… |
| `expIniLocator` | class | [Utility (static helper)](utility.md) | `kernel/classes/ini/expinilocator.php:25` | Every file that sets a variable, in the order eZINI loads them for a siteaccess, and the effective value. |
| `expIniMover` | class | – | `kernel/classes/ini/actions/expinimover.php:20` | Moves settings blocks from one scope's file to another's, for exp:ini move and move-all: a block with its comments and blank lines in order, every bl… |
| `expIniScope` | class | – | `kernel/classes/ini/expiniscope.php:22` | One INI scope: a settings directory and the rule for the file name in it. |
| `expIniScopeProvider` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/ini/expiniscopeprovider.php:20` | A source of INI scopes (places an INI file can be written). |
| `expIniWriter` | class | [Factory and static factory methods](factory.md) | `kernel/classes/ini/expiniwriter.php:26` | One INI file as a list of lines, read exactly the way eZINI::parseFile() reads it, and changed one line at a time: comments, blank lines, the order o… |
| `expIniWriteResult` | class | – | `kernel/classes/ini/expiniwriteresult.php:13` | The outcome of one expIniEditor::save(). |
| `expSettingsChain` | class | [Chain of Responsibility](chain.md), [Factory and static factory methods](factory.md) | `kernel/classes/ini/expsettingschain.php:32` | The override chain of one INI file, built once from the files eZINI reads for it, in eZINI's own order. |
| `expSettingsPage` | class | [Utility (static helper)](utility.md) | `kernel/classes/ini/expsettingspage.php:18` | The rows, figures, searches and comparisons of the settings pages, and what a write is followed by. |
| `expSettingsSecretRule` | class | [Factory and static factory methods](factory.md) | `kernel/classes/ini/expsettingssecretrule.php:30` | The rule the settings pages (settings/view, settings/edit) mask values by. |
| `expSettingsTarget` | class | [Utility (static helper)](utility.md) | `kernel/classes/ini/expsettingstarget.php:19` | The checks between a request and a settings file. |
