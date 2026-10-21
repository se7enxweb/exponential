---
title: layout.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von layout.ini.
keywords: Beispiele, Referenz, layout.ini, examples, reference
type: generated
generated: exp:help:examples
---
# layout.ini

Die ausgelieferten Einstellungen von `settings/layout.ini`: 3 Blöcke, 3 Einstellungen.

> Exponential-Konfigurationsdatei für das Modul layout  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/layout.ini.append oder  
>       settings/override/layout.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  
>
> Besteht aus Gruppen, die den Namen des Layouts tragen  
> Eine Layout-Gruppe kann folgende Variablen haben  
> - ContentType   - setzt den 'Content-Type' für die Ausgabe eines bestimmten Layouts.  
>                   Beispiel: ContentType=text/xml  
> - PageLayout    - setzt das Pagelayout für ein bestimmtes Layout.  
> - UseAccessPass - den Layout-Pfad in Links ein- oder ausschließen.  
>                   Beispiel: UseAccessPass=false  
> - UseFullUrl    - schaltet volle URLs in Links ein oder aus.  
>                   Beispiel: UseFullUrl=true  

| Block | Einstellungen |
|---|---|
| [fullscreen](fullscreen.md) | 1 |
| [popup](popup.md) | 1 |
| [print](print.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/layout.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
