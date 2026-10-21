---
title: Redakteure beim Design admin4 halten
audience: government, admin
category: public-sector
order: 450
summary: Schritt für Schritt - admin4, das Administrationsdesign, das Exponential gegen WCAG 2.2 AA testet, zum Design des Siteaccess der Administration machen und den Designwechsel je Sitzung einschränken, damit Mitarbeiter mit Behinderungen in der getesteten Umgebung bleiben.
keywords: admin4, admin4l, Administrationsdesign, SiteDesign, Designwechsel, uiswitch, Barrierefreiheit, WCAG 2.2, klassische Administration, Redakteure, assistive Technologien, öffentlicher Sektor, administration design, design switch, accessibility, classic admin, editors, assistive technology, public sector
type: help
---
# Redakteure beim Design admin4 halten

Für Administratoren. Die Arbeit des Projekts an der Barrierefreiheit und seine Tests betreffen admin4 und admin4l; die
klassische Administration und admin3 sind nur teilweise korrigiert
([Was admin4 von Exponential abdeckt](../../../../../eng-us/help/government/accessibility/admin4-what-is-covered.md)). Eine Erklärung zur
Barrierefreiheit oder eine Antwort in einer Beschaffung zur Bearbeitungsumgebung gilt nur, wenn die Redakteure admin4
verwenden.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Rechte, die Einstellungen des Siteaccess der Administration zu ändern (oder die versionierten Einstellungen mit
  `ManagedByDeployment`).
- Wissen Sie, welche Administrationsseiten von Erweiterungen Ihre Redakteure verwenden; manche gibt es nur in den
  klassischen Designs.

## Schritte

1. In `site.ini.append.php` des Siteaccess der Administration:

   ```ini
   [DesignSettings]
   SiteDesign=admin4
   ```

   ([Das Design admin4: darauf umstellen](../../../../../eng-us/features/6.0/admin4-design.md#switch-to-it)).
2. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-all --allow-root-user`; laden Sie PHP neu oder führen Sie auf
   Velocity ein Deployment aus.
3. Wenn der Designwechsel je Sitzung angeboten wird, beschränken Sie seine Liste in
   `settings/override/uiswitch.ini.append.php` (`[UISwitch_design] AllowedValues[]`) auf admin4 und admin4l und behalten
   Sie in jedem angebotenen Design einen Weg zurück ([Der Designwechsel der Administration](../../../../anleitungen/ui-umschalter-design.md)).
   Oder geben Sie die Richtlinie `uiswitch/design` nur denen, die andere Designs brauchen.
4. Informieren Sie die Redakteure über die [Tastenkürzel](../../../tastenkuerzel.md) und die Farbmodi hell, dunkel
   und System.

## Prüfen, ob es funktioniert hat

- Der Kopfbereich der Administration zeigt den Umschalter für den Farbmodus; der erste Tab zeigt
  „Zum Hauptinhalt springen“.
- Ein Test-Redakteur kann nicht zu einem Design außerhalb der Liste wechseln.

## Verwandte Seiten

- [Was admin4 von Exponential abdeckt](../../../../../eng-us/help/government/accessibility/admin4-what-is-covered.md)
- [Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md)
- [Einstellungen des UI-Umschalters](../../../../anleitungen/ui-umschalter-einstellungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
