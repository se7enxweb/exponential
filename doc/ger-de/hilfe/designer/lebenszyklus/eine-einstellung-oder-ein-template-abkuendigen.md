---
title: "Eine Einstellung oder ein Template abkündigen"
audience: designer, developer
category: theme-lifecycle
order: 230
summary: Schritt für Schritt - eine Theme-Einstellung umbenennen und den alten Namen weiter lesen, ein Template ersetzen und ein Include des alten behalten und beides später entfernen - mit den Changelog-Einträgen zu jedem Schritt.
keywords: Einstellung abkündigen, Einstellung umbenennen, Rückfall, Template abkündigen, include, Kompatibilität, Entfernung, Changelog, deprecate setting, rename setting, fallback, deprecate template, compatibility, removal
type: help
---
# Eine Einstellung oder ein Template abkündigen

Diese Seite ist für Designer, die ihrer [Abkündigungsrichtlinie](abkuendigungsrichtlinie.md) folgen.

## Eine Einstellung

Beispiel: Aus `mytheme.ini [Front] Teasers` wird `[FrontPage] TeaserCount`.

1. Fügen Sie die neue Einstellung mit ihrem Standardwert in die Einstellungsdatei des Themes ein.
2. Lesen Sie in den Templates zuerst die neue, dann die alte:

   ```
   {def $count = ezini( 'FrontPage', 'TeaserCount', 'mytheme.ini' )}
   {if ezini_hasvariable( 'Front', 'Teasers', 'mytheme.ini' )}
       {set $count = ezini( 'Front', 'Teasers', 'mytheme.ini' )}
   {/if}
   ```

   (Ordnen Sie das Lesen so, dass ein Override des alten Namens durch einen Nutzer noch gewinnt, bis er umstellt.)
3. Changelog: `Renamed: mytheme.ini [Front] Teasers to [FrontPage] TeaserCount; the old name is read until 2.0.0`.
4. Entfernen Sie im angekündigten Release das Lesen des alten Namens: `Removed: mytheme.ini [Front] Teasers (use [FrontPage] TeaserCount)`.

## Ein Template

Beispiel: `parts/teaser.tpl` wird durch `parts/card.tpl` ersetzt.

1. Legen Sie `parts/card.tpl` an.
2. Machen Sie `parts/teaser.tpl` zu einem einzeiligen Include des neuen, mit einem Kommentar:

   ```
   {* Deprecated since 1.4.0, removed in 2.0.0: include design:parts/card.tpl instead. *}
   {include uri='design:parts/card.tpl' node=$node}
   ```

3. Changelog: `Added: parts/card.tpl` und `Updated: parts/teaser.tpl is deprecated, use parts/card.tpl`.
4. Entfernen Sie `parts/teaser.tpl` im angekündigten Release, mit dem Upgrade-Schritt für Nutzer, die es überschreiben.

## Verwandte Seiten

- [Einem Theme eine eigene Einstellungsdatei geben](../erweiterungen/eine-eigene-ini-datei.md)
- [Der Operator ezini](../vorlagen/der-ezini-operator.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
