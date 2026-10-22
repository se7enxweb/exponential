---
title: Module, Views und Zugriffskontrolle
audience: software-engineer, developer
category: software-engineering
order: 140
summary: Wie Exponential eine Anfrage an Code weiterleitet und entscheidet, ob der Benutzer ihn ausführen darf - Module, Views und Richtlinienfunktionen in module.php, klassenbasierte Views, Rollen, Richtlinien und Einschränkungen, die Zugriffsprüfungen der Weiterleitungsschleife, Anfrageregeln und die Regeln, die eine neue View einhalten muss.
keywords: Modul, View, module.php, ViewList, FunctionList, ModuleView, Richtlinie, Rolle, Einschränkung, eZRole, eZPolicy, hasAccessTo, PolicyOmitList, Anfrageregeln, Routing, Autorisierung, Softwareingenieur, module, view, policy, role, limitation, request rules, routing, authorization
type: help
---
# Module, Views und Zugriffskontrolle

Diese Seite richtet sich an Ingenieure, die Seiten oder Endpunkte hinzufügen oder prüfen, ob eine Änderung vom falschen
Benutzer erreicht werden kann. In Exponential werden Routing und Autorisierung zusammen deklariert: Die Definition einer
View nennt die Berechtigung, die sie braucht. Die maßgeblichen Regeln stehen in
[Kernel-Architektur](../../standards/architektur/kernel-architektur.md#module-ansichten-und-funktionen).

## Inhalt

1. [Module, Views und Funktionen](#module-views-und-funktionen)
2. [Klassenbasierte Views](#klassenbasierte-views)
3. [Rollen, Richtlinien und Einschränkungen](#rollen-richtlinien-und-einschränkungen)
4. [Die Prüfungen der Weiterleitungsschleife](#die-prüfungen-der-weiterleitungsschleife)
5. [Anfrageregeln](#anfrageregeln)
6. [Regeln für eine neue View](#regeln-für-eine-neue-view)

## Module, Views und Funktionen

| Begriff | Bedeutung | Beispiel |
|---|---|---|
| Modul | ein URL-Namensraum | `content`, `user`, `exphelp` |
| View | eine Seite oder ein Endpunkt eines Moduls | `content/view`, `exphelp/viewer` |
| Funktion | eine Berechtigung, die Views verlangen | `content/read`, `exphelp/read` |

`kernel/<module>/module.php` legt `$Module`, `$ViewList` (je View: `script`, `params`, `functions`,
`default_navigation_part`, ...) und `$FunctionList` fest (die Richtlinienfunktionen und die Einschränkungen, die jede
annimmt). Die Modul-Repositorys sind `settings/module.ini [ModuleSettings] ModuleRepositories[]`, und nur Module aus
`ModuleList[]` sind erreichbar. Eine Erweiterung fügt Module über `ExtensionRepositories[]` und
`extension/<ext>/modules/<module>/module.php` hinzu
([Referenz der Moduldefinition](../ersteller/anwendungsentwickler/referenz-der-moduldefinition.md)).

Adressen werden meist nicht als Modulpfade eingegeben: Ein URL-Alias wie `/About-us` wird in `content/view/full/58`
übersetzt, bevor das Modul gefunden wird ([URLs und Aliase](../../anleitungen/urls-und-aliase.md)).

## Klassenbasierte Views

Seit 6.0.15 ist der Code einer Kernel-View eine Klasse. `kernel/content/view.php` ist eine einzeilige Einstiegsdatei, die
`\Exponential\View\Kernel\Content\View::main()` in `kernel/private/classes/views/content/view.php` aufruft, eine
Unterklasse von `Exponential\Runnable\ModuleView`. Befehle und Cronjob-Teile folgen demselben Muster. Views als einfache
Dateien laufen weiter, sodass Erweiterungen nicht umziehen müssen
([Befehle, Cronjob-Teile und Modul-Views als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)).

Eine View-Klasse lässt sich über `site.ini [RunnableSettings] Implementation[]` ersetzen, und ein ganzes Kernel-Modul
kann von einer Erweiterung ersetzt werden ([Override von Modulen durch Erweiterungen](../../funktionen/6.0/modulueberschreibung-durch-extensions.md)).

## Rollen, Richtlinien und Einschränkungen

| Objekt | Klasse | Bedeutung |
|---|---|---|
| Rolle | `eZRole` | ein benannter Satz von Richtlinien |
| Richtlinie | `eZPolicy` | gewährt eine Modulfunktion, optional eingeschränkt |
| Einschränkung | `eZPolicyLimitation`, `eZPolicyLimitationValue` | engt eine Richtlinie ein: nach Klasse, Sektion, Teilbaum, Eigentümer, Sprache, Zustand ... |
| Zuweisung | | eine Rolle, die einem Benutzer oder einer Gruppe gegeben wird, optional auf einen Teilbaum oder eine Sektion eingeschränkt |

Die Einschränkungen, die eine Funktion annimmt, werden in `$FunctionList` des Moduls deklariert. Die Prüfungen sind
`eZUser::hasAccessTo( $module, $function )` und `eZUser::hasAccessToView()`; Templates verwenden
`fetch( 'user', 'has_access_to', ... )`. Inhalts-Fetches wenden Leseberechtigungen in SQL über
`eZContentObjectTreeNode` an, sodass eine Liste nie einen Knoten zeigt, den der Benutzer nicht lesen darf. Eigene
Inhaltseinschränkungen sind über Handler für Einschränkungen möglich
([Handler für Inhaltseinschränkungen](../../funktionen/6.0/handler-fuer-inhaltseinschraenkungen.md)).

**Über den Zugriff entscheiden Richtlinien, nie eine Prüfung einer Benutzer- oder Gruppen-ID in Code oder Templates.**
Anleitung: [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md).

## Die Prüfungen der Weiterleitungsschleife

Für jede Webanfrage, der Reihe nach:

1. `eZModule::accessAllowed()`: Ist das Modul auf diesem Siteaccess erlaubt?
2. Die Anmeldeprüfung des Siteaccess: Hat der Benutzer `user/login` für diesen Siteaccess?
3. `eZUser::hasAccessToView()`: Hat der Benutzer die Funktionen, die die View nennt?
4. Anfrageregeln, wenn konfiguriert (unten).
5. Die View selbst kann feinere Berechtigungen prüfen (zum Beispiel `canEdit()` an einem Objekt).

Views in `site.ini [RoleSettings] PolicyOmitList[]` überspringen die Richtlinienprüfung absichtlich; jeder Eintrag dort
ist ein öffentlicher Endpunkt und verdient eine Prüfung.

## Anfrageregeln

Anfrageregeln laufen nach der URL-Übersetzung und nachdem die Rollenrichtlinien die View erlaubt haben, und können
erlauben, verweigern, weiterleiten, umschreiben oder protokollieren. Sie schließen die Lücke zwischen einer Seite und
ihrer System-URL (`/content/view/full/123` gegenüber ihrem Alias). Prüfen Sie, was die Regeln für eine beliebige Adresse
entscheiden, ohne etwas zu ändern:

```bash
php bin/php/ezrequestrules.php -s site --check
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2
```

Siehe [Anfrageregeln](../../funktionen/6.0/anfrageregeln.md) und [Sicherheit von view/full](../../bc/6.0/sicherheit-von-content-view-full.md).

## Regeln für eine neue View

- Sie **muss** in `functions` die Richtlinienfunktionen nennen, die sie braucht; eine View ohne sie steht jedem Benutzer
  offen, der die Siteaccess-Prüfung besteht.
- Sie **sollte** eine Unterklasse von `ModuleView` sein, kein einfaches Skript.
- Sie liest Eingaben über `eZHTTPTool`, verlässt sich bei POST-Anfragen auf die Prüfung des Formular-Tokens und rendert
  über `eZTemplate::factory()`.
- Ein neues Modul **sollte** seine Funktion einem Installations- oder Aktualisierungsskript hinzufügen, damit vorhandene
  Websites sie erhalten (zum Beispiel `bin/php/addexphelpreadpolicy.php` für `exphelp/read`).

## Verwandte Seiten

- [Eigene Module und Views](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md)
- [Richtlinienfunktionen und Einschränkungen](../ersteller/anwendungsentwickler/richtlinienfunktionen-und-einschraenkungen.md)
- [Checkliste für Sicherheitsprüfungen](checkliste-fuer-sicherheitspruefungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
