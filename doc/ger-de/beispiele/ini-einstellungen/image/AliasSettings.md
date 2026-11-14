---
title: image.ini [AliasSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AliasSettings] in image.ini: AliasList, AliasGeneration, OnDemandURL, Delivery, MaxWait, DraftTokenLifetime, SigningKeyGrace, PreferredConverter ..."
keywords: Einstellung, image.ini, AliasSettings, INI, setting, AliasList, AliasGeneration, OnDemandURL, Delivery, MaxWait, DraftTokenLifetime, SigningKeyGrace, PreferredConverter, Negotiate, Placeholder, SwapScript, PreGenerate, PreGenerateAliasList, PreGenerateFormats, MaxParallel, BulkNice, CacheMaxAge
type: generated
generated: exp:help:examples
---
# image.ini [AliasSettings]

Der Block `[AliasSettings]` von `settings/image.ini`, mit 17 Einstellung(en).

> -------- Einstellungen der Bild-Aliase --------  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AliasList

> Legt eine Liste von Aliasen fest, die der  
> Template-Engine und anderen Clients zur Verfügung stehen.  
> Der Alias muss als eigener INI-Block definiert sein.  

Standard: eine Liste mit 6: `reference`, `small`, `tiny`, `medium`, `large`, `rss`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
# ein leeres "AliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasList[]
AliasList[]=reference
AliasList[]=small
AliasList[]=tiny
AliasList[]=medium
AliasList[]=large
AliasList[]=rss
*/ ?>
```

## AliasGeneration

> AliasList\[\]=rssitem  
> AliasList\[\]=gray  
> AliasList\[\]=zoom  
> Wie Bild-Aliase erzeugt werden.  
>   on-demand  (der Standard) ein Template, das einen Alias zeigt, bekommt dessen URL, Dateinamen, MIME-Typ, Breite  
>              und Höhe berechnet, ohne dass etwas umgewandelt wird, während die Seite gerendert wird. Der Alias wird  
>              erzeugt, wenn der Browser ihn anfordert: durch die Ansicht content/image oder durch index\_image.php für einen  
>              Speicherpfad, den der Webserver nicht findet (siehe .htaccess\_root). Kommandozeilenskripte  
>              (Cronjobs, Mailer, Exporte) erzeugen Aliase weiterhin sofort, wie im Modus render.  
>   render     das ursprüngliche Verhalten: Der Template-Aufruf wandelt das Bild um, während die Seite gerendert wird.  
> Die Einstellung lässt sich jederzeit in beide Richtungen umschalten; es werden keine Daten migriert. In einem Modus erzeugte  
> Aliase werden vom anderen verwendet.  

Standard: `on-demand`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
AliasGeneration=on-demand
*/ ?>
```

## OnDemandURL

> Die URL, die ein Template für einen noch nicht erzeugten Alias bekommt (Modus on-demand):  
>   token      /content/image/<token>/<file name>; das signierte Token trägt alles, was die Ansicht braucht,  
>              sodass eine Anfrage keine Datenbank braucht, bis der Alias erzeugt ist  
>   ids-token  /content/image/<attribute>/<version>/<language>/<alias>/<token>/<file name>  
>   ids        /content/image/<attribute>/<version>/<language>/<alias>/<file name> (unsigniert: liefert einen  
>              vorhandenen Alias aus, erzeugt nie einen)  
>   node       /content/image/<node>/<attribute identifier>/<alias> (unsigniert, wie ids)  
>   storage    der einfache Speicherpfad, wie ihn der Modus render liefert; ein Fehltreffer erreicht index\_image.php, das  
>              den Alias erzeugt, wenn der Pfad einen konfigurierten Alias eines gespeicherten Bildes nennt  

Standard: `token`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
OnDemandURL=token
*/ ?>
```

## Delivery

> Wie content/image mit einem vorhandenen Alias antwortet:  
>   auto      eine Weiterleitung (302) auf die Speicher-URL, wenn der Webserver den Speicher ausliefert (ein einzelner Server  
>             oder ein gemeinsames Dateisystem), sonst die Datei selbst (ein Datenbank-Cluster, Entwürfe)  
>   redirect  immer die Weiterleitung  
>   stream    immer die Datei selbst  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
Delivery=auto
*/ ?>
```

## MaxWait

> Sekunden, die eine Anfrage mit dem Erzeugen (oder Holen) eines Alias verbringen darf, das Warten auf einen anderen Prozess und auf einen  
> Umwandlungs-Slot (MaxParallel) eingeschlossen. Dauert es länger, antwortet die Anfrage mit dem Platzhalter,  
> reiht den Alias in die Job-Warteschlange ein (wenn expqueue vorhanden ist), und der Browser fragt später erneut. Nach MaxWait beginnt keine neue  
> Umwandlung; eine begonnene wird zu Ende geführt (sie abzubrechen würde die Arbeit verlieren).  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
MaxWait=2
*/ ?>
```

## DraftTokenLifetime

> Sekunden, die eine signierte URL eines Entwurfs- oder Vorschaubildes (images-versioned) gültig bleibt; veröffentlichte Bilder  
> bekommen URLs, die nicht ablaufen.  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
DraftTokenLifetime=3600
*/ ?>
```

## SigningKeyGrace

> Sekunden, die der vorherige Signaturschlüssel (image\_alias\_signing\_previous in Setup > Geheimnisse) nach einem  
> Schlüsselwechsel noch akzeptiert wird, sodass Seiten, die mit den alten URLs zwischengespeichert sind, weiter funktionieren.  

Standard: `604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
SigningKeyGrace=604800
*/ ?>
```

## PreferredConverter

> Der Konverter, der die Formatvarianten schreibt (WebP, AVIF, siehe Formats\[\] unten):  
>   auto         die Erweiterung imagick, dann der ImageMagick-Befehl von \[ImageMagick\] (solange IsEnabled=true),  
>                dann GD: der erste von ihnen, der das Format schreiben kann (Imagick::queryFormats(),  
>                "convert -list format", gd\_info())  
>   imagick | imagemagick | gd   zuerst versucht, dann die anderen in der Reihenfolge von auto  
> Ein Format, das kein Konverter schreiben kann, wird übersprungen: Der Browser bekommt den Alias in seinem eigenen Format.  
> Die Aliase von AliasList\[\] selbst werden in beiden Modi von \[ImageConverterSettings\] ImageConverters\[\]  
> erzeugt, sodass ein auf Abruf erzeugter Alias Byte für Byte derselbe ist, den der Modus render erzeugt.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
PreferredConverter=auto
*/ ?>
```

## Negotiate

> content/image antwortet mit einer WebP- oder AVIF-Variante eines Alias, wenn der Accept-Header des Browsers  
> den Typ nennt (image/webp, image/avif; image/\* zählt nicht) und der Alias-Block das Format aufführt, mit  
> Vary: Accept bei jeder Antwort für diesen Alias. Im Modus on-demand wird ein solcher Alias auch dann über  
> content/image verlinkt, wenn er erzeugt ist (OnDemandURL token oder ids-token), sodass jeder Besuch ausgehandelt wird.  
> disabled: Mit Aliasen wird immer in ihrem eigenen Format geantwortet.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
Negotiate=enabled
*/ ?>
```

## Placeholder

> Ein neutrales Bild in der richtigen Größe (ein SVG, das dem Dunkelmodus des Browsers folgt, kein Text, kein  
> Logo), solange ein Bild fehlt, statt eines defekten Bildes. Gilt für jedes fehlende Bild.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
Placeholder=enabled
*/ ?>
```

## SwapScript

> Die Platzhalter tragen die Markierung (Server-Timing: exp-image-placeholder), nach der das kleine Skript  
> design/standard/javascript/exp\_image\_swap.js sucht; es fragt erneut und setzt das echte Bild ein.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
SwapScript=enabled
*/ ?>
```

## PreGenerate

> Nach dem Veröffentlichen wird jeder konfigurierte Alias der neuen Bilder im Hintergrund erzeugt, sodass der erste  
> Besucher sie fertig vorfindet: die Vereinigung von AliasList\[\] dieses Siteaccess und seiner verwandten Siteaccesses  
> (site.ini \[SiteAccessSettings\] RelatedSiteAccessList\[\]), jeder so erzeugt, wie ihn der Siteaccess definiert, der ihn  
> aufführt, dazu die WebP/AVIF-Varianten von Formats\[\]. Es ist ein Job der Job-Warteschlange (expqueue, Job-Typ  
> image.alias.generate in settings/expqueue.ini); ohne die Warteschlange wird beim Veröffentlichen nichts umgewandelt  
> (die Aliase werden auf Abruf erzeugt, imagealias.log sagt "no-queue"). Nur im Modus on-demand.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
PreGenerate=enabled
*/ ?>
```

## PreGenerateAliasList

> Beschränkt die Vorab-Erzeugung auf diese Aliase, einen pro Zeile (PreGenerateAliasList\[\]=small). Jeder wird weiterhin so erzeugt,  
> wie ihn der Siteaccess definiert, der ihn in AliasList\[\] aufführt. Ein Name, den kein Siteaccess der Vereinigung  
> definiert, wird übersprungen, mit einer Warnung und einer imagealias.log-Zeile pro Veröffentlichung (event=queue  
> result=unknown-alias). Jeder andere Alias wird auf Abruf erzeugt, wenn eine Seite ihn zum ersten Mal anfordert.  
> Leer (der Standard): jeder Alias der Vereinigung.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
# ein leeres "PreGenerateAliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PreGenerateAliasList[]
*/ ?>
```

## PreGenerateFormats

> Ob die Vorab-Erzeugung auch die WebP/AVIF-Varianten (Formats\[\] der Alias-Blöcke) der  
> Aliase erzeugt, die sie erzeugt. disabled: nur die Aliase selbst; die Varianten werden auf Abruf erzeugt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
PreGenerateFormats=enabled
*/ ?>
```

## MaxParallel

> Gleichzeitige Umwandlungen (Aliase und Varianten) pro Server, gezählt über alle Prozesse: PHP-FPM, Velocity  
> und die Kommandozeile (Warteschlangen-Worker, Skripte). Ein Slot ist eine Sperrdatei in  
> <cache dir>/imagealias-slots/<server name>/. Eine Anfrage, die jeden Slot belegt vorfindet, wartet innerhalb von  
> MaxWait, antwortet dann mit dem Platzhalter und reiht den Alias mit der Priorität der Besucher ein (Job-Typ  
> image.alias.generate), sodass eine Seite voller neuer Bilder nie mehr als so viele Umwandlungen ausführt. Eine  
> begonnene Umwandlung wird immer zu Ende geführt. Nur im Modus on-demand.  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
MaxParallel=2
*/ ?>
```

## BulkNice

> Der nice-Wert (0..19), auf den ein Massenlauf (Job-Typ image.alias.bulk: generate, regenerate oder purge über einen  
> Bereich) seinen Worker-Prozess herabstuft, damit er dem Webserver den Vortritt lässt; ein ImageMagick-Befehl, den er  
> startet, erbt ihn. 0 lässt die Priorität, wie sie ist. Massenläufe lassen sich in  
> Setup > Bild-Aliase oder auf der Kommandozeile pausieren und fortsetzen; ein pausierter Lauf macht dort weiter, wo er stehen blieb.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
BulkNice=10
*/ ?>
```

## CacheMaxAge

> Cache-Control max-age (Sekunden) eines echten Alias, mit dem content/image oder index\_image.php antwortet;  
> Platzhalter werden immer mit no-store gesendet.  

Standard: `31536000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
CacheMaxAge=31536000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
