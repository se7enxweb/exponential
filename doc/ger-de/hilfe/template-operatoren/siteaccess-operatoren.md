---
title: Siteaccess-Operatoren
audience: developer, designer, app-creator
category: development
order: 360
summary: Die Kernel-Operatoren, die die Siteaccesses einer Installation kennen - siteaccess_url, siteaccess_menu, siteaccess_menu_key und language_switcher - mit ihren Parametern, den Einstellungen dahinter, was sie zurückgeben, wie sie kompiliert werden, und Beispielen, auf alpha mit drei verschiedenen Siteaccesses gerendert.
keywords: Siteaccess, Sprachumschalter, Siteaccess-Menü, Startseite, Adresse, siteaccess_url, siteaccess_menu, siteaccess_menu_key, language_switcher, siteaccess, AdminSiteAccessMenu, FrontSiteAccess, TranslationSA, LanguageSwitcherClass, switchlanguage, ezpSiteAccessURL, ezpSiteAccessMenu
type: help
---
# Siteaccess-Operatoren

Drei in `kernel/common/eztemplateautoload.php` registrierte Klassen geben Templates die Adressen und Namen der anderen
Siteaccesses der Installation.

| Operator | Klasse und Datei | Arbeit erledigt von |
|---|---|---|
| `siteaccess_url` | `ezpSiteAccessURLOperator`, `kernel/common/ezpsiteaccessurloperator.php` | `ezpSiteAccessURL` (`kernel/private/classes/ezpsiteaccessurl.php`) |
| `siteaccess_menu`, `siteaccess_menu_key` | `ezpSiteAccessMenuOperator`, `kernel/common/ezpsiteaccessmenuoperator.php` | `ezpSiteAccessMenu` (`kernel/private/classes/ezpsiteaccessmenu.php`) |
| `language_switcher` | `ezpLanguageSwitcherOperator`, `kernel/private/eztemplate/ezplanguageswitcheroperator.php` | die Klasse aus `[RegionalSettings] LanguageSwitcherClass` |

Die Beispiele liefen auf der Kommandozeile, daher ist das Schema `http`, und der Host kommt aus den Einstellungen.

## Inhalt

1. [Die Operatoren](#die-operatoren): [siteaccess_url](#siteaccess_url), [siteaccess_menu](#siteaccess_menu),
   [siteaccess_menu_key](#siteaccess_menu_key), [language_switcher](#language_switcher)
2. [Kompilierte Templates](#kompilierte-templates)

## Die Operatoren

### siteaccess_url

Generierte Seite: [siteaccess_url](../../beispiele/template-operatoren/siteaccess_url.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `siteaccess` | Zeichenkette | nein | leer |

- **Eingabe:** keine.
- **Rückgabe:** die Adresse der Startseite eines Siteaccess, mit `/` am Ende, als Zeichenkette. Ohne Namen ist es die
  Startseite der Website, zu der der aktuelle Siteaccess gehört (`ezpSiteAccessURL::front()`): `[SiteSettings]
  FrontSiteAccess` des aktuellen Siteaccess (eine Administration nennt dort ihre Website), sonst `DefaultAccess`. Ein
  Siteaccess, der nicht in `AvailableSiteAccessList[]` steht, ergibt eine leere Zeichenkette.

Die Adresse wird aus Schema, Host und Port der Anfrage und aus der Art ermittelt, wie der Ziel-Siteaccess erkannt wird
(`MatchOrder`, `URIMatchType`, `HostMatchMapItems`, `SiteURL`); sie funktioniert also bei Erkennung über URI, Host und
Port gleichermaßen. Sie wird weder in Anführungszeichen gesetzt noch maskiert.

```tpl
1 front: {siteaccess_url()}
2 bold: {siteaccess_url( 'bold' )}
3 starter_ger: {siteaccess_url( 'starter_ger' )}
4 not available: {siteaccess_url( 'nosuchsite' )|get_type}
```

Ausgabe auf alpha:

```text
1 front: http://alpha.se7enx.com/site/
2 bold: http://alpha.se7enx.com/bold/
3 starter_ger: http://alpha.se7enx.com/starter_ger/
4 not available: string[0]
```

### siteaccess_menu

Generierte Seite: [siteaccess_menu](../../beispiele/template-operatoren/siteaccess_menu.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `user` | Objekt | nein | `null` |

- **Eingabe:** keine.
- **Rückgabe:** ein Hash mit `enabled` (boolescher Wert), `front` (der Eintrag der Website, die die Globus-Schaltfläche
  öffnet) und `groups` (eine Liste von Hashes mit `identifier`, `site` oder `admin`, und `items`). Ein Eintrag ist ein
  Hash mit `identifier`, `name` (`SiteName`, sonst der Bezeichner), `locale`, `lang`, `url`, `display_url` (ohne Schema
  und abschließenden Schrägstrich), `current` und `admin`.

Das sind die Daten der Globus-Schaltfläche in den Kopfzeilen von admin4 und admin4l, konfiguriert in `site.ini`
`[AdminSiteAccessMenu]` des Administrations-Siteaccess: `Show`, `List` (`related` verwendet `RelatedSiteAccessList[]`,
`available` verwendet `AvailableSiteAccessList[]`), `HiddenSiteAccessList[]`, `AdminSiteAccessList[]` und
`ListAdministrations`. Mit einem Benutzer wird ein Siteaccess außer der Startseiten-Website nur aufgeführt, wenn sich
dieser Benutzer dort anmelden darf (`eZUser::canLoginToSiteAccess()`); die Admin-Templates übergeben `$current_user`.

Auf dem Siteaccess `admin` ohne Benutzer gerendert:

```tpl
{def $menu = siteaccess_menu()}
enabled={$menu.enabled|choose( 'no', 'yes' )} key length={siteaccess_menu_key()|count_chars}
front: {$menu.front.identifier} {$menu.front.display_url}

{foreach $menu.groups as $group}
{$group.identifier}: {foreach $group.items as $item}{$item.identifier} ({$item.locale}){delimiter}, {/delimiter}{/foreach}

{/foreach}
```

Ausgabe auf alpha:

```text
enabled=yes key length=32
front: site admin.alpha.se7enx.com/site
site: bold (eng-US), bold_ger (ger-DE), editor (eng-US), starter (eng-US), starter_ger (ger-DE)
```

### siteaccess_menu_key

Generierte Seite: [siteaccess_menu_key](../../beispiele/template-operatoren/siteaccess_menu_key.md).

- **Eingabe:** keine. **Parameter:** keine.
- **Rückgabe:** eine MD5-Zeichenkette für Schlüssel von Cache-Blöcken (eine leere Zeichenkette, wenn das Menü
  abgeschaltet ist). Sie ändert sich mit den aufgeführten Siteaccesses, der Liste der Administrationen, dem
  Startseiten-Siteaccess, dem aktuellen Siteaccess sowie Schema, Host und Port der Anfrage, sodass eine gecachte
  Kopfzeile das richtige Menü zeigt. Zeile 1 des Beispiels oben zeigt ihre Länge. Der Schlüssel liest keine Einstellung
  der aufgeführten Siteaccesses selbst: Ein geänderter `SiteName` oder `Locale` eines von ihnen erscheint erst, nachdem
  die Template-Caches der Administration geleert wurden.

### language_switcher

Generierte Seite: [language_switcher](../../beispiele/template-operatoren/language_switcher.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `destination` | Zeichenkette | nein | leer |

- **Eingabe:** keine.
- **Rückgabe:** das, was `setupTranslationSAList()` der Klasse in `[RegionalSettings] LanguageSwitcherClass` zurückgibt,
  oder nichts, wenn die Einstellung fehlt. Das `ezpLanguageSwitcher` des Kernels gibt einen Eintrag je
  `[RegionalSettings] TranslationSA[<siteaccess>]=<name>` zurück: einen Hash mit `url` (`/switchlanguage/to/<siteaccess>/`
  plus das Ziel), `text` (der Name) und `locale` (`ContentObjectLocale` dieses Siteaccess). Ein leeres Array, wenn
  `TranslationSA` nicht gesetzt ist.

Wenn das Ziel kein `?` enthält, wird der Query-String der aktuellen Anfrage angehängt. Die `url` ist ein Pfad für `ezurl`,
das das Präfix des aktuellen Siteaccess ergänzt; die Ansicht `switchlanguage/to` (`kernel/private/modules/switchlanguage/to.php`)
fragt dieselbe Klasse nach der Adresse des Ziels im anderen Siteaccess und leitet dorthin weiter. Auf alpha setzen nur
einige Siteaccesses `TranslationSA`; auf `starter` gerendert:

```tpl
TranslationSA has {ezini( 'RegionalSettings', 'TranslationSA' )|count} entries

{foreach language_switcher( 'privacy-policy' ) as $sa => $lang}
{$sa}: {$lang.text} {$lang.locale} {$lang.url}

{/foreach}
```

Ausgabe auf alpha:

```text
TranslationSA has 2 entries
starter: English eng-US /switchlanguage/to/starter/privacy-policy
starter_ger: Deutsch ger-DE /switchlanguage/to/starter_ger/privacy-policy
```

## Kompilierte Templates

`siteaccess_url`, `siteaccess_menu` und `siteaccess_menu_key` haben Template-Hinweise mit Transformationen: Der Compiler
schreibt einen direkten Aufruf (`ezpSiteAccessURL::root()` oder `front()`, `ezpSiteAccessMenu::current()`,
`ezpSiteAccessMenu::key()`) in das kompilierte Template, sodass sie zur Laufzeit keine Operatorsuche kosten und bei jedem
Lauf ausgewertet werden, nie zur Kompilierzeit. `language_switcher` hat keine Hinweise und läuft über `modify()`.

## Verwandte Seiten

- [URL-, Design-, INI- und HTTP-Operatoren](url-operatoren.md)
- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
