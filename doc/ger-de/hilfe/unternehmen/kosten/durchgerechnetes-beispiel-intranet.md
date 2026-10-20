---
title: "Durchgerechnetes Beispiel: ein Intranet"
audience: businesses, project-manager, admin
category: business-solutions
order: 50
summary: Eine Kosten-Nutzen-Rechnung Schritt für Schritt für ein Mitarbeiter-Intranet auf Exponential, bei dem die Rendite aus eingesparter Zeit statt aus Verkäufen kommt, nur mit gekennzeichneten Beispielannahmen und einem realistischen Abschlag für Zeit, die nicht wirklich frei wird.
keywords: Intranet, durchgerechnetes Beispiel, eingesparte Zeit, Produktivität, TCO, ROI, Amortisation, Mitarbeiter, Rollen, zweistufige Anmeldung, LDAP, Budget, intranet, worked example, time saved, productivity, payback, employees, roles, two-step sign-in, budget
type: help
---
# Durchgerechnetes Beispiel: ein Intranet

Diese Seite ist für Führungskräfte, die ein Intranet planen, die interne Website, auf der Mitarbeiter Neuigkeiten,
Dokumente und Abläufe finden, und die zeigen müssen, was es wert ist. Ein Intranet verkauft nichts, seine Rendite ist
also Zeit: Minuten, die jeder Mitarbeiter jede Woche spart. Die Seite zeigt, wie Sie daraus eine Zahl machen, die die
Finanzabteilung akzeptiert.

> **Hinweis**
> Die Organisation, ihre Mitarbeiterzahlen, Zeiten und Kosten sind **Beispielannahmen**: runde Zahlen in einer
> neutralen Währungseinheit. Es sind weder Umfrageergebnisse noch Marktdaten. Ersetzen Sie jede durch Ihre eigenen
> Zahlen.

## Inhalt

1. [Was das Intranet nutzt](#was-das-intranet-nutzt)
2. [Die Eingabewerte](#die-eingabewerte)
3. [Schritt 1: die Kosten](#schritt-1-die-kosten)
4. [Schritt 2: der Wert der eingesparten Zeit](#schritt-2-der-wert-der-eingesparten-zeit)
5. [Schritt 3: ROI und Amortisation](#schritt-3-roi-und-amortisation)
6. [Wie Sie die eingesparte Zeit messen](#wie-sie-die-eingesparte-zeit-messen)
7. [Was Sie für Ihr Intranet ändern](#was-sie-für-ihr-intranet-ändern)

## Was das Intranet nutzt

| Bedarf | Was Exponential bietet | Quelle |
|---|---|---|
| Wer was sieht | „Fine grained role based permissions system“ (fein abgestuftes rollenbasiertes Berechtigungssystem), Sektionen | `README.md`, „Main Exponential features“; [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md), [Sektionen](../../../anleitungen/sektionen.md) |
| Freigabe vor dem Veröffentlichen | „Content approval and scheduled publication“ (Freigabe von Inhalten und zeitgesteuerte Veröffentlichung), Workflows | `README.md`; [Workflows](../../../anleitungen/workflows.md) |
| Sicherere Anmeldung | Zweistufige Anmeldung mit einer Authenticator-App oder E-Mail-Codes | [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |
| Anmeldung über das Firmenverzeichnis | Ein LDAP-Login-Handler (`settings/ldap.ini`, `kernel/classes/datatypes/ezuser/ezldapuser.php`) | [Verzeichnisanmeldung und LDAP](../intranets/verzeichnisanmeldung-und-ldap.md) |
| Dokumente | Datei-Uploads, Versionsverwaltung, Suche | `README.md`; [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |
| Benachrichtigungen für Mitarbeiter | Abonnements neuer Inhalte per E-Mail | [Benachrichtigungen](../../../anleitungen/benachrichtigungen-administrator.md) |
| Nachweis, wer was geändert hat | Der Audit-Trail | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |

Es gilt keine Lizenz pro Benutzer, die Kosten wachsen also nicht mit der Zahl der Mitarbeiterkonten
([Wofür Sie nicht bezahlen](wofuer-sie-nicht-bezahlen.md)).

## Die Eingabewerte

| Eingabewert | Wert |
|---|---|
| Beispielannahme: Mitarbeiter, die das Intranet nutzen | 100 |
| Beispielannahme: eingesparte Minuten pro Mitarbeiter und Woche | 15 |
| Beispielannahme: Arbeitswochen pro Jahr | 40 |
| Beispielannahme: durchschnittliche Personalkosten pro Stunde | 40 Währungseinheiten |
| Beispielannahme: Anteil der eingesparten Zeit, der zu nützlicher Arbeit wird | 50 % |
| Beispielannahme: Einrichtung durch einen Partner | 25.000 Währungseinheiten, einmalig |
| Beispielannahme: Schulung für Redakteure und Mitarbeiter | 2.000 Währungseinheiten, einmalig |
| Beispielannahme: Hosting | 150 Währungseinheiten im Monat |
| Beispielannahme: Administration | 50 Stunden im Jahr zu 50 Währungseinheiten pro Stunde |
| Lizenzgebühr, auch pro Benutzer | 0 (Fakt: `README.md`, „License“; `LICENSE.md` Abschnitt 0) |

## Schritt 1: die Kosten

1. Einmalig: 25.000 + 2.000 = **27.000**.
2. Hosting pro Jahr: 150 x 12 = **1.800**.
3. Administration pro Jahr: 50 x 50 = **2.500**.
4. Laufende Kosten pro Jahr: 1.800 + 2.500 = **4.300**.
5. Erstes Jahr: 27.000 + 4.300 = **31.300**.
6. Fünf Jahre: 27.000 + (4.300 x 5) = 27.000 + 21.500 = **48.500**.

## Schritt 2: der Wert der eingesparten Zeit

1. Eingesparte Minuten pro Jahr: 100 x 15 x 40 = **60.000** Minuten.
2. In Stunden: 60.000 / 60 = **1.000** Stunden.
3. Zu Personalkosten: 1.000 x 40 = **40.000** Währungseinheiten.
4. Nur ein Teil der eingesparten Zeit wird zu nützlicher Arbeit, also wenden Sie die Annahme von 50 % an:
   40.000 x 50 % = **20.000** im Jahr.

> **Tipp**
> Der Abschlag in Schritt 4 macht den Business Case glaubwürdig. Finanzabteilungen wissen, dass zehn hier und dort
> eingesparte Minuten nicht alle zu produktiver Zeit werden. Nennen Sie Ihren Faktor offen und erklären Sie, wie Sie
> ihn gewählt haben.

## Schritt 3: ROI und Amortisation

Erstes Jahr: 20.000 - 31.300 = **-11.300**. Das erste Jahr amortisiert sich nicht; das ist bei einem Intranet normal.

Fünf Jahre:

1. Nutzen: 20.000 x 5 = **100.000**.
2. Kosten: **48.500**.
3. Nettonutzen: 100.000 - 48.500 = **51.500**.
4. ROI: 51.500 / 48.500 x 100 % = etwa **106 %**.

Amortisation:

1. Monatlicher Nettonutzen nach laufenden Kosten: (20.000 - 4.300) / 12 = etwa **1.308**.
2. Amortisation der einmaligen Kosten: 27.000 / 1.308 = etwa **20,6 Monate**.

## Wie Sie die eingesparte Zeit messen

Raten Sie die Minuten nicht; messen Sie sie vorher und nachher:

- [ ] Bitten Sie vor dem Projekt eine Auswahl von Mitarbeitern, eine Woche lang zu notieren, wie lange sie nach
      Dokumenten, Telefonnummern und Abläufen suchen.
- [ ] Zählen Sie wiederholte Fragen an den Helpdesk oder die Personalabteilung, die eine Intranetseite beantworten
      könnte.
- [ ] Vergleichen Sie nach dem Start nach drei Monaten dieselben Notizen und Zählungen.
- [ ] Verwenden Sie die eigenen Zahlen der Website: Suchanfragen und ihre Ergebnisse und die Aufzeichnung der
      Veröffentlichungen im Audit-Trail ([Kennzahlen aus Logs und Audit](../../fuehrung/kennzahlen-aus-logs-und-audit.md)).
- [ ] Tragen Sie den gemessenen Wert beim [Jahresrückblick](../../../../eng-us/help/businesses/templates/annual-review-checklist.md) in die Berechnung
      zurück.

## Was Sie für Ihr Intranet ändern

- [ ] Ihre Mitarbeiterzahlen, Arbeitswochen und Kosten pro Stunde von der Personalabteilung.
- [ ] Einen Umsetzungsfaktor, den Sie begründen können.
- [ ] Posten für Verzeichnisanbindung, Single Sign-on oder Dokumentenmigration, wenn Ihr Projekt sie braucht.
- [ ] Einen Posten für eine jährliche Zugriffsprüfung ([Zugriffsprüfungen](../../../../eng-us/help/businesses/security/access-reviews.md)).
- [ ] Einen Einführungsplan: [Checkliste für die Intranet-Einführung](../intranets/checkliste-fuer-die-intranet-einfuehrung.md).

## Verwandte Seiten

- [Intranets](../intranets/README.md)
- [Intranet planen](../intranets/intranet-planen.md)
- [Gesamtbetriebskosten](gesamtbetriebskosten.md)
- [Vorlage ROI-Arbeitsblatt](../../../../eng-us/help/businesses/templates/roi-worksheet-template.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
