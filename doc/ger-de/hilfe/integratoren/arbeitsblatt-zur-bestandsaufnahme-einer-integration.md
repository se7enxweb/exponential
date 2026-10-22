---
title: Arbeitsblatt zur Bestandsaufnahme einer Integration
audience: integrator, project-manager
category: integration
order: 480
summary: Ein Arbeitsblatt, das Sie zu Beginn eines Integrationsprojekts mit dem Kunden ausfüllen - die Installation, die Systeme drumherum, jeder Datenfluss mit Richtung, Zeitverhalten, Volumen, Schlüsseln und Verantwortlichen, Identität, Rahmenbedingungen und die offenen Fragen - fertig zum Kopieren.
keywords: Bestandsaufnahme, Arbeitsblatt, Workshop, Anforderungen, Datenflüsse, Systemlandschaft, Fragebogen, Integrationsinventar, Umfang, Vorlage, discovery, worksheet, requirements, data flows, system landscape, questionnaire, integration inventory, scoping, template
type: help
---
# Arbeitsblatt zur Bestandsaufnahme einer Integration

Diese Seite ist für Integratoren, die den Workshop zur Bestandsaufnahme eines Integrationsprojekts leiten. Kopieren
Sie das Arbeitsblatt, füllen Sie es mit dem Kunden aus und bewahren Sie es als Grundlage für die
Schnittstellenspezifikationen und die Schätzung auf. Jede Frage verweist auf die Seite, die erklärt, warum sie zählt.

## Inhalt

1. [So verwenden Sie es](#so-verwenden-sie-es)
2. [Teil 1: die Installation](#teil-1-die-installation)
3. [Teil 2: die Systeme drumherum](#teil-2-die-systeme-drumherum)
4. [Teil 3: ein Block pro Datenfluss](#teil-3-ein-block-pro-datenfluss)
5. [Teil 4: Identität und Zugriff](#teil-4-identität-und-zugriff)
6. [Teil 5: Rahmenbedingungen und offene Fragen](#teil-5-rahmenbedingungen-und-offene-fragen)

## So verwenden Sie es

- Füllen Sie es in einem Workshop mit der Projektleitung des Kunden, den Verantwortlichen der anderen Systeme und einem
  Betreiber aus.
- Bitten Sie um **Beispiele**: eine echte Exportdatei, eine echte API-Antwort, echte Formulareinsendungen. Eine
  Bestandsaufnahme ohne echte Daten unterschätzt Probleme mit der Datenqualität jedes Mal.
- Markieren Sie jede Antwort, die eine Vermutung ist; sie wird zu einer Annahme in der Leistungsbeschreibung.

## Teil 1: die Installation

```text
Exponential version (Setup > System information or composer show): ...........
Distribution (Legacy / Nexus): ...........   Database engine: ...........   PHP: ...........
Served by (Apache + PHP-FPM / Velocity / FrankenPHP): ...........
Siteaccesses involved: ...........
Extensions active that matter (ezprestapi, expservices, xrowextract, syndication, sevenx_authentication_2fa ...):
...........
Environments available (development / staging / production), and who hosts them: ...........
Who may deploy code, and how (Git, Composer, a pipeline): ...........
```

Warum: [Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md#prüfen-was-eine-installation-aktiv-hat).

## Teil 2: die Systeme drumherum

```text
System         Product and version   Owner (team, contact)   API / export available   Test instance?   Docs?
CRM            ...                   ...                     ...                      ...              ...
ERP            ...
PIM / DAM      ...
Identity       ...
Search         ...
Mail           ...
Payment        ...
Analytics      ...
Other CMS      ...
```

Warum: [Was Integratoren mit Exponential tun](was-integratoren-mit-exponential-tun.md).

## Teil 3: ein Block pro Datenfluss

```text
Flow name: ...........................................   Priority: must / should / could
Direction: into Exponential / out of Exponential / both
Master of the data: ...........   Fields owned by editors: ...........
Started by: Exponential (push) / the other system (push) / a schedule (pull)
Timing: nightly / hourly / within minutes / on event       Acceptable delay: ...........
Volume: records in total ...........  changes per day ...........  peak ...........
Record key at the source: ...........   remote_id scheme: ...........
Content: plain fields / rich text / media / relations / languages (which) ...........
Removals at the source: remove / hide / state / keep
Errors: what happens with an invalid record? who is told?
Personal data in the flow: yes / no, which ...........
Sample received: yes / no (file, date) ...........
Surface proposed: ...........   Credential proposed: ...........
```

Warum: [Push und Pull, Stapel und Echtzeit](integrationsmuster-push-pull-batch-echtzeit.md),
[Idempotente Importe](idempotente-importe-ueber-die-remote-id.md),
[Zugangsdaten wählen](zugangsdaten-fuer-eine-integration-waehlen.md).

## Teil 4: Identität und Zugriff

```text
Who signs in to the site?  staff ......  members ......  partners ......  public ......
Where do their accounts live? directory (LDAP, Active Directory) / identity provider (SAML, OIDC) / social / Exponential
Password typed on the site acceptable? yes / no
Groups and roles derived from the directory? which mapping ...........
Leavers: how is Exponential told? ...........
Apps acting for users (OAuth)? ...........
```

Warum: [Login-Handler](login-handler-ldap-und-textdatei.md), [Single Sign-on](single-sign-on-und-social-login.md).

## Teil 5: Rahmenbedingungen und offene Fragen

```text
Security requirements (network zones, allowed protocols, IP allowlists, SIEM): ...........
Data protection (processors, regions, retention): ...........
Availability needed per flow, maintenance windows: ...........
Deadlines and fixed dates: ...........
Budget frame: ...........
Open questions (owner, due date):
1 ...........
2 ...........
```

Warum: [Sicherheit für Integrationen](sicherheit-fuer-integrationen.md), [Schätzen](integrationsaufwand-schaetzen.md).

## Verwandte Seiten

- [Vorlage für eine Schnittstellenspezifikation](vorlage-fuer-eine-schnittstellenspezifikation.md)
- [Playbook für Integrationsprojekte](playbook-fuer-integrationsprojekte.md)
- [Workshops zur Inhaltsmodellierung](../fuehrung/workshops-zur-inhaltsmodellierung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
