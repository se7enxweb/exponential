---
title: graphql.ini [GraphQLSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GraphQLSettings] in graphql.ini: Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList ..."
keywords: Einstellung, graphql.ini, GraphQLSettings, INI, setting, Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList, Introspection, MaxDepth, MaxComplexity, MaxListLimit, DefaultListLimit, ImageAliasList, Debug
type: generated
generated: exp:help:examples
---
# graphql.ini [GraphQLSettings]

Der Block `[GraphQLSettings]` von `settings/graphql.ini`, mit 15 Einstellung(en).

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
