---
title: Hilfsklasse (statische Hilfsfunktionen)
audience: developer
category: standards
order: 337
summary: Eine Klasse statischer Funktionen, nach Thema gruppiert; keine Instanzen. 540 Instanzen in Exponential.
keywords: Entwurfsmuster, Hilfsklasse, Struktur, Musterkatalog, design pattern, utility, pattern catalogue
type: catalogue
---
# Hilfsklasse (statische Hilfsfunktionen)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine Klasse statischer Funktionen, nach Thema gruppiert; keine Instanzen.

|  |  |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 540 |
| Erkannt an | Eine konkrete Klasse, deren Methoden alle statisch sind (mindestens zwei). |

## Klassen nach Bereich

### extension/bccie (5)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bccieExportPage` | class | `extension/bccie/classes/bccieExportPage.php:15` | Die Exportseite eines Formulars: das Optionsformular, der Download, der Start eines Exports im Hintergrund und das Entfernen der gesammelten Informationen. | – |
| `bccieExportUtils` | class | `extension/bccie/classes/bccieExportUtils.php:11` | – | – |
| `bccieJob` | class | `extension/bccie/classes/bccieJob.php:15` | Ein Export im Hintergrund, gestartet aus der Administrationsoberfläche: derselbe Konsolenbefehl, den die Shell verwendet (ext:bccie:export), gestartet mit expProcessTools wie d… | – |
| `bccieRunner` | class | `extension/bccie/classes/bccieRunner.php:15` | Die Arbeit der Exporte als Methoden, damit die Admin, die Konsolenbefehle und die Cronjob-Teile dasselbe tun: die Optionen validieren, ein e… | – |
| `bccieUI` | class | `extension/bccie/classes/bccieUI.php:13` | Gemeinsame Helfer der Modulansichten: Flash-Hinweise und die Parameter einer Listenansicht (Paginierung, Sortierung, Filter). | – |

### extension/cjw_newsletter (62)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterAbTest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtest.php:20` | Der A/B-Betrefftest eines Versands: Stichproben, Kriterium, Wartezeit, Gewinner. | – |
| `CjwNewsletterAbTester` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtester.php:27` | Der A/B-Betrefftest eines Versands (cjw_newsletter 4.2.0, Bereich N4 Statistik). | – |
| `CjwNewsletterAbVariant` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabvariant.php:20` | Eine Betreffvariante eines A/B-Tests und ihre Zähler. | – |
| `CjwNewsletterApprovalFlow` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapprovalflow.php:23` | Die optionale Freigabe von Ausgaben vor dem Versand, pro Liste (cjwnl_list.approval_required). | – |
| `CjwNewsletterArticlePoolFinder` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterarticlepoolfinder.php:25` | Findet die Artikel eines Artikelpools: die veröffentlichten Inhalte unter den Knoten des Pools, die zu seinen Klassen, Sektionen, Tags, Zuständen und seinem Alter passen, mit d… | – |
| `CjwNewsletterBounce` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterbounce.php:32` | Was ein Bounce für den Newsletter bedeutet und was dagegen unternommen wird (cjw_newsletter.ini [DeliverabilitySettings]). | – |
| `CjwNewsletterClassInstaller` | class | `extension/cjw_newsletter/classes/cjwnewsletterclassinstaller.php:18` | Installiert die Newsletter-Inhaltsklassen aus den in extension/cjw_newsletter/packages mitgelieferten Paketen in eine Inhaltsklassengruppe. | – |
| `CjwNewsletterClassLanguages` | class | `extension/cjw_newsletter/classes/cjwnewsletterclasslanguages.php:28` | Verschiebt die Newsletter-Inhaltsklassen von einer Sprache in eine andere (cjw_newsletter 4.2.1: eng-GB nach eng-US). | – |
| `CjwNewsletterConditions` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterconditions.php:39` | Die "Newsletter-Bedingung" einer Ausgabe: ein Teil des Textes, den nur einige Abonnenten erhalten. | – |
| `CjwNewsletterDashboard` | class | `extension/cjw_newsletter/classes/cjwnewsletterdashboard.php:15` | – | – |
| `CjwNewsletterDeliverability` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterdeliverability.php:26` | Die Stapel der Mail-Warteschlange und die fälligen Einträge (cjw_newsletter.ini [ThrottleSettings]). | – |
| `CjwNewsletterDeliverabilityHooks` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterdeliverabilityhooks.php:25` | Der Erweiterungspunkt-Handler des Bereichs „Zustellbarkeit“ (cjw_newsletter.ini [ExtensionPointSettings] Handlers[]=CjwNewsletterDeliverabilityHooks, se… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterEditionBuilder` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionbuilder.php:24` | Erstellt und befüllt Ausgaben: findet die neueste ungesendete Ausgabe einer Liste, kopiert eine Vorlagenausgabe unter ihre Liste und übernimmt Artikel aus einem Pool in eine… | – |
| `CjwNewsletterEditionSendOutput` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewslettereditionsendoutput.php:20` | Die gerenderte Ausgabe eines Versands in einer Sprache (eine Ausgabe, viele Übersetzungen). | – |
| `CjwNewsletterEditorialFetch` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialfetch.php:27` | Die Template-Fetch-Funktionen des Bereichs Editorial (modules/newsletter/function_definition.php): | – |
| `CjwNewsletterEditorialHooks` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialhooks.php:20` | Der Erweiterungspunkt-Handler des Bereichs Redaktion (registriert in cjw_newsletter.ini [ExtensionPointSettings] Handlers[]): die wiederkehrenden Versände laufen bei… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterEditorialUI` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialui.php:18` | Kleine Hilfsfunktionen der Admin-Ansichten des Bereichs Redaktion: die Listen und Ausgaben zur Auswahl, der Pfad, das Lesen gesendeter Werte. | – |
| `CjwNewsletterExtensionPoints` | class | `extension/cjw_newsletter/classes/cjwnewsletterextensionpoints.php:46` | Die Stellen, an denen die Funktionsbereiche von cjw_newsletter in den gemeinsamen Code eingreifen (der Queue-Runner, das Versandformular, das Listenattribut, das Dashboard) w… | – |
| `CjwNewsletterImportConsent` | class extends CjwNewsletterMailPreferences | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportconsent.php:25` | Die Einwilligung importierter Abonnements im Einwilligungsprotokoll des Kernels, mit der Quelle "import". | – |
| `CjwNewsletterImportMapping` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportmapping.php:20` | Eine gespeicherte Spaltenzuordnung eines CSV-Imports (Spalte -› Feld), Trennzeichen, Kopfzeile, Herkunft der Einwilligung. | – |
| `CjwNewsletterInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterest.php:20` | Ein Interesse, das Abonnenten wählen können (ein Thema einer Liste oder ein eztags-Tag). | – |
| `CjwNewsletterInterestBlock` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterestblock.php:20` | Der Ausgabeblock "Artikel für Ihre Interessen": ein Skin gibt {cjwnl_interests_block( 5, $heading, $accent )} aus, die Ausgabe behält die Markierung [[cjwnl:i… | – |
| `CjwNewsletterInterests` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterests.php:20` | Die Interessen, die Abonnenten wählen (cjwnl_interest, cjwnl_user_interest). | – |
| `CjwNewsletterJob` | class | `extension/cjw_newsletter/classes/cjwnewsletterjob.php:16` | – | – |
| `CjwNewsletterLink` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlink.php:20` | Ein Link eines Versands, den die Klick-Weiterleitung kennt (nur gespeicherte URLs werden weitergeleitet). | – |
| `CjwNewsletterLinkClick` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlinkclick.php:20` | Ein Klick auf einen Link; personenbezogen (Versandeintrag) nur mit Einwilligung, sonst ist der Eintrag 0. | – |
| `CjwNewsletterMailin` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailin.php:36` | An- und Abmelden per E-Mail (cjw_newsletter.ini [MailInSettings], die Adressen in cjwnl_mailin_address, jede bearbeitete Nachricht in cjwnl_maili… | extension/cjw_newsletter/settings/mailpreferences.ini [BounceSettings] MessageListeners[] |
| `CjwNewsletterMailinAddress` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinaddress.php:20` | Eine Adresse (oder ein Plus-Adress-Tag) einer Liste, die An- und Abmelde-Mails annimmt. | – |
| `CjwNewsletterMailinMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinmessage.php:20` | Eine Mail, die an einer Mail-In-Adresse eingegangen ist, und was mit ihr gemacht wurde. | – |
| `CjwNewsletterMailPreferences` | class | `extension/cjw_newsletter/classes/cjwnewslettermailpreferences.php:25` | Die Brücke zwischen dem Newsletter und den E-Mail-Präferenzen von Exponential 6.0.15 und später (kernel/classes/mailpreferences). | extension/cjw_newsletter/settings/mailpreferences.ini [SuppressionSettings] Listeners[] |
| `CjwNewsletterMappedImport` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettermappedimport.php:32` | Ein CSV-Import mit einer Spaltenzuordnung in eine Newsletter-Liste (cjw_newsletter 4.2.0). | – |
| `CjwNewsletterMigrationLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewslettermigrationlog.php:20` | Eine Zeile, die der eznewsletter-Importer gelesen hat, und was er damit gemacht hat (oder in einem Probelauf machen würde). | – |
| `CjwNewsletterOpen` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletteropen.php:20` | Ein Öffnen eines Versands; personenbezogen nur mit Einwilligung. | – |
| `CjwNewsletterPlaceholders` | class | `extension/cjw_newsletter/classes/cjwnewsletterplaceholders.php:30` | Ersetzt die Platzhalter einer Ausgabe ([[name]], [[first_name]] ..., #_hash_unsubscribe_# ...) für einen Empfänger. | – |
| `CjwNewsletterPlainText` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterplaintext.php:15` | Der Textteil eines Newsletters: Rich Text als einfacher Text, Zeilenumbruch und das Aufräumen eines gerenderten Textteils. | – |
| `CjwNewsletterRendering` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrendering.php:17` | Was das Rendering von cjw_newsletter 4.2.0 gemeinsam hat: die Skins (je Liste erlaubt, je Versand gewählt), die Sprachen einer Liste und eines Abonnenten, die Au… | – |
| `CjwNewsletterRenderingHooks` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrenderinghooks.php:25` | Der Erweiterungspunkt-Handler des Renderings (Bereich N3, cjw_newsletter.ini [ExtensionPointSettings] Handlers[]). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterRunner` | class | `extension/cjw_newsletter/classes/cjwnewsletterrunner.php:16` | – | – |
| `CjwNewsletterScheduleConditionNewArticles` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterscheduleconditionnewarticles.php:18` | Der Lauf findet nur statt, wenn der Artikelpool des Zeitplans Inhalte hat, die seit seinem letzten Versand veröffentlicht wurden (aus Sicht des anonymen Benutzers), in beiden… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ScheduleSettings] ConditionHandlers[] |
| `CjwNewsletterScheduleLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulelog.php:20` | Ein Lauf eines Zeitplans: gesendet, übersprungen (leer, Bedingung) oder fehlgeschlagen. | – |
| `CjwNewsletterScheduleRunner` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulerunner.php:32` | Führt die wiederkehrenden Versände aus, deren Zeit gekommen ist (cjwnl_schedule): aus dem Teil queue-create des Cronjobs (der Erweiterungspunkt queueCreateBefore, wenn… | – |
| `CjwNewsletterSendBatch` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersendbatch.php:20` | Ein Stapel eines Versands: welche Einträge er übernommen hat, wie weit er gekommen ist (fortsetzbar), seine Zähler. | – |
| `CjwNewsletterSms` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersms.php:25` | Der SMS-Kanal von cjw_newsletter: Einstellungen, die Transporte, Telefonnummern (E.164), der Bestätigungscode (das Double-Opt-in einer Nummer), das STOP-Sch… | – |
| `CjwNewsletterSmsCode` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscode.php:20` | Ein per SMS gesendeter Bestätigungscode (als Hash gespeichert), mit Versuchen und Ablauf. | – |
| `CjwNewsletterSmsHooks` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmshooks.php:27` | Der Handler des Bereichs N5 (SMS) an den Erweiterungspunkten (CjwNewsletterExtensionPoints), registriert in cjw_newsletter.ini [ExtensionPointSettings] Handle… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterSmsInbound` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsinbound.php:20` | Eine eingegangene SMS (ein STOP-Schlüsselwort, ein Code) und was mit ihr gemacht wurde. | – |
| `CjwNewsletterSmsMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsmessage.php:20` | Eine SMS einer SMS-Ausgabe an einen Newsletter-Benutzer. | – |
| `CjwNewsletterStatisticsHooks` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticshooks.php:31` | Der Erweiterungspunkt-Handler des Bereichs N4 Statistik ([ExtensionPointSettings] Handlers[]=CjwNewsletterStatisticsHooks). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterStatisticsReport` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticsreport.php:18` | Die Zahlen der Statistikseiten (cjw_newsletter 4.2.0, Bereich N4 Statistics): der Bericht eines Versands, der Dashboard-Block, die Artikelbox und der CS… | – |
| `CjwNewsletterStatisticsRetention` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticsretention.php:25` | Wie lange personenbezogene Statistiken aufbewahrt werden (cjw_newsletter 4.2.0, Bereich N4 Statistik). | – |
| `CjwNewsletterStatTotal` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstattotal.php:20` | Anonyme Summen pro Versand, Link, Typ und Tag; sie bleiben erhalten, nachdem die personenbezogenen Zeilen entfernt wurden. | – |
| `CjwNewsletterSubscriberExport` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettersubscriberexport.php:22` | Die Abonnenten einer Liste als CSV, mit Filtern (Abonnementstatus, ein Datumsbereich auf einem gewählten Datum) und einer Auswahl von Spalten. | – |
| `CjwNewsletterSuppressionImport` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersuppressionimport.php:24` | Importiert eine CSV-Datei mit Adressen in die Sperrliste des Kernels (expMailSuppression): eine Kontaktsperrliste, die Bounces eines früheren Mailsystems,… | – |
| `CjwNewsletterTestGroup` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettertestgroup.php:20` | Eine benannte Gruppe von Testadressen für Testversände einer Liste (oder jeder Liste). | – |
| `CjwNewsletterTestSend` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettertestsend.php:24` | Testversände an eine benannte Gruppe von Adressen (cjwnl_test_group, cjw_newsletter.ini [TestSendSettings]). | – |
| `CjwNewsletterThrottle` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterthrottle.php:30` | Die Ratenlimits der Transporte (cjw_newsletter.ini [ThrottleSettings]): wie viele Mails (oder SMS) ein Transport in der aktuellen Minute senden darf und die… | – |
| `CjwNewsletterThrottleState` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterthrottlestate.php:20` | Die Senderate eines Transports im aktuellen Minuten- oder Stundenfenster und eine Pause. | – |
| `CjwNewsletterTracking` | class | `extension/cjw_newsletter/classes/statistics/cjwnewslettertracking.php:33` | Öffnungs- und Klick-Tracking der Newsletter-Ausgaben (cjw_newsletter 4.2.0, Bereich N4 Statistik). | – |
| `CjwNewsletterTranslatableFields` | class | `extension/cjw_newsletter/classes/rendering/cjwnewslettertranslatablefields.php:21` | Die Textfelder der Newsletter-Klassen sind übersetzbar (cjw_newsletter 4.2.0): eine Ausgabe mit Übersetzungen, jeder Abonnent erhält seine Sprache. | – |
| `CjwNewsletterUI` | class | `extension/cjw_newsletter/classes/cjwnewsletterui.php:15` | – | – |
| `CjwNewsletterUserInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletteruserinterest.php:20` | Ein Interesse, das ein Newsletter-Benutzer gewählt hat. | – |
| `cjw_newsletterInfo` | class | `extension/cjw_newsletter/ezinfo.php:16` | Klassenbeschreibung hier | – |

### extension/cjw_themes_jumper (2)

[Alle Muster von extension/cjw_themes_jumper](bereich-extension-cjw-themes-jumper.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwJumperFormHoneypot` | class | `extension/cjw_themes_jumper/classes/cjwjumperformhoneypot.php:20` | Der Honeypot der Feedbackformulare des Design-Starters (Spamschutz, zusammen mit dem Formular-Token von ezformtoken). | – |
| `cjwOsmTileProxy` | class | `extension/cjw_themes_jumper/classes/cjwosmtileproxy.php:13` | Die Kartenkacheln des Moduls osm_proxy: Validierung, der lokale Kachel-Cache und der Abruf vom Kachelserver. | – |

### extension/cjw_tmv (2)

[Alle Muster von extension/cjw_tmv](bereich-extension-cjw-tmv.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `cjwTmvFunctionCollection` | class | `extension/cjw_tmv/classes/cjwtmvfunctioncollection.php:10` | Die Fetch-Funktionen des Moduls cjw_tmv (modules/cjw_tmv/function_definition.php). | – |
| `cjwTmvHtml` | class | `extension/cjw_tmv/classes/cjwtmvhtml.php:12` | Bereinigt das HTML eines TMV-Veranstaltungstextes, bevor es gecacht und gezeichnet wird: nur Absätze, Zeilenumbrüche, Hervorhebungen, Listen, kleine Überschriften und Links (http, htt… | – |

### extension/enhancedselection2 (1)

[Alle Muster von extension/enhancedselection2](bereich-extension-enhancedselection2.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SckEnhancedSelection` | class extends eZPersistentObject | `extension/enhancedselection2/classes/sckenhancedselection.php:3` | – | – |

### extension/expchangeclass (2)

[Alle Muster von extension/expchangeclass](bereich-extension-expchangeclass.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `conversionFunctions` | class | `extension/expchangeclass/classes/functions.php:39` | – | – |
| `expChangeClassConverters` | class | `extension/expchangeclass/classes/converters.php:21` | – | extension/expchangeclass/settings/changeclass.ini [ezobjectrelationlist] Class; extension/expchangeclass/settings/changeclass.ini [ezstring] Class; extension/expchangeclass/settings/changeclass.ini [eztext] Class |

### extension/explayouts (51)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsAjaxLoadMoreServer` | class | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer-Funktionen zum Laden seitenweiser Collection-Blöcke. | extension/explayouts/settings/ezjscore.ini [ezjscServer_expajaxloadmore] Class; extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini [ezjscServer_expajaxloadmore] Class |
| `expLayoutsBlockHandlerFactory` | class | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsDynamicCollection` | class | `extension/explayouts/classes/explayoutsdynamiccollection.php:15` | Führt „dynamische“ Collections aus (aus Netgen Layouts importierte Collection-Abfragen). | – |
| `expLayoutsExponential` | class | `extension/explayouts/classes/explayoutsexponential.php:4` | – | – |
| `expLayoutsExponentialorter` | class | `extension/explayouts/classes/explayoutsexporter.php:2` | – | – |
| `expLayoutsExpRelationListQuery` | class | `extension/explayouts/classes/explayoutsexprelationlistquery.php:6` | Exponential-4-Portierung des Netgen-Layouts-Handlers Exp RelationListQuery. | – |
| `expLayoutsExpReverseRelationListQuery` | class | `extension/explayouts/classes/explayoutsexpreverserelationlistquery.php:6` | Exponential-4-Portierung des Netgen-Layouts-Handlers Exp ReverseRelationListQuery. | – |
| `expLayoutsExpSiteApi` | class | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – | – |
| `expLayoutsExpTagsQuery` | class | `extension/explayouts/classes/explayoutsexptagsquery.php:6` | Exponential-4-Portierung des Netgen-Layouts-Handlers Exp TagsQuery. | – |
| `expLayoutsExpUtils` | class | `extension/explayouts/classes/explayoutsexputils.php:4` | – | – |
| `expLayoutsFixtures` | class | `extension/explayouts/classes/explayoutsfixtures.php:2` | – | – |
| `expLayoutsImporter` | class | `extension/explayouts/classes/explayoutsimporter.php:2` | – | – |
| `expLayoutsInstall` | class | `extension/explayouts/classes/explayoutsinstall.php:2` | – | – |
| `expLayoutsLanguageResolver` | class | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Sprachauflöser für Exponential 4, kompatibel mit dem LanguageResolver-Muster der Exp Site API. | – |
| `expLayoutsLayoutType` | class | `extension/explayouts/classes/explayoutslayouttype.php:2` | – | – |
| `expLayoutsLinkParameter` | class | `extension/explayouts/classes/explayoutslinkparameter.php:23` | Der Link-Parameter, den die Blöcke teilen, die einen tragen können. | – |
| `expLayoutsMailHelper` | class | `extension/explayouts/classes/explayoutsmailhelper.php:6` | Exponential-4-Portierung von Helper/MailHelper aus dem Netgen SiteBundle. | – |
| `expLayoutsQueryHandlerFactory` | class | `extension/explayouts/classes/explayoutsqueryhandlerfactory.php:2` | – | – |
| `expLayoutsQueryType` | class | `extension/explayouts/classes/explayoutsquerytype.php:9` | Query-Builder für Exponential 4, kompatibel mit dem QueryType-Muster der Exp Site API. | – |
| `expLayoutsRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsrelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_relation_list] Handler |
| `expLayoutsRenderer` | class | `extension/explayouts/classes/explayoutsrenderer.php:2` | – | – |
| `expLayoutsResolver` | class | `extension/explayouts/classes/explayoutsresolver.php:2` | – | – |
| `expLayoutsReverseRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_reverse_relation_list] Handler |
| `expLayoutsSiteAPI` | class | `extension/explayouts/classes/explayoutssiteapi.php:10` | Kompatibilitätsschicht Exponential 4 / Exponential Site API. | – |
| `expLayoutsSiteBundleCommand` | class | `extension/explayouts/classes/explayoutssitebundlecommand.php:4` | – | – |
| `expLayoutsSiteBundleContentForms` | class | `extension/explayouts/classes/explayoutssitebundlecontentforms.php:4` | – | – |
| `expLayoutsSiteBundleController` | class | `extension/explayouts/classes/explayoutssitebundlecontroller.php:4` | – | – |
| `expLayoutsSiteBundleCore` | class | `extension/explayouts/classes/explayoutssitebundlecore.php:4` | – | – |
| `expLayoutsSiteBundleDataCollector` | class | `extension/explayouts/classes/explayoutssitebundledatacollector.php:4` | – | – |
| `expLayoutsSiteBundleDebug` | class | `extension/explayouts/classes/explayoutssitebundledebug.php:4` | – | – |
| `expLayoutsSiteBundleDependencyInjection` | class | `extension/explayouts/classes/explayoutssitebundledependencyinjection.php:4` | – | – |
| `expLayoutsSiteBundleEvent` | class | `extension/explayouts/classes/explayoutssitebundleevent.php:4` | – | – |
| `expLayoutsSiteBundleEventListener` | class | `extension/explayouts/classes/explayoutssitebundleeventlistener.php:4` | – | – |
| `expLayoutsSiteBundleImagine` | class | `extension/explayouts/classes/explayoutssitebundleimagine.php:4` | – | – |
| `expLayoutsSiteBundleInfoCollection` | class | `extension/explayouts/classes/explayoutssitebundleinfocollection.php:4` | – | – |
| `expLayoutsSiteBundleLayouts` | class | `extension/explayouts/classes/explayoutssitebundlelayouts.php:4` | – | – |
| `expLayoutsSiteBundleMenu` | class | `extension/explayouts/classes/explayoutssitebundlemenu.php:4` | – | – |
| `expLayoutsSiteBundlePagerfanta` | class | `extension/explayouts/classes/explayoutssitebundlepagerfanta.php:4` | – | – |
| `expLayoutsSiteBundleQueryType` | class | `extension/explayouts/classes/explayoutssitebundlequerytype.php:4` | – | – |
| `expLayoutsSiteBundleRelation` | class | `extension/explayouts/classes/explayoutssitebundlerelation.php:4` | – | – |
| `expLayoutsSiteBundleRichText` | class | `extension/explayouts/classes/explayoutssitebundlerichtext.php:4` | – | – |
| `expLayoutsSiteBundleSecurity` | class | `extension/explayouts/classes/explayoutssitebundlesecurity.php:4` | – | – |
| `expLayoutsSiteBundleTopic` | class | `extension/explayouts/classes/explayoutssitebundletopic.php:4` | – | – |
| `expLayoutsTagsQueryHandler` | class | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_tags] Handler |
| `expLayoutsTemplateEditor` | class | `extension/explayouts/classes/explayoutstemplateeditor.php:2` | – | – |
| `ExplBlockDebug` | class | `extension/explayouts/event/explblockdebug.php:15` | Event-Listener für response/output, der ein Debug-Overlay der expl-Blöcke ausgibt, wenn die Seite mit ?expl_debug=1 angefordert wird. | – |
| `ExplBlockParser` | class | `extension/explayouts/classes/explblockparser.php:15` | ExplBlockParser — reiner Parser für {explblock}-Kommentarmarkierungen. | – |

### extension/explayouts_core (2)

[Alle Muster von extension/explayouts_core](bereich-extension-explayouts-core.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsCoreTransferDiff` | class | `extension/explayouts_core/classes/explayoutscoretransferdiff.php:23` | Vergleicht die Dokumente eines Layout-Transferarchivs mit denselben Dokumenten, die aus der Zielinstallation exportiert wurden, und plant, was ein Import damit tut… | – |
| `expLayoutsCoreTransferFormat` | class | `extension/explayouts_core/classes/explayoutscoretransferformat.php:19` | Die Layout-Transferdatei: ein ZIP mit JSON-Dokumenten und einem Manifest. | – |

### extension/explayouts_relation_list_query (1)

[Alle Muster von extension/explayouts_relation_list_query](bereich-extension-explayouts-relation-list-query.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsRelationListQueryFactory` | class | `extension/explayouts_relation_list_query/classes/explayoutsrelationlistqueryfactory.php:2` | – | – |

### extension/explayouts_ui (2)

[Alle Muster von extension/explayouts_ui](bereich-extension-explayouts-ui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsUiRuleInput` | class | `extension/explayouts_ui/classes/explayoutsuiruleinput.php:9` | Die Ziele und Bedingungen einer Zuordnung, wie sie die Regelformulare senden (TargetType[]/TargetValue[], ConditionType[]/ConditionValue[]): nur die Typen, di… | – |
| `expLayoutsUiTemplateEditor` | class extends expLayoutsTemplateEditor | `extension/explayouts_ui/classes/explayoutsuitemplateeditor.php:17` | Der Template-Editor von explayouts_ui (explayouts_ui/template_editor), aufbauend auf expLayoutsTemplateEditor von explayouts (der entscheidet, welche Ordner … | – |

### extension/explayouts_ui_api (1)

[Alle Muster von extension/explayouts_ui_api](bereich-extension-explayouts-ui-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsUIApplicationApi` | class | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – | – |

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
| `expCommerceExport` | class | `extension/expservices/classes/commerce/expcommerceexport.php:11` | Encoder der Shop-Objekte in einfache Arrays, gemeinsam genutzt von den Commerce-Diensten. | – |
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

### extension/expsite_api (1)

[Alle Muster von extension/expsite_api](bereich-extension-expsite-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSiteApi` | class | `extension/expsite_api/classes/expsiteapi.php:2` | – | – |

### extension/expsite_installer (1)

[Alle Muster von extension/expsite_installer](bereich-extension-expsite-installer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSiteInstaller` | class | `extension/expsite_installer/classes/expsiteinstaller.php:6` | Super-Installer für stundenweise arbeitende Site-Entwickler: ein Befehl, der alles einrichtet. | – |

### extension/expui (1)

[Alle Muster von extension/expui](bereich-extension-expui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUIServerFunctions` | class | `extension/expui/classes/expuiserverfunctions.php:28` | – | extension/expui/settings/ezjscore.ini [ezjscServer_exp] Class |

### extension/exp_oe_tiptap (2)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOETiptapEditor` | class | `extension/exp_oe_tiptap/classes/expoetiptapeditor.php:23` | Tiptap als Engine der Editor-Registry von ezoe und was das Bearbeitungs-Template darüber wissen muss. | – |
| `expOETiptapServerFunctions` | class extends ezjscServerFunctions | `extension/exp_oe_tiptap/classes/expoetiptapserverfunctions.php:29` | ezjscore-Serverfunktionen des Tiptap-Editors (ezjscore.ini [ezjscServer_expoetiptap]). | extension/exp_oe_tiptap/settings/ezjscore.ini [ezjscServer_expoetiptap] Class |

### extension/ezautosave (1)

[Alle Muster von extension/ezautosave](bereich-extension-ezautosave.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscServerFunctionsAutosave` | class extends ezjscServerFunctions | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:16` | Diese Klasse verarbeitet AJAX-Aufrufe für die Funktion zum automatischen Speichern | extension/ezautosave/settings/ezjscore.ini [ezjscServer_ezautosave] Class |

### extension/ezfind (3)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFindQueryText` | class | `extension/ezfind/classes/ezfindquerytext.php:29` | eZFindQueryText | – |
| `eZFindServerCallFunctions` | class | `extension/ezfind/classes/ezfindservercallfunctions.php:7` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden | extension/ezfind/settings/ezjscore.ini [ezjscServer_ezfind] Class |
| `ezfSolrUtils` | class | `extension/ezfind/classes/ezfsolrutils.php:16` | Die Klasse ezfSolrUtils enthält verschiedene Methoden, um einen oder mehrere Solr-Indizes mit Funktionen höherer Ebene zu bearbeiten | – |

### extension/ezflow (4)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFlowOperations` | class | `extension/ezflow/classes/ezflowoperations.php:28` | – | – |
| `eZFlowPool` | class | `extension/ezflow/classes/ezflowpool.php:28` | – | – |
| `eZFlowServerCallFunctions` | class | `extension/ezflow/classes/ezflowservercallfunctions.php:7` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden | extension/ezflow/settings/ezjscore.ini [ezjscServer_ezflow] Class |
| `eZSquidCacheManager` | class | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – | extension/ezflow/settings/http.ini [HTTPCacheHandlers] Handlers[] |

### extension/ezformtoken (1)

[Alle Muster von extension/ezformtoken](bereich-extension-ezformtoken.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezxFormToken` | class | `extension/ezformtoken/event/ezxformtoken.php:23` | Diese Klasse lauscht auf interne Kernel-Ereignisse in Exponential, um Formulare mit sitzungsbezogenen Tokens zu validieren | – |

### extension/ezie (2)

[Alle Muster von extension/ezie](bereich-extension-ezie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZIEImageToolResize` | class extends eZIEImageAction | `extension/ezie/classes/image_tool_resize.php:11` | – | – |
| `eZIEImageToolWatermark` | class extends eZIEImageAction | `extension/ezie/classes/image_tool_watermark.php:11` | – | – |

### extension/ezjscore (3)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscServerFunctionsAjaxUploader` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php:20` | Diese Klasse verarbeitet AJAX-Aufrufe für die Ajax-Uploader-Komponente | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezajaxuploader] Class |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjsc] Class |
| `ezjscServerFunctionsNode` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsnode.php:36` | Die Klasse ezjscServerFunctionsNode definiert Fetch-Funktionen für Knoten | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjscnode] Class |

### extension/ezmultiupload (2)

[Alle Muster von extension/ezmultiupload](bereich-extension-ezmultiupload.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMultiuploadFunctionCollection` | class | `extension/ezmultiupload/classes/expmultiuploadfunctioncollection.php:10` | Die Template-Fetch-Funktionen von ezmultiupload (modules/ezmultiupload/function_definition.php). | – |
| `expMultiuploadService` | class | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | Die Serverseite der Upload-Seite: Prüfungen vor einem Upload (der Zielort, die Inhaltsklasse, zu der jede Datei wird, die Berechtigung zum Anlegen dafür… | – |

### extension/ezoe (3)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOEEditor` | class | `extension/ezoe/classes/expoeeditor.php:18` | Die Registry der Editor-Engines und die Helfer, die jeder Aufrufer teilt: die Templates, das Modul ezoe, die ezjscore-Funktionen und die expservices-Klasse alle… | – |
| `expOEUrlFetcher` | class | `extension/ezoe/classes/expoeurlfetcher.php:23` | Ruft eine Datei von einer URL ab, für den Upload-Reiter des Online-Editors („Von einer URL“). | – |
| `ezoeServerFunctions` | class extends ezjscServerFunctions | `extension/ezoe/classes/ezoeserverfunctions.php:35` | – | extension/ezoe/settings/ezjscore.ini [ezjscServer_ezoe] Class |

### extension/ezprestapi (1)

[Alle Muster von extension/ezprestapi](bereich-extension-ezprestapi.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRestContentModel` | class extends ezpRestModel | `extension/ezprestapi/classes/models/rest_content_model.php:13` | Modellklasse mit der Backend-Methode für den REST-Inhaltscontroller | – |

### extension/ezstarrating (1)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsrServerFunctions` | class extends ezjscServerFunctions | `extension/ezstarrating/classes/ezsrserverfunctions.php:36` | – | extension/ezstarrating/settings/ezjscore.ini [ezjscServer_ezstarrating] Class |

### extension/eztags (2)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscTags` | class extends ezjscServerFunctions | `extension/eztags/classes/ezjsctags.php:6` | Die Klasse ezjscTags implementiert eZ-JS-Core-Serverfunktionen für eztags | extension/eztags/settings/ezjscore.ini [ezjscServer_ezjsctags] Class |
| `eZTagsFunctionCollection` | class | `extension/eztags/modules/tags/eztagsfunctioncollection.php:9` | Die Klasse eZTagsFunctionCollection implementiert Fetch-Funktionen für eztags | – |

### extension/ezurlmultisite (1)

[Alle Muster von extension/ezurlmultisite](bereich-extension-ezurlmultisite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLMultisite` | class | `extension/ezurlmultisite/classes/ezurlmultisite.php:27` | Siteaccess-übergreifende Linkauflösung für den Template-Operator ezurl. | – |

### extension/git_manager (1)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `GitManagerLog` | class | `extension/git_manager/classes/gitmanagerlog.php:17` | Das Commit-Log von git_manager/dashboard, seitenweise: die Ansichtsparameter, die festlegen, welche Seite und welche Commits, die Blätterrechnung und die Arg… | – |

### extension/hcaptcha (1)

[Alle Muster von extension/hcaptcha](bereich-extension-hcaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `hCaptchaLibrary` | class | `extension/hcaptcha/classes/hcaptchalib.php:44` | Die Server-URLs von hCAPTCHA | – |

### extension/nxc_powercontent (1)

[Alle Muster von extension/nxc_powercontent](bereich-extension-nxc-powercontent.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentFunctionCollection` | class | `extension/nxc_powercontent/modules/content/ezcontentfunctioncollection.php:20` | – | – |

### extension/owsimpleoperator (1)

[Alle Muster von extension/owsimpleoperator](bereich-extension-owsimpleoperator.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `OWEzpAutoloader` | class extends ezpAutoloader | `extension/owsimpleoperator/autoloads/owezpautoloader.php:6` | – | – |

### extension/sevenx_authentication_2fa (6)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZsevenxUser2faUser` | class extends eZUser | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:29` | – | – |
| `sevenxAuthentication2faAttempts` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faattempts.php:27` | – | – |
| `sevenxAuthentication2faCrypto` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2facrypto.php:27` | – | – |
| `sevenxAuthentication2faEmail` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faemail.php:21` | – | – |
| `sevenxAuthentication2faRedirect` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faredirect.php:29` | – | – |
| `sevenxAuthentication2faTOTP` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fatotp.php:24` | – | – |

### extension/sevenx_dse (4)

[Alle Muster von extension/sevenx_dse](bereich-extension-sevenx-dse.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AdminNeo\Drivers` | class | `extension/sevenx_dse/adminneo/core/Drivers.php:5` | – | – |
| `AdminNeo\FileModes` | class | `extension/sevenx_dse/adminneo/core/FileModes.php:11` | Gibt den Dateien und Verzeichnissen, die AdminNeo anlegt (kompilierte Assets, Sperrdateien), ihre Rechte innerhalb der Grenzen, die der Exponential-Kernel setzt (EZP_FILE_MO… | – |
| `AdminNeo\Hash` | class | `extension/sevenx_dse/adminneo/core/Hash.php:5` | – | – |
| `AdminNeo\Random` | class | `extension/sevenx_dse/adminneo/core/Random.php:7` | – | – |

### extension/sevenx_themes_media (1)

[Alle Muster von extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxThemesMediaYouTube` | class | `extension/sevenx_themes_media/classes/sevenxthemesmediayoutube.php:22` | YouTube, ohne Google zu kontaktieren, bevor der Besucher das Abspielen wählt. | – |

### extension/syndication (4)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSyndicationInstaller` | class | `extension/syndication/classes/ezsyndicationinstaller.php:17` | – | – |
| `eZSyndicationJob` | class | `extension/syndication/classes/ezsyndicationjob.php:16` | – | – |
| `eZSyndicationRunner` | class | `extension/syndication/classes/ezsyndicationrunner.php:15` | – | – |
| `eZSyndicationUI` | class | `extension/syndication/classes/ezsyndicationui.php:15` | – | – |

### extension/xrowextract (18)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractArchive` | class | `extension/xrowextract/classes/xrowextractarchive.php:12` | Das Site-Archiv: die Objekte vieler Klassen unterhalb vieler Knoten, eine CSV-Datei pro Klasse, mit einem Manifest, gepackt als zip, tar.gz, tar.bz2, tar.xz, 7z o… | – |
| `XrowExtractCatalogue` | class | `extension/xrowextract/classes/xrowextractcatalogue.php:13` | Der Spaltenkatalog über die Klassenattribute selbst hinaus: | – |
| `XrowExtractColumns` | class | `extension/xrowextract/classes/xrowextractcolumns.php:8` | Die Spalten eines Exports und ihre Werte: die besonderen Spalten (keine Klassenattribute), die Attributspalten einer Klasse und ein Objekt als Zeile von CSV-Zel… | – |
| `XrowExtractCron` | class | `extension/xrowextract/classes/xrowextractcron.php:16` | Wann ein Zeitplan läuft: die einfachen Auswahlmöglichkeiten der Seite Schedules (stündlich zur Minute M, täglich um HH:MM, wöchentlich an einem Tag um HH:MM, monatlich an einem Tag um HH:… | – |
| `XrowExtractEntry` | class | `extension/xrowextract/classes/xrowextractentry.php:12` | Die Einstiegsparameter von xrowextract/csv: das Menü Advanced › Export des Inhaltsbaums öffnet das CSV-Formular vorausgefüllt für einen Knoten mit xrowextract/csv/(nod… | – |
| `XrowExtractFetchAlias` | class | `extension/xrowextract/classes/xrowextractfetchalias.php:18` | Benannte Fetches aus fetchalias.ini, nutzbar in der Ansicht extract und in der CLI: die Constant-Werte eines Alias "tree" / "list" / "tree_count" / "list_count" le… | – |
| `XrowExtractFunctionCollection` | class | `extension/xrowextract/classes/xrowextractfunctioncollection.php:4` | Die Template-Fetch-Funktionen des Moduls xrowextract (modules/xrowextract/function_definition.php). | – |
| `XrowExtractImport` | class | `extension/xrowextract/classes/xrowextractimport.php:22` | Die andere Richtung des Exports: liest eine CSV- oder JSON-Datei, die XrowExtractWriter geschrieben hat (beliebiger Spaltensatz, insbesondere „migration“), zurück in Inhal… | – |
| `XrowExtractJob` | class | `extension/xrowextract/classes/xrowextractjob.php:16` | Ein Exportjob im Hintergrund: ein Ordner pro Job unterhalb des var-Verzeichnisses (var//xrowextract-jobs//), mit job.json (Zustand), progress.json (geschrieben vom… | – |
| `XrowExtractManifest` | class | `extension/xrowextract/classes/xrowextractmanifest.php:20` | Das typisierte Spaltenmanifest eines Exports: was jede Spalte einer Datei ist (ihre ID, der Name in der Datei, der Datentyp, das gewählte Format und die Sprache),… | – |
| `XrowExtractNotifier` | class | `extension/xrowextract/classes/xrowextractnotifier.php:13` | Was der Eigentümer eines Zeitplans (und wen er sonst nennt) über einen Lauf erfährt: - E-Mail bei Fehlschlag (ein fehlgeschlagener oder übersprungener Lauf oder eine fehlgeschlagene Zustellung): immer an… | – |
| `XrowExtractPackage` | class | `extension/xrowextract/classes/xrowextractpackage.php:18` | Unterstützung für Inhaltspakete (.ezpkg) für xrowextract/package: Prüfen der Inhaltsklassen und Inhaltsobjekte eines Pakets, bevor etwas geschrieben wird (die… | – |
| `XrowExtractPreset` | class | `extension/xrowextract/classes/xrowextractpreset.php:25` | Eine gespeicherte Exportvoreinstellung: eine vollständige, benannte Definition eines CSV- oder Archivexports (Bereich, Knoten, Klasse oder Sets, Spalten, Sprachen, alle Filter, Sortierung, Aus… | – |
| `XrowExtractRequirements` | class | `extension/xrowextract/classes/xrowextractrequirements.php:19` | Was die Extension von PHP und dem Server braucht, an einer Stelle: jede Anforderung (eine PHP-Version, eine PHP-Extension, eine Funktion, die ein Hoster deaktivieren kann, ein… | – |
| `XrowExtractScheduler` | class | `extension/xrowextract/classes/xrowextractscheduler.php:15` | Führt Zeitpläne aus: findet die fälligen (der Cronjob-Teil, cronjobs/xrowextract.php, oder bin/php/schedule.php --cron), macht aus jedem einen gewöhnlichen Hintergr… | – |
| `XrowExtractSchema` | class | `extension/xrowextract/classes/xrowextractschema.php:11` | Die eigenen Tabellen der Extension (xrowextract_schedule, xrowextract_destination, xrowextract_history), verzögert und idempotent angelegt, sobald eine d… | – |
| `XrowExtractSecrets` | class | `extension/xrowextract/classes/xrowextractsecrets.php:15` | Zugangsdaten von Zielen im Ruhezustand: libsodium secretbox (XSalsa20-Poly1305) mit einem 32-Byte-Schlüssel in einer Schlüsseldatei außerhalb der Datenbank. | – |
| `XrowExtractUpload` | class | `extension/xrowextract/classes/xrowextractupload.php:22` | Ein Upload in Teilstücken: ein Ordner pro Upload (var//xrowextract/uploads//), mit meta.json (Eigentümer, ursprünglicher Name, Gesamtgröße, bisher empfangene Bytes, ob… | – |

### extension/xrowmetadata (7)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowMetaDataFunctions` | class | `extension/xrowmetadata/classes/xrowmetadatafunctions.php:3` | – | – |
| `xrowMetaDataLicense` | class | `extension/xrowmetadata/classes/xrowmetadatalicense.php:27` | Die Inhaltslizenzen von xrowmetadata: der Katalog, die Positivliste der Installation und welche Lizenz eine Seite hat. | – |
| `xrowMetaDataLicenseHeader` | class | `extension/xrowmetadata/classes/xrowmetadatalicenseheader.php:28` | Die Lizenz einer Seite als HTTP-Antwort-Header: | – |
| `xrowMetaDataLicensePicker` | class | `extension/xrowmetadata/classes/xrowmetadatalicensepicker.php:15` | Was die Bearbeitungsformulare brauchen, um eine Lizenz zu wählen: den Katalog (xrowMetaDataLicense) als Optionsgruppen für die Auswahl, die geerbte Lizenz (Klassenstandard… | – |
| `xrowMetaDataOpenGraphImage` | class | `extension/xrowmetadata/classes/xrowmetadataopengraphimage.php:16` | Das Open-Graph-Bild eines Metadaten-Attributs: welches Objekt es ist, ob dieses Objekt noch angezeigt werden kann, und das Bild davon, das og:image und twi… | – |
| `xrowSitemapTools` | class | `extension/xrowmetadata/classes/xrowsitemaptools.php:3` | – | – |
| `xrowSiteUpdater` | class | `extension/xrowmetadata/classes/xrowsiteupdater.php:3` | – | – |

### kernel/classes (79)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAdminPagination` | class | `kernel/classes/expadminpagination.php:19` | Eine zentrale Stelle für die Frage, wie viele Zeilen eine Liste in der Administrationsoberfläche anzeigt. | – |
| `expBenchmark` | class | `kernel/classes/expbenchmark.php:28` | Die Statistik, die Berichtsformate und die Regressionsprüfung von exp:benchmark. | – |
| `expBenchmarkHttp` | class | `kernel/classes/expbenchmarkhttp.php:20` | Die HTTP-Seite von exp:benchmark: ruft eine Liste von URLs ab, mehrere gleichzeitig, mit PHPs eigenem curl (curl_multi), sodass kein externes Programm nötig ist. | – |
| `expBenchmarkKernel` | class | `kernel/classes/expbenchmarkkernel.php:29` | Die In-Process-Seite von exp:benchmark: was der Kernel für die Teile einer Seite aufwendet, ohne HTTP, Webserver oder Antwort-Cache dazwischen. | – |
| `expBenchmarkMicro` | class | `kernel/classes/expbenchmarkmicro.php:33` | Der Mikromodus von exp:benchmark: reine PHP-Hot-Paths des Kernels, in diesem Prozess ohne HTTP, Webserver oder Datenbank gemessen, sodass er auf einem CI… | – |
| `expBookmarkPage` | class | `kernel/classes/expbookmarkpage.php:18` | Was die Lesezeichenseite (content/bookmark) zeigt, aus einfachen Arrays ermittelt: die Ordner mit ihren Zahlen, welche Lesezeichen ein Ordner, eine Suche und… | – |
| `expCacheWarm` | class | `kernel/classes/expcachewarm.php:28` | Hält den Antwort-Cache gefüllt, damit kein Besucher derjenige ist, der für das Rendern einer Seite bezahlt. | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Legt die Kollaborationsgruppen eines Benutzers an, benennt sie um, löscht sie und verschiebt Einträge zwischen ihnen. | – |
| `expCollaborationInbox` | class | `kernel/classes/expcollaborationinbox.php:15` | Die Kollaborationsübersicht als Posteingang: jedes Element des aktuellen Benutzers mit seinem Zustand (wartend, freigegeben, abgelehnt), seiner Rolle für den Benutzer (Freigebender oder… | – |
| `expContentDraftList` | class | `kernel/classes/expcontentdraftlist.php:20` | Was die Entwurfsseite (content/draft, "My drafts") auflistet, aus einfachen Arrays ermittelt: die Filter der Adresse (Übersetzung, Klasse, Alter, Suche, o… | – |
| `expContentHistoryList` | class | `kernel/classes/expcontenthistorylist.php:22` | Was die Versionsseite eines Objekts (content/history) auflistet, aus einfachen Arrays ermittelt: die Filter der Adresse (Status, Übersetzung, Ersteller,… | – |
| `expContentLanguageStates` | class | `kernel/classes/expcontentlanguagestates.php:26` | Der Zustand jeder Sprache eines Inhaltsobjekts für die Sprach-Badges von admin4 (design:parts/ language_state_badge.tpl): die Hauptsprache (die… | – |
| `expContentPendingList` | class | `kernel/classes/expcontentpendinglist.php:21` | Was die Seite der ausstehenden Elemente (content/pendinglist, "My pending items") auflistet, aus einfachen Arrays ermittelt: die Versionen, die der Benutzer zum Veröffentlichen gesendet hat, die a… | – |
| `expCronjobPause` | class | `kernel/classes/expcronjobpause.php:27` | Pausiert Cronjob-Teile: einen Teil oder alle, bis jemand sie fortsetzt. | – |
| `expErrorHeaderList` | class | `kernel/classes/experrorheaderlist.php:25` | Die Antwort-Header einer Fehlerseite, aus error.ini [HTTPError-]: | – |
| `expImageUploadValidator` | class | `kernel/classes/expimageuploadvalidator.php:35` | Entscheidet anhand des Inhalts statt des Namens, ob eine hochgeladene Datei ein Bild ist, das die Site akzeptiert. | – |
| `ExpInstallationDetailsOutputFilter` | class | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Ausgabefilter für den Installationsnamen. | – |
| `expMaintenance` | class | `kernel/classes/expmaintenance.php:29` | Wartungsmodus: solange var/maintenance.json existiert, wird jede Anfrage an die Front Controller mit einer Wartungsseite beantwortet (503, Retry-After, ne… | – |
| `expManagedConfiguration` | class | `kernel/classes/expmanagedconfiguration.php:25` | site.ini [ConfigurationSettings] ManagedByDeployment=enabled: Einstellungen, Templates und Code kommen nur aus dem Deployment, und die Administrationsoberfläche… | – |
| `expNodeQuickPick` | class | `kernel/classes/expnodequickpick.php:18` | Schnellzugriffsschaltflächen (doc/eng-us/features/6.0/quick-location-buttons.md): einige bekannte Stellen des Inhaltsbaums, gefunden über einen kurzen Schlüssel (Websites, M… | – |
| `expNotificationJob` | class | `kernel/classes/expnotificationjob.php:17` | Ein Benachrichtigungslauf für die Statusseite („Jetzt ausführen“), im Hintergrund. | – |
| `expNotificationService` | class | `kernel/classes/expnotificationservice.php:26` | Die eine Stelle, die weiß, wie das Benachrichtigungssystem ausgeführt und eingesehen wird: der Cronjob-Teil, die Befehle exp:notification:*, die Statusseite und ihre… | – |
| `expPackageConflictReport` | class | `kernel/classes/exppackageconflictreport.php:22` | Was die Installation eines Pakets auf dieser Installation vorfinden würde, nach Remote-ID, ohne etwas zu schreiben: für jedes Inhaltsobjekt des Pakets, ob ein… | – |
| `expPackageJson` | class | `kernel/classes/exppackagejson.php:34` | Die JSON-Serialisierung eines Pakets (doc/eng-us/bc/6.0/package-json-format.md). | – |
| `expPhar` | class | `kernel/classes/expphar.php:21` | Packt die Engine – die Klassendateien von Kernel und Bibliotheken – in ein Phar, damit die Laufzeit sie aus einem Archiv statt aus tausend ein… | – |
| `expPolicyOmitList` | class | `kernel/classes/exppolicyomitlist.php:25` | Die Module und Ansichten, die die Richtlinienprüfung überspringen: site.ini [RoleSettings] PolicyOmitList[] ohne die Einträge von [RoleSettings] PolicyOmitListExclud… | – |
| `expPreloadJob` | class | `kernel/classes/exppreloadjob.php:27` | Ein Cache-Vorlade-Lauf: im Hintergrund über Setup › Vorladen gestartet oder im Vordergrund über die Shell (bin/php/preload.php), mit einer Sperre, einem St… | – |
| `expProcessTools` | class | `kernel/classes/expprocesstools.php:22` | Findet die Programme, die ein Hintergrundjob braucht: die PHP-Kommandozeile und setsid. | – |
| `expSecretRule` | class | `kernel/classes/expsecretrule.php:29` | Die eine Regel, was ein Geheimnis ist, geteilt von allem, was Einstellungen und Werte anzeigt oder aufzeichnet: exp:ini und expIniEditor, das Audit-Log (über ex… | – |
| `expSetupLog` | class | `kernel/classes/expsetuplog.php:31` | var/log/setup.log: ein lesbarer Eintrag pro Installationslauf. | – |
| `expSetupWizardGuard` | class | `kernel/classes/expsetupwizardguard.php:26` | Entscheidet, was eine Anfrage an den Web-Setup-Assistenten (setup/init) tun darf, bevor der Assistent eine Seite zeigt oder den Wartungsmodus berührt. | – |
| `expSiteAccessOverview` | class | `kernel/classes/expsiteaccessoverview.php:31` | Jeder Siteaccess der Installation, wie eine Anfrage ihn erreicht und wo er liegt: der Siteaccess-Teil von Setup › Systeminformationen (#552). | – |
| `expSystemReportMask` | class | `kernel/classes/expsystemreportmask.php:29` | Was Setup › System information, sein Textbericht und exp:system:info von einem Wert zeigen dürfen. | – |
| `expTestLanguage` | class | `kernel/classes/exptestlanguage.php:17` | Testsprachen: Locales, die in share/locale mit [LanguageSettings] TestLanguage=true markiert sind (die mitgelieferte ist Lorem Ipsum, qlo-ZZ), und der Lorem-Ipsum-Tex… | – |
| `expUserLoginIntegrity` | class | `kernel/classes/expuserloginintegrity.php:35` | Ein Konto pro Login: findet die Logins, die mehr als eine ezuser-Zeile haben, legt den eindeutigen Index auf ezuser.login an, sobald es keine mehr gibt, und speichert ein Kont… | – |
| `expViewAccess` | class | `kernel/classes/expviewaccess.php:25` | Ob ein Benutzer eine Adresse dieses Siteaccess öffnen kann, so entschieden, wie der Kernel eine Anfrage entscheidet (ezpKernelWeb): die URL-Alias- (und Wildcard-)Übers… | – |
| `eZAudit` | class | `kernel/classes/ezaudit.php:20` | Die Audit-API von 4.x, beibehalten, damit jeder bestehende Aufruf weiter funktioniert: eZAudit::writeAudit( $name, $attributes ) wird von expAudit aufgezeichnet (kernel/classes/audit… | – |
| `eZCache` | class | `kernel/classes/ezcache.php:22` | eZCache ezcache.php | – |
| `eZCacheTrash` | class | `kernel/classes/ezcachetrash.php:38` | Entfernt ein Cache-Verzeichnis, indem es zuerst aus dem Weg umbenannt wird. | – |
| `eZClusterFileHandler` | class | `kernel/classes/ezclusterfilehandler.php:11` | – | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | `kernel/classes/ezcollaborationprofile.php:18` | – | – |
| `eZContentCache` | class | `kernel/classes/ezcontentcache.php:18` | – | – |
| `eZContentCacheManager` | class | `kernel/classes/ezcontentcachemanager.php:26` | – | – |
| `eZContentClassName` | class extends eZPersistentObject | `kernel/classes/ezcontentclassname.php:12` | – | – |
| `eZContentFunctions` | class | `kernel/classes/ezcontentfunctions.php:12` | – | – |
| `eZContentObjectTreeNodeOperations` | class | `kernel/classes/ezcontentobjecttreenodeoperations.php:22` | – | – |
| `eZDBGarbageCollector` | class | `kernel/classes/ezdbgarbagecollector.php:33` | – | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrulevalue.php:17` | – | – |
| `eZHTTPHeader` | class | `kernel/classes/ezhttpheader.php:18` | – | – |
| `eZNavigationPart` | class | `kernel/classes/eznavigationpart.php:32` | – | – |
| `eZNodeviewfunctions` | class | `kernel/classes/eznodeviewfunctions.php:18` | – | – |
| `eZPackageCatalog` | class | `kernel/classes/ezpackagecatalog.php:23` | Was die Paketseiten über die Pakete auf der Festplatte sagen: jedes Repository mit seinen Zahlen, eine Karte je Paket (Name, Version, Typ, Zusammenfassung, Maintain… | – |
| `eZPackageComparison` | class | `kernel/classes/ezpackagecomparison.php:36` | Vergleicht, was ein Paket enthält, mit dem, was die Site hat, für package/compare/. | – |
| `eZPackageComparisonDiff` | class | `kernel/classes/ezpackagecomparisondiff.php:20` | Ein Unterschied zweier Texte auf Wortebene, für den Paketvergleichs-Viewer (package/compare, eZPackageComparison): welche Wörter aus dem ers… | – |
| `eZPackageComparisonImport` | class | `kernel/classes/ezpackagecomparisonimport.php:34` | Importiert, was ein Paket für einzelne Einträge seines Vergleichs mit der Site mitbringt (eZPackageComparison, package/compare): ein neues Objekt wird angelegt, wo… | – |
| `eZPackageDownload` | class | `kernel/classes/ezpackagedownload.php:25` | Sendet eine Datei des Moduls package (ein exportiertes Archiv, eine Datei aus einem Paket) stückweise an den Browser. | – |
| `eZPackageFileBrowser` | class | `kernel/classes/ezpackagefilebrowser.php:17` | Nur-Lese-Unterstützung für den Paketinhalts-Browser auf package/view/full/: jede Datei, die das eigene Verzeichnis eines Pakets enthält, seitenweise und gefiltert, und o… | – |
| `eZPackageLicense` | class | `kernel/classes/ezpackagelicense.php:28` | – | – |
| `eZPackageRemovalPlan` | class | `kernel/classes/ezpackageremovalplan.php:20` | Was das Entfernen einer Auswahl von Paketen bewirken würde, bevor es geschieht: pro Paket sein Verzeichnis, seine Dateien und seine Größe, ob es installiert ist (sein Installationsdat… | – |
| `eZPackageRequestGuard` | class | `kernel/classes/ezpackagerequestguard.php:16` | Die Prüfungen, die jede Paketansicht auf das anwendet, was eine Anfrage benennt, bevor etwas von der Platte gelesen wird: ein Paketname, ein Repository, ein Ansichtsmodus und eine Datei… | – |
| `eZPackageUploadInspector` | class | `kernel/classes/ezpackageuploadinspector.php:31` | Prüft ein Paketarchiv, bevor etwas davon in ein Repository geschrieben wird. | – |
| `eZPackageVersion` | class | `kernel/classes/ezpackageversion.php:46` | – | – |
| `eZPendingActions` | class extends eZPersistentObject | `kernel/classes/ezpendingactions.php:12` | – | – |
| `eZPreferences` | class | `kernel/classes/ezpreferences.php:22` | – | – |
| `eZProductCategory` | class extends eZPersistentObject | `kernel/classes/ezproductcategory.php:18` | – | – |
| `eZRedirectManager` | class | `kernel/classes/ezredirectmanager.php:22` | – | – |
| `eZRSSExportItem` | class extends eZPersistentObject | `kernel/classes/ezrssexportitem.php:19` | – | – |
| `eZRunCronjobs` | class | `kernel/classes/ezruncronjobs.php:15` | – | – |
| `eZSearch` | class | `kernel/classes/ezsearch.php:19` | – | – |
| `eZSearchLog` | class | `kernel/classes/ezsearchlog.php:18` | – | – |
| `eZShippingManager` | class | `kernel/classes/ezshippingmanager.php:17` | – | – |
| `eZSiteAccess` | class | `kernel/classes/ezsiteaccess.php:17` | Stellt Funktionen für die Siteaccess-Verarbeitung bereit | – |
| `eZSiteData` | class extends eZPersistentObject | `kernel/classes/ezsitedata.php:21` | eZPersistentObject-Implementierung für die Tabelle ezsite_data. | – |
| `eZSSLZone` | class | `kernel/classes/ezsslzone.php:27` | – | – |
| `eZSubtreeCache` | class | `kernel/classes/ezsubtreecache.php:18` | – | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | `kernel/classes/eztipafriendcounter.php:18` | – | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | `kernel/classes/eztipafriendrequest.php:17` | – | – |
| `eZVATManager` | class | `kernel/classes/ezvatmanager.php:18` | – | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | `kernel/classes/ezworkflowgrouplink.php:18` | – | – |

### kernel/classes/apikey (2)

[Alle Muster von kernel/classes/apikey](bereich-kernel-classes-apikey.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expApiKeyFunctionCollection` | class | `kernel/classes/apikey/expapikeyfunctioncollection.php:13` | Die Fetch-Funktionen des Moduls apikey (kernel/apikey/function_definition.php). | – |
| `expApiKeySchema` | class | `kernel/classes/apikey/expapikeyschema.php:16` | Die Tabelle expapikey auf jeder Engine: ob sie existiert, und ihr Anlegen aus ihrer Definition in share/db_schema.dba über den Schema-Handler der Engine… | – |

### kernel/classes/audit (18)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAudit` | class | `kernel/classes/audit/expaudit.php:26` | Das Audit: was in Exponential geschieht, aufgezeichnet als hash-verkettete JSON-Zeilen pro Kanal (doc/eng-us/bc/6.0/audit.md, „The developer API (Z3)“). | – |
| `expAuditAlertEvaluator` | class | `kernel/classes/audit/alerts/expauditalertevaluator.php:23` | Wertet die Alarmregeln aus (doc/eng-us/bc/6.0/audit.md, „Alerts (F5)“) und löst system.audit.alert aus. | – |
| `expAuditBridge` | class | `kernel/classes/audit/bridge/expauditbridge.php:24` | Die ezpEvent-Brücke des Audits (doc/eng-us/bc/6.0/audit.md, „The ezpEvent bridge“, Z3): bereits vorhandene Kernel- und Extension-Ereignisse (ezpEvent::getInsta… | – |
| `expAuditConfig` | class | `kernel/classes/audit/expauditconfig.php:21` | Die Audit-Einstellungen (settings/audit.ini, doc/eng-us/bc/6.0/audit.md „The settings reference“), einmal in ein Array gelesen. | – |
| `expAuditConsole` | class | `kernel/classes/audit/console/expauditconsole.php:16` | Was die Ansichten des Moduls audit gemeinsam haben (doc/eng-us/bc/6.0/audit.md, "The console: module audit"): die Kanäle, die der aktuelle Benutzer lesen darf (audit/read und i… | – |
| `expAuditFormatRegistry` | class | `kernel/classes/audit/format/expauditformatregistry.php:16` | Die Archivformat-Registry: [AuditArchiveSettings] FormatHandlers[]= (RAD-Erhebungs-Registry „auditformats“). | – |
| `expAuditFunctionCollection` | class | `kernel/classes/audit/console/expauditfunctioncollection.php:22` | Die Fetch-Funktionen des Moduls audit (kernel/audit/function_definition.php; doc/eng-us/bc/6.0/audit.md, „Template operator and fetch“): | – |
| `expAuditGuard` | class | `kernel/classes/audit/expauditguard.php:32` | [AuditSettings] OnWriteFailure (doc/eng-us/bc/6.0/audit.md, "When the audit cannot write"): was mit einer Aktion geschieht, deren Audit-Datensatz nicht geschrieben werden kann. | – |
| `expAuditHook` | class | `kernel/classes/audit/hook/expaudithook.php:25` | Die Audit-Aufrufstellen des Kernels (doc/eng-us/bc/6.0/audit.md, "The event catalogue", Stufe 3): ein abgesicherter Weg, ein Ereignis von einem Hook-Punkt aus aufzuzeichnen, und die … | – |
| `expAuditIndexRow` | class | `kernel/classes/audit/index/expauditindexrow.php:12` | Ein Audit-Datensatz als Zeile von expaudit_event (doc/eng-us/bc/6.0/audit.md, „Tables“): die Spalten, nach denen die Konsole filtert, der Datensatz selbst (wie geschrieben, damit d… | – |
| `expAuditIndexSchema` | class | `kernel/classes/audit/index/expauditindexschema.php:18` | Die Audit-Indextabellen (doc/eng-us/bc/6.0/audit.md, "The index"): ihre Definition im generischen Schemaformat von share/db_schema.dba, ihr Anlegen auf ei… | – |
| `expAuditIndexSettings` | class | `kernel/classes/audit/index/expauditindexsettings.php:12` | Die Einstellungen des Audit-Index und der Konsole (audit.ini [AuditIndexSettings], [AuditConsoleSettings] und [AuditPrivacySettings] PseudonymiseAfterDays)… | – |
| `expAuditJson` | class | `kernel/classes/audit/expauditjson.php:16` | Das kanonische JSON von Audit-Datensätzen (doc/eng-us/bc/6.0/audit.md, "Canonical JSON"). | – |
| `expAuditMailRecipients` | class | `kernel/classes/audit/sinks/expauditmailrecipients.php:30` | Wer die Alarm-Mail des Audits erhält (doc/eng-us/bc/6.0/audit.md, "E-mail"; Entscheidung des Eigentümers 2026-10-02). | – |
| `expAuditReauth` | class | `kernel/classes/audit/console/expauditreauth.php:26` | Erneute Passworteingabe vor den Verwaltungsaktionen des Audits (doc/eng-us/bc/6.0/audit.md, Q9; [AuditConsoleSettings] ReauthForManage, ReauthMinutes). | – |
| `expAuditSinkRegistry` | class | `kernel/classes/audit/sinks/expauditsinkregistry.php:23` | Die Sink-Registry und die Zustellung (doc/eng-us/bc/6.0/audit.md, „Sinks (Q7)“, „The sink registry“). | – |
| `expAuditSourceRegistry` | class | `kernel/classes/audit/console/expauditsourceregistry.php:16` | Die Registry der Quellen der Audit-Konsole (expAuditSource), aus audit.ini [AuditSourceSettings] Sources[]: welche registriert sind, welche hier funktionieren,… | – |
| `expAuditTaxonomy` | class | `kernel/classes/audit/expaudittaxonomy.php:21` | Die Registry der Audit-Taxonomie (doc/eng-us/bc/6.0/audit.md, "Taxonomy", "Naming rules", "The event catalogue"). | – |

### kernel/classes/classicmenu (2)

[Alle Muster von kernel/classes/classicmenu](bereich-kernel-classes-classicmenu.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expClassicMenuSettings` | class | `kernel/classes/classicmenu/expclassicmenusettings.php:22` | Liest und schreibt die klassischen Menüeinstellungen eines Siteaccess: menu.ini [SelectedMenu] CurrentMenu, TopMenu und LeftMenu, die die Seitenlayouts de… | – |
| `expClassicMenuSiteAccessInspector` | class | `kernel/classes/classicmenu/expclassicmenusiteaccessinspector.php:28` | Gibt für einen Siteaccess an, ob seine Seiten die klassischen Menüeinstellungen verwenden (menu.ini [SelectedMenu]). | – |

### kernel/classes/contentcheck (9)

[Alle Muster von kernel/classes/contentcheck](bereich-kernel-classes-contentcheck.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentCheck` | class | `kernel/classes/contentcheck/expcontentcheck.php:19` | Was die beiden Inhaltsprüfungen gemeinsam haben (doc/eng-us/features/6.0/content-checks.md): ihre Einstellungen, wo sie antworten, die Richtlinienprüfung, die Formular-Tokens der … | – |
| `expContentCheckBrokenRelations` | class | `kernel/classes/contentcheck/expcontentcheckbrokenrelations.php:38` | Sichtbarer, veröffentlichter Inhalt, der auf etwas verweist, das ein Besucher nicht erreichen kann (doc/eng-us/features/6.0/content-checks.md, "Broken relations"). | – |
| `expContentCheckFileFix` | class | `kernel/classes/contentcheck/expcontentcheckfilefix.php:25` | Die Auflösungen der Liste fehlender Dateien (doc/eng-us/features/6.0/content-checks.md, "Resolving"): jede für eine Feldversion oder für viele, immer geplant f… | – |
| `expContentCheckFiles` | class | `kernel/classes/contentcheck/expcontentcheckfiles.php:26` | Fehlende Dateien und Bild-Aliase (doc/eng-us/features/6.0/content-checks.md, "Missing files"): die dateibasierten Felder der Inhalte unterhalb eines Knotens (Bilder, Da… | – |
| `expContentCheckQuarantine` | class | `kernel/classes/contentcheck/expcontentcheckquarantine.php:25` | Die Aktionen der Speicherprüfung (doc/eng-us/features/6.0/content-checks.md, "Quarantine"). | – |
| `expContentCheckScope` | class | `kernel/classes/contentcheck/expcontentcheckscope.php:24` | Der Startort der Inhaltsprüfungen (doc/eng-us/features/6.0/content-checks.md, "Where a check starts"): jeder Knoten, vom ganzen Baum (Knoten 1) bis zu einem… | – |
| `expContentCheckStorage` | class | `kernel/classes/contentcheck/expcontentcheckstorage.php:28` | Die Speicherprüfung (doc/eng-us/features/6.0/content-checks.md, "Storage files"): die Dateien unter var//storage, die kein Inhalt verwendet (verwaiste Dateien), und die Da… | – |
| `expContentCheckTrash` | class | `kernel/classes/contentcheck/expcontentchecktrash.php:23` | Verschiebt nicht referenzierte Objekte in den Papierkorb (doc/eng-us/features/6.0/content-checks.md, "Moving to the trash"): die Liste sendet die angehakten Objekte, eine Bestätigu… | – |
| `expContentCheckUnreferenced` | class | `kernel/classes/contentcheck/expcontentcheckunreferenced.php:31` | Die Assets unterhalb eines Knotens, die nichts verwendet (doc/eng-us/features/6.0/content-checks.md, "Unreferenced objects"): veröffentlichte Objekte einer Asset-Klasse ([Unrefer… | – |

### kernel/classes/contentfinder (2)

[Alle Muster von kernel/classes/contentfinder](bereich-kernel-classes-contentfinder.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentFinderAction` | class | `kernel/classes/contentfinder/expcontentfinderaction.php:33` | Die Aktionen des Content-Finders (doc/eng-us/features/6.0/content-finder.md, "Actions"): ein Objekt in den Papierkorb verschieben, es endgültig löschen, es entfernen aus… | – |
| `expContentFinderInput` | class | `kernel/classes/contentfinder/expcontentfinderinput.php:28` | Was der Content Finder (Modul contentfinder, doc/eng-us/features/6.0/content-finder.md) aus seinem einen Suchfeld macht: die Arten von ID oder Pfad, die der Text ent… | – |

### kernel/classes/contentjob (2)

[Alle Muster von kernel/classes/contentjob](bereich-kernel-classes-contentjob.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentJobLock` | class | `kernel/classes/contentjob/expcontentjoblock.php:30` | Teilbaumsperren der Inhaltsjobs. | – |
| `expContentJobStore` | class | `kernel/classes/contentjob/expcontentjobstore.php:18` | Der Speicher der Inhaltsjobs: eine JSON-Datei je Job in var//jobs/content/, atomar geschrieben (eine temporäre Datei im selben Verzeichnis, auf die Pla… | – |

### kernel/classes/datatypes (9)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expCountryGroups` | class | `kernel/classes/datatypes/ezcountry/expcountrygroups.php:24` | Die Länderliste, wie man daraus auswählt: zuerst die Gruppen aus settings/countrygroups.ini (standardmäßig eine Gruppe der meistgenutzten Länder, in gereihter R… | – |
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZISBNGroup` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngroup.php:24` | – | – |
| `eZISBNGroupRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngrouprange.php:26` | – | – |
| `eZISBNRegistrantRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbnregistrantrange.php:30` | – | – |
| `eZLDAPUser` | class extends eZUser | `kernel/classes/datatypes/ezuser/ezldapuser.php:20` | – | – |
| `eZURLObjectLink` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurlobjectlink.php:19` | – | – |
| `eZUserAccountKey` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuseraccountkey.php:19` | – | – |
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |

### kernel/classes/debugbar (4)

[Alle Muster von kernel/classes/debugbar](bereich-kernel-classes-debugbar.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expDebugBarIPList` | class | `kernel/classes/debugbar/expdebugbariplist.php:28` | Eine Zeile von DebugIPList[] ist ein Eintrag: | – |
| `expDebugBarServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/debugbar/expdebugbarserverfunctions.php:28` | Die ezjscore-Serverfunktionen der Exp-Debug-Leiste, registriert als [ezjscServer_expdebugbar] in extension/ezjscore/settings/ezjscore.ini: | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expdebugbar] Class |
| `expDebugBarSummary` | class | `kernel/classes/debugbar/expdebugbarsummary.php:20` | Sammelt, was die Anfrage gekostet hat, aus dem, was eZDebug bereits gemessen hat: Seitenzeit, SQL-Anweisungen und ihre Zeit (die *_query-Akkumulatoren der Datenb… | – |
| `expRenderStats` | class | `kernel/classes/debugbar/exprenderstats.php:19` | Sammelt für die gerade bediente Anfrage, was der Reiter Rendering der Debug-Leiste zeigt: den Controller (Modul, Ansicht, Skript und Ansichtsklasse), was der Con… | – |

### kernel/classes/help (10)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelp` | class | `kernel/classes/help/exphelp.php:11` | Die gemeinsamen Hilfsfunktionen des Hilfesystems (doc/eng-us/features/6.0/help-system.md): seine Einstellungen, ob es eingeschaltet ist, das Installationsverzeichnis, aus dem es liest, und die URLs v… | – |
| `expHelpContentSchema` | class | `kernel/classes/help/content/exphelpcontentschema.php:20` | Das Inhaltsmodell der als Inhalt gepflegten Hilfe (doc/eng-us/specifications/6.0/help-system.md, doc/eng-us/features/6.0/help-content.md): die acht help_*-Inhaltsk… | – |
| `expHelpFrontMatter` | class | `kernel/classes/help/exphelpfrontmatter.php:28` | Die Front Matter einer Hilfeseite (doc/eng-us/features/6.0/help-system.md, "Front matter"): ein Block ganz oben in einer Markdown-Datei zwischen zwei Zeilen aus "-… | – |
| `expHelpFunctionCollection` | class | `kernel/classes/help/exphelpfunctioncollection.php:10` | Die Fetch-Funktionen von exphelp (kernel/exphelp/function_definition.php). | – |
| `expHelpHighlighter` | class | `kernel/classes/help/exphelphighlighter.php:29` | Serverseitige Syntaxhervorhebung der Codeblöcke der Hilfeseiten (exphelp, doc/eng-us/features/6.0/help-system.md), ohne JavaScript oder eine externe Bi… | – |
| `expHelpLicense` | class | `kernel/classes/help/exphelplicense.php:17` | Die Lizenz der Dokumentation, die die Hilfe anzeigt (exphelp.ini [License], doc/eng-us/README-LICENSE.md). | – |
| `expHelpLinks` | class | `kernel/classes/help/exphelplinks.php:12` | Die Link- und Bildauflöser, die der Viewer expHelpMarkdownRenderer übergibt (doc/eng-us/features/6.0/help-system.md): ein relativer Link auf eine erlaubte Seite wird i… | – |
| `expHelpPdf` | class | `kernel/classes/help/exphelppdf.php:18` | Eine Hilfeseite als PDF-Datei (doc/eng-us/features/6.0/help-system.md, „Print and PDF“): die Ansicht exphelp/pdf/ und `./console exp:help pdf ` fragen hier an. | – |
| `expHelpPdfFontSubset` | class | `kernel/classes/help/exphelppdffontsubset.php:17` | Reduziert eine TrueType-Schrift auf die Glyphen, die ein PDF verwendet (doc/eng-us/features/6.0/help-system.md, „Print and PDF“). | – |
| `expHelpReadPolicyGrant` | class | `kernel/classes/help/exphelpreadpolicygrant.php:22` | Plant und vergibt die Richtlinie exphelp/read (bin/php/addexphelpreadpolicy.php): Lesen des Hilfesystems (doc/eng-us/features/6.0/help-system.md). | – |

### kernel/classes/ini (4)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniCommand` | class | `kernel/classes/ini/actions/expinicommand.php:15` | Der Befehl exp:ini ohne sein Skript: Zerlegen der Befehlszeile, die Hilfetexte, Finden der Aktion in der Registry, Ausführen mit einem expIniComm… | – |
| `expIniLocator` | class | `kernel/classes/ini/expinilocator.php:25` | Jede Datei, die eine Variable setzt, in der Reihenfolge, in der eZINI sie für einen Siteaccess lädt, und der wirksame Wert. | – |
| `expSettingsPage` | class | `kernel/classes/ini/expsettingspage.php:18` | Die Zeilen, Zahlen, Suchen und Vergleiche der Einstellungsseiten und was auf einen Schreibvorgang folgt. | – |
| `expSettingsTarget` | class | `kernel/classes/ini/expsettingstarget.php:19` | Die Prüfungen zwischen einer Anfrage und einer Einstellungsdatei. | – |

### kernel/classes/logviewer (5)

[Alle Muster von kernel/classes/logviewer](bereich-kernel-classes-logviewer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLogActions` | class | `kernel/classes/logviewer/explogactions.php:13` | Die Aktionen des Log-Viewers neben dem Lesen (doc/eng-us/features/6.0/log-viewer.md, "Actions"): einen Bereich herunterladen, jetzt rotieren, eine Datei leeren, ein Archiv entf… | – |
| `expLogBundle` | class | `kernel/classes/logviewer/explogbundle.php:17` | „Alle Logs herunterladen“ (Setup › Logs verwalten; doc/eng-us/features/6.0/log-viewer.md, „Downloading every log“): ein Archiv aller Logs, die der Viewer auflistet, option… | – |
| `expLogHealth` | class | `kernel/classes/logviewer/exploghealth.php:17` | Der Zustand der Logs (Setup › Log files › Health, setup/logshealth; doc/eng-us/features/6.0/log-viewer.md, "Health"): der Plattenplatz, den die Logs und ihre Archive… | – |
| `expLogParser` | class | `kernel/classes/logviewer/explogparser.php:25` | Liest die Zeilen einer Logdatei als Einträge (doc/eng-us/features/6.0/log-viewer.md, "The line format"). | – |
| `expLogPermalink` | class | `kernel/classes/logviewer/explogpermalink.php:23` | Die Permalinks des Log-Viewers (doc/eng-us/features/6.0/log-viewer.md, „Permalinks“): jede Ansicht eines Logs als Pfad ohne Query-String, kurz genug, u… | – |

### kernel/classes/mail (1)

[Alle Muster von kernel/classes/mail](bereich-kernel-classes-mail.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailTemplateBody` | class | `kernel/classes/mail/expmailtemplatebody.php:16` | Der Inhalt einer aus einem Template erzeugten Mail: die Ausgabe des Templates und was das Template im Root-Scope gesetzt hat. | – |

### kernel/classes/mailpreferences (10)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailAddressChange` | class | `kernel/classes/mailpreferences/expmailaddresschange.php:20` | Eine neue E-Mail-Adresse für ein Konto wird erst wirksam, wenn sie bestätigt ist (Double-Opt-in, mailpreferences.ini [EmailChangeSettings] Confirm=enabled,… | – |
| `expMailBounceReader` | class | `kernel/classes/mailpreferences/expmailbouncereader.php:36` | Liest das Postfach, in dem Bounces und Beschwerden eingehen (mailpreferences.ini [BounceSettings], gesetzt in settings/override/mailpreferences.ini.append.ph… | – |
| `expMailCategoryRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailcategoryrow.php:15` | Eine Kategorie, die im Kategorienmanager der Administrationsoberfläche angelegt oder geändert wurde (Tabelle expmail_category). | – |
| `expMailGate` | class | `kernel/classes/mailpreferences/expmailgate.php:34` | Das zentrale Mail-Gate. | – |
| `expMailPreferenceRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailpreferencerow.php:14` | Eine gespeicherte Präferenz (Tabelle expmail_preference): ein Empfänger (recipient_key 'u:' oder 'a:'), eine Kategorie, ihr Zustand (on, off, pending) und die Häufigkeit. | – |
| `expMailPreferencesService` | class | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | Die Abläufe rund um die Präferenzen: die Double-Opt-in-Mail und ihre Bestätigung, die Abmeldung mit einem Klick, „Link zusenden“ für Personen ohne Login,… | settings/mailpreferences.ini [GateSettings] EssentialSenders[] |
| `expMailSecret` | class | `kernel/classes/mailpreferences/expmailsecret.php:20` | Das Site-Geheimnis des E-Mail-Präferenzsystems: der Schlüssel der Links in Mails (expMailToken) und das Salt der Adress-Hashes (expMailSuppression,… | – |
| `expMailSenderDetails` | class | `kernel/classes/mailpreferences/expmailsenderdetails.php:27` | Wer die optionale Mail sendet: der Name der Organisation und die Postanschrift, die das Mail-Gate in die Fußzeile jeder optionalen Mail setzt (CAN-SPAM, CASL und… | – |
| `expMailSuppression` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailsuppression.php:22` | Die Sperrliste (Tabelle expmail_suppression): Adressen, an die keine optionale Mail geht (Hard Bounces, Beschwerden, „von allem abmelden“, rechtliche Aufford… | – |
| `expMailToken` | class | `kernel/classes/mailpreferences/expmailtoken.php:24` | Die Links in Mails, die ohne Login funktionieren: Abmelden (ein Klick, RFC 8058), Verwalten (die Präferenzseite) und Bestätigen (ein Double-Opt-in). | – |

### kernel/classes/notification (3)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZNotificationCollectionItem` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – | – |
| `eZNotificationEventFilter` | class | `kernel/classes/notification/eznotificationeventfilter.php:17` | – | – |
| `eZNotificationSchedule` | class | `kernel/classes/notification/eznotificationschedule.php:18` | – | – |

### kernel/classes/pdfexport (4)

[Alle Muster von kernel/classes/pdfexport](bereich-kernel-classes-pdfexport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expPDFExportFile` | class | `kernel/classes/pdfexport/exppdfexportfile.php:19` | Die Datei eines PDF-Exports, die einmal erzeugt wird: ihr Name, wo sie aufbewahrt wird, was über sie bekannt ist, ihr Versand und ihr Entfernen. | – |
| `expPDFExportForm` | class | `kernel/classes/pdfexport/exppdfexportform.php:19` | Liest und prüft das Formular von pdf/edit. | – |
| `expPDFExportGenerator` | class | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Erzeugt das PDF eines Exports: in seine gespeicherte Datei oder in eine temporäre Datei, die dann gesendet und entfernt wird. | – |
| `expPDFExportInfo` | class | `kernel/classes/pdfexport/exppdfexportinfo.php:19` | Was die Seiten des PDF-Exports (pdf/list und ihre Löschbestätigung) über einen Export über seine Zeile hinaus sagen: wie er erzeugt wird, ob sein Quellknoten noc… | – |

### kernel/classes/requestlog (1)

[Alle Muster von kernel/classes/requestlog](bereich-kernel-classes-requestlog.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRequestLog` | class | `kernel/classes/requestlog/exprequestlog.php:32` | Das Anfrage-Log: höchstens eine Zeile je Web-Anfrage für eine Seite, die nicht existiert (404.log), mit dem Grund, den der Kernel gefunden hat, und die Übergabe an d… | – |

### kernel/classes/role (1)

[Alle Muster von kernel/classes/role](bereich-kernel-classes-role.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRolePage` | class | `kernel/classes/role/exprolepage.php:19` | Was die Rollenseiten (role/list, role/view, role/edit) neben der Rolle selbst ermitteln: die Zeilen der Rollenliste mit ihrer Suche und Reihenfolge, was e… | – |

### kernel/classes/subitems (4)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsColumnHandlers` | class | `kernel/classes/subitems/columns/expsubitemscolumnhandlers.php:16` | Beispiel-Handler=-Spalten der Unterelementliste der Administrationsoberfläche: einfache statische Methoden, die ein INI-Block als Handler=expSubitemsColumnHandlers:: nennt. | – |
| `expSubitemsCSVExport` | class | `kernel/classes/subitems/expsubitemscsvexport.php:14` | Das CSV der Admin-Unterelementliste (content/subitemsexport/). | – |
| `expSubitemsPreference` | class | `kernel/classes/subitems/expsubitemspreference.php:20` | Die benutzerspezifische Auswahl der Unterelementliste der Admin: sichtbare Spalten in Reihenfolge und das gewählte Preset je Navigationsteil, die Seitengröße und die eigenen … | – |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | Die ezjscore-Serverfunktionen der Unterelementliste der Administrationsoberfläche („Tabellenoptionen“), registriert als [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expsubitems] Class |

### kernel/classes/uiswitch (1)

[Alle Muster von kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUISwitch` | class | `kernel/classes/uiswitch/expuiswitch.php:28` | Das UI-Umschalt-Framework: eine Registry von Anbietern (uiswitch.ini [UISwitchSettings] ProviderList[]), die in der Sitzung gespeicherten Auswahlen des Benutzers und die ea… | – |

### kernel/classes/user (5)

[Alle Muster von kernel/classes/user](bereich-kernel-classes-user.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRegistrationAdminAction` | class | `kernel/classes/user/expregistrationadminaction.php:23` | Die signierten Links der Administrator-Mail „Neuer Benutzer registriert“ (design:user/registrationfeedback.tpl) und die Aktionen dahinter (user/registrat… | – |
| `expUnactivatedUsers` | class | `kernel/classes/user/expunactivatedusers.php:22` | Die Benutzer, die sich registriert und ihr Konto nie aktiviert haben, für die Seite user/unactivated: wie viele es sind, eine Seite davon, gesucht nach Login, E-Ma… | – |
| `expUserAccountStatus` | class | `kernel/classes/user/expuseraccountstatus.php:21` | Der Kontostatus, den die Administrationsoberfläche an Benutzerobjekten markiert: aktiviert, deaktiviert (Setup › Users, ezuser_setting.is_enabled = 0) oder gesperrt (mehr fehlgeschlagene… | – |
| `expUserActivation` | class | `kernel/classes/user/expuseractivation.php:25` | Was auf die Aktivierung eines Kontos folgt, für user/activate (der Link, den der Benutzer anklickt) und die Schaltfläche Activate von user/unactivated. | – |
| `expUserDeactivation` | class | `kernel/classes/user/expuserdeactivation.php:36` | „Deaktivieren und verschieben“ und „Reaktivieren und zurückverschieben“: statt ein Benutzerkonto zu entfernen (Benutzer kommen nie in den Papierkorb, daher lässt sich das Entfernen nicht rückgä… | – |

### kernel/common (2)

[Alle Muster von kernel/common](bereich-kernel-common.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpI18n` | class | `kernel/common/ezpi18n.php:12` | – | – |
| `eZTemplatesStatisticsReporter` | class | `kernel/common/eztemplatesstatisticsreporter.php:19` | – | – |

### kernel/modules (26)

[Alle Muster von kernel/modules](bereich-kernel-modules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expcontentextensionwizard.php:27` | Der Assistent für Inhalts-Extensions: was Autoren, Redakteure und Übersetzer sehen. | – |
| `expCronjobRunner` | class | `kernel/setup/expcronjobrunner.php:38` | Listet die Cronjob-Teile auf, die diese Installation definiert, und startet einen davon aus der Administrationsoberfläche, sodass er nach dem Ende der Anfrage weiterläuft… | – |
| `expDatatypeWizard` | class extends expExtensionWizard | `kernel/setup/expdatatypewizard.php:22` | Der Datentyp-Assistent: baut eine funktionierende Extension für einen Inhaltsdatentyp. | – |
| `expDesignExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expdesignextensionwizard.php:21` | Erstellt eine Design-Extension: die Verzeichnisse, die Einstellungen und die Templates, die ein funktionierendes Design braucht, bereit zum Einschalten. | – |
| `expHandlerWizard` | class extends expExtensionWizard | `kernel/setup/exphandlerwizard.php:29` | Erstellt einen Handler: eine Klasse, die durch eine INI-Einstellung benannt wird und die der Kernel anstelle seiner eigenen lädt. | – |
| `expKernelOverrideWizard` | class extends expExtensionWizard | `kernel/setup/expkerneloverridewizard.php:24` | Der Kernel-Überschreibungsassistent: eine Kernel-Klasse vollständig ersetzen. | – |
| `expModuleExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expmoduleextensionwizard.php:31` | Erstellt eine Modul-Extension über bereits vorhandene Tabellen. | – |
| `expModuleWizard` | class extends expExtensionWizard | `kernel/setup/expmodulewizard.php:28` | Der Modul-Assistent: ein Modul, seine Ansichten und die Richtlinien, die sie erreichen. | – |
| `expRADCatalogue` | class | `kernel/setup/expradcatalogue.php:29` | Jeder Punkt, an dem dieses System erweitert werden kann, und was dazu nötig ist. | – |
| `expRADHealth` | class | `kernel/setup/expradhealth.php:23` | Was in dieser Installation konfiguriert ist und nicht funktionieren kann. | – |
| `expRADSurvey` | class | `kernel/setup/expradsurvey.php:26` | Eine Live-Erhebung, an welchen Stellen diese Installation erweitert werden kann. | – |
| `expSettingsExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expsettingsextensionwizard.php:26` | Der Einstellungs-Extension-Assistent: eine Extension, die überwiegend aus INI-Dateien besteht. | – |
| `expTemplateExtensionWizard` | class extends expExtensionWizard | `kernel/setup/exptemplateextensionwizard.php:25` | Der Template-Extension-Assistent: alles, was man einem Template beibringen kann aufzurufen. | – |
| `expWorkflowEventWizard` | class extends expExtensionWizard | `kernel/setup/expworkfloweventwizard.php:27` | Erstellt einen Workflow-Ereignistyp: einen Schritt, den ein Workflow ausführen kann, wenn etwas veröffentlicht, verschoben, entfernt, registriert oder gekauft wird. | – |
| `eZCheckClasses` | class | `kernel/setup/expclassloadcheck.php:26` | Die Teile der Prüfung, die es lohnt, an einer Stelle zu haben. | – |
| `eZClassFunctions` | class | `kernel/class/ezclassfunctions.php:14` | – | – |
| `eZInfocollectorFunctionCollection` | class | `kernel/infocollector/ezinfocollectorfunctioncollection.php:20` | – | – |
| `eZMultiEdit` | class | `kernel/content/multiedit_functions.php:16` | Die Arbeitsteile von content/multiedit. | – |
| `eZMultiPriceData` | class extends eZPersistentObject | `kernel/shop/classes/ezmultipricedata.php:12` | – | – |
| `eZRSSEditFunction` | class | `kernel/rss/edit_functions.php:14` | – | – |
| `eZRSSFunctionCollection` | class | `kernel/rss/ezrssfunctioncollection.php:14` | – | – |
| `eZRSSListPager` | class | `kernel/rss/ezrsslistpager.php:24` | Ermittelt eine Seite einer Liste und die Links, die die anderen Seiten erreichen. | – |
| `eZShopFunctions` | class | `kernel/shop/classes/ezshopfunctions.php:12` | – | – |
| `eZShopReceipt` | class | `kernel/shop/classes/ezshopreceipt.php:35` | Eine dauerhafte, als Lesezeichen speicherbare Adresse für eine aufgegebene Bestellung: ihr Beleg. | – |
| `eZUserOperationCollection` | class | `kernel/user/ezuseroperationcollection.php:20` | – | – |
| `eZWorkflowFunctions` | class | `kernel/workflow/ezworkflowfunctions.php:14` | – | – |

### kernel/private (18)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expApiKeyRest` | class | `kernel/private/rest/classes/auth/apikey_rest.php:24` | Wie die REST-Schicht einen persönlichen API-Schlüssel auflöst (doc/eng-us/guides/api-keys.md, „For developers“). | – |
| `expContentAccessReport` | class | `kernel/private/classes/expcontentaccessreport.php:23` | Beantwortet „darf dieser Benutzer dies mit diesem Objekt oder Knoten tun, und wenn nicht, welche Einschränkung verweigert es“: die Zugriffsprüfungen des Kernels für einen anderen Ben… | – |
| `expContentSecurityPolicy` | class | `kernel/private/classes/expcontentsecuritypolicy.php:39` | Die Content Security Policy einer Seite, aus site.ini [ContentSecurityPolicy]. | – |
| `expOAuthAuthorizePolicy` | class | `kernel/private/rest/classes/oauth/authorize_policy.php:21` | Entscheidet, ob ein angemeldeter Benutzer einen OAuth-Client in oauth/authorize autorisieren darf. | – |
| `expOAuthAuthorizePolicyGrant` | class | `kernel/private/rest/classes/oauth/authorize_policy_grant.php:26` | Plant und vergibt beim Upgrade die Richtlinie oauth/authorize (bin/php/addoauthauthorizepolicy.php). | – |
| `expRestContentPermission` | class | `kernel/private/rest/classes/auth/content_permission.php:36` | Die Rechte des aktuellen Benutzers an den Inhalten, die ein REST-Aufruf liest oder schreibt, geprüft so, wie das Modul content sie prüft (doc/eng-us/guides/api-keys.md, „Permis… | – |
| `ezpAttributeOperatorManager` | class | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – | – |
| `ezpContentLimitation` | class | `kernel/private/classes/ezpcontentlimitation.php:26` | Fragt den Handler, den eine Extension für eine Inhaltsrichtlinien-Einschränkung registriert hat, die der Kernel nicht kennt (site.ini [RoleSettings] LimitationHandlers[]=, siehe… | – |
| `ezpContentPublishingQueue` | class | `kernel/private/classes/ezpcontentpublishingqueue.php:19` | Diese Klasse verwaltet die Warteschlange zum Veröffentlichen von Inhalten. | settings/content.ini [PublishingSettings] AsynchronousPublishingQueueReader |
| `ezpContentRepository` | class | `kernel/private/api/content/repository.php:16` | Diese Klasse verarbeitet Abfragen des Inhalts-Repositorys | – |
| `ezpFormTokenRefusal` | class | `kernel/private/classes/ezpformtokenrefusal.php:27` | Macht aus einer ezpFormTokenException -- ein POST, dessen Formular-Token fehlte oder falsch war -- eine Ablehnung: HTTP 403, Cache-Control: no-store, die Sicherheits-Hea… | – |
| `ezpOauthUtility` | class extends ezpRestModel | `kernel/private/rest/classes/oauth/utility.php:17` | Funktionalität für die Arbeit mit Draft 10 der OAuth2-Spezifikation. | – |
| `ezpRestContentRenderer` | class | `kernel/private/rest/classes/content_renderer.php:12` | – | – |
| `ezpRestDbConfig` | class | `kernel/private/rest/classes/lazy.php:12` | – | – |
| `ezpRestProvider` | class | `kernel/private/rest/classes/rest_provider.php:12` | – | – |
| `ezpRestRoutesCacheClear` | class | `kernel/private/rest/classes/cache/clear_routes.php:15` | Handler zum Leeren des Caches. | settings/site.ini [Cache_restRoutes] class; settings/site.ini [Cache_restRoutes] purgeClass |
| `ezpSiteAccessMenu` | class | `kernel/private/classes/ezpsiteaccessmenu.php:48` | Das Site-Menü der Administrationsoberfläche: die Siteaccesses dieser Installation mit ihren Startseitenadressen, für die Globus-Schaltfläche im Kopf von admin4. | – |
| `ezpSiteAccessURL` | class | `kernel/private/classes/ezpsiteaccessurl.php:36` | Die Adresse der Startseite eines Siteaccess, ermittelt aus den Siteaccess-Einstellungen und der Anfrage, auf der der Besucher ist: der Admin-Link "Site öffnen"… | – |

### kernel/private/classes/clusterfilehandlers (1)

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDFSFileHandlerBackendFactory` | class | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandlerbackendfactory.php:14` | Instanziiert das DFSBackend anhand der INI-Einstellungen | – |

### kernel/private/classes/commands (1)

[Alle Muster von kernel/private/classes/commands](bereich-kernel-private-classes-commands.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CommandLineArguments` | class | `kernel/private/classes/commands/ezconvertdbcharset.php:31` | Klasse zum Speichern einiger Befehlszeilenargumente | – |

### kernel/private/classes/httpcache (1)

[Alle Muster von kernel/private/classes/httpcache](bereich-kernel-private-classes-httpcache.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpHttpCacheListener` | class | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | Die Kernel-Seite des rollenbewussten HTTP-Caches: speichert gerenderte Inhaltsansichten, damit der frühe Ausstieg und der Elternprozess von Velocity sie ausliefern, und macht aus den… | – |

### kernel/private/classes/requestrules (1)

[Alle Muster von kernel/private/classes/requestrules](bereich-kernel-private-classes-requestrules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRequestRuleKernel` | class | `kernel/private/classes/requestrules/ezprequestrulekernel.php:18` | Wo die Anfrageregeln auf den Kernel treffen: die Fakten einer echten Anfrage (context()) und die Antwort für eine Entscheidung (moduleResult()). | – |

### kernel/private/classes/services (8)

[Alle Muster von kernel/private/classes/services](bereich-kernel-private-classes-services.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Service\ContentJobDetails` | class | `kernel/private/classes/services/contentjobdetails.php:17` | Was die Seiten der Inhaltsjobs über einen Job über seinen Fortschritt hinaus zeigen: wer ihn gestartet hat (und in wessen Auftrag), die Zeiten, die Dauer und eine Schätzung der… | – |
| `Exponential\Service\DraftsCleanup` | class | `kernel/private/classes/services/draftscleanup.php:22` | Entfernen von Entwürfen, die älter als ihre konfigurierte Lebensdauer sind: ein Dienst für die beiden Cronjob-Teile, die das mit demselben Code und unterschiedlichen Einstellungen taten, cr… | – |
| `Exponential\Service\MailPreferencesPage` | class | `kernel/private/classes/services/mailpreferencespage.php:27` | Was die Ansichten des Moduls mailpreferences gemeinsam haben: die Einstellungsseite (angemeldet, über einen persönlichen Link oder durch einen Administrator für einen Benutzer), die Boxen auf… | – |
| `Exponential\Service\ReverseRelationCheck` | class | `kernel/private/classes/services/reverserelationcheck.php:22` | Welche anderen Objekte auf den Inhalt verweisen, den ein Entfernen oder Verbergen gleich wegnimmt: die umgekehrten Relationen (ezcontentobject_link) der Objekte, die… | – |
| `Exponential\Service\SessionGarbageCollector` | class | `kernel/private/classes/services/sessiongarbagecollector.php:20` | Entfernen abgelaufener Sitzungen: ein Dienst für die Sitzungsansicht (setup/session, "Remove timed out sessions"), den Befehl (bin/php/ezsessiongc.php) und… | – |
| `Exponential\Service\Trash` | class | `kernel/private/classes/services/trash.php:23` | Leeren des Papierkorbs: ein Dienst für die Papierkorb-Ansicht (content/trash), den Befehl (bin/php/trashpurge.php) und den Cronjob-Teil (cronjobs/trashpurge.ph… | – |
| `Exponential\Service\TrashList` | class | `kernel/private/classes/services/trashlist.php:27` | Was die Papierkorbansicht (content/trash) über jedes Element, ihre Filter und ihre Zusammenfassungszeile zeigt. | – |
| `Exponential\Service\TrashRecord` | class | `kernel/private/classes/services/trashrecord.php:30` | Die Datei, in der festgehalten wurde, wer ein Objekt in den Papierkorb verschoben hat und von wo, bevor die Papierkorbzeilen die Spalten trashed_by und trashed_via hatten. | – |

### lib (1)

[Alle Muster von lib](bereich-lib.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ExponentialSDK` | class | `lib/version.php:21` | – | – |

### lib/ezdb (2)

[Alle Muster von lib/ezdb](bereich-lib-ezdb.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDBQueryCache` | class | `lib/ezdb/classes/ezdbquerycache.php:37` | Ein Ergebnis-Cache für die SQL-Treiber (MySQL/MariaDB, PostgreSQL, SQLite): die Zeilen, die ein SELECT geliefert hat, werden erneut geliefert, bis ein Schreibvorgang in eine der Tabellen… | – |
| `eZDBTool` | class | `lib/ezdb/classes/ezdbtool.php:18` | – | – |

### lib/ezdbschema (2)

[Alle Muster von lib/ezdbschema](bereich-lib-ezdbschema.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDbSchema` | class | `lib/ezdbschema/classes/ezdbschema.php:19` | – | – |
| `eZDbSchemaChecker` | class | `lib/ezdbschema/classes/ezdbschemachecker.php:19` | – | – |

### lib/ezfile (4)

[Alle Muster von lib/ezfile](bereich-lib-ezfile.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLogRotator` | class | `lib/ezfile/classes/explogrotator.php:28` | Die einzige Implementierung der Log-Rotation im Kernel (doc/eng-us/features/6.0/log-rotation.md). | – |
| `eZDir` | class | `lib/ezfile/classes/ezdir.php:17` | – | – |
| `eZFile` | class | `lib/ezfile/classes/ezfile.php:18` | – | – |
| `eZLog` | class | `lib/ezfile/classes/ezlog.php:21` | – | – |

### lib/ezi18n (3)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCharsetInfo` | class | `lib/ezi18n/classes/ezcharsetinfo.php:25` | – | – |
| `eZTranslationCache` | class | `lib/ezi18n/classes/eztranslationcache.php:18` | – | – |
| `eZUTF8Codec` | class | `lib/ezi18n/classes/ezutf8codec.php:22` | – | – |

### lib/ezimage (1)

[Alle Muster von lib/ezimage](bereich-lib-ezimage.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSvgSanitizer` | class | `lib/ezimage/classes/expsvgsanitizer.php:51` | Macht ein SVG-Bild per Erlaubnisliste sicher, damit es vom eigenen Ursprung der Site gespeichert und ausgeliefert werden kann. | – |

### lib/ezlocale (1)

[Alle Muster von lib/ezlocale](bereich-lib-ezlocale.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDateUtils` | class | `lib/ezlocale/classes/ezdateutils.php:18` | – | – |

### lib/eztemplate (5)

[Alle Muster von lib/eztemplate](bereich-lib-eztemplate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTemplateCacheBlock` | class | `lib/eztemplate/classes/eztemplatecacheblock.php:18` | – | – |
| `eZTemplateCompiler` | class | `lib/eztemplate/classes/eztemplatecompiler.php:49` | – | – |
| `eZTemplateNodeTool` | class | `lib/eztemplate/classes/eztemplatenodetool.php:19` | – | – |
| `eZTemplateOptimizer` | class | `lib/eztemplate/classes/eztemplateoptimizer.php:18` | – | – |
| `eZTemplateTreeCache` | class | `lib/eztemplate/classes/eztemplatetreecache.php:18` | – | – |

### lib/ezutils (14)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expDebugBarReport` | class | `lib/ezutils/classes/expdebugbarreport.php:30` | Der HTML-Debug-Bericht als "Exp Debug"-Leiste: eine am unteren Fensterrand fixierte Leiste mit einer Live-Zusammenfassung der Anfrage und darüber ein Panel mit Reitern… | – |
| `expInfo` | class | `lib/ezutils/classes/expinfo.php:21` | – | – |
| `eZDateTimeValidator` | class extends eZInputValidator | `lib/ezutils/classes/ezdatetimevalidator.php:18` | – | – |
| `eZDebugSetting` | class | `lib/ezutils/classes/ezdebugsetting.php:39` | Stellt bedingte Debug-Ausgabe bereit | – |
| `eZExecution` | class | `lib/ezutils/classes/ezexecution.php:34` | – | – |
| `eZExtension` | class | `lib/ezutils/classes/ezextension.php:18` | – | – |
| `eZFunctionHandler` | class | `lib/ezutils/classes/ezfunctionhandler.php:18` | – | – |
| `eZMath` | class | `lib/ezutils/classes/ezmath.php:21` | – | – |
| `eZOperationHandler` | class | `lib/ezutils/classes/ezoperationhandler.php:18` | – | – |
| `ezpRepairQueue` | class | `lib/ezutils/classes/ezprepairqueue.php:37` | Repariert eine Installation, deren Composer-Bibliotheken fehlen, von der Fehlerseite aus, die das meldet (eZExecution::renderErrorCasePage('dependencies')), d… | – |
| `eZStringUtils` | class | `lib/ezutils/classes/ezstringutils.php:18` | – | – |
| `eZTextTool` | class | `lib/ezutils/classes/eztexttool.php:19` | – | – |
| `eZTimestamp` | class | `lib/ezutils/classes/eztimestamp.php:12` | – | – |
| `eZTrustedProxy` | class | `lib/ezutils/classes/eztrustedproxy.php:30` | Entscheidet, welchen weitergeleiteten Anfrage-Headern man glauben kann. | – |
