---
title: Active Record (eZPersistentObject)
audience: developer
category: standards
order: 310
summary: "Eine Klasse, deren Objekte Zeilen einer Datenbanktabelle sind: Sie beschreibt die Tabelle in definition() und lädt, speichert und entfernt sich selbst über eZPersistentObject. 194 Instanzen in Exponential."
keywords: Entwurfsmuster, Active Record, Datenzugriff, Musterkatalog, design pattern, active-record, pattern catalogue
type: catalogue
---
# Active Record (eZPersistentObject)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Eine Klasse, deren Objekte Zeilen einer Datenbanktabelle sind: Sie beschreibt die Tabelle in definition() und lädt, speichert und entfernt sich selbst über eZPersistentObject.

|  |  |
| --- | --- |
| Gruppe | Datenzugriff |
| Instanzen | 194 |
| Erkannt an | Erweitert eZPersistentObject, direkt oder über eine Elternklasse. |
| Erweiterungspunkt | Eine neue Tabelle: eine Klasse, die eZPersistentObject mit einer statischen definition() erweitert; Schema in share/db_schema.dba oder sql/ENGINE/schema.sql der Extension. |

## Klassen nach Bereich

### extension/cjw_newsletter (38)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterAbTest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtest.php:20` | Der A/B-Betrefftest eines Versands: Stichproben, Kriterium, Wartezeit, Gewinner. | – |
| `CjwNewsletterAbVariant` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabvariant.php:20` | Eine Betreffvariante eines A/B-Tests und ihre Zähler. | – |
| `CjwNewsletterApproval` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapproval.php:20` | Der Freigabezustand einer Ausgabeversion für eine Liste, die eine Freigabe verlangt (Kollaborations-Posteingang). | – |
| `CjwNewsletterArticlePool` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterarticlepool.php:20` | Woher Artikel für Ausgaben kommen (Knoten, Klassen, Sektionen, Tags, Zustände, Alter), pro Liste oder als globaler Standard. | – |
| `CjwNewsletterBlacklistItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterblacklistitem.php:17` | Sperrliste von Newsletter-Benutzern oder E-Mail-Adressen verwalten | – |
| `CjwNewsletterCsvExport` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettercsvexport.php:17` | Verschiedene Daten im CSV-Format exportieren | – |
| `CjwNewsletterEdition` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletteredition.php:17` | Datenverwaltung Datentyp cjwnewsletteredition | – |
| `CjwNewsletterEditionArticle` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionarticle.php:20` | Ein Artikel, der von einem Redakteur oder durch das automatische Befüllen aus einem Pool in eine Ausgabe übernommen wurde. | – |
| `CjwNewsletterEditionSend` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsend.php:17` | Klassenbeschreibung hier | – |
| `CjwNewsletterEditionSendItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettereditionsenditem.php:20` | Mail-Warteschlange | – |
| `CjwNewsletterEditionSendOutput` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewslettereditionsendoutput.php:20` | Die gerenderte Ausgabe eines Versands in einer Sprache (eine Ausgabe, viele Übersetzungen). | – |
| `CjwNewsletterImport` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterimport.php:17` | Import-Sets von Abonnements verwalten | – |
| `CjwNewsletterImportMapping` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportmapping.php:20` | Eine gespeicherte Spaltenzuordnung eines CSV-Imports (Spalte -› Feld), Trennzeichen, Kopfzeile, Herkunft der Einwilligung. | – |
| `CjwNewsletterInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterest.php:20` | Ein Interesse, das Abonnenten wählen können (ein Thema einer Liste oder ein eztags-Tag). | – |
| `CjwNewsletterLink` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlink.php:20` | Ein Link eines Versands, den die Klick-Weiterleitung kennt (nur gespeicherte URLs werden weitergeleitet). | – |
| `CjwNewsletterLinkClick` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlinkclick.php:20` | Ein Klick auf einen Link; personenbezogen (Versandeintrag) nur mit Einwilligung, sonst ist der Eintrag 0. | – |
| `CjwNewsletterList` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterlist.php:20` | Datenverwaltung Datentyp cjwnewsletterlist | – |
| `CjwNewsletterListVirtual` | class extends CjwNewsletterList | `extension/cjw_newsletter/classes/cjwnewsletterlistvirtual.php:20` | Datenverwaltung Datentyp cjwnewsletterlist | – |
| `CjwNewsletterMailbox` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettermailbox.php:17` | Mit dem Mailkonto verbinden und Mails abrufen/parsen | – |
| `CjwNewsletterMailboxItem` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewslettermailboxitem.php:18` | Mail-Einträge speichern und parsen | – |
| `CjwNewsletterMailinAddress` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinaddress.php:20` | Eine Adresse (oder ein Plus-Adress-Tag) einer Liste, die An- und Abmelde-Mails annimmt. | – |
| `CjwNewsletterMailinMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinmessage.php:20` | Eine Mail, die an einer Mail-In-Adresse eingegangen ist, und was mit ihr gemacht wurde. | – |
| `CjwNewsletterMigrationLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewslettermigrationlog.php:20` | Eine Zeile, die der eznewsletter-Importer gelesen hat, und was er damit gemacht hat (oder in einem Probelauf machen würde). | – |
| `CjwNewsletterOpen` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletteropen.php:20` | Ein Öffnen eines Versands; personenbezogen nur mit Einwilligung. | – |
| `CjwNewsletterSchedule` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedule.php:20` | Ein wiederkehrender Versand einer Liste: eine Vorlagenausgabe kopieren oder die neueste Ausgabe senden, wann, automatisches Befüllen aus einem Pool. | – |
| `CjwNewsletterScheduleLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulelog.php:20` | Ein Lauf eines Zeitplans: gesendet, übersprungen (leer, Bedingung) oder fehlgeschlagen. | – |
| `CjwNewsletterSendBatch` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersendbatch.php:20` | Ein Stapel eines Versands: welche Einträge er übernommen hat, wie weit er gekommen ist (fortsetzbar), seine Zähler. | – |
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
| `CjwNewsletterUtils` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/cjwnewsletterutils.php:17` | Klasse mit einigen nützlichen Funktionen | – |

### extension/enhancedselection2 (1)

[Alle Muster von extension/enhancedselection2](bereich-extension-enhancedselection2.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SckEnhancedSelection` | class extends eZPersistentObject | `extension/enhancedselection2/classes/sckenhancedselection.php:3` | – | – |

### extension/explayouts (10)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsBlock` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblock.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsLayout` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutslayout.php:2` | – | – |
| `expLayoutsRule` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrule.php:2` | – | – |
| `expLayoutsRuleCondition` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsrulecondition.php:2` | – | – |
| `expLayoutsRuleTarget` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsruletarget.php:2` | – | – |
| `expLayoutsZone` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutszone.php:2` | – | – |

### extension/expservices (1)

[Alle Muster von extension/expservices](bereich-extension-expservices.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expServiceToken` | class extends eZPersistentObject | `extension/expservices/classes/expservicetoken.php:11` | Ein persönliches API-Token eines Benutzers für die Remote-Dienste (Tabelle expservices_token). | – |

### extension/ezfind (3)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFindElevateConfiguration` | class extends eZPersistentObject | `extension/ezfind/classes/ezfindelevateconfiguration.php:8` | – | – |
| `eZFindResultNode` | class extends eZContentObjectTreeNode | `extension/ezfind/classes/ezfindresultnode.php:8` | – | – |
| `eZFindResultObject` | class extends eZContentObject | `extension/ezfind/classes/ezfindresultobject.php:8` | – | – |

### extension/ezflow (2)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFlowBlock` | class extends eZPersistentObject | `extension/ezflow/classes/ezflowblock.php:28` | – | – |
| `eZFlowPoolItem` | class extends eZPersistentObject | `extension/ezflow/classes/ezflowpoolitem.php:28` | – | – |

### extension/ezgmaplocation (1)

[Alle Muster von extension/ezgmaplocation](bereich-extension-ezgmaplocation.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZGmapLocation` | class extends eZPersistentObject | `extension/ezgmaplocation/classes/ezgmaplocation.php:40` | Hilfsklasse, die zusammen mit dem Datentyp eZGmapLocationType verwendet wird. | – |

### extension/ezmbpaex (2)

[Alle Muster von extension/ezmbpaex](bereich-extension-ezmbpaex.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPaEx` | class extends eZPersistentObject | `extension/ezmbpaex/classes/ezpaex.php:11` | – | – |
| `eZPaExUser` | class extends eZUser | `extension/ezmbpaex/login_handler/ezpaexuser.php:23` | Verarbeitet Anmeldungen von Benutzern unter den neuen Regeln zum Passwortablauf | – |

### extension/ezpm (3)

[Alle Muster von extension/ezpm](bereich-extension-ezpm.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContact` | class extends eZPersistentObject | `extension/ezpm/classes/ezcontact.php:15` | – | – |
| `eZPm` | class extends eZPersistentObject | `extension/ezpm/classes/ezpm.php:15` | – | – |
| `eZPmBlackList` | class extends eZPersistentObject | `extension/ezpm/classes/ezpmblacklist.php:15` | – | – |

### extension/ezstarrating (3)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsrRatingDataObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingdataobject.php:27` | – | – |
| `ezsrRatingObject` | class extends eZPersistentObject | `extension/ezstarrating/classes/ezsrratingobject.php:27` | – | – |
| `ezsrRatingObjectTreeNode` | class extends eZContentObjectTreeNode | `extension/ezstarrating/classes/ezsrratingobjecttreenode.php:36` | Nur von {@link ezsrRatingObject::fetchNodeByRating()} zu verwenden, da andere Fetch-Funktionen die benötigten Bewertungswerte nicht in den zurückgegebenen Daten haben. | – |

### extension/eztags (3)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTagsAttributeLinkObject` | class extends eZPersistentObject | `extension/eztags/classes/eztagsattributelinkobject.php:7` | Die Klasse eZTagsAttributeLinkObject erbt von der Klasse eZPersistentObject, um über die API auf die Datenbanktabelle eztags_attribute_link zugreifen zu können | – |
| `eZTagsKeyword` | class extends eZPersistentObject | `extension/eztags/classes/eztagskeyword.php:7` | Die Klasse eZTagsKeyword erbt von der Klasse eZPersistentObject, um über die API auf die Datenbanktabelle eztags_keyword zugreifen zu können | – |
| `eZTagsObject` | class extends eZPersistentObject | `extension/eztags/classes/eztagsobject.php:9` | Die Klasse eZTagsObject erbt von der Klasse eZPersistentObject, um über die API auf die Datenbanktabelle eztags zugreifen zu können | – |

### extension/sevenx_authentication_2fa (8)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFacebookUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezfacebookuser.php:17` | – | – |
| `eZGoogleUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezgoogleuser.php:17` | – | – |
| `eZIdmeUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezidmeuser.php:22` | – | – |
| `eZInstagramUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/ezinstagramuser.php:17` | – | – |
| `eZMetaUser` | class extends eZFacebookUser | `extension/sevenx_authentication_2fa/login_handler/ezmetauser.php:17` | – | – |
| `eZOAuthUser` | abstract class extends eZUser | `extension/sevenx_authentication_2fa/classes/ezoauthuser.php:26` | – | – |
| `eZsevenxUser2faUser` | class extends eZUser | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:29` | – | – |
| `eZTwitterUser` | class extends eZOAuthUser | `extension/sevenx_authentication_2fa/login_handler/eztwitteruser.php:17` | – | – |

### extension/syndication (11)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFilterAttribute` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfilterattribute.php:48` | – | – |
| `eZFilterSection` | class extends eZSyndicationFilter | `extension/syndication/classes/filter/ezfiltersection.php:47` | – | – |
| `eZSyndicationFeed` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeed.php:47` | – | – |
| `eZSyndicationFeedItem` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditem.php:48` | – | – |
| `eZSyndicationFeedItemExport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemexport.php:48` | – | – |
| `eZSyndicationFeedItemStatus` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeeditemstatus.php:48` | – | – |
| `eZSyndicationFeedSource` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsource.php:47` | – | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationfilter.php:48` | – | – |
| `eZSyndicationImport` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimport.php:47` | – | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – | – |

### extension/xrowextract (3)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractDestination` | class extends eZPersistentObject | `extension/xrowextract/classes/xrowextractdestination.php:9` | Ein benanntes, wiederverwendbares Lieferziel (Tabelle xrowextract_destination). | – |
| `XrowExtractHistory` | class extends eZPersistentObject | `extension/xrowextract/classes/xrowextracthistory.php:12` | Der Index der Exportverläufe (Tabelle xrowextract_history): eine Zeile pro Lauf - ein Hintergrundjob (von Hand, von der Kommandozeile oder durch einen Zeitplan gestartet) oder… | – |
| `XrowExtractSchedule` | class extends eZPersistentObject | `extension/xrowextract/classes/xrowextractschedule.php:20` | Ein geplanter Export oder Import (Tabelle xrowextract_schedule, bei Bedarf von XrowExtractSchema angelegt). | – |

### kernel/classes (65)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZBasket` | class extends eZPersistentObject | `kernel/classes/ezbasket.php:20` | – | – |
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
| `eZContentClassName` | class extends eZPersistentObject | `kernel/classes/ezcontentclassname.php:12` | – | – |
| `eZContentLanguage` | class extends eZPersistentObject | `kernel/classes/ezcontentlanguage.php:12` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObject` | class extends eZPersistentObject | `kernel/classes/ezcontentobject.php:17` | Kapselt Daten über Inhaltsobjekte und Methoden, um mit ihnen zu arbeiten | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectattribute.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectTrashNode` | class extends eZContentObjectTreeNode | `kernel/classes/ezcontentobjecttrashnode.php:15` | Kapselt Daten über Inhaltsobjekte im Papierkorb und Methoden, um mit ihnen zu arbeiten | – |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | `kernel/classes/ezcontentobjecttreenode.php:17` | Kapselt Daten über Inhaltsobjekte im Inhaltsobjektbaum und Methoden, um mit ihnen zu arbeiten | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectVersion` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectversion.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountrule.php:18` | – | – |
| `eZDiscountSubRule` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrule.php:18` | – | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrulevalue.php:17` | – | – |
| `eZInformationCollection` | class extends eZPersistentObject | `kernel/classes/ezinformationcollection.php:22` | – | – |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | `kernel/classes/ezinformationcollectionattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZNodeAssignment` | class extends eZPersistentObject | `kernel/classes/eznodeassignment.php:17` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZOrder` | class extends eZPersistentObject | `kernel/classes/ezorder.php:20` | – | – |
| `eZOrderItem` | class extends eZPersistentObject | `kernel/classes/ezorderitem.php:27` | – | – |
| `eZOrderStatus` | class extends eZPersistentObject | `kernel/classes/ezorderstatus.php:47` | – | – |
| `eZOrderStatusHistory` | class extends eZPersistentObject | `kernel/classes/ezorderstatushistory.php:35` | – | – |
| `eZPathElement` | class extends eZPersistentObject | `kernel/classes/ezpathelement.php:23` | – | – |
| `eZPDFExport` | class extends eZPersistentObject | `kernel/classes/ezpdfexport.php:19` | – | – |
| `eZPendingActions` | class extends eZPersistentObject | `kernel/classes/ezpendingactions.php:12` | – | – |
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
| `eZSection` | class extends eZPersistentObject | `kernel/classes/ezsection.php:18` | – | – |
| `eZSiteData` | class extends eZPersistentObject | `kernel/classes/ezsitedata.php:21` | eZPersistentObject-Implementierung für die Tabelle ezsite_data. | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | `kernel/classes/eztipafriendcounter.php:18` | – | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | `kernel/classes/eztipafriendrequest.php:17` | – | – |
| `eZTrigger` | class extends eZPersistentObject | `kernel/classes/eztrigger.php:17` | – | – |
| `eZURLAliasML` | class extends eZPersistentObject | `kernel/classes/ezurlaliasml.php:58` | – | – |
| `eZURLWildcard` | class extends eZPersistentObject | `kernel/classes/ezurlwildcard.php:19` | – | – |
| `eZUserDiscountRule` | class extends eZPersistentObject | `kernel/classes/ezuserdiscountrule.php:18` | – | – |
| `eZVatRule` | class extends eZPersistentObject | `kernel/classes/ezvatrule.php:18` | – | – |
| `eZVatType` | class extends eZPersistentObject | `kernel/classes/ezvattype.php:19` | – | – |
| `eZViewCounter` | class extends eZPersistentObject | `kernel/classes/ezviewcounter.php:12` | – | – |
| `eZWishList` | class extends eZPersistentObject | `kernel/classes/ezwishlist.php:20` | – | – |
| `eZWorkflow` | class extends eZPersistentObject | `kernel/classes/ezworkflow.php:18` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZWorkflowEvent` | class extends eZPersistentObject | `kernel/classes/ezworkflowevent.php:18` | – | – |
| `eZWorkflowGroup` | class extends eZPersistentObject | `kernel/classes/ezworkflowgroup.php:18` | – | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | `kernel/classes/ezworkflowgrouplink.php:18` | – | – |
| `eZWorkflowProcess` | class extends eZPersistentObject | `kernel/classes/ezworkflowprocess.php:19` | – | – |

### kernel/classes/apikey (1)

[Alle Muster von kernel/classes/apikey](bereich-kernel-classes-apikey.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expApiKey` | class extends eZPersistentObject | `kernel/classes/apikey/expapikey.php:31` | Ein persönlicher API-Schlüssel (Veröffentlichungsschlüssel) eines Benutzers: eine Zeile von expapikey (doc/eng-us/guides/api-keys.md). | – |

### kernel/classes/datatypes (16)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZBinaryFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php:19` | – | – |
| `eZEnumObjectValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumobjectvalue.php:18` | – | – |
| `eZEnumValue` | class extends eZPersistentObject | `kernel/classes/datatypes/ezenum/ezenumvalue.php:19` | – | – |
| `eZForgotPassword` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezforgotpassword.php:19` | – | – |
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZISBNGroup` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngroup.php:24` | – | – |
| `eZISBNGroupRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngrouprange.php:26` | – | – |
| `eZISBNRegistrantRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbnregistrantrange.php:30` | – | – |
| `eZLDAPUser` | class extends eZUser | `kernel/classes/datatypes/ezuser/ezldapuser.php:20` | – | – |
| `eZMedia` | class extends eZPersistentObject | `kernel/classes/datatypes/ezmedia/ezmedia.php:19` | – | – |
| `eZTextFileUser` | class extends eZUser | `kernel/classes/datatypes/ezuser/eztextfileuser.php:27` | – | – |
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZURLObjectLink` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurlobjectlink.php:19` | – | – |
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUserAccountKey` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuseraccountkey.php:19` | – | – |
| `eZUserSetting` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezusersetting.php:18` | – | – |

### kernel/classes/mailpreferences (5)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expConsentLog` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expconsentlog.php:22` | Das Einwilligungsprotokoll (Tabelle expmail_consent_log): eine Zeile pro Änderung der E-Mail-Präferenzen einer Person, mit Zeit, Kategorie, Aktion (on, off, p… | – |
| `expMailCategoryRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailcategoryrow.php:15` | Eine Kategorie, die im Kategorienmanager der Administrationsoberfläche angelegt oder geändert wurde (Tabelle expmail_category). | – |
| `expMailPendingRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailpendingrow.php:14` | Ein Double-Opt-in, das auf seine Bestätigung wartet (Tabelle expmail_pending): Art 'category' (eine eingeschaltete Newsletter- oder Marketingkategorie), 'email_change… | – |
| `expMailPreferenceRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailpreferencerow.php:14` | Eine gespeicherte Präferenz (Tabelle expmail_preference): ein Empfänger (recipient_key 'u:' oder 'a:'), eine Kategorie, ihr Zustand (on, off, pending) und die Häufigkeit. | – |
| `expMailSuppression` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailsuppression.php:22` | Die Sperrliste (Tabelle expmail_suppression): Adressen, an die keine optionale Mail geht (Hard Bounces, Beschwerden, „von allem abmelden“, rechtliche Aufford… | – |

### kernel/classes/notification (6)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCollaborationNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php:17` | – | – |
| `eZGeneralDigestUserSettings` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigestusersettings.php:18` | – | – |
| `eZNotificationCollection` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollection.php:17` | – | – |
| `eZNotificationCollectionItem` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – | – |
| `eZNotificationEvent` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationevent.php:17` | – | – |
| `eZSubtreeNotificationRule` | class extends eZPersistentObject | `kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php:17` | – | – |

### kernel/classes/subitems (2)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsSearchWordLinkRow` | class extends eZPersistentObject | `kernel/classes/subitems/columns/expsubitemscolumntables.php:31` | Eine Zeile von ezsearch_object_word_link: eine indizierte Wortposition eines Objekts. | – |
| `expSubitemsUserVisitRow` | class extends eZPersistentObject | `kernel/classes/subitems/columns/expsubitemscolumntables.php:15` | Eine Zeile von ezuservisit: die Besuche, Anmeldungen und fehlgeschlagenen Anmeldungen eines Benutzers. | – |

### kernel/classes/workflowtypes (1)

[Alle Muster von kernel/classes/workflowtypes](bereich-kernel-classes-workflowtypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZWaitUntilDateValue` | class extends eZPersistentObject | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatevalue.php:18` | – | – |

### kernel/modules (3)

[Alle Muster von kernel/modules](bereich-kernel-modules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCurrencyData` | class extends eZPersistentObject | `kernel/shop/classes/ezcurrencydata.php:12` | – | – |
| `eZMultiPriceData` | class extends eZPersistentObject | `kernel/shop/classes/ezmultipricedata.php:12` | – | – |
| `eZPaymentObject` | class extends eZPersistentObject | `kernel/shop/classes/ezpaymentobject.php:22` | – | – |

### kernel/private (5)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentObjectState` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstate.php:18` | Klasse, die einen Inhaltsobjektzustand darstellt | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroup` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategroup.php:18` | Klasse, die eine Inhaltsobjekt-Zustandsgruppe darstellt | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroupLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategrouplanguage.php:21` | Diese Klasse kümmert sich um die Lokalisierung einer Inhaltsobjekt-Zustandsgruppe. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstatelanguage.php:21` | Diese Klasse kümmert sich um die Lokalisierung eines Inhaltsobjektzustands. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `ezpContentPublishingProcess` | class extends eZPersistentObject | `kernel/private/classes/ezpcontentpublishingprocess.php:18` | Diese Klasse bietet eine PersistentObject-Schnittstelle zu einem Prozess der Veröffentlichungswarteschlange. | – |

### lib/ezutils (1)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZOperationMemento` | class extends eZPersistentObject | `lib/ezutils/classes/ezoperationmemento.php:18` | – | – |
