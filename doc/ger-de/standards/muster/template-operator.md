---
title: Template-Operator (Interpreter-Plugin)
audience: developer
category: standards
order: 313
summary: "Eine Klasse, die der Template-Sprache Operatoren hinzufügt: Sie listet sie in operatorList() auf und berechnet sie in modify(). 321 Instanzen in Exponential."
keywords: Entwurfsmuster, Template-Operator, Plugins, Musterkatalog, design pattern, template-operator, pattern catalogue
type: catalogue
---
# Template-Operator (Interpreter-Plugin)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Eine Klasse, die der Template-Sprache Operatoren hinzufügt: Sie listet sie in operatorList() auf und berechnet sie in modify().

|  |  |
| --- | --- |
| Gruppe | Plugins |
| Instanzen | 321 |
| Erkannt an | Registriert in einer eztemplateautoload.php ($eZTemplateOperatorArray[]) oder hat operatorList() und modify(). |
| Erweiterungspunkt | autoloads/eztemplateautoload.php einer Extension, aktiviert über site.ini/template.ini [TemplateSettings] ExtensionAutoloadPath[]. |

## Klassen nach Bereich

### extension/bcwebsitestatistics (1)

[Alle Muster von extension/bcwebsitestatistics](bereich-extension-bcwebsitestatistics.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `BCWebsiteStatisticsOperators` | class | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php:30` | – | – |

### extension/cjw_newsletter (2)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterOperators` | class | `extension/cjw_newsletter/autoloads/cjwnewsletteroperators.php:21` | php preg_replace \ | Template-Operator str_replace | – |
| `CjwNewsletterRenderingOperators` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrenderingoperators.php:24` | Die Template-Operatoren des Newsletter-Renderings (cjw_newsletter 4.2.0): | – |

### extension/enhancedezbinaryfile (1)

[Alle Muster von extension/enhancedezbinaryfile](bereich-extension-enhancedezbinaryfile.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `TemplateParseXMLOperator` | class | `extension/enhancedezbinaryfile/autoloads/templateparsexmloperator.php:18` | – | – |

### extension/explayouts (1)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ExplBlockOperator` | class | `extension/explayouts/classes/explblockoperator.php:13` | Template-Operatoren für die {explblock}-Extraktion. | – |

### extension/explayouts_ui (1)

[Alle Muster von extension/explayouts_ui](bereich-extension-explayouts-ui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsUiTemplateOperators` | class | `extension/explayouts_ui/classes/explayoutsuitemplateoperators.php:11` | Template-Operatoren von explayouts_ui. | – |

### extension/expui (1)

[Alle Muster von extension/expui](bereich-extension-expui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUITemplateOperators` | class | `extension/expui/classes/expuitemplateoperators.php:17` | – | – |

### extension/ezdemo (4)

[Alle Muster von extension/ezdemo](bereich-extension-ezdemo.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZArchive` | class | `extension/ezdemo/autoloads/ezarchive.php:28` | – | – |
| `eZKeywordList` | class | `extension/ezdemo/autoloads/ezkeywordlist.php:28` | – | – |
| `eZPageData` | class | `extension/ezdemo/autoloads/ezpagedata.php:40` | – | – |
| `eZTagCloud` | class | `extension/ezdemo/autoloads/eztagcloud.php:28` | – | – |

### extension/ezfind (1)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfTemplateOperators` | class | `extension/ezfind/classes/ezfTemplateOperators.php:13` | Operatoren für eZFind | – |

### extension/ezflow (5)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFeedReader` | class | `extension/ezflow/autoloads/ezfeedreader.php:32` | Die Klasse eZFeedReader implementiert die Methoden des Template-Operators feedreader | – |
| `eZJSON` | class | `extension/ezflow/autoloads/ezjson.php:32` | Die Klasse eZJSON implementiert die Methoden des Template-Operators json | – |
| `eZPageLink` | class | `extension/ezflow/autoloads/ezpagelink.php:32` | Die Klasse eZPageLink implementiert die Methoden des Template-Operators pagelink | – |
| `eZRed5StreamListOperator` | class | `extension/ezflow/autoloads/ezred5streamlist.php:32` | Die Klasse eZRed5StreamListOperator implementiert die Methoden des Template-Operators red5list | – |
| `eZUnserialize` | class | `extension/ezflow/autoloads/ezunserialize.php:32` | Die Klasse eZUnserialize implementiert die Methoden des Template-Operators unserialize | – |

### extension/ezjscore (3)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscAccessTemplateFunctions` | class | `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php:40` | Eigener Has-Access-Aufruf, mit dem Sie auch prüfen können, ob der Benutzer Zugriff auf die angegebene(n) Einschränkung(en) hat | – |
| `ezjscEncodingTemplateFunctions` | class | `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php:44` | Template-Operatoren rund um ezjscAjaxContent | – |
| `ezjscPackerTemplateFunctions` | class | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php:86` | Template-Operatoren rund um ezjsPacker zum Zusammenführen und Packen von JavaScript- oder Stylesheet-Dateien, um Größe und Anzahl der Dateien zu verringern (wie in reduce… | – |

### extension/ezoe (1)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZOETemplateUtils` | class | `extension/ezoe/autoloads/ezoetemplateutils.php:37` | – | – |

### extension/ezstarrating (1)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsrTemplateOperators` | class | `extension/ezstarrating/autoloads/ezsrtemplateoperators.php:27` | – | – |

### extension/eztags (2)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTagsCloud` | class | `extension/eztags/autoloads/eztagscloud.php:6` | Die Klasse eZTagsCloud implementiert die Methode des Template-Operators eztagscloud | – |
| `eZTagsTemplateFunctions` | class | `extension/eztags/autoloads/eztagstemplatefunctions.php:6` | Die Klasse eZTagsTemplateFunctions implementiert die Methoden des Template-Operators eztags | – |

### extension/ezurlmultisite (2)

[Alle Muster von extension/ezurlmultisite](bereich-extension-ezurlmultisite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLMultisiteOperator` | class | `extension/ezurlmultisite/classes/ezurlmultisiteoperator.php:3` | – | – |
| `eZURLMultisiteURLOperator` | class extends eZURLOperator | `extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php:24` | Der eZURLOperator des Kernels mit einem multisite-fähigen ezurl. | – |

### extension/ezwt (1)

[Alle Muster von extension/ezwt](bereich-extension-ezwt.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCreateClassListGroups` | class | `extension/ezwt/autoloads/ezcreateclasslistgroups.php:28` | – | – |

### extension/hcaptcha (1)

[Alle Muster von extension/hcaptcha](bereich-extension-hcaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `hCaptchaTemplateOperator` | class | `extension/hcaptcha/autoloads/hcaptchatemplateoperator.php:22` | hCaptcha-Extension für Exponential, geschrieben von 7x, basierend auf Arbeiten von: Bruce Morrison | – |

### extension/owsimpleoperator (1)

[Alle Muster von extension/owsimpleoperator](bereich-extension-owsimpleoperator.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `OWSimpleOperator` | class | `extension/owsimpleoperator/autoloads/owsimpleoperator.php:21` | – | – |

### extension/recaptcha (1)

[Alle Muster von extension/recaptcha](bereich-extension-recaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `reCAPTCHATemplateOperator` | class | `extension/recaptcha/autoloads/recaptchatemplateoperator.php:22` | reCAPTCHA-Extension für Exponential, geschrieben von Bruce Morrison | – |

### extension/sevenx_themes_media (1)

[Alle Muster von extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxThemesMediaOperators` | class | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:58` | – | – |

### extension/str_replace (1)

[Alle Muster von extension/str_replace](bereich-extension-str-replace.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `MyStrReplaceOperator` | class | `extension/str_replace/autoloads/str_replace_controloperator.php:3` | – | – |

### extension/swark (1)

[Alle Muster von extension/swark](bereich-extension-swark.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SwarkOperator` | class | `extension/swark/autoloads/classes/SwarkOperator.php:22` | – | – |

### extension/xrowmetadata (3)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowMetaDataLicenseOperator` | class | `extension/xrowmetadata/autoloads/xrowmetadatalicenseoperator.php:37` | Der Template-Operator xrowmetadata_license: die Inhaltslizenz eines Knotens oder der angezeigten Seite, bereit für den Seitenkopf und einen sichtbaren Hinweis. | – |
| `xrowMetaDataLicensePickerOperator` | class | `extension/xrowmetadata/autoloads/xrowmetadatalicensepickeroperator.php:14` | Template-Operatoren der Lizenzauswahl (xrowMetaDataLicensePicker), für Templates, die keinen Inhalt eines Metadaten-Attributs zur Hand haben, etwa die Kla… | – |
| `xrowMetaDataOperator` | class | `extension/xrowmetadata/autoloads/xrowmetadataoperator.php:3` | – | – |

### kernel/classes (1)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ExpInstallationOperator` | class | `kernel/classes/expinstallationoperator.php:10` | Template-Operatoren, die den Installationsnamen für Templates bereitstellen: | – |

### kernel/classes/audit (1)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAuditTemplateOperator` | class | `kernel/classes/audit/console/expaudittemplateoperator.php:13` | Der Template-Operator audit_label (doc/eng-us/bc/6.0/audit.md, "Template operator and fetch"): die lesbare Bezeichnung eines Audit-Ereignisnamens aus der Taxonomie-Regi… | – |

### kernel/common (19)

[Alle Muster von kernel/common](bereich-kernel-common.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentSecurityPolicyOperator` | class | `kernel/common/expcontentsecuritypolicyoperator.php:21` | Der Template-Operator csp_nonce(): die Nonce der Content Security Policy dieser Anfrage, für ein Inline-Skript, das ein Template nicht in eine Datei verschieben kann… | – |
| `expEnvironmentOperator` | class | `kernel/common/expenvironmentoperator.php:29` | – | – |
| `eZAlphabetOperator` | class | `kernel/common/ezalphabetoperator.php:19` | – | – |
| `eZAutoLinkOperator` | class | `kernel/common/ezautolinkoperator.php:12` | – | – |
| `eZContentStructureTreeOperator` | class | `kernel/common/ezcontentstructuretreeoperator.php:17` | – | – |
| `eZDateOperatorCollection` | class | `kernel/common/ezdateoperatorcollection.php:12` | – | – |
| `eZi18nOperator` | class | `kernel/common/ezi18noperator.php:18` | – | – |
| `eZKernelOperator` | class | `kernel/common/ezkerneloperator.php:17` | – | – |
| `eZModuleOperator` | class | `kernel/common/ezmoduleoperator.php:19` | – | – |
| `eZModuleParamsOperator` | class | `kernel/common/ezmoduleparamsoperator.php:32` | – | – |
| `eZPackageOperator` | class | `kernel/common/ezpackageoperator.php:18` | – | – |
| `ezpSiteAccessMenuOperator` | class | `kernel/common/ezpsiteaccessmenuoperator.php:24` | Template-Operatoren des Site-Menüs der Administrationsoberfläche (ezpSiteAccessMenu): | – |
| `ezpSiteAccessURLOperator` | class | `kernel/common/ezpsiteaccessurloperator.php:22` | Template-Operator siteaccess_url: die Adresse der Startseite eines Siteaccess (ezpSiteAccessURL::root()), mit Schema, Host, Port und Siteaccess-Pf… | – |
| `eZSimpleTagsOperator` | class | `kernel/common/ezsimpletagsoperator.php:12` | – | – |
| `eZTOCOperator` | class | `kernel/common/eztocoperator.php:18` | – | – |
| `eZTopMenuOperator` | class | `kernel/common/eztopmenuoperator.php:19` | – | – |
| `eZTreeMenuOperator` | class | `kernel/common/eztreemenuoperator.php:12` | – | – |
| `eZURLOperator` | class | `kernel/common/ezurloperator.php:18` | – | – |
| `eZWordToImageOperator` | class | `kernel/common/ezwordtoimageoperator.php:17` | – | – |

### kernel/private (1)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpLanguageSwitcherOperator` | class | `kernel/private/eztemplate/ezplanguageswitcheroperator.php:12` | – | – |

### lib/ezpdf (1)

[Alle Muster von lib/ezpdf](bereich-lib-ezpdf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPDF` | class | `lib/ezpdf/classes/ezpdf.php:22` | – | – |

### lib/eztemplate (19)

[Alle Muster von lib/eztemplate](bereich-lib-eztemplate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTemplateArithmeticOperator` | class | `lib/eztemplate/classes/eztemplatearithmeticoperator.php:40` | – | – |
| `eZTemplateArrayOperator` | class | `lib/eztemplate/classes/eztemplatearrayoperator.php:28` | – | – |
| `eZTemplateAttributeOperator` | class | `lib/eztemplate/classes/eztemplateattributeoperator.php:56` | – | – |
| `eZTemplateControlOperator` | class | `lib/eztemplate/classes/eztemplatecontroloperator.php:26` | – | – |
| `eZTemplateDigestOperator` | class | `lib/eztemplate/classes/eztemplatedigestoperator.php:21` | – | – |
| `eZTemplateExecuteOperator` | class | `lib/eztemplate/classes/eztemplateexecuteoperator.php:18` | – | – |
| `eZTemplateExpInfoOperator` | class | `lib/eztemplate/classes/eztemplateexpinfooperator.php:36` | – | – |
| `eZTemplateImageOperator` | class | `lib/eztemplate/classes/eztemplateimageoperator.php:24` | – | – |
| `eZTemplateLocaleOperator` | class | `lib/eztemplate/classes/eztemplatelocaleoperator.php:30` | – | – |
| `eZTemplateLogicOperator` | class | `lib/eztemplate/classes/eztemplatelogicoperator.php:69` | – | – |
| `eZTemplateNl2BrOperator` | class | `lib/eztemplate/classes/eztemplatenl2broperator.php:21` | – | – |
| `eZTemplateOperator` | class | `lib/eztemplate/classes/eztemplateoperator.php:20` | – | – |
| `eZTemplatePHPOperator` | class | `lib/eztemplate/classes/eztemplatephpoperator.php:30` | – | – |
| `eZTemplateRoleOperator` | class | `lib/eztemplate/classes/eztemplateroleoperator.php:24` | Die Klasse eZTemplateRoleOperator stellt Template-Operatoren zur Prüfung der Rollenzugehörigkeit von Benutzern bereit | – |
| `eZTemplateStringOperator` | class | `lib/eztemplate/classes/eztemplatestringoperator.php:22` | – | – |
| `eZTemplateStringsOperator` | class | `lib/eztemplate/classes/eztemplatestringsoperator.php:80` | – | – |
| `eZTemplateTextOperator` | class | `lib/eztemplate/classes/eztemplatetextoperator.php:18` | – | – |
| `eZTemplateTypeOperator` | class | `lib/eztemplate/classes/eztemplatetypeoperator.php:36` | – | – |
| `eZTemplateUnitOperator` | class | `lib/eztemplate/classes/eztemplateunitoperator.php:39` | – | – |

## Template-Operatoren (76)

### extension/bcwebsitestatistics (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| bc_ga_urchin, bc_ga_urchinOrder, bc_ga_xmlAttributeValue, bc_ga_jsEscapedString, bc_ga_formatNumericDecimal, bc_ga_script_url | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/eztemplateautoload.php:34` |

### extension/cjw_newsletter (2)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| cjw_newsletter_preg_replace, cjw_newsletter_str_replace, cjw_newsletter_variable | `CjwNewsletterOperators` | `extension/cjw_newsletter/autoloads/eztemplateautoload.php:16` |
| cjwnl_condition_open, cjwnl_condition_else, cjwnl_condition_close, cjwnl_plaintext, cjwnl_richtext, cjwnl_interests_block, cjwnl_abs_url, cjwnl_text_underline, cjwnl_text_wrap, cjwnl_rendering_data | `CjwNewsletterRenderingOperators` | `extension/cjw_newsletter/autoloads/eztemplateautoload.php:24` |

### extension/enhancedezbinaryfile (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| parsexml | `TemplateParseXMLOperator` | `extension/enhancedezbinaryfile/autoloads/eztemplateautoload.php:7` |

### extension/explayouts_ui (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| explayouts_ui_has_template | `expLayoutsUiTemplateOperators` | `extension/explayouts_ui/autoloads/eztemplateautoload.php:3` |

### extension/expui (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| exp_config | `expUITemplateOperators` | `extension/expui/autoloads/eztemplateautoload.php:8` |

### extension/ezdemo (4)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezkeywordlist | `eZKeywordList` | `extension/ezdemo/autoloads/eztemplateautoload.php:30` |
| ezarchive | `eZArchive` | `extension/ezdemo/autoloads/eztemplateautoload.php:33` |
| eztagcloud | `eZTagCloud` | `extension/ezdemo/autoloads/eztemplateautoload.php:36` |
| ezpagedata, ezpagedata_set, ezpagedata_append | `eZPageData` | `extension/ezdemo/autoloads/eztemplateautoload.php:39` |

### extension/ezfind (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| solr_escape, solr_quotes_escape | `ezfTemplateOperators` | `extension/ezfind/autoloads/eztemplateautoload.php:12` |

### extension/ezflow (5)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| unserialize | `eZUnserialize` | `extension/ezflow/autoloads/eztemplateautoload.php:29` |
| json | `eZJSON` | `extension/ezflow/autoloads/eztemplateautoload.php:32` |
| feedreader | `eZFeedReader` | `extension/ezflow/autoloads/eztemplateautoload.php:35` |
| red5list | `eZRed5StreamListOperator` | `extension/ezflow/autoloads/eztemplateautoload.php:38` |
| pagelink | `eZPageLink` | `extension/ezflow/autoloads/eztemplateautoload.php:41` |

### extension/ezjscore (3)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:18` |
| json_encode, xml_encode, node_encode | `ezjscEncodingTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:29` |
| has_access_to_limitation | `ezjscAccessTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:36` |

### extension/ezoe (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezoe_ini_section | `eZOETemplateUtils` | `extension/ezoe/autoloads/eztemplateautoload.php:13` |

### extension/ezstarrating (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| fetch_starrating_data, fetch_starrating_stats, fetch_by_starrating | `ezsrTemplateOperators` | `extension/ezstarrating/autoloads/eztemplateautoload.php:14` |

### extension/eztags (2)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| eztags_parent_string, latest_tags, user_limitations, tag_icon | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztemplateautoload.php:10` |
| eztagscloud | `eZTagsCloud` | `extension/eztags/autoloads/eztemplateautoload.php:14` |

### extension/ezurlmultisite (2)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable | `eZURLMultisiteURLOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:14` |
| ezurl_path, ezurl_siteaccess, ezurl_roots | `eZURLMultisiteOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:21` |

### extension/ezwebin (4)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezkeywordlist | `eZKeywordList` | `extension/ezwebin/autoloads/eztemplateautoload.php:30` |
| ezarchive | `eZArchive` | `extension/ezwebin/autoloads/eztemplateautoload.php:33` |
| eztagcloud | `eZTagCloud` | `extension/ezwebin/autoloads/eztemplateautoload.php:36` |
| ezpagedata, ezpagedata_set, ezpagedata_append | `eZPageData` | `extension/ezwebin/autoloads/eztemplateautoload.php:39` |

### extension/ezwt (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezcreateclasslistgroups | `eZCreateClassListGroups` | `extension/ezwt/autoloads/eztemplateautoload.php:29` |

### extension/hcaptcha (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| – | `hCaptchaTemplateOperator` | `extension/hcaptcha/autoloads/eztemplateautoload.php:3` |

### extension/recaptcha (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| – | `reCAPTCHATemplateOperator` | `extension/recaptcha/autoloads/eztemplateautoload.php:23` |

### extension/str_replace (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezstr_replace | `MyStrReplaceOperator` | `extension/str_replace/autoloads/eztemplateautoload.php:7` |

### extension/xrowmetadata (3)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| metadata | `xrowMetaDataOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:4` |
| xrowmetadata_license | `xrowMetaDataLicenseOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:11` |
| xrowmetadata_license_enabled, xrowmetadata_license_choices, xrowmetadata_license_info, xrowmetadata_license_inherited, xrowmetadata_license_class_settings | `xrowMetaDataLicensePickerOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:19` |

### kernel/common (21)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable | `eZURLOperator` | `kernel/common/eztemplateautoload.php:14` |
| i18n, x18n, d18n | `eZi18nOperator` | `kernel/common/eztemplateautoload.php:18` |
| alphabet | `eZAlphabetOperator` | `kernel/common/eztemplateautoload.php:21` |
| month_overview | `eZDateOperatorCollection` | `kernel/common/eztemplateautoload.php:24` |
| autolink | `eZAutoLinkOperator` | `kernel/common/eztemplateautoload.php:27` |
| simpletags | `eZSimpleTagsOperator` | `kernel/common/eztemplateautoload.php:30` |
| treemenu | `eZTreeMenuOperator` | `kernel/common/eztemplateautoload.php:33` |
| content_structure_tree | `eZContentStructureTreeOperator` | `kernel/common/eztemplateautoload.php:36` |
| wordtoimage, mimetype_icon, class_icon, classgroup_icon, action_icon, icon, flag_icon, icon_info | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php:39` |
| ezpreference | `eZKernelOperator` | `kernel/common/eztemplateautoload.php:44` |
| siteaccess_url | `ezpSiteAccessURLOperator` | `kernel/common/eztemplateautoload.php:47` |
| siteaccess_menu, siteaccess_menu_key | `ezpSiteAccessMenuOperator` | `kernel/common/eztemplateautoload.php:50` |
| module_params | `eZModuleParamsOperator` | `kernel/common/eztemplateautoload.php:57` |
| topmenu | `eZTopMenuOperator` | `kernel/common/eztemplateautoload.php:60` |
| ezpackage | `eZPackageOperator` | `kernel/common/eztemplateautoload.php:63` |
| eztoc | `eZTOCOperator` | `kernel/common/eztemplateautoload.php:66` |
| ezmodule | `eZModuleOperator` | `kernel/common/eztemplateautoload.php:69` |
| installation_name, is_production_system | `ExpInstallationOperator` | `kernel/common/eztemplateautoload.php:72` |
| audit_label | `expAuditTemplateOperator` | `kernel/common/eztemplateautoload.php:75` |
| exp_environment, exp_environment_badge | `expEnvironmentOperator` | `kernel/common/eztemplateautoload.php:78` |
| csp_nonce | `expContentSecurityPolicyOperator` | `kernel/common/eztemplateautoload.php:81` |

### kernel/private (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| language_switcher | `ezpLanguageSwitcherOperator` | `kernel/private/eztemplate/eztemplateautoload.php:13` |

### lib/ezpdf (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| pdf | `eZPDF` | `lib/ezpdf/classes/eztemplateautoload.php:14` |

### lib/eztemplate (17)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| array, hash, array_prepend, prepend, array_append, append, array_merge, merge, contains, compare, extract, extract_left, extract_right, begins_with, ends_with, implode, explode, repeat, reverse, insert, remove, replace, unique, array_sum | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php:13` |
| fetch, fetch_alias | `eZTemplateExecuteOperator` | `lib/eztemplate/classes/eztemplateautoload.php:42` |
| l10n, locale, datetime, currentdate, maketime, makedate, gettime | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php:44` |
| attribute, dump | `eZTemplateAttributeOperator` | `lib/eztemplate/classes/eztemplateautoload.php:46` |
| nl2br | `eZTemplateNl2BrOperator` | `lib/eztemplate/classes/eztemplateautoload.php:48` |
| concat, indent | `eZTemplateTextOperator` | `lib/eztemplate/classes/eztemplateautoload.php:50` |
| si | `eZTemplateUnitOperator` | `lib/eztemplate/classes/eztemplateautoload.php:52` |
| lt, gt, le, ge, eq, ne, null, not, true, false, or, and, choose | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php:54` |
| is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_class, is_null, is_set, is_unset, get_type, get_class | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php:59` |
| cond, first_set | `eZTemplateControlOperator` | `lib/eztemplate/classes/eztemplateautoload.php:64` |
| sum, sub, inc, dec, div, mod, mul, max, min, abs, ceil, floor, round, int, float, count, roman, rand | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php:67` |
| texttoimage, image, imagefile | `eZTemplateImageOperator` | `lib/eztemplate/classes/eztemplateautoload.php:77` |
| upcase, downcase, count_words, count_chars, trim, break, wrap, upfirst, upword, simplify, trim, wash, chr, ord, shorten, pad | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php:83` |
| crc32, md5, rot13 | `eZTemplateDigestOperator` | `lib/eztemplate/classes/eztemplateautoload.php:101` |
| expinfo | `eZTemplateExpInfoOperator` | `lib/eztemplate/classes/eztemplateautoload.php:106` |
| has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user | `eZTemplateRoleOperator` | `lib/eztemplate/classes/eztemplateautoload.php:110` |
| – | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php:115` |
