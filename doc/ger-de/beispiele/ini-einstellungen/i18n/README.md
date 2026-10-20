---
title: i18n.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von i18n.ini.
keywords: Beispiele, Referenz, i18n.ini, examples, reference
type: generated
generated: exp:help:examples
---
# i18n.ini

Die ausgelieferten Einstellungen von `settings/i18n.ini`: 2 Blöcke, 9 Einstellungen.

> Exponential-Konfigurationsdatei für die Internationalisierung (i18n).  
> Wird für die Standards der internen Kodierung und der Umwandlung von Codepages verwendet.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/i18n.ini.append oder  
>       settings/override/i18n.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  
> # Diese Datei sollte ihre Standardkodierung iso-8859-1 nicht ändern.  
>
> Charset legt den internen Zeichensatz der Website fest,  
>  verwenden Sie UTF-8 für mehrsprachige Websites oder einen bestimmten Zeichensatz für einsprachige Websites  
> Setzen Sie HTTPCharset, wenn Sie wissen, dass der interne Zeichensatz vom  
> Browser nicht unterstützt wird oder einen falschen Namen hat (zum Beispiel ist UTF8 für Browser ein falscher  
>                                          Unicode-Name, UTF-8 dagegen korrekt)  
> MBStringExtension legt fest, ob die PHP-Erweiterung mbstring verwendet wird,  
>  die Erweiterung wird nur verwendet, wenn sie verfügbar ist und diese Variable auf enabled steht  

| Block | Einstellungen |
|---|---|
| [CharacterSettings](CharacterSettings.md) | 3 |
| [TranslationSettings](TranslationSettings.md) | 6 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/i18n.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
