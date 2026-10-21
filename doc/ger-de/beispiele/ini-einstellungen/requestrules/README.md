---
title: requestrules.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von requestrules.ini.
keywords: Beispiele, Referenz, requestrules.ini, examples, reference
type: generated
generated: exp:help:examples
---
# requestrules.ini

Die ausgelieferten Einstellungen von `settings/requestrules.ini`: 4 Blöcke, 19 Einstellungen.

> Anfrageregeln: was mit einer Anfrage geschieht, bevor ihre Modulansicht läuft.  
>
> Anleitung mit jeder Bedingung, Aktion und Tatsache und mit Rezepten:  
>   doc/eng-us/bc/6.0/view\_full\_security.md  
>
> Eine Regel ist eine Gruppe \[Rule-<name>\] mit Bedingungen und einer Aktion. Die in RuleList\[\]  
> genannten Regeln werden in dieser Reihenfolge zu jeder Modulansicht gefragt, die der  
> Kernel gleich ausführt, nachdem der URL-Alias übersetzt wurde und nachdem die  
> Rollenrichtlinien des Benutzers die Ansicht erlaubt haben. Die erste Regel, deren Bedingungen alle  
> passen, entscheidet; wenn keine passt, läuft die Anfrage wie immer.  
>
> Regeln nehmen immer nur Zugriff weg oder schicken den Besucher woandershin. Sie geben nie  
> Zugriff, den eine Richtlinie verweigert hat.  
>
> Bearbeiten Sie diese Datei nicht. Überschreiben Sie sie in  
>   settings/override/requestrules.ini.append.php           jeder Siteaccess  
>   settings/siteaccess/<name>/requestrules.ini.append.php  ein Siteaccess  
>   extension/<name>/settings/requestrules.ini.append.php   aus einer Erweiterung  
>
> Prüfen Sie die Einstellungen und sehen Sie, was die Regeln für eine Adresse entscheiden, mit  
>   php bin/php/ezrequestrules.php -s <siteaccess> --check  
>   php bin/php/ezrequestrules.php -s <siteaccess> --uri=content/view/full/2  

| Block | Einstellungen |
|---|---|
| [RequestRuleSettings](RequestRuleSettings.md) | 7 |
| [Rule-system_url_full_view_to_alias](Rule-system_url_full_view_to_alias.md) | 5 |
| [Rule-system_url_content_view_notfound](Rule-system_url_content_view_notfound.md) | 3 |
| [Rule-system_url_full_view_audit](Rule-system_url_full_view_audit.md) | 4 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestrules.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
