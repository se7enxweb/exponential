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

> Interpreter-Einstellungen für den Serverprozess, eine pro Zeile, genau so, wie sie  
> nach -d geschrieben würden.  
>
> Der Opcode-Cache steht hier, weil /etc/php.d/99-no-opcache-cli.ini ihn  
> für jedes Kommandozeilen-PHP auf diesem Rechner abschaltet. Diese Datei ist eine systemweite  
> Entscheidung, die jede Website auf dem Rechner betrifft, und bleibt bewusst unangetastet; dies  
> verlangt den Cache nur für diesen Prozess.  
>
> Gemessen auf einer Inhaltsseite, vier Worker, Anfragen reihum zwischen den  
> Servern verteilt, damit Schwankungen während des Laufs alle gleich treffen:  
>
>   keiner (heute)            526.6 ms    1.9 req/s     Basis  
>   opcache                   390.8 ms    2.6 req/s    +25.8%  
>   + keine Zeitstempelprüfung 390.0 ms   2.6 req/s    +25.9%  
>   + interned strings 32M    387.4 ms    2.6 req/s    +26.4%  
>   + tracing JIT             416.7 ms    2.4 req/s    +20.9%  
>
> Also: Der Cache allein ist der Gewinn. Zeitstempelprüfungen kosten nichts Messbares und  
> sind es wert, behalten zu werden, weil ihr Abschalten bedeutet, dass eine bearbeitete Datei ignoriert wird,  
> bis der Dienst neu startet. Tracing JIT ist bei dieser Last langsamer als kein JIT  
> und fehlt bewusst.  
>
> Die Zeitstempelprüfungen geschehen nur mit revalidate\_freq=0. Der Cache vergleicht  
> Zeitstempel, sobald revalidate\_freq Sekunden Anfragezeit vergangen sind, und ein  
> Kommandozeilenprozess nimmt diese Zeit einmal, beim Start; jeder Worker  
> erbt sie vom Elternprozess. Mit dem Standard von 2 sah kein Worker je  
> wieder nach, sodass eine PHP-Datei, die der Kernel unter demselben Namen neu schreibt (expiry.php,  
> der Cache der Inhaltssprachen, kompilierte Templates), so ausgeliefert wurde, wie sie war, als  
> ein Worker sie zuerst kompilierte, bis der Dienst neu startete. Ein Leeren des Caches von  
> der Kommandozeile bewirkte dann nichts. Mit 0 wird die Datei bei jedem  
> include geprüft, was hier nichts Messbares kostet: Der Server macht ohnehin ein stat jedes  
> include.  
> Speicher: 512 MB für Skripte und 64 MB für interned strings. Die Indexdateien des Hilfecenters sind je 18-24 MB  
> pro Sprache, und mit 256/32 war der Puffer der interned strings nach einigen Hilfeanfragen voll (gemessen auf  
> einer Entwicklungsinstallation, 2026-10-20: 32.0 von 32.0 MB, 141.6 von 256 MB belegt drei Minuten nach einem Neustart). Der Speicher wird  
> von jedem Worker einer Instanz geteilt und reserviert, nicht belegt, bis er sich füllt.  
> file\_update\_protection=0 aus demselben Grund: Der Cache behält kein Skript, das  
> weniger als file\_update\_protection Sekunden vor dem Start der ANFRAGE geändert wurde,  
> und die Anfrage eines Workers begann, als der Worker begann. Jede danach geschriebene Datei  
> (die INI-Caches, die Override-Caches, die kompilierten Templates, alle  
> nach einem Deployment oder einem Leeren des Caches neu erzeugt) galt für das ganze  
> Leben des Workers als „zu neu“ und wurde bei jedem include neu kompiliert: Der Cache lieferte  
> 2,7 % der includes (PHP-FPM, jedes Mal eine frische Anfrage: 97 %), und eine gerenderte Seite  
> brauchte etwa das Vierfache der CPU von PHP-FPM. Der Schutz ist dazu da, dass eine  
> Datei, die noch geschrieben wird, nicht behalten wird; dafür sorgt der Server schon selbst,  
> gegen die Zeit jedes include: Er liest ein include ganz (readWhole) und  
> verwirft die Kopie des Caches von einer Datei, die in den letzten zwei Sekunden geändert wurde (RACY\_SECONDS).  
> APCu für den Serverprozess. Der Kommandozeilen-Standard ist 32 MB, wo der  
> PHP-FPM-Pool daneben auf diesem Rechner 1.232 MB hat. Velocity hält die Speicherstufe  
> seines Antwort-Caches in APCu und der SQL-Abfrage-Cache  
> (querycache.ini Mode=shared) seine Ergebnisse: In 32 MB verdrängten sich die beiden  
> gegenseitig. Gemessen auf dem Demonstrationsserver (2026-09-27): Mit 32 MB hielt der Abfrage-Cache ~300  
> Ergebnisse bei 51 % Trefferquote, und eine gerenderte Startseite kostete 727 ms CPU  
> (13 Seiten pro Sekunde); mit 256 MB 303 ms (29-31 Seiten pro Sekunde). Der  
> Speicher wird reserviert, nicht belegt, bis er sich füllt.  

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
