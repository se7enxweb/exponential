# Anfrageregeln: entscheiden, was mit einer Anfrage geschieht, bevor ihre Ansicht läuft

Diese Seite ist für Administratoren, die steuern, welche Adressen Besucher verwenden dürfen, zum Beispiel um System-URLs
wie `/content/view/full/123` zu verbergen. Ein URL-Alias wird zu `content/view/full/<node>`, sobald der Kernel ihn
übersetzt hat, daher konnten weder `[SiteAccessRules]` noch Einschränkungen von `content/read` `/content/view/full/123`
von derselben Seite unter ihrem Alias unterscheiden. Anfrageregeln schließen diese Lücke. Sie laufen, nachdem der
URL-Alias übersetzt ist und nachdem die Rollenrichtlinien die Ansicht erlaubt haben, und können erlauben, verweigern,
weiterleiten, umschreiben oder protokollieren. Hinzugefügt am 02.10.2026.

Vollständige Anleitung mit jeder Bedingung, Aktion, jedem Fakt und sechzehn Rezepten:
[Sicherheit der Vollansicht](../../../eng-us/bc/6.0/view_full_security.md).

## Schnellstart: System-URLs auf ihren Alias schicken

1. Sehen Sie zuerst nach, wer betroffen wäre. Schalten Sie die Audit-Regel ein, die nur für jede System-URL, die ein
   Benutzer ohne `content/view_system_url` eingibt, eine Zeile in `var/log/requestrules.log` schreibt und nichts ändert.
2. Schalten Sie dann die Weiterleitung ein. In `settings/siteaccess/<site>/requestrules.ini.append.php` (oder
   `settings/override/`):

```ini
<?php /*
[RequestRuleSettings]
RuleList[]=system_url_full_view_to_alias
*/ ?>
```

3. Prüfen Sie für jede Adresse und als jeder Benutzer, was die Regeln entscheiden, ohne etwas zu ändern:

```bash
php bin/php/ezrequestrules.php -s site --check
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2
```

Ein Besucher, der `/content/view/full/123` eingibt, wird jetzt (301) auf den Alias des Knotens weitergeleitet, sofern er
nicht die neue Richtlinie `content/view_system_url` hat. Ohne Alias lautet die Antwort „nicht gefunden“.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `settings/requestrules.ini` | `RequestRuleSettings` | `Enabled` | `true` | Bei `false` wird keine Regel gefragt; der Kernel verhält sich wie bisher |
| `settings/requestrules.ini` | `RequestRuleSettings` | `RuleList[]` | leer | Regeln, die in dieser Reihenfolge gefragt werden; die erste, deren Bedingungen alle zutreffen, entscheidet |
| `settings/requestrules.ini` | `RequestRuleSettings` | `RuleProviders[]` | leer | Klassen, die `ezpRequestRuleProvider` implementieren (in PHP berechnete Regeln) |
| `settings/requestrules.ini` | `RequestRuleSettings` | `FactProviders[]` | leer | Klassen, die `ezpRequestFactProvider` implementieren, für `Conditions[fact:<name>]` |
| `settings/requestrules.ini` | `RequestRuleSettings` | `MaxRewrites` | `3` | Umschreibungen pro Anfrage vor „nicht gefunden“ (verhindert Schleifen) |
| `settings/requestrules.ini` | `RequestRuleSettings` | `ConditionHandlers[]`, `ActionHandlers[]` | leer | Bedingungen und Aktionen von Extensions nach Namen |
| `settings/requestrules.ini` | `Rule-<name>` | `Conditions[...]`, `Action`, `ActionArgs[...]`, `FallbackAction`, `Description` | pro Regel | Eine Regel |

Geltungsbereich: Override oder Siteaccess. Eine Siteaccess-Überschreibung, die mit einem leeren `RuleList[]` beginnt,
ersetzt die Liste.

Die mitgelieferten Standards prüfen (`Enabled=true`, ein leeres `RuleList[]`, `MaxRewrites=3`):

```bash
grep -n "RuleList\|Enabled\|MaxRewrites" settings/requestrules.ini
```

## Was eine Regel sagen kann

- **25 Bedingungen**: `requested_via` (System, Alias, Platzhalter, Startseite), `uri`, `module_view`, `view_mode`,
  `node`, `subtree`, `class`, `section`, `state`, `user`, `role`, `policy`, `siteaccess`, `host`, `header`, `ip`,
  `fact` und weitere.
- **8 Aktionen**: `allow`, `notfound`, `forbidden`, `login`, `redirect`, `redirect_to_alias`, `rewrite`, `log`.

Drei Regeln werden mitgeliefert, alle aus:

| Regel | Was sie tut |
|---|---|
| `system_url_full_view_to_alias` | Leitet eine eingegebene System-URL der Vollansicht auf den Alias weiter |
| `system_url_content_view_notfound` | Beantwortet jede System-Inhaltsansicht mit „nicht gefunden“ |
| `system_url_full_view_audit` | Schreibt für jede System-URL, die ein Benutzer ohne `content/view_system_url` eingibt, eine Zeile in `var/log/requestrules.log`; ändert nichts. Stellen Sie sie an den Anfang, um zu sehen, wer betroffen wäre, bevor Sie eine strengere Regel durchsetzen |

## Sicherheit

- Regeln nehmen nur Zugriff weg oder schicken den Besucher anderswohin; sie geben nie Zugriff, den eine Richtlinie
  verweigert hat.
- Jede Antwort außer `allow` wird als private, no-store gesendet. Eine Seite, über die eine Regel mit einer
  anfrageabhängigen Bedingung (Header, IP, Benutzer) entscheiden könnte, bleibt aus dem rollenbewussten HTTP-Cache und
  dem Antwort-Cache von Velocity heraus; Seiten, die die Regeln nicht beeinflussen können, werden wie bisher
  zwischengespeichert.
- Die Aktion `login` greift nie bei den Anmeldeansichten, sodass keine Regel eine Schleife erzeugen kann.
- Regeln lesen nur Fakten, die bei der ersten Verwendung aufgelöst werden, daher werden sie ohne Datenbank getestet:
  21 Unit-Tests in `tests/tests/kernel/classes/requestrules/ezpRequestRuleEngineTest.php`. Die 25 Bedingungen und
  8 Aktionen sind die Arrays `$builtInConditions` und `$builtInActions` von `ezpRequestRuleEngine`.

## Verwandte Seiten

- [Härtung](../../../eng-us/bc/6.0/hardening.md)
- [Das Prüfprotokoll](pruefprotokoll.md)
- [Objektzustände](../../anleitungen/objektzustaende.md#10-zustände-in-templates-fetches-und-request-regeln): die Zustände, auf die `Conditions[state]` passt
- [Admin-Links folgen den Berechtigungen](admin-links-folgen-den-berechtigungen.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
