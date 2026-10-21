---
title: Offenlegung von Schwachstellen
audience: government, admin, project-manager
category: public-sector
order: 250
summary: Für öffentliche Stellen - wie das Exponential-Projekt Meldungen über Schwachstellen entgegennimmt und behebt (SECURITY.md, vertrauliche E-Mail oder GitHub Security Advisory, unterstützte Versionen, unveränderliche Releases), und wie Sie eine eigene Offenlegungsrichtlinie für Ihre Website einrichten.
keywords: Offenlegung von Schwachstellen, koordinierte Offenlegung, Schwachstelle melden, unterstützte Versionen, unveränderliche Releases, Offenlegungsrichtlinie, Bug-Bounty, öffentlicher Sektor, vulnerability disclosure, coordinated disclosure, SECURITY.md, security@se7enx.com, GitHub security advisory, supported versions, immutable releases, ISO/IEC 29147, security.txt, disclosure policy, bug bounty, public sector
type: help
---
# Offenlegung von Schwachstellen

Diese Seite richtet sich an Administratorinnen und Administratoren, Sicherheitsbeauftragte und
Projektverantwortliche. Für eine öffentliche Website zählen zwei Meldewege: der des Projekts für Schwachstellen in
Exponential selbst und Ihr eigener für Schwachstellen in Ihrer Website. Das Projekt bewertet seine koordinierte
Offenlegung mit **Teilweise erfüllt**: `SECURITY.md` mit einem vertraulichen Kanal existiert, eine `security.txt` wird
nicht mitgeliefert
([Sicherheitsstandards: Offenlegung von Schwachstellen](../../../../eng-us/standards/security.md#vulnerability-disclosure)).

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Richtlinie des Projekts](#die-richtlinie-des-projekts)
2. [Eine Schwachstelle in Exponential melden](#eine-schwachstelle-in-exponential-melden)
3. [Ihre eigene Offenlegungsrichtlinie](#ihre-eigene-offenlegungsrichtlinie)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Richtlinie des Projekts

Aus [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

- Exponential 6.0 ist die gepflegte Linie. Korrekturen werden auf `main` vorgenommen und mit dem nächsten Release 6.0.x
  veröffentlicht.
- Veröffentlichte Tags von 6.0.x werden durch ein Upgrade auf die neueste 6.0.x korrigiert; 5.x und älter werden nicht
  unterstützt.
- **Ein veröffentlichtes Tag wird nie geändert**: Eine Korrektur kommt als nächstes Release, nicht als korrigierte
  Kopie.
- Meldungen gehen vertraulich an **security@se7enx.com**, nicht an den öffentlichen Issue-Tracker; eine erste Antwort
  wird innerhalb eines Werktags zugesagt; die meldende Person erfährt, ob die Meldung angenommen wird, und bleibt auf dem
  Laufenden.

## Eine Schwachstelle in Exponential melden

Schreiben Sie an security@se7enx.com oder eröffnen Sie ein vertrauliches Advisory auf GitHub
(<https://github.com/se7enxweb/exponential/security/advisories/new>). Geben Sie an:

- die Version (`composer show se7enxweb/exponential` oder das Tag bzw. den Commit) und die PHP-Version;
- die Engine, die die Website ausliefert (Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM), und die Datenbank;
- was ein Angreifer tun kann und die Schritte zur Reproduktion.

Lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg
([Sicherheitshärtung: Eine Schwachstelle melden](../../sicherheitshaertung.md#eine-schwachstelle-melden)). Für eine
Erweiterung melden Sie an die Betreuer ihres eigenen Repositorys; im Zweifel an dieselbe Adresse.

## Ihre eigene Offenlegungsrichtlinie

Viele öffentliche Stellen veröffentlichen für ihre eigenen Websites eine Richtlinie zur Offenlegung von
Schwachstellen. Ihre üblichen Bestandteile:

| Bestandteil | Inhalt |
|---|---|
| Geltungsbereich | welche Domains und Dienste abgedeckt sind; was außerhalb liegt (Dienste Dritter) |
| Wie gemeldet wird | ein Kontakt (Postfach oder Formular), die Sprache(n), ein Verschlüsselungsschlüssel, falls Sie einen anbieten |
| Was Sie zusagen | Zeit bis zur Eingangsbestätigung, Zwischenstände, keine rechtlichen Schritte bei Forschung in gutem Glauben im Rahmen der Regeln (soweit Ihre Rechtsberatung zustimmt) |
| Regeln für Forschende | kein Datenzugriff über das Minimum hinaus, keine Dienststörung, kein Social Engineering, Daten nach der Meldung löschen |
| Anerkennung | ob Sie Forschenden öffentlich danken |

Dann:

1. Veröffentlichen Sie die Richtlinie als Seite Ihrer Website mit einem stabilen URL-Alias.
2. Veröffentlichen Sie eine `security.txt` (RFC 9116), die darauf verweist (siehe „security.txt hinzufügen“).
3. Leiten Sie das Postfach an Personen, die handeln können, mit einer Vertretung.
4. Leiten Sie Meldungen über Exponential selbst an das Projekt weiter, wie oben beschrieben.

## Verwandte Seiten

- [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)
- [Sicherheitsupdates und Offenlegung für Hoster](../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
