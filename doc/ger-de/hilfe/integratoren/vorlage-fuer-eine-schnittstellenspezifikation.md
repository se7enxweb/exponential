---
title: Vorlage für eine Schnittstellenspezifikation
audience: integrator, developer, project-manager
category: integration
order: 490
summary: Eine Vorlage, um einen Integrationsfluss mit Exponential zu spezifizieren - Zweck, Beteiligte, Schnittstelle und Endpunkte, Authentifizierung, Datenabbildung und Schlüssel, Zeitpunkte und Mengen, Fehlerbehandlung, Sicherheit, Überwachung, Test- und Abnahmefälle und Änderungshistorie - mit Hinweisen, was einzutragen ist.
keywords: Schnittstellenspezifikation, Schnittstellenkontrolldokument, ICD, API-Spezifikation, Vorlage, Datenabbildung, Endpunkte, Authentifizierung, Fehlerbehandlung, SLA, Überwachung, Abnahmefälle, Änderungsprotokoll, interface specification, interface control document, API specification, template, data mapping, endpoints, authentication, error handling, monitoring, acceptance cases, change log
type: help
---
# Vorlage für eine Schnittstellenspezifikation

Diese Seite ist für Integratoren, die einen Integrationsfluss dokumentieren. Eine Spezifikation pro Fluss ist der
Vertrag zwischen den Teams: Entwickler bauen danach, Tester testen dagegen, Betreiber betreiben ihn, und der nächste
Integrator versteht ihn. Kopieren Sie die Vorlage unten; die Hinweise danach sagen, was in jeden Abschnitt gehört.

## Inhalt

1. [Die Vorlage](#die-vorlage)
2. [Hinweise pro Abschnitt](#hinweise-pro-abschnitt)
3. [Sie lebendig halten](#sie-lebendig-halten)

## Die Vorlage

Die Vorlage steht auf Englisch, damit sie in gemischten Teams und im Repository einheitlich bleibt; übersetzen Sie die
Überschriften bei Bedarf (Zweck, Beteiligte, Schnittstelle und Endpunkte, Authentifizierung und Autorisierung, Daten,
Zeitpunkte und Mengen, Fehlerbehandlung, Sicherheit, Überwachung und Betrieb, Tests und Abnahme, Änderungshistorie).

```text
INTERFACE SPECIFICATION   <flow name>                     version <n>   date <date>   status draft / agreed / live

1  Purpose
   One paragraph: what moves, why, for whom.

2  Parties
   Source system: <name, version, owner, contact>
   Target system: <name, version, owner, contact>
   Exponential installation: <host>, siteaccess <name>, version <6.0.x>

3  Surface and endpoints
   Surface: <REST /api/ezp/v2/... | expservices exp<domain>::<method> | cronjob part <name> | workflow event on <trigger>
            | file via <SFTP/S3/...> | RSS export /rss/feed/<address> | audit webhook>
   Endpoints / paths / files: ...
   Direction and initiator: ...

4  Authentication and authorization
   Credential: <personal API key of svc-... | OAuth application ... | expservices token | basic | none>
   Service account and role: <login>, role <name> (policies and limitations)
   Where the secret is stored on each side: ...
   Rotation: every <n> days; next <date>

5  Data
   5.1 Objects / records and the content class: <class identifier>
   5.2 Key: remote_id scheme <prefix>-<type>-<source id>
   5.3 Mapping table: source field | attribute | datatype | transformation | owner | required
   5.4 Removals: <remove | hide | state | keep>
   5.5 Languages, locations, sections, relations: ...
   5.6 Personal data: <none | fields> ; retention ...

6  Timing and volume
   Schedule / trigger: ...    Acceptable delay: ...
   Volume: total ..., per day ..., peak ...
   Full and delta runs: ...

7  Error handling
   Transient errors: retries <n>, backoff <...>, dead letter <where>
   Invalid records: skipped and reported to <who>
   Fatal conditions: <list>, run stops, alert <who>
   Safety stop: <rule>

8  Security
   Transport: HTTPS / SFTP ...; certificate checks on
   Input validation: ...
   Signatures: <HMAC header, secret location> or n/a

9  Monitoring and operations
   Log: var/log/<file>.log, summary line format
   Checks and thresholds: ...
   Runbook section: ...

10 Tests and acceptance
   Test data: ...
   Acceptance cases: AC-1 ..., AC-2 ...

11 Change history
   <version> <date> <author> <change>
```

## Hinweise pro Abschnitt

| Abschnitt | Eintragen |
|---|---|
| 3 Schnittstelle | die genaue Route, den Dienst oder den Namen des Teils, damit ein Leser den Code findet; warum diese Schnittstelle ([Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md)) |
| 4 Authentifizierung | die Art der Zugangsdaten und die Rolle; nie das Geheimnis ([Zugangsdaten wählen](zugangsdaten-fuer-eine-integration-waehlen.md), [Dienstkonten](dienstkonten-rollen-und-richtlinien.md)) |
| 5 Daten | die Abbildungstabelle aus [Klassen auf externe Daten abbilden](inhaltsklassen-auf-externe-daten-abbilden.md#eine-abbildungstabelle) und das Schlüsselschema aus [Idempotente Importe](idempotente-importe-ueber-die-remote-id.md#ein-schlüsselschema) |
| 6 Zeitpunkte | mit dem Datenverantwortlichen vereinbart, nicht mit dem Entwickler |
| 7 Fehler | die drei Arten aus [Fehlerbehandlung und Protokollierung](fehlerbehandlung-und-protokollierung.md#drei-arten-von-fehlern) |
| 9 Überwachung | der Plan aus [Integrationen überwachen](integrationen-ueberwachen.md#ein-überwachungsplan) |
| 10 Abnahme | Fälle in fachlicher Sprache mit echten Datensätzen, wie in [Integrationen testen](integrationen-testen.md#abnahme-mit-dem-kunden) |

## Sie lebendig halten

- Legen Sie die Spezifikation im Repository der Integrationserweiterung ab (`doc/`), neben dem Code, den sie
  beschreibt.
- Jede Änderung des Flusses ändert die Spezifikation im selben Pull Request.
- Erhöhen Sie die Version, und ergänzen Sie eine Zeile in der Änderungshistorie; die Leistungsbeschreibung verweist auf
  eine Version.
- Bei der Übergabe gehören die Spezifikationen zum Paket ([Übergabe](uebergabe-einer-integration.md)).

## Verwandte Seiten

- [Analyse-Arbeitsblatt für Integrationen](arbeitsblatt-zur-bestandsaufnahme-einer-integration.md)
- [Leistungsbeschreibungen für Integrationen](leistungsbeschreibungen-fuer-integrationen.md)
- [Ihre Erweiterung dokumentieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ihre-erweiterung-dokumentieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
