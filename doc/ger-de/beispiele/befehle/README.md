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
| [exp:addanonymouspublicpolicies](addanonymouspublicpolicies.md) |  | Gibt der Rolle Anonymous die öffentlichen Richtlinien, die ihr fehlen: user/register, rss/feed, tags/view, die öffentlichen Views des Newsletters (Probelauf, außer mit --apply) |
| [exp:adddefaultstates](adddefaultstates.md) |  | Gibt jedem Inhaltsobjekt den Standardzustand jeder Zustandsgruppe, aus der es keinen Zustand hat (Probelauf, außer mit --apply) |
| [exp:addeditorpolicies](addeditorpolicies.md) |  | Gibt der Rolle Editor die alltäglichen Richtlinien, die ihr fehlen: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, den Bildeditor, das eigene Konto (Probelauf, außer mit --apply) |
| [exp:addexphelpreadpolicy](addexphelpreadpolicy.md) | exp:help-grant | Gewährt exphelp/read (das Hilfesystem) der Rolle Editor oder den genannten Rollen (Probelauf, außer mit --apply) |
| [exp:addmembermanagement](addmembermanagement.md) |  | Richtet die Mitgliederverwaltung nach membermanagement.ini ein: die Rolle, mit der eine Gruppe (Standard Editors) die Konten einer anderen (Standard Members) verwaltet, nie Administrator users oder Editors (Probelauf, außer mit --apply) |
| [exp:addoauthauthorizepolicy](addoauthauthorizepolicy.md) |  | Gewährt oauth/authorize den Rollen, die Benutzern das Anmelden erlauben (Upgrade auf 6.0.15; Probelauf, außer mit --apply) |
| [exp:adduserapproval](adduserapproval.md) |  | Richtet die Freigabe neuer Konten in einer Benutzergruppe ein (Standard: neue Editors warten auf die Administrator users): das Workflow-Ereignis auf content/publish vorher und die Rolle, mit der die Anfragenden solche Konten anlegen dürfen; --mode=section richtet die optionale Alternative ein, einen Abschnitt Staff mit dem einfachen Freigabe-Ereignis (beides ein Probelauf, außer mit --apply) |
| [exp:audit](audit.md) |  | Audit-Log: die letzten Einträge, ein Ereignis anzeigen, die Hash-Ketten prüfen, die Kanäle auflisten |
| [exp:benchmark](benchmark.md) | exp:bench | Misst diese Installation: Seitenzeiten über HTTP, A/B zwischen Servern, Kernel-Messungen, Regressionsprüfung |
| [exp:cache](cache.md) |  | Caches nach Tag, ID oder alle leeren und untersuchen: Inhalte, statisch, HTTP, Velocity, OPcache, APCu |
| [exp:changelog2xmltext](changelog2xmltext.md) |  | Wandelt eine Changelog-Datei in das XML-Textformat um und gibt sie auf der Standardausgabe aus |
| [exp:checkclasses](checkclasses.md) |  | Lädt jede deklarierte Klasse und meldet die, deren Laden PHP verweigert |
| [exp:checkcodestyle](checkcodestyle.md) |  |  |
| [exp:checkdbfiles](checkdbfiles.md) |  | Prüft die Datenbank-Update-Dateien gegen den Upgrade-Pfad; meldet fehlende oder überzählige Dateien |
| [exp:checkmanifest](checkmanifest.md) |  |  |
| [exp:cleanuppolicies](cleanuppolicies.md) |  | Entfernt Richtlinien von Modulen, die es nicht mehr gibt (Probelauf, außer mit --apply) |
| [exp:cleanuprss](cleanuprss.md) |  | Kürzt per RSS importierte Inhalte und behält die neuesten Einträge jedes aktiven Imports (Probelauf, außer mit --apply) |
| [exp:cleanupversions](cleanupversions.md) |  | Entfernt archivierte Inhaltsversionen jenseits der Grenzen von VersionManagement (Probelauf, außer mit --apply; -n: nicht warten) |
| [exp:clusterize](clusterize.md) |  | Verschiebt die Dateien aus var in den Cluster-Speicher der Datenbank (-u verschiebt sie zurück) |
| [exp:clusterpurge](clusterpurge.md) |  | Löscht die Cluster-Dateien der angegebenen Bereiche physisch (--scopes=scope1,scope2; Probelauf, außer mit --apply) |
| [exp:collaborationsampledata](collaborationsampledata.md) | exp:collaboration:sample-data | Legt Beispielinhalte für das Kollaborationswerkzeug an (Freigaben, Kommentare, Gruppen) oder entfernt sie wieder (Probelauf, außer mit --apply) |
| [exp:configphp](configphp.md) | exp:config:list, exp:config:get, exp:config:set, exp:config:unset | Listet, liest, setzt und entfernt die define()-Einstellungen der config.php, mit Typ und mit php -l geprüft, beschrieben durch config.php-RECOMMENDED (set und unset: Probelauf ohne --apply) |
| [exp:contentcheck](contentcheck.md) | exp:content-check | Inhaltsprüfungen: den Referenzindex aufbauen, die ungenutzten Assets und die defekten Beziehungen unter einem Knoten auflisten, die fehlenden Dateien auflisten, die Speicherdateien durchsuchen (nur Bericht; --fix, --quarantine und --fix-permissions sind ein Probelauf, außer mit --apply) |
| [exp:convertezenumtoezselection](convertezenumtoezselection.md) |  |  |
| [exp:convertprice2multiprice](convertprice2multiprice.md) |  | Wandelt Inhaltsobjekte mit Attributen vom Datentyp 'price' in 'multiprice' um |
| [exp:dfscleanup](dfscleanup.md) |  | Gleicht Datenbank und DFS-Dateien miteinander ab (-S, -B) und löscht nicht vorhandene (-D) |
| [exp:expbackfillordersiteaccess](expbackfillordersiteaccess.md) |  | Leitet den Siteaccess von Bestellungen ab, die vor der Erfassung von ezorder.siteaccess aufgegeben wurden (Probelauf ohne --apply) |
| [exp:expcontentjob](expcontentjob.md) |  | Inhaltsaufträge ausführen, auflisten, fortsetzen und abbrechen: große Teilbäume stapelweise entfernen und kopieren |
| [exp:expfetch](expfetch.md) | exp:info, exp:about | Die Systemkarte: das Exponential-„e“ neben den Fakten dieser Installation, ihrer Engine und ihres Rechners, mit Hinweisen zum Zustand (exp:info ergänzt ein Inventar, exp:about das Projekt) |
| [exp:exphelp](exphelp.md) | exp:help | Das Hilfesystem auf der Kommandozeile: den Index von doc/ aufbauen und beschreiben, darin suchen, die Metadaten einer Seite zeigen, ihre Links und Bilder prüfen |
| [exp:exphelpcontent](exphelpcontent.md) | exp:help:content | Hilfe als Inhalt: die Hilfeklassen, Abschnitte, Zustände, den Baum und die Rollen anlegen (idempotent), die Themen gegen den Stilleitfaden prüfen, das Glossar und Themen anlegen, berichten und deinstallieren (die ändernden Aktionen sind ein Probelauf, außer mit --apply) |
| [exp:exphelpexamples](exphelpexamples.md) | exp:help:examples | Erzeugt die Referenzbeispiele der Hilfe unter doc/eng-us/examples/ aus dem Code: Template-Operatoren und -Funktionen, Fetch-Funktionen, Modulansichten, Richtlinien, INI-Einstellungen, Datentypen, Workflow-Ereignisse, Cronjobs, Konsolenbefehle und Erweiterungspunkte |
| [exp:explogs](explogs.md) | exp:logs | Der Log-Viewer auf der Kommandozeile: die Log-Dateien auflisten, einer folgen, sie und ihre Rotationen durchsuchen, eine Fehlerreferenz und die Zeilen ihrer Anfrage finden (standardmäßig maskiert); Logs rotieren und ihre Archive aufräumen (Probelauf, außer mit --apply) |
| [exp:exploremipsumfill](exploremipsumfill.md) | exp:loremipsum | Füllt eine Übersetzung von Objekten in einer Testsprache (Lorem Ipsum, qlo-ZZ) mit Lorem-ipsum-Text (Probelauf, außer mit --apply) |
| [exp:explovestack](explovestack.md) | exp:lovestack | Prüft eine von LoveStack kommende Installation (nur lesend) und trägt ihre Datenbank als die von Exponential ein (migrate) |
| [exp:exppatterncatalogue](exppatterncatalogue.md) |  |  |
| [exp:exprepair](exprepair.md) |  | Reparaturwarteschlange der Fehlerseite für fehlende Bibliotheken: --create-key, --disable, --status, --run |
| [exp:ezapplytemplate](ezapplytemplate.md) |  | Wendet die in Dateien gefundenen Template-Blöcke an und schreibt die Dateien zurück (Exit-Code 0, 1 oder 2) |
| [exp:ezasynchronouspublisher](ezasynchronouspublisher.md) |  |  |
| [exp:ezcache](ezcache.md) |  | Leert die Caches von Exponential nach Tag oder ID (--clear-tag, --clear-id, --clear-all, --list-tags) |
| [exp:ezcheckphptag](ezcheckphptag.md) |  | Prüft Dateien auf Zeichen vor dem PHP-Start-Tag oder nach dem End-Tag |
| [exp:ezchecktranslation](ezchecktranslation.md) |  | Zeigt Statistiken zu einer Übersetzung, z. B. ezchecktranslation.php ita-IT |
| [exp:ezcontentcache](ezcontentcache.md) |  | Leert die Inhalts-Caches der angegebenen Knoten oder Teilbäume (--clear-node, --clear-subtree) |
| [exp:ezconvert2isbn13](ezconvert2isbn13.md) |  | Wandelt ISBN-10-Nummern in ISBN-13 um |
| [exp:ezconvertdbcharset](ezconvertdbcharset.md) |  | Stellt alle Datenbanktabellen von Exponential auf den Zeichensatz UTF-8 um |
| [exp:ezconvertmysqltabletype](ezconvertmysqltabletype.md) |  | Wandelt die Tabellen einer MySQL-Datenbank in einen anderen Tabellentyp um (--list, --newtype=TYPE) |
| [exp:ezcreatedeactivatedusergroup](ezcreatedeactivatedusergroup.md) |  | Legt die Benutzergruppe für deaktivierte Konten („Deaktivieren und verschieben“) schon vor der ersten Verwendung an |
| [exp:ezcsvexport](ezcsvexport.md) |  | Exportiert den Teilbaum unter einem Knoten in eine CSV-Datei pro Inhaltsklasse (--storage-dir) |
| [exp:ezcsvimport](ezcsvimport.md) |  | Importiert Inhaltsobjekte einer Klasse unter einen Knoten aus einer CSV-Datei (--class, --creator) |
| [exp:ezexec](ezexec.md) |  | Führt ein PHP-Skript nach dem Bootstrap von Exponential aus: ezexec.php myscript.php |
| [exp:ezflowupgrade](ezflowupgrade.md) |  | Aktualisiert die installierten ezflow-Pakete (--to-version, --package, --url) |
| [exp:ezgeneratetranslationcache](ezgeneratetranslationcache.md) |  | Erzeugt die Cache-Dateien der Übersetzungen (--ts-list beschränkt es auf die angegebenen Übersetzungen) |
| [exp:ezimportdbafile](ezimportdbafile.md) |  | Importiert die dba-Daten eines Datentyps in die Datenbank (--datatype=ezisbn) |
| [exp:ezpgenerateautoloads](ezpgenerateautoloads.md) |  | Erzeugt die Autoload-Arrays der Klassen: Kernel (-k), Kernel-Overrides (-o), Erweiterungen (-e) |
| [exp:ezpublishingbenchmark](ezpublishingbenchmark.md) |  | Misst paralleles Veröffentlichen in gleichzeitigen Stapeln (--batches-count, -l level) |
| [exp:ezrequestrules](ezrequestrules.md) |  | Listet und prüft die Anfrageregeln eines Siteaccess und erklärt ihre Entscheidung für eine Adresse |
| [exp:ezsessiongc](ezsessiongc.md) |  | Entfernt abgelaufene Sitzungen gemäß site.ini [Session] SessionTimeout |
| [exp:ezsqldiff](ezsqldiff.md) |  | Zeigt die Unterschiede zwischen zwei Datenbankschemata; der Exit-Code sagt, ob sie sich unterscheiden |
| [exp:ezsqldumpisbndata](ezsqldumpisbndata.md) |  | Schreibt die ISBN-Gruppen- und -Bereichstabellen als SQL in eine Datei oder auf die Standardausgabe |
| [exp:ezsqldumpschema](ezsqldumpschema.md) |  | Schreibt das Datenbankschema in eine Datei oder auf die Standardausgabe (--type, --user) |
| [exp:ezsqlinsertschema](ezsqlinsertschema.md) |  | Fügt ein Datenbankschema und Daten aus einer .dba-Datei in eine Datenbank ein (--type, --user) |
| [exp:ezsubtreecopy](ezsubtreecopy.md) |  | Kopiert einen Inhaltsteilbaum an einen neuen Ort (--src-node-id, --dst-node-id; Probelauf, außer mit --apply) |
| [exp:ezsubtreeremove](ezsubtreeremove.md) |  | Entfernt Inhaltsteilbäume nach Knoten-ID, in den Papierkorb, außer mit --ignore-trash (Probelauf, außer mit --apply) |
| [exp:eztc](eztc.md) |  | Kompiliert die Templates eines Siteaccess in den Template-Cache (--force kompiliert alle neu) |
| [exp:eztemplatecheck](eztemplatecheck.md) |  | Prüft die Syntax der Templates eines Siteaccess oder eines Design-Verzeichnisses |
| [exp:ezwebincommon](ezwebincommon.md) |  | Hilfsbibliothek von ezwebininstall und ezwebinupgrade, nicht zum direkten Ausführen gedacht |
| [exp:ezwebininstall](ezwebininstall.md) |  | Installiert das Seitenpaket ezwebin und konfiguriert die Website |
| [exp:ezwebinupgrade](ezwebinupgrade.md) |  | Aktualisiert eine vorhandene ezwebin-Installation auf die aktuelle Paketversion (--to-version) |
| [exp:flatten](flatten.md) |  | Entfernt ungenutzte Daten aus der Datenbank: contentobject, contentclass, workflow, role oder all |
| [exp:frankenphp](frankenphp.md) | exp:fp | Steuert die FrankenPHP-Engine des Exponential-Webservers |
| [exp:i18n](i18n.md) |  | Die Übersetzungs-Fallbacks: pro Locale die Kette der Übersetzungen, auf die sie zurückfällt, und wie viel vom Kernel und von jeder Erweiterung übersetzt angezeigt wird; --check für CI |
| [exp:ini](ini.md) |  | INI-Einstellungen in jedem Bereich lesen und ändern: get, set, add, rem, clear, toggle, copy, move, where, list, scopes |
| [exp:install](install.md) | exp:names | Installation mit einem Befehl, standardmäßig SQLite, keine kickstart.ini nötig (ein Probelauf, außer mit --apply) |
| [exp:installcrontab](installcrontab.md) |  | Den Cronjob der Installation zeigen, hinzufügen oder entfernen: die Crontab-Zeilen, die ihre Cronjob-Teile ausführen (--add und --remove: Probelauf, außer mit --apply) |
| [exp:kickstarter](kickstarter.md) |  | Kickstarter: "ini" schreibt kickstart.ini, "run" installiert daraus (ein Probelauf, außer mit --apply) |
| [exp:mailbounces](mailbounces.md) | exp:mail:bounces | Liest das Bounce-Postfach: harte Bounces und Spam-Beschwerden kommen auf die Sperrliste (Probelauf, außer mit --apply) |
| [exp:mailconsent](mailconsent.md) | exp:mail:consent | Das Einwilligungsprotokoll der E-Mail-Einstellungen: auflisten, als CSV exportieren, nach Aufbewahrungsfrist aufräumen (Probelauf, außer mit --apply) |
| [exp:mailgate](mailgate.md) | exp:mail:gate | Testet das Mail-Gate: wer von den angegebenen Adressen eine Mail einer Kategorie bekäme und wie die ergänzte Mail aussieht |
| [exp:mailpreferences](mailpreferences.md) | exp:mail:preferences | Zeigt und ändert die E-Mail-Einstellungen eines Benutzers oder einer Adresse: Kategorien, Hauptschalter, Häufigkeit, Export, Löschen |
| [exp:mailstatus](mailstatus.md) | exp:mail:status | Zeigt das System der E-Mail-Einstellungen: Tabellen, Gate, Fußzeile, Kategorien, Sperrliste, Einwilligungsprotokoll, die letzten Entscheidungen des Gates und Probleme |
| [exp:mailsuppression](mailsuppression.md) | exp:mail:suppression | Die Sperrliste des E-Mail-Gates: Einträge auflisten, prüfen, hinzufügen und aufheben (es werden nur Hashes gespeichert) |
| [exp:maintenance](maintenance.md) |  | Nimmt die Website für Wartungsarbeiten vom Netz und wieder zurück: on, off oder status |
| [exp:makestaticcache](makestaticcache.md) |  | Durchläuft die öffentlichen Siteaccesses und erzeugt statische Seiten (--site, --max-pages, --max-depth) |
| [exp:notificationevents](notificationevents.md) | exp:notification:events, exp:notify:events | Die Benachrichtigungsereignisse auflisten oder die alten entfernen (Probelauf, außer mit --apply) |
| [exp:notificationrun](notificationrun.md) | exp:notification:run, exp:notify:run | Den Benachrichtigungsfilter ausführen: die Benachrichtigungen der wartenden Ereignisse und die fälligen Zusammenfassungen senden (Probelauf, außer mit --apply) |
| [exp:notificationstatus](notificationstatus.md) | exp:notification:status, exp:notify:status | Zeigt das Benachrichtigungssystem: anstehende Ereignisse, Einträge der Zusammenfassungen, Abonnements, die letzten Läufe und Probleme |
| [exp:notificationsubscriptions](notificationsubscriptions.md) | exp:notification:subscriptions, exp:notify:subscriptions | Die Teilbaum-Benachrichtigungsabonnements je Benutzer und Teilbaum auflisten oder die entfernen, deren Inhalt fehlt (Probelauf, außer mit --apply) |
| [exp:phar](phar.md) |  | Baut und untersucht das Engine-Archiv dist/engine.phar (Verben build, check, clean, info) |
| [exp:preload](preload.md) |  | Die Seiten-Caches einer Site vorwärmen: ihre Abschnittsseiten abrufen, dann ihren Links folgen und die defekten melden (Probelauf, außer mit --apply) |
| [exp:preloadjob](preloadjob.md) |  |  |
| [exp:publish_content](publish_content.md) |  |  |
| [exp:resetuserpassword](resetuserpassword.md) |  | Setzt das Passwort eines Benutzers zurück, mit Anmeldung als Administrator oder als root ohne Anmeldung |
| [exp:services](services.md) | exp:services:compile, exp:services:list, exp:services:show, exp:services:check | Compile, list, show and check the kernel service container (verbs compile, list, show, check) |
| [exp:setupfiles](setupfiles.md) | exp:setup:htaccess, exp:setup:config, exp:setup:status | Kopiert .htaccess_root (oder .htaccess_root_static) nach .htaccess und config.php-RECOMMENDED nach config.php, mit Diff, Sicherung und --force für eine vorhandene Datei (Probelauf ohne --apply) |
| [exp:solr](solr.md) | exp:search | Steuert den Solr-Suchserver, sobald er installiert und konfiguriert ist |
| [exp:systeminfo](systeminfo.md) | exp:system:info | Die Fakten und Zustandsprüfungen von Setup > Systeminformationen, als Text oder JSON (--json, --sizes) |
| [exp:trashpurge](trashpurge.md) |  | Löscht alle Objekte im Papierkorb endgültig (Probelauf, außer mit --apply) |
| [exp:updateisbn13](updateisbn13.md) |  | Aktualisiert die ISBN-Bereichsdaten in der Datenbank aus einer XML-Datei (--url ist erforderlich) |
| [exp:updateniceurls](updateniceurls.md) |  | Importiert alte URL-Aliase und aktualisiert die URL-Aliase aller Knoten (--update-nodes) |
| [exp:updatesearchindex](updatesearchindex.md) |  | Indiziert alle Inhaltsobjekte in der Suchmaschine neu |
| [exp:upgrade](upgrade.md) |  | Aktualisiert die Installation in einem Aufruf: jede Stufe der Reihe nach (Probelauf ohne --apply) |
| [exp:upgradebackup](upgradebackup.md) | exp:upgrade:backup | Upgrade-Sicherung: die Sicherung, die exp:upgrade vor dem ersten Schreiben macht, für sich allein (Datenbankabzug und Einstellungs-Overrides) |
| [exp:upgradecaptions](upgradecaptions.md) | exp:upgrade:captions | Upgrade-Sicherungspunkte: die sicheren Punkte zum Zurückrollen (von git_manager und von exp:upgrade), geprüft mit --verify, aufgeräumt mit --prune |
| [exp:upgradecode](upgradecode.md) | exp:upgrade:code | Upgrade-Stufe 1, Code: die Git-Checkouts auf das Ziel-Release gebracht, nur Fast-Forward, über git_manager (Probelauf ohne --apply) |
| [exp:upgradedatabase](upgradedatabase.md) | exp:upgrade:database | Upgrade-Stufe 3, Datenbank: die Schema- und Datenschritte auf jeder Datenbank-Engine (Probelauf ohne --apply) |
| [exp:upgradefinish](upgradefinish.md) | exp:upgrade:finish | Upgrade-Stufe 5, Abschluss: die Änderungen bereitstellen (exp:velocity deploy --kernel) und prüfen, dass die Site antwortet (Probelauf ohne --apply) |
| [exp:upgradelibraries](upgradelibraries.md) | exp:upgrade:libraries | Upgrade-Stufe 2, Bibliotheken: composer install aus composer.lock (oder ein Update benannter Pakete) mit Sicherheitsprüfung (Probelauf ohne --apply) |
| [exp:upgraderollback](upgraderollback.md) | exp:upgrade:rollback | Upgrade zurückrollen: Datenbank, Speicher, Einstellungen, Bibliotheken und Code auf einen Sicherungspunkt zurücksetzen (Probelauf ohne --apply) |
| [exp:upgradescripts](upgradescripts.md) | exp:upgrade:scripts | Upgrade-Stufe 4, Skripte: die PHP-Upgrade-Schritte, Richtlinien und Reparaturen (Probelauf ohne --apply) |
| [exp:upgradestatus](upgradestatus.md) | exp:upgrade:status | Upgrade-Status: jeder Upgrade-Schritt mit seinem Zustand (done, pending, not-needed, failed, unknown) |
| [exp:upgradesteps](upgradesteps.md) | exp:upgrade:steps | Upgrade-Schritte: die registrierten Schritte mit ID, Stufe, Version, Engines und Beschreibung |
| [exp:upgradeverify](upgradeverify.md) | exp:upgrade:verify | Upgrade prüfen: jeden Schritt neu erkennen und Abweichungen melden; --after: die ganze Prüfung nach einem Upgrade (Schritte, Rauchprobe-Adressen, Tests, Fehlerprotokoll, Autoloads) |
| [exp:userlogincheck](userlogincheck.md) | exp:user-login-check | Eindeutige Logins: listet die Logins auf, die mehr als ein Benutzerkonto hat; --fix-index fügt den eindeutigen Index auf ezuser.login hinzu, sobald es keine mehr gibt |
| [exp:userpasswordchangerequired](userpasswordchangerequired.md) | exp:user-password-change-required | Change password at next sign-in: show, set or clear the mark of a user account |
| [exp:velocity-router](velocity-router.md) |  |  |
| [exp:velocity-warmup](velocity-warmup.md) |  |  |
| [exp:velocity](velocity.md) | exp:vc |  |
| [exp:verify_aliases](verify_aliases.md) |  |  |
| [exp:warm](warm.md) |  | Fordert jede veröffentlichte Seite an, damit kein Besucher für das Rendern bezahlt (--limit, --concurrency) |
| [exp:webserver](webserver.md) |  | Steuert den Exponential-Webserver (Engines php, frankenphp oder qbix) |

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
