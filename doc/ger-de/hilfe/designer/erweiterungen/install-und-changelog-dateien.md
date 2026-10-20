---
title: "INSTALL.md und CHANGELOG.md"
audience: designer
category: theme-extensions
order: 110
summary: Wann ein Theme neben seinem README eine INSTALL.md braucht, wie ein Eintrag in CHANGELOG.md aussieht (mit den Präfixen Added, Updated, Removed, Renamed und Fixed) und ein echtes Beispiel eines veröffentlichten Themes.
keywords: INSTALL.md, CHANGELOG.md, Changelog, Release Notes, Installationsschritte, Added, Updated, Fixed, Historie, changelog, release notes, install steps, history
type: help
---
# INSTALL.md und CHANGELOG.md

Diese Seite ist für Designer, deren Theme über eine Installation in einem Schritt hinausgewachsen ist oder das schon
mehr als einmal veröffentlicht wurde.

## INSTALL.md: wenn das README nicht genügt

Schreiben Sie eine `INSTALL.md`, wenn die Installation mehr verlangt, als die Erweiterung einzuschalten, zum Beispiel
wenn das Theme Folgendes braucht:

- einen eigenen Siteaccess und dessen Einstellungen;
- Inhaltsklassen (aus mitgelieferten Paketen) oder Demo-Inhalte;
- eine Datenbanktabelle (`share/db_schema.dba`);
- andere Erweiterungen in einer bestimmten Ladereihenfolge.

Gliedern Sie sie in nummerierte Schritte, jeder mit dem Befehl und dem, was der Leser danach sehen sollte.
`cjw_themes_jumper` und `sevenx_themes_media` liefern beide eine `INSTALL.md` mit; lesen Sie eine, bevor Sie Ihre
schreiben.

## CHANGELOG.md: ein Eintrag pro Release

`cjw_themes_jumper` führt seine Historie so (gekürzt):

```markdown
# Changelog

## 0.1.5

- Fixed: The starter design passes the contrast, target size and link name checks (axe, WCAG 2.2 AA). ...

## 0.1.4

- Fixed: The single-location map escapes the address and the title for HTML before it escapes them
  for JavaScript ...
```

Die Regeln:

1. Das neueste Release zuerst, eine Überschrift pro Version, genau die Nummer des Tags ohne `v`.
2. Eine Zeile pro Änderung, beginnend mit einem Präfix: `Added:`, `Updated:`, `Removed:`, `Renamed:` oder `Fixed:`
   ([Changelog-Einträge](../veroeffentlichung/changelog-praefixe.md)).
3. Sagen Sie, was sich für die Person geändert hat, die das Theme nutzt, und warum: „Die Anmeldeseite zeigt den Link zur
   Registrierung nur Besuchern, die sich registrieren dürfen“, nicht „login.tpl geändert“.
4. Nennen Sie Beiträge: „Pull request #1 by <their public handle>“.
5. Erwähnen Sie, was der Nutzer nach dem Upgrade tun muss (einen Cache leeren, eine Einstellung ergänzen, ein
   Upgrade-Skript ausführen).

## Schritt für Schritt bei einem Release

1. Bevor Sie die Version erhöhen, fügen Sie `CHANGELOG.md` die neue Überschrift und die Einträge hinzu.
2. Committen Sie das Changelog zusammen mit den Versionsfeldern
   ([Alle Versionsfelder synchron halten](versionsfelder-synchron-halten.md)).
3. Verwenden Sie die Einträge erneut für die Release Notes auf GitHub ([Release Notes schreiben](../veroeffentlichung/release-notes-schreiben.md)).

## Verwandte Seiten

- [Eine CHANGELOG.md führen](../veroeffentlichung/eine-changelog-datei-fuehren.md)
- [Das README eines Themes schreiben](readme-fuer-ein-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
