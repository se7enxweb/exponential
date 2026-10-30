---
title: Seiten-Caching mit LiteSpeed und OpenLiteSpeed
audience: admin, hosting, developer
summary: LiteSpeed Web Server (Enterprise) oder OpenLiteSpeed ganze Seiten einer Exponential-Website aufbewahren und anonyme Besucher und Crawler daraus bedienen lassen, ohne PHP auszuführen, ohne Plugin und ohne Code - der auskommentierte Block aus .htaccess_root Zeile für Zeile erklärt, dasselbe im virtuellen Host von LiteSpeed, in Plesk, cPanel, DirectAdmin, CyberPanel und der WebAdmin-Konsole von OpenLiteSpeed, Treffer prüfen, leeren, Fehlersuche und Sicherheit.
keywords: LiteSpeed, OpenLiteSpeed, OLS, LSWS, LSCache, LiteSpeed Cache, Seiten-Cache, Ganzseiten-Cache, ohne Plugin, Umschreiberegeln, .htaccess, .htaccess_root, CacheLookup, CacheEnable, CacheEngine, CacheRoot, cache-control, E=cache-control, max-age, X-LiteSpeed-Cache, hit, miss, X-LiteSpeed-Purge, leeren, X-LiteSpeed-Cache-Control, eZSESSID, PHPSESSID, is_logged_in, Sitzungs-Cookie, anonym, Crawler, Bots, HTTPS-Leistung, Plesk, Zusätzliche Anweisungen für HTTPS, vhost_ssl.conf, cPanel, WHM, DirectAdmin, CyberPanel, WebAdmin, enableCache, checkPublicCache, qsCache, reqCookieCache, respCookieCache, ignoreRespCacheCtrl, expireInSeconds, storagePath, autoLoadHtaccess, page cache, full page cache, without plugin, rewrite rules, purge, crawler
type: guide
---
# Seiten-Caching mit LiteSpeed und OpenLiteSpeed

Diese Anleitung ist für Administratoren und Hoster, deren Exponential-Websites über HTTPS von **LiteSpeed Web Server**
(der kommerziellen Enterprise-Ausgabe) oder von **OpenLiteSpeed**, der Open-Source-Ausgabe, ausgeliefert werden.
Beide Server haben einen Seiten-Cache eingebaut (LiteSpeed nennt ihn LSCache). Diese Anleitung schaltet ihn für eine
Exponential-Website ein, **ohne Plugin und ohne eine Zeile Code zu ändern**: einige Umschreiberegeln (auskommentiert
in `.htaccess_root` mitgeliefert) oder dieselben Regeln im virtuellen Host. Danach beantwortet der Server eine Seite,
die ein anonymer Besucher oder ein Crawler anfragt, aus einer gespeicherten Kopie, und PHP, die Datenbank und der
Kernel laufen dafür überhaupt nicht.

Alles über Exponential unten wurde am Code dieses Repositorys geprüft (die Dateien stehen unter
[Quellen](#quellen)). Alles über LiteSpeed wurde an der eigenen Dokumentation von LiteSpeed und OpenLiteSpeed
geprüft, die dort verlinkt ist, wo sie verwendet wird. [Was geprüft wurde und was nicht](#was-geprüft-wurde-und-was-nicht)
sagt offen, welche Teile hier nicht auf einem LiteSpeed-Server ausgeführt wurden.

[Anleitungen](README.md) · Verwandt: [Caches](caches.md) · [Caches vorwärmen](caches-vorwaermen.md) ·
[LiteSpeed und OpenLiteSpeed (Hosting)](../hilfe/hosting/server/litespeed-und-openlitespeed.md) ·
[LiteSpeed und OpenLiteSpeed (Beispielkonfiguration)](../beispiele/webserver/litespeed.md) ·
[HTTP-Cache-Header](../hilfe/hosting/leistung/http-cache-header.md) ·
[Cache-Schichten](../hilfe/hosting/leistung/cache-schichten.md)

## Inhalt

1. [In Kürze](#in-kürze)
2. [Wann es hilft und wann nicht](#wann-es-hilft-und-wann-nicht)
3. [Was Exponential tut, das der Cache wissen muss](#was-exponential-tut-das-der-cache-wissen-muss)
4. [Mit .htaccess einschalten](#mit-htaccess-einschalten)
5. [Der Block, Zeile für Zeile](#der-block-zeile-für-zeile)
6. [LiteSpeed Enterprise: Server und virtueller Host](#litespeed-enterprise-server-und-virtueller-host)
7. [Plesk](#plesk)
8. [cPanel und WHM](#cpanel-und-whm)
9. [DirectAdmin](#directadmin)
10. [OpenLiteSpeed und CyberPanel](#openlitespeed-und-cyberpanel)
11. [Prüfen, ob er arbeitet](#prüfen-ob-er-arbeitet)
12. [Wie er zu den Caches von Exponential steht](#wie-er-zu-den-caches-von-exponential-steht)
13. [max-age wählen, und was Redakteure sehen](#max-age-wählen-und-was-redakteure-sehen)
14. [Leeren](#leeren)
15. [Was nie zwischengespeichert werden darf](#was-nie-zwischengespeichert-werden-darf)
16. [Fehlersuche](#fehlersuche)
17. [Hinweise zur Sicherheit](#hinweise-zur-sicherheit)
18. [Den Gewinn messen](#den-gewinn-messen)
19. [Crawler und Bots](#crawler-und-bots)
20. [Was geprüft wurde und was nicht](#was-geprüft-wurde-und-was-nicht)
21. [Quellen](#quellen)
22. [Verwandte Seiten](#verwandte-seiten)

## In Kürze

- **Was:** Der LiteSpeed-Server bewahrt das HTML einer Seite für eine feste Zeit auf (`max-age`, 120 Sekunden im
  mitgelieferten Block) und beantwortet dieselbe Adresse aus dieser Kopie. Ein Treffer kostet etwa so viel wie das
  Senden einer statischen Datei.
- **Wer gespeicherte Seiten bekommt:** nur eine `GET`- oder `HEAD`-Anfrage ohne Abfragezeichenkette, von einem
  Besucher **ohne Sitzungs-Cookie und ohne `is_logged_in`-Cookie**, an eine Adresse, die keine Administrations-,
  Benutzer-, Shop- oder Bearbeitungsansicht ist. Alle anderen (Redakteure, angemeldete Mitglieder, ein Besucher mit
  Warenkorb, jedes Formular) erreichen PHP wie bisher.
- **Einschalten:** In der `.htaccess` der Website (einer Kopie von `.htaccess_root`) das erste `#` jeder Zeile des
  Blocks entfernen, der mit `## >>> LiteSpeed page cache` beginnt. Ein `sed`-Befehl erledigt das
  ([Abschnitt 4](#mit-htaccess-einschalten)). Unter OpenLiteSpeed muss zusätzlich das Cache-Modul für den virtuellen
  Host eingeschaltet sein ([Abschnitt 10](#openlitespeed-und-cyberpanel)).
- **Apache und nginx übergehen ihn.** Der Block steht in `<IfModule LiteSpeed>`, den nur LiteSpeed-Server betreten.
- **Prüfen:** `curl -sI https://www.example.com/ | grep -i x-litespeed-cache` zweimal: `miss`, dann `hit`.
- **Der Preis:** Nachdem ein Redakteur veröffentlicht hat, können anonyme Besucher die alte Seite bis zu `max-age`
  Sekunden lang sehen. Exponential sagt LiteSpeed nicht, eine Seite zu verwerfen; halten Sie `max-age` kurz, oder
  bauen Sie den Purge-Listener aus [Abschnitt 14](#leeren) selbst ein.

## Wann es hilft und wann nicht

Der Seiten-Cache beantwortet **dieselbe Seite für alle, die keine Sitzung haben**. Er hilft am meisten, wo das den
größten Teil des Verkehrs ausmacht:

| Website | Wirkung |
|---|---|
| Öffentliche Inhaltsseiten: Nachrichten, Magazine, Dokumentation, Unternehmens- und Behördenseiten | die meisten Anfragen sind anonym: die meisten Seiten werden Treffer |
| Verkehrsspitzen (ein Newsletter, ein Link von einer großen Website, eine Kampagne) | die erste Anfrage rendert, die folgenden innerhalb von `max-age` sind Treffer |
| Wellen von Crawlern und Bots (Suchmaschinen, KI-Crawler, Überwachung, Scraper) | sie melden sich nie an und senden selten Cookies: Treffer ([Abschnitt 19](#crawler-und-bots)) |
| Shared Hosting mit wenigen PHP-Prozessen je Konto | Treffer belegen keinen PHP-Prozess, die Prozesse bleiben frei für die Anfragen, die einen brauchen |

Er hilft wenig oder muss eng gehalten werden, wo Seiten je Besucher verschieden sind:

| Website | Warum |
|---|---|
| Überwiegend angemeldete Benutzer (Intranets, Mitgliederbereiche, Communitys) | jede angemeldete Anfrage hat ein Sitzungs-Cookie und wird nie gespeichert |
| Shops | der Warenkorb liegt in der Sitzung; anonymes Stöbern ist zwischenspeicherbar, die Shop-Ansichten nicht ([Abschnitt 15](#was-nie-zwischengespeichert-werden-darf)) |
| Ein Siteaccess, der nach der Sprache oder dem Gerät des Browsers gewählt wird | eine Adresse antwortet je Besucher anders ([Abschnitt 15](#was-nie-zwischengespeichert-werden-darf)) |
| Seiten, die ohne Sitzung eigene Daten des Besuchers zeigen (ein Block nach Standort, ein zufälliger Teaser je Anfrage) | alle bekommen die Kopie, die zuerst gespeichert wurde |

## Was Exponential tut, das der Cache wissen muss

Das sind Tatsachen des Kernels, nicht von LiteSpeed. Der Block in `.htaccess_root` baut auf ihnen auf.

**Anonyme Besucher haben keine Sitzung.** `site.ini [Session] ForceStart=disabled` wird mitgeliefert: Der Kernel
startet eine Sitzung nur, wenn der Besucher bereits ein Sitzungs-Cookie sendet oder wenn etwas in die Sitzung
schreibt (Anmelden, etwas in den Warenkorb legen, ein Formular, das Zustand hält). Anonymes Blättern startet keine und
setzt kein Cookie (`eZSession::lazyStart()` in `lib/ezsession/classes/ezsession.php`; die Buchführung über die zuletzt
besuchte Seite in `ezpKernelWeb` läuft nur, wenn bereits eine Sitzung gestartet ist). Eine anonyme Seitenantwort hat
also kein `Set-Cookie`, und eine gespeicherte Kopie enthält nichts Privates. Setzt Ihre Website `ForceStart=enabled`,
bekommt jeder Besucher bei der ersten Anfrage ein Sitzungs-Cookie, jede weitere Anfrage trägt es, und der Block
speichert fast nichts: Lassen Sie `ForceStart=disabled`.

**Das Sitzungs-Cookie hat einen von zwei Namen.** Er hängt von `site.ini [Session] SessionNameHandler` ab:

| `SessionNameHandler` | Name des Sitzungs-Cookies |
|---|---|
| `default` (mitgeliefert) | der Name, den PHP ihm gibt, `session.name` aus `php.ini`: `PHPSESSID`, sofern Ihr Hoster ihn nicht geändert hat |
| `custom` | `SessionNamePrefix` (`eZSESSID`), gefolgt vom md5 des Siteaccess-Namens, solange `SessionNamePerSiteAccess=enabled` (mitgeliefert): `eZSESSID3f1c...` |

Eine Cookie-Bedingung ist ein Teilzeichenketten-Test auf den Request-Header `Cookie:`, `eZSESSID` passt also auf jeden
Namen je Siteaccess. Der Block prüft beide Namen. Gibt `php.ini` der Sitzung einen anderen Namen, ergänzen Sie ihn.

**Angemeldete Benutzer tragen `is_logged_in=true`.** Der Kernel setzt dieses Cookie für jeden angemeldeten Benutzer
„zur Verwendung durch HTTP-Cache-Lösungen“ und entfernt es beim Abmelden (`ezpKernelWeb`, mit Pfad, Domain,
Lebensdauer und `Secure`-Flag des Sitzungs-Cookies, `eZSession::siteCookieOptions()`). Es ist das zuverlässigste
Zeichen, dass eine Anfrage jemandem gehört, und der Block prüft es ebenfalls. Der eigene Antwort-Cache von Exponential
Velocity übergeht aus denselben Gründen genau dieselben zwei Cookies (`expVelocity`).

**Es gibt kein Warenkorb-Cookie.** Der Warenkorb des Shops liegt in der Sitzung. Ein Besucher, der etwas in den
Warenkorb legt, bekommt ein Sitzungs-Cookie und umgeht ab dann den Cache. Eine Cookie-Liste mit `basket` aus Beispielen
anderer Systeme trifft in Exponential nichts; sie schadet nicht, schützt aber auch nichts.

**Seiten sagen standardmäßig „nicht speichern“.** Jede Seitenantwort hat `Cache-Control: no-cache, must-revalidate`,
`Expires` aus 1997 und `Pragma: no-cache`, sofern `site.ini [HTTPHeaderSettings]` nichts anderes konfiguriert
([HTTP-Cache-Header](../hilfe/hosting/leistung/http-cache-header.md#die-standardwerte)). Ob ein LiteSpeed-Server sich
von diesem Header vom Speichern abhalten lässt, ist eine Einstellung des Servers („Ignore Response Cache-Control“,
unter OpenLiteSpeed `ignoreRespCacheCtrl`); siehe [Fehlersuche](#immer-miss) und
[LiteSpeed dasselbe aus Exponential sagen](#litespeed-dasselbe-aus-exponential-sagen).

**Manche Antworten sind absichtlich privat.** Ein Siteaccess, der nach den Sprachen des Browsers gewählt wird
(`DefaultHostUriMatchMapItems`), antwortet mit `Vary: Accept-Language` und `Cache-Control: private, no-cache`
(`ezpKernelWeb`); die Wartungsseite ist ein `503` mit `Cache-Control: no-store, no-cache, must-revalidate, private,
max-age=0` (`expMaintenance`). Abschnitt 15 sagt, was mit dem ersten zu tun ist.

## Mit .htaccess einschalten

Das ist der kürzeste Weg unter LiteSpeed Enterprise, das `.htaccess` wie Apache liest. Unter OpenLiteSpeed lesen Sie
zuerst [Abschnitt 10](#openlitespeed-und-cyberpanel): Es liest `.htaccess` nur mit `autoLoadHtaccess` und erst nach
einem Neustart.

1. **Die Website liefert `.htaccess` aus `.htaccess_root`.** Falls noch nicht: `cp .htaccess_root .htaccess`, oder als
   Benutzer der Website `./console exp:setup:htaccess --apply`
   ([.htaccess und config.php von der Konsole einrichten](setup-dateien-von-der-konsole.md)). Ist Ihre `.htaccess`
   älter als der Block, zeigt `./console exp:setup:htaccess` den Unterschied; der Block ist neu in 6.0.15.
2. **Ihr Hoster hat LiteSpeed ein Cache-Verzeichnis gegeben.** Unter LiteSpeed Enterprise ist das ein `CacheRoot` auf
   Serverebene, unter OpenLiteSpeed der `storagePath` des Cache-Moduls. Fragen Sie Ihren Hoster, wenn Sie den Server
   nicht selbst verwalten; die Abschnitte 6 bis 10 zeigen, wo er eingestellt wird.
3. **Passen Sie den Block bei Bedarf an** (jeder Punkt ist im [nächsten Abschnitt](#der-block-zeile-für-zeile)
   erklärt):
   - Ihr Administrations-Siteaccess wird über die URI gewählt und heißt nicht `admin`: Setzen Sie seinen Namen in die
     Liste;
   - er hat einen eigenen Hostnamen: Kommentieren Sie die Host-Zeile ein und setzen Sie seinen Namen ein;
   - `php.ini` gibt dem Sitzungs-Cookie einen anderen Namen als `PHPSESSID`, oder Ihre Website setzt eigene Cookies, die
     ändern, was eine Seite zeigt: Ergänzen Sie sie in der Cookie-Liste;
   - ändern Sie `max-age=120`, wenn zwei Minuten die falsche Verzögerung für Ihre Redakteure sind.
4. **Kommentieren Sie den Block ein.** Im Wurzelverzeichnis der Installation:

   ```bash
   sed -i '/^## >>> LiteSpeed page cache/,/^## <<< LiteSpeed page cache/ s/^#//' .htaccess
   ```

   Das entfernt das erste `#` jeder Zeile von der Markierung `>>>` bis zur Markierung `<<<`. Anweisungszeilen werden
   aktiv; Erklärungszeilen (`##`) behalten ein `#` und bleiben Kommentare. Führen Sie es einmal aus. Sie können die
   Zeilen auch von Hand bearbeiten.
5. **Prüfen** Sie wie in [Abschnitt 11](#prüfen-ob-er-arbeitet). LiteSpeed Enterprise liest die geänderte `.htaccess`
   bei der nächsten Anfrage; kein Neustart.

Zum Ausschalten setzen Sie wieder ein `#` vor die Zeile `<IfModule LiteSpeed>` und jede Zeile bis `</IfModule>`, oder
kopieren `.htaccess_root` erneut über `.htaccess`. Bereits gespeicherte Seiten laufen nach `max-age` ab.

## Der Block, Zeile für Zeile

So steht der Block nach dem Einkommentieren in `.htaccess_root` (die Erklärungszeilen sind hier weggelassen). Er steht
nach den Regeln, die statische Dateien sowie die Einstiegsskripte für REST und das Baummenü ihren eigenen Weg schicken,
und direkt vor der letzten Regel, `RewriteRule .* index.php`, sieht also nur Anfragen, die zu `index.php` gehen.

```apache
<IfModule LiteSpeed>
CacheLookup on
RewriteCond %{REQUEST_METHOD} ^(GET|HEAD)$
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(eZSESSID|PHPSESSID|is_logged_in) [NC]
RewriteCond %{REQUEST_URI} !^/([^/]+/)?((admin|user|shop|notification|layout|ezjscore|setup)(/|$)|content/(?!view/)) [NC]
#RewriteCond %{HTTP_HOST} !^admin\. [NC]
RewriteRule .* - [E=cache-control:max-age=120]
</IfModule>
```

| Zeile | Was sie tut | Warum |
|---|---|---|
| `<IfModule LiteSpeed>` | nur LiteSpeed Enterprise und OpenLiteSpeed betreten den Block | Apache und nginx überspringen ihn: dieselbe `.htaccess` bleibt überall sicher |
| `CacheLookup on` | die Anfrage im Seiten-Cache nachschlagen | die Dokumentation von LiteSpeed zum Caching über Umschreiberegeln beginnt damit („LSCache without a plugin“); allein speichert es nichts |
| `REQUEST_METHOD ^(GET\|HEAD)$` | nur Lesezugriffe | ein `POST` (jedes Formular: Anmelden, Kommentare, Suchformulare, gesammelte Informationen, der Warenkorb) erreicht immer PHP |
| `QUERY_STRING ^$` | nur Adressen ohne `?` | Suchen (`content/search?SearchText=`), Filter, Blättern per Abfrage und Tracking-Parameter (`?utm_source=`) füllen den Cache nie mit einer Kopie je Variante; die eigenen View-Parameter von Exponential (`/(offset)/10`) sind Teil des Pfads und werden als eigene Seiten gespeichert |
| `HTTP_COOKIE !(eZSESSID\|PHPSESSID\|is_logged_in)` | niemand mit Sitzung oder Anmeldung | siehe [den vorigen Abschnitt](#was-exponential-tut-das-der-cache-wissen-muss); eine Anfrage mit einem dieser Cookies wird weder aus dem Cache beantwortet noch in ihm gespeichert |
| `REQUEST_URI !^/([^/]+/)?(...)` | nicht die Ansichten, die je Besucher verschieden sind oder etwas ändern | `user/` (Anmelden, Registrieren, Passwort, Einstellungen), `shop/`, `notification/`, `layout/` (speichert eine Wahl des Besuchers), `ezjscore/` (AJAX-Aufrufe), `setup/`, die Administration (`admin`) und jede `content/`-Ansicht außer `content/view/` (Bearbeiten, Aktion, Suche, Download, gesammelte Informationen, Baummenü, Durchsuchen ...); `([^/]+/)?` trifft sie auch hinter einem Siteaccess-Namen in der URL (`/ger/user/login`) |
| `HTTP_HOST !^admin\.` (auskommentiert) | nicht der eigene Hostname der Administration | für Websites, deren Redakteure auf `admin.example.com` statt unter `/admin` arbeiten |
| `RewriteRule .* - [E=cache-control:max-age=120]` | LiteSpeed sagen: diese Antwort 120 Sekunden aufbewahren | `-` ändert nichts an der Adresse; die Umgebungsvariable `cache-control` ist der dokumentierte Weg von LiteSpeed, die Cache-Richtlinie aus einer Umschreiberegel zu setzen |

**Warum nicht `CacheEnable public /`.** Viele kurze Beispiele verwenden `CacheEnable public /`. Das schaltet das
Caching für jede Adresse unter `/` ein, mit der Lebensdauer der Richtlinie des Servers (die Dokumentation von LiteSpeed
merkt an, dass eine Umschreiberegel eine Lebensdauer mit `max-age` setzen kann, `CacheEnable` nicht). Welche Seiten
aufbewahrt werden, hängt dann von den Standardwerten des Servers ab statt von den Bedingungen oben. Der mitgelieferte
Block gibt nur Anfragen eine Lebensdauer, die jede Bedingung bestehen, und sonst wird nie etwas gespeichert.

**Varianten des Werts von cache-control**, alle von LiteSpeed für die Umgebungsvariable dokumentiert: `no-cache` (diese
nicht speichern), `private` (eine Kopie je Besucher, nach seinen Cookies geschlüsselt), `max-age=N`, und Kombinationen
in Anführungszeichen, `[E="cache-control:public,max-age=300"]`. Der mitgelieferte Block verwendet die einfache
öffentliche Form.

**Die Einstiegsskripte für REST, SOAP, WebDAV und das Baummenü** werden nie berührt: `/api/` wird von Regeln oberhalb
des Blocks mit `[L]` auf `index_rest.php` und `content/treemenu` auf `index_treemenu.php` umgeschrieben;
`index_soap.php` und `index_webdav.php` haben dort Durchlassregeln, wenn Sie sie einschalten; und die
`content/`-Bedingung schließt sie ohnehin aus. Statische Dateien (Designs, Bilder, gespeicherte Dateien) senden die
Regeln für statische Dateien oberhalb des Blocks, sie erreichen ihn nie.

## LiteSpeed Enterprise: Server und virtueller Host

LiteSpeed Enterprise liest die Konfiguration von Apache, es nimmt die Cache-Einstellungen also in Apache-Syntax
innerhalb von `<IfModule LiteSpeed>` (die Dokumentation von LiteSpeed schreibt `Litespeed` und `LiteSpeed`; beides
wird angenommen).

**Serverebene, einmal je Maschine.** Der Cache braucht ein Verzeichnis mit genug Platz. Die cPanel-Anleitung von
LiteSpeed setzt es in die Konfiguration auf Serverebene:

```apache
<IfModule LiteSpeed>
CacheRoot /home/lscache/
</IfModule>
```

Auf Systemen im Stil von Red Hat mit Plesk liegt das oft in einer eigenen Datei unter `/etc/httpd/conf.d/` (zum
Beispiel `lscache.conf`); auf Systemen im Stil von Debian unter `/etc/apache2/conf.d/` oder
`/etc/apache2/conf-enabled/`. Ein Hoster kann die Engine für den ganzen Server mit `CacheEngine off` nach der Zeile
`CacheRoot` ausschalten; dann schaltet ein virtueller Host sie mit `CacheEngine on` wieder ein. Die Dokumentation von
LiteSpeed sagt ausdrücklich, dass `CacheEngine` in die Konfiguration des Servers oder des virtuellen Hosts gehört und
**nicht** in `.htaccess`.

**Ebene des virtuellen Hosts, je Website, statt `.htaccess`.** Derselbe Block funktioniert im virtuellen HTTPS-Host,
was schneller ist als `.htaccess` (keine Datei, die bei jeder Anfrage gesucht wird; siehe die Warnung unter
[.htaccess und config.php von der Konsole einrichten](setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)).
Im Serverkontext sieht ein Umschreibemuster den Pfad mit führendem Schrägstrich, die Muster nehmen ihn deshalb mit
`^/?` an. Die fertige Datei ist
[`litespeed-page-cache.conf`](../../eng-us/examples/web-servers/litespeed-page-cache.conf):

```apache
<IfModule LiteSpeed>
    # Only needed when the server-level configuration says "CacheEngine off"
    #CacheEngine on
    CacheLookup on
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} ^(GET|HEAD)$
    RewriteCond %{QUERY_STRING} ^$
    RewriteCond %{HTTP_COOKIE} !(eZSESSID|PHPSESSID|is_logged_in) [NC]
    RewriteCond %{REQUEST_URI} !^/([^/]+/)?((admin|user|shop|notification|layout|ezjscore|setup)(/|$)|content/(?!view/)) [NC]
    #RewriteCond %{HTTP_HOST} !^admin\. [NC]
    RewriteRule ^/?(.*)$ - [E=cache-control:max-age=120]
</IfModule>
```

Setzen Sie ihn in den virtuellen HTTPS-Host (`<VirtualHost *:443>`), **vor** die Regel, die alles an `index.php`
schickt, und starten Sie LiteSpeed neu: Laut Dokumentation von LiteSpeed brauchen Änderungen an der Apache-Konfiguration
einen Neustart, Änderungen an `.htaccess` nicht. Verwenden Sie den Block entweder in `.htaccess` oder im virtuellen
Host, nicht in beiden.

## Plesk

Auf einem Plesk-Server mit LiteSpeed liest LiteSpeed die Apache-Konfiguration, die Plesk erzeugt. Für den Block gibt
es zwei Orte:

1. **Die `.htaccess` der Website** ([Abschnitt 4](#mit-htaccess-einschalten)). In Plesk muss sich nichts ändern,
   solange der Server einen `CacheRoot` hat.
2. **Websites & Domains > die Domain > Apache & nginx Settings > Additional Apache directives > „Additional
   directives for HTTPS“**: Fügen Sie den Block des virtuellen Hosts aus
   [Abschnitt 6](#litespeed-enterprise-server-und-virtueller-host) ein (mit einkommentiertem `CacheEngine on`, wenn
   die Datei des Servers die Engine für alle Websites ausschaltet) und klicken Sie auf OK. Plesk schreibt das Feld in
   die Konfiguration der Domain und lädt den Webserver neu, der hier LiteSpeed ist.

**Bearbeiten Sie `vhost_ssl.conf` nicht von Hand.** In der Datei
`/var/www/vhosts/system/<domain>/conf/vhost_ssl.conf` landen diese Anweisungen, und es ist verlockend, sie direkt zu
bearbeiten. Plesk hält den Inhalt des Feldes aber in seiner eigenen Datenbank und schreibt die Konfigurationsdateien
neu, wann immer die Webkonfiguration der Domain neu erzeugt wird: eine Änderung im Panel, ein Wechsel des
PHP-Handlers, `plesk repair web`, ein Update von Plesk. Eine von Hand angelegte `vhost_ssl.conf` wird durch das ersetzt,
was das Panel in diesem Moment hält, und die Cache-Regeln verschwinden ohne Meldung. Was in das Feld des Panels
eingetragen ist, übersteht jede Neuerzeugung. Ist das Feld schon belegt (zum Beispiel für die Durchlassregel von SOAP,
siehe [Plesk](../hilfe/hosting/control-panels/plesk.md)), fügen Sie den Block unter dem Vorhandenen ein.

Ein typisches Bild unter Plesk ist ein Server, der den `CacheRoot` auf Serverebene hat (in einer Datei wie
`/etc/httpd/conf.d/lscache.conf`), aber keine Website, die LiteSpeed sagt, was zu speichern ist: Jede Seite antwortet
ohne Header `X-LiteSpeed-Cache`. Der Block, an einem der beiden Orte, ist der fehlende Schalter je Website.

## cPanel und WHM

- **Serverebene:** Die cPanel-Anleitung von LiteSpeed setzt `CacheRoot /home/lscache/` (oder eine Partition mit
  genug Platz) in `/etc/apache2/conf.d/includes/pre_main_global.conf`, innerhalb von `<IfModule LiteSpeed>`, und
  empfiehlt, die Cache-Richtlinie auf Serverebene auf „Not Set“ zu lassen. Auf den meisten Servern verwaltet das
  LiteSpeed-Plugin in WHM das.
- **Je Website:** Die `.htaccess` des Kontos ist der übliche Ort ([Abschnitt 4](#mit-htaccess-einschalten));
  Kontoinhaber können das selbst im Dateimanager tun.
- **Je Website, im virtuellen Host:** cPanel bindet Dateien je Domain aus
  `/etc/apache2/conf.d/userdata/ssl/2_4/<user>/<domain>/` in den virtuellen HTTPS-Host ein. Legen Sie den Block des
  virtuellen Hosts aus Abschnitt 6 in einer Datei dort ab (zum Beispiel `lscache.conf`), dann
  `/scripts/rebuildhttpdconf` und einen Neustart des Webservers. Dort abgelegte Dateien überstehen die Neuerzeugungen
  von cPanel; eine Handänderung an `httpd.conf` nicht.

## DirectAdmin

- **Je Website:** die `.htaccess` der Domain in `public_html` (oder wo das Wurzelverzeichnis der Installation liegt),
  wie in [Abschnitt 4](#mit-htaccess-einschalten).
- **Je Website, im virtuellen Host:** Server Manager > Custom HTTPD Configurations > die Domain erlaubt dem
  Administrator, der erzeugten Konfiguration der Domain Zeilen hinzuzufügen, die die Neuschreibungen von DirectAdmin
  überstehen; fügen Sie dort den Block des virtuellen Hosts aus Abschnitt 6 ein.
- **Serverebene:** Der `CacheRoot` gehört zur LiteSpeed-Einrichtung, die DirectAdmin installiert; prüfen Sie ihn in der
  WebAdmin-Konsole von LiteSpeed (Port 7080) oder in der Konfiguration auf Serverebene.

## OpenLiteSpeed und CyberPanel

OpenLiteSpeed unterscheidet sich hier in drei Punkten von LiteSpeed Enterprise:

1. **Der Cache ist ein Modul, das in WebAdmin konfiguriert wird**, nicht mit `CacheRoot` und `CacheEnable`. Server
   Configuration > Modules > `cache` setzt die Standardwerte; Virtual Hosts > die Website > Modules > `cache`
   überschreibt sie je Website.
2. **Es liest von `.htaccess` nur die Umschreiberegeln, nur wenn `autoLoadHtaccess` eingeschaltet ist, und nach einer
   Änderung der Datei erst nach einem Neustart.** Andere Anweisungen in `.htaccess` werden übergangen („Unsupported
   directives will be ignored by OpenLiteSpeed“). `CacheLookup` in `.htaccess` bewirkt dort also nichts; das
   `checkPublicCache` des Moduls übernimmt seine Aufgabe.
3. **`RewriteBase` wird nicht unterstützt**; die mitgelieferten Regeln verwenden es nicht.

**Das Cache-Modul des virtuellen Hosts.** In `vhconf.conf` (oder in denselben Feldern in WebAdmin):

```apache
module cache {
  ls_enabled              1
  # Do not cache every page by policy: only what the rewrite rule marks
  enableCache             0
  enablePrivateCache      0
  # Look requests up in the public cache
  checkPublicCache        1
  checkPrivateCache       0
  # Addresses with a query string are never stored (the rule says so too)
  qsCache                 0
  # Requests with cookies may be answered from the cache; the rule decides
  # which cookies keep a request out (analytics cookies do not)
  reqCookieCache          1
  # Never store a response that sets a cookie
  respCookieCache         0
  ignoreReqCacheCtrl      1
  # 0: the response's own Cache-Control counts (see the guide, section 12)
  ignoreRespCacheCtrl     0
  expireInSeconds         120
  storagePath             $VH_ROOT/lscache
}
```

Die Parameter sind die der Dokumentation des Cache-Moduls von OpenLiteSpeed. `enableCache 0` mit `checkPublicCache 1`
überlässt die Entscheidung der Regel: Nur Anfragen, die sie mit `cache-control` markiert, werden gespeichert.
`reqCookieCache 0` wäre strenger (keine Anfrage mit irgendeinem Cookie wird aus dem Cache beantwortet), dann bekommt
aber ein Besucher mit nur einem Analyse-Cookie nie eine gespeicherte Seite. `respCookieCache 0` hält jede Antwort, die
ein Cookie setzt, aus dem Cache heraus, und das ist für Exponential der richtige Standard: Eine Seite, die ein Cookie
setzt, gehört einem Besucher. `ignoreRespCacheCtrl 0` lässt das Modul dem eigenen `Cache-Control` der Seite gehorchen;
mit dem Standard `no-cache` von Exponential heißt das, nichts wird gespeichert, bis der Kernel anonymen Besuchern einen
zwischenspeicherbaren Header sendet ([LiteSpeed dasselbe aus Exponential sagen](#litespeed-dasselbe-aus-exponential-sagen)).
Setzen Sie es nur auf `1`, wenn Sie die Header des Kernels nicht ändern wollen, und lesen Sie vorher
[Abschnitt 15](#was-nie-zwischengespeichert-werden-darf).

**Die Regeln.** Das [Beispiel für einen virtuellen Host von OpenLiteSpeed](../../eng-us/examples/web-servers/openlitespeed-vhost.conf)
hat die Umschreiberegeln von `.htaccess_root` in seinem Block `rewrite`, mit den Cache-Regeln auskommentiert direkt
vor der letzten Regel. Kommentieren Sie sie dort ein und starten Sie neu. Wenn Sie `.htaccess` bevorzugen: Setzen Sie
Virtual Hosts > die Website > Rewrite > „Auto Load from .htaccess“ auf Yes (`autoLoadHtaccess 1`), kommentieren Sie
den Block wie in Abschnitt 4 ein und starten Sie OpenLiteSpeed nach **jeder** Änderung der Datei neu
(`/usr/local/lsws/bin/lswsctrl restart` oder WebAdmin > Graceful Restart).

**CyberPanel** betreibt OpenLiteSpeed. Seine Website-Seite hat einen Editor „vHost Conf“ (die `vhconf.conf` oben, in
die der Block `module cache` gehört) und einen Editor „Rewrite Rules“, der die `.htaccess` der Website schreibt.
Stellen Sie nach dem Speichern von Umschreiberegeln sicher, dass OpenLiteSpeed neu gestartet wurde; bis dahin arbeitet
es mit den alten Regeln.

## Prüfen, ob er arbeitet

Verwenden Sie eine Adresse, die der Block speichert (eine Inhaltsseite, nicht `/user/login`), aus einer Shell **ohne**
Cookie:

```bash
curl -sI https://www.example.com/ | grep -i -E '^x-litespeed-cache|^cache-control|^set-cookie'
curl -sI https://www.example.com/ | grep -i -E '^x-litespeed-cache|^cache-control|^set-cookie'
```

| Was Sie sehen | Bedeutung |
|---|---|
| `X-LiteSpeed-Cache: miss` bei der ersten Anfrage, `hit` bei der zweiten | es funktioniert: die erste Antwort wurde gespeichert, die zweite kam aus dem Cache |
| `hit` bei beiden | die Seite war schon durch eine frühere Anfrage gespeichert |
| überhaupt kein Header `X-LiteSpeed-Cache` | der Block ist nicht aktiv, oder der Server hat kein Cache-Verzeichnis, oder eine Bedingung hat die Anfrage ausgeschlossen |
| jedes Mal `miss` | siehe [Immer miss](#immer-miss) |
| ein Header `Set-Cookie` | diese Seite startet eine Sitzung oder setzt ein Cookie: sie darf nicht gespeichert werden ([Fehlersuche](#eine-seite-setzt-ein-cookie)) |

Die Dokumentation von LiteSpeed nennt diese Werte des Headers: `X-LiteSpeed-Cache: miss` (noch nicht gespeichert, jetzt
gespeichert), `X-LiteSpeed-Cache: hit` (aus dem Cache ausgeliefert), `hit,private` für den privaten Cache, und
`X-LiteSpeed-Cache-Control: no-cache` für eine Seite, die die Anwendung als nicht zwischenspeicherbar markiert hat.
LiteSpeed betreibt außerdem eine Online-Prüfung unter [check.lscache.io](https://check.lscache.io/).

**Die andere Richtung zählt genauso.** Eine angemeldete Anfrage darf nie ein Treffer sein:

```bash
curl -sI -H 'Cookie: is_logged_in=true' https://www.example.com/ | grep -i x-litespeed-cache   # nichts
curl -sI -X POST https://www.example.com/ | grep -i x-litespeed-cache                         # nichts
curl -sI 'https://www.example.com/?x=1' | grep -i x-litespeed-cache                           # nichts
curl -sI https://www.example.com/user/login | grep -i x-litespeed-cache                       # nichts
```

Melden Sie sich dann in einem Browser an der Website an, öffnen Sie die Entwicklerwerkzeuge (Netzwerk), laden Sie eine
Inhaltsseite neu und sehen Sie sich ihre Response-Header an: Dort darf kein `X-LiteSpeed-Cache: hit` stehen.

**Antwortzeiten.** `curl` misst die Zeit bis zum ersten Byte:

```bash
for i in 1 2 3; do curl -s -o /dev/null -w '%{http_code} %{time_starttransfer}s\n' https://www.example.com/; done
```

Der erste Wert nach einem Leeren oder einem Ablauf ist die gerenderte Seite, die folgenden sind Treffer.

## Wie er zu den Caches von Exponential steht

Der Seiten-Cache von LiteSpeed steht **vor** allem, was Exponential hat. Ein Treffer erreicht PHP nie; ein Fehlschlag ist
eine gewöhnliche Anfrage, und auf ihrem Weg arbeiten die eigenen Caches des Kernels wie immer. Sie ergänzen sich:

| Schicht | Wo | Wer sie bekommt | Geleert durch |
|---|---|---|---|
| Seiten-Cache von LiteSpeed | der Webserver | anonyme Besucher, Crawler | `max-age`, ein Purge ([Abschnitt 14](#leeren)) |
| Rollenbewusster HTTP-Cache (`httpcache.ini`, standardmäßig aus) | bevor der Kernel startet | anonyme und angemeldete Benutzer je Berechtigungskontext | den Kernel, beim Veröffentlichen |
| Content-View-Cache, Template-Block-Cache | der Kernel | alle | den Kernel, beim Veröffentlichen ([Caches](caches.md)) |
| Statischer Cache (standardmäßig aus) | Dateien, die Umschreiberegeln ausliefern | anonyme Besucher | beim Veröffentlichen neu erzeugt |
| Browser-Cache | der Browser des Besuchers | jeder Besucher | `Cache-Control` der Antwort |

- **View-Cache und Template-Block-Cache bleiben an.** Sie machen jeden Fehlschlag, jede angemeldete Anfrage und jede
  Seite außerhalb des Blocks schnell. Der Cache von LiteSpeed ersetzt sie nicht.
- **Der rollenbewusste HTTP-Cache** ([HTTP-Cache-Header](../hilfe/hosting/leistung/http-cache-header.md#der-rollenbewusste-http-cache))
  kann ebenfalls laufen: Ein Fehlschlag bei LiteSpeed wird dann oft von seinem frühen Ausstieg beantwortet, und
  angemeldete Benutzer, die LiteSpeed nie bedient, bekommen seine nach Rollen geteilten Seiten. Er leert sich selbst,
  wenn sich Inhalte ändern; die Kopie von LiteSpeed läuft nach `max-age` ab. Eine gespeicherte Seite behält die Header,
  mit denen sie gespeichert wurde, ein Treffer bei LiteSpeed kann also `X-Exp-Cache: HIT` aus dem Moment des Speicherns
  zeigen. Lesen Sie `X-LiteSpeed-Cache`, um zu wissen, wer geantwortet hat.
- **Der statische Cache** ([Statischer Cache](../hilfe/hosting/leistung/statischer-cache.md)) tut für anonyme Besucher,
  was der Cache von LiteSpeed tut, mit Dateien, die beim Veröffentlichen neu erzeugt werden, statt abzulaufen.
  Verwenden Sie einen der beiden. Der Block steht in `.htaccess_root`, nicht in `.htaccess_root_static`.
- **Exponential Velocity** hat einen eigenen Antwort-Cache und liest `.htaccess` nicht. Diese Anleitung betrifft nur
  Websites, die LiteSpeed oder OpenLiteSpeed ausliefern.
- **Das Vorwärmen** ([Caches vorwärmen](caches-vorwaermen.md)) fragt die Seiten einer Website über ihre Adresse an
  (`site.ini [SiteSettings] SiteURL`), wie ein anonymer Besucher. Über einen LiteSpeed-Server füllt ein Lauf auch den
  Cache von LiteSpeed, für `max-age` Sekunden.

### LiteSpeed dasselbe aus Exponential sagen

Exponential kann zwischenspeicherbare Header nur an anonyme Besucher senden, damit Anwendung und Server
übereinstimmen. In `settings/override/site.ini.append.php`
([HTTP-Cache-Header](../hilfe/hosting/leistung/http-cache-header.md#zwischenspeicherbare-seiten-für-anonyme-besucher)):

```ini
[HTTPHeaderSettings]
CustomHeader=enabled
OnlyForAnonymous=enabled
OnlyForContent=enabled

HeaderList[]
HeaderList[]=Cache-Control

Cache-Control[]
Cache-Control[/]=public, max-age=120
```

Dann `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`. Anonyme Inhaltsseiten sagen jetzt
`public, max-age=120`; angemeldete Benutzer behalten `no-cache, must-revalidate`, und der Kernel lässt
`Pragma: no-cache` weg, wenn er `Cache-Control` überschreibt.

Das ist eine zweite Verteidigungslinie: Auf einem Server, der dem `Cache-Control` der Antwort gehorcht (OpenLiteSpeed
mit `ignoreRespCacheCtrl 0`), kann eine für einen angemeldeten Benutzer gerenderte Seite nie gespeichert werden, selbst
wenn eine Umschreibebedingung falsch wäre, und der nach Sprache gewählte Siteaccess behält sein `private`. Es lässt
auch Browser die Seite `max-age` Sekunden lang behalten; ein Besucher, der sich anmeldet, kann deshalb die anonyme
Kopie einer gerade angesehenen Seite aus seinem Browser sehen, bis sie abläuft; halten Sie den Wert kurz. Wenn nur
gemeinsame Caches Seiten behalten sollen, sagt `public, max-age=0, s-maxage=120` das den üblichen Caches; prüfen Sie
auf Ihrem Server, dass LiteSpeed `s-maxage` aus der Antwort liest, bevor Sie sich darauf verlassen.

## max-age wählen, und was Redakteure sehen

Nichts in Exponential sagt LiteSpeed, dass sich eine Seite geändert hat. `max-age` ist also die längste Zeit, die ein
anonymer Besucher eine Seite so sehen kann, wie sie vor einer Veröffentlichung war.

| `max-age` | Gut für |
|---|---|
| 60 bis 120 Sekunden | Websites, deren Redakteure oft veröffentlichen und die öffentliche Website sofort prüfen; fängt trotzdem jede Welle ab |
| 300 bis 900 Sekunden | Magazine und Unternehmensseiten, die ein paar Mal am Tag veröffentlichen |
| 3600 Sekunden und mehr | Archive und Dokumentation, die sich selten ändern, oder mit dem Purge-Listener aus [Abschnitt 14](#leeren) |

Schon 60 Sekunden nehmen fast die ganze Last einer Crawler-Welle oder einer Verkehrsspitze weg: Eine Seite, die
1.000 Mal pro Minute angefragt wird, wird einmal pro Minute gerendert.

**Was Redakteure sehen.** Redakteure sind angemeldet, tragen ein Sitzungs-Cookie und bekommen immer die aktuelle Seite.
Nur wenn sie die öffentliche Website in einem anderen Browser oder einem privaten Fenster ansehen (als anonymer
Besucher), sehen sie die gespeicherte Kopie, bis sie abläuft. Nennen Sie ihnen diese Zahl.

## Leeren

**Was es heute gibt.** Exponential sendet LiteSpeed nichts, wenn Inhalte veröffentlicht werden. Der Kernel leert seine
eigenen Caches (View-Cache, Template-Block-Cache, den rollenbewussten HTTP-Cache); die Kopie von LiteSpeed läuft nach
`max-age` ab.

**Wege, Seiten früher zu entfernen:**

1. **Auf `max-age` warten.** Der einfache, sichere Standard.
2. **Ein Purge-Header aus Ihrer eigenen Extension.** LiteSpeed entfernt Seiten, wenn eine Antwort `X-LiteSpeed-Purge`
   trägt (Developer Guide von LiteSpeed, „Basic Controls“): `X-LiteSpeed-Purge: *` leert jede öffentliche Seite der
   Website, `X-LiteSpeed-Purge: url=/news/mein-artikel` genau eine Adresse (keine Platzhalter). Die Antwort, die ihn
   trägt, muss nicht zwischenspeicherbar sein. Der Kernel meldet `content/cache` (Knoten-IDs, Objekt-IDs), wann immer
   er den View-Cache von Inhalten leert, und `content/cache/all`, wenn er ihn ganz leert (`eZContentCacheManager`,
   `eZCache`). Ein Listener in einer eigenen Extension kann daraus den Header machen. Das ist ein Beispiel, **nicht Teil
   des Kernels und hier nicht auf einem LiteSpeed-Server getestet**:

   ```php
   <?php
   // extension/mysite/classes/mysitelitespeedpurge.php (run "php bin/php/ezpgenerateautoloads.php -e")
   class mySiteLiteSpeedPurge
   {
       /**
        * site.ini [Event] Listeners[]=content/cache@mySiteLiteSpeedPurge::purge
        *          Listeners[]=content/cache/all@mySiteLiteSpeedPurge::purge
        */
       public static function purge()
       {
           // Only a web request has a response that LiteSpeed reads
           if ( !isset( $_SERVER['REQUEST_METHOD'] ) || headers_sent() )
               return;
           header( 'X-LiteSpeed-Purge: *' );
       }
   }
   ```

   Tragen Sie beide Listener in `settings/override/site.ini.append.php` unter `[Event]` ein, damit sie für den
   Siteaccess gelten, in dem Redakteure veröffentlichen. Grenzen: Der Purge erreicht den Cache der Website, die die
   Anfrage des Redakteurs beantwortet; er wirkt also, wenn Redakteure auf demselben Host und virtuellen Host wie die
   öffentliche Website veröffentlichen (`/admin` über die URI gewählt). Hat die Administration einen eigenen Hostnamen
   oder virtuellen Host, geht der Header dorthin, und die Seiten der öffentlichen Website laufen wie bisher nach
   `max-age` ab. Eine Veröffentlichung außerhalb einer Webanfrage (ein Cronjob, asynchrones Veröffentlichen, die
   Konsole) hat keine Antwort, die den Header tragen könnte. Alles zu leeren ist grob, aber sicher; ein Purge je
   Adresse bräuchte die URL-Aliase des Knotens in jedem Siteaccess und bleibt Ihnen überlassen.
3. **Das Cache-Verzeichnis leeren.** Die Cache-Dateien liegen unter dem `CacheRoot` (LiteSpeed Enterprise) oder dem
   `storagePath` des Moduls (OpenLiteSpeed). Mit einem `storagePath` je Website wie `$VH_ROOT/lscache` leert das
   Entfernen der Dateien darunter den Cache dieser Website. Ein `CacheRoot` auf Serverebene wird von jeder Website des
   Servers geteilt: Leeren Sie ihn nicht auf einem gemeinsam genutzten Server; fragen Sie den Hoster.
4. **`max-age` rechtzeitig senken**, für eine Migration oder eine Designänderung: auf wenige Sekunden setzen, warten,
   bis der alte Wert verstrichen ist, die Änderung vornehmen, dann zurücksetzen.

## Was nie zwischengespeichert werden darf

Der mitgelieferte Block hält bereits alles in den ersten Zeilen heraus. Die anderen hängen davon ab, wie eine Website
gebaut ist.

| Was | Wie es draußen bleibt |
|---|---|
| Redakteure, Administratoren, angemeldete Mitglieder | Sitzungs-Cookie und `is_logged_in`-Cookie |
| Formulare, Anmelden, Kommentare, gesammelte Informationen, der Warenkorb | `POST`; die Adressen `user/`, `shop/` und `content/` (außer `view`) |
| Der Administrations-Siteaccess | seine URI (`admin`) oder, mit eigenem Hostnamen, die Zeile `HTTP_HOST`; Redakteure tragen ohnehin ein Sitzungs-Cookie |
| REST (`/api/`, `index_rest.php`), SOAP (`index_soap.php`), WebDAV (`index_webdav.php`), das Baummenü | Regeln oberhalb des Blocks, mit `[L]`; die `content/`-Bedingung |
| Suchen und alles mit Abfragezeichenkette | `QUERY_STRING ^$` |
| Debug-Ausgabe (`site.ini [DebugSettings] DebugOutput=enabled` mit `DebugByIP`) | **nicht automatisch**: Eine für eine Debug-Adresse gerenderte Seite enthält den Debug-Bericht, und wenn sie gespeichert wird, bekommen ihn alle. Schalten Sie Debug-Ausgabe nie auf einer Produktivsite mit Seiten-Cache ein, oder fügen Sie dem Block `RewriteCond %{REMOTE_ADDR} !^203\.0\.113\.10$` mit Ihrer Adresse hinzu |
| Ein Siteaccess, der nach den Sprachen des Browsers gewählt wird (`DefaultHostUriMatchMapItems`) | **nicht automatisch**: Die Adresse antwortet je `Accept-Language`. Mit `ignoreRespCacheCtrl 0` und dem Header `private` des Kernels wird sie nicht gespeichert; sonst schließen Sie diese Adressen aus (meist nur den bloßen Hostnamen, `/`), oder geben Sie jeder Sprache eine eigene URI oder einen eigenen Host, was korrekt zwischengespeichert wird |
| Erkennung mobiler Geräte (`site.ini [SiteAccessSettings] DetectMobileDevice=enabled`) | **nicht automatisch**: Der Kernel leitet mobile Browser um und setzt das Cookie `eZMobileDeviceDetect`. Lassen Sie es mit dem Seiten-Cache `disabled` (mitgeliefert), oder ergänzen Sie die Regeln `vary=ismobile` von LiteSpeed, die genau der Erkennung des Kernels entsprechen |
| Ohne Sitzung personalisierte Blöcke (nach IP, Standort, einer zufälligen Wahl je Anfrage) | **nicht automatisch**: Alle bekommen die gespeicherte Variante; schließen Sie diese Seiten über die URI aus, oder laden Sie den Block per AJAX von einer ausgeschlossenen Adresse (`ezjscore/`) |
| Cookies Ihrer eigenen Extension, die eine Seite ändern | ergänzen Sie ihre Namen in der Cookie-Bedingung |

## Fehlersuche

### Immer miss

1. **Die Seite setzt ein Cookie.** `curl -sI <Adresse> | grep -i set-cookie` darf nichts ausgeben. Gibt es ein
   Sitzungs-Cookie aus, schreibt etwas auf der Seite für anonyme Besucher in die Sitzung (eine Extension, ein Template,
   das eine Einstellung speichert, `ForceStart=enabled`); beheben Sie das oder schließen Sie die Adresse aus. LiteSpeed
   speichert keine Antwort, die ein Cookie setzt, wenn seine Richtlinie das sagt (OpenLiteSpeed: `respCookieCache 0`),
   und darf es auch nicht.
2. **Die Seite sagt `no-cache`, und der Server gehorcht.** Mit `ignoreRespCacheCtrl 0` unter OpenLiteSpeed oder mit
   „Ignore Response Cache-Control“ auf No unter LiteSpeed Enterprise hält das standardmäßige
   `Cache-Control: no-cache` des Kernels die Seite heraus. Schalten Sie die anonymen Header aus
   [Abschnitt 12](#litespeed-dasselbe-aus-exponential-sagen) ein; das ist die sichere Abhilfe.
3. **Eine Abfragezeichenkette.** Jedes `?` hält die Anfrage heraus, auch Tracking-Parameter, die Kampagnen anhängen.
4. **Ein Cookie aus der Bedingung.** Fragen Sie ohne Cookie an (`curl` sendet keins). Ein Browser, der je angemeldet
   war, hat noch ein Sitzungs-Cookie: Testen Sie in einem privaten Fenster.
5. **OpenLiteSpeed hat die Regeln nicht gelesen.** Es liest `.htaccess` nur mit `autoLoadHtaccess 1` und erst nach
   einem Neustart; `CacheLookup` wird dort übergangen, `checkPublicCache 1` des Moduls ersetzt es, und das Modul
   braucht `ls_enabled 1` für den virtuellen Host.
6. **Kein Cache-Verzeichnis.** Ohne `CacheRoot` (Enterprise) oder einen beschreibbaren `storagePath` (OpenLiteSpeed)
   wird nichts gespeichert. Sehen Sie im Fehlerlog von LiteSpeed nach.

### Überhaupt kein Header X-LiteSpeed-Cache

Der Server ist nicht LiteSpeed (dann wird `<IfModule LiteSpeed>` wie beabsichtigt übersprungen), der Block ist noch
auskommentiert, ein `CacheEngine off` auf Serverebene ist für die Website nicht wieder eingeschaltet, oder eine
Bedingung schließt die Adresse aus. `curl -sI <Adresse> | grep -i ^server` nennt den Server.

### Veraltete Seiten

Eine Seite ist nach einer Veröffentlichung bis zu `max-age` Sekunden alt. Senken Sie `max-age`, leeren Sie
([Abschnitt 14](#leeren)), oder prüfen Sie, dass Redakteure die öffentliche Website angemeldet ansehen. Bleibt eine
Seite viel länger als `max-age` alt, prüfen Sie, ob etwas davor (ein CDN, ein Proxy) oder der Browser sie ebenfalls
behält: `Cache-Control` und `Age` der Antwort sagen es.

### Angemeldete Benutzer sehen gespeicherte Seiten

Das darf nie passieren. Prüfen Sie in dieser Reihenfolge:

1. Die Cookie-Bedingung nennt Ihr Sitzungs-Cookie: Melden Sie sich an, sehen Sie sich die Cookies der Website im
   Browser an und vergleichen Sie die Namen mit der Bedingung (`PHPSESSID`, `eZSESSID...` oder der Name aus `php.ini`).
2. `path` und `domain` des Sitzungs-Cookies (`site.ini [Session] CookiePath`, `CookieDomain`) decken die öffentliche
   Website ab; ein auf `/admin` beschränktes Cookie wird nicht an `/` gesendet, der Besucher wirkt dort anonym und
   bekommt zu Recht die öffentliche Seite. Das ist kein Leck: Es ist die anonyme Seite.
3. Die Seite wurde nicht aus einer angemeldeten Antwort gespeichert: Mit den anonymen Headern aus Abschnitt 12 und
   einem Server, der ihnen gehorcht, ist das nicht möglich.

### Falsche Sprache oder falsche Geräteversion

Siehe die Zeilen zu Sprache und Mobilgeräten in [Abschnitt 15](#was-nie-zwischengespeichert-werden-darf).

### Eine Seite setzt ein Cookie

Eine Antwort, die ein Cookie setzt, darf nicht gespeichert werden: Entweder ginge das Cookie für diesen Besucher
verloren (LiteSpeed lässt `Set-Cookie` aus gespeicherten Seiten weg), oder es gehörte jemand anderem. Finden Sie
heraus, wer es setzt (`curl -sI <Adresse> | grep -i set-cookie`), und schließen Sie diese Adresse aus, oder setzen Sie
das Cookie für anonyme Besucher nicht mehr.

## Hinweise zur Sicherheit

- **Was private Seiten privat hält, sind allein die Bedingungen.** Behalten Sie die Cookie- und Adressbedingungen;
  entfernen Sie nie eine, um mehr Treffer zu bekommen. Prüfen Sie nach jeder Änderung die angemeldete Richtung aus
  [Abschnitt 11](#prüfen-ob-er-arbeitet).
- **Behalten Sie die sicheren Standardwerte von Exponential:** `ForceStart=disabled`, das anonyme `Cache-Control` nur
  mit `OnlyForAnonymous=enabled`, Debug-Ausgabe im Produktivbetrieb aus.
- **Speichern Sie nie die Administration.** Schließen Sie sie über URI und Hostnamen aus; Redakteure tragen ohnehin ein
  Sitzungs-Cookie.
- **Formular-Token:** Die Extension `ezformtoken` schützt nur Benutzer, die eine Sitzung haben und angemeldet sind, und
  die bekommen nie gespeicherte Seiten; anonyme Seiten tragen kein Token, das eine gespeicherte Kopie verraten könnte.
- **Ein Purge-Header ist ein Schreibzugriff.** Wenn Sie den Purge-Listener bauen, senden Sie ihn nur, nachdem Inhalte
  geändert wurden, nie bei einem gewöhnlichen Seitenaufruf, damit ein Besucher den Cache nicht durch das Anfragen von
  Seiten leeren kann.
- **Gemeinsam genutzte Server:** Ein `CacheRoot` auf Serverebene enthält die Seiten jeder Website. Dateirechte sind
  Sache des Hosters; ein `storagePath` je Website hält den Cache jeder Website in einem eigenen Verzeichnis.

## Den Gewinn messen

Messen Sie auf Ihrer eigenen Website; keine Zahl hier wäre für sie wahr. Ein fairer Vergleich:

1. Wählen Sie fünf Adressen, die der Block speichert (Startseite, eine Bereichsseite, ein Artikel, eine Liste, eine
   Seite mit Bildern).
2. Fragen Sie bei ausgeschaltetem Block jede zehnmal an und notieren Sie den Median der Zeit bis zum ersten Byte (die
   `curl -w`-Schleife aus [Abschnitt 11](#prüfen-ob-er-arbeitet)), oder verwenden Sie `exp:benchmark`
   ([Benchmarks](benchmarking.md)).
3. Schalten Sie den Block ein, fragen Sie jede Adresse einmal an (der Fehlschlag), dann wieder zehnmal.
4. Unter Last (nur auf einer Website, die Sie belasten dürfen): dasselbe mit einem Lastwerkzeug und fester
   Parallelität, verglichen werden Anfragen pro Sekunde und belegte PHP-Prozesse.

Was Sie der Art nach erwarten können: Ein Treffer wird ohne PHP beantwortet, nahe an der Zeit einer statischen Datei
gleicher Größe; die PHP-Prozesse, die diese Anfragen vorher bedient haben, bleiben frei; die Datenbank sieht nur die
Fehlschläge und die angemeldeten Anfragen. Wie viel des Verkehrs zu Treffern wird, ist der Anteil anonymer
`GET`-Anfragen ohne Abfragezeichenkette an gespeicherte Adressen: Lesen Sie ihn nach einem Tag aus dem Zugriffslog
(`X-LiteSpeed-Cache` lässt sich protokollieren).

## Crawler und Bots

Crawler sind der Grund, warum viele Websites plötzlich langsam werden: Suchmaschinen, KI-Crawler, SEO-Werkzeuge und
Überwachung fragen Hunderte von Seiten in einer Minute an, parallel, alle anonym. Ohne Seiten-Cache rendert jede Anfrage
eine Seite, belegt einen PHP-Prozess und führt Datenbankabfragen aus; mit dem Block bekommt ein Crawler, der die Website
durchläuft, die erste Kopie jeder Seite, einmal je `max-age` gerendert, und jeder andere Besucher und Crawler in dieser
Zeit bekommt Treffer.

- Crawler senden keine Cookies, sie bestehen also immer die Cookie-Bedingung.
- Crawlern, die Abfragezeichenketten anhängen (`?utm_...`, Sortierparameter), hilft es nicht; `robots.txt` kann sie
  bitten, solchen Links nicht zu folgen, und kanonische Adressen in den Templates sagen ihnen, welche Adresse gilt.
- Ein Crawler, der Tausende verschiedener Seiten durchläuft, verursacht weiterhin einen Fehlschlag je Seite; das
  [Vorwärmen](caches-vorwaermen.md) wärmt die Caches des Kernels (und den von LiteSpeed, für `max-age`) im Voraus, und
  der View-Cache macht diese Fehlschläge schnell.
- Ist eine Website unter Crawlern langsam, führt das Runbook
  [Langsame Website](../hilfe/hosting/runbooks/langsame-website.md) hierher.

## Was geprüft wurde und was nicht

- **Am Code dieses Repositorys geprüft:** die Namen der Sitzungs-Cookies und wann eine Sitzung startet, das Cookie
  `is_logged_in`, die Standard-Header von Seiten, der private Header nach Sprache gewählter Siteaccesses, die Header der
  Wartungsseite, die Ereignisse `content/cache` und `content/cache/all`, die Reihenfolge der Regeln in `.htaccess_root`.
- **An der Dokumentation von LiteSpeed und OpenLiteSpeed geprüft:** `CacheLookup`, `CacheEnable`, `CacheEngine`,
  `CacheRoot`, die Umgebungsvariable `cache-control` und ihre Werte, die Werte des Headers `X-LiteSpeed-Cache`,
  `X-LiteSpeed-Purge`, die Parameter des Cache-Moduls von OpenLiteSpeed und wie OpenLiteSpeed `.htaccess` liest.
- **Hier nicht ausgeführt:** Der Block wurde für diese Anleitung nicht auf einem Server mit LiteSpeed Enterprise oder
  OpenLiteSpeed ausgeführt (auf dem Rechner, auf dem die Dokumentation geprüft wird, gibt es keinen), der
  Purge-Listener ist ein ungetestetes Beispiel, und die Standardwerte der Richtlinien „Ignore Response Cache-Control“
  und „Cache Response with Cookie“ der Server stehen nicht in der herangezogenen Dokumentation. Prüfen Sie Ihren
  eigenen Server mit [Abschnitt 11](#prüfen-ob-er-arbeitet), bevor Sie sich darauf verlassen; diese Prüfung sagt Ihnen
  in zwei Minuten, ob es funktioniert und ob es sicher ist.

## Quellen

- Kernel: `lib/ezsession/classes/ezsession.php` (`registerFunctions()`, `lazyStart()`, `siteCookieOptions()`),
  `kernel/private/classes/ezpkernelweb.php` (Standard-Header, `is_logged_in`, `Vary` und `private` für nach Sprache
  gewählte Siteaccesses), `kernel/classes/expmaintenance.php` (der `503`), `kernel/classes/ezcontentcachemanager.php`
  und `kernel/classes/ezcache.php` (`content/cache`, `content/cache/all`), `kernel/private/classes/ezpevent.php`
  (`[Event] Listeners[]`), `kernel/classes/expvelocity.php` (die Cookie-Liste von Velocity),
  `extension/ezformtoken/event/ezxformtoken.php`.
- Einstellungen: `settings/site.ini` `[Session]`, `[HTTPHeaderSettings]`, `[DebugSettings]`, `[SiteAccessSettings]`,
  `[Event]`.
- Der Block: `.htaccess_root`; für einen virtuellen Host:
  [`litespeed-page-cache.conf`](../../eng-us/examples/web-servers/litespeed-page-cache.conf); für OpenLiteSpeed:
  [`openlitespeed-vhost.conf`](../../eng-us/examples/web-servers/openlitespeed-vhost.conf).
- LiteSpeed: [LSCache without a plugin](https://docs.litespeedtech.com/lscache/noplugin/),
  [Installation und Prüfungen](https://docs.litespeedtech.com/lscache/noplugin/installation/),
  [Einstellungen der Umschreiberegeln](https://docs.litespeedtech.com/lscache/noplugin/settings/),
  [Getting Started (Cache-Wurzel und Engine)](https://docs.litespeedtech.com/lscache/start/),
  [Basic Controls des Developer Guide (Purge, Vary, Cache-Control-Header)](https://docs.litespeedtech.com/lscache/devguide/controls/),
  [LSCache unter cPanel](https://docs.litespeedtech.com/lsws/cp/cpanel/lscache/).
- OpenLiteSpeed: [das Cache-Modul](https://docs.openlitespeed.org/config/lscache/),
  [Umschreiberegeln und .htaccess](https://docs.openlitespeed.org/config/rewriterules/).

## Verwandte Seiten

- [LiteSpeed und OpenLiteSpeed](../hilfe/hosting/server/litespeed-und-openlitespeed.md): Exponential auf LiteSpeed hosten.
- [LiteSpeed und OpenLiteSpeed (Beispielkonfiguration)](../beispiele/webserver/litespeed.md) und
  [Apache .htaccess](../beispiele/webserver/apache-htaccess.md): die Regeln von `.htaccess_root` erklärt.
- [HTTP-Cache-Header](../hilfe/hosting/leistung/http-cache-header.md) und
  [Cache-Schichten](../hilfe/hosting/leistung/cache-schichten.md).
- [Varnish und Reverse Proxys](../hilfe/hosting/leistung/varnish-und-reverse-proxys.md): dieselbe Idee mit einem
  Proxy davor.
- [Caches](caches.md), [Caches vorwärmen](caches-vorwaermen.md), [Benchmarks](benchmarking.md).
- [Plesk](../hilfe/hosting/control-panels/plesk.md), [cPanel](../hilfe/hosting/control-panels/cpanel.md),
  [DirectAdmin](../hilfe/hosting/control-panels/directadmin.md).
- [Langsame Website](../hilfe/hosting/runbooks/langsame-website.md).
- [Freie Dokumentation](../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren,
  übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
