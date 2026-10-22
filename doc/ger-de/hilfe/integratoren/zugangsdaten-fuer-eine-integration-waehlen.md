---
title: Zugangsdaten für eine Integration wählen
audience: integrator, developer, admin
category: integration
order: 40
summary: Die Zugangsdaten, die Exponential von anderen Systemen annimmt - OAuth-2.0-Token, persönliche API-Schlüssel, expservices-Token, HTTP-Basic-Authentifizierung, Anmeldung per Sitzung - wofür jedes dient, wie es begrenzt ist und wie Sie es aufbewahren und rotieren.
keywords: Zugangsdaten, Authentifizierung, OAuth, Token, API-Schlüssel, expk, expt, expservices-Token, Basic-Authentifizierung, Bearer, Dienstkonto, Rotation, Widerruf, minimale Rechte, credentials, authentication, API key, basic authentication, service account, rotation, revocation, least privilege
type: help
---
# Zugangsdaten für eine Integration wählen

Diese Seite ist für Integrationsentwickler und Administratoren, die entscheiden müssen, wie ein anderes System
Exponential gegenüber nachweist, wer es ist. Jedes Zugangsdatum in Exponential steht für **einen Benutzer**, und eine
damit gestellte Anfrage kann nie mehr, als die Rollen dieses Benutzers erlauben. Die Wahl betrifft, wer dieser Benutzer
ist, wie lange das Zugangsdatum gilt und wie es widerrufen wird.

## Inhalt

1. [Die Zugangsdaten](#die-zugangsdaten)
2. [Welches für welche Aufgabe](#welches-für-welche-aufgabe)
3. [Grenzen, die immer gelten](#grenzen-die-immer-gelten)
4. [Zugangsdaten aufbewahren und rotieren](#zugangsdaten-aufbewahren-und-rotieren)
5. [Checkliste](#checkliste)

## Die Zugangsdaten

| Zugangsdatum | Sieht aus wie | Gesendet als | Funktioniert für | Erzeugt von | Endet |
|---|---|---|---|---|---|
| OAuth-2.0-Zugriffstoken | 40 hexadezimale Zeichen | `Authorization: Bearer <token>` oder `Authorization: OAuth <token>` | REST-API (`/api/...`) | Einer App, nachdem der Benutzer sie unter `oauth/authorize` zugelassen hat; Token von `POST /api/auth/v1/oauth/token` | Nach `rest.ini [OAuthSettings] TokenTTL` (3600 Sekunden); mit dem Refresh-Token erneuert |
| Persönlicher API-Schlüssel | `expk_` + 12 + `_` + 40 Zeichen | Nur `Authorization: Bearer expk_...` (in der Adresse abgelehnt) | REST-API | Dem Benutzer unter **Mein Konto > API-Zugang** (`/apikey/list`), mit `apikey/create` | Widerrufen, abgelaufen (`MaxExpiryDays`, Standard 365) oder Eigentümer deaktiviert |
| `expservices`-Token | `expt_` + 48 hexadezimale Zeichen | `Authorization: Bearer <token>` oder `X-Exp-Token: <token>` | Dienste unter `/ezjscore/call/exp...` | Dem Benutzer mit `expsession::tokenCreate` | Widerrufen (`expsession::tokenRevoke`), abgelaufen oder Eigentümer deaktiviert |
| HTTP-Basic-Authentifizierung | Login und Passwort | `Authorization: Basic ...` | REST-API, wenn `rest.ini [Authentication] AuthenticationStyle=ezpRestBasicAuthStyle`; WebDAV | Dem Benutzerkonto | Wenn sich das Passwort ändert |
| Anmeldung per Sitzung | Sitzungscookie plus Formular-Token | Cookie und `ezxform_token` | `expservices`- und ezjscore-Aufrufe aus einem Browser oder einem Skript, das sich anmeldet | `user/login` | Ende der Sitzung |

Die REST-Schicht löst einen persönlichen API-Schlüssel in `expApiKeyAuthFilter`
(`kernel/private/rest/classes/auth/apikey_auth_filter.php`) für beide Authentifizierungsarten des Kernels auf;
Schlüssel funktionieren also, ob die Website OAuth oder Basic-Authentifizierung verwendet.

## Welches für welche Aufgabe

| Aufgabe | Verwenden Sie | Warum |
|---|---|---|
| Ein Skript oder Server Ihres Unternehmens, der über REST Inhalte liest oder veröffentlicht | Einen **persönlichen API-Schlüssel** eines Dienstkontos | Ein Schlüssel pro Programm, mit Geltungsbereich (`read`, `publish`, `edit`, `remove`), einzeln widerrufbar, protokolliert |
| Eine App, bei der sich viele Menschen anmelden und als sie selbst handeln | Eine **OAuth-Anwendung** | Jeder Benutzer lässt die App zu; das Token handelt als dieser Benutzer |
| Ein Skript, das `expservices` aufruft (Caches, Inhaltsjobs, Benutzer, ...) | Ein **`expservices`-Token** eines Dienstkontos | Für Schreibzugriffe ist kein Formular-Token nötig; widerrufbar |
| Eine Browserseite auf derselben Website | Die **Sitzung** | Sie ist bereits da; Schreibzugriffe tragen den Formular-Token |
| Ein Desktop-Werkzeug, das Inhalte als Laufwerk einbindet | **Basic-Authentifizierung** über HTTPS (WebDAV) | WebDAV-Clients sprechen sie |
| Ein älterer Client, der nur Basic-Authentifizierung kennt | **Basic-Authentifizierung** über HTTPS, mit einem eigenen Konto | Letzter Ausweg: Das Passwort öffnet jede Seite, die das Konto öffnen darf |

> **Hinweis**
> Ein Passwort ist das weitreichendste Zugangsdatum: Es meldet überall an, wo das Konto hindarf, und eine Änderung
> bricht jedes Programm auf einmal. Bevorzugen Sie einen Schlüssel oder ein Token, wann immer die Schnittstelle es
> annimmt ([Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md#1-was-ein-schlüssel-ist-und-was-nicht)).

## Grenzen, die immer gelten

- **Nie mehr als der Benutzer.** Jeder REST-Lese- und Schreibzugriff fragt über `expRestContentPermission` die
  Richtlinien des Benutzers ab; jeder `expservices`-Dienst deklariert seinen Zugriff und prüft ihn. Der Geltungsbereich
  eines Schlüssels wird bei jeder Anfrage erneut gegen die aktuellen Richtlinien des Eigentümers geprüft.
- **Ratenlimit für Schlüssel.** `rest.ini [ApiKeySettings] RateLimitPerMinute=120` pro Schlüssel, in APCu gezählt;
  über dem Limit lautet die Antwort `429` mit `Retry-After`.
- **Schlüssel nur im Header.** Ein Schlüssel in `oauth_token`, `access_token` oder `api_key` der Adresse oder des
  Inhalts wird ohne Nachschlagen abgelehnt.
- **HTTPS.** `rest.ini [Authentication] RequireHTTPS` kann einfaches HTTP für die REST-API ablehnen; liefern Sie jede
  API trotzdem über HTTPS aus.
- **Proxys.** Wenn ein Proxy oder Apache mit PHP-FPM den Header `Authorization` verwirft, scheitern Schlüssel und Token
  mit 401. Reichen Sie den Header durch (`CGIPassAuth On`) oder verwenden Sie für `expservices` `X-Exp-Token`.

## Zugangsdaten aufbewahren und rotieren

| Tun Sie | Tun Sie nicht |
|---|---|
| Schlüssel und Token im Geheimnisspeicher oder in Umgebungsvariablen des aufrufenden Systems halten | Sie in Git, in ein Template, in eine URL oder in ein gemeinsam genutztes Skript legen |
| Ein Zugangsdatum pro Programm und pro Umgebung anlegen | Einen Schlüssel zwischen Test und Produktion teilen |
| Rotieren, indem Sie zuerst den neuen Schlüssel anlegen, umstellen, **Zuletzt benutzt** prüfen und dann den alten widerrufen | Zuerst widerrufen und das Programm danach reparieren |
| Ihrem Secret-Scanner die Präfixe `expk_` und `expt_` beibringen | Schlüssel in Tickets oder Chats einfügen |
| Sofort widerrufen, wenn ein Zugangsdatum gesehen worden sein könnte | Warten, bis es abläuft |

Jedes Anlegen, Widerrufen, die erste Nutzung und jede Ablehnung eines Schlüssels wird im Audit-Protokoll als
`access.apikey.*` festgehalten; `expservices`-Token als `access.expservices.token.*`:

```bash
php bin/php/console exp:audit tail --channel=access --name='access.apikey.*' --lines=20 --allow-root-user
```

## Checkliste

- [ ] Ein Dienstkonto pro Integration, mit einer engen Rolle ([wie](dienstkonten-rollen-und-richtlinien.md)).
- [ ] Die Art des Zugangsdatums pro Datenfluss gewählt und in die Schnittstellenspezifikation geschrieben.
- [ ] Geltungsbereiche auf das beschränkt, was der Fluss braucht (eine lesende Integration bekommt nur `read`).
- [ ] Ablaufdatum gesetzt und ein Rotationstermin im Betriebskalender.
- [ ] Das Zugangsdatum außerhalb von Git aufbewahrt; sein Ort im Runbook genannt.
- [ ] Die Audit-Ereignisse nach dem ersten Lauf geprüft.

## Verwandte Seiten

- [Persönliche API-Schlüssel für Integrationen](persoenliche-api-schluessel-fuer-integrationen.md)
- [OAuth-Anwendungen für Integrationen](oauth-anwendungen-fuer-integrationen.md)
- [Entfernte Dienste mit expservices](entfernte-dienste-mit-expservices.md)
- [OAuth und API-Schlüssel für App-Ersteller](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
