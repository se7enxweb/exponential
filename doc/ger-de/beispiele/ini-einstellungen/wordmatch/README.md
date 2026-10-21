---
title: wordmatch.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von wordmatch.ini.
keywords: Beispiele, Referenz, wordmatch.ini, examples, reference
type: generated
generated: exp:help:examples
---
# wordmatch.ini

Die ausgelieferten Einstellungen von `settings/wordmatch.ini`: 10 Blöcke, 10 Einstellungen.

> Exponential-Konfigurationsdatei für aus Word-Dokumenten kopierte Inhalte  
>
> Syntax unter jedem Tag-Block: MatString\[$className\]=$matchString, kann  
> mehrzeilig sein.  
> Beispiel:  
> -------------------------------------------------------------------  
> \[td\]  
> MatchString\[tdBlue\]=mso-shading: white; mso-pattern: gray-20 black  
> MatchString\[tdRed\]=mso-shading: white; mso-pattern: gray-30 black  
> -------------------------------------------------------------------  

| Block | Einstellungen |
|---|---|
| [header](header.md) | 1 |
| [paragraph](paragraph.md) | 1 |
| [table](table.md) | 1 |
| [td](td.md) | 1 |
| [th](th.md) | 1 |
| [b](b.md) | 1 |
| [i](i.md) | 1 |
| [ol](ol.md) | 1 |
| [ul](ul.md) | 1 |
| [a](a.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/wordmatch.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
