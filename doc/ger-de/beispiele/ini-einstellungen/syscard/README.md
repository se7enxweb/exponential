---
title: syscard.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von syscard.ini.
keywords: Beispiele, Referenz, syscard.ini, examples, reference
type: generated
generated: exp:help:examples
---
# syscard.ini

Die ausgelieferten Einstellungen von `settings/syscard.ini`: 16 Blöcke, 103 Einstellungen.

> The system card of the console: ./console exp:expfetch, and its larger forms exp:info (an inventory) and exp:about  
> (the project). The card is built from providers, one class per group of facts, drawn next to the Exponential "e"  
> (doc/eng-us/features/6.0/system-card.md).  
>
> Extensions add their own settings in extension/<ext>/settings/syscard.ini.append.php: a provider  
> (ProviderClasses\[\]=myExtSysCardProvider), a theme (\[Theme-<name>\] and Themes\[\]=<name>), a logo directory  
> (Directories\[\]=extension/<ext>/share/syscard/logos) or more settings for exp:info (Settings\[\]=...).  
>
> The card never shows a password, a key, a token or a full database address: a fact named like one is dropped and  
> a value that carries one is masked, whatever a provider returns.  

| Block | Einstellungen |
|---|---|
| [Providers](Providers.md) | 4 |
| [Runtime](Runtime.md) | 2 |
| [Display](Display.md) | 5 |
| [Sections](Sections.md) | 1 |
| [Logo](Logo.md) | 2 |
| [Theme](Theme.md) | 2 |
| [Theme-rainbow](Theme-rainbow.md) | 16 |
| [Theme-orange](Theme-orange.md) | 16 |
| [Theme-mono](Theme-mono.md) | 12 |
| [Theme-high-contrast](Theme-high-contrast.md) | 18 |
| [Theme-light](Theme-light.md) | 18 |
| [Php](Php.md) | 2 |
| [Database](Database.md) | 1 |
| [Updates](Updates.md) | 2 |
| [Inventory](Inventory.md) | 1 |
| [About](About.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
