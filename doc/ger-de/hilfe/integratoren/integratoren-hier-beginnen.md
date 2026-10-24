---
title: "Integratoren: hier beginnen"
audience: integrator, developer, partner
category: integration
order: 1
summary: Die Einstiegsseite für Systemintegratoren und Integrationsentwickler, die Exponential mit CRM, ERP, PIM, DAM, Identitätsanbietern, Suche, E-Mail, Zahlung, Analyse und anderen Inhaltssystemen verbinden. Welche Integrationsschnittstellen Exponential wirklich hat, wie dieser Bereich gegliedert ist, Lesepfade nach Rolle und wen Sie fragen.
keywords: Integrator, Integration, Systemintegrator, Integrationsentwickler, API, REST, OAuth, API-Schlüssel, expservices, ezjscore, Webhook, Import, Export, Synchronisation, CRM, ERP, PIM, DAM, SSO, LDAP, Suche, E-Mail, Zahlung, hier beginnen, Einstieg, system integrator, integration developer, API keys, sync, search, mail, payment, start here, landing
featured: true
type: help
---
# Integratoren: hier beginnen

Dieser Bereich der Hilfe ist für **Systemintegratoren**: Integrationsentwickler, Lösungsarchitekten und die
Integrationsunternehmen, die Exponential mit anderen Systemen verbinden. Typische Projekte verbinden ein CRM, ein ERP
oder ein Produktinformationssystem (PIM), ein Digital Asset Management (DAM), einen Identitätsanbieter, einen
Suchserver, einen E-Mail- oder Newsletter-Dienst, einen Zahlungsanbieter, ein Analysewerkzeug oder ein anderes
Content-Management-System, das ersetzt oder parallel weiterbetrieben wird.

Er beantwortet vier Fragen: welche Integrationsschnittstellen Exponential tatsächlich hat (und welche nicht), welche
zu einer bestimmten Aufgabe passt, wie Sie eine Integration bauen, die Importe, Ausfälle und Upgrades übersteht, und
wie Sie ein Integrationsprojekt von der Analyse bis zur Übergabe durchführen.

> **Hinweis**
> Jede technische Aussage in diesem Bereich nennt die Datei, Einstellung, Klasse oder den Befehl, auf dem sie beruht,
> und wurde am Code von Exponential 6.0.15 und der Erweiterungen geprüft, die die `composer.json` im
> Wurzelverzeichnis voraussetzt. Wo etwas nicht angeboten wird, sagt die Seite das klar. Beispiele verwenden
> `example.com`, „Ihr Unternehmen“ und Platzhalter-Bezeichner; keine Seite beschreibt einen echten Kunden oder Partner.

## Inhalt

1. [Exponential integrieren, in einer Minute](#exponential-integrieren-in-einer-minute)
2. [Was dieser Bereich abdeckt und was nicht](#was-dieser-bereich-abdeckt-und-was-nicht)
3. [Übersicht der Seiten für Integratoren](#übersicht-der-seiten-für-integratoren)
4. [Lesepfade nach Rolle](#lesepfade-nach-rolle)
5. [Ihre erste Woche in einem Integrationsprojekt](#ihre-erste-woche-in-einem-integrationsprojekt)
6. [Wen Sie fragen](#wen-sie-fragen)

## Exponential integrieren, in einer Minute

- **Zwei HTTP-APIs werden mit dem Produkt ausgeliefert.** Die REST-Inhalts-API der Erweiterung `ezprestapi` antwortet
  unter `/api/ezp/v1/` und `/api/ezp/v2/` (Lesen in beiden Versionen, Anlegen und Löschen in v2), und die Erweiterung
  `expservices` bietet etwa tausend benannte Dienste als JSON unter `/ezjscore/call/exp<domain>::<method>` an
  ([REST-API für Integratoren](rest-api-fuer-integratoren.md), [Remote-Dienste mit expservices](entfernte-dienste-mit-expservices.md)).
- **Drei Arten von Zugangsdaten.** OAuth-2.0-Anwendungen, registriert unter Setup > oAuth-Verwaltung, persönliche
  API-Schlüssel (`expk_...`) für ein Programm eines Benutzers und Tokens von `expservices` (`expt_...`). Keine davon
  kann mehr als der Benutzer dahinter ([Zugangsdaten wählen](zugangsdaten-fuer-eine-integration-waehlen.md)).
- **Die PHP-API ist die stärkste Schnittstelle.** Importe, die auf dem Server laufen, verwenden
  `eZContentFunctions::createAndPublishObject()` und `updateAndPublishObject()` mit einer stabilen `remote_id`, sodass
  Veröffentlichungsoperation, Workflows, Caches und Suchindex im Einklang bleiben ([Idempotente Importe mit remote_id als Schlüssel](idempotente-importe-ueber-die-remote-id.md)).
- **Hooks für „etwas ist passiert“.** Workflows an Triggern (zum Beispiel `content / publish / after`), Event-Listener
  von `ezpEvent`, Benachrichtigungs-Handler und die signierte Webhook-Senke des Audit-Trails
  ([Workflows und Trigger als Hooks](workflows-und-trigger-als-hooks.md),
  [Ausgehende Webhooks aus dem Audit-Trail](ausgehende-webhooks-aus-dem-audit-trail.md)).
- **Feeds, Pakete und Dateien.** Exporte als RSS 1.0, RSS 2.0, Atom und OPML, RSS-Importe per Cronjob, Pakete
  `.ezpkg`, CSV-Import und -Export und zeitgesteuerter Austausch mit SFTP, S3, WebDAV oder einem HTTP-Endpunkt über
  `xrowextract`.
- **Identität.** Login-Handler (`standard`, `LDAP`, `textfile`, Ihr eigener), Single-Sign-on-Handler und Social Login
  über die Erweiterung `sevenx_authentication_2fa` ([Login-Handler](login-handler-ldap-und-textdatei.md),
  [Single Sign-on und Social Login](single-sign-on-und-social-login.md)).
- **Alles läuft als Erweiterung.** Eine Integration ist eine Erweiterung: Einstellungen, Klassen, Cronjob-Teile,
  Konsolenbefehle, Workflow-Ereignisse. Sie lässt sich versionieren, testen, veröffentlichen und zurückgeben
  ([Eine Integration als Erweiterung bauen](eine-integration-als-erweiterung-bauen.md)).
- **Was es nicht gibt.** Kein GraphQL, keine OpenAPI-Beschreibung der Inhalts-API, kein XML-RPC, kein SAML, kein
  allgemeiner ausgehender Webhook für Inhalte, kein Connector für Message Broker im Kernel. Planen Sie diese als
  individuelle Arbeit ein ([Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md#was-nicht-angeboten-wird)).

## Was dieser Bereich abdeckt und was nicht

| Hier behandelt | An anderer Stelle lesen |
|---|---|
| Jede Integrationsschnittstelle für eine Aufgabe wählen und nutzen | Erweiterungen allgemein schreiben: [Erweiterungsentwicklung: das Buch](../erweiterungsentwicklung/README.md) |
| Integrationsmuster: Push und Pull, Batch und Echtzeit, Idempotenz, Abbildung | Anwendungen auf Exponential bauen: [App-Ersteller: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) |
| Sicherheit, Tests, Überwachung und Upgrades von Integrationen | Server hosten und betreiben: [Hosting-Unternehmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md) |
| Ein Integrationsprojekt durchführen: Schätzungen, Leistungsbeschreibungen, Übergabe | Das Partnerprogramm selbst: [Partner: hier beginnen](../partner/partner-hier-beginnen.md) |
| Arbeitsblätter, Checklisten, FAQ und Glossar für Integratoren | Mit Git am Code arbeiten: [Mit Git entwickeln](../mit-git-entwickeln.md) |

Zwei Schwesterbereiche sind für benachbarte Rollen geschrieben: [Wiederverkäufer: hier beginnen](../../../eng-us/help/resellers/resellers-start-here.md)
für Unternehmen, die Exponential und Leistungen rund darum verkaufen, und [Bündler: hier beginnen](../buendler/buendler-hier-beginnen.md)
für Unternehmen, die Exponential in einem Produkt oder einer Distribution ausliefern.

## Übersicht der Seiten für Integratoren

| Bereich | Seiten |
|---|---|
| Orientierung | [Was Integratoren mit Exponential tun](was-integratoren-mit-exponential-tun.md) · [Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md) · [Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md) · [Zugangsdaten wählen](zugangsdaten-fuer-eine-integration-waehlen.md) |
| HTTP-APIs | [REST-API](rest-api-fuer-integratoren.md) · [OAuth-Anwendungen](oauth-anwendungen-fuer-integrationen.md) · [Persönliche API-Schlüssel](persoenliche-api-schluessel-fuer-integrationen.md) · [Remote-Dienste mit expservices](entfernte-dienste-mit-expservices.md) · [Serverfunktionen von ezjscore](ezjscore-serverfunktionen-fuer-integrationen.md) |
| Feeds, Dateien und Protokolle | [RSS, Atom und OPML](rss-atom-und-opml-feeds.md) · [Syndication zwischen Installationen](syndizierung-zwischen-installationen.md) · [WebDAV und SOAP](webdav-und-soap.md) · [Pakete](pakete-importieren-und-exportieren.md) · [Massenaustausch mit xrowextract](massendatenaustausch-mit-xrowextract.md) |
| Serverseite | [CLI-Skripte und ezexec](cli-skripte-und-ezexec.md) · [Cronjobs](cronjobs-fuer-integrationen.md) · [Workflows und Trigger](workflows-und-trigger-als-hooks.md) · [Event-Listener](event-listener-fuer-integrationen.md) · [Audit-Webhooks](ausgehende-webhooks-aus-dem-audit-trail.md) |
| Systeme rund um die Website | [E-Mail und Benachrichtigungen](integration-von-e-mail-und-benachrichtigungen.md) · [Formulare und gesammelte Informationen](formulare-und-gesammelte-informationen.md) · [Login-Handler](login-handler-ldap-und-textdatei.md) · [Single Sign-on](single-sign-on-und-social-login.md) · [Suchmaschinen](anbindung-einer-suchmaschine.md) · [Zahlungs-Gateways](zahlungsanbieter-und-der-shop.md) · [KI, Job-Warteschlange und MCP](ki-jobwarteschlange-und-mcp.md) |
| Muster | [Push und Pull, Batch und Echtzeit](integrationsmuster-push-pull-batch-echtzeit.md) · [Idempotente Importe](idempotente-importe-ueber-die-remote-id.md) · [Klassen auf externe Daten abbilden](inhaltsklassen-auf-externe-daten-abbilden.md) · [Einen Import schreiben](ein-importskript-schreiben.md) · [Einen Export schreiben](einen-export-oder-ausgehenden-abgleich-schreiben.md) · [Aus einem anderen CMS migrieren](inhalte-aus-einem-anderen-cms-migrieren.md) |
| Qualität und Betrieb | [Fehlerbehandlung und Protokollierung](fehlerbehandlung-und-protokollierung.md) · [Sicherheit](sicherheit-fuer-integrationen.md) · [Dienstkonten und Rollen](dienstkonten-rollen-und-richtlinien.md) · [Caching nach Importen](caching-nach-importen.md) · [Velocity und Leistung](velocity-und-leistung-fuer-integrationen.md) · [Testen](integrationen-testen.md) · [Überwachung](integrationen-ueberwachen.md) · [Upgrades](upgrades-ohne-defekte-integrationen.md) |
| Bauen und teilen | [Eine Integration als Erweiterung](eine-integration-als-erweiterung-bauen.md) · [Zurückgeben](integrationen-zurueckgeben.md) |
| Projekte | [Projekt-Playbook](playbook-fuer-integrationsprojekte.md) · [Schätzen](integrationsaufwand-schaetzen.md) · [Leistungsbeschreibungen](leistungsbeschreibungen-fuer-integrationen.md) · [Übergabe](uebergabe-einer-integration.md) · [Integratoren und das Partnerprogramm](integratoren-und-das-partnerprogramm.md) |
| Arbeitsblätter und Checklisten | [Analyse-Arbeitsblatt](arbeitsblatt-zur-bestandsaufnahme-einer-integration.md) · [Vorlage für eine Schnittstellenspezifikation](vorlage-fuer-eine-schnittstellenspezifikation.md) · [Checkliste für die Sicherheitsprüfung](sicherheits-checkliste-fuer-integrationen.md) · [Checkliste zum Livegang](checkliste-fuer-den-livegang-einer-integration.md) |
| Nachschlagen | [FAQ](faq-fuer-integratoren.md) · [Glossar](glossar-fuer-integratoren.md) · [Lesepfad](lesepfad-fuer-integratoren.md) |

## Lesepfade nach Rolle

| Sie sind | Lesen Sie in dieser Reihenfolge |
|---|---|
| **Lösungsarchitekt**, der entscheidet, wie Systeme verbunden werden | [Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md), [Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md), [Push und Pull, Batch und Echtzeit](integrationsmuster-push-pull-batch-echtzeit.md), [Klassen auf externe Daten abbilden](inhaltsklassen-auf-externe-daten-abbilden.md), [Sicherheit](sicherheit-fuer-integrationen.md) |
| **Integrationsentwickler**, der den Code schreibt | [CLI-Skripte und ezexec](cli-skripte-und-ezexec.md), [Idempotente Importe](idempotente-importe-ueber-die-remote-id.md), [Einen Import schreiben](ein-importskript-schreiben.md), [Workflows und Trigger](workflows-und-trigger-als-hooks.md), [Fehlerbehandlung und Protokollierung](fehlerbehandlung-und-protokollierung.md), [Testen](integrationen-testen.md), [Eine Integration als Erweiterung](eine-integration-als-erweiterung-bauen.md) |
| **Entwickler einer externen App**, die Exponential über HTTP aufruft | [Zugangsdaten wählen](zugangsdaten-fuer-eine-integration-waehlen.md), [REST-API](rest-api-fuer-integratoren.md), [Persönliche API-Schlüssel](persoenliche-api-schluessel-fuer-integrationen.md), [Remote-Dienste mit expservices](entfernte-dienste-mit-expservices.md) |
| **Identitäts- und Sicherheitsingenieur** | [Login-Handler](login-handler-ldap-und-textdatei.md), [Single Sign-on](single-sign-on-und-social-login.md), [Dienstkonten und Rollen](dienstkonten-rollen-und-richtlinien.md), [Checkliste für die Sicherheitsprüfung](sicherheits-checkliste-fuer-integrationen.md) |
| **Betriebsingenieur**, der die Integration betreibt | [Cronjobs](cronjobs-fuer-integrationen.md), [Überwachung](integrationen-ueberwachen.md), [Caching nach Importen](caching-nach-importen.md), [Velocity und Leistung](velocity-und-leistung-fuer-integrationen.md), [Upgrades](upgrades-ohne-defekte-integrationen.md) |
| **Projektleitung oder Account Manager** bei einem Integrationsunternehmen | [Projekt-Playbook](playbook-fuer-integrationsprojekte.md), [Analyse-Arbeitsblatt](arbeitsblatt-zur-bestandsaufnahme-einer-integration.md), [Schätzen](integrationsaufwand-schaetzen.md), [Leistungsbeschreibungen](leistungsbeschreibungen-fuer-integrationen.md), [Übergabe](uebergabe-einer-integration.md), [Integratoren und das Partnerprogramm](integratoren-und-das-partnerprogramm.md) |

Der ganze Bereich in einer Reihenfolge steht im [Lesepfad für Integratoren](lesepfad-fuer-integratoren.md).

## Ihre erste Woche in einem Integrationsprojekt

| Tag | Tun Sie dies | Seite |
|---|---|---|
| 1 | Füllen Sie mit dem Kunden das Analyse-Arbeitsblatt aus: Systeme, Daten, Richtung, Menge, Zeitpunkte | [Analyse-Arbeitsblatt für Integrationen](arbeitsblatt-zur-bestandsaufnahme-einer-integration.md) |
| 1 | Prüfen Sie, welche Schnittstellen die Installation aktiv hat (`ActiveExtensions`, `rest.ini`, `soap.ini`) | [Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md) |
| 2 | Wählen Sie pro Datenfluss eine Schnittstelle und Zugangsdaten, und schreiben Sie sie auf | [Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md), [Zugangsdaten wählen](zugangsdaten-fuer-eine-integration-waehlen.md) |
| 2 | Bilden Sie jeden externen Datensatz auf eine Inhaltsklasse und ein Schema für die `remote_id` ab | [Klassen auf externe Daten abbilden](inhaltsklassen-auf-externe-daten-abbilden.md) |
| 3 | Richten Sie eine Entwicklungsinstallation aus Git und ein Dienstkonto mit einer engen Rolle ein | [Mit Git entwickeln](../mit-git-entwickeln.md), [Dienstkonten und Rollen](dienstkonten-rollen-und-richtlinien.md) |
| 4 | Bauen Sie einen ersten Import oder Aufruf von Anfang bis Ende, mit einem Probelauf | [Einen Import schreiben](ein-importskript-schreiben.md) |
| 5 | Schreiben Sie die Schnittstellenspezifikation, und vereinbaren Sie Fehlerbehandlung und Überwachung | [Vorlage für eine Schnittstellenspezifikation](vorlage-fuer-eine-schnittstellenspezifikation.md), [Überwachung](integrationen-ueberwachen.md) |

## Wen Sie fragen

| Frage | Wo |
|---|---|
| Technische Fragen zu den APIs und dem Kernel | die Foren <https://share.exponential.earth/forums> und Telegram <https://t.me/exponentialcms> |
| Bezahlter Support, Entwicklung oder eine Prüfung Ihrer Integration durch 7x | <https://se7enx.com/contact> |
| Das Partnerprogramm, Stufen, Empfehlungen | das Partnerteam: <partners@exponential.earth> oder das Formular unter <https://exponential.earth/partner/contact-us> |
| Ein Sicherheitsproblem, das Sie beim Integrieren gefunden haben | security@se7enx.com, vertraulich, nie in einem öffentlichen Issue |
| Ein Fehler oder eine fehlende Funktion in einer Erweiterung | das Repository der Erweiterung auf <https://github.com/se7enxweb> ([Gute Issues schreiben](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/gute-issues-schreiben.md)) |

## Verwandte Seiten

- [Mit Git entwickeln: vom Benutzer zum Entwickler](../mit-git-entwickeln.md)
- [Erweiterungsentwicklung: das Buch (hier beginnen)](../erweiterungsentwicklung/README.md)
- [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Implementierungspartner](../partner/implementierungspartner.md)
- [Wiederverkäufer: hier beginnen](../../../eng-us/help/resellers/resellers-start-here.md), [Bündler: hier beginnen](../buendler/buendler-hier-beginnen.md)
- [App-Ersteller: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
