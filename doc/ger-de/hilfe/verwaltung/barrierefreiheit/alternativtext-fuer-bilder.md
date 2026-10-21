---
title: Alternativtext für Bilder
audience: government, editor, admin, designer
category: public-sector
order: 160
summary: Für Redaktion und Administration öffentlicher Stellen - wie Sie Alternativtext schreiben, wo Exponential ihn speichert, wie Sie ihn für eine Inhaltsklasse verpflichtend machen, wie sich dekorative Bilder im Standarddesign verhalten und die optionalen KI-Vorschläge und der Bericht.
keywords: Alternativtext, Alt-Text, Alt-Attribut, Bilder, dekorative Bilder, Alternativer Bildtext, Alternativtext ist erforderlich, Redaktion, alternative text, alt text, alt attribute, images, decorative images, Alternative image text, Alternative text is required, ezimage, WCAG 1.1.1, Missing alt text, Exponential Intelligence, expai, editors
type: help
---
# Alternativtext für Bilder

Diese Seite richtet sich an die Redaktion, die Bilder veröffentlicht, an die Administration, die Inhaltsklassen
konfiguriert, und an das Design, das Bild-Templates schreibt. Alternativtext dient Menschen, die ein Bild nicht sehen
können: Nutzerinnen und Nutzern von Screenreadern, Menschen mit abgeschalteten Bildern und Suchmaschinen. Das
Erfolgskriterium 1.1.1 von WCAG 2.2 verlangt ihn für jedes informative Bild (<https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [Guten Alternativtext schreiben](#guten-alternativtext-schreiben)
2. [Wo Exponential ihn aufbewahrt](#wo-exponential-ihn-aufbewahrt)
3. [Ihn verpflichtend machen](#ihn-verpflichtend-machen)
4. [Dekorative Bilder](#dekorative-bilder)
5. [Bilder ohne Alternativtext finden](#bilder-ohne-alternativtext-finden)
6. [Verwandte Seiten](#verwandte-seiten)

## Guten Alternativtext schreiben

| Bild | Guter Alternativtext | Vermeiden |
|---|---|---|
| Foto des Rathauses auf einer Seite zu den Öffnungszeiten | Haupteingang des Rathauses am Marktplatz | „Bild eines Gebäudes“, der Dateiname |
| Diagramm mit Haushaltszahlen | die Schlussfolgerung („Die Ausgaben für Straßen stiegen von 2022 bis 2025 jedes Jahr“) und die Zahlen in einer Tabelle auf der Seite | „Diagramm“ |
| Logo, das auf die Startseite verlinkt | das Ziel („Musterbehörde, Startseite“) | „Logo“ |
| Scan einer Bekanntmachung | stattdessen der Text der Bekanntmachung auf der Seite; das Bild braucht dann nur eine kurze Beschreibung | den ganzen Text ins Alt-Attribut gezwängt |
| Muster, Trennlinie, rein dekoratives Foto | leer, wenn Ihr Design `alt=""` schreibt (siehe unten) | eine Beschreibung |

Halten Sie ihn kurz (ein Satz oder eine Wortgruppe), beginnen Sie nicht mit „Bild von“, und wiederholen Sie die
Bildunterschrift nicht wortwörtlich.

## Wo Exponential ihn aufbewahrt

Jedes Bildfeld (Datentyp `ezimage`) hat im Bearbeitungsformular ein Feld **Alternativer Bildtext**
(`design/standard/templates/content/datatype/edit/ezimage.tpl`). Er wird mit dem Bild dieser Version und Sprache
gespeichert, sodass jede Übersetzung ihren eigenen hat. Bilder in der Medienbibliothek sind Bildobjekte; ihr
Alternativtext wandert mit ihnen, wenn sie eingebettet werden.

## Ihn verpflichtend machen

Seit 6.0.15 hat ein Bildattribut einer Klasse die Option **Alternativtext ist erforderlich**
(`kernel/classes/datatypes/ezimage/ezimagetype.php`). Ist sie eingeschaltet, kann die Redaktion in diesem Feld kein
Bild ohne Alternativtext speichern; das Formular zeigt neben dem Feld „(erforderlich)“.

1. Öffnen Sie Setup > Klassen, die Klasse (zum Beispiel Image oder Article), **Bearbeiten**.
2. Kreuzen Sie am Bildattribut **Alternativtext ist erforderlich** an.
3. Speichern Sie die Klasse.

Vorhandene Objekte behalten ihre Bilder; die Regel gilt, wenn ein Bild das nächste Mal gespeichert wird. Ein
ZIP-Upload von Bildern überspringt Bilder, wenn die Website Alternativtext verlangt, weil eine ZIP-Datei keinen
mitbringt ([Bilder, Medien und Uploads](../../medien-und-uploads.md)).

## Dekorative Bilder

Das Bild-Template des Standarddesigns (`design/standard/templates/content/datatype/view/ezimage.tpl`) verwendet den
Alternativtext und, **wenn er leer ist, den Namen des Objekts** als `alt`; denselben Text verwendet es auch als
`title`. Ein leerer Alternativtext ergibt in diesem Template also kein `alt=""`. Wenn Ihr Leitfaden manche Bilder als
dekorativ behandelt, bitten Sie Ihr Design um einen Override des Bild-Templates (oder eine eigene Klasse oder Ansicht für
dekorative Bilder), der `alt=""` und kein `title` schreibt. Sagen Sie der Redaktion dann genau, wie sie ein Bild als
dekorativ kennzeichnet.

## Bilder ohne Alternativtext finden

- **Ihre eigene Überprüfung**: eine monatliche Stichprobe neuer Seiten (siehe „Überwachung und Rückmeldungen“).
- **Optional, Exponential Intelligence** (Erweiterung `expai`, von Composer installiert, aber nur aktiv, wo eine
  Website sie aktiviert): Das Bearbeitungsformular erhält die Schaltflächen **Write alt text** (Alternativtext
  schreiben) und **Decorative (empty alt text)** (Dekorativ, leerer Alternativtext), und der Bericht
  **Missing alt text** (Fehlender Alternativtext; `/expai/alttext`, Richtlinie `expai/write`) listet Bilder ohne
  Alternativtext unterhalb eines Knotens auf; **Queue suggestions as drafts** (Vorschläge als Entwürfe einreihen)
  schreibt Vorschläge in neue Entwürfe, nie veröffentlicht, zur Prüfung durch die Redaktion
  ([Exponential Intelligence](../../../../eng-us/features/6.0/extensions/expai.md)). Bilder werden an einen KI-Anbieter gesendet,
  nach der Datenrichtlinie der Erweiterung; entscheiden Sie das vor dem Einschalten mit Ihrer oder Ihrem
  Datenschutzbeauftragten.
- Im Kernel listet nichts die Bilder ohne Alternativtext über die gesamte Website auf.

## Verwandte Seiten

- [Bilder, Medien und Uploads](../../medien-und-uploads.md)
- [Bilder und Bildvarianten](../../../../eng-us/help/creators/website-creators/images-and-image-variations.md)
- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
