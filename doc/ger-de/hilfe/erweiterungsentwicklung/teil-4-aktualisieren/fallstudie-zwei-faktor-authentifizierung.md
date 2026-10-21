---
title: "Fallstudie: Zwei-Faktor-Authentifizierung ohne Inline-Skripte"
audience: developer, designer
category: extension-development
order: 250
summary: Wie sevenx_authentication_2fa in sechs kleinen Releases sein Inline-Skript, seinen style-Block und seine style-Attribute verloren hat, mit einem Test bei jedem Schritt, und warum seine Farben Fallbacks für die Tokens behalten.
keywords: Fallstudie, Zwei-Faktor-Authentifizierung, Inline-Skript, style-Block, style-Attribut, Tokens, dunkler Modus, Release, Upgrade, case study, sevenx_authentication_2fa, two-factor authentication, CSP, inline script, style block, style attribute, tokens, dark mode, release, upgrade
type: help
---
# Fallstudie: Zwei-Faktor-Authentifizierung ohne Inline-Skripte

Teil 4, Kapitel 25. se7enxweb/sevenx_authentication_2fa fügt der Anmeldung einen zweiten Schritt hinzu (unter anderem
eine Authenticator-App). Seine Seiten erscheinen in der Administration, auf öffentlichen Websites und auf
Anmeldeseiten, die fast nichts laden. Am 17. Oktober 2026 hat es den ganzen Browser-Teil eines Upgrades in sechs
kleinen Releases durchlaufen, 1.0.7 bis 1.0.12. Dieses Kapitel folgt diesen Commits, weil sie die CSP-Arbeit in der
richtigen Reihenfolge und mit einem Test bei jedem Schritt zeigen.

## Die Releases eines Tages

| Commit | Änderung | Release |
|---|---|---|
| e603c1c | das 2FA-Feld des Bearbeitungsformulars für Inhalte: fünf `style=""`-Attribute werden zu Klassen in einem neuen Administrations-Stylesheet, dazu ein Test | 1.0.8 (8803d7c) |
| e4c0319 | das Inline-Skript der Einrichtungsseite wird zu einer Datei | 1.0.9 (085ce86) |
| 599e793 | der `<style>`-Block der Seiten wird zu einem Stylesheet | |
| ea4947c | das Template verlinkt dieses Stylesheet dort, wo der Block stand | |
| 6a75f30 | Statusfarben aus den Admin-Tokens, mit Fallbacks | |
| 73bfdd2 | die letzten `style=""`-Attribute der Einrichtungsseite werden zu Klassen | 1.0.10 (9a5fd94) |
| 2031e45 | der Konto-Link und das öffentliche 2FA-Feld | 1.0.11 (771183d) |
| 0bee499 | die Seiten des Designs media | 1.0.12 (882295d) |

Jedes Release konnte für sich installiert werden und war besser als das vorherige.

## Schritt 1: das Inline-Skript

Die Einrichtungsseite zeigte die Registrierung einer Authenticator-App nur, solange diese Methode gewählt war, mit
einem Skriptblock im Template. Commit e4c0319 hat ihn nach
`design/standard/javascript/sevenx_authentication_2fa_setup.js` verschoben, ausgegeben mit `ezscript` dort, wo der
Block stand, sowohl im Design standard als auch im Design media. Vorher und nachher werden vollständig in
[CSP: Inline-Skripte in Dateien verschieben](csp-inline-skripte-auslagern.md) gezeigt.

Derselbe Commit hat `tests/sevenxauthentication2fainlinescripttest.php` hinzugefügt, das drei Dinge prüft: kein
Inline-Skript in irgendeinem Template, kein Event-Handler-Attribut oder `javascript:`-Link und dass beide
Einrichtungsseiten die neue Datei ausgeben. Die dritte Prüfung ist die interessante: Sie stellt sicher, dass die
Korrektur angebunden bleibt, nicht nur, dass der alte Code weg ist.

## Schritt 2: der style-Block und eine Anmeldeseite ohne Stylesheet-Liste

Commit 599e793 hat die Regeln des `<style>`-Blocks in `user2fa/exp_style.tpl` nach
`stylesheets/sevenx_authentication_2fa.css` verschoben, geladen über `design.ini [StylesheetSettings]` für öffentliche
und Administrationsdesigns.

Das reichte nicht, und der nächste Commit (ea4947c) erklärt, warum: „Die Anmeldeseite der Admin UI lädt keine
Stylesheet-Liste, daher hätte ihr zweiter Schritt bei der Anmeldung mit nur den Listen des Packers sein Aussehen
verloren.“ Also verlinkt das Template das Stylesheet zusätzlich an der Stelle des früheren Blocks, „was die Regeln auch
dort in der Kaskade hält, wo sie waren“.

Die Lehre: Bevor Sie sich auf `FrontendCSSFileList[]` oder `BackendCSSFileList[]` verlassen, finden Sie jedes
Pagelayout, in dem Ihre Templates dargestellt werden. Anmeldeseiten, Popups und Druckansichten laden oft nur einen
reduzierten Satz von Styles.

## Schritt 3: Farben, die im Dunkeln funktionieren

Das Verschieben der Styles hat ein zweites Problem sichtbar gemacht: „die Karte blieb unter hellem Text weiß, 1,2:1“ in
einem dunklen admin4 (599e793), und Statuswörter behielten auf der dunklen Karte die Farben des hellen Modus („Off“ in
dunklem Bernstein, 2,4:1, 6a75f30). Die Lösung nimmt die Farben aus den Admin-Tokens.

Hier hat diese Erweiterung eine andere Wahl getroffen als der Template-Editor der Layouts
([admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md)), und beide haben recht:

| | Template-Editor von explayouts_ui | sevenx_authentication_2fa |
|---|---|---|
| Wo es läuft | nur in admin4 | admin4, andere Administrationsdesigns, öffentliche Websites, Anmeldeseiten |
| Fallbacks für Tokens | entfernt: die Tokens existieren dort immer | beibehalten: „die früheren Werte für die Designs ohne sie“ |

Ein Fallback wie `var(--a4-ok, #166534)` (aus dem Stylesheet dieser Erweiterung) ist ein Problem, wenn das Token immer
existiert (er verdeckt Fehler), und eine Notwendigkeit, wenn es fehlen kann (das Design der öffentlichen Website
definiert keine Admin-Tokens). Entscheiden Sie danach, wo Ihr Stylesheet geladen wird.

## Schritt 4: die letzten Attribute

Die Commits 73bfdd2, 2031e45 und 0bee499 sind Seite für Seite durchgegangen, was übrig war: die Abstände der
Einrichtungsseite, der Konto-Link, das öffentliche 2FA-Feld und die Kopien der Seiten im Design media. Jeder hat
`style=""`-Attribute durch Klassen des Stylesheets ersetzt und den Test auf die Seite erweitert.

## Warum so viele Releases?

Weil jedes vollständig war. Eine Website, die nach 1.0.9 aktualisiert wurde, hatte kein Inline-Skript; eine, die nach
1.0.12 aktualisiert wurde, auch kein Inline-CSS mehr. Hätte ein Release etwas kaputt gemacht, wäre die Änderung, die
man sich ansehen musste, ein kleiner Commit gewesen. Jedes Release war ein eigener Commit, der nur die Version in
`ezinfo.php` erhöht hat (diese Erweiterung hat keine `extension.xml`; eine, die eine hat, muss sie dort ebenfalls
erhöhen, siehe [ezinfo.php und extension.xml hochzählen](../teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md)).

## Was Sie daraus mitnehmen können

1. Zuerst das Skript, dann der style-Block, dann die Attribute: geordnet danach, wie viel unter der Policy bricht.
2. Sehen Sie sich die Seite nach dem Verschieben der Styles im dunklen Modus an: Verschobene Styles machen fest
   eingetragene Farben sichtbar.
3. Finden Sie jedes Pagelayout, in dem Ihre Templates erscheinen, besonders Anmeldeseiten.
4. Verwenden Sie Fallbacks für Tokens nur dort, wo die Tokens fehlen können.
5. Erweitern Sie den Test um jede Seite, die Sie korrigieren.

## Verwandte Seiten

- Zurück: [Fallstudie: der Layout-Editor auf jQuery 4](fallstudie-der-layout-editor-auf-jquery-4.md)
- Weiter: [Fallstudie: kleine PHP-8-Korrekturen in vielen Erweiterungen](fallstudie-kleine-korrekturen-in-vielen-erweiterungen.md)
- [Von Inline-CSS zu Stylesheets](inline-css-in-stylesheets.md)
- [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
