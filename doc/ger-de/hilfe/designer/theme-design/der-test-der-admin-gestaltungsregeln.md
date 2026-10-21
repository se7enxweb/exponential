---
title: Der Test der Admin-Gestaltungsregeln
audience: designer, developer
category: theme-design
order: 570
summary: Was tests/tests/design/AdminStylingRulesTest.php in jedem Administrationsdesign prüft (kein Inline-CSS, nur Token, dunkle Werte, Fokusrahmen, Stapelebenen, identische Kopien der Komponenten), wie Sie ihn ausführen und wie Sie beheben, was er meldet.
keywords: AdminStylingRulesTest, Gestaltungstest, Designtest, PHPUnit-Suite design, Test auf Inline-CSS, Token-Test, Test der dunklen Werte, CSS-Regeln der Administration, Designprüfung in der CI, styling test, design test, phpunit design suite, inline css test, token test, dark value test, admin css rules, CI design check
type: help
---
# Der Test der Admin-Gestaltungsregeln

Diese Seite ist für Designer und Entwickler, die ein Template oder Stylesheet der Administration ändern. Die Regeln der
Admin-Designs sind nicht nur aufgeschrieben; ein Test setzt sie in der kontinuierlichen Integration durch. Die Seite beantwortet:
was er prüft, wie Sie ihn ausführen und was zu tun ist, wenn er fehlschlägt.

## Inhalt

1. [Was er prüft](#was-er-prüft)
2. [Ihn ausführen](#ihn-ausführen)
3. [Einen Fehlschlag beheben](#einen-fehlschlag-beheben)
4. [Verwandte Tests](#verwandte-tests)

## Was er prüft

`tests/tests/design/AdminStylingRulesTest.php` (in der PHPUnit-Testsuite `design`, ohne Datenbank) durchsucht die Templates und
Stylesheets der Designs `admin4`, `admin4l`, `admin3` und `admin`. Unter seinen Prüfungen:

| Test | Schlägt fehl, wenn |
|---|---|
| `testAdminTemplatesCarryNoInlineCss` | ein Template einen `<style>`-Block oder ein `style=""`-Attribut hat (außerhalb der dokumentierten Ausnahmen) |
| `testAllowlistOnlyShrinks` | eine Datei auf der Migrations-Positivliste nicht mehr dort stehen muss (die Liste ist heute leer) |
| `testModuleStylesheetsUseOnlyTokens` | eine Datei in `stylesheets/modules/` eine fest eingetragene Farbe oder einen Radius hat |
| `testEveryColourTokenHasADarkValue` | ein Farbtoken keinen Wert unter `html[data-a4-theme="dark"]` hat |
| `testBaseStylesheetsUseOnlyColourTokens`, `testPageStylesheetsUseOnlyColourTokens` | ein Basis- oder Seiten-Stylesheet einen Farbwert direkt verwendet |
| `testNoFocusRuleRemovesTheOutline` | eine Regel den Fokusrahmen ohne Ersatz entfernt |
| `testStackingComesFromTheLayerTokens` | ein `z-index` eine Zahl statt eines Tokens `--a4-z-*` ist oder sich die Reihenfolge der Ebenen geändert hat |
| `testDataTablesShareOneZebra`, `testEmptyListsUseTheEmptyState` | Tabellen oder leere Listen vom gemeinsamen Aussehen abweichen |
| `testComponentsAreTheSameInAdminAndAdmin4` | die beiden Kopien von `components.css` sich unterscheiden |
| `testEveryListedStylesheetExists` | ein in den Einstellungen genanntes Stylesheet fehlt |

Die einzige Ausnahme heute ist `design/admin/templates/pagelayout.tpl` (die Spaltenbreiten des Editors, bevor die Seite
gezeichnet wird), im Test mit ihrer Begründung aufgeführt.

## Ihn ausführen

```bash
php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php
# the whole design suite
php vendor/bin/phpunit --testsuite design
```

## Einen Fehlschlag beheben

Beheben Sie das Template oder das Stylesheet, nie den Test:

- **Inline-CSS**: Verschieben Sie die Regel in ein Stylesheet ([Kein Inline-CSS](../../../../eng-us/help/designers/theme-design/no-inline-css.md)); ein Wert pro Element wird
  zu einer Klasse (für Prozentwerte `.a4-pct-0` bis `.a4-pct-100`) oder zu einem `data-*`-Attribut.
- **Ein direkter Farbwert**: Verwenden Sie das Token mit dieser Rolle ([admin4-Token](admin4-tokens.md)).
- **Kein dunkler Wert**: Fügen Sie ihn dem dunklen Block in beiden Kopien von `components.css` hinzu.
- **Eine Zahl als z-index**: Verwenden Sie das Ebenen-Token für diesen Zweck.

Den Test abzuschwächen oder einen Eintrag in die Positivliste aufzunehmen, damit eine Änderung durchgeht, wird nicht akzeptiert
(Regel des Projekts).

## Verwandte Tests

Die Design-Suite hat weitere, zum Beispiel `AdminContentSecurityPolicyTest` (Inline-Skripte und -Handler; siehe
[CSP-sicheres Design](csp-sicheres-design.md)), `AdminTokenContrastTest` (Textkontrast in Zeilen), `AdminResponsiveTest` und
`AdminCardsTest`.

## Verwandte Seiten

- [Admin-Designs im Überblick](admin-designs-ueberblick.md)
- [admin4-Modul-Stylesheets](admin4-modul-stylesheets.md)
- [Checkliste: Code-Review für Templates](checkliste-code-review-fuer-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
