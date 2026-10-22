---
title: Datenschutz für Spender und Ehrenamtliche
audience: non-profit, businesses, admin, government
category: non-profits
order: 40
summary: Der Schutz der personenbezogenen Daten von Spendern, Ehrenamtlichen, Leistungsempfängern und Abonnenten auf der Exponential-Website einer Non-Profit-Organisation - wo sie liegen (Formulareinsendungen, Benutzerkonten, Bestellungen, Newsletter-Abonnements, Logs, Exporte, Sicherungen), wer sie sehen darf, weniger erheben, Aufbewahrung und Löschung, Anfragen von Betroffenen, Auftragsverarbeiter und Sicherheitsgrundlagen. Grundlagen, keine Rechtsberatung.
keywords: Datenschutz, Privatsphäre, DSGVO, personenbezogene Daten, Spender, Ehrenamtliche, Leistungsempfänger, Abonnenten, gesammelte Informationen, Bestellungen, Aufbewahrung, Löschung, Auskunftsrecht, Recht auf Löschung, Auftragsverarbeiter, Datenschutzerklärung, Zwei-Faktor-Anmeldung, data protection, privacy, GDPR, personal data, donors, volunteers, beneficiaries, subscribers, collected information, orders, retention, deletion, right of access, right to erasure, processors, privacy notice, two-factor login
type: help
---
# Datenschutz für Spender und Ehrenamtliche

Diese Seite richtet sich an die Geschäftsführung, die Ansprechperson für den Datenschutz und die Koordinatoren.
Non-Profit-Organisationen halten personenbezogene Daten, die Menschen ihnen anvertrauen: Namen und Adressen von
Spendern, die Verfügbarkeit und manchmal Hintergrundinformationen von Ehrenamtlichen und gelegentlich sensible Daten
über die Menschen, für die sie da sind. Dieses Vertrauen zu verlieren, kostet mehr als jedes Bußgeld.

> **Hinweis**
> Grundlagen, keine Rechtsberatung. Welches Recht gilt (zum Beispiel die DSGVO in Europa) und was es verlangt, ist
> eine Frage für Ihren Datenschutzbeauftragten oder Ihre Anwältin.

## Inhalt

1. [Wo personenbezogene Daten liegen](#wo-personenbezogene-daten-liegen)
2. [Weniger erheben](#weniger-erheben)
3. [Wer sie sehen darf](#wer-sie-sehen-darf)
4. [Wie lange Sie sie aufbewahren](#wie-lange-sie-sie-aufbewahren)
5. [Anfragen von Betroffenen](#anfragen-von-betroffenen)
6. [Auftragsverarbeiter und Dritte](#auftragsverarbeiter-und-dritte)
7. [Sicherheitsgrundlagen](#sicherheitsgrundlagen)
8. [Sensible Daten über die Menschen, für die Sie da sind](#sensible-daten-über-die-menschen-für-die-sie-da-sind)
9. [Checkliste](#checkliste)

## Wo personenbezogene Daten liegen

Aus [Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md):

| Daten | Wo | Von wem |
|---|---|---|
| Formulareinsendungen | Gesammelte Informationen | Ehrenamtliche, Hilfesuchende |
| Benutzerkonten | Der Benutzerbaum | Mitarbeitende, Ehrenamtliche, Mitglieder |
| Bestellungen | Der Shop | Spender, die den Weg über den Shop nutzen |
| Newsletter-Abonnements | `cjw_newsletter` | Unterstützer |
| E-Mail-Einstellungen und Einwilligungen | Kernel-Tabellen | Abonnenten |
| Logs und das Audit-Protokoll | Log-Verzeichnisse unter `var/` | Alle, die die Website besuchen oder sich anmelden (minimiert) |
| Suchbegriffe | Suchstatistik, falls eingeschaltet | Besucher |
| Exporte und Sicherungen | Wo auch immer Sie sie ablegen | Alle oben Genannten |

Exporte und Sicherungen werden am leichtesten vergessen. Schreiben Sie auf, wohin sie gehen.

## Weniger erheben

- Fragen Sie in Formularen nur, was Sie brauchen ([Arbeitsblatt Felder des Ehrenamtsformulars](../vorlagen/arbeitsblatt-felder-des-ehrenamtsformulars.md)).
- Fragen Sie Spender nicht nach Daten, die der Zahlungsanbieter bereits hat.
- Protokollieren Sie keine Suchbegriffe, wenn Sie sie nicht verwenden werden.
- Siehe [Datenminimierung](../../verwaltung/datenschutz/datenminimierung.md).

## Wer sie sehen darf

| Daten | Berechtigung | Geben Sie sie |
|---|---|---|
| Formulareinsendungen | `infocollector/read` | nur den Koordinatoren |
| Bestellungen | Shop-Rollen | einer oder zwei Personen |
| Abonnenten | Newsletter-Verwaltung | der Person, die den Newsletter redigiert |
| Audit-Protokoll | `audit/read` | den Administratoren |

Überprüfen Sie jedes Quartal, wer diese Berechtigungen hat ([Arbeitsblatt Rollen und Berechtigungen](../vorlagen/arbeitsblatt-rollen-und-berechtigungen.md),
[Überprüfung der Zugänge](../../unternehmen/sicherheit/zugriffspruefungen.md)).

## Wie lange Sie sie aufbewahren

Schreiben Sie einen Aufbewahrungsplan und halten Sie sich daran. Voreinstellungen und Einstellungen für Logs,
Audit-Einträge, den Papierkorb, Sitzungen und Formulardaten stehen unter
[Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md).

| Daten | Frage, die Sie entscheiden sollten |
|---|---|
| Bewerbungen von Ehrenamtlichen, die nicht weiterverfolgt werden | Wie viele Monate? |
| Anmeldungen zu Veranstaltungen | Nach der Veranstaltung löschen? |
| Konten ausgeschiedener Ehrenamtlicher | Jetzt deaktivieren, wann entfernen? ([Wenn Ehrenamtliche gehen](wenn-ehrenamtliche-gehen.md)) |
| CSV-Exporte | Löschen, wenn die Aufgabe erledigt ist |
| Sicherungen | Wie lang ist die Rotation? |

## Anfragen von Betroffenen

Menschen können fragen, was Sie über sie gespeichert haben, und verlangen, dass Sie es berichtigen oder löschen.

| Anfrage | Wo nachsehen | Lesen |
|---|---|---|
| Auskunft | Konten, Einsendungen, Bestellungen, Abonnements | [Auskunftsrecht](../../verwaltung/datenschutz/auskunftsrecht.md) |
| Löschung | Dieselben; danach Exporte; Sicherungen laufen aus | [Recht auf Löschung](../../verwaltung/datenschutz/recht-auf-loeschung.md) |
| Keine Newsletter mehr | Abmelden, Sperrliste | [Abmelden mit einem Klick](../../verwaltung/datenschutz/abmelden-mit-einem-klick.md) |
| Spenderdaten in Bestellungen | Shop | [Datenexport und Löschung](../../kunden/handel/datenexport-und-loeschung.md) |

Protokollieren Sie Anfragen und Antworten ([Vorlage für ein Protokoll der DSGVO-Anfragen](../../kunden/handel/vorlage-dsgvo-anfragenprotokoll.md)).
Manche Daten lassen sich nicht automatisch exportieren ([Was sich nicht exportieren lässt](../../verwaltung/datenschutz/was-nicht-exportiert-werden-kann.md)).

## Auftragsverarbeiter und Dritte

Nennen Sie in Ihrer Datenschutzerklärung jeden Dienst, der personenbezogene Daten erhält
([Gliederung einer Datenschutzerklärung](../../verwaltung/datenschutz/gliederung-einer-datenschutzerklaerung.md)):

- den Hoster;
- den Zahlungsanbieter oder die Spendenplattform;
- den Dienst für die E-Mail-Zustellung;
- den CAPTCHA-Anbieter (`hcaptcha` oder `recaptcha`) ([CAPTCHAs](../../verwaltung/datenschutz/captchas.md));
- einen Analysedienst, Karten, eingebettete Videos;
- einen KI-Anbieter, falls Exponential Intelligence genutzt wird ([Exponential Intelligence](../../kunden/wachstum/exponential-intelligence.md)).

## Sicherheitsgrundlagen

- Zwei-Faktor-Anmeldung für alle, die personenbezogene Daten lesen können ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- HTTPS überall.
- Updates rechtzeitig eingespielt ([Routine für Sicherungen und Updates](routine-fuer-sicherungen-und-updates.md)).
- Sicherungen genauso geschützt wie die Live-Daten.
- Siehe [Sicherheitsgrundlagen für Unternehmen](../../unternehmen/sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md).

## Sensible Daten über die Menschen, für die Sie da sind

Gesundheit, Religion, Aufenthaltsstatus, Vorstrafen und ähnliche Daten erfordern nach den meisten Gesetzen besondere
Sorgfalt. Erheben Sie sie möglichst gar nicht über die Website; nehmen Sie sie persönlich oder über ein dafür gebautes
System auf. Wenn es sein muss, beziehen Sie Ihren Datenschutzbeauftragten ein, bevor das Formular online geht.

## Checkliste

- [ ] Bestandsaufnahme, wo personenbezogene Daten liegen, einschließlich Exporten und Sicherungen.
- [ ] Formulare auf das Nötige reduziert; sensible Daten von der Website ferngehalten.
- [ ] Berechtigungen eingeschränkt und vierteljährlich überprüft.
- [ ] Aufbewahrungsplan geschrieben und eingehalten.
- [ ] Die Datenschutzerklärung nennt jeden Auftragsverarbeiter.
- [ ] Zwei-Faktor-Anmeldung für alle mit Zugang zu personenbezogenen Daten.
- [ ] Eine benannte Person bearbeitet Anfragen und führt ein Protokoll.

Siehe auch die [Datenschutz-Checkliste](../checklisten/datenschutz-checkliste.md) und die [Grundlagen der DSGVO](../../unternehmen/sicherheit/dsgvo-grundlagen.md).

## Verwandte Seiten

- [Betrieb für Non-Profit-Organisationen](README.md)
- [Bestandsaufnahme personenbezogener Daten (Behörden)](../../verwaltung/datenschutz/bestandsaufnahme-personenbezogener-daten.md)
- [Einwilligung und Formulare](../../unternehmen/sicherheit/einwilligung-und-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
