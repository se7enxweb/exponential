---
title: Operatoren für Installation, Umgebung, Audit und CSP
audience: developer, designer, app-creator
category: development
order: 370
summary: Sechs Operatoren, die der Exponential-Kernel hinzugefügt hat - installation_name, is_production_system, exp_environment, exp_environment_badge, audit_label und csp_nonce - mit den Einstellungen, die sie lesen, ihren Rückgabewerten, der Caching-Regel für csp_nonce und Beispielen, die auf alpha gerendert wurden.
keywords: installation_name, is_production_system, exp_environment, exp_environment_badge, audit_label, csp_nonce, EXP_ENV, EzInstallationName, ProductionInstallationList, AdminEnvironment, Content Security Policy, Nonce, Audit, Installationsname, Produktivsystem, Umgebung, Umgebungsabzeichen, environment, badge
type: help
---
# Operatoren für Installation, Umgebung, Audit und CSP

Diese Operatoren gehören zum aktuellen Exponential-Kernel und werden in
`kernel/common/eztemplateautoload.php` registriert. Sie benötigen keine Erweiterung.

| Operator | Klasse und Datei |
|---|---|
| `installation_name`, `is_production_system` | `ExpInstallationOperator`, `kernel/classes/expinstallationoperator.php` |
| `exp_environment`, `exp_environment_badge` | `expEnvironmentOperator`, `kernel/common/expenvironmentoperator.php` |
| `audit_label` | `expAuditTemplateOperator`, `kernel/classes/audit/console/expaudittemplateoperator.php` |
| `csp_nonce` | `expContentSecurityPolicyOperator`, `kernel/common/expcontentsecuritypolicyoperator.php` |

## Inhalt

1. [Die Operatoren](#die-operatoren): [installation_name](#installation_name),
   [is_production_system](#is_production_system), [exp_environment](#exp_environment),
   [exp_environment_badge](#exp_environment_badge), [audit_label](#audit_label), [csp_nonce](#csp_nonce)
2. [Beispiele](#beispiele)

## Die Operatoren

### installation_name

Generierte Seite: [installation_name](../../beispiele/template-operatoren/installation_name.md).

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `hide_on_prod` | boolean | nein | `false` |

- **Eingabe:** keine.
- **Rückgabe:** `site.ini` `[SiteSettings] EzInstallationName`, ohne umgebende Leerzeichen (eine leere Zeichenkette,
  wenn der Wert nicht gesetzt ist); mit `hide_on_prod` eine leere Zeichenkette auf einem Produktivsystem.

Der Wert wird einmal pro Anfrage gelesen (`ExpInstallationDetailsOutputFilter::installationName()`,
`kernel/classes/expinstallationdetailsoutputfilter.php`). Nutzen Sie ihn, um Testkopien einer Website zu kennzeichnen.

### is_production_system

Generierte Seite: [is_production_system](../../beispiele/template-operatoren/is_production_system.md).

- **Eingabe:** keine. **Parameter:** keine.
- **Rückgabe:** `true`, wenn der Installationsname nicht leer ist und in `[SiteSettings]
  ProductionInstallationList[]` aufgeführt ist, sonst `false`.

### exp_environment

Generierte Seite: [exp_environment](../../beispiele/template-operatoren/exp_environment.md).

- **Eingabe:** keine. **Parameter:** keine.
- **Rückgabe:** der Name der Umgebung aus der Konstante `EXP_ENV` (`config.env.php`, gelesen von `eZINI::environment()`)
  oder eine leere Zeichenkette, wenn sie nicht gesetzt oder ungültig ist (sie muss `^[a-z][a-z0-9_-]{0,31}$`
  entsprechen). Die Umgebung entscheidet außerdem, welche zusätzlichen Einstellungsdateien `eZINI` liest.

```tpl
{if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
```

### exp_environment_badge

Generierte Seite: [exp_environment_badge](../../beispiele/template-operatoren/exp_environment_badge.md).

- **Eingabe:** keine. **Parameter:** keine.
- **Rückgabe:** das Umgebungsabzeichen der Kopfzeile der Administration als Hash: `level`, `name`, `short`, `release`,
  `display`, `title` und `key`.

Das Abzeichen kommt aus `site.ini` `[AdminEnvironment]` des aktuellen Siteaccess (`expEnvironmentOperator::badge()`):

- `Level`: `development`, `test`, `staging` oder `production`; `auto` leitet es aus `EXP_ENV` ab (`dev`/`development`,
  `test`, `stage`/`staging`, `prod`/`production`/`live`). Leer, unbekannt oder `auto` ohne passende Umgebung:
  kein Abzeichen (`level` ist eine leere Zeichenkette, und alle anderen Felder bleiben leer).
- `Name`, `ShortName`: die Texte; leer übernimmt den übersetzten Namen und das Kürzel der Stufe (DEV, TEST, STAGE, PROD).
- `Release` und `ShowVersion=enabled`: Text und die Exponential-Version nach dem Namen, verbunden durch ` · `.
- `BadgeDisplay=always` zeigt es dauerhaft (`display` ist `always`), jeder andere Wert nur beim Überfahren mit der Maus
  oder bei Fokus (`hover`); `ShowInTitle` mit einem anderen Wert als `disabled` setzt `title`.
- `key` verbindet alle diese Werte für die Schlüssel von cache-blocks, die das Abzeichen zeichnen.

### audit_label

Generierte Seite: [audit_label](../../beispiele/template-operatoren/audit_label.md).

- **Eingabe:** der Name eines Audit-Ereignisses wie `content.node.move`. **Parameter:** keine.
- **Rückgabe:** die lesbare Bezeichnung aus der Audit-Taxonomie (`expAuditConsole::label()`), übersetzt im Kontext
  `kernel/audit`; eine leere Zeichenkette bei leerer oder nicht skalarer Eingabe.

Einen Namen, den die Taxonomie nicht kennt, macht der Operator zu einer Bezeichnung, indem er den ersten Teil weglässt
und Punkte und Unterstriche durch Leerzeichen ersetzt (`expAuditTaxonomy::label()`); dieser wird nicht übersetzt:
`no.such.event` ergibt `Such event`. Die Bezeichnung wird nicht maskiert: `{$e.name|audit_label|wash}`.

### csp_nonce

Generierte Seite: [csp_nonce](../../beispiele/template-operatoren/csp_nonce.md).

- **Eingabe:** keine. **Parameter:** keine.
- **Rückgabe:** die Nonce der Content Security Policy dieser Anfrage (`expContentSecurityPolicy::templateNonce()`,
  `kernel/private/classes/expcontentsecuritypolicy.php`) oder eine leere Zeichenkette, wenn die Policy des Siteaccess
  deaktiviert ist oder keine Nonce enthält (`nonce=""` schadet nicht).

```tpl
<script nonce="{csp_nonce()}">...</script>
```

Die Nonce ist bei jedem Aufruf innerhalb einer Anfrage gleich und bei jeder Anfrage neu. **Geben Sie sie nie innerhalb
eines `cache-block` oder eines im View-Cache gespeicherten Templates aus**: Die zwischengespeicherte Kopie trüge die
Nonce einer anderen Anfrage, und der Browser würde das Skript blockieren. Noch besser verschieben Sie das Skript in eine
Datei, die über die Skriptliste des Designs geladen wird; sie braucht keine Nonce.

## Beispiele

Auf dem Siteaccess `site`, der keinen Installationsnamen, kein `EXP_ENV` und keine Nonce in seiner Policy hat:

```tpl
1 installation_name: [{installation_name()}]
2 hide_on_prod: [{installation_name( true() )}]
3 is_production_system: {is_production_system()|choose( 'no', 'yes' )}
4 environment: [{exp_environment()}]
5 csp_nonce on site: length {csp_nonce()|count_chars}
6 audit_label: {'content.node.move'|audit_label} / {'no.such.event'|audit_label}
```

Ausgabe auf alpha:

```text
1 installation_name: []
2 hide_on_prod: []
3 is_production_system: no
4 environment: []
5 csp_nonce on site: length 0
6 audit_label: Node move / Such event
```

Auf dem Siteaccess `admin`, dessen Policy eine Nonce enthält (auf alpha ist keine Abzeichenstufe gesetzt):

```tpl
{def $badge = exp_environment_badge()}
level=[{$badge.level}] name=[{$badge.name}] short=[{$badge.short}] display={$badge.display} title={$badge.title|choose( 'no', 'yes' )}
csp_nonce on admin: length {csp_nonce()|count_chars}, same on the second call: {eq( csp_nonce(), csp_nonce() )|choose( 'no', 'yes' )}
```

Ausgabe auf alpha:

```text
level=[] name=[] short=[] display= title=no
csp_nonce on admin: length 22, same on the second call: yes
```

## Verwandte Seiten

- [Content Security Policy für Anwendungen](../ersteller/anwendungsentwickler/content-security-policy.md)
- [Umgebungseinstellungen und Geheimnisse](../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
