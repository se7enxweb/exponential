---
title: velocity.ini [DeploySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DeploySettings] in velocity.ini: PhpFpmService, PhpFpmReload, WarmUpURL"
keywords: Einstellung, velocity.ini, DeploySettings, INI, setting, PhpFpmService, PhpFpmReload, WarmUpURL
type: generated
generated: exp:help:examples
---
# velocity.ini [DeploySettings]

Der Block `[DeploySettings]` von `settings/velocity.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PhpFpmService

> exp:velocity deploy: everything a PHP code change needs, in order.  
>
> The PHP-FPM service it reloads, so the web server in front of this  
> installation (Apache, nginx) runs the new code too:  
>
>   auto       find it: the FPM pool named after this installation's hosting  
>              domain (/var/www/vhosts/<domain>/..., as Plesk names pools), or  
>              one whose configuration names this installation's directory.  
>              Looked for in /opt/plesk/php/\*/etc/php-fpm.d (Plesk, service  
>              plesk-php<XY>-fpm), /etc/php-fpm.d (php-fpm), /etc/php/\*/fpm/pool.d  
>              (php<X.Y>-fpm) and /etc/opt/remi/php\*/php-fpm.d (php<XY>-php-fpm).  
>   disabled   reload nothing; for an installation served by Velocity alone.  
>   <unit>     that systemd service, or several, comma-separated.  
>
> It is a reload (systemctl reload): the master re-reads its code and replaces  
> its workers as each finishes, so no request is dropped. Needs root; run as  
> anyone else, the step is skipped with a note naming the service.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[DeploySettings]
PhpFpmService=auto
*/ ?>
```

## PhpFpmReload

> Whether that reload is done at all:  
>
>   auto       only when needed. When the pool's OPcache revalidates the files  
>              (opcache.validate\_timestamps on, revalidate\_freq at most 60 s,  
>              read from the php-fpm binary and the pool file), every changed  
>              PHP file is recompiled within revalidate\_freq seconds; the step  
>              waits that long instead of reloading. Without OPcache it waits  
>              for nothing.  
>   always     reload every time (as before).  
>   never      neither reload nor wait.  
>
> A reload is not free, and not for this site alone: the master asks every  
> child of every pool to finish its request and starts no new child until the  
> last one has (up to process\_control\_timeout, 300 s on Plesk). Each request  
> that arrives in between waits for the longest one running on any site of  
> that PHP version: one open 25 s event stream made the next page wait 22 s,  
> and a long request elsewhere turned waiting requests into 504s (#374).  
> deploy --fpm-reload forces a reload: after a php.ini or pool change, a new  
> PHP extension, or PHP files removed or renamed in place.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[DeploySettings]
PhpFpmReload=auto
*/ ?>
```

## WarmUpURL

> Pages requested once each, one after the other, by a background process at  
> the end of a deploy, so the first visitor does not wait while the templates  
> are compiled and the INI, override, translation, design and packed-file  
> caches are rebuilt (and several first visitors do not rebuild them at the  
> same time). Anonymous requests: a site's front page, an admin's login page.  
> The deploy does not wait for them. Empty: no warm-up.  
>
> WarmUpURL\[\]=https://www.example.com/  
> WarmUpURL\[\]=https://www.example.com/admin/user/login  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[DeploySettings]
# ein leeres "WarmUpURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
WarmUpURL[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
