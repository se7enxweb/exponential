---
title: cluster.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von cluster.ini.
keywords: Beispiele, Referenz, cluster.ini, examples, reference
type: generated
generated: exp:help:examples
---
# cluster.ini

Die ausgelieferten Einstellungen von `settings/cluster.ini`: 1 Blöcke, 8 Einstellungen.

> Exponential configuration file.  
>
> NOTE: It is not recommended to edit this file directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/cluster.ini.append or  
>       settings/override/cluster.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
>
> The hosts that serve this installation, for the console commands that run  
> on them over ssh: exp:host:run, exp:cluster:run, exp:cluster:cache,  
> exp:cluster:deploy, exp:cluster:status and exp:cluster:hosts  
> (doc/eng-us/features/6.0/cluster-host-commands.md).  
>
> No secret belongs here. The commands call the system's ssh with  
> BatchMode=yes: the keys come from the ssh agent or ~/.ssh/config of the user  
> who runs the command, and a host that would ask for a password fails instead.  

| Block | Einstellungen |
|---|---|
| [ClusterSettings](ClusterSettings.md) | 8 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cluster.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
