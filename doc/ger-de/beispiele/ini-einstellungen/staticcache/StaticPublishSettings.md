---
title: staticcache.ini [StaticPublishSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [StaticPublishSettings] in staticcache.ini: Enabled, TargetList, WorkDir, MaxSiteBytes, MaxFileBytes, Push, CommitName, CommitEmail ..."
keywords: Einstellung, staticcache.ini, StaticPublishSettings, INI, setting, Enabled, TargetList, WorkDir, MaxSiteBytes, MaxFileBytes, Push, CommitName, CommitEmail, JobHistory, GitHubAPI, GitHubAPIURL, GitHubAppId, GitHubAppKey, GitHubOAuthClientId, FormEndpoint, FormTokenDays, FormRateLimit, FormRateWindow, FormFallbackText, FormFallbackLink, SearchMaxBytes, PublishDebounce, NotifyEmail, WebhookSecret
type: generated
generated: exp:help:examples
---
# staticcache.ini [StaticPublishSettings]

Der Block `[StaticPublishSettings]` von `settings/staticcache.ini`, mit 24 Einstellung(en).

Bearbeiten Sie `settings/staticcache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/staticcache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/staticcache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/staticcache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Statisches Veröffentlichen: ein Front-Siteaccess, als Ordner statischer Dateien gebaut,  
> den jeder statische Host ausliefert (GitHub Pages, jeder Webserver), mit umgeschriebenen  
> Links und kopierten Dateien. Jedes Ziel ist ein Block \[Target\_<id>\] weiter unten;  
> ./console exp:static:targets listet sie, und exp:static:build baut eines.  
>
> Statisches Veröffentlichen als Ganzes: enabled oder disabled. Solange es disabled ist,  
> wird nichts gebaut.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
Enabled=disabled
*/ ?>
```

## TargetList

> Die Ziele, nach Kennung: die Kennungen der Blöcke \[Target\_<id>\], die gebaut werden.  
> Eine Kennung besteht aus 1 bis 50 Kleinbuchstaben, Ziffern, \_ und -.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
# ein leeres "TargetList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TargetList[]
*/ ?>
```

## WorkDir

> Wo die Builds liegen, relativ zum var-Verzeichnis der Website  
> (var/<site>/static-publish), sofern nicht absolut. Jedes Ziel hat darunter seinen eigenen  
> Ordner, nie im eigenen Repository der Website.  

Standard: `static-publish`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
WorkDir=static-publish
*/ ?>
```

## MaxSiteBytes

> Die meisten Bytes, die ein Build haben darf, bevor der Build warnt: die Größe, die der  
> Host einer Website erlaubt (GitHub Pages: 1 GB). Der Build warnt oberhalb von 90 %.  

Standard: `1000000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
MaxSiteBytes=1000000000
*/ ?>
```

## MaxFileBytes

> Die meisten Bytes, die eine Datei haben darf, bevor der Build warnt: die Größe, die der  
> Host einer Datei erlaubt (GitHub: 100 MB).  

Standard: `100000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
MaxFileBytes=100000000
*/ ?>
```

## Push

> Ob exp:static:publish und exp:static:rollback zum Remote des Ziels pushen:  
> enabled, oder disabled, um nur im Ordner des Ziels zu committen und zu taggen  
> (exp:static:push pusht später). Ein Push wird nie erzwungen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
Push=enabled
*/ ?>
```

## CommitName

> Der Committer einer Veröffentlichung, wenn das Repository des Ziels keinen nennt.  

Standard: `Exponential static publishing`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
CommitName=Exponential static publishing
*/ ?>
```

## CommitEmail

> Seine E-Mail-Adresse; leer: static-publish@<Hostname>.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
CommitEmail=
*/ ?>
```

## JobHistory

> How many jobs (builds, publishes, rollbacks started in the administration) keep their files  
> in <WorkDir>/.jobs; older ones are removed. A job that is running is always kept.  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
JobHistory=50
*/ ?>
```

## GitHubAPI

> Die GitHub-Anbindung: die API von GitHub für Ziele mit Kind=github-pages  
> (das Repository anlegen, GitHub Pages aus dem Branch oder mit GitHub Actions  
> einschalten, die eigene Domain und HTTPS, der Build-Zustand von Pages). Sie  
> meldet sich mit dem Credential des Ziels an (token: oder app:). enabled oder  
> disabled; das Veröffentlichen in den Branch geht auch ohne sie.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAPI=enabled
*/ ?>
```

## GitHubAPIURL

> Die Adresse der REST-API von GitHub (GitHub Enterprise Server:  
> https://<host>/api/v3).  

Standard: `https://api.github.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAPIURL=https://api.github.com
*/ ?>
```

## GitHubAppId

> Eine GitHub App für Credential=app:<installation id>: die ID der App und der  
> Name des Geheimnisses (Setup > Secrets) mit dem privaten Schlüssel (PEM), den  
> GitHub für sie erzeugt hat. Die App braucht die Repository-Berechtigungen  
> Contents, Pages und Administration (zum Anlegen von Repositorys), Lesen und Schreiben.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAppId=
*/ ?>
```

## GitHubAppKey

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAppKey=
*/ ?>
```

## GitHubOAuthClientId

> Die Client-ID einer OAuth App mit eingeschaltetem Device Flow, für  
> ./console exp:static:github signin: ein Benutzer meldet sich auf github.com an,  
> und das Token wird für Credential=token:<secret> im Geheimnisspeicher abgelegt.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubOAuthClientId=
*/ ?>
```

## FormEndpoint

> Der Formular-Endpunkt (staticpublish/form): Die Informationssammler der  
> Ziele mit Forms=endpoint senden an diese Installation, die sie an die  
> normale Informationssammlung übergibt. Angenommen werden nur Posts von der  
> PublicURL oder CustomDomain eines Ziels mit einem von einem Build signierten  
> Token. enabled oder disabled (die Kopien zeigen dann den Ersatztext).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormEndpoint=enabled
*/ ?>
```

## FormTokenDays

> Wie lange das Token, das ein Build in ein Formular schreibt, gültig bleibt,  
> in Tagen. Eine neue Veröffentlichung schreibt neue Tokens.  

Standard: `400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormTokenDays=400
*/ ?>
```

## FormRateLimit

> Wie viele Posts eine Adresse in FormRateWindow Sekunden an ein Ziel senden  
> darf, bevor sie warten soll (429).  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormRateLimit=5
*/ ?>
```

## FormRateWindow

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormRateWindow=600
*/ ?>
```

## FormFallbackText

> Der Text, der ein Formular ersetzt, das eine statische Kopie nicht senden  
> kann (Kommentare, der Warenkorb, ein Sammler mit Forms=hide), und der Text  
> seines Links zur selben Seite auf der Live-Site.  

Standard: `This form works on the live site only.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormFallbackText=This form works on the live site only.
*/ ?>
```

## FormFallbackLink

Standard: `Open this page there`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormFallbackLink=Open this page there
*/ ?>
```

## SearchMaxBytes

> Die meisten Bytes des Suchindex einer Sprache (Search=enabled): darüber  
> behält der Index weniger Wörter je Seite, dann keine Anrisstexte, dann  
> weniger Seiten.  

Standard: `2000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
SearchMaxBytes=2000000
*/ ?>
```

## PublishDebounce

> Veröffentlichen beim Veröffentlichen von Inhalten (Triggers\[\]=publish): Der  
> Cronjob-Teil staticpublish baut die geänderten Seiten eines Ziels so viele  
> Sekunden nach der letzten Änderung, sodass eine Reihe von Änderungen eine  
> Veröffentlichung ergibt.  

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
PublishDebounce=120
*/ ?>
```

## NotifyEmail

> Wohin die Benachrichtigungen aus Notify\[\] der Ziele gehen; leer: site.ini  
> \[MailSettings\] AdminEmail.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
NotifyEmail=
*/ ?>
```

## WebhookSecret

> Der Name eines Secrets (Setup > Secrets), dessen Wert jeden Webhook signiert  
> (Header X-Exp-Static-Signature: sha256=<HMAC des Inhalts>). Leer: nicht  
> signiert. Hier steht nur der Name, nie der Wert.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
WebhookSecret=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von staticcache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/staticcache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
