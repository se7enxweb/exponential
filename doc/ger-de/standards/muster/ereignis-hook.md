---
title: Ereignis-Hook (ezpEvent)
audience: developer
category: standards
order: 328
summary: Ein benannter Punkt, an dem der Kernel ezpEvent::getInstance()-›notify() oder filter() aufruft, damit Listener reagieren oder einen Wert ändern können. 99 Instanzen in Exponential.
keywords: Entwurfsmuster, Ereignis-Hook, Verhalten, Musterkatalog, design pattern, event-hook, behavior, pattern catalogue
type: catalogue
---
# Ereignis-Hook (ezpEvent)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Ein benannter Punkt, an dem der Kernel ezpEvent::getInstance()->notify() oder filter() aufruft, damit Listener reagieren oder einen Wert ändern können.

| | |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 99 |
| Erkannt an | Ein Aufruf von ezpEvent::getInstance()-›notify( 'EVENT' ) oder -›filter( 'EVENT' ). |
| Erweiterungspunkt | event.ini [Event] Listeners[]=EVENT@CALLBACK. |

## Ereignis-Hooks (99)

### extension/expservices (12)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/class/cache` | notify | `extension/expservices/classes/content/expclassservices.php:530` |
| `content/section/cache` | notify | `extension/expservices/classes/content/expsectionservices.php:196` |
| `content/section/cache` | notify | `extension/expservices/classes/content/expsectionservices.php:208` |
| `content/state/group/cache` | notify | `extension/expservices/classes/content/expstateservices.php:230` |
| `content/state/group/cache` | notify | `extension/expservices/classes/content/expstateservices.php:242` |
| `content/state/group/cache` | notify | `extension/expservices/classes/content/expstateservices.php:253` |
| `content/state/cache` | notify | `extension/expservices/classes/content/expstateservices.php:266` |
| `content/state/cache` | notify | `extension/expservices/classes/content/expstateservices.php:280` |
| `content/state/cache` | notify | `extension/expservices/classes/content/expstateservices.php:294` |
| `content/cache` | notify | `extension/expservices/classes/content/expurlaliasservices.php:282` |
| `content/cache` | notify | `extension/expservices/classes/content/expurlaliasservices.php:303` |
| `content/cache` | notify | `extension/expservices/classes/content/expurlaliasservices.php:324` |

### extension/ezautosave (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache/version` | notify | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:225` |

### extension/ezjscore (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `response/output` | filter | `extension/ezjscore/classes/runnable/views/ezjscore/run.php:149` |

### extension/eztags (13)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `tag/add` | filter | `extension/eztags/classes/runnable/views/tags/add.php:150` |
| `tag/add` | filter | `extension/eztags/classes/runnable/views/tags/addsynonym.php:121` |
| `tag/makesynonym` | filter | `extension/eztags/classes/runnable/views/tags/addsynonym.php:122` |
| `tag/delete` | filter | `extension/eztags/classes/runnable/views/tags/delete.php:58` |
| `tag/transferobjects` | filter | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:55` |
| `tag/delete` | filter | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:70` |
| `tag/delete` | filter | `extension/eztags/classes/runnable/views/tags/deletetags.php:66` |
| `tag/edit` | filter | `extension/eztags/classes/runnable/views/tags/edit.php:179` |
| `tag/edit` | filter | `extension/eztags/classes/runnable/views/tags/editsynonym.php:141` |
| `tag/makesynonym` | filter | `extension/eztags/classes/runnable/views/tags/makesynonym.php:111` |
| `tag/merge` | filter | `extension/eztags/classes/runnable/views/tags/merge.php:74` |
| `tag/edit` | filter | `extension/eztags/classes/runnable/views/tags/movetags.php:116` |
| `tag/add` | filter | `extension/eztags/datatypes/eztags/eztags.php:623` |

### extension/nxc_powercontent (11)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache/version` | notify | `extension/nxc_powercontent/modules/content/attribute_edit.php:325` |
| `content/download` | notify | `extension/nxc_powercontent/modules/content/download.php:74` |
| `content/cache` | filter | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:960` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:1075` |
| `content/state/assign` | notify | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:1467` |
| `content/translations/cache` | notify | `extension/nxc_powercontent/modules/content/translations.php:76` |
| `content/translations/cache` | notify | `extension/nxc_powercontent/modules/content/translations.php:104` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/urlalias.php:55` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/urlalias.php:75` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/urlalias.php:157` |
| `content/view` | filter | `extension/nxc_powercontent/modules/content/view.php:52` |

### kernel/classes (11)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `image/invalidateAliases` | notify | `kernel/classes/ezcache.php:715` |
| `content/class/cache/all` | notify | `kernel/classes/ezcache.php:862` |
| `user/cache/all` | notify | `kernel/classes/ezcache.php:885` |
| `content/cache/all` | notify | `kernel/classes/ezcache.php:898` |
| `content/state/cache/all` | notify | `kernel/classes/ezcache.php:968` |
| `content/cache` | notify | `kernel/classes/ezcontentcachemanager.php:818` |
| `content/cache/all` | notify | `kernel/classes/ezcontentcachemanager.php:826` |
| `content/cache/all` | notify | `kernel/classes/ezcontentcachemanager.php:1182` |
| `content/edit/access` | filter | `kernel/classes/ezcontentobject.php:6375` |
| `content/move` | notify | `kernel/classes/ezcontentobjecttreenode.php:5415` |
| `content/view/cachekeys` | filter | `kernel/classes/eznodeviewfunctions.php:414` |

### kernel/classes/contentjob (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache` | notify | `kernel/classes/contentjob/expcontentjobhidesubtree.php:112` |

### kernel/classes/datatypes (3)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `image/purgeAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php:741` |
| `image/removeAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php:811` |
| `image/trashAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagetype.php:120` |

### kernel/modules (8)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache/version` | notify | `kernel/content/attribute_edit.php:325` |
| `content/publish` | notify | `kernel/content/ezcontentoperationcollection.php:277` |
| `content/notification/create` | filter | `kernel/content/ezcontentoperationcollection.php:983` |
| `content/cache` | filter | `kernel/content/ezcontentoperationcollection.php:1481` |
| `content/remove` | notify | `kernel/content/ezcontentoperationcollection.php:1490` |
| `content/cache` | notify | `kernel/content/ezcontentoperationcollection.php:1604` |
| `content/state/assign` | notify | `kernel/content/ezcontentoperationcollection.php:2117` |
| `content/cache/version` | notify | `kernel/content/multiedit_functions.php:496` |

### kernel/private (4)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `request/preinput` | notify | `kernel/private/classes/ezpkernelweb.php:1661` |
| `request/input` | notify | `kernel/private/classes/ezpkernelweb.php:1786` |
| `response/preoutput` | filter | `kernel/private/classes/global_functions.php:83` |
| `response/output` | filter | `kernel/private/classes/global_functions.php:102` |

### kernel/private/classes/httpcache (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `httpcache/store/headers` | filter | `kernel/private/classes/httpcache/ezphttpcachelistener.php:351` |

### kernel/private/classes/views (19)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/class/cache` | notify | `kernel/private/classes/views/class/edit.php:643` |
| `content/class/group/cache` | notify | `kernel/private/classes/views/class/groupedit.php:76` |
| `content/class/cache` | notify | `kernel/private/classes/views/class/removeclass.php:84` |
| `content/class/cache` | notify | `kernel/private/classes/views/class/removegroup.php:90` |
| `content/class/group/cache` | notify | `kernel/private/classes/views/class/removegroup.php:92` |
| `collaboration/item/access` | filter | `kernel/private/classes/views/collaboration/item.php:106` |
| `content/download` | notify | `kernel/private/classes/views/content/download.php:63` |
| `content/download/access` | filter | `kernel/private/classes/views/content/download.php:116` |
| `content/translations/cache` | notify | `kernel/private/classes/views/content/translations.php:116` |
| `content/translations/cache` | notify | `kernel/private/classes/views/content/translations.php:181` |
| `content/cache` | notify | `kernel/private/classes/views/content/urlalias.php:77` |
| `content/cache` | notify | `kernel/private/classes/views/content/urlalias.php:97` |
| `content/cache` | notify | `kernel/private/classes/views/content/urlalias.php:179` |
| `content/view` | filter | `kernel/private/classes/views/content/view.php:73` |
| `content/section/cache` | notify | `kernel/private/classes/views/section/edit.php:99` |
| `content/section/cache` | notify | `kernel/private/classes/views/section/list.php:145` |
| `content/state/cache` | notify | `kernel/private/classes/views/state/edit.php:76` |
| `content/state/group/cache` | notify | `kernel/private/classes/views/state/group_edit.php:67` |
| `content/state/group/cache` | notify | `kernel/private/classes/views/state/groups.php:76` |

### lib/ezimage (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `image/alias` | notify | `lib/ezimage/classes/ezimagemanager.php:989` |

### lib/ezsession (11)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:144` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:174` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:205` |
| `session/cleanup` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:270` |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:72` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:85` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:104` |
| `session/cleanup` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:116` |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:57` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:81` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:102` |

### lib/ezutils (2)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `response/output` | filter | `lib/ezutils/classes/ezdebug.php:1562` |
| `module/functionlist` | filter | `lib/ezutils/classes/ezmodule.php:314` |
