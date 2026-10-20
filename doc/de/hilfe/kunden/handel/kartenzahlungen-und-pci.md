---
title: Kartenzahlungen und PCI
audience: customer, admin, businesses
category: customer-success
order: 120
summary: Die Kartensicherheits-Routine des Betreibers eines Exponential-Shops - wissen, wo Kartennummern eingegeben werden, die jährlichen Fragen an Ihren Zahlungsanbieter, was nach Änderungen zu prüfen ist und welche besondere Sorgfalt ein Kartenformular auf der eigenen Website verlangt. Grundlagen, keine Compliance- oder Rechtsberatung.
keywords: Kartenzahlungen, PCI, PCI DSS, Kartensicherheit, Kreditkarte, Weiterleitung, ezauthorize, Kartendaten, Zahlungsanbieter, Selbstbewertung, keine Rechtsberatung, card payments, card security, credit card, redirect gateway, card data, payment provider, self-assessment
type: help
---
# Kartenzahlungen und PCI

Diese Seite ist für Betreiber, die Kartenzahlungen annehmen, direkt oder über einen Anbieter. Sie macht aus der
Kartensicherheit eine Routine, die Sie einhalten können: wissen, wie Karten in Ihren Shop gelangen, die Nachweise
aufbewahren, die Ihr Anbieter verlangt, und nach Änderungen prüfen. Den Hintergrund, und was die Zahlungsanbindungen
des Projekts laut ihrem Code mit Kartendaten tun, finden Sie unter
[Grundlagen zu Kartenzahlungen und PCI für Unternehmen](../../../../en/us/help/businesses/stores/card-payments-and-pci-basics.md).

> **Warnung**
> Grundlagen, keine Rechts- oder Compliance-Beratung. Pflichten zur Kartensicherheit ergeben sich aus dem Payment Card
> Industry Data Security Standard (PCI DSS) und Ihrem Vertrag mit Ihrem Zahlungsanbieter oder Ihrer acquirierenden
> Bank. Fragen Sie Ihren Anbieter, welche Pflichten für Ihre Anbindung gelten, und ziehen Sie eine qualifizierte
> Beratung hinzu, wo diese es verlangen.

## Die eine Frage, die Sie beantworten müssen

**Wo gibt Ihr Kunde die Kartennummer ein?** Schreiben Sie die Antwort auf und bewahren Sie sie bei Ihrem Vertrag auf.

| Antwort | Art der Anbindung | Was das für Sie bedeutet |
|---|---|---|
| Auf der Seite des Anbieters | Weiterleitung, zum Beispiel `ezpaypal` | Kartennummern laufen nicht über Ihren Server |
| In einem Formular auf Ihrer Website | Formular auf der eigenen Website, zum Beispiel `ezauthorize` | Kartennummern laufen auf dem Weg zum Anbieter über Ihren Server |

Quelle: [Grundlagen zu Kartenzahlungen und PCI für Unternehmen](../../../../en/us/help/businesses/stores/card-payments-and-pci-basics.md).
Wenn Sie nicht sicher sind, welche Variante Ihr Shop verwendet, fragen Sie Ihren Partner; die Antwort entscheidet
über das meiste, was folgt.

## Eine jährliche Routine

| Aufgabe | Wer | Womit |
|---|---|---|
| Mit dem Anbieter klären, welche Pflichten für Ihre Anbindung gelten | Eigentümer | Die Unterlagen des Anbieters |
| Die Selbstbewertung oder die Nachweise erbringen, die der Anbieter verlangt | Eigentümer, mit dem Partner | Die Formulare des Anbieters |
| Bestätigen, dass die Erweiterung für die Zahlungsanbindung und Exponential aktuell sind | Partner oder Hoster | [Upgrade](../../../../en/us/guides/upgrading.md) |
| Prüfen, wer Administrator- und Shop-Zugriff hat | Eigentümer | [Rollen und Richtlinien](../../../../en/us/guides/roles-and-policies.md) |
| Die Anmeldung in zwei Schritten für Administratoren und Shop-Personal bestätigen | Eigentümer, mit dem Hoster | [Zwei-Faktor-Anmeldung](../../../../en/us/guides/two-factor-login.md) |

## Nach jeder Änderung am Shop

- [ ] Die ganze Website, und vor allem die Kasse, läuft weiterhin über HTTPS.
- [ ] Testmodus- oder Sandbox-Adressen sind nicht aktiv geblieben.
- [ ] Die Debug-Ausgabe ist auf der Live-Website ausgeschaltet; Logs und Fehlerseiten zeigen keine Kartendaten
  ([Sicherheitshärtung](../../../../en/us/help/security-hardening.md)).
- [ ] Backups mit Bestelldaten sind weiterhin so geschützt wie die Datenbank selbst.

## Wenn Ihr Shop ein Kartenformular auf der eigenen Website verwendet

Die Erweiterung `ezauthorize` zeigt ein Kartenformular auf Ihrer Website und sendet die Daten an Authorize.Net. Laut
ihrem Code werden die Kartendaten in die gespeicherten Daten der Bestellung geschrieben, und sie werden nur
verschlüsselt, wenn die separate Erweiterung `ezgpg` installiert und mit einem Schlüssel konfiguriert ist
([Grundlagen zu Kartenzahlungen und PCI für Unternehmen](../../../../en/us/help/businesses/stores/card-payments-and-pci-basics.md)).

> **Warnung**
> Betreiben Sie kein Kartenformular auf der eigenen Website, ohne dass ein Entwickler anhand der Anforderungen Ihres
> Anbieters geprüft hat, was es speichert und wie lange. Lassen Sie sich diese Prüfung von Ihrem Partner schriftlich
> geben, und wiederholen Sie sie nach jedem Update der Erweiterung.

Fragen an Ihren Partner:

- Welche Kartendaten speichert unsere Zahlungsanbindung, wo, und sind sie verschlüsselt?
- Werden Kartendaten, insbesondere der Sicherheitscode, nach der Autorisierung aufbewahrt?
- Bietet unser Anbieter eine gehostete Zahlungsseite oder gehostete Kartenfelder an, damit Kartendaten nicht auf unseren Server gelangen?

## Fragen an Ihren Zahlungsanbieter

Aus [Grundlagen zu Kartenzahlungen und PCI für Unternehmen](../../../../en/us/help/businesses/stores/card-payments-and-pci-basics.md):

- Welche Pflichten zur Kartensicherheit gelten für eine Website, die zu Ihnen weiterleitet, und für eine, die Kartendaten an Sie sendet?
- Welche Selbstbewertung oder welche Nachweise verlangen Sie von uns, und wie oft?
- Welche Integrationsmethoden und Signaturprüfungen unterstützen Sie noch?
- Wer bearbeitet Reklamationen und Rückbuchungen, und welche Unterlagen brauchen Sie dafür von uns?

## Wenn Kartendaten offengelegt worden sein könnten

1. Informieren Sie sofort Ihren Zahlungsanbieter; er sagt Ihnen, was als Nächstes zu tun ist.
2. Bitten Sie Ihren Hoster, seinem Runbook für eine kompromittierte Website zu folgen
   ([Kompromittierte Website](../../../../en/us/help/hosting/runbooks/compromised-site.md)).
3. Melden Sie eine vermutete Sicherheitslücke in Exponential vertraulich an security@se7enx.com, wie es `SECURITY.md`
   verlangt, nie in einem öffentlichen Issue.
4. Notieren Sie, was wann passiert ist.

## Verwandte Seiten

- [Zahlungsanbieter](zahlungsanbieter.md)
- [Grundlagen zu Kartenzahlungen und PCI für Unternehmen](../../../../en/us/help/businesses/stores/card-payments-and-pci-basics.md)
- [Sicherheits-Checkliste für den Shop](../../../../en/us/help/businesses/stores/store-security-checklist.md)
- [Sicherheitshärtung](../../../../en/us/help/security-hardening.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
