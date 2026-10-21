# REST API v1 und v2: eine Extension

Diese Anleitung erklärt, wie Exponential 6.0.15 seine REST-Inhalts-API ausliefert: Version 1 und Version 2 stammen aus **einer**
Extension, `ezprestapi` (1.2.6 oder neuer). Sie zeigt, welche URL in welcher Version antwortet, wie sich ein Client authentifiziert,
wie Sie die API mit curl aufrufen, welche Einstellungen sie steuern und wie Sie eine Installation von der veralteten
Extension `ezprestapiprovider` lösen.

Sie ist für zwei Lesergruppen geschrieben. **Entwickler**, die Clients schreiben oder die API erweitern, lesen die Abschnitte 1 bis 5.
**Site-Administratoren**, die eine Installation einrichten, aktualisieren oder prüfen, lesen die Abschnitte 5 bis 8.

Jede Aussage unten wurde am 15. Oktober 2026 auf dem Demonstrationsserver (Apache mit PHP-FPM sowie Velocity) geprüft.
Hostnamen, Kennungen und Geheimnisse sind durch Dokumentationswerte ersetzt (`example.com`, `<client id>`, `expk_...`).

[Anleitungen](README.md) · Siehe auch: [API-Schlüssel](api-schluessel.md) ·
[ezprestapi](../../eng-us/features/6.0/extensions/ezprestapi.md) · [ezprestapiprovider](../../eng-us/features/6.0/extensions/ezprestapiprovider.md) (veraltet) ·
[Remote-Dienste und Apps](entfernte-dienste-und-apps.md)

## Kurz gesagt

- **Eine Extension, eine Aktivierung:** `ActiveExtensions[]=ezprestapi`. Sie registriert eine Provider-Klasse,
  `ezp7xRestApiProvider`, für die Präfixe `ezp` und `ezpl`, mit einer Routentabelle und einem Controller,
  `ezp7xRestContentController`.
- **Lesezugriffe antworten in v1 und v2** (`/api/ezp/v1/...`, `/api/ezp/v2/...`); **Schreibzugriffe** (Anlegen, Ändern, Löschen) antworten
  **nur in v2**.
- **Jeder Lese- und Schreibzugriff prüft die Richtlinien des Benutzers** (`content/read`, `content/create`, `content/edit`,
  `content/remove`), gleich womit die Anfrage authentifiziert wurde. Die Prüfung übernimmt `expRestContentPermission` aus dem Kernel,
  sie erfordert Exponential 6.0.15.
- **Authentifizierung:** ein OAuth2-Token von `POST /api/auth/v1/oauth/token` (Grants `authorization_code` und
  `refresh_token`) oder ein [persönlicher API-Schlüssel](api-schluessel.md), gesendet als `Authorization: Bearer expk_...`.
- **`ezprestapiprovider` ist veraltet.** Seine letzte Release, 6.0.4, ist eine Kompatibilitätsschicht über `ezprestapi`.
  Aktivieren Sie `ezprestapi` und entfernen Sie `ezprestapiprovider` aus `ActiveExtensions` ([Abschnitt 6](#6-migration-von-ezprestapiprovider)).
- Für Clients ändert sich nichts: Die Antworten von v1 und v2 sind Byte für Byte dieselben wie zuvor, nur dass
  `OutputFormat=xhtml` `renderedOutput` wieder füllt.

## Inhalt

1. [Was liefert was aus](#1-was-liefert-was-aus)
2. [Die Routen](#2-die-routen)
3. [Authentifizierung](#3-authentifizierung)
4. [Beispiele mit curl](#4-beispiele-mit-curl)
5. [Konfiguration](#5-konfiguration)
6. [Migration von ezprestapiprovider](#6-migration-von-ezprestapiprovider)
7. [Aktivierungskombinationen, vorher und seitdem](#7-aktivierungskombinationen-vorher-und-seitdem)
8. [Bekanntes Problem: listAtom](#8-bekanntes-problem-listatom)

---

## 1. Was liefert was aus

| Teil | Wo er liegt | Was er tut |
|---|---|---|
| REST-Framework | der Kernel (`kernel/private/rest/`), `settings/rest.ini` | Präfixfilter, Routing, Versionen, Authentifizierung, Antwort-Cache, Ausgabe-Renderer |
| Auth-Provider `ezpRestAuthProvider` | der Kernel, Präfix `auth` | der OAuth2-Token-Endpunkt und die Authentifizierungsrouten |
| Inhalts-Provider `ezp7xRestApiProvider` | `ezprestapi`, Präfixe `ezp` und `ezpl` | die Inhaltsrouten von v1 und v2 |
| Inhalts-Controller `ezp7xRestContentController` | `ezprestapi` | jeder Lese- und Schreibzugriff auf Inhalte, mit den Berechtigungsprüfungen |
| Backend für Anlegen und Löschen | [nxc_powercontent](../../eng-us/features/6.0/extensions/nxc_powercontent.md) | legt Inhalte für den Controller an und entfernt sie |
| Berechtigungshelfer `expRestContentPermission` | der Kernel (6.0.15) | fragt die Richtlinien des Benutzers so ab, wie es das Modul content tut |
| `ezprestapiprovider` 6.0.4 | veraltete Kompatibilitätsschicht | `ezpRestApiProvider` und `ezpRestContentController` erweitern die Klassen von `ezprestapi`; ordnet `ezp` und `ezpl` `ezp7xRestApiProvider` zu |

`ezpl` ist ein Alias-Präfix von `ezp`: dieselben Routen, dieselben Antworten.

## 2. Die Routen

Alle Pfade unten sind relativ zu `/api/ezp/<version>` (oder `/api/ezpl/<version>`).

| Methode | Pfad | v1 | v2 | Was sie tut |
|---|---|---|---|---|
| `GET` | `/content/node/<id>` | ja | ja | ein Knoten (`ResponseGroups=Metadata,Locations,Fields`, `OutputFormat=xhtml`) |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | ja | ja | die Kinder |
| `GET` | `/content/node/<id>/childrenCount` | ja | ja | die Anzahl der Kinder |
| `GET` | `/content/node/<id>/fields` | ja | ja | jedes Feld des Objekts des Knotens |
| `GET` | `/content/node/<id>/field/<identifier>` | ja | ja | ein Feld |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | ja | ja | dasselbe über die Objekt-ID |
| `GET` | `/content/node/<id>/listAtom` | ja | ja | Atom-Feed der Kinder; antwortet mit 500, siehe [Abschnitt 8](#8-bekanntes-problem-listatom) |
| `POST` | `/content/node/create` | nein | ja | unterhalb von `parentNodeID` anlegen und veröffentlichen |
| `POST` | `/content/node/<id>` | nein | ja | ändern: prüft `content/edit` und antwortet dann mit 501 (noch nicht implementiert) |
| `DELETE` oder `POST` | `/content/node/delete/<id>` | nein | ja | das Objekt des Knotens mit allen seinen Orten und allem darunter entfernen |

Ein an v1 gesendeter Schreibzugriff existiert dort nicht und wird abgewiesen. Die Links innerhalb einer v1-Antwort bleiben in v1, sodass bestehende Clients
ihnen weiterhin folgen.

Die Antworten und die Richtlinien, die jeder Aufruf prüft, sind beschrieben in
[API-Schlüssel, Abschnitt 3](api-schluessel.md#3-einen-schlüssel-mit-curl-benutzen) und
[Berechtigungen der REST-Schnittstelle](api-schluessel.md#32-berechtigungen-der-rest-schnittstelle). Kurz: `200`, `201` für ein
Anlegen, `400` für ein fehlendes oder unbekanntes Feld, `401` ohne gültiges Token oder gültigen Schlüssel, `403 access_denied`, wenn der Benutzer
es nicht darf, `404` für einen unbekannten Knoten.

## 3. Authentifizierung

`rest.ini [Authentication] RequireAuthentication=enabled` ist der Standard: Jede Inhaltsroute benötigt einen Benutzer. Zwei
Arten von Zugangsdaten werden akzeptiert.

### 3.1 OAuth2-Tokens

Für eine Anwendung, bei der sich *andere* Benutzer anmelden und für die sie in deren Namen handelt.

1. Ein Administrator registriert die Anwendung unter **Setup > oAuth admin** (`/oauthadmin/list`). Sie erhält eine Client-Kennung,
   ein Geheimnis und die Weiterleitungsadresse der Anwendung.
2. Die Anwendung schickt den Benutzer auf die Autorisierungsseite der Site:
   `https://example.com/oauth/authorize?client_id=<client id>&redirect_uri=<redirect uri>&response_type=code`.
   Der Benutzer meldet sich an und erlaubt der Anwendung den Zugriff; die Site leitet mit `code=<code>` zurück. Der Benutzer benötigt die
   Richtlinie *OAuth / Authorize* (`oauth/authorize`); ohne sie leitet die Site mit `error=access_denied` zurück
   ([Upgrade-Hinweis](../../eng-us/bc/6.0/oauth-authorize-policy.md)).
3. Die Anwendung tauscht den Code am Token-Endpunkt gegen ein Token,
   `POST /api/auth/v1/oauth/token`, mit `grant_type=authorization_code`, `client_id`, `client_secret`, `code` und
   `redirect_uri`.
4. Sie sendet das Token mit jeder Anfrage (`Authorization: OAuth <token>` oder den Parameter `oauth_token`).
5. Wenn das Token abgelaufen ist (`rest.ini [OAuthSettings] TokenTTL`, standardmäßig eine Stunde), fordert die Anwendung mit
   `grant_type=refresh_token` und `refresh_token=<refresh token>` ein neues an.

Der Token-Endpunkt unterstützt die Grants `authorization_code` und `refresh_token`.

### 3.2 Persönliche API-Schlüssel

Für ein eigenes Skript des Benutzers. Der Benutzer erstellt auf der Site einen Schlüssel (**Mein Konto > API-Zugang**) und sendet ihn als
`Authorization: Bearer expk_...`. Die Anfrage läuft als Eigentümer des Schlüssels, beschränkt auf die Scopes des Schlüssels und nie über
die Richtlinien des Eigentümers hinaus. Die ganze Geschichte steht unter [API-Schlüssel](api-schluessel.md).

## 4. Beispiele mit curl

```bash
export EXP_API_KEY='expk_...'     # a personal API key; an OAuth token works the same way with "OAuth <token>"

# one node, v1 (what existing clients call)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2

# the first 20 children with their fields, v1 (the call of the mobile app)
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Fields"

# the same node at v2, with the rendered XHTML of its view
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/2?OutputFormat=xhtml"

# one field by object id
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/object/<object id>/field/title

# create a folder below a node (v2 only)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
# {"message":"Created","objectId":<object id>,"nodeId":<node id>}

# remove it again (v2 only)
curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/delete/<node id>
# {"message":"Removed","nodeId":<node id>,"objectId":<object id>}
```

Einen Autorisierungscode gegen ein OAuth-Token tauschen:

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=authorization_code -d client_id='<client id>' -d client_secret='<client secret>' \
     -d code='<code>' -d redirect_uri='<redirect uri>'
```

Bewahren Sie Schlüssel, Geheimnisse und Tokens in Umgebungsvariablen oder einem Geheimnisspeicher auf, nie in einem Skript, einem Repository oder im Shell-Verlauf
auf einer gemeinsam genutzten Maschine.

## 5. Konfiguration

`settings/rest.ini` des Kernels enthält die Standardwerte; `ezprestapi` fügt seinen Provider und seinen Ausgabeabschnitt in
`extension/ezprestapi/settings/rest.ini.append.php` hinzu. Überschreiben Sie sie in `settings/override/rest.ini.append.php`.

```ini
[System]
# the path below which the REST interface answers
ApiPrefix=/api

[ApiProvider]
# set by ezprestapi (and, identically, by the ezprestapiprovider 6.0.4 shim)
ProviderClass[ezp]=ezp7xRestApiProvider
ProviderClass[ezpl]=ezp7xRestApiProvider

[Authentication]
RequireAuthentication=enabled
AuthenticationStyle=ezpRestOauthAuthenticationStyle
#AuthenticationStyle=ezpRestBasicAuthStyle
RequireHTTPS=disabled
DefaultUserID=

[OAuthSettings]
TokenTTL=3600

# the template of OutputFormat=xhtml for the controller of ezprestapi (ezprestapi 1.2.6, kernel 6.0.15)
[ezp7xRestContentController_viewContent_OutputSettings]
Template=rest_pagelayout.tpl
```

| Einstellung | Bedeutung |
|---|---|
| `[System] ApiPrefix` | der URL-Pfad, unterhalb dessen die REST-Schnittstelle antwortet (`/api`) |
| `[ApiProvider] ProviderClass[<prefix>]` | die Provider-Klasse eines Präfixes: `/api/<prefix>/<version>/...`. `ezp` und `ezpl` sind `ezp7xRestApiProvider`; `auth` ist `ezpRestAuthProvider` aus dem Kernel |
| `[Authentication] RequireAuthentication` | `disabled` lässt jede Anfrage als `DefaultUserID` (oder als anonymer Benutzer) laufen; die Berechtigungsprüfungen gelten weiterhin |
| `[Authentication] AuthenticationStyle` | OAuth (Standard) oder HTTP-Basic-Authentifizierung; persönliche API-Schlüssel werden von beiden akzeptiert |
| `[Authentication] RequireHTTPS` | einfaches HTTP abweisen |
| `[<Controller>_<action>_OutputSettings] Template` | das Pagelayout-Template des XHTML-Renderers für diese Controller-Aktion |
| `[ApiKeySettings]` | die persönlichen API-Schlüssel, siehe [API-Schlüssel, Abschnitt 13](api-schluessel.md#13-jede-einstellung) |

Der Ausgabeabschnitt ist nach dem Controller benannt. Bis `ezprestapi` 1.2.5 gab es nur
`[ezpRestContentController_viewContent_OutputSettings]`, den Namen des alten v1-Controllers, sodass ein Lesezugriff mit
`OutputFormat=xhtml` über `ezp7xRestContentController` kein Template fand und mit einem leeren `renderedOutput` antwortete.
1.2.6 und der Kernel von 6.0.15 fügen den Abschnitt für `ezp7xRestContentController` hinzu.

Leeren Sie nach einer Änderung an `rest.ini` den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`),
laden Sie PHP-FPM neu und starten Sie Velocity neu.

## 6. Migration von ezprestapiprovider

`ezprestapiprovider` 6.0.4 (15. Oktober 2026) ist seine letzte Release. Sie hält alte Installationen als Kompatibilitätsschicht am Laufen:
`ezpRestApiProvider` erweitert `ezp7xRestApiProvider`, `ezpRestContentController` erweitert
`ezp7xRestContentController`, ihre `rest.ini` ordnet `ezp` und `ezpl` `ezp7xRestApiProvider` zu, und ihr Composer-Paket
erfordert `se7enxweb/ezprestapi ~1.2.6`. Sie liefert die fünf Klassen, die sie doppelt enthielt, nicht mehr aus
(`ezpRestAtomController`, `ezpRestAtomView`, `ezpRestAtomDecorator`, `ezpRestContentModel`,
`ezpRestApiViewController`); `ezprestapi` liefert sie aus.

So stellen Sie eine bestehende Installation um:

1. Aktualisieren Sie die Pakete: `se7enxweb/ezprestapi` auf 1.2.6 oder neuer (und `se7enxweb/ezprestapiprovider` auf 6.0.4, solange
   es noch installiert ist). Die Wurzel-`composer.json` von Exponential 6.0.15 erfordert `se7enxweb/ezprestapi ~1.2.6` und
   erfordert `ezprestapiprovider` nicht mehr.
2. Behalten oder ergänzen Sie in `settings/override/site.ini.append.php` die eine Zeile und entfernen Sie die andere:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ezprestapi
   # remove: ActiveExtensions[]=ezprestapiprovider
   ```

3. Leeren Sie den INI-Cache, erzeugen Sie die Autoloads der Extensions neu und laden Sie die Web-Laufzeitumgebung neu:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   systemctl reload <your PHP-FPM service>
   ./console exp:velocity restart --allow-root-user     # when Velocity serves the site
   ```

4. Prüfen: `curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2` und dasselbe
   unter `/api/ezp/v2/` antworten mit `200`.

Was weiterhin funktioniert:

- Ein `rest.ini`-Override mit `ProviderClass[ezp]=ezpRestApiProvider` funktioniert über die Kompatibilitätsschicht, solange
  `ezprestapiprovider` installiert ist. Ändern Sie ihn auf `ezp7xRestApiProvider`.
- Code, der `ezpRestContentController` erweitert, funktioniert, solange die Kompatibilitätsschicht installiert ist. Erweitern Sie stattdessen
  `ezp7xRestContentController`.
- Für OAuth-Clients, ihre Tokens, persönliche API-Schlüssel und die v1-URLs ändert sich nichts.

Neue Installationen: Der Setup-Assistent (`eZStepCreateSites::restApiExtension()` in
`kernel/setup/steps/ezstep_create_sites.php`) aktiviert `ezprestapi`, oder `ezprestapiprovider` nur dann, wenn `ezprestapi`
nicht auf der Festplatte liegt, nie beide. Die Installer der Legacy-Site-Pakete (`ezwebin_site`, `ezflow_site`,
`ezdemo_site` und ihre `_clean`-Varianten) aktivieren `ezprestapi` ab den 6.0.15-Paketen vom 15. Oktober 2026; die
Site-Pakete von sevenx und cjw taten das bereits.

## 7. Aktivierungskombinationen, vorher und seitdem

Beide Extensions setzen `rest.ini [ApiProvider] ProviderClass[ezp]`. Einstellungen von Extensions werden pro
Eintrag in `ActiveExtensions` vorangestellt, daher wird die **weiter oben** in `ActiveExtensions` aufgeführte Extension zuletzt gelesen und ihr Wert gewinnt.

**Vorher** (`ezprestapi` bis 1.2.5, `ezprestapiprovider` bis 6.0.3):

| Aktiv | `ProviderClass[ezp]` | Ergebnis |
|---|---|---|
| nur `ezprestapi` | `ezp7xRestApiProvider` | Lesezugriffe in v1 sowie Lese- und Schreibzugriffe in v2, alle geprüft |
| beide, `ezprestapi` zuerst aufgeführt | `ezp7xRestApiProvider` | dasselbe; die Provider-Extension trug nichts bei |
| beide, `ezprestapiprovider` zuerst aufgeführt | `ezpRestApiProvider` | jedes `/api/ezp/v2/...` antwortete mit 404, auch Anlegen und Löschen; Lesezugriffe in v1 liefen in `ezpRestContentController` ohne die Berechtigungsprüfungen; `ezpl` lieferte v2 weiterhin über `ezprestapi` aus |
| nur `ezprestapiprovider` (wie der Setup-Assistent sie früher aktivierte) | `ezpRestApiProvider` | nur Lesezugriffe in v1, ungeprüft; v2 404; das Präfix `ezpl` existierte nicht |

Waren beide installiert, ließen die fünf doppelten Klassennamen außerdem den Autoload-Generator warnen und die Kopien von
`ezprestapi` behalten.

**Seitdem** (`ezprestapi` 1.2.6, `ezprestapiprovider` 6.0.4):

| Aktiv | `ProviderClass[ezp]` und `[ezpl]` | Ergebnis |
|---|---|---|
| nur `ezprestapi` | `ezp7xRestApiProvider` | v1 und v2, jede Inhaltsroute in `ezp7xRestContentController`, alle geprüft |
| beide, in beliebiger Reihenfolge | `ezp7xRestApiProvider` | dasselbe |
| nur `ezprestapiprovider` (mit `ezprestapi` als dessen Abhängigkeit installiert) | `ezp7xRestApiProvider` | dasselbe |

Das wurde geprüft, indem für jede Kombination die Routentabelle aufgebaut wurde. Die Antworten wurden mit Fixtures von 33
URLs verglichen, die vor und nach der Änderung unter Apache und unter Velocity aufgezeichnet wurden: Das
`GET /api/ezp/v1/content/node/<id>/list/offset/0/limit/20?ResponseGroups=Fields` der mobilen App und jeder andere Lesezugriff in v1 und v2 sind
Byte für Byte identisch. Der einzige Unterschied ist `OutputFormat=xhtml`, dessen `renderedOutput` wieder gefüllt wird.

## 8. Bekanntes Problem: listAtom

`GET /content/node/<id>/listAtom` antwortet in v1 und v2 mit `500`, vor und nach dieser Änderung. Zwei Dinge fehlen:
die URL-Erzeugung der Route und der Feed-View-Handler der MVC-Feed-Anbindung der Zeta Components
(`ezcMvcFeedViewHandler`), den die Distribution nicht installiert. Verwenden Sie die Route `list` oder den RSS-Export der Site,
bis das behoben ist.

**Demo-Modus.** Auf einer öffentlichen Demo (Umgebungsvariable `EXP_DEMO_MODE=1`) weigern sich die Webdienste, das Passwort eines Administrators zu ändern oder zurückzusetzen: `expaccount.reset`, `expaccount.changePassword` und `expuser.setPassword` antworten mit HTTP 403 und dem Grund, und `expaccount.forgotRequest` sendet keinen Schlüssel und gibt dieselbe Antwort wie für jede andere Adresse. [Demo-Modus](../../eng-us/bc/6.0/demo-mode.md).

## Verwandte Seiten

- [API-Schlüssel](api-schluessel.md): einen Schlüssel erstellen, verwenden und widerrufen; die Berechtigungstabelle der REST-Schnittstelle
- [ezprestapi](../../eng-us/features/6.0/extensions/ezprestapi.md) und seine [Release Notes](../../changelogs/extensions/ezprestapi.md)
- [ezprestapiprovider](../../eng-us/features/6.0/extensions/ezprestapiprovider.md) und seine [Release Notes](../../changelogs/extensions/ezprestapiprovider.md)
- [nxc_powercontent](../../eng-us/features/6.0/extensions/nxc_powercontent.md)
- [Verhaltensänderungen der Extensions](../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- [Remote-Dienste und Apps](entfernte-dienste-und-apps.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
