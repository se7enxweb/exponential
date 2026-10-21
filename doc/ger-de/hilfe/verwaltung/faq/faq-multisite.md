---
title: "FAQ: viele Websites und viele Abteilungen"
audience: government, admin, project-manager
category: public-sector
order: 180
summary: Antworten auf die Fragen öffentlicher Stellen zum Betrieb vieler Websites und Abteilungen mit Exponential - mehrere Websites in einer Installation, eigene Designs und Sprachen, gemeinsame Inhalte und Benutzer, Rechte pro Abteilung, getrennte Protokolle und Caches und Links zwischen Websites.
keywords: FAQ Multisite, mehrere Websites, Abteilungen, Behörden, Siteaccess, gemeinsame Inhalte, gemeinsame Benutzer, Rechte pro Abteilung, Sektionen, Links zwischen Websites, öffentlicher Sektor, multisite FAQ, several sites, departments, agencies, shared content, shared users, permissions per department, sections, cross-site links, public sector
type: help
---
# FAQ: viele Websites und viele Abteilungen

Diese Seite beantwortet die Fragen öffentlicher Stellen, wenn eine Organisation mehrere Websites betreibt oder viele
Abteilungen auf einer Website veröffentlichen. Die Antworten stammen aus
[Multisite einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md) und den Funktionsseiten zu Multisite.

## Inhalt

1. [Kann eine Installation mehrere Websites betreiben?](#kann-eine-installation-mehrere-websites-betreiben)
2. [Kann jede Website ihr eigenes Design und ihre eigenen Sprachen haben?](#kann-jede-website-ihr-eigenes-design-und-ihre-eigenen-sprachen-haben)
3. [Können Websites Inhalte und Benutzer teilen?](#können-websites-inhalte-und-benutzer-teilen)
4. [Kann jede Abteilung nur ihre eigenen Inhalte bearbeiten?](#kann-jede-abteilung-nur-ihre-eigenen-inhalte-bearbeiten)
5. [Sind Protokolle und Caches pro Website getrennt?](#sind-protokolle-und-caches-pro-website-getrennt)
6. [Kann eine Website auf eine andere verlinken?](#kann-eine-website-auf-eine-andere-verlinken)
7. [Eine Installation oder mehrere?](#eine-installation-oder-mehrere)

## Kann eine Installation mehrere Websites betreiben?

Ja. Jede Website wird über ihren eigenen Siteaccess erreicht, erkannt nach Host, Pfad oder Port
([Multisite einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md),
[Siteaccess-Erkennung nach Host und Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)).

## Kann jede Website ihr eigenes Design und ihre eigenen Sprachen haben?

Ja: Einstellungen pro Siteaccess wählen Design, Sprachen und weitere Einstellungen
([INI-Overrides für mehrere Websites](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)).

## Können Websites Inhalte und Benutzer teilen?

Ja. Sie teilen ein Inhaltsrepository und einen Benutzerbestand; ein Objekt kann über Platzierungen auf mehreren
Websites erscheinen, und ein Benutzer kann sich dort anmelden, wo seine Rollen es erlauben
([Relationen und Wiederverwendung](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)).

## Kann jede Abteilung nur ihre eigenen Inhalte bearbeiten?

Ja, mit Sektionen und Rollen, die auf eine Sektion oder einen Teilbaum beschränkt sind ([Sektionen](../../../anleitungen/sektionen.md),
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)). Die Checkliste
[Eine Abteilung einführen](../checklisten/eine-abteilung-einfuehren.md) zeigt die Schritte.

## Sind Protokolle und Caches pro Website getrennt?

Das können sie sein: Pfade für Protokolle und Caches lassen sich pro Website festlegen
([Pfade für Protokolle und Caches bei mehreren Websites](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)).

## Kann eine Website auf eine andere verlinken?

Ja, mit websiteübergreifenden Links, die auf den richtigen Host aufgelöst werden
([Websiteübergreifende Links in einer Multisite](../../../../eng-us/features/6.0/multisite-cross-site-links.md)).

## Eine Installation oder mehrere?

Eine Installation spart Aktualisierungen und Hosting und macht das Teilen leicht. Mehrere Installationen isolieren
Risiken, Rechte und Release-Zeitpläne. Für Hoster ist beides beschrieben
([Multisite in einer Installation](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md),
[Isolation von Mandanten](../../hosting/sicherheit/trennung-von-mandanten.md)). Für beides gibt es Schulungen
([Multisite und Seitenpakete](../../../../eng-us/help/trainers/multisite-and-packages/README.md)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Multisite einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md)
- [Eine Abteilung einführen](../checklisten/eine-abteilung-einfuehren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
