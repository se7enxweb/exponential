---
title: Glossar für Integratoren
audience: integrator, developer, project-manager
category: integration
order: 530
summary: Die Begriffe, denen Integratoren beim Verbinden von Exponential mit anderen Systemen begegnen - die eigenen Konzepte von Exponential (Klasse, Objekt, Knoten, Remote-ID, Siteaccess, Operation, Trigger, Cronjob-Teil) und allgemeine Integrationsbegriffe (Outbox, Idempotenz, Wasserzeichen, Dead Letter) - jeweils mit einem Verweis.
keywords: Glossar, Begriffe, Definitionen, Remote-ID, Knoten, Objekt, Klasse, Siteaccess, Operation, Trigger, Workflow, Cronjob-Teil, Richtlinie, Rolle, API-Schlüssel, expservices, ezjscore, Outbox, Idempotenz, Wasserzeichen, Dead Letter, Dienstkonto, glossary, terms, remote id, node, object, class, cronjob part, policy, role, API key, idempotency, watermark, dead letter, service account
type: help
---
# Glossar für Integratoren

Begriffe in der alphabetischen Reihenfolge der englischen Fachbegriffe, die als Stichwort stehen bleiben, wo sie im
Code und in Einstellungen so vorkommen. Die eigenen Begriffe von Exponential sind so erklärt, wie ein Integrator sie
braucht; das vollständige Produktglossar ist das [Glossar](../../glossar.md), und Entwickler haben
[das Glossar für Erweiterungsentwickler](../erweiterungsentwicklung/teil-8-referenz/glossar-fuer-erweiterungsentwickler.md).

## A bis D

| Begriff | Bedeutung |
|---|---|
| **Access extension (Zugangserweiterung)** | Eine Erweiterung, die nur für einige Siteaccesses aktiviert ist (`ActiveAccessExtensions`); Skripte müssen einen solchen Siteaccess mit `-s` angeben |
| **API key (persönlicher API-Schlüssel)** | Zugangsdaten `expk_...`, die ein Benutzer für ein Programm erzeugt; mit Bereichen, widerrufbar, gesendet als `Authorization: Bearer` ([Seite](persoenliche-api-schluessel-fuer-integrationen.md)) |
| **Audit trail (Audit-Trail)** | Die Aufzeichnung dessen, was in der Installation geschehen ist, mit Senken für Syslog, E-Mail und einen signierten Webhook ([Seite](ausgehende-webhooks-aus-dem-audit-trail.md)) |
| **Class (Inhaltsklasse)** | Die Definition einer Art von Inhalt: ihre Attribute und deren Datentypen ([Abbildung](inhaltsklassen-auf-externe-daten-abbilden.md)) |
| **Collection (Einsendung)** | Ein einzelnes Absenden eines Formulars (Informationssammlung) ([Seite](formulare-und-gesammelte-informationen.md)) |
| **Content job (Inhaltsjob)** | Eine große Operation (einen Teilbaum entfernen, kopieren, verschieben, verstecken), im Hintergrund in Stapeln ausgeführt |
| **Cronjob part (Cronjob-Teil)** | Eine benannte Gruppe von Skripten, ausgeführt von `runcronjobs.php <part>` ([Seite](cronjobs-fuer-integrationen.md)) |
| **Datatype (Datentyp)** | Der Typ eines Attributs (`ezstring`, `ezimage` ...); seine Methode `fromString()` legt das Importformat fest |
| **Dead letter** | Wohin ein Element nach seinem letzten fehlgeschlagenen Versuch geht, damit ein Mensch es sich ansieht |
| **Delta run (Deltalauf)** | Ein Lauf, der nur überträgt, was sich seit dem letzten Wasserzeichen geändert hat |
| **Dry run (Probelauf)** | Ein Lauf, der meldet, was er ändern würde, und nichts ändert |

## E bis N

| Begriff | Bedeutung |
|---|---|
| **Event listener (Event-Listener)** | Eine Funktion, die an ein Kernel-Ereignis von `ezpEvent` gehängt ist (`site.ini [Event] Listeners[]`) ([Seite](event-listener-fuer-integrationen.md)) |
| **`expservices`** | Die Erweiterung, die die Funktionen der Administration als JSON-Dienste unter `/ezjscore/call/` anbietet ([Seite](entfernte-dienste-mit-expservices.md)) |
| **Extension (Erweiterung)** | Die Einheit, als die eine Integration gebaut und ausgeliefert wird ([Seite](eine-integration-als-erweiterung-bauen.md)) |
| **ezjscore server function (Serverfunktion von ezjscore)** | Eine statische PHP-Methode, aufrufbar unter `/ezjscore/call/<server>::<function>` ([Seite](ezjscore-serverfunktionen-fuer-integrationen.md)) |
| **Idempotency (Idempotenz)** | Etwas zweimal auszuführen hat dieselbe Wirkung wie einmal; die Grundlage sicherer Wiederholungen ([Seite](idempotente-importe-ueber-die-remote-id.md)) |
| **Inbox** | Eine Tabelle, in der eingehende Nachrichten vor der Verarbeitung gespeichert werden, damit Duplikate erkannt werden |
| **Location (Platzierung)** | Ein Platz eines Objekts im Inhaltsbaum; dasselbe wie ein Knoten |
| **Login handler (Login-Handler)** | Eine Klasse, die einen Login und ein Passwort prüft (`standard`, `LDAP`, `textfile`, Ihre eigene) ([Seite](login-handler-ldap-und-textdatei.md)) |
| **Node (Knoten)** | Eine Platzierung eines Objekts im Baum, mit eigener ID und eigenem URL-Alias |

## O bis R

| Begriff | Bedeutung |
|---|---|
| **Object (Inhaltsobjekt)** | Ein Inhalt einer Klasse, mit Versionen und Übersetzungen; kann mehrere Knoten haben |
| **Operation** | Eine benannte Kernel-Aktion (Veröffentlichen, Verschieben, Kasse), ausgeführt über `eZOperationHandler`, mit Triggern davor und danach |
| **Outbox** | Eine Tabelle, in der Änderungen innerhalb von Exponential aufgezeichnet und später von einem getrennten Prozess gesendet werden ([Muster](integrationsmuster-push-pull-batch-echtzeit.md#outbox-und-inbox)) |
| **Package (Paket)** | Ein Archiv `.ezpkg` mit Klassen, Inhalten und Dateien ([Seite](pakete-importieren-und-exportieren.md)) |
| **Policy (Richtlinie)** | Ein Recht einer Rolle: Modul, Funktion und Einschränkungen (Subtree, Class, Section, Language ...) |
| **Publish operation (Veröffentlichungsoperation)** | Die Operation, die eine Version aktuell macht; führt Workflows aus, leert Caches, indiziert |
| **Remote id (Remote-ID)** | Ein Zeichenkettenschlüssel eines Objekts (bis zu 100 Zeichen, eindeutig), den eine Integration aus der ID der Quelle setzt |
| **Role (Rolle)** | Ein Satz von Richtlinien, der Benutzern oder Gruppen zugewiesen ist ([Seite](dienstkonten-rollen-und-richtlinien.md)) |

## S bis Z

| Begriff | Bedeutung |
|---|---|
| **Scope (Bereich)** | Wofür ein persönlicher API-Schlüssel verwendet werden darf: `read`, `publish`, `edit`, `remove` |
| **Service account (Dienstkonto)** | Ein Benutzer, der für eine Integration angelegt ist, mit einer engen Rolle |
| **Siteaccess** | Eine Konfiguration, über die die Installation erreicht wird (eine Website, eine Sprache, die Administration); Skripte wählen einen mit `-s` |
| **SSO handler (SSO-Handler)** | Eine in `SingleSignOnHandlerArray[]` genannte Klasse, die einen Benutzer ohne Anmeldeformular erkennt ([Seite](single-sign-on-und-social-login.md)) |
| **Trigger** | Die Verbindung zwischen einer Operation (davor oder danach) und einem Workflow ([Seite](workflows-und-trigger-als-hooks.md)) |
| **Velocity** | Der Webserver von Exponential mit dauerhaft laufenden Workern ([Seite](velocity-und-leistung-fuer-integrationen.md)) |
| **Watermark (Wasserzeichen)** | Der Punkt, bis zu dem Daten synchronisiert wurden (eine Zeit, eine ID) |
| **Workflow** | Eine geordnete Liste von Ereignissen, die bei einem Trigger abläuft |
| **Workflow event type (Workflow-Ereignistyp)** | Eine Klasse, die einen Schritt eines Workflows liefert; Ihr Integrations-Hook |

## Verwandte Seiten

- [Glossar](../../glossar.md)
- [Partner-Glossar](../partner/glossar-fuer-partner.md)
- [FAQ für Integratoren](faq-fuer-integratoren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
