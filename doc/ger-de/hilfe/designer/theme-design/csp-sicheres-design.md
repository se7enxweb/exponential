---
title: CSP-sicheres Design
audience: designer, developer
category: theme-design
order: 600
summary: So gestalten, dass sich eine strenge Content Security Policy durchsetzen lässt: was die Richtlinie standardmäßig erlaubt, die drei Regeln (kein Inline-Skript, kein Inline-Stil, keine fremden Ursprünge), die Muster, die Inline-Code ersetzen, die Nonce für die seltene Ausnahme und wie Sie ein Design prüfen.
keywords: Content Security Policy, CSP, CSP-sicher, script-src self, unsafe-inline, Nonce, csp_nonce, Inline-Skript, Inline-Stil, report-only, enforce, csp.log, ContentSecurityPolicy, content security policy, csp safe, inline script, inline style
featured: true
type: help
---
# CSP-sicheres Design

Diese Seite ist für Designer, deren Templates unter einer Content Security Policy (CSP) funktionieren müssen. Eine strenge Richtlinie
verhindert, dass ein eingeschleustes Skript läuft, selbst wo ein Template vergisst, einen Wert zu maskieren. Die Administration
sendet eine; öffentliche Websites können eine einschalten. Die Einstellungen sind in
[Content Security Policy](../../../funktionen/6.0/content-security-policy.md) beschrieben.

## Inhalt

1. [Die Richtlinie in Kürze](#die-richtlinie-in-kürze)
2. [Die drei Regeln für Designs](#die-drei-regeln-für-designs)
3. [Ersatzmuster](#ersatzmuster)
4. [Die Nonce: die seltene Ausnahme](#die-nonce-die-seltene-ausnahme)
5. [Ein Design prüfen](#ein-design-prüfen)

## Die Richtlinie in Kürze

`site.ini [ContentSecurityPolicy]` mit `Mode=auto` (Standard) sendet auf Siteaccesses der Administration eine Richtlinie im Modus
report-only und auf öffentlichen Websites keine; `report-only` und `enforce` lassen sich pro Siteaccess setzen. Die
Standarddirektiven erlauben nur den eigenen Ursprung der Website:

```ini
Directives[script-src]='self'
Directives[style-src]='self'
Directives[img-src]='self' data: blob:
Directives[font-src]='self' data:
Directives[connect-src]='self'
```

Also: Skripte und Stile nur aus Dateien auf demselben Host; kein Inline-`<script>`, kein `onclick=""`, keine `<style>`-Blöcke oder
`style=""`-Attribute, sobald die Richtlinie durchgesetzt wird.

## Die drei Regeln für Designs

1. **Kein Inline-JavaScript**: kein `<script>` mit Code, keine `on*=`-Attribute, keine `javascript:`-Links
   ([Kein Inline-JavaScript](kein-inline-javascript.md)).
2. **Kein Inline-CSS**: keine `<style>`-Blöcke, keine `style=""`-Attribute ([Kein Inline-CSS](kein-inline-css.md)).
3. **Keine fremden Ursprünge, die Sie nicht eingeplant haben**: Schriften, Skripte, Stile, Frames von anderen Hosts brauchen jeweils
   eine Änderung der Direktiven ([Externe Ressourcen und CDNs](externe-ressourcen-und-cdns.md)).

## Ersatzmuster

| Statt | Verwenden Sie |
|---|---|
| `<script>var cfg = {...}</script>` | `<script type="application/json" id="cfg">{json_encode(...)}</script>`, gelesen von Ihrer Skriptdatei |
| `onclick="doThing()"` | `data-action="thing"` und `addEventListener` in einer Skriptdatei |
| `<a href="javascript:...">` | einen `<button type="button">` mit einem Listener |
| `style="width: 40%"` | eine Klasse (`.pct-40`) oder einen `data-`-Wert, den ein Skript in eine Custom Property umwandelt |
| `<style>` mit Regeln pro Seite | ein Stylesheet, das das View-Template anfordert (`ezcss_require`) |
| ein Skript im Kopf, das vor dem Zeichnen laufen muss | eine kleine Datei, die mit `ezscript` im Kopf geladen wird ([Der Theme-Umschalter und der Schutz vor dem ersten Zeichnen](theme-umschalter-und-pre-paint-guard.md)) |

## Die Nonce: die seltene Ausnahme

Mit `[ContentSecurityPolicy] Nonce=enabled` (Standard) trägt die Richtlinie pro Anfrage eine frische Nonce für die Direktiven in
`NonceDirectives[]` (`script-src`). Der Template-Operator `csp_nonce()` gibt sie aus:

```tpl
<script nonce="{csp_nonce()}">/* code a template truly cannot move into a file */</script>
```

Geben Sie `csp_nonce()` nie innerhalb eines `{cache-block}` oder eines im View-Cache gespeicherten Templates aus: Die
zwischengespeicherte Kopie trüge die Nonce einer anderen Anfrage, und das Skript würde blockiert (Kommentare in `settings/site.ini`
und `kernel/common/expcontentsecuritypolicyoperator.php`). Deshalb sind Dateien immer die bessere Wahl.

## Ein Design prüfen

1. Lassen Sie die Website eine Weile mit `Mode=report-only` laufen und lesen Sie die Meldungen: Die Konsole des Browsers zeigt sie,
   und mit `ReportEndpoint=enabled` schreibt der Kernel sie in `var/log/csp.log`, das unter Setup > Logdateien aufgeführt ist.
2. Für Administrationsdesigns und Erweiterungen finden die Designtests Inline-Code in Templates:

   ```bash
   php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
   php vendor/bin/phpunit tests/tests/design/ExtensionInlineScriptsTest.php
   ```

3. Durchsuchen Sie Ihr eigenes Design:

   ```bash
   grep -rn -E "<script>|<script [^>]*>[^<]|on(click|load|change|submit)=|javascript:|<style|style=\"" extension/mytheme/design
   ```

4. Wenn für die Seiten, die Sie verwenden, nichts gemeldet wird, stellen Sie den Siteaccess auf `Mode=enforce` um.

## Verwandte Seiten

- [Kein Inline-CSS](kein-inline-css.md)
- [Kein Inline-JavaScript](kein-inline-javascript.md)
- [Checkliste: CSP](checkliste-csp.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
