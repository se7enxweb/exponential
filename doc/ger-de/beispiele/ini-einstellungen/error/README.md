---
title: error.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von error.ini.
keywords: Beispiele, Referenz, error.ini, examples, reference
type: generated
generated: exp:help:examples
---
# error.ini

Die ausgelieferten Einstellungen von `settings/error.ini`: 7 Blöcke, 22 Einstellungen.

> Exponential-Konfigurationsdatei für die Fehlerbehandlung  
>
> HINWEIS: Es wird nicht empfohlen, diese Dateien direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/error.ini.append oder  
>       settings/override/error.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  
> Die folgenden Fehlercodes des Kernels sind derzeit definiert  
> 1  - Zugriff verweigert  
> 2  - Objekt nicht gefunden  
> 3  - Objekt nicht verfügbar  
> 4  - Objekt wurde verschoben  
> 5  - Sprache nicht gefunden  
> 6  - Formular-Token abgelehnt (ein POST ohne oder mit falschem Formular-Token);  
>      immer sofort als HTTP 403 angezeigt (design:error/kernel/6.tpl),  
>      ErrorHandler/RedirectURL/RerunURL gelten dafür nicht  
> 20 - Modul nicht gefunden  
> 21 - Modulansicht nicht gefunden  
> 22 - Modul oder Ansicht deaktiviert  
> 50 - Keine Datenbankverbindung  

| Block | Einstellungen |
|---|---|
| [ErrorSettings](ErrorSettings.md) | 4 |
| [ErrorSettings-kernel](ErrorSettings-kernel.md) | 5 |
| [ErrorSettings-shop](ErrorSettings-shop.md) | 5 |
| [HTTPError-404](HTTPError-404.md) | 2 |
| [HTTPError-301](HTTPError-301.md) | 2 |
| [HTTPError-401](HTTPError-401.md) | 2 |
| [HTTPError-403](HTTPError-403.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
