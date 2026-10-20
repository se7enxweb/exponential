---
title: Taking inventory of an old extension
audience: developer, app-creator
category: extension-development
order: 30
summary: Find out what an old extension contains and what will need work before you change a line: files, settings, PHP entry points, JavaScript, inline code, database tables, with the commands to list each.
keywords: inventory, old extension, audit, grep, php -l, settings, modules, operators, datatypes, inline scripts, YUI, jQuery, database tables
type: help
---
# Taking inventory of an old extension

Part 4, chapter 3. Before you fix anything, find out what you have. This chapter gives you a set of commands that
each answer one question about an extension, and a table to write the answers into. Run them from the root of your
development installation, with the extension in `extension/<name>/`. In the examples the extension is called
`myoldext`; use your own name.

## 1. What is in it?

```bash
cd extension/myoldext
find . -type f -not -path './.git/*' | sed 's|/[^/]*$||' | sort | uniq -c
```

This prints each folder and how many files it holds. Compare the folder names with
[The extension directory layout](../part-3-extension-developer/extension-directory-layout.md). Folders the kernel
does not know (`lib/`, `src/`, `vendor/`) belong to the extension's own code; folders such as `kernel/` or `lib/ezutils/`
inside an extension usually mean it replaces kernel files, which is the hardest kind of upgrade
([Kernel overrides: the last resort](../part-3-extension-developer/kernel-overrides-the-last-resort.md)).

## 2. Who made it, which version, which license?

```bash
cat ezinfo.php extension.xml composer.json LICENSE* COPYING* 2>/dev/null | head -80
git log --format='%h %ad %an %s' --date=short | tail -5
git log --format='%h %ad %an %s' --date=short | head -5
```

Write down the license and the authors before anything else. If there is no license file and no license line, you
may not publish your changes; ask the author first
([Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md)).

## 3. Where does it plug into the kernel?

Every extension point is registered by a setting. Listing the settings files and their sections tells you which
mechanisms it uses:

```bash
ls settings/
grep -h '^\[' settings/*.ini* | sort | uniq -c
grep -rhn 'ExtensionDirectories\|DesignExtensions\|ModuleList\|ExtensionRepositories\|AvailableDataTypes\|AvailableEventTypes' settings/
```

Then the files with fixed names:

```bash
ls modules/*/module.php autoloads/eztemplateautoload.php cronjobs/*.php bin/php/*.php 2>/dev/null
```

Fill in this table:

| Mechanism | Found where | Count |
|---|---|---|
| Modules and views | `modules/*/module.php` | |
| Template operators | `autoloads/eztemplateautoload.php` | |
| Fetch functions | `modules/*/function_definition.php` | |
| Datatypes | `settings/content.ini.append.php` `[DataTypeSettings]` | |
| Workflow event types | `settings/workflow.ini.append.php` `[EventSettings]` | |
| Cronjob parts | `cronjobs/*.php` and `settings/cronjob.ini.append.php` | |
| CLI scripts | `bin/php/*.php` | |
| Designs | `design/*/` and `settings/design.ini.append.php` | |
| Translations | `translations/*/translation.ts` | |
| Own database tables | `sql/*/` or `share/db_schema.dba` | |

Each row has a chapter in [Part 3](../part-3-extension-developer/README.md), and you will check each of them when it
runs.

## 4. Does the PHP parse?

```bash
find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
```

No output means every file parses with the PHP on your path. Run it again with each PHP version you support; on a
server with several PHP builds the binary has its own path (for example `/usr/bin/php8.0` or
`/opt/<vendor>/php/8.0/bin/php`, depending on how PHP was installed).

> **Note**
> `php -l` only checks syntax. A file can parse and still stop at run time, for example when it calls a removed
> function. Steps 5 and 6 find those.

## 5. Which old PHP does it use?

These searches find the patterns PHP 8 removed or changed. Each hit is a line to look at, not always a line to change.

```bash
# removed functions
grep -rnE '\b(each|create_function|split|spliti|ereg|eregi|ereg_replace|eregi_replace|mysql_[a-z_]+|money_format|get_magic_quotes_gpc|get_magic_quotes_runtime)\s*\(' --include='*.php' .

# deprecated in 8.1 to 8.5
grep -rnE '\b(utf8_encode|utf8_decode|strftime|gmstrftime|curl_close|imagedestroy|finfo_close|xml_parser_free)\s*\(' --include='*.php' .
grep -rn 'FILTER_SANITIZE_STRING\|\$http_response_header' --include='*.php' .

# parameters typed with a class and defaulting to null, without a question mark
grep -rnE '\(\s*[A-Z][A-Za-z0-9_]+\s+\$[A-Za-z0-9_]+\s*=\s*null|,\s*[A-Z][A-Za-z0-9_]+\s+\$[A-Za-z0-9_]+\s*=\s*null' --include='*.php' .

# ezinfo.php with bare keys
grep -nE '^\s*(return array\(\s*)?(Name|Version|Copyright|License|Info_url|info_url)\s*=>' ezinfo.php
```

For old-style constructors, list each class and look for a method with the same name:

```bash
grep -rhoE '^\s*(abstract\s+|final\s+)?class\s+[A-Za-z0-9_]+' --include='*.php' . | awk '{print $NF}' | while read c; do
  grep -rlnE "function\s+$c\s*\(" --include='*.php' . | sed "s|^|$c: |"
done
```

Every line it prints is a class whose constructor PHP 8 no longer calls
([Old-style constructors and static calls](old-constructors-and-static-calls.md)).

## 6. What does it do in the browser?

```bash
# inline scripts and event handler attributes in templates
grep -rnE '<script[^>]*>[^<]|\son[a-z]+\s*=|javascript:' --include='*.tpl' design/

# inline styles
grep -rnE '<style|\sstyle\s*=' --include='*.tpl' design/

# old libraries
grep -rnE 'ezjsc::yui|YUI\(|YAHOO\.|jQuery\.browser|\.live\(|\.size\(\)|\.andSelf\(|jQuery\.camelCase' design/ settings/

# copies of libraries shipped with the extension
find design -name '*.js' | xargs -r grep -lE 'jQuery JavaScript Library v|YUI [0-9]' 2>/dev/null
```

The first two searches find most of what a Content Security Policy without `'unsafe-inline'` blocks; the browser
console finds the rest. Write down the count per template.

## 7. Does it have its own tables?

```bash
ls sql/*/ share/*.dba 2>/dev/null
grep -rhoE "'name'\s*=>\s*'[a-z0-9_]+'" classes/ | sort -u | head
```

The second line finds the table names in `definition()` methods of `eZPersistentObject` classes. Old extensions
often ship only a `sql/mysql/schema.sql`; Exponential 6 supports several database engines, so plan for a `.dba` file
([Database schema and .dba files](../part-3-extension-developer/database-schema-and-dba-files.md)).

## 8. Write it down

An inventory is only useful if you keep it. Copy this into an issue of your repository:

```text
Extension: myoldext            License: GPL-2.0-or-later      Authors: (from ezinfo.php and git log)
Last release: 1.2 (2012)       PHP files: 41                  Templates: 23

Loads on PHP 8.0: no (2 parse errors)
Old constructors: 4 classes     Removed functions: each() x2, split() x1
Implicitly nullable parameters: 7
Inline scripts: 5 templates     Event attributes: 12           Inline styles: 9
Libraries: YUI 2 (calendar)     Own tables: 2 (MySQL only)
Kernel overrides: none

Estimate: steps 1-4 one day, steps 5-6 two days
```

## What can go wrong

| Symptom | Cause | What to do |
|---|---|---|
| `grep` finds hundreds of hits in `vendor/` or a bundled library | you searched third-party code | exclude it; upgrade the library as a whole instead of patching it |
| `php -l` passes but the admin shows a blank page | a fatal error at run time | look at `var/log/error.log` and the PHP error log |
| The constructor search prints a method that is not a constructor | a method happens to share the class name in another class | check by hand; only a method in the same class counts |

## Related pages

- Previous: [The upgrade checklist](the-upgrade-checklist.md)
- Next: [PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md)
- [Evaluating an old extension](../part-1-from-user-to-developer/evaluating-an-old-extension.md)
- [Reading the code you have](../part-1-from-user-to-developer/reading-the-code-you-have.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
