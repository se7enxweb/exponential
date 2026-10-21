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

> exp:velocity deploy: alles, was eine Änderung am PHP-Code braucht, in Reihenfolge.  
>
> Der PHP-FPM-Dienst, den es neu lädt, damit auch der Webserver vor dieser  
> Installation (Apache, nginx) den neuen Code ausführt:  
>
>   auto       ihn finden: der FPM-Pool, benannt nach der Hosting-Domain dieser Installation  
>              (/var/www/vhosts/<domain>/..., wie Plesk Pools benennt), oder  
>              einer, dessen Konfiguration das Verzeichnis dieser Installation nennt.  
>              Gesucht in /opt/plesk/php/\*/etc/php-fpm.d (Plesk, Dienst  
>              plesk-php<XY>-fpm), /etc/php-fpm.d (php-fpm), /etc/php/\*/fpm/pool.d  
>              (php<X.Y>-fpm) und /etc/opt/remi/php\*/php-fpm.d (php<XY>-php-fpm).  
>   disabled   nichts neu laden; für eine Installation, die nur Velocity bedient.  
>   <unit>     dieser systemd-Dienst, oder mehrere, durch Kommas getrennt.  
>
> Es ist ein Neuladen (systemctl reload): Der Master liest seinen Code neu und ersetzt  
> seine Worker, sobald jeder fertig ist, sodass keine Anfrage verloren geht. Braucht root; von  
> jemand anderem ausgeführt, wird der Schritt mit einem Hinweis übersprungen, der den Dienst nennt.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[DeploySettings]
PhpFpmService=auto
*/ ?>
```

## PhpFpmReload

> Ob dieses Neuladen überhaupt geschieht:  
>
>   auto       nur bei Bedarf. Wenn der OPcache des Pools die Dateien neu prüft  
>              (opcache.validate\_timestamps an, revalidate\_freq höchstens 60 s,  
>              aus der php-fpm-Binärdatei und der Pool-Datei gelesen), wird jede geänderte  
>              PHP-Datei innerhalb von revalidate\_freq Sekunden neu kompiliert; der Schritt  
>              wartet so lange, statt neu zu laden. Ohne OPcache wartet er  
>              auf nichts.  
>   always     jedes Mal neu laden (wie bisher).  
>   never      weder neu laden noch warten.  
>
> Ein Neuladen ist nicht umsonst, und nicht nur für diese Website: Der Master bittet jedes  
> Kind jedes Pools, seine Anfrage abzuschließen, und startet kein neues Kind, bis das  
> letzte das getan hat (bis zu process\_control\_timeout, 300 s bei Plesk). Jede Anfrage,  
> die dazwischen eintrifft, wartet auf die längste, die auf einer Website dieser  
> PHP-Version läuft: Ein offener Event-Stream von 25 s ließ die nächste Seite 22 s warten,  
> und eine lange Anfrage anderswo machte aus wartenden Anfragen 504 (#374).  
> deploy --fpm-reload erzwingt ein Neuladen: nach einer Änderung an php.ini oder am Pool, einer neuen  
> PHP-Erweiterung oder an Ort und Stelle entfernten oder umbenannten PHP-Dateien.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[DeploySettings]
PhpFpmReload=auto
*/ ?>
```

## WarmUpURL

> Seiten, die am Ende eines Deployments von einem Hintergrundprozess je einmal nacheinander  
> angefordert werden, damit der erste Besucher nicht wartet, während die Templates  
> kompiliert und die Caches für INI, Override, Übersetzung, Design und gepackte Dateien  
> neu aufgebaut werden (und nicht mehrere erste Besucher sie gleichzeitig  
> neu aufbauen). Anonyme Anfragen: die Startseite einer Website, die Anmeldeseite einer Administration.  
> Das Deployment wartet nicht auf sie. Leer: kein Vorwärmen.  
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
