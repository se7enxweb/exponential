---
title: Betreiber mehrerer Websites: hier beginnen
audience: customer, admin
category: customer-success
order: 90
summary: Für Organisationen, die mehrere Websites, Marken oder Regionen aus einer Exponential-Installation betreiben: was geteilt wird und was pro Website gilt, Rollen pro Website, Website-übergreifende Links, Staging und Produktion und die Entscheidungen, die viele Websites beherrschbar halten.
keywords: Multisite, mehrere Websites, Marken, Regionen, eine Installation, Siteaccess, gemeinsame Inhalte, Einstellungen pro Website, Website-übergreifende Links, Staging, Installationsname, multisite, several sites, brands, regions, one installation, shared content, per-site settings, cross-site links, staging, installation name
type: help
---
# Betreiber mehrerer Websites: hier beginnen

Exponential hat eine eingebaute „Multi-site support“ (`README.md`, „Main Exponential features“): Eine Installation kann
mehrere Websites ausliefern, jede mit eigener Adresse, eigenem Design und eigenen Sprachen, aus einem Inhaltsbaum oder
aus mehreren. Mehrere Websites auf diese Weise zu betreiben spart Arbeit (ein Upgrade, ein Satz Konten, gemeinsame
Inhalte) und bringt eine neue Art von Frage mit sich: Ist diese Änderung für eine Website oder für alle?

Diese Seite ist für den Betreiber einer Gruppe von Websites (Marken, Regionen, Abteilungen, Kampagnen-Websites) und die
Personen, die jede davon leiten. Die Planung einer solchen Einrichtung behandelt
[Mehrere Marken für Unternehmen](../../unternehmen/multisite/README.md).

## Was geteilt wird und was pro Website gilt

| Von allen Websites einer Installation geteilt | Kann pro Website sein |
|---|---|
| Die Version von Exponential und ihre Upgrades | Das Design und die Templates (jede Website ihr eigenes Design) |
| Die Datenbank und der Inhaltsbaum (oder Teile davon) | Der Teil des Inhaltsbaums, den eine Website zeigt |
| Benutzerkonten, Rollen und die Administration | Sprachen und Adressen (Siteaccesses nach Host oder Pfad) |
| In der Installation installierte Erweiterungen | Einstellungen, einschließlich derer in einer eigenen Erweiterung der Website (`doc/eng-us/features/6.0/multi-site-ini-overrides.md`) |
| Backups, sofern Ihr Hoster sie nicht trennt | Logs und Caches, mit einem VarDir pro Website (`doc/eng-us/features/6.0/multi-site-log-and-cache-paths.md`) |

Bitten Sie Ihren Partner, diese Tabelle für Ihre Installation auszufüllen, und bewahren Sie sie bei Ihren Verträgen
auf. Sie beantwortet die meisten Fragen, die eine neue Leitung stellen wird.

## Ein Beispiel aus der Praxis

Die Demo-Installation unter <https://alpha.se7enx.com> liefert mehrere Websites aus einem Inhaltsbaum aus: die Website
„Fit & Healthy“ unter `/site` oder `/` und die Website „Bold Agency“ unter `/bold`, mit ihren Übersetzungen
(`doc/eng-us/features/6.0/multisite-cross-site-links.md`). Wenn eine Seite einer Website Inhalte einer anderen auflistet
(eine Tag-Seite, ein Suchergebnis, ein verwandtes Element), müssen die Links in die richtige Website zeigen; das
erledigt die Erweiterung `ezurlmultisite`. Wenn Sie Links einer Website sehen, die sich in einer anderen öffnen oder mit
„nicht gefunden“ antworten, nennen Sie diese Seite Ihrem Partner.

## Rollen pro Website

Geben Sie jeder Website ihre eigenen Leitungen und Redakteure, beschränkt auf den Teil des Inhaltsbaums und die
Sektionen dieser Website ([Rollen für Ihr Team](../alltag/rollen-fuer-ihr-team.md), [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
Halten Sie die wenigen Personen, die alle Websites oder die gemeinsamen Teile ändern dürfen, klein und namentlich benannt.

## Staging und Produktion

Gruppen von Websites haben meist eine Testkopie neben der Live-Installation. Die Funktion für den Installationsnamen
setzt den Namen der Installation auf jeder Installation, die nicht die Produktion ist, vor den Seitentitel, sodass
niemand die falsche ändert (`doc/eng-us/features/6.0/installation-name-in-pages.md`). Bitten Sie Ihren Partner, sie
einzuschalten, wenn Ihre Browser-Tabs gleich aussehen.

## Entscheidungen, die viele Websites beherrschbar halten

- **Eine verantwortliche Person pro Website**, schriftlich festgehalten, dazu eine für die gemeinsamen Teile.
- **Eine Regel für gemeinsame Inhalte**: Wer darf ein Element ändern, das auf mehreren Websites erscheint
  ([Gemeinsame Inhalte über Marken hinweg](../../unternehmen/multisite/inhalte-zwischen-marken-teilen.md)).
- **Ein Upgrade-Kalender für alle Websites**, mit allen Website-Verantwortlichen vereinbart, da ein Upgrade alle auf
  einmal erreicht ([Wie Updates funktionieren](../gesund-halten/wie-updates-funktionieren.md)).
- **Eine Regel für neue Websites**: Was eine neue Marke oder Region braucht, bevor sie startet
  ([Eine Website hinzufügen](../wachstum/eine-website-hinzufuegen.md)).

## Ihr nächster Schritt

Folgen Sie dem [Lesepfad für Betreiber mehrerer Websites](lesepfad-mehrere-websites.md). Die meisten Gruppen von
Websites sind auch mehrsprachig: Ergänzen Sie den [Lesepfad für mehrsprachige Websites](lesepfad-mehrsprachige-websites.md).

## Verwandte Seiten

- [Lesepfad für Betreiber mehrerer Websites](lesepfad-mehrere-websites.md)
- [Eine Installation, viele Websites (Unternehmen)](../../unternehmen/multisite/eine-installation-viele-websites.md)
- [Einstellungen und Logs pro Website (Unternehmen)](../../unternehmen/multisite/einstellungen-logs-und-caches-pro-website.md)
- [Multisite-Einrichtung (Website-Ersteller)](../../ersteller/website-ersteller/multisite-einrichten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
