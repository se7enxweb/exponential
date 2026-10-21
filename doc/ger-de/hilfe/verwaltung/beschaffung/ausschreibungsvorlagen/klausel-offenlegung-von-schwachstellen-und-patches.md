---
title: "Ausschreibungsvorlage: Klausel zu Offenlegung von Schwachstellen und Patches"
audience: government, project-manager, partner, hosting
category: public-sector
order: 50
summary: Klauseltext, der einen Lieferanten verpflichtet, Sicherheitsreleases von Exponential, PHP, der Datenbank und des Betriebssystems zu verfolgen, zu testen und innerhalb vereinbarter Fristen einzuspielen, Meldungen zu behandeln und die Website auf unterstützten Versionen zu halten - mit den eigenen Release-Fakten des Projekts.
keywords: Patches, Schwachstelle, Sicherheitsupdates, Offenlegung, security.txt, RFC 9116, unterstützte Versionen, Release, SECURITY.md, Frist zum Einspielen, Vertrag, Ausschreibungsvorlage, patching, vulnerability, security updates, disclosure, supported versions, time to apply, contract, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Offenlegung von Schwachstellen und Patches

Diese Seite richtet sich an Beschaffungsstellen und Sicherheitsbeauftragte, die brauchen, dass ein Lieferant das CMS
gepatcht hält. Die Klausel trennt, was der Lieferant steuert (Releases verfolgen, testen und einspielen), von dem, was
das vorgelagerte Projekt steuert (die Korrektur erstellen), nach dem Rat in [SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md):
„Versprechen Sie, eine veröffentlichte Korrektur innerhalb einer Frist einzuspielen, nicht, dass eine Schwachstelle
innerhalb einer Frist behoben wird.“

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Release-Fakten des Projekts](#die-release-fakten-des-projekts)
2. [Die Klausel](#die-klausel)
3. [Wie ein Lieferant sie erfüllt](#wie-ein-lieferant-sie-erfüllt)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Release-Fakten des Projekts

Aus [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) und
[Quellcodeverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md):

- Exponential 6.0 ist die gepflegte Linie. Sicherheitskorrekturen werden auf `main` vorgenommen und mit dem nächsten
  Release 6.0.x veröffentlicht. Ältere Linien (5.x, 4.x, 3.x) werden nicht unterstützt.
- Ein veröffentlichter Tag wird nie geändert: Eine Korrektur erreicht Sie als nächstes Release.
- Vertrauliche Meldungen gehen per E-Mail an die Adresse in `SECURITY.md`, mit einer ersten Antwort innerhalb eines
  Werktags.
- Es wird keine `security.txt` ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)) mitgeliefert; der Betreiber kann
  eine hinzufügen.

## Die Klausel

```text
Z. VULNERABILITIES AND PATCHING

Z.1 Monitoring. The Supplier shall monitor security releases and advisories for
    Exponential and every extension, library, PHP version, database, web server
    and operating system component in the delivered system.

Z.2 Classification. The Supplier shall classify each release as critical, high,
    medium or low for the Customer's system within <time> of its publication,
    and inform the Customer of critical and high classifications.

Z.3 Time to apply. The Supplier shall apply released fixes to production within:
      critical: <time>   high: <time>   medium: <time>   low: next planned release.
    Where a fix cannot be applied in time, the Supplier shall apply a documented
    mitigation and agree a date with the Customer.

Z.4 Testing. Every fix shall be tested on a staging copy before production,
    unless the Customer agrees otherwise in writing for an emergency.

Z.5 Supported versions. The Supplier shall keep the system on versions that
    receive security fixes from their maintainers. Exponential: the maintained
    line named in its SECURITY.md. The Supplier shall plan upgrades at least
    <period> before a component leaves support.

Z.6 Reports. The Supplier shall publish a security contact for the Customer's
    site [as /.well-known/security.txt following RFC 9116], forward reports about
    the software to its maintainers through their private channel, and never
    disclose an unfixed vulnerability publicly.

Z.7 Record. The Supplier shall keep a record of releases assessed, fixes applied
    and dates, and report it <monthly / quarterly>.
```

## Wie ein Lieferant sie erfüllt

| Klausel | Wie, mit Exponential |
|---|---|
| Z.1 | Die Releases des Repositorys und das Changelog verfolgen ([Changelog 6.0.15](../../../../../changelogs/6.0/6.0.15.md)); das Dashboard zeigt „Stay secure: keep Exponential up to date“ mit dem Git Manager und Composer |
| Z.3, Z.4 | Ein dokumentiertes Update-Verfahren: [Kundenwebsites aktualisieren](../../../hosting/betrieb/kunden-websites-aktualisieren.md), [Änderungen ausrollen](../../../hosting/betrieb/aenderungen-ausrollen.md), [Mit Git aktualisieren](../../../mit-git-aktualisieren.md) |
| Z.4 | Vor dem Upgrade: **Setup > Upgrade-Prüfung** ([Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)) |
| Z.5 | Unterstützungsstatus von PHP in [Anforderungen](../../../../installation/02-voraussetzungen.md); die Unterstützungszusage der Foundation ([Wer ist die Exponential Foundation?](../../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)) |
| Z.6 | [Eine Schwachstelle melden](../../../sicherheitshaertung.md#eine-schwachstelle-melden) |

Beachten Sie, dass das Projekt in der CI keine Prüfung der Abhängigkeiten auf Schwachstellen durchführt
([Sicherheitsstandards](../../../../standards/sicherheit.md)); der Härtungsleitfaden empfiehlt `composer audit`. Bitten
Sie den Lieferanten, es als Teil von Z.1 auszuführen.

## Hinweise für die bewertende Person

- Füllen Sie die Fristen in Z.3 nach Ihrer Risikobereitschaft aus; diese Hilfe gibt keine vor.
- Bitten Sie Bieter um ein Beispiel ihres monatlichen Nachweises (Z.7) von einem anderen Kunden, ohne personenbezogene
  Daten und Kundendaten.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zur Sicherheit](klausel-sicherheit.md)
- [Klausel zu Service-Levels](klausel-service-level.md)
- [Sicherheitsupdates und Offenlegung für Hoster](../../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
