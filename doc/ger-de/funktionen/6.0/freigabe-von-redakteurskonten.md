# Administratoren geben neue Redakteurskonten frei

**Für:** Website-Betreiber, Administratoren und Redakteure. **Seit:** 6.0.15.

Ein Redakteur darf das Konto eines neuen Redakteurs anlegen, aber das Konto wartet, bis ein Administrator es freigibt.
Vorher ist es nicht veröffentlicht und kann sich nicht anmelden. Die Administratoren bekommen eine E-Mail mit einem Link
zur Freigabe; sie melden sich an und geben in der Administration frei oder lehnen ab, so wie sie Inhalte freigeben.

## Kurz gesagt

- Redakteure legen Konten unter **Benutzer > Editors** an. Das Konto wird angehalten: *Wartet auf Freigabe*.
- Jeder Administrator bekommt die Mail **New user account waiting for approval**. Ihr einziger Link öffnet das
  Freigabeelement in der Administration, nach der Anmeldung. Die Mail gibt selbst nichts frei und enthält keinen
  Schlüssel.
- **Freigeben** veröffentlicht das Konto sofort: es kann sich anmelden. **Ablehnen** lässt es unveröffentlicht; es ist
  wieder ein Entwurf des Redakteurs.
- Konten, die Administratoren selbst in Editors anlegen, werden direkt veröffentlicht.
- Ein Redakteur kann kein Mitglied nach Editors verschieben und keinem Konto dort einen Ort geben: das würde die Freigabe
  umgehen.
- Alles steht im Prüfprotokoll: Anfrage, Freigabe, Ablehnung, abgewiesene Versuche.
- Welche Gruppen eine Freigabe brauchen, wer freigibt und wer keine braucht, sind die Einstellungen des
  Workflow-Ereignisses *Approve user accounts* unter Setup > Workflows. Eine neue Installation hat sie; eine bestehende
  führt `php bin/php/adduserapproval.php --apply` aus.

## Für Redakteure

1. **Benutzer > Editors** öffnen und unter **Hier anlegen** *Benutzerkonto* wählen.
2. Name, Benutzername, E-Mail-Adresse und Passwort eintragen und **Zur Veröffentlichung senden** drücken.
3. Die Seite sagt, dass das Objekt auf Freigabe wartet. Das Konto steht in Ihrer *Kollaboration* unter *Ihre Elemente, die auf andere warten* und unter *Meine ausstehenden
   Elemente*.
4. Nach der Freigabe meldet sich der neue Redakteur mit dem Benutzernamen und Passwort an, die Sie gesetzt haben. Nach
   einer Ablehnung ist das Konto wieder in Ihren Entwürfen: ändern und erneut senden, oder den Entwurf entfernen.

Die Konten der anderen Redakteure sehen und ändern Sie nicht; Editors zeigt nur, was Sie dort anlegen.

## Für Administratoren

1. Die Mail *New user account waiting for approval: <Name>* nennt das Konto, den Benutzernamen, die Gruppe und wer gefragt
   hat.
2. **Open the approval** drücken (oder Dashboard > Kollaboration > *Wartet auf Ihre Entscheidung*). Bei Bedarf anmelden.
3. Die Freigabeseite zeigt das Konto. Optional einen Kommentar schreiben, dann **Freigeben** oder **Ablehnen** drücken
   und bestätigen.
4. Die Meldung sagt, was geschehen ist: *Das Konto ... wurde freigegeben und veröffentlicht: es kann sich jetzt
   anmelden*, oder *... wurde abgelehnt*.

Die Freigabe sehen alle Administratoren; die erste Entscheidung gilt, danach zeigt das Element für alle *Freigegeben*
oder *Abgelehnt*.

## Einstellungen

Setup > Workflows > *Approve new editor accounts* > das Ereignis *Approve user accounts*:

- **Benutzergruppen, deren neue Konten eine Freigabe brauchen** (Standard Editors)
- **Benutzer, die freigeben** und **Gruppen, die freigeben** (Standard Administrator users)
- **Benutzergruppen ohne Freigabe**
- **E-Mail an die Freigebenden** (Standard an)

Die Adresse in der Mail ist die der Administration: setzen Sie `site.ini [UserSettings] RegistrationAdminBaseURL`, wenn
sie einen eigenen Host hat (zum Beispiel `https://admin.example.com`). Auch die Kollaborationsbenachrichtigungen
verlinken dorthin.

## Oder nach Abschnitt

Statt des Ereignisses oben können Sie das einfache Ereignis *Freigabe* des Kernels verwenden: einen Abschnitt **Staff**
für Administrator users und Editors und ein Freigabe-Ereignis für diesen Abschnitt (Freigebende und ohne Freigabe:
Administrator users, nur neue Objekte). `php bin/php/adduserapproval.php --mode=section` zeigt, was er tun würde,
`--apply` führt es aus. Die Freigebenden bekommen dieselbe Mail mit dem Link, Freigeben und Ablehnen werden ebenso
bestätigt und festgehalten. Wählen Sie einen der beiden Wege, nie beide: ein Konto würde zweimal angefragt, darum
verweigert der Befehl, solange das Ereignis *Benutzerkonten freigeben* läuft. Wann welcher Weg:
[Zwei Wege](../../bc/6.0/freigabe-von-benutzerkonten.md#zwei-wege-nach-gruppe-standard-oder-nach-abschnitt).

Mehr: die technische Notiz [Freigabe neuer Benutzerkonten](../../bc/6.0/freigabe-von-benutzerkonten.md), die Anleitung
[Neue Redakteurskonten freigeben](../../anleitungen/neue-redakteurskonten-freigeben.md) und die
[Mitgliederverwaltung](../../bc/6.0/mitgliederverwaltung.md), mit der Redakteure die Mitglieder der Website verwalten.
