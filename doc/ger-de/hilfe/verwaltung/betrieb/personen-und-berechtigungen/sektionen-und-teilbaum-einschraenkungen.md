---
title: Sektionen und Teilbaum-Einschränkungen
audience: government, admin
category: public-sector
order: 30
summary: Wann Sie den Zugang der Beschäftigten nach Sektion und wann nach Teilbaum des Inhaltsbaums einschränken, wie sich beides verhält, wenn Inhalte verschoben werden, und wie eine öffentliche Stelle mit vielen Fachbereichen, geschützten Bereichen und gemeinsamen Seiten beides kombiniert.
keywords: Sektionen, Teilbaum, Teilbaum-Einschränkung, Sektionseinschränkung, Fachbereiche, geschützte Inhalte, Intranet, Rolle Anonymous, Mit Einschränkungen zuordnen, section/assign, NewSection, Inhaltsbaum, sections, subtree, subtree limitation, section limitation, departments, restricted content, intranet, Anonymous role, Assign with limitation, content tree
type: help
---
# Sektionen und Teilbaum-Einschränkungen

Diese Seite richtet sich an Administratoren, die entscheiden, wie der Zugang der Beschäftigten der Struktur einer öffentlichen Website folgt:
ein Teilbaum je Fachbereich, eine Sektion für geschützte Inhalte, gemeinsame Seiten, die mehrere Teams bearbeiten. Sie fasst die zwei
Mechanismen so zusammen, wie [Sektionen](../../../../anleitungen/sektionen.md) und
[Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md) sie beschreiben, und gibt eine
Entscheidungshilfe.

## Inhalt
1. [Zwei Arten, eine Grenze zu ziehen](#zwei-arten-eine-grenze-zu-ziehen)
2. [Was geschieht, wenn Inhalte verschoben werden](#was-geschieht-wenn-inhalte-verschoben-werden)
3. [Was Sie verwenden sollten](#was-sie-verwenden-sollten)
4. [Ein typischer Aufbau im öffentlichen Sektor](#ein-typischer-aufbau-im-öffentlichen-sektor)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Zwei Arten, eine Grenze zu ziehen

| | Sektion | Teilbaum |
|---|---|---|
| Was es ist | Eine benannte Gruppe von Objekten; jedes Objekt liegt in genau einer Sektion | Eine Platzierung im Inhaltsbaum und alles darunter |
| Wo es verwaltet wird | **Setup > Sektionen** (`/section/list`) | der Inhaltsbaum selbst |
| In Richtlinien verwendet | die Einschränkung `Section` der meisten `content`-Funktionen | die Einschränkung `Subtree` der meisten `content`-Funktionen |
| In Zuweisungen verwendet | **Mit Einschränkungen zuordnen: Sektion** | **Mit Einschränkungen zuordnen: Teilbaum** |
| Folgt einem Objekt mit zwei Platzierungen | ja, eine Sektion für das Objekt | nur der Platzierung innerhalb des Teilbaums |
| Anderweitig verwendet | Zugang für Besucher (die Rolle Anonymous liest nur die Sektion Standard), Template-Overrides nach Sektion, der Menü-Tab der Administration | URLs und Navigation |

Die Standardinstallation hat die Sektionen Standard (1), Users (2), Media (3), Setup (4) und Design (5), und die Rolle Anonymous darf nur
Standard lesen ([Sektionen, Abschnitt 2](../../../../anleitungen/sektionen.md#2-die-sektionen-einer-neuen-installation)). Diese eine Regel ist der Grund,
warum Inhalte in einer neuen Sektion für Besucher unsichtbar sind, bis eine Richtlinie sie erlaubt.

## Was geschieht, wenn Inhalte verschoben werden

- **Neue Inhalte übernehmen die Sektion ihres übergeordneten Elements.**
- **Das Verschieben eines Teilbaums** gibt jedem darunterliegenden Objekt, das noch die Sektion des alten übergeordneten Elements hatte, die
  Sektion des neuen übergeordneten Elements; Objekte mit einer eigenen Sektion behalten sie.
- **Inhalt zuweisen** an einer Sektion legt ein Element und alles darunter in diese Sektion; große Teilbäume können als Inhaltsjob laufen
  ([Sektionen, Abschnitt 8](../../../../anleitungen/sektionen.md#8-in-welcher-sektion-inhalte-liegen-anlegen-verschieben-und-zuweisen)).
- Eine **Teilbaum-Einschränkung** folgt dem Baum: Ein Objekt, das aus dem Teilbaum verschoben wird, fällt sofort aus der Richtlinie heraus.

## Was Sie verwenden sollten

| Sie brauchen | Verwenden Sie |
|---|---|
| Jeder Fachbereich bearbeitet seine eigenen Seiten, die im Baum beieinanderliegen | eine Teilbaum-Einschränkung je Fachbereich (eine Rolle, je Fachbereich mit **Mit Einschränkungen zuordnen: Teilbaum** zugewiesen) |
| Inhalte, die Besuchern verborgen bleiben müssen, wo immer sie liegen (interne Entwürfe, vertrauliche Dokumente) | eine Sektion, die die Rolle Anonymous nicht liest |
| Dieselbe Redaktionsrolle für mehrere Bereiche | eine Rolle, mehrere eingeschränkte Zuweisungen |
| Ein Team, das einen Inhaltstyp überall bearbeitet (zum Beispiel Nachrichten) | eine Einschränkung `Class`, kombiniert mit einem Teilbaum oder einer Sektion |
| Ein öffentlicher Bereich und ein Mitgliederbereich unter einem Ordner | Sektionen, weil ein Ordner Objekte mehrerer Sektionen enthalten kann |

Im Zweifel verwenden Sie Teilbäume für „wer bearbeitet wo“ und Sektionen für „wer darf was sehen“. So bleiben der Zugang für Besucher und der
Zugang der Beschäftigten an verschiedenen Stellen, was sich einem Prüfer leichter erklären lässt.

## Ein typischer Aufbau im öffentlichen Sektor

Ein Beispielaufbau für die Website des „Fachbereichs Beispiel“ (Platzhalter, passen Sie ihn an Ihre eigene Struktur an):

| Baum | Sektion | Wer bearbeitet | Wie |
|---|---|---|---|
| `/` Startseite, Über uns, Kontakt | Standard | Web-Team | Rolle „Web-Team“ ohne Einschränkung auf Inhalte |
| `/services/<department>/` | Standard | Redaktionen der Fachbereiche | Rolle „Fachbereichsredaktion“, je Gruppe eines Fachbereichs mit einer Teilbaum-Einschränkung zugewiesen |
| `/news/` | Standard | Kommunikation | Rolle „Fachbereichsredaktion“, Teilbaum `/news/` |
| `/internal/` | eine neue Sektion „Intern“ | Web-Team | Besucher lesen sie nicht, weil Anonymous nur Standard liest |
| Medienbibliothek | Media | alle Redaktionen (Hochladen) | `content/create` eingeschränkt auf den Medienteilbaum und die Klassen für Bilder und Dateien |

Die Rollenentwürfe unter [Redaktionen der Fachbereiche](rollenentwurf-fachredaktionen.md) und
[Web-Team](rollenentwurf-webteam.md) bauen genau diese Richtlinien auf.

## Fallstricke

- **Eine neue Sektion ist für Besucher unsichtbar.** Wenn Sie eine Sektion für einen neuen öffentlichen Bereich anlegen, geben Sie der Rolle
  Anonymous auch dafür `content/read` ([Besucher eine neue Sektion lesen lassen](../../../../../eng-us/help/government/tasks/people-and-permissions/let-visitors-read-a-new-section.md)).
- **Das Entfernen einer Sektion** ist nur möglich, wenn kein Objekt, keine Richtlinie und keine Rollenzuweisung sie verwendet; es gibt kein
  Rückgängig, und eine neue Sektion mit demselben Namen erhält eine neue ID ([Sektionen, Abschnitt 7](../../../../anleitungen/sektionen.md#7-eine-sektion-entfernen)).
- **Sektionskennungen** werden von Templates verwendet; eine Umbenennung kann ein Design beschädigen
  ([Sektionen, Abschnitt 6](../../../../anleitungen/sektionen.md#6-eine-sektion-bearbeiten-und-was-eine-änderung-betrifft)).
- **Wer Inhalte in eine Sektion legen darf**, ist selbst ein Recht: `section/assign` mit `NewSection`. Ohne diese Einschränkung darf jede
  Sektion zugewiesen werden ([Sektionen, Abschnitt 9.3](../../../../anleitungen/sektionen.md#93-wer-sektionen-zuweisen-darf-sectionassign)).
- **Weitere Platzierungen** eines Objekts außerhalb des Teilbaums eines Fachbereichs werden von dessen Teilbaum-Einschränkung nicht abgedeckt.

## Verwandte Seiten
- [Rollen, Richtlinien und Einschränkungen](rollen-richtlinien-und-einschraenkungen.md)
- [Eine Sektion für geschützte Inhalte anlegen](../../../../../eng-us/help/government/tasks/people-and-permissions/create-a-section-for-restricted-content.md)
- [Eine Rolle mit einer Teilbaum-Einschränkung zuweisen](../../../../../eng-us/help/government/tasks/people-and-permissions/assign-a-role-with-a-subtree-limitation.md)
- [Eine Rolle mit einer Sektionseinschränkung zuweisen](../../../../../eng-us/help/government/tasks/people-and-permissions/assign-a-role-with-a-section-limitation.md)
- [Leitfaden zu Sektionen](../../../../anleitungen/sektionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
