---
title: Content Security Policy für Autoren von Erweiterungen
audience: developer, app-creator, designer
category: extension-development
order: 320
summary: Was eine Content Security Policy für Templates und Skripte bedeutet - die Modi, warum Inline-Code scheitert, das Muster mit Data-Attributen, csp_nonce() und seine Grenzen, und wie Sie Verstöße finden.
keywords: Content Security Policy, Inline-Skript, Data-Attribute, Nonce, Verstöße, content security policy, CSP, script-src, inline script, onclick, javascript:, style attribute, nonce, csp_nonce, report-only, enforce, csp.log, data attributes, unsafe-inline
type: help
---
# Content Security Policy für Autoren von Erweiterungen

Teil 3, Kapitel 32. Eine Content Security Policy (CSP) sagt dem Browser, welche Skripte und Styles eine Seite ausführen
darf. Eine strenge Richtlinie verhindert, dass eingeschleuste Skripte ausgeführt werden, selbst wenn ein Template
vergessen hat, etwas zu maskieren. Exponential sendet eine solche Richtlinie aus `site.ini [ContentSecurityPolicy]`;
die vollständige Referenz ist
[Content Security Policy](../../../funktionen/6.0/content-security-policy.md). Dieses Kapitel enthält, was
ein Autor von Erweiterungen wissen muss.

## Die Richtlinie

| Einstellung | Bedeutung |
|---|---|
| `Mode` | `disabled`, `report-only` (der Browser meldet, blockiert aber nichts), `enforce` (der Browser blockiert), `auto` |
| `Directives[]` | die Richtlinie: `script-src 'self'`, `style-src 'self'`, `default-src 'self'` und die übrigen |
| `Nonce`, `NonceDirectives[]` | eine Nonce pro Anfrage für Skripte, die inline stehen müssen |
| `ReportURI` | wohin Berichte gehen |

`auto` bedeutet report-only auf Siteaccesses der Administration und ausgeschaltet überall sonst; Installationen können
die Richtlinie durchsetzen, und die Administration der Referenzinstallation läuft mit `Mode=enforce`. **Schreiben Sie
Ihre Erweiterung für eine durchgesetzte Richtlinie**: Dann funktioniert sie überall.

## Was unter `script-src 'self'` scheitert

| In einem Template | Warum es scheitert | Stattdessen |
|---|---|---|
| `<script>doSomething();</script>` | Inline-Skript | eine Datei in `design/<design>/javascript/`, geladen über `design.ini` oder `ezscript_require` |
| `<button onclick="...">` | Inline-Event-Handler | `data-*`-Attribut + `addEventListener` in Ihrer Datei |
| `<a href="javascript:...">` | `javascript:`-URL | eine Schaltfläche mit einem Data-Attribut |
| `setTimeout( "code", 100 )`, `eval()`, `new Function()` | Auswertung von Zeichenketten | echte Funktionen |
| `style="color:red"`, `<style>` | Inline-Style unter `style-src 'self'` | eine Klasse in Ihrem Stylesheet |

Diese Regeln gelten im eigenen Code der Administration: Ihre Templates enthalten kein `<style>`, kein `style=""`, keine
Inline-Skripte, und ein Test lässt den Build fehlschlagen, wenn eines auftaucht
([Admin-Seiten im admin4-Stil](admin-seiten-im-admin4-stil.md)).

## Das Muster mit Data-Attributen

Template:

```text
<button type="button" class="a4-btn" data-mysite-greet="{$name|wash}">{'Greet'|i18n( 'extension/mysite_hello' )}</button>
<output data-mysite-greeting></output>
```

Skript (`design/standard/javascript/mysite_hello.js`):

```javascript
document.addEventListener( 'click', function ( e ) {
    var button = e.target.closest( '[data-mysite-greet]' );
    if ( !button )
        return;
    var out = document.querySelector( '[data-mysite-greeting]' );
    if ( out )
        out.textContent = 'Hello, ' + button.getAttribute( 'data-mysite-greet' ) + '!';
} );
```

Ein einziger Listener auf dem Dokument behandelt jede Schaltfläche, auch solche, die später hinzukommen. Daten
gelangen über maskierte Attribute vom Template zum Skript; das Skript schreibt mit `textContent`. Viele Erweiterungen
dieses Projekts wurden auf dieses Muster umgestellt; Teil 4 zeigt echte Beispiele vorher und nachher
([CSP: Event-Handler-Attribute zu Data-Attributen](../teil-4-aktualisieren/csp-event-handler-zu-data-attributen.md)).

## Die Nonce, für den seltenen Fall

Wenn ein Skript wirklich inline stehen muss, kann die Richtlinie eine Nonce tragen, und das Template gibt sie aus:

```text
<script nonce="{csp_nonce()}">...</script>
```

`csp_nonce()` gibt eine leere Zeichenkette zurück, wenn die Richtlinie keine Nonce hat. **Geben Sie sie nie innerhalb
eines `{cache-block}`, eines im View-Cache gespeicherten Templates oder eines statischen Caches aus**: Die
zwischengespeicherte Kopie würde die Nonce einer anderen Anfrage tragen und blockiert werden. Eine Datei ist fast
immer die bessere Lösung.

## Verstöße finden

- Die Entwicklerkonsole des Browsers nennt jedes blockierte Skript, jeden blockierten Handler und Style.
- Mit einer `ReportURI` werden Berichte gesammelt; auf der Referenzinstallation werden sie in `var/log/csp.log`
  geschrieben.
- Testen Sie die Seiten Ihrer Erweiterung vor dem Release in einem Siteaccess, dessen `Mode` auf `enforce` steht.

## Verwandte Seiten

- Zurück: [Sicherheit in Erweiterungen](sicherheit-in-erweiterungen.md)
- Weiter: [Admin-Seiten im admin4-Stil](admin-seiten-im-admin4-stil.md)
- [Content Security Policy](../../../funktionen/6.0/content-security-policy.md)
- [CSP: Inline-Skripte in Dateien verschieben](../teil-4-aktualisieren/csp-inline-skripte-auslagern.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
