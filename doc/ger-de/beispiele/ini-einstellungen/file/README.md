---
title: file.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von file.ini.
keywords: Beispiele, Referenz, file.ini, examples, reference
type: generated
generated: exp:help:examples
---
# file.ini

Die ausgelieferten Einstellungen von `settings/file.ini`: 6 Blöcke, 23 Einstellungen.

> Exponential-Konfigurationsdatei für den Umgang mit Dateien  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/file.ini.append oder  
>       settings/override/file.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  

| Block | Einstellungen |
|---|---|
| [BinaryFileSettings](BinaryFileSettings.md) | 1 |
| [FileSettings](FileSettings.md) | 4 |
| [ClusteringSettings](ClusteringSettings.md) | 2 |
| [eZDFSClusteringSettings](eZDFSClusteringSettings.md) | 12 |
| [PassThroughSettings](PassThroughSettings.md) | 2 |
| [ClusterEventsSettings](ClusterEventsSettings.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
