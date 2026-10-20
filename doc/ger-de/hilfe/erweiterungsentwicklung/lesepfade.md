---
title: Lesepfade durch das Buch
audience: developer, designer, app-creator, user
category: extension-development
order: 5
summary: Lesereihenfolgen durch das Buch für jede Rolle - neugieriger Benutzer, Builder, PHP-Entwickler, Designer, Anwendungsentwickler, Aktualisierer, Maintainer - mit dem, was Sie nach jeder Etappe können.
keywords: Lesepfad, Lernpfad, wo beginnen, Rollen, Builder, Entwickler, Designer, Anwendungsentwickler, Maintainer, Upgrade, Reihenfolge, reading path, learning path, where to start, roles, developer, application creator, maintainer, upgrade, order
type: help
---
# Lesepfade durch das Buch

Diese Seite gehört zu [Erweiterungsentwicklung: das Buch](README.md). Das Buch ist lang, und niemand braucht alles auf
einmal. Wählen Sie den Pfad, der zu dem passt, was Sie tun möchten. Jeder Pfad führt die Kapitel in ihrer
Reihenfolge auf und sagt, was Sie am Ende jeder Etappe können.

## Der neugierige Benutzer: „Was lässt sich ändern, und von wem?“

1. [Die Leiter vom Benutzer zum Entwickler](teil-1-vom-benutzer-zum-entwickler/die-leiter-vom-benutzer-zum-entwickler.md)
2. [Was eine Erweiterung ist](teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md)
3. [Eine Website ohne Code bauen](teil-2-builder/eine-website-ohne-code-bauen.md)
4. [Ideen auf GitHub finden](teil-1-vom-benutzer-zum-entwickler/ideen-auf-github-finden.md) und
   [projects.exponential.earth](teil-1-vom-benutzer-zum-entwickler/projects-exponential-earth.md)

Danach können Sie eine Einstellung von einem Template und von einer Erweiterung unterscheiden und Erweiterungen
finden, die andere geschrieben haben.

## Der Builder: Einstellungen, Templates und Overrides, ohne PHP

1. [Werkzeuge, die Sie brauchen](teil-1-vom-benutzer-zum-entwickler/werkzeuge-die-sie-brauchen.md) und
   [eine Entwicklungsinstallation einrichten](teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md)
2. [Wie INI-Einstellungen funktionieren](teil-2-builder/wie-ini-einstellungen-funktionieren.md), dann
   [Geltungsbereiche von Einstellungen und ihre Reihenfolge](teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md) und
   [Siteaccess-Einstellungen](teil-2-builder/siteaccess-einstellungen.md)
3. [Templates für Builder](teil-2-builder/templates-fuer-builder.md), [Die Design-Kette](teil-2-builder/die-design-kette.md),
   [Template-Overrides](teil-2-builder/grundlagen-der-template-overrides.md), [View-Modi](teil-2-builder/view-modi-fuer-builder.md)
4. [Fetch für Builder](teil-2-builder/fetch-fuer-builder.md),
   [Die Template-Operatoren, die Builder am häufigsten brauchen](teil-2-builder/die-wichtigsten-template-operatoren-fuer-builder.md)
5. [Inhaltsklassen](teil-2-builder/inhaltsklassen-fuer-builder.md),
   [Sektionen, Rollen und Zugriff](teil-2-builder/sektionen-rollen-und-zugriff-fuer-builder.md),
   [Workflows](teil-2-builder/workflows-fuer-builder.md), [Cronjobs](teil-2-builder/cronjobs-fuer-builder.md)
6. [Wenn Einstellungen nicht ausreichen](teil-2-builder/wenn-einstellungen-nicht-reichen.md)

Danach können Sie das Verhalten und das Aussehen einer Website gestalten, ohne PHP zu schreiben, und Ihre Arbeit in
einer Erweiterung aufbewahren.

## Der PHP-Entwickler, neu bei Exponential

1. [Was eine Erweiterung ist](teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md),
   [Den vorhandenen Code lesen](teil-1-vom-benutzer-zum-entwickler/den-vorhandenen-code-lesen.md),
   [Debug-Ausgabe und Logs](teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)
2. Teil 2, die Kapitel 1 bis 6 des Builder-Pfads (Einstellungen, Templates, die Design-Kette)
3. [Teil 3](teil-3-erweiterungsentwickler/README.md), der Aufbau, in dieser Reihenfolge
4. [Rundgang durch die RAD-Werkzeuge](teil-3-erweiterungsentwickler/tutorials/rundgang-durch-die-rad-werkzeuge.md), dann die Tutorials
   paarweise: jedes Tutorial „Mit RAD“ und sein Gegenstück „Von Hand“
5. [Abschluss-Tutorial: eine vollständige Erweiterung](teil-3-erweiterungsentwickler/tutorials/abschlussprojekt-eine-vollstaendige-erweiterung.md)
6. [Sicherheit in Erweiterungen](teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md) und
   [Content Security Policy für Erweiterungen](teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md)
7. Halten Sie [Teil 8](teil-8-referenz/README.md) während der Arbeit offen

Danach können Sie eine Erweiterung jeder Art, die der Kernel unterstützt, schreiben und testen.

## Der Designer

1. Der Builder-Pfad, Kapitel 1 bis 3
2. [Teil 6](teil-6-theme-entwickler/README.md), in dieser Reihenfolge
3. [admin4-Styling für Erweiterungsseiten](teil-4-aktualisieren/admin4-styling-fuer-erweiterungsseiten.md), wenn Sie
   Administrationsseiten gestalten
4. [Wo die Hilfe für Designer weitergeht](teil-6-theme-entwickler/wo-die-hilfe-fuer-designer-weitergeht.md)

Danach können Sie ein Theme bauen, ausliefern und pflegen.

## Der Anwendungsentwickler

1. Der Builder-Pfad
2. [Module und Views](teil-3-erweiterungsentwickler/module-und-views.md),
   [Persistente Objekte und eigene Tabellen](teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md),
   [Richtlinienfunktionen und Zugriff](teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)
3. [Ajax mit ezjscore-Serverfunktionen](teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md) und
   [expservices](teil-8-referenz/expservices.md) für Apps, die mit der Website sprechen
4. [Inhaltsfunktionen aus PHP](teil-8-referenz/inhaltsfunktionen-aus-php.md) für Importe
5. Das Abschluss-Tutorial

## Der Aktualisierer: „Ich habe eine alte Erweiterung“

1. [Eine alte Erweiterung bewerten](teil-1-vom-benutzer-zum-entwickler/eine-alte-erweiterung-bewerten.md) und
   [Lizenzen lesen, bevor Sie etwas wiederverwenden](teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)
2. [Teil 4](teil-4-aktualisieren/README.md), ab [der Checkliste](teil-4-aktualisieren/die-upgrade-checkliste.md), in dieser
   Reihenfolge
3. Die vier Fallstudien am Ende von Teil 4
4. [Wann Sie forken sollten und wann nicht](teil-5-erweitern-forken-zurueckgeben/wann-forken.md), wenn die Erweiterung
   jemand anderem gehört

## Der Maintainer

1. [Teil 5](teil-5-erweitern-forken-zurueckgeben/README.md): Forks, Anerkennung, Lizenzen, Pull Requests, Reviews
2. [Teil 7](teil-7-veroeffentlichen/README.md): Versionen, Tags, Packagist, Changelogs, die Release-Checkliste
3. [Der Weg zum Maintainer](teil-5-erweitern-forken-zurueckgeben/der-weg-zum-maintainer.md) und
   [Der Wartungszyklus](teil-7-veroeffentlichen/der-wartungslebenszyklus.md)

## Verwandte Seiten

- Zurück: [Erweiterungsentwicklung: das Buch](README.md)
- Weiter: [Teil 1: Vom Benutzer zum Entwickler](teil-1-vom-benutzer-zum-entwickler/README.md)
- [Index des Buchs: jedes Thema von A bis Z](themenindex.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
