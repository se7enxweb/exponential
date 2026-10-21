---
title: Auf Online-Verfahren verlinken
audience: government, website-creator, project-manager
category: public-sector
order: 70
summary: Wie in Exponential gebaute Leistungsseiten Menschen an die Systeme übergeben, in denen sie beantragen, bezahlen oder buchen - klare Links, was vor dem Link zu sagen ist, externe Dienste, die sich ändern, Linkprüfungen - und welche Verfahren nicht als Website-Formulare gebaut werden sollten.
keywords: Online-Verfahren, online beantragen, Zahlungen, Buchung, E-Government-Portal, externe Systeme, Links, Linkprüfung, einheitliche Anmeldung, Grenzen des Information Collectors, online transactions, apply online, payments, booking, e-government portal, external systems, links, ezurl, link check, single sign-on, information collector limits
type: help
---
# Auf Online-Verfahren verlinken

Diese Seite richtet sich an Site-Ersteller und Projektleiter, die Leistungsseiten mit den Systemen verbinden, in denen
Leistungen tatsächlich in Anspruch genommen werden: Antragsportale, Zahlungsanbieter, Buchungssysteme,
Fallbearbeitung. Die Website erklärt und verweist; das Verfahren findet anderswo statt. Diese Seite sagt, wie Sie diese
Übergabe klar und verlässlich machen und welche Verfahren ein CMS-Formular nicht ersetzen sollte.

## Inhalt

1. [Was die Website tut und was nicht](#was-die-website-tut-und-was-nicht)
2. [Der Link auf der Leistungsseite](#der-link-auf-der-leistungsseite)
3. [Links funktionsfähig halten](#links-funktionsfähig-halten)
4. [Wann ein einfaches Formular genügt](#wann-ein-einfaches-formular-genügt)
5. [Anmeldung über Systeme hinweg](#anmeldung-über-systeme-hinweg)

## Was die Website tut und was nicht

| Die Website (Exponential) | Das Verfahrenssystem |
|---|---|
| Erklärt, wer beantragen kann, was gebraucht wird, was es kostet | Nimmt den Antrag und die Dokumente entgegen |
| Verlinkt auf den Beginn des Verfahrens | Identifiziert den Antragsteller, nimmt die Zahlung entgegen |
| Sagt, was danach geschieht und wie lange es dauert | Verfolgt den Fall, versendet Bescheide |

Exponential hat einen Shop und Zahlungsschnittstellen für den Verkauf von Produkten
([Überblick über den Shop](../../../../../eng-us/help/creators/website-creators/shop-overview.md),
[Zahlungsschnittstellen](../../../../../eng-us/help/creators/website-creators/payment-gateways.md)); öffentliche Gebühren und Bußgelder
werden meist von eigenen staatlichen Zahlungsdiensten mit eigener Buchhaltung und eigenem Audit eingenommen und sind
besser verlinkt als nachgebaut.

## Der Link auf der Leistungsseite

| Praxis | Warum |
|---|---|
| Der Link steht in einem eigenen Attribut **Online beantragen** (`ezurl`) und wird als klare Schaltfläche weit oben gezeigt | eine Stelle, um ihn zu ändern; einheitliche Position |
| Der Linktext nennt die Handlung: „Bewohnerparkausweis beantragen“ | Screenreader-Nutzer hören Links ohne Zusammenhang |
| Sagen Sie vor dem Link, was bereitzuhalten ist (Dokumente, Ausweis, Zahlungskarte) und wie lange es dauert | Menschen brechen nicht auf halbem Weg ab |
| Sagen Sie, wenn der Link die Site verlässt, und in wessen System | Vertrauen; Datenschutzerklärungen unterscheiden sich |
| Bieten Sie die Alternative an (Telefon, Dienststelle) für Menschen, die das Online-System nicht nutzen können | Inklusion |

Ein Attribut `ezurl` speichert die Adresse in der URL-Tabelle, die alle Links teilen, und die liest die Linkprüfung
([Prüfungen von Links und Inhalten](../../../../../eng-us/help/creators/website-creators/link-and-content-checks.md)).

## Links funktionsfähig halten

Verfahrenssysteme ändern ihre Adressen öfter, als sich Leistungsseiten ändern.

| Maßnahme | Wie |
|---|---|
| Linkprüfung | der Cronjob `linkcheck.php` (Teil infrequent) testet externe Links; **Setup > URL-Verwaltung** (`/url/list`) zeigt die Ergebnisse |
| Ein URL-Objekt je Adresse | alle Seiten, die dieselbe Adresse verwenden, teilen es; einmal korrigiert, sind alle Seiten korrigiert |
| Ein Kontakt zu den Verantwortlichen der Systeme | sie sagen Ihnen Bescheid, bevor sich eine Adresse ändert |
| Eine Weiterleitung auf Ihrer Seite | eine kurze Adresse auf Ihrer eigenen Domain (ein URL-Platzhalter oder globaler Alias auf die externe Adresse), die Sie umleiten können ([Weiterleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)) |

## Wann ein einfaches Formular genügt

Der Information Collector passt zu kurzen Anfragen mit geringem Risiko: eine Frage, die Meldung einer defekten
Straßenlaterne, eine Rückmeldung. Er speichert Einsendungen unter **Setup > Gesammelte Informationen** und kann sie per
Mail senden ([Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).

| Geeignet für den Information Collector | Nicht geeignet |
|---|---|
| Wenige Felder, keine Identitätsprüfung | Anträge, die eine Identitätsprüfung brauchen |
| Keine Zahlung | alles mit Zahlung |
| Keine besonderen Kategorien personenbezogener Daten | Gesundheit, Sozialleistungen, Strafsachen |
| Keine Fallverfolgung nötig | Anfragen, die zu Fällen mit Fristen werden |
| Kleine Anhänge, selten | viele Dokumente je Antrag |

Formulare sind durch CSRF-Formular-Token geschützt; CAPTCHA-Erweiterungen sind Drittanbieter
([Spamschutz für Formulare](../../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).

## Anmeldung über Systeme hinweg

Menschen melden sich ungern zweimal an. Die Login-Handler von Exponential lassen sich erweitern, und
`sevenx_authentication_2fa` enthält Gerüste für OAuth-Handler zum Social Login
([sevenx_authentication_2fa](../../../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md)); die Anbindung an einen
staatlichen Identitätsdienst ist eine Entwicklungsaufgabe, keine Einstellung. Für die meisten Leistungskataloge brauchen
die öffentlichen Seiten überhaupt keine Anmeldung, und das Verfahrenssystem kümmert sich um die Identität.

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Die Leistungsseite und einfache Sprache](die-leistungsseite-und-einfache-sprache.md)
- [Sichere Weiterleitungen](../../../../../eng-us/features/6.0/safe-redirects.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
