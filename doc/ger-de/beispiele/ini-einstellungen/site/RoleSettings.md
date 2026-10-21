---
title: site.ini [RoleSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RoleSettings] in site.ini: EnableCaching, PoliciesPerPage, AccessArrayPrefetch, PreventPrivilegeEscalation, AssignmentsPerPage, RolesPerPageList, PolicyPreviewPerRole, PermissionSQLOptimization ..."
keywords: Einstellung, site.ini, RoleSettings, INI, setting, EnableCaching, PoliciesPerPage, AccessArrayPrefetch, PreventPrivilegeEscalation, AssignmentsPerPage, RolesPerPageList, PolicyPreviewPerRole, PermissionSQLOptimization, PolicyOmitList, PolicyOmitListExclude, ShowAccessDeniedReason, MaxParentDepthLimitation, LimitationHandlers
type: generated
generated: exp:help:examples
---
# site.ini [RoleSettings]

Der Block `[RoleSettings]` von `settings/site.ini`, mit 13 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EnableCaching

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
EnableCaching=true
*/ ?>
```

## PoliciesPerPage

> Wie viele Richtlinien eine Ansicht auf einmal zeigt.  
>
> Die Richtlinienliste einer Rolle wird geblättert. $role.policies ist jede Richtlinie, die die Rolle hat,  
> was das Berechtigungssystem braucht und eine Ansicht nicht: In einer Installation,  
> deren Rollen Millionen von Richtlinien tragen, erschöpft das Laden aller, um eine  
> Bildschirmseite zu zeichnen, den Speicher, bevor die erste Zeile geschrieben ist.  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PoliciesPerPage=25
*/ ?>
```

## AccessArrayPrefetch

> Wie das Zugriffs-Array eines Benutzers aufgebaut wird, wenn der Rollen-Cache neu aufgebaut wird.  
> enabled lädt die Richtlinien, Einschränkungen und Werte aller Rollen des Benutzers in  
> drei Abfragen und berechnet die Einschränkungen einer Richtlinie einmal für alle  
> Zuweisungen ihrer Rolle (ein Mitglied von 120 Teamräumen hat jede Richtlinie der  
> Mitgliederrolle 120-mal). disabled fragt die Datenbank wie bisher einmal für jede Rolle, Richtlinie  
> und Einschränkung. Das Zugriffs-Array ist in beiden Fällen gleich, in derselben  
> Reihenfolge.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
AccessArrayPrefetch=enabled
*/ ?>
```

## PreventPrivilegeEscalation

> Ob ein Benutzer, der Rollen bearbeiten darf, mehr vergeben kann, als er selbst hat.  
> enabled (der Standard) lehnt im Rolleneditor, im Richtlinienassistenten, im  
> Richtlinieneditor, in role/copy und role/assign jede Richtlinie oder Zuweisung ab, die  
> weiter reicht als der eigene Zugriff des Bearbeiters: Eine Richtlinie muss von einer der  
> Richtlinien des Bearbeiters abgedeckt sein (Modul, Funktion und jede Einschränkung dieser Richtlinie, ein  
> Teilbaum innerhalb eines ihrer Teilbäume, Sektionen, Klassen, Knoten, Sprachen und  
> andere Werte unter ihren Werten), und eine Rolle darf nur zugewiesen werden, wenn der  
> Bearbeiter jede ihrer Richtlinien abdeckt, eingegrenzt durch die Einschränkung der Zuweisung.  
> Benutzer mit Zugriff auf jede Funktion jedes Moduls dürfen alles vergeben.  
> disabled stellt das alte Verhalten wieder her: Rolleneditoren dürfen alles vergeben.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PreventPrivilegeEscalation=enabled
*/ ?>
```

## AssignmentsPerPage

> Wie viele der Benutzer und Benutzergruppen, denen eine Rolle zugewiesen ist, role/view  
> auf einmal zeigt, nach Namen sortiert und weitergeblättert (assignment\_offset). Eine Rolle, die  
> Tausenden von Benutzern gegeben wurde, brauchte lange zum Zeichnen. 0 listet alle auf einer Seite auf.  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
AssignmentsPerPage=50
*/ ?>
```

## RolesPerPageList

> Die Seitengrößen, die die Rollenliste in ihrer Auswahl für Einträge pro Seite anbietet.  
>
> Die gespeicherte Einstellung ist die Position in dieser Liste, ab eins gezählt, sodass eine  
> Website, die diese ändert, die Wahl aller behält statt sie zurückzusetzen,  
> und die alten 1, 2 und 3 bedeuten weiterhin den ersten, zweiten und dritten Eintrag.  

Standard: eine Liste mit 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# ein leeres "RolesPerPageList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RolesPerPageList[]
RolesPerPageList[]=10
RolesPerPageList[]=25
RolesPerPageList[]=50
*/ ?>
```

## PolicyPreviewPerRole

> Wie viele Richtlinien einer Rolle das Zusammenfassungsfenster eines Benutzers oder einer Benutzergruppe  
> zeigt, bevor es aufhört und auf die Rolle verlinkt. Dieses Fenster listet jede Rolle des  
> Benutzers auf, es ist also das Produkt zweier Listen statt einer.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PolicyPreviewPerRole=10
*/ ?>
```

## PermissionSQLOptimization

> Wie die Berechtigungsbedingung eines Inhalts-Fetches aus den Leserichtlinien  
> des Benutzers aufgebaut wird. enabled fasst die Richtlinien zusammen, die sich nur im Teilbaum unterscheiden, für den ihre  
> Rolle zugewiesen ist (ein Mitglied von 120 Teamräumen hat jede Leserichtlinie der  
> Mitgliederrolle 120-mal), und lässt die weg, die keinen Zugriff auf einen Knoten geben können,  
> den der Fetch zurückgibt: Teilbäume außerhalb des abgerufenen, Klassen außerhalb seines Klassen-  
> filters. Der Fetch gibt dieselben Knoten zurück. disabled verknüpft wie bisher jede Richtlinie mit OR;  
> SQLite lehnt diese Bedingung ab 1000 Richtlinien ab.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
PermissionSQLOptimization=enabled
*/ ?>
```

## PolicyOmitList

> Eine Liste der Module, bei denen die Richtlinienprüfung ausgelassen wird;  
> fügen Sie 'role' der Liste hinzu, wenn Sie den  
> Zugriff auf das Modul role verlieren  
> Sie können auch Ansichten angeben, indem Sie ein / und den Namen der Ansicht anhängen  
> Dies ist eine scheinbare Auslassung: Wir brauchen eine manuelle Richtlinienprüfung, daher direkt in oauth/authorize.php umgesetzt  
> Bestellbelege entscheiden selbst: der Kunde, der die Bestellung aufgegeben hat, oder  
> shop/administrate (eZShopReceipt::canView); alle anderen erhalten das Anmeldeformular.  
> E-Mail-Einstellungen: settings und export prüfen die Anmeldung selbst, manage,  
> unsubscribe und confirm arbeiten mit einem signierten Link, request sendet einen.  
> API-Zugang: apikey/list prüft selbst (angemeldet, um eigene Schlüssel zu sehen und zu widerrufen,  
> apikey/create, um einen zu erstellen).  
> Das Mega-Menü von admin4: user/megamenu prüft selbst (ein angemeldeter Benutzer, der sich am  
> Siteaccess anmelden darf, user/login mit seiner Einschränkung SiteAccess, und der für diesen Benutzer offene Reiter), sodass jede  
> Ablehnung für sein Skript ein bloßes 403 statt einer Seite ist.  
> Die Berichte der Content Security Policy kommen von Browsern, angemeldet oder nicht; csp/report schützt sich selbst  
> (Inhaltstypen, Größe, Ratenbegrenzung, maskierte Logzeilen).  

Standard: eine Liste mit 23: `user/login`, `user/logout`, `user/register`, `user/activate`, `user/success`, `user/forgotpassword`, `layout`, `manual`, `ezinfo`, `paypal/notify_url`, `switchlanguage`, `oauth/authorize` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# ein leeres "PolicyOmitList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyOmitList[]
PolicyOmitList[]=user/login
PolicyOmitList[]=user/logout
PolicyOmitList[]=user/register
PolicyOmitList[]=user/activate
PolicyOmitList[]=user/success
PolicyOmitList[]=user/forgotpassword
PolicyOmitList[]=layout
PolicyOmitList[]=manual
PolicyOmitList[]=ezinfo
PolicyOmitList[]=paypal/notify_url
PolicyOmitList[]=switchlanguage
PolicyOmitList[]=oauth/authorize
PolicyOmitList[]=shop/orderreceipt
PolicyOmitList[]=shop/orderreciept
PolicyOmitList[]=mailpreferences/settings
PolicyOmitList[]=mailpreferences/manage
PolicyOmitList[]=mailpreferences/unsubscribe
PolicyOmitList[]=mailpreferences/confirm
PolicyOmitList[]=mailpreferences/request
PolicyOmitList[]=mailpreferences/export
# ... 3 weitere in der ausgelieferten Datei
*/ ?>
```

## PolicyOmitListExclude

> Einträge, die nach dem Zusammenführen jeder Einstellungsdatei aus PolicyOmitList\[\] entfernt werden, sodass eine Website  
> einen Eintrag schließen kann, ohne die Liste zurückzusetzen (ein Zurücksetzen würde auch die Einträge verwerfen, die die aktiven  
> Erweiterungen hinzufügen). Neue Installationen schreiben PolicyOmitListExclude\[\]=user/register in  
> settings/override/site.ini.append.php: Die Registrierung braucht dann die Richtlinie user/register,  
> die ihre Rolle Anonymous hat (entfernen Sie sie dort, um die Registrierung zu schließen), und die Anmelde-  
> seiten zeigen den Link „Registrieren“ nur einem Benutzer, der sie hat. Eine aktualisierte Website lässt user/register offen;  
> um es zu schließen, fügen Sie die Zeile oben in settings/override/site.ini.append.php hinzu.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# ein leeres "PolicyOmitListExclude[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyOmitListExclude[]
*/ ?>
```

## ShowAccessDeniedReason

> Ob versucht wird, in der Ansicht den Grund für einen verweigerten Zugriff zu zeigen  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
ShowAccessDeniedReason=disabled
*/ ?>
```

## MaxParentDepthLimitation

> Gibt die höchste Ebene für die Einschränkung 'ParentDepth' an.  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
MaxParentDepthLimitation=20
*/ ?>
```

## LimitationHandlers

> Handler für Einschränkungen von Inhaltsrichtlinien, die der Kernel selbst nicht kennt, eine  
> Klasse pro Einschränkungsname; die Klasse implementiert ezpContentLimitationHandler.  
> Eine Erweiterung, die einer Inhaltsfunktion eine Einschränkung hinzufügt (Event-Filter  
> module/functionlist), registriert ihren Handler hier. Eine Einschränkung ohne  
> Handler verweigert: in checkAccess() und im SQL von Listen- und Baum-Fetches.  
> LimitationHandlers\[MyLimitation\]=myLimitationHandler  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RoleSettings]
# ein leeres "LimitationHandlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LimitationHandlers[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
