---
title: Fassade / Dienst
audience: developer
category: standards
order: 330
summary: Ein einfacher Einstiegspunkt vor einem Subsystem oder eine Dienstklasse, die die Operationen einer Domäne enthält. 40 Instanzen in Exponential.
keywords: Entwurfsmuster, Fassade, Struktur, Musterkatalog, design pattern, facade, pattern catalogue
type: catalogue
---
# Fassade / Dienst

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Ein einfacher Einstiegspunkt vor einem Subsystem oder eine Dienstklasse, die die Operationen einer Domäne enthält.

| | |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 40 |
| Erkannt an | Der Klassenname endet auf Facade, Service, Manager, API oder Api. |

## Klassen nach Bereich

### extension/expai (1)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAiBatchApi` | abstract class | `extension/expai/classes/lanes/expaibatchapi.php:27` | Die Batch-API eines Anbieters: viele Anfragen auf einmal einreichen, den Batch abfragen, die Ergebnisse abholen, abbrechen, löschen. | extension/expai/settings/rad.ini [Registry_expai_batch_apis] Implements |

### extension/explayouts (2)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsExpSiteApi` | class | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – | – |
| `expLayoutsSiteAPI` | class | `extension/explayouts/classes/explayoutssiteapi.php:10` | Kompatibilitätsschicht Exponential 4 / Exponential Site API. | – |

### extension/explayouts_core (6)

[Alle Muster von extension/explayouts_core](bereich-extension-explayouts-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsCoreBlockService` | class | `extension/explayouts_core/classes/explayoutscoreblockservice.php:3` | – | – |
| `expLayoutsCoreCollectionService` | class | `extension/explayouts_core/classes/explayoutscorecollectionservice.php:3` | – | – |
| `expLayoutsCoreLayoutService` | class | `extension/explayouts_core/classes/explayoutscorelayoutservice.php:3` | – | – |
| `expLayoutsCoreRuleService` | class | `extension/explayouts_core/classes/explayoutscoreruleservice.php:3` | – | – |
| `expLayoutsCoreTransferService` | class | `extension/explayouts_core/classes/explayoutscoretransferservice.php:17` | Export und Import von Layouts, geteilten Layouts und Regeln: der eine Einstiegspunkt, den die Admin-Seiten (explayouts_ui/transfer_export, explayouts_ui/transfer_im… | – |
| `expLayoutsCoreZoneService` | class | `extension/explayouts_core/classes/explayoutscorezoneservice.php:3` | – | – |

### extension/explayouts_ui_api (1)

[Alle Muster von extension/explayouts_ui_api](bereich-extension-explayouts-ui-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsUIApplicationApi` | class | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – | – |

### extension/expqueue (1)

[Alle Muster von extension/expqueue](area-extension-expqueue.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expQueueService` | class | `extension/expqueue/classes/expqueueservice.php:22` | Der systemd-Dienst der Worker der Warteschlange: ./console ext:expqueue:service install\|start\|stop\|status\|uninstall. | – |

### extension/expsite_api (4)

[Alle Muster von extension/expsite_api](bereich-extension-expsite-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSiteApi` | class | `extension/expsite_api/classes/expsiteapi.php:2` | – | – |
| `expSiteApiContentService` | class | `extension/expsite_api/classes/expsiteapicontentservice.php:2` | – | – |
| `expSiteApiFilterService` | class | `extension/expsite_api/classes/expsiteapifilterservice.php:2` | – | – |
| `expSiteApiLocationService` | class | `extension/expsite_api/classes/expsiteapilocationservice.php:2` | – | – |

### extension/exp_oe_tiptap (1)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOETiptapAIService` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiservice.php:19` | Die KI-Hooks auf dem Server: Prüfungen, Grenzen, Prompt, Anbieteraufruf, Antwort. | – |

### extension/ezflow (2)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZHTTPCacheManager` | class | `extension/ezflow/classes/ezhttpcachemanager.php:33` | Die Klasse eZHTTPCacheManager implementiert Methoden, die zum Laden benutzerdefinierter HTTP-Cache-Handler nötig sind | – |
| `eZSquidCacheManager` | class | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – | extension/ezflow/settings/http.ini [HTTPCacheHandlers] Handlers[] |

### extension/ezjscore (1)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscCacheManager` | class | `extension/ezjscore/classes/ezjsccachemanager.php:11` | – | extension/ezjscore/settings/site.ini [Cache_ezjscore] class |

### extension/ezmultiupload (2)

[Alle Muster von extension/ezmultiupload](bereich-extension-ezmultiupload.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMultiuploadService` | class | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | Die Serverseite der Upload-Seite: Prüfungen vor einem Upload (der Zielort, die Inhaltsklasse, zu der jede Datei wird, die Berechtigung zum Anlegen dafür… | – |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Service` | class extends ModuleView | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/service.php:23` | ezmultiupload/service: die Prüfungen und Aktionen der Upload-Seite, als JSON beantwortet. | – |

### extension/ezownerchange (1)

[Alle Muster von extension/ezownerchange](bereich-extension-ezownerchange.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOwnerChangeService` | class | `extension/ezownerchange/classes/expownerchangeservice.php:23` | Ändert den Eigentümer von Inhaltsobjekten: eines Objekts oder jedes Objekts mit einem Ort in einem Teilbaum. | – |

### extension/ezupdate (1)

[Alle Muster von extension/ezupdate](bereich-extension-ezupdate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZUpdateManager` | class | `extension/ezupdate/classes/ezupdatemanager.php:19` | Führt Composer für die Installation aus, zu der diese Extension gehört. | – |

### extension/git_manager (2)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `BackupManager` | class | `extension/git_manager/classes/backupmanager.php:9` | – | – |
| `GitManager` | class | `extension/git_manager/classes/git_manager.php:9` | – | – |

### extension/syndication (1)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSyndicationFeedCacheManager` | class | `extension/syndication/classes/ezsyndicationfeedcachemanager.php:48` | – | – |

### kernel/classes (7)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expCacheManager` | class | `kernel/classes/expcachemanager.php:30` | Alles, was Setup › Cache (kernel/setup/cache.php) kann, als Funktionen, die zurückgeben, was geschehen ist, statt Template-Variablen zu setzen, sodass die Admini… | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Legt die Kollaborationsgruppen eines Benutzers an, benennt sie um, löscht sie und verschiebt Einträge zwischen ihnen. | – |
| `expNotificationService` | class | `kernel/classes/expnotificationservice.php:26` | Die eine Stelle, die weiß, wie das Benachrichtigungssystem ausgeführt und eingesehen wird: der Cronjob-Teil, die Befehle exp:notification:*, die Statusseite und ihre… | – |
| `eZContentCacheManager` | class | `kernel/classes/ezcontentcachemanager.php:26` | – | – |
| `eZRedirectManager` | class | `kernel/classes/ezredirectmanager.php:22` | – | – |
| `eZShippingManager` | class | `kernel/classes/ezshippingmanager.php:17` | – | – |
| `eZVATManager` | class | `kernel/classes/ezvatmanager.php:18` | – | – |

### kernel/classes/logviewer (1)

[Alle Muster von kernel/classes/logviewer](bereich-kernel-classes-logviewer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLogManager` | class | `kernel/classes/logviewer/explogmanager.php:27` | Log-Verwaltung (doc/eng-us/features/6.0/log-rotation.md): die Logs der Installation mit ihren Archiven, Größen, ihrem Wachstum und ihren Limits; Rotation, Bereinigung… | – |

### kernel/classes/mailpreferences (1)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailPreferencesService` | class | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | Die Abläufe rund um die Präferenzen: die Double-Opt-in-Mail und ihre Bestätigung, die Abmeldung mit einem Klick, „Link zusenden“ für Personen ohne Login,… | settings/mailpreferences.ini [GateSettings] EssentialSenders[] |

### kernel/classes/passkey (1)

[Alle Muster von kernel/classes/passkey](area-kernel-classes-passkey.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expPasskeyService` | class | `kernel/classes/passkey/exppasskeyservice.php:32` | Die Passkey-Abläufe der Webseiten (doc/eng-us/guides/passkeys.md): | – |

### kernel/private (2)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpAttributeOperatorManager` | class | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – | – |
| `ezpRestTokenManager` | class | `kernel/private/oauth/classes/tokenmanager.php:17` | Diese Klasse verwaltet Anwendungs-Tokens | – |

### lib/ezi18n (1)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |

### lib/ezimage (1)

[Alle Muster von lib/ezimage](bereich-lib-ezimage.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageManager` | class | `lib/ezimage/classes/ezimagemanager.php:85` | – | – |
