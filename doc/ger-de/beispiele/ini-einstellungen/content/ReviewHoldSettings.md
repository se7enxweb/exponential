---
title: content.ini [ReviewHoldSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReviewHoldSettings] in content.ini: AutoHold, AutoHoldClassList, ExemptPolicyList"
keywords: Einstellung, content.ini, ReviewHoldSettings, INI, setting, AutoHold, AutoHoldClassList, ExemptPolicyList
type: generated
generated: exp:help:examples
---
# content.ini [ReviewHoldSettings]

Der Block `[ReviewHoldSettings]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AutoHold

> Automatische Zurückhaltung von Inhalten, die Besucher einreichen (doc/eng-us/features/6.0/review-hold.md, „Auto-hold“): Die erste  
> Veröffentlichung eines Objekts einer Klasse aus AutoHoldClassList\[\], durch einen Benutzer, der nicht ausgenommen ist (ExemptPolicyList\[\]),  
> kommt in die Zurückhaltung zur Prüfung (review\_hold/held), und ihre Orte werden verborgen, in der Veröffentlichungsoperation selbst (dem  
> Schritt auto-review-hold, direkt vor den Content-Edit-Handlern, dem View-Cache und dem Suchindex), sodass Besucher  
> sie nie sehen. Ein Prüfer gibt sie unter Dashboard > Neue Inhalte (moderation/recent) oder in der Website-Seitenleiste frei:  
> Die Zurückhaltung wird aufgehoben, und die von der automatischen Zurückhaltung verborgenen Orte werden wieder sichtbar. Im Audit aufgezeichnet als  
> content.object.hold.auto. Braucht die Zustandsgruppe der Zurückhaltung zur Prüfung (den Upgrade-Schritt 6.0.15-review-hold); ohne  
> sie wird nichts zurückgehalten. Gelesen im Siteaccess, in dem der Inhalt veröffentlicht wird.  
>
> enabled oder disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ReviewHoldSettings]
AutoHold=disabled
*/ ?>
```

## AutoHoldClassList

> Die Klassenbezeichner, deren neue Objekte zurückgehalten werden (Kommentare, Bewertungen, Forenbeiträge, Einreichungen von Benutzern ...).  

Standard: eine Liste mit 1: `comment`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ReviewHoldSettings]
# ein leeres "AutoHoldClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AutoHoldClassList[]
AutoHoldClassList[]=comment
*/ ?>
```

## ExemptPolicyList

> Wer nie automatisch zurückgehalten wird: ein Benutzer, der eine davon hat (Modul/Funktion). Drei werden genau gelesen:  
>   state/assign   darf den Zustand „zurückgehalten“ auf das Objekt setzen (die Richtlinie state/assign mit ihren Einschränkungen)  
>   content/hide   darf den Ort des Objekts verbergen (und damit wieder sichtbar machen)  
>   moderation/act darf die Aktionen von Dashboard > Neue Inhalte ausführen  
> jedes andere Modul/jede andere Funktion zählt, wenn der Benutzer sie überhaupt hat.  

Standard: eine Liste mit 2: `state/assign`, `moderation/act`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ReviewHoldSettings]
# ein leeres "ExemptPolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExemptPolicyList[]
ExemptPolicyList[]=state/assign
ExemptPolicyList[]=moderation/act
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
