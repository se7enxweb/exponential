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

> Konfiguration des Template-Operators 'simpletags'  

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Extensions

> Eine Liste der Erweiterungen, die auf zusätzliche  
> Einbindungen untersucht werden  

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

> Eine Liste von PHP-Dateien, die eingebunden werden, wenn der  
> Operator 'simpletags' verwendet wird.  
> Es wird in kernel/classes/simpletags oder im Unterverzeichnis simpletags  
> einer Erweiterung gesucht (muss in Extensions definiert sein)  

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

> Die Standardeinstellung für alles, was als normaler Text gilt,  
> d. h. Text außerhalb von Tags.  
> Definiert die Tags literal, strong und emphasize  

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
