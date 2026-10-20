# Admin-Links folgen den Berechtigungen

Diese Seite ist für Administratoren, die Rollen für Redakteure einrichten, und für Template-Autoren, die Admin-Links
anzeigen. Seit dem 02.10.2026 zeigen das Admin-Dashboard, das obere Menü und die linken Menüs einen Link nur einem
Benutzer, der die Adresse dahinter öffnen kann. Vorher sah ein Redakteur die Reiter Design, Newsletter und Export sowie
Einträge im linken Menü wie Benutzer, Tags oder Papierkorb, die seine Rolle nicht erlaubte, und erhielt beim Klick
eine Seite „Zugriff verweigert“.

## Was geprüft wird

Die Kernel-Klasse `expViewAccess` beantwortet die Frage „Kann dieser Benutzer diese Adresse öffnen?“ genauso, wie es
die Anfrage selbst tun würde. Sie prüft:

- den URL-Alias;
- die Richtlinienfunktionen der Modulansicht und ihre Einschränkungen (eine Ansicht ohne Funktionen braucht das Modul);
- `[SiteAccessRules]` und `RequireUserLogin` des Siteaccess;
- die Siteaccess-Einschränkung von `user/login`;
- bei `content/view` und `content/edit` den Knoten oder das Objekt.

Das kommt zu den Einträgen `PolicyList_<name>[]` in `menu.ini` hinzu, die für das bleiben, was eine Adresse nicht
verrät. Ein linkes Menü, in dem kein Link übrig bleibt, wird als Ganzes ausgeblendet.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/menu.ini` | `MenuAccessSettings` | `CheckViewAccess` | `enabled` | Siteaccess oder Override |
| `settings/menu.ini` | `MenuAccessSettings` | `NoAccessLinks` | `hidden` (`disabled` zeigt den Namen ohne Link, wie die Releases 4.x) | Siteaccess oder Override |

Die mitgelieferten Werte anzeigen:

```bash
grep -n "CheckViewAccess\|NoAccessLinks" settings/menu.ini
```

## In einem eigenen Template verwenden

Die Fetch-Funktion `user/can_open` (definiert in `kernel/user/function_definition.php`) gibt dieselbe Antwort:

```
{if fetch( 'user', 'can_open', hash( 'uri', 'setup/cache' ) )}
    <a href={'setup/cache'|ezurl}>Caches</a>
{/if}
```

## Eine Installation prüfen

1. Melden Sie sich als Redakteur mit einer eingeschränkten Rolle an. Die Reiter Design, Newsletter und Export und die
   Einträge ohne Richtlinie sind verschwunden.
2. Melden Sie sich als Administrator an. Nichts fehlt.

Um einen Reiter auf einem Siteaccess für alle auszublenden, unabhängig von den Richtlinien, verwenden Sie
[HiddenTabs](ausgeblendete-admin-reiter.md).

## Verwandte Seiten

- [Admin-Reiter ausblenden: HiddenTabs](ausgeblendete-admin-reiter.md)
- [Das Design admin4](../../../eng-us/features/6.0/admin4-design.md)
- [Einrichtungsassistent und Editor-Siteaccess](../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
