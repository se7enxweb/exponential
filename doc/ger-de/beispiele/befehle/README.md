---
title: Konsolenbefehle
audience: developer, admin
category: examples
order: 0
summary: Jeder ./console-Befehl mit Hilfetext, Optionen und Aliasen.
keywords: Beispiele, Referenz, Konsolenbefehle, examples, reference
type: generated
generated: exp:help:examples
---
# Konsolenbefehle

Jeder Befehl von `./console` (`bin/php/console`): Jedes `bin/php/<name>.php` ist `exp:<name>`, ein Skript kann mit `@alias` Aliase hinzufügen. Hilfetext und Optionen werden aus dem Code gelesen und stimmen daher mit `--help` überein. `./console list` zeigt sie auf einem Rechner an; `./console help <command>` gibt die Hilfe eines Befehls aus.

| Befehl | Aliase | Was er tut |
|---|---|---|
| [exp:accesscheck](accesscheck.md) | exp:access:check | Prüft, was ein Benutzer mit einem Knoten oder Objekt tun darf (content/read, edit, ...) und welche Einschränkung es verweigert |
| [exp:adddefaultstates](adddefaultstates.md) |  |  |
| [exp:addexphelpreadpolicy](addexphelpreadpolicy.md) | exp:help-grant | Gewährt der Rolle Editor oder den genannten Rollen exphelp/read (das Hilfesystem) (--dry-run listet sie zuerst auf) |
| [exp:addoauthauthorizepolicy](addoauthauthorizepolicy.md) |  | Gewährt oauth/authorize den Rollen, mit denen sich Benutzer anmelden (Upgrade auf 6.0.15; --dry-run listet sie zuerst auf) |
| [exp:audit](audit.md) |  | Audit-Log: die letzten Einträge, ein Ereignis anzeigen, die Hash-Ketten prüfen, die Kanäle auflisten |
| [exp:benchmark](benchmark.md) | exp:bench | Misst diese Installation: Seitenzeiten über HTTP, A/B zwischen Servern, Kernel-Messungen, Regressionsprüfung |
| [exp:cache](cache.md) |  | Caches nach Tag, ID oder alle leeren und untersuchen: Inhalte, statisch, HTTP, Velocity, OPcache, APCu |
| [exp:changelog2xmltext](changelog2xmltext.md) |  | Wandelt eine Changelog-Datei in das XML-Textformat um und gibt sie auf der Standardausgabe aus |
| [exp:checkclasses](checkclasses.md) |  | Lädt jede deklarierte Klasse und meldet die, deren Laden PHP verweigert |
| [exp:checkcodestyle](checkcodestyle.md) |  |  |
| [exp:checkdbfiles](checkdbfiles.md) |  | Prüft die Datenbank-Update-Dateien gegen den Upgrade-Pfad; meldet fehlende oder überzählige Dateien |
| [exp:checkmanifest](checkmanifest.md) |  |  |
| [exp:cleanuppolicies](cleanuppolicies.md) |  | Entfernt Richtlinien von Modulen, die es nicht mehr gibt (--dry-run listet sie zuerst auf) |
| [exp:cleanuprss](cleanuprss.md) |  | Kürzt per RSS importierte Inhalte und behält die neuesten Einträge jedes aktiven Imports |
| [exp:cleanupversions](cleanupversions.md) |  | Entfernt archivierte Inhaltsversionen über die Grenzen von VersionManagement hinaus (-n: nicht warten) |
| [exp:clusterize](clusterize.md) |  | Verschiebt die Dateien aus var in den Cluster-Speicher der Datenbank (-u verschiebt sie zurück) |
| [exp:clusterpurge](clusterpurge.md) |  | Löscht die Cluster-Dateien der angegebenen Bereiche physisch (--scopes=scope1,scope2) |
| [exp:collaborationsampledata](collaborationsampledata.md) | exp:collaboration:sample-data | Legt Beispielinhalte für das Werkzeug zur Zusammenarbeit an (Freigaben, Kommentare, Gruppen) oder entfernt sie wieder |
| [exp:contentcheck](contentcheck.md) | exp:content-check | Inhaltsprüfungen: den Referenzindex aufbauen, die ungenutzten Assets und die defekten Verknüpfungen unter einem Knoten auflisten, die fehlenden Dateien auflisten, die Speicherdateien durchsuchen (nur ein Bericht, außer mit --fix, --quarantine oder --fix-permissions ohne --dry-run) |
| [exp:convertezenumtoezselection](convertezenumtoezselection.md) |  |  |
| [exp:convertprice2multiprice](convertprice2multiprice.md) |  | Wandelt Inhaltsobjekte mit Attributen vom Datentyp 'price' in 'multiprice' um |
| [exp:dfscleanup](dfscleanup.md) |  | Gleicht Datenbank und DFS-Dateien miteinander ab (-S, -B) und löscht nicht vorhandene (-D) |
| [exp:expcontentjob](expcontentjob.md) |  | Inhaltsaufträge ausführen, auflisten, fortsetzen und abbrechen: große Teilbäume stapelweise entfernen und kopieren |
| [exp:expfetch](expfetch.md) | exp:info, exp:about | Die Systemkarte: das Exponential-„e“ neben den Fakten dieser Installation, ihrer Engine und ihres Rechners, mit Hinweisen zum Zustand (exp:info ergänzt ein Inventar, exp:about das Projekt) |
| [exp:exphelp](exphelp.md) | exp:help | Das Hilfesystem auf der Kommandozeile: den Index von doc/ aufbauen und beschreiben, darin suchen, die Metadaten einer Seite zeigen, ihre Links und Bilder prüfen |
| [exp:exphelpcontent](exphelpcontent.md) | exp:help:content | Hilfe als Inhalt gepflegt: die Hilfeklassen, Sektionen, Zustände, den Baum und die Rollen anlegen (idempotent), die Themen gegen den Styleguide prüfen, das Glossar und die Themen befüllen, berichten und deinstallieren |
| [exp:exphelpexamples](exphelpexamples.md) | exp:help:examples | Erzeugt die Referenzbeispiele der Hilfe unter doc/eng-us/examples/ aus dem Code: Template-Operatoren und -Funktionen, Fetch-Funktionen, Modulansichten, Richtlinien, INI-Einstellungen, Datentypen, Workflow-Ereignisse, Cronjobs, Konsolenbefehle und Erweiterungspunkte |
| [exp:explogs](explogs.md) | exp:logs | Der Log-Viewer auf der Kommandozeile: die Logdateien auflisten, einer folgen, sie und ihre Rotationen durchsuchen, eine Fehlerreferenz und die Zeilen ihrer Anfrage finden (standardmäßig maskiert); Logs rotieren und ihre Archive bereinigen |
| [exp:exploremipsumfill](exploremipsumfill.md) | exp:loremipsum | Fill a translation of objects in a test language (Lorem Ipsum, qlo-ZZ) with lorem ipsum text |
| [exp:explovestack](explovestack.md) | exp:lovestack | Check an installation coming from LoveStack (read-only), and record its database as Exponential's (migrate) |
| [exp:exppatterncatalogue](exppatterncatalogue.md) |  |  |
| [exp:exprepair](exprepair.md) |  | Repair queue of the missing-libraries error page: --create-key, --disable, --status, --run |
| [exp:ezapplytemplate](ezapplytemplate.md) |  | Apply the template blocks found in files and write the files back (exit code 0, 1 or 2) |
| [exp:ezasynchronouspublisher](ezasynchronouspublisher.md) |  |  |
| [exp:ezcache](ezcache.md) |  | Clear Exponential caches by tag or id (--clear-tag, --clear-id, --clear-all, --list-tags) |
| [exp:ezcheckphptag](ezcheckphptag.md) |  | Check files for characters before the PHP start tag or after the end tag |
| [exp:ezchecktranslation](ezchecktranslation.md) |  | Show statistics for one translation, e.g. ezchecktranslation.php ita-IT |
| [exp:ezcontentcache](ezcontentcache.md) |  | Clear content caches of given nodes or subtrees (--clear-node, --clear-subtree) |
| [exp:ezconvert2isbn13](ezconvert2isbn13.md) |  | Convert ISBN-10 numbers to ISBN-13 |
| [exp:ezconvertdbcharset](ezconvertdbcharset.md) |  | Convert all Exponential database tables to use UTF-8 character set |
| [exp:ezconvertmysqltabletype](ezconvertmysqltabletype.md) |  | Convert the tables of a MySQL database to another table type (--list, --newtype=TYPE) |
| [exp:ezcreatedeactivatedusergroup](ezcreatedeactivatedusergroup.md) |  | Create the user group for deactivated accounts ("Deactivate and move") ahead of first use |
| [exp:ezcsvexport](ezcsvexport.md) |  | Export the subtree below a node to one CSV file per content class (--storage-dir) |
| [exp:ezcsvimport](ezcsvimport.md) |  | Import content objects of a class below a node from a CSV file (--class, --creator) |
| [exp:ezexec](ezexec.md) |  | Run a PHP script with the Exponential bootstrap done: ezexec.php myscript.php |
| [exp:ezflowupgrade](ezflowupgrade.md) |  | Upgrade the installed ezflow packages (--to-version, --package, --url) |
| [exp:ezgeneratetranslationcache](ezgeneratetranslationcache.md) |  | Generate translation cache files (--ts-list limits it to the given translations) |
| [exp:ezimportdbafile](ezimportdbafile.md) |  | Import the dba data of a datatype into the database (--datatype=ezisbn) |
| [exp:ezpgenerateautoloads](ezpgenerateautoloads.md) |  | Generate the class autoload arrays: kernel (-k), kernel overrides (-o), extensions (-e) |
| [exp:ezpublishingbenchmark](ezpublishingbenchmark.md) |  | Benchmark parallel publishing in concurrent batches (--batches-count, -l level) |
| [exp:ezrequestrules](ezrequestrules.md) |  | List and check the request rules of a siteaccess and explain their decision for an address |
| [exp:ezsessiongc](ezsessiongc.md) |  | Remove expired sessions as defined by site.ini [Session] SessionTimeout |
| [exp:ezsqldiff](ezsqldiff.md) |  | Show the differences between two database schemas; the exit code tells if they differ |
| [exp:ezsqldumpisbndata](ezsqldumpisbndata.md) |  | Dump the ISBN group and range tables as SQL to a file or standard output |
| [exp:ezsqldumpschema](ezsqldumpschema.md) |  | Dump the database schema to a file or standard output (--type, --user) |
| [exp:ezsqlinsertschema](ezsqlinsertschema.md) |  | Insert a database schema and data from a .dba file into a database (--type, --user) |
| [exp:ezsubtreecopy](ezsubtreecopy.md) |  | Copy a content subtree to a new location (--src-node-id, --dst-node-id) |
| [exp:ezsubtreeremove](ezsubtreeremove.md) |  | Remove content subtrees by node ID, to the trash unless --ignore-trash is given |
| [exp:eztc](eztc.md) |  | Compile the templates of a siteaccess into the template cache (--force recompiles all) |
| [exp:eztemplatecheck](eztemplatecheck.md) |  | Check the syntax of the templates of a siteaccess or of a design directory |
| [exp:ezwebincommon](ezwebincommon.md) |  | Helper library of ezwebininstall and ezwebinupgrade, not meant to be run directly |
| [exp:ezwebininstall](ezwebininstall.md) |  | Install the ezwebin site package and configure the site |
| [exp:ezwebinupgrade](ezwebinupgrade.md) |  | Upgrade an existing ezwebin installation to the current package version (--to-version) |
| [exp:flatten](flatten.md) |  | Remove unused data from the database: contentobject, contentclass, workflow, role or all |
| [exp:frankenphp](frankenphp.md) | exp:fp | Control the FrankenPHP engine of the Exponential web server |
| [exp:i18n](i18n.md) |  | The translation fallbacks: per locale the chain of translations it falls back to, and how much of the kernel and of each extension it shows translated; --check for CI |
| [exp:ini](ini.md) |  | Read and change INI settings in every scope: get, set, add, rem, clear, toggle, copy, move, where, list, scopes |
| [exp:install](install.md) | exp:names | One-command installation, SQLite by default, no kickstart.ini needed |
| [exp:kickstarter](kickstarter.md) |  | Kickstarter runner. Supports "ini" generation and full "run" setup subcommands |
| [exp:mailbounces](mailbounces.md) | exp:mail:bounces | Read the bounce mailbox: hard bounces and spam complaints go on the suppression list |
| [exp:mailconsent](mailconsent.md) | exp:mail:consent | The consent log of the e-mail preferences: list, export as CSV, retention cleanup |
| [exp:mailgate](mailgate.md) | exp:mail:gate | Test the mail gate: who of the given addresses would get a mail of a category, and what the decorated mail looks like |
| [exp:mailpreferences](mailpreferences.md) | exp:mail:preferences | Show and change the e-mail preferences of a user or an address: categories, master switch, frequency, export, erase |
| [exp:mailstatus](mailstatus.md) | exp:mail:status | Show the e-mail preference system: tables, gate, footer, categories, suppression, consent log, the gate's last decisions and problems |
| [exp:mailsuppression](mailsuppression.md) | exp:mail:suppression | The suppression list of the e-mail gate: list, check, add and lift entries (only hashes are stored) |
| [exp:maintenance](maintenance.md) |  | Take the site offline for maintenance and back: on, off or status |
| [exp:makestaticcache](makestaticcache.md) |  | Crawl the public siteaccesses and generate static pages (--site, --max-pages, --max-depth) |
| [exp:notificationevents](notificationevents.md) | exp:notification:events, exp:notify:events | List the notification events, or remove the old ones |
| [exp:notificationrun](notificationrun.md) | exp:notification:run, exp:notify:run | Run the notification filter: send the notifications of the pending events and the due digests (--dry-run: only list them) |
| [exp:notificationstatus](notificationstatus.md) | exp:notification:status, exp:notify:status | Show the notification system: pending events, digest items, subscriptions, the last runs and problems |
| [exp:notificationsubscriptions](notificationsubscriptions.md) | exp:notification:subscriptions, exp:notify:subscriptions | List the subtree notification subscriptions per user and subtree, or remove those whose content is gone |
| [exp:phar](phar.md) |  | Build and inspect the engine archive dist/engine.phar (verbs build, check, clean, info) |
| [exp:preload](preload.md) |  | Warm the page caches of a site: request its section pages, then follow its links, and report the broken ones |
| [exp:preloadjob](preloadjob.md) |  |  |
| [exp:publish_content](publish_content.md) |  |  |
| [exp:resetuserpassword](resetuserpassword.md) |  | Reset a user password with admin authentication or root bypass |
| [exp:solr](solr.md) | exp:search | Control the Solr search server, once it is installed and configured |
| [exp:systeminfo](systeminfo.md) | exp:system:info | The facts and health checks of Setup > System information, as text or JSON (--json, --sizes) |
| [exp:trashpurge](trashpurge.md) |  | Permanently delete all objects in the trash |
| [exp:updateisbn13](updateisbn13.md) |  | Update the ISBN range data in the database from an XML file (--url is required) |
| [exp:updateniceurls](updateniceurls.md) |  | Import old URL aliases and update the URL aliases of all nodes (--update-nodes) |
| [exp:updatesearchindex](updatesearchindex.md) |  | Reindex all content objects in the search engine |
| [exp:userlogincheck](userlogincheck.md) | exp:user-login-check | Unique logins: list the logins more than one user account has; --fix-index adds the unique index on ezuser.login once there are none |
| [exp:velocity-router](velocity-router.md) |  |  |
| [exp:velocity-warmup](velocity-warmup.md) |  |  |
| [exp:velocity](velocity.md) | exp:vc |  |
| [exp:verify_aliases](verify_aliases.md) |  |  |
| [exp:warm](warm.md) |  | Request every published page so no visitor pays for a render (--limit, --concurrency) |
| [exp:webserver](webserver.md) |  | Control the Exponential web server (php, frankenphp or qbix engines) |

## Shell-Skripte

`bin/shell/<name>.sh` läuft als `shell:<name>`:

| Befehl | Datei | Was er tut |
|---|---|---|
| `shell:checkcalltimeref` | `bin/shell/checkcalltimeref.sh` | Prüft PHP-Quelldateien auf veraltete Übergabe per Referenz beim Aufruf (call-time pass-by-reference) |
| `shell:checkcode` | `bin/shell/checkcode.sh` | Prüft PHP-Quelldateien auf Verstöße gegen den Codierstandard durch kurze Open-Tags |
| `shell:checkdbschema` | `bin/shell/checkdbschema.sh` | Prüft die Datenbankschema-Dateien gegen das erwartete Basisschema |
| `shell:checkdbupdate` | `bin/shell/checkdbupdate.sh` | Prüft die Datenbank-Update-Dateien auf Übereinstimmung mit der aktuellen Version |
| `shell:checkforeach` | `bin/shell/checkforeach.sh` | Prüft die Syntax von PHP-foreach-Schleifen auf Einhaltung des Exponential-Codierstandards |
| `shell:checkini` | `bin/shell/checkini.sh` | Findet INI-Konfigurationsdateien, die sich nicht als gültiges PHP parsen lassen |
| `shell:checkprint` | `bin/shell/checkprint.sh` | Prüft PHP-Quelldateien auf Verwendung von print() |
| `shell:checkversionnumbers` | `bin/shell/checkversionnumbers.sh` | Prüft die Versionsnummern in den Quelldateien auf Übereinstimmung (veraltet seit 5.0) |
| `shell:clearcache` | `bin/shell/clearcache.sh` | Veraltet: Leeren Sie die Caches der Website stattdessen mit bin/php/ezcache.php |
| `shell:common` | `bin/shell/common.sh` |  |
| `shell:creatediff` | `bin/shell/creatediff.sh` | Erstellt ein Patch-/Diff-Archiv zwischen zwei Exponential-Releases |
| `shell:distcommon` | `bin/shell/distcommon.sh` | Gemeinsame Funktionsbibliothek, die die Skripte zum Bau der Distribution einbinden (nicht ausführbar) |
| `shell:extensionscommon` | `bin/shell/extensionscommon.sh` | Gemeinsame Funktionsbibliothek, die die Skripte zum Paketieren von Erweiterungen einbinden (nicht ausführbar) |
| `shell:fixlicense` | `bin/shell/fixlicense.sh` | Aktualisiert alte Lizenz-URLs von eZ Systems in PHP-Quelldateien |
| `shell:generatefilelist` | `bin/shell/generatefilelist.sh` | Erzeugt die Prüfsummen der Dateien in share/filelist.md5 für das aktuelle Release |
| `shell:i18ncontrol` | `bin/shell/i18ncontrol.sh` | Steuert und verwaltet die Quelldateien der i18n-Übersetzungen |
| `shell:i18nstats` | `bin/shell/i18nstats.sh` | Zeigt Statistiken zur Übersetzungsabdeckung für die angegebenen Locales |
| `shell:makeaddonpackages` | `bin/shell/makeaddonpackages.sh` | Baut Zusatzpakete zur Aufnahme in ein Distributions-Release |
| `shell:makedist` | `bin/shell/makedist.sh` | Baut ein vollständiges Distributionspaket eines Exponential-Releases (veraltet seit 5.0) |
| `shell:makedoc` | `bin/shell/makedoc.sh` | Speichert die laufende Website als statisches HTML für eine Offline-Dokumentation |
| `shell:makei18ndist` | `bin/shell/makei18ndist.sh` | Baut Distributionspakete der i18n-Übersetzungen für die angegebenen Locales |
| `shell:makestylepackages` | `bin/shell/makestylepackages.sh` | Baut Distributionspakete für Styles und Designs |
| `shell:packagescommon` | `bin/shell/packagescommon.sh` | Gemeinsame Funktionsbibliothek, die die Skripte zum Bau von Paketen einbinden (nicht ausführbar) |
| `shell:packext` | `bin/shell/packext.sh` | Packt ein Erweiterungsverzeichnis in ein verteilbares Archiv |
| `shell:phpcheck` | `bin/shell/phpcheck.sh` | Findet PHP-Quelldateien mit Syntaxfehlern und gibt ihre Pfade aus |
| `shell:redumpall` | `bin/shell/redumpall.sh` | Schreibt alle SQL-Datenbankschemata und Startdaten neu in SQL-Dateien |
| `shell:setdistribution` | `bin/shell/setdistribution.sh` | Setzt Name und Version der Distribution für den Bau eines Releases |
| `shell:setupdist` | `bin/shell/setupdist.sh` | Richtet eine Build-Umgebung für die Distribution und die Verzeichnisstruktur des Staging-Bereichs ein |
| `shell:sqlcommon` | `bin/shell/sqlcommon.sh` | Gemeinsame Variablen für die Datenbankverbindung, die SQL-Skripte einbinden (nicht ausführbar) |
| `shell:sqlredump` | `bin/shell/sqlredump.sh` | Schreibt ein bestimmtes SQL-Schema oder einen Datenbestand neu in eine benannte Ausgabedatei |
| `shell:stats` | `bin/shell/stats.sh` | Zeigt Code-Statistiken des Repositorys, darunter Zeilenzahlen und Dateisummen |
| `shell:updateisbn13` | `bin/shell/updateisbn13.sh` | Lädt aktualisierte Verlagspräfixe für ISBN-13 herunter und importiert sie in die Datenbank |
| `shell:updatelicense` | `bin/shell/updatelicense.sh` | Aktualisiert die Lizenzköpfe in allen Quelldateien |
| `shell:updatetranslations` | `bin/shell/updatetranslations.sh` | Lädt die Übersetzungsdateien für alle konfigurierten Locales herunter und aktualisiert sie |
| `shell:verifyfiles` | `bin/shell/verifyfiles.sh` | Prüft die Integrität der Dateien des Repositorys gegen die Prüfsummen in share/filelist.md5 |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/console`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
