# WebSockets, Server-Sent Events und Räume in Velocity

Diese Seite ist für Entwickler, die Echtzeitfunktionen (Chat, Fortschritt live, Benachrichtigungen) neben einer
Exponential-Website betreiben wollen, die Velocity ausliefert. Eine gewöhnliche Exponential-Website braucht sie nicht.
Gilt für Exponential Velocity 0.0.4.x; Übersicht: [Velocity-Webserver](../../../eng-us/features/6.0/velocity-web-server.md).

Velocity hat drei im Server eingebaute Wege, eine Verbindung zwischen einem Browser und PHP offen zu halten:

| Modell | Ein PHP-Prozess pro | Zustand liegt | Verwenden Sie es für |
|---|---|---|---|
| **WebSocket-Verbindung** (kompatibel mit socket.io) | Verbindung | in den statischen Variablen dieses Prozesses, bis der Client geht | Zustand pro Benutzer: Anmeldung, Einstellungen, Ratenbegrenzungen, Benachrichtigungen |
| **Raum** | aktivem Raum | im Prozess des Raums, geteilt von allen Mitgliedern | Chat, Spielpositionen, Live-Zählungen, geteilte Cursor |
| **Server-Sent Events** | Antwort | im Skript, solange es streamt | Fortschrittsbalken, Live-Protokolle, Token-Streams |

Die eigenen Endpunkte des Servers sind `/socket.io` (das Protokoll socket.io) und `/Q/socket.js` (ein minimaler Client).
Setzen Sie `Q.socket.io` oder `Q.socket.js` auf `false`, um einen abzuschalten. Das Dashboard selbst verwendet `/Q/ws`.

Diese Funktionen gehören zum Handler-Modell der Engine (`handlers/<path>.php`, verteilt mit `Q::event()`), sodass eine
Anwendung, die sie will, die Anwendungsschicht der Engine verwendet.

## Fortschritt in einer Datei an einen Browser streamen (Server-Sent Events)

Ein Skript, das `Content-Type: text/event-stream` sendet, wird gestreamt statt gepuffert: Die Header gehen sofort mit
Chunked Encoding hinaus, und jedes `flush()` wird zu einem Chunk.

```php
<?php
Q_Response::header('Content-Type: text/event-stream');
Q_Response::header('Cache-Control: no-cache');
for ($i = 1; $i <= 10; $i++) {
    echo 'data: ' . json_encode(['count' => $i]) . "\n\n";
    @ob_flush(); flush();
    sleep(1);
}
```

Beobachten Sie es mit `curl -N https://host/<path of the script>`: Pro Sekunde kommt eine Zeile `data:` an.

Zwei andere Auslöser streamen eine Antwort ebenfalls: der Header `X-Accel-Buffering: no` oder
`Q_Response::setStreaming(true)`. Gestreamte Antworten werden vom [Antwort-Cache](../../../eng-us/features/6.0/velocity-response-cache.md) nicht
gespeichert und belegen einen Worker, solange sie laufen.

Nicht geprüft: ob `Q.webserver.requestTimeout` (Standard 30 Sekunden) einen Stream beendet. Testen Sie es, indem Sie 40
Sekunden lang mit `curl -N` streamen und sehen, ob der Server den Stream abschneidet; erhöhen Sie die Einstellung, wenn
er es tut.

## Ereignisse pro Verbindung

Ordnen Sie in der Konfiguration Ereignisnamen Handlern zu. Ein Ereignis ohne Zuordnung verwendet seinen eigenen Namen als
Handler-Pfad. `_connect` und `_disconnect` werden von selbst ausgelöst.

```json
{ "Q": { "webserver": { "sockets": { "events": {
    "_connect":     "auth/login",
    "_disconnect":  "chat/leave",
    "chat/message": "chat/message"
} } } } }
```

```javascript
const socket = io('https://host', { transports: ['websocket'] });
socket.emit('chat/message', { text: 'hello' }, (res) => console.log('saved', res.id));
```

## Räume

```json
{ "Q": { "webserver": { "sockets": { "rooms": {
    "chat/$room": { "handler": "chat/room" },
    "game/$id":   { "handler": "game/room", "tick": 100 }
} } } } }
```

- `$name` in einem Muster passt auf einen Namen (`chat/$room` passt auf `chat/general`).
- Ein Handler pro Verbindung betritt einen Raum mit `$socket->join('chat/general', [...])`. Das ist der einzige Weg
  hinein, daher liegt die Zugriffskontrolle dort.
- Der Prozess des Raums verteilt pro Ereignis eine Datei unter dem Handler-Präfix: `init.php` (erstes Mitglied),
  `join.php`, `leave.php`, `tick.php` (alle `tick` Millisekunden), `destroy.php` (das letzte Mitglied ist gegangen) und
  eine Datei pro Nachrichtenname.
- Wenn das letzte Mitglied geht, endet der Prozess.

## Einstellungen

| Konfigurationsschlüssel | Standard | Bedeutung |
|---|---|---|
| `Q.webserver.sockets.events.<event>` | der Ereignisname | Handler-Pfad für ein Ereignis; `_connect`, `_disconnect` |
| `Q.webserver.sockets.rooms.<pattern>` | keiner | `{handler, tick}` eines Raums |
| `Q.socket.io`, `Q.socket.js` | `/socket.io`, `/Q/socket.js` | Pfade der Endpunkte; `false` schaltet ab |
| `Q.webserver.maxConnections` | `1024` | Gleichzeitig offene Verbindungen (WebSockets zählen mit) |

## Grenzen

- Eine WebSocket-Verbindung belegt einen Prozess; planen Sie den Speicher mit den Zahlen des
  [Worker-Pools](../../spezifikationen/6.0/velocity-worker-pool.md).
- Hinter einem Proxy, der keine Upgrades durchlässt, scheitern WebSockets. Halten Sie den Proxy für `Upgrade`
  durchlässig.
- Der WebSocket der Shell nimmt nur denselben Ursprung an ([Q-Shell](velocity-q-shell.md)).

## Verwandte Seiten

- [Velocity-Webserver](../../../eng-us/features/6.0/velocity-web-server.md), [Velocity-Zeitplaner](velocity-zeitplaner.md), [Control Panel und Dashboard](../../../eng-us/features/6.0/velocity-control-panel.md), [Erkennung, Föderation und Deployment](velocity-erkennung-foederation-und-deployment.md)
- [Worker-Pool und Kompatibilitätsschicht von Velocity](../../spezifikationen/6.0/velocity-worker-pool.md)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [Juli 2026](../../../eng-us/history/velocity/2026-07.md) (langlebige Prozesse, socket.io), [August 2026](../../../eng-us/history/velocity/2026-08.md) (socket.io in allen Browsern, Server-Sent Events)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
