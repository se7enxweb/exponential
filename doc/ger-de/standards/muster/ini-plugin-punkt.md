---
title: INI-Plugin-Punkt (Erweiterungspunkt)
audience: developer
category: standards
order: 338
summary: Eine Einstellung, die die Klasse nennt, die der Kernel für eine Aufgabe verwendet, sodass eine Extension oder eine Website eine Klasse ersetzen oder hinzufügen kann, ohne Code zu ändern. 1376 Instanzen in Exponential.
keywords: Entwurfsmuster, INI-Plugin-Punkt, Plugins, Musterkatalog, design pattern, ini-plugin-point, pattern catalogue
type: catalogue
---
# INI-Plugin-Punkt (Erweiterungspunkt)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine Einstellung, die die Klasse nennt, die der Kernel für eine Aufgabe verwendet, sodass eine Extension oder eine Website eine Klasse ersetzen oder hinzufügen kann, ohne Code zu ändern.

| | |
| --- | --- |
| Gruppe | Plugins |
| Instanzen | 1376 |
| Erkannt an | Eine Einstellung einer settings/*.ini-Datei (oder einer Extension), deren Wert der Name einer Klasse des Katalogs ist. |
| Erweiterungspunkt | Setzen Sie die Einstellung in settings/FILE.ini.append.php einer Extension oder in settings/override/. |

## Klassen nach Bereich

### extension/bccie (25)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bccieExportFormatOutputHandlerCP1252` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerCP1252.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[cp1252] |
| `bccieExportFormatOutputHandlerUtf8` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[utf8] |
| `bccieExportFormatOutputHandlerUtf8Bom` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8Bom.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[utf8bom] |
| `bccieExportFormatOutputHandlerUtf16Le` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf16Le.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[utf16le] |
| `eZBirthdayExportHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayexporthandler.php:13` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBirthdayHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayhandler.php:11` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBooleanHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbooleanhandler.php:11` | – | extension/bccie/settings/export.ini [ezboolean] HandlerClass |
| `eZCountryHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezcountrtyhandler.php:11` | – | extension/bccie/settings/export.ini [ezcountry] HandlerClass |
| `eZDateHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatehandler.php:11` | – | extension/bccie/settings/export.ini [ezdate] HandlerClass |
| `eZDateTimeHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatetimehandler.php:11` | – | extension/bccie/settings/export.ini [ezdatetime] HandlerClass |
| `eZEmailHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezemailhandler.php:11` | – | extension/bccie/settings/export.ini [ezemail] HandlerClass |
| `ezenhancedobjectrelationHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezenhancedobjectrelationhandler.php:11` | – | extension/bccie/settings/export.ini [ezenhancedobjectrelation] HandlerClass |
| `eZFloatHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezfloathandler.php:11` | – | extension/bccie/settings/export.ini [ezfloat] HandlerClass |
| `eZIdentifierHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezidentifierhandler.php:11` | – | extension/bccie/settings/export.ini [ezidentifier] HandlerClass |
| `eZIntegerHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezintegerhandler.php:11` | – | extension/bccie/settings/export.ini [ezinteger] HandlerClass |
| `eZObjectRelationHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezobjectrelationhandler.php:11` | – | extension/bccie/settings/export.ini [ezobjectrelation] HandlerClass |
| `eZOptionHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezoptionhandler.php:11` | – | extension/bccie/settings/export.ini [ezoption] HandlerClass |
| `eZSelectionHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezselectionhandler.php:11` | – | extension/bccie/settings/export.ini [ezselection] HandlerClass |
| `eZStringHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezstringhandler.php:11` | – | extension/bccie/settings/export.ini [ezstring] HandlerClass |
| `eZTextHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/eztexthandler.php:11` | – | extension/bccie/settings/export.ini [eztext] HandlerClass |
| `eZTimeHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/eztimehandler.php:11` | – | extension/bccie/settings/export.ini [eztime] HandlerClass |
| `eZURLHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezurlhandler.php:11` | – | extension/bccie/settings/export.ini [ezurl] HandlerClass |
| `eZXMLTextHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezxmltexthandler.php:11` | – | extension/bccie/settings/export.ini [ezxmltext] HandlerClass |
| `OWEnhancedSelectionHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/owenhancedselectionhandler.php:11` | – | extension/bccie/settings/export.ini [owenhancedselection] HandlerClass |
| `smileobjectrelationlistHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/smileobjectrelationlisthandler.php:11` | – | extension/bccie/settings/export.ini [smileobjectrelationlist] HandlerClass |

### extension/birthday (1)

[Alle Muster von extension/birthday](bereich-extension-birthday.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZBirthdayCsvHandler` | class extends XrowBaseHandler | `extension/birthday/classes/parsers/ezbirthdaycsvhandler.php:29` | CSV-Export-Handler für den Datentyp ezbirthday. | extension/birthday/settings/csv.ini [ezbirthday] HandlerClass |

### extension/cjw_newsletter (20)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwNewsletterAuditBranch` | class | `extension/cjw_newsletter/classes/cjwnewsletterauditbranch.php:12` | Der Zweig des Newsletters in der Audit-Taxonomie, registriert in settings/audit.ini.append.php ([AuditEventSettings] Branches[cjw_newsletter]). | extension/cjw_newsletter/settings/audit.ini [AuditEventSettings] Branches[cjw_newsletter] |
| `CjwNewsletterDeliverabilityHooks` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterdeliverabilityhooks.php:25` | Der Erweiterungspunkt-Handler des Bereichs „Zustellbarkeit“ (cjw_newsletter.ini [ExtensionPointSettings] Handlers[]=CjwNewsletterDeliverabilityHooks, se… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterEditionFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewslettereditionfilter.php:32` | Dieser Filter erlaubt es, alle Listen nach einem bestimmten Status abzurufen, Statusvarianten: -›DRAFT -›PROCESS | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterEditionFilter] ClassName |
| `CjwNewsletterEditorialHooks` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialhooks.php:20` | Der Erweiterungspunkt-Handler des Bereichs Redaktion (registriert in cjw_newsletter.ini [ExtensionPointSettings] Handlers[]): die wiederkehrenden Versände laufen bei… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterFilterTypeEmail` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypeemail.php:19` | Klassenbeschreibung hier | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterFilterTypeSalutation` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypesalutation.php:19` | Klassenbeschreibung hier | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterImportExportHooks` | class | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportexporthooks.php:18` | Der Erweiterungspunkt-Handler des Bereichs Import/Export und Migration ([ExtensionPointSettings] Handlers[]). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterListFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewsletterlistfilter.php:29` | Dieser Filter erlaubt es, alle Listen nach einem bestimmten Siteaccess abzurufen; reserviertes Wort „current_siteaccess“ == aktuellen Siteaccess nehmen, um den Parameter siteaccess abzugleichen… | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterListFilter] ClassName |
| `CjwNewsletterMailCategoryHandler` | class | `extension/cjw_newsletter/classes/cjwnewslettermailcategoryhandler.php:20` | Der Handler der Kategorie „newsletter“ der E-Mail-Präferenzen (Exponential 6.0.15 und später; settings/mailpreferences.ini.append.php dieser Ext… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter] HandlerClass |
| `CjwNewsletterMailin` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailin.php:36` | An- und Abmelden per E-Mail (cjw_newsletter.ini [MailInSettings], die Adressen in cjwnl_mailin_address, jede bearbeitete Nachricht in cjwnl_maili… | extension/cjw_newsletter/settings/mailpreferences.ini [BounceSettings] MessageListeners[] |
| `CjwNewsletterMailPreferences` | class | `extension/cjw_newsletter/classes/cjwnewslettermailpreferences.php:25` | Die Brücke zwischen dem Newsletter und den E-Mail-Präferenzen von Exponential 6.0.15 und später (kernel/classes/mailpreferences). | extension/cjw_newsletter/settings/mailpreferences.ini [SuppressionSettings] Listeners[] |
| `CjwNewsletterRenderingHooks` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrenderinghooks.php:25` | Der Erweiterungspunkt-Handler des Renderings (Bereich N3, cjw_newsletter.ini [ExtensionPointSettings] Handlers[]). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterScheduleConditionNewArticles` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterscheduleconditionnewarticles.php:18` | Der Lauf findet nur statt, wenn der Artikelpool des Zeitplans Inhalte hat, die seit seinem letzten Versand veröffentlicht wurden (aus Sicht des anonymen Benutzers), in beiden… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ScheduleSettings] ConditionHandlers[] |
| `CjwNewsletterSmsCategoryHandler` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscategoryhandler.php:25` | Der Handler der Mail-Präferenzkategorie „sms“ des Kernels ([Category_sms] HandlerClass in settings/mailpreferences.ini.append.php der Extension). | extension/cjw_newsletter/settings/mailpreferences.ini [Category_sms] HandlerClass |
| `CjwNewsletterSmsHooks` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmshooks.php:27` | Der Handler des Bereichs N5 (SMS) an den Erweiterungspunkten (CjwNewsletterExtensionPoints), registriert in cjw_newsletter.ini [ExtensionPointSettings] Handle… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterSmsTransportFile` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportfile.php:23` | Das simulierte SMS-Senden: nichts verlässt den Server, jede SMS wird als eine JSON-Datei -.sms.json (to, from, text, segments, time) in Dir geschrieben. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_file] Class |
| `CjwNewsletterSmsTransportHttp` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporthttp.php:38` | Der generische SMS-Transport: eine HTTP-Anfrage pro SMS an eine beliebige Anbieter-API, abgebildet in der INI-Gruppe. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_http] Class |
| `CjwNewsletterSmsTransportTwilio` | class extends CjwNewsletterSmsTransportHttp | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporttwilio.php:29` | Eine Voreinstellung des generischen HTTP-Transports für APIs im Stil der Messages-Ressource von Twilio: ein Formular-POST an .../Accounts/{AccountSid}/Messages.json mi… | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_twilio] Class |
| `CjwNewsletterStatisticsCategoryHandler` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticscategoryhandler.php:23` | Der Handler der E-Mail-Präferenzkategorie „newsletter_statistics“ (cjw_newsletter 4.2.0, Bereich N4 Statistik): die Einwilligung in personenbezogene Zählung… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter_statistics] HandlerClass |
| `CjwNewsletterStatisticsHooks` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticshooks.php:31` | Der Erweiterungspunkt-Handler des Bereichs N4 Statistik ([ExtensionPointSettings] Handlers[]=CjwNewsletterStatisticsHooks). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |

### extension/cjw_themes_jumper (1)

[Alle Muster von extension/cjw_themes_jumper](bereich-extension-cjw-themes-jumper.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwJumperImagineImageFactory` | class extends eZImageFactory | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagefactory.php:10` | Bild-Handler-Fabrik des Konverters cjwImagine (image.ini [cjwImagine] Handler=cjwJumperImagineImageFactory): erzeugt cjwJumperImagineImageHandle… | extension/cjw_themes_jumper/settings/image.ini [cjwImagine] Handler |

### extension/cjw_tmv (1)

[Alle Muster von extension/cjw_tmv](bereich-extension-cjw-tmv.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwTmvEventsBlockHandler` | class | `extension/cjw_tmv/classes/cjwtmveventsblockhandler.php:11` | Der Layouts-Block „tmv_events“ (v2: Cjw\TmvBundle TmvEventsHandler): die Veranstaltungen eines tmv_container, mit der Filterleiste (Ansichtstyp Liste), nur d… | extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_tmv_events] Handler |

### extension/enhancedselection2 (1)

[Alle Muster von extension/enhancedselection2](bereich-extension-enhancedselection2.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfSolrDocumentFieldSckEnhancedSelection` | class extends ezfSolrDocumentFieldBase | `extension/enhancedselection2/classes/ezfsolrdocumentfieldsckenhancedselection.php:2` | – | extension/enhancedselection2/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[sckenhancedselection] |

### extension/expai (49)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAiAltTextFieldAction` | class extends expAiPromptFieldAction | `extension/expai/classes/alttext/expaialttextfieldaction.php:15` | Die Feldaktion „Write alt text“ ([FieldAction_alt_text], doc/alt-text.md) von ezimage-Feldern. | extension/expai/settings/expai.ini [FieldAction_alt_text] Handler |
| `expAiAltTextJobHandler` | class extends expQueueHandlerBase | `extension/expai/classes/alttext/expaialttextjobhandler.php:19` | Der Jobtyp expai.alttext (expAiAltText::queue(), der Bericht „Missing alt text“): fragt ein Vision-Modell nach dem Alternativtext eines Bildes und schreibt ihn in… | extension/expai/settings/expqueue.ini [JobType_expai.alttext] Handler |
| `expAiAltTextServerFunctions` | class extends expAiServerFunctions | `extension/expai/classes/alttext/expaialttextserverfunctions.php:21` | Die ezjscore-Funktion der Schaltfläche „Write alt text“ (ezjscore.ini [ezjscServer_expaialttext]; der Aufrufer braucht ezjscore/call mit der Funktionsliste… | extension/expai/settings/ezjscore.ini [ezjscServer_expaialttext] Class |
| `expAiAuditBranch` | class | `extension/expai/classes/audit/expaiauditbranch.php:15` | Der Audit-Taxonomiezweig von Exponential Intelligence (audit.ini [AuditEventSettings] Branches[expai]): die Namen, die expAiAudit aufzeichnet, alle in den Kanal… | extension/expai/settings/audit.ini [AuditEventSettings] Branches[expai] |
| `expAiAuditSource` | class extends expAuditSourceBase | `extension/expai/classes/audit/expaiauditsource.php:18` | Die Quelle von Exponential Intelligence in der Audit-Konsole (audit.ini [AuditSourceSettings] Sources[expai], doc/audit-trail.md): die Dashboard-Panel… | extension/expai/settings/audit.ini [AuditSourceSettings] Sources[expai] |
| `expAiBatchApi` | abstract class | `extension/expai/classes/lanes/expaibatchapi.php:27` | Die Batch-API eines Anbieters: viele Anfragen auf einmal einreichen, den Batch abfragen, die Ergebnisse abholen, abbrechen, löschen. | extension/expai/settings/rad.ini [Registry_expai_batch_apis] Implements |
| `expAiBatchPollHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaibatchpollhandler.php:24` | Der Jobtyp expai.batch.poll (Klasse system): fragt den Anbieter alle PollSeconds nach dem Zustand eines Batches (dazwischen zurückgestellt, ohne einen… | extension/expai/settings/expqueue.ini [JobType_expai.batch.poll] Handler |
| `expAiBatchSubmitHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaibatchsubmithandler.php:20` | Der Jobtyp expai.batch.submit (Klasse system): sammelt die wartenden expai.request-Jobs eines Modells und einer Batch-Lane, höchstens MaxRequestsPerBatch, und reicht… | extension/expai/settings/expqueue.ini [JobType_expai.batch.submit] Handler |
| `expAiChainApplier` | class | `extension/expai/classes/chain/expaichainapplier.php:14` | Der Applier „chain“ der eingereihten Anfragen ([QueueSettings] Appliers[chain]): übergibt die gespeicherte Antwort eines Kettenschritts an expAiChainEngine::onAnswer(), das… | extension/expai/settings/expai.ini [QueueSettings] Appliers[chain] |
| `expAiChainTranslateSync` | class | `extension/expai/classes/chain/expaichaintranslatesync.php:20` | Die mitgelieferte Übersetzungsübergabe der Ketten ([ChainSettings] TranslateHandler=expAiChainTranslateSync): Der Übersetzungsschritt übergibt das Objekt des Laufs an den… | extension/expai/settings/expai.ini [ChainSettings] TranslateHandler |
| `expAiDraftRevertServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/diff/expaidraftrevertserverfunctions.php:20` | „Was sich geändert hat“ bei einem direkt geschriebenen Entwurf (ezjscore.ini [ezjscServer_expairevert], doc/review.md): | extension/expai/settings/ezjscore.ini [ezjscServer_expairevert] Class |
| `expAiExchangeRatesHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaiexchangerateshandler.php:15` | Der Jobtyp expai.exchange.rates (Klasse system, kein Modellaufruf, #392): holt einmal am Tag die Referenzkurse der Europäischen Zentralbank (expAiExch… | extension/expai/settings/expqueue.ini [JobType_expai.exchange.rates] Handler |
| `expAiFieldAction` | interface | `extension/expai/classes/fieldactions/expaifieldaction.php:16` | Eine Aktion neben einem Feld des Bearbeitungsformulars für Inhalt (schreiben, umschreiben, kürzen, übersetzen, vorschlagen, ...). | extension/expai/settings/rad.ini [Registry_expai_field_actions] Entries[Handler]; extension/expai/settings/rad.ini [Registry_expai_presets] Entries[Handler] |
| `expAiModelServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/expaimodelserverfunctions.php:24` | Die ezjscore-Funktionen der Aktionen „Test prompt“ und „Load model“ von expai/models (ezjscore.ini [ezjscServer_expaimodel]; der Aufrufer braucht ezjscor… | extension/expai/settings/ezjscore.ini [ezjscServer_expaimodel] Class |
| `expAiModelTestHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaimodeltesthandler.php:16` | Der Jobtyp expai.model.test: das „Test prompt“ (Aktion test) und „Load model“ (Aktion load) von expai/models, vom Worker als der Benutzer ausgeführt, der geklickt hat… | extension/expai/settings/expqueue.ini [JobType_expai.model.test] Handler |
| `expAiPlanItemApplier` | class | `extension/expai/classes/vector/expaiplanitemapplier.php:17` | Der Applier vector_plan_item ([QueueSettings] Appliers[]): Die Antwort eines Elements eines genehmigten Plans, das auf einer Batch-Lane lief (expAiPlanBatch), wird… | extension/expai/settings/expai.ini [QueueSettings] Appliers[vector_plan_item] |
| `expAiPresetServerFunctions` | class extends expAiServerFunctions | `extension/expai/classes/fieldactions/expaipresetserverfunctions.php:21` | Die ezjscore-Funktion der Redakteur-Voreinstellungen und des Menüs „Improve“ (ezjscore.ini [ezjscServer_expaipreset]; der Aufrufer braucht ezjscore/call mit der Funktionsliste… | extension/expai/settings/ezjscore.ini [ezjscServer_expaipreset] Class |
| `expAiPromptFieldAction` | class | `extension/expai/classes/fieldactions/expaipromptfieldaction.php:15` | Eine Feldaktion, gesteuert durch ihren INI-Abschnitt: [FieldAction_] Instruction ist die Aufgabe; das Objekt, das Feld, sein aktueller Wert, die anderen Felder und die… | extension/expai/settings/expai.ini [FieldAction_improve_casual] Handler; extension/expai/settings/expai.ini [FieldAction_improve_clarify] Handler; extension/expai/settings/expai.ini [FieldAction_improve_expand] Handler; extension/expai/settings/expai.ini [FieldAction_improve_formal] Handler; extension/expai/settings/expai.ini [FieldAction_improve_grammar] Handler; extension/expai/settings/expai.ini [FieldAction_improve_shorten] Handler; extension/expai/settings/expai.ini [FieldAction_improve_simplify] Handler; extension/expai/settings/expai.ini [FieldAction_preset_keywords] Handler; extension/expai/settings/expai.ini [FieldAction_preset_meta_description] Handler; extension/expai/settings/expai.ini [FieldAction_preset_summary] Handler; extension/expai/settings/expai.ini [FieldAction_preset_title] Handler; extension/expai/settings/expai.ini [FieldAction_rewrite] Handler; extension/expai/settings/expai.ini [FieldAction_shorten] Handler; extension/expai/settings/expai.ini [FieldAction_suggest] Handler; extension/expai/settings/expai.ini [FieldAction_translate] Handler; extension/expai/settings/expai.ini [FieldAction_write] Handler |
| `expAiProvider` | interface | `extension/expai/classes/expaiprovider.php:16` | Ein Modell-Backend von expai. | extension/expai/settings/rad.ini [Registry_expai_provider_types] Implements |
| `expAiRequestApplier` | interface | `extension/expai/classes/queue/expairequestapplier.php:18` | Wendet die Antwort einer in die Warteschlange gestellten Anfrage (expai.request) an, z. B. | extension/expai/settings/rad.ini [Registry_expai_appliers] Entries[Appliers] |
| `expAiRequestHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expairequesthandler.php:28` | Der Jobtyp expai.request: ein Aufruf an ein Modell auf einer Lane (expAiQueue::request() reiht ihn ein). | extension/expai/settings/expqueue.ini [JobType_expai.request] Handler |
| `expAiRunWatch` | class extends ezjscServerFunctions | `extension/expai/classes/expairunwatch.php:20` | „Lauf beobachten“ bei „Schreiben starten“ des Dashboards: Die Anfrage öffnet den Tab Intelligence des gewählten Speicherorts, wo sie zu einem Thread wird, wenn die… | extension/expai/settings/ezjscore.ini [ezjscServer_expaiwatch] Class |
| `expAiSegmenter` | interface | `extension/expai/classes/diff/expaisegmenter.php:28` | Zerlegt den Wert eines Datentyps in Blöcke und Sätze und setzt Werte wieder zusammen (doc/diff.md). | extension/expai/settings/rad.ini [Registry_expai_segmenters] Entries[FallbackSegmenter]; extension/expai/settings/rad.ini [Registry_expai_segmenters] Entries[Segmenters] |
| `expAiSegmenterGeneric` | class extends expAiSegmenterBase | `extension/expai/classes/diff/expaisegmentergeneric.php:15` | Der Fallback für Datentypen ohne eigenen Segmentierer: Der Wert als Zeichenkette (toString()) ist ein Block, nicht in Sätze geteilt. | extension/expai/settings/expai.ini [DiffSettings] FallbackSegmenter |
| `expAiSegmenterKeyword` | class extends expAiSegmenterBase | `extension/expai/classes/diff/expaisegmenterkeyword.php:14` | ezkeyword: eine Menge. | extension/expai/settings/expai.ini [DiffSettings] Segmenters[ezkeyword] |
| `expAiSegmenterRichText` | class | `extension/expai/classes/diff/expaisegmenterrichtext.php:28` | Rich Text (ezxmltext-Speicher-XML oder HTML): strukturbewusste Blöcke (doc/diff.md). | extension/expai/settings/expai.ini [DiffSettings] Segmenters[ezrichtext]; extension/expai/settings/expai.ini [DiffSettings] Segmenters[ezxmltext] |
| `expAiSegmenterString` | class extends expAiSegmenterBase | `extension/expai/classes/diff/expaisegmenterstring.php:13` | ezstring (und ezemail, ezisbn): der Wert ist ein Block, aufgeteilt in Sätze. | extension/expai/settings/expai.ini [DiffSettings] Segmenters[ezemail]; extension/expai/settings/expai.ini [DiffSettings] Segmenters[ezisbn]; extension/expai/settings/expai.ini [DiffSettings] Segmenters[ezstring] |
| `expAiSegmenterText` | class extends expAiSegmenterBase | `extension/expai/classes/diff/expaisegmentertext.php:14` | eztext: Blöcke sind Absätze (durch Leerzeilen getrennt), aufgeteilt in Sätze. | extension/expai/settings/expai.ini [DiffSettings] Segmenters[eztext] |
| `expAiServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/expaiserverfunctions.php:24` | Die ezjscore-Serverfunktionen von expai (ezjscore.ini [ezjscServer_expai]): | extension/expai/settings/ezjscore.ini [ezjscServer_expai] Class |
| `expAiThreadItemHandler` | class extends expQueueHandlerBase | `extension/expai/classes/vector/expaithreaditemhandler.php:22` | Der Jobtyp expai.thread.item: ein Element eines genehmigten Plans als Kind-Lauf des Threads (doc/parallel-runs.md), neben den anderen Elementen. | extension/expai/settings/expqueue.ini [JobType_expai.thread.item] Handler |
| `expAiThreadTurnHandler` | class extends expQueueHandlerBase | `extension/expai/classes/vector/expaithreadturnhandler.php:23` | Der Jobtyp expai.thread.turn: ein Zug eines Threads (die Nachricht des Redakteurs und jeder Schritt, bis das Modell in Worten antwortet), vom Warteschlangen-Worker ausgeführt… | extension/expai/settings/expqueue.ini [JobType_expai.thread.turn] Handler |
| `expAiTool` | abstract class | `extension/expai/classes/expaitool.php:16` | Ein Tool, das der Assistent aufrufen kann. | extension/expai/settings/rad.ini [Registry_expai_tools] Entries[Class] |
| `expAiToolCreateDraft` | class extends expAiTool | `extension/expai/classes/tools/expaitoolcreatedraft.php:12` | create_draft: ein neues Objekt als Entwurf des aktuellen Benutzers unter einem Knoten (content/create des Benutzers). | extension/expai/settings/expai.ini [Tool_create_draft] Class |
| `expAiToolGetClassDefinition` | class extends expAiTool | `extension/expai/classes/tools/expaitoolgetclassdefinition.php:9` | get_class_definition: die Felder einer Inhaltsklasse | extension/expai/settings/expai.ini [Tool_get_class_definition] Class |
| `expAiToolGetNode` | class extends expAiTool | `extension/expai/classes/tools/expaitoolgetnode.php:9` | get_node: ein Knoten und die Felder seines Objekts (aktuelle Version), so wie der aktuelle Benutzer sie lesen darf | extension/expai/settings/expai.ini [Tool_get_node] Class |
| `expAiToolGetTreeContext` | class extends expAiTool | `extension/expai/classes/tools/expaitoolgettreecontext.php:9` | get_tree_context: wo ein Knoten sitzt: seine Vorfahren, seine Geschwister und die Anzahl seiner Kinder | extension/expai/settings/expai.ini [Tool_get_tree_context] Class |
| `expAiToolListChildren` | class extends expAiTool | `extension/expai/classes/tools/expaitoollistchildren.php:9` | list_children: die Kinder eines Knotens, die der aktuelle Benutzer lesen darf | extension/expai/settings/expai.ini [Tool_list_children] Class |
| `expAiToolListCreatableClasses` | class extends expAiTool | `extension/expai/classes/tools/expaitoollistcreatableclasses.php:9` | list_creatable_classes: die Klassen, die der aktuelle Benutzer unter einem Knoten anlegen darf | extension/expai/settings/expai.ini [Tool_list_creatable_classes] Class |
| `expAiToolProjectHistory` | class extends expAiWebTool | `extension/expai/classes/tools/expaitoolprojecthistory.php:13` | project_history: aktuelle Commits (Kurz-Hash, Datum, Titel; der Autor nur mit [HistorySettings] ShowAuthors) und Release-Notes der Repositories von… | extension/expai/settings/expai.ini [Tool_project_history] Class |
| `expAiToolSearchContent` | class extends expAiTool | `extension/expai/classes/tools/expaitoolsearchcontent.php:17` | search_content: Volltextsuche über den Inhalt, durch die Suchmaschine der Site (eZSearch: Solr von eZ Find, wenn ezfind aktiv ist, sonst die des Kernels… | extension/expai/settings/expai.ini [Tool_search_content] Class |
| `expAiToolUpdateDraft` | class extends expAiTool | `extension/expai/classes/tools/expaitoolupdatedraft.php:12` | update_draft: schreibt Felder in einen Entwurf eines bestehenden Objekts: den Entwurf, den diese Unterhaltung dafür geschrieben hat, wenn es einen gibt, sonst eine neue Version (conte… | extension/expai/settings/expai.ini [Tool_update_draft] Class |
| `expAiToolWebFetch` | class extends expAiWebTool | `extension/expai/classes/tools/expaitoolwebfetch.php:13` | web_fetch: liest eine Seite einer Site auf der Erlaubnisliste ([WebSettings] AllowedDomains[]) als Markdown (Titel, Überschriften, Links mit absoluten URLs), gekürzt auf [We… | extension/expai/settings/expai.ini [Tool_web_fetch] Class |
| `expAiToolWebSearch` | class extends expAiWebTool | `extension/expai/classes/tools/expaitoolwebsearch.php:14` | web_search: eine Websuche über den Anbieter von [SearchSettings] Provider (standardmäßig Brave Search); Ergebnisse sind Titel, URL und Snippet. | extension/expai/settings/expai.ini [Tool_web_search] Class |
| `expAiTranslationCheckHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaitranslationcheckhandler.php:15` | Der Jobtyp expai.translation.check (Klasse system, kein Modellaufruf): entscheidet, nachdem eine Version in der Ausgangssprache veröffentlicht wurde, für jede Übersetzung des… | extension/expai/settings/expqueue.ini [JobType_expai.translation.check] Handler |
| `expAiTranslationSyncApplier` | class | `extension/expai/classes/diff/expaitranslationsyncapplier.php:15` | Der Applier „translation_sync“ (expai.ini [QueueSettings] Appliers[translation_sync]): schreibt die Antwort eines Übersetzungs-Patches oder einer vollständigen Übersetzung in… | extension/expai/settings/expai.ini [QueueSettings] Appliers[translation_sync] |
| `expAiTranslationSyncServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/diff/expaitranslationsyncserverfunctions.php:23` | Die ezjscore-Funktionen des Abschnitts „Translations“ des Tabs Intelligence (ezjscore.ini [ezjscServer_expaitranslation]; der Aufrufer braucht ezjscore/… | extension/expai/settings/ezjscore.ini [ezjscServer_expaitranslation] Class |
| `expAiVectorServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/vector/expaivectorserverfunctions.php:23` | Die ezjscore-Funktionen von Threads, Vector und der Tastatur (ezjscore.ini [ezjscServer_expaivector]; der Aufrufer braucht ezjscore/call mit der Funktionsliste… | extension/expai/settings/ezjscore.ini [ezjscServer_expaivector] Class |
| `expAiWebSearchBrave` | class extends expAiWebSearchProvider | `extension/expai/classes/web/expaiwebsearchbrave.php:15` | Brave Search API (Websuche): GET ?q=...&count=... | extension/expai/settings/expai.ini [SearchProvider_brave] Class |
| `expAiWebSearchProvider` | abstract class | `extension/expai/classes/web/expaiwebsearchprovider.php:16` | Ein Websuchdienst für web_search. | extension/expai/settings/rad.ini [Registry_expai_search_providers] Entries[Class] |

### extension/expchangeclass (1)

[Alle Muster von extension/expchangeclass](bereich-extension-expchangeclass.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expChangeClassConverters` | class | `extension/expchangeclass/classes/converters.php:21` | – | extension/expchangeclass/settings/changeclass.ini [ezobjectrelationlist] Class; extension/expchangeclass/settings/changeclass.ini [ezstring] Class; extension/expchangeclass/settings/changeclass.ini [eztext] Class |

### extension/explayouts (47)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsAccordionBlockHandler` | class | `extension/explayouts/classes/explayoutsaccordionblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_accordion] Handler |
| `expLayoutsAdminLeftMenuBlockHandler` | class | `extension/explayouts/classes/explayoutsadminleftmenublockhandler.php:7` | Der linke Menüblock der Admin-Layouts. | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_left_menu] Handler |
| `expLayoutsAdminPartBlockHandler` | class | `extension/explayouts/classes/explayoutsadminpartblockhandler.php:7` | Der Handler der Admin-Blöcke, die keinen Parameter nehmen (Design admin4l): der Block zeigt einen Teil der Admin-Seite, gezeichnet von seinem Ansichts-Template desig… | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_bookmarks] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_breadcrumb] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_clear_cache] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_content_tree] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_current_user] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_debug_area] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_footer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_logo] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_menu_button] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_module_result] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_overlay] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_popup_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_preferences] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_quick_settings] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_search] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_controls] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_toggles] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_tab_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_theme_switch] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_developer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_right] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_ui_switch] Handler |
| `expLayoutsAjaxLoadMoreServer` | class | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer-Funktionen zum Laden seitenweiser Collection-Blöcke. | extension/explayouts/settings/ezjscore.ini [ezjscServer_expajaxloadmore] Class; extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini [ezjscServer_expajaxloadmore] Class |
| `expLayoutsAlertBlockHandler` | class | `extension/explayouts/classes/explayoutsalertblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_alert] Handler |
| `expLayoutsBadgeBlockHandler` | class | `extension/explayouts/classes/explayoutsbadgeblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_badge] Handler |
| `expLayoutsButtonBlockHandler` | class | `extension/explayouts/classes/explayoutsbuttonblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_button] Handler |
| `expLayoutsCardBlockHandler` | class | `extension/explayouts/classes/explayoutscardblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_card] Handler |
| `expLayoutsCarouselBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutscarouselblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_carousel] Handler |
| `expLayoutsChildrenQueryHandler` | class | `extension/explayouts/classes/explayoutschildrenqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_children] Handler |
| `expLayoutsComponentBlockHandler` | class | `extension/explayouts/classes/explayoutscomponentblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_about] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_hero] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_lead] Handler |
| `expLayoutsContainerBlockHandler` | class | `extension/explayouts/classes/explayoutscontainerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_column] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_four_columns] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_three_columns] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_two_columns] Handler |
| `expLayoutsContentByTopicQueryHandler` | class | `extension/explayouts/classes/explayoutscontentbytopicqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_content_by_topic] Handler |
| `expLayoutsContentComponentBlockHandler` | class | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php:17` | Handler für die inhaltsgestützten Komponentenblöcke (hero, features, about, logos, quote, lead). | extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_about] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_features] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_hero] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_lead] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_logos] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_quote] Handler |
| `expLayoutsDebugBarPanel` | class | `extension/explayouts/classes/explayoutsdebugbarpanel.php:16` | Das Panel Exp Layouts der Exp-Debug-Leiste: welches Layout die Seite gebaut hat, welche Regel es gewählt hat, seine Zonen und Blöcke mit Links, um sie in der Adm… | extension/explayouts/settings/debugbar.ini [Panel_explayouts] Class |
| `expLayoutsDividerBlockHandler` | class | `extension/explayouts/classes/explayoutsdividerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_divider] Handler |
| `expLayoutsExponentialContentSearchQueryHandler` | class | `extension/explayouts/classes/explayoutsexponentialcontentsearchqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exponential_content_search] Handler |
| `expLayoutsFeaturesBlockHandler` | class extends expLayoutsComponentBlockHandler | `extension/explayouts/classes/explayoutsfeaturesblockhandler.php:8` | Der Features-Block: die Felder der anderen Komponentenblöcke (Titel, Text, Bild, Link) und eine Liste von Features, eines je Zeile als „Title\|Text“, die… | extension/explayouts/settings/explayouts.ini [BlockDefinition_features] Handler |
| `expLayoutsFullViewBlockHandler` | class | `extension/explayouts/classes/explayoutsfullviewblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_full_view] Handler |
| `expLayoutsGalleryBlockHandler` | class | `extension/explayouts/classes/explayoutsgalleryblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_grid_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_logos] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_slider] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_sushi_bar] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_thumb_gallery] Handler |
| `expLayoutsGridBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutsgridblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_grid] Handler |
| `expLayoutsHtmlBlockHandler` | class | `extension/explayouts/classes/explayoutshtmlblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_html] Handler |
| `expLayoutsImageBlockHandler` | class | `extension/explayouts/classes/explayoutsimageblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_image] Handler |
| `expLayoutsLatestQueryHandler` | class | `extension/explayouts/classes/explayoutslatestqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_latest] Handler |
| `expLayoutsListBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutslistblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_list] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_list_accordion] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_list_zigzag] Handler; extension/sevenx_themes_media/settings/explayouts.ini [BlockDefinition_product_highlight] Handler |
| `expLayoutsManualQueryHandler` | class | `extension/explayouts/classes/explayoutsmanualqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_manual] Handler |
| `expLayoutsMapBlockHandler` | class | `extension/explayouts/classes/explayoutsmapblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_map] Handler |
| `expLayoutsMarkdownBlockHandler` | class | `extension/explayouts/classes/explayoutsmarkdownblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_markdown] Handler |
| `expLayoutsParentQueryHandler` | class | `extension/explayouts/classes/explayoutsparentqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_parent] Handler |
| `expLayoutsProgressBlockHandler` | class | `extension/explayouts/classes/explayoutsprogressblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_progress] Handler |
| `expLayoutsQuoteBlockHandler` | class | `extension/explayouts/classes/explayoutsquoteblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_quote] Handler |
| `expLayoutsRandomQueryHandler` | class | `extension/explayouts/classes/explayoutsrandomqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_random] Handler |
| `expLayoutsRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsrelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_relation_list] Handler |
| `expLayoutsReverseRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_reverse_relation_list] Handler |
| `expLayoutsReverseRelationProvider` | class | `extension/explayouts/classes/explayoutsreverserelationprovider.php:15` | Die Layouts-Blöcke, die Inhalte nennen, die ein Entfernen oder Verbergen wegnimmt, für die Prüfung umgekehrter Relationen des Kernels (content.ini [ReverseRelationSettings] P… | extension/explayouts/settings/content.ini [ReverseRelationSettings] ProviderClassList[] |
| `expLayoutsRichTextBlockHandler` | class | `extension/explayouts/classes/explayoutsrichtextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_rich_text] Handler |
| `expLayoutsSiblingsQueryHandler` | class | `extension/explayouts/classes/explayoutssiblingsqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_siblings] Handler |
| `expLayoutsSingleBlockHandler` | class | `extension/explayouts/classes/explayoutssingleblockhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_single] Handler |
| `expLayoutsSpacerBlockHandler` | class | `extension/explayouts/classes/explayoutsspacerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_spacer] Handler |
| `expLayoutsSubtreeQueryHandler` | class | `extension/explayouts/classes/explayoutssubtreequeryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_subtree] Handler |
| `expLayoutsTabsBlockHandler` | class | `extension/explayouts/classes/explayoutstabsblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tabs] Handler |
| `expLayoutsTagsQueryHandler` | class | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_tags] Handler |
| `expLayoutsTextBlockHandler` | class | `extension/explayouts/classes/explayoutstextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_text] Handler |
| `expLayoutsTitleBlockHandler` | class | `extension/explayouts/classes/explayoutstitleblockhandler.php:2` | – | extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_ezcontent_field] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_title] Handler |
| `expLayoutsTplBlockHandler` | class | `extension/explayouts/classes/explayoutstplblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tpl_block] Handler |
| `expLayoutsVideoBlockHandler` | class | `extension/explayouts/classes/explayoutsvideoblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_video] Handler |
| `ExplBlockXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | `extension/explayouts/classes/explblockxhtmlxmloutput.php:10` | Rich-Text-Ausgabe-Handler, der die Markierungssequenz expl: maskiert. | extension/explayouts/settings/ezxml.ini [OutputSettings] HandlerClass |

### extension/explayouts_content_browser_core (1)

[Alle Muster von extension/explayouts_content_browser_core](bereich-extension-explayouts-content-browser-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowserCoreSearchFilter` | class | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercoresearchfilter.php:10` | Der erweiterte Attributfilter der Suche des Content-Browsers (extendedattributefilter.ini [explayouts_content_browser_search]): jedes Wort muss in… | extension/explayouts_content_browser_core/settings/extendedattributefilter.ini [explayouts_content_browser_search] ClassName |

### extension/explayouts_core (1)

[Alle Muster von extension/explayouts_core](bereich-extension-explayouts-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsCoreAuditBranch` | class | `extension/explayouts_core/classes/explayoutscoreauditbranch.php:11` | Der Zweig von explayouts_core in der Audit-Taxonomie, registriert in settings/audit.ini.append.php ([AuditEventSettings] Branches[explayouts_core]). | extension/explayouts_core/settings/audit.ini [AuditEventSettings] Branches[explayouts_core] |

### extension/expqueue (5)

[Alle Muster von extension/expqueue](area-extension-expqueue.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expQueueAuditBranch` | class | `extension/expqueue/classes/expqueueauditbranch.php:15` | Der Audit-Taxonomiezweig von expqueue (registriert in settings/audit.ini.append.php): system.queue., ein Ereignis je Bedieneraktion an der Warteschlange und pe… | extension/expqueue/settings/audit.ini [AuditEventSettings] Branches[expqueue] |
| `expQueueFailHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuefailhandler.php:16` | expqueue.fail: schlägt absichtlich fehl, um Wiederholungen, Backoff, die Dead Letter Queue und die Anpassung des Ratenlimits auszuprobieren. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.fail] Handler |
| `expQueueHandler` | interface | `extension/expqueue/classes/expqueuehandler.php:27` | Ein Job-Handler: der Code, der die Jobs eines Typs ausführt. | extension/expqueue/settings/rad.ini [Registry_expqueue_job_types] Entries[Handler] |
| `expQueueNoopHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuenoophandler.php:12` | expqueue.noop: tut nichts und ist erledigt. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.noop] Handler |
| `expQueueSleepHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuesleephandler.php:16` | expqueue.sleep: wartet in Schritten, meldet den Fortschritt und beachtet zwischen den Schritten einen Abbruch. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.sleep] Handler |

### extension/expservices (69)

[Alle Muster von extension/expservices](bereich-extension-expservices.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAccountServices` | class extends expUsersBase | `extension/expservices/classes/users/expaccountservices.php:13` | expaccount: die Kontoabläufe einer Person: Registrierung und Aktivierung, vergessenes Passwort und Zurücksetzen, Ändern des eigenen Passworts und der E-Mail-Adresse, und die… | extension/expservices/settings/ezjscore.ini [ezjscServer_expaccount] Class |
| `expAttributeServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expattributeservices.php:14` | Dienste für Attribute: ezjscore/call/expattribute::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expattribute] Class |
| `expAuditServices` | class extends expServiceBase | `extension/expservices/classes/misc/expauditservices.php:12` | ezjscore/call/expaudit:: - das Audit-System, nur lesend (Richtlinie audit/read, eingeschränkt auf die Kanäle seiner Channel-Einschränkung): Ereignisse, ein Ereignis, re… | extension/expservices/settings/ezjscore.ini [ezjscServer_expaudit] Class |
| `expBasketServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expbasketservices.php:12` | ezjscore/call/expbasket::: der Warenkorb der aktuellen Sitzung und die Schritte des Bestellvorgangs. | extension/expservices/settings/ezjscore.ini [ezjscServer_expbasket] Class |
| `expBookmarkServices` | class extends expUsersBase | `extension/expservices/classes/users/expbookmarkservices.php:12` | expbookmark: die Lesezeichen des aktuellen Benutzers in seinem Baum virtueller Ordner. | extension/expservices/settings/ezjscore.ini [ezjscServer_expbookmark] Class |
| `expCacheServices` | class extends expServiceBase | `extension/expservices/classes/expcacheservices.php:10` | expcache: die Caches (setup/managecache): was existiert, ihr Zustand und das Leeren. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcache] Class |
| `expClassGroupServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassgroupservices.php:13` | Dienste für Klassengruppen: ezjscore/call/expclassgroup::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclassgroup] Class |
| `expClassServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassservices.php:15` | Dienste für Klassen: ezjscore/call/expclass::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclass] Class |
| `expCollaborationServices` | class extends expUsersBase | `extension/expservices/classes/users/expcollaborationservices.php:12` | expcollaboration: die Kollaborationseinträge des aktuellen Benutzers (Freigaben und andere Handler-Einträge), ihre Nachrichten, Teilnehmer und Gruppen, und die a… | extension/expservices/settings/ezjscore.ini [ezjscServer_expcollaboration] Class |
| `expCommentServices` | class extends expCommunityBase | `extension/expservices/classes/community/expcommentservices.php:12` | ezjscore/call/expcomment::: Kommentare zu beliebigen Inhalten (Inhaltsobjekte der Kommentarklasse unterhalb des kommentierten Knotens) und die Bewertungen von Produkten (die… | extension/expservices/settings/ezjscore.ini [ezjscServer_expcomment] Class |
| `expContentJobServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expcontentjobservices.php:15` | Dienste für Inhaltsjobs: ezjscore/call/expcontentjob::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expcontentjob] Class |
| `expCronjobServices` | class extends expServiceBase | `extension/expservices/classes/expcronjobservices.php:10` | expcronjob: die Cronjob-Teile und -Skripte (setup/managecronjobs), nur lesend. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcronjob] Class |
| `expCurrencyServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expcurrencyservices.php:11` | ezjscore/call/expcurrency::: die Währungen des Shops mit ihren Kursen, die Umrechnung, die bevorzugte Währung des Benutzers. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcurrency] Class |
| `expDebugServices` | class extends expServiceBase | `extension/expservices/classes/expdebugservices.php:10` | expdebug: die Zusammenfassung der Debug-Ausgabe der Anfrage (wie die Exp-Debug-Leiste), Einstellungen nur lesend. | extension/expservices/settings/ezjscore.ini [ezjscServer_expdebug] Class |
| `expDesignServices` | class extends expServiceBase | `extension/expservices/classes/misc/expdesignservices.php:12` | ezjscore/call/expdesign:: - Designs, Templates und Template-Überschreibungen (nur lesend, Richtlinie setup/administrate): welche Designs existieren und verwendet werden, die… | extension/expservices/settings/ezjscore.ini [ezjscServer_expdesign] Class |
| `expDiscountServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expdiscountservices.php:12` | ezjscore/call/expdiscount::: Rabattgruppen (Regeln), ihre Regeln (Prozentsatz mit Klassen-, Sektions- oder Produkteinschränkungen) und die Benutzer und Benutzergruppen, die… | extension/expservices/settings/ezjscore.ini [ezjscServer_expdiscount] Class |
| `expEditorServices` | class extends expServiceBase | `extension/expservices/classes/expeditorservices.php:12` | expeditor: der Online-Editor (ezoe) für Remote-Clients: die registrierten Editor-Engines, die Engine des aktuellen Benutzers, die Dateitypen, die der Upload des Editors… | extension/expservices/settings/ezjscore.ini [ezjscServer_expeditor] Class |
| `expExtensionServices` | class extends expServiceBase | `extension/expservices/classes/expextensionservices.php:10` | expextension: die Extensions der Installation, nur lesend. | extension/expservices/settings/ezjscore.ini [ezjscServer_expextension] Class |
| `expFeedServices` | class extends expServiceBase | `extension/expservices/classes/community/expfeedservices.php:13` | ezjscore/call/expfeed::: Feeds. | extension/expservices/settings/ezjscore.ini [ezjscServer_expfeed] Class |
| `expFileServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/expfileservices.php:11` | ezjscore/call/expfile:: - Binärdateien: Informationen, Download-URLs (content/download, das content/read selbst prüft), signierte zeitlich begrenzte Links, l… | extension/expservices/settings/ezjscore.ini [ezjscServer_expfile] Class |
| `expForumServices` | class extends expCommunityBase | `extension/expservices/classes/community/expforumservices.php:12` | ezjscore/call/expforum::: Foren (Inhaltsobjekte der Forumklasse, abgelegt in Forencontainern). | extension/expservices/settings/ezjscore.ini [ezjscServer_expforum] Class |
| `expImageServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/expimageservices.php:11` | ezjscore/call/expimage:: - Bilder und ihre Aliase (image.ini), pro Knoten und Attribut. | extension/expservices/settings/ezjscore.ini [ezjscServer_expimage] Class |
| `expInfoCollectionServices` | class extends expServiceBase | `extension/expservices/classes/community/expinfocollectionservices.php:13` | ezjscore/call/expinfocollection::: gesammelte Formulare (Informationssammlung): die Formulare mit Sammlungen, die Sammlungen und ihre Werte, Expor… | extension/expservices/settings/ezjscore.ini [ezjscServer_expinfocollection] Class |
| `expIniServices` | class extends expServiceBase | `extension/expservices/classes/expiniservices.php:11` | expini: liest die Einstellungen (INI-Dateien), nur lesend; Geheimnisse werden maskiert, so wie exp:ini sie maskiert. | extension/expservices/settings/ezjscore.ini [ezjscServer_expini] Class |
| `expLanguageServices` | class extends expServiceBase | `extension/expservices/classes/misc/explanguageservices.php:12` | ezjscore/call/explanguage:: - Inhaltssprachen, Locales und Übersetzungsdateien: die Sprachen der Installation, die Prioritätenliste dieses Siteacce… | extension/expservices/settings/ezjscore.ini [ezjscServer_explanguage] Class |
| `expLayoutServices` | class extends expServiceBase | `extension/expservices/classes/misc/explayoutservices.php:12` | ezjscore/call/explayout:: - die Layouts der Extension explayouts: Layouts, Zonen, Blöcke, Regeln, das für einen Knoten aufgelöste Layout und Entwürfe (anle… | extension/expservices/settings/ezjscore.ini [ezjscServer_explayout] Class |
| `expLocationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/explocationservices.php:13` | Dienste für Orte: ezjscore/call/explocation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_explocation] Class |
| `expMediaServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/expmediaservices.php:11` | ezjscore/call/expmedia:: - Audio- und Videomediendateien (der Datentyp ezmedia): Player-Einstellungen, Dateiinformationen, URLs, Auflistungen und Statistiken. | extension/expservices/settings/ezjscore.ini [ezjscServer_expmedia] Class |
| `expNewsletterServices` | class extends expServiceBase | `extension/expservices/classes/misc/expnewsletterservices.php:13` | ezjscore/call/expnewsletter:: - die Extension cjw_newsletter: Listen, Ausgaben, Abonnentenzahlen und die eigenen Abonnements eines Benutzers (an- und abme… | extension/expservices/settings/ezjscore.ini [ezjscServer_expnewsletter] Class |
| `expNodeServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expnodeservices.php:15` | Dienste für Knoten: ezjscore/call/expnode::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expnode] Class |
| `expNotificationServices` | class extends expUsersBase | `extension/expservices/classes/users/expnotificationservices.php:12` | expnotification: die Benachrichtigungsabonnements des aktuellen Benutzers (Teilbaumregeln, die Kollaborationstypen, die Digest-Einstellungen) und die Benachrichtigu… | extension/expservices/settings/ezjscore.ini [ezjscServer_expnotification] Class |
| `expObjectServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expobjectservices.php:14` | Dienste für Objekte: ezjscore/call/expobject::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expobject] Class |
| `expOrderServices` | class extends expServiceBase | `extension/expservices/classes/commerce/exporderservices.php:12` | ezjscore/call/exporder::: die Bestellungen. | extension/expservices/settings/ezjscore.ini [ezjscServer_exporder] Class |
| `expPackageServices` | class extends expServiceBase | `extension/expservices/classes/exppackageservices.php:10` | exppackage: die Pakete (package/list, package/read), nur lesend. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppackage] Class |
| `expPaymentServices` | class extends expServiceBase | `extension/expservices/classes/commerce/exppaymentservices.php:13` | ezjscore/call/exppayment::: die Zahlungen des Shops: die Zahlungsobjekte, die die Gateways anlegen, ihr Status nach Bestellung, die Gateways und Zahlungs-Workfl… | extension/expservices/settings/ezjscore.ini [ezjscServer_exppayment] Class |
| `expPdfServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/exppdfservices.php:12` | ezjscore/call/exppdf:: - PDF-Ausgabe: ob ein Knoten exportiert werden kann (Richtlinie content/pdf), der Link, der das PDF eines Knotens erzeugt, und der PDF-Expor… | extension/expservices/settings/ezjscore.ini [ezjscServer_exppdf] Class |
| `expPolicyServices` | class extends expUsersBase | `extension/expservices/classes/users/exppolicyservices.php:11` | exppolicy: Richtlinien von Rollen und ihre Einschränkungen sowie die Module und Funktionen, die eine Richtlinie nennen kann. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppolicy] Class |
| `expPollServices` | class extends expCommunityBase | `extension/expservices/classes/community/exppollservices.php:12` | ezjscore/call/exppoll::: Umfragen (Inhaltsobjekte der Umfrageklasse mit einem Optionsattribut, das Informationen sammelt): Liste, Ergebnisse, Abstimmen. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppoll] Class |
| `expPreferencesServices` | class extends expUsersBase | `extension/expservices/classes/users/exppreferencesservices.php:11` | exppreferences: die Präferenzen des aktuellen Benutzers (der Schlüssel-Wert-Speicher, den die Administrationsoberfläche für den Baumzustand, Seitengrößen usw. verwendet). | extension/expservices/settings/ezjscore.ini [ezjscServer_exppreferences] Class |
| `expProductServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expproductservices.php:11` | ezjscore/call/expproduct::: die Produkte des Shops (Inhaltsobjekte, deren Klasse ein Preisattribut hat): Listen, Suche, ein Produkt mit seinen Preisen… | extension/expservices/settings/ezjscore.ini [ezjscServer_expproduct] Class |
| `expRadServices` | class extends expServiceBase | `extension/expservices/classes/expradservices.php:10` | exprad: die Erhebung der Erweiterungspunkte (setup/rad), auf Zusammenfassungsebene, nur lesend. | extension/expservices/settings/ezjscore.ini [ezjscServer_exprad] Class |
| `expRelationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exprelationservices.php:13` | Dienste für Relationen: ezjscore/call/exprelation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exprelation] Class |
| `expReplyServices` | class extends expCommunityBase | `extension/expservices/classes/community/expreplyservices.php:11` | ezjscore/call/expreply::: die Antworten eines Themas (Inhaltsobjekte der Antwortklasse unterhalb eines Themas). | extension/expservices/settings/ezjscore.ini [ezjscServer_expreply] Class |
| `expRoleServices` | class extends expUsersBase | `extension/expservices/classes/users/exproleservices.php:13` | exprole: Rollen, ihre Bearbeitungsentwürfe und ihre Zuweisungen an Benutzer und Gruppen. | extension/expservices/settings/ezjscore.ini [ezjscServer_exprole] Class |
| `expSearchServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expsearchservices.php:14` | Dienste für die Suche: ezjscore/call/expsearch::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expsearch] Class |
| `expSectionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expsectionservices.php:13` | Dienste für Sektionen: ezjscore/call/expsection::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expsection] Class |
| `expServicesAuditBranch` | class | `extension/expservices/classes/expservicesauditbranch.php:10` | Der Audit-Taxonomiezweig von expservices: persönliche API-Tokens. | extension/expservices/settings/audit.ini [AuditEventSettings] Branches[expservices] |
| `expServicesCatalog` | class extends expServiceBase | `extension/expservices/classes/expservicescatalog.php:12` | ezjscore/call/expservices::catalog \ | schema:: \ | version \ | domains \ | service:::: Die Erkennung der API: aufgebaut aus den $services jeder reg… | extension/expservices/settings/ezjscore.ini [ezjscServer_expservices] Class |
| `expSessionAdminServices` | class extends expUsersBase | `extension/expservices/classes/users/expsessionadminservices.php:12` | expsessionadmin: die Sitzungen der Installation. | extension/expservices/settings/ezjscore.ini [ezjscServer_expsessionadmin] Class |
| `expSessionServices` | class extends expServiceBase | `extension/expservices/classes/expsessionservices.php:11` | expsession: das Sitzungs-Login von Remote-Apps. | extension/expservices/settings/ezjscore.ini [ezjscServer_expsession] Class |
| `expShippingServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expshippingservices.php:11` | ezjscore/call/expshipping::: was der Shop über den Versand weiß: der Handler, die Kosten des einfachen Versand-Workflows, die Versandinformationen eines Warenkorbs oder einer… | extension/expservices/settings/ezjscore.ini [ezjscServer_expshipping] Class |
| `expSitemapServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/expsitemapservices.php:12` | ezjscore/call/expsitemap:: - Sitemap-Daten: die Einträge eines Teilbaums, wie eine Sitemap sie auflistet (Ort, letzte Änderung, Änderungshäufigkeit, Priorität)… | extension/expservices/settings/ezjscore.ini [ezjscServer_expsitemap] Class |
| `expStateServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expstateservices.php:14` | Dienste für Objektzustände: ezjscore/call/expstate::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expstate] Class |
| `expStatsServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/expstatsservices.php:11` | ezjscore/call/expstats:: - Statistiken: Suchbegriffe, Inhaltszahlen, Wachstum, Sprachen, Benutzer und Sitzungen. | extension/expservices/settings/ezjscore.ini [ezjscServer_expstats] Class |
| `expSubitemsServices` | class extends expServiceBase | `extension/expservices/classes/misc/expsubitemsservices.php:13` | ezjscore/call/expsubitems:: - der Spaltenkatalog und die Zeilen der Unterelemente (die „Tabellenoptionen“ der Administrationsoberfläche), unter Wiederverwendung von expSubitemsColumnRegistry, expSubitemsSer… | extension/expservices/settings/ezjscore.ini [ezjscServer_expsubitems_svc] Class |
| `expSystemServices` | class extends expServiceBase | `extension/expservices/classes/expsystemservices.php:10` | expsystem: was die Installation ist und worauf sie läuft. | extension/expservices/settings/ezjscore.ini [ezjscServer_expsystem] Class |
| `expTagsServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/exptagsservices.php:13` | ezjscore/call/exptags:: - der Tag-Baum der Extension eztags: Baum, Suche, Kinder, Synonyme, Übersetzungen, verwandte Inhalte und Schreibvorgänge (hinzufügen, umbe… | extension/expservices/settings/ezjscore.ini [ezjscServer_exptags] Class |
| `expTopicServices` | class extends expCommunityBase | `extension/expservices/classes/community/exptopicservices.php:12` | ezjscore/call/exptopic::: die Themen eines Forums (Inhaltsobjekte der Themenklasse). | extension/expservices/settings/ezjscore.ini [ezjscServer_exptopic] Class |
| `expTranslationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exptranslationservices.php:14` | Dienste für Übersetzungen: ezjscore/call/exptranslation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exptranslation] Class |
| `expTrashServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exptrashservices.php:13` | Dienste für den Papierkorb: ezjscore/call/exptrash::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exptrash] Class |
| `expUrlAliasServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expurlaliasservices.php:14` | Dienste für URL-Aliase und Platzhalter: ezjscore/call/expurlalias::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expurlalias] Class |
| `expUrlServices` | class extends expServiceBase | `extension/expservices/classes/misc/expurlservices.php:12` | ezjscore/call/expurl:: - die externen Links, die von URL-Attributen (ezurl) gespeichert werden: Listen, Suche, Gültigkeit, die Objekte, die einen Link verwenden, und eine Prüfung eines… | extension/expservices/settings/ezjscore.ini [ezjscServer_expurl] Class |
| `expUserGroupServices` | class extends expUsersBase | `extension/expservices/classes/users/expusergroupservices.php:10` | expusergroup: der Benutzergruppenbaum und seine Mitglieder. | extension/expservices/settings/ezjscore.ini [ezjscServer_expusergroup] Class |
| `expUserServices` | class extends expUsersBase | `extension/expservices/classes/users/expuserservices.php:13` | expuser: Benutzer nachschlagen, auflisten, suchen und verwalten. | extension/expservices/settings/ezjscore.ini [ezjscServer_expuser] Class |
| `expVatServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expvatservices.php:13` | ezjscore/call/expvat::: MwSt.-Typen, MwSt.-Regeln nach Land und Produktkategorie, die Produktkategorien, die die Regeln verwenden, und die MwSt. eines Produkts… | extension/expservices/settings/ezjscore.ini [ezjscServer_expvat] Class |
| `expVelocityServices` | class extends expServiceBase | `extension/expservices/classes/expvelocityservices.php:10` | expvelocity: der Zustand des Velocity-Servers, nur lesend (setup/system_info). | extension/expservices/settings/ezjscore.ini [ezjscServer_expvelocity] Class |
| `expVersionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expversionservices.php:13` | Dienste für Versionen: ezjscore/call/expversion::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expversion] Class |
| `expWishlistServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expwishlistservices.php:11` | ezjscore/call/expwishlist::: die Wunschliste des angemeldeten Benutzers und für Shop-Administratoren die Wunschlisten aller Benutzer. | extension/expservices/settings/ezjscore.ini [ezjscServer_expwishlist] Class |
| `expWorkflowServices` | class extends expServiceBase | `extension/expservices/classes/expworkflowservices.php:10` | expworkflow: Workflows, ihre Ereignisse, Auslöser und Prozesse, nur lesend (setup/administrate). | extension/expservices/settings/ezjscore.ini [ezjscServer_expworkflow] Class |

### extension/expui (1)

[Alle Muster von extension/expui](bereich-extension-expui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUIServerFunctions` | class | `extension/expui/classes/expuiserverfunctions.php:30` | – | extension/expui/settings/ezjscore.ini [ezjscServer_exp] Class |

### extension/exp_oe_tiptap (3)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOETiptapEngine` | class extends expOEEditorEngineBase | `extension/exp_oe_tiptap/classes/expoetiptapengine.php:16` | Tiptap als Engine der Editor-Registry von ezoe: registriert durch settings/ezoe.ini.append.php (ezoe.ini [EditorSettings] Engines[tiptap]=expOETiptapEngin… | extension/exp_oe_tiptap/settings/ezoe.ini [EditorSettings] Engines[tiptap] |
| `expOETiptapServerFunctions` | class extends ezjscServerFunctions | `extension/exp_oe_tiptap/classes/expoetiptapserverfunctions.php:29` | ezjscore-Serverfunktionen des Tiptap-Editors (ezjscore.ini [ezjscServer_expoetiptap]). | extension/exp_oe_tiptap/settings/ezjscore.ini [ezjscServer_expoetiptap] Class |
| `expOETiptapXMLInput` | class extends eZOEXMLInput | `extension/exp_oe_tiptap/classes/expoetiptapxmlinput.php:20` | Der ezxmltext-Eingabe-Handler von exp_oe_tiptap: eZOEXMLInput von ezoe, beibehalten für das, was 0.1.0 eingerichtet hat. | extension/exp_oe_tiptap/settings/ezxml.ini [InputSettings] AliasClasses[eZSimplifiedXMLInput] |

### extension/ezautosave (1)

[Alle Muster von extension/ezautosave](bereich-extension-ezautosave.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscServerFunctionsAutosave` | class extends ezjscServerFunctions | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:16` | Diese Klasse verarbeitet AJAX-Aufrufe für die Funktion zum automatischen Speichern | extension/ezautosave/settings/ezjscore.ini [ezjscServer_ezautosave] Class |

### extension/ezfind (6)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFindAllWordsExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindallwordsextendedattributefilter.php:25` | eZFindAllWordsExtendedAttributeFilter | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[ezfindallwords] |
| `eZFindGeoDistExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindgeodistextendedattributefilter.php:28` | Erweiterter Attributfilter SolR Geodist | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[geodist] |
| `eZFindServerCallFunctions` | class | `extension/ezfind/classes/ezfindservercallfunctions.php:7` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden | extension/ezfind/settings/ezjscore.ini [ezjscServer_ezfind] Class |
| `ezfSolrDocumentFieldDummyExample` | class extends ezfSolrDocumentFieldBase | `extension/ezfind/classes/ezfsolrdocumentfielddummyexample.php:42` | Diese Klasse dient als Beispiel dafür, wie man einen datentypspezifischen Handler für Indizierung und Abfrage schreibt. | extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[dummy_example] |
| `ezfSolrDocumentFieldObjectRelation` | class extends ezfSolrDocumentFieldBase | `extension/ezfind/classes/ezfsolrdocumentfieldobjectrelation.php:14` | Die Klasse ezfSolrDocumentFieldObjectRelation übernimmt Indizierung und Abfrage für ezobjectrelation und ezobjectrelationlist in eZFind. | extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezobjectrelation]; extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezobjectrelationlist] |
| `ezfSolrDocumentFieldXML` | class extends ezfSolrDocumentFieldBase | `extension/ezfind/classes/ezfsolrdocumentfieldxml.php:8` | – | extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezmatrix]; extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezxmltext] |

### extension/ezflow (6)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFlowKeywordsFetch` | class | `extension/ezflow/classes/fetches/ezflowkeywordsfetch.php:28` | – | extension/ezflow/settings/block.ini [Keyword] FetchClass |
| `eZFlowLatestContent` | class | `extension/ezflow/classes/fetches/ezflowlatestcontent.php:28` | – | extension/ezflow/settings/block.ini [LatestContent] FetchClass |
| `eZFlowLatestObjects` | class | `extension/ezflow/classes/fetches/ezflowlatestobjects.php:28` | – | extension/ezflow/settings/block.ini [Dynamic3Items] FetchClass |
| `eZFlowMCFetch` | class | `extension/ezflow/classes/fetches/ezflowmcfetch.php:3` | – | extension/ezflow/settings/block.ini [MultimediaCarousel] FetchClass |
| `eZFlowServerCallFunctions` | class | `extension/ezflow/classes/ezflowservercallfunctions.php:7` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden | extension/ezflow/settings/ezjscore.ini [ezjscServer_ezflow] Class |
| `eZSquidCacheManager` | class | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – | extension/ezflow/settings/http.ini [HTTPCacheHandlers] Handlers[] |

### extension/ezgmaplocation (1)

[Alle Muster von extension/ezgmaplocation](bereich-extension-ezgmaplocation.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezgmlLocationFilter` | class | `extension/ezgmaplocation/classes/ezgmllocationfilter.php:32` | – | extension/ezgmaplocation/settings/extendedattributefilter.ini [ezgmlLocationFilter] ClassName |

### extension/ezjscore (9)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscCacheManager` | class | `extension/ezjscore/classes/ezjsccachemanager.php:11` | – | extension/ezjscore/settings/site.ini [Cache_ezjscore] class |
| `ezjscCssOptimizer` | class | `extension/ezjscore/classes/ezjsccssoptimizer.php:32` | – | extension/ezjscore/settings/ezjscore.ini [eZJSCore] CssOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [eZJSCore] CssOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [eZJSCore] CssOptimizer[] |
| `ezjscJavascriptOptimizer` | class | `extension/ezjscore/classes/ezjscjavascriptoptimizer.php:32` | – | extension/ezjscore/settings/ezjscore.ini [eZJSCore] JavaScriptOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [eZJSCore] JavaScriptOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [eZJSCore] JavaScriptOptimizer[] |
| `ezjscServerFunctionsAjaxUploader` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php:20` | Diese Klasse verarbeitet AJAX-Aufrufe für die Ajax-Uploader-Komponente | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezajaxuploader] Class |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjsc] Class |
| `ezjscServerFunctionsNode` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsnode.php:36` | Die Klasse ezjscServerFunctionsNode definiert Fetch-Funktionen für Knoten | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjscnode] Class |
| `ezjscServerFunctionsPublishingQueue` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionspublishingqueue.php:18` | Diese Klasse verarbeitet AJAX-Aufrufe für das System der Veröffentlichungswarteschlange | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezpublishingqueue] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezpublishingqueue] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezpublishingqueue] Class |
| `ezpRelationAjaxUploader` | class extends ezpRelationListAjaxUploader | `extension/ezjscore/classes/ajaxuploader/ezprelationajaxuploader.php:19` | Diese Klasse verarbeitet AJAX-Uploads für eZObjectRelation-Attribute | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation] |
| `ezpRelationListAjaxUploader` | class | `extension/ezjscore/classes/ajaxuploader/ezprelationlistajaxuploader.php:19` | Diese Klasse verarbeitet AJAX-Uploads für eZObjectRelationList-Attribute | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist] |

### extension/ezodf (9)

[Alle Muster von extension/ezodf](bereich-extension-ezodf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOdfImportDocumentFeedSource` | class extends expOdfImportDocumentUrlsSource | `extension/ezodf/classes/sqliimport/expodfimportdocumentfeedsource.php:18` | „Dokumente eines Feeds“ von 7x SQLI Import: ein RSS- oder Atom-Feed, dessen Einträge auf Dokumente verweisen (Enclosures oder Links, die auf eine Dokumenterweiterung enden: ein pu… | extension/ezodf/settings/sqliimport.ini [Source_document_feed] Class |
| `expOdfImportDocumentSource` | class extends expImportJsonSource | `extension/ezodf/classes/sqliimport/expodfimportdocumentsource.php:30` | Die Dokumentformate von 7x SQLI Import (sqliimport 2.x), von ezodf in seiner kernelservices.ini mit dem Tag sqliimport.source deklariert; nur von s… | extension/ezodf/settings/sqliimport.ini [Source_documents] Class |
| `expOdfImportDocumentUrlsSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportdocumenturlssource.php:16` | „Dokumente von URLs“ von 7x SQLI Import: eine Liste von Adressen von Dokumenten (eine pro Zeile). | extension/ezodf/settings/sqliimport.ini [Source_document_urls] Class |
| `expOdfImportGoogleDriveSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportgoogledrivesource.php:33` | „Dokumente in einem Google-Drive-Ordner“ von 7x SQLI Import: die Dokumente eines Drive-Ordners (in Meine Ablage oder einem geteilten Laufwerk) und seiner Unterordner bis zu… | extension/ezodf/settings/sqliimport.ini [Source_google_drive] Class |
| `expOdfImportMailboxDocumentsSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportmailboxdocumentssource.php:31` | „Dokumente als E-Mail-Anhänge (IMAP)“ von 7x SQLI Import: die Dokumentanhänge der Nachrichten eines IMAP-Postfachs (eine Einreichungsadresse, eine Pres… | extension/ezodf/settings/sqliimport.ini [Source_mailbox_documents] Class |
| `expOdfImportOneDriveSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportonedrivesource.php:36` | „Dokumente in einem OneDrive- oder SharePoint-Ordner“ von 7x SQLI Import: die Dokumente eines Ordners eines OneDrive-for-Business-Laufwerks oder einer SharePoint-Dokument… | extension/ezodf/settings/sqliimport.ini [Source_onedrive] Class |
| `expOdfImportWebdavSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportwebdavsource.php:33` | „Dokumente in einem WebDAV- oder Nextcloud-Ordner“ von 7x SQLI Import: die Dokumente eines Ordners auf einem WebDAV-Server (Nextcloud, ownCloud, ein SharePoint-WebDAV… | extension/ezodf/settings/sqliimport.ini [Source_webdav] Class |
| `expOdfQueueHandler` | class extends expQueueHandlerBase | `extension/ezodf/classes/converter/expodfqueuehandler.php:14` | Der expqueue-Handler der Jobs von ezodf (expqueue.ini [JobType_ezodf.*]); die eigentliche Arbeit steht in expOdfJobs::run(). | extension/ezodf/settings/expqueue.ini [JobType_ezodf.export] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.import] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.install] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.server] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.test] Handler |
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | `extension/ezodf/classes/ezrestodfhandler.php:46` | – | extension/ezodf/settings/ezrest.ini [RESTSettings] HandlerList[] |

### extension/ezoe (7)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOEAuditBranch` | class | `extension/ezoe/classes/expoeauditbranch.php:11` | Der Audit-Taxonomiezweig des Online-Editors: die Wahl der Editor-Engine. | extension/ezoe/settings/audit.ini [AuditEventSettings] Branches[ezoe] |
| `expOEEditorEngine` | interface | `extension/ezoe/classes/expoeeditorengine.php:21` | Eine Editor-Engine des Online-Editors: das, was ein ezxmltext-Attribut im Browser zu einem Editor macht. | extension/ezoe/settings/rad.ini [Registry_ezoeengines] Entries[Engines] |
| `expOETinyMCE3Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce3engine.php:10` | Die Standard-Engine: eZ Online Editor auf TinyMCE 3.5, gerendert von ezxmltext_ezoe.tpl selbst. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce3] |
| `expOETinyMCE8Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce8engine.php:10` | Die Opt-in-Engine: eZ Online Editor auf TinyMCE 8 (GPL), gerendert von ezxmltext_ezoe_tinymce8.tpl. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce8] |
| `ezoeServerFunctions` | class extends ezjscServerFunctions | `extension/ezoe/classes/ezoeserverfunctions.php:35` | – | extension/ezoe/settings/ezjscore.ini [ezjscServer_ezoe] Class |
| `eZOEXMLInput` | class extends eZXMLInputHandler | `extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php:40` | – | extension/ezoe/settings/ezxml.ini [InputSettings] AliasClasses[eZSimplifiedXMLInput] |
| `GoogleSpell` | class extends SpellChecker | `extension/ezoe/modules/ezoe/classes/GoogleSpell.php:9` | – | extension/ezoe/settings/ezoe.ini [SpellChecker] config[general.engine] |

### extension/ezoracle (2)

[Alle Muster von extension/ezoracle](area-extension-ezoracle.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZOracleDB` | class extends eZDBInterface | `extension/ezoracle/ezdb/dbms-drivers/ezoracledb.php:50` | \class eZOracleDB ezoracledb.php \ingroup eZDB \brief Stellt Oracle-Datenbankfunktionen für das eZDB-Subsystem bereit | extension/ezoracle/settings/site.ini [DatabaseSettings] ImplementationAlias[ezoracle]; extension/ezoracle/settings/site.ini [DatabaseSettings] ImplementationAlias[oracle] |
| `eZOracleSchema` | class extends eZDBSchemaInterface | `extension/ezoracle/ezdb/dbms-schema/ezoracleschema.php:39` | – | extension/ezoracle/settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[oracle] |

### extension/ezownerchange (1)

[Alle Muster von extension/ezownerchange](bereich-extension-ezownerchange.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOwnerChangeAuditBranch` | class | `extension/ezownerchange/classes/expownerchangeauditbranch.php:12` | Der Zweig von ezownerchange in der Audit-Taxonomie, registriert in settings/audit.ini.append.php ([AuditEventSettings] Branches[ezownerchange]). | extension/ezownerchange/settings/audit.ini [AuditEventSettings] Branches[ezownerchange] |

### extension/ezprestapi (1)

[Alle Muster von extension/ezprestapi](bereich-extension-ezprestapi.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezp7xRestApiProvider` | class | `extension/ezprestapi/classes/rest_provider.php:25` | Die Routen des ezp-Anbieters. | extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezpl]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezpl] |

### extension/ezstarrating (2)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsrRatingFilter` | class | `extension/ezstarrating/classes/ezsrratingfilter.php:32` | – | extension/ezstarrating/settings/extendedattributefilter.ini [ezsrRatingFilter] ClassName |
| `ezsrServerFunctions` | class extends ezjscServerFunctions | `extension/ezstarrating/classes/ezsrserverfunctions.php:36` | – | extension/ezstarrating/settings/ezjscore.ini [ezjscServer_ezstarrating] Class |

### extension/eztags (5)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfSolrDocumentFieldeZTags` | class extends ezfSolrDocumentFieldBase | `extension/eztags/classes/ezfsolrdocumentfieldeztags.php:7` | Die Klasse ezfSolrDocumentFieldeZTags implementiert einen eigenen Indexierungs-Handler für eZ Find | extension/eztags/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[eztags] |
| `ezjscTags` | class extends ezjscServerFunctions | `extension/eztags/classes/ezjsctags.php:6` | Die Klasse ezjscTags implementiert eZ-JS-Core-Serverfunktionen für eztags | extension/eztags/settings/ezjscore.ini [ezjscServer_ezjsctags] Class |
| `ezjscTagsChildren` | class extends ezjscServerFunctions | `extension/eztags/classes/ezjsctagschildren.php:6` | Die Klasse ezjscTagsChildren implementiert eZ-JS-Core-Serverfunktionen für die Liste der Kinder von eztags | extension/eztags/settings/ezjscore.ini [ezjscServer_ezjsctagschildren] Class |
| `eZTagsAttributeFilter` | class | `extension/eztags/classes/eztagsattributefilter.php:6` | Die Klasse eZTagsAttributeFilter implementiert das erweiterte Attribut TagsAttributeFilter | extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndMultipleFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeFilter] ClassName |
| `eZTagsTreeAttributeFilter` | class | `extension/eztags/classes/eztagstreeattributefilter.php:6` | Die Klasse eZTagsTreeAttributeFilter implementiert das erweiterte Attribut TagsTreeAttributeFilter | extension/eztags/settings/extendedattributefilter.ini [TagsTreeAttributeFilter] ClassName |

### extension/ezurlmultisite (3)

[Alle Muster von extension/ezurlmultisite](bereich-extension-ezurlmultisite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLMultisiteBinaryFileHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisitebinaryfilehandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezbinaryfile] |
| `eZURLMultisiteObjectRelationHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteobjectrelationhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezobjectrelation] |
| `eZURLMultisiteURLHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteurlhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezstring]; extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezurl] |

### extension/ezwt (1)

[Alle Muster von extension/ezwt](bereich-extension-ezwt.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezwtServerCallFunctions` | class | `extension/ezwt/classes/ezwtservercallfunctions.php:15` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden | extension/ezwt/settings/ezjscore.ini [ezjscServer_ezwt] Class |

### extension/git_manager (1)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `gitManagerAuditBranch` | class | `extension/git_manager/classes/gitmanagerauditbranch.php:13` | Der Zweig von git_manager in der Audit-Taxonomie, registriert in settings/audit.ini.append.php ([AuditEventSettings] Branches[git_manager]). | extension/git_manager/settings/audit.ini [AuditEventSettings] Branches[git_manager] |

### extension/sevenx_authentication_2fa (1)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxAuthentication2faSignIn` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fasignin.php:42` | – | extension/sevenx_authentication_2fa/settings/site.ini [SignInSecondFactorSettings] Handlers[] |

### extension/sevenx_themes_media (1)

[Alle Muster von extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxThemesMediaXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | `extension/sevenx_themes_media/classes/sevenxthemesmediaxhtmlxmloutput.php:11` | Erweitert eZXHTMLXMLOutput, um die alten XML-Tag-Namen zu registrieren, die der importierte Inhalt verwendet (title, itemizedlist, listitem, orderedlist, literallayo… | extension/sevenx_themes_media/settings/ezxml.ini [OutputSettings] HandlerClass |

### extension/sqliimport (66)

[Alle Muster von extension/sqliimport](area-extension-sqliimport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expImportAirtableSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportairtablesource.php:30` | Die Datensätze einer Airtable-Tabelle über die Airtable Web API (v0): Ein Datensatz ist eine Zeile der Tabelle (einer Ansicht, des Filters einer Formel), seine Felder… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.airtable] Arguments[] |
| `expImportArangoDbSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportarangodbsource.php:23` | Die Ergebnisse einer AQL-Abfrage an ArangoDB, gelesen über deren HTTP-Cursor-API. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.arangodb] Arguments[] |
| `expImportArrowSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportarrowsource.php:19` | Apache-Arrow-IPC-Dateien (.arrow), Feather-Version-2-Dateien (.feather, dasselbe Format) und Arrow-IPC-Streams (.arrows), wie pandas, Polars, R und DuckDB… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.arrow] Arguments[] |
| `expImportAuditBranch` | class | `extension/sqliimport/classes/exp/expimportauditbranch.php:15` | Der Audit-Taxonomiezweig von 7x SQLI Import (registriert in settings/audit.ini.append.php): system.import., ein Ereignis je Lauf, der startet, endet, fehlschlägt… | extension/sqliimport/settings/audit.ini [AuditEventSettings] Branches[sqliimport] |
| `expImportAvroSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportavrosource.php:18` | Apache-Avro-Object-Container-Dateien (.avro), wie Kafka-Connectoren, Hadoop und Datenpipelines sie schreiben: Ein Datensatz ist ein Avro-Record, verschachtelte Records… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.avro] Arguments[] |
| `expImportBaserowSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportbaserowsource.php:29` | Die Zeilen einer Baserow-Tabelle über deren REST-API (baserow.io oder ein selbst gehostetes Baserow): Ein Datensatz ist eine Zeile, seine Felder die Felder der Tabelle nach Name… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.baserow] Arguments[] |
| `expImportBigQuerySource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportbigquerysource.php:33` | Die Zeilen einer Google-BigQuery-Abfrage (GoogleSQL), gelesen über die BigQuery REST API v2 mit einem Dienstkonto (expImportGoogleAuth). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.bigquery] Arguments[] |
| `expImportCassandraSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportcassandrasource.php:30` | Die Zeilen eines CQL-SELECT an Apache Cassandra oder ScyllaDB. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.cassandra] Arguments[] |
| `expImportCieSource` | class extends expImportCsvSource | `extension/sqliimport/classes/exp/sources/expimportciesource.php:17` | Der CSV-Export von CIE (bccie): die Formulareinsendungen eines Information Collectors, je eine Zeile. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.cie] Arguments[] |
| `expImportClickHouseSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportclickhousesource.php:27` | Die Zeilen eines SELECT an ClickHouse, gelesen über dessen HTTP-Schnittstelle (Port 8123, oder 8443 mit TLS) im Format JSONEachRow. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.clickhouse] Arguments[] |
| `expImportCosmosDbSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportcosmosdbsource.php:31` | Die Elemente eines Azure-Cosmos-DB-Containers (die API für NoSQL, früher SQL API), die eine Abfrage auswählt, gelesen über die REST-API. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.cosmosdb] Arguments[] |
| `expImportCouchbaseSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportcouchbasesource.php:31` | Die Ergebnisse einer SQL++-Abfrage (N1QL) von Couchbase Server oder Capella, gelesen über die REST-API des Query-Dienstes (/query/service, Port 8093, mit T… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.couchbase] Arguments[] |
| `expImportCouchDbSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportcouchdbsource.php:32` | Die Dokumente einer CouchDB-Datenbank (auch PouchDB-Server und IBM Cloudant, die dieselbe API sprechen), über HTTP gelesen: alle Dokumente (_all_docs), die… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.couchdb] Arguments[] |
| `expImportCsvSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportcsvsource.php:16` | CSV- und TSV-Dateien, als Stream gelesen: ein Datensatz pro Zeile (ein Feld in Anführungszeichen darf sich über mehrere Zeilen erstrecken), geschlüsselt nach der Kopfzeile. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.csv] Arguments[]; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.tsv] Arguments[] |
| `expImportDataTableFieldSource` | class extends expImportDataTableFieldSourceBase | `extension/sqliimport/classes/exp/expimportdatatablefieldsource.php:18` | – | extension/sqliimport/settings/kernelservices.ini [Service_datatable.field_source.sqliimport] Class |
| `expImportDataTableHandler` | class | `extension/sqliimport/classes/exp/handlers/expimportdatatablehandler.php:23` | Das Ziel „Eine Datentabelle“: Jeder Datensatz wird zu einer Zeile einer persistenten Klasse (die eigene Tabelle eines Moduls, Exponential 6.0.15, Setup › Datentabellen), geschrieben… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.handler.datatable] Class |
| `expImportDrupalSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportdrupalsource.php:23` | Inhalt einer Drupal-Website über deren JSON:API (Core-Modul jsonapi, Drupal 8.7 und neuer). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.drupal] Arguments[] |
| `expImportDuckDbSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportduckdbsource.php:33` | Die Zeilen einer DuckDB-Abfrage über Dateien auf dem Server: Parquet-, CSV- oder JSON-Dateien (eine oder viele, auch ein Muster wie data/*.parquet) oder eine DuckDB-Dat… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.duckdb] Arguments[] |
| `expImportDynamoDbSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportdynamodbsource.php:34` | Die Elemente einer Amazon-DynamoDB-Tabelle oder eines Index, gelesen über die DynamoDB-HTTPS-JSON-API (Protokollversion 20120810) mit AWS Signature Version 4 (ex… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.dynamodb] Arguments[] |
| `expImportExponentialSiteSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportexponentialsitesource.php:26` | Inhalt einer anderen Exponential-Website über deren REST-API (v1, die API der mobilen Anwendungen, bereitgestellt von der Extension ezprestapi): die Quelle liest… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.exponential_site] Arguments[] |
| `expImportEzpkgSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportezpkgsource.php:26` | Ein Paket des Paketsystems von Exponential (.ezpkg: ein tar.gz mit package.xml, ezcontentobject/contentobjects.xml und je einer XML-Datei pro Inhaltsobjekt,… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ezpkg] Arguments[] |
| `expImportFirebaseSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportfirebasesource.php:29` | Die Kinder eines Pfads einer Firebase Realtime Database, gelesen über ihre REST-API (GET .json). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.firebase_rtdb] Arguments[] |
| `expImportFirestoreSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportfirestoresource.php:34` | Die Dokumente einer Google-Cloud-Firestore-Sammlung (oder Sammlungsgruppe), gelesen über die Firestore REST API v1 mit einem Dienstkonto (expImportG… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.firestore] Arguments[] |
| `expImportGoogleMerchantSource` | class extends expImportXmlSource | `extension/sqliimport/classes/exp/sources/expimportgooglemerchantsource.php:28` | Google-Merchant-Center-Produkt-Feeds: RSS 2.0 (oder Atom) mit dem Namensraum g:, wie Shops ihren Katalog für Google Shopping und viele Preisver… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.google_merchant] Arguments[] |
| `expImportGoogleSheetsSource` | class extends expImportCsvSource | `extension/sqliimport/classes/exp/sources/expimportgooglesheetssource.php:15` | Eine Google-Sheets-Tabelle über ihre Adresse: der Link der Tabelle (freigegeben, sodass jeder mit dem Link sie ansehen kann) oder ihr Link „Im Web veröffentlichen“. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.google_sheets] Arguments[] |
| `expImportHubSpotSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimporthubspotsource.php:34` | Die Objekte eines HubSpot-CRM-Objekttyps über die CRM API v3: Kontakte, Unternehmen, Deals, Tickets, Produkte, Positionen oder ein benutzerdefiniertes Objekt (über seine… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.hubspot] Arguments[] |
| `expImportIcalSource` | class extends expImportContentLineSource | `extension/sqliimport/classes/exp/sources/expimporticalsource.php:29` | iCalendar-Dateien (.ics, RFC 5545), wie Kalender, Buchungssysteme und Veranstaltungsplattformen sie veröffentlichen, aus einer Datei oder von einer Adresse. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ical] Arguments[] |
| `expImportImapSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportimapsource.php:22` | Die Nachrichten eines IMAP-Postfachs (ein Presse-Posteingang, eine Einreichungsadresse): ein Datensatz ist eine Nachricht, mit ihrer Message-ID, Absender, Empfängern, Betreff,… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.imap] Arguments[] |
| `expImportInfluxDbSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportinfluxdbsource.php:29` | Die Zeilen einer Abfrage an InfluxDB: Flux über die API von InfluxDB 2 und Cloud (POST /api/v2/query, die Antwort als annotiertes CSV), oder InfluxQL über… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.influxdb] Arguments[] |
| `expImportJobHandler` | class extends expQueueHandlerBase | `extension/sqliimport/classes/exp/engine/expimportjobhandler.php:19` | Die Jobs eines Laufs in der Job-Warteschlange (expqueue.ini [JobType_sqliimport.*]): sqliimport.run, sqliimport.chunk und sqliimport.finish (siehe expImportRunner… | extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.chunk] Handler; extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.finish] Handler; extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.run] Handler |
| `expImportJoomlaSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportjoomlasource.php:22` | Joomla-Artikel (com_content), als CSV exportiert (die Spalten der Tabelle content: id, title, alias, introtext, fulltext, catid oder category, state, cre… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.joomla] Arguments[] |
| `expImportJsonSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportjsonsource.php:15` | JSON- und NDJSON-Dateien. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.json] Arguments[]; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ndjson] Arguments[] |
| `expImportLdapSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportldapsource.php:33` | Die Einträge eines LDAP-Verzeichnisses (OpenLDAP, Active Directory, 389 Directory Server ...): ein Datensatz ist ein Eintrag einer Suche, seine Felder der DN (dn)… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ldap] Arguments[] |
| `expImportMarkdownSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportmarkdownsource.php:29` | Markdown-Dateien mit Front Matter, eine Datei oder ein ZIP eines Ordners (eine Jekyll- oder Hugo-Website, ein Obsidian-Vault, ein Dokumentationsordner). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.markdown] Arguments[] |
| `expImportMediaSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportmediasource.php:27` | Mediendateien: ein ZIP eines Ordners (Fotos, Dokumente, Audio, Video) oder eine Datei. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.media] Arguments[] |
| `expImportMongoDbSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportmongodbsource.php:28` | Die Dokumente einer MongoDB-Collection: ein Datensatz ist ein Dokument, seine verschachtelten Dokumente zu gepunkteten Feldern (author.name) abgeflacht und seine BSON-Werte… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.mongodb] Arguments[] |
| `expImportNeo4jSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportneo4jsource.php:33` | Die Zeilen einer Cypher-Abfrage an Neo4j oder Memgraph. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.neo4j] Arguments[] |
| `expImportNotionSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportnotionsource.php:32` | Die Seiten einer Notion-Datenbank über die Notion API (Version 2022-06-28): ein Datensatz ist eine Seite (eine Zeile der Datenbank), seine Felder die der Datenbank… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.notion] Arguments[] |
| `expImportOpmlSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportopmlsource.php:25` | OPML-Dateien (Gliederungslisten; meistens die Liste der Feeds, die ein Feed-Reader exportiert). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.opml] Arguments[] |
| `expImportRedisSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportredissource.php:26` | Die Schlüssel einer Redis-Datenbank, die einem Muster entsprechen: ein Datensatz ist ein Schlüssel, mit _key (sein Name), _type und _ttl und seinem Wert je nach Typ: die Felder eines Hashs… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.redis] Arguments[] |
| `expImportRegistry` | class | `extension/sqliimport/classes/exp/engine/expimportregistry.php:23` | Der Katalog einer Site: ihre Quellen, Handler und Transformationen. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.registry] Class |
| `expImportRestApiSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportrestapisource.php:37` | Die Datensätze einer beliebigen JSON-REST-API, seitenweise gelesen (rest_api): Ein Datensatz ist ein Objekt der Liste einer Antwort, an deren Anfang oder an einem Datensatzpfad (da… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.rest_api] Arguments[] |
| `expImportRssSource` | class extends expImportXmlSource | `extension/sqliimport/classes/exp/sources/expimportrsssource.php:18` | RSS-2.0- und Atom-Feeds, aus einer Datei oder einer URL (die Engine lädt eine URL zuerst in den Snapshot des Laufs herunter). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.exponential_rss] Arguments[]; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.podcast] Arguments[]; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.rss] Arguments[]; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.video_feed] Arguments[] |
| `expImportSalesforceSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportsalesforcesource.php:37` | Die Datensätze einer SOQL-Abfrage über die Salesforce-REST-API: Ein Datensatz ist eine Zeile der Antwort, Beziehungsfelder werden zu Punktnamen abgeflacht (Acco… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.salesforce] Arguments[] |
| `expImportSearchSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportsearchsource.php:34` | Die Dokumente eines Elasticsearch- oder OpenSearch-Index (oder Indexmusters), gelesen über die Such-API per HTTP. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.elasticsearch] Arguments[] |
| `expImportServerFunctions` | class extends ezjscServerFunctions | `extension/sqliimport/classes/exp/expimportserverfunctions.php:22` | Die ezjscore-Serverfunktionen des Tabs Imports (ezjscore.ini [ezjscServer_sqliimport]), aufgerufen von javascript/sqliimport_admin.js über Exp.io: | extension/sqliimport/settings/ezjscore.ini [ezjscServer_sqliimport] Class |
| `expImportShopifyCsvSource` | class extends expImportShopCsvSource | `extension/sqliimport/classes/exp/sources/expimportshopifycsvsource.php:25` | Der Produkt-CSV-Export von Shopify (Products, Export). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.shopify] Arguments[] |
| `expImportSitemapSource` | class extends expImportCrawlSource | `extension/sqliimport/classes/exp/sources/expimportsitemapsource.php:17` | Der Sitemap-Crawl: die Seiten, die die Sitemap einer Site auflistet (eine Sitemap, ein Sitemap-Index oder die Sitemap-Zeilen der robots.txt, wenn die Wurzel einer Site angegeben ist), gefil… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.sitemap] Arguments[] |
| `expImportSnowflakeSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportsnowflakesource.php:26` | Die Zeilen einer Abfrage an Snowflake, gelesen über die SQL API v2 (POST /api/v2/statements) mit Schlüsselpaar-Authentifizierung: ein JWT, signiert mit dem Benutzer… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.snowflake] Arguments[] |
| `expImportSourceEntry` | class | `extension/sqliimport/classes/exp/expimportsourceentry.php:37` | Ein Eintrag des Katalogs der Formate: eine Quellklasse oder eine Variante davon (TSV ist die CSV-Quelle mit einem Tabulator, NDJSON die JSON-Quelle mit einem Objekt… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.airtable] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.arangodb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.arrow] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.avro] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.baserow] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.bigquery] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.cassandra] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.cie] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.clickhouse] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.cosmosdb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.couchbase] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.couchdb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.csv] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.drupal] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.duckdb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.dynamodb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.elasticsearch] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.exponential_rss] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.exponential_site] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ezpkg] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.firebase_rtdb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.firestore] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.google_merchant] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.google_sheets] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.hubspot] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ical] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.imap] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.influxdb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.joomla] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.json] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ldap] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.markdown] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.media] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.mongodb] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.ndjson] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.neo4j] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.notion] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.opml] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.podcast] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.redis] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.rest_api] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.rss] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.salesforce] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.shopify] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.sitemap] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.snowflake] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.spreadsheet] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.sql] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.supabase] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.toml] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.tsv] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.vcard] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.video_feed] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.webpage] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.woocommerce] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.wxr] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.xml] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.xrowextract] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.yaml] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.youtube] Class; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.youtube_page] Class |
| `expImportSpreadsheetSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportspreadsheetsource.php:16` | Tabellenkalkulationen: Excel-Dateien (XLSX) und OpenDocument-Dateien (ODS), ohne Bibliothek gelesen: beides sind ZIP-Dateien aus XML, als Streams gelesen (ext-zip und XMLReader). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.spreadsheet] Arguments[] |
| `expImportSqlSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportsqlsource.php:36` | Die Zeilen einer SQL-Abfrage an eine MySQL-, MariaDB-, PostgreSQL-, SQLite-, Oracle- oder SQL-Server-Datenbank oder an die eigene Datenbank dieser Site, wenn die… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.sql] Arguments[] |
| `expImportSupabaseSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportsupabasesource.php:34` | Die Zeilen einer Tabelle oder Ansicht eines Supabase-Projekts über dessen REST-API (PostgREST) oder eines beliebigen anderen PostgREST-Servers: Ein Datensatz ist eine Zeile, seine Fel… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.supabase] Arguments[] |
| `expImportVcardSource` | class extends expImportContentLineSource | `extension/sqliimport/classes/exp/sources/expimportvcardsource.php:28` | vCard-Dateien (.vcf; Versionen 4.0, 3.0 und, soweit es geht, 2.1), wie Adressbücher und Telefone Kontakte exportieren. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.vcard] Arguments[] |
| `expImportWebPageSource` | class extends expImportCrawlSource | `extension/sqliimport/classes/exp/sources/expimportwebpagesource.php:17` | Der Web-Seiten-Scraper: eine Seite oder eine Liste von Adressen, optional ein „item“-Selektor, der sich auf einer Seite wiederholt (ein Produkt einer Liste), so dass eine Seite… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.webpage] Arguments[] |
| `expImportWooCommerceCsvSource` | class extends expImportShopCsvSource | `extension/sqliimport/classes/exp/sources/expimportwoocommercecsvsource.php:30` | Der Produkt-CSV-Export von WooCommerce (Products, Export, mit den englischen Spaltennamen). | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.woocommerce] Arguments[] |
| `expImportWxrSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportwxrsource.php:29` | WordPress-Exportdateien (WXR, Werkzeuge › Export in WordPress): RSS 2.0 mit dem Namensraum wp:. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.wxr] Arguments[] |
| `expImportXmlSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportxmlsource.php:18` | XML-Dateien, mit XMLReader als Stream gelesen: Jedes Element am Datensatzpfad ist ein Datensatz, in ein Array umgewandelt (Attribute als „@name“, Kindelemen… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.xml] Arguments[] |
| `expImportXrowextractSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportxrowextractsource.php:21` | Die Exportdateien von Export (xrowextract): ein Klassenexport als CSV, JSON oder XML, mit oder ohne sein typisiertes Spaltenmanifest. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.xrowextract] Arguments[] |
| `expImportYamlSource` | class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportyamlsource.php:16` | YAML- und TOML-Datendateien: eine Liste von Datensätzen an der Spitze oder an einem Datensatzpfad (products, data.items), wie statische Site-Generatoren, konfigurationsgesteuerte… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.toml] Arguments[]; extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.yaml] Arguments[] |
| `expImportYouTubePageSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportyoutubepagesource.php:27` | Die Videos, die auf einer youtube.com-Seite aufgelistet sind (die /videos-Seite eines Kanals oder eine Playlist-Seite), aus dem ytInitialData der Seite gelesen, ohne API-Schlüssel. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.youtube_page] Arguments[] |
| `expImportYouTubeSource` | class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportyoutubesource.php:33` | Die Videos eines YouTube-Kanals, einer Playlist oder einer Suche über die YouTube Data API v3: Ein Datensatz ist ein Video mit seinen Details (Titel, Beschr… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.youtube] Arguments[] |
| `SQLIBinaryFileDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqlibinaryfilediffhandler.php:17` | Diff-Handler für ezbinaryfile-Attribute. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DiffHandlerMap[ezbinaryfile] |
| `SQLIDefaultDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqlidefaultdiffhandler.php:18` | Standard-Diff-Handler für Inhaltsfelder. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DefaultDiffHandler |
| `SQLIImageDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqliimagediffhandler.php:17` | Diff-Handler für ezimage-Attribute. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DiffHandlerMap[ezimage] |
| `SQLIRSSImportHandler` | class extends SQLIImportAbstractHandler | `extension/sqliimport/classes/sourcehandlers/sqlirssimporthandler.php:12` | – | extension/sqliimport/settings/sqliimport.ini [rssimporthandler-HandlerSettings] ClassName |

### extension/xrowextract (30)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractHasChildrenFilter` | class | `extension/xrowextract/classes/xrowextracthaschildrenfilter.php:11` | Erweiterter Attributfilter „XrowExtractHasChildren“: nur Objekte, deren Knoten mindestens ein Kind hat (sonst Blattinhalte). | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractHasChildren] ClassName |
| `XrowExtractRelationFilter` | class | `extension/xrowextract/classes/xrowextractrelationfilter.php:17` | Erweiterter Attributfilter „XrowExtractRelation“: nur Objekte, die eine Relation zu einem gegebenen Objekt haben (relation_type wird ignoriert — jede Relation zählt, einschließlich o… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractRelation] ClassName |
| `XrowExtractTranslationFilter` | class | `extension/xrowextract/classes/xrowextracttranslationfilter.php:24` | Erweiterter Attributfilter „XrowExtractTranslation“: nur Objekte, die eine Übersetzung in der gegebenen Sprache haben. | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractTranslation] ClassName |
| `XrowExtractUserStatusFilter` | class | `extension/xrowextract/classes/xrowextractuserstatusfilter.php:12` | Erweiterter Attributfilter „XrowExtractUserStatus“: nur Benutzerobjekte, die aktiviert sind, oder nur solche, die deaktiviert sind, über das Standard-`ezuser_setti… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractUserStatus] ClassName |
| `XroweZBinaryfileExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezbinaryfile] HandlerClass |
| `XroweZBooleanHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbooleanhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezboolean] HandlerClass |
| `XroweZDateHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdate] HandlerClass |
| `XroweZDateTimeHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatetimehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdatetime] HandlerClass |
| `XroweZEmailHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezemailhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezemail] HandlerClass |
| `XroweZenhancedobjectrelationHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezenhancedobjectrelation] HandlerClass |
| `XroweZEnumHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenumhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezenum] HandlerClass |
| `XroweZFloatHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezfloathandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezfloat] HandlerClass |
| `XroweZIdentifierHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezidentifierhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezidentifier] HandlerClass |
| `XroweZImageExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezimagehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezimage] HandlerClass |
| `XroweZIntegerHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezintegerhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezinteger] HandlerClass |
| `XroweZKeywordHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezkeywordhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezkeyword] HandlerClass; extension/xrowextract/settings/csv.ini [eztags] HandlerClass |
| `XroweZMatrixExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezmatrixhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezmatrix] HandlerClass |
| `XroweZMediaExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezmediahandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezmedia] HandlerClass |
| `XroweZObjectRelationHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezobjectrelationhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezobjectrelation] HandlerClass |
| `XroweZObjectRelationListHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezobjectrelationlisthandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezobjectrelationlist] HandlerClass |
| `XroweZPriceHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezpricehandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezprice] HandlerClass |
| `XroweZSelectionHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezselectionhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezcountry] HandlerClass; extension/xrowextract/settings/csv.ini [ezselection] HandlerClass |
| `XroweZStringHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezstringhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezenhancedselection] HandlerClass; extension/xrowextract/settings/csv.ini [ezstring] HandlerClass |
| `XroweZTextHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xroweztexthandler.php:3` | – | extension/xrowextract/settings/csv.ini [eztext] HandlerClass |
| `XroweZTimeHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xroweztimehandler.php:2` | – | extension/xrowextract/settings/csv.ini [eztime] HandlerClass |
| `XroweZURLHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezurlhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezurl] HandlerClass |
| `XroweZUserHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezuserhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezuser] HandlerClass |
| `XroweZXMLTextHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezxmltexthandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezxmltext] HandlerClass |
| `XrowhmregexplineHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowhmregexplinehandler.php:3` | – | extension/xrowextract/settings/csv.ini [hmregexpline] HandlerClass |
| `XrowxrowmetadataHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowxrowmetadatahandler.php:7` | xrowmetadata (SEO- und Sitemap-Metadaten): standardmäßig der Titel. | extension/xrowextract/settings/csv.ini [xrowmetadata] HandlerClass |

### extension/xrowmetadata (1)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfSolrDocumentFieldxrowMetadata` | class extends ezfSolrDocumentFieldBase | `extension/xrowmetadata/classes/ezfSolrDocumentFieldxrowMetadata.php:3` | – | extension/xrowmetadata/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[xrowmetadata] |

### kernel/classes (14)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expKernelExtensionOverrideList` | class | `kernel/classes/expkernelextensionoverridelist.php:21` | Der Dienst „extensions.overrides“: was die beiden Extension-Aktivierungsdurchgänge des Web-Kernels (eZExtension::activateExtensions() 'default' und 'acce… | settings/kernelservices.ini [Service_extensions.overrides] Class |
| `eZContentBrowseBookmark` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowsebookmark.php:35` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClass` | class extends eZPersistentObject | `kernel/classes/ezcontentclass.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentclassattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassEditHandler` | class | `kernel/classes/ezcontentclassedithandler.php:15` | Handler für die Bearbeitung von Inhaltsklassen. | settings/site.ini [ContentSettings] ContentClassEditHandler |
| `eZContentLanguage` | class extends eZPersistentObject | `kernel/classes/ezcontentlanguage.php:12` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObject` | class extends eZPersistentObject | `kernel/classes/ezcontentobject.php:17` | Kapselt Daten über Inhaltsobjekte und Methoden, um mit ihnen zu arbeiten | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectattribute.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | `kernel/classes/ezcontentobjecttreenode.php:17` | Kapselt Daten über Inhaltsobjekte im Inhaltsobjektbaum und Methoden, um mit ihnen zu arbeiten | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectVersion` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectversion.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | `kernel/classes/ezinformationcollectionattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZNodeAssignment` | class extends eZPersistentObject | `kernel/classes/eznodeassignment.php:17` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZStaticCache` | class | `kernel/classes/ezstaticcache.php:30` | Die Klasse eZStaticCache verwaltet das statische Cache-System. | settings/site.ini [ContentSettings] StaticCacheHandler |
| `eZWorkflow` | class extends eZPersistentObject | `kernel/classes/ezworkflow.php:18` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |

### kernel/classes/audit (12)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAuditBzip2Format` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditbzip2format.php:11` | Das Archivformat bzip2 (PHP-Erweiterung bz2): .jsonl.bz2. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[bzip2] |
| `expAuditGzipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditgzipformat.php:11` | Das Archivformat gzip (zlib, immer verfügbar): .jsonl.gz. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[gzip] |
| `expAuditMailSink` | class extends expAuditSinkBase | `kernel/classes/audit/sinks/expauditmailsink.php:23` | Die E-Mail-Senke (doc/eng-us/bc/6.0/audit.md, „E-mail“): Ereignisse ab MinSeverity (critical) oder passend zu Events[] werden über eZMail gemailt, eine Mail je… | settings/audit.ini [AuditSinkSettings] SinkClasses[mail] |
| `expAuditMatchRule` | class extends expAuditAlertRuleBase | `kernel/classes/audit/alerts/expauditmatchrule.php:20` | Die Alarmregelklasse "match" (RuleClasses[match]=expAuditMatchRule): löst für jeden Datensatz aus, der zu Event passt (und mit Policies[] nur, wenn die Rolle… | settings/audit.ini [AuditAlertSettings] RuleClasses[match] |
| `expAuditScheduleRule` | class extends expAuditAlertRuleBase | `kernel/classes/audit/alerts/expauditschedulerule.php:15` | Die Alarmregelklasse "schedule" (RuleClasses[schedule]=expAuditScheduleRule): löst für Datensätze aus, die zu Event passen und außerhalb der Geschäftszeiten auftreten ([A… | settings/audit.ini [AuditAlertSettings] RuleClasses[schedule] |
| `expAuditSource` | interface | `kernel/classes/audit/console/expauditsource.php:23` | Eine optionale Quelle der Audit-Konsole: eine Extension, die eigene Ereignisse schreibt (meist in einen eigenen Kanal) und den Ansichten der Konsole mitteilt, wi… | settings/rad.ini [Registry_auditsources] Entries[Sources] |
| `expAuditSyslogSink` | class extends expAuditSinkBase | `kernel/classes/audit/sinks/expauditsyslogsink.php:36` | Die Senke für syslog / journald (doc/eng-us/bc/6.0/audit.md, "Sinks", "syslog / journald"): eine RFC-5424-Nachricht je Datensatz, | settings/audit.ini [AuditSinkSettings] SinkClasses[syslog] |
| `expAuditThresholdRule` | class extends expAuditAlertRuleBase | `kernel/classes/audit/alerts/expauditthresholdrule.php:16` | Die Alarmregelklasse "threshold" (RuleClasses[threshold]=expAuditThresholdRule): löst aus, wenn Threshold zu Event passende Ereignisse innerhalb von Window Sekunde… | settings/audit.ini [AuditAlertSettings] RuleClasses[threshold] |
| `expAuditWebhookSink` | class extends expAuditSinkBase | `kernel/classes/audit/sinks/expauditwebhooksink.php:20` | Die Webhook-Senke (doc/eng-us/bc/6.0/audit.md, "Webhook"): sendet Stapel von Datensätzen per POST als JSON, | settings/audit.ini [AuditSinkSettings] SinkClasses[webhook] |
| `expAuditXzFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditxzformat.php:11` | Das Archivformat xz: .jsonl.xz, über das Programm xz (keine PHP-Erweiterung ist allgemein verfügbar). | settings/audit.ini [AuditArchiveSettings] FormatHandlers[xz] |
| `expAuditZipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzipformat.php:12` | Das Archivformat zip (PHP-Erweiterung zip): .jsonl.zip, ein Archiv pro Live-Datei, das genau diese Datei enthält, sodass eine einzelne Datei wiederhergestellt werden kann. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zip] |
| `expAuditZstdFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzstdformat.php:11` | Das Archivformat zstd: .jsonl.zst, über die PHP-Erweiterung zstd, wenn sie geladen ist, sonst über das Programm zstd. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zstd] |

### kernel/classes/binaryhandlers (1)

[Alle Muster von kernel/classes/binaryhandlers](bereich-kernel-classes-binaryhandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFilePassthroughHandler` | class extends eZBinaryFileHandler | `kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php:18` | – | settings/file.ini [BinaryFileSettings] Handler |

### kernel/classes/clusterfilehandlers (1)

[Alle Muster von kernel/classes/clusterfilehandlers](bereich-kernel-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFSFileHandler` | class | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php:12` | – | settings/file.ini [ClusteringSettings] FileHandler |

### kernel/classes/contentcheck (2)

[Alle Muster von kernel/classes/contentcheck](bereich-kernel-classes-contentcheck.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentCheckFileImage` | class | `kernel/classes/contentcheck/expcontentcheckfileimage.php:13` | Die Dateien eines Bildfeldes (ezimage): das Original und die Aliase, die sein gespeichertes XML nennt (doc/eng-us/features/6.0/content-checks.md, „Missing files“). | settings/contentcheck.ini [MissingFileSettings] Handlers[ezimage] |
| `expContentCheckFileTable` | class | `kernel/classes/contentcheck/expcontentcheckfiletable.php:16` | Die Datei eines Feldes, dessen Datentyp sie in einer Tabelle mit den Spalten von ezbinaryfile hält: contentobject_attribute_id, version, filename, original_file… | settings/contentcheck.ini [MissingFileSettings] Handlers[ezbinaryfile]; settings/contentcheck.ini [MissingFileSettings] Handlers[ezmedia] |

### kernel/classes/contentjob (13)

[Alle Muster von kernel/classes/contentjob](bereich-kernel-classes-contentjob.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentJobAddLocation` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobaddlocation.php:22` | Der Inhaltsjob 'addlocation': gibt den Objekten vieler ausgewählter Knoten einen neuen Ort unter einem Zielknoten, pro Objekt über die kernel-eigene Ope… | settings/content.ini [ContentJobSettings] JobTypes[addlocation] |
| `expContentJobAuditExport` | class | `kernel/classes/contentjob/expcontentjobauditexport.php:28` | Der Inhaltsjob 'auditexport': der Export der Audit-Konsole (audit/export) im Hintergrund, für einen Filter, der für einen Download im Browser zu groß ist… | settings/content.ini [ContentJobSettings] JobTypes[auditexport] |
| `expContentJobCopySubtree` | class | `kernel/classes/contentjob/expcontentjobcopysubtree.php:34` | Der Inhaltsjob 'copy': kopiert einen Teilbaum stapelweise unter ein neues Elternelement, Ebene für Ebene von oben, mit der Semantik von content/copysubtree und bi… | settings/content.ini [ContentJobSettings] JobTypes[copy] |
| `expContentJobHelpManuals` | class | `kernel/classes/contentjob/expcontentjobhelpmanuals.php:27` | Der Content-Job 'helpmanuals': baut die Handbücher der Hilfe im Hintergrund aus der Markdown-Hilfe (doc/eng-us/features/6.0/help-content.md, „Manuals… | settings/content.ini [ContentJobSettings] JobTypes[helpmanuals] |
| `expContentJobHideSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobhidesubtree.php:25` | Der Inhaltsjob 'hide' (und über expContentJobRevealSubtree 'reveal'): verbirgt einen Knoten und seinen Teilbaum oder macht sie sichtbar, mit der Semantik von eZContentOb… | settings/content.ini [ContentJobSettings] JobTypes[hide] |
| `expContentJobMoveSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobmovesubtree.php:24` | Der Inhaltsjob 'move': verschiebt einen Teilbaum unter ein neues Elternelement. | settings/content.ini [ContentJobSettings] JobTypes[move] |
| `expContentJobPackageInstall` | class | `kernel/classes/contentjob/expcontentjobpackageinstall.php:26` | Der Inhaltsjob 'packageinstall': installiert ein Paket aus dem Paket-Repository im Hintergrund, als der Benutzer, der es angefordert hat, mit den getroffenen Entscheidungen… | settings/content.ini [ContentJobSettings] JobTypes[packageinstall] |
| `expContentJobRemoveLocation` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobremovelocation.php:23` | Der Inhaltsjob 'removelocation': entfernt viele ausgewählte Orte (Knoten) und behält ihre Objekte, über die kernel-eigene Operation, wie content/acti… | settings/content.ini [ContentJobSettings] JobTypes[removelocation] |
| `expContentJobRemoveSubtree` | class | `kernel/classes/contentjob/expcontentjobremovesubtree.php:33` | Der Inhaltsjob 'remove': entfernt einen oder mehrere Teilbäume stapelweise, die tiefsten Knoten zuerst (Blätter vor ihren Eltern), jeden Stapel über die kernel-eigene… | settings/content.ini [ContentJobSettings] JobTypes[remove] |
| `expContentJobRevealSubtree` | class extends expContentJobHideSubtree | `kernel/classes/contentjob/expcontentjobrevealsubtree.php:16` | Der Inhaltsjob 'reveal': macht einen verborgenen Knoten und seinen Teilbaum sichtbar, mit der Semantik von eZContentObjectTreeNode::unhideSubTree() (ein Knoten, der selbst verborgen… | settings/content.ini [ContentJobSettings] JobTypes[reveal] |
| `expContentJobSectionSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobsectionsubtree.php:29` | Der Inhaltsjob 'section': weist jedem Objekt eines Teilbaums eine Sektion zu, mit der Semantik von eZContentObjectTreeNode::assignSectionToSubTree() (e… | settings/content.ini [ContentJobSettings] JobTypes[section] |
| `expContentJobStateSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobstatesubtree.php:22` | Der Inhaltsjob 'state': setzt einen Objektzustand (einer Zustandsgruppe) auf jedes Objekt eines Teilbaums, pro Objekt über die kernel-eigene Operation, wie co… | settings/content.ini [ContentJobSettings] JobTypes[state] |
| `expContentJobTransfer` | class | `kernel/classes/contentjob/expcontentjobtransfer.php:25` | Der Content-Job 'transfer': ein Hintergrundlauf eines Import- oder Export-Tools (Export, CIE ...), wenn die Installation keine Job-Warteschlange hat (expKernelTransfe… | settings/content.ini [ContentJobSettings] JobTypes[transfer] |

### kernel/classes/datatypes (6)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZPDFXMLOutput` | class extends eZXHTMLXMLOutput | `kernel/classes/datatypes/ezxmltext/handlers/output/ezpdfxmloutput.php:12` | – | settings/ezxml.ini [OutputSettings] AliasClasses[ezpdf] |
| `eZSimplifiedXMLInput` | class extends eZXMLInputHandler | `kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinput.php:12` | – | settings/ezxml.ini [InputSettings] HandlerClass |
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZXHTMLXMLOutput` | class extends eZXMLOutputHandler | `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php:12` | – | settings/ezxml.ini [OutputSettings] HandlerClass |

### kernel/classes/debugbar (1)

[Alle Muster von kernel/classes/debugbar](bereich-kernel-classes-debugbar.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expDebugBarServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/debugbar/expdebugbarserverfunctions.php:28` | Die ezjscore-Serverfunktionen der Exp-Debug-Leiste, registriert als [ezjscServer_expdebugbar] in extension/ezjscore/settings/ezjscore.ini: | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expdebugbar] Class |

### kernel/classes/help (3)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelpContentProvider` | class | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Als Inhalt gepflegte Hilfe, ausgeliefert in das Hilfesystem (exphelp): die Themen, die der aktuelle Benutzer sehen darf, gerendert mit dem Markdown-Renderer der Hilfe… | settings/exphelp.ini [ContextHelp] Providers[] |
| `expHelpContentRepository` | class | `kernel/classes/help/content/exphelpcontentrepository.php:15` | Liest als Inhalt gepflegte Hilfe aus der Datenbank in einfache Arrays (die Formen, die expHelpContentValidator beschreibt), ohne jeden Berechtigungsfilter:… | settings/site.ini [Cache_exphelpcontent] class; settings/site.ini [Cache_exphelpcontent] purgeClass |
| `expHelpRestProvider` | class | `kernel/classes/help/content/rest/exphelprestprovider.php:21` | Die REST-Routen der Kontexthilfe (doc/eng-us/features/6.0/help-content.md, „Context help by help_id“; Auslieferungsweg 2 des Plans: „internal topics wit… | settings/rest.ini [ApiProvider] ProviderClass[help] |

### kernel/classes/ini (15)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniActionActions` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionactions.php:14` | exp:ini actions: die Aktionen des Befehls, die eingebauten und die, die Extensions in ini.ini [IniCommandSettings] Actions[] registrieren, mit ihren Al… | settings/ini.ini [IniCommandSettings] Actions[actions] |
| `expIniActionAdd` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionadd.php:14` | exp:ini add: hängt einen Wert an ein Array (Variable[]=value) in der Datei eines Scopes an und legt die Datei und den Block an, wenn sie fehlen (außer mit --no-c… | settings/ini.ini [IniCommandSettings] Actions[add] |
| `expIniActionClear` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionclear.php:13` | exp:ini clear: schreibt die Array-Reset-Zeile Variable[] in die Datei eines Scopes, sodass die Werte der zuvor geladenen Dateien verworfen werden und nur, was fo… | settings/ini.ini [IniCommandSettings] Actions[clear] |
| `expIniActionCopy` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactioncopy.php:15` | exp:ini copy: kopiert eine Einstellung aus der Datei eines Scopes in die eines anderen: eine einfache Variable oder ein Hash-Eintrag erhält denselben Wert, ein ganzes Array wird geschrie… | settings/ini.ini [IniCommandSettings] Actions[copy] |
| `expIniActionGet` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionget.php:14` | exp:ini get: der wirksame Wert (alle Dateien in Ladereihenfolge zusammengeführt, für den aktuellen Siteaccess oder den von -s) oder, mit einem Scope, der Wert, den dieser Scope… | settings/ini.ini [IniCommandSettings] Actions[get] |
| `expIniActionList` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionlist.php:14` | exp:ini list: die Blöcke einer INI-Datei oder die Variablen eines Blocks, wirksam (alle Dateien zusammengeführt, für den aktuellen Siteaccess) oder als Datei eines Scopes… | settings/ini.ini [IniCommandSettings] Actions[list] |
| `expIniActionMove` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionmove.php:16` | exp:ini move: verschiebt einen Block (mit seinen Kommentaren und Leerzeilen, in Reihenfolge) oder jeden Block einer Datei von einem Scope in einen anderen, typischerweise von settin… | settings/ini.ini [IniCommandSettings] Actions[move] |
| `expIniActionMoveAll` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionmoveall.php:15` | exp:ini move-all: verschiebt jede INI-Datei eines Scopes (oder die, die --files nennt) Block für Block in einen anderen Scope, eine Transaktion je Datei wie exp:ini mo… | settings/ini.ini [IniCommandSettings] Actions[move-all] |
| `expIniActionRem` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionrem.php:14` | exp:ini rem (Alias remove): entfernt eine Variable aus der Datei eines Scopes oder einen Wert eines Arrays oder einen Eintrag eines Hashs. | settings/ini.ini [IniCommandSettings] Actions[rem] |
| `expIniActionScopes` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionscopes.php:14` | exp:ini scopes: die Scopes, die diese Installation hat (global, default, jeder Siteaccess, jede Extension und die Siteaccess-Verzeichnisse der Extensions, und di… | settings/ini.ini [IniCommandSettings] Actions[scopes] |
| `expIniActionSet` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionset.php:14` | exp:ini set: setzt eine einfache Variable (Variable=value) oder einen Hash-Eintrag (Variable[key]=value) in der Datei eines Scopes und legt die Datei und den Block an, wenn s… | settings/ini.ini [IniCommandSettings] Actions[set] |
| `expIniActionToggle` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactiontoggle.php:14` | exp:ini toggle: schaltet einen Schalter in der Datei eines Scopes so um, wie die Einstellung ihn schreibt: enableddisabled, truefalse, yesno, onoff, 10 (die Groß-/Kleinschreibung bleibt erhalten). | settings/ini.ini [IniCommandSettings] Actions[toggle] |
| `expIniActionWhere` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionwhere.php:14` | exp:ini where: jede Datei, die eine Variable setzt, in der Reihenfolge, in der eZINI sie lädt (bei einem einfachen Wert gewinnt die letzte, Arrays summieren sich, außer eine Datei setzt zurü… | settings/ini.ini [IniCommandSettings] Actions[where] |
| `expIniCoreScopeProvider` | class | `kernel/classes/ini/expinicorescopeprovider.php:16` | global (settings/override), default (settings/, die mitgelieferten Standardwerte, per Richtlinie nicht beschreibbar) und ein Bereich pro Siteaccess: jedes Verzeichnis in settin… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |
| `expIniExtensionScopeProvider` | class | `kernel/classes/ini/expiniextensionscopeprovider.php:14` | extension: für jedes Verzeichnis in extension/ (aktiv oder nicht; die Beschriftung sagt, welches) und extension::siteaccess: für jedes extension//settings/siteacces… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |

### kernel/classes/mailpreferences (1)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailPreferencesService` | class | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | Die Abläufe rund um die Präferenzen: die Double-Opt-in-Mail und ihre Bestätigung, die Abmeldung mit einem Klick, „Link zusenden“ für Personen ohne Login,… | settings/mailpreferences.ini [GateSettings] EssentialSenders[] |

### kernel/classes/notification (1)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expNotificationMailCategoryHandler` | class | `kernel/classes/notification/expnotificationmailcategoryhandler.php:25` | Die Kategorien „content“ und „collaboration“ der E-Mail-Präferenzen lesen die eigenen Daten des Benachrichtigungssystems, sodass nichts migriert werden muss und die… | settings/mailpreferences.ini [Category_collaboration] HandlerClass; settings/mailpreferences.ini [Category_content] HandlerClass |

### kernel/classes/packagecreators (4)

[Alle Muster von kernel/classes/packagecreators](bereich-kernel-classes-packagecreators.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentClassPackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezcontentclass/ezcontentclasspackagecreator.php:19` | – | settings/package.ini [CreationSettings] HandlerAlias[ezcontentclass] |
| `eZContentObjectPackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezcontentobject/ezcontentobjectpackagecreator.php:18` | – | settings/package.ini [CreationSettings] HandlerAlias[ezcontentobject] |
| `eZExtensionPackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php:19` | – | settings/package.ini [CreationSettings] HandlerAlias[ezextension] |
| `eZStylePackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezstyle/ezstylepackagecreator.php:21` | – | settings/package.ini [CreationSettings] HandlerAlias[ezstyle] |

### kernel/classes/packagehandlers (7)

[Alle Muster von kernel/classes/packagehandlers](bereich-kernel-classes-packagehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentClassPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezcontentclass/ezcontentclasspackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezcontentclass] |
| `eZContentObjectPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezcontentobject/ezcontentobjectpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezcontentobject] |
| `eZDBPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezdb/ezdbpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezsql] |
| `eZExtensionPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezextension] |
| `eZFilePackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezdesign]; settings/package.ini [PackageSettings] HandlerAlias[ezfile]; settings/package.ini [PackageSettings] HandlerAlias[ezini]; settings/package.ini [PackageSettings] HandlerAlias[eztemplate]; settings/package.ini [PackageSettings] HandlerAlias[ezthumbnail] |
| `eZINIAddonPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/eziniaddon/eziniaddonpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[eziniaddon] |
| `eZInstallScriptPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezinstallscript/ezinstallscriptpackagehandler.php:19` | – | settings/package.ini [PackageSettings] HandlerAlias[ezinstallscript] |

### kernel/classes/packageinstallers (2)

[Alle Muster von kernel/classes/packageinstallers](bereich-kernel-classes-packageinstallers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentObjectPackageInstaller` | class extends eZPackageInstallationHandler | `kernel/classes/packageinstallers/ezcontentobject/ezcontentobjectpackageinstaller.php:18` | – | settings/package.ini [InstallerSettings] HandlerAlias[ezcontentobject] |
| `eZInstallScriptPackageInstaller` | class extends eZPackageInstallationHandler | `kernel/classes/packageinstallers/ezinstallscript/ezinstallscriptpackageinstaller.php:17` | – | settings/package.ini [InstallerSettings] HandlerAlias[ezinstallscript] |

### kernel/classes/reauth (1)

[Alle Muster von kernel/classes/reauth](area-kernel-classes-reauth.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expReauthTotpCode` | class | `kernel/classes/reauth/expreauthtotpcode.php:19` | „Identität bestätigen“ mit dem Code einer Authenticator-App, eingerichtet in sevenx_authentication_2fa (wenn diese Extension aktiv ist): das TOTP-Geheimnis des Kontos… | settings/site.ini [ReauthSettings] CodeHandlers[] |

### kernel/classes/subitems (12)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsDateColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsdatecolumn.php:16` | Spalten der Unterelementliste dazu, wann etwas geschah, in den Formen, die die eingebauten Spalten Published und Modified nicht zeigen: relativ ("vor 3 Tagen"), in T… | settings/subitemscolumns.ini [Column_dayssincemodified] Class; settings/subitemscolumns.ini [Column_dayssincepublished] Class; settings/subitemscolumns.ini [Column_modifiedage] Class; settings/subitemscolumns.ini [Column_modifiediso] Class; settings/subitemscolumns.ini [Column_publishedage] Class; settings/subitemscolumns.ini [Column_publishediso] Class |
| `expSubitemsMediaColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsmediacolumn.php:20` | Spalten der Unterelementliste zu den Bildern und Dateien des Inhaltsobjekts. | settings/subitemscolumns.ini [Column_filedownloads] Class; settings/subitemscolumns.ini [Column_filemime] Class; settings/subitemscolumns.ini [Column_filename] Class; settings/subitemscolumns.ini [Column_filesize] Class; settings/subitemscolumns.ini [Column_hasimage] Class; settings/subitemscolumns.ini [Column_imagealt] Class; settings/subitemscolumns.ini [Column_imagecount] Class; settings/subitemscolumns.ini [Column_imagedimensions] Class; settings/subitemscolumns.ini [Column_imageheight] Class; settings/subitemscolumns.ini [Column_imagemime] Class; settings/subitemscolumns.ini [Column_imagesize] Class; settings/subitemscolumns.ini [Column_imagewidth] Class |
| `expSubitemsNodeColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsnodecolumn.php:18` | Spalten der Unterelementliste, gelesen aus dem Ort (der Zeile von ezcontentobject_tree) und seinen Kindern. | settings/subitemscolumns.ini [Column_childclasses] Class; settings/subitemscolumns.ini [Column_childrencount] Class; settings/subitemscolumns.ini [Column_childsortfield] Class; settings/subitemscolumns.ini [Column_childsortorder] Class; settings/subitemscolumns.ini [Column_depth] Class; settings/subitemscolumns.ini [Column_hidden] Class; settings/subitemscolumns.ini [Column_invisible] Class; settings/subitemscolumns.ini [Column_iscontainer] Class; settings/subitemscolumns.ini [Column_ismain] Class; settings/subitemscolumns.ini [Column_mainnodeid] Class; settings/subitemscolumns.ini [Column_modifiedsubnode] Class; settings/subitemscolumns.ini [Column_newestchild] Class; settings/subitemscolumns.ini [Column_newestchildname] Class; settings/subitemscolumns.ini [Column_parentname] Class; settings/subitemscolumns.ini [Column_parentnodeid] Class; settings/subitemscolumns.ini [Column_pathidentification] Class; settings/subitemscolumns.ini [Column_pathstring] Class; settings/subitemscolumns.ini [Column_subtreecount] Class; settings/subitemscolumns.ini [Column_viewcount] Class |
| `expSubitemsObjectColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsobjectcolumn.php:18` | Spalten der Unterelementliste, gelesen aus dem Inhaltsobjekt: Klasse, Sektion, Besitzer, Sprachen, Zustände und die Größe seines Haupttextes. | settings/subitemscolumns.ini [Column_alwaysavailable] Class; settings/subitemscolumns.ini [Column_attributecount] Class; settings/subitemscolumns.ini [Column_classgroups] Class; settings/subitemscolumns.ini [Column_classid] Class; settings/subitemscolumns.ini [Column_classidentifier] Class; settings/subitemscolumns.ini [Column_initiallanguage] Class; settings/subitemscolumns.ini [Column_languagecodes] Class; settings/subitemscolumns.ini [Column_languagecount] Class; settings/subitemscolumns.ini [Column_languagemask] Class; settings/subitemscolumns.ini [Column_locked] Class; settings/subitemscolumns.ini [Column_missingtranslations] Class; settings/subitemscolumns.ini [Column_namelength] Class; settings/subitemscolumns.ini [Column_owner] Class; settings/subitemscolumns.ini [Column_ownerid] Class; settings/subitemscolumns.ini [Column_sectionid] Class; settings/subitemscolumns.ini [Column_sectionidentifier] Class; settings/subitemscolumns.ini [Column_textlength] Class; settings/subitemscolumns.ini [Column_wordcount] Class |
| `expSubitemsPermissionColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemspermissioncolumn.php:18` | Spalten der Unterelementliste, die sagen, was der aktuelle Benutzer mit jedem Element tun darf, und technische Fakten dazu. | settings/subitemscolumns.ini [Column_cancreate] Class; settings/subitemscolumns.ini [Column_canedit] Class; settings/subitemscolumns.ini [Column_canhide] Class; settings/subitemscolumns.ini [Column_canmove] Class; settings/subitemscolumns.ini [Column_canremove] Class; settings/subitemscolumns.ini [Column_cantranslate] Class; settings/subitemscolumns.ini [Column_searchwords] Class |
| `expSubitemsReadingTimeColumn` | class extends expSubitemsColumn | `kernel/classes/subitems/columns/expsubitemsreadingtimecolumn.php:14` | Die Spalte "Lesezeit" der Admin-Unterelementliste: die Minuten, die ein Leser für den Haupttext des Elements braucht, bei WordsPerMinute= Wörtern pro Minute (Standard… | settings/subitemscolumns.ini [Column_readingtime] Class |
| `expSubitemsRelationColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsrelationcolumn.php:20` | Spalten der Unterelementliste dazu, womit das Objekt verknüpft ist und was darauf verweist: Objektrelationen, Tags (eztags) und Bewertungen (ezstarrating). | settings/subitemscolumns.ini [Column_rating] Class; settings/subitemscolumns.ini [Column_ratingcount] Class; settings/subitemscolumns.ini [Column_relatedcount] Class; settings/subitemscolumns.ini [Column_relatednames] Class; settings/subitemscolumns.ini [Column_reverserelatedcount] Class; settings/subitemscolumns.ini [Column_reverserelatednames] Class; settings/subitemscolumns.ini [Column_tagcount] Class; settings/subitemscolumns.ini [Column_tags] Class |
| `expSubitemsSEOColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsseocolumn.php:32` | Spalten der Unterelementliste für Suchmaschinen und Besucher: der Seitentitel im Browser, seine Länge sowie Meta-Titel, Beschreibung und Schlüsselwörter, die ein xro… | settings/subitemscolumns.ini [Column_metadescription] Class; settings/subitemscolumns.ini [Column_metakeywords] Class; settings/subitemscolumns.ini [Column_metatitle] Class; settings/subitemscolumns.ini [Column_pagetitle] Class; settings/subitemscolumns.ini [Column_titlelength] Class |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | Die ezjscore-Serverfunktionen der Unterelementliste der Administrationsoberfläche („Tabellenoptionen“), registriert als [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expsubitems] Class |
| `expSubitemsURLColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsurlcolumn.php:20` | Spalten der Unterelementliste zu den Adressen eines Knotens: sein URL-Alias, seine System-URL, die Adresse, die Besucher auf der öffentlichen Site verwenden, eigene und historische… | settings/subitemscolumns.ini [Column_aliascount] Class; settings/subitemscolumns.ini [Column_allaliases] Class; settings/subitemscolumns.ini [Column_customaliascount] Class; settings/subitemscolumns.ini [Column_customaliases] Class; settings/subitemscolumns.ini [Column_historycount] Class; settings/subitemscolumns.ini [Column_locationcount] Class; settings/subitemscolumns.ini [Column_mainlocation] Class; settings/subitemscolumns.ini [Column_otherlocations] Class; settings/subitemscolumns.ini [Column_publicpath] Class; settings/subitemscolumns.ini [Column_publicurl] Class; settings/subitemscolumns.ini [Column_systemurl] Class; settings/subitemscolumns.ini [Column_urlalias] Class; settings/subitemscolumns.ini [Column_urlslug] Class |
| `expSubitemsUserColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsusercolumn.php:19` | Spalten der Unterelementliste für Benutzerkonten: die Zeilen unterhalb einer Benutzergruppe. | settings/subitemscolumns.ini [Column_useremail] Class; settings/subitemscolumns.ini [Column_userenabled] Class; settings/subitemscolumns.ini [Column_userfailedlogins] Class; settings/subitemscolumns.ini [Column_userlastvisit] Class; settings/subitemscolumns.ini [Column_userlocked] Class; settings/subitemscolumns.ini [Column_userlogin] Class; settings/subitemscolumns.ini [Column_userlogincount] Class; settings/subitemscolumns.ini [Column_userrolecount] Class; settings/subitemscolumns.ini [Column_userroles] Class |
| `expSubitemsVersionColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsversioncolumn.php:17` | Spalten der Unterelementliste zu den Versionen des Inhaltsobjekts. | settings/subitemscolumns.ini [Column_archivedcount] Class; settings/subitemscolumns.ini [Column_contributors] Class; settings/subitemscolumns.ini [Column_draftauthors] Class; settings/subitemscolumns.ini [Column_draftcount] Class; settings/subitemscolumns.ini [Column_initialcreator] Class; settings/subitemscolumns.ini [Column_latestdraft] Class; settings/subitemscolumns.ini [Column_pendingcount] Class; settings/subitemscolumns.ini [Column_rejectedcount] Class; settings/subitemscolumns.ini [Column_version] Class; settings/subitemscolumns.ini [Column_versioncount] Class; settings/subitemscolumns.ini [Column_versioncreated] Class; settings/subitemscolumns.ini [Column_workflowprocesses] Class |

### kernel/classes/syscard (16)

[Alle Muster von kernel/classes/syscard](area-kernel-classes-syscard.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSysCardAboutProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardaboutprovider.php:19` | Die Projektinformationen von exp:about: was Exponential ist, die Foundation und 7x, Velocity, die Lizenz, die Links, wie man mitwirkt, das Partnerpro… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardCacheProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcacheprovider.php:15` | Die Caches: welche aktiv sind (View-Cache, Template-Kompilierung, der statische Cache) und für exp:info die Größe des Cache-Verzeichnisses und jedes Caches in… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardComposerProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcomposerprovider.php:16` | Composer: wie viele Pakete vendor/ hat (vendor/composer/installed.json) und, wenn die Extension ezupdate aktiv ist, wie viele davon nicht über… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardCronProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcronprovider.php:14` | Die Cronjobs: wann einer zuletzt lief (das neueste Cronjob-Log oder der Lauf unter Setup › Cronjobs) und, für exp:info, jeder Teil von cronjob.ini mit seinen Skripten. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardDatabaseProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscarddatabaseprovider.php:15` | Die Datenbank: ihr Typ, die Version des Servers (wenn syscard.ini [Database] AskVersion=enabled: eine Verbindung, eine Frage) und der Host, auf dem sie liegt. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardExponentialProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardexponentialprovider.php:14` | Exponential selbst: die Version und ihre Linie und, bei einem Git-Checkout, der Branch und der Commit. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardExtensionsProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardextensionsprovider.php:14` | exp:info: jede aktive Extension mit ihrer Version, aus extension.xml oder ezinfo.php, danach aus composer.json (dieselben Quellen wie die Info-Seite). | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardHelpProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardhelpprovider.php:15` | Die Hilfe: wie viele Seiten sie ausliefert, ihre Sprachen und ob ihr Index aktuell ist (ein Index, der älter ist als die Seiten, ist ein Hinweis: ./console exp:help inde… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardHostProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardhostprovider.php:14` | Die Maschine: Betriebssystem, Kernel, Hostname, Uptime, Last, CPU, Speicher, der Datenträger der Installation (exp:info: jeder lokale Datenträger), die Shell und… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardPhpProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardphpprovider.php:17` | PHP: Version und SAPI, wie lange der Zweig Sicherheitsunterstützung hat (syscard.ini [Php] SecuritySupportEnds[]), OPcache und dessen JIT, die Extensions, die… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardQueueProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardqueueprovider.php:14` | exp:info: die Job-Warteschlange der Extension expqueue, wenn sie aktiv ist: ihr Dienst, wie viele Worker laufen und die letzte Aktion darauf (expQueueServ… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSettingsProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsettingsprovider.php:15` | exp:info: die INI-Einstellungen einer Erlaubnisliste (syscard.ini [Inventory] Settings[]=//). | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSiteAccessProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsiteaccessprovider.php:14` | exp:info: wie eine Anfrage ihren Siteaccess findet (site.ini [SiteAccessSettings] MatchOrder: uri, host, port ...) und, für jeden Siteaccess von AvailableSit… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSiteProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsiteprovider.php:14` | Die Site: die Siteaccesses und die Standard-Siteaccess, die aktiven Extensions, der Demo-Modus, die Debug-Ausgabe und der Mail-Transport (nur sein Typ: file, sendmail,… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardVelocityProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardvelocityprovider.php:17` | Exponential Velocity, der Anwendungsserver von 6.0.15: seine Version, ob er läuft, die aktiven und konfigurierten Worker, die Ports und TLS, die Umgebung… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardWebServerProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardwebserverprovider.php:15` | Die anderen Webserver auf der Maschine: Apache, nginx, Caddy, FrankenPHP und PHP-FPM mit seinen Mastern (einer je PHP-Version) und Pool-Workern (exp:info… | settings/syscard.ini [Providers] ProviderClasses[] |

### kernel/classes/transfer (3)

[Alle Muster von kernel/classes/transfer](area-kernel-classes-transfer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expKernelTransferQueueHandler` | class extends expQueueHandlerBase | `kernel/classes/transfer/expkerneltransferqueuehandler.php:37` | Der expqueue-Handler der Transfer-Läufe (expKernelTransferRunner, doc/eng-us/features/6.0/transfer-runs.md). | extension/bccie/settings/expqueue.ini [JobType_bccie.export] Handler; extension/xrowextract/settings/expqueue.ini [JobType_xrowextract.run] Handler |
| `expKernelTransferRegistry` | class | `kernel/classes/transfer/expkerneltransferregistry.php:30` | Die Transfer-Registry: jedes Import- und Export-Werkzeug der Installation, wie es sich in kernelservices.ini selbst deklariert (ein [Service_transfer.]-Block t… | settings/kernelservices.ini [Service_transfer.registry] Class |
| `expKernelTransferTool` | class | `kernel/classes/transfer/expkerneltransfertool.php:49` | Ein Import- oder Export-Tool der Transfer-Registry (expKernelTransferRegistry), wie es sich in einem Block [Service_transfer.] von kernelservice… | extension/bccie/settings/kernelservices.ini [Service_transfer.bccie] Class; extension/cjw_newsletter/settings/kernelservices.ini [Service_transfer.newsletter] Class; extension/ezodf/settings/kernelservices.ini [Service_transfer.ezodf] Class; extension/sqliimport/settings/kernelservices.ini [Service_transfer.sqliimport] Class; extension/syndication/settings/kernelservices.ini [Service_transfer.syndication] Class; extension/xrowextract/settings/kernelservices.ini [Service_transfer.xrowextract] Class; settings/kernelservices.ini [Service_transfer.audit] Class; settings/kernelservices.ini [Service_transfer.datatable] Class; settings/kernelservices.ini [Service_transfer.infocollector] Class; settings/kernelservices.ini [Service_transfer.package] Class; settings/kernelservices.ini [Service_transfer.pdf] Class; settings/kernelservices.ini [Service_transfer.rss_export] Class; settings/kernelservices.ini [Service_transfer.rss_import] Class |

### kernel/classes/uiswitch (2)

[Alle Muster von kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUISwitchDesignProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchdesignprovider.php:25` | Das Design der Administrationsoberfläche (das „UI-Chrome“): admin4l, admin4, das klassische Admin-Design, AdminUI oder eine andere installierte Administrations… | settings/uiswitch.ini [UISwitch_design] Class |
| `expUISwitchLanguageProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchlanguageprovider.php:27` | Die Anzeigesprache der Administrationsoberfläche: was site.ini [RegionalSettings] Locale des Siteaccess festlegt (die Übersetzungen der Oberfl… | settings/uiswitch.ini [UISwitch_language] Class |

### kernel/modules (1)

[Alle Muster von kernel/modules](bereich-kernel-modules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSearchEngine` | class | `kernel/search/plugins/ezsearchengine/ezsearchengine.php:18` | – | settings/site.ini [SearchSettings] SearchEngine |

### kernel/private (16)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentObjectState` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstate.php:18` | Klasse, die einen Inhaltsobjektzustand darstellt | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroup` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategroup.php:18` | Klasse, die eine Inhaltsobjekt-Zustandsgruppe darstellt | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroupLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategrouplanguage.php:21` | Diese Klasse kümmert sich um die Lokalisierung einer Inhaltsobjekt-Zustandsgruppe. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstatelanguage.php:21` | Diese Klasse kümmert sich um die Lokalisierung eines Inhaltsobjektzustands. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `ezpAttributeOperatorHTMLFormatter` | class extends ezpAttributeOperatorFormatter | `kernel/private/eztemplate/ezpattributeoperatorhtmlformatter.php:12` | – | settings/template.ini [AttributeOperator] OutputFormatter[html] |
| `ezpAttributeOperatorTextFormatter` | class extends ezpAttributeOperatorFormatter | `kernel/private/eztemplate/ezpattributeoperatortextformatter.php:12` | – | settings/template.ini [AttributeOperator] OutputFormatter[text] |
| `ezpContentPublishingQueue` | class | `kernel/private/classes/ezpcontentpublishingqueue.php:19` | Diese Klasse verwaltet die Warteschlange zum Veröffentlichen von Inhalten. | settings/content.ini [PublishingSettings] AsynchronousPublishingQueueReader |
| `ezpContentXHTMLRenderer` | class extends ezpRestContentRendererInterface | `kernel/private/rest/classes/renderers/xhtml_content_renderer.php:12` | – | settings/rest.ini [OutputSettings] RendererClass[xhtml] |
| `ezpLanguageSwitcher` | class | `kernel/private/classes/ezplanguageswitcher.php:18` | Hilfsklasse zum Umwandeln von URLs zwischen Siteaccesses. | settings/site.ini [RegionalSettings] LanguageSwitcherClass |
| `ezpMobileDeviceRegexpFilter` | class | `kernel/private/classes/ezpmobiledeviceregexpfilter.php:15` | Standardimplementierung der Schnittstelle zur Erkennung mobiler Geräte. | settings/site.ini [SiteAccessSettings] MobileDeviceFilterClass |
| `ezpMultivariateTestHandler` | class | `kernel/private/classes/ezpmultivariatetesthandler.php:12` | – | settings/content.ini [TestingSettings] MultivariateTestingHandlerClass |
| `ezpRestAuthProvider` | class | `kernel/private/rest/classes/auth/auth_provider.php:11` | – | settings/rest.ini [ApiProvider] ProviderClass[auth] |
| `ezpRestDefaultRegexpPrefixFilter` | class extends ezpRestPrefixFilterInterface | `kernel/private/rest/classes/default_regexp_prefix_filter.php:15` | Standardimplementierung der Präfixfilter-Schnittstelle. | settings/rest.ini [System] PrefixFilterClass |
| `ezpRestIniRouteFilter` | class extends ezpRestRouteFilterInterface | `kernel/private/rest/classes/auth/ini_route_security.php:12` | – | settings/rest.ini [RouteSettings] RouteSettingImpl |
| `ezpRestOauthAuthenticationStyle` | class extends ezpRestAuthenticationStyle | `kernel/private/rest/classes/auth/styles/oauth.php:12` | – | settings/rest.ini [Authentication] AuthenticationStyle |
| `ezpRestRoutesCacheClear` | class | `kernel/private/rest/classes/cache/clear_routes.php:15` | Handler zum Leeren des Caches. | settings/site.ini [Cache_restRoutes] class; settings/site.ini [Cache_restRoutes] purgeClass |

### kernel/private/classes/clusterfilehandlers (2)

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandlerDFSBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php:12` | – | settings/file.ini [eZDFSClusteringSettings] DFSBackend |
| `eZDFSFileHandlerMySQLiBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php:50` | – | settings/file.ini [eZDFSClusteringSettings] DBBackend |

### kernel/private/classes/urlaliasfilters (1)

[Alle Muster von kernel/private/classes/urlaliasfilters](bereich-kernel-private-classes-urlaliasfilters.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLAliasFilterAppendNodeID` | class extends eZURLAliasFilter | `kernel/private/classes/urlaliasfilters/ezurlaliasfilterappendnodeid.php:18` | Diese Klasse ist ein URL-Alias-Filter, der mit dem eZURLAliasFilter-System ausgeführt wird | settings/site.ini [URLTranslator] FilterClasses[] |

### lib/ezdb (5)

[Alle Muster von lib/ezdb](bereich-lib-ezdb.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMongoDB` | class extends eZDBInterface | `lib/ezdb/classes/expmongodb.php:18` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[mongodb] |
| `eZDBInterface` | class | `lib/ezdb/classes/ezdbinterface.php:21` | Die eZDBInterface definiert die Schnittstelle für alle Datenbankimplementierungen | settings/kernelservices.ini [Service_db] Class |
| `eZMySQLiDB` | class extends eZDBInterface | `lib/ezdb/classes/ezmysqlidb.php:22` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[ezmysql]; settings/site.ini [DatabaseSettings] ImplementationAlias[ezmysqli]; settings/site.ini [DatabaseSettings] ImplementationAlias[mysql]; settings/site.ini [DatabaseSettings] ImplementationAlias[mysqli] |
| `eZPostgreSQLDB` | class extends eZDBInterface | `lib/ezdb/classes/ezpostgresqldb.php:23` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[ezpostgresql]; settings/site.ini [DatabaseSettings] ImplementationAlias[pgsql]; settings/site.ini [DatabaseSettings] ImplementationAlias[postgresql] |
| `eZSQLite3DB` | class extends eZDBInterface | `lib/ezdb/classes/ezsqlite3db.php:3` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[sqlite3] |

### lib/ezdbschema (4)

[Alle Muster von lib/ezdbschema](bereich-lib-ezdbschema.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMongoSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/expmongoschema.php:11` | MongoDB-Schema-Handler für eZDbSchema. | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[mongo] |
| `eZMysqlSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/ezmysqlschema.php:19` | – | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[mysql]; settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[mysqli] |
| `eZPgsqlSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/ezpgsqlschema.php:19` | – | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[postgresql] |
| `eZSQLiteSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/ezsqliteschema.php:3` | – | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[sqlite3]; settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[sqlite] |

### lib/ezimage (2)

[Alle Muster von lib/ezimage](bereich-lib-ezimage.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageGDFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimagegdfactory.php:12` | – | settings/image.ini [GD] Handler |
| `eZImageShellFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimageshellfactory.php:12` | – | settings/image.ini [ImageMagick] Handler |

### lib/ezmath (1)

[Alle Muster von lib/ezmath](bereich-lib-ezmath.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPHPMath` | class | `lib/ezmath/classes/mathhandlers/ezphpmath.php:17` | – | settings/shop.ini [MathSettings] MathHandler |

### lib/ezutils (5)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFileTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezfiletransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[file] |
| `eZHTTPTool` | class | `lib/ezutils/classes/ezhttptool.php:25` | – | settings/kernelservices.ini [Service_http] Class |
| `eZINI` | class | `lib/ezutils/classes/ezini.php:53` | ### EXP-MULTI-SITE-OVERRIDE-SETTINGS ### - lib/ezutils/classes/ezini.php - neue INI-Einstellung ext-siteaccess-override - - neue INI-Einstellung ext-siteaccess… | settings/kernelservices.ini [Service_ini.i18n] Class; settings/kernelservices.ini [Service_ini.site] Class |
| `eZSendmailTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsendmailtransport.php:20` | – | settings/site.ini [MailSettings] TransportAlias[sendmail] |
| `eZSMTPTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsmtptransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[smtp] |

## INI-Plugin-Punkte (842)

### extension/AdminAid (18)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SearchDatabase] ClassList[]` | `eZContentObject` | `extension/AdminAid/settings/aid.ini.append.php:26` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectTreeNode` | `extension/AdminAid/settings/aid.ini.append.php:30` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectAttribute` | `extension/AdminAid/settings/aid.ini.append.php:34` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectVersion` | `extension/AdminAid/settings/aid.ini.append.php:38` |
| `[SearchDatabase] ClassList[]` | `eZContentClass` | `extension/AdminAid/settings/aid.ini.append.php:41` |
| `[SearchDatabase] ClassList[]` | `eZContentClassAttribute` | `extension/AdminAid/settings/aid.ini.append.php:45` |
| `[SearchDatabase] ClassList[]` | `eZUser` | `extension/AdminAid/settings/aid.ini.append.php:49` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectState` | `extension/AdminAid/settings/aid.ini.append.php:53` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroup` | `extension/AdminAid/settings/aid.ini.append.php:57` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateLanguage` | `extension/AdminAid/settings/aid.ini.append.php:61` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroupLanguage` | `extension/AdminAid/settings/aid.ini.append.php:65` |
| `[SearchDatabase] ClassList[]` | `eZContentLanguage` | `extension/AdminAid/settings/aid.ini.append.php:69` |
| `[SearchDatabase] ClassList[]` | `eZContentBrowseBookmark` | `extension/AdminAid/settings/aid.ini.append.php:73` |
| `[SearchDatabase] ClassList[]` | `eZImageFile` | `extension/AdminAid/settings/aid.ini.append.php:77` |
| `[SearchDatabase] ClassList[]` | `eZInformationCollectionAttribute` | `extension/AdminAid/settings/aid.ini.append.php:81` |
| `[SearchDatabase] ClassList[]` | `eZNodeAssignment` | `extension/AdminAid/settings/aid.ini.append.php:85` |
| `[SearchDatabase] ClassList[]` | `eZURL` | `extension/AdminAid/settings/aid.ini.append.php:88` |
| `[SearchDatabase] ClassList[]` | `eZWorkflow` | `extension/AdminAid/settings/aid.ini.append.php:96` |

### extension/bccie (44)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[CieSettings] ExportOutputFormatHandlers[utf8]` | `bccieExportFormatOutputHandlerUtf8` | `extension/bccie/settings/cie.ini.append.php:90` |
| `[CieSettings] ExportOutputFormatHandlers[utf8bom]` | `bccieExportFormatOutputHandlerUtf8Bom` | `extension/bccie/settings/cie.ini.append.php:91` |
| `[CieSettings] ExportOutputFormatHandlers[utf16le]` | `bccieExportFormatOutputHandlerUtf16Le` | `extension/bccie/settings/cie.ini.append.php:92` |
| `[CieSettings] ExportOutputFormatHandlers[cp1252]` | `bccieExportFormatOutputHandlerCP1252` | `extension/bccie/settings/cie.ini.append.php:93` |
| `[ezboolean] HandlerClass` | `eZBooleanHandler` | `extension/bccie/settings/export.ini:31` |
| `[ezcountry] HandlerClass` | `eZCountryHandler` | `extension/bccie/settings/export.ini:35` |
| `[ezdate] HandlerClass` | `eZDateHandler` | `extension/bccie/settings/export.ini:39` |
| `[ezdatetime] HandlerClass` | `eZDateTimeHandler` | `extension/bccie/settings/export.ini:43` |
| `[ezemail] HandlerClass` | `eZEmailHandler` | `extension/bccie/settings/export.ini:47` |
| `[ezenhancedobjectrelation] HandlerClass` | `ezenhancedobjectrelationHandler` | `extension/bccie/settings/export.ini:51` |
| `[ezfloat] HandlerClass` | `eZFloatHandler` | `extension/bccie/settings/export.ini:58` |
| `[ezidentifier] HandlerClass` | `eZIdentifierHandler` | `extension/bccie/settings/export.ini:62` |
| `[ezinteger] HandlerClass` | `eZIntegerHandler` | `extension/bccie/settings/export.ini:66` |
| `[ezobjectrelation] HandlerClass` | `eZObjectRelationHandler` | `extension/bccie/settings/export.ini:70` |
| `[ezoption] HandlerClass` | `eZOptionHandler` | `extension/bccie/settings/export.ini:77` |
| `[ezselection] HandlerClass` | `eZSelectionHandler` | `extension/bccie/settings/export.ini:81` |
| `[ezstring] HandlerClass` | `eZStringHandler` | `extension/bccie/settings/export.ini:85` |
| `[eztext] HandlerClass` | `eZTextHandler` | `extension/bccie/settings/export.ini:89` |
| `[eztime] HandlerClass` | `eZTimeHandler` | `extension/bccie/settings/export.ini:93` |
| `[ezurl] HandlerClass` | `eZURLHandler` | `extension/bccie/settings/export.ini:97` |
| `[ezxmltext] HandlerClass` | `eZXMLTextHandler` | `extension/bccie/settings/export.ini:101` |
| `[owenhancedselection] HandlerClass` | `OWEnhancedSelectionHandler` | `extension/bccie/settings/export.ini:105` |
| `[smileobjectrelationlist] HandlerClass` | `smileobjectrelationlistHandler` | `extension/bccie/settings/export.ini:109` |
| `[ezbirthday] HandlerClass` | `eZBirthdayHandler` | `extension/bccie/settings/export.ini:113` |
| `[ezstring] HandlerClass` | `eZStringHandler` | `extension/bccie/settings/export.ini.append.php:32` |
| `[ezinteger] HandlerClass` | `eZIntegerHandler` | `extension/bccie/settings/export.ini.append.php:36` |
| `[ezxmltext] HandlerClass` | `eZXMLTextHandler` | `extension/bccie/settings/export.ini.append.php:40` |
| `[ezboolean] HandlerClass` | `eZBooleanHandler` | `extension/bccie/settings/export.ini.append.php:44` |
| `[ezidentifier] HandlerClass` | `eZIdentifierHandler` | `extension/bccie/settings/export.ini.append.php:48` |
| `[ezfloat] HandlerClass` | `eZFloatHandler` | `extension/bccie/settings/export.ini.append.php:52` |
| `[ezemail] HandlerClass` | `eZEmailHandler` | `extension/bccie/settings/export.ini.append.php:56` |
| `[ezurl] HandlerClass` | `eZURLHandler` | `extension/bccie/settings/export.ini.append.php:60` |
| `[eztext] HandlerClass` | `eZTextHandler` | `extension/bccie/settings/export.ini.append.php:64` |
| `[ezenhancedobjectrelation] HandlerClass` | `ezenhancedobjectrelationHandler` | `extension/bccie/settings/export.ini.append.php:68` |
| `[ezselection] HandlerClass` | `eZSelectionHandler` | `extension/bccie/settings/export.ini.append.php:75` |
| `[ezoption] HandlerClass` | `eZOptionHandler` | `extension/bccie/settings/export.ini.append.php:79` |
| `[ezcountry] HandlerClass` | `eZCountryHandler` | `extension/bccie/settings/export.ini.append.php:83` |
| `[ezdatetime] HandlerClass` | `eZDateTimeHandler` | `extension/bccie/settings/export.ini.append.php:87` |
| `[eztime] HandlerClass` | `eZTimeHandler` | `extension/bccie/settings/export.ini.append.php:91` |
| `[ezdate] HandlerClass` | `eZDateHandler` | `extension/bccie/settings/export.ini.append.php:95` |
| `[smileobjectrelationlist] HandlerClass` | `smileobjectrelationlistHandler` | `extension/bccie/settings/export.ini.append.php:99` |
| `[ezbirthday] HandlerClass` | `eZBirthdayExportHandler` | `extension/bccie/settings/export.ini.append.php:103` |
| `[JobType_bccie.export] Handler` | `expKernelTransferQueueHandler` | `extension/bccie/settings/expqueue.ini.append.php:9` |
| `[Service_transfer.bccie] Class` | `expKernelTransferTool` | `extension/bccie/settings/kernelservices.ini.append.php:9` |

### extension/birthday (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezbirthday] HandlerClass` | `eZBirthdayCsvHandler` | `extension/birthday/settings/csv.ini.append.php:19` |

### extension/cjw_newsletter (21)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[cjw_newsletter]` | `cjwNewsletterAuditBranch` | `extension/cjw_newsletter/settings/audit.ini.append.php:6` |
| `[NewsletterFilterSettings] AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeSalutation` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:128` |
| `[NewsletterFilterSettings] AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeEmail` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:129` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterDeliverabilityHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:186` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterEditorialHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:256` |
| `[ScheduleSettings] ConditionHandlers[]` | `CjwNewsletterScheduleConditionNewArticles` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:269` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterRenderingHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:306` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterStatisticsHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:381` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterSmsHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:423` |
| `[SmsTransport_file] Class` | `CjwNewsletterSmsTransportFile` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:469` |
| `[SmsTransport_http] Class` | `CjwNewsletterSmsTransportHttp` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:481` |
| `[SmsTransport_twilio] Class` | `CjwNewsletterSmsTransportTwilio` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:504` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterImportExportHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:513` |
| `[CjwNewsletterListFilter] ClassName` | `CjwNewsletterListFilter` | `extension/cjw_newsletter/settings/extendedattributefilter.ini.append.php:23` |
| `[CjwNewsletterEditionFilter] ClassName` | `CjwNewsletterEditionFilter` | `extension/cjw_newsletter/settings/extendedattributefilter.ini.append.php:34` |
| `[Service_transfer.newsletter] Class` | `expKernelTransferTool` | `extension/cjw_newsletter/settings/kernelservices.ini.append.php:10` |
| `[Category_newsletter] HandlerClass` | `CjwNewsletterMailCategoryHandler` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:8` |
| `[SuppressionSettings] Listeners[]` | `CjwNewsletterMailPreferences` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:13` |
| `[BounceSettings] MessageListeners[]` | `CjwNewsletterMailin` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:24` |
| `[Category_newsletter_statistics] HandlerClass` | `CjwNewsletterStatisticsCategoryHandler` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:35` |
| `[Category_sms] HandlerClass` | `CjwNewsletterSmsCategoryHandler` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:49` |

### extension/cjw_themes_jumper (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[cjwImagine] Handler` | `cjwJumperImagineImageFactory` | `extension/cjw_themes_jumper/settings/image.ini.append.php:40` |
| `[BlockDefinition_ezcontent_field] Handler` | `expLayoutsTitleBlockHandler` | `extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini.append.php:50` |

### extension/cjw_tmv (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[BlockDefinition_tmv_events] Handler` | `cjwTmvEventsBlockHandler` | `extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini.append.php:10` |

### extension/enhancedselection2 (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SolrFieldMapSettings] CustomMap[sckenhancedselection]` | `ezfSolrDocumentFieldSckEnhancedSelection` | `extension/enhancedselection2/settings/ezfind.ini.append.php:4` |

### extension/expai (69)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[expai]` | `expAiAuditBranch` | `extension/expai/settings/audit.ini.append.php:12` |
| `[AuditSourceSettings] Sources[expai]` | `expAiAuditSource` | `extension/expai/settings/audit.ini.append.php:28` |
| `[Tool_get_node] Class` | `expAiToolGetNode` | `extension/expai/settings/expai.ini.append.php:670` |
| `[Tool_list_children] Class` | `expAiToolListChildren` | `extension/expai/settings/expai.ini.append.php:673` |
| `[Tool_get_tree_context] Class` | `expAiToolGetTreeContext` | `extension/expai/settings/expai.ini.append.php:676` |
| `[Tool_list_creatable_classes] Class` | `expAiToolListCreatableClasses` | `extension/expai/settings/expai.ini.append.php:679` |
| `[Tool_get_class_definition] Class` | `expAiToolGetClassDefinition` | `extension/expai/settings/expai.ini.append.php:682` |
| `[Tool_search_content] Class` | `expAiToolSearchContent` | `extension/expai/settings/expai.ini.append.php:685` |
| `[Tool_create_draft] Class` | `expAiToolCreateDraft` | `extension/expai/settings/expai.ini.append.php:688` |
| `[Tool_update_draft] Class` | `expAiToolUpdateDraft` | `extension/expai/settings/expai.ini.append.php:691` |
| `[QueueSettings] Appliers[chain]` | `expAiChainApplier` | `extension/expai/settings/expai.ini.append.php:719` |
| `[ChainSettings] TranslateHandler` | `expAiChainTranslateSync` | `extension/expai/settings/expai.ini.append.php:739` |
| `[FieldAction_write] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:860` |
| `[FieldAction_rewrite] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:869` |
| `[FieldAction_shorten] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:878` |
| `[FieldAction_translate] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:886` |
| `[FieldAction_suggest] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:895` |
| `[QueueSettings] Appliers[vector_plan_item]` | `expAiPlanItemApplier` | `extension/expai/settings/expai.ini.append.php:1328` |
| `[Tool_web_fetch] Class` | `expAiToolWebFetch` | `extension/expai/settings/expai.ini.append.php:1358` |
| `[Tool_web_search] Class` | `expAiToolWebSearch` | `extension/expai/settings/expai.ini.append.php:1361` |
| `[Tool_project_history] Class` | `expAiToolProjectHistory` | `extension/expai/settings/expai.ini.append.php:1364` |
| `[SearchProvider_brave] Class` | `expAiWebSearchBrave` | `extension/expai/settings/expai.ini.append.php:1400` |
| `[DiffSettings] Segmenters[ezstring]` | `expAiSegmenterString` | `extension/expai/settings/expai.ini.append.php:1451` |
| `[DiffSettings] Segmenters[ezemail]` | `expAiSegmenterString` | `extension/expai/settings/expai.ini.append.php:1452` |
| `[DiffSettings] Segmenters[ezisbn]` | `expAiSegmenterString` | `extension/expai/settings/expai.ini.append.php:1453` |
| `[DiffSettings] Segmenters[eztext]` | `expAiSegmenterText` | `extension/expai/settings/expai.ini.append.php:1454` |
| `[DiffSettings] Segmenters[ezxmltext]` | `expAiSegmenterRichText` | `extension/expai/settings/expai.ini.append.php:1455` |
| `[DiffSettings] Segmenters[ezrichtext]` | `expAiSegmenterRichText` | `extension/expai/settings/expai.ini.append.php:1456` |
| `[DiffSettings] Segmenters[ezkeyword]` | `expAiSegmenterKeyword` | `extension/expai/settings/expai.ini.append.php:1457` |
| `[DiffSettings] FallbackSegmenter` | `expAiSegmenterGeneric` | `extension/expai/settings/expai.ini.append.php:1458` |
| `[QueueSettings] Appliers[translation_sync]` | `expAiTranslationSyncApplier` | `extension/expai/settings/expai.ini.append.php:1515` |
| `[FieldAction_alt_text] Handler` | `expAiAltTextFieldAction` | `extension/expai/settings/expai.ini.append.php:1531` |
| `[FieldAction_preset_title] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1640` |
| `[FieldAction_preset_summary] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1648` |
| `[FieldAction_preset_meta_description] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1657` |
| `[FieldAction_preset_keywords] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1666` |
| `[FieldAction_improve_shorten] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1675` |
| `[FieldAction_improve_expand] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1681` |
| `[FieldAction_improve_clarify] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1687` |
| `[FieldAction_improve_grammar] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1693` |
| `[FieldAction_improve_simplify] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1699` |
| `[FieldAction_improve_formal] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1705` |
| `[FieldAction_improve_casual] Handler` | `expAiPromptFieldAction` | `extension/expai/settings/expai.ini.append.php:1711` |
| `[JobType_expai.request] Handler` | `expAiRequestHandler` | `extension/expai/settings/expqueue.ini.append.php:9` |
| `[JobType_expai.batch.submit] Handler` | `expAiBatchSubmitHandler` | `extension/expai/settings/expqueue.ini.append.php:19` |
| `[JobType_expai.batch.poll] Handler` | `expAiBatchPollHandler` | `extension/expai/settings/expqueue.ini.append.php:29` |
| `[JobType_expai.thread.turn] Handler` | `expAiThreadTurnHandler` | `extension/expai/settings/expqueue.ini.append.php:63` |
| `[JobType_expai.thread.item] Handler` | `expAiThreadItemHandler` | `extension/expai/settings/expqueue.ini.append.php:74` |
| `[JobType_expai.translation.check] Handler` | `expAiTranslationCheckHandler` | `extension/expai/settings/expqueue.ini.append.php:85` |
| `[JobType_expai.alttext] Handler` | `expAiAltTextJobHandler` | `extension/expai/settings/expqueue.ini.append.php:97` |
| `[JobType_expai.model.test] Handler` | `expAiModelTestHandler` | `extension/expai/settings/expqueue.ini.append.php:110` |
| `[JobType_expai.exchange.rates] Handler` | `expAiExchangeRatesHandler` | `extension/expai/settings/expqueue.ini.append.php:120` |
| `[ezjscServer_expai] Class` | `expAiServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:11` |
| `[ezjscServer_expaivector] Class` | `expAiVectorServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:17` |
| `[ezjscServer_expaitranslation] Class` | `expAiTranslationSyncServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:24` |
| `[ezjscServer_expaialttext] Class` | `expAiAltTextServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:31` |
| `[ezjscServer_expaipreset] Class` | `expAiPresetServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:37` |
| `[ezjscServer_expaimodel] Class` | `expAiModelServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:43` |
| `[ezjscServer_expairevert] Class` | `expAiDraftRevertServerFunctions` | `extension/expai/settings/ezjscore.ini.append.php:50` |
| `[ezjscServer_expaiwatch] Class` | `expAiRunWatch` | `extension/expai/settings/ezjscore.ini.append.php:56` |
| `[Registry_expai_provider_types] Implements` | `expAiProvider` | `extension/expai/settings/rad.ini.append.php:35` |
| `[Registry_expai_batch_apis] Implements` | `expAiBatchApi` | `extension/expai/settings/rad.ini.append.php:61` |
| `[Registry_expai_tools] Entries[Class]` | `expAiTool` | `extension/expai/settings/rad.ini.append.php:79` |
| `[Registry_expai_search_providers] Entries[Class]` | `expAiWebSearchProvider` | `extension/expai/settings/rad.ini.append.php:88` |
| `[Registry_expai_field_actions] Entries[Handler]` | `expAiFieldAction` | `extension/expai/settings/rad.ini.append.php:145` |
| `[Registry_expai_presets] Entries[Handler]` | `expAiFieldAction` | `extension/expai/settings/rad.ini.append.php:156` |
| `[Registry_expai_appliers] Entries[Appliers]` | `expAiRequestApplier` | `extension/expai/settings/rad.ini.append.php:165` |
| `[Registry_expai_segmenters] Entries[Segmenters]` | `expAiSegmenter` | `extension/expai/settings/rad.ini.append.php:174` |
| `[Registry_expai_segmenters] Entries[FallbackSegmenter]` | `expAiSegmenter` | `extension/expai/settings/rad.ini.append.php:175` |

### extension/expchangeclass (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezstring] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:39` |
| `[eztext] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:48` |
| `[ezobjectrelationlist] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:55` |

### extension/explayouts (85)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ReverseRelationSettings] ProviderClassList[]` | `expLayoutsReverseRelationProvider` | `extension/explayouts/settings/content.ini.append.php:6` |
| `[Panel_explayouts] Class` | `expLayoutsDebugBarPanel` | `extension/explayouts/settings/debugbar.ini.append.php:10` |
| `[BlockDefinition_two_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:61` |
| `[BlockDefinition_column] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:77` |
| `[BlockDefinition_tpl_block] Handler` | `expLayoutsTplBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:85` |
| `[BlockDefinition_text] Handler` | `expLayoutsTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:92` |
| `[BlockDefinition_title] Handler` | `expLayoutsTitleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:99` |
| `[BlockDefinition_html] Handler` | `expLayoutsHtmlBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:107` |
| `[BlockDefinition_rich_text] Handler` | `expLayoutsRichTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:114` |
| `[BlockDefinition_image] Handler` | `expLayoutsImageBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:121` |
| `[BlockDefinition_list] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:130` |
| `[BlockDefinition_single] Handler` | `expLayoutsSingleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:148` |
| `[BlockDefinition_button] Handler` | `expLayoutsButtonBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:155` |
| `[BlockDefinition_spacer] Handler` | `expLayoutsSpacerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:165` |
| `[BlockDefinition_divider] Handler` | `expLayoutsDividerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:171` |
| `[BlockDefinition_card] Handler` | `expLayoutsCardBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:177` |
| `[BlockDefinition_accordion] Handler` | `expLayoutsAccordionBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:188` |
| `[BlockDefinition_tabs] Handler` | `expLayoutsTabsBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:195` |
| `[BlockDefinition_grid] Handler` | `expLayoutsGridBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:202` |
| `[BlockDefinition_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:209` |
| `[BlockDefinition_slider] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:231` |
| `[BlockDefinition_thumb_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:247` |
| `[BlockDefinition_grid_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:263` |
| `[BlockDefinition_sushi_bar] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:279` |
| `[BlockDefinition_list_zigzag] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:295` |
| `[BlockDefinition_list_accordion] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:302` |
| `[BlockDefinition_quote] Handler` | `expLayoutsQuoteBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:309` |
| `[BlockDefinition_alert] Handler` | `expLayoutsAlertBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:318` |
| `[BlockDefinition_video] Handler` | `expLayoutsVideoBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:325` |
| `[BlockDefinition_badge] Handler` | `expLayoutsBadgeBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:335` |
| `[BlockDefinition_progress] Handler` | `expLayoutsProgressBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:343` |
| `[BlockDefinition_map] Handler` | `expLayoutsMapBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:351` |
| `[BlockDefinition_carousel] Handler` | `expLayoutsCarouselBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:363` |
| `[BlockDefinition_markdown] Handler` | `expLayoutsMarkdownBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:370` |
| `[BlockDefinition_full_view] Handler` | `expLayoutsFullViewBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:377` |
| `[BlockDefinition_hero] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:386` |
| `[BlockDefinition_about] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:398` |
| `[BlockDefinition_features] Handler` | `expLayoutsFeaturesBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:410` |
| `[BlockDefinition_lead] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:423` |
| `[BlockDefinition_logos] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:435` |
| `[BlockDefinition_three_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:442` |
| `[BlockDefinition_four_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:460` |
| `[BlockDefinition_exp_component_hero] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:485` |
| `[BlockDefinition_exp_component_features] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:494` |
| `[BlockDefinition_exp_component_about] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:507` |
| `[BlockDefinition_exp_component_logos] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:518` |
| `[BlockDefinition_exp_component_quote] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:526` |
| `[BlockDefinition_exp_component_lead] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:533` |
| `[QueryType_children] Handler` | `expLayoutsChildrenQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:555` |
| `[QueryType_parent] Handler` | `expLayoutsParentQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:559` |
| `[QueryType_subtree] Handler` | `expLayoutsSubtreeQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:563` |
| `[QueryType_siblings] Handler` | `expLayoutsSiblingsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:567` |
| `[QueryType_latest] Handler` | `expLayoutsLatestQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:571` |
| `[QueryType_random] Handler` | `expLayoutsRandomQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:575` |
| `[QueryType_manual] Handler` | `expLayoutsManualQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:579` |
| `[QueryType_exp_content_relation_list] Handler` | `expLayoutsRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:583` |
| `[QueryType_exp_content_reverse_relation_list] Handler` | `expLayoutsReverseRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:587` |
| `[QueryType_exp_content_tags] Handler` | `expLayoutsTagsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:591` |
| `[QueryType_exponential_content_search] Handler` | `expLayoutsExponentialContentSearchQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:595` |
| `[QueryType_content_by_topic] Handler` | `expLayoutsContentByTopicQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:599` |
| `[BlockDefinition_admin_logo] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:632` |
| `[BlockDefinition_admin_search] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:639` |
| `[BlockDefinition_admin_theme_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:646` |
| `[BlockDefinition_admin_menu_button] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:653` |
| `[BlockDefinition_admin_sidebar_controls] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:660` |
| `[BlockDefinition_admin_sidebar_toggles] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:669` |
| `[BlockDefinition_admin_tab_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:676` |
| `[BlockDefinition_admin_left_menu] Handler` | `expLayoutsAdminLeftMenuBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:683` |
| `[BlockDefinition_admin_content_tree] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:690` |
| `[BlockDefinition_admin_clear_cache] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:697` |
| `[BlockDefinition_admin_bookmarks] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:704` |
| `[BlockDefinition_admin_ui_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:711` |
| `[BlockDefinition_admin_current_user] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:718` |
| `[BlockDefinition_admin_preferences] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:725` |
| `[BlockDefinition_admin_quick_settings] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:732` |
| `[BlockDefinition_admin_toolbar_right] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:739` |
| `[BlockDefinition_admin_toolbar_developer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:746` |
| `[BlockDefinition_admin_breadcrumb] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:753` |
| `[BlockDefinition_admin_module_result] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:760` |
| `[BlockDefinition_admin_footer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:767` |
| `[BlockDefinition_admin_popup_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:774` |
| `[BlockDefinition_admin_overlay] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:781` |
| `[BlockDefinition_admin_debug_area] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:788` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/explayouts/settings/ezjscore.ini.append.php:7` |
| `[OutputSettings] HandlerClass` | `ExplBlockXHTMLXMLOutput` | `extension/explayouts/settings/ezxml.ini.append.php:4` |

### extension/explayouts_content_browser_core (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[explayouts_content_browser_search] ClassName` | `expLayoutsContentBrowserCoreSearchFilter` | `extension/explayouts_content_browser_core/settings/extendedattributefilter.ini.append.php:7` |

### extension/explayouts_core (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[explayouts_core]` | `expLayoutsCoreAuditBranch` | `extension/explayouts_core/settings/audit.ini.append.php:6` |

### extension/expqueue (5)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[expqueue]` | `expQueueAuditBranch` | `extension/expqueue/settings/audit.ini.append.php:5` |
| `[JobType_expqueue.noop] Handler` | `expQueueNoopHandler` | `extension/expqueue/settings/expqueue.ini.append.php:167` |
| `[JobType_expqueue.sleep] Handler` | `expQueueSleepHandler` | `extension/expqueue/settings/expqueue.ini.append.php:173` |
| `[JobType_expqueue.fail] Handler` | `expQueueFailHandler` | `extension/expqueue/settings/expqueue.ini.append.php:178` |
| `[Registry_expqueue_job_types] Entries[Handler]` | `expQueueHandler` | `extension/expqueue/settings/rad.ini.append.php:21` |

### extension/expservices (69)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[expservices]` | `expServicesAuditBranch` | `extension/expservices/settings/audit.ini.append.php:5` |
| `[ezjscServer_expservices] Class` | `expServicesCatalog` | `extension/expservices/settings/ezjscore.ini.append.php:7` |
| `[ezjscServer_expsession] Class` | `expSessionServices` | `extension/expservices/settings/ezjscore.ini.append.php:10` |
| `[ezjscServer_expsystem] Class` | `expSystemServices` | `extension/expservices/settings/ezjscore.ini.append.php:13` |
| `[ezjscServer_expini] Class` | `expIniServices` | `extension/expservices/settings/ezjscore.ini.append.php:16` |
| `[ezjscServer_expcache] Class` | `expCacheServices` | `extension/expservices/settings/ezjscore.ini.append.php:19` |
| `[ezjscServer_expcronjob] Class` | `expCronjobServices` | `extension/expservices/settings/ezjscore.ini.append.php:22` |
| `[ezjscServer_expextension] Class` | `expExtensionServices` | `extension/expservices/settings/ezjscore.ini.append.php:25` |
| `[ezjscServer_exppackage] Class` | `expPackageServices` | `extension/expservices/settings/ezjscore.ini.append.php:28` |
| `[ezjscServer_expworkflow] Class` | `expWorkflowServices` | `extension/expservices/settings/ezjscore.ini.append.php:31` |
| `[ezjscServer_expvelocity] Class` | `expVelocityServices` | `extension/expservices/settings/ezjscore.ini.append.php:34` |
| `[ezjscServer_exprad] Class` | `expRadServices` | `extension/expservices/settings/ezjscore.ini.append.php:37` |
| `[ezjscServer_expdebug] Class` | `expDebugServices` | `extension/expservices/settings/ezjscore.ini.append.php:40` |
| `[ezjscServer_expuser] Class` | `expUserServices` | `extension/expservices/settings/ezjscore.ini.append.php:44` |
| `[ezjscServer_expusergroup] Class` | `expUserGroupServices` | `extension/expservices/settings/ezjscore.ini.append.php:47` |
| `[ezjscServer_exprole] Class` | `expRoleServices` | `extension/expservices/settings/ezjscore.ini.append.php:50` |
| `[ezjscServer_exppolicy] Class` | `expPolicyServices` | `extension/expservices/settings/ezjscore.ini.append.php:53` |
| `[ezjscServer_expsessionadmin] Class` | `expSessionAdminServices` | `extension/expservices/settings/ezjscore.ini.append.php:56` |
| `[ezjscServer_exppreferences] Class` | `expPreferencesServices` | `extension/expservices/settings/ezjscore.ini.append.php:59` |
| `[ezjscServer_expnotification] Class` | `expNotificationServices` | `extension/expservices/settings/ezjscore.ini.append.php:62` |
| `[ezjscServer_expcollaboration] Class` | `expCollaborationServices` | `extension/expservices/settings/ezjscore.ini.append.php:65` |
| `[ezjscServer_expbookmark] Class` | `expBookmarkServices` | `extension/expservices/settings/ezjscore.ini.append.php:68` |
| `[ezjscServer_expaccount] Class` | `expAccountServices` | `extension/expservices/settings/ezjscore.ini.append.php:71` |
| `[ezjscServer_expimage] Class` | `expImageServices` | `extension/expservices/settings/ezjscore.ini.append.php:75` |
| `[ezjscServer_expfile] Class` | `expFileServices` | `extension/expservices/settings/ezjscore.ini.append.php:78` |
| `[ezjscServer_expmedia] Class` | `expMediaServices` | `extension/expservices/settings/ezjscore.ini.append.php:81` |
| `[ezjscServer_exptags] Class` | `expTagsServices` | `extension/expservices/settings/ezjscore.ini.append.php:84` |
| `[ezjscServer_explayout] Class` | `expLayoutServices` | `extension/expservices/settings/ezjscore.ini.append.php:87` |
| `[ezjscServer_expaudit] Class` | `expAuditServices` | `extension/expservices/settings/ezjscore.ini.append.php:90` |
| `[ezjscServer_expsubitems_svc] Class` | `expSubitemsServices` | `extension/expservices/settings/ezjscore.ini.append.php:93` |
| `[ezjscServer_expnewsletter] Class` | `expNewsletterServices` | `extension/expservices/settings/ezjscore.ini.append.php:96` |
| `[ezjscServer_expsitemap] Class` | `expSitemapServices` | `extension/expservices/settings/ezjscore.ini.append.php:99` |
| `[ezjscServer_expstats] Class` | `expStatsServices` | `extension/expservices/settings/ezjscore.ini.append.php:102` |
| `[ezjscServer_expdesign] Class` | `expDesignServices` | `extension/expservices/settings/ezjscore.ini.append.php:105` |
| `[ezjscServer_explanguage] Class` | `expLanguageServices` | `extension/expservices/settings/ezjscore.ini.append.php:108` |
| `[ezjscServer_expurl] Class` | `expUrlServices` | `extension/expservices/settings/ezjscore.ini.append.php:111` |
| `[ezjscServer_exppdf] Class` | `expPdfServices` | `extension/expservices/settings/ezjscore.ini.append.php:114` |
| `[ezjscServer_expproduct] Class` | `expProductServices` | `extension/expservices/settings/ezjscore.ini.append.php:118` |
| `[ezjscServer_expbasket] Class` | `expBasketServices` | `extension/expservices/settings/ezjscore.ini.append.php:121` |
| `[ezjscServer_exporder] Class` | `expOrderServices` | `extension/expservices/settings/ezjscore.ini.append.php:124` |
| `[ezjscServer_expvat] Class` | `expVatServices` | `extension/expservices/settings/ezjscore.ini.append.php:127` |
| `[ezjscServer_expcurrency] Class` | `expCurrencyServices` | `extension/expservices/settings/ezjscore.ini.append.php:130` |
| `[ezjscServer_expdiscount] Class` | `expDiscountServices` | `extension/expservices/settings/ezjscore.ini.append.php:133` |
| `[ezjscServer_expwishlist] Class` | `expWishlistServices` | `extension/expservices/settings/ezjscore.ini.append.php:136` |
| `[ezjscServer_expshipping] Class` | `expShippingServices` | `extension/expservices/settings/ezjscore.ini.append.php:139` |
| `[ezjscServer_exppayment] Class` | `expPaymentServices` | `extension/expservices/settings/ezjscore.ini.append.php:142` |
| `[ezjscServer_expinfocollection] Class` | `expInfoCollectionServices` | `extension/expservices/settings/ezjscore.ini.append.php:145` |
| `[ezjscServer_exppoll] Class` | `expPollServices` | `extension/expservices/settings/ezjscore.ini.append.php:148` |
| `[ezjscServer_expforum] Class` | `expForumServices` | `extension/expservices/settings/ezjscore.ini.append.php:151` |
| `[ezjscServer_exptopic] Class` | `expTopicServices` | `extension/expservices/settings/ezjscore.ini.append.php:154` |
| `[ezjscServer_expreply] Class` | `expReplyServices` | `extension/expservices/settings/ezjscore.ini.append.php:157` |
| `[ezjscServer_expcomment] Class` | `expCommentServices` | `extension/expservices/settings/ezjscore.ini.append.php:160` |
| `[ezjscServer_expfeed] Class` | `expFeedServices` | `extension/expservices/settings/ezjscore.ini.append.php:163` |
| `[ezjscServer_expnode] Class` | `expNodeServices` | `extension/expservices/settings/ezjscore.ini.append.php:167` |
| `[ezjscServer_expobject] Class` | `expObjectServices` | `extension/expservices/settings/ezjscore.ini.append.php:170` |
| `[ezjscServer_expclass] Class` | `expClassServices` | `extension/expservices/settings/ezjscore.ini.append.php:173` |
| `[ezjscServer_expclassgroup] Class` | `expClassGroupServices` | `extension/expservices/settings/ezjscore.ini.append.php:176` |
| `[ezjscServer_expattribute] Class` | `expAttributeServices` | `extension/expservices/settings/ezjscore.ini.append.php:179` |
| `[ezjscServer_expversion] Class` | `expVersionServices` | `extension/expservices/settings/ezjscore.ini.append.php:182` |
| `[ezjscServer_exptranslation] Class` | `expTranslationServices` | `extension/expservices/settings/ezjscore.ini.append.php:185` |
| `[ezjscServer_exprelation] Class` | `expRelationServices` | `extension/expservices/settings/ezjscore.ini.append.php:188` |
| `[ezjscServer_explocation] Class` | `expLocationServices` | `extension/expservices/settings/ezjscore.ini.append.php:191` |
| `[ezjscServer_exptrash] Class` | `expTrashServices` | `extension/expservices/settings/ezjscore.ini.append.php:194` |
| `[ezjscServer_expsection] Class` | `expSectionServices` | `extension/expservices/settings/ezjscore.ini.append.php:197` |
| `[ezjscServer_expstate] Class` | `expStateServices` | `extension/expservices/settings/ezjscore.ini.append.php:200` |
| `[ezjscServer_expurlalias] Class` | `expUrlAliasServices` | `extension/expservices/settings/ezjscore.ini.append.php:203` |
| `[ezjscServer_expsearch] Class` | `expSearchServices` | `extension/expservices/settings/ezjscore.ini.append.php:206` |
| `[ezjscServer_expcontentjob] Class` | `expContentJobServices` | `extension/expservices/settings/ezjscore.ini.append.php:209` |
| `[ezjscServer_expeditor] Class` | `expEditorServices` | `extension/expservices/settings/ezjscore.ini.append.php:212` |

### extension/expui (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_exp] Class` | `expUIServerFunctions` | `extension/expui/settings/ezjscore.ini.append.php:5` |

### extension/exp_oe_tiptap (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_expoetiptap] Class` | `expOETiptapServerFunctions` | `extension/exp_oe_tiptap/settings/ezjscore.ini.append.php:7` |
| `[EditorSettings] Engines[tiptap]` | `expOETiptapEngine` | `extension/exp_oe_tiptap/settings/ezoe.ini.append.php:8` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `expOETiptapXMLInput` | `extension/exp_oe_tiptap/settings/ezxml.ini.append.php:7` |

### extension/ezautosave (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_ezautosave] Class` | `ezjscServerFunctionsAutosave` | `extension/ezautosave/settings/ezjscore.ini.append.php:4` |

### extension/ezfind (8)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SolrFieldMapSettings] CustomMap[dummy_example]` | `ezfSolrDocumentFieldDummyExample` | `extension/ezfind/settings/ezfind.ini:118` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelation]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:119` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelationlist]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:120` |
| `[SolrFieldMapSettings] CustomMap[ezxmltext]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:121` |
| `[SolrFieldMapSettings] CustomMap[ezmatrix]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:122` |
| `[ExtendedAttributeFilters] FiltersList[geodist]` | `eZFindGeoDistExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:337` |
| `[ExtendedAttributeFilters] FiltersList[ezfindallwords]` | `eZFindAllWordsExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:341` |
| `[ezjscServer_ezfind] Class` | `eZFindServerCallFunctions` | `extension/ezfind/settings/ezjscore.ini.append.php:4` |

### extension/ezflow (6)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[Dynamic3Items] FetchClass` | `eZFlowLatestObjects` | `extension/ezflow/settings/block.ini:151` |
| `[Keyword] FetchClass` | `eZFlowKeywordsFetch` | `extension/ezflow/settings/block.ini:246` |
| `[MultimediaCarousel] FetchClass` | `eZFlowMCFetch` | `extension/ezflow/settings/block.ini:275` |
| `[LatestContent] FetchClass` | `eZFlowLatestContent` | `extension/ezflow/settings/block.ini:302` |
| `[ezjscServer_ezflow] Class` | `eZFlowServerCallFunctions` | `extension/ezflow/settings/ezjscore.ini.append.php:4` |
| `[HTTPCacheHandlers] Handlers[]` | `eZSquidCacheManager` | `extension/ezflow/settings/http.ini:5` |

### extension/ezgmaplocation (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezgmlLocationFilter] ClassName` | `ezgmlLocationFilter` | `extension/ezgmaplocation/settings/extendedattributefilter.ini.append.php:5` |

### extension/ezjscore (11)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/ezjscore/settings/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/ezjscore/settings/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/ezjscore/settings/ezjscore.ini:116` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/ezjscore/settings/ezjscore.ini:122` |
| `[ezjscServer_expsubitems] Class` | `expSubitemsServerFunctions` | `extension/ezjscore/settings/ezjscore.ini:149` |
| `[ezjscServer_expdebugbar] Class` | `expDebugBarServerFunctions` | `extension/ezjscore/settings/ezjscore.ini:167` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/ezjscore/settings/ezjscore.ini:178` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:181` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:190` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:191` |
| `[Cache_ezjscore] class` | `ezjscCacheManager` | `extension/ezjscore/settings/site.ini.append.php:41` |

### extension/ezodf (14)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[JobType_ezodf.install] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:5` |
| `[JobType_ezodf.server] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:12` |
| `[JobType_ezodf.test] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:19` |
| `[JobType_ezodf.import] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:26` |
| `[JobType_ezodf.export] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:33` |
| `[RESTSettings] HandlerList[]` | `eZRESTODFHandler` | `extension/ezodf/settings/ezrest.ini.append.php:4` |
| `[Service_transfer.ezodf] Class` | `expKernelTransferTool` | `extension/ezodf/settings/kernelservices.ini.append.php:9` |
| `[Source_documents] Class` | `expOdfImportDocumentSource` | `extension/ezodf/settings/sqliimport.ini.append.php:16` |
| `[Source_document_urls] Class` | `expOdfImportDocumentUrlsSource` | `extension/ezodf/settings/sqliimport.ini.append.php:19` |
| `[Source_document_feed] Class` | `expOdfImportDocumentFeedSource` | `extension/ezodf/settings/sqliimport.ini.append.php:22` |
| `[Source_webdav] Class` | `expOdfImportWebdavSource` | `extension/ezodf/settings/sqliimport.ini.append.php:25` |
| `[Source_google_drive] Class` | `expOdfImportGoogleDriveSource` | `extension/ezodf/settings/sqliimport.ini.append.php:28` |
| `[Source_onedrive] Class` | `expOdfImportOneDriveSource` | `extension/ezodf/settings/sqliimport.ini.append.php:31` |
| `[Source_mailbox_documents] Class` | `expOdfImportMailboxDocumentsSource` | `extension/ezodf/settings/sqliimport.ini.append.php:34` |

### extension/ezoe (7)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[ezoe]` | `expOEAuditBranch` | `extension/ezoe/settings/audit.ini.append.php:6` |
| `[ezjscServer_ezoe] Class` | `ezoeServerFunctions` | `extension/ezoe/settings/ezjscore.ini.append.php:6` |
| `[EditorSettings] Engines[tinymce3]` | `expOETinyMCE3Engine` | `extension/ezoe/settings/ezoe.ini:30` |
| `[EditorSettings] Engines[tinymce8]` | `expOETinyMCE8Engine` | `extension/ezoe/settings/ezoe.ini:31` |
| `[SpellChecker] config[general.engine]` | `GoogleSpell` | `extension/ezoe/settings/ezoe.ini:336` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `eZOEXMLInput` | `extension/ezoe/settings/ezxml.ini.append.php:11` |
| `[Registry_ezoeengines] Entries[Engines]` | `expOEEditorEngine` | `extension/ezoe/settings/rad.ini.append.php:17` |

### extension/ezoracle (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SchemaSettings] SchemaHandlerClasses[oracle]` | `eZOracleSchema` | `extension/ezoracle/settings/dbschema.ini.append.php:4` |
| `[DatabaseSettings] ImplementationAlias[oracle]` | `eZOracleDB` | `extension/ezoracle/settings/site.ini.append.php:3` |
| `[DatabaseSettings] ImplementationAlias[ezoracle]` | `eZOracleDB` | `extension/ezoracle/settings/site.ini.append.php:4` |

### extension/ezownerchange (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[ezownerchange]` | `expOwnerChangeAuditBranch` | `extension/ezownerchange/settings/audit.ini.append.php:6` |

### extension/ezprestapi (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:8` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:9` |

### extension/ezprestapiprovider (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:7` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:8` |

### extension/ezstarrating (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezsrRatingFilter] ClassName` | `ezsrRatingFilter` | `extension/ezstarrating/settings/extendedattributefilter.ini.append.php:6` |
| `[ezjscServer_ezstarrating] Class` | `ezsrServerFunctions` | `extension/ezstarrating/settings/ezjscore.ini.append.php:11` |

### extension/eztags (7)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[TagsAttributeFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:4` |
| `[TagsAttributeAndFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:8` |
| `[TagsAttributeAndMultipleFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:12` |
| `[TagsTreeAttributeFilter] ClassName` | `eZTagsTreeAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:16` |
| `[SolrFieldMapSettings] CustomMap[eztags]` | `ezfSolrDocumentFieldeZTags` | `extension/eztags/settings/ezfind.ini.append.php:5` |
| `[ezjscServer_ezjsctags] Class` | `ezjscTags` | `extension/eztags/settings/ezjscore.ini.append.php:8` |
| `[ezjscServer_ezjsctagschildren] Class` | `ezjscTagsChildren` | `extension/eztags/settings/ezjscore.ini.append.php:11` |

### extension/ezurlmultisite (4)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[DataTypeSettings] HandlerList[ezurl]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:79` |
| `[DataTypeSettings] HandlerList[ezstring]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:80` |
| `[DataTypeSettings] HandlerList[ezbinaryfile]` | `eZURLMultisiteBinaryFileHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:81` |
| `[DataTypeSettings] HandlerList[ezobjectrelation]` | `eZURLMultisiteObjectRelationHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:82` |

### extension/ezwt (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_ezwt] Class` | `ezwtServerCallFunctions` | `extension/ezwt/settings/ezjscore.ini.append.php:4` |

### extension/git_manager (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[git_manager]` | `gitManagerAuditBranch` | `extension/git_manager/settings/audit.ini.append.php:5` |

### extension/sevenx_authentication_2fa (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SignInSecondFactorSettings] Handlers[]` | `sevenxAuthentication2faSignIn` | `extension/sevenx_authentication_2fa/settings/site.ini.append.php:23` |

### extension/sevenx_themes_media (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[BlockDefinition_product_highlight] Handler` | `expLayoutsListBlockHandler` | `extension/sevenx_themes_media/settings/explayouts.ini.append.php:16` |
| `[OutputSettings] HandlerClass` | `sevenxThemesMediaXHTMLXMLOutput` | `extension/sevenx_themes_media/settings/ezxml.ini.append.php:4` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini.append.php:15` |

### extension/sevenx_themes_simple (16)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:88` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:94` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:112` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:115` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:119` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:120` |
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:88` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:94` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:112` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:115` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:119` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:120` |

### extension/sqliimport (135)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[sqliimport]` | `expImportAuditBranch` | `extension/sqliimport/settings/audit.ini.append.php:6` |
| `[JobType_sqliimport.run] Handler` | `expImportJobHandler` | `extension/sqliimport/settings/expqueue.ini.append.php:6` |
| `[JobType_sqliimport.chunk] Handler` | `expImportJobHandler` | `extension/sqliimport/settings/expqueue.ini.append.php:13` |
| `[JobType_sqliimport.finish] Handler` | `expImportJobHandler` | `extension/sqliimport/settings/expqueue.ini.append.php:20` |
| `[ezjscServer_sqliimport] Class` | `expImportServerFunctions` | `extension/sqliimport/settings/ezjscore.ini.append.php:10` |
| `[Service_sqliimport.registry] Class` | `expImportRegistry` | `extension/sqliimport/settings/kernelservices.ini.append.php:9` |
| `[Service_transfer.sqliimport] Class` | `expKernelTransferTool` | `extension/sqliimport/settings/kernelservices.ini.append.php:15` |
| `[Service_sqliimport.source.csv] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:108` |
| `[Service_sqliimport.source.csv] Arguments[]` | `expImportCsvSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:110` |
| `[Service_sqliimport.source.tsv] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:116` |
| `[Service_sqliimport.source.tsv] Arguments[]` | `expImportCsvSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:118` |
| `[Service_sqliimport.source.spreadsheet] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:125` |
| `[Service_sqliimport.source.spreadsheet] Arguments[]` | `expImportSpreadsheetSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:127` |
| `[Service_sqliimport.source.json] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:133` |
| `[Service_sqliimport.source.json] Arguments[]` | `expImportJsonSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:135` |
| `[Service_sqliimport.source.ndjson] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:141` |
| `[Service_sqliimport.source.ndjson] Arguments[]` | `expImportJsonSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:143` |
| `[Service_sqliimport.source.avro] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:150` |
| `[Service_sqliimport.source.avro] Arguments[]` | `expImportAvroSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:152` |
| `[Service_sqliimport.source.arrow] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:158` |
| `[Service_sqliimport.source.arrow] Arguments[]` | `expImportArrowSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:160` |
| `[Service_sqliimport.source.elasticsearch] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:166` |
| `[Service_sqliimport.source.elasticsearch] Arguments[]` | `expImportSearchSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:168` |
| `[Service_sqliimport.source.duckdb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:174` |
| `[Service_sqliimport.source.duckdb] Arguments[]` | `expImportDuckDbSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:176` |
| `[Service_sqliimport.source.google_sheets] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:182` |
| `[Service_sqliimport.source.google_sheets] Arguments[]` | `expImportGoogleSheetsSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:184` |
| `[Service_sqliimport.source.yaml] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:190` |
| `[Service_sqliimport.source.yaml] Arguments[]` | `expImportYamlSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:192` |
| `[Service_sqliimport.source.toml] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:198` |
| `[Service_sqliimport.source.toml] Arguments[]` | `expImportYamlSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:200` |
| `[Service_sqliimport.source.xml] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:207` |
| `[Service_sqliimport.source.xml] Arguments[]` | `expImportXmlSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:209` |
| `[Service_sqliimport.source.rss] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:216` |
| `[Service_sqliimport.source.rss] Arguments[]` | `expImportRssSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:218` |
| `[Service_sqliimport.source.opml] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:224` |
| `[Service_sqliimport.source.opml] Arguments[]` | `expImportOpmlSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:226` |
| `[Service_sqliimport.source.wxr] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:232` |
| `[Service_sqliimport.source.wxr] Arguments[]` | `expImportWxrSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:234` |
| `[Service_sqliimport.source.markdown] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:240` |
| `[Service_sqliimport.source.markdown] Arguments[]` | `expImportMarkdownSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:242` |
| `[Service_sqliimport.source.podcast] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:248` |
| `[Service_sqliimport.source.podcast] Arguments[]` | `expImportRssSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:250` |
| `[Service_sqliimport.source.video_feed] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:257` |
| `[Service_sqliimport.source.video_feed] Arguments[]` | `expImportRssSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:259` |
| `[Service_sqliimport.source.imap] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:266` |
| `[Service_sqliimport.source.imap] Arguments[]` | `expImportImapSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:268` |
| `[Service_sqliimport.source.xrowextract] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:275` |
| `[Service_sqliimport.source.xrowextract] Arguments[]` | `expImportXrowextractSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:277` |
| `[Service_sqliimport.source.sql] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:283` |
| `[Service_sqliimport.source.sql] Arguments[]` | `expImportSqlSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:285` |
| `[Service_sqliimport.source.mongodb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:291` |
| `[Service_sqliimport.source.mongodb] Arguments[]` | `expImportMongoDbSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:293` |
| `[Service_sqliimport.source.redis] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:299` |
| `[Service_sqliimport.source.redis] Arguments[]` | `expImportRedisSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:301` |
| `[Service_sqliimport.source.couchdb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:307` |
| `[Service_sqliimport.source.couchdb] Arguments[]` | `expImportCouchDbSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:309` |
| `[Service_sqliimport.source.couchbase] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:315` |
| `[Service_sqliimport.source.couchbase] Arguments[]` | `expImportCouchbaseSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:317` |
| `[Service_sqliimport.source.cosmosdb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:323` |
| `[Service_sqliimport.source.cosmosdb] Arguments[]` | `expImportCosmosDbSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:325` |
| `[Service_sqliimport.source.dynamodb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:331` |
| `[Service_sqliimport.source.dynamodb] Arguments[]` | `expImportDynamoDbSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:333` |
| `[Service_sqliimport.source.firestore] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:339` |
| `[Service_sqliimport.source.firestore] Arguments[]` | `expImportFirestoreSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:341` |
| `[Service_sqliimport.source.bigquery] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:347` |
| `[Service_sqliimport.source.bigquery] Arguments[]` | `expImportBigQuerySource` | `extension/sqliimport/settings/kernelservices.ini.append.php:349` |
| `[Service_sqliimport.source.firebase_rtdb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:355` |
| `[Service_sqliimport.source.firebase_rtdb] Arguments[]` | `expImportFirebaseSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:357` |
| `[Service_sqliimport.source.cassandra] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:363` |
| `[Service_sqliimport.source.cassandra] Arguments[]` | `expImportCassandraSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:365` |
| `[Service_sqliimport.source.neo4j] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:371` |
| `[Service_sqliimport.source.neo4j] Arguments[]` | `expImportNeo4jSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:373` |
| `[Service_sqliimport.source.arangodb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:379` |
| `[Service_sqliimport.source.arangodb] Arguments[]` | `expImportArangoDbSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:381` |
| `[Service_sqliimport.source.clickhouse] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:387` |
| `[Service_sqliimport.source.clickhouse] Arguments[]` | `expImportClickHouseSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:389` |
| `[Service_sqliimport.source.snowflake] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:395` |
| `[Service_sqliimport.source.snowflake] Arguments[]` | `expImportSnowflakeSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:397` |
| `[Service_sqliimport.source.influxdb] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:403` |
| `[Service_sqliimport.source.influxdb] Arguments[]` | `expImportInfluxDbSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:405` |
| `[Service_sqliimport.source.ldap] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:411` |
| `[Service_sqliimport.source.ldap] Arguments[]` | `expImportLdapSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:413` |
| `[Service_sqliimport.source.cie] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:419` |
| `[Service_sqliimport.source.cie] Arguments[]` | `expImportCieSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:421` |
| `[Service_sqliimport.source.exponential_rss] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:427` |
| `[Service_sqliimport.source.exponential_rss] Arguments[]` | `expImportRssSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:429` |
| `[Service_sqliimport.source.sitemap] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:436` |
| `[Service_sqliimport.source.sitemap] Arguments[]` | `expImportSitemapSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:438` |
| `[Service_sqliimport.source.webpage] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:444` |
| `[Service_sqliimport.source.webpage] Arguments[]` | `expImportWebPageSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:446` |
| `[Service_sqliimport.source.exponential_site] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:452` |
| `[Service_sqliimport.source.exponential_site] Arguments[]` | `expImportExponentialSiteSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:454` |
| `[Service_sqliimport.source.ezpkg] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:460` |
| `[Service_sqliimport.source.ezpkg] Arguments[]` | `expImportEzpkgSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:462` |
| `[Service_sqliimport.source.drupal] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:468` |
| `[Service_sqliimport.source.drupal] Arguments[]` | `expImportDrupalSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:470` |
| `[Service_sqliimport.source.joomla] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:476` |
| `[Service_sqliimport.source.joomla] Arguments[]` | `expImportJoomlaSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:478` |
| `[Service_sqliimport.source.airtable] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:484` |
| `[Service_sqliimport.source.airtable] Arguments[]` | `expImportAirtableSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:486` |
| `[Service_sqliimport.source.notion] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:492` |
| `[Service_sqliimport.source.notion] Arguments[]` | `expImportNotionSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:494` |
| `[Service_sqliimport.source.supabase] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:500` |
| `[Service_sqliimport.source.supabase] Arguments[]` | `expImportSupabaseSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:502` |
| `[Service_sqliimport.source.baserow] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:508` |
| `[Service_sqliimport.source.baserow] Arguments[]` | `expImportBaserowSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:510` |
| `[Service_sqliimport.source.salesforce] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:516` |
| `[Service_sqliimport.source.salesforce] Arguments[]` | `expImportSalesforceSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:518` |
| `[Service_sqliimport.source.hubspot] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:524` |
| `[Service_sqliimport.source.hubspot] Arguments[]` | `expImportHubSpotSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:526` |
| `[Service_sqliimport.source.youtube] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:532` |
| `[Service_sqliimport.source.youtube] Arguments[]` | `expImportYouTubeSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:534` |
| `[Service_sqliimport.source.youtube_page] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:540` |
| `[Service_sqliimport.source.youtube_page] Arguments[]` | `expImportYouTubePageSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:542` |
| `[Service_sqliimport.source.rest_api] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:548` |
| `[Service_sqliimport.source.rest_api] Arguments[]` | `expImportRestApiSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:550` |
| `[Service_sqliimport.source.media] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:556` |
| `[Service_sqliimport.source.media] Arguments[]` | `expImportMediaSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:558` |
| `[Service_sqliimport.source.ical] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:563` |
| `[Service_sqliimport.source.ical] Arguments[]` | `expImportIcalSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:565` |
| `[Service_sqliimport.source.vcard] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:571` |
| `[Service_sqliimport.source.vcard] Arguments[]` | `expImportVcardSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:573` |
| `[Service_sqliimport.source.google_merchant] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:580` |
| `[Service_sqliimport.source.google_merchant] Arguments[]` | `expImportGoogleMerchantSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:582` |
| `[Service_sqliimport.source.shopify] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:588` |
| `[Service_sqliimport.source.shopify] Arguments[]` | `expImportShopifyCsvSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:590` |
| `[Service_sqliimport.source.woocommerce] Class` | `expImportSourceEntry` | `extension/sqliimport/settings/kernelservices.ini.append.php:596` |
| `[Service_sqliimport.source.woocommerce] Arguments[]` | `expImportWooCommerceCsvSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:598` |
| `[Service_datatable.field_source.sqliimport] Class` | `expImportDataTableFieldSource` | `extension/sqliimport/settings/kernelservices.ini.append.php:606` |
| `[Service_sqliimport.handler.datatable] Class` | `expImportDataTableHandler` | `extension/sqliimport/settings/kernelservices.ini.append.php:614` |
| `[ContentSettings] DiffHandlerMap[ezimage]` | `SQLIImageDiffHandler` | `extension/sqliimport/settings/sqliimport.ini:60` |
| `[ContentSettings] DiffHandlerMap[ezbinaryfile]` | `SQLIBinaryFileDiffHandler` | `extension/sqliimport/settings/sqliimport.ini:61` |
| `[ContentSettings] DefaultDiffHandler` | `SQLIDefaultDiffHandler` | `extension/sqliimport/settings/sqliimport.ini:66` |
| `[rssimporthandler-HandlerSettings] ClassName` | `SQLIRSSImportHandler` | `extension/sqliimport/settings/sqliimport.ini.append.php:12` |

### extension/syndication (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[Service_transfer.syndication] Class` | `expKernelTransferTool` | `extension/syndication/settings/kernelservices.ini.append.php:10` |

### extension/xrowextract (35)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezstring] HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/settings/csv.ini.append.php:67` |
| `[ezenhancedselection] HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/settings/csv.ini.append.php:71` |
| `[ezinteger] HandlerClass` | `XroweZIntegerHandler` | `extension/xrowextract/settings/csv.ini.append.php:75` |
| `[ezxmltext] HandlerClass` | `XroweZXMLTextHandler` | `extension/xrowextract/settings/csv.ini.append.php:79` |
| `[ezidentifier] HandlerClass` | `XroweZIdentifierHandler` | `extension/xrowextract/settings/csv.ini.append.php:83` |
| `[ezfloat] HandlerClass` | `XroweZFloatHandler` | `extension/xrowextract/settings/csv.ini.append.php:87` |
| `[ezemail] HandlerClass` | `XroweZEmailHandler` | `extension/xrowextract/settings/csv.ini.append.php:91` |
| `[ezurl] HandlerClass` | `XroweZURLHandler` | `extension/xrowextract/settings/csv.ini.append.php:95` |
| `[eztext] HandlerClass` | `XroweZTextHandler` | `extension/xrowextract/settings/csv.ini.append.php:99` |
| `[ezenhancedobjectrelation] HandlerClass` | `XroweZenhancedobjectrelationHandler` | `extension/xrowextract/settings/csv.ini.append.php:103` |
| `[ezselection] HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/settings/csv.ini.append.php:109` |
| `[ezenum] HandlerClass` | `XroweZEnumHandler` | `extension/xrowextract/settings/csv.ini.append.php:113` |
| `[ezuser] HandlerClass` | `XroweZUserHandler` | `extension/xrowextract/settings/csv.ini.append.php:117` |
| `[ezdate] HandlerClass` | `XroweZDateHandler` | `extension/xrowextract/settings/csv.ini.append.php:121` |
| `[ezboolean] HandlerClass` | `XroweZBooleanHandler` | `extension/xrowextract/settings/csv.ini.append.php:125` |
| `[ezcountry] HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/settings/csv.ini.append.php:129` |
| `[ezimage] HandlerClass` | `XroweZImageExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:133` |
| `[ezmedia] HandlerClass` | `XroweZMediaExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:137` |
| `[ezbinaryfile] HandlerClass` | `XroweZBinaryfileExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:141` |
| `[ezmatrix] HandlerClass` | `XroweZMatrixExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:145` |
| `[ezobjectrelationlist] HandlerClass` | `XroweZObjectRelationListHandler` | `extension/xrowextract/settings/csv.ini.append.php:149` |
| `[hmregexpline] HandlerClass` | `XrowhmregexplineHandler` | `extension/xrowextract/settings/csv.ini.append.php:153` |
| `[ezprice] HandlerClass` | `XroweZPriceHandler` | `extension/xrowextract/settings/csv.ini.append.php:157` |
| `[ezdatetime] HandlerClass` | `XroweZDateTimeHandler` | `extension/xrowextract/settings/csv.ini.append.php:161` |
| `[eztime] HandlerClass` | `XroweZTimeHandler` | `extension/xrowextract/settings/csv.ini.append.php:165` |
| `[ezkeyword] HandlerClass` | `XroweZKeywordHandler` | `extension/xrowextract/settings/csv.ini.append.php:169` |
| `[eztags] HandlerClass` | `XroweZKeywordHandler` | `extension/xrowextract/settings/csv.ini.append.php:173` |
| `[ezobjectrelation] HandlerClass` | `XroweZObjectRelationHandler` | `extension/xrowextract/settings/csv.ini.append.php:177` |
| `[xrowmetadata] HandlerClass` | `XrowxrowmetadataHandler` | `extension/xrowextract/settings/csv.ini.append.php:181` |
| `[JobType_xrowextract.run] Handler` | `expKernelTransferQueueHandler` | `extension/xrowextract/settings/expqueue.ini.append.php:13` |
| `[XrowExtractTranslation] ClassName` | `XrowExtractTranslationFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:8` |
| `[XrowExtractHasChildren] ClassName` | `XrowExtractHasChildrenFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:15` |
| `[XrowExtractRelation] ClassName` | `XrowExtractRelationFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:23` |
| `[XrowExtractUserStatus] ClassName` | `XrowExtractUserStatusFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:30` |
| `[Service_transfer.xrowextract] Class` | `expKernelTransferTool` | `extension/xrowextract/settings/kernelservices.ini.append.php:10` |

### extension/xrowmetadata (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SolrFieldMapSettings] CustomMap[xrowmetadata]` | `ezfSolrDocumentFieldxrowMetadata` | `extension/xrowmetadata/settings/ezfind.ini.append.php:4` |

### settings (254)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditSinkSettings] SinkClasses[syslog]` | `expAuditSyslogSink` | `settings/audit.ini:298` |
| `[AuditSinkSettings] SinkClasses[webhook]` | `expAuditWebhookSink` | `settings/audit.ini:299` |
| `[AuditSinkSettings] SinkClasses[mail]` | `expAuditMailSink` | `settings/audit.ini:300` |
| `[AuditAlertSettings] RuleClasses[threshold]` | `expAuditThresholdRule` | `settings/audit.ini:377` |
| `[AuditAlertSettings] RuleClasses[match]` | `expAuditMatchRule` | `settings/audit.ini:378` |
| `[AuditAlertSettings] RuleClasses[schedule]` | `expAuditScheduleRule` | `settings/audit.ini:379` |
| `[AuditArchiveSettings] FormatHandlers[gzip]` | `expAuditGzipFormat` | `settings/audit.ini:523` |
| `[AuditArchiveSettings] FormatHandlers[bzip2]` | `expAuditBzip2Format` | `settings/audit.ini:524` |
| `[AuditArchiveSettings] FormatHandlers[xz]` | `expAuditXzFormat` | `settings/audit.ini:525` |
| `[AuditArchiveSettings] FormatHandlers[zstd]` | `expAuditZstdFormat` | `settings/audit.ini:526` |
| `[AuditArchiveSettings] FormatHandlers[zip]` | `expAuditZipFormat` | `settings/audit.ini:527` |
| `[ContentJobSettings] JobTypes[remove]` | `expContentJobRemoveSubtree` | `settings/content.ini:547` |
| `[ContentJobSettings] JobTypes[copy]` | `expContentJobCopySubtree` | `settings/content.ini:548` |
| `[ContentJobSettings] JobTypes[move]` | `expContentJobMoveSubtree` | `settings/content.ini:549` |
| `[ContentJobSettings] JobTypes[hide]` | `expContentJobHideSubtree` | `settings/content.ini:550` |
| `[ContentJobSettings] JobTypes[reveal]` | `expContentJobRevealSubtree` | `settings/content.ini:551` |
| `[ContentJobSettings] JobTypes[section]` | `expContentJobSectionSubtree` | `settings/content.ini:552` |
| `[ContentJobSettings] JobTypes[state]` | `expContentJobStateSubtree` | `settings/content.ini:553` |
| `[ContentJobSettings] JobTypes[addlocation]` | `expContentJobAddLocation` | `settings/content.ini:554` |
| `[ContentJobSettings] JobTypes[removelocation]` | `expContentJobRemoveLocation` | `settings/content.ini:555` |
| `[ContentJobSettings] JobTypes[packageinstall]` | `expContentJobPackageInstall` | `settings/content.ini:557` |
| `[ContentJobSettings] JobTypes[auditexport]` | `expContentJobAuditExport` | `settings/content.ini:559` |
| `[ContentJobSettings] JobTypes[helpmanuals]` | `expContentJobHelpManuals` | `settings/content.ini:561` |
| `[ContentJobSettings] JobTypes[transfer]` | `expContentJobTransfer` | `settings/content.ini:564` |
| `[PublishingSettings] AsynchronousPublishingQueueReader` | `ezpContentPublishingQueue` | `settings/content.ini:690` |
| `[TestingSettings] MultivariateTestingHandlerClass` | `ezpMultivariateTestHandler` | `settings/content.ini:709` |
| `[MissingFileSettings] Handlers[ezimage]` | `expContentCheckFileImage` | `settings/contentcheck.ini:175` |
| `[MissingFileSettings] Handlers[ezbinaryfile]` | `expContentCheckFileTable` | `settings/contentcheck.ini:176` |
| `[MissingFileSettings] Handlers[ezmedia]` | `expContentCheckFileTable` | `settings/contentcheck.ini:177` |
| `[SchemaSettings] SchemaHandlerClasses[mysql]` | `eZMysqlSchema` | `settings/dbschema.ini:22` |
| `[SchemaSettings] SchemaHandlerClasses[mysqli]` | `eZMysqlSchema` | `settings/dbschema.ini:23` |
| `[SchemaSettings] SchemaHandlerClasses[postgresql]` | `eZPgsqlSchema` | `settings/dbschema.ini:24` |
| `[SchemaSettings] SchemaHandlerClasses[sqlite]` | `eZSQLiteSchema` | `settings/dbschema.ini:25` |
| `[SchemaSettings] SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | `settings/dbschema.ini:26` |
| `[SchemaSettings] SchemaHandlerClasses[mongo]` | `expMongoSchema` | `settings/dbschema.ini:27` |
| `[ContextHelp] Providers[]` | `expHelpContentProvider` | `settings/exphelp.ini:1429` |
| `[InputSettings] HandlerClass` | `eZSimplifiedXMLInput` | `settings/ezxml.ini:14` |
| `[OutputSettings] HandlerClass` | `eZXHTMLXMLOutput` | `settings/ezxml.ini:40` |
| `[OutputSettings] AliasClasses[ezpdf]` | `eZPDFXMLOutput` | `settings/ezxml.ini:43` |
| `[BinaryFileSettings] Handler` | `eZFilePassthroughHandler` | `settings/file.ini:13` |
| `[ClusteringSettings] FileHandler` | `eZFSFileHandler` | `settings/file.ini:57` |
| `[eZDFSClusteringSettings] DBBackend` | `eZDFSFileHandlerMySQLiBackend` | `settings/file.ini:91` |
| `[eZDFSClusteringSettings] DFSBackend` | `eZDFSFileHandlerDFSBackend` | `settings/file.ini:92` |
| `[GD] Handler` | `eZImageGDFactory` | `settings/image.ini:277` |
| `[ImageMagick] Handler` | `eZImageShellFactory` | `settings/image.ini:305` |
| `[IniCommandSettings] Actions[get]` | `expIniActionGet` | `settings/ini.ini:21` |
| `[IniCommandSettings] Actions[set]` | `expIniActionSet` | `settings/ini.ini:22` |
| `[IniCommandSettings] Actions[add]` | `expIniActionAdd` | `settings/ini.ini:23` |
| `[IniCommandSettings] Actions[rem]` | `expIniActionRem` | `settings/ini.ini:24` |
| `[IniCommandSettings] Actions[clear]` | `expIniActionClear` | `settings/ini.ini:25` |
| `[IniCommandSettings] Actions[toggle]` | `expIniActionToggle` | `settings/ini.ini:26` |
| `[IniCommandSettings] Actions[copy]` | `expIniActionCopy` | `settings/ini.ini:27` |
| `[IniCommandSettings] Actions[move]` | `expIniActionMove` | `settings/ini.ini:28` |
| `[IniCommandSettings] Actions[move-all]` | `expIniActionMoveAll` | `settings/ini.ini:29` |
| `[IniCommandSettings] Actions[where]` | `expIniActionWhere` | `settings/ini.ini:30` |
| `[IniCommandSettings] Actions[list]` | `expIniActionList` | `settings/ini.ini:31` |
| `[IniCommandSettings] Actions[scopes]` | `expIniActionScopes` | `settings/ini.ini:32` |
| `[IniCommandSettings] Actions[actions]` | `expIniActionActions` | `settings/ini.ini:33` |
| `[IniCommandSettings] ScopeProviders[]` | `expIniCoreScopeProvider` | `settings/ini.ini:50` |
| `[IniCommandSettings] ScopeProviders[]` | `expIniExtensionScopeProvider` | `settings/ini.ini:51` |
| `[Service_db] Class` | `eZDBInterface` | `settings/kernelservices.ini:76` |
| `[Service_http] Class` | `eZHTTPTool` | `settings/kernelservices.ini:85` |
| `[Service_ini.site] Class` | `eZINI` | `settings/kernelservices.ini:92` |
| `[Service_ini.i18n] Class` | `eZINI` | `settings/kernelservices.ini:102` |
| `[Service_extensions.overrides] Class` | `expKernelExtensionOverrideList` | `settings/kernelservices.ini:113` |
| `[Service_transfer.registry] Class` | `expKernelTransferRegistry` | `settings/kernelservices.ini:157` |
| `[Service_transfer.package] Class` | `expKernelTransferTool` | `settings/kernelservices.ini:164` |
| `[Service_transfer.rss_import] Class` | `expKernelTransferTool` | `settings/kernelservices.ini:193` |
| `[Service_transfer.rss_export] Class` | `expKernelTransferTool` | `settings/kernelservices.ini:214` |
| `[Service_transfer.infocollector] Class` | `expKernelTransferTool` | `settings/kernelservices.ini:237` |
| `[Service_transfer.audit] Class` | `expKernelTransferTool` | `settings/kernelservices.ini:258` |
| `[Service_transfer.datatable] Class` | `expKernelTransferTool` | `settings/kernelservices.ini:280` |
| `[Service_transfer.pdf] Class` | `expKernelTransferTool` | `settings/kernelservices.ini:316` |
| `[GateSettings] EssentialSenders[]` | `expMailPreferencesService` | `settings/mailpreferences.ini:37` |
| `[Category_content] HandlerClass` | `expNotificationMailCategoryHandler` | `settings/mailpreferences.ini:91` |
| `[Category_collaboration] HandlerClass` | `expNotificationMailCategoryHandler` | `settings/mailpreferences.ini:103` |
| `[PackageSettings] HandlerAlias[ezcontentclass]` | `eZContentClassPackageHandler` | `settings/package.ini:89` |
| `[PackageSettings] HandlerAlias[ezcontentobject]` | `eZContentObjectPackageHandler` | `settings/package.ini:91` |
| `[PackageSettings] HandlerAlias[eziniaddon]` | `eZINIAddonPackageHandler` | `settings/package.ini:93` |
| `[PackageSettings] HandlerAlias[ezfile]` | `eZFilePackageHandler` | `settings/package.ini:95` |
| `[PackageSettings] HandlerAlias[ezini]` | `eZFilePackageHandler` | `settings/package.ini:96` |
| `[PackageSettings] HandlerAlias[ezdesign]` | `eZFilePackageHandler` | `settings/package.ini:97` |
| `[PackageSettings] HandlerAlias[eztemplate]` | `eZFilePackageHandler` | `settings/package.ini:98` |
| `[PackageSettings] HandlerAlias[ezthumbnail]` | `eZFilePackageHandler` | `settings/package.ini:99` |
| `[PackageSettings] HandlerAlias[ezsql]` | `eZDBPackageHandler` | `settings/package.ini:101` |
| `[PackageSettings] HandlerAlias[ezextension]` | `eZExtensionPackageHandler` | `settings/package.ini:103` |
| `[PackageSettings] HandlerAlias[ezinstallscript]` | `eZInstallScriptPackageHandler` | `settings/package.ini:104` |
| `[CreationSettings] HandlerAlias[ezcontentclass]` | `eZContentClassPackageCreator` | `settings/package.ini:144` |
| `[CreationSettings] HandlerAlias[ezstyle]` | `eZStylePackageCreator` | `settings/package.ini:145` |
| `[CreationSettings] HandlerAlias[ezcontentobject]` | `eZContentObjectPackageCreator` | `settings/package.ini:146` |
| `[CreationSettings] HandlerAlias[ezextension]` | `eZExtensionPackageCreator` | `settings/package.ini:147` |
| `[InstallerSettings] HandlerAlias[ezcontentobject]` | `eZContentObjectPackageInstaller` | `settings/package.ini:159` |
| `[InstallerSettings] HandlerAlias[ezinstallscript]` | `eZInstallScriptPackageInstaller` | `settings/package.ini:160` |
| `[Registry_auditsources] Entries[Sources]` | `expAuditSource` | `settings/rad.ini:77` |
| `[System] PrefixFilterClass` | `ezpRestDefaultRegexpPrefixFilter` | `settings/rest.ini:13` |
| `[ApiProvider] ProviderClass[auth]` | `ezpRestAuthProvider` | `settings/rest.ini:18` |
| `[ApiProvider] ProviderClass[help]` | `expHelpRestProvider` | `settings/rest.ini:21` |
| `[OutputSettings] RendererClass[xhtml]` | `ezpContentXHTMLRenderer` | `settings/rest.ini:27` |
| `[Authentication] AuthenticationStyle` | `ezpRestOauthAuthenticationStyle` | `settings/rest.ini:76` |
| `[RouteSettings] RouteSettingImpl` | `ezpRestIniRouteFilter` | `settings/rest.ini:216` |
| `[MathSettings] MathHandler` | `eZPHPMath` | `settings/shop.ini:19` |
| `[Cache_restRoutes] class` | `ezpRestRoutesCacheClear` | `settings/site.ini:59` |
| `[Cache_restRoutes] purgeClass` | `ezpRestRoutesCacheClear` | `settings/site.ini:60` |
| `[Cache_exphelpcontent] class` | `expHelpContentRepository` | `settings/site.ini:70` |
| `[Cache_exphelpcontent] purgeClass` | `expHelpContentRepository` | `settings/site.ini:71` |
| `[DatabaseSettings] ImplementationAlias[mysql]` | `eZMySQLiDB` | `settings/site.ini:134` |
| `[DatabaseSettings] ImplementationAlias[mysqli]` | `eZMySQLiDB` | `settings/site.ini:135` |
| `[DatabaseSettings] ImplementationAlias[postgresql]` | `eZPostgreSQLDB` | `settings/site.ini:136` |
| `[DatabaseSettings] ImplementationAlias[ezmysql]` | `eZMySQLiDB` | `settings/site.ini:137` |
| `[DatabaseSettings] ImplementationAlias[ezmysqli]` | `eZMySQLiDB` | `settings/site.ini:138` |
| `[DatabaseSettings] ImplementationAlias[ezpostgresql]` | `eZPostgreSQLDB` | `settings/site.ini:139` |
| `[DatabaseSettings] ImplementationAlias[pgsql]` | `eZPostgreSQLDB` | `settings/site.ini:140` |
| `[DatabaseSettings] ImplementationAlias[sqlite3]` | `eZSQLite3DB` | `settings/site.ini:141` |
| `[DatabaseSettings] ImplementationAlias[mongodb]` | `expMongoDB` | `settings/site.ini:142` |
| `[URLTranslator] FilterClasses[]` | `eZURLAliasFilterAppendNodeID` | `settings/site.ini:593` |
| `[SearchSettings] SearchEngine` | `eZSearchEngine` | `settings/site.ini:795` |
| `[ReauthSettings] CodeHandlers[]` | `expReauthTotpCode` | `settings/site.ini:1185` |
| `[SiteAccessSettings] MobileDeviceFilterClass` | `ezpMobileDeviceRegexpFilter` | `settings/site.ini:1484` |
| `[RegionalSettings] LanguageSwitcherClass` | `ezpLanguageSwitcher` | `settings/site.ini:1774` |
| `[ContentSettings] StaticCacheHandler` | `eZStaticCache` | `settings/site.ini:2101` |
| `[ContentSettings] ContentClassEditHandler` | `eZContentClassEditHandler` | `settings/site.ini:2110` |
| `[MailSettings] TransportAlias[file]` | `eZFileTransport` | `settings/site.ini:2141` |
| `[MailSettings] TransportAlias[sendmail]` | `eZSendmailTransport` | `settings/site.ini:2142` |
| `[MailSettings] TransportAlias[smtp]` | `eZSMTPTransport` | `settings/site.ini:2143` |
| `[Column_parentnodeid] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:172` |
| `[Column_depth] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:183` |
| `[Column_pathstring] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:194` |
| `[Column_pathidentification] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:205` |
| `[Column_mainnodeid] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:216` |
| `[Column_childsortfield] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:227` |
| `[Column_childsortorder] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:236` |
| `[Column_hidden] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:245` |
| `[Column_invisible] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:254` |
| `[Column_iscontainer] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:264` |
| `[Column_childrencount] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:273` |
| `[Column_subtreecount] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:283` |
| `[Column_childclasses] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:293` |
| `[Column_newestchildname] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:302` |
| `[Column_classidentifier] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:314` |
| `[Column_classid] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:325` |
| `[Column_classgroups] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:336` |
| `[Column_sectionidentifier] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:345` |
| `[Column_sectionid] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:356` |
| `[Column_wordcount] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:367` |
| `[Column_textlength] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:377` |
| `[Column_readingtime] Class` | `expSubitemsReadingTimeColumn` | `settings/subitemscolumns.ini:387` |
| `[Column_namelength] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:397` |
| `[Column_rating] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:407` |
| `[Column_ratingcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:417` |
| `[Column_version] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:430` |
| `[Column_versioncount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:440` |
| `[Column_draftcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:450` |
| `[Column_draftauthors] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:460` |
| `[Column_latestdraft] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:469` |
| `[Column_archivedcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:478` |
| `[Column_versioncreated] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:488` |
| `[Column_parentname] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:500` |
| `[Column_ismain] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:509` |
| `[Column_locationcount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:518` |
| `[Column_otherlocations] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:528` |
| `[Column_mainlocation] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:537` |
| `[Column_urlalias] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:550` |
| `[Column_allaliases] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:561` |
| `[Column_systemurl] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:571` |
| `[Column_publicpath] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:581` |
| `[Column_publicurl] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:593` |
| `[Column_urlslug] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:606` |
| `[Column_aliascount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:616` |
| `[Column_customaliases] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:626` |
| `[Column_customaliascount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:635` |
| `[Column_historycount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:645` |
| `[Column_pagetitle] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:658` |
| `[Column_titlelength] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:673` |
| `[Column_metatitle] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:685` |
| `[Column_metadescription] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:694` |
| `[Column_metakeywords] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:703` |
| `[Column_modifiedsubnode] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:715` |
| `[Column_publishedage] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:725` |
| `[Column_modifiedage] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:735` |
| `[Column_dayssincepublished] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:745` |
| `[Column_dayssincemodified] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:756` |
| `[Column_publishediso] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:767` |
| `[Column_modifiediso] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:778` |
| `[Column_newestchild] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:789` |
| `[Column_owner] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:801` |
| `[Column_ownerid] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:810` |
| `[Column_initialcreator] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:821` |
| `[Column_contributors] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:830` |
| `[Column_relatedcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:842` |
| `[Column_relatednames] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:852` |
| `[Column_reverserelatedcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:863` |
| `[Column_reverserelatednames] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:873` |
| `[Column_tags] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:883` |
| `[Column_tagcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:892` |
| `[Column_initiallanguage] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:905` |
| `[Column_languagecount] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:914` |
| `[Column_languagecodes] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:924` |
| `[Column_missingtranslations] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:934` |
| `[Column_alwaysavailable] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:943` |
| `[Column_locked] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:963` |
| `[Column_pendingcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:972` |
| `[Column_rejectedcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:982` |
| `[Column_workflowprocesses] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:992` |
| `[Column_canedit] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1002` |
| `[Column_canremove] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1011` |
| `[Column_canmove] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1020` |
| `[Column_canhide] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1029` |
| `[Column_cantranslate] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1038` |
| `[Column_cancreate] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1047` |
| `[Column_userlogin] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1060` |
| `[Column_useremail] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1072` |
| `[Column_userenabled] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1084` |
| `[Column_userlocked] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1095` |
| `[Column_userlastvisit] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1106` |
| `[Column_userlogincount] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1117` |
| `[Column_userfailedlogins] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1129` |
| `[Column_userroles] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1141` |
| `[Column_userrolecount] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1152` |
| `[Column_hasimage] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1167` |
| `[Column_imagecount] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1176` |
| `[Column_imagedimensions] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1186` |
| `[Column_imagewidth] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1195` |
| `[Column_imageheight] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1205` |
| `[Column_imagesize] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1215` |
| `[Column_imagemime] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1225` |
| `[Column_imagealt] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1234` |
| `[Column_filename] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1243` |
| `[Column_filesize] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1253` |
| `[Column_filemime] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1263` |
| `[Column_filedownloads] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1272` |
| `[Column_searchwords] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1285` |
| `[Column_viewcount] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:1295` |
| `[Column_languagemask] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:1305` |
| `[Column_attributecount] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:1315` |
| `[Providers] ProviderClasses[]` | `expSysCardExponentialProvider` | `settings/syscard.ini:17` |
| `[Providers] ProviderClasses[]` | `expSysCardHostProvider` | `settings/syscard.ini:18` |
| `[Providers] ProviderClasses[]` | `expSysCardPhpProvider` | `settings/syscard.ini:19` |
| `[Providers] ProviderClasses[]` | `expSysCardVelocityProvider` | `settings/syscard.ini:20` |
| `[Providers] ProviderClasses[]` | `expSysCardWebServerProvider` | `settings/syscard.ini:21` |
| `[Providers] ProviderClasses[]` | `expSysCardDatabaseProvider` | `settings/syscard.ini:22` |
| `[Providers] ProviderClasses[]` | `expSysCardSiteProvider` | `settings/syscard.ini:23` |
| `[Providers] ProviderClasses[]` | `expSysCardCacheProvider` | `settings/syscard.ini:24` |
| `[Providers] ProviderClasses[]` | `expSysCardHelpProvider` | `settings/syscard.ini:25` |
| `[Providers] ProviderClasses[]` | `expSysCardCronProvider` | `settings/syscard.ini:26` |
| `[Providers] ProviderClasses[]` | `expSysCardComposerProvider` | `settings/syscard.ini:27` |
| `[Providers] ProviderClasses[]` | `expSysCardExtensionsProvider` | `settings/syscard.ini:28` |
| `[Providers] ProviderClasses[]` | `expSysCardSiteAccessProvider` | `settings/syscard.ini:29` |
| `[Providers] ProviderClasses[]` | `expSysCardSettingsProvider` | `settings/syscard.ini:30` |
| `[Providers] ProviderClasses[]` | `expSysCardQueueProvider` | `settings/syscard.ini:31` |
| `[Providers] ProviderClasses[]` | `expSysCardAboutProvider` | `settings/syscard.ini:32` |
| `[AttributeOperator] OutputFormatter[html]` | `ezpAttributeOperatorHTMLFormatter` | `settings/template.ini:111` |
| `[AttributeOperator] OutputFormatter[text]` | `ezpAttributeOperatorTextFormatter` | `settings/template.ini:112` |
| `[UISwitch_language] Class` | `expUISwitchLanguageProvider` | `settings/uiswitch.ini:25` |
| `[UISwitch_design] Class` | `expUISwitchDesignProvider` | `settings/uiswitch.ini:97` |
