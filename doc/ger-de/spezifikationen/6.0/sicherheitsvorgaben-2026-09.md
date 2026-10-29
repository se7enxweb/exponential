# Spezifikation: Sicherheitsvorgaben vom September 2026

Zwischen dem 22. und 30. September 2026 wurde eine Reihe von Standardwerten geändert, damit eine **Standardinstallation**
ohne zusätzlichen Aufwand sicher ist. Diese Seite führt jeden Standardwert auf, die Einstellung, die ihn steuert, was sich
für eine bestehende Installation geändert hat und wie Sie es prüfen. Lesen Sie sie, bevor Sie eine Installation mit eigener
`.htaccess`, einem cachenden Proxy oder Skripten, die das Sitzungs-Cookie lesen, aktualisieren; die
[Upgrade-Checkliste](#upgrade-checkliste) am Ende ist die Kurzfassung. Die Eingabeverarbeitung einzelner Datentypen steht in
[Härtung von Datentypen und Eingaben](datentyp-eingabehaertung.md).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).

## Einstellungen im Überblick

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/site.ini` | `HTTPHeaderSettings` | `SecurityHeaders[<header>]` | siehe [Abschnitt 1](#1-sicherheits-header-auf-jeder-seite) | Installation oder Siteaccess |
| `settings/site.ini` | `Session` | `CookieSecure` | `auto` | Installation oder Siteaccess |
| `settings/site.ini` | `Session` | `CookieHttponly` | `true` | Installation oder Siteaccess |
| `settings/site.ini` | `Session` | `CookieSameSite` | `Lax` | Installation oder Siteaccess |
| `settings/httpcache.ini` | `HttpCacheSettings` | `TagHeader` | `disabled` (ersetzt `ProxyHeaders`) | Installation |
| `settings/velocity.ini` | `ServerSettings` | `ResponseHeaders[]`, `ResponseHeadersOnScripts` | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` mit den Werten aus [Abschnitt 1](#1-sicherheits-header-auf-jeder-seite); `enabled` | Velocity |
| `settings/velocity.ini` | `HTTPSSettings` | `HSTSMaxAge`, `HSTSIncludeSubDomains`, `HSTSPreload` | `300` (`0` schreibt nichts), `disabled`, `disabled` | Velocity |
| `settings/velocity.ini` | `CacheSettings` | `SkipCookies[]` | leer (die Liste wird abgeleitet) | Velocity |
| `settings/velocity.ini` | `ServerSettings` | `User`, `Group`, `AllowRootWorkers` | `AllowRootWorkers=disabled` | Velocity |

Die drei `[Session]`-Schlüssel sind in der mitgelieferten `settings/site.ini` auskommentiert; solange sie nicht gesetzt
sind, gelten die oben genannten Standardwerte.

## 1. Sicherheits-Header auf jeder Seite

Vor dem 27. September beantwortete eine Standardinstallation jede Seite, die
Administration eingeschlossen, ohne `X-Content-Type-Options`, ohne Einschränkung
der Einbettung in Frames, ohne `Referrer-Policy` und ohne `Permissions-Policy`, unter Apache
ebenso wie unter Velocity. Die Administration ließ sich von jeder Site in einen Frame einbetten
(Clickjacking auf die Schaltflächen zum Veröffentlichen und Löschen), und hochgeladene Dateien konnten vom Browser
per Sniffing als ein anderer Typ interpretiert werden.

`settings/site.ini` `[HTTPHeaderSettings]`:

| Schlüssel | Standard |
|---|---|
| `SecurityHeaders[X-Content-Type-Options]` | `nosniff` |
| `SecurityHeaders[X-Frame-Options]` | `SAMEORIGIN` |
| `SecurityHeaders[Content-Security-Policy]` | `frame-ancestors 'self'` |
| `SecurityHeaders[Referrer-Policy]` | `strict-origin-when-cross-origin` |
| `SecurityHeaders[Permissions-Policy]` | `camera=(), microphone=(), payment=(), usb=()` |
| `SecurityHeaders[Strict-Transport-Security]` | leer (aus; wird ohnehin nur über HTTPS gesendet) |

- Ein leerer Wert lässt einen Header weg. Overrides von `HeaderList` pro Pfad haben weiterhin Vorrang.
  Namen und Werte, die die Antwort aufspalten könnten, werden ignoriert.
- Damit ein vertrauenswürdiger Editor-Host die Administration in einen Frame einbetten darf, überschreiben Sie die Richtlinie:
  `SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self' https://edit.example.com`.
- Schalten Sie HSTS nur ein, wenn die gesamte Site ausschließlich über HTTPS erreichbar ist:
  `SecurityHeaders[Strict-Transport-Security]=max-age=31536000`.
- **Seiten, die aus dem HTTP-Cache beantwortet werden, tragen dieselben Header.** Der Cache speichert
  die konfigurierten Header mit jeder Seite; der Schlüssel enthält das Schema, sodass ein Header,
  der für HTTPS gedacht ist, nie eine HTTP-Antwort erreicht.
- **Velocity** sendet dieselben Header auf statischen Dateien, seinen eigenen Fehlerseiten,
  Weiterleitungen und 304-Antworten (`[ServerSettings] ResponseHeaders[]`) und auf den
  Antworten der drei Front-Controller (`ResponseHeadersOnScripts=enabled`,
  nur dort, wo das Skript keine gesendet hat), dazu `Strict-Transport-Security` auf jeder
  HTTPS-Antwort (`[HTTPSSettings] HSTSMaxAge=300`, `HSTSIncludeSubDomains`,
  `HSTSPreload`; `HSTSMaxAge=0` schreibt nichts). Dafür ist die Engine-Release nötig, die
  `Q.webserver.headers`, `headersOnScripts` und `hsts` liest; eine ältere ignoriert
  sie.

Prüfen:

```bash
curl -sI https://example.com/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
```

## 2. Das Sitzungs-Cookie

Das Sitzungs-Cookie ist die Anmeldung, und dennoch wurde es ohne `HttpOnly`, `SameSite` oder
`Secure` versendet, sofern die Site diese nicht gesetzt hatte, was eine Standardinstallation nie tat. Ein
in eine beliebige Seite eingeschleustes Skript konnte die Sitzung der Administration lesen; ein gefälschtes
Cross-Site-Formular nahm es mit; unter HTTPS wurde es trotzdem auch reinem HTTP angeboten.

`settings/site.ini` `[Session]`:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `CookieSecure` | `auto` | `auto` kennzeichnet das Cookie als `Secure`, wann immer die Anfrage selbst über HTTPS kam; `true` und `false` erzwingen es weiterhin. |
| `CookieHttponly` | `true` | Skripte können das Cookie nicht lesen. |
| `CookieSameSite` | `Lax` | `Lax`, `Strict`, `None` (wird nur zusammen mit `Secure` beachtet) oder leer, um kein Attribut zu senden. |

`setCookieParams()` verwendet die Array-Form von `session_set_cookie_params()`;
die Sitzungsschicht von Velocity beachtet sie bereits, sodass beide Server dasselbe
Cookie senden. Ausdrückliche Einstellungen haben weiterhin Vorrang. Braucht eine Integration Cross-Site-Cookies,
setzen Sie `CookieSameSite=None` und `CookieSecure=true`.

## 3. Anmeldung und Kontowiederherstellung

| Verhalten | Vorher | Jetzt |
|---|---|---|
| Zeitverhalten bei fehlgeschlagener Anmeldung | Ein unbekannter Benutzername kam sofort zurück; ein bekannter durchlief das Passwort-Hashing (mit dem Standard-Hash etwa eine Viertelsekunde), sodass drei Anfragen pro Name verrieten, welche Konten existieren. | Passt kein Konto, berechnet `eZUser` einen Hash des übermittelten Passworts mit dem konfigurierten Hash-Typ, der so viel kostet wie die Prüfung, die er ersetzt. |
| Passwort vergessen | „Es gibt keinen registrierten Benutzer mit dieser E-Mail-Adresse“ (auch im Anmeldeformular der Administration) erlaubte es, Adressen eine nach der anderen zu testen. | Eine gültige, aber unbekannte Adresse erhält dieselbe Seite wie eine bekannte („eine Mail wurde gesendet, falls ein Konto registriert ist“); nur Eingaben, die überhaupt keine Adresse sind, werden abgelehnt. Templates maskieren (wash) jede Adresse, die sie ausgeben. |
| Schlüssel im Link für „Passwort vergessen“ (`ezmbpaex` 6.0.3) | | Enthält einen Schlüssel, der sich nicht erraten lässt. |
| Passwort oder Hash, der keine Zeichenkette ist | In eine Zeichenkette umgewandelt; `true` konnte zu einem gespeicherten `true` passen. | Authentifiziert nie. |
| Erstes Administratorpasswort eines Kickstarts | Erratbarer Standardwert oder das eigene Passwort der Site-Daten, wenn keine E-Mail-Adresse angegeben war. | Zufällige 20 Zeichen, einmal ausgegeben und nach `var/log/initial-admin-password` geschrieben. Siehe [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md). |

## 4. Dateien, die der Webserver ausliefert

`.htaccess_root` und `.htaccess_root_static` (und Velocity, das dieselbe
Liste für jede Engine verwendet) wurden so geändert, dass Apache und Velocity Regel für Regel
dieselben Dateien ausliefern:

- Eine `.php`-, `.phtml`- oder `.phar`-Datei unterhalb des Document Root antwortet mit 404, sodass nur
  die Front-Controller im Root ausgeführt werden. Vorher wurde jede `.php` im Paketspeicher
  (56 in den Demopaketen), in einer Extension oder unter `var/` ausgeführt, wenn ihr
  Pfad angefordert wurde.
- Aus `var/storage/packages` werden nur die Vorschaubilder (png, jpg, gif, webp) importierter Pakete
  ausgeliefert. Vorher waren Paket-XML, SQL, Templates und Einstellungen
  lesbar. SVG ist bewusst ausgenommen, weil es Skript enthalten kann.
- Eine Punktdatei oder ein Punktverzeichnis (`.git`, `.env`, `.htaccess`) antwortet mit 404, wo immer es
  liegt; `.well-known` bleibt erreichbar.
- Die eigenen Verweise der Site werden ausgeliefert: der Service Worker `/index.js` (und `/sw.js`, sein früherer Name, beibehalten für Browser, die sich vor der Umbenennung am 30. September registriert haben), die Ordner `vendor`
  und `media` des Designs einer Extension (die Skripte und Schriften des Layout-Editors)
  und die Bilddateien unter den gespeicherten Originalen (sodass SVG-Bilder
  angezeigt werden). Andere Originale bleiben hinter `content/download`.
- Schriften im Design einer Extension werden von den mitgelieferten Regeln nicht mehr abgelehnt.
- Velocity liefert aus `var/` nur Bilddateien unterhalb von `storage/original/image`
  (png, jpeg, gif, webp, svg) und aus dem Paketspeicher nur die Vorschaubilder aus.
  Vorher wurde jede Datei unterhalb von `var/*/storage/original/image` und der gesamte
  Paketspeicher herausgegeben.

Upgrade-Hinweis: Wenn Sie eine eigene Kopie der `.htaccess` pflegen, übernehmen Sie diese Regeln.
Das mitgelieferte Beispiel liegt in `doc/eng-us/examples/`, dazu die Root-Dateien `.htaccess_root` und
`.htaccess_root_static`.

## 5. Caching und persönliche Seiten

- Eine Seite, die ein Formular-Token trägt (Meta-Tag `csrf-token`, verstecktes Feld in jedem
  POST-Formular), wird mit `Cache-Control: private, no-cache, must-revalidate` gesendet. Vorher
  verlangte `no-cache` nur eine Revalidierung, und ein gemeinsam genutzter Cache, der das
  Sitzungs-Cookie nicht kannte, konnte die Seite eines angemeldeten Benutzers mitsamt Token speichern und sie
  jedem anonymen Besucher ausliefern.
- Velocity teilt seinem Antwort-Cache mit, welche Cookies „persönlich“ bedeuten: das Sitzungs-Cookie,
  wie der Handler es benennt (`session.name` beim Handler `default`,
  `[Session] SessionNamePrefix` als Präfix bei `custom`), dazu `is_logged_in`. Eine
  ausdrückliche Liste ersetzt die abgeleitete: `SkipCookies[]` in `velocity.ini`
  `[CacheSettings]` (das frühere `[ServerSettings] CacheSkipCookies` funktioniert weiterhin;
  ein Wert in `[CacheSettings]` hat Vorrang; Standard leer = die Liste wird abgeleitet).
  Vorher erhielt eine Installation mit dem Standard-Handler (`PHPSESSID`) die Ausschlussliste
  `eZSESSID` und erkannte ihr eigenes Cookie nie, sodass bei eingeschaltetem Antwort-Cache eine
  Anfrage eines angemeldeten Benutzers aus dem Cache beantwortet werden konnte.
- **Purge-Tags sind nicht mehr öffentlich.** Jede Antwort eines gecachten Siteaccess trug
  früher die Purge-Tags der Seite doppelt (`xkey` und `Surrogate-Key`) und nannte damit ihre
  Knoten, Objekte und Layouts. `httpcache.ini [HttpCacheSettings] TagHeader`
  ersetzt `ProxyHeaders`; der Standard `disabled` sendet keinen. Setzen Sie ihn auf den einen
  Header, den Ihr Purge-Proxy liest (`xkey` für Varnish, `Surrogate-Key` für Fastly,
  `Cache-Tag` für Cloudflare). `ProxyHeaders=enabled` in einem bestehenden Override
  bedeutet weiterhin `xkey`. Ein Name, der kein HTTP-Token oder ein Rahmungs-Header ist, wird
  abgelehnt. Siehe [HTTP-Cache](../../bc/6.0/httpcache.md).

## 6. Weitere Korrekturen mit Sicherheitswirkung

| Korrektur | Einzelheiten |
|---|---|
| Phar-Stream-Wrapper | Der Bootstrap meldet den Stream-Wrapper `phar` ab, abgesichert durch `PHP_SAPI !== 'cli'`. Ein Server mit persistenten Workern bedient öffentlichen Verkehr unter der CLI-SAPI, sodass der Schutz genau dort fehlte, wo er gebraucht wird (gemessen: auf dem Worker-Server weiterhin registriert, unter FPM abgemeldet). Die Absicherung fragt jetzt, ob die Anfrage eine Web-Anfrage ist. |
| Paketansicht und viewfile | Eine Paketbeschreibung wird maskiert; eine direkt geöffnete Paketdatei erhält eine Content-Security-Policy mit Sandbox. |
| Paket-Datentyp | Kann kein Paket außerhalb des Repositorys lesen und keine INI-Datei außerhalb eines konfigurierten Siteaccess schreiben. |
| INI-Schreiber | Werte und Namen können keine Zeilen einschleusen und nicht außerhalb des Einstellungsverzeichnisses schreiben. |
| Mail-Header, Dateinamen von Downloads | Siehe [Härtung von Datentypen und Eingaben](datentyp-eingabehaertung.md). |
| REST | Eine nicht authentifizierte Anfrage antwortet auf jedem Server mit 401 und `WWW-Authenticate` (bei fehlenden Zugangsdaten wurde früher `HTTP/1.1 500` angegeben); der OAuth-Header `Authorization` wird ohne Beachtung der Groß- und Kleinschreibung gelesen, mit `HTTP_AUTHORIZATION` als Ausweichlösung; ein unbekanntes Token antwortet auf SQLite-Sites und auf persistenten Workern mit 401. |
| Sortierung von Knoten | `content/action` verlangt Bearbeitungsrechte sowie ein bekanntes Sortierfeld und eine bekannte Sortierreihenfolge. |
| Ablehnungen wegen des Formular-Tokens | Eine 403-Seite ohne Token im Log. Siehe [Seite für abgelaufene Formulare](../../funktionen/6.0/seite-abgelaufenes-formular.md). |
| Velocity-Worker | `[ServerSettings] User`, `Group`, `AllowRootWorkers=disabled`: Worker geben nach dem Start die Root-Rechte ab, sodass Dateien, die sie schreiben, dem Benutzer der Site gehören. |
| Anonyme Formulare | Abonnieren nicht mehr das gemeinsame anonyme Konto. |
| Ausgehende Identität | Der Generator des statischen Caches, die Linkprüfung, der RSS-Import, Wechselkurs- und Paket-Downloads, ausgehende Mails und der REST-Realm geben sich als Exponential zu erkennen. Eine Webserver-Regel, die auf den früheren User-Agent des statischen Caches passt, muss geändert werden. |

## Upgrade-Checkliste

1. Vergleichen Sie Ihre `.htaccess` mit `.htaccess_root` und übernehmen Sie die neuen Regeln.
2. Sehen Sie sich die Antwort-Header Ihrer Seiten an (Befehl oben) und entscheiden Sie, ob Sie
   die `Content-Security-Policy` für einen vertrauenswürdigen Editor-Host lockern müssen.
3. Wenn ein Proxy vor der Site über `xkey` oder `Surrogate-Key` geleert hat, setzen Sie
   `[HttpCacheSettings] TagHeader` in `settings/override/httpcache.ini.append.php`.
4. Wenn eines Ihrer Skripte auf ein Sitzungs-Cookie ohne HttpOnly oder mit Cross-Site-Versand angewiesen war,
   setzen Sie `[Session] CookieHttponly` oder `CookieSameSite` ausdrücklich.
5. Ändern Sie den User-Agent des statischen Caches in jeder Webserver-Regel, die auf den alten
   Namen passte.
6. Leeren Sie nach den Änderungen die Caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.

## Verwandte Seiten

- Spezifikationen: [Härtung von Datentypen und Eingaben](datentyp-eingabehaertung.md), [Sicherheitshärtung vom August 2026](sicherheitshaertung-2026-08.md) (das frühere Patch-Set), [Sicherheitshärtung 6.0.13](sicherheitshaertung-6.0.13.md), [Velocity – HTTP/2 und Sicherheit](velocity-http2-und-sicherheit.md)
- Upgrade-Hinweise: [Härtungsleitfaden](../../bc/6.0/haertung.md), [HTTP-Cache](../../bc/6.0/httpcache.md), [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md), [RAD-Werkzeuge – Sicherheit](../../bc/6.0/rad-sicherheit.md)
- Funktionen: [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md), [Seite für abgelaufene Formulare](../../funktionen/6.0/seite-abgelaufenes-formular.md), [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), Chroniken [16. bis 30. September 2026](../../geschichte/2026/2026-09b.md) und [Februar 2026](../../geschichte/2026/2026-02.md)
