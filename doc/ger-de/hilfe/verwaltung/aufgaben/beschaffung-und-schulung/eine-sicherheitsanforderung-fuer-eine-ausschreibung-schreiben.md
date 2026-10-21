---
title: Eine Sicherheitsanforderung für eine Ausschreibung schreiben
audience: government, project-manager, admin
category: public-sector
order: 30
summary: Schritt für Schritt Sicherheitsanforderungen für eine Ausschreibung einer Exponential-Site schreiben - Grundhärtung, zu ändernde Voreinstellungen, Patchen, Audit, Tests und Berichte - die ein Bieter beantworten und Sie prüfen können.
keywords: Sicherheitsanforderung, Ausschreibung, Härtung, OWASP, ASVS, Zwei-Faktor, Kontosperre, Patchen, Audit-Protokoll, Penetrationstest, Schwachstelle, Aufgabe zur Beschaffung, security requirement, tender, hardening, two-factor, lockout, patching, audit trail, penetration test, vulnerability, procurement task
type: help
---
# Eine Sicherheitsanforderung für eine Ausschreibung schreiben

**Wer:** die Beschaffungsverantwortlichen mit dem Sicherheitsbeauftragten. **Wann:** beim Entwurf der Anforderungen.
Die Fakten stammen aus den [Sicherheitsstandards](../../../../../eng-us/standards/security.md) und der
[Sicherheitshärtung](../../../sicherheitshaertung.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Beispiel](#beispiel)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Lesen Sie die Zusammenfassung der [Sicherheitsstandards](../../../../../eng-us/standards/security.md#summary): OWASP Top 10
  **Teilweise erfüllt**, ASVS **Nicht geprüft**, CSP **Teilweise erfüllt**.
- Kennen Sie den Sicherheitsrahmen Ihrer Organisation (zum Beispiel das
  [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework) oder ISO/IEC 27001,
  [iso.org](https://www.iso.org/standard/27001)).

## Schritte

1. **Verlangen Sie eine Grundhärtung** per Verweis: die Schritte aus
   [13. Sicherheitshärtung für die Produktion](../../../../installation/13-sicherheitshaertung.md) und
   [Sicherheitshärtung](../../../sicherheitshaertung.md), mit dokumentierter Abweichung.
2. **Nennen Sie die Voreinstellungen, die geändert werden sollen**, weil sie ausgeschaltet oder offen ausgeliefert
   werden ([Sicherheitsstandards](../../../../../eng-us/standards/security.md#known-gaps)):
   - zweistufige Anmeldung für Administratoren ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md));
   - Kontosperre oder Ratenbegrenzung vor der Site;
   - Mindestlänge für Passwörter (ASVS Stufe 1 verlangt 12; die Voreinstellung ist 10);
   - HSTS, sobald die Site nur noch über HTTPS läuft;
   - Durchsetzen der Content Security Policy auf der öffentlichen Site, wo das Design es erlaubt.
3. **Verlangen Sie das Patchen**: veröffentlichte Sicherheitskorrekturen innerhalb einer von Ihnen festgelegten Zeit
   eingespielt ([Sicherheitsupdates und Patch-Rhythmus](../../../../../eng-us/help/government/procurement/lifecycle/security-updates-and-patching-cadence.md)).
4. **Verlangen Sie Protokollierung**: Audit-Protokoll eingeschaltet, Warnungen an benannte Personen, Kette regelmäßig
   verifiziert ([Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)).
5. **Verlangen Sie Tests**: wenn Ihre Regeln es verlangen, einen Penetrationstest der gelieferten Site durch einen
   unabhängigen Tester vor dem Go-live; das Projekt veröffentlicht keinen.
6. **Verlangen Sie Pflichten bei Vorfällen**: Meldefrist und Mitwirkung
   ([Bereitschaft für Vorfälle](../../checklisten/bereitschaft-fuer-vorfaelle.md)).

## Beispiel

```text
SEC-01  The Supplier shall harden the Site according to chapter 13 of the Exponential installation
        book and document every deviation.
SEC-02  Administrator accounts shall use two-step sign-in. Failed sign-ins shall be limited by
        <account lockout | a request filter>. Minimum password length: <n>.
SEC-03  Released security fixes of Exponential and its extensions shall be applied within <time>.
SEC-04  The audit trail shall be enabled, its alerts sent to <role>, its chain verified every <period>.
SEC-05  Before acceptance, <an independent tester> shall test the Site against <scope/method>;
        findings rated <level> or higher shall be fixed before acceptance.
```

## Ergebnis

Anforderungen, die ein Bieter mit einem Plan beantwortet und die Sie bei der Abnahme mit der
[Checkliste zur Sicherheit](../../checklisten/checkliste-sicherheit.md) prüfen.

## Fallstricke

- „Das System muss sicher sein“: Das kann niemand testen.
- Eine ASVS-Zertifizierung des Produkts verlangen, die es nicht gibt; verlangen Sie stattdessen eine Bewertung Ihrer
  Site.
- Vergessen, dass Korrekturen nur als neue Releases kommen: Ohne Pflicht zum Update erreicht Sie keine Korrektur.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Sicherheitsanforderungen](../../../../../eng-us/help/government/procurement/requirements/security-requirements.md)
- [Klausel zur Sicherheit](../../../../../eng-us/help/government/procurement/tender-templates/security-clause.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
