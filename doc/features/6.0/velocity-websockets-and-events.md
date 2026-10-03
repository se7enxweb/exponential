# Velocity WebSockets, Server-Sent Events and rooms

*Applies to: Exponential Velocity 0.0.4.x. History: [July 2026](../../history/velocity/2026-07.md) (long-lived processes, socket.io), [August 2026](../../history/velocity/2026-08.md) (socket.io in all browsers, Server-Sent Events). Overview: [Velocity web server](velocity-web-server.md).*

## What it is

Three ways to keep a conversation open between a browser and PHP, built into the server:

| Model | One PHP process per | State lives | Use it for |
|---|---|---|---|
| **WebSocket connection** (socket.io compatible) | connection | in that process's statics, until the client leaves | per-user state: login, preferences, rate limits, notifications |
| **Room** | active room | in the room's process, shared by every member | chat, game positions, live tallies, shared cursors |
| **Server-Sent Events** | response | in the script, while it streams | progress bars, live logs, token streams |

The server's own endpoints are `/socket.io` (the socket.io protocol) and `/Q/socket.js` (a minimal client); set `Q.socket.io` or `Q.socket.js` to `false` to switch one off. The dashboard itself uses `/Q/ws`.

These features belong to the engine's handler model (`handlers/<path>.php`, dispatched with `Q::event()`), so an application that wants them uses the engine's application layer. An Exponential site served by Velocity does not need them; they are there for the extensions that want real-time features.

## Per-connection events

Map event names to handlers in the configuration; an event with no mapping uses its own name as the handler path. `_connect` and `_disconnect` fire by themselves.

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

## Rooms

```json
{ "Q": { "webserver": { "sockets": { "rooms": {
    "chat/$room": { "handler": "chat/room" },
    "game/$id":   { "handler": "game/room", "tick": 100 }
} } } } }
```

`$name` in a pattern matches one name (`chat/$room` matches `chat/general`). A per-connection handler enters a room with `$socket->join('chat/general', [...])`; that is the only gateway, so access control lives there. The room process dispatches one file per event under the handler prefix: `init.php` (first member), `join.php`, `leave.php`, `tick.php` (every `tick` milliseconds), `destroy.php` (last member left), and one file per message name. When the last member leaves, the process ends.

## Server-Sent Events

A script that sends `Content-Type: text/event-stream` is streamed instead of buffered: the headers go out at once with chunked encoding and every `flush()` becomes a chunk. Two other triggers do the same: the header `X-Accel-Buffering: no`, or `Q_Response::setStreaming(true)`.

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

Streaming responses are not stored by the [response cache](velocity-response-cache.md). They occupy a worker for as long as they run. Not verified here: whether `Q.webserver.requestTimeout` (default 30 seconds) ends a stream; test it by streaming for 40 seconds with `curl -N` and see whether the server cuts the stream, then raise the setting if it does.

## Settings

| Key | Default | Meaning |
|---|---|---|
| `Q.webserver.sockets.events.<event>` | the event name | Handler path for an event; `_connect`, `_disconnect` |
| `Q.webserver.sockets.rooms.<pattern>` | none | `{handler, tick}` of a room |
| `Q.socket.io`, `Q.socket.js` | `/socket.io`, `/Q/socket.js` | Endpoint paths; `false` disables |
| `Q.webserver.maxConnections` | `1024` | Open connections at once (WebSockets count) |

## Limits

- A WebSocket connection holds a process; plan memory with the [worker pool](../../specifications/6.0/velocity-worker-pool.md) numbers.
- Behind a proxy that does not pass upgrades, WebSockets fail; keep the proxy transparent for `Upgrade`.
- The shell's WebSocket accepts the same origin only ([Q shell](velocity-q-shell.md)).

## See also

[Velocity scheduler](velocity-scheduler.md), [Control panel and dashboard](velocity-control-panel.md), [July](../../history/velocity/2026-07.md) and [August 2026](../../history/velocity/2026-08.md) chronicles, [changelog](../../changelogs/extensions/exponential-velocity.md).
