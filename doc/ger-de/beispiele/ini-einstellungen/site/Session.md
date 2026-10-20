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

> "Remember me" feature of the login.  
> Number of seconds a session cookie will last,  
> if an user wants to store current session to the cookie  
> should click on "Remember me" checkbox on login page.  
> Empty or 0 means this ability will be disabled and CookieTimeout will be used.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
RememberMeTimeout=
*/ ?>
```

## Handler

> Exponential legacy session handler (class name) : not applicable if using the symfony stack  
> When empty uses ezpSessionHandlerPHP, a session handler that lets php remain in control  
> of the session handling (def: files, as defined by session.save\_handler in php.ini)  
> To get back old behavior for logged in/anonymous count & session clearing, use ezpSessionHandlerDB  
> and enable ForceStart setting.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
Handler=
*/ ?>
```

## SessionTimeout

> Number of seconds a session will last, 3 days is standard  
> Overwrites session.gc\_maxlifetime when used with ezpSessionHandlerPHP  

Standard: `259200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionTimeout=259200
*/ ?>
```

## ActivityTimeout

> # DB (ezpSessionHandlerDB) Handler specific settings  
> Number of seconds before a session is considered inactive/logged out, 1 hour is standard  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
ActivityTimeout=3600
*/ ?>
```

## ForceStart

> # Backward compatibility settings: not applicable if using the symfony stack  
> Makes session always start even if not needed (for anonymous users that just browse the site)  
> by default it will not start before it is used  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
ForceStart=disabled
*/ ?>
```

## SessionNameHandler

> Decides how the name of the session value is handled.  
> Possible values are:  
> default - Let PHP decide the session name, this also rules  
>           out the possiblity for per-siteaccess sessions.  
> custom  - Use SessionNamePrefix for name. per-siteaccess sessions are possible.  

Standard: `default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionNameHandler=default
*/ ?>
```

## SessionNamePrefix

> The name of the session ID, is prefixed with the current  
> siteaccess to provide login per siteaccess.  
> Only alphanumeric characters are allowed (ref: php.net/session\_name)  
> Note: Will only work if SessionNameHandler is set to custom  

Standard: `eZSESSID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionNamePrefix=eZSESSID
*/ ?>
```

## SessionNamePerSiteAccess

> Whether the session name should be unique per siteaccess.  
> By enabling this you will be able to get one unique login per siteaccess.  
> Session name is md5 encoded to avoid issues with non alphanumeric characters.  
> Note: Will only work if SessionNameHandler is set to custom  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
SessionNamePerSiteAccess=enabled
*/ ?>
```

## CookieAlwaysAddToHttpResponse

> # Optional settings for override php.ini session cookie settings  
>  Lifetime of the session cookie, defined in seconds.  
>  Leaving the field empty or undefined means to use the default PHP setting  
>  - PHP setting: session.cookie\_lifetime  
> CookieTimeout=<int>  
> Example for 6 minutes  
> CookieTimeout=360  
>  Path on the domain where the cookie will work. Use a single slash ('/') for all paths on the domain.  
>  - PHP setting: session.cookie\_path  
> CookiePath=<string>  
>  Cookie domain, for example 'www.php.net'. To make cookies visible on all subdomains then the  
>  domain must be prefixed with a dot like '.php.net'.  
>  - PHP setting: session.cookie\_domain  
> CookieDomain=<string>  
>  If <true> cookie will only be sent over secure connections. <auto>, the  
>  default, marks it Secure whenever the request itself came over HTTPS.  
> CookieSecure=auto|false|true  
>  Tells browser to not allow scripts to access cookie. Default: true.  
> CookieHttponly=true|false  
>  SameSite attribute of the session cookie: Lax (default), Strict, None  
>  (only honoured together with Secure) or empty to send none.  
>  The older name SameSite (used by LoveStack) is still read when CookieSameSite is not set,  
>  and noted once per request as deprecated (var/log/deprecated.log); rename it to CookieSameSite.  
> CookieSameSite=Lax  
>  enabled sends the session cookie with every response, not only when the session starts, for a load balancer  
>  that keeps a user on one server by the session id in the cookie. Same name, value and flags as the session  
>  cookie; only once a session is running, never to a visitor without one. Such a response is sent with  
>  Cache-Control: private, so no shared cache (proxy, CDN, service worker) keeps the session id. Default: disabled.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
CookieAlwaysAddToHttpResponse=disabled
*/ ?>
```

## BasketCleanup

> Controls how baskets are cleaned up when session expires.  
> It can take on these values:  
> - cronjob - Cleanup values in a cronjob, Note: runcronjobs.php must be run  
> - pageload - Cleanup on pageload when the session expires (Controlled by PHP)  

Standard: `cronjob`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanup=cronjob
*/ ?>
```

## BasketCleanupTime

> Controls the number of seconds the cleanup can run  
> The default is 2 minutes  

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanupTime=120
*/ ?>
```

## BasketCleanupIdleTime

> Controls how many seconds the cleanup will stay idle between  
> operations. This can be increased to help keep the load down  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanupIdleTime=2
*/ ?>
```

## BasketCleanupFetchLimit

> The maximum number of basket entries it will fetch in one batch  
> Can be increased to improve the amount of time it uses for each  
> cronjob run or decreased to reduce memory usage and load on server.  

Standard: `3000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Session]
BasketCleanupFetchLimit=3000
*/ ?>
```

## BasketCleanupAverageFrequency

> Controls how often baskets are cleaned up in the cronjob  
> A value of 10 means it will clean up in average 1 out of  
> 10 times.  

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
