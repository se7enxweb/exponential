---
title: Ihre Website planen
audience: website-creator, project-manager
category: website-creation
order: 40
summary: Planen Sie eine Website auf Exponential in sieben Schritten: Ziele, Zielgruppen, Inhaltsmodell, Websites und Sprachen, Design, Funktionen und Betrieb, mit einem einseitigen Planungsbogen zum Ausfüllen.
keywords: planen, Planung, Website-Plan, Anforderungen, Ziele, Zielgruppen, Inhaltsmodell, Siteaccess, Sprachen, Design, Start, Betrieb, Planungsbogen, Website-Ersteller, plan, planning, requirements, goals, audiences, content model, languages, launch, operations, planning sheet, website creator
type: help
---
# Ihre Website planen

Diese Seite richtet sich an Website-Ersteller am Anfang einer neuen Website. Sie macht aus den Entscheidungen, die
Exponential von Ihnen verlangt, sieben Planungsschritte in der Reihenfolge, in der das Produkt die Antworten braucht,
und endet mit einem Planungsbogen zum Ausfüllen. Projektleiter, die größere Projekte führen, finden dieselben Schritte
mit Rollen und Budgets unter [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md).

## Inhalt

1. [Warum die Reihenfolge zählt](#warum-die-reihenfolge-zählt)
2. [Schritt 1: Ziele und Zielgruppen](#schritt-1-ziele-und-zielgruppen)
3. [Schritt 2: Inhaltsbestandsaufnahme und Sitemap](#schritt-2-inhaltsbestandsaufnahme-und-sitemap)
4. [Schritt 3: das Inhaltsmodell](#schritt-3-das-inhaltsmodell)
5. [Schritt 4: Websites, Adressen und Sprachen](#schritt-4-websites-adressen-und-sprachen)
6. [Schritt 5: Design](#schritt-5-design)
7. [Schritt 6: Funktionen](#schritt-6-funktionen)
8. [Schritt 7: Hosting und Betrieb](#schritt-7-hosting-und-betrieb)
9. [Der Planungsbogen](#der-planungsbogen)

## Warum die Reihenfolge zählt

Manche Entscheidungen lassen sich später günstig ändern, andere nicht:

| Entscheidung | Später ändern | Warum |
|---|---|---|
| Design, Templates, Stylesheets | Einfach | Templates sind Dateien; ein Override lässt sich entfernen ([Template-Overrides](template-overrides.md)) |
| Menüs, Layouts von Landingpages | Einfach | Einstellungen und der Layouts-Editor |
| Inhaltsklassen (Felder hinzufügen) | Einfach | Ein neues Attribut wird jedem Objekt der Klasse hinzugefügt |
| Inhaltsklassen (Datentyp eines Feldes ändern) | Schwer | Die gespeicherten Daten gehören zum Datentyp; eine Änderung bedeutet, Inhalte zu migrieren |
| Siteaccess-Aufteilung (URL, Host, Port) | Mittel | Einstellungen, aber Links, Zertifikate und Suchmaschinen hängen daran |
| Sprachen | Mittel | Hinzufügen ist einfach; das Entfernen einer Übersetzung löscht Inhalte ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)) |
| Datenbank-Engine | Schwer | Ein Wechsel bedeutet einen Export und einen Import |

Planen Sie also das Inhaltsmodell, die Sprachen und die Datenbank mit Sorgfalt, und halten Sie das Design flexibel.

## Schritt 1: Ziele und Zielgruppen

Schreiben Sie jeweils in einem Satz auf: wofür die Website da ist, wer sie besucht, was die Besucher dort tun sollen
(lesen, Kontakt aufnehmen, kaufen, abonnieren, sich anmelden) und woran Sie erkennen, dass sie funktioniert (siehe
[Besucherstatistik und Analyse](besucherstatistik-und-analyse.md)).

## Schritt 2: Inhaltsbestandsaufnahme und Sitemap

Listen Sie auf, welche Inhalte es gibt und welche geschrieben werden, und zeichnen Sie den Baum. Die Methode steht unter
[Sitemap und Inhaltsbestandsaufnahme](sitemap-und-inhaltsbestandsaufnahme.md). Das Ergebnis bestimmt die Ordner des
Inhaltsbaums ([Den Inhaltsbaum strukturieren](den-inhaltsbaum-strukturieren.md)).

## Schritt 3: das Inhaltsmodell

Fassen Sie den Bestand zu Arten von Inhalten zusammen, und geben Sie jeder Art eine Liste von Feldern. Bevorzugen Sie
die Klassen, die ein Seitenpaket bereits mitliefert (Article, Folder, Image, File, Product ...), und ergänzen Sie nur,
was fehlt. Methode und Beispiele: [Ihr Inhaltsmodell entwerfen](ihr-inhaltsmodell-entwerfen.md),
[Inhaltsklassen für Websites](inhaltsklassen-fuer-websites.md), [Datentypen wählen](datentypen-waehlen.md).

## Schritt 4: Websites, Adressen und Sprachen

Entscheiden Sie:

- **Wie viele öffentliche Websites** (Marken, Regionen) aus einer Installation betrieben werden. Jede ist ein oder
  mehrere Siteaccesses ([Multisite einrichten](multisite-einrichten.md)).
- **Wie Besucher jede erreichen**: über den Pfad (`/de`), über den Host (`www.example.de`) oder über den Port; der
  Installer bietet alle drei an
  ([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
- **Welche Sprachen**, und welche Sprache jede Website zuerst zeigt ([Eine mehrsprachige Website bauen](mehrsprachige-websites.md)).
- **Von rechts nach links?** Arabische oder hebräische Inhalte brauchen ein RTL-fähiges Design
  ([Websites mit Schreibrichtung von rechts nach links](websites-von-rechts-nach-links.md)).

## Schritt 5: Design

Wählen Sie einen Ausgangspunkt: ein mitgeliefertes Theme, ein Theme, das Sie anpassen, oder Ihr eigenes Design in einer
Erweiterung ([Ein Design oder Theme wählen](ein-design-oder-theme-waehlen.md)). Planen Sie Barrierefreiheit von Anfang an
ein ([Barrierefreiheit für Website-Ersteller](barrierefreiheit-fuer-website-ersteller.md)).

## Schritt 6: Funktionen

Haken Sie ab, was die Website braucht; jede Zeile ist eine Seite dieses Abschnitts:

| Funktion | Seite |
|---|---|
| Visuell bearbeitete Landingpages | [Landingpages mit dem Layouts-Editor](landingpages-mit-dem-layouts-editor.md) |
| Kontakt- oder Feedbackformulare | [Formulare mit dem Informationssammler](formulare-mit-dem-informationssammler.md) |
| Newsletter | [Newsletter mit cjw_newsletter](newsletter-mit-cjw-newsletter.md) |
| Shop | [Einen Shop betreiben: Überblick](shop-ueberblick.md) |
| Suche | [Suche auf der Website](suche-auf-der-website.md) |
| Mitgliederbereich | [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](mitgliederregistrierung-und-mitgliederbereiche.md) |
| RSS | [RSS-Feeds für Ihre Website](rss-feeds-fuer-ihre-website.md) |
| Tags | [Verschlagworten mit eztags](verschlagworten-mit-eztags.md) |
| Eigene Logik | [Anwendungsentwickler: hier beginnen](../anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) |

## Schritt 7: Hosting und Betrieb

Entscheiden Sie, wo die Website läuft und wer sie betreut: Webserver (Velocity, Apache oder nginx), Datenbank, Backups,
Cronjobs, Updates ([Hosting für eine Website](hosting-fuer-eine-website.md), [Ihre Website pflegen](ihre-website-pflegen.md)).

## Der Planungsbogen

Kopieren Sie diesen Bogen in Ihre Projektnotizen und füllen Sie ihn aus.

| Frage | Antwort |
|---|---|
| Zweck der Website, in einem Satz | `<answer>` |
| Besucher und was sie tun | `<answer>` |
| Arten von Inhalten (Klassen) | `<answer>` |
| Anzahl der öffentlichen Websites und ihre Adressen | `<answer>` |
| Sprachen, erste Sprache je Website | `<answer>` |
| Design: mitgeliefertes Theme, angepasstes Theme oder eigenes | `<answer>` |
| Funktionen (Formulare, Newsletter, Shop, Suche, Mitglieder, Layouts) | `<answer>` |
| Datenbank und Webserver | `<answer>` |
| Wer bearbeitet, wer gibt frei, wer administriert | `<answer>` |
| Starttermin und wer ihn abnimmt | `<answer>` |

> **Tipp**
> Installieren Sie zuerst eine Demo-Website, und klicken Sie sich mit den Personen hindurch, die die Website bearbeiten
> werden. Wer die Administration gesehen hat, kann die Hälfte der Fragen oben beantworten
> ([Ihre erste Website Schritt für Schritt](ihre-erste-website-schritt-fuer-schritt.md)).

## Verwandte Seiten

- [Sitemap und Inhaltsbestandsaufnahme](sitemap-und-inhaltsbestandsaufnahme.md)
- [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md)
- [Workshops zur Inhaltsmodellierung durchführen](../../fuehrung/workshops-zur-inhaltsmodellierung.md)
- [Checkliste für den Start](checkliste-fuer-den-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
