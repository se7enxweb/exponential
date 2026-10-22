---
title: Lesepfade für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 3
summary: Für CMS-Entwickler Lesereihenfolgen durch diesen Bereich und die zugehörigen Bücher, nach Ziel und verfügbarer Zeit. Sie decken einen ersten Tag ab, eine erste Woche, den Umstieg von einem anderen CMS, den Bau einer ersten Website, Headless und den Einstieg in die Erweiterungsentwicklung. Jeder Pfad sagt, was Sie am Ende können.
keywords: Lesepfade, Lernpfad, wo anfangen, erster Tag, erste Woche, Einarbeitung, Lehrplan, CMS-Entwickler, Migration, Headless, Erweiterungsentwicklung, Kurs, reading paths, learning path, onboarding, extension development
type: help
---
# Lesepfade für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die wissen möchten, was sie in welcher Reihenfolge lesen sollen. Wählen Sie den
Pfad, der zu Ihrem Ziel passt; jeder sagt, was Sie am Ende können. Alle Seiten dieses Bereichs stehen unter
[CMS-Entwickler: hier beginnen](cms-entwickler-hier-beginnen.md#die-seiten-dieses-bereichs).

## Inhalt

1. [Der erste Tag](#der-erste-tag)
2. [Die erste Woche](#die-erste-woche)
3. [Umstieg von einem anderen CMS](#umstieg-von-einem-anderen-cms)
4. [Eine erste Website bauen](#eine-erste-website-bauen)
5. [Headless arbeiten](#headless-arbeiten)
6. [In die Tiefe: das Buch zur Erweiterungsentwicklung](#in-die-tiefe-das-buch-zur-erweiterungsentwicklung)
7. [In einem Kurs lernen](#in-einem-kurs-lernen)

## Der erste Tag

Etwa vier Stunden.

1. [Exponential für CMS-Entwickler](exponential-fuer-cms-entwickler.md): das System im Überblick.
2. [Entwicklungsumgebung](entwicklungsumgebung.md): eine Installation, die auf Ihrem Rechner läuft.
3. [Das Inhaltsmodell](das-inhaltsmodell.md) und [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md).
4. [Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md).
5. [Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md), Abschnitte 1 und 7.

Am Ende können Sie das Modell erklären, das Template hinter einer Seite finden und wissen, welchen Cache Sie leeren.

## Die erste Woche

Ein Thema pro Tag, mit geöffneter Entwicklungsinstallation.

| Tag | Lesen | Tun |
|---|---|---|
| 1 | der erste Tag oben | installieren, durch die Demo-Website und die Administration klicken |
| 2 | [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md), [Datentypen wählen](datentypen-waehlen.md), [Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md) | eine Klasse mit einem Full-View-Template anlegen |
| 3 | [Ansichtsmodi](ansichtsmodi.md), [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md), [Relationen und Einbettungen](relationen-und-einbettungen.md) | eine Listenseite mit Line-Views und Seitenblättern |
| 4 | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md), [Benutzer und Registrierung](benutzer-und-registrierung.md), [Siteaccesses und Multisite](siteaccesses-und-multisite.md) | eine Sektion nur für Mitglieder, mit einem Testkonto geprüft |
| 5 | [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md), [Einstellungen und INI für CMS-Entwickler](einstellungen-und-ini-fuer-cms-entwickler.md), [Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md) | eine Landingpage im Layout-Editor; Ihre Einstellungen in Ihrer Erweiterung |

Am Ende können Sie die Teile einer typischen Website bauen und wissen, wohin jeder gehört.

## Umstieg von einem anderen CMS

Lesen Sie zuerst den Vergleich, dann das Modell, dann die Migrationsanleitung, wenn Sie eine Website umziehen:

| Von | Lesen |
|---|---|
| WordPress | [Umstieg von WordPress](umstieg-von-wordpress.md), dann [WordPress und Exponential](../../installation/migration/wordpress.md) |
| Drupal | [Umstieg von Drupal](umstieg-von-drupal.md), dann [Drupal und Exponential](../../installation/migration/drupal.md) |
| TYPO3 | [Umstieg von TYPO3](umstieg-von-typo3.md), dann [Andere Anwendungen und Datenbanken](../../installation/migration/andere-anwendungen.md) |
| eZ Platform oder Ibexa | [Umstieg von eZ Platform oder Ibexa](umstieg-von-ez-platform-oder-ibexa.md), dann [Kapitel 16](../../installation/16-migration-von-ez-platform-und-ibexa.md) |
| Alles andere | [Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md), dann [Inhalte importieren](inhalte-importieren.md) |

## Eine erste Website bauen

In der Reihenfolge eines Projekts:

1. [Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md) und die [Checkliste zur Prüfung des Inhaltsmodells](checkliste-zur-pruefung-des-inhaltsmodells.md).
2. [Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md), [Rich Text](rich-text.md), [Bilder und Bildvarianten](bilder-und-bildvarianten.md).
3. [Templates und Overrides](templates-und-overrides-fuer-cms-entwickler.md), [Ansichtsmodi](ansichtsmodi.md),
   [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md).
4. [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md), [Siteaccesses und Multisite](siteaccesses-und-multisite.md),
   [URL-Aliase und Routing](url-aliase-und-routing.md).
5. [Formulare und Informationssammlung](formulare-und-informationssammlung.md), [Suche](suche.md),
   [Workflows und Auslöser](workflows-und-ausloeser.md), [Cronjobs](cronjobs.md).
6. [Inhalte importieren](inhalte-importieren.md), [Seitenpakete und Installer](seitenpakete-und-installer.md).
7. [Leistung](leistung.md), [Sicherheits-Checkliste](sicherheits-checkliste.md), [Barrierefreiheit](barrierefreiheit.md),
   [SEO-Grundlagen](seo-grundlagen.md).
8. [Eine Website bereitstellen](eine-website-bereitstellen.md) und die [Checkliste für den Projektstart](checkliste-fuer-den-projektstart.md).

## Headless arbeiten

[Headless-Auslieferung](headless-auslieferung.md), [Die REST-API](die-rest-api.md), [Inhalte aus PHP](inhalte-aus-php.md),
danach die Seiten für Anwendungsentwickler ([Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)).

## In die Tiefe: das Buch zur Erweiterungsentwicklung

Wenn die Website eigenen Code braucht, machen Sie in [Erweiterungsentwicklung](../erweiterungsentwicklung/README.md)
weiter. Dessen eigene [Lesepfade](../erweiterungsentwicklung/lesepfade.md) haben einen Pfad für Website-Bauer
(Einstellungen, Templates, Overrides, kein PHP) und einen Pfad für PHP-Entwickler; beide beginnen dort, wo dieser
Bereich endet. Designer in Ihrem Team beginnen bei [Designer: hier beginnen](../designer/designer-hier-beginnen.md), und
die Template-Sprache hat ihre eigene Referenz ([Die Template-Sprache](../template-sprache/README.md)).

## In einem Kurs lernen

Der [Kurs für Entwickler](../trainer/entwickler/README.md) der Trainer vermittelt denselben Stoff in dreizehn Modulen
mit Übungen und Quizfragen; [Exponential Layouts](../trainer/layouts/README.md) und
[Umzug zu Exponential](../trainer/migration/README.md) sind eigene Kurse.

## Verwandte Seiten

- [CMS-Entwickler: hier beginnen](cms-entwickler-hier-beginnen.md)
- [FAQ für CMS-Entwickler](faq-fuer-cms-entwickler.md)
- [Glossar für CMS-Entwickler](glossar-fuer-cms-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
