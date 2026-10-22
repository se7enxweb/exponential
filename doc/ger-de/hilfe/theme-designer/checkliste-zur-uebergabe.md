---
title: "Checkliste zur Übergabe"
audience: theme-designer
category: theme-design
order: 37
summary: Die Checkliste von allem, was ein Theme-Designer für ein Exponential-Theme an Entwickler übergibt - Bestandsaufnahme und Zuordnungsblatt, Tokens mit dunklen Werten, Komponenten mit Zuständen, Ansichtsmatrix, Bilder- und Blockblätter, besondere Seiten, Bewegung, Budget und Anleitung für Inhalte - damit Templates ohne Raten geschrieben werden können.
keywords: Theme-Designer, Übergabe, Handoff, Checkliste, Lieferumfang, Spezifikation, Tokens, Komponenten, Ansichtsmatrix, Bilderblatt, Blockblatt, Übergabe an Entwickler, theme designer, handoff, hand-off, checklist, deliverables, specification, components, view matrix, image sheet, block sheet, developer handoff
type: help
---
# Checkliste zur Übergabe

Diese Seite ist für Theme-Designer, die ein Design an die Entwickler übergeben, die das Theme bauen werden, oder an sich
selbst in einer späteren Rolle. Eine vollständige Übergabe heißt, dass Templates und Stylesheets ohne Raten geschrieben
werden können und Reviews gegen etwas Schriftliches prüfen können. Jede Zeile nennt die Seite, die den Punkt erklärt.

## Inhalt

1. [Vor der Übergabe](#vor-der-übergabe)
2. [Inhalte](#inhalte)
3. [Grundlagen](#grundlagen)
4. [Komponenten](#komponenten)
5. [Seiten und Ansichten](#seiten-und-ansichten)
6. [Bilder, Blöcke und Verhalten](#bilder-blöcke-und-verhalten)
7. [Qualitätsziele](#qualitätsziele)
8. [Wo alles liegt](#wo-alles-liegt)

## Vor der Übergabe

- [ ] Bestandsaufnahme und Grundlagen sind freigegeben ([Mit Kunden und Reviews arbeiten](mit-kunden-und-reviews-arbeiten.md#freigabe)).
- [ ] Der Entwickler hat die Bestandsaufnahme gesehen und alles Teure markiert ([Mit Entwicklern arbeiten](mit-entwicklern-arbeiten.md)).
- [ ] Jedes Mockup verwendet echte Inhalte, einschließlich der Extreme ([Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md)).

## Inhalte

- [ ] Zuordnungsblatt: jedes sichtbare Element mit Klasse, Feld, Datentyp, Ansichten und Design für leer
      ([Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md#das-zuordnungsblatt)).
- [ ] Anfragen nach neuen Feldern geschrieben, mit Begründung ([Mit Entwicklern arbeiten](mit-entwicklern-arbeiten.md#anfragen-an-den-entwickler)).
- [ ] Anleitungstexte an den Feldern für Redakteure, wo sie helfen ([Für Redakteure gestalten](fuer-redakteure-gestalten.md#redakteuren-sagen-was-das-design-erwartet)).
- [ ] Oberflächentexte für die Übersetzung aufgelistet (Schaltflächen, Beschriftungen, Meldungen).

## Grundlagen

- [ ] Token-Tabelle: Name, heller Wert, dunkler Wert, Verwendung ([Von Designtool-Variablen zu CSS Custom Properties](von-designtool-variablen-zu-css-custom-properties.md#die-exporttabelle)).
- [ ] Palettenblatt mit gemessenen Kontrastverhältnissen in beiden Themes ([Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md#ein-palettenblatt)).
- [ ] Typografische Skala mit Zeilenhöhen und Zeilenlänge ([Typografische Skala](typografische-skala.md)).
- [ ] Abstandsskala, Regeln für innen und zwischen, Seitenlayouts nach Absicht ([Abstände und Raster](abstaende-und-raster.md)).
- [ ] Schriften: Dateien, Lizenzen, Schnitte, Ersatzstapel ([Schriften in einem Design](../designer/theme-design/schriften-in-einem-design.md)).
- [ ] Bewegungs-Tokens (Dauern, Easing) ([Bewegung und Interaktion](bewegung-und-interaktion.md)).

## Komponenten

- [ ] Komponenteninventar mit Varianten, Tokens, Klassennamen ([Komponenteninventar](komponenteninventar.md)).
- [ ] Zustandsmatrix pro Komponente, hell und dunkel ([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md#die-zustandsmatrix)).
- [ ] Symbole als SVG-Dateien mit Namen ([Symbole in einem Design](../designer/theme-design/icons-in-einem-design.md)).

## Seiten und Ansichten

- [ ] Rahmen: Kopf- und Fußbereich in drei Breiten, angemeldet und abgemeldet, Menü für kleine Bildschirme geöffnet
      ([Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md)).
- [ ] Navigation: Quellen, Tiefe, aktueller Zustand ([Navigation und Menüs gestalten](navigation-und-menues-gestalten.md)).
- [ ] Ansichtsmatrix: jede Klasse gegen jeden Ansichtsmodus ([Ansichtsmodi gestalten](ansichtsmodi-gestalten.md#die-ansichtsmatrix)).
- [ ] Ein Mockup pro "ja" in der Matrix, mit kürzestem und längstem Inhalt.
- [ ] Listen und Seitennavigation ([Inhaltslisten und Karten gestalten](inhaltslisten-und-karten-gestalten.md)).
- [ ] Suche mit Ergebnissen und ohne ([Suchergebnisse gestalten](suchergebnisse-gestalten.md)).
- [ ] Fehlerseiten, leere Zustände ([Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md)).
- [ ] Formulare und Kontoseiten ([Formulare gestalten](formulare-gestalten.md), [Anmelde- und Benutzerseiten gestalten](anmelde-und-benutzerseiten-gestalten.md)).
- [ ] Versionen der wichtigen Bildschirme von rechts nach links, falls nötig ([Für viele Sprachen gestalten](fuer-viele-sprachen-gestalten.md)).

## Bilder, Blöcke und Verhalten

- [ ] Bilderblatt: Platz, Alias, Filter, Verhältnis und Zuschnitt, Partner in srcset, Design ohne Bild
      ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md#das-bilderblatt)).
- [ ] Bild zum Teilen und Standardbild zum Teilen ([Bilder zum Teilen und für Metadaten](bilder-fuer-social-sharing-und-metadaten.md)).
- [ ] Blatt für Blockdesigns pro Block- und Ansichtstyp, wenn die Website Layouts verwendet
      ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md#das-blatt-für-blockdesigns)).
- [ ] Bewegungstabelle mit Fassungen für reduzierte Bewegung ([Bewegung und Interaktion](bewegung-und-interaktion.md#bewegung-bei-der-übergabe-festlegen)).
- [ ] Markenmatrix, bei Installationen mit mehreren Marken ([Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md#die-markenmatrix)).
- [ ] Design des Newsletter-Skins, wenn die Website Newsletter versendet ([Newsletter-E-Mails gestalten](newsletter-e-mails-gestalten.md)).

## Qualitätsziele

- [ ] Performance-Budget vereinbart ([Für Performance-Budgets gestalten](fuer-performance-budgets-gestalten.md)).
- [ ] Ziel für Barrierefreiheit genannt (Kontrast, Tastatur, Zoom).
- [ ] Zu testende Breiten und Themes: 1440 px, 960 px bei 200 % Zoom, 375 px; hell und dunkel.
- [ ] Die Regeln wiederholt: kein Inline-CSS und keine Inline-Skripte, nur Tokens, dunkle Werte, kein `?v=` an Links
      ([Mit Entwicklern arbeiten](mit-entwicklern-arbeiten.md#regeln-die-beide-seiten-einhalten)).

## Wo alles liegt

Alle schriftlichen Punkte kommen in das Repository des Themes (oder in einen Ordner, auf den es verlinkt), damit der
Entwickler, die Prüfenden und der nächste Designer sie finden. Mockup-Dateien können im Designtool liegen; verlinken Sie
jede aus der Dokumentation des Themes.

## Verwandte Seiten

- [Vom Mockup zum Theme](vom-mockup-zum-theme.md)
- [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md)
- [Checkliste: Die Theme-Erweiterung ist bereit](../designer/erweiterungen/checkliste-theme-erweiterung-bereit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
