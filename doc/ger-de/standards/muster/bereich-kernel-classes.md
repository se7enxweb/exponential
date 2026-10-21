---
title: Muster in kernel/classes
audience: developer
category: standards
order: 478
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes mit den Entwurfsmustern.
keywords: kernel/classes, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (202)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expAdminPagination` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expadminpagination.php:19` | Eine zentrale Stelle für die Frage, wie viele Zeilen eine Liste in der Administrationsoberfläche anzeigt. |
| `expBenchmark` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expbenchmark.php:28` | Die Statistik, die Berichtsformate und die Regressionsprüfung von exp:benchmark. |
| `expBenchmarkHttp` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expbenchmarkhttp.php:20` | Die HTTP-Seite von exp:benchmark: ruft eine Liste von URLs ab, mehrere gleichzeitig, mit PHPs eigenem curl (curl_multi), sodass kein externes Programm nötig ist. |
| `expBenchmarkKernel` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expbenchmarkkernel.php:29` | Die In-Process-Seite von exp:benchmark: was der Kernel für die Teile einer Seite aufwendet, ohne HTTP, Webserver oder Antwort-Cache dazwischen. |
| `expBenchmarkMicro` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expbenchmarkmicro.php:33` | Der Mikromodus von exp:benchmark: reine PHP-Hot-Paths des Kernels, in diesem Prozess ohne HTTP, Webserver oder Datenbank gemessen, sodass er auf einem CI… |
| `expBookmarkPage` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expbookmarkpage.php:18` | Was die Lesezeichenseite (content/bookmark) zeigt, aus einfachen Arrays ermittelt: die Ordner mit ihren Zahlen, welche Lesezeichen ein Ordner, eine Suche und… |
| `expCacheCatalogue` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expcachecatalogue.php:22` | Die Caches von Setup › Caches, für einen Menschen beschrieben: was jeder enthält, seine ID und Tags, sein Verzeichnis, seine Größe und Dateianzahl, wann er zuletzt… |
| `expCacheManager` | class | [Fassade / Dienst](fassade.md) | `kernel/classes/expcachemanager.php:30` | Alles, was Setup › Cache (kernel/setup/cache.php) kann, als Funktionen, die zurückgeben, was geschehen ist, statt Template-Variablen zu setzen, sodass die Admini… |
| `expCacheWarm` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcachewarm.php:28` | Hält den Antwort-Cache gefüllt, damit kein Besucher derjenige ist, der für das Rendern einer Seite bezahlt. |
| `expCleanupRSS` | class | – | `kernel/classes/expcleanuprss.php:28` | Kürzt die Inhalte, die ein RSS-Import angelegt hat: Die neuesten Einträge jedes Feeds bleiben, der Rest wird entfernt. |
| `expCollaborationGroupManager` | class | [Fassade / Dienst](fassade.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcollaborationgroupmanager.php:14` | Legt die Kollaborationsgruppen eines Benutzers an, benennt sie um, löscht sie und verschiebt Einträge zwischen ihnen. |
| `expCollaborationInbox` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcollaborationinbox.php:15` | Die Kollaborationsübersicht als Posteingang: jedes Element des aktuellen Benutzers mit seinem Zustand (wartend, freigegeben, abgelehnt), seiner Rolle für den Benutzer (Freigebender oder… |
| `expCollaborationSampleData` | class | – | `kernel/classes/expcollaborationsampledata.php:19` | Erstellt, meldet und entfernt einen in sich geschlossenen Satz von Beispielinhalten für das Kollaborationswerkzeug: eine Benutzergruppe mit drei Redakteuren, einen Freigabe-Workflow… |
| `expCollaborationSampleModule` | class | – | `kernel/classes/expcollaborationsampledata.php:856` | Das Modul, mit dem der Freigabe-Handler weiterleitet; hier wird nichts weitergeleitet. |
| `expContentDraftList` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcontentdraftlist.php:20` | Was die Entwurfsseite (content/draft, "My drafts") auflistet, aus einfachen Arrays ermittelt: die Filter der Adresse (Übersetzung, Klasse, Alter, Suche, o… |
| `expContentHistoryList` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcontenthistorylist.php:22` | Was die Versionsseite eines Objekts (content/history) auflistet, aus einfachen Arrays ermittelt: die Filter der Adresse (Status, Übersetzung, Ersteller,… |
| `expContentLanguageStates` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcontentlanguagestates.php:26` | Der Zustand jeder Sprache eines Inhaltsobjekts für die Sprach-Badges von admin4 (design:parts/ language_state_badge.tpl): die Hauptsprache (die… |
| `expContentPendingList` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcontentpendinglist.php:21` | Was die Seite der ausstehenden Elemente (content/pendinglist, "My pending items") auflistet, aus einfachen Arrays ermittelt: die Versionen, die der Benutzer zum Veröffentlichen gesendet hat, die a… |
| `expCronjobPause` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expcronjobpause.php:27` | Pausiert Cronjob-Teile: einen Teil oder alle, bis jemand sie fortsetzt. |
| `expErrorHeaderList` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/experrorheaderlist.php:25` | Die Antwort-Header einer Fehlerseite, aus error.ini [HTTPError-]: |
| `expExtensionCatalogue` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expextensioncatalogue.php:20` | Was Setup › Extensions über jede Extension weiß: ihre Metadaten (expInfo), ihre deklarierten Abhängigkeiten (extension.xml ), die Designs und Einstellungsda… |
| `expExtensionChangePlan` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expextensionchangeplan.php:33` | Der Änderungsplaner von Setup › Extensions: was aus einer Liste aktiver Extensions wird, wenn ein Administrator eine verschiebt, aktiviert oder deaktiviert, was di… |
| `expImageUploadValidator` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expimageuploadvalidator.php:35` | Entscheidet anhand des Inhalts statt des Namens, ob eine hochgeladene Datei ein Bild ist, das die Site akzeptiert. |
| `ExpInstallationDetailsOutputFilter` | class | [Dekorierer / Filter](dekorierer.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Ausgabefilter für den Installationsnamen. |
| `ExpInstallationOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/classes/expinstallationoperator.php:10` | Template-Operatoren, die den Installationsnamen für Templates bereitstellen: |
| `expJsonSchemaValidator` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expjsonschemavalidator.php:20` | Prüft dekodiertes JSON (json_decode( ..., true )) gegen ein JSON Schema (Draft 2020-12), für die Schlüsselwörter, die die eigenen Schemas des Kernels verwenden: type, enum, cons… |
| `expKickstarter` | class | – | `kernel/classes/expkickstarter.php:12` | – |
| `expKickstarterIni` | class | – | `kernel/classes/expkickstarterini.php:19` | – |
| `expLinkCheck` | class | – | `kernel/classes/explinkcheck.php:15` | Entscheidet, ob ein Link veröffentlichter Inhalte funktioniert, für den Linkprüfungs-Cronjob (linkcheck.php) und die Linkliste (url/list). |
| `expLoveStack` | class | – | `kernel/classes/explovestack.php:29` | Findet, was eine von LoveStack (mugoweb/Exponential-legacy) kommende Website mitbringt, das Exponential anders behandelt: die Markierungen ihrer Datenbank (… |
| `expMaintenance` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expmaintenance.php:29` | Wartungsmodus: solange var/maintenance.json existiert, wird jede Anfrage an die Front Controller mit einer Wartungsseite beantwortet (503, Retry-After, ne… |
| `expManagedConfiguration` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expmanagedconfiguration.php:25` | site.ini [ConfigurationSettings] ManagedByDeployment=enabled: Einstellungen, Templates und Code kommen nur aus dem Deployment, und die Administrationsoberfläche… |
| `expNamePatternResolverInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/expnamepatternresolverinterface.php:27` | Eine PHP-Klasse, die den Namen eines Inhaltsobjekts oder den Namen, aus dem sein URL-Alias gebildet wird, erzeugt, statt der Attribut-Tokens des Namensmusters der Klasse… |
| `expNodeQuickPick` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expnodequickpick.php:18` | Schnellzugriffsschaltflächen (doc/eng-us/features/6.0/quick-location-buttons.md): einige bekannte Stellen des Inhaltsbaums, gefunden über einen kurzen Schlüssel (Websites, M… |
| `expNotificationJob` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expnotificationjob.php:17` | Ein Benachrichtigungslauf für die Statusseite („Jetzt ausführen“), im Hintergrund. |
| `expNotificationService` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expnotificationservice.php:26` | Die eine Stelle, die weiß, wie das Benachrichtigungssystem ausgeführt und eingesehen wird: der Cronjob-Teil, die Befehle exp:notification:*, die Statusseite und ihre… |
| `expPackageConflictReport` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/exppackageconflictreport.php:22` | Was die Installation eines Pakets auf dieser Installation vorfinden würde, nach Remote-ID, ohne etwas zu schreiben: für jedes Inhaltsobjekt des Pakets, ob ein… |
| `expPackageJson` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/exppackagejson.php:34` | Die JSON-Serialisierung eines Pakets (doc/eng-us/bc/6.0/package-json-format.md). |
| `expPasswordPolicy` | class | [Singleton / instance()](singleton.md) | `kernel/classes/exppasswordpolicy.php:24` | Die Regeln, die ein neues Passwort erfüllen muss, und was geschieht, nachdem ein Passwort geändert wurde. |
| `expPhar` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expphar.php:21` | Packt die Engine – die Klassendateien von Kernel und Bibliotheken – in ein Phar, damit die Laufzeit sie aus einem Archiv statt aus tausend ein… |
| `expPolicyOmitList` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/exppolicyomitlist.php:25` | Die Module und Ansichten, die die Richtlinienprüfung überspringen: site.ini [RoleSettings] PolicyOmitList[] ohne die Einträge von [RoleSettings] PolicyOmitListExclud… |
| `expPreloadAddress` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/exppreloadaddress.php:26` | Wo der Cache-Preloader einen Siteaccess findet: die Adresse seiner Site, das URL-Präfix, das ihn auf diesem Host auswählt, und die Seiten, mit denen ein Lauf beginnt. |
| `expPreloadHistory` | class | – | `kernel/classes/exppreloadhistory.php:22` | Die Läufe des Cache-Preloaders: eine Statusdatei pro Lauf, .json, neben seinen Ereignissen (.jsonl) in var//preload. |
| `expPreloadJob` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/exppreloadjob.php:27` | Ein Cache-Vorlade-Lauf: im Hintergrund über Setup › Vorladen gestartet oder im Vordergrund über die Shell (bin/php/preload.php), mit einer Sperre, einem St… |
| `expPreloadLock` | class | – | `kernel/classes/exppreloadlock.php:21` | Jeweils ein Cache-Preload. |
| `expProcessTools` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expprocesstools.php:22` | Findet die Programme, die ein Hintergrundjob braucht: die PHP-Kommandozeile und setsid. |
| `expScriptStatus` | class | [Singleton / instance()](singleton.md) | `kernel/classes/expscriptstatus.php:18` | expScriptStatus - schlanker Fortschritts-/Statusmelder für die CLI. |
| `expSecretRule` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expsecretrule.php:29` | Die eine Regel, was ein Geheimnis ist, geteilt von allem, was Einstellungen und Werte anzeigt oder aufzeichnet: exp:ini und expIniEditor, das Audit-Log (über ex… |
| `expSetupLog` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expsetuplog.php:31` | var/log/setup.log: ein lesbarer Eintrag pro Installationslauf. |
| `expSetupWizardGuard` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expsetupwizardguard.php:26` | Entscheidet, was eine Anfrage an den Web-Setup-Assistenten (setup/init) tun darf, bevor der Assistent eine Seite zeigt oder den Wartungsmodus berührt. |
| `expSiteAccessOverview` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expsiteaccessoverview.php:31` | Jeder Siteaccess der Installation, wie eine Anfrage ihn erreicht und wo er liegt: der Siteaccess-Teil von Setup › Systeminformationen (#552). |
| `expSystemReport` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expsystemreport.php:29` | Die Fakten von Setup › System information und von exp:system:info, in Abschnitten, mit Zustandsprüfungen. |
| `expSystemReportMask` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expsystemreportmask.php:29` | Was Setup › System information, sein Textbericht und exp:system:info von einem Wert zeigen dürfen. |
| `expTestLanguage` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/exptestlanguage.php:17` | Testsprachen: Locales, die in share/locale mit [LanguageSettings] TestLanguage=true markiert sind (die mitgelieferte ist Lorem Ipsum, qlo-ZZ), und der Lorem-Ipsum-Tex… |
| `expUserLoginIntegrity` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expuserloginintegrity.php:35` | Ein Konto pro Login: findet die Logins, die mehr als eine ezuser-Zeile haben, legt den eindeutigen Index auf ezuser.login an, sobald es keine mehr gibt, und speichert ein Kont… |
| `expVelocity` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expvelocity.php:38` | Steuert den mitgelieferten Anwendungsserver als Dienst. |
| `expVelocityConfig` | class | – | `kernel/classes/expvelocityconfig.php:36` | Die Einstellungen lesen und schreiben, mit denen Exponential Velocity tatsächlich läuft. |
| `expVelocityConfigLayout` | class | – | `kernel/classes/expvelocityconfiglayout.php:41` | Die Konfiguration des Servers als Baum im Stil von Debian Apache und der Umzug dorthin. |
| `expVelocityDeploy` | class | – | `kernel/classes/expvelocitydeploy.php:47` | Alles, was eine PHP-Codeänderung braucht, bevor sie live ist, in der einen Reihenfolge, die funktioniert: exp:velocity deploy. |
| `expVelocityFrankenPHP` | class extends expVelocity | – | `kernel/classes/expvelocityfrankenphp.php:34` | Die zweite Engine von Velocity: FrankenPHP, der Caddy-Webserver mit eingebautem PHP. |
| `expVelocityFrankenPHPInstaller` | class | – | `kernel/classes/expvelocityfrankenphpinstaller.php:28` | Legt die FrankenPHP-Binärdatei für die Engine frankenphp bereit: `exp:velocity install` und AutoInstall von start. |
| `expVelocityMarkers` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expvelocitymarkers.php:30` | Die kleinen Statusdateien, die sagen, warum eine Engine nicht läuft. |
| `expVelocityPHPServer` | class extends expVelocity | – | `kernel/classes/expvelocityphpserver.php:29` | Die dritte Engine von Velocity: der eingebaute Webserver von PHP (php -S). |
| `expVelocityRestartUnit` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/expvelocityrestartunit.php:34` | Führt exp:velocity restart als eine losgelöste Einheit aus: das Stoppen und das Starten in einem Prozess, der nicht zum Aufrufer gehört. |
| `expVelocityWatchdog` | class | – | `kernel/classes/expvelocitywatchdog.php:49` | Ein Sicherheitsnetz, das die Standard-Engine wieder startet, wenn sie läuft nicht und niemand das wollte: exp:velocity watchdog. |
| `expViewAccess` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/expviewaccess.php:25` | Ob ein Benutzer eine Adresse dieses Siteaccess öffnen kann, so entschieden, wie der Kernel eine Anfrage entscheidet (ezpKernelWeb): die URL-Alias- (und Wildcard-)Übers… |
| `expWeakPasswordList` | class | [Singleton / instance()](singleton.md) | `kernel/classes/expweakpasswordlist.php:35` | Die eine Liste bekannter und häufiger Passwörter und die Prüfung, die jede Stelle verwendet, die ein neues Passwort setzt: der Setup-Assistent, der Kickstarter und exp:i… |
| `eZAudit` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezaudit.php:20` | Die Audit-API von 4.x, beibehalten, damit jeder bestehende Aufruf weiter funktioniert: eZAudit::writeAudit( $name, $attributes ) wird von expAudit aufgezeichnet (kernel/classes/audit… |
| `eZBasket` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezbasket.php:20` | – |
| `eZBinaryFileHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezbinaryfilehandler.php:23` | – |
| `eZCache` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcache.php:22` | eZCache ezcache.php |
| `eZCacheTrash` | class | [Registry](registry.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcachetrash.php:38` | Entfernt ein Cache-Verzeichnis, indem es zuerst aus dem Weg umbenannt wird. |
| `eZClusterFileFailure` | class | – | `kernel/classes/ezclusterfilefailure.php:20` | – |
| `eZClusterFileHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezclusterfilehandler.php:11` | – |
| `eZCodeTemplate` | class | – | `kernel/classes/ezcodetemplate.php:18` | – |
| `eZCollaborationGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcollaborationgroup.php:18` | – |
| `eZCollaborationItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcollaborationitem.php:18` | – |
| `eZCollaborationItemGroupLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcollaborationitemgrouplink.php:18` | – |
| `eZCollaborationItemHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezcollaborationitemhandler.php:18` | – |
| `eZCollaborationItemMessageLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcollaborationitemmessagelink.php:18` | – |
| `eZCollaborationItemParticipantLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcollaborationitemparticipantlink.php:18` | – |
| `eZCollaborationItemStatus` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcollaborationitemstatus.php:18` | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcollaborationprofile.php:18` | – |
| `eZCollaborationSimpleMessage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcollaborationsimplemessage.php:18` | – |
| `eZCollaborationViewHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezcollaborationviewhandler.php:18` | – |
| `eZConfirmOrderHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezconfirmorderhandler.php:18` | – |
| `eZContentBrowse` | class | – | `kernel/classes/ezcontentbrowse.php:33` | – |
| `eZContentBrowseBookmark` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentbrowsebookmark.php:35` | – |
| `eZContentBrowseBookmarkFolder` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcontentbrowsebookmarkfolder.php:32` | – |
| `eZContentBrowseRecent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcontentbrowserecent.php:34` | – |
| `eZContentCache` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcontentcache.php:18` | – |
| `eZContentCacheManager` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcontentcachemanager.php:26` | – |
| `eZContentClass` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentclass.php:20` | – |
| `eZContentClassAttribute` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentclassattribute.php:19` | – |
| `eZContentClassClassGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcontentclassclassgroup.php:18` | – |
| `eZContentClassEditHandler` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezcontentclassedithandler.php:15` | Handler für die Bearbeitung von Inhaltsklassen. |
| `eZContentClassGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcontentclassgroup.php:18` | – |
| `eZContentClassName` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcontentclassname.php:12` | – |
| `eZContentClassNameList` | class extends eZSerializedObjectNameList | – | `kernel/classes/ezcontentclassnamelist.php:12` | – |
| `eZContentClassOperations` | class | – | `kernel/classes/ezcontentclassoperations.php:20` | – |
| `eZContentFunctions` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcontentfunctions.php:12` | – |
| `eZContentLanguage` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentlanguage.php:12` | – |
| `eZContentObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentobject.php:17` | Kapselt Daten über Inhaltsobjekte und Methoden, um mit ihnen zu arbeiten |
| `eZContentObjectAssignmentHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezcontentobjectassignmenthandler.php:18` | – |
| `eZContentObjectAttribute` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentobjectattribute.php:20` | – |
| `eZContentObjectEditHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezcontentobjectedithandler.php:19` | – |
| `eZContentObjectOperations` | class | – | `kernel/classes/ezcontentobjectoperations.php:20` | – |
| `eZContentObjectTranslation` | class | – | `kernel/classes/ezcontentobjecttranslation.php:20` | – |
| `eZContentObjectTrashNode` | class extends eZContentObjectTreeNode | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezcontentobjecttrashnode.php:15` | Kapselt Daten über Inhaltsobjekte im Papierkorb und Methoden, um mit ihnen zu arbeiten |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentobjecttreenode.php:17` | Kapselt Daten über Inhaltsobjekte im Inhaltsobjektbaum und Methoden, um mit ihnen zu arbeiten |
| `eZContentObjectTreeNodeOperations` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezcontentobjecttreenodeoperations.php:22` | – |
| `eZContentObjectVersion` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezcontentobjectversion.php:19` | – |
| `eZContentUpload` | class | – | `kernel/classes/ezcontentupload.php:51` | – |
| `eZContentUploadHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezcontentuploadhandler.php:28` | – |
| `eZDataType` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Registry](registry.md) | `kernel/classes/ezdatatype.php:46` | – |
| `eZDBGarbageCollector` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezdbgarbagecollector.php:33` | – |
| `eZDiscount` | class | – | `kernel/classes/ezdiscount.php:12` | – |
| `eZDiscountRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezdiscountrule.php:18` | – |
| `eZDiscountSubRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezdiscountsubrule.php:18` | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezdiscountsubrulevalue.php:17` | – |
| `eZError` | class | – | `kernel/classes/ezerror.php:16` | – |
| `eZHTTPHeader` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezhttpheader.php:18` | – |
| `eZInformationCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezinformationcollection.php:22` | – |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezinformationcollectionattribute.php:19` | – |
| `eZNamePatternResolver` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/eznamepatternresolver.php:40` | eZNamePatternResolver ist eine Hilfsklasse zum Auflösen von Objektnamen- und URL-Alias-Mustern. |
| `eZNavigationPart` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/eznavigationpart.php:32` | – |
| `eZNodeAssignment` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/eznodeassignment.php:17` | – |
| `eZNodeviewfunctions` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/eznodeviewfunctions.php:18` | – |
| `eZOrder` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezorder.php:20` | – |
| `eZOrderItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezorderitem.php:27` | – |
| `eZOrderStatus` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezorderstatus.php:47` | – |
| `eZOrderStatusHistory` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezorderstatushistory.php:35` | – |
| `eZPackage` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezpackage.php:20` | – |
| `eZPackageCatalog` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagecatalog.php:23` | Was die Paketseiten über die Pakete auf der Festplatte sagen: jedes Repository mit seinen Zahlen, eine Karte je Paket (Name, Version, Typ, Zusammenfassung, Maintain… |
| `eZPackageComparison` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagecomparison.php:36` | Vergleicht, was ein Paket enthält, mit dem, was die Site hat, für package/compare/. |
| `eZPackageComparisonDiff` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagecomparisondiff.php:20` | Ein Unterschied zweier Texte auf Wortebene, für den Paketvergleichs-Viewer (package/compare, eZPackageComparison): welche Wörter aus dem ers… |
| `eZPackageComparisonFileCollector` | class | – | `kernel/classes/ezpackagecomparisonfilecollector.php:18` | Steht für ein eZPackage, wenn eZPackageComparison einen Datentyp bittet, eines der eigenen Attribute der Website zu serialisieren, sodass der Wert der Website herauskommt… |
| `eZPackageComparisonImport` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagecomparisonimport.php:34` | Importiert, was ein Paket für einzelne Einträge seines Vergleichs mit der Site mitbringt (eZPackageComparison, package/compare): ein neues Objekt wird angelegt, wo… |
| `eZPackageCreationHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezpackagecreationhandler.php:21` | – |
| `eZPackageDownload` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagedownload.php:25` | Sendet eine Datei des Moduls package (ein exportiertes Archiv, eine Datei aus einem Paket) stückweise an den Browser. |
| `eZPackageFileBrowser` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagefilebrowser.php:17` | Nur-Lese-Unterstützung für den Paketinhalts-Browser auf package/view/full/: jede Datei, die das eigene Verzeichnis eines Pakets enthält, seitenweise und gefiltert, und o… |
| `eZPackageHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezpackagehandler.php:18` | – |
| `eZPackageInstallationHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezpackageinstallationhandler.php:19` | – |
| `eZPackageLicense` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagelicense.php:28` | – |
| `eZPackageRemovalPlan` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackageremovalplan.php:20` | Was das Entfernen einer Auswahl von Paketen bewirken würde, bevor es geschieht: pro Paket sein Verzeichnis, seine Dateien und seine Größe, ob es installiert ist (sein Installationsdat… |
| `eZPackageRequestGuard` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackagerequestguard.php:16` | Die Prüfungen, die jede Paketansicht auf das anwendet, was eine Anfrage benennt, bevor etwas von der Platte gelesen wird: ein Paketname, ein Repository, ein Ansichtsmodus und eine Datei… |
| `eZPackageUploadInspector` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackageuploadinspector.php:31` | Prüft ein Paketarchiv, bevor etwas davon in ein Repository geschrieben wird. |
| `eZPackageVersion` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpackageversion.php:46` | – |
| `eZPathElement` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezpathelement.php:23` | – |
| `eZPDFExport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezpdfexport.php:19` | – |
| `eZPendingActions` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpendingactions.php:12` | – |
| `eZPersistentObject` | class | – | `kernel/classes/ezpersistentobject.php:38` | Ermöglicht die Persistenz von Objekten in einer Datenbank |
| `eZPolicy` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezpolicy.php:19` | – |
| `eZPolicyLimitation` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezpolicylimitation.php:18` | – |
| `eZPolicyLimitationValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezpolicylimitationvalue.php:18` | – |
| `eZPreferences` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezpreferences.php:22` | – |
| `eZProductCategory` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezproductcategory.php:18` | – |
| `eZProductCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezproductcollection.php:16` | eZProductCollection ist eine Containerklasse, die Gruppen von Produkten verwaltet |
| `eZProductCollectionItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezproductcollectionitem.php:20` | – |
| `eZProductCollectionItemOption` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezproductcollectionitemoption.php:12` | – |
| `eZRedirectManager` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezredirectmanager.php:22` | – |
| `eZRole` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezrole.php:35` | – |
| `eZRSSExport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezrssexport.php:19` | – |
| `eZRSSExportItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezrssexportitem.php:19` | – |
| `eZRSSExportOPMLItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezrssexportopmlitem.php:26` | Eine Zeile eines OPML-Exports: ein Feed, den der Export auflistet. |
| `eZRSSImport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezrssimport.php:19` | – |
| `eZRunCronjobs` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezruncronjobs.php:15` | – |
| `eZScript` | class | [Singleton / instance()](singleton.md) | `kernel/classes/ezscript.php:49` | – |
| `eZSearch` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezsearch.php:19` | – |
| `eZSearchLog` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezsearchlog.php:18` | – |
| `eZSection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezsection.php:18` | – |
| `eZSerializedObjectNameList` | class | – | `kernel/classes/ezserializedobjectnamelist.php:12` | – |
| `eZShippingManager` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezshippingmanager.php:17` | – |
| `eZShopAccountHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/ezshopaccounthandler.php:12` | – |
| `eZSiteAccess` | class | [Front Controller](front-controller.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezsiteaccess.php:17` | Stellt Funktionen für die Siteaccess-Verarbeitung bereit |
| `eZSiteData` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezsitedata.php:21` | eZPersistentObject-Implementierung für die Tabelle ezsite_data. |
| `eZSiteInstaller` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezsiteinstaller.php:23` | – |
| `eZSSLZone` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezsslzone.php:27` | – |
| `eZStaticCache` | class implements ezpStaticCache | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezstaticcache.php:30` | Die Klasse eZStaticCache verwaltet das statische Cache-System. |
| `eZSubtreeCache` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezsubtreecache.php:18` | – |
| `eZTextInputParser` | class | – | `kernel/classes/eztextinputparser.php:17` | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/eztipafriendcounter.php:18` | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/eztipafriendrequest.php:17` | – |
| `eZTrigger` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/eztrigger.php:17` | – |
| `eZURLAliasFilter` | abstract class | [Dekorierer / Filter](dekorierer.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/classes/ezurlaliasfilter.php:22` | Die Klasse eZURLAliasFilter ist die Schnittstelle für alle URL-Alias-Filter; die Filterimplementierung muss die Methode process implementieren. |
| `eZURLAliasML` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezurlaliasml.php:58` | – |
| `eZURLAliasQuery` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezurlaliasquery.php:34` | – |
| `eZURLWildcard` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezurlwildcard.php:19` | – |
| `eZUserDiscountRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezuserdiscountrule.php:18` | – |
| `eZVATManager` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezvatmanager.php:18` | – |
| `eZVatRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezvatrule.php:18` | – |
| `eZVatType` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezvattype.php:19` | – |
| `eZViewCounter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezviewcounter.php:12` | – |
| `eZWishList` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/ezwishlist.php:20` | – |
| `eZWorkflow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/ezworkflow.php:18` | – |
| `eZWorkflowEvent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezworkflowevent.php:18` | – |
| `eZWorkflowEventType` | class extends eZWorkflowType | [Registry](registry.md) | `kernel/classes/ezworkfloweventtype.php:18` | – |
| `eZWorkflowGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezworkflowgroup.php:18` | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/ezworkflowgrouplink.php:18` | – |
| `eZWorkflowGroupType` | class extends eZWorkflowType | [Registry](registry.md) | `kernel/classes/ezworkflowgrouptype.php:18` | – |
| `eZWorkflowProcess` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/ezworkflowprocess.php:19` | – |
| `eZWorkflowType` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Registry](registry.md) | `kernel/classes/ezworkflowtype.php:18` | – |

## Ereignis-Hooks (10)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `image/invalidateAliases` | notify | `kernel/classes/ezcache.php:703` |
| `content/class/cache/all` | notify | `kernel/classes/ezcache.php:850` |
| `user/cache/all` | notify | `kernel/classes/ezcache.php:873` |
| `content/cache/all` | notify | `kernel/classes/ezcache.php:886` |
| `content/state/cache/all` | notify | `kernel/classes/ezcache.php:956` |
| `content/cache` | notify | `kernel/classes/ezcontentcachemanager.php:818` |
| `content/cache/all` | notify | `kernel/classes/ezcontentcachemanager.php:826` |
| `content/cache/all` | notify | `kernel/classes/ezcontentcachemanager.php:1182` |
| `content/edit/access` | filter | `kernel/classes/ezcontentobject.php:6319` |
| `content/view/cachekeys` | filter | `kernel/classes/eznodeviewfunctions.php:414` |
