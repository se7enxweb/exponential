---
title: site.ini [Session]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Session] in site.ini: RememberMeTimeout, Handler, SessionTimeout, ActivityTimeout, ForceStart, SessionNameHandler, SessionNamePrefix, SessionNamePerSiteAccess ..."
keywords: Einstellung, site.ini, Session, INI, setting, RememberMeTimeout, Handler, SessionTimeout, ActivityTimeout, ForceStart, SessionNameHandler, SessionNamePrefix, SessionNamePerSiteAccess, CookieAlwaysAddToHttpResponse, BasketCleanup, BasketCleanupTime, BasketCleanupIdleTime, BasketCleanupFetchLimit, BasketCleanupAverageFrequency
type: generated
generated: exp:help:examples
---
# site.ini [Session]

Der Block `[Session]` von `settings/site.ini`, mit 14 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RememberMeTimeout

> Die Funktion „Angemeldet bleiben“ der Anmeldung.  
> Sekunden, die ein Sitzungs-Cookie hält,  
> wenn ein Benutzer die aktuelle Sitzung im Cookie speichern will und  
> auf der Anmeldeseite das Kontrollkästchen „Angemeldet bleiben“ anklickt.  
> Leer oder 0 bedeutet, dass diese Möglichkeit deaktiviert ist und CookieTimeout verwendet wird.  
> 360 Tage, und das Kästchen ist auf den Anmeldeformularen vorausgewählt: Browser, die  
> Sitzungs-Cookies von sich aus verwerfen (iOS Safari und Chrome), würden den Benutzer  
> sonst abmelden. Die Lebensdauer gilt für die ganze Anmeldung, auch wenn die Sitzungs-ID  
> später wechselt (der zweite Schritt der 2FA). SessionTimeout mindestens ebenso lang halten.  

Standard: `31104000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
RememberMeTimeout=31104000
*/ ?>
```

## Handler

> Session-Handler von Exponential Legacy (Klassenname): nicht anwendbar mit dem Symfony-Stack  
> Wenn leer, wird ezpSessionHandlerPHP verwendet, ein Session-Handler, der PHP die Kontrolle  
> über die Sitzungsverwaltung lässt (Standard: files, wie in session.save\_handler in php.ini festgelegt)  
> Um das alte Verhalten für die Zählung angemeldeter/anonymer Benutzer und das Leeren von Sitzungen zurückzuerhalten, verwenden Sie ezpSessionHandlerDB  
> und aktivieren Sie die Einstellung ForceStart.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
Handler=
*/ ?>
```

## SessionTimeout

> Sekunden, die eine unbenutzte Sitzung hält, 360 Tage (wie RememberMeTimeout:  
> der Server behält eine ruhende Sitzung so lange, wie ein Angemeldet-bleiben-Cookie sie zurückholen kann;  
> 3 Tage, der frühere Standard, meldeten solche Benutzer nach einem langen Wochenende ab)  
> Überschreibt session.gc\_maxlifetime, wenn ezpSessionHandlerPHP verwendet wird  

Standard: `31104000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionTimeout=31104000
*/ ?>
```

## ActivityTimeout

> # Einstellungen speziell für den Handler DB (ezpSessionHandlerDB)  
> Sekunden, bis eine Sitzung als inaktiv/abgemeldet gilt; 1 Stunde ist Standard  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
ActivityTimeout=3600
*/ ?>
```

## ForceStart

> # Einstellungen zur Rückwärtskompatibilität: nicht anwendbar mit dem Symfony-Stack  
> Lässt die Sitzung immer starten, auch wenn sie nicht gebraucht wird (für anonyme Benutzer, die nur die Website besuchen)  
> standardmäßig startet sie erst, wenn sie verwendet wird  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
ForceStart=disabled
*/ ?>
```

## SessionNameHandler

> Entscheidet, wie der Name des Sitzungswerts behandelt wird.  
> Mögliche Werte sind:  
> default - PHP entscheidet über den Sitzungsnamen; das schließt auch  
>           Sitzungen pro Siteaccess aus.  
> custom  - SessionNamePrefix als Name verwenden; Sitzungen pro Siteaccess sind möglich.  

Standard: `default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionNameHandler=default
*/ ?>
```

## SessionNamePrefix

> Dem Namen der Sitzungs-ID wird der aktuelle  
> Siteaccess vorangestellt, um eine Anmeldung pro Siteaccess zu ermöglichen.  
> Nur alphanumerische Zeichen sind erlaubt (siehe php.net/session\_name)  
> Hinweis: Funktioniert nur, wenn SessionNameHandler auf custom steht  

Standard: `eZSESSID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionNamePrefix=eZSESSID
*/ ?>
```

## SessionNamePerSiteAccess

> Ob der Sitzungsname pro Siteaccess eindeutig sein soll.  
> Wenn Sie das aktivieren, erhalten Sie pro Siteaccess eine eigene Anmeldung.  
> Der Sitzungsname ist md5-kodiert, um Probleme mit nicht alphanumerischen Zeichen zu vermeiden.  
> Hinweis: Funktioniert nur, wenn SessionNameHandler auf custom steht  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionNamePerSiteAccess=enabled
*/ ?>
```

## CookieAlwaysAddToHttpResponse

> # Optionale Einstellungen zum Überschreiben der Sitzungs-Cookie-Einstellungen aus php.ini  
>  Lebensdauer des Sitzungs-Cookies in Sekunden.  
>  Ein leeres oder nicht definiertes Feld bedeutet die Standardeinstellung von PHP  
>  - PHP-Einstellung: session.cookie\_lifetime  
> CookieTimeout=<int>  
> Beispiel für 6 Minuten  
> CookieTimeout=360  
>  Pfad auf der Domain, auf dem das Cookie gilt. Ein einzelner Schrägstrich ('/') für alle Pfade der Domain.  
>  - PHP-Einstellung: session.cookie\_path  
> CookiePath=<string>  
>  Cookie-Domain, zum Beispiel 'www.php.net'. Um Cookies auf allen Subdomains sichtbar zu machen, muss der  
>  Domain ein Punkt vorangestellt sein, etwa '.php.net'.  
>  - PHP-Einstellung: session.cookie\_domain  
> CookieDomain=<string>  
>  Bei <true> wird das Cookie nur über sichere Verbindungen gesendet. <auto>, der  
>  Standard, markiert es als Secure, sobald die Anfrage selbst über HTTPS kam.  
> CookieSecure=auto|false|true  
>  Sagt dem Browser, Skripten keinen Zugriff auf das Cookie zu erlauben. Standard: true.  
> CookieHttponly=true|false  
>  Attribut SameSite des Sitzungs-Cookies: Lax (Standard), Strict, None  
>  (nur zusammen mit Secure beachtet) oder leer, um keines zu senden.  
>  Der ältere Name SameSite (von LoveStack verwendet) wird weiterhin gelesen, wenn CookieSameSite nicht gesetzt ist,  
>  und einmal pro Anfrage als veraltet vermerkt (var/log/deprecated.log); benennen Sie ihn in CookieSameSite um.  
> CookieSameSite=Lax  
>  enabled sendet das Sitzungs-Cookie mit jeder Antwort, nicht nur beim Start der Sitzung, für einen Lastverteiler,  
>  der einen Benutzer über die Sitzungs-ID im Cookie auf einem Server hält. Gleicher Name, Wert und gleiche Flags wie das Sitzungs-  
>  Cookie; nur sobald eine Sitzung läuft, nie an einen Besucher ohne eine. Eine solche Antwort wird mit  
>  Cache-Control: private gesendet, damit kein gemeinsamer Cache (Proxy, CDN, Service Worker) die Sitzungs-ID behält. Standard: disabled.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
CookieAlwaysAddToHttpResponse=disabled
*/ ?>
```

## BasketCleanup

> Steuert, wie Warenkörbe aufgeräumt werden, wenn die Sitzung abläuft.  
> Mögliche Werte:  
> - cronjob - Aufräumen in einem Cronjob; Hinweis: runcronjobs.php muss ausgeführt werden  
> - pageload - Aufräumen beim Laden einer Seite, wenn die Sitzung abläuft (von PHP gesteuert)  

Standard: `cronjob`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanup=cronjob
*/ ?>
```

## BasketCleanupTime

> Steuert, wie viele Sekunden das Aufräumen laufen darf  
> Standard sind 2 Minuten  

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanupTime=120
*/ ?>
```

## BasketCleanupIdleTime

> Steuert, wie viele Sekunden das Aufräumen zwischen  
> Operationen pausiert. Ein höherer Wert hilft, die Last niedrig zu halten  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanupIdleTime=2
*/ ?>
```

## BasketCleanupFetchLimit

> Die Höchstzahl der Warenkorbeinträge, die in einem Stapel geholt werden  
> Kann erhöht werden, um die Zeit pro  
> Cronjob-Lauf zu verbessern, oder verringert, um Speicherverbrauch und Serverlast zu senken.  

Standard: `3000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanupFetchLimit=3000
*/ ?>
```

## BasketCleanupAverageFrequency

> Steuert, wie oft Warenkörbe im Cronjob aufgeräumt werden  
> Ein Wert von 10 bedeutet, dass im Durchschnitt bei 1 von  
> 10 Läufen aufgeräumt wird.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanupAverageFrequency=10
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
