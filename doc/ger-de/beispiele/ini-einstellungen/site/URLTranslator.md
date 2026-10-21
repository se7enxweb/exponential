---
title: site.ini [URLTranslator]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [URLTranslator] in site.ini: Translation, TranslatableSystemUrls, LoadOnPartialAliasPath, WordSeparator, TransformationGroup, UrlAliasNameLimit, DecodeOnce, TrailingCleanup ..."
keywords: Einstellung, site.ini, URLTranslator, INI, setting, Translation, TranslatableSystemUrls, LoadOnPartialAliasPath, WordSeparator, TransformationGroup, UrlAliasNameLimit, DecodeOnce, TrailingCleanup, WildcardTranslation, MaximumWildcardIterations, NodeTranslation, FilterClasses
type: generated
generated: exp:help:examples
---
# site.ini [URLTranslator]

Der Block `[URLTranslator]` von `settings/site.ini`, mit 12 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Translation

> Steuert, ob die URL-Übersetzung aktiviert ist.  
> Mit aktivierter URL-Übersetzung können Sie den Pfad eines Inhaltsobjekts  
> in der URL eingeben, etwa my/nice/path, und erhalten das Inhaltsobjekt.  
> Außerdem erlaubt die URL-Übersetzung eigene Übersetzungen von URLs  
> Verwenden Sie enabled oder disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
Translation=enabled
*/ ?>
```

## TranslatableSystemUrls

> Steuert, ob eigene URL-Übersetzungen für System-URLs deaktiviert werden, deren  
> erster Teil in module.ini Modulelist definiert ist, z. B. 'content'  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
TranslatableSystemUrls=enabled
*/ ?>
```

## LoadOnPartialAliasPath

> Pfad, der beim Aufruf eines teilweisen Alias geladen wird  
> Zum Beispiel, wenn es eine Weiterleitung von /path/one/two gibt, der Benutzer aber /path/one aufruft  
> Standardmäßig wird die Startseite geladen, Sie können aber zum Beispiel eine 404-Seite laden lassen  

Standard: `/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
LoadOnPartialAliasPath=/
*/ ?>
```

## WordSeparator

> Art des Worttrenners für URL-Aliase, kann eines sein von:  
> dash - einen Bindestrich verwenden  
> underscore - einen Unterstrich verwenden  
> space - ein Leerzeichen verwenden  
> Jeder andere Wert ergibt einen Bindestrich  

Standard: `dash`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
WordSeparator=dash
*/ ?>
```

## TransformationGroup

Standard: `urlalias`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
TransformationGroup=urlalias
*/ ?>
```

## UrlAliasNameLimit

> Entfernen Sie den Kommentar, um URL-Aliase neuen Stils mit Unicode-Unterstützung zu erhalten  
> TransformationGroup=urlalias\_iri  
> Entfernen Sie den Kommentar, um URL-Aliase alten Stils zu erhalten  
> TransformationGroup=urlalias\_compat  
> Längengrenze für urlAliasName, die Grenze pro Pfadelement der URL.  
> Auf 0 setzen, um sie abzuschalten.  

Standard: `255`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
UrlAliasNameLimit=255
*/ ?>
```

## DecodeOnce

> Die URL der Anfrage einmal (enabled) statt zweimal (disabled, der ausgelieferte Wert) dekodieren.  
> Die Adresse wird beim Start der Anfrage URL-dekodiert, und eZURI dekodiert sie erneut, sodass ein  
> kodiertes "+" in einem Pfad wie /(foo)/bar%2Bbar eine Ansicht als "bar bar" statt als  
> "bar+bar" erreicht. Mit enabled kommt es als "bar+bar" an, und ein aus einer Zeichenkette erzeugtes eZURI nimmt  
> diese Zeichenkette als dekodierten Text (PHP-Code mit einer kodierten URL übergibt $decode = true an  
> new eZURI()). Adressen mit einem kodierten "%" oder "+" in einem Pfadelement oder einem View-  
> Parameter erreichen die Ansicht dann anders; prüfen Sie sie also, bevor Sie es einschalten.  
> Setzen Sie es in settings/override/site.ini.append.php: Die Adresse wird gelesen, bevor der  
> Siteaccess bekannt ist.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
DecodeOnce=disabled
*/ ?>
```

## TrailingCleanup

> In der IRI-Transformation (TransformationGroup=urlalias\_iri) auch das doppelte  
> Anführungszeichen als reserviertes Zeichen behandeln (enabled), sodass es zum Worttrenner wird und  
> am Anfang und Ende eines Alias entfernt wird, wie schon "!", Punkte und Trennzeichen.  
> disabled (der ausgelieferte Wert) behält die heutigen Aliase. Vorhandene Aliase bleiben,  
> wie sie sind, bis ihr Objekt erneut veröffentlicht wird.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
TrailingCleanup=disabled
*/ ?>
```

## WildcardTranslation

> Steuert, ob die Platzhalter-Übersetzung verwendet wird  
> Wenn aktiviert, werden Platzhalter-Übersetzungen verwendet, wenn keine normale  
> Übersetzung verfügbar ist, und sie dienen auch dazu, sich an  
> alte Teilbäume zu erinnern. Es wird empfohlen, dies aktiviert zu lassen,  
> sofern Sie keine Probleme damit haben.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
WildcardTranslation=enabled
*/ ?>
```

## MaximumWildcardIterations

> Steuert, wie oft die Platzhalter-Treffer iterieren dürfen.  
> Das Platzhaltersystem iteriert, wenn die übersetzte  
> URL keine vollständige URL ist; das erlaubt eine rekursive  
> Suche nach URLs und ist für eine korrekte Historie von Teilbäumen nötig.  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
MaximumWildcardIterations=20
*/ ?>
```

## NodeTranslation

> VERALTET: Verwenden Sie stattdessen die Einstellung Translation  
> Aktivieren Sie dies, wenn Sie die URL als Pfadelement  
> des Knotenbaums auffassen wollen. Geben Sie den Pfad in der URL ein und beenden Sie ihn  
> mit .html, um den richtigen Knoten zu erhalten.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
NodeTranslation=enabled
*/ ?>
```

## FilterClasses

> Liste der Klassen, die Sie als eZURLALiasFilter aufrufen wollen  

Standard: eine Liste mit 1: `eZURLAliasFilterAppendNodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
# ein leeres "FilterClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FilterClasses[]
FilterClasses[]=eZURLAliasFilterAppendNodeID
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
