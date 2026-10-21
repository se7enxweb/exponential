---
title: "Beispiele zur Migration, alle Seiten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1499
summary: Die 34 Beispiele der Migrationsphase im Leben einer Site, jedes eine Aufgabe, im Layout-Editor und mit Templates erledigt. Layouts, Einstellungen und Templates zwischen Installationen, Versionen und Sites verschieben.
keywords: migrieren, Migration, Beispiele, Lebenszyklus, Layouts, Templates, zwei Wege zum Bauen, migrate, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Beispiele zur Migration, alle Seiten

Layouts, Einstellungen und Templates zwischen Installationen, Versionen und Sites verschieben.

1. [Alle Layouts auf eine andere Installation übertragen](alle-layouts-auf-andere-installation.md): exp:layouts export --all auf der Quelle, Import mit Probelauf auf dem Ziel; Inhalte werden über Remote-ID und Pfad zugeordnet.
2. [Nur einige Layouts übertragen](nur-einige-layouts.md): --only mit den Bezeichnern der Layouts; die gemeinsamen Layouts, mit denen sie verknüpft sind, kommen mit.
3. [Die Zuordnungen mit den Layouts übertragen](zuordnungen-mit-den-layouts.md): --rules=none|selected|all entscheidet, welche Layout-Zuordnungen mitreisen; selected (der Standard) nimmt die der gewählten Layouts.
4. [Wenn dem Ziel Inhalte fehlen](wenn-dem-ziel-inhalte-fehlen.md): --unmapped=drop|keep|fail entscheidet, was mit Verweisen auf Inhalte geschieht, die das Ziel nicht hat (Standard drop).
5. [Neben den vorhandenen Layouts importieren](als-kopie-importieren.md): --strategy=copy importiert bereits vorhandene Einträge als neue Kopien und lässt die Originale stehen.
6. [Die Strategie pro Layout wählen](strategie-pro-layout.md): --item=<key>=<strategy>,... setzt skip, overwrite oder copy pro Eintrag, mit den Schlüsseln, die der Probelauf ausgibt.
7. [Layouts im Projekt-Repository aufbewahren](layouts-im-repository.md): Exporte neben der Design-Erweiterung committen; die layouts.json im Archiv lässt sich zwischen Versionen vergleichen.
8. [Die Einstellungen einer Site in ihre Erweiterung verschieben](einstellungen-in-die-erweiterung.md): exp:ini move / move-all verschieben Blöcke oder ganze Siteaccess-Geltungsbereiche in eine Erweiterung (--create-extension --activate), mit Probelauf.
9. [Die Menü-Einstellungen in einen anderen Siteaccess kopieren](menue-einstellungen-kopieren.md): exp:ini copy kopiert eine Einstellung (ein ganzes Array mit seiner Zurücksetz-Zeile) von einem Geltungsbereich in einen anderen.
10. [Die Einstellungsdateien einer anderen Wurzel ändern](einstellungen-einer-anderen-wurzel.md): exp:ini --root=<dir> arbeitet auf dem Einstellungsbaum einer anderen Wurzel (einer Kopie der Installation); der INI-Cache dort wird nicht geleert, und der Geltungsbereich muss dort existieren.
11. [Gespeicherte Bezeichner von Komponentenblöcken aktualisieren](bezeichner-von-komponentenbloecken.md): Layouts von vor der Umbenennung speichern Komponenten als ibexa_component_<type>; ext:explayouts:updatecomponentblockidentifiers benennt sie in exp_component_<type> um.
12. [Gespeicherte Block-Links auf Remote-IDs umstellen](block-links-auf-remote-ids.md): ext:explayouts:updatelinklocationreferences wandelt Block-Links von ibexa-location://<id> in exp-remote-location://<remote id> um.
13. [exp:layouts statt der alten Hilfsskripte verwenden](exp-layouts-statt-alter-hilfsskripte.md): Mehrere ext:explayouts:*-Helfer (block_list, layout_info, layout_copy, layout_list, rule_list, share_create, collection_list) brachen mit einem Fehler „class not found“ ab; exp:layouts deckt Auflisten und Übertragen ab.
14. [Eine mit Templates gebaute Site zu Layouts migrieren](template-site-zu-layouts.md): Das Design behalten; die Teile des Seitenlayouts zu Template-Blöcken eines gemeinsamen Header / Footer machen; Seitenlayouts pro Klasse hinzufügen; sie zuordnen.
15. [Eine Layouts-Site in Templates einfrieren](layouts-site-einfrieren.md): Wenn sich eine Site nicht mehr ändern soll (ein Archiv), können ihre Layouts bleiben; Einfrieren heißt weniger Redakteure, nicht neu schreiben.
16. [Eine Sprache hinzufügen und ihr Layouts geben](sprache-hinzufuegen.md): Ein neuer Siteaccess bekommt seine Einstellungen (menu.ini) mit exp:ini copy und seine eigenen Layouts über Zuordnungen mit einer Siteaccess-Bedingung.
17. [Inhalte an eine andere Stelle im Baum verschieben](inhalte-im-baum-verschieben.md): Eine Verschiebung behält die Knoten-IDs: Menüs, Ort-Zuordnungen und manuelle Sammlungen folgen; Ziele Pfad und Pfadpräfix nicht.
18. [Seitenpakete und Layouts](seitenpakete-und-layouts.md): Die Seitenpakete dieser Installation bringen Inhalte, aber keine Layouts mit; installieren Sie die Layouts mit exp:layouts import nach den Inhalten.
19. [Layouts nach einem Upgrade prüfen](layouts-nach-upgrade-pruefen.md): Nach dem Upgrade von Exponential exportieren, auflisten, die wichtigsten Layouts im Editor öffnen und die Seiten vergleichen.
20. [Ihre Template-Overrides nach einem Upgrade prüfen](template-overrides-nach-upgrade.md): Ihre Kopien von Theme-Templates bekommen die Korrekturen des Themes nicht; vergleichen Sie sie mit den neuen Theme-Versionen.
21. [Die Reihenfolge der Erweiterungen nach dem Verschieben von Einstellungen prüfen](reihenfolge-der-erweiterungen-pruefen.md): Nach dem Verschieben von Einstellungen oder dem Hinzufügen von Erweiterungen DesignExtensions[] und AvailableBlocks[] in ihrer Wirkung prüfen.
22. [Unterschiedliche Knoten-IDs auf Staging und Produktion](knoten-ids-auf-staging-und-produktion.md): Knoten-IDs unterscheiden sich zwischen Installationen; Menüs (MainMenuID) brauchen die IDs des Ziels, Layouts werden über Remote-ID und Pfad zugeordnet.
23. [Layouts umbenennen, ohne Importe zu brechen](layouts-umbenennen.md): Importe ordnen Layouts über den Bezeichner zu; halten Sie Bezeichner stabil und ändern Sie Namen nach Belieben.
24. [Eine Installation in zwei Sites aufteilen](installation-aufteilen.md): Die Layouts des Teilbaums der Site exportieren und auf der neuen Installation nach deren Inhalten importieren.
25. [Layouts aus zwei Installationen zusammenführen](layouts-zweier-installationen-zusammenfuehren.md): Die Layouts der zweiten Site mit --strategy=copy importieren, wo Bezeichner kollidieren, dann umbenennen und neu zuordnen.
26. [Ein durch einen Import beschädigtes Layout retten](beschaedigtes-layout-retten.md): Den vorherigen Export mit overwrite und publish importieren; Inhalte leeren.
27. [Ein fest eingebautes Menü in Einstellungen überführen](fest-eingebautes-menue-in-einstellungen.md): Ein altes Template mit eingetippten Links wird zu einer Liste MainMenuID[], die main_menu.tpl des Themes liest.
28. [Kopierte HTML-Snippets durch einen Blocktyp ersetzen](html-snippets-durch-blocktyp-ersetzen.md): Viele HTML-snippet-Blöcke mit demselben Markup werden zu einem Blocktyp; ersetzen Sie sie einen nach dem anderen.
29. [Inline-Styles ins Stylesheet verschieben](inline-styles-ins-stylesheet.md): style="" und <style> in alten Templates kommen in Ihr Stylesheet und werden mit dem des Themes gepackt.
30. [Festhalten, was die Migration geändert hat](migration-festhalten.md): Verschobene Einstellungen, importierte Layouts und ihre Strategien, hinzugefügte oder entfernte Overrides auflisten, für die nächste Migration.
31. [Velocity nach einer Migration](velocity-nach-der-migration.md): Auf einer Site, die Exponential Velocity ausliefert, brauchen Änderungen an Einstellungen exp:velocity restart, wie exp:ini sagt.
32. [Die Migration auf einer Kopie proben](migration-auf-einer-kopie-proben.md): Führen Sie jeden Befehl zuerst auf einer Kopie aus; die Probeläufe auf dem Live-System bergen dann keine Überraschungen.
33. [Den Weg zurück planen](weg-zurueck-planen.md): Exporte der Layouts, Sicherungen der INI-Dateien (exp:ini legt sie unter var/backup/ini/ ab) und das vorherige Release der Erweiterung.
34. [Die Checkliste nach einer Migration](checkliste-nach-der-migration.md): Seiten nach Markup, Menüs, Zuordnungen, Entwürfe, Caches, Velocity: die Prüfungen nach dem Umzug.

## Verwandte Seiten

- [Beispiele zum Lebenszyklus](../README.md)
- [Zwei Wege zum Bauen](../../zwei-wege-zum-bauen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
