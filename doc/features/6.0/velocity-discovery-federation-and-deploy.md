# Velocity discovery, federation and deploy

This page is for developers who run Velocity applications beside Exponential and want several servers to work
together. An Exponential site does not need these features. Applies to Exponential Velocity 0.0.4.x; overview:
[Velocity web server](velocity-web-server.md).

Four small features treat the server as one of several:

1. **Discovery.** The server publishes what it is and what it offers at fixed `/.well-known/` addresses.
2. **Federation.** An event can be handled here or forwarded to another Velocity server, with the same handler
   signature.
3. **Deploy.** `--deploy=TARGET` copies the application's directories to a remote server.
4. **Mesh** (experimental, off by default): peer-to-peer links between devices. Mentioned only so you know to leave it
   off.

They belong to the engine's handler model.

## Check discovery on a running server

```bash
curl -s https://host/.well-known/qbix.json
```

Expected: a JSON document with the server identity, version, certificate fingerprint, endpoints and plugins.

| URL | What it returns |
|---|---|
| `/.well-known/qbix.json` | Server identity, version, certificate fingerprint, endpoints (`/Q/event`, `/Q/health`, `/Q/ws`), installed plugins |
| `/.well-known/openapi.json` | An OpenAPI description built from the `handlers/` directory and the built-in endpoints; open it in any OpenAPI tool |
| `/.well-known/mcp.json` | The same handlers described as tools for the Model Context Protocol |
| `/.well-known/openclaiming/<host>/server.json` | A signed claim of the server's identity |

Add a handler file and the descriptions change by themselves. To publish none of it, set
`{"Q":{"federation":{"advertise":false}}}` (default `true`).

## Forward events to another server (federation)

Name the events a server forwards:

```json
{ "Q": { "handlersUsingRemote": {
    "Users/login":    { "baseUrl": "https://auth.example.com" },
    "Streams/stream": { "baseUrl": "https://streams.example.com" }
} } }
```

A forwarded event is signed (HMAC over the sorted, URL-encoded data, compatible with the Qbix Platform), verified by
the receiver, dispatched there and answered. An event that was forwarded is never forwarded again.

Servers trust each other at three levels:

- a **pinned peer**: its fingerprint is kept in configuration;
- an **owned server**: it shares `Q.internal.secret`;
- **public**: HTTPS only, read-only access to the discovery documents.

The fingerprint is the SHA-256 of the server's own certificate, kept in `local/server.crt` and made on first run. It
works like SSH's `known_hosts`, with no certificate authority.

## Deploy an application to a remote server

1. List the targets in `config/deploy.json` of the application directory:

   ```json
   { "targets": { "production": {
       "host": "myserver.com", "user": "deploy", "path": "/var/www/myapp",
       "key": "~/.ssh/deploy_key", "dirs": ["web", "handlers", "classes", "config"]
   } } }
   ```

2. Deploy:

   ```bash
   php sbin/qbixserver.php --deploy=production
   ```

   Each listed directory is copied with rsync.
3. A remote Velocity with hot reload (`--hotreload`, `Q.webserver.hotReload`) picks the change up by itself; otherwise
   run `--reload` on the remote. The control panel's Servers tab does the same from a browser.

Exponential installations deploy a PHP change with `exp:velocity deploy` instead
([Velocity engines](../../bc/6.0/velocity-engines.md)); `--deploy` is for engine applications.

## Mesh (experimental)

The mesh lets devices find each other over Bluetooth, Wi-Fi Direct or TCP and exchange HTTP requests over encrypted
sessions. It is **off**: with `Q.webserver.mesh` unset or `false`, no identity is made, no `/Q/sync/*` endpoint answers
(all give `404`) and no peer connection is made. Switch it on only for a test (`Q.webserver.mesh = true` or
`QBIX_MESH_ENABLED=1`), and read the engine's `docs/Mesh.md` first.

## Settings

| Configuration key | Default | Meaning |
|---|---|---|
| `Q.federation.advertise` | `true` | Publish the discovery documents |
| `Q.handlersUsingRemote.<event>.baseUrl` | none | Forward this event to that server |
| `Q.internal.secret` | none | Shared secret of servers you own |
| `Q.webserver.mesh` | `false` | Experimental peer-to-peer layer |

## Related pages

- [Velocity web server](velocity-web-server.md), [WebSockets and events](velocity-websockets-and-events.md), [Velocity scheduler](velocity-scheduler.md), [HTTPS certificates](velocity-https-certificates.md)
- [Velocity engines (upgrade notes)](../../bc/6.0/velocity-engines.md)
- [Changelog: Exponential Velocity engine](../../changelogs/extensions/exponential-velocity.md)
- History: [July 2026](../../history/velocity/2026-07.md) (API discovery from comments, federated apps), [September 2026](../../history/velocity/2026-09d.md) (signing)
