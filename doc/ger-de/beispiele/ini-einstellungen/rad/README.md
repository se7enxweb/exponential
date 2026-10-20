---
title: rad.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von rad.ini.
keywords: Beispiele, Referenz, rad.ini, examples, reference
type: generated
generated: exp:help:examples
---
# rad.ini

Die ausgelieferten Einstellungen von `settings/rad.ini`: 4 Blöcke, 20 Einstellungen.

> The registries the extension point survey reads entry by entry (Setup > RAD tools > Extension point survey,  
> setup/radsurvey/(show)/registries; doc/eng-us/bc/6.0/rad-extension-surface.md "Registries declared in rad.ini").  
>
> A registry is a list of implementations that something reads from its settings or from its code: AI providers,  
> MCP tools, queue job types, event listeners. Each one is declared here by the kernel, or by an extension in  
> extension/<name>/settings/rad.ini.append.php, so that the survey counts its entries, checks each of them, and  
> adds them to the headline numbers of setup/rad, without the kernel having to know the extension.  
>
> Declarations are read from settings/rad.ini, settings/override/rad.ini.append.php and the rad.ini.append.php of  
> every active extension. A later file replaces a single value of an earlier one and adds to an array; a line  
> "Registries\[\]" without a value empties the list first, as everywhere in the settings (so an extension never starts  
> its own list with one: it would drop the kernel's registries and every other extension's before it).  

| Block | Einstellungen |
|---|---|
| [RegistrySettings](RegistrySettings.md) | 1 |
| [Registry_eventlisteners](Registry_eventlisteners.md) | 6 |
| [Registry_cronjobparts](Registry_cronjobparts.md) | 7 |
| [Registry_auditsources](Registry_auditsources.md) | 6 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rad.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
