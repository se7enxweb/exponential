---
title: Ein nur lesender API-Schlüssel, Schritt für Schritt
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 65
summary: Eine Aufgabenanleitung für die engsten Zugangsdaten, die ein KI-Agent in Exponential halten kann - ein Konto für den Agenten, eine Rolle mit apikey/create, beschränkt auf den Geltungsbereich read, ein persönlicher API-Schlüssel mit kurzer Laufzeit, ein erster Test mit curl, die Antworten, die der Agent behandeln muss, sowie Erneuerung und Widerruf.
keywords: API-Schlüssel, Geltungsbereich read, persönlicher API-Schlüssel, expk_, apikey/create, Einschränkung Scope, Agentenkonto, Test mit curl, Bearer, insufficient_scope, erneuern, widerrufen, minimale Rechte, API key, read scope, personal API key, Scope limitation, agent account, curl test, rotate, revoke, least privilege
type: help
---
# Ein nur lesender API-Schlüssel, Schritt für Schritt

Diese Seite richtet sich an Administratoren und Entwickler, die einem KI-Agenten über die REST-API Lesezugriff auf Inhalte geben.
Sie führt durch das Erstellen der engsten Zugangsdaten: ein persönlicher API-Schlüssel nur mit dem Geltungsbereich `read`, der einem
für den Agenten angelegten Konto gehört. Die vollständige Referenz ist [Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md).

## Inhalt

1. [Schritt 1: ein Konto für den Agenten](#schritt-1-ein-konto-für-den-agenten)
2. [Schritt 2: eine Rolle, die nur Leseschlüssel erlaubt](#schritt-2-eine-rolle-die-nur-leseschlüssel-erlaubt)
3. [Schritt 3: den Schlüssel erstellen](#schritt-3-den-schlüssel-erstellen)
4. [Schritt 4: ihn testen](#schritt-4-ihn-testen)
5. [Schritt 5: ihn an den Agenten übergeben](#schritt-5-ihn-an-den-agenten-übergeben)
6. [Schritt 6: erneuern und widerrufen](#schritt-6-erneuern-und-widerrufen)

## Schritt 1: ein Konto für den Agenten

Legen Sie ein Benutzerkonto an, das nach dem Agenten und seiner Aufgabe benannt ist ("Berichtsagent"), in einer Benutzergruppe für
Agenten. Geben Sie der Gruppe eine Rolle mit `content/read` für genau die Inhalte, die der Agent lesen soll, wo möglich nach Teilbaum
oder Sektion beschränkt ([Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)).

Prüfen Sie, was er lesen kann:

```bash
./console exp:access:check --user=<agent login> --node=<a node it should read>
./console exp:access:check --user=<agent login> --node=<a node it should not read>
```

Die erste Prüfung sollte mit ALLOWED enden, die zweite mit DENIED und der Einschränkung, die abgelehnt hat.

## Schritt 2: eine Rolle, die nur Leseschlüssel erlaubt

In einer neuen Installation darf keine Rolle Schlüssel erstellen. Fügen Sie der Rolle des Agenten hinzu:

1. Setup > Rollen und Richtlinien > die Rolle > **Neue Richtlinie**.
2. Modul **apikey**, Funktion **create**.
3. Einschränkung **Scope**: nur `read`.

Ein Schlüssel erhält nie ein Recht, das sein Eigentümer nicht hat, und die Seite bietet nur die Geltungsbereiche an, die die Rechte
des Eigentümers erlauben.

## Schritt 3: den Schlüssel erstellen

1. Melden Sie sich als das Konto des Agenten an und öffnen Sie **Mein Konto > API-Zugang** (`/apikey/list`).
2. Benennen Sie ihn unter **Neuen Schlüssel erstellen** nach dem Ort seiner Verwendung ("Berichtsagent, wöchentlicher Lauf").
3. Kreuzen Sie nur **read** an.
4. Wählen Sie die kürzeste passende Laufzeit (standardmäßig 7, 30, 90 oder 365 Tage).
5. Drücken Sie **Schlüssel erstellen**. Der Schlüssel `expk_...` wird **einmal** angezeigt; kopieren Sie ihn direkt in Ihren
   Geheimnisspeicher.

## Schritt 4: ihn testen

```bash
export EXP_API_KEY='expk_...'
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
```

| Antwort | Bedeutung |
|---|---|
| 200 mit dem Inhalt | Der Schlüssel funktioniert, und der Eigentümer darf den Knoten lesen |
| 401 `invalid_token` | Kein Schlüssel, ein falscher oder widerrufener Schlüssel, ein deaktivierter Eigentümer oder der Schlüssel wurde in der Adresse statt im Header gesendet |
| 401 `expired_token` | Der Schlüssel hat sein Enddatum überschritten |
| 403 `access_denied` | Der Eigentümer darf diesen Inhalt nicht lesen |
| 403 `insufficient_scope` | Die Route braucht einen Geltungsbereich, der dem Schlüssel fehlt, zum Beispiel für das Anlegen |
| 429 `rate_limited` | Zu viele Anfragen in dieser Minute; warten Sie auf `Retry-After` |

Testen Sie auch, dass ein Schreibzugriff abgelehnt wird: Ein `POST` an `/api/ezp/v2/content/node/create` mit diesem Schlüssel muss
mit 403 `insufficient_scope` antworten.

## Schritt 5: ihn an den Agenten übergeben

- Legen Sie den Schlüssel in den Geheimnisspeicher der Agentenplattform oder in eine Umgebungsvariable.
- Lassen Sie Ihren Code den Header hinzufügen; das Modell sieht den Schlüssel nie ([Agenten und die REST-API](agenten-und-die-rest-api.md)).
- Tragen Sie Name, Eigentümer, Geltungsbereich und Enddatum des Schlüssels in Ihre Liste der Zugangsdaten von Agenten ein.

## Schritt 6: erneuern und widerrufen

- **Erneuern** Sie vor dem Enddatum: Erstellen Sie einen zweiten Schlüssel mit demselben Geltungsbereich, stellen Sie den Agenten
  um, prüfen Sie, dass sich **Zuletzt benutzt** des neuen Schlüssels füllt, und widerrufen Sie dann den alten Schlüssel.
- **Widerrufen** Sie sofort, wenn jemand anderes den Schlüssel gesehen haben könnte (in einen Chat eingefügt, committet, in einem Log
  ausgegeben, einem Modell gezeigt) oder wenn der Agent außer Dienst geht.
- Administratoren sehen und widerrufen jeden Schlüssel unter **Setup > oAuth-Verwaltung > API-Schlüssel**. Jedes Erstellen, jeder
  Widerruf, jede erste Verwendung und jede Ablehnung steht im Audit-Trail als `access.apikey.*`.

## Verwandte Seiten

- [Zugangsdaten für Agenten](zugangsdaten-fuer-agenten.md)
- [Ein nur lesender Inhaltsbericht](ablauf-nur-lesender-inhaltsbericht.md)
- [OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
