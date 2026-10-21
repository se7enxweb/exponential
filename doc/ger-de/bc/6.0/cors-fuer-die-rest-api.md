# CORS für die REST-API

Ab 6.0.15 kann die REST-Schnittstelle (alles, was `index_rest.php` unterhalb von `/api/` ausliefert: die Inhalts-API
v1 und v2, der OAuth-Token-Endpunkt und die Routen jedes REST-Providers) ursprungsübergreifende Anfragen eines
Browsers beantworten. Das ist **standardmäßig ausgeschaltet**: Wie ausgeliefert ändert sich nichts, es wird kein
CORS-Header gesendet und eine `OPTIONS`-Anfrage wird wie bisher geroutet.

Eine Single-Page-Anwendung oder ein Web-Frontend auf einem anderen Host braucht es, um die API mit `fetch()`
aufzurufen. Eine native mobile App, ein Server, ein Skript oder `curl` brauchen es nicht: CORS ist eine Regel der
Browser.

## Einschalten

In einer Überschreibung von `rest.ini` (zum Beispiel `settings/override/rest.ini.append.php` oder der eines
Siteaccess):

```ini
[CorsSettings]
Cors=enabled
AllowedOrigins[]
AllowedOrigins[]=https://app.example.com
AllowedOrigins[]=http://localhost:3000
```

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Cors` | `disabled` | `enabled` schaltet CORS ein |
| `AllowedOrigins[]` | keine | die Ursprünge, die die API aufrufen dürfen, genau so, wie der Browser sie sendet: Schema, Host und ein Port, der nicht der Standardport ist; kein Pfad, kein abschließender Schrägstrich. Schema und Host werden ohne Groß- und Kleinschreibung verglichen. `null` wird nie angenommen. `*` (jeder Ursprung) nur, solange `AllowCredentials` ausgeschaltet ist |
| `AllowedMethods[]` | `GET`, `POST`, `PUT`, `PATCH`, `DELETE` | die Methoden, die ein Preflight erfragen darf |
| `AllowedHeaders[]` | `Authorization`, `Content-Type`, `Accept`, `Accept-Language`, `X-Siteaccess` | die Anfrage-Header, die ein Preflight erfragen darf (ohne Groß- und Kleinschreibung) |
| `ExposedHeaders[]` | keine | Antwort-Header, die ein Skript außer den CORS-sicheren lesen darf |
| `MaxAge` | `600` | wie lange ein Browser die Antwort auf einen Preflight behalten darf, in Sekunden; `0` sendet kein `Access-Control-Max-Age` |
| `AllowCredentials` | `disabled` | `enabled` sendet `Access-Control-Allow-Credentials: true`, sodass der Browser das Sitzungs-Cookie eines angemeldeten Benutzers mitsendet. Für Clients, die ein OAuth-Token oder einen API-Schlüssel im Header `Authorization` senden, ausgeschaltet lassen |

Nach der Änderung den INI-Cache leeren und PHP neu laden (`./console exp:velocity deploy --allow-root-user` erledigt
beides).

## Was gesendet wird

Solange CORS eingeschaltet ist:

- Die Antwort auf eine Anfrage mit erlaubtem `Origin` trägt `Access-Control-Allow-Origin` mit diesem Ursprung (nie
  `*` zusammen mit Credentials), `Access-Control-Allow-Credentials: true`, wenn Credentials erlaubt sind, und
  `Access-Control-Expose-Headers`, wenn `ExposedHeaders[]` welche nennt.
- Eine Anfrage von einem nicht erlaubten Ursprung oder ohne `Origin` erhält **keinen einzigen
  `Access-Control-*`-Header**.
- Jede Antwort trägt `Vary: Origin`, damit ein Cache vor der Website die Antwort für einen Ursprung nie an einen
  anderen gibt. Das ist keine CORS-Erlaubnis und wird unabhängig vom Ursprung gesendet.
- Ein Preflight (`OPTIONS` mit `Origin` und `Access-Control-Request-Method`) wird vom REST-Kernel selbst beantwortet
  und **führt die Anfrage nie aus**: `204 No Content` mit `Access-Control-Allow-Methods`,
  `Access-Control-Allow-Headers` und `Access-Control-Max-Age`, wenn Ursprung, Methode und jeder erfragte Header
  erlaubt sind, sonst `403 Forbidden` ohne `Access-Control-*`-Header.

CORS entscheidet nur, welche Seiten die Antworten *lesen* dürfen. Es ist keine Zugriffskontrolle: Jede Anfrage wird
weiterhin authentifiziert und gegen die Richtlinien des Benutzers geprüft.

## Prüfen

```bash
# aus (Standard): kein Access-Control-Header
curl -s -o /dev/null -D - -H 'Origin: https://app.example.com' https://example.com/api/ezp/v2/content/node/2 | grep -i '^access-control'

# an: ein Preflight von einem erlaubten Ursprung
curl -s -o /dev/null -D - -X OPTIONS -H 'Origin: https://app.example.com' \
     -H 'Access-Control-Request-Method: GET' -H 'Access-Control-Request-Headers: authorization' \
     https://example.com/api/ezp/v2/content/node/2
```

## Code und Tests

- `expRestCors` (`kernel/private/rest/classes/cors.php`), aufgerufen von `ezpKernelRest::run()`, bevor die Anfrage
  verteilt wird.
- `tests/tests/kernel/private/restapi/expRestCorsTest.php` (ohne Datenbank): standardmäßig aus, erlaubte und nicht
  erlaubte Ursprünge, erlaubte und abgelehnte Preflights, kein Platzhalter zusammen mit Credentials.
