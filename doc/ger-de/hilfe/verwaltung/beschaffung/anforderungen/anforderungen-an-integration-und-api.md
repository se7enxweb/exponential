---
title: Anforderungen an Integration und API
audience: government, project-manager, developer, partner
category: public-sector
order: 90
summary: Formulierungen dazu, wie ein öffentliches CMS sich mit anderen Systemen verbindet - Inhalts-API, Authentifizierung, Anmeldung über Verzeichnisdienste, Feeds, Webhooks, E-Mail - mit den dokumentierten Protokollen von Exponential und ihrem Status laut der Seite zu den Standards für APIs und Protokolle.
keywords: Integration, API, REST, OAuth, API-Schlüssel, LDAP, Single Sign-on, SSO, Webhooks, RSS, Sitemaps, SMTP, WebDAV, SOAP, Interoperabilität, offene Standards, Ausschreibung, integration, API keys, single sign-on, interoperability, open standards, tender
type: help
---
# Anforderungen an Integration und API

Diese Seite richtet sich an Projektleitungen und Architekten, die festlegen müssen, wie das CMS Daten mit dem Rest der
Organisation austauscht: einem Bürgerportal, einem Verzeichnisdienst, einem Schriftgutsystem, einer mobilen App, einer
Suchplattform. Sie gibt Formulierungen für Anforderungen und den Status jedes Protokolls, wie er in den
[Standards zu APIs und Protokollen](../../../../../eng-us/standards/apis-and-protocols.md) dokumentiert ist.

## Inhalt
1. [Gehen Sie von Ihrer Integrationslandkarte aus](#gehen-sie-von-ihrer-integrationslandkarte-aus)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Die dokumentierten Protokolle von Exponential](#die-dokumentierten-protokolle-von-exponential)
4. [Lücken, nach denen Sie fragen sollten](#lücken-nach-denen-sie-fragen-sollten)
5. [Fallstricke](#fallstricke)

## Gehen Sie von Ihrer Integrationslandkarte aus

Führen Sie jedes System auf, mit dem das CMS sprechen muss, in welche Richtung, wie oft und mit welchen Daten.
Anforderungen, die aus dieser Liste geschrieben werden, sind konkret; Anforderungen wie „muss eine offene API haben“
sind es nicht.

| System | Richtung | Daten | Häufigkeit |
|---|---|---|---|
| <Verzeichnisdienst> | eingehend | Konten der Beschäftigten, Gruppen | bei der Anmeldung |
| <Schriftgutsystem> | ausgehend | veröffentlichte Seiten und Dokumente | <Intervall> |
| <mobile App> | ausgehend | Nachrichten, Veranstaltungen | auf Anfrage |
| <Mail-Relay> | ausgehend | Benachrichtigungen, Newsletter | laufend |

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| INT-01 | Veröffentlichte Inhalte sind über eine dokumentierte HTTP-API verfügbar, die JSON liefert; beschreiben Sie Authentifizierung, Versionen und Grenzen. |
| INT-02 | Skripte und Integrationen authentifizieren sich, ohne das Passwort einer Person zu teilen (Tokens oder Schlüssel, die sich widerrufen lassen). |
| INT-03 | Beschäftigte können sich über den Verzeichnisdienst der Organisation anmelden (<Protokoll>); beschreiben Sie die unterstützten Protokolle und ihre Sicherheitsoptionen. |
| INT-04 | Inhaltslisten werden als RSS- oder Atom-Feeds veröffentlicht, und die Website veröffentlicht eine XML-Sitemap. |
| INT-05 | Sicherheitsrelevante Ereignisse lassen sich an die Log- oder SIEM-Plattform der Organisation liefern, mit Integritätsschutz. |
| INT-06 | E-Mails werden über das SMTP-Relay der Organisation mit TLS versandt. |
| INT-07 | Inhalte lassen sich in großen Mengen aus <Format> importieren und in einem offenen, dokumentierten Format exportieren. |
| INT-08 | Jede API und jedes Import- und Exportformat ist öffentlich dokumentiert. |

## Die dokumentierten Protokolle von Exponential

| Id | Nachweis | Status |
|---|---|---|
| INT-01 | REST API v1 und v2 aus einer Erweiterung ([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)); JSON | REST **Teilweise erfüllt**, JSON **Erfüllt** |
| INT-02 | OAuth-2.0-Token-Endpunkt; persönliche API-Schlüssel ([Persönliche API-Schlüssel](../../../../anleitungen/api-schluessel.md)); entfernte Dienste ([Entfernte Dienste und Apps](../../../../anleitungen/entfernte-dienste-und-apps.md)) | OAuth 2.0 **Teilweise erfüllt** (kein PKCE auf Serverseite) |
| INT-03 | LDAP-Authentifizierung im Kernel; Anmeldung über soziale Netzwerke und OpenID Connect über `sevenx_authentication_2fa` | LDAP **Teilweise erfüllt** (standardmäßig LDAPv2, kein StartTLS); OIDC-Client **Teilweise erfüllt** (ID-Tokens nicht validiert) |
| INT-04 | Export von RSS 1.0/2.0 und Atom, Import von RSS ([RSS-Feeds](../../../../anleitungen/rss-feeds.md)); Sitemaps mit `bcgooglesitemaps` | RSS **Erfüllt**, Atom **Teilweise erfüllt**, Sitemaps **Erfüllt** |
| INT-05 | Audit-Senken einschließlich eines signierten ausgehenden Webhooks (HMAC-SHA-256) ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) | **Erfüllt** mit seinem eigenen dokumentierten Vertrag |
| INT-06 | SMTP-Transport mit STARTTLS | **Erfüllt** |
| INT-07 | Exportwerkzeuge (Website-Archiv, CSV, JSON, Pakete) ([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)); Importwerkzeuge ([Die Werkzeuge von Exponential](../../../../installation/migration/exponential-tools.md)) | dokumentiert |
| INT-08 | Die API-Leitfäden und Seiten zu den Standards oben sind öffentlich in `doc/` unter der GNU FDL | kein OpenAPI-Dokument für die REST API des Kernels |

## Lücken, nach denen Sie fragen sollten

Aus den [Standards zu APIs und Protokollen](../../../../../eng-us/standards/apis-and-protocols.md):

- **Keine OpenAPI-Beschreibung** der REST API des Kernels; fragen Sie den Bieter, wie er die Endpunkte dokumentiert, die
  er baut.
- **SAML** ist unter den dokumentierten Protokollen nicht aufgeführt. Verlangt Ihr Verzeichnisdienst Single Sign-on über
  SAML, fragen Sie den Bieter, wie er es liefert, und behandeln Sie es als eigens entwickelte Arbeit.
- Die Standardwerte für **LDAP** (Version 2, kein StartTLS) müssen geändert werden oder brauchen einen sicheren
  Netzwerkpfad; schreiben Sie die Zielkonfiguration in die Anforderung.
- **SOAP** existiert, ist aber aus (`EnableSOAP=false`) und hat kein WSDL; **XML-RPC** ist nicht implementiert.
- **Bearer-Tokens in Query-Parametern** werden akzeptiert; bitten Sie den Bieter, Clients so zu konfigurieren, dass sie
  den Header verwenden.

## Fallstricke

- „Muss mit allem integriert werden.“ Nennen Sie die Systeme; bei Integrationen überziehen Projekte.
- Annehmen, eine API bedeute Synchronisation in Echtzeit. Nennen Sie die Häufigkeit und wer den Job betreibt.
- Das Eigentum vergessen: Für Sie geschriebener Integrationscode sollte als Quellcode geliefert werden
  ([Ausschreibungsvorlage: Rechte an individuellen Entwicklungen](../ausschreibungsvorlagen/klausel-rechte-an-individuellen-entwicklungen.md)).

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Funktionale Anforderungen an ein CMS für den öffentlichen Sektor](funktionale-anforderungen-an-ein-oeffentliches-cms.md)
- [Anforderungen an Ausstieg und Portabilität](anforderungen-an-ausstieg-und-portabilitaet.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
