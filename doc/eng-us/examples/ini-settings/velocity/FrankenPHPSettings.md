---
title: velocity.ini [FrankenPHPSettings]
audience: developer, admin
category: examples
summary: "The settings of [FrankenPHPSettings] in velocity.ini: Port, HTTPSPort, HTTPS, Host, Workers, SpareWorkers, DocumentRoot, Version ..."
keywords: velocity.ini, FrankenPHPSettings, INI, setting, Port, HTTPSPort, HTTPS, Host, Workers, SpareWorkers, DocumentRoot, Version, Variant, BinaryPath, BinarySha256, BinaryDir, AutoInstall, DownloadUrl, ApiUrl, Sha256, AdminAddress, ConfigFile, PidFile, LogFile, LogDir, AccessLog, ErrorLog, Compression, SiteInclude, PHPIniFile, IniOptions, ExtraOptions
type: generated
generated: exp:help:examples
---
# velocity.ini [FrankenPHPSettings]

The block `[FrankenPHPSettings]` of `settings/velocity.ini`, with 28 setting(s).

Never edit `settings/velocity.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/velocity.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in an extension (read when the extension is active).

## Port

> The frankenphp engine (ServerSettings Engine=frankenphp, or --engine=frankenphp):  
> the production engine.  
>
> FrankenPHP is one self-contained file: the Caddy web server, PHP and its  
> extensions. It runs in classic mode -- a pool of PHP threads in one process,  
> each request starting from a clean state, as under php-fpm -- so the Qbix  
> server's ForkPerRequest and KeepGlobals do not apply to it. Workers is the  
> number of PHP threads; SpareWorkers more are started under load.  
> This engine's own address and pool. Empty = the \[ServerSettings\] value;  
> set, so that it can run beside the other engines. Workers is the number of  
> PHP threads.  

Default: `8089`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Port=8089
*/ ?>
```

## HTTPSPort

Default: `8444`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
HTTPSPort=8444
*/ ?>
```

## HTTPS

> HTTPS on HTTPSPort beside plain HTTP on Port -- on by default, so a  
> development machine has https:// from the first start. It uses  
> \[HTTPSSettings\] Certificate and Key when both are set; with both empty it  
> makes a self-signed certificate for this machine (localhost, 127.0.0.1, ::1,  
> the host name) in var/vc/frankenphp/tls/, valid a year and renewed a month  
> before it runs out. A browser warns about that one: fine for development and  
> behind a reverse proxy, not for visitors -- a public site names its  
> certificate here or terminates TLS in front. When the self-signed one cannot  
> be made (no openssl extension) the server starts with plain HTTP and says  
> why. \`start --no-https\` leaves it off for one start, \`start --https\` insists  
> on it; disabled switches it off for good.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
HTTPS=enabled
*/ ?>
```

## Host

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Host=
*/ ?>
```

## Workers

> PHP threads, and extra threads started under load (num\_threads and  
> max\_threads). Empty = twice the CPU cores and no extra, FrankenPHP's own  
> default -- never the Qbix server's \[ServerSettings\] Workers, which counts  
> processes, most of them idle, where these are threads that all run at once.  
> More threads than that makes heavy load slower: 64 concurrent rendered pages  
> did 16.0 req/s at 12-48 threads on a 12-core machine and 14.6 at 96 or 590,  
> with 24% more CPU per page at 590 (measured 2026-09-26).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Workers=
*/ ?>
```

## SpareWorkers

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
SpareWorkers=
*/ ?>
```

## DocumentRoot

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
DocumentRoot=
*/ ?>
```

## Version

> The release \`exp:velocity install\` downloads, and the checksums it insists  
> on. Moving to a new release means changing Version and every Sha256\[\] line  
> together; the files are named after the version, so the old binary stays  
> next to the new one and going back is only this setting.  

Default: `1.12.7`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Version=1.12.7
*/ ?>
```

## Variant

> Which build of that release, on Linux:  
>
>   gnu       linked against glibc. The default: glibc's allocator is faster  
>             than musl's under PHP, and it can load .so extensions (oci8 ...).  
>             Needs glibc 2.17 or later -- any distribution but Alpine.  
>   musl      fully static, needs nothing from the system; for Alpine.  
>   mimalloc  musl with the mimalloc allocator (x86\_64 only).  
>
> macOS always gets the one build there is. The official builds carry the PHP  
> version the project chose for that release (1.12.x: PHP 8.5).  

Default: `gnu`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Variant=gnu
*/ ?>
```

## BinaryPath

> An own binary instead of the download: another PHP version, extra Caddy  
> modules, a distribution package. Nothing is downloaded then, and it is  
> checked against BinarySha256 only when that is set.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
BinaryPath=
*/ ?>
```

## BinarySha256

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
BinarySha256=
*/ ?>
```

## BinaryDir

> Where install puts the binary. Relative paths resolve against the root.  

Default: `var/vc/frankenphp/bin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
BinaryDir=var/vc/frankenphp/bin
*/ ?>
```

## AutoInstall

> Install on the first start when the binary is missing.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
AutoInstall=enabled
*/ ?>
```

## DownloadUrl

> Where the binary comes from. {version} and {asset} are filled in. ApiUrl is  
> read only by install --trust-github-digest, for a version with no Sha256.  

Default: `https://github.com/php/frankenphp/releases/download/v{version}/{asset}`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
DownloadUrl=https://github.com/php/frankenphp/releases/download/v{version}/{asset}
*/ ?>
```

## ApiUrl

Default: `https://api.github.com/repos/php/frankenphp/releases/tags/v{version}`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
ApiUrl=https://api.github.com/repos/php/frankenphp/releases/tags/v{version}
*/ ?>
```

## Sha256

> SHA-256 of every release asset of Version, from the digests GitHub publishes.  
> A download that does not match is deleted, never run.  

Default: a hash of 7: `frankenphp-linux-x86_64=378fbac43deb0585be0ad554769fe52421d1391f2cc8ca87e6201a12c5671d47`, `frankenphp-linux-x86_64-gnu=cfc17817a29895891eff5f564492dd614ed185275d25783b0f8045bc2e3f8ce2`, `frankenphp-linux-x86_64-mimalloc=8c20f12bd8177016b5e3592ba81eb3ba9e5ce217c674f078b7dcf186f987c784`, `frankenphp-linux-aarch64=a4973cdf7f23b84e8ab509638952d4ed364425967e9cdee4a1abcf2d956239c9`, `frankenphp-linux-aarch64-gnu=0118b41f4c0aa921b255d015a65dc1050e3746b7400c63e2312f50ad12a89676`, `frankenphp-mac-arm64=9f7c83fb13f61cd81c54eefb3cae9676df62bfe7af1b1fd8959a5c52665eac82`, `frankenphp-mac-x86_64=c42caa8269f1403dc539ae18d48030abe288ac7345b7606364f6ed93c6b10bdf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
# an empty "Sha256[]" first replaces the shipped list; without it the items are added
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

> Caddy's admin API, which stop and graceful use. auto is a unix socket at  
> var/vc/frankenphp/run/admin.sock (owner-only, no port to clash), or localhost on the  
> HTTP port + 10000 when the installation's path is too long for a socket.  
> Otherwise unix/<path> or localhost:<port>.  

Default: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
AdminAddress=auto
*/ ?>
```

## ConfigFile

> The generated configuration, the pid file and the server's console output.  

Default: `var/vc/frankenphp/run/Caddyfile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
ConfigFile=var/vc/frankenphp/run/Caddyfile
*/ ?>
```

## PidFile

Default: `var/vc/frankenphp/run/server.pid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
PidFile=var/vc/frankenphp/run/server.pid
*/ ?>
```

## LogFile

Default: `var/vc/frankenphp/run/console.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
LogFile=var/vc/frankenphp/run/console.log
*/ ?>
```

## LogDir

> Access and error log: access.log and error.log in LogDir (empty =  
> var/vc/frankenphp/log), or the paths given. A directory of its own  
> because Caddy writes JSON: switching engines does not leave one file in two  
> formats. LogSettings Enabled and FileMode apply.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
LogDir=
*/ ?>
```

## AccessLog

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
AccessLog=
*/ ?>
```

## ErrorLog

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
ErrorLog=
*/ ?>
```

## Compression

> Compress responses for clients that ask: the codecs to offer, in order of  
> preference, from zstd, br and gzip; or disabled. Caddy compresses nothing  
> unless told to, and skips small bodies and types that do not shrink  
> (images, archives). Without it the front page went out at 95.7 KB here;  
> gzipped it is about 10 KB.  

Default: `zstd br gzip`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
Compression=zstd br gzip
*/ ?>
```

## SiteInclude

> A Caddyfile fragment imported into every site block, for directives of your  
> own (headers, redirects, extra routes). The generated file is rewritten on  
> every start and must not be edited.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
SiteInclude=
*/ ?>
```

## PHPIniFile

> A php.ini for the server. The binary reads none of the machine's own, so  
> without one PHP runs on its compiled-in defaults plus the options below.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
PHPIniFile=
*/ ?>
```

## IniOptions

> Interpreter settings, after \[PHPSettings\] IniOptions\[\] (a later key wins).  
> These stand in for the php.ini the binary does not read: without them PHP  
> shows errors to visitors and stops at 128M. The opcode cache needs no  
> revalidate\_freq=0 here -- every request has its own request time.  

Default: a list of 6: `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M`, `upload_max_filesize=32M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
# an empty "IniOptions[]" first replaces the shipped list; without it the items are added
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

> Extra options for \`frankenphp run\`, appended verbatim.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[FrankenPHPSettings]
# an empty "ExtraOptions[]" first replaces the shipped list; without it the items are added
ExtraOptions[]
*/ ?>
```

## Related

- [All blocks of velocity.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/velocity.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
