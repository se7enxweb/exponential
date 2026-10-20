# Erkennung, Föderation und Deployment in Velocity

Diese Seite ist für Entwickler, die Velocity-Anwendungen neben Exponential betreiben und mehrere Server zusammenarbeiten
lassen wollen. Eine Exponential-Website braucht diese Funktionen nicht. Gilt für Exponential Velocity 0.0.4.x;
Übersicht: [Velocity-Webserver](../../../eng-us/features/6.0/velocity-web-server.md).

Vier kleine Funktionen behandeln den Server als einen von mehreren:

1. **Erkennung.** Der Server veröffentlicht unter festen Adressen `/.well-known/`, was er ist und was er anbietet.
2. **Föderation.** Ein Ereignis kann hier behandelt oder an einen anderen Velocity-Server weitergeleitet werden, mit
   derselben Handler-Signatur.
3. **Deployment.** `--deploy=TARGET` kopiert die Verzeichnisse der Anwendung auf einen entfernten Server.
4. **Mesh** (experimentell, standardmäßig aus): Peer-to-Peer-Verbindungen zwischen Geräten. Nur erwähnt, damit Sie wissen,
   dass Sie es auslassen sollten.

Sie gehören zum Handler-Modell der Engine.

## Die Erkennung auf einem laufenden Server prüfen

```bash
curl -s https://host/.well-known/qbix.json
```

Erwartet: ein JSON-Dokument mit Identität, Version, Zertifikats-Fingerabdruck, Endpunkten und Plugins des Servers.

| URL | Was sie liefert |
|---|---|
| `/.well-known/qbix.json` | Identität, Version, Zertifikats-Fingerabdruck, Endpunkte (`/Q/event`, `/Q/health`, `/Q/ws`), installierte Plugins des Servers |
| `/.well-known/openapi.json` | Eine OpenAPI-Beschreibung, gebaut aus dem Verzeichnis `handlers/` und den eingebauten Endpunkten; öffnen Sie sie in einem beliebigen OpenAPI-Werkzeug |
| `/.well-known/mcp.json` | Dieselben Handler, beschrieben als Werkzeuge für das Model Context Protocol |
| `/.well-known/openclaiming/<host>/server.json` | Eine signierte Aussage über die Identität des Servers |

Fügen Sie eine Handler-Datei hinzu, und die Beschreibungen ändern sich von selbst. Um nichts davon zu veröffentlichen,
setzen Sie `{"Q":{"federation":{"advertise":false}}}` (Standard `true`).

## Ereignisse an einen anderen Server weiterleiten (Föderation)

Nennen Sie die Ereignisse, die ein Server weiterleitet:

```json
{ "Q": { "handlersUsingRemote": {
    "Users/login":    { "baseUrl": "https://auth.example.com" },
    "Streams/stream": { "baseUrl": "https://streams.example.com" }
} } }
```

Ein weitergeleitetes Ereignis wird signiert (HMAC über die sortierten, URL-kodierten Daten, kompatibel mit der Qbix
Platform), vom Empfänger geprüft, dort verteilt und beantwortet. Ein Ereignis, das weitergeleitet wurde, wird nie erneut
weitergeleitet.

Server vertrauen einander auf drei Stufen:

- ein **fest hinterlegter Peer**: Sein Fingerabdruck steht in der Konfiguration;
- ein **eigener Server**: Er teilt `Q.internal.secret`;
- **öffentlich**: nur HTTPS, nur lesender Zugriff auf die Erkennungsdokumente.

Der Fingerabdruck ist der SHA-256 des eigenen Zertifikats des Servers, aufbewahrt in `local/server.crt` und beim ersten
Start erzeugt. Er funktioniert wie `known_hosts` von SSH, ohne Zertifizierungsstelle.

## Eine Anwendung auf einen entfernten Server bringen

1. Führen Sie die Ziele in `config/deploy.json` des Anwendungsverzeichnisses auf:

   ```json
   { "targets": { "production": {
       "host": "myserver.com", "user": "deploy", "path": "/var/www/myapp",
       "key": "~/.ssh/deploy_key", "dirs": ["web", "handlers", "classes", "config"]
   } } }
   ```

2. Bringen Sie sie auf den Server:

   ```bash
   php sbin/qbixserver.php --deploy=production
   ```

   Jedes aufgeführte Verzeichnis wird mit rsync kopiert.
3. Ein entferntes Velocity mit Hot Reload (`--hotreload`, `Q.webserver.hotReload`) übernimmt die Änderung von selbst;
   andernfalls führen Sie auf dem entfernten Server `--reload` aus. Der Reiter Servers des Control Panels tut dasselbe
   aus einem Browser.

Exponential-Installationen bringen eine PHP-Änderung stattdessen mit `exp:velocity deploy` aus
([Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md)); `--deploy` ist für Engine-Anwendungen.

## Mesh (experimentell)

Das Mesh lässt Geräte einander über Bluetooth, Wi-Fi Direct oder TCP finden und HTTP-Anfragen über verschlüsselte
Sitzungen austauschen. Es ist **aus**: Ist `Q.webserver.mesh` nicht gesetzt oder `false`, wird keine Identität erzeugt,
kein Endpunkt `/Q/sync/*` antwortet (alle geben `404`), und es wird keine Peer-Verbindung aufgebaut. Schalten Sie es nur
für einen Test ein (`Q.webserver.mesh = true` oder `QBIX_MESH_ENABLED=1`), und lesen Sie vorher `docs/Mesh.md` der
Engine.

## Einstellungen

| Konfigurationsschlüssel | Standard | Bedeutung |
|---|---|---|
| `Q.federation.advertise` | `true` | Die Erkennungsdokumente veröffentlichen |
| `Q.handlersUsingRemote.<event>.baseUrl` | keiner | Dieses Ereignis an diesen Server weiterleiten |
| `Q.internal.secret` | keiner | Gemeinsames Geheimnis der eigenen Server |
| `Q.webserver.mesh` | `false` | Experimentelle Peer-to-Peer-Schicht |

## Verwandte Seiten

- [Velocity-Webserver](../../../eng-us/features/6.0/velocity-web-server.md), [WebSockets und Ereignisse](velocity-websockets-und-ereignisse.md), [Velocity-Zeitplaner](velocity-zeitplaner.md), [HTTPS-Zertifikate](../../../eng-us/features/6.0/velocity-https-certificates.md)
- [Velocity-Engines (Hinweise zur Aktualisierung)](../../../eng-us/bc/6.0/velocity-engines.md)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [Juli 2026](../../../eng-us/history/velocity/2026-07.md) (API-Erkennung aus Kommentaren, föderierte Anwendungen), [September 2026](../../../eng-us/history/velocity/2026-09d.md) (Signieren)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
