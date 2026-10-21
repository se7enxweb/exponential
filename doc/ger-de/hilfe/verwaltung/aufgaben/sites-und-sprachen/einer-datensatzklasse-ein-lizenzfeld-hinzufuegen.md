---
title: Einer Datensatzklasse ein Lizenzfeld hinzufügen
audience: government, admin, website-creator
category: public-sector
order: 230
summary: Aufgabe für Administratoren - der Datensatzklasse eine verpflichtende Auswahl License mit den von Ihrer Behörde genehmigten Lizenzen hinzufügen und sie für bestehende Datensätze ausfüllen.
keywords: Lizenzfeld, Datensatzklasse, Auswahlliste, Pflichtfeld, offene Lizenz, Klasseneditor, license field, dataset class, selection datatype, ezselection, required field, open license, class editor
type: help
---
# Einer Datensatzklasse ein Lizenzfeld hinzufügen

Diese Aufgabe richtet sich an Administratoren. Hintergrund: [Datensätze lizenzieren](../../offene-daten/datensaetze-lizenzieren.md)
und [Inhaltsklassen für Websites](../../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md).

## Bevor Sie beginnen

Die Liste der genehmigten Lizenzen, jede mit ihrem offiziellen Namen und ihrer Version.

## Schritte

1. **Setup > Klassen**, Gruppe **Content**, die Klasse `dataset`, **Bearbeiten**.
2. Wählen Sie unter **Attribut hinzufügen** den Typ **Auswahlliste** und drücken Sie **Hinzufügen**.
3. Name `License`, Bezeichner `license`; kreuzen Sie **Erforderlich** an; entfernen Sie das Kreuz bei **Übersetzbar**.
4. Fügen Sie je genehmigter Lizenz eine Option hinzu, dazu „Nicht zur Weiterverwendung verfügbar“, wenn Sie
   geschlossene Datensätze beschreiben.
5. **OK**. Jeder bestehende Datensatz erhält ein leeres Lizenzfeld.
6. Öffnen Sie jeden bestehenden Datensatz, wählen Sie seine Lizenz und veröffentlichen Sie.

## Prüfen

Ein neuer Datensatz kann nicht ohne Lizenz veröffentlicht werden; die Datensatzseite und die Felder der REST-API
zeigen sie.

## Probleme

| Problem | Ursache |
|---|---|
| Bestehende Datensätze bleiben leer | ein neues Feld füllt sich nicht selbst; bearbeiten Sie jeden einzeln |
| Sie brauchen den Lizenztext auf der Seite | fügen Sie ein URL-Feld `license_url` hinzu oder verknüpfen Sie eine Lizenzseite |

## Verwandte Seiten

- [Eine Datensatzklasse entwerfen](../../offene-daten/eine-datensatzklasse-entwerfen.md)
