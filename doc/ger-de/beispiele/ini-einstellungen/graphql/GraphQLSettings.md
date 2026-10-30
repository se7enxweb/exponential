---
title: graphql.ini [GraphQLSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GraphQLSettings] in graphql.ini: Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList ..."
keywords: Einstellung, graphql.ini, GraphQLSettings, INI, setting, Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList, Introspection, MaxDepth, MaxComplexity, MaxListLimit, DefaultListLimit, PersistedQueries, AutomaticPersistedQueries, MaxPersistedQueries, PersistedQueriesOnly, PersistedQueryDirectoryList, MaxQueryLength, ResponseCache, ResponseCacheTtl, CredentialCacheTtl, RateLimit, RateLimitAnonymous, RateLimitWindow, ImageAliasList, Debug
type: generated
generated: exp:help:examples
---
# graphql.ini [GraphQLSettings]

Der Block `[GraphQLSettings]` von `settings/graphql.ini`, mit 27 Einstellung(en).

Bearbeiten Sie `settings/graphql.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/graphql.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/graphql.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/graphql.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> enabled oder disabled (der Standard). Aus, bis sie eingeschaltet wird, für jeden Siteaccess oder in den  
> Einstellungen eines einzelnen (settings/siteaccess/<siteaccess>/graphql.ini.append.php). Ausgeschaltet: /api/graphql  
> antwortet mit 404, der Sitzungs-Endpunkt graphql/query antwortet mit 404, sonst ändert sich nichts.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Enabled=disabled
*/ ?>
```

## SiteAccessList

> Die Siteaccesses, die bei Enabled=enabled antworten; leer: jeder Siteaccess. Eine Anfrage beantwortet der  
> Siteaccess, zu dem sie passt (Host, Pfad oder der Header X-Siteaccess, wie bei jeder REST-Anfrage).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# ein leeres "SiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteAccessList[]
*/ ?>
```

## LibraryPath

> Woher webonyx/graphql-php 15 geladen wird, wenn der Composer-Autoload der Installation kein GraphQL\\GraphQL hat:  
> ein Verzeichnis mit vendor/autoload.php (ein Composer-Verzeichnis, das webonyx/graphql-php verlangt) oder das Paket  
> selbst (das Verzeichnis mit src/GraphQL.php). Relativ zum Wurzelverzeichnis der Installation oder absolut. Die  
> Umgebungsvariable GRAPHQL\_LIBRARY\_PATH wird genauso gelesen, wenn diese Einstellung leer ist.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
LibraryPath=
*/ ?>
```

## NamingStyle

> Wie Inhaltsklassen und ihre Attribute im Schema heißen:  
>   identifier  (der Standard) wie in der Administration: Die Klasse ng\_article ist der Typ ng\_article, ihr  
>               Attribut short\_title das Feld short\_title  
>   pascal      Typen in PascalCase (NgArticle), Felder in camelCase (shortTitle)  
>   map         wie identifier, aber die Namen aus \[GraphQLNaming\] TypeNames\[\] und FieldNames\[\] haben Vorrang  
> In einem Namen, der kein gültiger GraphQL-Name ist (eine Ziffer vorn, ein Bindestrich), wird jedes andere Zeichen  
> durch \_ ersetzt, und vor eine führende Ziffer kommt ein \_. Ein Attribut, das wie ein Feld heißt, das jeder Inhalt  
> hat (id, name, children ...), erhält die Endung \_field (name\_field).  

Standard: `identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
NamingStyle=identifier
*/ ?>
```

## ClassList

> Die Klassen, die das Schema zeigt. Leer: jede Klasse außer denen aus ExcludedClassList\[\]. Die Richtlinien gelten  
> immer: Eine Klasse im Schema liest trotzdem nur, wer ihre Objekte lesen darf.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# ein leeres "ClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassList[]
*/ ?>
```

## ExcludedClassList

> Nie im Schema (außer ClassList\[\] nennt sie): die Benutzerkonten, die Benutzergruppen und die Klassen der  
> Einstellungen.  

Standard: eine Liste mit 4: `user`, `user_group`, `common_ini_settings`, `template_look`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# ein leeres "ExcludedClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludedClassList[]
ExcludedClassList[]=user
ExcludedClassList[]=user_group
ExcludedClassList[]=common_ini_settings
ExcludedClassList[]=template_look
*/ ?>
```

## HiddenFieldList

> Attribute, die nie im Schema stehen, <klasse>/<attribut> oder \*/<attribut> für jede Klasse.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# ein leeres "HiddenFieldList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HiddenFieldList[]
*/ ?>
```

## HiddenDatatypeList

> Datentypen, die nie im Schema stehen: das Benutzerkonto (Login, E-Mail, Passwort-Hash) und das Passwortfeld.  

Standard: eine Liste mit 2: `ezuser`, `ezpassword`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# ein leeres "HiddenDatatypeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HiddenDatatypeList[]
HiddenDatatypeList[]=ezuser
HiddenDatatypeList[]=ezpassword
*/ ?>
```

## Introspection

> Introspection (das Schema, von einem Client gelesen, wie es der Playground tut):  
>   signed-in  (der Standard) für eine Anfrage, die mit einem Token, einem persönlichen API-Schlüssel oder einer  
>              Sitzung angemeldet ist; für anonyme Anfragen abgelehnt  
>   enabled    für jede Anfrage  
>   disabled   für keine (die Konsolenbefehle geben das Schema trotzdem aus)  

Standard: `signed-in`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Introspection=signed-in
*/ ?>
```

## MaxDepth

> Die tiefste Verschachtelung von Feldern, die eine Abfrage haben darf, und die höchste Komplexität (jedes Feld  
> zählt 1, ein Listenfeld zählt sein Argument limit oder first mal die Felder darunter). Eine Abfrage über der  
> Grenze wird abgelehnt, bevor sie läuft (extensions.code DEPTH\_LIMIT oder COMPLEXITY\_LIMIT).  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxDepth=12
*/ ?>
```

## MaxComplexity

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxComplexity=1000
*/ ?>
```

## MaxListLimit

> Die meisten Einträge, die ein Listenfeld liefert (limit, first); ein größerer Wert wird darauf gesenkt.  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxListLimit=100
*/ ?>
```

## DefaultListLimit

> Die Zahl der Einträge, die ein Listenfeld liefert, wenn die Abfrage keine nennt.  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
DefaultListLimit=25
*/ ?>
```

## PersistedQueries

> Gespeicherte Abfragen: Abfragen, die auf dem Server liegen (var/<site>/graphql/persisted/) und mit einem Namen  
> (GET /api/graphql?id=articles) oder mit ihrem SHA-256 (extensions.persistedQuery.sha256Hash von Apollo) aufgerufen  
> werden. Gespeichert mit ./console exp:graphql:persist <Datei oder Verzeichnis> --apply. enabled (der Standard) oder  
> disabled.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
PersistedQueries=enabled
*/ ?>
```

## AutomaticPersistedQueries

> Wer eine Abfrage speichern darf, indem er ihren Text mit ihrem SHA-256 sendet (die automatischen Persisted Queries von Apollo):  
>   signed-in  (der Standard) eine Anfrage, die mit einem Token, einem persönlichen API-Schlüssel oder einer Sitzung angemeldet ist  
>   enabled    jede Anfrage, auch die anonyme (es werden höchstens MaxPersistedQueries gespeichert)  
>   disabled   niemand; Abfragen werden nur mit exp:graphql:persist gespeichert  
> Eine Abfrage, die nicht gespeichert werden darf, läuft trotzdem, als Ad-hoc-Abfrage.  

Standard: `signed-in`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
AutomaticPersistedQueries=signed-in
*/ ?>
```

## MaxPersistedQueries

> Die meisten Abfragen, die der Speicher hält, bevor eine automatische gespeicherte Abfrage nicht mehr gespeichert wird  
> (exp:graphql:persist speichert trotzdem).  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxPersistedQueries=1000
*/ ?>
```

## PersistedQueriesOnly

> Unter /api/graphql werden nur gespeicherte Abfragen ausgeführt (eine Ad-hoc-Abfrage wird mit PERSISTED\_QUERY\_REQUIRED abgelehnt):  
>   disabled   (der Standard) jede Abfrage  
>   anonymous  anonyme Anfragen dürfen nur gespeicherte Abfragen ausführen, angemeldete jede Abfrage  
>   enabled    jede Anfrage an /api/graphql führt nur gespeicherte Abfragen aus  
> Der Sitzungs-Endpunkt (der Playground unter Setup > GraphQL) und die Konsole führen immer jede Abfrage aus.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
PersistedQueriesOnly=disabled
*/ ?>
```

## PersistedQueryDirectoryList

> Verzeichnisse gespeicherter Abfragen, die mit dem Code ausgeliefert werden: Jede Datei <name>.graphql ist die  
> gespeicherte Abfrage <name>, nur lesbar (exp:graphql:persist --list zeigt sie). Relativ zum Wurzelverzeichnis der  
> Installation oder absolut; eine Erweiterung fügt ihr eigenes Verzeichnis in ihrer settings/graphql.ini.append.php hinzu.  
> PersistedQueryDirectoryList\[\]=extension/myfrontend/graphql  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# ein leeres "PersistedQueryDirectoryList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PersistedQueryDirectoryList[]
*/ ?>
```

## MaxQueryLength

> Der längste angenommene Abfragetext, in Bytes.  

Standard: `100000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxQueryLength=100000
*/ ?>
```

## ResponseCache

> Der Antwort-Cache: die Antworten gespeicherter Abfragen, gehalten je Siteaccess, Schema, Benutzerrechten (Rollen und  
> ihre Einschränkungen, wie beim View-Cache), Variablen und Adresse, in var/cache/graphql/responses/. Ein anonymes GET  
> einer gespeicherten Abfrage wird daraus am Anfang von index\_rest.php beantwortet, vor den Einstellungen und der  
> Datenbank. Jede Antwort läuft ab, wenn Inhalt veröffentlicht wird (wann immer View-Caches geleert werden), wenn sich  
> eine Klasse ändert, wenn gespeicherte Abfragen gespeichert oder entfernt werden, und mit ezcache --clear-id=graphql  
> (auch --clear-tag=ini und content). Ad-hoc-Abfragen werden nie behalten. enabled (der Standard) oder disabled.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
ResponseCache=enabled
*/ ?>
```

## ResponseCacheTtl

> Die Sekunden, die eine Antwort höchstens gehalten wird, auch ohne Veröffentlichung; 0: bis sie abläuft.  

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
ResponseCacheTtl=86400
*/ ?>
```

## CredentialCacheTtl

> Ein angemeldetes GET einer gespeicherten Abfrage wird ebenfalls am Anfang von index\_rest.php beantwortet, sobald der  
> Kernel sein Token oder seinen Schlüssel geprüft hat: Die Rechte der Anmeldedaten (die Rollen ihres Benutzers) werden so  
> viele Sekunden gemerkt (var/cache/graphql/credentials/) und sofort vergessen, wenn sich ein API-Schlüssel, eine Rolle  
> oder die Rechte eines Benutzers ändern. Ein anders widerrufenes Token liest bis dahin noch die Antworten, die für seine  
> Rechte gecacht sind. 0: Angemeldete Anfragen gehen immer durch den Kernel.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
CredentialCacheTtl=60
*/ ?>
```

## RateLimit

> Ratenbegrenzung: die Anfragen, die ein angemeldeter Benutzer oder ein persönlicher API-Schlüssel (RateLimit) oder  
> eine IP-Adresse anonymer Anfragen (RateLimitAnonymous) in RateLimitWindow Sekunden senden darf; 0: keine Grenze. Eine  
> Anfrage über ihrer Grenze wird mit 429, RATE\_LIMITED und Retry-After beantwortet; jede Antwort trägt  
> X-RateLimit-Limit, X-RateLimit-Remaining und X-RateLimit-Reset. Gezählt in var/cache/graphql/rate/, von jedem Worker  
> des Webservers gleich. Die Konsole ist nicht begrenzt.  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
RateLimit=600
*/ ?>
```

## RateLimitAnonymous

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
RateLimitAnonymous=120
*/ ?>
```

## RateLimitWindow

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
RateLimitWindow=60
*/ ?>
```

## ImageAliasList

> Bildaliase (settings/image.ini), die das Feld variations liefert, wenn die Abfrage keine nennt; url(alias:)  
> nimmt jeden Alias aus image.ini.  

Standard: eine Liste mit 3: `small`, `medium`, `large`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# ein leeres "ImageAliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImageAliasList[]
ImageAliasList[]=small
ImageAliasList[]=medium
ImageAliasList[]=large
*/ ?>
```

## Debug

> enabled oder disabled (der Standard). Eingeschaltet: Ein Fehler trägt die PHP-Exception und ihren Trace in  
> extensions.debug. Nur für einen Entwicklungs-Siteaccess: Ein Trace verrät jedem, der fragt, Pfade und Code.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Debug=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von graphql.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/graphql.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
