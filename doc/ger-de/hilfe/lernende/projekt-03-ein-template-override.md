---
title: "Projekt 3: ein Template-Override"
audience: student
category: learning
order: 27
summary: Lernprojekt 3 - das Design Ihres öffentlichen Siteaccess finden, sehen, welches Template eine Seite ausgibt, ein Override für eine Inhaltsklasse mit dem Template-Editor und dann als Dateien mit einer Regel in override.ini anlegen, das Template in eZ TPL mit maskierter Ausgabe schreiben, es prüfen und die Dateien und Nachweise abgeben, bewertet mit einem Bewertungsraster.
keywords: lernprojekt, template, override, template-override, override.ini, design, template-editor, pfadkommentare, eztemplatecheck, wash, fetch, node/view/full.tpl, class_identifier, lernende, study project, template editor, path comments, learner
type: help
---
# Projekt 3: ein Template-Override

In diesem Projekt ändern Sie, wie eine Art von Inhalt auf der Website aussieht, ohne eine Datei anzufassen, die Ihnen
nicht gehört. Sie tun es zweimal: einmal mit dem Template-Editor in der Administrationsoberfläche, einmal als Dateien,
die Sie in Git aufbewahren. Die Schritte folgen [Templates und Design](../../anleitungen/templates-und-design.md).

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Ziel](#ziel)
3. [Bevor Sie beginnen](#bevor-sie-beginnen)
4. [Aufgaben](#aufgaben)
5. [Abgabe](#abgabe)
6. [Bewertungsraster](#bewertungsraster)
7. [Zusatzziele](#zusatzziele)

## Auf einen Blick

| | |
|---|---|
| Stufe | Fortgeschritten |
| Zeit | Etwa 3 Stunden |
| Pfade | Entwickler, Designer |
| Voraussetzungen | Ein Terminal auf Ihrer Lernwebsite; [Die Template-Sprache lernen](die-template-sprache-lernen.md); [Projekt 1](projekt-01-eine-eigene-inhaltsklasse.md) ist nützlich |

## Ziel

Eine vollständige Ansicht für eine Inhaltsklasse (Ihr „Club event“ aus Projekt 1 oder Artikel), die den Titel, ein
oder mehrere Attribute und eine Liste der Geschwisterelemente zeigt, nur auf diese Klasse angewendet und als Dateien in
Ihrem eigenen Git-Repository aufbewahrt.

## Bevor Sie beginnen

Lesen Sie [Templates und Design](../../anleitungen/templates-und-design.md), Teile 1 bis 6, und
[Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md).

## Aufgaben

1. **Finden Sie Ihr Design.** Führen Sie `php bin/php/console exp:ini where site/DesignSettings/SiteDesign site` aus
   (verwenden Sie den Siteaccess der Seite, die Sie betrachten). Notieren Sie den Designnamen aus der Zeile
   `In effect:` ([Templates und Design](../../anleitungen/templates-und-design.md#2-herausfinden-welches-design-sie-verwenden)).
2. **Sehen Sie, welches Template antwortet.** Schalten Sie die Template-Pfadkommentare für Ihren öffentlichen
   Siteaccess ein, leeren Sie den Template-Cache, öffnen Sie ein Element Ihrer Klasse und suchen Sie im Quelltext der
   Seite nach `START` ([Templates und Design](../../anleitungen/templates-und-design.md#3-sehen-welches-template-welches-markup-geschrieben-hat)).
   Notieren Sie das Template, das die vollständige Ansicht ausgibt.
3. **Klickpfad.** Öffnen Sie unter **Design > Template-Editor** die Datei `node/view/full.tpl` Ihres Designs, drücken
   Sie **Neues Überschreib-Template**, wählen Sie *Kopie des Standard-Templates*, geben Sie die Objekt-ID eines
   Elements ein und **Erstellen**. Fügen Sie eine sichtbare Zeile hinzu, speichern Sie und laden Sie das Element neu.
   Entfernen Sie das Override danach wieder mit **Ausgewählte entfernen**
   ([Templates und Design](../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen)).
4. **Dateipfad.** Legen Sie das Template im Override-Verzeichnis Ihres Designs an, oder besser in einer eigenen
   Design-Erweiterung ([Lernpfad: Designer](lernpfad-designer.md), Schritt 7). Schreiben Sie eine Override-Regel
   in `override.ini.append.php` mit `Source=node/view/full.tpl`, Ihrer `MatchFile`, `Subdir=templates` und
   `Match[class_identifier]=<your class identifier>` statt eines einzelnen Objekts.
5. **Schreiben Sie das Template.** Zeigen Sie den Titel mit `{$node.name|wash}`, mindestens ein Attribut mit
   `attribute_view_gui` und die Liste der Geschwisterelemente aus dem Beispiel der Anleitung. Jeder ausgegebene Wert
   ist maskiert.
6. **Prüfen Sie die Syntax**: `php bin/php/eztemplatecheck.php -s<siteaccess> <your file> --allow-root-user`.
   Erwartet: `Template file valid: <your file>` ([Templates und Design](../../anleitungen/templates-und-design.md#5-die-syntax-prüfen-bevor-sie-in-den-browser-schauen)).
7. **Leeren Sie die Caches einmal** für den neuen Template-Pfad (`template-override`, den Tag template und den Tag
   content, wie in der Anleitung) und laden Sie zwei Elemente Ihrer Klasse und ein Element einer anderen Klasse neu.
   Nur Ihre Klasse sollte sich ändern.
8. **Schalten Sie die Pfadkommentare wieder aus** und leeren Sie den Template-Cache. Sie fügen jedem Template Markup
   hinzu und dürfen niemals eingeschaltet bleiben.
9. **Committen Sie** das Template und die Regel in Ihr eigenes Repository.

## Abgabe

- Die Template-Datei und die Override-Regel.
- Screenshots: der Quelltext der Seite mit dem Kommentar `START ... (node/view/full.tpl)` Ihrer Datei; zwei Elemente
  Ihrer Klasse und eines einer anderen Klasse.
- Die Ausgabe der Syntaxprüfung.
- Ein kurzer Bericht: warum Sie nach Klasse und nicht nach Objekt zugeordnet haben und was jede Zeile des Templates
  tut.

## Bewertungsraster

Bewerten Sie die vier gemeinsamen Kriterien aus [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md) und diese
Prüfungen (8):

1. Der Designname wurde mit `exp:ini where` gefunden, nicht geraten.
2. Keine Datei in `design/standard/` wurde geändert.
3. Das Override gilt für die ganze Klasse und nur für sie.
4. Jeder ausgegebene Wert verwendet `wash` oder eine View-Funktion.
5. Die Syntaxprüfung ist erfolgreich.
6. Die Pfadkommentare sind am Ende ausgeschaltet.
7. Die Dateien liegen im eigenen Git-Repository der lernenden Person, ohne Geheimnisse aus `settings/override/`.
8. Der Bericht erklärt `Source`, `MatchFile`, `Subdir` und `Match`.

## Zusatzziele

- Fügen Sie für dieselbe Klasse ein Override der Ansicht `line` hinzu und verwenden Sie es in einer Liste mit
  `node_view_gui ... view='line'`.
- Ordnen Sie zwei passende Overrides mit `Priority` und erklären Sie, welches gewinnt
  ([Template-Editor](../../funktionen/6.0/template-editor-ueberschreibungen.md)).
- Lesen Sie [Template-Ressourcen und Overrides](../template-sprache/template-ressourcen-und-overrides.md) und zeichnen Sie auf,
  wie Ihr Template aufgelöst wird.

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Die Template-Sprache lernen](die-template-sprache-lernen.md)
- [Grundlagen der Overrides](../designer/vorlagen/override-grundlagen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
