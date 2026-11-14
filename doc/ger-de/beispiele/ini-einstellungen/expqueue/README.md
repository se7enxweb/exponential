---
title: expqueue.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von expqueue.ini.
keywords: Beispiele, Referenz, expqueue.ini, examples, reference
type: generated
generated: exp:help:examples
---
# expqueue.ini

Die ausgelieferten Einstellungen von `settings/expqueue.ini`: 3 Blöcke, 16 Einstellungen.

> Die Job-Typen des Kernels für expqueue, die Job-Warteschlange (eine Erweiterung). Sie werden nur gelesen, solange expqueue aktiv ist  
> und seine Tabellen installiert sind; ohne expqueue erledigt der Kernel dieselbe Arbeit sofort, wo er darf (ein Skript, die  
> Administration), und nie in einer Seitenanfrage. Die eigenen Einstellungen der Warteschlange (Klassen, Worker, Kontingente) kommen mit der  
> Erweiterung (extension/expqueue/settings/expqueue.ini.append.php); eine Site ändert beides in  
> settings/override/expqueue.ini.append.php.  
>
> \[JobType\_<type>\] Handler=<Klasse, die expQueueHandler implementiert>, optional Class (die Prioritätsklasse, wenn der  
> Erzeuger keine nennt), Priority, MaxAttempts, Timeout, Resource, RetryBase, MaxBackoff, Description.  

| Block | Einstellungen |
|---|---|
| [JobType_image.alias.generate](JobType_image.alias.generate.md) | 7 |
| [JobType_image.alias.bulk](JobType_image.alias.bulk.md) | 5 |
| [JobType_image.alias.resync](JobType_image.alias.resync.md) | 4 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/expqueue.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
