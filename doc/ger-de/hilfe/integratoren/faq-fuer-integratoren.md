---
title: FAQ für Integratoren
audience: integrator, developer, project-manager
category: integration
order: 520
summary: Kurze Antworten auf die Fragen, die Integratoren am häufigsten zu Exponential stellen - APIs, Authentifizierung, Webhooks, Importe, Identität, Suche, Leistung, Upgrades, Lizenzierung und das Partnerprogramm - jeweils mit einem Verweis auf die Seite, die es erklärt.
keywords: FAQ, Fragen, Antworten, Integrator, API, REST, GraphQL, Webhook, OAuth, API-Schlüssel, SSO, SAML, LDAP, Import, remote_id, Solr, Velocity, Upgrade, Lizenz, Partner, questions, answers, API key, license
type: help
---
# FAQ für Integratoren

Kurze Antworten mit Verweisen. Jede Antwort ist am Code und an den Einstellungen von Exponential 6.0.15 und der
Erweiterungen geprüft, die die `composer.json` im Wurzelverzeichnis voraussetzt.

## Inhalt

1. [APIs](#apis)
2. [Authentifizierung und Identität](#authentifizierung-und-identität)
3. [Importe und Exporte](#importe-und-exporte)
4. [Ereignisse und Hooks](#ereignisse-und-hooks)
5. [Betrieb](#betrieb)
6. [Geschäftliches](#geschäftliches)

## APIs

**Gibt es eine REST-API?** Ja: Die Erweiterung `ezprestapi` liefert Lesezugriffe auf Inhalte unter `/api/ezp/v1/...`
und `/api/ezp/v2/...` sowie Anlegen und Löschen in v2 ([REST-API für Integratoren](rest-api-fuer-integratoren.md)).

**Kann ich Inhalte über REST aktualisieren?** Nicht über `ezprestapi`: Die Route zum Aktualisieren antwortet mit
`501`. Verwenden Sie `expobject::update` von `expservices` oder PHP auf dem Server ([Remote-Dienste mit expservices](entfernte-dienste-mit-expservices.md)).

**Gibt es GraphQL oder eine OpenAPI-Beschreibung?** Nein. Velocity veröffentlicht `/.well-known/openapi.json` für seine
eigenen Handler, nicht für die Inhalts-API ([Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md#was-nicht-angeboten-wird)).

**Was ist `expservices`?** Etwa tausend benannte JSON-Dienste über `/ezjscore/call/exp<domain>::<method>`, die
Funktionen der Administration als API, jede hinter ihrer Richtlinie.

**Gibt es eine Suche über eine API?** `expsearch::search`, `count`, `facets` und mehr in `expservices`; nicht in der
REST-API.

**Gibt es SOAP noch?** Ja, SOAP 1.1 ohne WSDL, standardmäßig aus (`soap.ini EnableSOAP=false`)
([WebDAV und SOAP](webdav-und-soap.md)).

## Authentifizierung und Identität

**Wie authentifiziert sich ein Server?** Mit einem persönlichen API-Schlüssel eines Dienstkontos
(`Authorization: Bearer expk_...`) für REST oder einem Token von `expservices` ([Zugangsdaten wählen](zugangsdaten-fuer-eine-integration-waehlen.md)).

**Gibt es einen Client-Credentials-Grant?** Nein; der OAuth-Server unterstützt `authorization_code` und
`refresh_token`. Verwenden Sie für Maschine-zu-Maschine einen API-Schlüssel ([OAuth-Anwendungen](oauth-anwendungen-fuer-integrationen.md)).

**Können sich Mitarbeitende mit Active Directory anmelden?** Über den LDAP-Login-Handler, mit `LDAPVersion=3` und einer
Verbindung über `ldaps://` ([Login-Handler](login-handler-ldap-und-textdatei.md)).

**Gibt es SAML?** Nein. Verwenden Sie einen vorgeschalteten identitätsbewussten Proxy und einen eigenen SSO-Handler oder
individuelle Arbeit ([Single Sign-on](single-sign-on-und-social-login.md)).

**Social Login?** Ja, über `sevenx_authentication_2fa` (Google, Facebook, Meta, Instagram, Twitter, ID.me).

## Importe und Exporte

**Wie vermeide ich Duplikate?** Geben Sie jedem importierten Objekt eine `remote_id` nach einem Schlüsselschema, und
suchen Sie es vor dem Anlegen mit `eZContentObject::fetchByRemoteID()` ([Idempotente Importe](idempotente-importe-ueber-die-remote-id.md)).

**Kann ich direkt in die Datenbanktabellen schreiben?** Nein. Verwenden Sie `eZContentFunctions`; direkte
Schreibzugriffe umgehen Versionen, Workflows, Caches, den Suchindex und das Audit.

**Verschickt ein Import Tausende Benachrichtigungs-E-Mails?** Nicht, wenn Sie den Inhaltsfunktionen `$notify = false`
übergeben ([E-Mail und Benachrichtigungen](integration-von-e-mail-und-benachrichtigungen.md#importe-und-benachrichtigungen)).

**Gibt es einen Dateiaustausch ohne Code?** `xrowextract`: CSV, JSON, XML und Pakete, zeitgesteuert, geliefert an SFTP,
S3, WebDAV oder HTTP ([Massendatenaustausch](massendatenaustausch-mit-xrowextract.md)).

**Wie verschiebe ich einen Teilbaum zwischen Installationen?** Ein Paket mit `ezpm.php` oder fortlaufend mit
`syndication`.

## Ereignisse und Hooks

**Gibt es einen Webhook, wenn Inhalte veröffentlicht werden?** Keinen allgemeinen. Verwenden Sie ein Workflow-Ereignis
auf `content / publish / after` mit einer Outbox, oder leiten Sie `content.object.publish` an die signierte
Webhook-Senke des Audit-Trails ([Workflows und Trigger](workflows-und-trigger-als-hooks.md), [Audit-Webhooks](ausgehende-webhooks-aus-dem-audit-trail.md)).

**Kann ein anderes System eine Veröffentlichung freigeben?** Ein Workflow-Ereignis auf `content / publish / before` kann
annehmen, ablehnen oder an den Cronjob weiterreichen.

**Wie leere ich ein CDN?** Lauschen Sie auf `content/cache` und `content/cache/all` ([Event-Listener](event-listener-fuer-integrationen.md)).

**Gibt es einen Endpunkt für eingehende Webhooks?** Nein; bauen Sie einen als Modulansicht, Serverfunktion oder Domäne
von `expservices` in Ihrer Erweiterung, die die Nutzdaten zuerst speichert (Inbox).

## Betrieb

**Muss ich für Velocity etwas Besonderes tun?** Starten Sie es nach Klassenänderungen neu, halten Sie nie
Anfragezustand in statischen Variablen, und wissen Sie, dass anonyme `GET`-Antworten aus seinem Antwort-Cache kommen
können, wenn dieser eingeschaltet ist ([Velocity und Leistung](velocity-und-leistung-fuer-integrationen.md)).

**Warum zeigt die Website nach meinem Import alte Inhalte?** Über die Inhaltsfunktionen veröffentlichte Inhalte leeren
ihre eigenen Caches; Template-Blöcke, der Antwort-Cache von Velocity und äußere Caches müssen eventuell geleert werden
([Caching nach Importen](caching-nach-importen.md)).

**Woran erkenne ich, dass die Integration noch funktioniert?** Zusammenfassungszeilen, Alarm bei ihrem Fehlen,
Prüfungen von Rückstand und Aktualität ([Überwachung](integrationen-ueberwachen.md)).

**Macht ein Upgrade meine Integration kaputt?** Lesen Sie die Hinweise zu Verhaltensänderungen, proben Sie auf einer
Kopie, führen Sie die Abnahmefälle aus ([Upgrades](upgrades-ohne-defekte-integrationen.md)).

## Geschäftliches

**Unter welcher Lizenz kann ich meinen Connector verbreiten?** Wählen Sie für Code, der in Exponential läuft und
verbreitet wird, GPL v2 oder später; keine Rechtsberatung ([Integrationen zurückgeben](integrationen-zurueckgeben.md#punkte-zur-lizenzierung)).

**Passt das Partnerprogramm zu Integrationsunternehmen?** Ja, meist als IT-Partner; Stufen von Student bis Platinum,
10 % oder 20 % Provision für Leads ([Integratoren und das Partnerprogramm](integratoren-und-das-partnerprogramm.md)).

**Wer beantwortet Fragen zum Kernel?** Die Foren und Telegram für die Gemeinschaft; 7x für bezahlten Support
([Integratoren: hier beginnen](integratoren-hier-beginnen.md#wen-sie-fragen)).

## Verwandte Seiten

- [Integratoren: hier beginnen](integratoren-hier-beginnen.md)
- [Glossar für Integratoren](glossar-fuer-integratoren.md)
- [Partner-FAQ](../partner/faq-fuer-partner.md)
- [FAQ für App-Ersteller](../ersteller/anwendungsentwickler/faq-fuer-anwendungsentwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
