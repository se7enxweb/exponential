---
title: Velocity neben Apache oder nginx
audience: hosting, admin
category: hosting
order: 120
summary: Die Formen, in denen Velocity und ein klassischer Webserver sich eine Maschine oder eine Installation teilen - ein Proxy vor Velocity, beide liefern während einer Migration dieselben Dateien aus, und ein A/B-Vergleich, mit den Regeln für Vertrauen, Eigentum, Sitzungen und Ausrollen, die jede Form braucht.
keywords: Velocity, Apache, nginx, Reverse Proxy, ProxyPass, proxy_pass, X-Forwarded-Proto, X-Forwarded-For, TrustedProxies, Q.webserver.proxy.trusted, nebeneinander, Migration, Eigentum an Dateien, Sitzungen, PhpFpmService, Ausrollen, Let's Encrypt hinter Proxy, reverse proxy, side by side, migration, file ownership, sessions, deploy, Let's Encrypt behind proxy
type: help
---
# Velocity neben Apache oder nginx

Diese Seite ist für Systemadministratoren bei Hosting-Unternehmen, deren Maschinen bereits Apache oder nginx betreiben und die Velocity
hinzufügen. Sie beantwortet: welche Formen funktionieren, was jede braucht (vertrauenswürdige Proxys, ein Benutzer für die Dateien,
geteilte Sitzungen, ein Ausrollen) und wie Sie prüfen, dass sich ein weitergeleiteter Header nicht fälschen lässt. Die Quellen sind
[Kapitel 8, Abschnitte 8.7 und 8.8](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy) und
[Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../../../eng-us/bc/6.0/trusted-proxies.md).

## Inhalt

1. [Die drei Formen](#die-drei-formen)
2. [Form 1: ein Proxy vor Velocity](#form-1-ein-proxy-vor-velocity)
3. [Wer dem Proxy vertraut](#wer-dem-proxy-vertraut)
4. [Form 2: beide liefern dieselbe Installation aus](#form-2-beide-liefern-dieselbe-installation-aus)
5. [Form 3: nebeneinander für einen Vergleich](#form-3-nebeneinander-für-einen-vergleich)
6. [Zertifikate hinter einem Proxy](#zertifikate-hinter-einem-proxy)
7. [Prüfungen](#prüfungen)
8. [Checkliste](#checkliste)

## Die drei Formen

| Form | Wann | Woran Velocity sich bindet |
|---|---|---|
| 1. Apache oder nginx vor Velocity | eine öffentliche Adresse trägt mehrere Dienste; Velocity-Instanzen mehrerer Kunden hinter einem Frontserver; Velocity muss als gewöhnlicher Benutzer auf einem hohen Port laufen | `Host=127.0.0.1` (ausgeliefert), ein hoher Port |
| 2. Velocity und Apache/nginx liefern dieselben Dateien aus | eine Migration von PHP-FPM zu Velocity, mit dem alten Server auf denselben Dateien belassen | eigene Ports |
| 3. Beide auf eigenen Ports für einen Test | Vergleich von Engines auf den eigenen Seiten des Kunden | eigene Ports |

Velocity braucht für TLS keinen Proxy; es liefert HTTPS selbst aus. Ein Proxy ist dennoch die richtige Form für die Fälle oben oder wenn
ein Load Balancer oder CDN TLS bereits beendet.

## Form 1: ein Proxy vor Velocity

**nginx vor Velocity** (abgeleitetes Beispiel aus Kapitel 8):

```nginx
location / {
    proxy_pass http://127.0.0.1:8088;
    proxy_set_header Host              $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    # appending is safe: Exponential reads the list from the right
    proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
    proxy_http_version 1.1;
}
```

**Apache vor Velocity** (abgeleitetes Beispiel; braucht `mod_proxy`, `mod_proxy_http` und `mod_headers`):

```apache
ProxyPreserveHost On
ProxyPass        / http://127.0.0.1:8088/
ProxyPassReverse / http://127.0.0.1:8088/
RequestHeader set X-Forwarded-Proto "https"
```

Für mehrere Kunden auf einer Maschine geben Sie der Velocity jedes Kunden einen eigenen Port und einen eigenen Worker-Benutzer, und einen
virtuellen Host des Frontservers pro Kunde, der auf diesen Port zeigt. Der Proxy gibt den ursprünglichen Header `Host` weiter; Velocity
nutzt `X-Forwarded-Host` nicht.

## Wer dem Proxy vertraut

Zwei Listen, weil Velocity entscheidet, bevor irgendein PHP der Anwendung läuft:

| Liste | Wo | Standard | Entscheidet |
|---|---|---|---|
| `Q.webserver.proxy.trusted` | die Konfiguration der Engine (ein Snippet in `/etc/vc/conf-available/`) | `127.0.0.1`, `::1` | unter Velocity: die Adresse des Besuchers und ob die Anfrage HTTPS ist |
| `[HTTPHeaderSettings] TrustedProxies[]` | `settings/override/site.ini.append.php` | `127.0.0.1`, `::1` | unter Apache oder nginx mit PHP-FPM: welchen weitergeleiteten Headern der Kernel glaubt |

Ein Proxy auf derselben Maschine ist von beiden Standards abgedeckt. Ein Proxy auf einer anderen Adresse muss hinzugefügt werden. Für
Velocity:

```bash
cat > /etc/vc/conf-available/reverse-proxy.conf <<'EOF'
{ "Q": { "webserver": { "proxy": { "trusted": ["127.0.0.1", "::1", "192.0.2.10", "10.0.0.0/8"] } } } }
EOF
php bin/php/console exp:velocity conf enable reverse-proxy --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

Von einem vertrauenswürdigen Proxy nimmt Velocity die Adresse des Besuchers aus `X-Forwarded-For` (von rechts gelesen) und übergibt sie dem
Kernel als `REMOTE_ADDR`, sodass `ClientIpByCustomHTTPHeader` unter Velocity nicht nötig ist. Es behandelt eine einfache Verbindung als
HTTPS, wenn ein vertrauenswürdiger Proxy `X-Forwarded-Proto: https`, `CloudFront-Forwarded-Proto: https` oder
`CF-Visitor: {"scheme":"https"}` sendet.

> **Warnung**
> Dass das weitergeleitete Protokoll nur von vertrauenswürdigen Proxys gelesen wird, kam mit Exponential Velocity **0.0.4.44**. Bis
> einschließlich 0.0.4.43 konnte ein Besucher auf einem einfachen Listener eine Anfrage wie HTTPS aussehen lassen. Prüfen Sie mit
> `composer show se7enxweb/exponential-velocity`; halten Sie bei einer älteren Engine den einfachen Port von Velocity von außen
> unerreichbar, solange ein Proxy davor steht ([Kapitel 8, Abschnitt 8.8](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).

## Form 2: beide liefern dieselbe Installation aus

Wenn Apache (PHP-FPM) und Velocity dieselbe Installation ausliefern, teilen sie `var/`, die Caches und die Sitzungen
([Kapitel 8, Ein Benutzer oder eine Gruppe](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)):

- **Ein Benutzer.** Geben Sie dem PHP-FPM-Pool und den Workern von Velocity denselben Benutzer (`[ServerSettings] User` und `Group`) oder
  eine gemeinsame Gruppe mit für die Gruppe beschreibbaren Verzeichnissen mit setgid und den Berechtigungen in `[FileSettings]` aus
  Kapitel 8. Testen Sie, während beide laufen, und sehen Sie sich nach einigen Anfragen den Eigentümer neuer Dateien in `var/` an.
- **Ein Ausrollen.** `exp:velocity deploy` lädt das PHP-FPM neu, das die Website ausliefert (`[DeploySettings] PhpFpmService=auto` findet
  es), und startet Velocity neu, dann leert es die Caches der gerenderten Ausgabe. Setzen Sie in dieser Form nicht
  `PhpFpmService=disabled`.
- **Sitzungen.** Mit eingeschaltetem rollenbewusstem HTTP-Cache müssen beide Server Sitzungen im selben Verzeichnis halten: das
  `session.save_path` des PHP-FPM-Pools und `[PHPSettings] IniOptions[]=session.save_path=0;0660;<dir>` von Velocity
  ([HTTP-Cache](../../../../eng-us/bc/6.0/httpcache.md#switching-it-on)).
- **Caches.** Das Leeren eines Caches unter **Setup > Caches** erreicht beide, weil die Dateien geteilt sind. Zwei Dinge nicht: Velocity
  hält Einstellungen im Speicher (starten Sie es nach dem Leeren der INI-Caches neu) und beantwortet Seiten aus seinem eigenen
  Antwort-Cache (`exp:velocity cache clear`) ([Caches](../../../anleitungen/caches.md#4-velocity)).
- **OPcache und APCu** gehören zu den eigenen Prozessen jedes Servers.

## Form 3: nebeneinander für einen Vergleich

Jede Engine hat ihren eigenen Port, ihre eigene PID-Datei und ihre eigenen Logs, sodass Velocity für einen Test neben dem vorhandenen
Server laufen kann. Messen Sie dann beide direkt nacheinander:

```bash
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --cold --runs=30 --allow-root-user
```

Vergleichen Sie nur Zeilen mit derselben Antwort in `cache`: Ein Cache-Treffer gegen ein Rendering ist kein Vergleich von Engines
([Eine gehostete Website messen](../leistung/benchmark-einer-gehosteten-website.md)).

## Zertifikate hinter einem Proxy

Der Proxy besitzt Port 80, sodass die ACME-Challenge HTTP-01 von Velocity es nicht direkt erreichen kann. Lassen Sie entweder den Proxy
das Zertifikat beziehen, oder leiten Sie `/.well-known/acme-challenge/` an Velocity weiter, oder setzen Sie `acme.webroot` der Engine auf
ein Verzeichnis, das der Proxy ausliefert ([HTTPS-Zertifikate in Velocity](https-zertifikate-in-velocity.md)).

## Prüfungen

Ein weitergeleiteter Header, den ein Besucher sendet, darf nichts ändern. Direkt gegen die Website (ohne Proxy):

```bash
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
# expected: http://example.com/   -- an https:// address here means the header was believed
```

Über den Proxy müssen absolute Links und Umleitungen `https://` tragen, und `Is my address listed?` in der Debug-Leiste zeigt die Adresse,
die der Kernel für den Besucher nutzt.

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Umleitungen und absolute Links gehen auf `http://` | dem Proxy wird nicht vertraut, oder er sendet kein `X-Forwarded-Proto` | ihn aufführen; den Header im Proxy setzen |
| jeder Besucher hat die Adresse des Proxys | dem Proxy wird nicht vertraut (oder, unter PHP-FPM, `ClientIpByCustomHTTPHeader` nicht gesetzt) | den Proxy aufführen; die Header-Einstellung setzen |
| eine Umleitungsschleife nach dem Wechsel auf HTTPS | der Proxy spricht HTTPS mit dem Besucher und einfaches HTTP mit dem Server, und der Server weiß es nicht | wie in der ersten Zeile |
| Dateien, die root gehören, erscheinen in `var/` | ein Skript oder ein Server läuft als root | alles als Benutzer der Website ausführen |

## Checkliste

- [ ] Velocity an `127.0.0.1` gebunden, wenn ein Proxy davor steht; sein einfacher Port von außen nicht erreichbar.
- [ ] Engine 0.0.4.44 oder neuer; der Proxy in `Q.webserver.proxy.trusted` aufgeführt (und in `TrustedProxies[]` für PHP-FPM).
- [ ] Der Proxy setzt `X-Forwarded-Proto` und gibt `Host` weiter; die Prüfung mit curl besteht.
- [ ] In Form 2: ein Benutzer für beide Server; `deploy --dry-run` zeigt das Neuladen von FPM und den Neustart von Velocity.
- [ ] Weg für das Zertifikat entschieden (Proxy, weitergeleitete Challenge oder `acme.webroot`).

## Verwandte Seiten

- [Velocity für Hoster](velocity-fuer-hoster.md)
- [Varnish und Reverse Proxys](../leistung/varnish-und-reverse-proxys.md)
- [Apache mit PHP-FPM](apache-mit-php-fpm.md)
- [nginx mit PHP-FPM](nginx-mit-php-fpm.md)
- [Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../../../eng-us/bc/6.0/trusted-proxies.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
