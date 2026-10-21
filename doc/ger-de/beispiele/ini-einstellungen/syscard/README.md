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

> Die Systemkarte der Konsole: ./console exp:expfetch und ihre größeren Formen exp:info (ein Inventar) und exp:about  
> (das Projekt). Die Karte wird aus Providern gebaut, eine Klasse pro Gruppe von Fakten, gezeichnet neben dem „e“ von Exponential  
> (doc/eng-us/features/6.0/system-card.md).  
>
> Erweiterungen fügen ihre eigenen Einstellungen in extension/<ext>/settings/syscard.ini.append.php hinzu: einen Provider  
> (ProviderClasses\[\]=myExtSysCardProvider), ein Theme (\[Theme-<name>\] und Themes\[\]=<name>), ein Logo-Verzeichnis  
> (Directories\[\]=extension/<ext>/share/syscard/logos) oder weitere Einstellungen für exp:info (Settings\[\]=...).  
>
> Die Karte zeigt nie ein Passwort, einen Schlüssel, ein Token oder eine vollständige Datenbankadresse: Ein so benannter Fakt entfällt, und  
> ein Wert, der eines enthält, wird maskiert, egal was ein Provider zurückgibt.  

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
