---
title: site.ini [ContentSecurityPolicy]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentSecurityPolicy] in site.ini: Mode, Directives, AdditionalSources, ReportOnlyDirectives, ReportOnlyDesigns, ReportOnlyViews, Nonce, NonceDirectives ..."
keywords: Einstellung, site.ini, ContentSecurityPolicy, INI, setting, Mode, Directives, AdditionalSources, ReportOnlyDirectives, ReportOnlyDesigns, ReportOnlyViews, Nonce, NonceDirectives, ReportURI, ReportEndpoint, ReportsPerMinute, ReportsPerMinuteTotal, MaxReportBytes, MaxViolationsPerReport, LogFile
type: generated
generated: exp:help:examples
---
# site.ini [ContentSecurityPolicy]

Der Block `[ContentSecurityPolicy]` von `settings/site.ini`, mit 15 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Mode

> Die Content Security Policy der Seiten (doc/eng-us/features/6.0/content-security-policy.md):  
> welche Skripte, Stylesheets, Bilder und Verbindungen eine Seite verwenden darf. Sie wird  
> neben den Sicherheits-Headern aus \[HTTPHeaderSettings\] SecurityHeaders\[\] gesendet.  
>
> disabled: keine Richtlinie  
> report-only: Content-Security-Policy-Report-Only; der Browser meldet, was die  
>   Richtlinie blockieren würde (Entwicklerkonsole, ReportURI), und blockiert nichts  
> enforce: Content-Security-Policy; der Browser blockiert es. Als zweite  
>   Richtlinie nach SecurityHeaders\[Content-Security-Policy\] gesendet, beide gelten  
> auto (der Standard): report-only in Administrations-Siteaccesses (denen, deren  
>   \[DesignSettings\] SiteDesign mit "admin" beginnt), in den anderen disabled  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
Mode=auto
*/ ?>
```

## Directives

> Die Richtlinie, Directives\[<directive>\]=<sources>. Setzen Sie einen Wert in einem Override leer,  
> um diese Direktive wegzulassen; ein Override, das mit "Directives\[\]" allein beginnt,  
> ersetzt die ganze Liste. Werte mit ";" oder "," werden ignoriert. frame-ancestors  
> und sandbox werden nur mit Mode=enforce gesendet (Browser ignorieren sie in einer  
> reinen Berichtsrichtlinie). object-src 'none' blockiert <object>/<embed> (die alten  
> Vorschauen für Flash, Windows Media, RealVideo und QuickTime, ein eingebettet angezeigtes PDF).  
> script-src 'self' erlaubt nur Skriptdateien dieser Website: kein Inline-<script>,  
> keine on...=-Attribute, keine javascript:-Links, kein eval(). style-src 'self' erlaubt  
> nur Stylesheet-Dateien: keine <style>-Elemente und keine style=""-Attribute (element.style  
> aus einer Skriptdatei zu setzen bleibt erlaubt).  
> Worker (new Worker, ein Service Worker) nur dieser Website: kein blob:- oder data:-Worker,  
> der aus einer Zeichenkette gebauten Code ausführen würde. Hier genannt (sonst fiele es  
> auf script-src zurück), damit AdditionalSources\[\] einen Host oder blob: hinzufügen kann, wo eine Funktion es braucht.  

Standard: ein Hash mit 13: `default-src='self'`, `script-src='self'`, `style-src='self'`, `img-src='self' data: blob:`, `font-src='self' data:`, `media-src='self' blob:`, `connect-src='self'`, `worker-src='self'`, `frame-src='self'`, `object-src='none'`, `base-uri='self'`, `form-action='self'` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# ein leeres "Directives[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Directives[]
Directives[default-src]='self'
Directives[script-src]='self'
Directives[style-src]='self'
Directives[img-src]='self' data: blob:
Directives[font-src]='self' data:
Directives[media-src]='self' blob:
Directives[connect-src]='self'
Directives[worker-src]='self'
Directives[frame-src]='self'
Directives[object-src]='none'
Directives[base-uri]='self'
Directives[form-action]='self'
Directives[frame-ancestors]='self'
*/ ?>
```

## AdditionalSources

> Quellen, die einer Direktive aus Directives\[\] hinzugefügt werden, ein Eintrag pro Direktive und  
> Quellen: AdditionalSources\[\]=<directive> <sources>. Eine Liste, damit eine Erweiterung und  
> der Siteaccess jeweils einen Host hinzufügen können, ohne die Direktive zu wiederholen. Ein Eintrag  
> für eine Direktive, die Directives\[\] nicht hat, wird ignoriert.  
> Beispiel: AdditionalSources\[\]=script-src https://maps.googleapis.com  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# ein leeres "AdditionalSources[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdditionalSources[]
*/ ?>
```

## ReportOnlyDirectives

> Mit Mode=enforce: eine zweite, strengere Richtlinie, gesendet als  
> Content-Security-Policy-Report-Only (Directives\[\] mit diesen Einträgen an ihrer  
> Stelle), um zu messen, was sie noch blockieren würde, während die durchgesetzte Richtlinie  
> es erlaubt. Leer: keine.  
> Beispiel: ReportOnlyDirectives\[style-src\]='self'  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# ein leeres "ReportOnlyDirectives[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReportOnlyDirectives[]
*/ ?>
```

## ReportOnlyDesigns

> Designs, deren Anfragen unter Mode=enforce bei report-only bleiben: die klassische  
> Administration mit Inline-Code, die ein Benutzer mit dem Oberflächenschalter wählen kann.  
> Das Design der Anfrage (nach dem Oberflächenschalter) entscheidet.  

Standard: eine Liste mit 3: `admin`, `admin2`, `admin_classic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# ein leeres "ReportOnlyDesigns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReportOnlyDesigns[]
ReportOnlyDesigns[]=admin
ReportOnlyDesigns[]=admin2
ReportOnlyDesigns[]=admin_classic
*/ ?>
```

## ReportOnlyViews

> Ansichten (module/view), die für ihre eigenen Inline-Skripte eine eigene Richtlinie senden,  
> wie ein mitgeliefertes Datenbankwerkzeug: report-only unter Mode=enforce, damit  
> die Richtlinie des Kernels sie nicht blockieren kann, wo ein Server beide Header behält.  
> Beispiel: ReportOnlyViews\[\]=dse/adminneo  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# ein leeres "ReportOnlyViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReportOnlyViews[]
*/ ?>
```

## Nonce

> enabled: Eine Nonce der Anfrage, 'nonce-...', wird den Direktiven aus  
> NonceDirectives\[\] hinzugefügt, für ein Inline-Skript, das ein Template nicht in eine Datei verlagern kann:  
> <script nonce="{csp\_nonce()}">. Nicht hinzugefügt, wo die Direktive  
> 'unsafe-inline' erlaubt (Browser würden 'unsafe-inline' daneben ignorieren). Geben Sie  
> csp\_nonce() nie in einem Cache-Block oder einem im View-Cache gecachten Template aus.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
Nonce=enabled
*/ ?>
```

## NonceDirectives

Standard: eine Liste mit 1: `script-src`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# ein leeres "NonceDirectives[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NonceDirectives[]
NonceDirectives[]=script-src
*/ ?>
```

## ReportURI

> Wohin der Browser seine Berichte sendet (report-uri), z. B. an einen eigenen  
> Sammler; leer: nur die Entwicklerkonsole oder, mit ReportEndpoint=enabled, die  
> Ansicht csp/report des Siteaccess. Eine absolute Adresse wird außerdem als  
> Reporting-Endpoints-Endpunkt csp-endpoint angekündigt (report-to).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportURI=
*/ ?>
```

## ReportEndpoint

> enabled: Mit leerem ReportURI gehen die Berichte an csp/report des  
> Siteaccess (das Kernel-Modul csp), das sie in var/log/csp.log protokolliert  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportEndpoint=disabled
*/ ?>
```

## ReportsPerMinute

> Die Berichte, die die Ansicht csp/report annimmt (eine Zeile pro Verstoß in var/log/csp.log,  
> Setup > Logdateien, Schlüssel csp). Pro Client (der gesalzene Adress-Hash des  
> Anfrage-Klassifizierers), pro Minute; darüber antwortet die Ansicht mit 429, und der  
> Anfrage-Klassifizierer schreibt eine Zeile abuse/csp-report-flood in security.log.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportsPerMinute=60
*/ ?>
```

## ReportsPerMinuteTotal

> Alle Clients zusammen, pro Minute.  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportsPerMinuteTotal=600
*/ ?>
```

## MaxReportBytes

> Der größte angenommene Berichtsinhalt, in Bytes (darüber 413).  

Standard: `65536`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
MaxReportBytes=65536
*/ ?>
```

## MaxViolationsPerReport

> Die meisten Verstöße, die aus einem Bericht geschrieben werden (die Reporting API sendet mehrere).  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
MaxViolationsPerReport=20
*/ ?>
```

## LogFile

> Die Logdatei, im Log-Verzeichnis (var/log).  

Standard: `csp.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
LogFile=csp.log
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
