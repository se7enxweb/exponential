---
title: Kontextmenüs und Integration in die Administration
audience: developer
category: extension-development
order: 200
summary: Wie sich die Kontextmenüeinträge, Links im linken Menü, Tabs der Knotenansicht und oberen Tabs einer alten Erweiterung in die Administration von Exponential 6 einfügen und wie Sie jeden davon ohne Inline-Code und mit Zugriffsprüfungen zum Laufen bringen.
keywords: Kontextmenü, linkes Menü, Tabs, Knotenansicht, Navigationsteil, Zugriffsprüfung, Upgrade, context menu, popupmenu, ContextMenuTemplateArray, SubitemsContextMenuTemplateArray, menuArray, data-menu-form, left menu, Leftmenu_setup, Links, PolicyList, AdditionalTabs, admininterface.ini, menu.ini, upgrade
type: help
---
# Kontextmenüs und Integration in die Administration

Teil 4, Kapitel 20. Eine Erweiterung erscheint an vier Stellen in der Administration: in den Kontextmenüs des
Inhaltsbaums, im linken Menü, in den Tabs der Knotenansicht und im oberen Menü. Alle vier werden über Einstellungen
registriert, die sich seit den alten Releases nicht geändert haben, daher erscheinen die Einträge einer alten
Erweiterung weiterhin. Geändert hat sich, wie sie sich verhalten müssen: kein Inline-Code und kein Link, den ein
Benutzer nicht öffnen kann. Dieses Kapitel geht die vier Stellen durch.

## 1. Einträge im Kontextmenü

Das Rechtsklickmenü des Inhaltsbaums und der Liste der Unterelemente sammelt Templates aus
`admininterface.ini [AdditionalMenuSettings]`. se7enxweb/ezownerchange registriert seine beiden Einträge so:

```ini
<?php /* #?ini charset="utf-8"?

[AdditionalMenuSettings]
ContextMenuTemplateArray[]=popupmenu/ownercontextmenu.tpl
SubitemsContextMenuTemplateArray[]=popupmenu/ownersubitemscontextmenu.tpl

*/ ?>
```

| Einstellung | Menü |
|---|---|
| `ContextMenuTemplateArray[]` | das Kontextmenü des Inhaltsbaums |
| `SubitemsContextMenuTemplateArray[]` | das Kontextmenü der Liste der Unterelemente |
| `SubMenuTemplateArray[]` | Untermenüs davon |
| `ClassMenuTemplateArray[]`, `BookmarkMenuTemplateArray[]`, `AdvancedMenuTemplateArray[]` | die Menüs für Klassen, Lesezeichen und erweiterte Funktionen |
| `ExportMenuTemplateArray[]`, `ImportExportMenuTemplateArray[]` | Einträge für Export und Import |

Die Einstellungen bleiben. Die Templates sind es, die Arbeit brauchen, denn die alten verwenden Inline-Skripte und
`onmouseover`- oder `onclick`-Attribute, die eine durchgesetzte Content Security Policy blockiert: Der Eintrag wird
gezeichnet und tut nichts.

Zwei Lösungen, je nachdem, was der Eintrag tut:

| Der Eintrag ... | Lösung | Beispiel |
|---|---|---|
| sendet ein Formular ab | `data-menu-form="<form id>"` am Eintrag, das `ezpopupmenu.js` des Kernels absendet (ab 6.0.15); eine kleine eigene Datei für ältere Kernel | se7enxweb/expchangeclass, Commit 88b60d9 |
| öffnet eine URL mit den Werten des Knotens | die URL in einem data-Attribut, eine kleine Datei, die den Eintrag zu `menuArray` hinzufügt, ein `mouseover`-Listener | se7enxweb/ezownerchange, Commit a5ebbb8 |

Beide werden vollständig, vorher und nachher, in
[CSP: von Event-Handler-Attributen zu data-Attributen](csp-event-handler-zu-data-attributen.md) gezeigt.

Die eigenen Menüs des Kernels sind vollständig in `data-menu-*`-Attributen deklariert (`data-menu-url`,
`data-menu-form`, `data-menu-submenu`, `data-menu-shown-if` und weitere; der Kopfkommentar von `ezpopupmenu.js`
listet sie auf). Ein Eintrag, der nur in Exponential 6.0.15 oder neuer laufen muss, kann diese direkt verwenden und
braucht überhaupt kein Skript.

> **Warnung**
> Schreiben Sie in einer Erweiterung niemals `var menuArray = new Array();` oder `menuArray = {};`. Das ersetzt die
> Menüs des Kernels, und die zentralen Kontextmenüs öffnen sich nicht mehr. Fügen Sie Ihren Eintrag zum vorhandenen
> Objekt hinzu.

### Den Eintrag nur Benutzern zeigen, die ihn verwenden dürfen

Beide Beispiel-Templates beginnen mit einer Zugriffsprüfung, sodass ein Benutzer ohne die Richtlinie den Eintrag nicht
sieht:

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'owner', 'function', 'change' ) )}
...
{/if}
```

Alte Templates zeigen den Eintrag oft allen und lassen die View ablehnen. Fügen Sie die Prüfung hinzu.

## 2. Links im linken Menü

Ein Link in einem linken Menü (das Menü Setup ist der übliche Ort für die Werkzeuge einer Erweiterung) ist ein
Schlüssel in einem Abschnitt `[Leftmenu_*]` von `menu.ini`. `settings/menu.ini.append.php` von se7enxweb/expqueue:

```ini
<?php /* #?ini charset="utf-8"?
[Leftmenu_setup]
Links[expqueue]=expqueue/dashboard
LinkNames[expqueue]=Job queue
PolicyList_expqueue[]=expqueue/view
*/ ?>
```

- `Links[<key>]`: die Adresse.
- `LinkNames[<key>]`: der Text.
- `PolicyList_<key>[]`: die Richtlinien, die ein Benutzer braucht, um den Link zu sehen.

In Exponential 6 prüft der Kernel standardmäßig auch, ob der Benutzer die Adresse selbst öffnen kann
(`menu.ini [MenuAccessSettings] CheckViewAccess=enabled`), und lässt Links weg, die der Benutzer nicht öffnen kann
(`NoAccessLinks=hidden`). Der Link einer alten Erweiterung erscheint also für Benutzer ohne Zugriff nicht mehr, selbst
wenn sie nie ein `PolicyList_` hatte. Behalten Sie das `PolicyList_` trotzdem oder fügen Sie es hinzu: Es drückt die
Absicht aus und deckt ab, was eine Adresse nicht verraten kann.

### Gruppen in admin4

admin4 ordnet das Menü Setup in Gruppen an (`Groups[]`, `GroupLinks_<group>[]`). Ein Link in keiner Gruppe erscheint
unter „Mehr“. Der Kommentar in der `menu.ini` des Kernels sagt, dass eine Erweiterung ihren Link „mit
`GroupLinks_<group>[]=<key>` in ihrer menu.ini.append.php“ in eine Gruppe setzt; die Gruppen `ai` und `updates`
existieren für die Links von Erweiterungen. Wählen Sie die Gruppe, die zu dem passt, was Ihr Werkzeug tut.

## 3. Tabs der Knotenansicht

Die Tabs der Knotenansicht sind `admininterface.ini [WindowControlsSettings] AdditionalTabs[]`, jeder mit einem
Abschnitt `[AdditionalTab_<id>]`. Der Audit-Tab des Kernels ist ein gutes Vorbild:

```ini
[WindowControlsSettings]
AdditionalTabs[]=audit

[AdditionalTab_audit]
Title=
Description=
NavigationPartName=ezcontentnavigationpart
HeaderTemplate=audit/node_header.tpl
Template=audit/node.tpl
```

Was Sie am Tab einer alten Erweiterung prüfen sollten:

- seine Templates enthalten keinen Inline-Code und kein Inline-CSS (Kapitel 13 bis 16 dieses Teils);
- er prüft den Zugriff in seinem Template, wie der Audit-Tab es für `audit/read` tut;
- er sieht in admin4 richtig aus, hell und dunkel ([admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md)).

Ein Siteaccess kann den Tab einer Erweiterung mit `HiddenTabs[]` ausblenden, das existiert, weil „ein Siteaccess
einen Tab, den eine Erweiterung anhängt, nicht aus AdditionalTabs[] herausnehmen kann“ (der Kommentar in der
`admininterface.ini` des Kernels).

## 4. Tabs im oberen Menü und Navigationsteile

Eine Erweiterung mit einem eigenen Bereich der Administration kann einen Navigationsteil
(`menu.ini [NavigationPart] Part[<identifier>]=<name>`) und einen oberen Tab (`[TopAdminMenu] Tabs[]` mit einem
Abschnitt `[Topmenu_<name>]`) hinzufügen. Die meisten Erweiterungen brauchen keinen: Eine Gruppe von Links in Setup ist
leichter zu finden und hält das obere Menü kurz. Wenn Ihre alte Erweiterung einen oberen Tab hat, ziehen Sie in
Betracht, ihn beim Upgrade nach Setup zu verschieben, und erwähnen Sie das im Changelog.

`[TopAdminMenu]` hat aus demselben Grund wie `HiddenTabs[]` ebenfalls eine Liste von Tabs, die weggelassen werden.

## Ihre Arbeit prüfen

- Klicken Sie mit der rechten Maustaste auf einen Knoten im Inhaltsbaum und in der Liste der Unterelemente, als
  Administrator und als Benutzer ohne Ihre Richtlinie: Der Eintrag erscheint nur für den Ersten und funktioniert.
- Bei geöffneter Browser-Konsole: keine Meldungen der Content Security Policy von Ihren Einträgen.
- Der Link im linken Menü erscheint in admin4 in der richtigen Gruppe und nicht für einen Benutzer, der ihn nicht
  öffnen kann.

## Verwandte Seiten

- Zurück: [admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md)
- Weiter: [Namen und Marken bei einem Upgrade](namen-und-marken-bei-einem-upgrade.md)
- [Content Security Policy](../../../funktionen/6.0/content-security-policy.md)
- [Richtlinienfunktionen und Zugriff](../teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)
- [Module und Views](../teil-3-erweiterungsentwickler/module-und-views.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
