---
title: Inhalte zwischen Marken teilen
audience: businesses, editor, project-manager
category: business-solutions
order: 30
summary: Wie mehrere Marken in einer Exponential-Installation Inhalte teilen, ohne sie zu kopieren - getrennte Ordner, mehrere Platzierungen, Beziehungen und Einbettungen, gemeinsame Layouts für Kopf- und Fußzeile, gemeinsame Medien und Benutzer und wie Redakteure in ihrer Marke gehalten werden.
keywords: gemeinsame Inhalte, Wiederverwendung, Multisite, Marken, Platzierungen, Beziehungen, Einbettung, gemeinsame Layouts, Medien-Bibliothek, Benutzer, Rollen, Teilbaum, Unternehmen, shared content, reuse, multisite, brands, locations, relations, embed, shared layouts, media library, users, roles, subtree, business
type: help
---
# Inhalte zwischen Marken teilen

Diese Seite richtet sich an Markenverantwortliche, Content-Manager und Projektleiter, die mehrere Marken in einer
Exponential-Installation betreiben und manches einmal für alle veröffentlichen wollen: Produktinformationen,
Unternehmensnachrichten, ein Impressum, ein Pressekit, eine Filialsuche. Sie erklärt, auf welche Weise Inhalte ohne
Kopieren geteilt werden, was getrennt bleibt und wie Sie jedes Redaktionsteam in seinem eigenen Bereich halten.

## Inhalt

1. [Warum teilen statt kopieren](#warum-teilen-statt-kopieren)
2. [Die Wege des Teilens](#die-wege-des-teilens)
3. [Gemeinsame Kopfzeile, Fußzeile und Seitenteile](#gemeinsame-kopfzeile-fußzeile-und-seitenteile)
4. [Medien, Benutzer und Tags](#medien-benutzer-und-tags)
5. [Teams in ihrer Marke halten](#teams-in-ihrer-marke-halten)
6. [Entscheiden, was geteilt wird](#entscheiden-was-geteilt-wird)

## Warum teilen statt kopieren

Ein kopierter Text muss in jeder Kopie korrigiert werden. In einer Installation ist ein gemeinsames Element ein Objekt:
Korrigieren Sie es einmal, veröffentlichen Sie es, und jede Marke, die es zeigt, zeigt die Korrektur. Das ist am
wichtigsten bei Inhalten mit rechtlichem oder finanziellem Gewicht, etwa Preisen, Sicherheitshinweisen,
Geschäftsbedingungen und Kontaktdaten.

## Die Wege des Teilens

Alle Marken einer Installation arbeiten auf demselben Inhaltsbaum. Die Dokumentation für Website-Ersteller nennt drei
Wege, ein Objekt wiederzuverwenden ([Beziehungen und Wiederverwendung von Inhalten](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md#three-ways-to-reuse)):

| Weg | Verwenden, wenn | Ergebnis |
|---|---|---|
| Ein Beziehungsfeld | die Beziehung Teil des Inhaltsmodells ist („verwandte Produkte“, „Autor“, „Veranstaltungsort“) | ein typisiertes Feld, das Redakteure über eine Auswahl füllen |
| Eine Einbettung in Rich Text | ein Redakteur ein Bild oder einen Kasten in einem Text haben will | das Objekt wird an dieser Stelle im Text angezeigt |
| Mehrere Platzierungen | das Element an zwei Stellen des Menüs gehört | dasselbe Objekt unter zwei Adressen |

Für Marken sind **mehrere Platzierungen** die direkteste Form: Ein Produktobjekt, das im Produktordner von Marke A und von
Marke B platziert ist, erscheint auf beiden Websites, jeweils im Design der Website. **Beziehungen und Einbettungen**
eignen sich für Inhalte, die eine Marke von einer anderen zitiert oder anteasert.

Die andere Form ist eine **gemeinsame Wurzel mit unterschiedlichen Designs**: Zwei Siteaccesses zeigen denselben
Inhaltsbaum, jeder mit seinem eigenen Design ([Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md#shapes-of-a-multisite)).
Das passt zu einer Website für den Fachhandel und einer für Endkunden, die denselben Katalog unterschiedlich darstellen.

> **Warnung**
> Wenn die Seite einer Marke auf Inhalte verlinkt, die im Ordner einer anderen Marke liegen, muss der Link auf die
> Adresse der anderen Marke zeigen, sonst landen Besucher auf einem 404. Die Erweiterung `ezurlmultisite` erledigt das;
> siehe [Links zwischen Ihren Websites](links-zwischen-ihren-websites.md).

## Gemeinsame Kopfzeile, Fußzeile und Seitenteile

Mit Exponential Layouts kann ein Seitenlayout seine Zonen mit den Zonen eines **gemeinsamen Layouts** verknüpfen: Die
Fußzeile wird einmal definiert, und „eine neue Telefonnummer bedeutet“, eine Stelle zu bearbeiten statt jedes Layouts
([Gemeinsame Layouts: Kopfzeile, Fußzeile und Prefooter](../../../../eng-us/help/creators/website-creators/shared-header-and-footer.md)).
Wissenswerte Regeln:

- Eine Zone verknüpft sich mit einer Zone eines **veröffentlichten** gemeinsamen Layouts, nie mit einem Entwurf.
- Blöcke eines veröffentlichten gemeinsamen Layouts lassen sich nicht von einer Seite aus ändern, die darauf verlinkt;
  das gemeinsame Layout wird geöffnet, als Entwurf angelegt und dort bearbeitet.
- Eine Liste der gemeinsamen Layouts zeigt, wie viele Zonen mit jedem verknüpft sind, sodass Sie die Reichweite einer
  Änderung sehen, bevor Sie sie vornehmen.

Bei mehreren Marken kann ein gemeinsames Layout tragen, was alle Marken gemeinsam haben (rechtliche Links, den
Kontaktblock der Gruppe), während jede Marke ihre eigene Kopfzeile mit ihrem Logo behält.

## Medien, Benutzer und Tags

| Gemeinsames Element | Warum es hilft | Hinweis |
|---|---|---|
| Medien-Bibliothek (Bilder, Dateien) | Ein Logo-Satz und ein Produktfoto pro Artikel | Ordnen Sie sie nach Marke und Verwendung ([Die Medien-Bibliothek ordnen](../../../../eng-us/help/creators/website-creators/organizing-the-media-library.md)) |
| Benutzer und Gruppen | Ein Konto pro Person, eine Stelle, um jemanden zu sperren, der das Unternehmen verlässt | Rollen entscheiden, was jede Person in welcher Marke tun darf |
| Tags (`eztags`) | Ein Vokabular für Produkte, Themen oder Regionen über alle Marken | [Inhalte mit eztags verschlagworten](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md) |

Wenn Ihre Marken auch in mehreren Sprachen veröffentlichen, markieren Sie gemeinsame Ordner, Benutzer und Medien als
**immer verfügbar**, damit sie auf einem Sprach-Siteaccess auch ohne Übersetzung erscheinen
([Hauptsprache und immer verfügbar](../../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)).

## Teams in ihrer Marke halten

- Eine Administration kann jede Marke bedienen; Rollen, die **auf einen Teilbaum beschränkt** sind, halten jedes Team
  im Ordner seiner Marke ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- Ein gemeinsamer Ordner (Gruppennachrichten, Rechtliches) kann nur für ein zentrales Team bearbeitbar und für alle
  lesbar sein.
- Mehrere Administrations-Siteaccesses sind möglich, wenn Teams die Marken der anderen überhaupt nicht sehen sollen
  ([Redakteure und die Administration](../../../../eng-us/help/creators/website-creators/multisite-setup.md#editors-and-the-administration)).
- Ein Freigabe-Workflow kann Änderungen an gemeinsamen Inhalten zurückhalten, bis der Verantwortliche der Inhalte sie
  freigibt ([Workflows](../../../anleitungen/workflows.md)).

## Entscheiden, was geteilt wird

Verwenden Sie diese Tabelle in einem Workshop mit den Markenverantwortlichen:

| Inhalt | Gemeinsam, ein Objekt | Getrennt pro Marke | Verantwortlich |
|---|---|---|---|
| Unternehmensnachrichten | | | |
| Produktdaten und Preise | | | |
| Impressum, Datenschutz, Geschäftsbedingungen | | | |
| Kontaktdaten, Filialsuche | | | |
| Kampagnen und Landingpages | | | |
| Fotos und Logos | | | |

Faustregeln: Teilen Sie, was überall identisch sein muss (Fakten, Rechtstexte, Produktdaten), und halten Sie getrennt,
worüber jede Marke mit ihrer eigenen Stimme spricht (Kampagnen, Geschichten, Landingpages).

## Verwandte Seiten

- [Ein Design pro Marke](ein-design-pro-marke.md)
- [Links zwischen Ihren Websites](links-zwischen-ihren-websites.md)
- [Beziehungen und Wiederverwendung von Inhalten](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Gemeinsame Layouts: Kopfzeile, Fußzeile und Prefooter](../../../../eng-us/help/creators/website-creators/shared-header-and-footer.md)
- [Den Inhaltsbaum strukturieren](../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
