---
title: Eine Datei security.txt hinzufügen
audience: government, admin, hosting
category: public-sector
order: 380
summary: Schritt für Schritt - /.well-known/security.txt (RFC 9116) für Ihre Site veröffentlichen, das Exponential nicht mitliefert, die eine Rewrite-Regel hinzufügen, die die mitgelieferten Apache-Regeln zum Ausliefern brauchen, und es mit curl prüfen.
keywords: security.txt, RFC 9116, .well-known, Meldung von Schwachstellen, Contact, Expires, Policy, Rewrite-Regel, htaccess, nginx, Velocity, öffentlicher Sektor, vulnerability disclosure, rewrite rule, public sector
type: help
---
# Eine Datei security.txt hinzufügen

Für Administratoren und Hosting-Teams. Eine `security.txt` sagt Forschenden, wo sie Schwachstellen Ihrer Site melden
können. Die Erklärung des Projekts führt sie als nicht mitgeliefert
([security.txt hinzufügen](../../sicherheit/security-txt-hinzufuegen.md)).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../../beispiele/webserver/litespeed.md), [Caddy](../../../../beispiele/webserver/caddy.md), [IIS](../../../../beispiele/webserver/iis.md),
> [Velocity](../../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../../hosting/control-panels/plesk.md), [cPanel](../../../hosting/control-panels/cpanel.md), [DirectAdmin](../../../hosting/control-panels/directadmin.md), [ISPConfig](../../../hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../../anleitungen/seiten-cache-mit-litespeed.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Postfach für Sicherheitskontakte und idealerweise eine veröffentlichte Seite mit Ihrer Richtlinie zur Meldung
  von Schwachstellen (siehe „Meldung von Schwachstellen“).
- Zugriff auf das Document Root und die Konfiguration des Webservers.

## Schritte

1. Legen Sie `.well-known/security.txt` im Document Root an:

   ```
   Contact: mailto:security@example.gov
   Expires: 2027-10-31T23:00:00.000Z
   Policy: https://www.example.gov/vulnerability-disclosure
   Preferred-Languages: en
   Canonical: https://www.example.gov/.well-known/security.txt
   ```

   `Contact` und `Expires` verlangt RFC 9116 (<https://www.rfc-editor.org/rfc/rfc9116>); halten Sie `Expires` weniger
   als ein Jahr in der Zukunft.
2. **Apache mit `.htaccess_root`**: Punkt-Pfade außer `.well-known` antworten mit 404, und die letzte Regel schickt
   alles andere an `index.php`. Fügen Sie vor `RewriteRule .* index.php` hinzu:

   ```apache
   RewriteRule ^\.well-known/security\.txt$ - [L]
   ```

3. **nginx**: Stellen Sie sicher, dass eine `location` die Datei ausliefert, bevor Anfragen an `index.php`
   durchfallen.
4. Nehmen Sie die Datei (und die Regel) in Ihr Deployment auf, damit das nächste Release sie behält.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://www.example.gov/.well-known/security.txt | head -3
curl -s https://www.example.gov/.well-known/security.txt
```

Status 200, ein Typ für einfachen Text, Ihre Datei. Eine HTML-Seite bedeutet, dass die Anfrage bei `index.php`
angekommen ist. Tragen Sie das Datum aus `Expires` in Ihren Kalender ein.

## Verwandte Seiten

- [security.txt hinzufügen](../../sicherheit/security-txt-hinzufuegen.md)
- [Meldung von Schwachstellen](../../sicherheit/offenlegung-von-schwachstellen.md)
- [Eine nützliche robots.txt ab Werk](../../../../funktionen/6.0/robots-txt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
