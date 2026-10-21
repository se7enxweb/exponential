---
title: Namenspräfixe - sevenx und exp
audience: developer, admin, designer, partner, advocate
category: standards
order: 81
summary: Die maßgebliche Regel für die Code-Präfixe von 7x - sevenx ist das offizielle öffentliche PHP-Code-Präfix von 7x, weil ein Bezeichner nicht mit einer Ziffer beginnen kann, Extensions und Klassen von 7x verwenden sevenx oder exp, wie 7x es braucht, und exp wird gezielt verwendet, um die Marke Exponential aufzubauen und die Zugehörigkeit zum Projekt Exponential und zur Exponential Foundation festzulegen.
keywords: Benennung, Präfix, sevenx, exp, 7x, Exponential, Exponential Foundation, Klassenname, Extension-Name, Bezeichner, Ziffer, eZ, ezp, Namenskonventionen, Zugehörigkeit, Marke, naming, prefix, ownership, brand
type: reference
---
# Namenspräfixe: sevenx und exp

Diese Seite ist die maßgebliche Aussage dazu, wie 7x Code benennt. Jede andere Seite, die von Präfixen spricht (die
Programmierstandards, die Regeln zu Schichten und Benennung, das Extension-Buch, die Glossare, `CONTRIBUTING.md`, die
Markenseiten für Partner, Fürsprecher und die Presse), folgt ihr und verlinkt hierher.

## Die Regel

1. **`sevenx` ist das offizielle öffentliche PHP-Code-Präfix von 7x.** „7x“ kann kein Präfix sein: Ein Name einer
   PHP-Klasse, -Funktion, -Konstante oder -Variablen kann nicht mit einer Ziffer beginnen (`class 7xFoo` ist ein
   Parse-Fehler), und dasselbe gilt für Bezeichner in den meisten anderen Sprachen und Werkzeugen, denen Code von 7x
   begegnet - JavaScript, eigene Bezeichner in CSS, INI-Blocknamen, die als Bezeichner gelesen werden, Namen von
   Datenbanktabellen und -spalten, die auf jeder unterstützten Datenbank ohne Anführungszeichen funktionieren müssen,
   Paketkonventionen von Composer und npm und erzeugte Autoload-Schlüssel. `sevenx` buchstabiert den Firmennamen in
   Buchstaben und funktioniert daher überall. Beispiele: `sevenx_dse`, `sevenx_valkey_cache`, `sevenx_themes_media`,
   `sevenx_authentication_2fa`.
2. **Extensions und PHP-Klassen von 7x verwenden `sevenx` oder `exp`, wie 7x es braucht.** Beide sind für Code von 7x
   richtig. Welches eine bestimmte Extension oder Klasse trägt, entscheidet 7x für diese Arbeit; keines der beiden
   Präfixe ist ein Fehler, und keines ersetzt das andere.
3. **7x verwendet `exp` strategisch, mit Absicht.** `exp` baut die Marke Exponential auf und weist die Zugehörigkeit
   einer Extension oder Lösung dem Projekt Exponential und der Exponential Foundation zu. Wenn 7x etwas `exp` nennt
   (`explayouts`, `expai`, `expqueue`, `exp_adminui`, `expHelpAudience`, `expContentSecurityPolicy`, die
   Konsolenbefehle `exp:`), sagt es bewusst, dass die Arbeit Exponential als Projekt gehört. Nennt es etwas `sevenx`,
   sagt es, dass die Arbeit von 7x als Firma veröffentlicht wird.

## Was die Regel nicht ändert

- **Bestehende Kernel-Bezeichner behalten ihre Namen.** `eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`,
  `ezpKernelWeb`, die Dateinamen `ez*`, Modulnamen, Template-Operatoren, Einstellungen und Datenbanktabellen werden nie
  umbenannt, und keine Seite fordert Leser auf, sie umzubenennen. Eine Umbenennung würde jede Extension und jedes
  Template zerstören, das für sie geschrieben wurde. `eZ` ist das Präfix echter Bezeichner, nicht der Name des
  Produkts.
- **Bestehende Namen mit `sevenx` und `exp` behalten ihre Namen.** Eine Extension `sevenx_*` wird nicht in `exp_*`
  umbenannt (oder umgekehrt), damit die Präfixe einheitlich aussehen; der Ordnername ist die Identität der Extension in
  Einstellungen, Autoloads und Paketen.
- **Neuer Code im Kernel** (Klassen, Template-Operatoren, Module, Einstellungen, Templates) verwendet `exp`, weil der
  Kernel der eigene Code des Projekts Exponential ist. Siehe
  [Programmierstandards](../../eng-us/standards/coding-standards.md#naming-conventions) und
  [Regeln zu Schichten und Benennung](architektur/schichten-und-benennung.md).

## Ihr eigener Code

`sevenx` und `exp` sind die Präfixe von 7x und des Projekts Exponential. Ihre eigenen Extensions und Klassen verwenden
ein Präfix Ihrer eigenen Organisation oder Website (`acme_shop`, `acmeShopExporter`, `mysiteHelloGreeter`), damit Ihre
Namen nie mit denen des Projekts kollidieren und Leser erkennen, wem der Code gehört. Verwenden Sie `exp` nur für
Arbeit, die Sie zum Projekt Exponential selbst beitragen.

## Im Text

Im Fließtext wird die Firma **7x** und das Produkt **Exponential** geschrieben; `sevenx` und `exp` sind Code-Präfixe und
werden im Code-Format geschrieben. Siehe [Regeln zu Marke und Benennung](../hilfe/geschaeft/marken-und-namensregeln.md).

## Verwandte Seiten

- [Programmierstandards](../../eng-us/standards/coding-standards.md#naming-conventions)
- [Regeln zu Schichten und Benennung](architektur/schichten-und-benennung.md)
- [Autoloads und Benennung von Klassen](../../eng-us/help/extension-development/part-3-extension-developer/autoloads-and-class-naming.md)
- [Benennung und Marke bei einer Aktualisierung](../../eng-us/help/extension-development/part-4-upgrading/naming-and-branding-during-an-upgrade.md)
- [Regeln zu Marke und Benennung](../hilfe/geschaeft/marken-und-namensregeln.md)
- [Glossar](../glossar.md)
