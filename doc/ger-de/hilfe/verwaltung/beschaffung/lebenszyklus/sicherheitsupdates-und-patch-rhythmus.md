---
title: Sicherheitsupdates und Patch-Rhythmus
audience: government, project-manager, admin, hosting
category: public-sector
order: 70
summary: Wie Sicherheitskorrekturen eine Exponential-Website erreichen - vertrauliche Meldung, Korrekturen auf main, das nächste Release -, wie eine öffentliche Stelle davon erfährt und wie sie mit einem Lieferanten oder Hoster eine Frist für Patches vereinbart, ohne die Seite des Projekts am Versprechen zu erfinden.
keywords: Sicherheitsupdates, Patches, Schwachstelle, SECURITY.md, Offenlegung, Release, Patch-Fenster, Reaktionszeit, Pflicht des Lieferanten, Releases verfolgen, öffentlicher Sektor, security updates, patching, vulnerability, disclosure, release, patch window, response time, supplier obligation, monitoring releases, public sector
type: help
---
# Sicherheitsupdates und Patch-Rhythmus

Diese Seite richtet sich an Sicherheitsbeauftragte und Vertragsmanagement einer öffentlichen Stelle. Sie erklärt, wie
eine Schwachstelle in Exponential von der Meldung bis zur Korrektur behandelt wird, was das Projekt verspricht und was
nicht, und wie Sie daraus eine Pflicht zum Einspielen von Patches für einen Lieferanten machen. Die Quellen sind [`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md),
[Sicherheitsstandards](../../../../../eng-us/standards/security.md#vulnerability-disclosure) und
[SLA-Optionen für das Hosting von Exponential](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md).

## Inhalt

1. [Von der Meldung zur Korrektur](#von-der-meldung-zur-korrektur)
2. [Was das Projekt verspricht](#was-das-projekt-verspricht)
3. [Wie Sie von einer Korrektur erfahren](#wie-sie-von-einer-korrektur-erfahren)
4. [Eine Frist für Patches vereinbaren](#eine-frist-für-patches-vereinbaren)
5. [Ihr eigener Meldekanal](#ihr-eigener-meldekanal)

## Von der Meldung zur Korrektur

1. Jemand meldet eine Schwachstelle vertraulich per E-Mail an die Adresse in `SECURITY.md`, nicht im öffentlichen
   Tracker.
2. Die meldende Person erhält eine erste Antwort und erfährt, ob die Meldung angenommen wird.
3. Ein angenommenes Problem wird auf `main` behoben.
4. Die Korrektur wird mit dem nächsten Release 6.0.x veröffentlicht. Veröffentlichte Tags werden nie geändert.
5. Websites erhalten sie durch ein Upgrade auf dieses Release ([Upgrades im Haushalt planen](upgrades-im-haushalt-planen.md)).

## Was das Projekt verspricht

| Versprechen | Quelle |
|---|---|
| Vertraulicher Meldekanal | `SECURITY.md` |
| „Eine erste Antwort innerhalb eines Werktags“ | `SECURITY.md` |
| Korrektur auf `main`, veröffentlicht als nächstes 6.0.x | `SECURITY.md` |
| Unterstützte Linie: 6.0.x; 5.x und älter nicht | `SECURITY.md` |

Nicht versprochen: eine Frist für die Korrektur, eine Vorabinformation der Nutzer, ein Feed mit Sicherheitshinweisen. Es
wird keine Datei `security.txt` mitgeliefert ([Sicherheitsstandards](../../../../../eng-us/standards/security.md#vulnerability-disclosure)). Die
SLA-Seite für das Hosting rät: Versprechen Sie, eine veröffentlichte Korrektur innerhalb einer Frist einzuspielen, nicht,
dass eine Schwachstelle innerhalb einer Frist behoben wird; „das Zweite hängt vom Projekt ab, das Erste von Ihnen“
([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#sicherheitsupdates)).

## Wie Sie von einer Korrektur erfahren

- Verfolgen Sie die Releases des Repositorys auf GitHub und das Changelog der aktuellen Linie
  ([Changelog 6.0.15](../../../../../changelogs/6.0/6.0.15.md)).
- Das Dashboard der Administration hat ein Panel „Stay secure: keep Exponential up to date“ mit Links zum Git Manager
  und zu Composer-Updates.
- **Setup > Updates and packages** listet neuere Versionen der installierten Pakete auf
  ([Mit Git aktualisieren](../../../mit-git-aktualisieren.md#weg-b-die-administration)).
- Hoster: [Sicherheitsupdates und Offenlegung](../../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md) und
  [Mit dem Projekt und 7x arbeiten](../../../hosting/geschaeft/mit-dem-projekt-und-7x-zusammenarbeiten.md).
- Hinweise für die Presse dazu, wie Korrekturen angekündigt werden: [Über Sicherheitskorrekturen berichten](../../../../../eng-us/help/press/covering-security-fixes.md).

Machen Sie eine benannte Rolle für das Verfolgen verantwortlich. Ohne verantwortliche Person bemerkt niemand ein Release.

## Eine Frist für Patches vereinbaren

```text
Security updates
1. The Supplier monitors the releases of Exponential and of every extension used on the Site.
2. When a release fixes a security issue that affects the Site, the Supplier applies it to the
   production Site within <time> of the release, after a test on the staging copy.
3. If the release cannot be applied in time, the Supplier informs <role> within <time> and applies a
   documented mitigation (for example a web server rule, maintenance mode, or disabling the affected
   feature) until it can.
4. The Supplier records each security update in the <period> report: release, date applied, site checked.
```

Wählen Sie die Fristen mit Ihren Sicherheitsbeauftragten, unter Berücksichtigung Ihres Risikos und der Kapazität des
Lieferanten. Ein kürzeres Versprechen, als der Lieferant personell abdecken kann, ist wertlos; siehe den Rat für Partner
in [Support-Stufen und Reaktionszeiten](../../../partner/support-stufen-und-reaktionszeiten.md#ihre-eigenen-slas-abstimmen).

Maßnahmen zur Abmilderung, die es in Exponential gibt und die dokumentiert sind: der Wartungsmodus ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)),
Anfrageregeln und ein Anfragefilter vor der Website ([Sicherheitshärtung](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Ihr eigener Meldekanal

Ihre öffentliche Website sollte Besuchern sagen, wo sie ein Sicherheitsproblem mit ihr melden können. Exponential
liefert keine `security.txt` mit (RFC 9116, [rfc-editor.org/rfc/rfc9116](https://www.rfc-editor.org/rfc/rfc9116)); ein
Betreiber kann eine hinzufügen. Leiten Sie Meldungen zur Software selbst an das Projekt weiter, wie es `SECURITY.md`
beschreibt, ohne personenbezogene Daten.

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Unterstützte Versionen](unterstuetzte-versionen.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
