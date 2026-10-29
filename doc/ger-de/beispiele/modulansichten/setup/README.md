---
title: Das Modul setup
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls setup: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul setup, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul setup

Name `eZSetup`, definiert in `kernel/setup/module.php`. 45 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [cache](cache.md) | `/setup/cache` | managecache |
| [cachetoolbar](cachetoolbar.md) | `/setup/cachetoolbar` | managecache |
| [contentextension](contentextension.md) | `/setup/contentextension` | setup |
| [cronjobs](cronjobs.md) | `/setup/cronjobs` | managecronjobs |
| [cronjobsstream](cronjobsstream.md) | `/setup/cronjobsstream` | managecronjobs |
| [datatype](datatype.md) | `/setup/datatype` | setup |
| [designextension](designextension.md) | `/setup/designextension` | setup |
| [extensions](extensions.md) | `/setup/extensions/<ExtensionName>/<ExtensionFormat>` | setup |
| [handlerextension](handlerextension.md) | `/setup/handlerextension/<Kind>` | setup |
| [info](info.md) | `/setup/info/<Mode>` | system_info |
| [init](init.md) | `/setup/init` | install |
| [kerneloverride](kerneloverride.md) | `/setup/kerneloverride` | setup |
| [languages](languages.md) | `/setup/languages` | system_info |
| [logs](logs.md) | `/setup/logs/<Mode>/<Key>` | logs |
| [logsaction](logsaction.md) | `/setup/logsaction` | logs |
| [logsdigest](logsdigest.md) | `/setup/logsdigest/<Key>` | logs |
| [logsfeed](logsfeed.md) | `/setup/logsfeed` | logs |
| [logshealth](logshealth.md) | `/setup/logshealth` | logs |
| [logsmanage](logsmanage.md) | `/setup/logsmanage/<Key>` | managelogs |
| [maintenance](maintenance.md) | `/setup/maintenance` | administrate |
| [menu](menu.md) | `/setup/menu` | setup or administrate or managecache or managecronjobs or preload or system_info or logs |
| [menuconfig](menuconfig.md) | `/setup/menuconfig` | setup |
| [moduleextension](moduleextension.md) | `/setup/moduleextension` | setup |
| [moduletable](moduletable.md) | `/setup/moduletable` | setup |
| [modulewizard](modulewizard.md) | `/setup/modulewizard` | setup |
| [preload](preload.md) | `/setup/preload` | preload |
| [preloadjob](preloadjob.md) | `/setup/preloadjob/<JobID>/<Offset>` | preload |
| [preloadstream](preloadstream.md) | `/setup/preloadstream` | preload |
| [rad](rad.md) | `/setup/rad` | setup |
| [radsurvey](radsurvey.md) | `/setup/radsurvey` | setup |
| [reauth](reauth.md) | `/setup/reauth/<SiteAccess>` | setup |
| [session](session.md) | `/setup/session/<UserID>` | administrate |
| [settingsextension](settingsextension.md) | `/setup/settingsextension` | setup |
| [settingstoolbar](settingstoolbar.md) | `/setup/settingstoolbar` | setup |
| [sitedata](sitedata.md) | `/setup/sitedata` | system_info |
| [staticcachestream](staticcachestream.md) | `/setup/staticcachestream` | managecache |
| [systemupgrade](systemupgrade.md) | `/setup/systemupgrade` | setup |
| [templatecreate](templatecreate.md) | `/setup/templatecreate` | setup |
| [templateedit](templateedit.md) | `/setup/templateedit` | setup |
| [templatelist](templatelist.md) | `/setup/templatelist` | setup |
| [templateoperator](templateoperator.md) | `/setup/templateoperator` | setup |
| [templateview](templateview.md) | `/setup/templateview` | setup |
| [toolbar](toolbar.md) | `/setup/toolbar/<SiteAccess>/<Position>` | setup |
| [toolbarlist](toolbarlist.md) | `/setup/toolbarlist/<SiteAccess>` | setup |
| [workflowevent](workflowevent.md) | `/setup/workflowevent` | setup |

- [Die Richtlinien dieses Moduls](../../richtlinien/setup.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
