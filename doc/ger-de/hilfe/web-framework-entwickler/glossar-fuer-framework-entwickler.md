---
title: Glossar für Web-Framework-Entwickler
audience: web-framework-developer, developer
category: web-frameworks
order: 530
summary: Die Exponential-Begriffe, denen ein Framework-Entwickler begegnet, alphabetisch nach dem englischen Begriff geordnet, jeweils mit einer Definition in einer Zeile, dem nächstliegenden Framework-Begriff und der Seite, die ihn erklärt - von Aktion und Attribut bis Workflow und wash.
keywords: Glossar, glossary, Begriffe, terms, Definitionen, definitions, Vokabular, Siteaccess, Modul, module, View, Knoten, node, Objekt, object, Klasse, class, Datentyp, datatype, Richtlinie, policy, Einschränkung, limitation, Erweiterung, extension, Override, Pagelayout, fetch, Operator, cache-block, Web-Framework-Entwickler, web framework developer
type: help
---
# Glossar für Web-Framework-Entwickler

Diese Seite richtet sich an Entwickler, die auf einen Exponential-Begriff stoßen und seine Bedeutung sowie seine
nächstliegende Entsprechung im Framework suchen. Die umgekehrte Richtung, von Framework-Begriffen zu Exponential, finden
Sie im [Begriffswörterbuch](begriffswoerterbuch.md). Die Tabelle ist nach dem englischen Begriff alphabetisch geordnet,
der jeweils in Klammern steht.

| Begriff | Bedeutung | Nächstliegender Framework-Begriff | Lesen |
|---|---|---|---|
| Aktion (Action) | eine POST-Schaltfläche, die in `module.php` einem Namen zugeordnet ist | Handler für das Absenden eines Formulars | [Formulare](formulare-und-csrf-schutz.md) |
| Attribut einer Klasse (Attribute) | eine Felddefinition einer Inhaltsklasse | Feld, Spalte | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Attribut eines Objekts (Attribute) | der Wert eines Feldes in einer Version und einer Sprache | Eigenschaftswert | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Cache-Block (Cache block) | `{cache-block}`: ein gecachtes Template-Fragment | Fragment-Cache | [Caching](cache-ebenen.md) |
| Inhaltsklasse (Content class) | ein Inhaltstyp mit Attributen | Model, Entity, Content Type | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Inhaltsobjekt (Content object) | ein Inhalt mit seinen Versionen und Übersetzungen | Datensatz | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Cronjob-Teil (Cronjob part) | eine benannte Gruppe geplanter Skripte | geplante Aufgabe | [Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md) |
| Datentyp (Datatype) | der Typ eines Attributs: Speicherung, Bearbeitung, Validierung, Anzeige | Feldtyp | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md#datentypen) |
| Design | ein Verzeichnis mit Templates und Assets in der Design-Kette | Theme | [Template-Auflösung](template-aufloesung-designs-und-overrides.md) |
| `.dba`-Datei | eine datenbankneutrale Schema- oder Datendatei | Schema-Dump, Migration | [Migrationen](migrationen-und-dba-schemadateien.md) |
| Ereignis (Event, `ezpEvent`) | ein prozessinternes Notify- oder Filter-Ereignis | Event, Signal, Hook | [Ereignisse und Listener](ereignisse-und-listener.md) |
| Ereignistyp eines Workflows (Event type) | eine Klasse, die einen Schritt eines Workflows ausführt | Listener, Prozessschritt | [Workflows](workflows-trigger-und-domain-events.md) |
| `EXP_ENV` | der Umgebungsname einer Maschine | `APP_ENV` | [Umgebungen](umgebungen-und-geheimnisse.md) |
| Erweiterung (Extension) | die Code-Einheit: Module, Klassen, Templates, Einstellungen | Bundle, Paket, App | [Pakete](pakete-erweiterungen-und-composer.md) |
| Fetch-Funktion (Fetch function) | eine Datenquelle, die Templates mit `fetch()` aufrufen | View Composer, Template-Tag | [Fetch-Funktionen](fetch-funktionen-und-template-daten.md) |
| Funktion einer Richtlinie (Function) | eine Berechtigung, die ein Modul deklariert, etwa `content/read` | Permission, Ability | [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| Handler | eine in einer INI-Einstellung benannte Klasse, die ein Subsystem implementiert | konfigurierter Dienst | [Dependency Injection](dependency-injection-und-dienste.md) |
| INI-Datei (INI file) | eine Einstellungsdatei in Ebenen | Konfigurationsdatei | [Konfiguration](konfiguration-ini-dateien.md) |
| Einschränkung (Limitation) | eine Einschränkung einer Richtlinie: Klasse, Sektion, Teilbaum, Sprache | Voter-Bedingung, Scope | [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| Modul (Module) | der erste Teil einer Adresse und sein Verzeichnis mit Views | Routengruppe, Controller | [Routing](routing-mit-modulen-und-views.md) |
| Knoten (Node) | ein Ort eines Objekts im Inhaltsbaum | Baumknoten, Seite | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Objektzustand (Object state) | ein Status pro Objekt, nutzbar in Richtlinien und Regeln | Zustand einer State Machine | [Workflows](workflows-trigger-und-domain-events.md) |
| Operation | eine Kernel-Aktion mit Triggern, etwa `content_publish` | Domänenoperation | [Workflows](workflows-trigger-und-domain-events.md) |
| Operator | eine in PHP geschriebene Template-Funktion oder ein Filter | Twig-Filter, Helper | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md) |
| Override | eine Regel in `override.ini`, die ein Template abhängig von Bedingungen austauscht | Template-Override | [Template-Auflösung](template-aufloesung-designs-und-overrides.md) |
| Paket (Package) | ein `.ezpkg` mit Klassen, Inhalten oder einer Erweiterung | Fixture-Bundle | [Migrationen](migrationen-und-dba-schemadateien.md#startdaten-und-inhalte) |
| Pagelayout | das Grundlayout um die Ausgabe jeder View | Basis-Layout | [Responses](responses-weiterleitungen-und-fehler.md#das-pagelayout) |
| Persistentes Objekt (Persistent object) | eine Unterklasse von `eZPersistentObject`, die einer Tabelle zugeordnet ist | Active Record | [ORM](orm-und-ezpersistentobject.md) |
| Richtlinie (Policy) | eine in einer Rolle gewährte, eventuell eingeschränkte Funktion | Berechtigungsregel | [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| Remote-ID (Remote ID) | ein stabiler externer Schlüssel eines Objekts oder Knotens | UUID, natürlicher Schlüssel | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Request-Regel (Request rule) | eine konfigurierte Regel, die einen Request zulässt, ablehnt, weiterleitet oder umschreibt | Middleware | [Middleware](middleware-und-request-regeln.md) |
| Rolle (Role) | eine Menge von Richtlinien, die Benutzern oder Gruppen zugewiesen ist | Rolle | [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| Sektion (Section) | eine Gruppe von Inhalten für Berechtigungen und Design | Bereich | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md#bereiche-zustände-und-beziehungen) |
| Siteaccess | eine benannte Konfiguration, die pro Request gewählt wird | Mandant, Site, Host-Konfiguration | [Siteaccesses](siteaccesses-hosts-und-mandanten.md) |
| Trigger | die Verbindung zwischen einer Operation und einem Workflow, davor oder danach | Event-Abonnement | [Workflows](workflows-trigger-und-domain-events.md) |
| URL-Alias (URL alias) | die lesbare Adresse eines Knotens | Slug, Permalink | [Sprechende URLs](url-aliase-und-platzhalter.md) |
| URL-Platzhalter (URL wildcard) | ein Muster, das Adressen weiterleitet oder intern weiterreicht | Weiterleitungsroute | [Sprechende URLs](url-aliase-und-platzhalter.md#url-platzhalter) |
| Velocity | der Web- und Anwendungsserver von Exponential mit persistenten Workern | Anwendungsserver | [Server und Deployment](server-und-deployment.md) |
| Version | eine Revision eines Inhaltsobjekts | Revision | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md#versionen-und-übersetzungen) |
| View | der Code hinter einer Adresse eines Moduls | Controller-Action | [Controller](controller-als-modul-views.md) |
| Wash | der Escaping-Operator, `\|wash` | Auto-Escaping, `e()` | [Validierung](validierung.md#ausgabe-dort-escapen-wo-sie-ausgeben) |
| Workflow | eine Liste von Ereignistypen, die bei einer Operation ausgeführt werden | Prozess, Pipeline | [Workflows](workflows-trigger-und-domain-events.md) |

## Verwandte Seiten

- [Begriffswörterbuch](begriffswoerterbuch.md)
- [Glossar für Journalisten](../presse/glossar-fuer-journalisten.md)
- [PHP-Entwickler: hier beginnen](../php-entwickler/php-entwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
