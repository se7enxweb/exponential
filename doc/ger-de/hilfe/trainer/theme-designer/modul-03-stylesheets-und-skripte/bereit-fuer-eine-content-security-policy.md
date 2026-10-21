---
title: "Lektion 3.4: Ein Theme, bereit für eine Content Security Policy"
audience: trainer, designer, developer
category: training
order: 34
summary: Die Teilnehmenden erklären, was die mitgelieferte Content Security Policy erlaubt, betreiben ihren Siteaccess im Nur-Bericht-Modus, lesen einen durch einen Inline-Stil verursachten Verstoß und ersetzen Inline-Code durch Klassen, Data-Attribute und Skriptdateien. 45 Minuten.
keywords: theme-design, Content Security Policy, CSP, report-only, nur-bericht-modus, enforce, auto, script-src, style-src, inline-skript, inline-stil, onclick, data-attribut, AdditionalSources, ContentSecurityPolicy Mode, theme design, inline script, inline style, data attribute
type: help
---
# Lektion 3.4: Ein Theme, bereit für eine Content Security Policy

Eine Content Security Policy teilt dem Browser mit, welche Skripte und Stile eine Seite verwenden darf. Die Policy, die
Exponential mitliefert, erlaubt nur Dateien der Website selbst: kein `<script>` mit Code, kein `onclick=`, kein
`<style>`-Block, kein `style=""`. Ein Theme, das von Anfang an so geschrieben ist, lässt sich jederzeit unter die
Policy stellen; ein Theme voller Inline-Code nicht. In dieser Lektion schreiben die Teilnehmenden die erste Art.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 3: Stylesheets und Skripte](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 3.1](dateien-in-design-ini-auflisten.md); ein Browser mit Entwicklerwerkzeugen |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die vier Werte von `[ContentSecurityPolicy] Mode` erklären und sagen, was `auto` tut;
- nennen, was `script-src 'self'` und `style-src 'self'` in einem Template verbieten;
- ihren Siteaccess im Nur-Bericht-Modus betreiben und einen Verstoß in der Browserkonsole lesen;
- Inline-Stile und Inline-Skripte durch Klassen, `data-*`-Attribute und Skriptdateien ersetzen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md) und
[Stylesheets und Skripte für Ihre Website, CSP](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md#scripts-that-work-under-a-content-security-policy).

1. **Die Modi** (4 Minuten). `site.ini [ContentSecurityPolicy] Mode`:

   | Wert | Wirkung |
   |---|---|
   | `disabled` | keine Policy |
   | `report-only` | der Browser meldet, was die Policy blockieren würde, und blockiert nichts |
   | `enforce` | der Browser blockiert es |
   | `auto` (Standard) | Nur-Bericht-Modus in Siteaccesses der Administration, in allen anderen ausgeschaltet |

2. **Was die Policy sagt** (4 Minuten). `Directives[script-src]='self'` erlaubt nur Skriptdateien der Website: kein
   Inline-`<script>`, keine Attribute `on...=`, keine Links `javascript:`, kein `eval()`.
   `Directives[style-src]='self'` erlaubt nur Stylesheet-Dateien: keine `<style>`-Elemente und kein `style=""`.
   `element.style` aus einer Skriptdatei heraus zu setzen bleibt erlaubt.
3. **Die Ersetzungen** (5 Minuten):

   | Statt | Schreiben Sie |
   |---|---|
   | `<a onclick="openGallery()">` | `<a class="js-gallery">` und `addEventListener` in der Skriptdatei des Themes |
   | `<script>var root = "...";</script>` | ein `data-*`-Attribut, das die Skriptdatei liest, oder einen Datenblock `<script type="application/json">` |
   | `<div style="width: 40%">` | eine CSS-Klasse oder ein `data-*`-Attribut, das das Skript liest |
   | ein Skript von einem anderen Host | eine Kopie im Design oder `AdditionalSources[]=script-src https://<host>` |

4. **Der Weg zu enforce** (2 Minuten). Zuerst der Nur-Bericht-Modus, damit arbeiten, das Gemeldete beheben, dann
   `enforce`. Wer eine Live-Website umschaltet, entscheidet der Eigentümer der Website, nicht das Theme.

### Häufige Fragen

**Ist ein Block `<script type="application/json">` nicht Inline-Code?** Es sind Daten; der Browser führt sie nicht aus,
also blockiert die Policy sie nicht.

**Können Inhalte von Redakteuren die Policy verletzen?** Ja, ein `style=""` im Inhalt würde gemeldet. Das ist Inhalt,
nicht das Theme; beginnen Sie solche Websites im Nur-Bericht-Modus.

**Hält sich die Administration an diese Regeln?** Ja; admin4 läuft bis auf dokumentierte Ausnahmen ohne Inline-Code
(Modul 4).

### Stolperfallen

- **Die Konsole ist geschlossen.** Verstöße im Nur-Bericht-Modus erscheinen nur in der Entwicklerkonsole (oder unter
  `ReportURI`). Öffnen Sie sie, bevor Sie die Seite laden.
- **Den Nur-Bericht-Modus eingeschaltet lassen.** Er kostet sichtbar nichts, aber beenden Sie die Übung, indem Sie die
  Einstellung entfernen, damit die Übungsumgebung wieder im Standardzustand ist.
- **Widgets von Drittanbietern.** Karten, Videoplayer und Tag-Manager brauchen meist Hosts in `AdditionalSources[]`;
  prüfen Sie das, bevor Sie einem Kunden eine erzwungene Policy versprechen.

## Praktische Übung

Auf Ihrer Schulungsinstallation, mit geöffneter Entwicklerkonsole des Browsers. `D` ist Ihr Design-Ordner.

1. Schalten Sie die Policy für Ihren Siteaccess auf den Nur-Bericht-Modus:

   ```bash
   php bin/php/console exp:ini set site.ini/ContentSecurityPolicy/Mode report-only siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=ini
   ```

2. Laden Sie eine Seite von `<site>` neu. Suchen Sie in der Netzwerkanalyse den Antwort-Header
   `Content-Security-Policy-Report-Only` der Seite und lesen Sie dessen `script-src` und `style-src`.
3. Lesen Sie die Konsole: Notieren Sie gemeldete Verstöße.
4. Fügen Sie in `D/templates/page_footer.tpl` dem Element `<footer>` das Attribut `style="color: red"` hinzu. Leeren
   Sie mit `--clear-tag=template` und laden Sie neu. Lesen Sie die neue Meldung in der Konsole.
5. Entfernen Sie das Attribut wieder. Fügen Sie stattdessen in `training.css` die Regel
   `.training_theme-footer { color: #555; }` hinzu (die Klasse, die der Assistent der Fußzeile gegeben hat). Führen
   Sie die Routine aus Lektion 3.3 aus und laden Sie neu: keine neue Meldung.
6. Übergeben Sie die Stammadresse der Website ohne Inline-Code an ein Skript: Fügen Sie in `D/templates/pagelayout.tpl`
   dem Element `<body>` das Attribut `data-root={'/'|ezurl}` hinzu.
7. Fügen Sie in `D/javascript/site.js` hinzu:

   ```js
   document.addEventListener( 'DOMContentLoaded', function () {
       var root = document.body.getAttribute( 'data-root' );
       document.querySelectorAll( '.training_theme-logo' ).forEach( function ( a ) {
           a.setAttribute( 'title', 'Home: ' + root );
       } );
   } );
   ```

8. Führen Sie die Routine aus Lektion 3.3 aus, laden Sie neu und fahren Sie mit der Maus über den Namen der Website in
   der Kopfzeile: Der Titel zeigt die Stammadresse.
9. Prüfen Sie die Konsole noch einmal: kein Verstoß durch Ihr Theme.
10. Entfernen Sie die Policy-Einstellung wieder:

    ```bash
    php bin/php/console exp:ini rem site.ini/ContentSecurityPolicy/Mode siteaccess:<site>
    php bin/php/ezcache.php --clear-tag=ini
    ```

## Erwartetes Ergebnis

- Schritt 2: Die Seite sendet den Header für den Nur-Bericht-Modus mit `script-src 'self'` und `style-src 'self'`.
- Schritt 4: Die Konsole meldet, dass ein Inline-Stil gegen `style-src` verstößt; die Seite zeigt trotzdem Rot, weil
  im Nur-Bericht-Modus nichts blockiert wird.
- Schritte 5 bis 9: Die Farbe der Fußzeile kommt aus dem Stylesheet, das Skript liest das Data-Attribut, und die
  Konsole zeigt keinen Verstoß durch das Theme.
- Schritt 10: Die Seite sendet den Header für den Nur-Bericht-Modus nicht mehr.
- Der Trainer prüft nach Schritt 9 die Konsole einer Person am Projektor.

## Quiz

1. Was tut `Mode=auto` in einem öffentlichen Siteaccess?
2. Was davon blockiert `script-src 'self'`?
   A. `<script src="...">` von der Website B. ein Attribut `onclick=` C. eine Skriptdatei im Design D. `ezjsc::jquery`
3. Wie übergeben Sie ohne Inline-Code einen Wert aus einem Template an ein Skript?
4. Im Nur-Bericht-Modus verstößt ein Inline-Stil gegen die Policy. Zeigt die Seite ihn trotzdem an?
5. Ein Kunde möchte auf einer Website mit erzwungener Policy eine Karte von einem anderen Host. Welche Einstellung
   erlaubt das?

<details>
<summary>Antworten</summary>

1. Nichts: `auto` ist in öffentlichen Siteaccesses ausgeschaltet und in Administrationen im Nur-Bericht-Modus
   ([Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)).
2. **B.**
3. In einem `data-*`-Attribut (oder einem JSON-Datenblock), das eine Skriptdatei liest.
4. Ja; der Nur-Bericht-Modus meldet und blockiert nichts.
5. `[ContentSecurityPolicy] AdditionalSources[]`, zum Beispiel `script-src https://<host>`.

</details>

## Weiterführende Literatur

- [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md): Direktiven, Berichte und die
  Schritte zum Erzwingen.
- [Content Security Policy für Ihren Code](../../../../../eng-us/help/creators/app-creators/content-security-policy.md): die Sicht der
  Entwickler.
- [Inline-Skripte und Inline-Stile entfernen](../../../../../eng-us/help/designers/lifecycle/removing-inline-scripts-and-styles.md): ein
  älteres Theme aufräumen.
- [CSP-Prüfung bei jeder Veröffentlichung](../../../../../eng-us/help/designers/lifecycle/csp-audit-per-release.md): ein Theme auf Dauer
  sauber halten.

## Verwandte Seiten

- [Modul 3: Stylesheets und Skripte](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
