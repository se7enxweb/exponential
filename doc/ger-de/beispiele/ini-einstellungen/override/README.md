---
title: override.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von override.ini.
keywords: Beispiele, Referenz, override.ini, examples, reference
type: generated
generated: exp:help:examples
---
# override.ini

Die ausgelieferten Einstellungen von `settings/override.ini`: 0 Blöcke, 0 Einstellungen.

> Exponential-Konfigurationsdatei für Template-Overrides.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/override.ini.append oder  
>       settings/override/override.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  
> Konfigurationsdatei der Template-Overrides  
> Legen Sie Ihre eigene Override-Konfigurationsdatei  
> in settings/siteaccess/myaccess/override.ini.append ab  
>
> Diese Datei sollte nur für Overrides verwendet werden, die  
> allen Siteaccesses gemeinsam sind  
> Beispielsyntax  
> \[article\]  
> Source=node/view/full.tpl  
> MatchFile=article.tpl  
> Subdir=templates  
> Match\[section\]=1  
> Match\[class\]=2  

| Block | Einstellungen |
|---|---|

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/override.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
