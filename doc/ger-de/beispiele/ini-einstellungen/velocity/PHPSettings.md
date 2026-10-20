---
title: velocity.ini [PHPSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PHPSettings] in velocity.ini: IniOptions"
keywords: Einstellung, velocity.ini, PHPSettings, INI, setting, IniOptions
type: generated
generated: exp:help:examples
---
# velocity.ini [PHPSettings]

Der Block `[PHPSettings]` von `settings/velocity.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## IniOptions

> Interpreter settings for the server process, one per line, exactly as they  
> would be written after -d.  
>
> The opcode cache is here because /etc/php.d/99-no-opcache-cli.ini disables it  
> for every command-line PHP on this machine. That file is a system-wide  
> decision affecting every site on the box and is deliberately left alone; this  
> asks for the cache for this process only.  
>
> Measured on a content page, four workers, requests round-robined between  
> servers so that drift during the run hits all of them equally:  
>
>   none (today)              526.6 ms    1.9 req/s     baseline  
>   opcache                   390.8 ms    2.6 req/s    +25.8%  
>   + no timestamp checks     390.0 ms    2.6 req/s    +25.9%  
>   + interned strings 32M    387.4 ms    2.6 req/s    +26.4%  
>   + tracing JIT             416.7 ms    2.4 req/s    +20.9%  
>
> So: the cache alone is the win. Timestamp checks cost nothing measurable and  
> are worth keeping, because turning them off means an edited file is ignored  
> until the service restarts. Tracing JIT is slower than no JIT on this  
> workload and is deliberately absent.  
>
> The timestamp checks only happen with revalidate\_freq=0. The cache compares  
> timestamps once revalidate\_freq seconds of request time have passed, and a  
> command-line process takes that time once, when it starts; every worker  
> inherits it from the parent. With the default of 2 no worker ever looked  
> again, so a PHP file the kernel rewrites under the same name -- expiry.php,  
> the content language cache, compiled templates -- was served as it was when  
> a worker first compiled it, until the service restarted. A cache clear from  
> the command line then did nothing. With 0 the file is checked on every  
> include, which costs nothing measurable here: the server takes a stat of each  
> include anyway.  
> Memory: 512 MB for scripts and 64 MB for interned strings. The help center's index files are 18-24 MB each  
> per language, and with 256/32 the interned strings buffer was full after a few help requests (measured on  
> alpha, 2026-10-20: 32.0 of 32.0 MB, 141.6 of 256 MB used three minutes after a restart). The memory is  
> shared by every worker of an instance and reserved, not used, until it fills.  
> file\_update\_protection=0 for the same reason: the cache does not keep a script  
> changed less than file\_update\_protection seconds before the REQUEST's start,  
> and a worker's request started when the worker did. Every file written after  
> that -- the INI caches, the override caches, the compiled templates, all  
> regenerated after a deploy or a cache clear -- counted as "too new" for the  
> worker's whole life and was compiled again on every include: the cache served  
> 2.7% of includes (PHP-FPM, a fresh request each time: 97%) and a rendered page  
> took about four times the CPU of PHP-FPM. The protection is there so that a  
> file still being written is not kept; the server already sees to that itself,  
> against the time of each include: it reads an include whole (readWhole) and  
> drops the cache's copy of a file changed in the last two seconds (RACY\_SECONDS).  
> APCu for the server process. The command-line default is 32 MB, where the  
> PHP-FPM pool beside it on this machine has 1,232 MB. Velocity keeps its  
> response cache's memory tier in APCu, and the SQL query cache  
> (querycache.ini Mode=shared) its results: in 32 MB the two evicted each  
> other. Measured on the demonstration server (2026-09-27): with 32 MB the query cache held ~300  
> results at a 51 % hit rate and a rendered front page cost 727 ms of CPU  
> (13 pages a second); with 256 MB, 303 ms (29-31 pages a second). The  
> memory is reserved, not used, until it fills.  

Standard: eine Liste mit 7: `opcache.enable_cli=1`, `opcache.memory_consumption=512`, `opcache.max_accelerated_files=20000`, `opcache.interned_strings_buffer=64`, `opcache.revalidate_freq=0`, `opcache.file_update_protection=0`, `apc.shm_size=256M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPSettings]
# ein leeres "IniOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IniOptions[]
IniOptions[]=opcache.enable_cli=1
IniOptions[]=opcache.memory_consumption=512
IniOptions[]=opcache.max_accelerated_files=20000
IniOptions[]=opcache.interned_strings_buffer=64
IniOptions[]=opcache.revalidate_freq=0
IniOptions[]=opcache.file_update_protection=0
IniOptions[]=apc.shm_size=256M
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
