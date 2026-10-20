---
title: Admin-Seiten im admin4-Stil
audience: developer, app-creator, designer
category: extension-development
order: 330
summary: Admin-Seiten einer Erweiterung, die wie der Rest der Administration aussehen - gemeinsame .a4-Komponenten, --a4-Tokens mit dunklen Werten, ein eigenes Stylesheet, kein Inline-CSS, Menülinks, Prüfung im hellen und dunklen Modus.
keywords: admin4, Design der Administration, Komponenten, Tokens, Dunkelmodus, Stylesheet, linkes Menü, Barrierefreiheit, administration design, components.css, a4-card, a4-btn, a4-table, a4-toolbar, tokens, --a4, dark mode, BackendCSSFileList, admin stylesheet, menu.ini, left menu, accessibility
type: help
---
# Admin-Seiten im admin4-Stil

Teil 3, Kapitel 33. Die meisten Erweiterungen fügen der Administration eine Seite hinzu. Sie sollte wie der Rest der
Administration aussehen, im Dunkelmodus und bei starker Vergrößerung funktionieren und der Administration kein
zusätzliches CSS bescheren, das mit der Zeit auseinanderläuft. Die Regeln und die vollständige API der Komponenten und
Tokens stehen in [Die Administration gestalten](../../../../eng-us/bc/6.0/admin4-styling.md); dieses Kapitel ist die
Zusammenfassung für Autoren von Erweiterungen.

## Die Regeln

1. **Kein `<style>` und kein `style=""` in Templates.** CSS gehört in das Verzeichnis `stylesheets/` Ihres Designs
   und wird über `design.ini` geladen.
2. **Verwenden Sie zuerst die gemeinsamen Komponenten**: `.a4-page`, `.a4-card`, `.a4-table`, `.a4-btn`, `.a4-toolbar`,
   `.a4-filterbar`, `.a4-badge`, `.a4-notice`, `.a4-empty` und die anderen aus `stylesheets/components.css`.
3. **Farben, Radius, Abstände und Schriften nur aus `--a4-*`-Tokens**: Sie wechseln für den Dunkelmodus von selbst.
4. **Ihre eigenen Klassen bekommen Ihr eigenes Präfix** und folgen denselben Regeln; Ihr Stylesheet enthält nur, was
   die Komponenten nicht können.
5. **Prüfen Sie hell und dunkel**, bei 1440 px und bei 960 px mit 200 % Zoom.

## Ihr Stylesheet

`design/admin/stylesheets/mysite_hello.css` (oder `design/admin4/...`, wenn es die Seite nur in admin4 gibt),
registriert so, wie es die Erweiterung `expqueue` tut:

```ini
[StylesheetSettings]
BackendCSSFileList[]=mysite_hello.css
```

Erweiterungen ergänzen `BackendCSSFileList[]`, niemals `AdminComponentCSSFileList[]` (diese Liste gehört der
Administration selbst). Das Stylesheet von `expqueue` beginnt mit einem Kommentar, der sich zum Übernehmen lohnt: Die
Seiten "are built from the administration's shared components ... and take every colour, radius, size and space from
the --a4-* tokens of components.css, which have a light and a dark value. This file holds only what the components
cannot do, as .a4-queue-* classes." (Sie sind aus den gemeinsamen Komponenten der Administration gebaut und nehmen jede
Farbe, jeden Radius, jede Größe und jeden Abstand aus den `--a4-*`-Tokens, die einen hellen und einen dunklen Wert
haben; die Datei enthält nur, was die Komponenten nicht können.)

```css
/* mysite_hello: the Hello admin page. Built from the shared components; only what they cannot do is here. */
.a4-hello-list {
    display: grid;
    gap: var(--a4-space-3);
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
}
.a4-hello-name {
    color: var(--a4-accent-text);
    font-size: var(--a4-text-l);
}
```

## Ein Seiten-Template

`design/admin4/templates/hello/admin.tpl`:

```text
<div class="a4-page">
    <div class="a4-pagehead">
        <div class="a4-pagehead-main">
            <h1 class="a4-pagehead-title">{'Greetings'|i18n( 'extension/mysite_hello' )}</h1>
        </div>
    </div>
    {if $greetings|count}
    <div class="a4-table-wrap">
        <table class="a4-table">
            <thead><tr><th scope="col">{'Name'|i18n( 'extension/mysite_hello' )}</th><th scope="col">{'When'|i18n( 'extension/mysite_hello' )}</th></tr></thead>
            <tbody>
            {foreach $greetings as $greeting}
                <tr><td>{$greeting.name|wash}</td><td>{$greeting.created|l10n( 'shortdatetime' )}</td></tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {else}
    <p class="a4-empty">{'No greetings yet.'|i18n( 'extension/mysite_hello' )}</p>
    {/if}
    <form method="post" action={'/hello/admin'|ezurl} class="a4-toolbar">
        <button type="submit" name="ClearButton" class="a4-btn a4-btn-danger">{'Clear all'|i18n( 'extension/mysite_hello' )}</button>
    </form>
</div>
```

Eine leere Liste sagt das in `.a4-empty`, Datentabellen verwenden `.a4-table` (von der Administration gestreift),
Formulare senden per POST, und das Formular-Token wird automatisch hinzugefügt.

## Die klassische Administration und andere Designs

Das klassische Design `admin` hat eine identische Kopie von `components.css`, daher funktionieren dieselben Klassen
auch dort; legen Sie das Template zusätzlich in `design/admin/templates/` ab, wenn Ihre Seite in beiden funktionieren
muss. Designs ohne die Komponentenklassen erhalten trotzdem lesbares HTML.

## Das linke Menü

Fügen Sie einem Menü der Administration in der Datei `settings/menu.ini.append.php` Ihrer Erweiterung einen Link
hinzu. `expqueue` fügt seine "Job queue" so dem Setup-Menü hinzu, zusammen mit der Richtlinie, die ein Benutzer
braucht, um den Link zu sehen:

```ini
[Leftmenu_setup]
Links[expqueue]=expqueue/dashboard
LinkNames[expqueue]=Job queue
PolicyList_expqueue[]=expqueue/view
```

In einem Menü, das in Gruppen unterteilt ist, wird ein Link mit `GroupLinks_<group>[]` platziert; ein Link ohne
Gruppe erscheint unter "Mehr".

## Barrierefreiheit

- Ein `h1` (der Seitentitel), danach `h2`/`h3` in der richtigen Reihenfolge.
- Jedes Formularfeld hat eine Beschriftung; jede Schaltfläche, die nur ein Symbol zeigt, hat Text für Screenreader
  (`.a4-sr-only`) und einen `title`.
- Meldungen, die ein Ergebnis mitteilen, haben `role="status"` (oder `role="alert"` für Fehler).
- Textkontrast mindestens 4,5:1, den Ihnen die Tokens liefern, wenn Sie sie verwenden.

## Prüfen

Öffnen Sie die Seite im hellen und im dunklen Modus, bei 1440 px und bei 960 px mit dem Browser auf 200 %: keine weiße
Karte auf der dunklen Seite, keine horizontale Bildlaufleiste, keine Fehler in der Konsole (auch keine Berichte der
Content Security Policy).

## Verwandte Seiten

- Zurück: [Content Security Policy für Autoren von Erweiterungen](content-security-policy-fuer-erweiterungen.md)
- Weiter: [Tests für Erweiterungen](../../../../eng-us/help/extension-development/part-3-extension-developer/tests-for-extensions.md)
- [Die Administration gestalten](../../../../eng-us/bc/6.0/admin4-styling.md)
- [admin4-Komponenten und -Tokens](../teil-8-referenz/admin4-komponenten-und-tokens.md)
- [admin4-Stil für Seiten von Erweiterungen](../../../../eng-us/help/extension-development/part-4-upgrading/admin4-styling-for-extension-pages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
