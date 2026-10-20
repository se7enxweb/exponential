---
title: Vorlage für Eskalationskontakte
audience: customer, admin, project-manager
category: customer-success
order: 12
summary: Eine Vorlage für die Kontaktliste, die das Team des Betreibers einer Exponential-Website an einem schlechten Tag braucht - interne Rollen, der Hoster, der Partner, die Kanäle des Projekts - mit Schweregrad, Zeiten und dem nächsten Schritt, wenn niemand antwortet, sowie Regeln, um sie aktuell und vertraulich zu halten.
keywords: Eskalation, Kontakte, Vorlage, Bereitschaft, Telefonliste, Hoster, Partner, Schweregrad, Vorfall, Sicherheitskontakt, escalation, contacts, template, on call, phone list, host, partner, severity, incident, security contact
type: help
---
# Vorlage für Eskalationskontakte

**Vorlage.** Füllen Sie sie an einem ruhigen Tag aus, bewahren Sie sie außerhalb der Website auf (ausgedruckt und an
einem gemeinsamen Ort, der nicht von der Website abhängt), und prüfen Sie sie jedes Quartal. An einem schlechten Tag
ersetzt sie eine Suche durch einen Anruf.

Schreiben Sie nur Rollen und geschäftliche Kontaktdaten hinein. Schreiben Sie keine Passwörter und keine privaten
Telefonnummern hinein, und machen Sie sie nur für die Personen lesbar, die sie brauchen.

## Interne Rollen

Der Vorfallplan für kleine Teams nennt drei Rollen: eine leitende Person, die entscheidet, eine Person, die behebt, und
eine Person, die kommuniziert ([Vorfallreaktion für kleine Teams](../../../../en/us/help/businesses/reliability/incident-response-for-small-teams.md)).

| Rolle | Name | Geschäftliche Telefonnummer | E-Mail | Vertretung |
|---|---|---|---|---|
| Eigentümer (entscheidet über Kosten, Zurücksetzen, öffentliche Erklärungen) | [name] | [phone] | [e-mail] | [name] |
| Leitung im Vorfall | [name] | [phone] | [e-mail] | [name] |
| Administrator der Website (Konten, Rollen, Wartungsmodus) | [name] | [phone] | [e-mail] | [name] |
| Kommunikation (Kunden, Mitarbeitende) | [name] | [phone] | [e-mail] | [name] |
| Kontakt für den Datenschutz | [name] | [phone] | [e-mail] | [name] |

## Anbieter

| Anbieter | Für | Kanal und Zeiten | Schweregrad 1 außerhalb der Zeiten | Konto- oder Vertragsnummer | Nächster Schritt ohne Antwort innerhalb [agreed time] |
|---|---|---|---|---|---|
| Hoster | Server, PHP, Datenbank, Backups, Zertifikate, E-Mail | [channel], [hours, time zone] | [phone of the on-call role] | [number] | [manager role, phone] |
| Partner | Templates, Designs, Erweiterungen, Einstellungen der Website | [channel], [hours, time zone] | [phone or "none"] | [number] | [manager role, phone] |
| Domain-Registrar und DNS | Domain, DNS-Einträge | [channel] | [phone or "none"] | [account] | [step] |
| Sonstige: [payment provider, mail service, CDN] | [for] | [channel] | [phone] | [number] | [step] |

Tragen Sie die Zeiten und Reaktionszeiten aus Ihren Vereinbarungen ein ([Vorlage für eine Support-Vereinbarung](vorlage-support-vereinbarung.md)).

## Das Projekt

| Für | Wo | Hinweise |
|---|---|---|
| Eine Sicherheitslücke in Exponential | security@se7enx.com (`SECURITY.md`) | Vertraulich; erste Antwort „innerhalb eines Werktags“ |
| Ein Fehler in Exponential | <https://github.com/se7enxweb/exponential/issues> | Öffentlich; meist von Ihrem Partner gemeldet ([GitHub-Issues](../gesund-halten/github-issues.md)) |
| Ein Fehler in Exponential Velocity | <https://github.com/se7enxweb/exponential-velocity/issues> | Öffentlich |
| Das Projekt und die Community | <https://github.com/se7enxweb/exponential-community/issues> | Öffentlich |
| Fragen | Die Foren und Chats der Community ([Support erhalten](../../projekt/unterstuetzung-erhalten.md)) | Keine zugesagte Antwortzeit |
| Bezahlter Support | 7x ([Mit 7x und dem Projekt zusammenarbeiten](mit-7x-und-dem-projekt-zusammenarbeiten.md)) | Im Rahmen eines Vertrags |

## Fakten, die griffbereit sein sollten

| Fakt | Wert |
|---|---|
| Adressen der Websites | [addresses] |
| Adresse der Administration | [address] |
| Version von Exponential, zuletzt geprüft | [version], [date] |
| Engine und Datenbank | [Velocity / Apache or nginx with PHP-FPM / other], [database] |
| Wo die Backups liegen und wer wiederherstellen kann | [place], [role] |
| Wo das Notfallverfahren aufgeschrieben ist | [place, never the password] |

## Aktuell halten

- [ ] Jedes Quartal geprüft, und wann immer jemand kommt oder geht oder ein Anbieter wechselt.
- [ ] Jede Nummer einmal getestet (ein kurzer Anruf genügt).
- [ ] Stimmt mit [Wer was macht](wer-was-macht.md) überein.
- [ ] Eine Kopie dort aufbewahrt, wo sie erreichbar ist, wenn die Website und Ihre E-Mail ausgefallen sind.

## Verwandte Seiten

- [Wann Sie wen anrufen](wann-sie-wen-anrufen.md)
- [Wenn die Website nicht erreichbar ist](../gesund-halten/wenn-die-website-nicht-erreichbar-ist.md)
- [Vorlage für Vorfallnotizen](../gesund-halten/vorlage-vorfallnotizen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
