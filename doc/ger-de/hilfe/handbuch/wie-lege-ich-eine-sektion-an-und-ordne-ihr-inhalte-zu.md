---
title: Wie lege ich eine Sektion an und ordne ihr Inhalte zu?
audience: admin
category: manual
order: 600
summary: Legen Sie die Sektion unter Setup > Sektionen mit einem Namen, einem Bezeichner und einem Navigationsbereich an und verschieben Sie dann mit Inhalt zuweisen auf ihrer Karte einen Teil des Inhaltsbaums hinein.
keywords: Sektion, neue Sektion, Inhalt zuweisen, Bezeichner, Navigationsbereich, Teilbaum, Rechte, Sektion entfernen, section/assign, section/edit, section, new section, assign content, identifier, navigation part, subtree, permissions, remove section
context: section/list
type: help
---
# Wie lege ich eine Sektion an und ordne ihr Inhalte zu?

Eine Sektion ist eine Kennzeichnung, die jedes Objekt trägt, genau eine pro Objekt. Rollen entscheiden damit, wer was
lesen oder bearbeiten darf, die Administration wählt damit den aktiven Tab im oberen Menü, und Designs können einer
Sektion eigene Templates geben. Sie legen zuerst die Kennzeichnung an und ordnen ihr dann Inhalte zu.

## Wer das tun kann

- Sektionen anlegen und bearbeiten erfordert die Richtlinie `section/edit`.
- Inhalte einer Sektion zuordnen erfordert `section/assign`. Diese Richtlinie lässt sich eingrenzen: Ihre Einschränkung
  **NewSection** listet die Sektionen auf, die Sie vergeben dürfen, und ihre Einschränkungen Class, Owner und Section
  entscheiden, welche Objekte Sie ändern dürfen.
- Administratoren haben beides.

## Schritte

1. Klicken Sie im oberen Menü auf **Setup** und wählen Sie **Sektionen**.
   Die Liste der Sektionen öffnet sich, mit Zahlen zu jeder einzelnen.
2. Klicken Sie auf **Neue Sektion**.
3. Füllen Sie **Name** aus, zum Beispiel „Members area“.
   Der **Bezeichner** wird beim Tippen aus dem Namen vorgeschlagen (hier `members_area`). Ändern Sie ihn, wenn Sie
   möchten: Buchstaben, Ziffern und `_`, beginnend mit einem Buchstaben, von keiner anderen Sektion verwendet.
4. Lassen Sie **Navigationsbereich** für gewöhnliche Seiten auf **Inhaltsstruktur**.
5. Klicken Sie auf **Sektion anlegen**.
   Die Liste öffnet sich wieder und meldet, dass die Sektion angelegt wurde. Sie ist leer.
6. Klicken Sie auf der Karte der neuen Sektion auf **Inhalt zuweisen**.
   Der Inhaltsbrowser öffnet sich.
7. Suchen Sie das Element an der Spitze des Baumteils, der in die Sektion gehört, wählen Sie es mit seinem
   Optionsfeld aus und klicken Sie auf **Auswählen**.
   Das Element und die Inhalte darunter tragen jetzt die Sektion. Bei einem sehr großen Teilbaum werden Sie gefragt,
   ob es sofort oder als Hintergrundauftrag geschehen soll.
8. Öffnen Sie die Sektion mit **Ansehen** und prüfen Sie die Anzahl der veröffentlichten Objekte.

Um einem einzelnen Objekt eine andere Sektion zu geben, öffnen Sie es, wechseln Sie in den Tab **Details**, wählen Sie
die Sektion und klicken Sie auf **Setzen**.

## Was schiefgehen kann

- **Die Inhalte sind von der öffentlichen Website verschwunden.** Das ist die übliche erste Wirkung: Besucher dürfen
  nur die Sektionen lesen, die ihre Rolle nennt, normalerweise nur Standard. Fügen Sie der Rolle, die die Inhalte sehen
  soll, eine Richtlinie `content/read` für die neue Sektion hinzu
  ([Wie richte ich einen Bereich nur für Mitglieder ein?](wie-richte-ich-einen-bereich-nur-fuer-mitglieder-ein.md)).
- **Einige Objekte unter dem Element haben ihre alte Sektion behalten.** Das Zuordnen ändert die Objekte, deren
  Hauptort in diesem Baumteil liegt. Ein Objekt, das dort nur einen zusätzlichen Ort hat, behält seine Sektion, denn
  ein Objekt hat eine einzige Sektion, egal an welcher Stelle es angezeigt wird.
- **Inhalt zuweisen ist ausgegraut.** Ihre Richtlinie `section/assign` bietet diese Sektion nicht an. Der Tooltip
  nennt den Grund.
- **Neue Seiten landen in der falschen Sektion.** Neue Inhalte übernehmen beim Veröffentlichen die Sektion ihres
  übergeordneten Elements. Wenn Sie einen Teilbaum verschieben, wird die Sektion des neuen übergeordneten Elements auf
  die Objekte übertragen, die noch die Sektion des alten übergeordneten Elements hatten.
- **Die Sektion lässt sich nicht entfernen.** **Ausgewählte entfernen** lehnt eine Sektion ab, die noch Objekte enthält
  (Entwürfe und archivierte Versionen zählen mit) oder die eine Richtlinie oder eine Rollenzuweisung nennt. Verschieben
  Sie die Objekte mit **Inhalt zuweisen** auf der Karte einer anderen Sektion dorthin, und entfernen Sie zuerst die
  Einschränkung aus den Rollen.
- **Ein umbenannter Bezeichner hat ein Template beschädigt.** Override-Regeln, Fetches und Templates können die Sektion
  über ihren Bezeichner nennen. Rollen nennen sie über ihre ID; das Umbenennen der Sektion selbst ändert also keine
  Rechte.

## Verwandte Seiten

- [Sektionen](../../anleitungen/sektionen.md): jede Seite und Regel zu Sektionen im Detail
- [Wie gebe ich einer Gruppe Bearbeitungsrechte nur für eine Sektion?](wie-gebe-ich-einer-gruppe-bearbeitungsrechte-fuer-eine-sektion.md)
- [Wie richte ich einen Bereich nur für Mitglieder ein?](wie-richte-ich-einen-bereich-nur-fuer-mitglieder-ein.md)
- [Sektionen, Rollen und Richtlinien](../../../eng-us/help/cms-developers/sections-roles-and-policies.md)
- [Override nach Sektion](../designer/vorlagen/override-nach-sektion.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
