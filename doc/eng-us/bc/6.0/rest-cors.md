# CORS for the REST API

From 6.0.15 on, the REST interface (everything `index_rest.php` serves below `/api/`: the content API v1 and v2, the
OAuth token endpoint and the routes of every REST provider) can answer cross-origin requests from a browser. It is
**off by default**: as shipped nothing changes, no CORS header is sent and an `OPTIONS` request is routed as before.

A single page application or a web front end on another host needs it to call the API with `fetch()`. A native
mobile app, a server, a script or `curl` does not: CORS is a rule of browsers only.

## Switching it on

In an override of `rest.ini` (for example `settings/override/rest.ini.append.php`, or a siteaccess's):

```ini
[CorsSettings]
Cors=enabled
AllowedOrigins[]
AllowedOrigins[]=https://app.example.com
AllowedOrigins[]=http://localhost:3000
```

| Setting | Default | Meaning |
|---|---|---|
| `Cors` | `disabled` | `enabled` switches CORS on |
| `AllowedOrigins[]` | none | the origins allowed to call the API, exactly as the browser sends them: scheme, host and a port that is not the default; no path, no trailing slash. Scheme and host are compared without case. `null` is never accepted. `*` (any origin) is accepted only while `AllowCredentials` is disabled |
| `AllowedMethods[]` | `GET`, `POST`, `PUT`, `PATCH`, `DELETE` | the methods a preflight may ask for |
| `AllowedHeaders[]` | `Authorization`, `Content-Type`, `Accept`, `Accept-Language`, `X-Siteaccess` | the request headers a preflight may ask for (compared without case) |
| `ExposedHeaders[]` | none | response headers a script may read besides the CORS-safelisted ones |
| `MaxAge` | `600` | how long a browser may keep a preflight answer, in seconds; `0` sends no `Access-Control-Max-Age` |
| `AllowCredentials` | `disabled` | `enabled` sends `Access-Control-Allow-Credentials: true`, so the browser sends the session cookie of a signed in user. Leave it off for clients that send an OAuth token or an API key in the `Authorization` header |

Clear the INI cache and reload PHP after the change (`./console exp:velocity deploy --allow-root-user` does both).

## What is sent

While CORS is enabled:

- A response to a request whose `Origin` is allowed carries `Access-Control-Allow-Origin` with that origin (never
  `*` together with credentials), `Access-Control-Allow-Credentials: true` when credentials are allowed, and
  `Access-Control-Expose-Headers` when `ExposedHeaders[]` lists any.
- A request from an origin that is not allowed, or without an `Origin`, gets **no `Access-Control-*` header at all**.
- Every response carries `Vary: Origin`, so a cache in front of the site never gives the answer meant for one origin
  to another. It is not a CORS permission and is sent whatever the origin.
- A preflight (`OPTIONS` with `Origin` and `Access-Control-Request-Method`) is answered by the REST kernel itself and
  **never runs the request**: `204 No Content` with `Access-Control-Allow-Methods`, `Access-Control-Allow-Headers`
  and `Access-Control-Max-Age` when the origin, the method and every requested header are allowed, otherwise
  `403 Forbidden` with no `Access-Control-*` header.

CORS decides only which pages may *read* the answers. It is no access control: every request is still
authenticated and checked against the user's policies as before.

## Check it

```bash
# off (the default): no access-control header
curl -s -o /dev/null -D - -H 'Origin: https://app.example.com' https://example.com/api/ezp/v2/content/node/2 | grep -i '^access-control'

# on: a preflight from an allowed origin
curl -s -o /dev/null -D - -X OPTIONS -H 'Origin: https://app.example.com' \
     -H 'Access-Control-Request-Method: GET' -H 'Access-Control-Request-Headers: authorization' \
     https://example.com/api/ezp/v2/content/node/2
```

## Code and tests

- `expRestCors` (`kernel/private/rest/classes/cors.php`), called by `ezpKernelRest::run()` before the request is
  dispatched.
- `tests/tests/kernel/private/restapi/expRestCorsTest.php` (no database): off by default, allowed and disallowed
  origins, preflights allowed and refused, no wildcard with credentials.
