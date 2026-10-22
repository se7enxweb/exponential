---
title: Glossar für Softwareingenieure
audience: software-engineer, developer
category: software-engineering
order: 990
summary: Die Begriffe von Exponential, denen ein Softwareingenieur begegnet, von Audit-Trail und Baseline bis Zygote, jeweils in ein oder zwei Sätzen erklärt, mit der Seite, die sie erläutert, und dem nächstliegenden Begriff anderer Frameworks, wo es einen gibt.
keywords: Glossar, Begriffe, Definitionen, Siteaccess, Modul, View, Datentyp, Knoten, Erweiterung, eZINI, Persistenzobjekt, Velocity, Zygote, Warm-up, Cache-Block, Baseline, Manifest, CLA, Softwareingenieur, glossary, terms, definitions, module, view, datatype, node, extension, persistent object, cache block, manifest
type: help
---
# Glossar für Softwareingenieure

Begriffe, die auf den Seiten für Softwareingenieure verwendet werden, mit dem nächstliegenden Begriff aus anderen
Frameworks, wo das hilft. Das Glossar für Erweiterungsentwickler enthält weitere API-Begriffe
([Glossar für Erweiterungsentwickler](../erweiterungsentwicklung/teil-8-referenz/glossar-fuer-erweiterungsentwickler.md)).

| Begriff | Bedeutung | Anderswo oft genannt | Seite |
|---|---|---|---|
| Audit-Trail (audit trail) | hashverkettetes, signiertes Log sicherheitsrelevanter Ereignisse | Audit-Log | [Beobachtbarkeit](beobachtbarkeit-logs-debug-und-audit.md) |
| Baseline (PHPStan) | `phpstan-baseline.neon`, die geduldeten Befunde, weil sie älter als die Prüfung sind | Baseline | [Statische Analyse und Codestil](statische-analyse-und-codestil.md) |
| Cache-Block (cache block) | ein gecachter Abschnitt eines Templates, `{cache-block}` | Fragment-Cache | [Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md) |
| Cluster-Datei-Handler (cluster file handler) | `eZClusterFileHandler`; jede Cache- und Speicherdatei läuft darüber | Speicherabstraktion | [Skalierung und Clustering](skalierung-und-clustering.md) |
| Inhaltsklasse (content class) | eine Art von Inhalt mit typisierten Attributen, zur Laufzeit definiert | Content Type, Schema | [Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md) |
| Inhaltsjob (content job) | eine große Inhaltsoperation, im Hintergrund in Stapeln ausgeführt | Hintergrundjob | [Konsole, Cronjobs und Hintergrundarbeit](konsole-cron-und-hintergrundarbeit.md) |
| Cache der Inhaltsansichten (content view cache) | das gecachte Ergebnis einer Inhaltsansicht | Seiten- oder View-Cache | [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md) |
| Cronjob-Teil (cronjob part) | eine benannte Gruppe von Skripten, die `runcronjobs.php` ausführt | geplante Aufgabe | [Konsole, Cronjobs und Hintergrundarbeit](konsole-cron-und-hintergrundarbeit.md) |
| Datentyp (datatype) | Speicherung, Prüfung, Bearbeitung und Indizierung einer Art von Attribut | Feldtyp | [Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md) |
| Design-Kette (design chain) | die gestapelten Design-Verzeichnisse, in denen ein Template gesucht wird | Theme-Vererbung | [Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md) |
| Engine | der Server, den `exp:velocity` steuert: `qbix`, `frankenphp` oder `php` | Laufzeitumgebung | [Anwendungsserver](anwendungsserver.md) |
| Erweiterung (extension) | ein Verzeichnis in `extension/`, das Einstellungen, Module, Designs und Klassen hinzufügt | Plugin, Bundle, Modul | [Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md) |
| Fetch-Funktion (fetch function) | `fetch( 'module', 'function', hash( ... ) )`, der Datenzugriff von Templates | View-Helper, Abfrage | [Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md) |
| Dateimanifest (file manifest) | `share/filelist.md5`, die Prüfsumme jeder mitgelieferten Datei | Prüfsummenliste | [CI-Gates](ci-gates.md) |
| INI-Kaskade (INI cascade) | die zusammengeführten Einstellungen: Standards, Erweiterungen, Siteaccess, Override | Konfigurationsschichten | [Siteaccesses und Konfiguration](siteaccesses-und-konfiguration.md) |
| Kernel | `kernel/` und `lib/`, der Kern von Exponential | Framework-Kern | [Architekturüberblick](architekturueberblick.md) |
| Einschränkung (limitation) | eine Beschränkung einer Richtlinie: Klasse, Sektion, Teilbaum, Sprache | Geltungsbereich, Bedingung | [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md) |
| Modul (module) | ein URL-Namensraum mit Views und Richtlinienfunktionen | Controller-Gruppe | [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md) |
| Knoten (node) | eine Platzierung eines Inhaltsobjekts im Baum | Location, Seite | [Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md) |
| Override (Template) | eine Regel in `override.ini`, die ein anderes Template wählt, wenn Bedingungen zutreffen | Template-Override | [Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md) |
| Pagelayout | das äußere Template, in das ein View-Ergebnis gesetzt wird | Layout | [Der Lebenszyklus einer Anfrage](lebenszyklus-einer-anfrage.md) |
| Persistenzobjekt (persistent object) | eine Unterklasse von `eZPersistentObject`, einer Tabelle zugeordnet | Active Record, Entität | [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md) |
| Richtlinie (policy) | das Recht, eine Modulfunktion zu nutzen, mit Einschränkungen | Berechtigung | [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md) |
| Anfrageregeln (request rules) | Regeln, die nach der URL-Übersetzung erlauben, verweigern, weiterleiten, umschreiben oder protokollieren | Middleware, Firewall-Regeln | [HTTP-Sicherheit](http-sicherheit-csp-und-proxys.md) |
| Antwort-Cache (response cache) | der Cache öffentlicher Seiten im Server Velocity | Reverse-Proxy-Cache | [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md) |
| Rolle (role) | eine Menge von Richtlinien, Benutzern oder Gruppen zugewiesen | Rolle | [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md) |
| Siteaccess | eine benannte Konfiguration, je Anfrage gewählt: eine Website, die Administration, eine Sprache | Mandant, Frontend, Umgebung | [Siteaccesses und Konfiguration](siteaccesses-und-konfiguration.md) |
| Upgrade-Hinweis (upgrade note) | eine Seite in `doc/eng-us/bc/` zu einer Änderung, die eine Handlung verlangt | Migrationsanleitung | [Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md) |
| URL-Alias (URL alias) | eine lesbare Adresse, die in eine Moduladresse übersetzt wird | Route, Slug | [Der Lebenszyklus einer Anfrage](lebenszyklus-einer-anfrage.md) |
| Velocity | der Anwendungsserver von Exponential mit persistenten Workern | Anwendungsserver | [Persistente Worker von Velocity](persistente-worker-von-velocity.md) |
| View | eine Seite oder ein Endpunkt eines Moduls | Action, Controller-Methode | [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md) |
| Warm-up | ein Skript, das einmal im Elternprozess von Velocity läuft, bevor Worker geforkt werden | Preload | [Persistente Worker von Velocity](persistente-worker-von-velocity.md) |
| Workflow-Ereignis (workflow event) | ein Schritt vor oder nach einer Operation, über einen Auslöser | Hook, Listener | [Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md) |
| Zygote | ein sauberer Prozess, aus dem Velocity spätere Worker forkt | Prefork-Vorlage | [Persistente Worker von Velocity](persistente-worker-von-velocity.md) |

## Verwandte Seiten

- [Häufige Fragen von Softwareingenieuren](faq-fuer-softwareingenieure.md)
- [Glossar für Hoster](../hosting/glossar-fuer-hoster.md)
- [Softwareingenieure: hier beginnen](softwareingenieure-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
