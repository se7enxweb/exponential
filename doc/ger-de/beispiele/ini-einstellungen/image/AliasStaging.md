---
title: image.ini [AliasStaging]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AliasStaging] in image.ini: Environment, SyncFrom, PathMap, Timeout, MaxFileSize, FailuresBeforePause, PauseSeconds, AlertEmail ..."
keywords: Einstellung, image.ini, AliasStaging, INI, setting, Environment, SyncFrom, PathMap, Timeout, MaxFileSize, FailuresBeforePause, PauseSeconds, AlertEmail, AlertRecipients
type: generated
generated: exp:help:examples
---
# image.ini [AliasStaging]

Der Block `[AliasStaging]` von `settings/image.ini`, mit 9 Einstellung(en).

> Ein Alias-Block kann zusätzliche Formate für die Browser aufführen, die sie akzeptieren (siehe Negotiate):  
> Formats\[\]=webp  
> Formats\[\]=avif  
> Die Reihenfolge ist die Präferenz, wenn ein Browser beide gleichermaßen akzeptiert. Eine Variante wird auf Abruf aus der  
> Alias-Datei erzeugt und daneben als "<alias file>.<format>" gespeichert (foo\_large.jpg.webp); weder das XML des  
> Attributs noch ezimagefile nennen sie, sodass beide genau so bleiben, wie der Modus render sie schreibt. Sie wird  
> neu erzeugt, wenn der Alias neuer ist, und mit ihrem Alias entfernt. Ihr Speicherpfad funktioniert auch für sich allein  
> (index\_image.php erzeugt sie), für Webserver, die selbst aushandeln. Qualität: Quality\[\]=image/webp;80 des Alias-Blocks,  
> sonst \[MIMETypeSettings\] Quality\[\]=image/webp;80, sonst 80 (WebP) und 60 (AVIF).  
> GIF- und SVG-Aliase bekommen keine Varianten.  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Environment

> Staging: Eine Site, die nicht die Produktions-Site ist, holt die Bilder, die ihr fehlen (Originale und  
> Aliase), von einer anderen Installation, sodass eine Entwicklungs- oder Staging-Site keine Kopie von var/storage braucht.  
> Ein fehlender Alias wird zuerst von der Quelle geholt; hat auch die Quelle ihn nicht, wird sein  
> Original geholt und der Alias hier erzeugt. Ein fehlendes Original, das über seinen Speicherpfad angefordert wird, wird  
> geholt. Alles innerhalb von \[AliasSettings\] MaxWait; danach wird mit dem Platzhalter geantwortet, und der Browser fragt  
> erneut. Nur veröffentlichte Bilder (keine Entwürfe) werden per Staging geholt. Ein resync (Setup > Bild-Aliase oder die  
> Kommandozeile) holt die Dateien vieler Bilder auf einmal: die fehlenden oder auch die abweichenden.  
>
> Die Umgebungen (eine pro Installation derselben Site):  
>   alpha  frühe Testinstallation von neuem Code, weit vor einem Release  
>   dev    die Installation eines Entwicklers  
>   beta   eine Vorschau für Tester und Redakteure  
>   stage  die Vorproduktionskopie: die letzte Prüfung, bevor ein Release live geht  
>   pro    Produktion, die Live-Site: die Quelle der anderen; sie holt nie etwas  
>   post   Postproduktion: eine Kopie der Produktion nach einem Release, für Prüfungen und Hotfixes  
>   arc    ein Archiv: eine eingefrorene Kopie einer früheren Produktion  
> Die meisten Sites brauchen nur eine dev- oder stage-Installation, die von pro synchronisiert; von pro (Produktion) zu synchronisieren ist  
> der übliche Anfang.  
>
> Die Umgebung dieser Installation; pro (der Standard) holt nie. Ein unbekannter Wert zählt als pro.  

Standard: `pro`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
Environment=pro
*/ ?>
```

## SyncFrom

> Die Umgebung, von der fehlende Bilder geholt werden (zum Beispiel pro); ihr Block \[AliasSource\_<environment>\]  
> nennt, wo sie ist. Leer: kein Staging.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
SyncFrom=
*/ ?>
```

## PathMap

> Pfadzuordnung für eine Quelle, deren Speicherpfade von denen dieser Site abweichen: <local prefix>;<source prefix>, wobei  
> das Quellpräfix ein Pfad unterhalb von BaseURL oder eine ganze URL ist. Das längste passende lokale Präfix gewinnt; ohne  
> Treffer wird eine Datei von BaseURL plus ihrem eigenen Speicherpfad geholt.  
> PathMap\[\]=var/site/storage;var/production/storage  
> PathMap\[\]=var/site/storage;https://media.example.org/var/production/storage  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
# ein leeres "PathMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PathMap[]
*/ ?>
```

## Timeout

> Sekunden, die ein Download dauern darf (nie mehr, als bei einer Seitenanfrage von MaxWait übrig ist).  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
Timeout=5
*/ ?>
```

## MaxFileSize

> Die größte Datei, die geholt wird, in Bytes; eine größere wird nicht gespeichert.  

Standard: `67108864`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
MaxFileSize=67108864
*/ ?>
```

## FailuresBeforePause

> Nach so vielen Fehlschlägen in Folge (keine Antwort, eine Zeitüberschreitung, eine abgelehnte oder ungültige Antwort wie eine  
> HTML-Fehlerseite, eine Datei, die die Quelle weder als Alias noch als Original hat) wird die Quelle pausiert (Circuit  
> Breaker)...  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
FailuresBeforePause=5
*/ ?>
```

## PauseSeconds

> ...für so viele Sekunden: Fehlende Bilder bekommen sofort den Platzhalter, niemand wartet auf die Quelle.  
> Die Pause, ihr Grund und der letzte Fehler werden in Setup > Bild-Aliase angezeigt; eine Datei, die wieder  
> durchkommt, beendet die Zählung.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
PauseSeconds=300
*/ ?>
```

## AlertEmail

> Wenn eine Quelle pausiert wird, geht eine E-Mail an AlertRecipients\[\] (höchstens einmal pro Stunde und Quelle).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
AlertEmail=enabled
*/ ?>
```

## AlertRecipients

> Die Adressen dieser E-Mail; keine: site.ini \[MailSettings\] AdminEmail.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
# ein leeres "AlertRecipients[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AlertRecipients[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
