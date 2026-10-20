---
title: Glossar für Hoster
audience: hosting
category: hosting
order: 900
summary: Die Begriffe des Hosting-Bereichs der Hilfe, von Siteaccess und VarDir bis Velocity, Antwort-Cache, Kickstarter und Request-Klassifizierer, jeweils mit einer Bedeutung in einer Zeile und der Seite, die sie erklärt.
keywords: Glossar, Begriffe, Definitionen, Siteaccess, VarDir, Velocity, qbix, FrankenPHP, Antwort-Cache, View-Cache, Kickstarter, exp:install, Setup-Assistent, CheckValidity, Seitenpaket, Request-Klassifizierer, request-shield, Prüfprotokoll, DFS, Cluster, Wartungsmodus, glossary, terms, definitions, response cache, view cache, setup wizard, site package, request classifier, audit trail, cluster, maintenance mode
type: help
---
# Glossar für Hoster

Die Begriffe, die die Seiten zum Hosting verwenden, in alphabetischer Reihenfolge, jeweils mit der Seite, die sie
erklärt. Das allgemeine Glossar der Dokumentation ist [doc/en/us/glossary.md](../../../en/us/glossary.md).

| Begriff | Bedeutung | Mehr |
|---|---|---|
| Prüfprotokoll (Audit trail) | Hash-verkettete Aufzeichnung von Anmeldungen, Änderungen an Passwörtern, Rollen und Einstellungen, Löschungen und Befehlen; `exp:audit verify` prüft die Kette | [Prüfprotokoll](sicherheit/pruefprotokoll.md) |
| `CheckValidity` | Schlüssel in `[SiteAccessSettings]`; solange `true`, geht jede Anfrage an den Setup-Assistenten; der Installer setzt ihn auf `false` | [Der Setup-Assistent auf Shared Hosting](bereitstellung/der-setup-assistent-auf-shared-hosting.md) |
| Cluster (DFS) | mehrere Webknoten, die sich den Dateispeicher über die Cluster-Handler aus `file.ini` teilen | [Clustering und gemeinsamer Speicher](betrieb/clustering-und-gemeinsamer-speicher.md) |
| Content Security Policy (CSP) | die Browser-Richtlinie aus `site.ini [ContentSecurityPolicy]`; in der Administration durchgesetzt, sobald ihre Berichte sauber sind | [Content Security Policy](sicherheit/content-security-policy.md) |
| `CreateSites` | der Installationsschritt, der die Datenbank, die Pakete und die Einstellungen schreibt; jede Installationsmethode endet darin | [Ein-Klick-Installer](bereitstellung/ein-klick-installer-fuer-exponential.md) |
| `exp:install` | die Installation auf der Konsole: ein Befehl, Optionen mit Standardwerten, Exit-Status für Skripte | [exp:install für Hoster](bereitstellung/exp-install-fuer-hoster.md) |
| `exp:velocity deploy` | führt jeden Schritt, den eine Änderung am PHP-Code braucht, in der richtigen Reihenfolge aus, mit PASS oder FAIL pro Schritt | [Änderungen ausrollen](betrieb/aenderungen-ausrollen.md) |
| ezupdate | die Erweiterung, die nach Updates sucht und sie einspielt | [Automatische Updates mit ezupdate](betrieb/automatische-updates-mit-ezupdate.md) |
| FrankenPHP | Caddy mit eingebautem PHP; eine produktionsreife alternative Engine, betrieben über Velocity | [FrankenPHP](server/frankenphp.md) |
| Kickstarter | installiert aus einer geprüften `kickstart.ini`; `exp:kickstarter run --force` | [Der Kickstarter für Hoster](bereitstellung/der-kickstarter-fuer-hoster.md) |
| Log-Viewer | **Setup > Logdateien** und `./console exp:logs`: listet, verfolgt, durchsucht und rotiert die Logs, mit maskierten personenbezogenen Daten | [Log-Viewer und Log-Rotation](betrieb/log-viewer-und-log-rotation.md) |
| Wartungsmodus | eine 503-Seite für Besucher, während gearbeitet wird; `php bin/php/maintenance.php` mit `on`, `off` oder `status`, Markierung `var/maintenance.json` | [Wartungsmodus](betrieb/wartungsmodus.md) |
| `ManagedByDeployment` | Schalter pro Siteaccess, der verhindert, dass die Administration Einstellungen, Templates und Pakete schreibt | [Härtung für Hoster](sicherheit/haertung-fuer-hoster.md) |
| qbix | der Name der eigenen Engine von Velocity in Einstellungen und Befehlen (`[ServerSettings] Engine=qbix`) | [Velocity für Hoster](server/velocity-fuer-hoster.md) |
| Request-Klassifizierer | der Teil des Kernels, der Sondierungen und Angriffe in `var/log/security.log` schreibt; er ordnet ein, er blockiert nicht | [Sicherheitslog](betrieb/sicherheitslog.md) |
| request-shield | eine separate Web Application Firewall für PHP, die entscheidet, bevor der Kernel startet | [request-shield und Request-Regeln](sicherheit/request-shield-und-request-regeln.md) |
| Antwort-Cache (Response cache) | der Cache von Velocity für ganze gerenderte Seiten, beantwortet ohne PHP; `exp:velocity cache clear` | [Antwort-Cache von Velocity](leistung/antwort-cache-von-velocity.md) |
| Runbook | eine Schritt-für-Schritt-Seite für einen Vorfall | [Runbooks](runbooks/README.md) |
| Setup-Assistent | der Installer im Browser, eine Seite pro Schritt | [Der Setup-Assistent auf Shared Hosting](bereitstellung/der-setup-assistent-auf-shared-hosting.md) |
| Seitenpaket | das Paket vom Typ `site`, das eine Installation auf eine neue Website bringt, zum Beispiel `sevenx_multisite` | [Seitenpakete](bereitstellung/seitenpakete-fuer-hoster.md) |
| Siteaccess | ein Zugang zur Installation: ein Name, seine Einstellungen und eine Zuordnungsregel (URL, Host oder Port) | [Mehrere Websites in einer Installation](bereitstellung/mehrere-websites-in-einer-installation.md) |
| `settings/override` | die eigenen Einstellungen der Installation, einschließlich des Datenbankpassworts; sichern Sie sie, halten Sie sie vertraulich | [Was gesichert werden muss](betrieb/backups-was-gesichert-werden-muss.md) |
| `var/storage` | hochgeladene Dateien und Bildvarianten: Kundendaten | [Aufbau des Speichers](betrieb/aufbau-des-speichers.md) |
| `VarDir` | das Verzeichnis, in das eine Website Caches, Logs und Speicher schreibt (`var/` oder `var/<site>`) | [Aufbau des Speichers](betrieb/aufbau-des-speichers.md) |
| Velocity | Exponential Velocity, der in PHP geschriebene Webserver mit dauerhaften Workern; empfohlen für jede Phase | [Velocity für Hoster](server/velocity-fuer-hoster.md) |
| View-Cache | der Cache gerenderter Inhaltsansichten, `[ContentSettings] ViewCaching=enabled` | [Cache-Schichten](leistung/cache-schichten.md) |
| Watchdog | ein optionaler systemd-Timer, der Velocity nach einem Absturz neu startet; `exp:velocity watchdog install` | [Velocity startet nicht](runbooks/velocity-startet-nicht.md) |
| Worker | ein Velocity-Prozess, der die Anwendung im Speicher hält und Anfragen bedient | [Velocity-Worker und Pools](server/velocity-worker-und-pools.md) |

**Demomodus** - Der Zustand einer öffentlichen Demo, eingeschaltet durch die Umgebungsvariable `EXP_DEMO_MODE=1` für die Prozesse der Website: Administratorpasswörter können nicht geändert oder zurückgesetzt werden, die Installer akzeptieren das veröffentlichte Passwort, die Administration zeigt ein Banner. Nie für die echte Website eines Kunden. [Demomodus für Hoster](sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](hosting-anbieter-hier-beginnen.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
