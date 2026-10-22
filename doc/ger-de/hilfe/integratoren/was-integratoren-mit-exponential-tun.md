---
title: Was Integratoren mit Exponential tun
audience: integrator, developer, project-manager
category: integration
order: 10
summary: Die typischen Integrationsaufgaben rund um eine Exponential-Website (CRM, ERP, PIM, DAM, Identität, Suche, Mail, Zahlung, Analyse, andere CMS), welche Rolle Exponential in jeder spielt und welche Schnittstelle die Daten meist trägt.
keywords: Integrator, Integrationsaufgaben, Identitätsanbieter, Suche, Mail, Newsletter, Zahlung, Analyse, CMS-Migration, Content-Hub, integrator, integration jobs, CRM, ERP, PIM, DAM, identity provider, LDAP, search, Solr, mail, newsletter, payment, analytics, CMS migration, headless, content hub
type: help
---
# Was Integratoren mit Exponential tun

Diese Seite richtet sich an Integratoren, die neu bei Exponential sind und wissen möchten, welche Art von Arbeit sie
erwartet. Sie zählt die Systeme auf, die üblicherweise mit einem Content-Management-System verbunden werden, sagt,
welche Rolle Exponential in jeder Verbindung spielt, und nennt die Schnittstelle, die die Daten normalerweise trägt.
Die Einzelheiten jeder Schnittstelle stehen auf ihrer eigenen Seite.

## Inhalt

1. [Der Platz von Exponential in einer Systemlandschaft](#der-platz-von-exponential-in-einer-systemlandschaft)
2. [Typische Integrationsaufgaben](#typische-integrationsaufgaben)
3. [Wer welche Daten führt](#wer-welche-daten-führt)
4. [Was ein Integrator liefert](#was-ein-integrator-liefert)

## Der Platz von Exponential in einer Systemlandschaft

Exponential ist ein Content-Management-System mit einem Inhalts-Repository (Klassen, Objekte, Versionen, Orte in einem
Baum), Benutzern und Rollen, einer Template-Engine, einem Shop-Modul, Formularen, die Informationen sammeln, und einer
Administration. In einer Landschaft von Geschäftssystemen übernimmt es meist eine oder mehrere dieser Rollen:

| Rolle | Was sie bedeutet | Typische Schnittstellen |
|---|---|---|
| **Veröffentlicher** | Empfängt Daten aus anderen Systemen und veröffentlicht sie als Seiten | PHP-Importskripte mit `eZContentFunctions`, die REST-Route zum Anlegen, Importe mit `xrowextract`, RSS-Importe |
| **Content-Hub** | Führt redaktionelle Inhalte, die andere Kanäle lesen | Die REST-Inhalts-API, `expservices`, RSS- und Atom-Exporte, `syndication` |
| **Sammler** | Sammelt Formularantworten, Registrierungen und Bestellungen, die andere Systeme verarbeiten | Informationssammlung, der Shop, Benachrichtigungs- und Audit-Hooks, CSV-Exporte |
| **Vertrauende Partei** | Lässt Menschen sich mit einem Konto anmelden, das anderswo liegt | Login-Handler (LDAP, Textdatei, ein eigener), Single-Sign-on-Handler, Social Login |
| **Verbraucher von Diensten** | Verwendet einen Mailserver, einen Suchserver, einen Zahlungsanbieter, einen KI-Anbieter | `[MailSettings]`, ein Such-Plugin, Zahlungsgateways, `expai` |

## Typische Integrationsaufgaben

| System | Typische Aufgabe | Richtung | Übliche Schnittstelle | Seite |
|---|---|---|---|---|
| CRM | Leads aus Kontaktformularen gehen ins CRM; Kontodaten erscheinen auf Mitgliederseiten | Hinaus, manchmal herein | Workflow-Ereignis oder Cronjob, der gesammelte Informationen liest; ein Login- oder SSO-Handler für Mitglieder | [Formulare und gesammelte Informationen](formulare-und-gesammelte-informationen.md) |
| ERP | Produktdaten und Preise kommen herein; Bestellungen gehen hinaus | Beide | Importskript, geschlüsselt nach `remote_id`; Shop-Trigger (`shop / checkout`, `confirmorder`) | [Zahlungsanbieter und der Shop](zahlungsanbieter-und-der-shop.md) |
| PIM | Produktseiten werden aus dem PIM angelegt und aktualisiert | Herein | Geplanter Import (Cronjob-Teil) oder ein Zeitplan von `xrowextract` | [Ein Importskript schreiben](ein-importskript-schreiben.md) |
| DAM | Bilder und Dateien kommen aus dem Asset-System | Herein | Import mit Bild- und Dateiattributen, per Pfad angegeben (`storage_dir`) | [Klassen externen Daten zuordnen](inhaltsklassen-auf-externe-daten-abbilden.md) |
| Identitätsanbieter | Mitarbeiter oder Mitglieder melden sich mit ihrem zentralen Konto an | Herein (Identität) | `LoginHandler[]` (LDAP oder ein eigener), `SingleSignOnHandlerArray[]` | [Login-Handler](login-handler-ldap-und-textdatei.md) |
| Suchserver | Website-Suche mit Facetten und Relevanz | Hinaus (Index) | Ein Such-Plugin (`[SearchSettings] SearchEngine`), `ezfind` für Solr | [Anbindung einer Suchmaschine](anbindung-einer-suchmaschine.md) |
| Mail und Newsletter | Transaktionsmails über einen Anbieter; Newsletter | Hinaus | `[MailSettings] Transport` oder eine eigene Transportklasse; `cjw_newsletter` | [Mail und Benachrichtigungen](integration-von-e-mail-und-benachrichtigungen.md) |
| Zahlungsanbieter | Zahlungen beim Bestellabschluss | Beide | Ein Zahlungsgateway (`paymentgateways.ini`), `ezpaypal` | [Zahlungsanbieter und der Shop](zahlungsanbieter-und-der-shop.md) |
| Analyse | Seiten-Tracking, Statistiken | Hinaus | Templates (Tracking-Snippet, beachten Sie die Content Security Policy); `bcwebsitestatistics` | [Sicherheit für Integrationen](sicherheit-fuer-integrationen.md) |
| Ein anderes CMS | Inhalte hereinholen oder in beide Richtungen austauschen | Herein oder beide | Importskripte, Pakete, `syndication`, RSS | [Inhalte aus einem anderen CMS migrieren](inhalte-aus-einem-anderen-cms-migrieren.md) |
| Mobile oder JavaScript-App | Liest Inhalte, veröffentlicht manchmal | Beide | REST-API mit OAuth oder API-Schlüsseln; `expservices` | [REST-API für Integratoren](rest-api-fuer-integratoren.md) |
| Überwachung und SIEM | Sicherheits- und Änderungsereignisse in eine Protokollplattform | Hinaus | Ziele des Audit-Protokolls: Syslog, signierter Webhook, Mail | [Ausgehende Webhooks aus dem Audit-Protokoll](ausgehende-webhooks-aus-dem-audit-trail.md) |

> **Hinweis**
> Die Spalte "Übliche Schnittstelle" ist ein Ausgangspunkt, keine Regel. Ein täglicher Produktimport mit 50 000
> Datensätzen ist ein serverseitiges Skript, auch wenn die REST-API dieselben Objekte einzeln anlegen könnte.
> [Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md) erklärt die Abwägungen.

## Wer welche Daten führt

Jede Integration braucht je Art von Daten eine klare Antwort: welches System **führend** ist (die Quelle der
Wahrheit) und welche Systeme **Kopien** halten. Schreiben Sie es auf, bevor Sie Code schreiben.

| Frage | Warum sie in Exponential zählt |
|---|---|
| Ist das externe System oder Exponential für diesen Datensatz führend? | Ist das externe System führend, dürfen Redakteure importierte Felder nicht ändern, sonst überschreibt der nächste Lauf ihre Änderungen. Kennzeichnen Sie solche Felder in der Klassenbeschreibung oder beschränken Sie das Bearbeiten mit Rollen. |
| Was identifiziert den Datensatz in beiden Systemen? | Verwenden Sie die externe ID in der `remote_id` des Objekts (eindeutig, bis zu 100 Zeichen), nicht die Objekt-ID von Exponential, die sich zwischen Installationen unterscheidet. |
| Was geschieht, wenn der Datensatz im führenden System gelöscht wird? | Entscheiden Sie zwischen Entfernen in den Papierkorb, Verbergen oder Behalten mit einem Status; jedes hat andere Folgen für URLs und Suche. |
| Wer darf veröffentlichen? | Ein Import veröffentlicht über die Veröffentlichungsoperation; liegt ein Freigabe-Workflow auf diesem Trigger, warten importierte Versionen auf Freigabe. |

## Was ein Integrator liefert

Eine vollständige Integration ist mehr als der Code, der Daten bewegt:

- [ ] Eine Schnittstellenspezifikation je Datenfluss ([Vorlage](vorlage-fuer-eine-schnittstellenspezifikation.md)).
- [ ] Die Integration als Erweiterung in ihrem eigenen Git-Repository ([wie](eine-integration-als-erweiterung-bauen.md)).
- [ ] Ein Dienstkonto mit einer engen Rolle ([wie](dienstkonten-rollen-und-richtlinien.md)).
- [ ] Zugangsdaten außerhalb von Git, in `settings/override` oder der Umgebung ([wie](sicherheit-fuer-integrationen.md)).
- [ ] Ein Probelaufmodus, Protokollierung und eine Überwachungsprüfung ([wie](integrationen-ueberwachen.md)).
- [ ] Tests, die ohne das externe System laufen ([wie](integrationen-testen.md)).
- [ ] Ein Runbook und eine Übergabe an die Menschen, die sie betreiben ([wie](uebergabe-einer-integration.md)).

## Verwandte Seiten

- [Integratoren: hier beginnen](integratoren-hier-beginnen.md)
- [Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md)
- [Implementierungspartner](../partner/implementierungspartner.md)
- [Headless Exponential](../ersteller/anwendungsentwickler/headless-exponential.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
