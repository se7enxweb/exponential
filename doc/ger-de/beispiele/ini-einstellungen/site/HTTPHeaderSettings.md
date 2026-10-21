---
title: site.ini [HTTPHeaderSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HTTPHeaderSettings] in site.ini: CustomHeader, OnlyForAnonymous, OnlyForContent, HeaderList, Cache-Control, Pragma, Expires, ClientIpByCustomHTTPHeader ..."
keywords: Einstellung, site.ini, HTTPHeaderSettings, INI, setting, CustomHeader, OnlyForAnonymous, OnlyForContent, HeaderList, Cache-Control, Pragma, Expires, ClientIpByCustomHTTPHeader, TrustedProxies, SecurityHeaders
type: generated
generated: exp:help:examples
---
# site.ini [HTTPHeaderSettings]

Der Block `[HTTPHeaderSettings]` von `settings/site.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CustomHeader

> Eigene HTTP-Header-Daten aktivieren/deaktivieren.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
CustomHeader=disabled
*/ ?>
```

## OnlyForAnonymous

> Eigene Header nur für anonyme Benutzer anwenden  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
OnlyForAnonymous=disabled
*/ ?>
```

## OnlyForContent

> Die eigenen Ausweich-Header der Wurzel \[/\] nur für das Modul content/view anwenden  
> Wenn deaktiviert, werden die eigenen Header des Wurzelpfads auf alle Module und ihre Ansichten angewendet  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
OnlyForContent=enabled
*/ ?>
```

## HeaderList

> Header-Liste. Enthält alle HTTP-Header, die die Standard-Header überschreiben sollen.  

Standard: eine Liste mit 3: `Cache-Control`, `Pragma`, `Expires`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "HeaderList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HeaderList[]
HeaderList[]=Cache-Control
HeaderList[]=Pragma
HeaderList[]=Expires
*/ ?>
```

## Cache-Control

> Standard-Header Cache-Control  
> HTTP-Header werden in folgendem Format angegeben:  
> <HTTP header>\[<Exponential path|module{/view}>\]=<value>{;<depth>{;<level>}}  
> Hinweis: Um ein Semikolon im Wert zu verwenden, maskieren Sie es mit einem Backslash.  
>
> Beispiel:  
> # Set Pragma HTTP header to no-cache for whole site, except /news, and 2 levels below news.  
> Pragma\[\]  
> Pragma\[/\]=no-cache;2  
> Pragma\[/news\]=;2;0  
> Werte von Cache-Control werden direkt gesetzt  

Standard: ein Hash mit 1: `/=no-cache, must-revalidate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "Cache-Control[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Cache-Control[]
Cache-Control[/]=no-cache, must-revalidate
*/ ?>
```

## Pragma

> Werte von Pragma werden direkt gesetzt  

Standard: ein Hash mit 1: `/=no-cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "Pragma[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Pragma[]
Pragma[/]=no-cache
*/ ?>
```

## Expires

> Expires gibt einen Zeitversatz zur aktuellen Zeit an  
> Standard ist vor 2 Stunden abgelaufen (kein Caching)  

Standard: ein Hash mit 1: `/=-7200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "Expires[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Expires[]
Expires[/]=-7200
*/ ?>
```

## ClientIpByCustomHTTPHeader

> Einen eigenen HTTP-Header verwenden, um die IP des Clients zu ermitteln  
> false ist der Standardwert  
> Der Header wird nur für eine Anfrage von einem der TrustedProxies unten gelesen,  
> und von rechts: Vertrauenswürdige Proxys werden übersprungen, und die erste andere Adresse  
> ist der Client. Einträge weiter links sind das, was der Client gesendet hat, und werden ignoriert.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
ClientIpByCustomHTTPHeader=false
*/ ?>
```

## TrustedProxies

> Nützliches Beispiel, wenn sich Clients über einen (Reverse-)Proxy verbinden  
> ClientIpByCustomHTTPHeader=X-Forwarded-For  
> Die Reverse-Proxys und Lastverteiler vor dieser Website, deren weitergeleiteten  
> Headern man glauben kann: X-Forwarded-For (mit ClientIpByCustomHTTPHeader),  
> X-Forwarded-Proto, X-Forwarded-Port, X-Forwarded-Server und X-Forwarded-Host.  
> Es sind Anfrage-Header, die jeder Besucher senden kann, sie zählen also nur, wenn die  
> Verbindung von einer dieser Adressen kommt (REMOTE\_ADDR). Adressen und  
> Adress-/Präfixbereiche, IPv4 und IPv6, z. B.  
>  TrustedProxies\[\]=10.0.0.0/8  
>  TrustedProxies\[\]=192.0.2.10  
>  TrustedProxies\[\]=2001:db8::/32  
> Der Standard vertraut nur einem Proxy auf diesem Rechner. Ein vertrauenswürdiger Proxy muss  
> diese Header selbst setzen, nicht weitergeben, was der Besucher gesendet hat.  
> Ein Override, das mit "TrustedProxies\[\]" allein beginnt, ersetzt die Liste;  
> lassen Sie sie leer, um gar keinem Proxy zu vertrauen.  
> Siehe doc/eng-us/bc/6.0/trusted-proxies.md.  

Standard: eine Liste mit 2: `127.0.0.1`, `::1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "TrustedProxies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
*/ ?>
```

## SecurityHeaders

> Sicherheits-Header, die mit jeder Seite gesendet werden, SecurityHeaders\[<header>\]=<value>.  
> Setzen Sie einen Wert in einem Override leer, um diesen Header wegzulassen. HeaderList oben  
> überschreibt diese weiterhin pro Pfad.  
>  - frame-ancestors / X-Frame-Options: Seiten dürfen nur von derselben Website  
>    eingerahmt werden (Clickjacking); erlauben Sie einen vertrauenswürdigen Editor-Host ausdrücklich, z. B.  
>    SecurityHeaders\[Content-Security-Policy\]=frame-ancestors 'self' https://edit.example.com  
>  - Strict-Transport-Security wird nur über HTTPS gesendet und ist standardmäßig  
>    aus: Aktivieren Sie es, sobald die ganze Website nur noch HTTPS verwendet, z. B.  
>    SecurityHeaders\[Strict-Transport-Security\]=max-age=31536000  

Standard: ein Hash mit 6: `X-Content-Type-Options=nosniff`, `X-Frame-Options=SAMEORIGIN`, `Content-Security-Policy=frame-ancestors 'self'`, `Referrer-Policy=strict-origin-when-cross-origin`, `Permissions-Policy=camera=(), microphone=(), payment=(), usb=()`, `Strict-Transport-Security=`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "SecurityHeaders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityHeaders[]
SecurityHeaders[X-Content-Type-Options]=nosniff
SecurityHeaders[X-Frame-Options]=SAMEORIGIN
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self'
SecurityHeaders[Referrer-Policy]=strict-origin-when-cross-origin
SecurityHeaders[Permissions-Policy]=camera=(), microphone=(), payment=(), usb=()
SecurityHeaders[Strict-Transport-Security]=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
