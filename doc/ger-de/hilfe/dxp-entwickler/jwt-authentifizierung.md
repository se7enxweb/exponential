---
title: JWT-Authentifizierung
audience: dxp-developer
category: dxp-development
order: 26
summary: Token-Authentifizierung für die REST- und GraphQL-APIs der DXP mit lexik/jwt-authentication-bundle - Erzeugen und Wechseln des Schlüsselpaars, die Umgebungsvariablen, die Firewalls, die das Rezept auskommentiert schreibt, das Holen eines Tokens und der sichere Umgang mit den Schlüsseln.
keywords: JWT, JSON Web Token, lexik/jwt-authentication-bundle, Schlüsselpaar, lexik:jwt:generate-keypair, JWT_PASSPHRASE, Firewall, security.yaml, Token, REST, GraphQL, Schlüsselwechsel, keypair, token, rotation
type: help
---
# JWT-Authentifizierung

Diese Seite richtet sich an Entwickler, die Apps und Dienste sich mit Tokens an den APIs der DXP anmelden lassen. Die
DXP verlangt `lexik/jwt-authentication-bundle`; die Anleitung des Skeletons (Abschnitt 10) sagt, das Schlüsselpaar sei
"required for the REST API to function", und es wird bei jeder Installation erzeugt.

## Inhalt

1. [Das Schlüsselpaar](#das-schlüsselpaar)
2. [Die JWT-Firewalls einschalten](#die-jwt-firewalls-einschalten)
3. [Ein Token holen und verwenden](#ein-token-holen-und-verwenden)
4. [Die Schlüssel wechseln](#die-schlüssel-wechseln)
5. [Sicher halten](#sicher-halten)

## Das Schlüsselpaar

```bash
php bin/console lexik:jwt:generate-keypair
# writes config/jwt/private.pem and config/jwt/public.pem
```

Die Pfade und die Passphrase kommen aus `.env.local`:

```bash
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<64 random hex characters>
```

Git ignoriert die Schlüsseldateien. Sichern Sie sie sicher: Gehen sie verloren, ist jedes Token ungültig.

## Die JWT-Firewalls einschalten

In einem installierten Projekt auf Platform v5 enthält die `config/packages/security.yaml` des Rezepts drei
JWT-Firewalls, auskommentiert, unter der Zeile "Uncomment the rules below to enable JSON Web Token (JWT) authentication
for REST and/or GraphQL":

| Firewall | Zweck |
|---|---|
| `ibexa_jwt_rest` | stellt Tokens aus: eine JSON-Anmeldung auf der Route `ibexa.rest.create_token` |
| `ibexa_jwt_rest.api` | nimmt Tokens bei REST-Anfragen an, die einen Header `Authorization` tragen |
| `ibexa_jwt_graphql` | nimmt Tokens bei GraphQL-Anfragen von außerhalb der Administration an |

Kommentieren Sie die benötigten ein, behalten Sie ihre Reihenfolge vor den allgemeinen Firewalls bei und leeren Sie den
Cache. Prüfen Sie die Datei Ihres eigenen Projekts: Hat das Rezept sie anders geschrieben, folgen Sie Ihrer Datei.

## Ein Token holen und verwenden

Die Token-Route `ibexa.rest.create_token` ist `/user/token/jwt` unter dem REST-Präfix
([Die REST-API](die-rest-api.md#wo-sie-liegt)). Die Anmeldefelder sind `JWTInput.username` und `JWTInput.password`:

```bash
BASE="https://127.0.0.1:8000/api/ibexa/v2"       # your prefix
curl -s -X POST "$BASE/user/token/jwt" \
  -H "Content-Type: application/vnd.ibexa.api.JWTInput+json" \
  -H "Accept: application/vnd.ibexa.api.JWT+json" \
  -d '{"JWTInput": {"username": "api-reader", "password": "..."}}'

curl -s "$BASE/content/locations/1/2" \
  -H "Accept: application/vnd.ibexa.api.Location+json" \
  -H "Authorization: Bearer <token>"
```

Legen Sie für jeden Client einen eigenen Benutzer an, mit einer Rolle, die nur erlaubt, was der Client braucht.
Verwenden Sie nie das Administratorkonto für einen API-Client.

## Die Schlüssel wechseln

```bash
php bin/console lexik:jwt:generate-keypair --overwrite
php bin/console cache:clear
```

Vorhandene Tokens funktionieren nicht mehr; Clients melden sich erneut an. Planen Sie den Wechsel wie eine
Bereitstellung.

## Sicher halten

- Nur HTTPS; ein Token ist für seine Lebensdauer ein Passwort.
- `JWT_PASSPHRASE` und die Schlüsseldateien gehören nie in Git, Tickets oder Chats.
- Kurze Lebensdauern für Tokens von Clients, die auf Geräten von Benutzern laufen.
- `JWT authentication errors` nach einer Bereitstellung bedeuten meist fehlende oder nicht lesbare Schlüsseldateien
  ([Fehlersuche](fehlersuche-in-der-dxp.md)).

## Verwandte Seiten

- [Die REST-API](die-rest-api.md)
- [GraphQL und GraphiQL](graphql-und-graphiql.md)
- [Umgebungsvariablen](umgebungsvariablen.md#jwt)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
