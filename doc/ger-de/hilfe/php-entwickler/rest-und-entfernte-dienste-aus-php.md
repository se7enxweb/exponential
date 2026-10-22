---
title: REST und entfernte Dienste aus PHP
audience: php-developer, developer
category: php-development
order: 320
summary: Die HTTP-APIs von Exponential von der PHP-Seite - der REST-Kernel hinter index_rest.php, rest.ini, Provider, die ezpRestProviderInterface mit Routen und ezpRestMvcController-Controllern implementieren, Authentifizierung mit OAuth2 und API-Schlüsseln, Berechtigungsprüfungen in jeder Aktion und wie sich REST, ezjscore und expservices unterscheiden.
keywords: REST, Provider, Controller, Authentifizierung, Autorisierung, API-Schlüssel, entfernte Dienste, index_rest.php, rest.ini, ezpKernelRest, ezpRestProviderInterface, getRoutes, getViewController, ezpRestMvcController, ProviderClass, ApiProvider, route filter, OAuth2, API keys, expRestContentPermission, ezprestapi, expservices, ezjscore
type: help
---
# REST und entfernte Dienste aus PHP

Diese Seite richtet sich an PHP-Entwickler, die anderen Programmen Daten bereitstellen: mobilen Apps, anderen Frontends,
Integrationen. Exponential hat dafür drei HTTP-Schichten. Diese Seite erklärt, wie sie in PHP gebaut sind und wie Sie
der REST-Schicht etwas hinzufügen; die Sicht der Verbraucher ist
[REST-API v1 und v2 für Anwendungen](../ersteller/anwendungsentwickler/rest-api.md).

## Inhalt

1. [Drei Schichten](#drei-schichten)
2. [Der REST-Kernel](#der-rest-kernel)
3. [Einen Provider hinzufügen](#einen-provider-hinzufügen)
4. [Controller](#controller)
5. [Authentifizierung](#authentifizierung)
6. [Autorisierung ist Ihre Aufgabe](#autorisierung-ist-ihre-aufgabe)
7. [Eine Schicht wählen](#eine-schicht-wählen)

## Drei Schichten

| Schicht | Einstieg | Gebaut für |
|---|---|---|
| REST | `index_rest.php`, unter `/api/` (`rest.ini [System] ApiPrefix=/api`) | Inhalte und Ressourcen für andere Programme, OAuth2 für Dritte |
| expservices | `ezjscore/call/exp<domain>::<function>` | Administrationsfunktionen aus Skripten und Apps, mit Auffindbarkeit und API-Tokens ([Backend-Dienste über ezjscore](../../bc/6.0/backend-dienste-ueber-ezjscore.md)) |
| ezjscore-Serverfunktionen | `ezjscore/call/<name>::<function>` | das JavaScript Ihrer eigenen Seiten ([ezjscore-JSON-Endpunkte in PHP](ezjscore-json-endpunkte-in-php.md)) |

## Der REST-Kernel

`index_rest.php` führt den REST-Kernel in `kernel/private/rest/` aus (`ezpkernelrest.php`, der Router, die
MVC-Konfiguration, Renderer, OAuth). Er baut auf den MVC-Werkzeugen der Zeta Components (`zetacomponents/mvc-tools`)
auf, sodass ein Provider Routen und Controller in der Form dieses Frameworks liefert. Die Einstellungen stehen in
`settings/rest.ini`:

| Block | Enthält |
|---|---|
| `[System]` | `ApiPrefix`, die Klasse des Präfixfilters |
| `[ApiProvider]` | `ProviderClass[<prefix>]=<class>`, ein Eintrag pro Provider (`auth` ist `ezpRestAuthProvider`) |
| `[RouteSettings]` | den Routenfilter (`RouteSettingImpl`) und Routen, die die Authentifizierung überspringen |
| `[Authentication]` | ob Anfragen sich authentifizieren müssen (standardmäßig `RequireAuthentication=enabled`) |
| `[OAuthSettings]`, `[ApiKeySettings]` | Lebensdauer der Tokens und API-Schlüssel |

Die Content-API (`/api/ezp/v1/...`, `/api/ezp/v2/...`) kommt aus der Erweiterung `ezprestapi`, deren Provider auf
dieselbe Weise registriert ist.

## Einen Provider hinzufügen

Ein Provider implementiert `ezpRestProviderInterface` (`kernel/private/rest/classes/interfaces/rest_provider.php`):

| Methode | Gibt zurück |
|---|---|
| `getRoutes()` | die Routen des Providers, die URL-Muster und HTTP-Methoden Controller-Aktionen zuordnen |
| `getViewController()` | den View-Controller, der einen Renderer für das Ergebnis wählt |

Registrieren Sie ihn für ein Präfix in der `rest.ini.append.php` Ihrer Erweiterung:

```ini
<?php /* #?ini charset="utf-8"?
[ApiProvider]
ProviderClass[acme]=acmeRestProvider
*/ ?>
```

**Setup > RAD** hat ein Werkzeug, das das Grundgerüst schreibt (`/setup/handlerextension/restprovider`); Routenfilter
sind ein eigener Erweiterungspunkt (`[RouteSettings] RouteSettingImpl`)
([Erweiterungspunkte: REST-Provider](../../bc/6.0/rad-erweiterungspunkte.md#rest-provider)). Lesen Sie die mitgelieferten
Provider in `kernel/private/rest/` und in `ezprestapi`, bevor Sie Ihren eigenen schreiben: Ihre Routentabellen sind die
beste Referenz für das genaue Format.

## Controller

Controller erweitern `ezpRestMvcController` (`kernel/private/rest/classes/controllers/rest_controller.php`), das
`ezcMvcController` der Zeta Components erweitert. Jede Aktion liest ihre Routenvariablen und die Anfrage, erledigt die
Arbeit über Ihre Dienstklassen und gibt ein Ergebnis zurück, das der View-Controller als JSON rendert. Halten Sie
Aktionen schlank, wie bei Modul-Views: Derselbe Dienst sollte aus einem Befehl und einem Test aufrufbar sein.

## Authentifizierung

| Berechtigungsnachweis | Für |
|---|---|
| OAuth2-Access-Token (`POST /api/auth/v1/oauth/token`, Grants `authorization_code` und `refresh_token`) | eine Anwendung, die für ihre Benutzer handelt; Clients werden unter **Setup > oAuth-Verwaltung** registriert |
| persönlicher API-Schlüssel (`Authorization: Bearer expk_...`) | das eigene Skript eines Benutzers ([Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)) |

Hinter Apache mit FastCGI stellen Sie sicher, dass der Header `Authorization` PHP erreicht
([OAuth und API-Schlüssel](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)).

## Autorisierung ist Ihre Aufgabe

Das REST-Framework **authentifiziert**: Es sagt Ihnen, wer der Benutzer ist. Es entscheidet nicht, was der Benutzer tun
darf. Jede Controller-Aktion muss Richtlinien für das prüfen, was sie liest oder schreibt. Die Content-API tut das über
`expRestContentPermission` des Kernels (`content/read`, `content/create`, `content/edit`, `content/remove`) und antwortet
ohne gültige Berechtigungsnachweise mit `401`, mit `403`, wenn der Benutzer es nicht darf, mit `404` bei einem
unbekannten Knoten. Tun Sie in Ihren Aktionen dasselbe, mit `eZUser::currentUser()->hasAccessTo()` und
`canRead()`/`canEdit()` bei Inhalten.

## Eine Schicht wählen

| Bedarf | Schicht |
|---|---|
| ein Dritter integriert sich mit Ihren Inhalten, mit OAuth-Zustimmung | REST |
| eine mobile App liest und erstellt Inhalte | REST (Content-API) |
| ein Skript automatisiert Administrationsaufgaben | expservices mit einem API-Token |
| das JavaScript Ihrer Seite braucht eine kleine Antwort | ezjscore-Serverfunktion |
| eine andere Exponential-Installation synchronisiert Inhalte | die Erweiterung `syndication`, falls installiert |

## Verwandte Seiten

- [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)
- [Entfernte Dienste mit expservices](../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md)
- [Exponential headless](../ersteller/anwendungsentwickler/headless-exponential.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
