---
title: Rollen für neue Mitarbeitende wählen
audience: customer, admin, project-manager
category: customer-success
order: 3
summary: Eine kurze Entscheidungshilfe für den Betreiber oder die Leitung, wenn eine neue Person dazukommt - welche Gruppe und Rolle Sie anfordern, welche Einschränkungen Sie hinzufügen, was Sie weglassen und wie Sie die Anfrage schreiben, damit der Administrator danach handeln kann.
keywords: neue Mitarbeitende, Rollen, Benutzergruppe, Redakteur, freigebende Person, Übersetzer, minimale Rechte, Zugriffsanfrage, Einschränkung, Einarbeitung, new staff, roles, user group, editor, approver, translator, least privilege, access request, limitation, onboarding
type: help
---
# Rollen für neue Mitarbeitende wählen

Diese Seite ist für die Leitung, die dem Administrator sagen muss, was ein neuer Kollege tun darf. Die Regel ist
einfach: Geben Sie die kleinste Rolle, mit der die Person ihre Arbeit erledigen kann, im kleinsten Teil der Website,
und fügen Sie mehr erst hinzu, wenn es gebraucht wird. Exponential macht das praktikabel, weil sich eine Rolle auf
eine Sektion, einen Teilbaum oder eine Sprache einschränken lässt ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Inhalt

1. [Fünf Fragen](#fünf-fragen)
2. [Häufige Antworten](#häufige-antworten)
3. [Was Sie weglassen sollten](#was-sie-weglassen-sollten)
4. [Die Anfrage schreiben](#die-anfrage-schreiben)

## Fünf Fragen

1. **Was wird die Person im ersten Monat tun?** Schreiben, veröffentlichen, freigeben, übersetzen, Formulare
   bearbeiten, den Shop betreiben?
2. **Wo im Baum?** Die ganze Website, der Ordner einer Abteilung, eine Marke einer Multisite-Installation?
3. **In welchen Sprachen?** Alle, oder nur die, in die sie übersetzt?
4. **Muss ihre Arbeit geprüft werden, bevor sie online geht?** Dann gehört sie hinter eine Freigabe
   ([Freigabe und Prüfung](freigabe-und-pruefung.md)).
5. **Braucht sie die Administration überhaupt?** Mitglieder und Kunden melden sich nur auf der öffentlichen Website an.

## Häufige Antworten

| Neue Person | Anzufordernde Gruppe und Rolle | Einschränkungen |
|---|---|---|
| Redakteur in einer Abteilung | die Gruppe der Redakteure mit der Rolle Editor | zugewiesen mit einer Einschränkung auf Teilbaum oder Sektion der Abteilung ([Abteilungen, Rollen und Sektionen](../../unternehmen/intranets/abteilungen-rollen-und-sektionen.md)) |
| Nachwuchsautor oder Praktikant | die Gruppe der Redakteure, nicht unter den *Ausgenommenen Benutzergruppen* der Freigabe, sodass ihre Versionen auf eine freigebende Person warten | wie oben, in einer Sektion, die die Freigabe abdeckt |
| Freigebende Person oder Chefredaktion | in die Gruppe der freigebenden Personen des Freigabe-Workflows aufgenommen | die betroffenen Sektionen des Workflows ([Workflows](../../../anleitungen/workflows.md)) |
| Übersetzer oder Agentur | eine Übersetzerrolle | Bearbeiten, Übersetzen und Anlegen nach Sprache eingeschränkt ([Übersetzungs-Workflow](../../unternehmen/mehrsprachig/uebersetzungsablauf.md)) |
| Person, die Kontaktformulare beantwortet | eine Rolle mit `infocollector/read` | sonst nichts ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) |
| Shop-Personal | eine Shop-Rolle, die Ihr Partner eingerichtet hat | der Teil des Baums, der zum Shop gehört ([Shop-Dashboard](../../../funktionen/6.0/shop-dashboard.md)) |
| Vorübergehende Hilfe | die kleinste der obigen | ein Enddatum in Ihren eigenen Notizen, siehe [Ausscheiden von Mitarbeitenden](ausscheiden-von-mitarbeitenden.md) |

> **Tipp**
> Wenn zwei Personen „etwas mehr“ brauchen als die Rolle Editor, fordern Sie eine zweite Rolle mit nur diesem Zusatz an
> und weisen Sie beide zu. Eine Änderung an der gemeinsamen Rolle Editor ändert sie für alle, die sie haben.

## Was Sie weglassen sollten

- **Vollzugriff.** Administrator ist für die wenigen Personen gedacht, die die Installation betreiben, nicht für die
  Person, die „nur eine Einstellung ändern muss“.
- **Das Modul role.** Wer Rollen ändern darf, kann sich selbst alles geben; die Rollenliste kennzeichnet eine solche
  Rolle bernsteinfarben.
- **Kontenüberprüfung** (`user/hygiene`) und **Prüfprotokoll**-Richtlinien: Lassen Sie diese bei der Person, die die
  Konten verwaltet, und beim Eigentümer.
- **Geteilte Konten.** Ein persönliches Konto pro Person; keine geteilten Anmeldungen
  ([Die Website an die Redaktion übergeben](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md)).

## Die Anfrage schreiben

Schicken Sie dem Administrator eine Anfrage, nach der er handeln kann, ohne nachfragen zu müssen:

```text
ACCESS REQUEST
Name: ______________________   Start date: __________   End date (if temporary): __________
Job on the site: ______________________________________________
Group: ______________________   Role(s): ______________________
Limited to subtree/section: ______________________   Languages: ______________
Behind an approval? yes / no      Two-step sign-in required? yes / no
Requested by: ______________   Approved by (site owner): ______________
```

Bewahren Sie die Anfrage auf: Sie ist der Nachweis, mit dem Sie bei der jährlichen
[Zugriffsüberprüfung](../../unternehmen/sicherheit/zugriffspruefungen.md) vergleichen.

## Verwandte Seiten

- [Rollen für Ihr Team](rollen-fuer-ihr-team.md)
- [Neue Mitarbeitende einarbeiten](neue-mitarbeitende-einarbeiten.md)
- [Arbeitsblatt für Teamrollen](arbeitsblatt-teamrollen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
