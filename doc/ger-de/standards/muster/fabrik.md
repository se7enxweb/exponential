---
title: Fabrik und statische Fabrikmethoden
audience: developer
category: standards
order: 321
summary: Eine Klasse oder statische Methode, die Objekte für den Aufrufer erzeugt, sodass der Aufrufer die konkrete Klasse nicht nennt. 352 Instanzen in Exponential.
keywords: Entwurfsmuster, Fabrik, Erzeugung, Musterkatalog, design pattern, factory, pattern catalogue
type: catalogue
---
# Fabrik und statische Fabrikmethoden

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Eine Klasse oder statische Methode, die Objekte für den Aufrufer erzeugt, sodass der Aufrufer die konkrete Klasse nicht nennt.

|  |  |
| --- | --- |
| Gruppe | Anlegen |
| Instanzen | 352 |
| Erkannt an | Der Klassenname endet auf Factory, oder die Klasse hat statische Methoden create*(), from*(), factory(), make*(), oder sie erzeugt new self/new static in einer statischen Methode. |
| Erweiterungspunkt | Oft die INI-Einstellung, die die zu erzeugende Klasse nennt (siehe die INI-Plugin-Punkte). |

## Klassen nach Bereich

### extension/autorss (1)

[Alle Muster von extension/autorss](bereich-extension-autorss.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AutoRSSType` | class extends eZWorkflowEventType | `extension/autorss/eventtypes/event/autorss/autorsstype.php:27` | – | – |

### extension/cjw_newsletter (37)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterAbTest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtest.php:20` | Der A/B-Betrefftest eines Versands: Stichproben, Kriterium, Wartezeit, Gewinner. | – |
| `CjwNewsletterAbTester` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtester.php:27` | Der A/B-Betrefftest eines Versands (cjw_newsletter 4.2.0, Bereich N4 Statistik). | – |
| `CjwNewsletterAbVariant` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabvariant.php:20` | Eine Betreffvariante eines A/B-Tests und ihre Zähler. | – |
| `CjwNewsletterApproval` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapproval.php:20` | Der Freigabezustand einer Ausgabeversion für eine Liste, die eine Freigabe verlangt (Kollaborations-Posteingang). | – |
| `CjwNewsletterApprovalFlow` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapprovalflow.php:23` | Die optionale Freigabe von Ausgaben vor dem Versand, pro Liste (cjwnl_list.approval_required). | – |
| `CjwNewsletterArticlePool` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterarticlepool.php:20` | Woher Artikel für Ausgaben kommen (Knoten, Klassen, Sektionen, Tags, Zustände, Alter), pro Liste oder als globaler Standard. | – |
| `CjwNewsletterBlacklistItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterblacklistitem.php:17` | Sperrliste von Newsletter-Benutzern oder E-Mail-Adressen verwalten | – |
| `CjwNewsletterClassInstaller` | class | `extension/cjw_newsletter/classes/cjwnewsletterclassinstaller.php:18` | Installiert die Newsletter-Inhaltsklassen aus den in extension/cjw_newsletter/packages mitgelieferten Paketen in eine Inhaltsklassengruppe. | – |
| `CjwNewsletterEditionArticle` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionarticle.php:20` | Ein Artikel, der von einem Redakteur oder durch das automatische Befüllen aus einem Pool in eine Ausgabe übernommen wurde. | – |
| `CjwNewsletterEditionSend` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsend.php:17` | Klassenbeschreibung hier | – |
| `CjwNewsletterEditionSendItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsenditem.php:20` | Mail-Warteschlange | – |
| `CjwNewsletterEditionSendOutput` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewslettereditionsendoutput.php:20` | Die gerenderte Ausgabe eines Versands in einer Sprache (eine Ausgabe, viele Übersetzungen). | – |
| `CjwNewsletterEznewsletterSource` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettereznewslettersource.php:21` | Nur-Lese-Zugriff auf die Tabellen einer alten eznewsletter-Installation, für ext:cjw_newsletter:import-eznewsletter. | – |
| `CjwNewsletterImport` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterimport.php:17` | Import-Sets von Abonnements verwalten | – |
| `CjwNewsletterImportMapping` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportmapping.php:20` | Eine gespeicherte Spaltenzuordnung eines CSV-Imports (Spalte -› Feld), Trennzeichen, Kopfzeile, Herkunft der Einwilligung. | – |
| `CjwNewsletterInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterest.php:20` | Ein Interesse, das Abonnenten wählen können (ein Thema einer Liste oder ein eztags-Tag). | – |
| `CjwNewsletterLink` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlink.php:20` | Ein Link eines Versands, den die Klick-Weiterleitung kennt (nur gespeicherte URLs werden weitergeleitet). | – |
| `CjwNewsletterLinkClick` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlinkclick.php:20` | Ein Klick auf einen Link; personenbezogen (Versandeintrag) nur mit Einwilligung, sonst ist der Eintrag 0. | – |
| `CjwNewsletterMailinAddress` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinaddress.php:20` | Eine Adresse (oder ein Plus-Adress-Tag) einer Liste, die An- und Abmelde-Mails annimmt. | – |
| `CjwNewsletterMailinMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinmessage.php:20` | Eine Mail, die an einer Mail-In-Adresse eingegangen ist, und was mit ihr gemacht wurde. | – |
| `CjwNewsletterMigrationLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewslettermigrationlog.php:20` | Eine Zeile, die der eznewsletter-Importer gelesen hat, und was er damit gemacht hat (oder in einem Probelauf machen würde). | – |
| `CjwNewsletterOpen` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletteropen.php:20` | Ein Öffnen eines Versands; personenbezogen nur mit Einwilligung. | – |
| `CjwNewsletterRichText` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrichtext.php:21` | Rich Text im DocBook-Format von ezrichtext (sections, para, emphasis, link, title, Listen, Tabellen, literallayout, programlisting, blockquote, ezemb… | – |
| `CjwNewsletterSchedule` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedule.php:20` | Ein wiederkehrender Versand einer Liste: eine Vorlagenausgabe kopieren oder die neueste Ausgabe senden, wann, automatisches Befüllen aus einem Pool. | – |
| `CjwNewsletterScheduleLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulelog.php:20` | Ein Lauf eines Zeitplans: gesendet, übersprungen (leer, Bedingung) oder fehlgeschlagen. | – |
| `CjwNewsletterSendBatch` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersendbatch.php:20` | Ein Stapel eines Versands: welche Einträge er übernommen hat, wie weit er gekommen ist (fortsetzbar), seine Zähler. | – |
| `CjwNewsletterSms` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersms.php:25` | Der SMS-Kanal von cjw_newsletter: Einstellungen, die Transporte, Telefonnummern (E.164), der Bestätigungscode (das Double-Opt-in einer Nummer), das STOP-Sch… | – |
| `CjwNewsletterSmsCode` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscode.php:20` | Ein per SMS gesendeter Bestätigungscode (als Hash gespeichert), mit Versuchen und Ablauf. | – |
| `CjwNewsletterSmsInbound` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsinbound.php:20` | Eine eingegangene SMS (ein STOP-Schlüsselwort, ein Code) und was mit ihr gemacht wurde. | – |
| `CjwNewsletterSmsMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsmessage.php:20` | Eine SMS einer SMS-Ausgabe an einen Newsletter-Benutzer. | – |
| `CjwNewsletterStatTotal` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstattotal.php:20` | Anonyme Summen pro Versand, Link, Typ und Tag; sie bleiben erhalten, nachdem die personenbezogenen Zeilen entfernt wurden. | – |
| `CjwNewsletterSubscription` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettersubscription.php:20` | Klassenbeschreibung hier | – |
| `CjwNewsletterSubscriptionVirtual` | class extends CjwNewsletterSubscription | `extension/cjw_newsletter/classes/cjwnewslettersubscriptionvirtual.php:21` | CjwNewsletterSubscriptionVirtual behandelt spezielle Funktionen, die nur für virtuelle Newsletter-Listen gelten | – |
| `CjwNewsletterTestGroup` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettertestgroup.php:20` | Eine benannte Gruppe von Testadressen für Testversände einer Liste (oder jeder Liste). | – |
| `CjwNewsletterThrottleState` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterthrottlestate.php:20` | Die Senderate eines Transports im aktuellen Minuten- oder Stundenfenster und eine Pause. | – |
| `CjwNewsletterUser` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletteruser.php:20` | Datenverwaltung Datentyp cjwnewsletterlist | – |
| `CjwNewsletterUserInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletteruserinterest.php:20` | Ein Interesse, das ein Newsletter-Benutzer gewählt hat. | – |

### extension/cjw_themes_jumper (2)

[Alle Muster von extension/cjw_themes_jumper](bereich-extension-cjw-themes-jumper.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwJumperImagineImageFactory` | class extends eZImageFactory | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagefactory.php:10` | Bild-Handler-Fabrik des Konverters cjwImagine (image.ini [cjwImagine] Handler=cjwJumperImagineImageFactory): erzeugt cjwJumperImagineImageHandle… | extension/cjw_themes_jumper/settings/image.ini [cjwImagine] Handler |
| `cjwJumperImagineImageHandler` | class extends eZImageHandler | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagehandler.php:28` | Bildkonverter für die cjw_*-Aliase des Design-Starters: zeichnet sie genau so, wie der Nexus-v2-Starter seine eigenen Aliase zeichnet (Imagine 1.x mit… | – |

### extension/cjw_tmv (2)

[Alle Muster von extension/cjw_tmv](bereich-extension-cjw-tmv.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwTmvFeed` | class | `extension/cjw_tmv/classes/cjwtmvfeed.php:14` | Die TMV-Ereignisse eines Ereigniscontainers (ein tmv_container-Knoten), gelesen aus den Inhaltsobjekten, die der Importer (cjwTmvImporter, Cronjob-Teil cjw_tmv)… | – |
| `cjwTmvImporter` | class | `extension/cjw_tmv/classes/cjwtmvimporter.php:28` | Importiert die Veranstaltungen der TMV-Veranstaltungsdatenbank als Inhaltsobjekte unterhalb ihres tmv_container, wie es die alte Extension cjw_tmv_veranstdb tat (TmvHelper::… | – |

### extension/explayouts (16)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsBlock` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblock.php:2` | – | – |
| `expLayoutsBlockHandlerFactory` | class | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsLayout` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutslayout.php:2` | – | – |
| `expLayoutsPager` | class | `extension/explayouts/classes/explayoutspager.php:8` | Paginierungshelfer für Exponential 4, kompatibel mit den Pagerfanta-Adaptern der Exp Site API. | – |
| `expLayoutsQueryHandlerFactory` | class | `extension/explayouts/classes/explayoutsqueryhandlerfactory.php:2` | – | – |
| `expLayoutsRenderer` | class | `extension/explayouts/classes/explayoutsrenderer.php:2` | – | – |
| `expLayoutsRule` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrule.php:2` | – | – |
| `expLayoutsRuleCondition` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrulecondition.php:2` | – | – |
| `expLayoutsRuleTarget` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsruletarget.php:2` | – | – |
| `expLayoutsSiteBundlePagerfanta` | class | `extension/explayouts/classes/explayoutssitebundlepagerfanta.php:4` | – | – |
| `expLayoutsSiteBundleQueryType` | class | `extension/explayouts/classes/explayoutssitebundlequerytype.php:4` | – | – |
| `expLayoutsZone` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutszone.php:2` | – | – |

### extension/explayouts_relation_list_query (1)

[Alle Muster von extension/explayouts_relation_list_query](bereich-extension-explayouts-relation-list-query.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsRelationListQueryFactory` | class | `extension/explayouts_relation_list_query/classes/explayoutsrelationlistqueryfactory.php:2` | – | – |

### extension/explayouts_ui_api (1)

[Alle Muster von extension/explayouts_ui_api](bereich-extension-explayouts-ui-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsUIApplicationApi` | class | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – | – |

### extension/expservices (28)

[Alle Muster von extension/expservices](bereich-extension-expservices.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expBookmarkServices` | class extends expUsersBase | `extension/expservices/classes/users/expbookmarkservices.php:12` | expbookmark: die Lesezeichen des aktuellen Benutzers in seinem Baum virtueller Ordner. | extension/expservices/settings/ezjscore.ini [ezjscServer_expbookmark] Class |
| `expClassGroupServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassgroupservices.php:13` | Dienste für Klassengruppen: ezjscore/call/expclassgroup::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclassgroup] Class |
| `expClassServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassservices.php:15` | Dienste für Klassen: ezjscore/call/expclass::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclass] Class |
| `expCommentServices` | class extends expCommunityBase | `extension/expservices/classes/community/expcommentservices.php:12` | ezjscore/call/expcomment::: Kommentare zu beliebigen Inhalten (Inhaltsobjekte der Kommentarklasse unterhalb des kommentierten Knotens) und die Bewertungen von Produkten (die… | extension/expservices/settings/ezjscore.ini [ezjscServer_expcomment] Class |
| `expCommunityBase` | abstract class extends expServiceBase | `extension/expservices/classes/community/expcommunitybase.php:12` | Die gemeinsame Arbeit der Dienste für Forum, Thema, Antwort und Kommentar: es sind Inhaltsdienste über konfigurierbare Inhaltsklassen ([Community] in expservi… | – |
| `expContentJobServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expcontentjobservices.php:15` | Dienste für Inhaltsjobs: ezjscore/call/expcontentjob::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expcontentjob] Class |
| `expCurrencyServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expcurrencyservices.php:11` | ezjscore/call/expcurrency::: die Währungen des Shops mit ihren Kursen, die Umrechnung, die bevorzugte Währung des Benutzers. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcurrency] Class |
| `expDiscountServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expdiscountservices.php:12` | ezjscore/call/expdiscount::: Rabattgruppen (Regeln), ihre Regeln (Prozentsatz mit Klassen-, Sektions- oder Produkteinschränkungen) und die Benutzer und Benutzergruppen, die… | extension/expservices/settings/ezjscore.ini [ezjscServer_expdiscount] Class |
| `expFeedServices` | class extends expServiceBase | `extension/expservices/classes/community/expfeedservices.php:13` | ezjscore/call/expfeed::: Feeds. | extension/expservices/settings/ezjscore.ini [ezjscServer_expfeed] Class |
| `expForumServices` | class extends expCommunityBase | `extension/expservices/classes/community/expforumservices.php:12` | ezjscore/call/expforum::: Foren (Inhaltsobjekte der Forumklasse, abgelegt in Forencontainern). | extension/expservices/settings/ezjscore.ini [ezjscServer_expforum] Class |
| `expLayoutServices` | class extends expServiceBase | `extension/expservices/classes/misc/explayoutservices.php:12` | ezjscore/call/explayout:: - die Layouts der Extension explayouts: Layouts, Zonen, Blöcke, Regeln, das für einen Knoten aufgelöste Layout und Entwürfe (anle… | extension/expservices/settings/ezjscore.ini [ezjscServer_explayout] Class |
| `expNodeServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expnodeservices.php:15` | Dienste für Knoten: ezjscore/call/expnode::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expnode] Class |
| `expObjectServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expobjectservices.php:14` | Dienste für Objekte: ezjscore/call/expobject::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expobject] Class |
| `expPollServices` | class extends expCommunityBase | `extension/expservices/classes/community/exppollservices.php:12` | ezjscore/call/exppoll::: Umfragen (Inhaltsobjekte der Umfrageklasse mit einem Optionsattribut, das Informationen sammelt): Liste, Ergebnisse, Abstimmen. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppoll] Class |
| `expRelationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exprelationservices.php:13` | Dienste für Relationen: ezjscore/call/exprelation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exprelation] Class |
| `expReplyServices` | class extends expCommunityBase | `extension/expservices/classes/community/expreplyservices.php:11` | ezjscore/call/expreply::: die Antworten eines Themas (Inhaltsobjekte der Antwortklasse unterhalb eines Themas). | extension/expservices/settings/ezjscore.ini [ezjscServer_expreply] Class |
| `expRoleServices` | class extends expUsersBase | `extension/expservices/classes/users/exproleservices.php:13` | exprole: Rollen, ihre Bearbeitungsentwürfe und ihre Zuweisungen an Benutzer und Gruppen. | extension/expservices/settings/ezjscore.ini [ezjscServer_exprole] Class |
| `expSectionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expsectionservices.php:13` | Dienste für Sektionen: ezjscore/call/expsection::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expsection] Class |
| `expServiceToken` | class extends eZPersistentObject | `extension/expservices/classes/expservicetoken.php:11` | Ein persönliches API-Token eines Benutzers für die Remote-Dienste (Tabelle expservices_token). | – |
| `expSitemapServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/expsitemapservices.php:12` | ezjscore/call/expsitemap:: - Sitemap-Daten: die Einträge eines Teilbaums, wie eine Sitemap sie auflistet (Ort, letzte Änderung, Änderungshäufigkeit, Priorität)… | extension/expservices/settings/ezjscore.ini [ezjscServer_expsitemap] Class |
| `expStateServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expstateservices.php:14` | Dienste für Objektzustände: ezjscore/call/expstate::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expstate] Class |
| `expTagsServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/exptagsservices.php:13` | ezjscore/call/exptags:: - der Tag-Baum der Extension eztags: Baum, Suche, Kinder, Synonyme, Übersetzungen, verwandte Inhalte und Schreibvorgänge (hinzufügen, umbe… | extension/expservices/settings/ezjscore.ini [ezjscServer_exptags] Class |
| `expTopicServices` | class extends expCommunityBase | `extension/expservices/classes/community/exptopicservices.php:12` | ezjscore/call/exptopic::: die Themen eines Forums (Inhaltsobjekte der Themenklasse). | extension/expservices/settings/ezjscore.ini [ezjscServer_exptopic] Class |
| `expUrlAliasServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expurlaliasservices.php:14` | Dienste für URL-Aliase und Platzhalter: ezjscore/call/expurlalias::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expurlalias] Class |
| `expUserGroupServices` | class extends expUsersBase | `extension/expservices/classes/users/expusergroupservices.php:10` | expusergroup: der Benutzergruppenbaum und seine Mitglieder. | extension/expservices/settings/ezjscore.ini [ezjscServer_expusergroup] Class |
| `expUserServices` | class extends expUsersBase | `extension/expservices/classes/users/expuserservices.php:13` | expuser: Benutzer nachschlagen, auflisten, suchen und verwalten. | extension/expservices/settings/ezjscore.ini [ezjscServer_expuser] Class |
| `expVatServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expvatservices.php:13` | ezjscore/call/expvat::: MwSt.-Typen, MwSt.-Regeln nach Land und Produktkategorie, die Produktkategorien, die die Regeln verwenden, und die MwSt. eines Produkts… | extension/expservices/settings/ezjscore.ini [ezjscServer_expvat] Class |
| `expVersionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expversionservices.php:13` | Dienste für Versionen: ezjscore/call/expversion::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expversion] Class |

### extension/exp_oe_tiptap (3)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOETiptapAIException` | class extends Exception | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexception.php:18` | Ein Fehler der KI-Hooks, dessen Meldung dem Redakteur angezeigt werden darf (ezjscore sendet Ausnahmemeldungen als error_text an den Browser). | – |
| `expOETiptapAIExpaiProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexpaiprovider.php:32` | Die KI-Befehle über die Extension expai (Exponential Intelligence), [AISettings] Provider=expai: die Anfrage geht an das Modell, mit dem expai eingerichtet is… | – |
| `expOETiptapAIService` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiservice.php:19` | Die KI-Hooks auf dem Server: Prüfungen, Grenzen, Prompt, Anbieteraufruf, Antwort. | – |

### extension/ezfind (2)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFindExtendedAttributeFilterFactory` | class | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Fabrik für erweiterte Attributfilter | – |
| `ezpFileArchiveFactory` | class | `extension/ezfind/classes/archive/filestorage/ezpfilearchivefactory.php:12` | – | – |

### extension/ezflow (5)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFeedReader` | class | `extension/ezflow/autoloads/ezfeedreader.php:32` | Die Klasse eZFeedReader implementiert die Methoden des Template-Operators feedreader | – |
| `eZPage` | class | `extension/ezflow/classes/ezpage.php:28` | – | – |
| `eZPageBlock` | class | `extension/ezflow/classes/ezpageblock.php:28` | – | – |
| `eZPageBlockItem` | class | `extension/ezflow/classes/ezpageblockitem.php:28` | – | – |
| `eZPageZone` | class | `extension/ezflow/classes/ezpagezone.php:28` | – | – |

### extension/ezgmaplocation (1)

[Alle Muster von extension/ezgmaplocation](bereich-extension-ezgmaplocation.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZGmapLocation` | class extends eZPersistentObject | `extension/ezgmaplocation/classes/ezgmaplocation.php:40` | Hilfsklasse, die zusammen mit dem Datentyp eZGmapLocationType verwendet wird. | – |

### extension/ezjscore (2)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscPacker` | class | `extension/ezjscore/classes/ezjscpacker.php:53` | – | – |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjsc] Class |

### extension/ezmbpaex (1)

[Alle Muster von extension/ezmbpaex](bereich-extension-ezmbpaex.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPaEx` | class extends eZPersistentObject | `extension/ezmbpaex/classes/ezpaex.php:11` | – | – |

### extension/ezmultiupload (1)

[Alle Muster von extension/ezmultiupload](bereich-extension-ezmultiupload.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMultiuploadService` | class | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | Die Serverseite der Upload-Seite: Prüfungen vor einem Upload (der Zielort, die Inhaltsklasse, zu der jede Datei wird, die Berechtigung zum Anlegen dafür… | – |

### extension/ezownerchange (1)

[Alle Muster von extension/ezownerchange](bereich-extension-ezownerchange.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOwnerChangeRequest` | class | `extension/ezownerchange/classes/expownerchangerequest.php:14` | Eine Anfrage zum Besitzerwechsel, geprüft, bevor irgendetwas gelesen oder geschrieben wird: der neue Besitzer, der Umfang (ein Objekt oder ein Teilbaum), der Start (Objekt oder Knoten),… | – |

### extension/ezpm (1)

[Alle Muster von extension/ezpm](bereich-extension-ezpm.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPm` | class extends eZPersistentObject | `extension/ezpm/classes/ezpm.php:15` | – | – |

### extension/ezstarrating (3)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsrRatingDataObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingdataobject.php:27` | – | – |
| `ezsrRatingObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingobject.php:27` | – | – |
| `ezsrRatingObjectTreeNode` | class extends eZContentObjectTreeNode | `extension/ezstarrating/classes/ezsrratingobjecttreenode.php:36` | Nur von {@link ezsrRatingObject::fetchNodeByRating()} zu verwenden, da andere Fetch-Funktionen die benötigten Bewertungswerte nicht in den zurückgegebenen Daten haben. | – |

### extension/eztags (1)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTags` | class | `extension/eztags/datatypes/eztags/eztags.php:6` | Die Klasse eZTags implementiert Funktionen, die der Datentyp eztags verwendet | – |

### extension/ezupdate (1)

[Alle Muster von extension/ezupdate](bereich-extension-ezupdate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZUpdateJob` | class | `extension/ezupdate/classes/ezupdatejob.php:21` | Ein Composer-Lauf im Hintergrund, damit ein langes Update oder eine lange Installation weder eine Web-Anfrage offen hält noch an deren Zeitlimit stößt und die Seite die Aus… | – |

### extension/ezurlmultisite (1)

[Alle Muster von extension/ezurlmultisite](bereich-extension-ezurlmultisite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLMultisite` | class | `extension/ezurlmultisite/classes/ezurlmultisite.php:27` | Siteaccess-übergreifende Linkauflösung für den Template-Operator ezurl. | – |

### extension/git_manager (1)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `GitManagerBackupFreshness` | class | `extension/git_manager/classes/gitmanagerbackupfreshness.php:32` | Wie aktuell die Backups sind: eine Antwort, ermittelt aus dem NEUESTEN Backup. | – |

### extension/nxc_powercontent (1)

[Alle Muster von extension/nxc_powercontent](bereich-extension-nxc-powercontent.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentOperationCollection` | class | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:20` | – | – |

### extension/sevenx_authentication_2fa (1)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxAuthentication2fa` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fa.php:22` | – | – |

### extension/sevenx_dse (4)

[Alle Muster von extension/sevenx_dse](bereich-extension-sevenx-dse.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AdminNeo\Connection` | abstract class | `extension/sevenx_dse/adminneo/core/Connection.php:5` | – | – |
| `AdminNeo\Driver` | abstract class | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – | – |
| `AdminNeo\Locale` | class | `extension/sevenx_dse/adminneo/core/Locale.php:5` | – | – |
| `AdminNeo\Origin` | abstract class extends Plugin | `extension/sevenx_dse/adminneo/core/Origin.php:7` | – | – |

### extension/syndication (9)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSyndicationFeed` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeed.php:47` | – | – |
| `eZSyndicationFeedItem` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditem.php:48` | – | – |
| `eZSyndicationFeedItemExport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemexport.php:48` | – | – |
| `eZSyndicationFeedItemStatus` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemstatus.php:48` | – | – |
| `eZSyndicationFeedSource` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsource.php:47` | – | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfilter.php:48` | – | – |
| `eZSyndicationImport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimport.php:47` | – | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – | – |

### extension/xrowextract (7)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractArchive` | class | `extension/xrowextract/classes/xrowextractarchive.php:12` | Das Site-Archiv: die Objekte vieler Klassen unterhalb vieler Knoten, eine CSV-Datei pro Klasse, mit einem Manifest, gepackt als zip, tar.gz, tar.bz2, tar.xz, 7z o… | – |
| `XrowExtractFilters` | class | `extension/xrowextract/classes/xrowextractfilters.php:16` | Filter eines Exports: ein Datumsbereich (veröffentlicht, geändert oder ein Datumsattribut der Klasse; beliebig, heute, die letzten N Tage, seit, vor, zwischen, zukünf… | – |
| `XrowExtractImport` | class | `extension/xrowextract/classes/xrowextractimport.php:22` | Die andere Richtung des Exports: liest eine CSV- oder JSON-Datei, die XrowExtractWriter geschrieben hat (beliebiger Spaltensatz, insbesondere „migration“), zurück in Inhal… | – |
| `XrowExtractJob` | class | `extension/xrowextract/classes/xrowextractjob.php:16` | Ein Exportjob im Hintergrund: ein Ordner pro Job unterhalb des var-Verzeichnisses (var//xrowextract-jobs//), mit job.json (Zustand), progress.json (geschrieben vom… | – |
| `XrowExtractManifest` | class | `extension/xrowextract/classes/xrowextractmanifest.php:20` | Das typisierte Spaltenmanifest eines Exports: was jede Spalte einer Datei ist (ihre ID, der Name in der Datei, der Datentyp, das gewählte Format und die Sprache),… | – |
| `XrowExtractPackage` | class | `extension/xrowextract/classes/xrowextractpackage.php:18` | Unterstützung für Inhaltspakete (.ezpkg) für xrowextract/package: Prüfen der Inhaltsklassen und Inhaltsobjekte eines Pakets, bevor etwas geschrieben wird (die… | – |
| `XrowExtractUpload` | class | `extension/xrowextract/classes/xrowextractupload.php:22` | Ein Upload in Teilstücken: ein Ordner pro Upload (var//xrowextract/uploads//), mit meta.json (Eigentümer, ursprünglicher Name, Gesamtgröße, bisher empfangene Bytes, ob… | – |

### extension/xrowmetadata (1)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowSitemapTools` | class | `extension/xrowmetadata/classes/xrowsitemaptools.php:3` | – | – |

### kernel/classes (80)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expCacheCatalogue` | class | `kernel/classes/expcachecatalogue.php:22` | Die Caches von Setup › Caches, für einen Menschen beschrieben: was jeder enthält, seine ID und Tags, sein Verzeichnis, seine Größe und Dateianzahl, wann er zuletzt… | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Legt die Kollaborationsgruppen eines Benutzers an, benennt sie um, löscht sie und verschiebt Einträge zwischen ihnen. | – |
| `expErrorHeaderList` | class | `kernel/classes/experrorheaderlist.php:25` | Die Antwort-Header einer Fehlerseite, aus error.ini [HTTPError-]: | – |
| `expExtensionCatalogue` | class | `kernel/classes/expextensioncatalogue.php:20` | Was Setup › Extensions über jede Extension weiß: ihre Metadaten (expInfo), ihre deklarierten Abhängigkeiten (extension.xml ), die Designs und Einstellungsda… | – |
| `expExtensionChangePlan` | class | `kernel/classes/expextensionchangeplan.php:33` | Der Änderungsplaner von Setup › Extensions: was aus einer Liste aktiver Extensions wird, wenn ein Administrator eine verschiebt, aktiviert oder deaktiviert, was di… | – |
| `expJsonSchemaValidator` | class | `kernel/classes/expjsonschemavalidator.php:20` | Prüft dekodiertes JSON (json_decode( ..., true )) gegen ein JSON Schema (Draft 2020-12), für die Schlüsselwörter, die die eigenen Schemas des Kernels verwenden: type, enum, cons… | – |
| `expPackageConflictReport` | class | `kernel/classes/exppackageconflictreport.php:22` | Was die Installation eines Pakets auf dieser Installation vorfinden würde, nach Remote-ID, ohne etwas zu schreiben: für jedes Inhaltsobjekt des Pakets, ob ein… | – |
| `expPhar` | class | `kernel/classes/expphar.php:21` | Packt die Engine – die Klassendateien von Kernel und Bibliotheken – in ein Phar, damit die Laufzeit sie aus einem Archiv statt aus tausend ein… | – |
| `expPreloadAddress` | class | `kernel/classes/exppreloadaddress.php:26` | Wo der Cache-Preloader einen Siteaccess findet: die Adresse seiner Site, das URL-Präfix, das ihn auf diesem Host auswählt, und die Seiten, mit denen ein Lauf beginnt. | – |
| `expSiteAccessOverview` | class | `kernel/classes/expsiteaccessoverview.php:31` | Jeder Siteaccess der Installation, wie eine Anfrage ihn erreicht und wo er liegt: der Siteaccess-Teil von Setup › Systeminformationen (#552). | – |
| `expSystemReport` | class | `kernel/classes/expsystemreport.php:29` | Die Fakten von Setup › System information und von exp:system:info, in Abschnitten, mit Zustandsprüfungen. | – |
| `expVelocity` | class | `kernel/classes/expvelocity.php:38` | Steuert den mitgelieferten Anwendungsserver als Dienst. | – |
| `expVelocityMarkers` | class | `kernel/classes/expvelocitymarkers.php:30` | Die kleinen Statusdateien, die sagen, warum eine Engine nicht läuft. | – |
| `expVelocityRestartUnit` | class | `kernel/classes/expvelocityrestartunit.php:34` | Führt exp:velocity restart als eine losgelöste Einheit aus: das Stoppen und das Starten in einem Prozess, der nicht zum Aufrufer gehört. | – |
| `eZCollaborationGroup` | class extends eZPersistentObject | `kernel/classes/ezcollaborationgroup.php:18` | – | – |
| `eZCollaborationItem` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitem.php:18` | – | – |
| `eZCollaborationItemGroupLink` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemgrouplink.php:18` | – | – |
| `eZCollaborationItemMessageLink` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemmessagelink.php:18` | – | – |
| `eZCollaborationItemParticipantLink` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemparticipantlink.php:18` | – | – |
| `eZCollaborationItemStatus` | class extends eZPersistentObject | `kernel/classes/ezcollaborationitemstatus.php:18` | – | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | `kernel/classes/ezcollaborationprofile.php:18` | – | – |
| `eZCollaborationSimpleMessage` | class extends eZPersistentObject | `kernel/classes/ezcollaborationsimplemessage.php:18` | – | – |
| `eZContentBrowseBookmark` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowsebookmark.php:35` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentBrowseBookmarkFolder` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowsebookmarkfolder.php:32` | – | – |
| `eZContentBrowseRecent` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowserecent.php:34` | – | – |
| `eZContentClass` | class extends eZPersistentObject | `kernel/classes/ezcontentclass.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentclassattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassClassGroup` | class extends eZPersistentObject | `kernel/classes/ezcontentclassclassgroup.php:18` | – | – |
| `eZContentClassGroup` | class extends eZPersistentObject | `kernel/classes/ezcontentclassgroup.php:18` | – | – |
| `eZContentFunctions` | class | `kernel/classes/ezcontentfunctions.php:12` | – | – |
| `eZContentObject` | class extends eZPersistentObject | `kernel/classes/ezcontentobject.php:17` | Kapselt Daten über Inhaltsobjekte und Methoden, um mit ihnen zu arbeiten | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectattribute.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectTrashNode` | class extends eZContentObjectTreeNode | `kernel/classes/ezcontentobjecttrashnode.php:15` | Kapselt Daten über Inhaltsobjekte im Papierkorb und Methoden, um mit ihnen zu arbeiten | – |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | `kernel/classes/ezcontentobjecttreenode.php:17` | Kapselt Daten über Inhaltsobjekte im Inhaltsobjektbaum und Methoden, um mit ihnen zu arbeiten | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectVersion` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectversion.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZDataType` | class | `kernel/classes/ezdatatype.php:46` | – | – |
| `eZDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountrule.php:18` | – | – |
| `eZDiscountSubRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrule.php:18` | – | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrulevalue.php:17` | – | – |
| `eZInformationCollection` | class extends eZPersistentObject | `kernel/classes/ezinformationcollection.php:22` | – | – |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | `kernel/classes/ezinformationcollectionattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZNamePatternResolver` | class | `kernel/classes/eznamepatternresolver.php:40` | eZNamePatternResolver ist eine Hilfsklasse zum Auflösen von Objektnamen- und URL-Alias-Mustern. | – |
| `eZNodeAssignment` | class extends eZPersistentObject | `kernel/classes/eznodeassignment.php:17` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZOrderStatus` | class extends eZPersistentObject | `kernel/classes/ezorderstatus.php:47` | – | – |
| `eZOrderStatusHistory` | class extends eZPersistentObject | `kernel/classes/ezorderstatushistory.php:35` | – | – |
| `eZPackage` | class | `kernel/classes/ezpackage.php:20` | – | – |
| `eZPackageComparison` | class | `kernel/classes/ezpackagecomparison.php:36` | Vergleicht, was ein Paket enthält, mit dem, was die Site hat, für package/compare/. | – |
| `eZPackageRemovalPlan` | class | `kernel/classes/ezpackageremovalplan.php:20` | Was das Entfernen einer Auswahl von Paketen bewirken würde, bevor es geschieht: pro Paket sein Verzeichnis, seine Dateien und seine Größe, ob es installiert ist (sein Installationsdat… | – |
| `eZPDFExport` | class extends eZPersistentObject | `kernel/classes/ezpdfexport.php:19` | – | – |
| `eZPolicy` | class extends eZPersistentObject | `kernel/classes/ezpolicy.php:19` | – | – |
| `eZPolicyLimitation` | class extends eZPersistentObject | `kernel/classes/ezpolicylimitation.php:18` | – | – |
| `eZPolicyLimitationValue` | class extends eZPersistentObject | `kernel/classes/ezpolicylimitationvalue.php:18` | – | – |
| `eZProductCategory` | class extends eZPersistentObject | `kernel/classes/ezproductcategory.php:18` | – | – |
| `eZProductCollection` | class extends eZPersistentObject | `kernel/classes/ezproductcollection.php:16` | eZProductCollection ist eine Containerklasse, die Gruppen von Produkten verwaltet | – |
| `eZProductCollectionItem` | class extends eZPersistentObject | `kernel/classes/ezproductcollectionitem.php:20` | – | – |
| `eZProductCollectionItemOption` | class extends eZPersistentObject | `kernel/classes/ezproductcollectionitemoption.php:12` | – | – |
| `eZRole` | class extends eZPersistentObject | `kernel/classes/ezrole.php:35` | – | – |
| `eZRSSExport` | class extends eZPersistentObject | `kernel/classes/ezrssexport.php:19` | – | – |
| `eZRSSExportItem` | class extends eZPersistentObject | `kernel/classes/ezrssexportitem.php:19` | – | – |
| `eZRSSExportOPMLItem` | class extends eZPersistentObject | `kernel/classes/ezrssexportopmlitem.php:26` | Eine Zeile eines OPML-Exports: ein Feed, den der Export auflistet. | – |
| `eZRSSImport` | class extends eZPersistentObject | `kernel/classes/ezrssimport.php:19` | – | – |
| `eZSearch` | class | `kernel/classes/ezsearch.php:19` | – | – |
| `eZSiteData` | class extends eZPersistentObject | `kernel/classes/ezsitedata.php:21` | eZPersistentObject-Implementierung für die Tabelle ezsite_data. | – |
| `eZSiteInstaller` | class | `kernel/classes/ezsiteinstaller.php:23` | – | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | `kernel/classes/eztipafriendcounter.php:18` | – | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | `kernel/classes/eztipafriendrequest.php:17` | – | – |
| `eZTrigger` | class extends eZPersistentObject | `kernel/classes/eztrigger.php:17` | – | – |
| `eZURLAliasML` | class extends eZPersistentObject | `kernel/classes/ezurlaliasml.php:58` | – | – |
| `eZURLAliasQuery` | class | `kernel/classes/ezurlaliasquery.php:34` | – | – |
| `eZURLWildcard` | class extends eZPersistentObject | `kernel/classes/ezurlwildcard.php:19` | – | – |
| `eZUserDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezuserdiscountrule.php:18` | – | – |
| `eZVatRule` | class extends eZPersistentObject | `kernel/classes/ezvatrule.php:18` | – | – |
| `eZVatType` | class extends eZPersistentObject | `kernel/classes/ezvattype.php:19` | – | – |
| `eZViewCounter` | class extends eZPersistentObject | `kernel/classes/ezviewcounter.php:12` | – | – |
| `eZWorkflow` | class extends eZPersistentObject | `kernel/classes/ezworkflow.php:18` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZWorkflowEvent` | class extends eZPersistentObject | `kernel/classes/ezworkflowevent.php:18` | – | – |
| `eZWorkflowGroup` | class extends eZPersistentObject | `kernel/classes/ezworkflowgroup.php:18` | – | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | `kernel/classes/ezworkflowgrouplink.php:18` | – | – |
| `eZWorkflowProcess` | class extends eZPersistentObject | `kernel/classes/ezworkflowprocess.php:19` | – | – |
| `eZWorkflowType` | class | `kernel/classes/ezworkflowtype.php:18` | – | – |

### kernel/classes/apikey (1)

[Alle Muster von kernel/classes/apikey](bereich-kernel-classes-apikey.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expApiKey` | class extends eZPersistentObject | `kernel/classes/apikey/expapikey.php:31` | Ein persönlicher API-Schlüssel (Veröffentlichungsschlüssel) eines Benutzers: eine Zeile von expapikey (doc/eng-us/guides/api-keys.md). | – |

### kernel/classes/audit (6)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAudit` | class | `kernel/classes/audit/expaudit.php:26` | Das Audit: was in Exponential geschieht, aufgezeichnet als hash-verkettete JSON-Zeilen pro Kanal (doc/eng-us/bc/6.0/audit.md, „The developer API (Z3)“). | – |
| `expAuditAlertState` | class | `kernel/classes/audit/alerts/expauditalertstate.php:19` | Der Fensterzustand einer Alarmregel (doc/eng-us/bc/6.0/audit.md, "Alerts (F5)"): /alerts/.json, gelesen und geschrieben unter flock() nur, wenn ein passender Datensatz … | – |
| `expAuditConfig` | class | `kernel/classes/audit/expauditconfig.php:21` | Die Audit-Einstellungen (settings/audit.ini, doc/eng-us/bc/6.0/audit.md „The settings reference“), einmal in ein Array gelesen. | – |
| `expAuditExportStream` | class | `kernel/classes/audit/console/expauditexportstream.php:28` | Die Audit-Datensätze eines Filters als CSV, JSON Lines oder JSON, neueste zuerst, Stück für Stück an einen Callback geschrieben, während sie gelesen werden: der Download von audit/… | – |
| `expAuditIndexRow` | class | `kernel/classes/audit/index/expauditindexrow.php:12` | Ein Audit-Datensatz als Zeile von expaudit_event (doc/eng-us/bc/6.0/audit.md, „Tables“): die Spalten, nach denen die Konsole filtert, der Datensatz selbst (wie geschrieben, damit d… | – |
| `expAuditSourceRegistry` | class | `kernel/classes/audit/console/expauditsourceregistry.php:16` | Die Registry der Quellen der Audit-Konsole (expAuditSource), aus audit.ini [AuditSourceSettings] Sources[]: welche registriert sind, welche hier funktionieren,… | – |

### kernel/classes/collaborationhandlers (1)

[Alle Muster von kernel/classes/collaborationhandlers](bereich-kernel-classes-collaborationhandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZApproveCollaborationHandler` | class extends eZCollaborationItemHandler | `kernel/classes/collaborationhandlers/ezapprove/ezapprovecollaborationhandler.php:25` | – | – |

### kernel/classes/contentcheck (1)

[Alle Muster von kernel/classes/contentcheck](bereich-kernel-classes-contentcheck.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentCheckReferences` | class | `kernel/classes/contentcheck/expcontentcheckreferences.php:37` | Der Referenzindex der Liste nicht referenzierter Inhalte (doc/eng-us/features/6.0/content-checks.md, "What counts as a reference"): jede Objekt-ID, Knoten-ID, Remote-ID… | – |

### kernel/classes/contentjob (2)

[Alle Muster von kernel/classes/contentjob](bereich-kernel-classes-contentjob.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentJob` | class | `kernel/classes/contentjob/expcontentjob.php:32` | Ein Inhaltsjob: ein großes Entfernen oder Kopieren eines Teilbaums, das in Stapeln in einem Hintergrund-Worker statt in der Web-Anfrage läuft, sodass es nie an einem Anfra… | – |
| `expContentJobException` | class extends Exception | `kernel/classes/contentjob/expcontentjobexception.php:13` | Der Fehler, mit dem ein Inhaltsjob ablehnt oder fehlschlägt; seine Meldung wird dem Benutzer angezeigt. | – |

### kernel/classes/datatypes (17)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expCountryGroups` | class | `kernel/classes/datatypes/ezcountry/expcountrygroups.php:24` | Die Länderliste, wie man daraus auswählt: zuerst die Gruppen aus settings/countrygroups.ini (standardmäßig eine Gruppe der meistgenutzten Länder, in gereihter R… | – |
| `eZBinaryFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php:19` | – | – |
| `eZEnumObjectValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumobjectvalue.php:18` | – | – |
| `eZEnumValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumvalue.php:19` | – | – |
| `eZForgotPassword` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezforgotpassword.php:19` | – | – |
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZISBNGroup` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngroup.php:24` | – | – |
| `eZISBNGroupRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngrouprange.php:26` | – | – |
| `eZISBNRegistrantRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbnregistrantrange.php:30` | – | – |
| `eZMedia` | class extends eZPersistentObject | `kernel/classes/datatypes/ezmedia/ezmedia.php:19` | – | – |
| `eZObjectRelationListType` | class extends eZDataType | `kernel/classes/datatypes/ezobjectrelationlist/ezobjectrelationlisttype.php:26` | – | – |
| `eZObjectRelationType` | class extends eZDataType | `kernel/classes/datatypes/ezobjectrelation/ezobjectrelationtype.php:19` | – | – |
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZURLObjectLink` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurlobjectlink.php:19` | – | – |
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUserAccountKey` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuseraccountkey.php:19` | – | – |
| `eZUserSetting` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezusersetting.php:18` | – | – |

### kernel/classes/help (7)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelpAllowlist` | class | `kernel/classes/help/exphelpallowlist.php:29` | Die Dateien, die das Hilfesystem anzeigen darf (doc/eng-us/features/6.0/help-system.md, "Which files are shown"). | – |
| `expHelpAudience` | class | `kernel/classes/help/exphelpaudience.php:29` | Für wen eine Hilfeseite ist, zu welchem Abschnitt der Hilfe sie gehört und welche Art von Seite sie ist (doc/eng-us/features/6.0/help-system.md, "Audiences, categories… | – |
| `expHelpContextRegistry` | class | `kernel/classes/help/exphelpcontextregistry.php:44` | Kontexthilfe: welche Hilfeseite „Hilfe zu dieser Seite“ beantwortet, für ein Modul, eine Ansicht und ein Design. | – |
| `expHelpMarkdownRenderer` | class | `kernel/classes/help/exphelpmarkdownrenderer.php:50` | Rendert das Markdown der Dokumentation (doc/**.md) in das HTML des Hilfe-Viewers (exphelp/viewer, doc/eng-us/features/6.0/help-system.md): CommonMark-Bl… | – |
| `expHelpPdf` | class | `kernel/classes/help/exphelppdf.php:18` | Eine Hilfeseite als PDF-Datei (doc/eng-us/features/6.0/help-system.md, „Print and PDF“): die Ansicht exphelp/pdf/ und `./console exp:help pdf ` fragen hier an. | – |
| `expHelpPdfFontSubset` | class | `kernel/classes/help/exphelppdffontsubset.php:17` | Reduziert eine TrueType-Schrift auf die Glyphen, die ein PDF verwendet (doc/eng-us/features/6.0/help-system.md, „Print and PDF“). | – |
| `expHelpSearch` | class | `kernel/classes/help/exphelpsearch.php:34` | expHelpSearch: die Volltextsuche des Hilfesystems (doc/eng-us/features/6.0/help-system.md, "Search"). | – |

### kernel/classes/ini (6)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniActionRegistry` | class | `kernel/classes/ini/actions/expiniactionregistry.php:22` | Die Aktionen des Befehls exp:ini und die Bereichsanbieter des Einstellungseditors, gelesen aus ini.ini [IniCommandSettings]: | – |
| `expIniEditor` | class | `kernel/classes/ini/expinieditor.php:27` | Der INI-Editor. | – |
| `expIniException` | class extends Exception | `kernel/classes/ini/expiniexception.php:15` | Ein Fehler des INI-Editors. | – |
| `expIniWriter` | class | `kernel/classes/ini/expiniwriter.php:26` | Eine INI-Datei als Liste von Zeilen, genau so gelesen, wie eZINI::parseFile() sie liest, und Zeile für Zeile geändert: Kommentare, Leerzeilen, die Reihenfolge d… | – |
| `expSettingsChain` | class | `kernel/classes/ini/expsettingschain.php:32` | Die Überschreibungskette einer INI-Datei, einmal aufgebaut aus den Dateien, die eZINI dafür liest, in eZINIs eigener Reihenfolge. | – |
| `expSettingsSecretRule` | class | `kernel/classes/ini/expsettingssecretrule.php:30` | Die Regel, nach der die Einstellungsseiten (settings/view, settings/edit) Werte maskieren. | – |

### kernel/classes/logviewer (7)

[Alle Muster von kernel/classes/logviewer](bereich-kernel-classes-logviewer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLogBundle` | class | `kernel/classes/logviewer/explogbundle.php:17` | „Alle Logs herunterladen“ (Setup › Logs verwalten; doc/eng-us/features/6.0/log-viewer.md, „Downloading every log“): ein Archiv aller Logs, die der Viewer auflistet, option… | – |
| `expLogManagerFactory` | class | `kernel/classes/logviewer/explogmanagerfactory.php:12` | Der Log-Manager, den die Ansichten des Log-Viewers verwenden (doc/eng-us/features/6.0/log-viewer.md, "Managing logs"): der Dienst expLogManager des Kernels, wenn er vorhan… | – |
| `expLogPermalink` | class | `kernel/classes/logviewer/explogpermalink.php:23` | Die Permalinks des Log-Viewers (doc/eng-us/features/6.0/log-viewer.md, „Permalinks“): jede Ansicht eines Logs als Pfad ohne Query-String, kurz genug, u… | – |
| `expLogRateLimit` | class | `kernel/classes/logviewer/explogratelimit.php:16` | Das Anfragelimit des Feeds des Log-Viewers je Sitzung ([RateLimitSettings], doc/eng-us/features/6.0/log-viewer.md, "Cost") und das Gedächtnis, welche Dateien… | – |
| `expLogRedactor` | class | `kernel/classes/logviewer/explogredactor.php:16` | Was der Log-Viewer maskiert, bevor eine Zeile den Server verlässt (doc/eng-us/features/6.0/log-viewer.md, "Privacy"). | – |
| `expLogViewer` | class | `kernel/classes/logviewer/explogviewer.php:16` | Der Log-Viewer (Setup › Log files, setup/logs; doc/eng-us/features/6.0/log-viewer.md): was seine Seite, sein Feed und seine Kommandozeile verwenden. | – |
| `expLogViewerConfig` | class | `kernel/classes/logviewer/explogviewerconfig.php:11` | Die Einstellungen des Log-Viewers (settings/logviewer.ini, doc/eng-us/features/6.0/log-viewer.md, „Settings“), einmal pro Anfrage mit sicheren Grenzen gelesen. | – |

### kernel/classes/mailpreferences (6)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expConsentContext` | class | `kernel/classes/mailpreferences/expconsentcontext.php:19` | Woher eine Änderung einer Präferenz stammt, wie das Einwilligungsprotokoll sie aufzeichnet: die Quelle (page, link, admin, import, signup, bridge, confirm, system), di… | – |
| `expConsentLog` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expconsentlog.php:22` | Das Einwilligungsprotokoll (Tabelle expmail_consent_log): eine Zeile pro Änderung der E-Mail-Präferenzen einer Person, mit Zeit, Kategorie, Aktion (on, off, p… | – |
| `expMailPreferences` | class | `kernel/classes/mailpreferences/expmailpreferences.php:28` | Die E-Mail-Einstellungen einer Person (expMailRecipient): der Hauptschalter "alle optionalen Mails", der Zustand jeder Kategorie (an, aus oder ausstehend, solange… | – |
| `expMailRecipient` | class | `kernel/classes/mailpreferences/expmailrecipient.php:23` | Bestimmt die Person, deren Präferenzen gemeint sind: ein Benutzerkonto oder eine E-Mail-Adresse ohne Konto. | – |
| `expMailSuppression` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailsuppression.php:22` | Die Sperrliste (Tabelle expmail_suppression): Adressen, an die keine optionale Mail geht (Hard Bounces, Beschwerden, „von allem abmelden“, rechtliche Aufford… | – |
| `expMailToken` | class | `kernel/classes/mailpreferences/expmailtoken.php:24` | Die Links in Mails, die ohne Login funktionieren: Abmelden (ein Klick, RFC 8058), Verwalten (die Präferenzseite) und Bestätigen (ein Double-Opt-in). | – |

### kernel/classes/notification (7)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCollaborationNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php:17` | – | – |
| `eZGeneralDigestUserSettings` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigestusersettings.php:18` | – | – |
| `eZNotificationCollection` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollection.php:17` | – | – |
| `eZNotificationCollectionItem` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – | – |
| `eZNotificationEvent` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationevent.php:17` | – | – |
| `eZNotificationEventType` | class | `kernel/classes/notification/eznotificationeventtype.php:18` | – | – |
| `eZSubtreeNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php:17` | – | – |

### kernel/classes/packagehandlers (1)

[Alle Muster von kernel/classes/packagehandlers](bereich-kernel-classes-packagehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentObjectPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezcontentobject/ezcontentobjectpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezcontentobject] |

### kernel/classes/requestclassifier (1)

[Alle Muster von kernel/classes/requestclassifier](bereich-kernel-classes-requestclassifier.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRequestContext` | class | `kernel/classes/requestclassifier/exprequestcontext.php:20` | Was der Anfrageklassifizierer betrachtet: eine Anfrage, so wie sie eingegangen ist. | – |

### kernel/classes/role (1)

[Alle Muster von kernel/classes/role](bereich-kernel-classes-role.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRoleGrantCheck` | class | `kernel/classes/role/exprolegrantcheck.php:37` | Ob ein Benutzer, der Rollen bearbeitet, eine Richtlinie vergeben darf: nur eine, die sein eigener Zugriff abdeckt (site.ini [RoleSettings] PreventPrivilegeEscalation). | – |

### kernel/classes/subitems (1)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsAttributeColumn` | class extends expSubitemsColumn | `kernel/classes/subitems/expsubitemsattributecolumn.php:16` | Eine automatische Unterelement-Spalte für ein Klassenattribut, Schlüssel attr:/. | – |

### kernel/classes/uiswitch (1)

[Alle Muster von kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUISwitch` | class | `kernel/classes/uiswitch/expuiswitch.php:28` | Das UI-Umschalt-Framework: eine Registry von Anbietern (uiswitch.ini [UISwitchSettings] ProviderList[]), die in der Session gespeicherten Auswahlen des Benutzers und die ea… | – |

### kernel/classes/user (5)

[Alle Muster von kernel/classes/user](bereich-kernel-classes-user.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRegistrationAdminAction` | class | `kernel/classes/user/expregistrationadminaction.php:23` | Die signierten Links der Administrator-Mail „Neuer Benutzer registriert“ (design:user/registrationfeedback.tpl) und die Aktionen dahinter (user/registrat… | – |
| `expUnactivatedUsers` | class | `kernel/classes/user/expunactivatedusers.php:22` | Die Benutzer, die sich registriert und ihr Konto nie aktiviert haben, für die Seite user/unactivated: wie viele es sind, eine Seite davon, gesucht nach Login, E-Ma… | – |
| `expUserAccountOverview` | class | `kernel/classes/user/expuseraccountoverview.php:22` | Die Kontoseite eines Benutzers (user/edit, user/edit/), für einen Menschen beschrieben: wer das Konto ist (Name, Login, E-Mail, Gruppen, Rollen), sein Zustand (aktiv… | – |
| `expUserDeactivation` | class | `kernel/classes/user/expuserdeactivation.php:36` | „Deaktivieren und verschieben“ und „Reaktivieren und zurückverschieben“: statt ein Benutzerkonto zu entfernen (Benutzer kommen nie in den Papierkorb, daher lässt sich das Entfernen nicht rückgä… | – |
| `expUserHygiene` | class | `kernel/classes/user/expuserhygiene.php:31` | Die Konten, die die Seite user/hygiene ("Check user accounts") auflistet: jedes Benutzerkonto oder die unterhalb einer Benutzergruppe, eingegrenzt durch einen Filter (nie angem… | – |

### kernel/classes/workflowtypes (2)

[Alle Muster von kernel/classes/workflowtypes](bereich-kernel-classes-workflowtypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPaymentLogger` | class | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentlogger.php:16` | – | – |
| `eZWaitUntilDateValue` | class extends eZPersistentObject | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatevalue.php:18` | – | – |

### kernel/modules (7)

[Alle Muster von kernel/modules](bereich-kernel-modules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCurrencyData` | class extends eZPersistentObject | `kernel/shop/classes/ezcurrencydata.php:12` | – | – |
| `eZExchangeRatesUpdateHandler` | class | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php:12` | – | – |
| `eZMultiEdit` | class | `kernel/content/multiedit_functions.php:16` | Die Arbeitsteile von content/multiedit. | – |
| `eZMultiPriceData` | class extends eZPersistentObject | `kernel/shop/classes/ezmultipricedata.php:12` | – | – |
| `eZPaymentObject` | class extends eZPersistentObject | `kernel/shop/classes/ezpaymentobject.php:22` | – | – |
| `eZShopFunctions` | class | `kernel/shop/classes/ezshopfunctions.php:12` | – | – |
| `eZStepCreateSites` | class extends eZStepInstaller | `kernel/setup/steps/ezstep_create_sites.php:52` | – | – |

### kernel/private (15)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expFileConsistencyReport` | class | `kernel/private/classes/expfileconsistencyreport.php:36` | Vergleicht die Dateien einer Installation mit den Prüfsummenlisten, die mit ihr ausgeliefert werden: share/filelist.md5 von Exponential und die share/filelist.md5, die eine Exte… | – |
| `expRestContentPermission` | class | `kernel/private/rest/classes/auth/content_permission.php:36` | Die Rechte des aktuellen Benutzers an den Inhalten, die ein REST-Aufruf liest oder schreibt, geprüft so, wie das Modul content sie prüft (doc/eng-us/guides/api-keys.md, „Permis… | – |
| `expSchemaConsistencyReport` | class | `kernel/private/classes/expschemaconsistencyreport.php:36` | Vergleicht die Datenbank mit dem Schema, das mit Exponential und seinen aktiven Extensions ausgeliefert wird (share/db_schema.dba der Wurzel und jeder Extension),… | – |
| `eZAutoloadGenerator` | class | `kernel/private/classes/ezautoloadgenerator.php:19` | Hilfsklasse zum Erzeugen von Autoload-Arrays für Exponential. | – |
| `ezpActiveExtensions` | class | `kernel/private/classes/ezpactiveextensions.php:26` | Ändert [ExtensionSettings] ActiveExtensions in settings/override/site.ini.append.php für Setup › Extensions und lässt alles andere in dieser… | – |
| `ezpAttributeOperatorManager` | class | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – | – |
| `ezpContent` | class | `kernel/private/api/content/content.php:56` | Klasse, die ein Inhaltselement darstellt. | – |
| `ezpContentField` | class | `kernel/private/api/content/field.php:17` | Diese Klasse verwaltet Inhaltsfelder. | – |
| `ezpContentFieldSet` | class | `kernel/private/api/content/field_set.php:18` | Ermöglicht das Lesen und Schreiben von Inhaltsfeldern (Attributen) über eine Array-ähnliche Schnittstelle. | – |
| `ezpContentLocation` | class extends ezpLocation | `kernel/private/api/content/location.php:21` | Diese Klasse wird verwendet, um einen bestimmten Inhaltsort zu bearbeiten. | – |
| `ezpContentPublishingProcess` | class extends eZPersistentObject | `kernel/private/classes/ezpcontentpublishingprocess.php:18` | Diese Klasse bietet eine PersistentObject-Schnittstelle zu einem Prozess der Veröffentlichungswarteschlange. | – |
| `ezpRestContentRenderer` | class | `kernel/private/rest/classes/content_renderer.php:12` | – | – |
| `ezpRestMvcResult` | class extends ezcMvcResult | `kernel/private/rest/classes/response/result.php:15` | Diese Struktur enthält die Ergebnisdaten, die von eZP-REST-Controllern zurückgegeben werden | – |
| `ezpRestProvider` | class | `kernel/private/rest/classes/rest_provider.php:12` | – | – |
| `ezpSiteAccessMenu` | class | `kernel/private/classes/ezpsiteaccessmenu.php:48` | Das Site-Menü der Administrationsoberfläche: die Siteaccesses dieser Installation mit ihren Startseitenadressen, für die Globus-Schaltfläche im Kopf von admin4. | – |

### kernel/private/classes/clusterfilehandlers (2)

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandlerBackendFactory` | class | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandlerbackendfactory.php:14` | Instanziiert das DFSBackend anhand der INI-Einstellungen | – |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | interface | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendfactoryinterface.php:14` | Macht einen DFSBackend-Handler über die statische Methode build instanziierbar. | – |

### kernel/private/classes/commands (1)

[Alle Muster von kernel/private/classes/commands](bereich-kernel-private-classes-commands.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Kernel\Checkdbfiles` | class extends Command | `kernel/private/classes/commands/checkdbfiles.php:149` | Der Code von bin/php/checkdbfiles.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### kernel/private/classes/httpcache (1)

[Alle Muster von kernel/private/classes/httpcache](bereich-kernel-private-classes-httpcache.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpHttpCacheContract` | class | `kernel/private/classes/httpcache/ezphttpcachecontract.php:38` | Der rollenbewusste HTTP-Cache-Vertrag: Schlüssel, Kontext-Hashes, die Eintrags- und Datensatzformate, Tag-Validierung und Platzhalterersetzung. | – |

### kernel/private/classes/requestrules (2)

[Alle Muster von kernel/private/classes/requestrules](bereich-kernel-private-classes-requestrules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRequestRule` | class | `kernel/private/classes/requestrules/ezprequestrule.php:33` | Eine Anfrageregel: Wenn jede Bedingung zutrifft, entscheidet die Aktion. | – |
| `ezpRequestRuleResult` | class | `kernel/private/classes/requestrules/ezprequestruleresult.php:24` | Was eine Regel entschieden hat. | – |

### kernel/private/classes/runnable (1)

[Alle Muster von kernel/private/classes/runnable](bereich-kernel-private-classes-runnable.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Runnable\Runnable` | abstract class | `kernel/private/classes/runnable/runnable.php:30` | Die Basis von allem, was Exponential aus einer Datei ausführt: CLI-Befehle (bin/), Cronjob-Teile (cronjobs/) und Modulansichten (kernel//.php, extension//module… | – |

### kernel/private/classes/views (1)

[Alle Muster von kernel/private/classes/views](bereich-kernel-private-classes-views.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Kernel\Content\Job` | class extends ModuleView | `kernel/private/classes/views/content/job.php:19` | Die Ansicht content/job/: die Fortschrittsseite eines Inhaltsjobs (ein großes Entfernen oder Kopieren eines Teilbaums, das im Hintergrund läuft, siehe kernel/classes/contentjob/… | – |

### lib/ezfile (2)

[Alle Muster von lib/ezfile](bereich-lib-ezfile.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDir` | class | `lib/ezfile/classes/ezdir.php:17` | – | – |
| `eZFile` | class | `lib/ezfile/classes/ezfile.php:18` | – | – |

### lib/ezi18n (2)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |
| `eZUTF8Codec` | class | `lib/ezi18n/classes/ezutf8codec.php:22` | – | – |

### lib/ezimage (11)

[Alle Muster von lib/ezimage](bereich-lib-ezimage.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageAnalyzer` | class | `lib/ezimage/classes/ezimageanalyzer.php:23` | – | – |
| `eZImageFactory` | class | `lib/ezimage/classes/ezimagefactory.php:21` | – | – |
| `eZImageGDFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimagegdfactory.php:12` | – | settings/image.ini [GD] Handler |
| `eZImageGDHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimagegdhandler.php:24` | – | – |
| `eZImageHandler` | class | `lib/ezimage/classes/ezimagehandler.php:19` | – | – |
| `eZImageInterface` | class | `lib/ezimage/classes/ezimageinterface.php:20` | – | – |
| `eZImageLayer` | class extends eZImageInterface | `lib/ezimage/classes/ezimagelayer.php:19` | – | – |
| `eZImageManager` | class | `lib/ezimage/classes/ezimagemanager.php:85` | – | – |
| `eZImageShellFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimageshellfactory.php:12` | – | settings/image.ini [ImageMagick] Handler |
| `eZImageShellHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimageshellhandler.php:19` | – | – |
| `eZImageTextLayer` | class extends eZImageLayer | `lib/ezimage/classes/ezimagetextlayer.php:19` | – | – |

### lib/ezlocale (4)

[Alle Muster von lib/ezlocale](bereich-lib-ezlocale.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDate` | class | `lib/ezlocale/classes/ezdate.php:58` | – | – |
| `eZDateTime` | class | `lib/ezlocale/classes/ezdatetime.php:62` | – | – |
| `eZLocale` | class | `lib/ezlocale/classes/ezlocale.php:111` | – | – |
| `eZTime` | class | `lib/ezlocale/classes/eztime.php:59` | – | – |

### lib/ezmath (1)

[Alle Muster von lib/ezmath](bereich-lib-ezmath.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPHPMath` | class | `lib/ezmath/classes/mathhandlers/ezphpmath.php:17` | – | settings/shop.ini [MathSettings] MathHandler |

### lib/eztemplate (4)

[Alle Muster von lib/eztemplate](bereich-lib-eztemplate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTemplate` | class | `lib/eztemplate/classes/eztemplate.php:200` | – | – |
| `eZTemplateCompiler` | class | `lib/eztemplate/classes/eztemplatecompiler.php:49` | – | – |
| `eZTemplateNodeTool` | class | `lib/eztemplate/classes/eztemplatenodetool.php:19` | – | – |
| `eZTemplateRoleOperator` | class | `lib/eztemplate/classes/eztemplateroleoperator.php:24` | Die Klasse eZTemplateRoleOperator stellt Template-Operatoren zur Prüfung der Rollenzugehörigkeit von Benutzern bereit | – |

### lib/ezutils (10)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDebug` | class | `lib/ezutils/classes/ezdebug.php:59` | – | – |
| `eZFatalUserError` | class extends Error | `lib/ezutils/classes/ezfatalusererror.php:35` | Ein schwerer Fehler, den der Kernel selbst auslöst, anstelle eines Benutzerfehlers der Stufe E_USER_ERROR, den PHP 8.4 für trigger_error() als veraltet markiert („throw a… | – |
| `eZHTTPTool` | class | `lib/ezutils/classes/ezhttptool.php:25` | – | – |
| `eZINI` | class | `lib/ezutils/classes/ezini.php:53` | ### EXP-MULTI-SITE-OVERRIDE-SETTINGS ### - lib/ezutils/classes/ezini.php - neue INI-Einstellung ext-siteaccess-override - - neue INI-Einstellung ext-siteaccess… | – |
| `eZModule` | class | `lib/ezutils/classes/ezmodule.php:36` | Die Klasse eZModule dient zum Instanziieren und Verwenden von Modulen und Ansichten. | – |
| `eZOperationMemento` | class extends eZPersistentObject | `lib/ezutils/classes/ezoperationmemento.php:18` | – | – |
| `ezpRepairQueue` | class | `lib/ezutils/classes/ezprepairqueue.php:37` | Repariert eine Installation, deren Composer-Bibliotheken fehlen, von der Fehlerseite aus, die das meldet (eZExecution::renderErrorCasePage('dependencies')), d… | – |
| `eZSys` | class | `lib/ezutils/classes/ezsys.php:33` | Einfacher Zugriff auf verschiedene Systemeinstellungen | – |
| `eZURI` | class | `lib/ezutils/classes/ezuri.php:32` | Bietet Zugriff auf die HTTP-URI | – |
| `eZWizardBaseClassLoader` | class | `lib/ezutils/classes/ezwizardbaseclassloader.php:18` | – | – |
