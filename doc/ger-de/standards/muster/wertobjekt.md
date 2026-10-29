---
title: Wertobjekt / Struktur
audience: developer
category: standards
order: 334
summary: Ein kleines Objekt, das Daten trägt und keine eigene Identität hat. 106 Instanzen in Exponential.
keywords: Entwurfsmuster, Wertobjekt, Struktur, Musterkatalog, design pattern, value-object, pattern catalogue
type: catalogue
---
# Wertobjekt / Struktur

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Ein kleines Objekt, das Daten trägt und keine eigene Identität hat.

| | |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 106 |
| Erkannt an | Der Klassenname endet auf Struct, Value, Result, Item, Info oder Definition und die Klasse hat wenige Methoden, oder sie erweitert ezcBaseStruct. |

## Klassen nach Bereich

### extension/AdminAid (1)

[Alle Muster von extension/AdminAid](bereich-extension-adminaid.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezAdminAidInfo` | class | `extension/AdminAid/ezinfo.php:27` | – | – |

### extension/autorss (1)

[Alle Muster von extension/autorss](bereich-extension-autorss.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `autorssInfo` | class | `extension/autorss/ezinfo.php:3` | – | – |

### extension/bccie (1)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bccieInfo` | class | `extension/bccie/ezinfo.php:10` | – | – |

### extension/bcgooglesitemaps (1)

[Alle Muster von extension/bcgooglesitemaps](bereich-extension-bcgooglesitemaps.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bcGoogleSitemapsInfo` | class | `extension/bcgooglesitemaps/ezinfo.php:11` | – | – |

### extension/bcwebsitestatistics (1)

[Alle Muster von extension/bcwebsitestatistics](bereich-extension-bcwebsitestatistics.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bcwebsitestatisticsInfo` | class | `extension/bcwebsitestatistics/ezinfo.php:3` | – | – |

### extension/birthday (1)

[Alle Muster von extension/birthday](bereich-extension-birthday.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `birthdayInfo` | class | `extension/birthday/ezinfo.php:6` | Die Informationen der Extension für die Seite „Über“ und die Upgrade-Prüfungen. | – |

### extension/cjw_newsletter (1)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjw_newsletterInfo` | class | `extension/cjw_newsletter/ezinfo.php:16` | Name, Version, Copyright, Lizenz und Projektseite der Extension, auf der Info-Seite angezeigt. | – |

### extension/cjw_themes_jumper (1)

[Alle Muster von extension/cjw_themes_jumper](bereich-extension-cjw-themes-jumper.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjw_themes_jumperInfo` | class | `extension/cjw_themes_jumper/ezinfo.php:7` | – | – |

### extension/cjw_tmv (1)

[Alle Muster von extension/cjw_tmv](bereich-extension-cjw-tmv.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjw_tmvInfo` | class | `extension/cjw_tmv/ezinfo.php:7` | – | – |

### extension/enhancedezbinaryfile (1)

[Alle Muster von extension/enhancedezbinaryfile](bereich-extension-enhancedezbinaryfile.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `enhancedezbinaryfileInfo` | class | `extension/enhancedezbinaryfile/ezinfo.php:40` | – | – |

### extension/enhancedselection2 (1)

[Alle Muster von extension/enhancedselection2](bereich-extension-enhancedselection2.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `enhancedselection2Info` | class | `extension/enhancedselection2/ezinfo.php:14` | Extension Enhanced Selection | – |

### extension/expai (2)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expaiInfo` | class | `extension/expai/ezinfo.php:7` | – | – |
| `expAiToolGetClassDefinition` | class extends expAiTool | `extension/expai/classes/tools/expaitoolgetclassdefinition.php:9` | get_class_definition: die Felder einer Inhaltsklasse | extension/expai/settings/expai.ini [Tool_get_class_definition] Class |

### extension/expchangeclass (1)

[Alle Muster von extension/expchangeclass](bereich-extension-expchangeclass.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expchangeclassInfo` | class | `extension/expchangeclass/ezinfo.php:14` | – | – |

### extension/exphelpsite (1)

[Alle Muster von extension/exphelpsite](area-extension-exphelpsite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `exphelpsiteInfo` | class | `extension/exphelpsite/ezinfo.php:11` | Die Extension exphelpsite: die Node-Templates der als Inhalt gepflegten Hilfe für die öffentlichen Seiten einer Site (doc/eng-us/features/6.0/help-content.md, „Help… | – |

### extension/expimageeditor (1)

[Alle Muster von extension/expimageeditor](area-extension-expimageeditor.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expimageeditorInfo` | class | `extension/expimageeditor/ezinfo.php:9` | – | – |

### extension/explayouts (2)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsExpItem` | class | `extension/explayouts/classes/explayoutsexpitem.php:4` | – | – |
| `explayoutsInfo` | class | `extension/explayouts/ezinfo.php:12` | – | – |

### extension/explayouts_api (1)

[Alle Muster von extension/explayouts_api](bereich-extension-explayouts-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_apiInfo` | class | `extension/explayouts_api/ezinfo.php:12` | – | – |

### extension/explayouts_content_browser (2)

[Alle Muster von extension/explayouts_content_browser](bereich-extension-explayouts-content-browser.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowserItem` | class | `extension/explayouts_content_browser/classes/explayoutscontentbrowseritem.php:2` | – | – |
| `explayouts_content_browserInfo` | class | `extension/explayouts_content_browser/ezinfo.php:12` | – | – |

### extension/explayouts_content_browser_core (1)

[Alle Muster von extension/explayouts_content_browser_core](bereich-extension-explayouts-content-browser-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_content_browser_coreInfo` | class | `extension/explayouts_content_browser_core/ezinfo.php:12` | – | – |

### extension/explayouts_content_browser_ui (1)

[Alle Muster von extension/explayouts_content_browser_ui](bereich-extension-explayouts-content-browser-ui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_content_browser_uiInfo` | class | `extension/explayouts_content_browser_ui/ezinfo.php:12` | – | – |

### extension/explayouts_core (1)

[Alle Muster von extension/explayouts_core](bereich-extension-explayouts-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_coreInfo` | class | `extension/explayouts_core/ezinfo.php:12` | – | – |

### extension/explayouts_relation_list_query (1)

[Alle Muster von extension/explayouts_relation_list_query](bereich-extension-explayouts-relation-list-query.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_relation_list_queryInfo` | class | `extension/explayouts_relation_list_query/ezinfo.php:12` | – | – |

### extension/explayouts_site_api (1)

[Alle Muster von extension/explayouts_site_api](bereich-extension-explayouts-site-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_site_apiInfo` | class | `extension/explayouts_site_api/ezinfo.php:12` | – | – |

### extension/explayouts_standard (1)

[Alle Muster von extension/explayouts_standard](bereich-extension-explayouts-standard.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_standardInfo` | class | `extension/explayouts_standard/ezinfo.php:12` | – | – |

### extension/explayouts_tags_query (1)

[Alle Muster von extension/explayouts_tags_query](bereich-extension-explayouts-tags-query.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_tags_queryInfo` | class | `extension/explayouts_tags_query/ezinfo.php:12` | – | – |

### extension/explayouts_ui (1)

[Alle Muster von extension/explayouts_ui](bereich-extension-explayouts-ui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_uiInfo` | class | `extension/explayouts_ui/ezinfo.php:12` | – | – |

### extension/explayouts_ui_api (1)

[Alle Muster von extension/explayouts_ui_api](bereich-extension-explayouts-ui-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `explayouts_ui_apiInfo` | class | `extension/explayouts_ui_api/ezinfo.php:12` | – | – |

### extension/expquery_translator (1)

[Alle Muster von extension/expquery_translator](bereich-extension-expquery-translator.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expquery_translatorInfo` | class | `extension/expquery_translator/ezinfo.php:12` | – | – |

### extension/expqueue (1)

[Alle Muster von extension/expqueue](area-extension-expqueue.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expqueueInfo` | class | `extension/expqueue/ezinfo.php:7` | – | – |

### extension/expservices (1)

[Alle Muster von extension/expservices](bereich-extension-expservices.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expservicesInfo` | class | `extension/expservices/ezinfo.php:7` | – | – |

### extension/expsite_api (1)

[Alle Muster von extension/expsite_api](bereich-extension-expsite-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expsite_apiInfo` | class | `extension/expsite_api/ezinfo.php:12` | – | – |

### extension/expsite_app (1)

[Alle Muster von extension/expsite_app](bereich-extension-expsite-app.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expsite_appInfo` | class | `extension/expsite_app/ezinfo.php:12` | – | – |

### extension/expsite_core (1)

[Alle Muster von extension/expsite_core](bereich-extension-expsite-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expsite_coreInfo` | class | `extension/expsite_core/ezinfo.php:12` | – | – |

### extension/expsite_data_media (1)

[Alle Muster von extension/expsite_data_media](bereich-extension-expsite-data-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expsite_data_mediaInfo` | class | `extension/expsite_data_media/ezinfo.php:12` | – | – |

### extension/expsite_installer (1)

[Alle Muster von extension/expsite_installer](bereich-extension-expsite-installer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expsite_installerInfo` | class | `extension/expsite_installer/ezinfo.php:12` | – | – |

### extension/expui (1)

[Alle Muster von extension/expui](bereich-extension-expui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expuiInfo` | class | `extension/expui/ezinfo.php:7` | – | – |

### extension/exp_adminui (1)

[Alle Muster von extension/exp_adminui](bereich-extension-exp-adminui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `exp_adminuiInfo` | class | `extension/exp_adminui/ezinfo.php:7` | – | – |

### extension/exp_enhanced_link (1)

[Alle Muster von extension/exp_enhanced_link](bereich-extension-exp-enhanced-link.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `exp_enhanced_linkInfo` | class | `extension/exp_enhanced_link/ezinfo.php:12` | – | – |

### extension/exp_oe_tiptap (1)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `exp_oe_tiptapInfo` | class | `extension/exp_oe_tiptap/ezinfo.php:7` | – | – |

### extension/ezauthorize (1)

[Alle Muster von extension/ezauthorize](bereich-extension-ezauthorize.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZAuthorizeInfo` | class | `extension/ezauthorize/classes/ezauthorizeinfo.php:11` | – | – |

### extension/ezautosave (1)

[Alle Muster von extension/ezautosave](bereich-extension-ezautosave.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezautosaveInfo` | class | `extension/ezautosave/ezinfo.php:7` | – | – |

### extension/ezdemo (1)

[Alle Muster von extension/ezdemo](bereich-extension-ezdemo.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezdemoInfo` | class | `extension/ezdemo/ezinfo.php:12` | – | – |

### extension/ezfind (2)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfindInfo` | class | `extension/ezfind/ezinfo.php:7` | – | – |
| `ezfSearchResultInfo` | class | `extension/ezfind/classes/ezfsearchresultinfo.php:12` | ezfSearchResultInfo enthält zusätzliche Informationen zum Suchergebnis. | – |

### extension/ezflow (1)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezflowInfo` | class | `extension/ezflow/ezinfo.php:12` | – | – |

### extension/ezgmaplocation (1)

[Alle Muster von extension/ezgmaplocation](bereich-extension-ezgmaplocation.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezgmaplocationInfo` | class | `extension/ezgmaplocation/ezinfo.php:7` | – | – |

### extension/ezie (1)

[Alle Muster von extension/ezie](bereich-extension-ezie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezieInfo` | class | `extension/ezie/ezinfo.php:11` | – | – |

### extension/ezjscore (1)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscoreInfo` | class | `extension/ezjscore/ezinfo.php:7` | – | – |

### extension/ezmbpaex (1)

[Alle Muster von extension/ezmbpaex](bereich-extension-ezmbpaex.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezmbpaexInfo` | class | `extension/ezmbpaex/ezinfo.php:7` | – | – |

### extension/ezmultiupload (1)

[Alle Muster von extension/ezmultiupload](bereich-extension-ezmultiupload.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezmultiuploadInfo` | class | `extension/ezmultiupload/ezinfo.php:7` | – | – |

### extension/ezodf (1)

[Alle Muster von extension/ezodf](bereich-extension-ezodf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezodfInfo` | class | `extension/ezodf/ezinfo.php:12` | – | – |

### extension/ezoracle (1)

[Alle Muster von extension/ezoracle](area-extension-ezoracle.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezoracleInfo` | class | `extension/ezoracle/ezinfo.php:11` | – | – |

### extension/ezownerchange (1)

[Alle Muster von extension/ezownerchange](bereich-extension-ezownerchange.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezownerchangeInfo` | class | `extension/ezownerchange/ezinfo.php:10` | Die Informationen von ezownerchange für die Seite „Über“ (ezinfo/about). | – |

### extension/ezpaypal (1)

[Alle Muster von extension/ezpaypal](bereich-extension-ezpaypal.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpaypalInfo` | class | `extension/ezpaypal/ezinfo.php:2` | – | – |

### extension/ezpm (1)

[Alle Muster von extension/ezpm](bereich-extension-ezpm.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpmInfo` | class | `extension/ezpm/ezinfo.php:25` | – | – |

### extension/ezprestapi (1)

[Alle Muster von extension/ezprestapi](bereich-extension-ezprestapi.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezprestapiInfo` | class | `extension/ezprestapi/ezinfo.php:10` | – | – |

### extension/ezssp (1)

[Alle Muster von extension/ezssp](bereich-extension-ezssp.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsspInfo` | class | `extension/ezssp/ezinfo.php:2` | – | – |

### extension/ezstarrating (1)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezstarratingInfo` | class | `extension/ezstarrating/ezinfo.php:7` | – | – |

### extension/eztags (1)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eztagsInfo` | class | `extension/eztags/ezinfo.php:11` | – | – |

### extension/ezupdate (1)

[Alle Muster von extension/ezupdate](bereich-extension-ezupdate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezupdateInfo` | class | `extension/ezupdate/ezinfo.php:7` | – | – |

### extension/ezurlmultisite (1)

[Alle Muster von extension/ezurlmultisite](bereich-extension-ezurlmultisite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezurlmultisiteInfo` | class | `extension/ezurlmultisite/ezinfo.php:10` | – | – |

### extension/ezwebin (1)

[Alle Muster von extension/ezwebin](bereich-extension-ezwebin.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezwebinInfo` | class | `extension/ezwebin/ezinfo.php:12` | – | – |

### extension/ezwt (1)

[Alle Muster von extension/ezwt](bereich-extension-ezwt.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezwtInfo` | class | `extension/ezwt/ezinfo.php:7` | – | – |

### extension/git_manager (2)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\GitManager\BackupInfo` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-info.php:28` | Der Code von extension/git_manager/bin/php/backup-info.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `git_managerInfo` | class | `extension/git_manager/ezinfo.php:9` | – | – |

### extension/hcaptcha (1)

[Alle Muster von extension/hcaptcha](bereich-extension-hcaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `hcaptchaInfo` | class | `extension/hcaptcha/ezinfo.php:22` | hCaptcha-Extension für Exponential, geschrieben von 7x, basierend auf Arbeiten von: Bruce Morrison | – |

### extension/ngclasslist (1)

[Alle Muster von extension/ngclasslist](bereich-extension-ngclasslist.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ngclasslistInfo` | class | `extension/ngclasslist/ezinfo.php:3` | – | – |

### extension/nxc_powercontent (1)

[Alle Muster von extension/nxc_powercontent](bereich-extension-nxc-powercontent.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `nxc_powercontentInfo` | class | `extension/nxc_powercontent/ezinfo.php:8` | – | – |

### extension/owsimpleoperator (1)

[Alle Muster von extension/owsimpleoperator](bereich-extension-owsimpleoperator.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `owSimpleOperatorInfo` | class | `extension/owsimpleoperator/ezinfo.php:11` | – | – |

### extension/powercontent (1)

[Alle Muster von extension/powercontent](bereich-extension-powercontent.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `powercontentInfo` | class | `extension/powercontent/ezinfo.php:2` | – | – |

### extension/recaptcha (1)

[Alle Muster von extension/recaptcha](bereich-extension-recaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `recaptchaInfo` | class | `extension/recaptcha/ezinfo.php:22` | reCAPTCHA-Extension für Exponential, geschrieben von Bruce Morrison | – |

### extension/sevenx_authentication_2fa (1)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenx_authentication_2faInfo` | class | `extension/sevenx_authentication_2fa/ezinfo.php:12` | – | – |

### extension/sevenx_dse (9)

[Alle Muster von extension/sevenx_dse](bereich-extension-sevenx-dse.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AdminNeo\ClickHouseResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:130` | – | – |
| `AdminNeo\ElasticResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:123` | – | – |
| `AdminNeo\MongoResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:114` | – | – |
| `AdminNeo\MySqlResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:145` | – | – |
| `AdminNeo\PgSqlResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:149` | – | – |
| `AdminNeo\Result` | abstract class | `extension/sevenx_dse/adminneo/core/Result.php:7` | – | – |
| `AdminNeo\SimpleDbResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:96` | – | – |
| `AdminNeo\SqLiteResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:65` | – | – |
| `sevenx_dseInfo` | class | `extension/sevenx_dse/ezinfo.php:7` | – | – |

### extension/sevenx_themes_media (1)

[Alle Muster von extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenx_themes_mediaInfo` | class | `extension/sevenx_themes_media/ezinfo.php:13` | – | – |

### extension/sevenx_themes_simple (1)

[Alle Muster von extension/sevenx_themes_simple](bereich-extension-sevenx-themes-simple.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenx_themes_simpleInfo` | class | `extension/sevenx_themes_simple/ezinfo.php:11` | – | – |

### extension/sevenx_themes_super (1)

[Alle Muster von extension/sevenx_themes_super](bereich-extension-sevenx-themes-super.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SevenxthemessuperInfo` | class | `extension/sevenx_themes_super/ezinfo.php:13` | Was die Administrationsoberfläche über diese Extension liest. | – |

### extension/sqliimport (1)

[Alle Muster von extension/sqliimport](area-extension-sqliimport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sqliimportInfo` | class | `extension/sqliimport/ezinfo.php:7` | – | – |

### extension/str_replace (1)

[Alle Muster von extension/str_replace](bereich-extension-str-replace.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `str_replaceInfo` | class | `extension/str_replace/ezinfo.php:2` | – | – |

### extension/swark (1)

[Alle Muster von extension/swark](bereich-extension-swark.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SwarkInfo` | class | `extension/swark/ezinfo.php:22` | – | – |

### extension/syndication (1)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `syndicationInfo` | class | `extension/syndication/ezinfo.php:42` | – | – |

### extension/xrowextract (1)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowextractInfo` | class | `extension/xrowextract/ezinfo.php:10` | – | – |

### extension/xrowmetadata (10)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowMetaData` | class extends ezcBaseStruct | `extension/xrowmetadata/classes/structs/xrowmetadata.php:3` | – | – |
| `xrowmetadataInfo` | class | `extension/xrowmetadata/ezinfo.php:8` | – | – |
| `xrowSitemapItem` | abstract class extends ezcBaseStruct | `extension/xrowmetadata/classes/structs/xrowsitemapitem.php:3` | – | – |
| `xrowSitemapItemAlternateLink` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemalternatelink.php:3` | – | – |
| `xrowSitemapItemFrequency` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemfrequency.php:3` | – | – |
| `xrowSitemapItemImage` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemimage.php:3` | – | – |
| `xrowSitemapItemModified` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemmodified.php:3` | – | – |
| `xrowSitemapItemNews` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapnewsitem.php:3` | – | – |
| `xrowSitemapItemPriority` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitempriority.php:3` | – | – |
| `xrowSitemapItemVideo` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapvideoitem.php:3` | – | – |

### kernel/private (2)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpAutoloadFileFindContext` | class extends ezcBaseStruct | `kernel/private/structs/ezpautoloadfilefindcontext.php:17` | Struktur, die die vom Datei-Walker gesammelten Informationen zum Auffinden von Dateien definiert. | – |
| `ezpRestMvcResult` | class extends ezcMvcResult | `kernel/private/rest/classes/response/result.php:15` | Diese Struktur enthält die Ergebnisdaten, die von eZP-REST-Controllern zurückgegeben werden | – |

### kernel/private/classes/views (1)

[Alle Muster von kernel/private/classes/views](bereich-kernel-private-classes-views.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Kernel\Collaboration\Item` | class extends ModuleView | `kernel/private/classes/views/collaboration/item.php:21` | Der Code von kernel/collaboration/item.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### lib/ezi18n (1)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCharsetInfo` | class | `lib/ezi18n/classes/ezcharsetinfo.php:25` | – | – |
