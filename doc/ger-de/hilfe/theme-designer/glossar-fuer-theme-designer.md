---
title: "Glossar für Theme-Designer"
audience: theme-designer
category: theme-design
order: 43
summary: Die Begriffe, denen Theme-Designer in diesem Bereich begegnen und die das Design-Glossar nicht schon erklärt - Bild-Alias, Ansichtstyp, Layouttyp, gemeinsames Layout, Remote-ID, Vorschau-Siteaccess, Skin, Farbabstufung, Rollen-Token, Zustandsmatrix und weitere - jeweils mit einem Link auf die Stelle, an der er erklärt wird.
keywords: Theme-Designer, Glossar, Begriffe, Definitionen, Bild-Alias, Ansichtstyp, Layouttyp, Remote-ID, Vorschau-Siteaccess, Skin, Farbabstufung, Rollen-Token, Design-Engine, theme designer, glossary, terms, definitions, image alias, view type, layout type, remote id, preview siteaccess, skin, ramp, role token, design engine
type: help
---
# Glossar für Theme-Designer

Diese Seite ist für Theme-Designer, die schnell die Bedeutung eines Worts nachschlagen möchten. Sie ergänzt die Begriffe
aus den Seiten für Theme-Designer, die das [Design-Glossar](../designer/theme-design/design-glossar.md) nicht schon
abdeckt. Für Attribut, Block, Kaskade, Klasse, Datentyp, Design, Knoten, Objekt, Override, Packer, Pagelayout,
Siteaccess, Token, Ansichtsmodus, Zone und die anderen Grundbegriffe lesen Sie zuerst jenes Glossar.

## Inhalt

1. [Inhalte und Templates](#inhalte-und-templates)
2. [Layouts](#layouts)
3. [Bilder und Medien](#bilder-und-medien)
4. [Designsystem](#designsystem)
5. [Ablauf](#ablauf)
6. [Exponential Platform DXP](#exponential-platform-dxp)

## Inhalte und Templates

**Feldbeschreibung (field description).** Ein kurzer Text pro Feld einer Inhaltsklasse, der neben dem Feld im
Bearbeitungsformular erscheint. Ein Ort für Anleitung an Redakteure. Siehe
[Für Redakteure gestalten](fuer-redakteure-gestalten.md#redakteuren-sagen-was-das-design-erwartet).

**Informationssammlung (information collection).** Felder einer Inhaltsklasse, die Eingaben von Besuchern sammeln; so
werden Feedback- und Kontaktformulare meist gebaut. Siehe [Formulare gestalten](formulare-gestalten.md).

**Locale.** Die Sprache und das regionale Format eines Siteaccess, die bestimmen, wie Daten und Zahlen ausgegeben
werden. Siehe [Daten, Zahlen und l10n](../designer/vorlagen/datum-zahlen-und-l10n.md).

**Vorschau-Siteaccess (preview siteaccess).** Ein Siteaccess, der die Live-Inhalte mit einem Design in Entwicklung unter
einer eigenen Adresse ausliefert. Siehe [Ein Design auf einem Siteaccess in der Vorschau ansehen](../designer/theme-design/design-vorschau-auf-einem-siteaccess.md).

**Remote-ID (remote id).** Ein Textbezeichner eines Objekts oder Knotens, der gleich bleibt, wenn Inhalte anderswo
installiert werden; wird verwendet, um Overrides einer bestimmten Seite zuzuordnen. Siehe
[Einen einzelnen Knoten überschreiben](../designer/vorlagen/einen-einzelnen-knoten-ueberschreiben.md).

**Sektion (section).** Eine Kennzeichnung eines Teils des Inhaltsbaums, verwendet für Zugriffsrechte und für Overrides
nach Bereich. Siehe [Override nach Sektion](../designer/vorlagen/override-nach-sektion.md).

**Kommentare mit Template-Pfaden (template path comments).** HTML-Kommentare um jedes ausgegebene Template, die seine
Datei nennen, auf einem Entwicklungs-Siteaccess mit `ShowTemplatePathComments` eingeschaltet. Siehe
[Kommentare mit Template-Pfaden](../designer/vorlagen/template-pfad-kommentare.md).

**Template-Teil (template part).** Ein kleines Template, das von anderen eingebunden wird, der übliche Ort für das Markup
einer Komponente. Siehe [Komponenteninventar](komponenteninventar.md#vom-inventar-zum-code).

## Layouts

**Blocktyp (block type).** Eine Art von Layoutblock (Text, Liste, Galerie, Hero ...), in den Layout-Einstellungen mit
Handler und Ansichtstypen definiert. Siehe [Blockdefinitionen](../designer/theme-design/blockdefinitionen.md).

**Sammlung (collection).** Die Inhalte, die ein listenartiger Block zeigt, von Hand ausgewählt oder per Abfrage
gefunden. Siehe [Layouts für Designer](../designer/theme-design/layouts-fuer-designer.md).

**Container-Block (container block).** Ein Block, der andere Blöcke an benannten Stellen enthält, etwa in zwei oder drei
Spalten. Siehe [Layoutblöcke entwerfen](layoutbloecke-gestalten.md#container).

**Layouttyp (layout type).** Ein Seitengerüst mit benannten Zonen, gewählt, wenn ein Layout angelegt wird. Siehe
[Layouttypen und Zonen](../designer/theme-design/layouttypen-und-zonen.md).

**Gemeinsames Layout (shared layout).** Ein Layout, dessen Zonen in viele andere Layouts verknüpft sind, oft für Inhalte
im Kopfbereich oder vor dem Fußbereich. Siehe [Gemeinsame Layouts und verknüpfte Zonen](../designer/theme-design/gemeinsame-layouts-und-verknuepfte-zonen.md).

**Ansichtstyp (view type).** Eine der Darstellungen, die ein Blocktyp anbietet; Redakteure wählen sie pro Block. Ein
Template und eine Zeile Einstellungen, kein PHP. Siehe [Layoutblöcke entwerfen](layoutbloecke-gestalten.md#blocktypen-und-ansichtstypen).

## Bilder und Medien

**Art Direction.** Bei verschiedenen Bildschirmbreiten einen anderen Zuschnitt oder ein anderes Bild zeigen. Siehe
[Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md#art-direction).

**Bild-Alias (image alias).** Eine benannte, erzeugte Variante eines hochgeladenen Bildes, hergestellt von Filtern wie
"zum Einpassen verkleinern". Siehe [Bildaliase](../designer/theme-design/bildaliase.md).

**Open-Graph-Tags.** Die `og:*`-Tags im Kopf einer Seite, die soziale Netzwerke lesen, um eine Vorschaukarte zu bauen.
Siehe [Bilder zum Teilen und für Metadaten](bilder-fuer-social-sharing-und-metadaten.md).

**Sicherer Bereich (safe area).** Der mittlere Teil eines Bildes zum Teilen, der jeden Zuschnitt eines Netzwerks
übersteht. Siehe [Bilder zum Teilen und für Metadaten](bilder-fuer-social-sharing-und-metadaten.md#das-bild-zum-teilen).

**Skin (Newsletter).** Das Paar von Templates (HTML-Teil und Textteil), das Newsletter-E-Mails in der Erweiterung CJW
Newsletter ihr Aussehen gibt. Siehe [Newsletter-E-Mails gestalten](newsletter-e-mails-gestalten.md).

## Designsystem

**Markenmatrix (brand matrix).** Eine Tabelle dessen, was in einer Installation mit mehreren Marken pro Marke variiert.
Siehe [Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md#die-markenmatrix).

**Komponenteninventar (component inventory).** Die Tabelle aller wiederverwendbaren Teile eines Themes mit ihren
Varianten, Zuständen, Tokens, Template und Klasse. Siehe [Komponenteninventar](komponenteninventar.md).

**Farbabstufung (ramp).** Eine Marken- oder neutrale Farbe in etwa zehn Helligkeitsstufen, aus der Rollen-Tokens gewählt
werden. Siehe [Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md).

**Rollen-Token (role token).** Ein Token, der nach seiner Verwendung (`--site-ink`, `--site-accent`) statt nach seinem
Wert benannt ist. Siehe [Design-Tokens](../designer/theme-design/design-tokens.md#benennung).

**Zustandsmatrix (state matrix).** Eine Tabelle der interaktiven Zustände jeder Komponente, hell und dunkel gezeichnet.
Siehe [Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md#die-zustandsmatrix).

**Typografische Skala (type scale).** Die kurze Liste von Schriftgrößen, die ein Theme verwendet. Siehe
[Typografische Skala](typografische-skala.md).

## Ablauf

**Übergabe (handoff).** Ein Design an die Menschen weitergeben, die es bauen, mit allem schriftlich festgehalten, was sie
brauchen. Siehe [Checkliste zur Übergabe](checkliste-zur-uebergabe.md).

**Zuordnungsblatt (mapping sheet).** Die Tabelle, die jedes Element eines Mockups mit einer Klasse, einem Feld und einer
Ansicht verbindet. Siehe [Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md#das-zuordnungsblatt).

**Stressfälle (stress set).** Testobjekte mit extremen Inhalten, die jedes Design überstehen muss. Siehe
[Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md#die-stressfälle).

**Styleguide-Seite (style guide page).** Eine Seite der Website, die jeden Token und jede Komponente zeigt, gezeichnet
vom eigenen Code des Themes. Siehe [Eine Styleguide-Seite bauen](eine-styleguide-seite-bauen.md).

**Ansichtsmatrix (view matrix).** Eine Tabelle aus Inhaltsklassen und Ansichtsmodi, die markiert, welche Kombinationen
ein Design brauchen. Siehe [Ansichtsmodi gestalten](ansichtsmodi-gestalten.md#die-ansichtsmatrix).

## Exponential Platform DXP

**Design (DXP).** Eine geordnete Liste von Themes; das erste Theme, das ein Template hat, gewinnt. Siehe
[Twig-Templates und die Design-Engine](../dxp-entwickler/twig-templates-und-die-design-engine.md).

**@ibexadesign.** Der Twig-Namensraum, über den DXP-Templates auf Theme-Templates verweisen, aufgelöst von der
Design-Engine. Siehe [Themes für Exponential Platform](themes-fuer-exponential-platform.md).

**Theme (DXP).** Ein Ordner mit Twig-Templates unter `templates/themes/<theme>/`. Siehe
[Das Standarddesign und Ihr Theme](../dxp-entwickler/das-standarddesign-und-ihr-theme.md).

## Verwandte Seiten

- [Design-Glossar](../designer/theme-design/design-glossar.md)
- [Häufige Fragen von Theme-Designern](faq-fuer-theme-designer.md)
- [Theme-Designer: hier beginnen](theme-designer-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
