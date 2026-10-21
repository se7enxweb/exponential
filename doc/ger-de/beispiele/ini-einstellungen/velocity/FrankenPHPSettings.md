---
title: velocity.ini [FrankenPHPSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FrankenPHPSettings] in velocity.ini: Port, HTTPSPort, HTTPS, Host, Workers, SpareWorkers, DocumentRoot, Version ..."
keywords: Einstellung, velocity.ini, FrankenPHPSettings, INI, setting, Port, HTTPSPort, HTTPS, Host, Workers, SpareWorkers, DocumentRoot, Version, Variant, BinaryPath, BinarySha256, BinaryDir, AutoInstall, DownloadUrl, ApiUrl, Sha256, AdminAddress, ConfigFile, PidFile, LogFile, LogDir, AccessLog, ErrorLog, Compression, SiteInclude, PHPIniFile, IniOptions, ExtraOptions
type: generated
generated: exp:help:examples
---
# velocity.ini [FrankenPHPSettings]

Der Block `[FrankenPHPSettings]` von `settings/velocity.ini`, mit 28 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Port

> Die Engine frankenphp (ServerSettings Engine=frankenphp oder --engine=frankenphp):  
> die Produktions-Engine.  
>
> FrankenPHP ist eine eigenständige Datei: der Webserver Caddy, PHP und seine  
> Erweiterungen. Es läuft im klassischen Modus (ein Pool von PHP-Threads in einem Prozess,  
> jede Anfrage beginnt mit einem sauberen Zustand, wie unter php-fpm), daher gelten  
> ForkPerRequest und KeepGlobals des Qbix-Servers dafür nicht. Workers ist die  
> Zahl der PHP-Threads; unter Last werden SpareWorkers weitere gestartet.  
> Die eigene Adresse und der eigene Pool dieser Engine. Leer = der Wert aus \[ServerSettings\];  
> gesetzt, damit sie neben den anderen Engines laufen kann. Workers ist die Zahl der  
> PHP-Threads.  

Standard: `8089`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Port=8089
*/ ?>
```

## HTTPSPort

Standard: `8444`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
HTTPSPort=8444
*/ ?>
```

## HTTPS

> HTTPS auf HTTPSPort neben einfachem HTTP auf Port, standardmäßig an, sodass ein  
> Entwicklungsrechner ab dem ersten Start https:// hat. Es verwendet  
> \[HTTPSSettings\] Certificate und Key, wenn beide gesetzt sind; sind beide leer,  
> erzeugt es ein selbstsigniertes Zertifikat für diesen Rechner (localhost, 127.0.0.1, ::1,  
> der Hostname) in var/vc/frankenphp/tls/, ein Jahr gültig und einen Monat  
> vor Ablauf erneuert. Ein Browser warnt davor: in Ordnung für die Entwicklung und  
> hinter einem Reverse-Proxy, nicht für Besucher; eine öffentliche Website nennt ihr  
> Zertifikat hier oder beendet TLS davor. Wenn sich das selbstsignierte nicht  
> erzeugen lässt (keine Erweiterung openssl), startet der Server mit einfachem HTTP und sagt,  
> warum. \`start --no-https\` lässt es für einen Start aus, \`start --https\` besteht  
> darauf; disabled schaltet es dauerhaft ab.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
HTTPS=enabled
*/ ?>
```

## Host

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Host=
*/ ?>
```

## Workers

> PHP-Threads und zusätzliche Threads, die unter Last gestartet werden (num\_threads und  
> max\_threads). Leer = das Doppelte der CPU-Kerne und keine zusätzlichen, der eigene Standard von FrankenPHP;  
> nie \[ServerSettings\] Workers des Qbix-Servers, das Prozesse zählt,  
> die meisten davon untätig, wo dies Threads sind, die alle gleichzeitig laufen.  
> Mehr Threads als das machen hohe Last langsamer: 64 gleichzeitig gerenderte Seiten  
> schafften 16,0 Anfragen/s bei 12-48 Threads auf einem Rechner mit 12 Kernen und 14,6 bei 96 oder 590,  
> mit 24 % mehr CPU pro Seite bei 590 (gemessen am 2026-09-26).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Workers=
*/ ?>
```

## SpareWorkers

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
SpareWorkers=
*/ ?>
```

## DocumentRoot

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
DocumentRoot=
*/ ?>
```

## Version

> Das Release, das \`exp:velocity install\` herunterlädt, und die Prüfsummen, auf denen es  
> besteht. Der Wechsel zu einem neuen Release bedeutet, Version und jede Zeile Sha256\[\]  
> gemeinsam zu ändern; die Dateien sind nach der Version benannt, die alte Binärdatei bleibt also  
> neben der neuen, und zurück geht es nur über diese Einstellung.  

Standard: `1.12.7`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Version=1.12.7
*/ ?>
```

## Variant

> Welcher Build dieses Releases, unter Linux:  
>
>   gnu       gegen glibc gelinkt. Der Standard: Der Allokator von glibc ist unter PHP schneller  
>             als der von musl, und er kann .so-Erweiterungen laden (oci8 ...).  
>             Braucht glibc 2.17 oder später, jede Distribution außer Alpine.  
>   musl      vollständig statisch, braucht nichts vom System; für Alpine.  
>   mimalloc  musl mit dem Allokator mimalloc (nur x86\_64).  
>
> macOS erhält immer den einen Build, den es gibt. Die offiziellen Builds enthalten die PHP-  
> Version, die das Projekt für dieses Release gewählt hat (1.12.x: PHP 8.5).  

Standard: `gnu`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Variant=gnu
*/ ?>
```

## BinaryPath

> Eine eigene Binärdatei statt des Downloads: eine andere PHP-Version, zusätzliche Caddy-  
> Module, ein Paket der Distribution. Dann wird nichts heruntergeladen, und sie wird  
> nur gegen BinarySha256 geprüft, wenn das gesetzt ist.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
BinaryPath=
*/ ?>
```

## BinarySha256

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
BinarySha256=
*/ ?>
```

## BinaryDir

> Wohin install die Binärdatei legt. Relative Pfade gelten ab dem Wurzelverzeichnis.  

Standard: `var/vc/frankenphp/bin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
BinaryDir=var/vc/frankenphp/bin
*/ ?>
```

## AutoInstall

> Beim ersten Start installieren, wenn die Binärdatei fehlt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
AutoInstall=enabled
*/ ?>
```

## DownloadUrl

> Woher die Binärdatei kommt. {version} und {asset} werden ausgefüllt. ApiUrl wird  
> nur von install --trust-github-digest gelesen, für eine Version ohne Sha256.  

Standard: `https://github.com/php/frankenphp/releases/download/v{version}/{asset}`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
DownloadUrl=https://github.com/php/frankenphp/releases/download/v{version}/{asset}
*/ ?>
```

## ApiUrl

Standard: `https://api.github.com/repos/php/frankenphp/releases/tags/v{version}`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
ApiUrl=https://api.github.com/repos/php/frankenphp/releases/tags/v{version}
*/ ?>
```

## Sha256

> SHA-256 jedes Release-Assets von Version, aus den Digests, die GitHub veröffentlicht.  
> Ein Download, der nicht passt, wird gelöscht, nie ausgeführt.  

Standard: ein Hash mit 7: `frankenphp-linux-x86_64=378fbac43deb0585be0ad554769fe52421d1391f2cc8ca87e6201a12c5671d47`, `frankenphp-linux-x86_64-gnu=cfc17817a29895891eff5f564492dd614ed185275d25783b0f8045bc2e3f8ce2`, `frankenphp-linux-x86_64-mimalloc=8c20f12bd8177016b5e3592ba81eb3ba9e5ce217c674f078b7dcf186f987c784`, `frankenphp-linux-aarch64=a4973cdf7f23b84e8ab509638952d4ed364425967e9cdee4a1abcf2d956239c9`, `frankenphp-linux-aarch64-gnu=0118b41f4c0aa921b255d015a65dc1050e3746b7400c63e2312f50ad12a89676`, `frankenphp-mac-arm64=9f7c83fb13f61cd81c54eefb3cae9676df62bfe7af1b1fd8959a5c52665eac82`, `frankenphp-mac-x86_64=c42caa8269f1403dc539ae18d48030abe288ac7345b7606364f6ed93c6b10bdf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
# ein leeres "Sha256[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sha256[]
Sha256[frankenphp-linux-x86_64]=378fbac43deb0585be0ad554769fe52421d1391f2cc8ca87e6201a12c5671d47
Sha256[frankenphp-linux-x86_64-gnu]=cfc17817a29895891eff5f564492dd614ed185275d25783b0f8045bc2e3f8ce2
Sha256[frankenphp-linux-x86_64-mimalloc]=8c20f12bd8177016b5e3592ba81eb3ba9e5ce217c674f078b7dcf186f987c784
Sha256[frankenphp-linux-aarch64]=a4973cdf7f23b84e8ab509638952d4ed364425967e9cdee4a1abcf2d956239c9
Sha256[frankenphp-linux-aarch64-gnu]=0118b41f4c0aa921b255d015a65dc1050e3746b7400c63e2312f50ad12a89676
Sha256[frankenphp-mac-arm64]=9f7c83fb13f61cd81c54eefb3cae9676df62bfe7af1b1fd8959a5c52665eac82
Sha256[frankenphp-mac-x86_64]=c42caa8269f1403dc539ae18d48030abe288ac7345b7606364f6ed93c6b10bdf
*/ ?>
```

## AdminAddress

> Die Admin-API von Caddy, die stop und graceful verwenden. auto ist ein Unix-Socket unter  
> var/vc/frankenphp/run/admin.sock (nur für den Eigentümer, kein Port, der kollidiert), oder localhost auf dem  
> HTTP-Port + 10000, wenn der Pfad der Installation für einen Socket zu lang ist.  
> Sonst unix/<path> oder localhost:<port>.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
AdminAddress=auto
*/ ?>
```

## ConfigFile

> Die erzeugte Konfiguration, die PID-Datei und die Konsolenausgabe des Servers.  

Standard: `var/vc/frankenphp/run/Caddyfile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
ConfigFile=var/vc/frankenphp/run/Caddyfile
*/ ?>
```

## PidFile

Standard: `var/vc/frankenphp/run/server.pid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
PidFile=var/vc/frankenphp/run/server.pid
*/ ?>
```

## LogFile

Standard: `var/vc/frankenphp/run/console.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
LogFile=var/vc/frankenphp/run/console.log
*/ ?>
```

## LogDir

> Zugriffs- und Fehlerlog: access.log und error.log in LogDir (leer =  
> var/vc/frankenphp/log) oder die angegebenen Pfade. Ein eigenes Verzeichnis,  
> weil Caddy JSON schreibt: Ein Wechsel der Engine hinterlässt keine Datei in zwei  
> Formaten. Enabled und FileMode aus LogSettings gelten.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
LogDir=
*/ ?>
```

## AccessLog

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
AccessLog=
*/ ?>
```

## ErrorLog

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
ErrorLog=
*/ ?>
```

## Compression

> Antworten für Clients komprimieren, die danach fragen: die anzubietenden Codecs, in der Reihenfolge der  
> Bevorzugung, aus zstd, br und gzip; oder disabled. Caddy komprimiert nichts,  
> sofern es nicht dazu aufgefordert wird, und überspringt kleine Inhalte und Typen, die nicht schrumpfen  
> (Bilder, Archive). Ohne das ging die Startseite hier mit 95,7 KB hinaus;  
> gzip-komprimiert sind es etwa 10 KB.  

Standard: `zstd br gzip`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Compression=zstd br gzip
*/ ?>
```

## SiteInclude

> Ein Caddyfile-Fragment, das in jeden Site-Block importiert wird, für eigene Direktiven  
> (Header, Weiterleitungen, zusätzliche Routen). Die erzeugte Datei wird bei  
> jedem Start neu geschrieben und darf nicht bearbeitet werden.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
SiteInclude=
*/ ?>
```

## PHPIniFile

> Eine php.ini für den Server. Die Binärdatei liest keine der eigenen des Rechners, ohne  
> eine läuft PHP also mit seinen einkompilierten Standards plus den Optionen unten.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
PHPIniFile=
*/ ?>
```

## IniOptions

> Interpreter-Einstellungen, nach \[PHPSettings\] IniOptions\[\] (ein späterer Schlüssel gewinnt).  
> Sie ersetzen die php.ini, die die Binärdatei nicht liest: Ohne sie zeigt PHP  
> Besuchern Fehler und hört bei 128M auf. Der Opcode-Cache braucht hier kein  
> revalidate\_freq=0: Jede Anfrage hat ihre eigene Anfragezeit.  

Standard: eine Liste mit 6: `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M`, `upload_max_filesize=32M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
# ein leeres "IniOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IniOptions[]
IniOptions[]=display_errors=Off
IniOptions[]=log_errors=On
IniOptions[]=memory_limit=512M
IniOptions[]=opcache.enable=1
IniOptions[]=post_max_size=64M
IniOptions[]=upload_max_filesize=32M
*/ ?>
```

## ExtraOptions

> Zusätzliche Optionen für \`frankenphp run\`, unverändert angehängt.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
# ein leeres "ExtraOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtraOptions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
