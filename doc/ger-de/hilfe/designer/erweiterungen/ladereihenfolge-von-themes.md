---
title: "Ladereihenfolge und welches Theme gewinnt"
audience: designer, admin
category: theme-extensions
order: 180
summary: Liefern zwei Erweiterungen dasselbe Template oder dieselbe Einstellung, entscheidet die Ladereihenfolge, welche gewinnt; wie die Reihenfolge festgelegt wird, wie ExtensionOrdering und die Abhängigkeiten in extension.xml sie ändern und wie Sie sie prüfen.
keywords: Ladereihenfolge, Priorität, Reihenfolge von ActiveExtensions, ExtensionOrdering, extends, requires, uses, Template-Konflikt, welches Theme gewinnt, loading order, priority, template conflict, which theme wins
type: help
---
# Ladereihenfolge und welches Theme gewinnt

Diese Seite ist für Designer, deren Theme Templates einer anderen Erweiterung überschreibt (zum Beispiel eines
Basis-Themes), und für Administratoren, die mehrere Themes kombinieren.

## Die Regel

Die Reihenfolge der aktiven Erweiterungen ist die Rangfolge: **Eine früher geladene Erweiterung gewinnt über eine
spätere**, bei Einstellungen und bei Design-Templates.

## Was die Reihenfolge bestimmt

1. Die Reihenfolge der Zeilen `ActiveExtensions[]` (und `ActiveAccessExtensions[]` für einen Siteaccess).
2. Mit `site.ini [ExtensionSettings] ExtensionOrdering=enabled` (der Standard in `settings/site.ini`) ordnen die in der
   `extension.xml` jeder aktiven Erweiterung deklarierten Abhängigkeiten diese Liste zuerst um:

   | In Ihrer `extension.xml` deklariert | Wirkung auf das Laden |
   |---|---|
   | `<extends><extension name="base_theme" /></extends>` | Ihr Theme lädt **vor** `base_theme` und gewinnt damit |
   | `<requires>` oder `<uses>` | Ihr Theme lädt **nach** der genannten Erweiterung |

   Siehe [Abhängigkeiten in extension.xml deklarieren](abhaengigkeiten-in-extension-xml.md). Der Kommentar in `site.ini`
   nennt die bekannten Grenzen: Die Sortierung behebt keine Reihenfolgeprobleme zwischen `ActiveExtensions` und
   `ActiveAccessExtensions`, und sie lädt eine Erweiterung auch dann, wenn deren Abhängigkeit nicht aktiv ist.

## Schritt für Schritt: Ihr Theme über ein Basis-Theme gewinnen lassen

1. Fügen Sie in Ihrer `extension.xml` hinzu:

   ```xml
   <dependencies>
       <extends>
           <extension name="base_theme" />
       </extends>
   </dependencies>
   ```

2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. Öffnen Sie **Setup > Extensions**: Jede Karte zeigt, wo die Erweiterung tatsächlich lädt.
4. Öffnen Sie eine Seite, für die beide Themes ein Template haben. Bei eingeschaltetem Template-Debugging nennt der
   Pfadkommentar Ihre Datei.

## Wenn Sie es nicht deklarieren können

Kontrollieren Sie die andere Erweiterung nicht oder stimmt die Reihenfolge immer noch nicht, verschieben Sie Ihr Theme
in der Liste unter **Setup > Extensions** über die andere (die Reihenfolge wird in der Liste geändert, geprüft und dann
geschrieben).

## Verwandte Seiten

- [Setup > Extensions: Ladereihenfolge und sicheres Speichern](../../../../eng-us/features/6.0/extension-loading-order.md)
- [Die Design-Kaskade](../../../../eng-us/help/designers/theme-design/the-design-cascade.md)
- [Override-Reihenfolge](../templates/override-reihenfolge.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
