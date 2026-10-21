---
title: rest.ini [CorsSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CorsSettings] in rest.ini: Cors, AllowedOrigins, AllowedMethods, AllowedHeaders, ExposedHeaders, MaxAge, AllowCredentials"
keywords: Einstellung, rest.ini, CorsSettings, INI, setting, Cors, AllowedOrigins, AllowedMethods, AllowedHeaders, ExposedHeaders, MaxAge, AllowCredentials
type: generated
generated: exp:help:examples
---
# rest.ini [CorsSettings]

Der Block `[CorsSettings]` von `settings/rest.ini`, mit 7 Einstellung(en).

> Cross-Origin Resource Sharing (CORS) für alles, was index\_rest.php ausliefert (/api/...: die Content-API, den  
> OAuth-Token-Endpunkt und die Routen jedes REST-Providers). doc/eng-us/bc/6.0/rest-cors.md  
> Standardmäßig aus: Es wird kein CORS-Header gesendet, und eine OPTIONS-Anfrage wird wie bisher geroutet.  

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Cors

> enabled oder disabled (der Standard)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CorsSettings]
Cors=disabled
*/ ?>
```

## AllowedOrigins

> Die Origins, die die API aus einem Browser aufrufen dürfen, genau so, wie der Browser sie sendet: Schema, Host und Port  
> (wenn nicht der Standardport), kein Pfad und kein abschließender Schrägstrich. Eine Antwort an jeden anderen Origin hat keinen  
> Access-Control-\*-Header. "null" wird nie akzeptiert. "\*" (jeder Origin) wird nur akzeptiert, solange AllowCredentials deaktiviert ist.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CorsSettings]
# ein leeres "AllowedOrigins[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedOrigins[]
*/ ?>
```

## AllowedMethods

> AllowedOrigins\[\]=https://app.example.com  
> AllowedOrigins\[\]=http://localhost:3000  
> Die Methoden, nach denen ein Preflight fragen darf  

Standard: eine Liste mit 5: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CorsSettings]
# ein leeres "AllowedMethods[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedMethods[]
AllowedMethods[]=GET
AllowedMethods[]=POST
AllowedMethods[]=PUT
AllowedMethods[]=PATCH
AllowedMethods[]=DELETE
*/ ?>
```

## AllowedHeaders

> Die Anfrage-Header, nach denen ein Preflight fragen darf (ohne Beachtung der Groß- und Kleinschreibung verglichen); ein Preflight, der nach einem anderen fragt, wird abgelehnt  

Standard: eine Liste mit 5: `Authorization`, `Content-Type`, `Accept`, `Accept-Language`, `X-Siteaccess`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CorsSettings]
# ein leeres "AllowedHeaders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedHeaders[]
AllowedHeaders[]=Authorization
AllowedHeaders[]=Content-Type
AllowedHeaders[]=Accept
AllowedHeaders[]=Accept-Language
AllowedHeaders[]=X-Siteaccess
*/ ?>
```

## ExposedHeaders

> Antwort-Header, die ein Skript eines erlaubten Origins neben den CORS-sicheren Headern lesen darf  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CorsSettings]
# ein leeres "ExposedHeaders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExposedHeaders[]
*/ ?>
```

## MaxAge

> Wie lange ein Browser die Antwort auf einen Preflight behalten darf, in Sekunden; 0 sendet kein Access-Control-Max-Age  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CorsSettings]
MaxAge=600
*/ ?>
```

## AllowCredentials

> enabled oder disabled (der Standard). enabled: Access-Control-Allow-Credentials: true, damit ein Browser bei der  
> Cross-Origin-Anfrage Cookies sendet und behält (die Sitzung eines angemeldeten Benutzers). Lassen Sie es für Clients, die ein  
> OAuth-Token oder einen API-Schlüssel im Authorization-Header senden, deaktiviert; das ist der übliche Fall.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CorsSettings]
AllowCredentials=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
