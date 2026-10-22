---
title: Wann Sie Exponential wählen sollten und wann ein reines Framework
audience: web-framework-developer, developer, project-manager
category: web-frameworks
order: 480
summary: Eine faire Entscheidungshilfe für Teams, die zwischen Exponential und einem allgemeinen Web-Framework wie Symfony, Laravel, Django oder Rails wählen - die Projektarten, bei denen Exponential Monate spart, die Projektarten, zu denen ein reines Framework besser passt, die Kosten auf beiden Seiten und die Fragen, die Sie vor der Entscheidung beantworten sollten.
keywords: Entscheidung, Auswahl, Bewertung, Framework oder CMS, selbst bauen oder kaufen, Abwägungen, inhaltsreich, Multisite, mehrsprachig, Berechtigungen, wann nicht einsetzen, decision, choose, evaluation, framework vs CMS, build vs buy, Symfony, Laravel, Django, Rails, trade-offs, content-heavy, multisite, multilingual, permissions, when not to use, Web-Framework-Entwickler, web framework developer
type: help
---
# Wann Sie Exponential wählen sollten und wann ein reines Framework

Diese Seite richtet sich an Entwickler und Projektleiter, die entscheiden, wie sie ein Projekt bauen. Exponential und
allgemeine Web-Frameworks sind in unterschiedlichen Dingen gut; die Frage ist, welches näher an Ihrem Projekt startet.
Nichts auf dieser Seite behauptet, das eine sei grundsätzlich besser.

## Inhalt

1. [Exponential startet näher, wenn](#exponential-startet-näher-wenn)
2. [Ein reines Framework startet näher, wenn](#ein-reines-framework-startet-näher-wenn)
3. [Kosten auf beiden Seiten](#kosten-auf-beiden-seiten)
4. [Fragen, die Sie zuerst beantworten sollten](#fragen-die-sie-zuerst-beantworten-sollten)
5. [Beides zugleich](#beides-zugleich)

## Exponential startet näher, wenn

| Ihr Projekt hat | Exponential bietet Ihnen ohne Code |
|---|---|
| Inhalte, die Menschen bearbeiten, prüfen und veröffentlichen | Inhaltsklassen, Entwürfe, Versionen, Verlauf, eine Administrationsoberfläche |
| Mehrere Sprachen, auch von rechts nach links geschriebene | übersetzte Inhalte, Adressen pro Sprache, RTL ([Übersetzungen](i18n-l10n-und-rtl.md)) |
| Mehrere Sites oder Marken auf einem gemeinsamen Inhaltsbestand | Siteaccesses ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)) |
| Fein abgestufte Berechtigungen, die Administratoren verwalten | Rollen und Richtlinien mit Einschränkungen ([Autorisierung](autorisierung-rollen-und-richtlinien.md)) |
| Freigabeprozesse | Workflows mit Freigabe ([Workflows](workflows-trigger-und-domain-events.md)) |
| Seiten, die Redakteure zusammenstellen | Exponential Layouts ([Der Layout-Editor](../layout-editor.md)) |
| Einen Shop neben den Inhalten | den eingebauten Shop ([Überblick über den Shop](../ersteller/website-ersteller/shop-ueberblick.md)) |
| Viele Datenbankoptionen oder keine, die betrieben werden muss | standardmäßig SQLite; PostgreSQL, MySQL/MariaDB, Oracle, MongoDB ([Datenbankunterstützung](../datenbankunterstuetzung.md)) |

Bei solchen Projekten würde ein Framework-Team seine ersten Monate damit verbringen, eine Administrationsoberfläche,
Versionierung, Übersetzungen und Berechtigungen zu bauen; Exponential bringt all das vom ersten Tag an mit
([Exponential als Anwendungsplattform](../ersteller/anwendungsentwickler/exponential-als-anwendungsplattform.md)).

## Ein reines Framework startet näher, wenn

- Der Kern **große Mengen transaktionaler Daten** ohne redaktionelles Leben sind (ein Kontenbuch, Telemetrie, der
  Bestand einer Buchungsmaschine).
- Das Produkt ein **reiner API-Dienst** ist und das Team Container, ORM und Werkzeuge des Frameworks durchgängig nutzen
  möchte.
- Das Team **überall die neuesten PHP-Idiome** möchte: Attribute, einen DI-Container, Middleware nach PSR-7 und
  PSR-15, typisierte Konfiguration. Der Kernel von Exponential hat eine lange Geschichte und eigene Konventionen
  ([Dependency Injection](dependency-injection-und-dienste.md)).
- Bei der **Personalsuche** auf den größten Pool von Entwicklern für ein Framework zurückgegriffen werden muss.
- Sie ein Framework-spezifisches Paket aus dessen Ökosystem brauchen, für das es keine Entsprechung gibt.

## Kosten auf beiden Seiten

| | Exponential | Reines Framework |
|---|---|---|
| Einarbeitung | Siteaccesses, INI-Ebenen, die Template-Sprache, Overrides | das Framework plus jedes Paket, das Sie für CMS-Funktionen hinzufügen |
| Redaktionelle Funktionen bauen | größtenteils Konfiguration | Code und Wartung |
| Eigene Anwendungslogik | Erweiterungen mit Modulen und Klassen | nativ |
| Testen | PHPUnit, kein HTTP-Client im selben Prozess ([Testen](testen.md)) | meist umfangreichere Testwerkzeuge |
| Upgrades | ein Produkt, Composer-Updates von Kernel und Erweiterungen | das Framework plus jedes einzelne Paket |

## Fragen, die Sie zuerst beantworten sollten

1. Wer bearbeitet die Daten, und braucht er Entwürfe, Verlauf, Übersetzungen und Freigaben?
2. Wie viele Sites, Sprachen und Zielgruppen teilen sich die Inhalte?
3. Wer entscheidet über Berechtigungen: Entwickler im Code oder Administratoren zur Laufzeit?
4. Sind die meisten Daten Inhalte oder Transaktionen?
5. Was kann das Team, und was wird es in fünf Jahren pflegen?

Lauten die Antworten auf 1 bis 3 „Redakteure“, „mehrere“ und „Administratoren“, ist Exponential meist der kürzere Weg.
Lautet die Antwort auf 4 „Transaktionen“, halten Sie diese in eigenen Tabellen oder einem separaten Dienst, ganz gleich,
was Sie für den Rest wählen.

## Beides zugleich

Sie müssen sich nicht für alles auf eines festlegen: Halten Sie die Inhalte in Exponential und einen transaktionalen
Dienst in einem Framework, oder verwenden Sie die auf Symfony basierenden Plattformprodukte
([Exponential und ein Framework kombinieren](exponential-und-ein-framework-kombinieren.md),
[Die Plattformprodukte](die-plattformprodukte.md)).

## Verwandte Seiten

- [Exponential für Web-Framework-Entwickler](exponential-fuer-framework-entwickler.md)
- [Was Exponential ist: die Produktfamilie](../fuehrung/was-exponential-ist.md)
- [Checklisten für Framework-Entwickler](checklisten-fuer-framework-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
