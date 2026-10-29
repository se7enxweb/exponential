---
title: Strategie / Handler
audience: developer
category: standards
order: 324
summary: "Austauschbare Implementierungen einer Aufgabe, gewählt über eine Einstellung: Cluster-Datei-Handler, Bild-Handler, Login-Handler, Suchmaschinen, Mail-Transporte, Cache-Backends ... 382 Instanzen in Exponential."
keywords: Entwurfsmuster, Strategie / Handler, Verhalten, Musterkatalog, design pattern, strategy-handler, pattern catalogue
type: catalogue
---
# Strategie / Handler

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Austauschbare Implementierungen einer Aufgabe, gewählt über eine Einstellung: Cluster-Datei-Handler, Bild-Handler, Login-Handler, Suchmaschinen, Mail-Transporte, Cache-Backends ...

| | |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 382 |
| Erkannt an | Der Klassenname endet auf Handler, Backend, Driver, Transport, Engine oder Strategy, oder die Klasse implementiert eine Schnittstelle, deren Name das tut. |
| Erweiterungspunkt | Die INI-Einstellung, die den Handler benennt (siehe die Seite der INI-Plugin-Punkte). |

## Klassen nach Bereich

### extension/bccie (24)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `BaseHandler` | class | `extension/bccie/classes/basehandler.php:11` | – | – |
| `bccieExportFormatOutputHandler` | class | `extension/bccie/classes/bccieExportFormatOutputHandler.php:11` | – | – |
| `eZBirthdayExportHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayexporthandler.php:13` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBirthdayHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayhandler.php:11` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBooleanHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbooleanhandler.php:11` | – | extension/bccie/settings/export.ini [ezboolean] HandlerClass |
| `eZCountryHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezcountrtyhandler.php:11` | – | extension/bccie/settings/export.ini [ezcountry] HandlerClass |
| `eZDateHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatehandler.php:11` | – | extension/bccie/settings/export.ini [ezdate] HandlerClass |
| `eZDateTimeHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatetimehandler.php:11` | – | extension/bccie/settings/export.ini [ezdatetime] HandlerClass |
| `eZEmailHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezemailhandler.php:11` | – | extension/bccie/settings/export.ini [ezemail] HandlerClass |
| `ezenhancedobjectrelationHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezenhancedobjectrelationhandler.php:11` | – | extension/bccie/settings/export.ini [ezenhancedobjectrelation] HandlerClass |
| `eZExampleCustomHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezexamplecustomhandler.php:11` | – | – |
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

### extension/cjw_newsletter (13)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterApprovalCollaborationHandler` | class extends eZCollaborationItemHandler | `extension/cjw_newsletter/collaboration/cjwnewsletterapproval/cjwnewsletterapprovalcollaborationhandler.php:23` | Die Freigabe einer Newsletter-Ausgabe im Kollaborations-Posteingang. | – |
| `CjwNewsletterGateTransport` | class extends eZMailTransport | `extension/cjw_newsletter/classes/cjwnewslettergatetransport.php:14` | Der Transport, dem das Mail-Gate der E-Mail-Präferenzen eine Newsletter-Mail übergibt: er sendet die umhüllte ezcMail mit dem eigenen Transport des Newsletters (S… | – |
| `CjwNewsletterHandler` | class extends eZNotificationEventHandler | `extension/cjw_newsletter/notification/handler/cjwnewsletter/cjwnewsletterhandler.php:21` | Die Newsletter-Karte der Seiten mit den Benachrichtigungseinstellungen (notification/settings, cjw_newsletter 4.2.0): die Newsletter des angemeldeten Benutzers, ihre La… | – |
| `CjwNewsletterMailCategoryHandler` | class | `extension/cjw_newsletter/classes/cjwnewslettermailcategoryhandler.php:20` | Der Handler der Kategorie „newsletter“ der E-Mail-Präferenzen (Exponential 6.0.15 und später; settings/mailpreferences.ini.append.php dieser Ext… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter] HandlerClass |
| `CjwNewsletterSmsCategoryHandler` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscategoryhandler.php:25` | Der Handler der Mail-Präferenzkategorie „sms“ des Kernels ([Category_sms] HandlerClass in settings/mailpreferences.ini.append.php der Extension). | extension/cjw_newsletter/settings/mailpreferences.ini [Category_sms] HandlerClass |
| `CjwNewsletterSmsTransport` | interface | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransport.php:25` | Ein Weg, SMS zu senden und die SMS zu lesen, die ein Anbieter zurückgibt (der anbieterneutrale Teil des SMS-Kanals). | – |
| `CjwNewsletterSmsTransportBase` | abstract class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportbase.php:29` | Was die mitgelieferten SMS-Transporte gemeinsam haben: die Einstellungen mit ihrer Voreinstellung, die generische Prüfung einer eingehenden Anfrage (ein HMAC des Inhalts mit einem gemeinsamen Gehe… | – |
| `CjwNewsletterSmsTransportFile` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportfile.php:23` | Das simulierte SMS-Senden: nichts verlässt den Server, jede SMS wird als eine JSON-Datei -.sms.json (to, from, text, segments, time) in Dir geschrieben. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_file] Class |
| `CjwNewsletterSmsTransportHttp` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporthttp.php:38` | Der generische SMS-Transport: eine HTTP-Anfrage pro SMS an eine beliebige Anbieter-API, abgebildet in der INI-Gruppe. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_http] Class |
| `CjwNewsletterSmsTransportTwilio` | class extends CjwNewsletterSmsTransportHttp | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporttwilio.php:29` | Eine Voreinstellung des generischen HTTP-Transports für APIs im Stil der Messages-Ressource von Twilio: ein Formular-POST an .../Accounts/{AccountSid}/Messages.json mi… | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_twilio] Class |
| `CjwNewsletterStatisticsCategoryHandler` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticscategoryhandler.php:23` | Der Handler der E-Mail-Präferenzkategorie „newsletter_statistics“ (cjw_newsletter 4.2.0, Bereich N4 Statistik): die Einwilligung in personenbezogene Zählung… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter_statistics] HandlerClass |
| `CjwNewsletterTransport` | class | `extension/cjw_newsletter/classes/cjwnewslettertransport.php:17` | Ausgabe erzeugen und mailen | – |
| `CjwNewsletterTransportFile` | class | `extension/cjw_newsletter/classes/cjwnewslettertransportfile.php:17` | Speichern einer Mail in eine Datei | – |

### extension/cjw_themes_jumper (1)

[Alle Muster von extension/cjw_themes_jumper](bereich-extension-cjw-themes-jumper.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwJumperImagineImageHandler` | class extends eZImageHandler | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagehandler.php:28` | Bildkonverter für die cjw_*-Aliase des Design-Starters: zeichnet sie genau so, wie der Nexus-v2-Starter seine eigenen Aliase zeichnet (Imagine 1.x mit… | – |

### extension/cjw_tmv (1)

[Alle Muster von extension/cjw_tmv](bereich-extension-cjw-tmv.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwTmvEventsBlockHandler` | class | `extension/cjw_tmv/classes/cjwtmveventsblockhandler.php:11` | Der Layouts-Block „tmv_events“ (v2: Cjw\TmvBundle TmvEventsHandler): die Veranstaltungen eines tmv_container, mit der Filterleiste (Ansichtstyp Liste), nur d… | extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_tmv_events] Handler |

### extension/expai (18)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAiAltTextJobHandler` | class extends expQueueHandlerBase | `extension/expai/classes/alttext/expaialttextjobhandler.php:19` | Der Jobtyp expai.alttext (expAiAltText::queue(), der Bericht „Missing alt text“): fragt ein Vision-Modell nach dem Alternativtext eines Bildes und schreibt ihn in… | extension/expai/settings/expqueue.ini [JobType_expai.alttext] Handler |
| `expAiAnthropicProvider` | class extends expAiProviderBase | `extension/expai/classes/expaianthropicprovider.php:16` | Die Anthropic Messages API (POST {Endpoint}, Standard https://api.anthropic.com/v1/messages) mit Tool-Nutzung, adaptivem Denken auf der konfigurierten Stufe… | – |
| `expAiBatchPollHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaibatchpollhandler.php:24` | Der Jobtyp expai.batch.poll (Klasse system): fragt den Anbieter alle PollSeconds nach dem Zustand eines Batches (dazwischen zurückgestellt, ohne einen… | extension/expai/settings/expqueue.ini [JobType_expai.batch.poll] Handler |
| `expAiBatchSubmitHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaibatchsubmithandler.php:20` | Der Jobtyp expai.batch.submit (Klasse system): sammelt die wartenden expai.request-Jobs eines Modells und einer Batch-Lane, höchstens MaxRequestsPerBatch, und reicht… | extension/expai/settings/expqueue.ini [JobType_expai.batch.submit] Handler |
| `expAiChainEngine` | class | `extension/expai/classes/chain/expaichainengine.php:24` | Führt KI-Ketten (doc/chains.md) über die Job-Warteschlange aus. | – |
| `expAiExchangeRatesHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaiexchangerateshandler.php:15` | Der Jobtyp expai.exchange.rates (Klasse system, kein Modellaufruf, #392): holt einmal am Tag die Referenzkurse der Europäischen Zentralbank (expAiExch… | extension/expai/settings/expqueue.ini [JobType_expai.exchange.rates] Handler |
| `expAiModelTestHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaimodeltesthandler.php:16` | Der Jobtyp expai.model.test: das „Test prompt“ (Aktion test) und „Load model“ (Aktion load) von expai/models, vom Worker als der Benutzer ausgeführt, der geklickt hat… | extension/expai/settings/expqueue.ini [JobType_expai.model.test] Handler |
| `expAiOllamaNativeProvider` | class | `extension/expai/classes/expaiollamanativeprovider.php:20` | Ein Modell eines lokalen Ollama-Servers (ein Anbieter mit Managed=ollama), das über Ollamas eigene API auf der Loopback-Adresse statt über dessen OpenAI-Ko… | – |
| `expAiOpenAiChatProvider` | class extends expAiProviderBase | `extension/expai/classes/expaiopenaichatprovider.php:36` | Die OpenAI Chat Completions API und jeder Server, der sie spricht ([Provider_] Type=openai_chat): OpenAI, Hugging Face Inference Providers (Router), Op… | – |
| `expAiPromptedToolProvider` | class | `extension/expai/classes/expaipromptedtoolprovider.php:21` | Werkzeugnutzung für Modelle ohne native Tool-Aufrufe ([Model_] ToolUse=prompted): Die Werkzeuge werden in den Anweisungen beschrieben („Tool-Protokoll“) und das Mo… | – |
| `expAiProvider` | interface | `extension/expai/classes/expaiprovider.php:16` | Ein Modell-Backend von expai. | extension/expai/settings/rad.ini [Registry_expai_provider_types] Implements |
| `expAiProviderBase` | abstract class | `extension/expai/classes/expaiproviderbase.php:16` | Was jeder eingebaute Anbieter gemeinsam hat: seine Einstellungen (die [Provider_]-Werte, mit den [Model_]-Werten zusammengeführt, siehe expAiProviderRegistry::settingsFor()),… | – |
| `expAiRequestHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expairequesthandler.php:28` | Der Jobtyp expai.request: ein Aufruf an ein Modell auf einer Lane (expAiQueue::request() reiht ihn ein). | extension/expai/settings/expqueue.ini [JobType_expai.request] Handler |
| `expAiThreadItemHandler` | class extends expQueueHandlerBase | `extension/expai/classes/vector/expaithreaditemhandler.php:22` | Der Jobtyp expai.thread.item: ein Element eines genehmigten Plans als Kind-Lauf des Threads (doc/parallel-runs.md), neben den anderen Elementen. | extension/expai/settings/expqueue.ini [JobType_expai.thread.item] Handler |
| `expAiThreadTurnHandler` | class extends expQueueHandlerBase | `extension/expai/classes/vector/expaithreadturnhandler.php:23` | Der Jobtyp expai.thread.turn: ein Zug eines Threads (die Nachricht des Redakteurs und jeder Schritt, bis das Modell in Worten antwortet), vom Warteschlangen-Worker ausgeführt… | extension/expai/settings/expqueue.ini [JobType_expai.thread.turn] Handler |
| `expAiTranslationCheckHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaitranslationcheckhandler.php:15` | Der Jobtyp expai.translation.check (Klasse system, kein Modellaufruf): entscheidet, nachdem eine Version in der Ausgangssprache veröffentlicht wurde, für jede Übersetzung des… | extension/expai/settings/expqueue.ini [JobType_expai.translation.check] Handler |
| `expAiUsageRecorder` | class | `extension/expai/classes/expaiusagerecorder.php:16` | Ein Anbieter, der jede Anfrage des Anbieters, den er umhüllt, in expai_usage_log aufzeichnet (expAiUsage) und vor jeder Anfrage [Limits] (expAiLimits) durchsetzt… | – |
| `expAiWebSearchProvider` | abstract class | `extension/expai/classes/web/expaiwebsearchprovider.php:16` | Ein Websuchdienst für web_search. | extension/expai/settings/rad.ini [Registry_expai_search_providers] Entries[Class] |

### extension/explayouts (50)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsAbstractContentBlockHandler` | abstract class | `extension/explayouts/classes/explayoutsabstractcontentblockhandler.php:3` | – | – |
| `expLayoutsAccordionBlockHandler` | class | `extension/explayouts/classes/explayoutsaccordionblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_accordion] Handler |
| `expLayoutsAdminLeftMenuBlockHandler` | class | `extension/explayouts/classes/explayoutsadminleftmenublockhandler.php:7` | Der linke Menüblock der Admin-Layouts. | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_left_menu] Handler |
| `expLayoutsAdminPartBlockHandler` | class | `extension/explayouts/classes/explayoutsadminpartblockhandler.php:7` | Der Handler der Admin-Blöcke, die keinen Parameter nehmen (Design admin4l): der Block zeigt einen Teil der Admin-Seite, gezeichnet von seinem Ansichts-Template desig… | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_bookmarks] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_breadcrumb] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_clear_cache] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_content_tree] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_current_user] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_debug_area] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_footer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_logo] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_menu_button] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_module_result] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_overlay] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_popup_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_preferences] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_quick_settings] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_search] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_controls] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_toggles] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_tab_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_theme_switch] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_developer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_right] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_ui_switch] Handler |
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
| `expLayoutsDividerBlockHandler` | class | `extension/explayouts/classes/explayoutsdividerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_divider] Handler |
| `expLayoutsExpContentProvider` | class | `extension/explayouts/classes/explayoutsexpcontentprovider.php:4` | – | – |
| `expLayoutsExponentialContentSearchQueryHandler` | class | `extension/explayouts/classes/explayoutsexponentialcontentsearchqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exponential_content_search] Handler |
| `expLayoutsExpSiteApiContentProvider` | class | `extension/explayouts/classes/explayoutsexpsiteapicontentprovider.php:3` | – | – |
| `expLayoutsFeaturesBlockHandler` | class extends expLayoutsComponentBlockHandler | `extension/explayouts/classes/explayoutsfeaturesblockhandler.php:8` | Der Features-Block: die Felder der anderen Komponentenblöcke (Titel, Text, Bild, Link) und eine Liste von Features, eines je Zeile als „Title\|Text“, die… | extension/explayouts/settings/explayouts.ini [BlockDefinition_features] Handler |
| `expLayoutsFullViewBlockHandler` | class | `extension/explayouts/classes/explayoutsfullviewblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_full_view] Handler |
| `expLayoutsGalleryBlockHandler` | class | `extension/explayouts/classes/explayoutsgalleryblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_grid_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_logos] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_slider] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_sushi_bar] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_thumb_gallery] Handler |
| `expLayoutsGridBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutsgridblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_grid] Handler |
| `expLayoutsHtmlBlockHandler` | class | `extension/explayouts/classes/explayoutshtmlblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_html] Handler |
| `expLayoutsImageBlockHandler` | class | `extension/explayouts/classes/explayoutsimageblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_image] Handler |
| `expLayoutsLanguageResolver` | class | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Sprachauflöser für Exponential 4, kompatibel mit dem LanguageResolver-Muster der Exp Site API. | – |
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
| `expLayoutsResolver` | class | `extension/explayouts/classes/explayoutsresolver.php:2` | – | – |
| `expLayoutsReverseRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_reverse_relation_list] Handler |
| `expLayoutsReverseRelationProvider` | class | `extension/explayouts/classes/explayoutsreverserelationprovider.php:15` | Die Layouts-Blöcke, die Inhalte nennen, die ein Entfernen oder Verbergen wegnimmt, für die Prüfung umgekehrter Relationen des Kernels (content.ini [ReverseRelationSettings] P… | extension/explayouts/settings/content.ini [ReverseRelationSettings] ProviderClassList[] |
| `expLayoutsRichTextBlockHandler` | class | `extension/explayouts/classes/explayoutsrichtextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_rich_text] Handler |
| `expLayoutsSiblingsQueryHandler` | class | `extension/explayouts/classes/explayoutssiblingsqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_siblings] Handler |
| `expLayoutsSingleBlockHandler` | class | `extension/explayouts/classes/explayoutssingleblockhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_single] Handler |
| `expLayoutsSiteBundleContextProvider` | class | `extension/explayouts/classes/explayoutssitebundlecontextprovider.php:4` | – | – |
| `expLayoutsSpacerBlockHandler` | class | `extension/explayouts/classes/explayoutsspacerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_spacer] Handler |
| `expLayoutsSubtreeQueryHandler` | class | `extension/explayouts/classes/explayoutssubtreequeryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_subtree] Handler |
| `expLayoutsTabsBlockHandler` | class | `extension/explayouts/classes/explayoutstabsblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tabs] Handler |
| `expLayoutsTagsQueryHandler` | class | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_tags] Handler |
| `expLayoutsTextBlockHandler` | class | `extension/explayouts/classes/explayoutstextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_text] Handler |
| `expLayoutsTitleBlockHandler` | class | `extension/explayouts/classes/explayoutstitleblockhandler.php:2` | – | extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_ezcontent_field] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_title] Handler |
| `expLayoutsTplBlockHandler` | class | `extension/explayouts/classes/explayoutstplblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tpl_block] Handler |
| `expLayoutsVideoBlockHandler` | class | `extension/explayouts/classes/explayoutsvideoblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_video] Handler |

### extension/explayouts_api (4)

[Alle Muster von extension/explayouts_api](bereich-extension-explayouts-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsEzContentContextProvider` | class | `extension/explayouts_api/classes/explayoutsezcontentcontextprovider.php:2` | – | – |
| `expLayoutsEzContentProvider` | class | `extension/explayouts_api/classes/explayoutsezcontentprovider.php:2` | – | – |
| `expLayoutsEzRemoteIdConverter` | class | `extension/explayouts_api/classes/explayoutsezremoteidconverter.php:2` | – | – |
| `expLayoutsEzValueConverter` | class | `extension/explayouts_api/classes/explayoutsezvalueconverter.php:2` | – | – |

### extension/explayouts_content_browser (1)

[Alle Muster von extension/explayouts_content_browser](bereich-extension-explayouts-content-browser.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowserProvider` | class | `extension/explayouts_content_browser/classes/explayoutscontentbrowserprovider.php:3` | – | – |

### extension/explayouts_content_browser_core (1)

[Alle Muster von extension/explayouts_content_browser_core](bereich-extension-explayouts-content-browser-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsContentBrowserCoreBackend` | class | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercorebackend.php:2` | – | – |

### extension/explayouts_site_api (1)

[Alle Muster von extension/explayouts_site_api](bereich-extension-explayouts-site-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsSiteApiValueConverter` | class | `extension/explayouts_site_api/classes/explayoutssiteapivalueconverter.php:2` | – | – |

### extension/explayouts_standard (22)

[Alle Muster von extension/explayouts_standard](bereich-extension-explayouts-standard.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsStandardAccordionBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardaccordionblockhandler.php:3` | – | – |
| `expLayoutsStandardAlertBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardalertblockhandler.php:3` | – | – |
| `expLayoutsStandardBadgeBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardbadgeblockhandler.php:3` | – | – |
| `expLayoutsStandardButtonBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardbuttonblockhandler.php:3` | – | – |
| `expLayoutsStandardCardBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardcardblockhandler.php:3` | – | – |
| `expLayoutsStandardCarouselBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardcarouselblockhandler.php:3` | – | – |
| `expLayoutsStandardDividerBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandarddividerblockhandler.php:3` | – | – |
| `expLayoutsStandardGalleryBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardgalleryblockhandler.php:3` | – | – |
| `expLayoutsStandardGridBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardgridblockhandler.php:3` | – | – |
| `expLayoutsStandardHtmlSnippetBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardhtmlsnippetblockhandler.php:2` | – | – |
| `expLayoutsStandardImageBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardimageblockhandler.php:3` | – | – |
| `expLayoutsStandardListBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardlistblockhandler.php:3` | – | – |
| `expLayoutsStandardMapBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardmapblockhandler.php:3` | – | – |
| `expLayoutsStandardMarkdownBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardmarkdownblockhandler.php:2` | – | – |
| `expLayoutsStandardProgressBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardprogressblockhandler.php:3` | – | – |
| `expLayoutsStandardQuoteBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardquoteblockhandler.php:3` | – | – |
| `expLayoutsStandardSingleBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardsingleblockhandler.php:3` | – | – |
| `expLayoutsStandardSpacerBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardspacerblockhandler.php:3` | – | – |
| `expLayoutsStandardTabsBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardtabsblockhandler.php:3` | – | – |
| `expLayoutsStandardTextBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardtextblockhandler.php:3` | – | – |
| `expLayoutsStandardTitleBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardtitleblockhandler.php:3` | – | – |
| `expLayoutsStandardVideoBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardvideoblockhandler.php:3` | – | – |

### extension/expqueue (5)

[Alle Muster von extension/expqueue](area-extension-expqueue.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expQueueFailHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuefailhandler.php:16` | expqueue.fail: schlägt absichtlich fehl, um Wiederholungen, Backoff, die Dead Letter Queue und die Anpassung des Ratenlimits auszuprobieren. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.fail] Handler |
| `expQueueHandler` | interface | `extension/expqueue/classes/expqueuehandler.php:27` | Ein Job-Handler: der Code, der die Jobs eines Typs ausführt. | extension/expqueue/settings/rad.ini [Registry_expqueue_job_types] Entries[Handler] |
| `expQueueHandlerBase` | abstract class | `extension/expqueue/classes/expqueuehandlerbase.php:12` | Eine Basis für Handler mit den optionalen Teilen: validate() wird aufgerufen, wenn ein Job in die Warteschlange gestellt wird (eine falsche Nutzlast wird dort sofort abgelehnt, nicht nachts um 3 Uhr. | – |
| `expQueueNoopHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuenoophandler.php:12` | expqueue.noop: tut nichts und ist erledigt. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.noop] Handler |
| `expQueueSleepHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuesleephandler.php:16` | expqueue.sleep: wartet in Schritten, meldet den Fortschritt und beachtet zwischen den Schritten einen Abbruch. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.sleep] Handler |

### extension/exp_oe_tiptap (5)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOETiptapAIAnthropicProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaianthropicprovider.php:15` | Die Anthropic Messages API: POST {Endpoint} (Standard https://api.anthropic.com/v1/messages) mit den Headern x-api-key und anthropic-version und { mo… | – |
| `expOETiptapAIExpaiProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexpaiprovider.php:32` | Die KI-Befehle über die Extension expai (Exponential Intelligence), [AISettings] Provider=expai: die Anfrage geht an das Modell, mit dem expai eingerichtet is… | – |
| `expOETiptapAIOpenAIProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiopenaiprovider.php:16` | Jeder Anbieter, der die OpenAI-Chat-Completions-API spricht: POST {Endpoint} mit { model, messages: [ system, user ], max_tokens, temperature }, Antwort ch… | – |
| `expOETiptapAIProvider` | interface | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprovider.php:16` | Ein Textgenerierungs-Backend der KI-Hooks. | – |
| `expOETiptapEngine` | class extends expOEEditorEngineBase | `extension/exp_oe_tiptap/classes/expoetiptapengine.php:16` | Tiptap als Engine der Editor-Registry von ezoe: registriert durch settings/ezoe.ini.append.php (ezoe.ini [EditorSettings] Engines[tiptap]=expOETiptapEngin… | extension/exp_oe_tiptap/settings/ezoe.ini [EditorSettings] Engines[tiptap] |

### extension/ezauthorize (1)

[Alle Muster von extension/ezauthorize](bereich-extension-ezauthorize.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCustomShopAccountHandler` | class | `extension/ezauthorize/shopaccounthandlers/ezcustomshopaccounthandler.php:5` | – | – |

### extension/ezfind (3)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfIndexParentName` | class | `extension/ezfind/classes/indexplugins/ezfindexparentname.php:16` | Diese Klasse fügt den Namen des Elternknotens des Hauptknotens als eigenes Feld hinzu. | – |
| `ezfIndexPlugin` | interface | `extension/ezfind/classes/indexplugins/ezfindexplugin.php:18` | Beschreibung von ezfIndexPlugin. | – |
| `eZSolr` | class | `extension/ezfind/search/plugins/ezsolr/ezsolr.php:11` | Solr-Such-Plugin für Exponential | – |

### extension/ezie (3)

[Alle Muster von extension/ezie](bereich-extension-ezie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZIEEzcGDHandler` | class extends ezcImageGdHandler | `extension/ezie/classes/handlers/gd.php:11` | – | – |
| `eZIEezcImageConverter` | class | `extension/ezie/classes/image_converter.php:12` | – | – |
| `eZIEEzcImageMagickHandler` | class extends ezcImageImagemagickHandler | `extension/ezie/classes/handlers/magick.php:13` | Implementiert die Methoden, die der Editor verwendet (drehen, spiegeln usw.), für ImageMagick. | – |

### extension/ezjscore (2)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRelationAjaxUploader` | class extends ezpRelationListAjaxUploader | `extension/ezjscore/classes/ajaxuploader/ezprelationajaxuploader.php:19` | Diese Klasse verarbeitet AJAX-Uploads für eZObjectRelation-Attribute | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation] |
| `ezpRelationListAjaxUploader` | class | `extension/ezjscore/classes/ajaxuploader/ezprelationlistajaxuploader.php:19` | Diese Klasse verarbeitet AJAX-Uploads für eZObjectRelationList-Attribute | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist] |

### extension/ezmultiupload (1)

[Alle Muster von extension/ezmultiupload](bereich-extension-ezmultiupload.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZMultiuploadHandler` | class | `extension/ezmultiupload/classes/ezmultiuploadhandler.php:16` | Klasse eZMultiuploadHandler | – |

### extension/ezodf (6)

[Alle Muster von extension/ezodf](bereich-extension-ezodf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOdfConverter` | class | `extension/ezodf/classes/converter/expodfconverter.php:29` | Konvertiert Dokumente. | – |
| `expOdfPhpConverter` | class | `extension/ezodf/classes/converter/expodfphpconverter.php:25` | Der letzte Fallback des Konverters: nur PHP, kein LibreOffice. | – |
| `expOdfQueueHandler` | class extends expQueueHandlerBase | `extension/ezodf/classes/converter/expodfqueuehandler.php:14` | Der expqueue-Handler der Jobs von ezodf (expqueue.ini [JobType_ezodf.*]); die eigentliche Arbeit steht in expOdfJobs::run(). | extension/ezodf/settings/expqueue.ini [JobType_ezodf.export] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.import] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.install] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.server] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.test] Handler |
| `eZOOConverter` | class | `extension/ezodf/classes/ezooconverter.php:41` | – | – |
| `eZOpenofficeUploadHandler` | class extends eZContentUploadHandler | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php:41` | – | – |
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | `extension/ezodf/classes/ezrestodfhandler.php:46` | – | extension/ezodf/settings/ezrest.ini [RESTSettings] HandlerList[] |

### extension/ezoe (4)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOEEditorEngine` | interface | `extension/ezoe/classes/expoeeditorengine.php:21` | Eine Editor-Engine des Online-Editors: das, was ein ezxmltext-Attribut im Browser zu einem Editor macht. | extension/ezoe/settings/rad.ini [Registry_ezoeengines] Entries[Engines] |
| `expOEEditorEngineBase` | abstract class | `extension/ezoe/classes/expoeeditorenginebase.php:14` | Bequeme Basis für Editor-Engines: liest den eigenen ezoe.ini-Block der Engine [Engine_] (ToolbarMap[]=, ExternalPlugins[]=, Scripts[], Styles[], Version,… | – |
| `expOETinyMCE3Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce3engine.php:10` | Die Standard-Engine: eZ Online Editor auf TinyMCE 3.5, gerendert von ezxmltext_ezoe.tpl selbst. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce3] |
| `expOETinyMCE8Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce8engine.php:10` | Die Opt-in-Engine: eZ Online Editor auf TinyMCE 8 (GPL), gerendert von ezxmltext_ezoe_tinymce8.tpl. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce8] |

### extension/ezoracle (2)

[Alle Muster von extension/ezoracle](area-extension-ezoracle.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDBFileHandlerOracleBackend` | class | `extension/ezoracle/clusterfilehandlers/dbbackends/oracle.php:68` | – | – |
| `eZDFSFileHandlerOracleBackend` | class | `extension/ezoracle/clusterfilehandlers/dfsbackends/oracle.php:55` | – | – |

### extension/ezpostgresqlcluster (1)

[Alle Muster von extension/ezpostgresqlcluster](bereich-extension-ezpostgresqlcluster.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandlerPostgresqlBackend` | class | `extension/ezpostgresqlcluster/clustering/dfs/ezpostsgresqlbackend.php:16` | Diese Klasse ermöglicht DFS-basiertes Clustering mit PostgreSQL | – |

### extension/ezprestapi (1)

[Alle Muster von extension/ezprestapi](bereich-extension-ezprestapi.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezp7xRestApiProvider` | class | `extension/ezprestapi/classes/rest_provider.php:25` | Die Routen des ezp-Anbieters. | extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezpl]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezpl] |

### extension/ezprestapiprovider (1)

[Alle Muster von extension/ezprestapiprovider](bereich-extension-ezprestapiprovider.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRestApiProvider` | class extends ezp7xRestApiProvider | `extension/ezprestapiprovider/classes/rest_provider.php:19` | Veraltet: der Inhaltsanbieter der REST-API v1 liegt jetzt in der Extension ezprestapi, die die Lesezugriffe unter v1 und v2 und die Schreibzugriffe unter v2 bedient, aus… | – |

### extension/ezurlmultisite (4)

[Alle Muster von extension/ezurlmultisite](bereich-extension-ezurlmultisite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLMultisiteBinaryFileHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisitebinaryfilehandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezbinaryfile] |
| `eZURLMultisiteDataTypeHandler` | interface | `extension/ezurlmultisite/classes/ezurlmultisitedatatypehandler.php:3` | – | – |
| `eZURLMultisiteObjectRelationHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteobjectrelationhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezobjectrelation] |
| `eZURLMultisiteURLHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteurlhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezstring]; extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezurl] |

### extension/sevenx_authentication_2fa (1)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxAuthentication2faSignIn` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fasignin.php:42` | – | extension/sevenx_authentication_2fa/settings/site.ini [SignInSecondFactorSettings] Handlers[] |

### extension/sevenx_dse (11)

[Alle Muster von extension/sevenx_dse](bereich-extension-sevenx-dse.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AdminNeo\ClickHouseDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:196` | – | – |
| `AdminNeo\Driver` | abstract class | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – | – |
| `AdminNeo\ElasticDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:159` | – | – |
| `AdminNeo\MongoDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:204` | – | – |
| `AdminNeo\MsSqlDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:310` | – | – |
| `AdminNeo\MySqlDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:242` | – | – |
| `AdminNeo\OracleDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:215` | – | – |
| `AdminNeo\PgSqlDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:279` | – | – |
| `AdminNeo\Plugin` | abstract class | `extension/sevenx_dse/adminneo/core/Plugin.php:13` | Plugin-Elternklasse. | – |
| `AdminNeo\SimpleDbDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:183` | – | – |
| `AdminNeo\SqliteDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:174` | – | – |

### extension/sqliimport (29)

[Alle Muster von extension/sqliimport](area-extension-sqliimport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expImportAiExpaiTransport` | class | `extension/sqliimport/classes/exp/ai/expimportaiexpaitransport.php:25` | Der KI-Assistent über die Extension expai (Exponential Intelligence), der einzige Weg, auf dem er ein Modell erreicht. | – |
| `expImportAiTransport` | interface | `extension/sqliimport/classes/exp/ai/expimportaitransport.php:16` | Die Art, wie die KI-Unterstützung von 7x SQLI Import (expImportAi) ein Modell erreicht. | – |
| `expImportBoltSocket` | class | `extension/sqliimport/classes/exp/nosql/expimportbolttransport.php:35` | Eine TCP-Verbindung, mit TLS (Zertifikat und Name geprüft) für bolt+s:// und neo4j+s://. | – |
| `expImportBoltTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportbolttransport.php:13` | Der Byte-Strom, über den der Bolt-Client (expImportBoltClient) spricht: ein Socket (expImportBoltSocket) oder ein Testdouble. | – |
| `expImportCassandraClient` | class | `extension/sqliimport/classes/exp/nosql/expimportcassandraclient.php:23` | Ein minimaler Client des nativen CQL-Protokolls, Version 4 (Cassandra 2.2 und neuer, ScyllaDB), über einen Socket (Treiber „socket“), für ein PHP ohne die Ex… | – |
| `expImportCassandraExtension` | class | `extension/sqliimport/classes/exp/nosql/expimportcassandraextension.php:15` | Die Verbindung der Cassandra/ScyllaDB-Quelle über die PHP-Extension cassandra von DataStax (Cassandra\Cluster, Cassandra\SimpleStatement). | – |
| `expImportCassandraTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportcassandratransport.php:21` | Die Verbindung, über die die Cassandra/ScyllaDB-Quelle liest: die PHP-Extension cassandra von DataStax (expImportCassandraExtension), wo sie installiert is… | – |
| `expImportDataTableHandler` | class | `extension/sqliimport/classes/exp/handlers/expimportdatatablehandler.php:23` | Das Ziel „Eine Datentabelle“: Jeder Datensatz wird zu einer Zeile einer persistenten Klasse (die eigene Tabelle eines Moduls, Exponential 6.0.15, Setup › Datentabellen), geschrieben… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.handler.datatable] Class |
| `expImportImapClient` | class | `extension/sqliimport/classes/exp/mail/expimportimapclient.php:20` | Ein kleiner IMAP4rev1-Client (RFC 3501) für PHP ohne die Extension imap: Er spricht nur, was die IMAP-Quelle braucht, von Anfang an über TLS (Port 993)… | – |
| `expImportImapExtension` | class | `extension/sqliimport/classes/exp/mail/expimportimapextension.php:14` | Der IMAP-Transport der PHP-Extension imap (imap_open). | – |
| `expImportImapTransport` | interface | `extension/sqliimport/classes/exp/mail/expimportimaptransport.php:15` | Die Verbindung zu einem IMAP-Postfach, das die IMAP-Quelle liest: die PHP-Extension imap, wo sie installiert ist (expImportImapExtension), sonst ein kleiner Client o… | – |
| `expImportJobHandler` | class extends expQueueHandlerBase | `extension/sqliimport/classes/exp/engine/expimportjobhandler.php:19` | Die Jobs eines Laufs in der Job-Warteschlange (expqueue.ini [JobType_sqliimport.*]): sqliimport.run, sqliimport.chunk und sqliimport.finish (siehe expImportRunner… | extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.chunk] Handler; extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.finish] Handler; extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.run] Handler |
| `expImportLdapExtension` | class | `extension/sqliimport/classes/exp/ldap/expimportldapextension.php:15` | Der LDAP-Transport der PHP-Extension ldap: LDAPv3, Referrals werden nicht verfolgt, das Zertifikat von ldaps:// und StartTLS werden verlangt, Suchen mit ldap_s… | – |
| `expImportLdapTransport` | interface | `extension/sqliimport/classes/exp/ldap/expimportldaptransport.php:15` | Was die LDAP-Quelle von einem Verzeichnisserver braucht: verbinden, StartTLS, binden und suchen, nichts, was schreibt. | – |
| `expImportLegacyHandlerAdapter` | class | `extension/sqliimport/classes/exp/handlers/expimportlegacyhandleradapter.php:21` | Führt einen Handler von sqliimport 1.x (ISQLIImportHandler, der SQLIImportAbstractHandler erweitert) unverändert in der 2.x-Engine aus: Der Handler wird mit seinem… | – |
| `expImportMappingHandler` | class | `extension/sqliimport/classes/exp/handlers/expimportmappinghandler.php:23` | Der No-Code-Handler „mapping“: wendet das Mapping des Imports (expImportMapper) auf jeden Datensatz an und veröffentlicht das Ergebnis mit der Content-API von 1.x… | – |
| `expImportMongoDbExtension` | class | `extension/sqliimport/classes/exp/nosql/expimportmongodbextension.php:15` | Die Verbindung der MongoDB-Quelle über die PHP-Extension mongodb (MongoDB\Driver\Manager, ohne die Composer-Bibliothek). | – |
| `expImportMongoDbTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportmongodbtransport.php:15` | Die Verbindung, über die die MongoDB-Quelle liest: die PHP-Extension mongodb (expImportMongoDbExtension) oder ein Testdouble. | – |
| `expImportRedisClient` | class | `extension/sqliimport/classes/exp/nosql/expimportredisclient.php:15` | Ein minimaler Redis-Client über einen Socket (Treiber „socket“), für ein PHP ohne die Extension redis: Befehle als RESP-Arrays aus Bulk-Strings, Antworten in RE… | – |
| `expImportRedisExtension` | class | `extension/sqliimport/classes/exp/nosql/expimportredisextension.php:15` | Die Verbindung der Redis-Quelle über die PHP-Extension redis (phpredis, Treiber „phpredis“). | – |
| `expImportRedisTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportredistransport.php:15` | Die Verbindung, über die die Redis-Quelle liest: die PHP-Extension redis (expImportRedisExtension), sonst ein kleiner eigener RESP-Client über einen Socket (… | – |
| `expImportUndoHandler` | class | `extension/sqliimport/classes/exp/handlers/expimportundohandler.php:22` | Nimmt die Änderungen eines früheren Laufs zurück (ein Rückgängig-Lauf, run_kind undo). | – |
| `ISQLIDiffHandler` | interface | `extension/sqliimport/classes/content/diffhandlers/isqlidiffhandler.php:27` | Interface für Diff-Handler. | – |
| `ISQLIImportHandler` | interface | `extension/sqliimport/classes/sourcehandlers/isqliimporthandler.php:11` | Interface für Import-Handler. Muss von allen Import-Handlern implementiert werden | – |
| `SQLIBinaryFileDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqlibinaryfilediffhandler.php:17` | Diff-Handler für ezbinaryfile-Attribute. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DiffHandlerMap[ezbinaryfile] |
| `SQLIDefaultDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqlidefaultdiffhandler.php:18` | Standard-Diff-Handler für Inhaltsfelder. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DefaultDiffHandler |
| `SQLIImageDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqliimagediffhandler.php:17` | Diff-Handler für ezimage-Attribute. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DiffHandlerMap[ezimage] |
| `SQLIImportAbstractHandler` | abstract class | `extension/sqliimport/classes/sourcehandlers/sqliimportabstracthandler.php:13` | Abstrakte Klasse für alle Import-Handler. Alle Import-Handler müssen von dieser Klasse erben | – |
| `SQLIRSSImportHandler` | class extends SQLIImportAbstractHandler | `extension/sqliimport/classes/sourcehandlers/sqlirssimporthandler.php:12` | – | extension/sqliimport/settings/sqliimport.ini [rssimporthandler-HandlerSettings] ClassName |

### extension/xrowextract (29)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowBaseHandler` | class | `extension/xrowextract/classes/xrowbasehandler.php:3` | – | – |
| `XrowExtractTransport` | abstract class | `extension/xrowextract/classes/transports/xrowextracttransport.php:10` | Ein Weg, eine Datei an ein Ziel zu liefern (oder, bei einem geplanten Import, von dort abzuholen). | – |
| `XroweZBinaryfileExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezbinaryfile] HandlerClass |
| `XroweZBooleanHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbooleanhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezboolean] HandlerClass |
| `XroweZDateHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdate] HandlerClass |
| `XroweZDateTimeHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatetimehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdatetime] HandlerClass |
| `XroweZEmailHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezemailhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezemail] HandlerClass |
| `XroweZenhancedobjectrelationHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezenhancedobjectrelation] HandlerClass |
| `XroweZEnumHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenumhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezenum] HandlerClass |
| `XroweZExampleHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezexamplehandler.php:2` | – | – |
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
| `xrowSitemapConverter` | class | `extension/xrowmetadata/classes/plugins/standardplugin.php:3` | – | – |

### kernel/classes (14)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZBinaryFileHandler` | class | `kernel/classes/ezbinaryfilehandler.php:23` | – | – |
| `eZClusterFileHandler` | class | `kernel/classes/ezclusterfilehandler.php:11` | – | – |
| `eZCollaborationItemHandler` | class | `kernel/classes/ezcollaborationitemhandler.php:18` | – | – |
| `eZCollaborationViewHandler` | class | `kernel/classes/ezcollaborationviewhandler.php:18` | – | – |
| `eZConfirmOrderHandler` | class | `kernel/classes/ezconfirmorderhandler.php:18` | – | – |
| `eZContentClassEditHandler` | class | `kernel/classes/ezcontentclassedithandler.php:15` | Handler für die Bearbeitung von Inhaltsklassen. | settings/site.ini [ContentSettings] ContentClassEditHandler |
| `eZContentObjectAssignmentHandler` | class | `kernel/classes/ezcontentobjectassignmenthandler.php:18` | – | – |
| `eZContentObjectEditHandler` | class | `kernel/classes/ezcontentobjectedithandler.php:19` | – | – |
| `eZContentUploadHandler` | class | `kernel/classes/ezcontentuploadhandler.php:28` | – | – |
| `eZNamePatternResolver` | class | `kernel/classes/eznamepatternresolver.php:40` | eZNamePatternResolver ist eine Hilfsklasse zum Auflösen von Objektnamen- und URL-Alias-Mustern. | – |
| `eZPackageCreationHandler` | class | `kernel/classes/ezpackagecreationhandler.php:21` | – | – |
| `eZPackageHandler` | class | `kernel/classes/ezpackagehandler.php:18` | – | – |
| `eZPackageInstallationHandler` | class | `kernel/classes/ezpackageinstallationhandler.php:19` | – | – |
| `eZShopAccountHandler` | class | `kernel/classes/ezshopaccounthandler.php:12` | – | – |

### kernel/classes/audit (7)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAuditBzip2Format` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditbzip2format.php:11` | Das Archivformat bzip2 (PHP-Erweiterung bz2): .jsonl.bz2. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[bzip2] |
| `expAuditFormatBase` | abstract class | `kernel/classes/audit/format/expauditformatbase.php:14` | Gemeinsamer Code der Archivformat-Handler (doc/eng-us/bc/6.0/audit.md, „Rotation, archives and retention“): Streaming-Kompression über einen PHP-Stre… | – |
| `expAuditFormatHandler` | interface | `kernel/classes/audit/expauditformathandler.php:14` | Ein Archivformat (gzip, bzip2, xz, zstd, zip), registriert in audit.ini: [AuditArchiveSettings] FormatHandlers[]= Gezählt von der RAD-Erhebung (Registry… | – |
| `expAuditGzipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditgzipformat.php:11` | Das Archivformat gzip (zlib, immer verfügbar): .jsonl.gz. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[gzip] |
| `expAuditXzFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditxzformat.php:11` | Das Archivformat xz: .jsonl.xz, über das Programm xz (keine PHP-Erweiterung ist allgemein verfügbar). | settings/audit.ini [AuditArchiveSettings] FormatHandlers[xz] |
| `expAuditZipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzipformat.php:12` | Das Archivformat zip (PHP-Erweiterung zip): .jsonl.zip, ein Archiv pro Live-Datei, das genau diese Datei enthält, sodass eine einzelne Datei wiederhergestellt werden kann. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zip] |
| `expAuditZstdFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzstdformat.php:11` | Das Archivformat zstd: .jsonl.zst, über die PHP-Erweiterung zstd, wenn sie geladen ist, sonst über das Programm zstd. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zstd] |

### kernel/classes/basketinfohandlers (1)

[Alle Muster von kernel/classes/basketinfohandlers](bereich-kernel-classes-basketinfohandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDefaultBasketInfoHandler` | class | `kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php:12` | – | – |

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

### kernel/classes/collaborationhandlers (1)

[Alle Muster von kernel/classes/collaborationhandlers](bereich-kernel-classes-collaborationhandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZApproveCollaborationHandler` | class extends eZCollaborationItemHandler | `kernel/classes/collaborationhandlers/ezapprove/ezapprovecollaborationhandler.php:25` | – | – |

### kernel/classes/confirmorderhandlers (1)

[Alle Muster von kernel/classes/confirmorderhandlers](bereich-kernel-classes-confirmorderhandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDefaultConfirmOrderHandler` | class | `kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php:18` | – | – |

### kernel/classes/contentcheck (3)

[Alle Muster von kernel/classes/contentcheck](bereich-kernel-classes-contentcheck.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentCheckFileHandler` | interface | `kernel/classes/contentcheck/expcontentcheckfilehandler.php:15` | Gibt an, für welche Speicherdateien ein dateibasiertes Feld steht (doc/eng-us/features/6.0/content-checks.md, „Missing files“). | – |
| `expContentCheckFileImage` | class | `kernel/classes/contentcheck/expcontentcheckfileimage.php:13` | Die Dateien eines Bildfeldes (ezimage): das Original und die Aliase, die sein gespeichertes XML nennt (doc/eng-us/features/6.0/content-checks.md, „Missing files“). | settings/contentcheck.ini [MissingFileSettings] Handlers[ezimage] |
| `expContentCheckFileTable` | class | `kernel/classes/contentcheck/expcontentcheckfiletable.php:16` | Die Datei eines Feldes, dessen Datentyp sie in einer Tabelle mit den Spalten von ezbinaryfile hält: contentobject_attribute_id, version, filename, original_file… | settings/contentcheck.ini [MissingFileSettings] Handlers[ezbinaryfile]; settings/contentcheck.ini [MissingFileSettings] Handlers[ezmedia] |

### kernel/classes/datatypes (5)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageAliasHandler` | class | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php:28` | – | – |
| `eZISBN10To13Converter` | class | `kernel/classes/datatypes/ezisbn/ezisbn10to13converter.php:12` | – | – |
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |
| `eZXMLInputHandler` | class | `kernel/classes/datatypes/ezxmltext/ezxmlinputhandler.php:19` | – | – |
| `eZXMLOutputHandler` | class | `kernel/classes/datatypes/ezxmltext/ezxmloutputhandler.php:20` | – | – |

### kernel/classes/help (3)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelpContentEditionProvider` | class extends expHelpContentProvider | `kernel/classes/help/content/exphelpcontenteditionprovider.php:16` | Was in eine gespeicherte Ausgabe eines Handbuchs gelangt (expHelpContentEbooks): jedes Objekt im Status Released, wer auch immer das Buch erstellt, und bei einem öffentlichen Handbuch nur was… | – |
| `expHelpContentProvider` | class | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Als Inhalt gepflegte Hilfe, ausgeliefert in das Hilfesystem (exphelp): die Themen, die der aktuelle Benutzer sehen darf, gerendert mit dem Markdown-Renderer der Hilfe… | settings/exphelp.ini [ContextHelp] Providers[] |
| `expHelpRestProvider` | class | `kernel/classes/help/content/rest/exphelprestprovider.php:21` | Die REST-Routen der Kontexthilfe (doc/eng-us/features/6.0/help-content.md, „Context help by help_id“; Auslieferungsweg 2 des Plans: „internal topics wit… | settings/rest.ini [ApiProvider] ProviderClass[help] |

### kernel/classes/ini (3)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniCoreScopeProvider` | class | `kernel/classes/ini/expinicorescopeprovider.php:16` | global (settings/override), default (settings/, die mitgelieferten Standardwerte, per Richtlinie nicht beschreibbar) und ein Bereich pro Siteaccess: jedes Verzeichnis in settin… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |
| `expIniExtensionScopeProvider` | class | `kernel/classes/ini/expiniextensionscopeprovider.php:14` | extension: für jedes Verzeichnis in extension/ (aktiv oder nicht; die Beschriftung sagt, welches) und extension::siteaccess: für jedes extension//settings/siteacces… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |
| `expIniScopeProvider` | interface | `kernel/classes/ini/expiniscopeprovider.php:20` | Eine Quelle von INI-Bereichen (Orte, an die eine INI-Datei geschrieben werden kann). | – |

### kernel/classes/mailpreferences (1)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailCategoryHandler` | interface | `kernel/classes/mailpreferences/expmailcategoryhandler.php:26` | Die optionale PHP-Klasse einer Kategorie ([Category_] HandlerClass): sie liest Daten, die vor dem Präferenzsystem existierten (Teilbaum-Benachrichtigungen, Dige… | – |

### kernel/classes/notification (7)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expNotificationMailCategoryHandler` | class | `kernel/classes/notification/expnotificationmailcategoryhandler.php:25` | Die Kategorien „content“ und „collaboration“ der E-Mail-Präferenzen lesen die eigenen Daten des Benachrichtigungssystems, sodass nichts migriert werden muss und die… | settings/mailpreferences.ini [Category_collaboration] HandlerClass; settings/mailpreferences.ini [Category_content] HandlerClass |
| `eZCollaborationNotificationHandler` | class extends eZNotificationEventHandler | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationhandler.php:18` | – | – |
| `eZGeneralDigestHandler` | class extends eZNotificationEventHandler | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigesthandler.php:17` | – | – |
| `eZMailNotificationTransport` | class extends eZNotificationTransport | `kernel/classes/notification/ezmailnotificationtransport.php:18` | – | – |
| `eZNotificationEventHandler` | class | `kernel/classes/notification/eznotificationeventhandler.php:18` | – | – |
| `eZNotificationTransport` | class | `kernel/classes/notification/eznotificationtransport.php:17` | – | – |
| `eZSubTreeHandler` | class extends eZNotificationEventHandler | `kernel/classes/notification/handler/ezsubtree/ezsubtreehandler.php:18` | – | – |

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

### kernel/classes/reauth (2)

[Alle Muster von kernel/classes/reauth](area-kernel-classes-reauth.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expReauthCodeHandler` | interface | `kernel/classes/reauth/expreauthcodehandler.php:14` | Eine Klasse, die den Code einer zweistufigen Anmeldemethode für „Identität bestätigen“ prüft (expReauth). | – |
| `expReauthTotpCode` | class | `kernel/classes/reauth/expreauthtotpcode.php:19` | „Identität bestätigen“ mit dem Code einer Authenticator-App, eingerichtet in sevenx_authentication_2fa (wenn diese Extension aktiv ist): das TOTP-Geheimnis des Kontos… | settings/site.ini [ReauthSettings] CodeHandlers[] |

### kernel/classes/shopaccounthandlers (4)

[Alle Muster von kernel/classes/shopaccounthandlers](bereich-kernel-classes-shopaccounthandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bcusauserShopAccountHandler` | class extends eZUserShopAccountHandler | `kernel/classes/shopaccounthandlers/bcusausershopaccounthandler.php:21` | Shop-Konto-Handler für das US-Adressmodell: die Adresszeile wird als statt gespeichert, und eine Telefonnummer wird erfasst und als gespeichert. | – |
| `eZDefaultShopAccountHandler` | class | `kernel/classes/shopaccounthandlers/ezdefaultshopaccounthandler.php:12` | – | – |
| `eZSimpleShopAccountHandler` | class | `kernel/classes/shopaccounthandlers/ezsimpleshopaccounthandler.php:12` | – | – |
| `eZUserShopAccountHandler` | class | `kernel/classes/shopaccounthandlers/ezusershopaccounthandler.php:12` | – | – |

### kernel/classes/syscard (18)

[Alle Muster von kernel/classes/syscard](area-kernel-classes-syscard.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSysCardAboutProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardaboutprovider.php:19` | Die Projektinformationen von exp:about: was Exponential ist, die Foundation und 7x, Velocity, die Lizenz, die Links, wie man mitwirkt, das Partnerpro… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardBaseProvider` | abstract class | `kernel/classes/syscard/expsyscardbaseprovider.php:23` | Der übliche Anbieter: die Eigenschaften setzen, collect() schreiben und fact() sowie die Helfer verwenden. | – |
| `expSysCardCacheProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcacheprovider.php:15` | Die Caches: welche aktiv sind (View-Cache, Template-Kompilierung, der statische Cache) und für exp:info die Größe des Cache-Verzeichnisses und jedes Caches in… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardComposerProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcomposerprovider.php:16` | Composer: wie viele Pakete vendor/ hat (vendor/composer/installed.json) und, wenn die Extension ezupdate aktiv ist, wie viele davon nicht über… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardCronProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcronprovider.php:14` | Die Cronjobs: wann einer zuletzt lief (das neueste Cronjob-Log oder der Lauf unter Setup › Cronjobs) und, für exp:info, jeder Teil von cronjob.ini mit seinen Skripten. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardDatabaseProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscarddatabaseprovider.php:15` | Die Datenbank: ihr Typ, die Version des Servers (wenn syscard.ini [Database] AskVersion=enabled: eine Verbindung, eine Frage) und der Host, auf dem sie liegt. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardExponentialProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardexponentialprovider.php:14` | Exponential selbst: die Version und ihre Linie und, bei einem Git-Checkout, der Branch und der Commit. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardExtensionsProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardextensionsprovider.php:14` | exp:info: jede aktive Extension mit ihrer Version, aus extension.xml oder ezinfo.php, danach aus composer.json (dieselben Quellen wie die Info-Seite). | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardHelpProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardhelpprovider.php:15` | Die Hilfe: wie viele Seiten sie ausliefert, ihre Sprachen und ob ihr Index aktuell ist (ein Index, der älter ist als die Seiten, ist ein Hinweis: ./console exp:help inde… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardHostProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardhostprovider.php:14` | Die Maschine: Betriebssystem, Kernel, Hostname, Uptime, Last, CPU, Speicher, der Datenträger der Installation (exp:info: jeder lokale Datenträger), die Shell und… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardPhpProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardphpprovider.php:17` | PHP: Version und SAPI, wie lange der Zweig Sicherheitsunterstützung hat (syscard.ini [Php] SecuritySupportEnds[]), OPcache und dessen JIT, die Extensions, die… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardProvider` | interface | `kernel/classes/syscard/expsyscardprovider.php:19` | Ein Anbieter der Systemkarte: eine Klasse pro Gruppe von Fakten (die Maschine, PHP, die Engine, die Datenbank ...). | – |
| `expSysCardQueueProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardqueueprovider.php:14` | exp:info: die Job-Warteschlange der Extension expqueue, wenn sie aktiv ist: ihr Dienst, wie viele Worker laufen und die letzte Aktion darauf (expQueueServ… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSettingsProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsettingsprovider.php:15` | exp:info: die INI-Einstellungen einer Erlaubnisliste (syscard.ini [Inventory] Settings[]=//). | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSiteAccessProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsiteaccessprovider.php:14` | exp:info: wie eine Anfrage ihren Siteaccess findet (site.ini [SiteAccessSettings] MatchOrder: uri, host, port ...) und, für jeden Siteaccess von AvailableSit… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSiteProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsiteprovider.php:14` | Die Site: die Siteaccesses und die Standard-Siteaccess, die aktiven Extensions, der Demo-Modus, die Debug-Ausgabe und der Mail-Transport (nur sein Typ: file, sendmail,… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardVelocityProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardvelocityprovider.php:17` | Exponential Velocity, der Anwendungsserver von 6.0.15: seine Version, ob er läuft, die aktiven und konfigurierten Worker, die Ports und TLS, die Umgebung… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardWebServerProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardwebserverprovider.php:15` | Die anderen Webserver auf der Maschine: Apache, nginx, Caddy, FrankenPHP und PHP-FPM mit seinen Mastern (einer je PHP-Version) und Pool-Workern (exp:info… | settings/syscard.ini [Providers] ProviderClasses[] |

### kernel/classes/transfer (1)

[Alle Muster von kernel/classes/transfer](area-kernel-classes-transfer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expKernelTransferQueueHandler` | class extends expQueueHandlerBase | `kernel/classes/transfer/expkerneltransferqueuehandler.php:37` | Der expqueue-Handler der Transfer-Läufe (expKernelTransferRunner, doc/eng-us/features/6.0/transfer-runs.md). | extension/bccie/settings/expqueue.ini [JobType_bccie.export] Handler; extension/xrowextract/settings/expqueue.ini [JobType_xrowextract.run] Handler |

### kernel/classes/uiswitch (3)

[Alle Muster von kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUISwitchDesignProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchdesignprovider.php:25` | Das Design der Administrationsoberfläche (das „UI-Chrome“): admin4l, admin4, das klassische Admin-Design, AdminUI oder eine andere installierte Administrations… | settings/uiswitch.ini [UISwitch_design] Class |
| `expUISwitchLanguageProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchlanguageprovider.php:27` | Die Anzeigesprache der Administrationsoberfläche: was site.ini [RegionalSettings] Locale des Siteaccess festlegt (die Übersetzungen der Oberfl… | settings/uiswitch.ini [UISwitch_language] Class |
| `expUISwitchProvider` | abstract class | `kernel/classes/uiswitch/expuiswitchprovider.php:18` | Der gemeinsame Teil eines UI-Umschalt-Anbieters: seine Einstellungsgruppe uiswitch.ini [UISwitch_], die Erlaubnisliste aus AllowedValues[] (jeder Wert geprüft durch die… | – |

### kernel/classes/user (1)

[Alle Muster von kernel/classes/user](bereich-kernel-classes-user.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSignInSecondFactorHandler` | interface | `kernel/classes/user/expsigninsecondfactorhandler.php:18` | Was eine Erweiterung für die zweistufige Anmeldung dem Kernel über Anmeldungen außerhalb des Web-Anmeldeformulars mitteilt (REST mit Passwort, WebDAV, SOAP, die expservices-Anmeld… | – |

### kernel/classes/vathandlers (1)

[Alle Muster von kernel/classes/vathandlers](bereich-kernel-classes-vathandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDefaultVATHandler` | class | `kernel/classes/vathandlers/ezdefaultvathandler.php:21` | – | – |

### kernel/modules (4)

[Alle Muster von kernel/modules](bereich-kernel-modules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCurrencyConverter` | class | `kernel/shop/classes/ezcurrencyconverter.php:20` | – | – |
| `eZECBHandler` | class extends eZExchangeRatesUpdateHandler | `kernel/shop/classes/exchangeratehandlers/ezecb/ezecbhandler.php:12` | – | – |
| `eZExchangeRatesUpdateHandler` | class | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php:12` | – | – |
| `eZSearchEngine` | class | `kernel/search/plugins/ezsearchengine/ezsearchengine.php:18` | – | settings/site.ini [SearchSettings] SearchEngine |

### kernel/private (13)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpContentLimitationHandler` | interface | `kernel/private/interfaces/ezpcontentlimitationhandler.php:29` | Wertet eine Richtlinieneinschränkung des Moduls content aus, die der Kernel selbst nicht kennt. | – |
| `ezpContentLimitationSolrHandler` | interface extends ezpContentLimitationHandler | `kernel/private/interfaces/ezpcontentlimitationsolrhandler.php:23` | Ein Handler für Inhaltseinschränkungen, der auch eine Suche in einer Suchmaschine einschränken kann, die selbst nach den Richtlinien des Benutzers filtert, etwa eZ Find mit… | – |
| `ezpDatabaseBasedClusterFileHandler` | interface | `kernel/private/interfaces/ezpdatabasebasedclusterfilehandler.php:20` | Diese Schnittstelle beschreibt einen datenbankbasierten Cluster-Datei-Handler | – |
| `ezpKernel` | class | `kernel/private/classes/ezpkernel.php:16` | Basisklasse des Exponential-Kernels. | – |
| `ezpKernelHandler` | interface | `kernel/private/interfaces/ezpkernelhandler.php:14` | Schnittstelle für Kernel-Handler | – |
| `ezpKernelRest` | class | `kernel/private/rest/classes/ezpkernelrest.php:11` | – | – |
| `ezpKernelTreeMenu` | class | `kernel/private/classes/ezpkerneltreemenu.php:11` | – | – |
| `ezpKernelWeb` | class | `kernel/private/classes/ezpkernelweb.php:16` | Stellt einen Kernel-Handler im Web-Kontext bereit | – |
| `ezpMultivariateTestHandler` | class | `kernel/private/classes/ezpmultivariatetesthandler.php:12` | – | settings/content.ini [TestingSettings] MultivariateTestingHandlerClass |
| `ezpRestAuthProvider` | class | `kernel/private/rest/classes/auth/auth_provider.php:11` | – | settings/rest.ini [ApiProvider] ProviderClass[auth] |
| `ezpRestProvider` | class | `kernel/private/rest/classes/rest_provider.php:12` | – | – |
| `ezpSearchEngine` | interface | `kernel/private/interfaces/ezpsearchengine.php:18` | Diese Schnittstelle dient als Grundlage für die verschiedenen Suchmaschinen-Implementierungen | – |
| `ezpWebBasedKernelHandler` | interface extends ezpKernelHandler | `kernel/private/interfaces/ezpwebbasedkernelhandler.php:14` | Schnittstelle für webbasierte Kernel-Handler | – |

### kernel/private/classes/clusterfilehandlers (3)

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandler` | class | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandler.php:27` | Verarbeitet Dateioperationen für verteilte Dateisysteme (z. B. | – |
| `eZDFSFileHandlerDFSBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php:12` | – | settings/file.ini [eZDFSClusteringSettings] DFSBackend |
| `eZDFSFileHandlerMySQLiBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php:50` | – | settings/file.ini [eZDFSClusteringSettings] DBBackend |

### kernel/private/classes/requestrules (3)

[Alle Muster von kernel/private/classes/requestrules](bereich-kernel-private-classes-requestrules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRequestFactProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:90` | Fügt dem Kontext jeder Anfrage Fakten hinzu, bevor die Regeln laufen. | – |
| `ezpRequestRuleEngine` | class | `kernel/private/classes/requestrules/ezprequestruleengine.php:32` | Die Anfrageregeln: eine geordnete Liste von Regeln, jede eine Menge von Bedingungen und eine Aktion, befragt zu jeder Modulansicht, die der Kernel gleich ausführt. | – |
| `ezpRequestRuleProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:76` | Fügt Regeln aus PHP hinzu: eine Extension, deren Regeln berechnet werden (aus einer Datenbanktabelle, aus einer anderen INI-Datei), statt in requestrules.ini geschrieben zu sein. | – |

### kernel/private/classes/webdav (1)

[Alle Muster von kernel/private/classes/webdav](bereich-kernel-private-classes-webdav.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZWebDAVContentBackend` | class extends ezcWebdavSimpleBackend | `kernel/private/classes/webdav/ezwebdavcontentbackend.php:30` | WebDAV-Backend für Exponential, basierend auf der Webdav-Komponente der eZ Components. | – |

### lib/ezdiff (4)

[Alle Muster von lib/ezdiff](bereich-lib-ezdiff.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDiffContainerObjectEngine` | class extends eZDiffEngine | `lib/ezdiff/classes/ezdiffcontainerobjectengine.php:21` | – | – |
| `eZDiffEngine` | class | `lib/ezdiff/classes/ezdiffengine.php:21` | – | – |
| `eZDiffTextEngine` | class extends eZDiffEngine | `lib/ezdiff/classes/ezdifftextengine.php:21` | – | – |
| `eZDiffXMLTextEngine` | class extends eZDiffEngine | `lib/ezdiff/classes/ezdiffxmltextengine.php:18` | – | – |

### lib/ezfile (8)

[Alle Muster von lib/ezfile](bereich-lib-ezfile.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZBZIP2Handler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezbzip2compressionhandler.php:20` | – | – |
| `eZCompressionHandler` | class extends eZFileHandler | `lib/ezfile/classes/ezcompressionhandler.php:34` | – | – |
| `eZFileHandler` | class | `lib/ezfile/classes/ezfilehandler.php:56` | – | – |
| `eZForwardCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezforwardcompressionhandler.php:20` | – | – |
| `eZGZIPCompressionHandler` | class extends eZForwardCompressionHandler | `lib/ezfile/classes/ezgzipcompressionhandler.php:22` | – | – |
| `eZGZIPShellCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezgzipshellcompressionhandler.php:22` | – | – |
| `eZGZIPZLIBCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezgzipzlibcompressionhandler.php:20` | – | – |
| `eZNoCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/eznocompressionhandler.php:18` | – | – |

### lib/ezi18n (2)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expI18nFallbackResolver` | class | `lib/ezi18n/classes/expi18nfallbackresolver.php:32` | Auf welche Übersetzungen eine Locale in welcher Reihenfolge zurückfällt: die eine verbindliche Quelle für den Übersetzer (eZTSTranslator), die Wahl der Oberflächensprache (expU… | – |
| `eZTranslatorHandler` | class | `lib/ezi18n/classes/eztranslatorhandler.php:19` | – | – |

### lib/ezimage (3)

[Alle Muster von lib/ezimage](bereich-lib-ezimage.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageGDHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimagegdhandler.php:24` | – | – |
| `eZImageHandler` | class | `lib/ezimage/classes/ezimagehandler.php:19` | – | – |
| `eZImageShellHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimageshellhandler.php:19` | – | – |

### lib/ezsession (2)

[Alle Muster von lib/ezsession](bereich-lib-ezsession.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpSessionHandler` | abstract class | `lib/ezsession/classes/ezpsessionhandler.php:27` | Abstrakte Session-Handler-Klasse zum Erweitern | – |
| `ezpSessionHandlerAdapter` | class | `lib/ezsession/classes/ezpsessionhandleradapter.php:24` | Übergibt PHP einen ezpSessionHandler als Objekt, das SessionHandlerInterface implementiert. | – |

### lib/ezutils (7)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZExpiryHandler` | class | `lib/ezutils/classes/ezexpiryhandler.php:16` | Verwaltet Ablaufschlüssel und ihre Zeitstempel | – |
| `eZFileTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezfiletransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[file] |
| `eZFunctionHandler` | class | `lib/ezutils/classes/ezfunctionhandler.php:18` | – | – |
| `eZMailTransport` | class | `lib/ezutils/classes/ezmailtransport.php:18` | – | – |
| `eZOperationHandler` | class | `lib/ezutils/classes/ezoperationhandler.php:18` | – | – |
| `eZSendmailTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsendmailtransport.php:20` | – | settings/site.ini [MailSettings] TransportAlias[sendmail] |
| `eZSMTPTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsmtptransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[smtp] |
