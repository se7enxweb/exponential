---
title: velocity.ini [ServerSettings]
audience: developer, admin
category: examples
summary: "The settings of [ServerSettings] in velocity.ini: Engine, ScriptPath, DocumentRoot, FollowSymlinks, Host, Port, HTTPSPort, User ..."
keywords: velocity.ini, ServerSettings, INI, setting, Engine, ScriptPath, DocumentRoot, FollowSymlinks, Host, Port, HTTPSPort, User, Group, AllowRootWorkers, Workers, SpareWorkers, IdleWorkerTimeout, Instances, SharedInstances, Nice, StatTtl, Zygote, Brand, BrandUrl, Maintainer, MaintainerUrl, BrandMark, BrandColor, BrandAccent, BrandBackground, BrandIcon, BrandOgImage, BrandDescription, PreloadWarmup
type: generated
generated: exp:help:examples
---
# velocity.ini [ServerSettings]

The block `[ServerSettings]` of `settings/velocity.ini`, with 48 setting(s).

Never edit `settings/velocity.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/velocity.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in an extension (read when the extension is active).

## Engine

> The server start, stop and status drive when no --engine is given:  
>
>   php         PHP's own built-in web server (php -S), with the PHP that runs  
>               exp:velocity. For development: nothing to install and nothing  
>               it could lack, but no TLS and no reload. The shipped default,  
>               because it always works. See \[PHPServerSettings\].  
>   frankenphp  FrankenPHP, the Caddy web server with PHP built in, as one  
>               binary that \`exp:velocity install\` downloads. The  
>               production-ready alternative.  
>               See \[FrankenPHPSettings\].  
>   qbix        the bundled Qbix server (vendor/se7enxweb/exponential-velocity,  
>               formerly vendor/se7enxweb/qbix-webserver; either is found), a  
>               PHP process with persistent or per-request workers. The  
>               recommended engine for every stage -- development, alpha,  
>               beta, demo, stable and production -- and the fastest.  
>
> Set qbix here for any site; frankenphp is the production-ready  
> alternative. The verbs are the same for every  
> engine, and --engine=<name> (or --all) reaches the others for one command.  
>
> The engines can run side by side, for tests: each has a port of its own  
> (php 8087, qbix 8088 below, frankenphp 8089), a pid file and logs of its own.  
> \[FrankenPHPSettings\] and \[PHPServerSettings\] may set Port, HTTPSPort, Host,  
> Workers, SpareWorkers and DocumentRoot for their engine; left empty, the  
> value here applies. Settings only one server has are reported by \`status\`  
> when they are set for another. See doc/eng-us/bc/6.0/velocity-engines.md.  

Default: `php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Engine=php
*/ ?>
```

## ScriptPath

> The Qbix server's script. Relative paths resolve against the installation  
> root. The qbix engine only; Port, HTTPSPort, Host and Workers below are the  
> qbix engine's, and the others' when their own blocks leave them empty.  
> From Exponential Velocity 0.0.4.41 the engine keeps the server in  
> sbin/qbixserver.php (its qbixctl and qbixconsole in sbin/ too, its shell in  
> bin/), and qbixserver.php at the top of the package is a forwarder that runs  
> it in the same process: the value below works with the engine before and  
> after that release, and sbin/qbixserver.php may be named instead (the other  
> path is used when the one named is not there). A server started by either  
> path is this installation's for status, stop and restart.  

Default: `vendor/se7enxweb/exponential-velocity/qbixserver.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
ScriptPath=vendor/se7enxweb/exponential-velocity/qbixserver.php
*/ ?>
```

## DocumentRoot

> Document root handed to the server. Empty means the installation root,  
> which is where index.php lives.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
DocumentRoot=
*/ ?>
```

## FollowSymlinks

> Serve files that a symbolic link inside the document root leads to outside  
> of it. disabled refuses them: the qbix engine answers 403, the php engine  
> 404. A link placed in the document root -- by an upload, an installer or a  
> shared asset tree -- would otherwise hand out whatever it points at.  
> enabled is for installations that link on purpose, such as extensions  
> linked in from a shared checkout or var/storage on another disk. A path  
> that climbs out with ../ is refused either way, and neither engine ever  
> serves a .php file as a file. The frankenphp engine always follows links  
> (Caddy's file server has no switch for it).  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
FollowSymlinks=disabled
*/ ?>
```

## Host

> Bind address. 127.0.0.1 keeps the server reachable only through a tunnel or  
> a reverse proxy, which is the right default: this server has no HTTP auth  
> of its own and serves the whole document root.  

Default: `127.0.0.1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Host=127.0.0.1
*/ ?>
```

## Port

Default: `8088`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Port=8088
*/ ?>
```

## HTTPSPort

Default: `8080`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
HTTPSPort=8080
*/ ?>
```

## User

> The user and group the workers run as when the server is started as root  
> (the qbix engine; Apache's User and Group). The server process itself stays  
> root -- it binds the ports and reads the certificate -- and every worker gives  
> root up before it runs any of the application, so what the site writes  
> (image variations, caches, sessions) belongs to the site's user, as it does  
> under Apache and PHP-FPM, and not to root.  
>
> Empty: VC\_RUN\_USER / VC\_RUN\_GROUP from the environment or /etc/vc/envvars,  
> else the owner and group of the document root. When the document root  
> itself belongs to root the workers stay root, with a warning. A user or  
> group that does not exist stops the start. root is refused unless  
> AllowRootWorkers=enabled. Names or numeric ids (#10009).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
User=
*/ ?>
```

## Group

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Group=
*/ ?>
```

## AllowRootWorkers

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
AllowRootWorkers=disabled
*/ ?>
```

## Workers

> Explicit, always. Left to itself the server sizes the pool from the machine  
> and can choose a number so large it accepts connections and answers none.  
> With SpareWorkers set this is the most the pool grows to, not what it runs.  

Default: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=4
*/ ?>
```

## SpareWorkers

> Dynamic pool. Workers to keep running when quiet; more are forked when all  
> of them are busy, up to Workers, and retired again after IdleWorkerTimeout  
> seconds without a request. Each worker holds ~10 MB of real memory of its  
> own from the moment it is forked, idle or not, so a large fixed pool pays  
> for workers that never serve anything. 0 = fixed pool (all Workers, always).  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
SpareWorkers=0
*/ ?>
```

## IdleWorkerTimeout

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
IdleWorkerTimeout=60
*/ ?>
```

## Instances

> How many servers to run on the same ports (the qbix engine only). A server  
> answers cached pages in its own process, about 0.3 ms of CPU a page, so one  
> server is bounded by one core: ~3,500 cached pages a second measured on a  
> 12-core machine. With more than one, every instance listens on the same ports  
> with SO\_REUSEPORT and the kernel spreads connections across them: two  
> measured 6,600-6,900, four 12,200-13,100. Renders are not faster; they are  
> bounded by the workers.  
>
> Each instance has its own worker pool: Workers and SpareWorkers above are  
> per instance, so divide them by Instances to keep the same memory. Each has  
> its own pid file beside PidFile (server.pid, server.1.pid, ...). graceful  
> reloads them one at a time, so the port keeps answering. Linux and the BSDs  
> only.  

Default: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Instances=1
*/ ?>
```

## SharedInstances

> enabled: the instances share one cache of compiled scripts and one APCu  
> segment (the engine starts the first and forks the others from it, Exponential  
> Velocity 0.0.4.56 and later), so a script is compiled once and a result the  
> kernel keeps in APCu (the query cache) is paid for once, not once per  
> instance; all of them write to LogFile. disabled: every instance is started  
> on its own, with caches and a log (console.1.log ...) of its own. An instance  
> reloaded by graceful sets PHP up again and has caches of its own from then on.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
SharedInstances=enabled
*/ ?>
```

## Nice

> The priority (nice value, -20 to 19) the server and its workers run at,  
> whatever the caller of start, restart or deploy runs at: a deploy started  
> under nice -n 19 used to leave every worker at 19. Lowering it below the  
> caller's needs root. inherit keeps the caller's value (the old behavior).  
> exp:velocity status reports a running parent at another value.  

Default: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Nice=5
*/ ?>
```

## StatTtl

> How long a worker may keep what it knows about files -- whether a path  
> exists, a file's mtime and size -- across requests, in seconds (at most 10),  
> the way opcache.revalidate\_freq does for compiles. 0 forgets at the end of  
> every request. Changes the worker makes itself, clearstatcache() and a  
> program it runs (ImageMagick) are seen at once either way; only a change made  
> by another process -- another worker, Apache on the same files, an editor --  
> can take up to this long to show. 1 measured 13% less CPU per rendered page.  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StatTtl=0
*/ ?>
```

## Zygote

> Fork workers after the start from a zygote -- a process forked before the  
> server accepted its first connection -- instead of from the server itself.  
> A worker forked from the server inherits every open TLS connection and cannot  
> close it (that would end it for the server too), so under load each one kept  
> visitors' connections open until it retired: up to 50 in CLOSE-WAIT after a  
> 20-second burst here. enabled, disabled, or empty for the engine's default  
> (enabled). Needs PHP's sockets extension; if the zygote fails, workers are  
> forked from the server as before.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Zygote=
*/ ?>
```

## Brand

> Product name shown across the served /Q/ views: the dashboard  
> heading and title, the docs chrome, the panel, the manifest. Empty  
> keeps the server's own default ("Qbix Server"); an installation  
> that names itself overrides this in settings/override/velocity.ini.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Brand=
*/ ?>
```

## BrandUrl

> Where the brand name links (the product's home), and the maintainer  
> credit. All optional; empty shows plain text with no link.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandUrl=
*/ ?>
```

## Maintainer

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Maintainer=
*/ ?>
```

## MaintainerUrl

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
MaintainerUrl=
*/ ?>
```

## BrandMark

> Icon and link preview of the /Q/ views (favicon, home-screen icons, web app  
> manifest, the image a shared link shows). All optional.  
>   BrandMark        the letter on the drawn icon (default: the brand's first)  
>   BrandColor       the letter's colour, #rgb or #rrggbb  
>   BrandAccent      the colour of the speed streaks beside it  
>   BrandBackground  the icon tile and theme colour  
>   BrandIcon        an image file used instead of the drawn mark (.svg/.png/.ico)  
>   BrandOgImage     a 1200x630 PNG/JPEG used instead of the drawn preview  
>   BrandDescription one line for link previews and the manifest  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandMark=
*/ ?>
```

## BrandColor

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandColor=
*/ ?>
```

## BrandAccent

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandAccent=
*/ ?>
```

## BrandBackground

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandBackground=
*/ ?>
```

## BrandIcon

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandIcon=
*/ ?>
```

## BrandOgImage

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandOgImage=
*/ ?>
```

## BrandDescription

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandDescription=
*/ ?>
```

## PreloadWarmup

> Load the kernel and render a page in the parent before forking, so workers  
> share it copy-on-write instead of each building it. 'enabled' or 'disabled'.  
>
> STATUS: left DISABLED. Do not enable on a site with real content without the  
> regression pass below -- it has broken a different feature at each attempt.  
>
> The warm-up shares the warmed arena (~21 MB/worker vs ~209) and, with the  
> comprehensive static reset below, renders correct, styled pages with correct  
> breadcrumbs. But that reset -- resetting every user-class static -- also broke  
> the ezjscore AJAX endpoint (/ezjscore/call/...), so the "load more" buttons  
> returned 502; skipping the ezjsc\* classes did not fix it, the fatal was in  
> another reset. That is the fourth distinct feature the warm-up has broken  
> (wrong pages, missing CSS, wrong breadcrumb, dead load-more), each surfacing  
> only after the last was fixed. The render-in-parent approach couples too much  
> of eZ's request-time static state to share safely by resetting it; a viable  
> warm-up needs a different mechanism (e.g. snapshotting the pool BEFORE the  
> render, an engine change) and a regression suite that exercises EVERY feature,  
> AJAX included. Its memory saving is not the constraint that matters here --  
> the throughput and event-loop ceilings arrive first -- so it is off.  
>
> The path here was long. Clearing request globals by name fixed three bugs  
> (routing, the template override map, ezjscore's CSS "loaded" flag) but a  
> fourth kept surfacing: the home page read a frozen current-node and showed the  
> wrong breadcrumb. Naming leaks did not converge, so bin/php/velocity-warmup.php  
> now does the opposite: after rendering it resets EVERY user-class static  
> property to its declared default -- the state a freshly bootstrapped worker  
> has -- and clears the request globals, keeping only the pure config and type  
> registries (eZINI, eZDataType, eZWorkflowType, eZModule, eZExtension, eZLocale,  
> eZCharsetInfo, eZTextCodec). Those are expensive to rebuild and hold no request  
> state; resetting them too timed pages out and doubled memory. Everything else  
> -- template engine, design resolver, content-class caches, the current node --  
> is reset, which is what finally cleared the breadcrumb and any future leak of  
> the same kind. The database is closed before the fork.  
>
> Verify after any change: several distinct urls return distinct content, each  
> carries a resolving stylesheet, the home page has NO breadcrumb, and a deep  
> page has its OWN correct breadcrumb. That last pair is the test that catches  
> the current-node leak.  
>
> It renders a representative page in the parent so workers inherit the warmed  
> Zend arena copy-on-write: measured ~17-24 MB private per warm worker with it,  
> versus ~209 MB without. The parent's render leaves eZ request state behind,  
> which the pool's snapshot would otherwise freeze as every worker's baseline,  
> so bin/php/velocity-warmup.php clears it after rendering: the request-routing  
> globals (or every url served the front page), the template override cache map  
> (or other page types lost their CSS), and ezjscore's packer "already loaded"  
> static (or packer-loaded stylesheets, e.g. the search page, were skipped).  
> The database is closed before the fork, so no worker shares the connection.  
>
> If you change what the warm-up renders or how it resets, re-verify BEFORE  
> trusting it: several distinct urls must return distinct content AND each must  
> carry a resolving stylesheet.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
PreloadWarmup=disabled
*/ ?>
```

## ForkPerRequest

> How long a worker lives.  
>
>   enabled    one request per worker, then the parent forks a fresh one.  
>              Nothing a request leaves behind can reach the next, as under  
>              php-fpm, and KeepGlobals has nothing left to do. The default  
>              until 2026-09-30.  
>   disabled   persistent workers: each serves requests until it has served  
>              the server's maxRequests, with the request's state cleared in  
>              between. This is what KeepGlobals below exists for. The  
>              shipped default since 2026-09-30 (approved that day), for the  
>              reasons at the end of this comment.  
>
> Measured on an installation of the demo site, rendered front page, sixteen  
> workers, sixteen concurrent, the two modes alternated three times on the  
> same server code: 23.6-23.9 req/s with a fresh worker per request against  
> 17.5-19.8 with persistent ones, p99 0.97-1.10s against 1.35-1.49s. Clearing  
> a used worker cost more than forking a new one.  
>
> Needs a server that can carry headers and run the request's shutdown  
> functions in this mode -- qbix-webserver v0.0.4.25 or later. On older  
> versions a sign-in is accepted and then forgotten: the response carries  
> neither the session cookie nor the redirect.  
>
> Set to disabled on 2026-09-30, for the time one request takes rather than  
> how many sixteen concurrent ones get through. A fresh worker starts every  
> request cold: a new database connection (40-70 ms on Oracle, where a  
> persistent one is reused in 0.5 ms), empty stat and realpath caches, and  
> nothing the kernel keeps per process. Signed-in admin pages took 256-284 ms  
> against Apache's 60 ms; with persistent workers 73-80 ms, and a publish went  
> from 2.0 s to 1.4 s. The state a request leaves is cleared after the response  
> has gone out, so it costs the next request nothing on a quiet server.  
> Persistent workers need the server to set REQUEST\_TIME\_FLOAT per request  
> (the kernel keys per-request caches on it), which it does from the  
> 2026-09-30 engine fix on (Velocity v0.0.4.42 or later).  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
ForkPerRequest=disabled
*/ ?>
```

## StreamTimeout

> Streaming responses (Server-Sent Events). A response with Content-Type  
> text/event-stream or X-Accel-Buffering: no is sent piece by piece, on every  
> flush, from the persistent workers too (Velocity after v0.0.4.48; older  
> engines send it whole at the end and ignore these settings).  
>
> StreamTimeout: seconds a stream, or a request with Accept: text/event-stream  
> before its first byte, may run instead of the request timeout (30 s);  
> then the stream ends cleanly and an EventSource reconnects. 0 = no limit.  
> Empty = the engine's default, 300.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StreamTimeout=
*/ ?>
```

## StreamShare

> Each open stream holds one worker. StreamShare is the share of Workers that  
> event streams may hold at once (engine default 0.25, at least one);  
> MaxStreams the same as a number of workers, used instead when above 0. Past  
> the cap an event stream is answered 503 with Retry-After, so pages always  
> keep the rest of the pool. Empty = the engine's default.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StreamShare=
*/ ?>
```

## MaxStreams

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
MaxStreams=
*/ ?>
```

## RetryWithin

> Seconds after a request reached its worker within which a GET whose worker  
> died without answering is run again, once (never an event stream, never a  
> response that had started). Empty = the engine's default, 10.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
RetryWithin=
*/ ?>
```

## GraphQLWebSocketPaths

> GraphQL subscriptions over a WebSocket (the protocol graphql-transport-ws;  
> doc/eng-us/features/6.0/graphql.md, "Subscriptions"). On these paths the  
> server accepts the WebSocket itself and opens the kernel's event stream of  
> the same path for each subscription, through the workers: each open  
> subscription is one stream (StreamShare and StreamTimeout above hold for it;  
> when a stream ends after graphql.ini SubscriptionMaxDuration the server  
> opens it again where it stopped). No path: no WebSocket. Needs an engine with  
> the bridge (Velocity after v0.0.4.57); an older one ignores these settings.  

Default: a list of 1: `/api/graphql/subscribe`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
# an empty "GraphQLWebSocketPaths[]" first replaces the shipped list; without it the items are added
GraphQLWebSocketPaths[]
GraphQLWebSocketPaths[]=/api/graphql/subscribe
*/ ?>
```

## GraphQLWebSocketInitTimeout

> Seconds a client has to send connection\_init (empty: 10), a connection may  
> stay open without a subscription (empty: 60), between two pings to the  
> client (empty: 30; 0: none), and the subscriptions one connection may hold  
> (empty: 20).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketInitTimeout=
*/ ?>
```

## GraphQLWebSocketIdleTimeout

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketIdleTimeout=
*/ ?>
```

## GraphQLWebSocketPingInterval

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketPingInterval=
*/ ?>
```

## GraphQLWebSocketMaxSubscriptions

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketMaxSubscriptions=
*/ ?>
```

## EnginePhar

> Run the engine from an archive instead of the files on disk.  
>
> The bootstrap loads the engine out of dist/engine.phar only when the  
> EXP\_ENGINE\_PHAR environment variable names an archive that exists. Nothing  
> used to set it, so the archive could be built and inspected but never run --  
> testing it meant exporting the variable by hand into whatever started the  
> server, and Setup > System information could only ever say "Individual files  
> on disk".  
>
>   disabled   the files on disk. The default, and what a normal install wants.  
>   enabled    dist/engine.phar, wherever expPhar puts it.  
>   <path>     that archive, relative to the installation root or absolute.  
>
> Build or refresh the archive first with bin/php/phar.php build. A path that  
> does not exist stops the server from starting rather than quietly falling  
> back, because a silent fall back is indistinguishable from success.  
>
> Measured on this installation, an archive was within noise of the files on  
> disk once the opcode cache was warm. It is worth having for distribution and  
> for knowing the engine is exactly what was built, not for speed.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
EnginePhar=disabled
*/ ?>
```

## StaticMaxAge

> How long a browser may keep a stylesheet, script or image without asking  
> again, in seconds. 0 leaves the server's own default, which permits caching  
> and then makes the client revalidate anyway -- so a page with 41 assets costs  
> 41 conditional requests on every view.  
>
> A year is what the other web server in front of this installation already  
> sends for the same files. The trade is that a file replaced in place is not  
> noticed until the lifetime expires; clear a browser's cache, or lower this,  
> while working on a stylesheet.  

Default: `31536000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StaticMaxAge=31536000
*/ ?>
```

## HTTP2

> Speak HTTP/2 to clients that ask for it during the TLS handshake.  
>
> A page on this installation references 41 assets. Over HTTP/1.1 a browser  
> fetches them about six at a time in waves; over HTTP/2 they share one  
> connection. Measured here, asset delivery was 393-418ms against 85-104ms  
> through a front end that spoke it, on identical bytes.  
>
> The protocol is agreed during the handshake and a client does not fall back  
> afterwards, so a fault would be a page that never arrives rather than one  
> that arrives slowly. That is why the server answers any stream still open  
> with a readable page instead of closing silently, and why this is a switch  
> rather than the default.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
HTTP2=enabled
*/ ?>
```

## HTTP2ErrorImage

> The picture on that page. Empty uses the one the server draws, which is  
> inline and needs no second request -- the page appears exactly when the  
> connection carrying it is unreliable. Point this at an svg, png or jpg to  
> change it, or set the server's own errorPage.image to "none" for no picture.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
HTTP2ErrorImage=
*/ ?>
```

## ResponseHeaders

> Headers added to every answer the server builds itself: static files,  
> resized images, its own error pages, redirects and 304 answers. The same  
> four the Apache and nginx front ends of this installation send on every  
> answer ("Header always set", "add\_header ... always"), which the server did  
> not send on a static file at all. One "Name: value" per line. A header an  
> answer already carries is never replaced or sent twice. An empty list  
> (ResponseHeaders\[\] alone) adds none.  
>
> Framing headers (Content-Length, Content-Type, Connection, Date ...) cannot  
> be set here, and Strict-Transport-Security has its own setting, HSTSMaxAge  
> in \[HTTPSSettings\], because it must never go out over plain HTTP.  

Default: a list of 4: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy: camera=(), microphone=(), payment=(), usb=()`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
# an empty "ResponseHeaders[]" first replaces the shipped list; without it the items are added
ResponseHeaders[]
ResponseHeaders[]=X-Content-Type-Options: nosniff
ResponseHeaders[]=X-Frame-Options: SAMEORIGIN
ResponseHeaders[]=Referrer-Policy: strict-origin-when-cross-origin
ResponseHeaders[]=Permissions-Policy: camera=(), microphone=(), payment=(), usb=()
*/ ?>
```

## ResponseHeadersOnScripts

> Also add them to what index.php, index\_rest.php and index\_treemenu.php  
> answer, each one only when the script did not send that header itself: a  
> page that sets its own X-Frame-Options keeps it. The kernel sends these four  
> on its pages already; REST answers and the tree menu carry none of their  
> own. enabled or disabled.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
ResponseHeadersOnScripts=enabled
*/ ?>
```

## PidFile

Default: `var/vc/qbix/run/server.pid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
PidFile=var/vc/qbix/run/server.pid
*/ ?>
```

## LogFile

Default: `var/vc/qbix/run/console.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
LogFile=var/vc/qbix/run/console.log
*/ ?>
```

## Related

- [All blocks of velocity.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/velocity.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
