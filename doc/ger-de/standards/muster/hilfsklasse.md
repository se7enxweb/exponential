---
title: Hilfsklasse (statische Hilfsfunktionen)
audience: developer
category: standards
order: 337
summary: Eine Klasse statischer Funktionen, nach Thema gruppiert; keine Instanzen. 730 Instanzen in Exponential.
keywords: Entwurfsmuster, Hilfsklasse, Struktur, Musterkatalog, design pattern, utility, pattern catalogue
type: catalogue
---
# Hilfsklasse (statische Hilfsfunktionen)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine Klasse statischer Funktionen, nach Thema gruppiert; keine Instanzen.

| | |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 730 |
| Erkannt an | Eine konkrete Klasse, deren Methoden alle statisch sind (mindestens zwei). |

## Klassen nach Bereich

### extension/bccie (6)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bccieExportPage` | class | `extension/bccie/classes/bccieExportPage.php:15` | Die Exportseite eines Formulars: das Optionsformular, der Download, der Start eines Exports im Hintergrund und das Entfernen der gesammelten Informationen. | – |
| `bccieExportUtils` | class | `extension/bccie/classes/bccieExportUtils.php:11` | – | – |
| `bccieJob` | class | `extension/bccie/classes/bccieJob.php:15` | Ein Export im Hintergrund, gestartet aus der Administrationsoberfläche: derselbe Konsolenbefehl, den die Shell verwendet (ext:bccie:export), gestartet mit expProcessTools wie d… | – |
| `bccieRunner` | class | `extension/bccie/classes/bccieRunner.php:15` | Die Arbeit der Exporte als Methoden, damit die Admin, die Konsolenbefehle und die Cronjob-Teile dasselbe tun: die Optionen validieren, ein e… | – |
| `bccieTransfer` | class | `extension/bccie/classes/bccieTransfer.php:26` | CIE und die Transferläufe des Kernels (Exponential 6.0.15 und neuer, doc/eng-us/features/6.0/transfer-runs.md des Kernels): eine Engine und ein Lauf-Ver… | – |
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
| `cjw_newsletterInfo` | class | `extension/cjw_newsletter/ezinfo.php:16` | Name, Version, Copyright, Lizenz und Projektseite der Extension, auf der Info-Seite angezeigt. | – |

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

### extension/expai (56)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAiAltText` | class | `extension/expai/classes/alttext/expaialttext.php:21` | Alternativtext für Bilder (doc/alt-text.md): Ein Vision-Modell betrachtet das gespeicherte Bild eines Feldes ezimage, mit dem Titel und dem Text um das Objekt herum, und… | – |
| `expAiAltTextFunctionCollection` | class | `extension/expai/classes/alttext/expaialttextfunctioncollection.php:14` | fetch( 'expai', 'alt_text_model', hash( 'object', $object ) ): das Vision-Modell, das die Schaltfläche „Alternativtext schreiben“ für die Bilder dieses Objekts verwendet, oder wa… | – |
| `expAiApprovalCheck` | class | `extension/expai/classes/expaiapprovalcheck.php:19` | Was „Zur Freigabe senden“ mit einem Entwurf tun wird: die Trigger vor content/publish und ihre Workflows, mit den Bedingungen jedes Freigabe-Ereignisses (ev… | – |
| `expAiAudit` | class | `extension/expai/classes/audit/expaiaudit.php:23` | Was Exponential Intelligence im Audit-Pfad aufzeichnet (doc/audit-trail.md): ein Datensatz je Modellanfrage, je geschriebenem Entwurf und je Änderung eines Ent… | – |
| `expAiChainCriteria` | class | `extension/expai/classes/chain/expaichaincriteria.php:23` | Die Bestehenskriterien eines Kettenschritts ([ChainStep_.] PassCriteria[]): jeder Eintrag ist ein Vergleich „ “, alle müssen gelten. | – |
| `expAiChainPrompt` | class | `extension/expai/classes/chain/expaichainprompt.php:21` | Die Prompts der Kettenschritte. | – |
| `expAiCompactPrompt` | class | `extension/expai/classes/expaicompactprompt.php:29` | Das kompakte Prompt-Profil für kleine und lokale Modelle (doc/local-models.md „Making a local model usable“). | – |
| `expAiCost` | class | `extension/expai/classes/expaicost.php:24` | Der eine Ort, an dem Kosten berechnet werden: gespeicherte Token-Zahlen (Eingabe, Ausgabe, Cache schreiben, Cache lesen) x die Preistabelle von expai.ini [Pricing], zur Lesezeit… | – |
| `expAiDashboard` | class | `extension/expai/classes/expaidashboard.php:15` | Was expai/dashboard und der Dashboard-Block dem aktuellen Benutzer zeigen: seine Entwürfe aus dem Protokoll, die mit Exponential Intelligence geschriebenen Versionen… | – |
| `expAiDiff` | class | `extension/expai/classes/diff/expaidiff.php:27` | Der Diff zweier Versionen oder Sprachen eines Objekts, je Attribut, auf Block- und Satzebene (doc/diff.md). | – |
| `expAiDraftRevertServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/diff/expaidraftrevertserverfunctions.php:20` | „Was sich geändert hat“ bei einem direkt geschriebenen Entwurf (ezjscore.ini [ezjscServer_expairevert], doc/review.md): | extension/expai/settings/ezjscore.ini [ezjscServer_expairevert] Class |
| `expAiEditForm` | class | `extension/expai/classes/expaieditform.php:15` | Die Seite des Inhaltsbearbeitungsformulars: Feldaktionen und „Felder aus einem Briefing füllen“. | – |
| `expAiEditPresets` | class extends expAiEditForm | `extension/expai/classes/fieldactions/expaieditpresets.php:25` | Redakteur-Voreinstellungen und das Menü Improve des Bearbeitungsformulars für Inhalt (doc/presets.md): | – |
| `expAiExchangeRates` | class | `extension/expai/classes/expaiexchangerates.php:24` | Die Referenzkurse der Europäischen Zentralbank zur Umrechnung der Preise und gemeldeten Kosten eines Anbieters, der in einer anderen Währung abrechnet (Melious:… | – |
| `expAiHelpLinks` | class | `extension/expai/classes/expaihelplinks.php:21` | Die Adressen der Dokumentation der Extension. | – |
| `expAiJsonSchema` | class | `extension/expai/classes/expaijsonschema.php:15` | Eine kleine Prüfung der Tool-Eingabe gegen das JSON-Schema des Tools (die Teilmenge, die die Tools von expai verwenden: type, required, properties, additionalProperties false, ite… | – |
| `expAiLimits` | class | `extension/expai/classes/expailimits.php:34` | Die Limits von expai.ini [Limits], vor jeder Anfrage an das Modell geprüft (expAiUsageRecorder) und auf dem Dashboard angezeigt. | – |
| `expAiMcp` | class | `extension/expai/classes/mcp/expaimcp.php:22` | expai als MCP-Client, zwei Ebenen: | – |
| `expAiModelFallback` | class | `extension/expai/classes/expaimodelfallback.php:35` | Der Fallback auf das Standardmodell der Site ([AISettings] FallbackToDefaultModel, Standard enabled): wenn das Modell, das eine Anfrage verlangte (die Wahl des Benutzers,… | – |
| `expAiModelServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/expaimodelserverfunctions.php:24` | Die ezjscore-Funktionen der Aktionen „Test prompt“ und „Load model“ von expai/models (ezjscore.ini [ezjscServer_expaimodel]; der Aufrufer braucht ezjscor… | extension/expai/settings/ezjscore.ini [ezjscServer_expaimodel] Class |
| `expAiModelStats` | class | `extension/expai/classes/expaimodelstats.php:25` | Die Erfolgsbilanz jedes Modells für die Karte „Modelle“ von expai/dashboard: aus dem Nutzungsprotokoll eines Zeitraums (ein Benutzer oder die Site) die Anfragen, die erfolgr… | – |
| `expAiModelTest` | class | `extension/expai/classes/expaimodeltest.php:25` | Die Aktionen „Test prompt“ und „Load model“ von expai/models als Jobs der Job-Warteschlange (Jobtyp expai.model.test, classes/queue/expaimodeltesthandler.p… | – |
| `expAiObjects` | class | `extension/expai/classes/expaiobjects.php:16` | Inhaltsobjekte über eine ID, die expai früher gespeichert hat (das Entwurfslog, gestufte Änderungen, Kontrollstellen, Kettenläufe, Jobs in der Warteschlange): Ein solches Objekt kann entfernt worden sein… | – |
| `expAiPlanBatch` | class | `extension/expai/classes/vector/expaiplanbatch.php:22` | Ein freigegebener Plan auf einer Batch-Lane (doc/parallel-runs.md „On a batch lane“): Wenn die Lane des Threads eine Batch-Lane ist (Anthropic Message Batches, die Ope… | – |
| `expAiPlanner` | class | `extension/expai/classes/vector/expaiplanner.php:22` | Plan-Modus (doc/plan-mode.md): der Plan, den propose_plan speichert, seine Validierung gegen den Geltungsbereich des Threads und die Berechtigungen des Redakteurs, die Freigabe des Redakteurs… | – |
| `expAiPolicyMigration` | class | `extension/expai/classes/expaipolicymigration.php:15` | Benennt die Richtlinienfunktionen von expai um, die Vorabversionen anders nannten (RENAMED), in jeder Rolle, mit ihren Einschränkungen (sie gehören zum… | – |
| `expAiPrompts` | class | `extension/expai/classes/expaiprompts.php:15` | Die Anweisungen (System-Prompts) des Assistenten. | – |
| `expAiProviderHealth` | class | `extension/expai/classes/expaiproviderhealth.php:24` | Ob ein Anbieter im Moment erreichbar ist, für die Modellauswahl (ein gestopptes oder nicht erreichbares Modell wird angezeigt und kann nicht gewählt werden) und für den Fall… | – |
| `expAiPublisher` | class | `extension/expai/classes/expaipublisher.php:15` | Was der Redakteur mit den Entwürfen tut, die der Assistent geschrieben hat: behalten („Als Entwürfe speichern“), senden („Zur Freigabe senden“: der reguläre content/publish… | – |
| `expAiQueue` | class | `extension/expai/classes/queue/expaiqueue.php:25` | Stellt Anfragen an ein Modell über die Extension expqueue in die Warteschlange (Job-Typ expai.request): Die Lane wird gewählt (expAiLanePolicy), die Kosten geschätzt und reserv… | – |
| `expAiQueueKick` | class | `extension/expai/classes/queue/expaiqueuekick.php:39` | Startet abgekoppelte Warteschlangen-Worker unmittelbar nach dem Einreihen eines Jobs, damit der Job nicht auf den Cron-Teil von expqueue wartet (doc/parallel-runs.md „Workers“). | – |
| `expAiRunWatch` | class extends ezjscServerFunctions | `extension/expai/classes/expairunwatch.php:20` | „Lauf beobachten“ bei „Schreiben starten“ des Dashboards: Die Anfrage öffnet den Tab Intelligence des gewählten Speicherorts, wo sie zu einem Thread wird, wenn die… | extension/expai/settings/ezjscore.ini [ezjscServer_expaiwatch] Class |
| `expAiSchema` | class | `extension/expai/classes/expaischema.php:24` | Die zwei Tabellen von expai, einmal im generischen Schemaformat des Kernels definiert. | – |
| `expAiScript` | class | `extension/expai/classes/expaiscript.php:22` | Die Siteaccess eines Kommandozeilenskripts von expai (bin/php). | – |
| `expAiSegmenterRegistry` | class | `extension/expai/classes/diff/expaisegmenterregistry.php:15` | Der Segmentierer eines Datentyps: expai.ini [DiffSettings] Segmenters[]=, sonst FallbackSegmenter (expAiSegmenterGeneric). | – |
| `expAiSentences` | class | `extension/expai/classes/diff/expaisentences.php:21` | Textnormalisierung für das Hashing und ein sprachbewusster Satztrenner (doc/diff.md). | – |
| `expAiServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/expaiserverfunctions.php:24` | Die ezjscore-Serverfunktionen von expai (ezjscore.ini [ezjscServer_expai]): | extension/expai/settings/ezjscore.ini [ezjscServer_expai] Class |
| `expAiServiceTools` | class | `extension/expai/classes/tools/expaiservicetools.php:28` | Service-Tools: die Lesedienste der Extension expservices, die der Assistent aufrufen darf (doc/service-tools.md). | – |
| `expAiSlowModel` | class | `extension/expai/classes/vector/expaislowmodel.php:23` | Ein laufender Turn auf einem langsamen Modell (doc/parallel-runs.md „Slow models“): Vector und der Tab Intelligence zeigen „Läuft auf - etwa N s pro Schritt (Schritt k)“… | – |
| `expAiText` | class | `extension/expai/classes/expaitext.php:14` | Text-Helfer: Kürzen, die HTML-Teilmenge, in der der Assistent Rich-Text schreibt, und eine einfache HTML-Ansicht von gespeichertem ezxmltext, die der Assistent lesen kann. | – |
| `expAiThreadAccess` | class | `extension/expai/classes/vector/expaithreadaccess.php:23` | Wer einen Thread sehen und verwenden darf und was die Richtlinie expai/vector erlaubt (doc/permissions.md, doc/threads.md). | – |
| `expAiThreadFanout` | class | `extension/expai/classes/vector/expaithreadfanout.php:27` | Parallele Läufe eines freigegebenen Plans (doc/parallel-runs.md). | – |
| `expAiThreadMigration` | class | `extension/expai/classes/vector/expaithreadmigration.php:17` | Macht aus den Panel-Unterhaltungen eines früheren expai Threads (Herkunft „panel“, Bereich: der Node der Unterhaltung, Review „direct“), mit je einem Lead-Lauf… | – |
| `expAiThreadPrompts` | class | `extension/expai/classes/vector/expaithreadprompts.php:15` | Die Texte, die ein Vector-Thread für das Modell ergänzt (Englisch, wie der Rest der Anweisungen): der Bereich des Threads, der Plan-Modus (Lese-Werkzeuge und propose_p… | – |
| `expAiThreadRetention` | class | `extension/expai/classes/vector/expaithreadretention.php:36` | Die Aufbewahrung von Thread-Daten (Entscheidung des Eigentümers 2026-10-11: Ereignisse, Vorschläge und Checkpoints werden 30 Tage aufbewahrt), prune() läuft über den Cronjob-Teil „expai“… | – |
| `expAiThreadRunner` | class | `extension/expai/classes/vector/expaithreadrunner.php:23` | Hintergrundläufe von Threads (doc/vector.md „Background runs“). | – |
| `expAiThreadSchema` | class | `extension/expai/classes/vector/expaithreadschema.php:27` | Die Tabellen von Threads und Vector (doc/threads.md, doc/database.md), im generischen Schemaformat des Kernels. | – |
| `expAiTranslationSyncServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/diff/expaitranslationsyncserverfunctions.php:23` | Die ezjscore-Funktionen des Abschnitts „Translations“ des Tabs Intelligence (ezjscore.ini [ezjscServer_expaitranslation]; der Aufrufer braucht ezjscore/… | extension/expai/settings/ezjscore.ini [ezjscServer_expaitranslation] Class |
| `expAiTurnRecord` | class | `extension/expai/classes/vector/expaiturnrecord.php:18` | Die Aufzeichnung jedes Durchgangs eines Threads (doc/parallel-runs.md „The record of a turn“), aus dem Nutzungsprotokoll: das Modell, das tatsächlich geantwortet hat (und das… | – |
| `expAiUsage` | class | `extension/expai/classes/expaiusage.php:14` | Nutzungserfassung: Aufzeichnen einer Anfrage (expAiUsageRecorder ruft record() auf), das tägliche Token-Limit, Preise und Kosten, die gefilterte Liste von expai/log mit… | – |
| `expAiVector` | class | `extension/expai/classes/vector/expaivector.php:17` | Vector (expai/vector, doc/vector.md): was der Arbeitsbereich vom Rest von expai braucht. | – |
| `expAiVectorServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/vector/expaivectorserverfunctions.php:23` | Die ezjscore-Funktionen von Threads, Vector und der Tastatur (ezjscore.ini [ezjscServer_expaivector]; der Aufrufer braucht ezjscore/call mit der Funktionsliste… | extension/expai/settings/ezjscore.ini [ezjscServer_expaivector] Class |
| `expAiVectorStream` | class | `extension/expai/classes/vector/expaivectorstream.php:19` | Der Transport der Thread-Ereignisse (modules/expai/stream.php): welcher gilt, die Stream-Slots, die Stream-Schleife. | – |
| `expAiVectorStrings` | class | `extension/expai/classes/vector/expaivectorstrings.php:14` | Die Texte der Befehlspalette und der Tastenkürzel-Übersicht (Kontext extension/expai/keyboard). | – |
| `expAiWebBudget` | class | `extension/expai/classes/expaiwebbudget.php:26` | Die Zeit, die eine Web-Anfrage für einen Modellaufruf aufwenden darf. | – |
| `expAiWebUrl` | class | `extension/expai/classes/web/expaiweburl.php:16` | URLs der Web-Werkzeuge: Parsen (nur http und https, kein Benutzername oder Passwort, nur die Ports 80 und 443, höchstens 2048 Zeichen), Host-Normalisierung (l… | – |

### extension/expchangeclass (2)

[Alle Muster von extension/expchangeclass](bereich-extension-expchangeclass.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `conversionFunctions` | class | `extension/expchangeclass/classes/functions.php:39` | – | – |
| `expChangeClassConverters` | class | `extension/expchangeclass/classes/converters.php:21` | – | extension/expchangeclass/settings/changeclass.ini [ezobjectrelationlist] Class; extension/expchangeclass/settings/changeclass.ini [ezstring] Class; extension/expchangeclass/settings/changeclass.ini [eztext] Class |

### extension/expimageeditor (1)

[Alle Muster von extension/expimageeditor](area-extension-expimageeditor.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expImageEditorStore` | class | `extension/expimageeditor/classes/expimageeditorstore.php:31` | Was die Speichern-Ansicht des Bild-Editors auf dem Server tut: Der Editor arbeitet im Browser und sendet das fertige Bild; diese Klasse prüft, wer sendet… | – |

### extension/explayouts (53)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsAjaxLoadMoreServer` | class | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer-Funktionen zum Laden seitenweiser Collection-Blöcke. | extension/explayouts/settings/ezjscore.ini [ezjscServer_expajaxloadmore] Class; extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini [ezjscServer_expajaxloadmore] Class |
| `expLayoutsBlockHandlerFactory` | class | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsDynamicCollection` | class | `extension/explayouts/classes/explayoutsdynamiccollection.php:15` | Führt „dynamische“ Collections aus (aus der Nexus-Referenz importierte Collection-Abfragen). | – |
| `expLayoutsExponential` | class | `extension/explayouts/classes/explayoutsexponential.php:4` | – | – |
| `expLayoutsExporter` | class | `extension/explayouts/classes/explayoutsexporter.php:7` | Der Export eines Layouts (Version 1). | – |
| `expLayoutsExpRelationListQuery` | class | `extension/explayouts/classes/explayoutsexprelationlistquery.php:6` | Exponential-Layouts-Portierung des Upstream-Handlers RelationListQuery. | – |
| `expLayoutsExpReverseRelationListQuery` | class | `extension/explayouts/classes/explayoutsexpreverserelationlistquery.php:6` | Exponential-Layouts-Portierung des Upstream-Handlers ReverseRelationListQuery. | – |
| `expLayoutsExpSiteApi` | class | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – | – |
| `expLayoutsExpTagsQuery` | class | `extension/explayouts/classes/explayoutsexptagsquery.php:6` | Exponential-Layouts-Portierung des Upstream-Handlers TagsQuery. | – |
| `expLayoutsExpUtils` | class | `extension/explayouts/classes/explayoutsexputils.php:4` | – | – |
| `expLayoutsFixtures` | class | `extension/explayouts/classes/explayoutsfixtures.php:2` | – | – |
| `expLayoutsImporter` | class | `extension/explayouts/classes/explayoutsimporter.php:11` | Importiert einen Layout-Export, den dieser Extension (expLayoutsExporter, „version“: 1, Parameter als Map aus Name und Wert) und den des Layou… | – |
| `expLayoutsInstall` | class | `extension/explayouts/classes/explayoutsinstall.php:2` | – | – |
| `expLayoutsLanguageResolver` | class | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Sprachauflöser für Exponential 4, kompatibel mit dem LanguageResolver-Muster der Exp Site API. | – |
| `expLayoutsLayoutType` | class | `extension/explayouts/classes/explayoutslayouttype.php:2` | – | – |
| `expLayoutsLinkParameter` | class | `extension/explayouts/classes/explayoutslinkparameter.php:23` | Der Link-Parameter, den die Blöcke teilen, die einen tragen können. | – |
| `expLayoutsMailHelper` | class | `extension/explayouts/classes/explayoutsmailhelper.php:6` | Exponential-4-Portierung von Helper/MailHelper aus dem Netgen SiteBundle. | – |
| `expLayoutsMarkdown` | class | `extension/explayouts/classes/explayoutsmarkdown.php:22` | Markdown des Markdown-Blocks, als HTML. | – |
| `expLayoutsPreviewSiteAccess` | class | `extension/explayouts/classes/explayoutspreviewsiteaccess.php:25` | Die öffentliche Siteaccess, in der eine Administration Layouts darstellt: die Layout-Vorschau (explayouts_ui/layout_preview) und das Nachladen einer Listenblock-Liste, das von… | – |
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

### extension/expqueue (7)

[Alle Muster von extension/expqueue](area-extension-expqueue.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expQueue` | class | `extension/expqueue/classes/expqueue.php:26` | Die Job-Warteschlange: was Produzenten aufrufen (enqueue, groups, cancel, pause/resume, retry, results) und die Zustandswechsel, die die Worker und die Administration vornehmen. | – |
| `expQueueAdmin` | class | `extension/expqueue/classes/expqueueadmin.php:14` | Hilfsfunktionen des Admin-Moduls expqueue: das Seitenergebnis mit seinem Pfad, die Zeilen, die die Templates zeigen (jede Bezeichnung und Dauer hier aufbereitet, sodass das Temp… | – |
| `expQueueLock` | class | `extension/expqueue/classes/expqueuelock.php:25` | Die Dispatcher-Sperre und das Verzeichnis der Warteschlange. | – |
| `expQueueResource` | class | `extension/expqueue/classes/expqueueresource.php:22` | Der Nebenläufigkeitsbegrenzer einer Ressource (Tabelle expqueue_resource): ein Schlüssel wie „anthropic:standard“ oder „imaging“, den Jobs mit der Option resou… | – |
| `expQueueSchema` | class | `extension/expqueue/classes/expqueueschema.php:28` | Die Tabellen von expqueue, einmal im generischen Schemaformat des Kernels definiert. | – |
| `expQueueService` | class | `extension/expqueue/classes/expqueueservice.php:22` | Der systemd-Dienst der Worker der Warteschlange: ./console ext:expqueue:service install\|start\|stop\|status\|uninstall. | – |
| `expQueueStats` | class | `extension/expqueue/classes/expqueuestats.php:15` | Die Kennzahlen der Warteschlange für das Dashboard (expqueue/dashboard) und ./console ext:expqueue:status: Anzahlen je Zustand, je Klasse und je Ressource, die… | – |

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
| `expUIServerFunctions` | class | `extension/expui/classes/expuiserverfunctions.php:30` | – | extension/expui/settings/ezjscore.ini [ezjscServer_exp] Class |

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

### extension/ezodf (23)

[Alle Muster von extension/ezodf](bereich-extension-ezodf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOdfAiAssist` | class | `extension/ezodf/classes/converter/expodfaiassist.php:20` | KI-Unterstützung des Imports (expai, optional): für ein importiertes Dokument eine Zusammenfassung für das Intro und Schlüsselwörter, als Vorschläge, die eine Person im Batch… | – |
| `expOdfCleanup` | class | `extension/ezodf/classes/converter/expodfcleanup.php:15` | Entfernt, was ezodf zurücklässt: Arbeits- und Spool-Verzeichnisse von Konvertierungen (älter als eine Stunde: eine Konvertierung dauert nie so lange), hochgeladene Batches… | – |
| `expOdfConverter` | class | `extension/ezodf/classes/converter/expodfconverter.php:29` | Konvertiert Dokumente. | – |
| `expOdfConverterAdmin` | class | `extension/ezodf/classes/converter/expodfconverteradmin.php:14` | Die Daten und die Aktionen der Seite Document converter (ezodf/converter) und ihr Live-Zustand (ezodf/converterstatus). | – |
| `expOdfConverterTest` | class | `extension/ezodf/classes/converter/expodfconvertertest.php:15` | Die Testkonvertierungen der Seite Dokumentkonverter: Jedes mitgelieferte Beispiel (share/samples) wird mit dem Konverter konvertiert, wie er eingerichtet ist (sein Backend… | – |
| `expOdfDocumentsAdmin` | class | `extension/ezodf/classes/converter/expodfdocumentsadmin.php:13` | Die Daten der Seiten Import documents, batch und runs (ezodf/import, ezodf/batch, ezodf/runs). | – |
| `expOdfExporter` | class | `extension/ezodf/classes/converter/expodfexporter.php:18` | Items als Dokumente aus. | – |
| `expOdfFilterCatalogue` | class | `extension/ezodf/classes/converter/expodffiltercatalogue.php:16` | Die Import- und Exportfilter einer LibreOffice-Installation, gelesen aus ihren Dateien share/registry/*.xcd (die Filters und Types der Typerkennung), damit… | – |
| `expOdfImportDocumentFeedSource` | class extends expOdfImportDocumentUrlsSource | `extension/ezodf/classes/sqliimport/expodfimportdocumentfeedsource.php:18` | „Dokumente eines Feeds“ von 7x SQLI Import: ein RSS- oder Atom-Feed, dessen Einträge auf Dokumente verweisen (Enclosures oder Links, die auf eine Dokumenterweiterung enden: ein pu… | extension/ezodf/settings/sqliimport.ini [Source_document_feed] Class |
| `expOdfImportDocumentUrlsSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportdocumenturlssource.php:16` | „Dokumente von URLs“ von 7x SQLI Import: eine Liste von Adressen von Dokumenten (eine pro Zeile). | extension/ezodf/settings/sqliimport.ini [Source_document_urls] Class |
| `expOdfImportGoogleDriveSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportgoogledrivesource.php:33` | „Dokumente in einem Google-Drive-Ordner“ von 7x SQLI Import: die Dokumente eines Drive-Ordners (in Meine Ablage oder einem geteilten Laufwerk) und seiner Unterordner bis zu… | extension/ezodf/settings/sqliimport.ini [Source_google_drive] Class |
| `expOdfImportHttp` | class | `extension/ezodf/classes/sqliimport/expodfimporthttp.php:27` | Die HTTP-Anfragen der Remote-Dokumentquellen von 7x SQLI Import (WebDAV und Nextcloud, Google Drive, OneDrive und SharePoint), mit der Adresse po… | – |
| `expOdfImportMailboxDocumentsSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportmailboxdocumentssource.php:31` | „Dokumente als E-Mail-Anhänge (IMAP)“ von 7x SQLI Import: die Dokumentanhänge der Nachrichten eines IMAP-Postfachs (eine Einreichungsadresse, eine Pres… | extension/ezodf/settings/sqliimport.ini [Source_mailbox_documents] Class |
| `expOdfImportOneDriveSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportonedrivesource.php:36` | „Dokumente in einem OneDrive- oder SharePoint-Ordner“ von 7x SQLI Import: die Dokumente eines Ordners eines OneDrive-for-Business-Laufwerks oder einer SharePoint-Dokument… | extension/ezodf/settings/sqliimport.ini [Source_onedrive] Class |
| `expOdfImportWebdavSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportwebdavsource.php:33` | „Dokumente in einem WebDAV- oder Nextcloud-Ordner“ von 7x SQLI Import: die Dokumente eines Ordners auf einem WebDAV-Server (Nextcloud, ownCloud, ein SharePoint-WebDAV… | extension/ezodf/settings/sqliimport.ini [Source_webdav] Class |
| `expOdfJobs` | class | `extension/ezodf/classes/converter/expodfjobs.php:24` | Hintergrundarbeit von ezodf, aus der Administration gestartet und nie innerhalb einer Webanfrage ausgeführt: | – |
| `expOdfPaths` | class | `extension/ezodf/classes/converter/expodfpaths.php:24` | Wo ezodf 7 seine Dateien ablegt, alle unterhalb des var-Verzeichnisses der Site (var//ezodf/): | – |
| `expOdfPhpConverter` | class | `extension/ezodf/classes/converter/expodfphpconverter.php:25` | Der letzte Fallback des Konverters: nur PHP, kein LibreOffice. | – |
| `expOdfRemoteFetcher` | class | `extension/ezodf/classes/converter/expodfremotefetcher.php:24` | Lädt ein Dokument von einer URL herunter, die ein Redakteur angegeben hat, ohne dass die URL das eigene Netzwerk des Servers erreichen kann (Server-Side Request Forgery): | – |
| `expOdfRemoteServer` | class | `extension/ezodf/classes/converter/expodfremoteserver.php:22` | Ein Konvertierungsdienst an anderer Stelle, eingestellt auf der Seite Dokumentkonverter (converter.remote_url, remote_kind, optional ein Benutzer und Passwort für HTTP-Basic-A… | – |
| `expOdfSandbox` | class | `extension/ezodf/classes/converter/expodfsandbox.php:23` | Führt LibreOffice (und das Worker-Skript von ezodf) in einer Sandbox aus: | – |
| `expOdfServer` | class | `extension/ezodf/classes/converter/expodfserver.php:23` | Der Konvertierungsserver: ein oder mehrere langlebige Headless-LibreOffice-Prozesse, jeder vom Worker-Skript von ezodf (bin/ezodfworker.py) überwacht, im… | – |
| `expOdfSettings` | class | `extension/ezodf/classes/converter/expodfsettings.php:22` | Die Einstellungen von ezodf 7, wie die Administration sie ändert: Die Einstellungsseiten schreiben var//ezodf/settings.json (der eigene Speicher der Extension, nie setti… | – |

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

### extension/sevenx_authentication_2fa (7)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZsevenxUser2faUser` | class extends eZUser | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:32` | – | – |
| `sevenxAuthentication2faAttempts` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faattempts.php:27` | – | – |
| `sevenxAuthentication2faCrypto` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2facrypto.php:27` | – | – |
| `sevenxAuthentication2faEmail` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faemail.php:21` | – | – |
| `sevenxAuthentication2faPolicy` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fapolicy.php:37` | – | – |
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

### extension/sqliimport (50)

[Alle Muster von extension/sqliimport](area-extension-sqliimport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expImport` | class | `extension/sqliimport/classes/exp/expimport.php:13` | Die kleinen gemeinsamen Helfer der 2.x-Engine: Zeit, JSON, Einstellungen, das Speicherverzeichnis und der Audit-Pfad. | – |
| `expImportAdmin` | class | `extension/sqliimport/classes/exp/expimportadmin.php:15` | Was die Admin-Seiten des Tabs Imports zeigen: die Kennzahlen des Dashboards, die Imports mit ihrem letzten Lauf und nächsten Zeitplan, die Läufe gefiltert nach… | – |
| `expImportAi` | class | `extension/sqliimport/classes/exp/ai/expimportai.php:35` | Der KI-Assistent des Import-Editors und der Laufseite über die Extension expai (expImportAiExpaiTransport): | – |
| `expImportApiHttp` | class | `extension/sqliimport/classes/exp/sources/expimportapihttp.php:18` | Die JSON-Anfragen der Quellen, die die API eines gehosteten Dienstes mit einem Token lesen (Airtable, Notion, Supabase, Baserow, Salesforce, HubSpot): eine Pause zwi… | – |
| `expImportArangoDbSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportarangodbsource.php:23` | Die Ergebnisse einer AQL-Abfrage an ArangoDB, gelesen über deren HTTP-Cursor-API. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.arangodb] Arguments[] |
| `expImportAwsSigV4` | class | `extension/sqliimport/classes/exp/sources/expimportawssigv4.php:17` | AWS Signature Version 4 für eine Anfrage an einen AWS-Dienst (ohne SDK): die kanonische Anfrage, der zu signierende String, der aus dem Geheimnis abgeleitete Signaturschlüssel… | – |
| `expImportClickHouseSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportclickhousesource.php:27` | Die Zeilen eines SELECT an ClickHouse, gelesen über dessen HTTP-Schnittstelle (Port 8123, oder 8443 mit TLS) im Format JSONEachRow. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.clickhouse] Arguments[] |
| `expImportCloudHttp` | class | `extension/sqliimport/classes/exp/sources/expimportcloudhttp.php:18` | Die JSON-Anfragen der Quellen, die eine Cloud-Datenbank über ihre HTTPS-API lesen (Amazon DynamoDB, Firestore, BigQuery, Firebase Realtime Database): eine… | – |
| `expImportCommerce` | class | `extension/sqliimport/classes/exp/sources/expimportcommerce.php:14` | Was die Produkt-Feed-Quellen gemeinsam haben: Preise als Dezimalzeichenfolgen mit ihrer Währung, in einer Zelle geschriebene Listen und das Mapping-Preset auf die Klassen-Pr… | – |
| `expImportCron` | class | `extension/sqliimport/classes/exp/expimportcron.php:28` | Wann ein Zeitplan als Nächstes läuft. | – |
| `expImportCssSelector` | class | `extension/sqliimport/classes/exp/crawl/expimportcssselector.php:21` | Die Selektoren der „picks“ der Crawl-Quellen: eine praktische Teilmenge von CSS, in XPath umgesetzt, oder XPath selbst (geschrieben „xpath:“), und der Wert, der… | – |
| `expImportDataTableFields` | class | `extension/sqliimport/classes/exp/expimportdatatablefields.php:18` | Die Felder einer neuen Datentabelle, erraten aus dem Beispiel einer Quelle (der „Module table“-Assistent des Kernels fragt sie über expImportDataTableField… | – |
| `expImportDataTables` | class | `extension/sqliimport/classes/exp/expimportdatatables.php:16` | Die Datentabellen von Exponential 6.0.15 (Setup › Data tables), wie 7x SQLI Import sie anbietet: die Gruppe „Data tables“ des Katalogs (Import in eine Tabelle,… | – |
| `expImportDocumentHttp` | class | `extension/sqliimport/classes/exp/nosql/expimportdocumenthttp.php:21` | Was die Dokumentspeicher gemeinsam haben, die über HTTP gelesen werden (CouchDB, der Couchbase-Query-Dienst, Azure Cosmos DB): die Prüfung der Serveradresse (http… | – |
| `expImportExternalFile` | class | `extension/sqliimport/classes/exp/expimportexternalfile.php:25` | Eine Datei, die ein anderes Tool an Imports übergibt: „Diese Datei erneut importieren“ bei einem Export, die Datei eines CIE-Exports. | – |
| `expImportFrontMatter` | class | `extension/sqliimport/classes/exp/expimportfrontmatter.php:15` | Front Matter von Markdown-Dateien (Jekyll, Hugo, Obsidian, Eleventy ...): YAML zwischen „---“-Zeilen oder TOML zwischen „+++“-Zeilen am Dateianfang,… | – |
| `expImportFunctionCollection` | class | `extension/sqliimport/classes/exp/expimportfunctioncollection.php:11` | Die Template-Fetch-Funktionen des Moduls sqliimport (modules/sqliimport/function_definition.php). | – |
| `expImportGoogleAuth` | class | `extension/sqliimport/classes/exp/sources/expimportgoogleauth.php:21` | Das Zugriffstoken eines Google-Cloud-Dienstkontos für die Quellen, die Firestore, BigQuery und die Firebase Realtime Database lesen: das Dienstkonto… | – |
| `expImportGoogleSheetsSource` | class extends expImportCsvSource | `extension/sqliimport/classes/exp/sources/expimportgooglesheetssource.php:15` | Eine Google-Sheets-Tabelle über ihre Adresse: der Link der Tabelle (freigegeben, sodass jeder mit dem Link sie ansehen kann) oder ihr Link „Im Web veröffentlichen“. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.google_sheets] Arguments[] |
| `expImportInfluxDbSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportinfluxdbsource.php:29` | Die Zeilen einer Abfrage an InfluxDB: Flux über die API von InfluxDB 2 und Cloud (POST /api/v2/query, die Antwort als annotiertes CSV), oder InfluxQL über… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.influxdb] Arguments[] |
| `expImportLegacy` | class | `extension/sqliimport/classes/exp/expimportlegacy.php:24` | Was die 2.x-Seiten mit den Import-Anfragen (sqliimport_item, SQLIImportItem) und den geplanten Imports (sqliimport_scheduled, SQLIScheduledImport… | – |
| `expImportMailMessage` | class | `extension/sqliimport/classes/exp/mail/expimportmailmessage.php:16` | Eine E-Mail-Nachricht (RFC 5322 mit MIME) als Datensatz: die Nachrichten-ID, Absender und Empfänger, der Betreff und die Namen der Header, nach UTF-8 dekodiert… | – |
| `expImportMapper` | class | `extension/sqliimport/classes/exp/expimportmapper.php:30` | Das No-Code-Mapping: welches Quellfeld welches Attribut einer Content-Klasse füllt, wie ein Datensatz das Objekt findet, das er aktualisiert, und wo ein neues Objekt… | – |
| `expImportMigration` | class | `extension/sqliimport/classes/exp/engine/expimportmigration.php:25` | Die Migration einer Installation von sqliimport 1.x (./console ext:sqliimport:migrate-1x): | – |
| `expImportNeo4jSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportneo4jsource.php:33` | Die Zeilen einer Cypher-Abfrage an Neo4j oder Memgraph. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.neo4j] Arguments[] |
| `expImportNotifier` | class | `extension/sqliimport/classes/exp/engine/expimportnotifier.php:22` | Wer vom Ende eines Laufs erfährt, nach den Einstellungen des Imports (Schritt Verarbeitung, Verhalten): | – |
| `expImportOptionSchema` | class | `extension/sqliimport/classes/exp/expimportoptionschema.php:28` | Typisierte Optionen. | – |
| `expImportPlatformHttp` | class | `extension/sqliimport/classes/exp/sources/expimportplatformhttp.php:20` | Die HTTP-Anfragen der Quellen, die die API einer Website selbst lesen (Drupal JSON:API, eine andere Exponential-Site). | – |
| `expImportPreview` | class | `extension/sqliimport/classes/exp/engine/expimportpreview.php:21` | Die Stichprobe und die Vorschau des Import-Editors, in der Anfrage selbst (kein Job, kein Lauf gespeichert): | – |
| `expImportPrice` | class | `extension/sqliimport/classes/exp/expimportprice.php:17` | Der Wert eines Preisattributs (ezprice), wie der Kernel ihn liest und schreibt: „price\|VAT type id\|1 or 0“ (MwSt. enthalten). | – |
| `expImportReport` | class | `extension/sqliimport/classes/exp/expimportreport.php:18` | Der CSV-Bericht eines Laufs: eine Zeile pro Datensatz mit dem, was an welchem Objekt getan wurde. | – |
| `expImportRestApiHttp` | class extends expImportPlatformHttp | `extension/sqliimport/classes/exp/sources/expimportrestapihttp.php:20` | Die Anfragen der REST-API-Quelle (rest_api) im Netzwerk. | – |
| `expImportRunner` | class | `extension/sqliimport/classes/exp/engine/expimportrunner.php:23` | Startet, bricht ab und überwacht Läufe. | – |
| `expImportScheduler` | class | `extension/sqliimport/classes/exp/engine/expimportscheduler.php:15` | Die Arbeit des Cron-Teils „sqliimport“ (jede Minute): die Läufe der fälligen Zeitpläne starten, Läufe beenden, deren Jobs ohne sie endeten, die… | – |
| `expImportSchema` | class | `extension/sqliimport/classes/exp/expimportschema.php:35` | Die Tabellen von 7x SQLI Import, hier einmal im generischen Schemaformat des Kernels definiert. | – |
| `expImportSecrets` | class | `extension/sqliimport/classes/exp/expimportsecrets.php:25` | Geheimnisse von Quellen (Datenbankpasswörter, Postfachpasswörter, API-Schlüssel). | – |
| `expImportServerFunctions` | class extends ezjscServerFunctions | `extension/sqliimport/classes/exp/expimportserverfunctions.php:22` | Die ezjscore-Serverfunktionen des Tabs Imports (ezjscore.ini [ezjscServer_sqliimport]), aufgerufen von javascript/sqliimport_admin.js über Exp.io: | extension/sqliimport/settings/ezjscore.ini [ezjscServer_sqliimport] Class |
| `expImportSnowflakeSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportsnowflakesource.php:26` | Die Zeilen einer Abfrage an Snowflake, gelesen über die SQL API v2 (POST /api/v2/statements) mit Schlüsselpaar-Authentifizierung: ein JWT, signiert mit dem Benutzer… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.snowflake] Arguments[] |
| `expImportSourceFiles` | class | `extension/sqliimport/classes/exp/engine/expimportsourcefiles.php:21` | Woher die Daten eines Laufs stammen, einmal je Lauf in eine lokale Datei umgewandelt (der Snapshot, den jeder Chunk liest): | – |
| `expImportStatistics` | class | `extension/sqliimport/classes/exp/expimportstatistics.php:27` | Die Statistik des Laufverlaufs: die Diagramme des Dashboards (sqliimport/list) und die Seite Statistik (sqliimport/statistics) mit ihren Zeiträumen, Fil… | – |
| `expImportTemplates` | class | `extension/sqliimport/classes/exp/expimporttemplates.php:28` | Templates von Importen: eine Importdefinition ohne ihre Quelldaten (keine Datei, Adresse oder Serverpfad, keine Zeitpläne, Auslöser, Lauf-Benutzer oder Benachrichtigung… | – |
| `expImportTransfer` | class | `extension/sqliimport/classes/exp/expimporttransfer.php:38` | Import-Definitionen als JSON-Datei, um sie zwischen Installationen zu verschieben (Staging nach Live), mit einem Projekt aufzubewahren oder zu teilen: „Als JSON herunterladen“ o… | – |
| `expImportTransferRuns` | class | `extension/sqliimport/classes/exp/engine/expimporttransferruns.php:20` | Imports im Lauf-Verlauf der Import- und Export-Tools von Exponential (Kernel 6.0.15 und neuer, expKernelTransferHistory, Tabelle exp_transfer_run): jeder… | – |
| `expImportTransforms` | class | `extension/sqliimport/classes/exp/expimporttransforms.php:18` | Die Transformationen eines Mappings: die eingebauten und die der Kernel-Services mit dem Tag sqliimport.transform. | – |
| `expImportTriggerAuth` | class | `extension/sqliimport/classes/exp/expimporttriggerauth.php:86` | Die Prüfungen des Webhook-/API-Auslösers eines Imports, ohne Datenbank (expImportTriggerEndpoint übernimmt die Anfrage): | – |
| `expImportTriggerKey` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimporttrigger.php:89` | Ein API-Schlüssel eines Triggers (sqliimport_trigger_key): seine Bezeichnung, seine Schlüssel-ID (der Teil des Schlüssels, der ihn findet) und der HMAC des Schlüssels (expImportTrigger… | – |
| `SQLIContentUtils` | class | `extension/sqliimport/classes/content/sqlicontentutils.php:15` | Hilfsklasse, die mehrere Helfermethoden rund um Inhalt bündelt | – |
| `SQLIImportLogger` | class | `extension/sqliimport/classes/sqliimportlogger.php:12` | SQLIImportLogger Klasse zum Loggen und/oder Anzeigen von Meldungen während des Importvorgangs | – |
| `SQLIImportToken` | class extends eZPersistentObject | `extension/sqliimport/classes/sqliimporttoken.php:12` | Token-Klasse für SQLI Import, behandelt den Import-Status | – |
| `SQLIImportUtils` | class | `extension/sqliimport/classes/sqliimportutils.php:11` | SQLIImportUtils | – |

### extension/syndication (4)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSyndicationInstaller` | class | `extension/syndication/classes/ezsyndicationinstaller.php:17` | – | – |
| `eZSyndicationJob` | class | `extension/syndication/classes/ezsyndicationjob.php:16` | – | – |
| `eZSyndicationRunner` | class | `extension/syndication/classes/ezsyndicationrunner.php:15` | – | – |
| `eZSyndicationUI` | class | `extension/syndication/classes/ezsyndicationui.php:15` | – | – |

### extension/xrowextract (20)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractArchive` | class | `extension/xrowextract/classes/xrowextractarchive.php:12` | Das Site-Archiv: die Objekte vieler Klassen unterhalb vieler Knoten, eine CSV-Datei pro Klasse, mit einem Manifest, gepackt als zip, tar.gz, tar.bz2, tar.xz, 7z o… | – |
| `XrowExtractCatalogue` | class | `extension/xrowextract/classes/xrowextractcatalogue.php:13` | Der Spaltenkatalog über die Klassenattribute selbst hinaus: | – |
| `XrowExtractColumns` | class | `extension/xrowextract/classes/xrowextractcolumns.php:8` | Die Spalten eines Exports und ihre Werte: die besonderen Spalten (keine Klassenattribute), die Attributspalten einer Klasse und ein Objekt als Zeile von CSV-Zel… | – |
| `XrowExtractCron` | class | `extension/xrowextract/classes/xrowextractcron.php:16` | Wann ein Zeitplan läuft: die einfachen Auswahlmöglichkeiten der Seite Schedules (stündlich zur Minute M, täglich um HH:MM, wöchentlich an einem Tag um HH:MM, monatlich an einem Tag um HH:… | – |
| `XrowExtractEntry` | class | `extension/xrowextract/classes/xrowextractentry.php:14` | Die Einstiegsparameter von xrowextract/csv: das Menü Advanced › Export des Inhaltsbaums öffnet das CSV-Formular vorausgefüllt für einen Knoten mit xrowextract/csv/(nod… | – |
| `XrowExtractFetchAlias` | class | `extension/xrowextract/classes/xrowextractfetchalias.php:18` | Benannte Fetches aus fetchalias.ini, nutzbar in der Ansicht extract und in der CLI: die Constant-Werte eines Alias "tree" / "list" / "tree_count" / "list_count" le… | – |
| `XrowExtractFunctionCollection` | class | `extension/xrowextract/classes/xrowextractfunctioncollection.php:4` | Die Template-Fetch-Funktionen des Moduls xrowextract (modules/xrowextract/function_definition.php). | – |
| `XrowExtractImport` | class | `extension/xrowextract/classes/xrowextractimport.php:22` | Die andere Richtung des Exports: liest eine CSV- oder JSON-Datei, die XrowExtractWriter geschrieben hat (beliebiger Spaltensatz, insbesondere „migration“), zurück in Inhal… | – |
| `XrowExtractJob` | class | `extension/xrowextract/classes/xrowextractjob.php:16` | Ein Exportjob im Hintergrund: ein Ordner pro Job unterhalb des var-Verzeichnisses (var//xrowextract-jobs//), mit job.json (Zustand), progress.json (geschrieben vom… | – |
| `XrowExtractManifest` | class | `extension/xrowextract/classes/xrowextractmanifest.php:20` | Das typisierte Spaltenmanifest eines Exports: was jede Spalte einer Datei ist (ihre ID, der Name in der Datei, der Datentyp, das gewählte Format und die Sprache),… | – |
| `XrowExtractNotifier` | class | `extension/xrowextract/classes/xrowextractnotifier.php:13` | Was der Eigentümer eines Zeitplans (und wen er sonst nennt) über einen Lauf erfährt: - E-Mail bei Fehlschlag (ein fehlgeschlagener oder übersprungener Lauf oder eine fehlgeschlagene Zustellung): immer an… | – |
| `XrowExtractPackage` | class | `extension/xrowextract/classes/xrowextractpackage.php:18` | Unterstützung für Inhaltspakete (.ezpkg) für xrowextract/package: Prüfen der Inhaltsklassen und Inhaltsobjekte eines Pakets, bevor etwas geschrieben wird (die… | – |
| `XrowExtractPreset` | class | `extension/xrowextract/classes/xrowextractpreset.php:25` | Eine gespeicherte Exportvoreinstellung: eine vollständige, benannte Definition eines CSV- oder Archivexports (Bereich, Knoten, Klasse oder Sets, Spalten, Sprachen, alle Filter, Sortierung, Aus… | – |
| `XrowExtractReimport` | class | `extension/xrowextract/classes/xrowextractreimport.php:13` | „Diese Datei erneut importieren“ auf den Seiten Jobs und Verlauf: die Datei eines abgeschlossenen Klassenexports (ein CSV-, JSON- oder XML-Job der Ansicht One class, die Befehlszeile… | – |
| `XrowExtractRequirements` | class | `extension/xrowextract/classes/xrowextractrequirements.php:19` | Was die Extension von PHP und dem Server braucht, an einer Stelle: jede Anforderung (eine PHP-Version, eine PHP-Extension, eine Funktion, die ein Hoster deaktivieren kann, ein… | – |
| `XrowExtractScheduler` | class | `extension/xrowextract/classes/xrowextractscheduler.php:15` | Führt Zeitpläne aus: findet die fälligen (der Cronjob-Teil, cronjobs/xrowextract.php, oder bin/php/schedule.php --cron), macht aus jedem einen gewöhnlichen Hintergr… | – |
| `XrowExtractSchema` | class | `extension/xrowextract/classes/xrowextractschema.php:11` | Die eigenen Tabellen der Extension (xrowextract_schedule, xrowextract_destination, xrowextract_history), verzögert und idempotent angelegt, sobald eine d… | – |
| `XrowExtractSecrets` | class | `extension/xrowextract/classes/xrowextractsecrets.php:15` | Zugangsdaten von Zielen im Ruhezustand: libsodium secretbox (XSalsa20-Poly1305) mit einem 32-Byte-Schlüssel in einer Schlüsseldatei außerhalb der Datenbank. | – |
| `XrowExtractTransfer` | class | `extension/xrowextract/classes/xrowextracttransfer.php:20` | Der Export-Teil der Transferläufe von Exponential (6.0.15 und neuer): eine Engine und ein Lauf-Verlauf für jedes Import- und Export-Tool der Installation. | – |
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

### kernel/classes (89)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAdminPagination` | class | `kernel/classes/expadminpagination.php:19` | Eine zentrale Stelle für die Frage, wie viele Zeilen eine Liste in der Administrationsoberfläche anzeigt. | – |
| `expAnonymousPublicPolicies` | class | `kernel/classes/expanonymouspublicpolicies.php:29` | Die Richtlinien, die ein nicht angemeldeter Besucher auf einer öffentlichen Site braucht, über das Lesen von Inhalt hinaus: sich registrieren, die RSS-Feeds lesen, die Tag-Seiten von ezta… | – |
| `expBenchmark` | class | `kernel/classes/expbenchmark.php:28` | Die Statistik, die Berichtsformate und die Regressionsprüfung von exp:benchmark. | – |
| `expBenchmarkHttp` | class | `kernel/classes/expbenchmarkhttp.php:20` | Die HTTP-Seite von exp:benchmark: ruft eine Liste von URLs ab, mehrere gleichzeitig, mit PHPs eigenem curl (curl_multi), sodass kein externes Programm nötig ist. | – |
| `expBenchmarkKernel` | class | `kernel/classes/expbenchmarkkernel.php:29` | Die In-Process-Seite von exp:benchmark: was der Kernel für die Teile einer Seite aufwendet, ohne HTTP, Webserver oder Antwort-Cache dazwischen. | – |
| `expBenchmarkMicro` | class | `kernel/classes/expbenchmarkmicro.php:33` | Der Mikromodus von exp:benchmark: reine PHP-Hot-Paths des Kernels, in diesem Prozess ohne HTTP, Webserver oder Datenbank gemessen, sodass er auf einem CI… | – |
| `expBookmarkPage` | class | `kernel/classes/expbookmarkpage.php:18` | Was die Lesezeichenseite (content/bookmark) zeigt, aus einfachen Arrays ermittelt: die Ordner mit ihren Zahlen, welche Lesezeichen ein Ordner, eine Suche und… | – |
| `expBookmarkView` | class extends eZPersistentObject | `kernel/classes/expbookmarkview.php:39` | Ein Lesezeichen auf eine Systemseite: eine Seite einer Modulansicht (setup/systemupgrade, exphelp/dashboard, audit/console/(user)/14 ...) statt eines Knotens. | – |
| `expCacheWarm` | class | `kernel/classes/expcachewarm.php:28` | Hält den Antwort-Cache gefüllt, damit kein Besucher derjenige ist, der für das Rendern einer Seite bezahlt. | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Legt die Kollaborationsgruppen eines Benutzers an, benennt sie um, löscht sie und verschiebt Einträge zwischen ihnen. | – |
| `expCollaborationInbox` | class | `kernel/classes/expcollaborationinbox.php:15` | Die Kollaborationsübersicht als Posteingang: jedes Element des aktuellen Benutzers mit seinem Zustand (wartend, freigegeben, abgelehnt), seiner Rolle für den Benutzer (Freigebender oder… | – |
| `expContentDraftList` | class | `kernel/classes/expcontentdraftlist.php:20` | Was die Entwurfsseite (content/draft, "My drafts") auflistet, aus einfachen Arrays ermittelt: die Filter der Adresse (Übersetzung, Klasse, Alter, Suche, o… | – |
| `expContentHistoryList` | class | `kernel/classes/expcontenthistorylist.php:22` | Was die Versionsseite eines Objekts (content/history) auflistet, aus einfachen Arrays ermittelt: die Filter der Adresse (Status, Übersetzung, Ersteller,… | – |
| `expContentLanguageStates` | class | `kernel/classes/expcontentlanguagestates.php:26` | Der Zustand jeder Sprache eines Inhaltsobjekts für die Sprach-Badges von admin4 (design:parts/ language_state_badge.tpl): die Hauptsprache (die… | – |
| `expContentPendingList` | class | `kernel/classes/expcontentpendinglist.php:21` | Was die Seite der ausstehenden Elemente (content/pendinglist, "My pending items") auflistet, aus einfachen Arrays ermittelt: die Versionen, die der Benutzer zum Veröffentlichen gesendet hat, die a… | – |
| `expCronjobPause` | class | `kernel/classes/expcronjobpause.php:27` | Pausiert Cronjob-Teile: einen Teil oder alle, bis jemand sie fortsetzt. | – |
| `expDemoMode` | class | `kernel/classes/expdemomode.php:38` | Demo-Modus: Die Umgebungsvariable EXP_DEMO_MODE=1 kennzeichnet eine Installation als öffentliche Demo. | – |
| `expEditorPolicies` | class | `kernel/classes/expeditorpolicies.php:39` | Die Richtlinien, die ein Redakteur für die tägliche Arbeit braucht und die der Rolle Editor einer neuen Installation fehlen: Ausblenden und Einblenden, der Papierkorb (hineinsehen und… | – |
| `expErrorHeaderList` | class | `kernel/classes/experrorheaderlist.php:25` | Die Antwort-Header einer Fehlerseite, aus error.ini [HTTPError-]: | – |
| `expImageUploadValidator` | class | `kernel/classes/expimageuploadvalidator.php:35` | Entscheidet anhand des Inhalts statt des Namens, ob eine hochgeladene Datei ein Bild ist, das die Site akzeptiert. | – |
| `ExpInstallationDetailsOutputFilter` | class | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Ausgabefilter für den Installationsnamen. | – |
| `expInstallDefaultExtensions` | class | `kernel/classes/expinstalldefaultextensions.php:33` | Die Extensions, die jede neue Installation eingerichtet bekommt, aus welchem Site-Paket sie auch gemacht ist: zweistufige Anmeldung (sevenx_authentication_2fa) und Importe (s… | – |
| `expKernelServiceCompiler` | class | `kernel/classes/expkernelservicecompiler.php:33` | Baut, prüft und entfernt den kompilierten Service-Container des Kernels (var/cache/kernelservices/). | – |
| `expKernelServices` | class | `kernel/classes/expkernelservices.php:44` | Der Kernel-Service-Container: die statische Fassade und der kompilierte Start des Web-Kernels. | – |
| `expMaintenance` | class | `kernel/classes/expmaintenance.php:29` | Wartungsmodus: solange var/maintenance.json existiert, wird jede Anfrage an die Front Controller mit einer Wartungsseite beantwortet (503, Retry-After, ne… | – |
| `expManagedConfiguration` | class | `kernel/classes/expmanagedconfiguration.php:25` | site.ini [ConfigurationSettings] ManagedByDeployment=enabled: Einstellungen, Templates und Code kommen nur aus dem Deployment, und die Administrationsoberfläche… | – |
| `expMemberManagement` | class | `kernel/classes/expmembermanagement.php:27` | Mitgliederverwaltung: Benutzergruppen, die die Konten anderer Benutzergruppen verwalten, eingerichtet über membermanagement.ini (doc/eng-us/bc/6.0/member-management.m… | – |
| `expNodeQuickPick` | class | `kernel/classes/expnodequickpick.php:18` | Schnellzugriffe auf Speicherorte (doc/eng-us/features/6.0/quick-location-buttons.md): einige bekannte Orte des Inhaltsbaums, gefunden über ein kurzes Kürzel (Webs… | – |
| `expNotificationJob` | class | `kernel/classes/expnotificationjob.php:17` | Ein Benachrichtigungslauf für die Statusseite („Jetzt ausführen“), im Hintergrund. | – |
| `expNotificationService` | class | `kernel/classes/expnotificationservice.php:26` | Die eine Stelle, die weiß, wie das Benachrichtigungssystem ausgeführt und eingesehen wird: der Cronjob-Teil, die Befehle exp:notification:*, die Statusseite und ihre… | – |
| `expPackageArchive` | class | `kernel/classes/exppackagearchive.php:41` | Öffnet ein Paketarchiv (.ezpkg: ein gzip-komprimiertes tar-Archiv) zum Lesen, nach einem begrenzten Scan seiner tar-Header. | – |
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
| `expViewParameters` | class | `kernel/classes/expviewparameters.php:25` | Die Optionen einer Administrationsansicht oder eines Endpunkts sind View-Parameter, /(name)/value, nie GET-Query-Parameter (wie beim Help Center, doc/eng-us/fe… | – |
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
| `eZPackageUploadInspector` | class | `kernel/classes/ezpackageuploadinspector.php:33` | Prüft ein Paketarchiv, bevor etwas davon in ein Repository geschrieben wird. | – |
| `eZPackageVersion` | class | `kernel/classes/ezpackageversion.php:55` | – | – |
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
| `expAuditBridge` | class | `kernel/classes/audit/bridge/expauditbridge.php:24` | Die ezpEvent-Brücke des Audits (doc/eng-us/bc/6.0/audit.md, „The ezpEvent bridge“, Z3): Ereignisse von Kernel und Extensions, die bereits existieren (ezpEvent::g… | – |
| `expAuditConfig` | class | `kernel/classes/audit/expauditconfig.php:21` | Die Audit-Einstellungen (settings/audit.ini, doc/eng-us/bc/6.0/audit.md „The settings reference“), einmal in ein Array gelesen. | – |
| `expAuditConsole` | class | `kernel/classes/audit/console/expauditconsole.php:16` | Was die Ansichten des Moduls audit gemeinsam haben (doc/eng-us/bc/6.0/audit.md, „The console: module audit“): die Kanäle, die der aktuelle Benutzer lesen darf (audit/rea… | – |
| `expAuditFormatRegistry` | class | `kernel/classes/audit/format/expauditformatregistry.php:16` | Die Archivformat-Registry: [AuditArchiveSettings] FormatHandlers[]= (RAD-Erhebungs-Registry „auditformats“). | – |
| `expAuditFunctionCollection` | class | `kernel/classes/audit/console/expauditfunctioncollection.php:22` | Die Fetch-Funktionen des Moduls audit (kernel/audit/function_definition.php; doc/eng-us/bc/6.0/audit.md, „Template operator and fetch“): | – |
| `expAuditGuard` | class | `kernel/classes/audit/expauditguard.php:32` | [AuditSettings] OnWriteFailure (doc/eng-us/bc/6.0/audit.md, „When the audit cannot write“): was mit einer Aktion geschieht, deren Audit-Datensatz nicht geschrieben werden kann… | – |
| `expAuditHook` | class | `kernel/classes/audit/hook/expaudithook.php:25` | Die Audit-Aufrufstellen des Kernels (doc/eng-us/bc/6.0/audit.md, „The event catalogue“, Stufe 3): ein abgesicherter Weg, ein Ereignis an einem Hook-Punkt aufzuzeichnen, und… | – |
| `expAuditIndexRow` | class | `kernel/classes/audit/index/expauditindexrow.php:12` | Ein Audit-Eintrag als Zeile von expaudit_event (doc/eng-us/bc/6.0/audit.md, „Tables“): die Spalten, nach denen die Konsole filtert, der Eintrag selbst (wie geschrieben… | – |
| `expAuditIndexSchema` | class | `kernel/classes/audit/index/expauditindexschema.php:18` | Die Audit-Indextabellen (doc/eng-us/bc/6.0/audit.md, „The index“): ihre Definition im generischen Schemaformat von share/db_schema.dba, ihr Anlegen… | – |
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
| `expContentCheck` | class | `kernel/classes/contentcheck/expcontentcheck.php:19` | Was die beiden Inhaltsprüfungen gemeinsam haben (doc/eng-us/features/6.0/content-checks.md): ihre Einstellungen, wo sie antworten, die Richtlinienprüfung, die Formular-Token… | – |
| `expContentCheckBrokenRelations` | class | `kernel/classes/contentcheck/expcontentcheckbrokenrelations.php:38` | Sichtbarer, veröffentlichter Inhalt, der auf etwas verweist, das ein Besucher nicht erreichen kann (doc/eng-us/features/6.0/content-checks.md, "Broken relations"). | – |
| `expContentCheckFileFix` | class | `kernel/classes/contentcheck/expcontentcheckfilefix.php:25` | Die Auflösungen der Liste fehlender Dateien (doc/eng-us/features/6.0/content-checks.md, „Resolving“): jeweils für eine Feldversion oder für viele, immer pl… | – |
| `expContentCheckFiles` | class | `kernel/classes/contentcheck/expcontentcheckfiles.php:26` | Fehlende Dateien und Bild-Aliase (doc/eng-us/features/6.0/content-checks.md, „Missing files“): die dateigestützten Felder des Inhalts unterhalb eines Knotens (ima… | – |
| `expContentCheckQuarantine` | class | `kernel/classes/contentcheck/expcontentcheckquarantine.php:25` | Die Aktionen der Speicherprüfung (doc/eng-us/features/6.0/content-checks.md, "Quarantine"). | – |
| `expContentCheckScope` | class | `kernel/classes/contentcheck/expcontentcheckscope.php:24` | Der Startpunkt der Inhaltsprüfungen (doc/eng-us/features/6.0/content-checks.md, „Where a check starts“): ein beliebiger Node, vom gesamten Baum (Node 1)… | – |
| `expContentCheckStorage` | class | `kernel/classes/contentcheck/expcontentcheckstorage.php:28` | Die Speicherprüfung (doc/eng-us/features/6.0/content-checks.md, „Storage files“): die Dateien unter var//storage, die kein Inhalt verwendet (verwaiste Dateien), und… | – |
| `expContentCheckTrash` | class | `kernel/classes/contentcheck/expcontentchecktrash.php:23` | Verschiebt nicht referenzierte Objekte in den Papierkorb (doc/eng-us/features/6.0/content-checks.md, „Moving to the trash“): Die Liste sendet die angehakten Objekte, eine Bestät… | – |
| `expContentCheckUnreferenced` | class | `kernel/classes/contentcheck/expcontentcheckunreferenced.php:31` | Die Assets unterhalb eines Knotens, die nichts verwendet (doc/eng-us/features/6.0/content-checks.md, „Unreferenced objects“): veröffentlichte Objekte einer Asset-Klasse ([… | – |

### kernel/classes/contentfinder (2)

[Alle Muster von kernel/classes/contentfinder](bereich-kernel-classes-contentfinder.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentFinderAction` | class | `kernel/classes/contentfinder/expcontentfinderaction.php:33` | Die Aktionen des Content Finders (doc/eng-us/features/6.0/content-finder.md, „Actions“): ein Objekt in den Papierkorb verschieben, endgültig löschen, bereinigen… | – |
| `expContentFinderInput` | class | `kernel/classes/contentfinder/expcontentfinderinput.php:28` | Was der Content Finder (Modul contentfinder, doc/eng-us/features/6.0/content-finder.md) aus seinem einen Suchfeld macht: die Arten von ID oder Pfad, die… | – |

### kernel/classes/contentjob (2)

[Alle Muster von kernel/classes/contentjob](bereich-kernel-classes-contentjob.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentJobLock` | class | `kernel/classes/contentjob/expcontentjoblock.php:30` | Teilbaumsperren der Inhaltsjobs. | – |
| `expContentJobStore` | class | `kernel/classes/contentjob/expcontentjobstore.php:18` | Der Speicher der Inhaltsjobs: eine JSON-Datei je Job in var//jobs/content/, atomar geschrieben (eine temporäre Datei im selben Verzeichnis, auf die Pla… | – |

### kernel/classes/datatable (9)

[Alle Muster von kernel/classes/datatable](area-kernel-classes-datatable.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expDataConnections` | class | `kernel/classes/datatable/expdataconnections.php:35` | Die benannten Datenbankverbindungen der Installation (doc/eng-us/features/6.0/data-tables.md, „Connections“): | – |
| `expDataSync` | class | `kernel/classes/datatable/sync/expdatasync.php:39` | Die Synchronisation zwischen Inhalt und Datentabellen (doc/eng-us/features/6.0/data-tables.md, „Synchronisation“): | – |
| `expDataSyncListener` | class | `kernel/classes/datatable/sync/expdatasynclistener.php:20` | Der Live-Spiegel der Datentabellen-Synchronisationen (doc/eng-us/features/6.0/data-tables.md, „Live mirror“): der Kernel benachrichtigt content/publish, cont… | – |
| `expDataTableDescriber` | class | `kernel/classes/datatable/expdatatabledescriber.php:25` | Eine Datentabelle, wie ihre Datenbank sie kennt (doc/eng-us/features/6.0/data-tables.md, „Describe“): ob die Tabelle existiert, ihre Spalten aus der eigenen Auskunft der Engine… | – |
| `expDataTableFieldSources` | class | `kernel/classes/datatable/expdatatablefieldsources.php:17` | Die Feldquellen des Modultabellen-Assistenten: die des Kernels (eine Content-Klasse, eine bestehende Tabelle auf einer Verbindung) und die Kernel-Services mit dem Tag datata… | – |
| `expDataTableFunctionCollection` | class | `kernel/classes/datatable/expdatatablefunctioncollection.php:28` | Die Template-Fetch-Funktionen des Moduls datatable (kernel/datatable/function_definition.php, doc/eng-us/features/6.0/data-tables.md, „In templates… | – |
| `expDataTableRelation` | class | `kernel/classes/datatable/expdatatablerelation.php:25` | Beziehungen zwischen Datentabellen und Inhalt (doc/eng-us/features/6.0/data-tables.md, „Relations“): worauf eine Beziehungsspalte einer Zeile zeigt (ein Inhal… | – |
| `expDataTableSchema` | class | `kernel/classes/datatable/expdatatableschema.php:30` | Das Schema der Datentabellen auf jeder Verbindung (doc/eng-us/features/6.0/data-tables.md, „Schema“): ein .dba-Schema oder eines, das aus der einer persistenten Klasse… | – |
| `expKernelTableSchema` | class | `kernel/classes/datatable/expkerneltableschema.php:25` | Tabellen eines .dba-Schemas auf jeder Engine: ob eine Tabelle existiert (beim Katalog der Engine selbst erfragt, nie aus einem Abfrageergebnis geschlossen: MongoDB antwo… | – |

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

### kernel/classes/help (16)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelp` | class | `kernel/classes/help/exphelp.php:11` | Die gemeinsamen Helfer des Hilfesystems (doc/eng-us/features/6.0/help-system.md): seine Einstellungen, ob es aktiv ist, das Installationsverzeichnis, aus dem es liest, und die UR… | – |
| `expHelpContentFeedback` | class | `kernel/classes/help/content/exphelpcontentfeedback.php:21` | „War das hilfreich?“ bei jedem Hilfethema (doc/eng-us/specifications/6.0/help-system.md, Phase 7; ISO/IEC/IEEE 26514, Gebrauchstauglichkeit der Informationen): die… | – |
| `expHelpContentHelpIds` | class | `kernel/classes/help/content/exphelpcontenthelpids.php:21` | Die help_ids, die Released waren (doc/eng-us/specifications/6.0/help-style-guide.md, Abschnitt 7: „A help_id is never renamed and never reused“): die Tabelle… | – |
| `expHelpContentPackage` | class | `kernel/classes/help/content/exphelpcontentpackage.php:23` | Die Hilfeklassen als Content-Klassen-Paket (doc/eng-us/specifications/6.0/help-content-package.md, CJW #16564): die acht help_*-Klassen von expHelpC… | – |
| `expHelpContentReview` | class | `kernel/classes/help/content/exphelpcontentreview.php:23` | Überfällige Reviews von als Inhalt gepflegter Hilfe (doc/eng-us/specifications/6.0/help-system.md, Phase 7 „a list of outdated topics (review date passed)… | – |
| `expHelpContentSchema` | class | `kernel/classes/help/content/exphelpcontentschema.php:20` | Das Inhaltsmodell der als Inhalt gepflegten Hilfe (doc/eng-us/specifications/6.0/help-system.md, doc/eng-us/features/6.0/help-content.md): die acht Hilfe… | – |
| `expHelpContentTables` | class | `kernel/classes/help/content/exphelpcontenttables.php:21` | Die zwei Tabellen der als Inhalt gepflegten Hilfe (doc/eng-us/features/6.0/help-content.md) auf jeder Engine: ob sie existieren, und ihre Erstellung aus ihren… | – |
| `expHelpContentWorkflow` | class | `kernel/classes/help/content/exphelpcontentworkflow.php:23` | Der Prüfablauf der als Inhalt gepflegten Hilfe (doc/eng-us/specifications/6.0/help-system.md, „Status“ und „Permissions“; Styleguide Abschnitt 8, „… | – |
| `expHelpFrontMatter` | class | `kernel/classes/help/exphelpfrontmatter.php:28` | Der Front Matter einer Hilfeseite (doc/eng-us/features/6.0/help-system.md, „Front matter“): ein Block ganz oben in einer Markdown-Datei zwischen zwei Zeilen… | – |
| `expHelpFunctionCollection` | class | `kernel/classes/help/exphelpfunctioncollection.php:10` | Die Fetch-Funktionen von exphelp (kernel/exphelp/function_definition.php). | – |
| `expHelpHighlighter` | class | `kernel/classes/help/exphelphighlighter.php:29` | Serverseitige Syntaxhervorhebung der Codeblöcke der Hilfeseiten (exphelp, doc/eng-us/features/6.0/help-system.md), ohne JavaScript oder eine externe… | – |
| `expHelpLicense` | class | `kernel/classes/help/exphelplicense.php:17` | Die Lizenz der Dokumentation, die die Hilfe anzeigt (exphelp.ini [License], doc/eng-us/README-LICENSE.md). | – |
| `expHelpLinks` | class | `kernel/classes/help/exphelplinks.php:12` | Die Link- und Bild-Resolver, die der Viewer expHelpMarkdownRenderer gibt (doc/eng-us/features/6.0/help-system.md): ein relativer Link auf eine erlaubte Seite wird… | – |
| `expHelpPdf` | class | `kernel/classes/help/exphelppdf.php:18` | Eine Hilfeseite als PDF-Datei (doc/eng-us/features/6.0/help-system.md, „Print and PDF“): die Ansicht exphelp/pdf/ und `./console exp:help pdf ` fragen hier an. | – |
| `expHelpPdfFontSubset` | class | `kernel/classes/help/exphelppdffontsubset.php:17` | Reduziert eine TrueType-Schrift auf die Glyphen, die ein PDF verwendet (doc/eng-us/features/6.0/help-system.md, „Print and PDF“). | – |
| `expHelpReadPolicyGrant` | class | `kernel/classes/help/exphelpreadpolicygrant.php:22` | Plant und wendet die Vergabe der Richtlinie exphelp/read an (bin/php/addexphelpreadpolicy.php): das Lesen des Hilfesystems (doc/eng-us/features/6.0/help-syst… | – |

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
| `expLogActions` | class | `kernel/classes/logviewer/explogactions.php:13` | Die Aktionen des Log-Viewers außer dem Lesen (doc/eng-us/features/6.0/log-viewer.md, „Actions“): einen Bereich herunterladen, jetzt rotieren, eine Datei leeren, entfernen… | – |
| `expLogBundle` | class | `kernel/classes/logviewer/explogbundle.php:17` | „Alle Logs herunterladen“ (Setup › Logs verwalten; doc/eng-us/features/6.0/log-viewer.md, „Downloading every log“): ein Archiv aller Logs, die der Viewer auflistet,… | – |
| `expLogHealth` | class | `kernel/classes/logviewer/exploghealth.php:17` | Der Zustand der Logs (Setup › Log files › Health, setup/logshealth; doc/eng-us/features/6.0/log-viewer.md, „Health“): der Datenträger, auf dem die Logs und ihre Arch… | – |
| `expLogParser` | class | `kernel/classes/logviewer/explogparser.php:25` | Liest die Zeilen einer Logdatei als Einträge (doc/eng-us/features/6.0/log-viewer.md, "The line format"). | – |
| `expLogPermalink` | class | `kernel/classes/logviewer/explogpermalink.php:25` | Die Permalinks des Log-Viewers (doc/eng-us/features/6.0/log-viewer.md, „Permalinks“): jede Ansicht eines Logs als Pfad ohne Query-String, kurz e… | – |

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

### kernel/classes/passkey (1)

[Alle Muster von kernel/classes/passkey](area-kernel-classes-passkey.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expPasskeySchema` | class | `kernel/classes/passkey/exppasskeyschema.php:17` | Die Tabelle exppasskey auf jeder Engine: ob sie existiert, und ihr Anlegen aus ihrer Definition in share/db_schema.dba über den Schema-Handler der Engine… | – |

### kernel/classes/pdfexport (4)

[Alle Muster von kernel/classes/pdfexport](bereich-kernel-classes-pdfexport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expPDFExportFile` | class | `kernel/classes/pdfexport/exppdfexportfile.php:19` | Die Datei eines PDF-Exports, die einmal erzeugt wird: ihr Name, wo sie aufbewahrt wird, was über sie bekannt ist, ihr Versand und ihr Entfernen. | – |
| `expPDFExportForm` | class | `kernel/classes/pdfexport/exppdfexportform.php:19` | Liest und prüft das Formular von pdf/edit. | – |
| `expPDFExportGenerator` | class | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Erzeugt das PDF eines Exports: in seine gespeicherte Datei oder in eine temporäre Datei, die dann gesendet und entfernt wird. | – |
| `expPDFExportInfo` | class | `kernel/classes/pdfexport/exppdfexportinfo.php:19` | Was die Seiten des PDF-Exports (pdf/list und ihre Löschbestätigung) über einen Export über seine Zeile hinaus sagen: wie er erzeugt wird, ob sein Quellknoten noc… | – |

### kernel/classes/reauth (3)

[Alle Muster von kernel/classes/reauth](area-kernel-classes-reauth.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expReauth` | class | `kernel/classes/reauth/expreauth.php:37` | „Identität bestätigen“ (doc/eng-us/guides/verify-its-you.md): Vor einer heiklen Aktion bestätigt der angemeldete Benutzer erneut, wer er ist, mit seinem Passwor… | – |
| `expReauthRules` | class | `kernel/classes/reauth/expreauthrules.php:28` | Die Regeln von „Identität bestätigen“ (site.ini [ReauthSettings], [ReauthGroup_] Rules[]) und die Entscheidung, ob eine Anfrage zu einer passt (doc/eng-us/guides/… | – |
| `expReauthSettings` | class | `kernel/classes/reauth/expreauthsettings.php:18` | Die Einstellungen von „Identität bestätigen“, wie die Seite setup/reauth sie bearbeitet (doc/eng-us/guides/verify-its-you.md): der Schalter der gesamten Funktion, die min… | – |

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

### kernel/classes/secrets (2)

[Alle Muster von kernel/classes/secrets](area-kernel-classes-secrets.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSecretKey` | class | `kernel/classes/secrets/expsecretkey.php:31` | Der Schlüssel des Secret Stores des Kernels (expSecrets, doc/eng-us/features/6.0/secret-store.md): | – |
| `expSecrets` | class | `kernel/classes/secrets/expsecrets.php:34` | Der Secret Store des Kernels (doc/eng-us/features/6.0/secret-store.md): ein Ort für jedes Passwort, jedes Token und jeden API-Schlüssel, den ein Tool braucht (die Datentabellen-Verbindung… | – |

### kernel/classes/subitems (4)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsColumnHandlers` | class | `kernel/classes/subitems/columns/expsubitemscolumnhandlers.php:16` | Beispiel-Handler=-Spalten der Unterelementliste der Administrationsoberfläche: einfache statische Methoden, die ein INI-Block als Handler=expSubitemsColumnHandlers:: nennt. | – |
| `expSubitemsCSVExport` | class | `kernel/classes/subitems/expsubitemscsvexport.php:14` | Das CSV der Admin-Unterelementliste (content/subitemsexport/). | – |
| `expSubitemsPreference` | class | `kernel/classes/subitems/expsubitemspreference.php:20` | Die benutzerspezifische Auswahl der Unterelementliste der Admin: sichtbare Spalten in Reihenfolge und das gewählte Preset je Navigationsteil, die Seitengröße und die eigenen … | – |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | Die ezjscore-Serverfunktionen der Unterelementliste der Administrationsoberfläche („Tabellenoptionen“), registriert als [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expsubitems] Class |

### kernel/classes/syscard (1)

[Alle Muster von kernel/classes/syscard](area-kernel-classes-syscard.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSysCardColor` | class | `kernel/classes/syscard/expsyscardcolor.php:21` | Die Farben der Systemkarte auf einem Terminal: welche Farbtiefe das Terminal hat und die ANSI-Codes einer Farbe in dieser Tiefe. | – |

### kernel/classes/transfer (3)

[Alle Muster von kernel/classes/transfer](area-kernel-classes-transfer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expKernelTransferHistory` | class | `kernel/classes/transfer/expkerneltransferhistory.php:38` | Der Laufverlauf der Import- und Export-Werkzeuge (doc/eng-us/features/6.0/transfer-runs.md): der Schreiber, den jedes Werkzeug für seine Läufe aufruft, und der Leser des… | – |
| `expKernelTransferRunner` | class | `kernel/classes/transfer/expkerneltransferrunner.php:36` | Ein einheitlicher Weg für jedes Import- und Export-Tool, seine Arbeit im Hintergrund auszuführen (doc/eng-us/features/6.0/transfer-runs.md), auf jeder Engine, die die Installation… | – |
| `expKernelTransferRunSchema` | class | `kernel/classes/transfer/expkerneltransferrunschema.php:21` | Die Tabelle exp_transfer_run auf jeder Engine: ob sie existiert, und ihre Erstellung aus ihrer Definition in share/db_schema.dba über den Schema-Handler der Engine… | – |

### kernel/classes/uiswitch (1)

[Alle Muster von kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUISwitch` | class | `kernel/classes/uiswitch/expuiswitch.php:28` | Das UI-Umschalt-Framework: eine Registry von Anbietern (uiswitch.ini [UISwitchSettings] ProviderList[]), die in der Sitzung gespeicherten Auswahlen des Benutzers und die ea… | – |

### kernel/classes/user (10)

[Alle Muster von kernel/classes/user](bereich-kernel-classes-user.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expPasswordChangeRequired` | class | `kernel/classes/user/exppasswordchangerequired.php:35` | „Passwort bei der nächsten Anmeldung ändern“: Ein Administrator markiert ein Konto, und nach dessen nächster Anmeldung wird der Benutzer zum Formular für die Passwortänderung (user/p… | – |
| `expRegistrationAdminAction` | class | `kernel/classes/user/expregistrationadminaction.php:23` | Die signierten Links der Administrator-Mail „Neuer Benutzer registriert“ (design:user/registrationfeedback.tpl) und die Aktionen dahinter (user/registrat… | – |
| `expSignInSecondFactor` | class | `kernel/classes/user/expsigninsecondfactor.php:37` | Der zweite Schritt der Anmeldung auf den Wegen, die nicht über das Web-Anmeldeformular laufen: die REST-Schnittstelle mit Passwort (ezpRestBasicAuthStyle), Web… | – |
| `expUnactivatedUsers` | class | `kernel/classes/user/expunactivatedusers.php:22` | Die Benutzer, die sich registriert und ihr Konto nie aktiviert haben, für die Seite user/unactivated: wie viele es sind, eine Seite davon, gesucht nach Login, E-Ma… | – |
| `expUserAccountStatus` | class | `kernel/classes/user/expuseraccountstatus.php:21` | Der Kontostatus, den die Administrationsoberfläche an Benutzerobjekten markiert: aktiviert, deaktiviert (Setup › Users, ezuser_setting.is_enabled = 0) oder gesperrt (mehr fehlgeschlagene… | – |
| `expUserActivation` | class | `kernel/classes/user/expuseractivation.php:25` | Was auf die Aktivierung eines Kontos folgt, für user/activate (der Link, den der Benutzer anklickt) und die Schaltfläche Activate von user/unactivated. | – |
| `expUserAgent` | class | `kernel/classes/user/expuseragent.php:30` | Was eine Person über eine angemeldete Sitzung liest (user/sessions): den Browser, das Betriebssystem und die Geräteart, die ein User-Agent-Header nennt, und… | – |
| `expUserApproval` | class | `kernel/classes/user/expuserapproval.php:30` | Freigabe neuer Konten in ausgewählten Benutzergruppen (doc/eng-us/bc/6.0/user-account-approval.md): das Workflow-Ereignis „Approve user accounts“ (expUserAppro… | – |
| `expUserDeactivation` | class | `kernel/classes/user/expuserdeactivation.php:36` | „Deaktivieren und verschieben“ und „Reaktivieren und zurückverschieben“: statt ein Benutzerkonto zu entfernen (Benutzer kommen nie in den Papierkorb, daher lässt sich das Entfernen nicht rückgä… | – |
| `expUserSessionTracker` | class | `kernel/classes/user/expusersessiontracker.php:44` | Die angemeldeten Sitzungen eines Benutzers („Ihre Geräte“, user/sessions): Jede Anmeldung erhält eine Zeile in expuser_session (expUserSession), der Benutzer sieht, wo… | – |

### kernel/classes/webauthn (4)

[Alle Muster von kernel/classes/webauthn](area-kernel-classes-webauthn.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expWebAuthn` | class | `kernel/classes/webauthn/expwebauthn.php:34` | Die Serverseite von WebAuthn (W3C Web Authentication, Level 2/3) für Passkeys: die Optionen, die ein Browser erhält, und die Prüfungen dessen, was er zurücksendet, wenn… | – |
| `expWebAuthnCbor` | class | `kernel/classes/webauthn/expwebauthncbor.php:20` | Der Teil von CBOR (RFC 8949), den WebAuthn braucht: das Dekodieren des Attestation-Objekts eines neuen Passkeys und des COSE-Public-Keys in seinen Authenticator-Daten… | – |
| `expWebAuthnChallenge` | class | `kernel/classes/webauthn/expwebauthnchallenge.php:19` | Die WebAuthn-Challenges, die eine Sitzung erhalten hat: Jede Anfrage nach Optionen bekommt eine neue zufällige Challenge, die auf dem Server (in der Sitzung) mit dem gehalten wird, wofür sie… | – |
| `expWebAuthnCose` | class | `kernel/classes/webauthn/expwebauthncose.php:26` | Der öffentliche Schlüssel eines Passkeys als COSE (RFC 9052/9053) und die Prüfung einer damit erstellten Signatur. | – |

### kernel/common (2)

[Alle Muster von kernel/common](bereich-kernel-common.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpI18n` | class | `kernel/common/ezpi18n.php:12` | – | – |
| `eZTemplatesStatisticsReporter` | class | `kernel/common/eztemplatesstatisticsreporter.php:19` | – | – |

### kernel/modules (28)

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
| `expModuleTableCode` | class | `kernel/setup/expmoduletablecode.php:22` | Die Dateien einer Modultabelle (expModuleTableWizard): die persistente Klasse, das Modul mit seinen Ansichten list, edit, remove und JSON und seinen Richtlinien, die… | – |
| `expModuleTableWizard` | class extends expExtensionWizard | `kernel/setup/expmoduletablewizard.php:34` | Der Modultabellen-Assistent (Setup › RAD › Module table, setup/moduletable; doc/eng-us/features/6.0/data-tables.md, „The module table wizard“): eine NEUE Datentabelle… | – |
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

### kernel/private (19)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expApiKeyRest` | class | `kernel/private/rest/classes/auth/apikey_rest.php:24` | Wie die REST-Schicht einen persönlichen API-Schlüssel auflöst (doc/eng-us/guides/api-keys.md, „For developers“). | – |
| `expContentAccessReport` | class | `kernel/private/classes/expcontentaccessreport.php:23` | Beantwortet „darf dieser Benutzer dies mit diesem Objekt oder Knoten tun, und wenn nicht, welche Einschränkung verweigert es“: die Zugriffsprüfungen des Kernels für einen anderen Ben… | – |
| `expContentSecurityPolicy` | class | `kernel/private/classes/expcontentsecuritypolicy.php:39` | Die Content Security Policy einer Seite, aus site.ini [ContentSecurityPolicy]. | – |
| `expOAuthAuthorizePolicy` | class | `kernel/private/rest/classes/oauth/authorize_policy.php:21` | Entscheidet, ob ein angemeldeter Benutzer einen OAuth-Client in oauth/authorize autorisieren darf. | – |
| `expOAuthAuthorizePolicyGrant` | class | `kernel/private/rest/classes/oauth/authorize_policy_grant.php:26` | Plant und vergibt beim Upgrade die Richtlinie oauth/authorize (bin/php/addoauthauthorizepolicy.php). | – |
| `expOAuthPkce` | class | `kernel/private/rest/classes/oauth/pkce.php:24` | Proof Key for Code Exchange (PKCE, RFC 7636) für den OAuth-Authorization-Code-Flow. | – |
| `expRestContentPermission` | class | `kernel/private/rest/classes/auth/content_permission.php:36` | Die Rechte des aktuellen Benutzers an dem Inhalt, den ein REST-Aufruf liest oder schreibt, geprüft so, wie das Content-Modul sie prüft (doc/eng-us/guides/api-keys.md,… | – |
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
| `ezpSiteAccessURL` | class | `kernel/private/classes/ezpsiteaccessurl.php:44` | Die Adresse der Startseite eines Siteaccess, ermittelt aus den Siteaccess-Einstellungen und der Anfrage, auf der der Besucher ist: der Admin-Link "Site öffnen"… | – |

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
