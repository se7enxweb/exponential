---
title: "Lektion 4.3: Theme-Designer, schriftlicher Test"
audience: trainer, designer
category: training
order: 43
summary: Ein schriftlicher Test mit dreizehn Fragen für das Ende des Kurses Theme-Design, zu Theme-Erweiterungen, dem Registrieren eines Designs, den Design-Einstellungen, der Ladereihenfolge, Overrides, Template-Pfadkommentaren, Inline-Code und der Content Security Policy sowie Versionsnummern, mit einem Lösungsschlüssel, der die Seiten für Designer zitiert. 25 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, theme-design, schriftlicher test, theme-erweiterung, ladereihenfolge, override, template-pfadkommentare, inline-skripte, versionsnummer, assessments and practical exercises, theme design, written test, theme extension, DesignExtensions, SiteDesign, AdditionalSiteDesignList, loading order, extends, template path comments, CSP, inline scripts, version number
type: help
---
# Lektion 4.3: Theme-Designer, schriftlicher Test

Dieser Test schließt den Kurs Theme-Design ab. Er prüft, ob Designer ein Theme dort halten können, wo es hingehört (in
seiner eigenen Erweiterung), dafür sorgen können, dass die Installation es findet, es dort gegen ein Basis-Theme
gewinnen lassen können, wo das gewünscht ist, und es ohne Inline-Code und mit einer korrekten Versionsnummer
ausliefern können.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 4: Die technischen Kurse prüfen](README.md) |
| Dauer | 40 Minuten (5 Einweisung, 25 Test, 10 Besprechung der Antworten) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Theme-Design |
| Voraussetzungen | Der Kurs Theme-Design; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- erklären, warum ein Theme in einer Erweiterung liegt und was es enthält;
- ein Design registrieren und es für einen Siteaccess auswählen;
- vorhersagen, welches Theme gewinnt, wenn zwei dasselbe Template liefern;
- herausfinden, welches Template ein Stück Markup geschrieben hat;
- ein Theme auf eine Content Security Policy vorbereiten und seine Releases nummerieren.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 25 Minuten.
2. Gehen Sie die Antworten mit den Seiten für Designer auf dem Projektor durch.

### Häufige Fragen

**Ist `DesignExtensions[]` der Name der Erweiterung oder der Name des Designs?** Der Name der Erweiterung (des
Ordners); Frage 4 prüft das, und es ist der häufigste Grund, warum ein Theme nicht gefunden wird.

### Stolperfallen

- **Teilnehmende, die `design/standard` bearbeitet haben.** Nutzen Sie Frage 1, um zu besprechen, warum das beim
  nächsten Upgrade verloren geht.

## Der Test

1. Nennen Sie zwei Gründe, die Dateien eines Themes nicht in die eigenen `design/`-Ordner der Installation zu legen.
2. Nennen Sie vier Dinge, die eine minimale Theme-Erweiterung enthält.
3. Welche Datei des Themes registriert sein Design, und mit welcher Einstellung?
4. Ist der Wert dieser Einstellung der Name der Erweiterung oder der Name des Designs?
5. Warum sollte ein Theme `SiteDesign` nicht global setzen?
6. Zwei aktive Themes haben beide eine `pagelayout.tpl`. Welche wird verwendet?
7. Ihr Theme soll gegen `base_theme` gewinnen. Was deklarieren Sie in Ihrer `extension.xml`?
8. Wie finden Sie heraus, welche Template-Datei ein Stück Markup auf einer Seite geschrieben hat?
9. Warum dürfen Template-Pfadkommentare für einen Produktiv-Siteaccess niemals eingeschaltet bleiben?
10. Woher kommt die Content Security Policy, und wie ist sie auf Websites im Auslieferungszustand eingestellt?
11. Nennen Sie zwei Arten von Inline-Code, die Sie aus einem Theme entfernen, bevor eine Website die Richtlinie
    einschaltet.
12. Ein Wert pro Element (ein Fortschritt in Prozent) wurde mit `style=""` gesetzt. Wohin gehört er stattdessen?
13. Das letzte Release Ihres Themes war `1.0.9`. Was ist das nächste Patch-Release?

<details>
<summary>Antworten</summary>

1. Zwei davon: Das nächste Upgrade überschreibt sie, niemand kann Ihre Änderungen von denen des Produkts
   unterscheiden, es lässt sich nicht getrennt weitergeben oder verkaufen, die Prüfung der Dateikonsistenz listet
   jede geänderte Datei auf
   ([Was eine Theme-Erweiterung ist](../../../designer/erweiterungen/was-eine-theme-erweiterung-ist.md)).
2. Vier davon: `design/<name>/templates/` mit `pagelayout.tpl`, `stylesheets/`, `javascript/`, `images/`,
   `settings/design.ini.append.php`, `ezinfo.php`, `extension.xml`, `LICENSE`, `README.md`
   ([Was eine Theme-Erweiterung ist](../../../designer/erweiterungen/was-eine-theme-erweiterung-ist.md)).
3. `settings/design.ini.append.php` mit `[ExtensionSettings] DesignExtensions[]=<extension>`
   ([Das Design registrieren](../../../designer/erweiterungen/das-design-registrieren.md)).
4. Der Name der Erweiterung (des Ordners); jedes `design/<name>/` darunter steht dann zur Verfügung
   ([Das Design registrieren](../../../designer/erweiterungen/das-design-registrieren.md)).
5. Es würde auch die Administration und jede andere Website verändern
   ([Die Design-Einstellungen, die ein Theme mitliefert](../../../designer/erweiterungen/design-einstellungen-eines-themes.md)).
6. Die der früher geladenen Erweiterung
   ([Ladereihenfolge und welches Theme gewinnt](../../../designer/erweiterungen/ladereihenfolge-von-themes.md)).
7. `<extends><extension name="base_theme" /></extends>` in `<dependencies>`, damit Ihr Theme vor ihm geladen wird
   ([Ladereihenfolge und welches Theme gewinnt](../../../designer/erweiterungen/ladereihenfolge-von-themes.md)).
8. Schalten Sie die Template-Pfadkommentare ein und suchen Sie im Seitenquelltext nach `START`
   ([Templates und Design](../../../../anleitungen/templates-und-design.md#3-sehen-welches-template-welches-markup-geschrieben-hat)).
9. Sie fügen jedem Template Markup mit den Pfaden der Template-Dateien hinzu; sie sind für die Entwicklung gedacht,
   niemals für einen Produktiv-Siteaccess oder für Feeds
   ([Templates und Design](../../../../anleitungen/templates-und-design.md#3-sehen-welches-template-welches-markup-geschrieben-hat)).
10. Aus `site.ini [ContentSecurityPolicy]`; auf Websites ausgeschaltet, in der Administration im Nur-Bericht-Modus
    ([Inline-Skripte und -Styles entfernen](../../../designer/lebenszyklus/inline-skripte-und-styles-entfernen.md)).
11. Zwei davon: `<script>`-Blöcke, Ereignisattribute wie `onclick=`, `javascript:`-Links, `style=""`-Attribute
    ([Inline-Skripte und -Styles entfernen](../../../designer/lebenszyklus/inline-skripte-und-styles-entfernen.md)).
12. In ein `data-*`-Attribut oder eine Custom Property, die über eine Klasse im Stylesheet gesetzt wird
    ([Inline-Skripte und -Styles entfernen](../../../designer/lebenszyklus/inline-skripte-und-styles-entfernen.md)).
13. `1.0.10`: Nur die letzte Stelle zählt weiter
    ([Nur die letzte Stelle zählt](../../../designer/veroeffentlichung/nur-die-letzte-stelle-zaehlt.md)).

</details>

## Bewertung

- Ein Punkt pro Frage; Frage 2 braucht für den Punkt vier Angaben.
- Bestanden: 9 von 13 Punkten oder mehr, mit den Fragen 3 und 4 richtig.

## Weiterführende Literatur

- [Theme-Erweiterungen: hier beginnen](../../../designer/erweiterungen/theme-erweiterungen-hier-beginnen.md)
- [Ladereihenfolge und welches Theme gewinnt](../../../designer/erweiterungen/ladereihenfolge-von-themes.md)
- [Inline-Skripte und -Styles entfernen](../../../designer/lebenszyklus/inline-skripte-und-styles-entfernen.md)

## Verwandte Seiten

- [Lektion 4.4: Theme-Designer, praktische Aufgabe](theme-designer-praktische-aufgabe.md)
- [Modul 4: Die technischen Kurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
