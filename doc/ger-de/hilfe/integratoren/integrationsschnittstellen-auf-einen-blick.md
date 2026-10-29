---
title: Integrationsschnittstellen auf einen Blick
audience: integrator, developer, admin
category: integration
order: 20
summary: Jede Integrationsschnittstelle, die Exponential 6.0.15 und seine vorausgesetzten Erweiterungen bieten, mit Richtung, Protokoll, Authentifizierung, Ort und ob sie standardmäßig aktiv ist, dazu was nicht angeboten wird und wie Sie prüfen, was eine Installation aktiv hat.
keywords: Integrationsschnittstellen, API-Überblick, REST, OAuth, API-Schlüssel, expservices, ezjscore, RSS, Atom, OPML, SOAP, WebDAV, Syndication, Pakete, CSV, xrowextract, Workflows, Trigger, ezpEvent, Audit-Webhook, Login-Handler, LDAP, SSO, Such-Plugin, E-Mail-Transport, Zahlungs-Gateway, nicht angeboten, GraphQL, SAML, integration surfaces, API overview, API keys, packages, payment gateway, not provided
type: help
---
# Integrationsschnittstellen auf einen Blick

Diese Seite ist das Inventar. Sie listet jeden Weg auf, auf dem ein anderes System Daten mit Exponential 6.0.15
austauschen kann, so wie der Kernel und die Erweiterungen, die die `composer.json` im Wurzelverzeichnis voraussetzt,
ihn ausliefern. Jede Zeile nennt, wo die Schnittstelle liegt und ob sie sofort funktioniert oder eingeschaltet werden
muss. Die Standards hinter jedem Protokoll, mit ihren bekannten Lücken, stehen in
[API- und Protokollstandards](../../standards/apis-und-protokolle.md).

## Inhalt

1. [HTTP-APIs](#http-apis)
2. [Feeds, Dateien und Protokolle](#feeds-dateien-und-protokolle)
3. [Serverseitige Schnittstellen](#serverseitige-schnittstellen)
4. [Identität, E-Mail, Suche und Zahlung](#identität-e-mail-suche-und-zahlung)
5. [Was nicht angeboten wird](#was-nicht-angeboten-wird)
6. [Prüfen, was eine Installation aktiv hat](#prüfen-was-eine-installation-aktiv-hat)

## HTTP-APIs

| Schnittstelle | Richtung | Protokoll und Format | Authentifizierung | Wo | Standard |
|---|---|---|---|---|---|
| REST-Inhalts-API | hinein und hinaus | HTTP, JSON; `/api/ezp/v1/...`, `/api/ezp/v2/...` | OAuth-2.0-Token, persönlicher API-Schlüssel oder HTTP Basic (`AuthenticationStyle`) | Kernel-Framework `kernel/private/rest/`, Routen in `ezprestapi` | antwortet, wenn `ezprestapi` aktiv ist; `rest.ini [Authentication] RequireAuthentication=enabled` |
| OAuth-2.0-Autorisierungsserver | hinein (Zugangsdaten) | `oauth/authorize`, `POST /api/auth/v1/oauth/token` | Client-ID und Geheimnis, Zustimmung des Benutzers | `kernel/private/modules/oauth/`, Setup > oAuth-Verwaltung | gewährt `authorization_code` und `refresh_token` |
| Persönliche API-Schlüssel | hinein (Zugangsdaten) | `Authorization: Bearer expk_...` | der Besitzer des Schlüssels, auf dessen Bereiche beschränkt | `kernel/apikey/`, `rest.ini [ApiKeySettings]` | `ApiKeys=enabled`; keine Rolle hat `apikey/create`, bis Sie es vergeben |
| Remote-Dienste (`expservices`) | hinein und hinaus | JSON über `/ezjscore/call/exp<domain>::<method>` | Sitzungs-Cookie und Formular-Token oder ein Token `expt_...` | `extension/expservices` | `expservices.ini [Services] Enabled=enabled` |
| Serverfunktionen von ezjscore | hinein und hinaus | JSON über `/ezjscore/call/<server>::<function>` | Sitzung, Richtlinie `ezjscore/call` | `extension/ezjscore` | an; Ihre Funktionen kommen über `ezjscore.ini` hinzu |

## Feeds, Dateien und Protokolle

| Schnittstelle | Richtung | Format | Wo | Standard |
|---|---|---|---|---|
| RSS-, Atom- und OPML-Exporte | hinaus | RSS 1.0, RSS 2.0, Atom, OPML 2.0, Apple Podcasts; `/rss/feed/<address>` | `kernel/classes/ezrssexport.php`, Setup > RSS | keine eingerichtet |
| RSS-Importe | hinein | RSS 0.91 bis 2.0 | `kernel/classes/ezrssimport.php`, Cronjob `rssimport.php` | im globalen Cronjob-Teil |
| `syndication` | beide, Installation zu Installation | XML-Feeds über SOAP | Erweiterung `syndication`, `/syndication/menu` | braucht `soap.ini EnableSOAP=true` |
| SOAP-Server | hinein | SOAP 1.1, keine WSDL | `index_soap.php` (vor 6.0.15 `soap.php`), `lib/ezsoap/` | `soap.ini [GeneralSettings] EnableSOAP=false` |
| WebDAV | beide | WebDAV Klasse 1 | `index_webdav.php` ([in 6.0.15 umbenannt](../../bc/6.0/webdav-einstiegsskript-index-webdav.md)), `kernel/private/classes/webdav/` | `webdav.ini [GeneralSettings] EnableWebDAV=false` |
| Pakete (`.ezpkg`) | beide | Paketarchive; JSON-Paketformat | `ezpm.php`, Setup > Pakete | an |
| CSV-Import und -Export | beide | CSV | `bin/php/ezcsvimport.php`, `bin/php/ezcsvexport.php` | an |
| `xrowextract` | beide, zeitgesteuert | CSV, JSON, XML, `.ezpkg`; SFTP, FTP, S3, WebDAV, HTTP POST | Erweiterung `xrowextract`, Administration **Export** | mit der Distribution installiert |
| Export gesammelter Informationen | hinaus | CSV | `infocollector/export/<object id>`; Erweiterung `bccie` | an |

## Serverseitige Schnittstellen

| Schnittstelle | Was sie einer Integration bietet | Wo |
|---|---|---|
| PHP-Inhalts-API | Inhalte auf dem unterstützten Weg anlegen, aktualisieren, veröffentlichen, verschieben, verstecken, entfernen | `kernel/classes/ezcontentfunctions.php`, `eZContentObject`, `eZContentObjectTreeNode` |
| CLI-Skripte und `ezexec.php` | PHP mit geladenem Kernel ausführen, aus Cron oder einem Deployment | `bin/php/ezexec.php`, `eZScript`, `bin/php/console` |
| Cronjob-Teile | zeitgesteuerte Importe und Exporte | `settings/cronjob.ini`, `runcronjobs.php` |
| Workflows und Trigger | Code vor oder nach einer Operation ausführen (Veröffentlichen, Kasse, ...) | `settings/workflow.ini`, Setup > Trigger |
| Event-Listener | auf Ereignisse des Kernels reagieren (Cache geleert, Zustand zugewiesen, Ausgabe) | `ezpEvent`, `site.ini [Event] Listeners[]` |
| Benachrichtigungs-Handler und Transporte | Benachrichtigungsereignisse auf eigene Weise zustellen | `settings/notification.ini`, `kernel/classes/notification/` |
| Senken des Audit-Trails | signierter Webhook, Syslog und E-Mail für aufgezeichnete Ereignisse | `settings/audit.ini [AuditSink_webhook]` |
| Inhaltsjobs und `expqueue` | große Operationen und Hintergrundarbeit in Stapeln | `content.ini [ContentJobSettings]`, Erweiterung `expqueue` |

## Identität, E-Mail, Suche und Zahlung

| Schnittstelle | Einstellung | Mitgelieferte Handler |
|---|---|---|
| Login-Handler | `site.ini [UserSettings] LoginHandler[]` | `standard`, `LDAP` (`settings/ldap.ini`), `textfile` (`settings/textfile.ini`); `sevenxUser2fa` und Social Login aus `sevenx_authentication_2fa` |
| Single-Sign-on-Handler | `site.ini [UserSettings] SingleSignOnHandlerArray[]` | keine mitgeliefert; eine eigene Klasse `eZ<name>SSOHandler` |
| E-Mail-Transport | `site.ini [MailSettings] Transport`, `TransportAlias[]` | `sendmail`, `smtp`, `file` |
| Suchmaschine | `site.ini [SearchSettings] SearchEngine` | `eZSearchEngine` (eingebaut); Solr über das getrennt installierte `ezfind` |
| Zahlungs-Gateways | `paymentgateways.ini [GatewaysSettings]` | Erweiterung `ezpaypal` |

## Was nicht angeboten wird

Planen Sie Folgendes als individuelle Arbeit ein, oder wählen Sie einen anderen Ansatz:

| Nicht angeboten | Was Sie stattdessen tun |
|---|---|
| GraphQL | die REST-API oder `expservices` verwenden oder ein eigenes Modul schreiben |
| Eine OpenAPI-Beschreibung der Inhalts-API | Ihre Routen in Ihrer Schnittstellenspezifikation dokumentieren ([Vorlage](vorlage-fuer-eine-schnittstellenspezifikation.md)) |
| XML-RPC | nicht umgesetzt; REST oder SOAP verwenden |
| SAML oder ein OpenID-Connect-Provider | einen Login- oder SSO-Handler schreiben oder einen identitätsbewussten Proxy vorschalten ([Single Sign-on](single-sign-on-und-social-login.md)) |
| Ein allgemeiner ausgehender Webhook für Inhaltsereignisse | ein eigenes Workflow-Ereignis oder ein eigener Event-Listener oder die Webhook-Senke des Audit-Trails mit eingeschaltetem `content.object.publish` ([Audit-Webhooks](ausgehende-webhooks-aus-dem-audit-trail.md)) |
| Inhalte über REST aktualisieren | `POST /content/node/<id>` prüft `content/edit` und antwortet mit 501; aus PHP oder über `expservices` aktualisieren |
| Ein Connector für Message Broker (AMQP, Kafka) | aus einem Workflow-Ereignis oder einem Cronjob über eine PHP-Client-Bibliothek veröffentlichen, die Sie Ihrer Erweiterung hinzufügen |
| Ein MCP-Server im Kernel | das Kernel-Repository liefert keinen mit ([KI, Job-Warteschlange und MCP](ki-jobwarteschlange-und-mcp.md)) |

## Prüfen, was eine Installation aktiv hat

Führen Sie dies im Wurzelverzeichnis der Installation aus (ergänzen Sie `--allow-root-user`, wenn Sie root sind):

```bash
php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions   # ezprestapi, expservices, xrowextract ...
php bin/php/console exp:ini get rest.ini/Authentication/RequireAuthentication
php bin/php/console exp:ini get expservices.ini/Services/Enabled
php bin/php/console exp:ini get soap.ini/GeneralSettings/EnableSOAP
php bin/php/console exp:ini get webdav.ini/GeneralSettings/EnableWebDAV
php bin/php/console exp:ini get site.ini/UserSettings/LoginHandler
php bin/php/console exp:ini get site.ini/MailSettings/Transport
php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
```

Von außen zeigen zwei Anfragen, ob die HTTP-APIs antworten:

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/api/ezp/v1/content/node/2   # 401 without credentials
```

Setup > RAD (`/setup/rad`) listet die Erweiterungspunkte und den Katalog von `expservices` der Installation auf.

## Verwandte Seiten

- [Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md)
- [API- und Protokollstandards](../../standards/apis-und-protokolle.md)
- [FAQ zu Integrationen für öffentliche Stellen](../verwaltung/faq/faq-integrationen.md)
- [Erweiterungspunkte auf einen Blick](../erweiterungsentwicklung/teil-8-referenz/erweiterungspunkte-auf-einen-blick.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
