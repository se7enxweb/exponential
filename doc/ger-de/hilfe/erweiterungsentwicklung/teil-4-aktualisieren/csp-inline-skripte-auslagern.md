---
title: "CSP: Inline-Skripte in Dateien verschieben"
audience: developer, designer
category: extension-development
order: 130
summary: Verschieben Sie die Inline-Skriptblöcke einer alten Erweiterung in JavaScript-Dateien, die mit ezscript geladen werden, übergeben Sie Template-Werte über data-Attribute und halten Sie die Seite unter einer Content Security Policy ohne unsafe-inline funktionsfähig.
keywords: Inline-Skript, data-Attribute, JavaScript-Datei, Template, Upgrade, CSP, Content Security Policy, inline script, ezscript, data attributes, script-src self, unsafe-inline, JavaScript file, template, upgrade
type: help
---
# CSP: Inline-Skripte in Dateien verschieben

Teil 4, Kapitel 13. Dies ist das erste von drei Kapiteln über die Content Security Policy (CSP). Es behandelt die
größte Aufgabe: `<script>`-Blöcke, die direkt in Templates geschrieben sind. Danach liegt der Code der Seite in
Dateien, und die Seite funktioniert unter der Policy `script-src 'self'`, auf die die Administration von
Exponential 6 umgestellt wird.

## Warum das jetzt wichtig ist

Exponential 6 sendet eine Content Security Policy aus `site.ini [ContentSecurityPolicy]`. Mit `Mode=auto`, dem
Standard, ist sie in der Administration **nur meldend** (report-only): Der Browser meldet in seiner Konsole, was die
Policy blockieren würde, und blockiert nichts. Mit `Mode=enforce` blockiert der Browser Inline-Skripte, und die
Schaltflächen und Menüs einer alten Erweiterung werden gezeichnet, tun aber nichts, ohne einen Fehler auf der Seite.
Die Einzelheiten, auch wie Sie sie einschalten, stehen in
[Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md).

Die Administrations-Templates des Kernels enthalten keinen Inline-Code mehr. Was übrig ist, steckt in Erweiterungen,
und diese zu korrigieren, ist der Zweck dieses Kapitels. Teil 3 erklärt die Policy für neuen Code:
[Content Security Policy für Erweiterungen](../teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md).

## Sehen, was die Policy meldet

1. Öffnen Sie eine der Seiten Ihrer Erweiterung in der Administration.
2. Öffnen Sie die Entwicklerwerkzeuge des Browsers, Tab **Konsole**.
3. Laden Sie neu. Jede Zeile wie „Refused to execute inline script because it violates the following Content
   Security Policy directive“ (der Wortlaut unterscheidet sich je nach Browser) ist ein Inline-Skript oder ein
   Handler.

Die Suche aus der Bestandsaufnahme findet sie in den Dateien:

```bash
grep -rnE '<script[^>]*>[^<]|<script>\s*$|<script type="text/javascript">\s*$' --include='*.tpl' extension/myoldext/design/
```

## Das Muster, Schritt für Schritt

Hier ein echtes Beispiel: die Einrichtungsseite von se7enxweb/sevenx_authentication_2fa, Commit e4c0319. Die Seite
zeigt die Registrierung einer Authenticator-App nur, solange diese Methode gewählt ist.

### Vorher

```text
{if $show_enrol}
<script type="text/javascript">
{literal}
(function () {
    var form = document.getElementById( 'tfa-setup-form' ), enrol = document.getElementById( 'tfa-enrol' );
    if ( !form || !enrol ) return;
    function update() {
        var picked = form.querySelector( 'input[name="Method"]:checked' );
        enrol.hidden = !picked || picked.value !== 'totp';
    }
    form.addEventListener( 'change', function ( e ) { if ( e.target && e.target.name === 'Method' ) update(); } );
    update();
})();
{/literal}
</script>
{/if}
```

### Schritt 1: den Code in eine Datei verschieben

Legen Sie in der Erweiterung `design/standard/javascript/<extension>_<page>.js` an. Geben Sie ihr einen Namen, der
nicht mit der Datei einer anderen Erweiterung kollidieren kann, denn alle Designs teilen sich eine Suche in
`javascript/`. Die Datei aus dem Commit:

```javascript
(function () {
    function start() {
        var form = document.getElementById( 'tfa-setup-form' ), enrol = document.getElementById( 'tfa-enrol' );
        if ( !form || !enrol || form.getAttribute( 'data-tfa-ready' ) ) return;
        form.setAttribute( 'data-tfa-ready', '1' );
        function update() {
            var picked = form.querySelector( 'input[name="Method"]:checked' );
            enrol.hidden = !picked || picked.value !== 'totp';
        }
        form.addEventListener( 'change', function ( e ) { if ( e.target && e.target.name === 'Method' ) update(); } );
        update();
    }
    if ( document.readyState === 'loading' ) { document.addEventListener( 'DOMContentLoaded', start ); } else { start(); }
})();
```

Zwei Dinge sind hinzugekommen, und Sie brauchen beide in fast jedem umgewandelten Skript:

- **Auf die Seite warten.** Das Inline-Skript lief genau dort, wo es stand, nach dem Formular. Eine Datei kann früher
  oder später laufen, daher wartet sie auf `DOMContentLoaded`, wenn die Seite noch lädt.
- **Nur einmal einrichten.** Das Template kann zweimal ausgegeben und die Datei zweimal geladen werden. Eine Markierung
  (`data-tfa-ready`) verhindert den zweiten Durchlauf.

### Schritt 2: die Datei dort ausgeben, wo das Skript stand

```text
{if $show_enrol}
{* the enrolment is shown while "totp" is picked: sevenx_authentication_2fa_setup.js, no inline script *}
{ezscript( array( 'sevenx_authentication_2fa_setup.js' ) )}
{/if}
```

`ezscript` ist die Template-Funktion von ezjscore, die ein `<script src=...>`-Tag für eine Datei der Design-Kette
ausgibt. Verwenden Sie im Seiteninhalt `ezscript`, nicht `ezscript_load`: Die Hinweise des Kernels erklären, dass
`ezscript_load` im Inhalt einer Seite an dieser Stelle jede bis dahin gesammelte Datei ausgibt und ihre Reihenfolge
ändern kann.

### Schritt 3: Werte aus dem Template werden zu data-Attributen

Inline-Skripte enthalten oft Werte aus dem Template:

```text
<script type="text/javascript">
var myOldExtUrl = {'/myoldext/save'|ezurl};
var myOldExtConfirm = '{'Really delete?'|i18n( 'extension/myoldext' )}';
</script>
```

Setzen Sie sie auf das Element, mit dem das Skript arbeitet, für HTML gewaschen:

```text
<form id="myoldext-form" method="post" action={'/myoldext/save'|ezurl}
      data-myoldext-confirm="{'Really delete?'|i18n( 'extension/myoldext' )|wash}">
```

und lesen Sie sie in der Datei:

```javascript
var form = document.getElementById( 'myoldext-form' );
var question = form.getAttribute( 'data-myoldext-confirm' );
```

Für strukturierte Werte JSON in einem Attribut:

```text
<div id="myoldext-chart" data-myoldext-conf="{json_encode( hash( 'max', $max, 'labels', $labels ) )|wash}"></div>
```

```javascript
var conf = JSON.parse( document.getElementById( 'myoldext-chart' ).getAttribute( 'data-myoldext-conf' ) || '{}' );
```

Der Operator `wash` escapet den Wert für ein HTML-Attribut, sodass ein Anführungszeichen in einer Übersetzung das
Attribut nicht beenden kann. Die alte Inline-Form hatte dieses Problem ebenfalls: Eine Übersetzung mit einem
Apostroph hat das Skript zerbrochen.

### Schritt 4: ein Test, der dafür sorgt, dass es so bleibt

Der Commit hat einen Test hinzugefügt, der die Templates ohne den Kernel liest und bei jedem Inline-Skript
fehlschlägt:

```php
<?php
$root = dirname( __DIR__ );
$found = array();
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/design', FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $file )
{
    if ( substr( (string)$file, -4 ) !== '.tpl' )
    {
        continue;
    }
    $source = preg_replace( '/\{\*.*?\*\}/s', '', file_get_contents( (string)$file ) );
    if ( preg_match( '/<script\b(?![^>]*\bsrc\s*=)(?![^>]*type\s*=\s*["\']application\/(?:ld\+)?json["\'])[^>]*>/i', $source ) )
    {
        $found[] = substr( (string)$file, strlen( $root ) + 1 );
    }
}
echo $found ? 'FAIL: inline script in ' . implode( ', ', $found ) . "\n" : "PASS: no inline script\n";
exit( $found ? 1 : 0 );
```

Er erlaubt ein `<script src=...>` und einen JSON-Datenblock (`type="application/json"`) und ignoriert
Template-Kommentare. Mehr dazu in [Tests und CI für eine aktualisierte Erweiterung](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md).

## Sonderfälle

| Ein Inline-Skript, das ... | Tun Sie Folgendes |
|---|---|
| globale Konfiguration für eine Bibliothek setzt (`var MyLib_config = {...}`) | ein JSON-Datenblock `<script type="application/json" id="myoldext-conf">...</script>`, gelesen mit `JSON.parse( el.textContent )` |
| in einer Schleife einmal pro Eintrag ausgegeben wird | eine Datei und ein data-Attribut pro Eintrag |
| `document.write()` aufruft | bauen Sie die Elemente in der Datei mit DOM-Methoden |
| laufen muss, bevor die Seite gezeichnet wird (ein Theme- oder Farbschutz) | eine kleine Datei, im `<head>` als normales, blockierendes Skript geladen |
| aus dem Einbettungscode eines Drittanbieters stammt | laden Sie die Skriptdatei des Dienstes und fügen Sie seinen Host zu `script-src` hinzu, wie der Kernel es für Karten und reCAPTCHA dokumentiert |

## Was Sie nicht tun sollten

- Fügen Sie der Policy nicht `'unsafe-inline'` hinzu, damit die Erweiterung funktioniert. Das schaltet den Schutz für
  jede Seite des Siteaccess ab, nicht nur für Ihre.
- Verschieben Sie den Code nicht in eine Datei, um ihn dann weiter aus `onclick="..."` aufzurufen. Das Attribut ist
  ebenfalls Inline-Code; siehe [CSP: von Event-Handler-Attributen zu data-Attributen](csp-event-handler-zu-data-attributen.md).
- Hängen Sie keine `?v=`- oder anderen Versionsabfragen an Skript-URLs. Der Packer von ezjscore setzt die neueste
  Änderungszeit der gebündelten Dateien in den Namen des Bündels, sodass Browser eine geänderte Datei ohnehin holen
  (nachdem die Caches geleert wurden).

## Verwandte Seiten

- Zurück: [composer.json modernisieren](composer-json-modernisieren.md)
- Weiter: [CSP: von Event-Handler-Attributen zu data-Attributen](csp-event-handler-zu-data-attributen.md)
- [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)
- [Fallstudie: Zwei-Faktor-Authentifizierung ohne Inline-Skripte](fallstudie-zwei-faktor-authentifizierung.md)
- [CSS und JavaScript in einer Erweiterung](../teil-3-erweiterungsentwickler/css-und-javascript-in-einer-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
