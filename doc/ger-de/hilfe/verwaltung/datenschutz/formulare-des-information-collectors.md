---
title: Formulare zur Informationssammlung und Datenschutz
audience: government, admin, editor, project-manager
category: public-sector
order: 260
summary: Für öffentliche Stellen, die Daten über Webformulare erheben - was Exponential pro Einsendung speichert, wer sie lesen und exportieren darf, die Audit-Einträge zu Exporten und Entfernungen, das Fehlen einer automatischen Aufbewahrungsfrist, und der Einwilligungstext, der Hinweis und die Aufbewahrungsroutine, die jedes Formular braucht.
keywords: Informationssammlung, Informationssammler, Formulare, Formulareinsendungen, Gesammelte Informationen, infocollector/read, CSV-Export, Aufbewahrung, Entfernen, Einwilligungstext, Datenschutzerklärung, data.export.csv, data.infocollection.remove, E-Mail an Empfänger, öffentlicher Sektor, information collection, forms, collected information, retention
type: help
---
# Formulare zur Informationssammlung und Datenschutz

Diese Seite richtet sich an Administratoren, an Redakteure, die für Formulare zuständig sind, und an
Datenschutzbeauftragte. Kontakt-, Beschwerde- und Anmeldeformulare einer öffentlichen Website erheben oft sensible
Informationen. In Exponential ist ein Formular ein Inhaltsobjekt, dessen Klasse Attribute mit der Eigenschaft
**Informationssammler** hat; jede Einsendung wird als Sammlung gespeichert
([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)). Das Projekt führt Formulare unter den bekannten
Lücken: „Die Website muss einen Einwilligungstext und eine Aufbewahrungsregel selbst hinzufügen“
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#known-gaps)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was gespeichert wird](#was-gespeichert-wird)
2. [Wer es sieht](#wer-es-sieht)
3. [Aufbewahrung: eine manuelle Routine](#aufbewahrung-eine-manuelle-routine)
4. [Was jedes Formular braucht](#was-jedes-formular-braucht)
5. [Verwandte Seiten](#verwandte-seiten)

## Was gespeichert wird

- Eine Sammlung pro Einsendung, mit einem Wert pro Attribut des Informationssammlers, der Zeit und der Benutzer-ID (für
  Besucher der anonyme Benutzer).
- Wenn das Formular eine E-Mail sendet, geht eine Kopie an den Empfänger
  (`site.ini [InformationCollectionSettings] EmailReceiver` oder ein Attribut `recipient` in den ezwebin-Templates):
  Dieses Postfach ist ein weiterer Ort, an dem die Daten liegen.
- Nicht angemeldete Besucher dürfen standardmäßig einsenden
  (`collect.ini [CollectionSettings] CollectAnonymousData=enabled`).

## Wer es sieht

- **Setup > Gesammelte Informationen** und jede Seite darunter erfordern `infocollector/read`; geben Sie dieses Recht
  den Personen, die die Formulare bearbeiten.
- **CSV exportieren** lädt alle Sammlungen eines Formulars herunter (bis zu 50 000); jeder Export wird im Audit als
  `data.export.csv` mit der Zahl der Zeilen aufgezeichnet, nie mit den Werten.
- Entfernungen werden als `data.infocollection.remove` aufgezeichnet.

## Aufbewahrung: eine manuelle Routine

Nichts entfernt Sammlungen automatisch. Legen Sie eine Routine fest:

1. Legen Sie die Aufbewahrung pro Formular in Ihrem Aufbewahrungsplan fest (zum Beispiel: bis der Vorgang
   abgeschlossen ist, plus eine Frist).
2. Öffnen Sie zum geplanten Zeitpunkt Setup > Gesammelte Informationen, exportieren Sie, was in Ihrem
   Vorgangssystem aufbewahrt werden muss, markieren Sie dann die Objekte (oder die einzelnen Sammlungen) und klicken
   Sie auf **Ausgewählte entfernen**; die Bestätigung listet auf, was entfernt wird.
3. Löschen Sie die exportierten CSV-Dateien, wenn sie ihren Zweck erfüllt haben.
4. Prüfen Sie auch die Aufbewahrung im Empfängerpostfach.

## Was jedes Formular braucht

- [ ] Einen kurzen Hinweis neben dem Formular: wer die Daten verarbeitet, warum, wie lange, und einen Link zur
  Datenschutzerklärung.
- [ ] Nur die Felder, die der Zweck braucht; optionale Felder als solche gekennzeichnet.
- [ ] Ein Kontrollkästchen für die Einwilligung nur dort, wo die Einwilligung wirklich die Grundlage ist (oft ist es
  eine öffentliche Aufgabe).
- [ ] Eine benannte zuständige Person, die die Einsendungen liest.
- [ ] Eine Aufbewahrungsregel und die Routine oben.
- [ ] Ein barrierefreies Formular (der Bereich Barrierefreiheit dieser Hilfe).
- [ ] Kein CAPTCHA, sofern es keinen tatsächlichen Missbrauch gibt (siehe „CAPTCHAs und Datenschutz“).

## Verwandte Seiten

- [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)
- [Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)
- [Formulareinsendungen](../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
