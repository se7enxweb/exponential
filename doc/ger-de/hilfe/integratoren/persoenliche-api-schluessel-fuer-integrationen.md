---
title: Persönliche API-Schlüssel für Integrationen
audience: integrator, developer, admin
category: integration
order: 70
summary: Persönliche API-Schlüssel eines Dienstkontos für Integrationen von Server zu Server über REST verwenden - Schlüssel für eine Rolle einschalten, Scopes, den Schlüssel erstellen und rotieren, Ratenbegrenzung, Audit-Ereignisse und eigene REST-Routen für Schlüssel öffnen.
keywords: API-Schlüssel, persönlicher API-Schlüssel, Dienstkonto, Scopes, Ratenbegrenzung, Rotation, Audit, API key, personal API key, expk, service account, apikey/create, scopes, read, publish, edit, remove, RouteScopes, RouteGuards, rate limit, rotation, audit, access.apikey
type: help
---
# Persönliche API-Schlüssel für Integrationen

Diese Seite richtet sich an Integratoren, die einen eigenen Server (ein ERP, ein PIM, einen CI-Job) mit der REST-API
von Exponential verbinden. Die übliche Antwort für Aufrufe von Maschine zu Maschine ist ein **persönlicher
API-Schlüssel eines Dienstkontos**: ein Schlüssel, der einem eigens für die Integration angelegten Benutzer gehört und
auf die Scopes begrenzt ist, die die Integration braucht. Die vollständige Anleitung für alle Leser ist
[Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md).

## Inhalt

1. [Die Einrichtung in sechs Schritten](#die-einrichtung-in-sechs-schritten)
2. [Scopes und die Routen, die sie öffnen](#scopes-und-die-routen-die-sie-öffnen)
3. [Rotieren ohne Ausfall](#rotieren-ohne-ausfall)
4. [Grenzen und Audit](#grenzen-und-audit)
5. [Eigene REST-Routen für Schlüssel öffnen](#eigene-rest-routen-für-schlüssel-öffnen)
6. [Fehlersuche](#fehlersuche)

## Die Einrichtung in sechs Schritten

1. **Legen Sie das Dienstkonto an**, zum Beispiel `svc-erp-import`, in einer eigenen Benutzergruppe
   ([Dienstkonten und Rollen](dienstkonten-rollen-und-richtlinien.md)).
2. **Legen Sie dafür eine Rolle an** mit genau den Inhaltsrechten, die die Integration braucht: `content/read` auf den
   Teilbäumen, die sie liest, `content/create`, nach Klasse und Teilbaum begrenzt, für das, was sie veröffentlicht,
   `content/remove` nur, wenn sie entfernt.
3. **Fügen Sie die Richtlinie** `apikey/create` zu dieser Rolle hinzu, mit der Einschränkung **Scope** auf die Scopes,
   die der Schlüssel erhalten darf.
4. **Melden Sie sich als Dienstkonto an** (einmal, in einem privaten Browserfenster), öffnen Sie
   **Mein Konto > API-Zugang** (`/apikey/list`), benennen Sie den Schlüssel nach Programm und Umgebung
   ("ERP-Import, Produktion"), haken Sie die Scopes an und wählen Sie eine Laufzeit (standardmäßig 7, 30, 90 oder
   365 Tage).
5. **Kopieren Sie den Schlüssel** von der Seite, die ihn einmal zeigt (`expk_` und 53 weitere Zeichen), in den
   Geheimnisspeicher des aufrufenden Systems. Er wird nie wieder angezeigt; gespeichert wird nur ein
   HMAC-SHA-256-Hash.
6. **Testen Sie ihn**:

   ```bash
   curl -s -o /dev/null -w '%{http_code}\n' -H "Authorization: Bearer $EXP_API_KEY" \
        https://example.com/api/ezp/v2/content/node/2
   ```

   `200` bedeutet, dass der Schlüssel funktioniert und Knoten 2 lesen darf; `403` bedeutet, dass das Konto diesen
   Knoten nicht lesen darf.

## Scopes und die Routen, die sie öffnen

| Scope | Steht für die Richtlinie | Öffnet |
|---|---|---|
| `read` | `content/read` | Jede `GET`- und `HEAD`-Route (`DefaultReadScope=read`) |
| `publish` | `content/create` | `ezp7xRestContentController_CreateContentNode` |
| `edit` | `content/edit` | `ezp7xRestContentController_UpdateContentNode` |
| `remove` | `content/remove` | `ezp7xRestContentController_DeleteContentNode` |

Bei jeder Anfrage laufen drei Prüfungen: Der Schlüssel hat den Scope, der Besitzer hält noch die Richtlinie des
Scopes, und bei schreibenden Routen, die in `RouteGuards[]` stehen, die Rechte des Besitzers am beschriebenen Knoten.
Die Anfrage läuft dann als der Besitzer, sodass auch die eigenen Rechteprüfungen des Controllers greifen. Eine
schreibende Route einer Erweiterung, die nicht in `RouteScopes[]` steht, ist für Schlüssel geschlossen.

## Rotieren ohne Ausfall

1. Erstellen Sie einen zweiten Schlüssel mit denselben Scopes, benannt mit dem Datum ("ERP-Import, Produktion,
   2027-01").
2. Hinterlegen Sie ihn im aufrufenden System und führen Sie einen Aufruf aus.
3. Prüfen Sie, dass sich **Zuletzt benutzt** des neuen Schlüssels auf `/apikey/list` füllt.
4. Widerrufen Sie den alten Schlüssel. Jede Anfrage damit wird ab der nächsten abgelehnt, auf jedem Server.

Planen Sie die Rotation, bevor der Schlüssel abläuft: Tragen Sie das Enddatum beim Erstellen des Schlüssels in den
Betriebskalender ein. Administratoren sehen jeden Schlüssel, mit Filtern für "läuft innerhalb von sieben Tagen ab" und
"aktiv, aber nie benutzt", unter **Setup > oAuth-Verwaltung > API-Schlüssel** (`/oauthadmin/keys`).

## Grenzen und Audit

| Einstellung (`rest.ini [ApiKeySettings]`) | Standard | Bedeutung für eine Integration |
|---|---|---|
| `RateLimitPerMinute` | `120` | Anfragen je Schlüssel und Minute, gezählt in APCu je PHP-FPM-Pool oder Velocity-Server; darüber: `429` mit `Retry-After` |
| `MaxKeysPerUser` | `10` | Gültige Schlüssel je Benutzer |
| `MaxExpiryDays` | `365` | Die längste Laufzeit; `0` erlaubt Schlüssel ohne Enddatum |
| `LastUsedInterval` | `60` | Sekunden zwischen zwei Schreibvorgängen der letzten Verwendung eines Schlüssels |

Ein Massenjob, der mehr als 120 Anfragen pro Minute braucht, ist ein Zeichen, dass er als Cronjob-Teil auf den Server
gehört ([Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md)), nicht, dass die Grenze fallen
sollte.

Der Kanal `access` des Audits zeichnet `access.apikey.create`, `access.apikey.revoke`, das erste `access.apikey.use`
und jede Ablehnung als `access.apikey.use.failed` mit ihrem Grund auf (`unknown`, `revoked`, `expired`, `scope`,
`policy`, `permission`, `rate_limited` ...).

## Eigene REST-Routen für Schlüssel öffnen

Wenn Ihre Erweiterung einen REST-Controller hinzufügt, nennen Sie in `settings/rest.ini.append.php` Ihrer Erweiterung
den Scope, den jede schreibende Route braucht:

```ini
[ApiKeySettings]
RouteScopes[myExtRestController_doImport]=publish
```

`GET`- und `HEAD`-Routen, die nicht aufgeführt sind, brauchen `DefaultReadScope`.

## Fehlersuche

| Symptom | Ursache |
|---|---|
| Kein Link **API-Zugang** im Profil des Dienstkontos | Der Rolle fehlt `apikey/create` |
| `401` `invalid_token` mit einem frischen Schlüssel | Der Schlüssel wurde beim Kopieren abgeschnitten (er hat 58 Zeichen), in der Adresse gesendet oder ohne `Bearer` gesendet |
| `401` nur auf einem Server | Ein Proxy verwirft den Header `Authorization`; reichen Sie ihn durch (`CGIPassAuth On`) |
| `403` `insufficient_scope` | Fehlender Scope, verlorene Richtlinie oder ein Schreibvorgang außerhalb der Rechte des Besitzers; das Audit-Ereignis nennt den Grund |
| `429` | Über der Ratenbegrenzung; beachten Sie `Retry-After` |

## Verwandte Seiten

- [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md), die vollständige Anleitung
- [Die REST-API für Integratoren](rest-api-fuer-integratoren.md)
- [Dienstkonten, Rollen und Richtlinien](dienstkonten-rollen-und-richtlinien.md)
- [Zugangsdaten für eine Integration wählen](zugangsdaten-fuer-eine-integration-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
