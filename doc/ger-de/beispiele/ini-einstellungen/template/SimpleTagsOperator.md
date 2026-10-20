---
title: template.ini [SimpleTagsOperator]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SimpleTagsOperator] in template.ini: Extensions, IncludeList, TagList"
keywords: Einstellung, template.ini, SimpleTagsOperator, INI, setting, Extensions, IncludeList, TagList
type: generated
generated: exp:help:examples
---
# template.ini [SimpleTagsOperator]

Der Block `[SimpleTagsOperator]` von `settings/template.ini`, mit 3 Einstellung(en).

> Configuration for the 'simpletags' template operator  

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Extensions

> A list of extension which should be examined for  
> extra includes  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[SimpleTagsOperator]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## IncludeList

> A list of PHP files that should be included when the  
> 'simpletags' operator is used.  
> It will look in kernel/classes/simpletags or the simpletags  
> subdirectory of an extension (must be defined in Extensions)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[SimpleTagsOperator]
# ein leeres "IncludeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IncludeList[]
*/ ?>
```

## TagList

> The default setting for everything that is considered normal text,  
> ie. text not inside tags.  
> Defines literal, strong and emphasize tags  

Standard: ein Hash mit 5: `text=;;htmlspecialchars,nl2br`, `literal=<pre>;</pre>;htmlspecialchars`, `code=<pre class="code">;</pre>;htmlspecialchars`, `strong=<b>;</b>;htmlspecialchars,nl2br`, `emphasize=<i>;</i>;htmlspecialchars,nl2br`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[SimpleTagsOperator]
# ein leeres "TagList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TagList[]
TagList[text]=;;htmlspecialchars,nl2br
TagList[literal]=<pre>;</pre>;htmlspecialchars
TagList[code]=<pre class="code">;</pre>;htmlspecialchars
TagList[strong]=<b>;</b>;htmlspecialchars,nl2br
TagList[emphasize]=<i>;</i>;htmlspecialchars,nl2br
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
