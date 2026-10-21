# Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut (6.0.15)

Ab dem 5. Oktober 2026 glaubt der Kernel den weitergeleiteten Anfrage-Headern nur dann, wenn die Verbindung von einem
Proxy kommt, der in der neuen Einstellung `site.ini [HTTPHeaderSettings] TrustedProxies[]` aufgeführt ist. Diese
Seite beschreibt, was sich geändert hat, wer die Liste setzen muss und wie, für die üblichen vorgeschalteten Server.

## Was sich geändert hat

`X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Port`, `X-Forwarded-Server` und `X-Forwarded-Host` sind
gewöhnliche Anfrage-Header. Ein Proxy schreibt sie, um der Anwendung mitzuteilen, was der Besucher angefordert hat,
aber jeder Besucher kann sie ebenfalls senden. Das Einzige, was der Server sicher weiß, ist die Adresse der Gegenstelle,
die die Verbindung geöffnet hat, `REMOTE_ADDR`; deshalb entscheidet jetzt diese.

| | Vorher | Jetzt |
|---|---|---|
| `eZSys::isSSLNow()` | `X-Forwarded-Proto: https` (sonst `X-Forwarded-Port` gleich `SSLPort`, sonst `X-Forwarded-Server` gleich `SSLProxyServerName`) von **beliebigen** Absendern machte die Anfrage zu HTTPS. | Dieselben Header zählen nur, wenn `REMOTE_ADDR` ein vertrauenswürdiger Proxy ist. `$_SERVER['HTTPS']` und der Port werden wie bisher zuerst geprüft und brauchen keinen Proxy. |
| `eZSys::hostname()` | Der erste Eintrag von `X-Forwarded-Host` von **beliebigen** Absendern ersetzte `Host`. | `X-Forwarded-Host` zählt nur von einem vertrauenswürdigen Proxy; sonst wie bisher `Host`, dann `SiteURL`. |
| `eZSys::clientIP()` mit `ClientIpByCustomHTTPHeader=X-Forwarded-For` | Die Adresse **ganz links** im Header, also der Teil, den der Besucher schreibt. | Nur von einem vertrauenswürdigen Proxy, und **von rechts** gelesen: Vertrauenswürdige Proxys werden übersprungen, und die erste Adresse, die keiner ist, ist der Besucher. Alles weiter links wird ignoriert. Jeder Eintrag wird mit `filter_var( FILTER_VALIDATE_IP )` geprüft; ein Eintrag, der keine Adresse ist, beendet den Durchlauf beim letzten vertrauenswürdigen Proxy. Sonst `REMOTE_ADDR`. |
| Lookup des HTTP-Caches (`httpcache.ini`) | Ermittelte Schema und Host aus denselben Headern, von beliebigen Absendern. | Folgt dem Kernel: Der Vertrag enthält die vertrauenswürdige Liste (`trustedProxies`), und der Lookup glaubt den Headern nur, wenn er `REMOTE_ADDR` kennt und diese Adresse vertrauenswürdig ist. |
| Debug-Leiste, „iptest“ | `trusts_proxy` war immer wahr, wenn `ClientIpByCustomHTTPHeader` gesetzt war. | Nur wahr, wenn die Anfrage außerdem von einem vertrauenswürdigen Proxy kam. |

Warum das wichtig ist: Ein Besucher konnte den Kernel glauben lassen, eine einfache HTTP-Anfrage sei HTTPS (absolute
URLs, Weiterleitungen, `CookieSecure=auto`, alles, was HTTPS verlangt, und Cache-Schlüssel, die das Schema
enthalten), konnte einen anderen Hostnamen in jede absolute URL bringen, die der Kernel erzeugt, und konnte die
Adresse wählen, die `DebugByIP`, `[UserSettings] TrustedIPList` (die Anmeldesperre), Anfrageregeln nach Netzwerk, das
Audit-Protokoll und das Einwilligungsprotokoll der E-Mail-Einstellungen festhalten.

Ist ein Header eine Liste, an die Proxys anhängen, wird der Wert verwendet, den der **äußerste vertrauenswürdige**
Proxy geschrieben hat, von rechts gezählt mit der Zahl der vertrauenswürdigen Hops, die in `X-Forwarded-For` zu
sehen sind. Ein Proxy, der den Header *setzt* (ein Wert), wird wie bisher gelesen. Das verhindert, dass ein Wert
verwendet wird, den der Besucher vor den eigenen Wert eines Proxys gestellt hat.

`$_SERVER['HTTPS']` ist die eigene Antwort des Webservers und nicht betroffen: Apache, nginx, PHP-FPM, FrankenPHP
und Velocity setzen es, und der Kernel glaubt es ohne jede Proxy-Einstellung. Velocity setzt es auf seinem
TLS-Listener und, ab 0.0.4.44, für einen vertrauenswürdigen Proxy seiner eigenen Liste, der HTTPS weiterleitet (siehe
„Exponential Velocity“ unten).

## Die Einstellung

```ini
[HTTPHeaderSettings]
# Default, in settings/site.ini
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
```

- Einträge sind IP-Adressen oder Bereiche der Form Adresse/Präfix, IPv4 und IPv6: `192.0.2.10`, `10.0.0.0/8`,
  `2001:db8::/32`. Eine IPv4-Gegenstelle, die als IPv4-gemappte IPv6-Adresse gemeldet wird (`::ffff:10.0.0.5`, was
  ein Dual-Stack-Socket liefert), passt zu ihrem IPv4-Eintrag. Hostnamen werden nicht aufgelöst: Ein Eintrag, der
  weder Adresse noch Bereich ist, wird ignoriert, sodass ein Tippfehler niemandem vertraut statt jemandem, der nicht
  gemeint war.
- Ein Override, das mit `TrustedProxies[]` allein auf einer Zeile beginnt, ersetzt die Liste. `TrustedProxies[]`
  allein, ohne Eintrag danach, vertraut überhaupt keinem Proxy.
- Fehlt die Einstellung ganz (eine alte `site.ini`, die über die ausgelieferte kopiert wurde), verwendet der Kernel
  denselben Loopback-Standard.
- `ClientIpByCustomHTTPHeader` ist unverändert: Es nennt weiterhin den Header, aus dem die Adresse des Besuchers
  gelesen wird (`X-Forwarded-For` oder ein Header mit einer einzigen Adresse wie `X-Real-IP` oder
  `CF-Connecting-IP`), und ist standardmäßig weiterhin `false`. Es wirkt jetzt nur bei Anfragen von einem
  vertrauenswürdigen Proxy.
- `[SiteSettings] SSLProxyServerName` ist unverändert und zählt wie `X-Forwarded-Server` nur von einem
  vertrauenswürdigen Proxy.

### Warum Loopback standardmäßig vertrauenswürdig ist

Nichts außerhalb der Maschine kann eine Verbindung von `127.0.0.1` oder `::1` öffnen, daher steht der Standard nur
für einen Proxy auf demselben Host: einen TLS-Terminator, Varnish oder einen lokalen Webserver vor PHP. Das ist die
häufigste Form einer Installation hinter einem Proxy, und diese Websites funktionieren nach dem Upgrade ohne
Änderung der Einstellungen weiter. Einem Proxy an anderer Stelle (eine andere Maschine, ein Load Balancer, ein CDN)
wurde nie stillschweigend vertraut; er muss aufgeführt werden.

Einem Proxy zu vertrauen heißt, ihm zu vertrauen, dass er diese Header **selbst setzt** und nicht weitergibt, was der
Besucher gesendet hat. Ein lokaler Proxy, der das `X-Forwarded-Proto` eines Besuchers unverändert weiterleitet,
würde den Wert des Besuchers durchreichen. Haben Sie einen solchen Proxy und können ihn nicht ändern, setzen Sie
`TrustedProxies[]` leer.

## Wer handeln muss

- **Kein vorgeschalteter Proxy** (Apache oder nginx mit PHP-FPM, Velocity liefert die Website direkt aus): Nichts zu
  tun. Die weitergeleiteten Header, die ein Besucher sendet, werden jetzt ignoriert, und genau darum geht es.
- **Ein Proxy auf derselben Maschine** (nginx oder Apache vor Velocity oder PHP-FPM auf `127.0.0.1`, Varnish, das
  nginx von Plesk vor Apache): Nichts zu tun, solange der Proxy `X-Forwarded-Proto` (und `X-Forwarded-For`, wenn Sie
  es auslesen) selbst setzt. Prüfen Sie es mit dem Test am Ende dieser Seite.
- **Ein Proxy, Load Balancer oder CDN unter einer anderen Adresse**: Fügen Sie seine Adressen oder Bereiche zu
  `TrustedProxies[]` in `settings/override/site.ini.append.php` hinzu. Bis dahin behandelt der Kernel Anfragen als
  einfaches HTTP von der Adresse des Proxys; das zeigt sich an absoluten URLs mit `http://`, Weiterleitungen auf
  `http://` und daran, dass jeder Besucher die Adresse des Proxys hat.
- **Websites, die `ClientIpByCustomHTTPHeader` setzen**: Die Adresse des Besuchers ist jetzt der Eintrag ganz rechts,
  der nicht vertrauenswürdig ist, nicht der ganz links. Hinter einem Proxy, der anhängt
  (`$proxy_add_x_forwarded_for`, Apache `mod_proxy`), ist das Ergebnis für ehrliche Besucher dasselbe und lässt sich
  nicht mehr fälschen. Hinter mehreren Proxys führen Sie alle auf.

## Beispiele

### Apache davor (mod_proxy)

`mod_proxy` von Apache hängt an `X-Forwarded-For` an und setzt `X-Forwarded-Host` und `X-Forwarded-Server`, setzt
aber nicht `X-Forwarded-Proto`; setzen Sie es, damit der Wert ersetzt wird, den ein Besucher gesendet hat:

```apache
ProxyPreserveHost On
ProxyPass        / http://127.0.0.1:8088/
ProxyPassReverse / http://127.0.0.1:8088/
RequestHeader set X-Forwarded-Proto "https"
```

Auf derselben Maschine deckt die Standardliste das bereits ab. Von einer anderen Maschine aus:

```ini
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=192.0.2.10
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

### nginx davor

```nginx
location / {
    proxy_pass http://127.0.0.1:8088;
    proxy_set_header Host              $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
    proxy_http_version 1.1;
}
```

Das Anhängen (`$proxy_add_x_forwarded_for`) ist jetzt sicher: Der Kernel liest von rechts. `$remote_addr`, das den
Header ersetzt, funktioniert ebenfalls.

### Apache mit PHP-FPM hinter einem Proxy, der REMOTE_ADDR umschreibt

Plesk stellt nginx vor Apache, und `mod_remoteip` von Apache (`RemoteIPHeader X-Forwarded-For`,
`RemoteIPInternalProxy 127.0.0.1`) setzt `REMOTE_ADDR` auf den Besucher; nginx spricht mit Apache über HTTPS, daher
setzt Apache `HTTPS=on`. Der Kernel sieht dann den Besucher als Gegenstelle und braucht keinen weitergeleiteten
Header: Es ist nichts zu konfigurieren, und `ClientIpByCustomHTTPHeader` kann `false` bleiben. Dasselbe gilt für das
Modul `realip` von nginx (`set_real_ip_from`, `real_ip_header`).

### Exponential Velocity

Velocity ermittelt die Adresse des Besuchers selbst, anhand seiner eigenen Liste vertrauenswürdiger Proxys
(`Q.webserver.proxy.trusted`, in `/etc/vc/conf-available/reverse-proxy.conf`; standardmäßig `127.0.0.1` und `::1`),
und übergibt diese Adresse der Anwendung als `REMOTE_ADDR`. Konfigurieren Sie Proxys vor Velocity **dort**. Der
Kernel sieht dann den Besucher als Gegenstelle, daher gilt:

- `ClientIpByCustomHTTPHeader` wird nicht gebraucht, und `TrustedProxies[]` wirkt sich nicht auf die Adresse des
  Besuchers aus.
- `$_SERVER['HTTPS']` (und `REQUEST_SCHEME`) wird von der Engine gesetzt, und der Kernel nimmt es als Antwort des
  Servers: `on` auf dem TLS-Listener von Velocity (`https://example.com:8080/`), von jedem Client. Seit Velocity
  **0.0.4.44** ist es auch `on` für eine Anfrage auf dem einfachen Listener, wenn die Gegenstelle in
  `Q.webserver.proxy.trusted` steht und `X-Forwarded-Proto: https` sendet (oder den in
  `Q.webserver.proxy.headers.proto` genannten Header; bei einer Liste den ersten Eintrag),
  `CloudFront-Forwarded-Proto: https` oder `CF-Visitor: {"scheme":"https"}`. Von jeder anderen Gegenstelle werden
  diese Header ignoriert. Bis einschließlich 0.0.4.43 las die Engine sie von jedem Client; halten Sie bei einer
  solchen Engine den einfachen Port nur über den Proxy erreichbar (das ausgelieferte `Host=127.0.0.1`) und
  aktualisieren Sie mit `composer require se7enxweb/exponential-velocity:~0.0.4.44`.
- `X-Forwarded-Host` erreicht den Kernel von der Adresse des Besuchers und wird nicht verwendet. Lassen Sie den Proxy
  den ursprünglichen `Host` weitergeben (`ProxyPreserveHost On`, `proxy_set_header Host $host`).
- Dem Lookup des HTTP-Caches in der Engine wird die Adresse der Gegenstelle nicht mitgeteilt, daher ignoriert auch er
  die weitergeleiteten Header; das entspricht dem, was der Kernel speichert. Eine Engine, die `remoteAddr` an den
  Cache übergibt, wird genauso behandelt wie der Kernel.

### Load Balancer in der Cloud und CDNs

Führen Sie die Bereiche auf, die der Anbieter veröffentlicht, und nur diese:

```ini
[HTTPHeaderSettings]
TrustedProxies[]
# the load balancer's subnet (AWS ALB, GCP, Azure, Hetzner ...)
TrustedProxies[]=10.0.0.0/16
# Cloudflare, two of its published ranges
TrustedProxies[]=173.245.48.0/20
TrustedProxies[]=2400:cb00::/32
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

Kommentare stehen auf eigenen Zeilen: Nach einem Wert wird ein einzelnes `#` als Teil des Wertes gelesen, und ein
solcher Eintrag ist keine Adresse und wird ignoriert.

- Die Load Balancer von AWS ALB, Google Cloud und Azure hängen an `X-Forwarded-For` an und setzen
  `X-Forwarded-Proto`; vertrauen Sie dem Subnetz, aus dem die privaten Adressen des Load Balancers kommen.
- Cloudflare hängt an `X-Forwarded-For` an und sendet außerdem `CF-Connecting-IP` (eine Adresse); beide eignen sich
  als `ClientIpByCustomHTTPHeader`. Seine Bereiche ändern sich von Zeit zu Zeit: Übernehmen Sie sie aus der
  veröffentlichten Liste von Cloudflare.
- Ein CDN vor einem Load Balancer: Führen Sie beide auf; der Durchlauf von rechts geht durch alle.
- Stellen Sie sicher, dass der Ursprungsserver nicht am Load Balancer vorbei erreichbar ist (Firewall,
  Sicherheitsgruppe), sonst spricht ein Besucher direkt mit PHP, und der Kernel ignoriert die Header, zu Recht.

## So prüfen Sie es

Ist die Website direkt erreichbar (kein Proxy), dürfen die Header eines Besuchers nichts ändern:

```bash
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
# http://example.com/   (before: https://example.com/)
```

Hinter einem Proxy zeigt „Is my address listed?“ (iptest) in der Debug-Leiste `REMOTE_ADDR`, die Adresse, die der
Kernel verwendet, und ob der Proxy-Header gelesen wurde. `eZSys::trustedProxies()` gibt die wirksame Liste zurück;
`eZSys::isFromTrustedProxy()` sagt, ob die aktuelle Anfrage von einem solchen Proxy kam.

## Code

- `lib/ezutils/classes/eztrustedproxy.php` (`eZTrustedProxy`): Abgleich von Adressen und Bereichen, der Durchlauf
  von rechts nach links, die Zahl der Hops. Unabhängig von jeder anderen Klasse, sodass der frühe Ausstieg des
  HTTP-Caches und der Prozess von Velocity sie laden können.
- `lib/ezutils/classes/ezsys.php`: `isSSLNow()`, `hostname()`, `clientIP()`, die neuen `trustedProxies()` und
  `isFromTrustedProxy()`.
- `kernel/private/classes/httpcache/ezphttpcachecontract.php` und `ezphttpcachelistener.php`: die Sicht des Lookups
  auf Schema und Host.
- Tests: `tests/tests/lib/ezutils/eZTrustedProxyTest.php`, `eZSysTrustedProxyTest.php`, `eZSysTest.php` sowie HC-15
  und HC-16 in `tests/tests/kernel/classes/httpcache/ezpHttpCacheContractTest.php` (Suiten `lib` und
  `kernel-classes`).
