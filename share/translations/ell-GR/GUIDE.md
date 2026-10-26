# Greek (ell-GR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Modern Greek (Κοινή Νεοελληνική) in the monotonic system, as used in Greece and Cyprus. The tone is
neutral, concise and professional, as in the Greek versions of current operating systems and office software
(Microsoft terminology and style guide for Greek). No colloquial forms, no katharevousa endings ("εστάλει",
"εισαχθέντα", "μεταφορτωθέν" are wrong: "στάλθηκε", "εισαγόμενα", "που μεταφορτώθηκε"), and no needless English
words where an established Greek term exists.

The older Greek catalogue already used nouns on buttons and the formal plural in sentences; those conventions
stay. Its spelling slips ("Απορρίματα", "κλίκ", "όνοματος") and some of its terms (Section "Τμήμα", Module
"Δομοστοιχείο", Cache "κρυφή μνήμη", Dashboard "Πίνακας ελέγχου", Override "Παράκαμψη") are replaced by the
glossary below. An old translation is reused only when it agrees with this guide.

## 2. Form of address

The user is addressed formally, in the second person plural (**εσείς**), everywhere.

- **Buttons, menu items, links and action labels** are verbal nouns (the form of the Greek Microsoft interface):
  "Αποθήκευση", "Άκυρο", "Επεξεργασία", "Διαγραφή", "Αφαίρεση επιλεγμένων", "Προσθήκη", "Δημιουργία νέου",
  "Εφαρμογή αλλαγών", "Αποστολή για δημοσίευση", "Αποθήκευση προσχεδίου", "Απόρριψη προσχεδίου",
  "Άδειασμα κάδου απορριμμάτων", "Επιλογή όλων". "OK", "Ναι", "Όχι", "Πίσω", "Επόμενο", "Κλείσιμο" as they are.
- **Titles, column headings and tabs** are nouns or noun phrases: "Δομή περιεχομένου", "Λογαριασμοί χρηστών",
  "Ρυθμίσεις ειδοποιήσεων", "Καταστάσεις αντικειμένων", "Κύρια γλώσσα".
- **Requests and instructions** in hints and messages use the plain plural imperative; "Please" is not
  translated with "Παρακαλώ":
  - "Επιλέξτε μια τοποθεσία." (Please choose a location)
  - "Εισαγάγετε μια έγκυρη διεύθυνση e-mail."
  - "Ελέγξτε τη σύνδεση και δοκιμάστε ξανά."
- **Confirmations**: "Θέλετε σίγουρα να …;": "Θέλετε σίγουρα να αφαιρέσετε αυτή την κλάση;", "Θέλετε σίγουρα να
  αφαιρέσετε τα επιλεγμένα ψευδώνυμα;". This avoids a gendered "βέβαιος/βέβαιοι".
- **Statements about the user**: "Δεν έχετε δικαίωμα να αφαιρέσετε κλάσεις.", "Μπορείτε να αλλάξετε τις ρυθμίσεις
  ιστορικού εκδόσεων στο αρχείο content.ini.", "Η διεύθυνση e-mail του λογαριασμού σας αλλάζει".
- **Tooltips that describe a click**: "Κάντε κλικ για …": "Κάντε κλικ στο εικονίδιο για να εμφανιστεί το μενού
  περιβάλλοντος."
- **Error messages** say what happened, then what to do: "Δεν ήταν δυνατή η αποθήκευση του αρχείου που
  μεταφορτώθηκε.", "Δοκιμάστε ξανά ή επικοινωνήστε με τον διαχειριστή του ιστότοπου." "Αποτυχία …" is allowed as
  a short heading.
- **Status messages** are passive or impersonal: "Το μπλοκ διαγράφηκε.", "Οι αλλαγές αποθηκεύτηκαν.",
  "Δεν βρέθηκε καμία εκτέλεση εργασιών cron".
- Not allowed: the singular "εσύ" forms (σου, θέλεις, μπορείς, επίλεξε, κάνε κλικ); "Σας" with a capital letter
  inside a sentence; the imperative on buttons ("Αποθηκεύστε" as a button is wrong); the subjunctive as a button
  ("Να αποθηκευτεί"); katharevousa forms.

## 3. Script and typography

- **Greek script, monotonic**, with the tonos on every word of more than one syllable that carries it, and the
  dialytika where needed ("προϊόν", "προϋπολογισμός"). Words written in all capitals drop the tonos ("ΦΠΑ",
  "ΠΡΟΣΟΧΗ"); the source rarely asks for all capitals.
- **Sentence case**: only the first word of a button, title or heading has a capital letter (and proper names):
  "Δομή περιεχομένου", "Καταστάσεις αντικειμένων". No English title case.
- **Pronoun σας** is always written in lower case. The enclitic possessive after a noun with an accent on the
  antepenult takes a second accent: "το περιεχόμενό σας", "τον λογαριασμό σας" (no second accent here).
- **Question mark**: the Greek question mark is written with the semicolon character ";" (U+003B; U+037E normalizes
  to it). An English "?" in a translation is wrong. The ano teleia is "·" (U+00B7), used rarely.
- **Quotation marks** in running text: «…» (U+00AB, U+00BB); nested quotes “…”. Straight quotes stay only where
  the source quotes code, an attribute value, JavaScript or a value that is typed exactly. A button name quoted in a
  sentence: «Αποθήκευση».
- **Final article -ν**: keep the final ν of τον/την/έναν before vowels and before κ, π, τ, γκ, μπ, ντ, τσ, τζ, ξ, ψ
  ("τον διαχειριστή", "την κλάση", "τη μετάφραση").
- **Plural and number forms**: Greek has two forms (1, other). The source writes "item(s)"; never copy that as
  "κόμβο(ους)" or "αντικείμενο(α)". Use a label form, which works for every number: "Αντικείμενα: %count",
  "Διεργασίες: %count", "Προσχέδια που αφαιρέθηκαν: %count.". Where the source has separate singular and plural
  messages, translate each with its own form.
- **Placeholders and case endings**: a placeholder is never declined and nothing is glued to it. Greek letters
  directly after a placeholder would not break the checker, but they are not used: a placeholder always stands
  free, after a noun that carries the case through its article: "Επεξεργασία της ενότητας %name",
  "Η κλάση <%class_name>", "από τον χρήστη %user". When the gender of what a placeholder names is unknown
  (%classname, %name), put a neutral head noun in front: "Επιλογή τοποθεσίας για νέο στοιχείο τύπου
  <%classname>", not "τη νέα %classname".
- **Numbers**: digits 0–9; decimal comma and a full stop for thousands in prose (1,5; 10.000). Units after a
  number with a space: "MB", "KB", "δευτ.", "λεπτά", "ώρες"; "πριν από %n λεπτά". Dates and times come from the
  locale, not from the catalogue.
- **Dashes**: the en dash with spaces – for a break in a sentence; the em dash as in the source.
- **English names and codes** stay in Latin script and take a Greek article for the case: "το Exponential",
  "του Solr", "το αρχείο content.ini", "η ετικέτα <embed>".

## 4. Glossary

Binding for the kernel and every extension. "MS" in a note means the term is the one of the Greek Microsoft
terminology.

| English | Greek | Note |
|---|---|---|
| content | περιεχόμενο | |
| content object | αντικείμενο περιεχομένου | |
| object | αντικείμενο | |
| item (list entry, general) | στοιχείο | items: στοιχεία |
| node | κόμβος | main node: κύριος κόμβος |
| location (node placement) | τοποθεσία | main location: κύρια τοποθεσία; placement: τοποθέτηση |
| content class | κλάση περιεχομένου | |
| class | κλάση | a PHP class: κλάση PHP |
| class group | ομάδα κλάσεων | |
| attribute | χαρακτηριστικό | MS; not "ιδιότητα" (property) |
| datatype | τύπος δεδομένων | |
| version | έκδοση | version history: ιστορικό εκδόσεων |
| draft | προσχέδιο | Store draft: Αποθήκευση προσχεδίου; Discard draft: Απόρριψη προσχεδίου |
| publish | δημοσίευση / δημοσιεύω | Send for publishing: Αποστολή για δημοσίευση |
| published | δημοσιευμένο | status |
| publishing | δημοσίευση | |
| translation | μετάφραση | |
| language | γλώσσα | |
| main language | κύρια γλώσσα | |
| content language | γλώσσα περιεχομένου | |
| interface language | γλώσσα περιβάλλοντος εργασίας | |
| siteaccess | πρόσβαση ιστότοπου (siteaccess) | pl. προσβάσεις ιστότοπου; the code word "siteaccess" may follow in parentheses in headings and settings; a narrow column abbreviated "SA": Siteaccess |
| site / website | ιστότοπος | |
| design (Exponential design) | σχεδίαση | the design of a siteaccess |
| structure | δομή | |
| Content structure | Δομή περιεχομένου | tab |
| Media library | Βιβλιοθήκη πολυμέσων | tab |
| Dashboard | Πίνακας εργαλείων | MS; not "Πίνακας ελέγχου" (Control Panel) |
| Setup (admin tab) | Ρυθμίσεις συστήματος | the tab; plain "settings": ρυθμίσεις; setup wizard (installation): οδηγός εγκατάστασης |
| setting / settings | ρύθμιση / ρυθμίσεις | |
| configuration / configure | διαμόρφωση / διαμόρφωση | button "Configure": Διαμόρφωση |
| template | πρότυπο | template editor: επεξεργασία προτύπων |
| page layout (pagelayout.tpl) | πρότυπο σελίδας | not "διάταξη σελίδας"; διάταξη is reserved for Exponential Layouts |
| override (template) | πρότυπο αντικατάστασης | an override replaces another template or setting |
| override (verb) / overridden | αντικατάσταση / αντικαθίσταται | |
| fallback | εναλλακτικό | fallback language: εναλλακτική γλώσσα; fallback chain: αλυσίδα εναλλακτικών |
| alias | ψευδώνυμο | |
| URL alias | ψευδώνυμο URL | |
| wildcard (URL) | χαρακτήρας μπαλαντέρ | URL wildcard: μπαλαντέρ URL |
| redirect / forward (URL) | ανακατεύθυνση / προώθηση | `redirect_uri` stays code |
| cache | προσωρινή μνήμη | MS |
| clear cache | εκκαθάριση προσωρινής μνήμης | button: Εκκαθάριση προσωρινής μνήμης |
| workflow | ροή εργασιών | MS |
| workflow event / event | συμβάν ροής εργασιών / συμβάν | MS |
| trigger | έναυσμα | MS; pl. εναύσματα |
| role | ρόλος | |
| policy | πολιτική | pl. πολιτικές |
| limitation (policy) | περιορισμός | |
| permission | δικαίωμα | pl. δικαιώματα; never "άδεια", which is a license |
| access / access denied | πρόσβαση / άρνηση πρόσβασης | |
| user | χρήστης | |
| user group | ομάδα χρηστών | |
| user account(s) | λογαριασμός χρήστη / λογαριασμοί χρηστών | |
| anonymous user | ανώνυμος χρήστης | |
| administrator | διαχειριστής | |
| admin interface | περιβάλλον διαχείρισης | |
| user interface | περιβάλλον εργασίας χρήστη | MS |
| log in / log out | σύνδεση / αποσύνδεση | as verbs: συνδεθείτε / αποσυνδεθείτε; users online: σε σύνδεση |
| login (field) / username | όνομα χρήστη | |
| password | κωδικός πρόσβασης | |
| e-mail | e-mail | e-mail address: διεύθυνση e-mail |
| section | ενότητα | always the Exponential section; not "τμήμα" ("τμήμα πλοήγησης" is the navigation part) |
| INI section / INI block | ενότητα ρυθμίσεων | "the [Name] section": η ενότητα [Name]; narrow column: Ενότητα; never "μπλοκ" |
| object state | κατάσταση αντικειμένου | |
| state | κατάσταση | the object state |
| state group | ομάδα καταστάσεων | |
| state (province, shop address) | νομός / πολιτεία | only in addresses |
| relation | συσχέτιση | |
| related objects | συσχετισμένα αντικείμενα | |
| reverse related | αντικείμενα που παραπέμπουν σε αυτό | |
| trash | κάδος απορριμμάτων | Empty trash: Άδειασμα κάδου απορριμμάτων |
| bookmark | σελιδοδείκτης | |
| sub-items / children / child (node) | δευτερεύοντα στοιχεία / δευτερεύον στοιχείο | the content tree; never "θυγατρικά" or "παιδιά" for nodes |
| child (Solr document, XML element, process) | θυγατρικό έγγραφο / θυγατρικό στοιχείο / θυγατρική διεργασία | child documents: θυγατρικά έγγραφα; "παιδί" only for a real child (a pupil) |
| parent | γονικό | parent node: γονικός κόμβος |
| tree / subtree | δένδρο / υποδένδρο | not "δέντρο" |
| owner | κάτοχος | MS; not "ιδιοκτήτης" |
| creator | δημιουργός | |
| modifier | χρήστης που τροποποίησε | column heading: Τροποποιήθηκε από |
| created / modified | Δημιουργήθηκε / Τροποποιήθηκε | column headings |
| layout (Exponential Layouts) | διάταξη | layout type: τύπος διάταξης; the name Exponential Layouts stays |
| layout (directory or release structure) | δομή | |
| block / zone | μπλοκ / ζώνη | only Exponential Layouts blocks and template cache-blocks; to block an address (mail): αποκλεισμός |
| extension | επέκταση | |
| package | πακέτο | |
| module | λειτουργική μονάδα | MS; not "δομοστοιχείο" |
| view (module view, noun) | προβολή | "Module view": προβολή λειτουργικής μονάδας |
| View (button) | Προβολή | |
| function (module function) | λειτουργία | |
| search (noun / button) | αναζήτηση | search engine: μηχανή αναζήτησης; advanced search: σύνθετη αναζήτηση |
| search index | ευρετήριο αναζήτησης | |
| reindex / index (verb) | επαναδημιουργία ευρετηρίου / δημιουργία ευρετηρίου | indexing: ευρετηρίαση |
| index (database) | ευρετήριο | |
| index file | αρχείο ευρετηρίου | |
| cronjob | εργασία cron | |
| schedule | χρονοδιάγραμμα | scheduled: προγραμματισμένο; on a schedule: βάσει χρονοδιαγράμματος |
| job | εργασία | |
| queue | ουρά | job queue: ουρά εργασιών |
| worker | διεργασία εργασίας | worker process |
| lane (queue) | λωρίδα | |
| log / log file | αρχείο καταγραφής | never "log in" |
| audit / audit trail | έλεγχος / ίχνος ελέγχου | audit log: αρχείο καταγραφής ελέγχου; audit sink: προορισμός ελέγχου; actor: φορέας ενέργειας |
| notification | ειδοποίηση | |
| alert | συναγερμός | kept apart from notification |
| collaboration | συνεργασία | |
| preview | προεπισκόπηση | |
| ID / remote ID | αναγνωριστικό / απομακρυσμένο αναγνωριστικό | "ID" alone may stay in narrow columns |
| identifier | αναγνωριστικό | |
| file | αρχείο | |
| folder / directory | φάκελος / κατάλογος | directory on the server: κατάλογος |
| image | εικόνα | |
| media | πολυμέσα | |
| thumbnail | μικρογραφία | |
| upload / download | μεταφόρτωση / λήψη | |
| export / import | εξαγωγή / εισαγωγή | |
| shop / store (shop tab) | κατάστημα | the header tab "Store" in the shop sense: Κατάστημα |
| basket | καλάθι αγορών | |
| order (shop) | παραγγελία | order status: κατάσταση παραγγελίας |
| order (sorting) | σειρά | sort order: σειρά ταξινόμησης |
| customer / product | πελάτης / προϊόν | |
| checkout | ολοκλήρωση αγοράς | |
| VAT / VAT type | ΦΠΑ / τύπος ΦΠΑ | |
| discount / discount rule | έκπτωση / κανόνας έκπτωσης | |
| rule | κανόνας | |
| wish list | λίστα επιθυμιών | |
| price / currency / exchange rate | τιμή / νόμισμα / ισοτιμία | |
| payment gateway / gateway | πύλη πληρωμών / πύλη | |
| newsletter | ενημερωτικό δελτίο | |
| subscriber / subscription / subscribe | συνδρομητής / συνδρομή / εγγραφή | |
| tag (keyword, eztags) | ετικέτα | pl. ετικέτες |
| keyword | λέξη-κλειδί | pl. λέξεις-κλειδιά |
| tag (HTML/XML) | ετικέτα HTML / ετικέτα XML | always with HTML/XML; custom tag: προσαρμοσμένη ετικέτα |
| move / copy / swap | μετακίνηση / αντιγραφή / εναλλαγή | |
| embed / embedded (object in text) | ενσωμάτωση / ενσωματωμένο | the XML tag stays `<embed>` |
| embedding (vector) | διανυσματική αναπαράσταση | to embed (vector): μετατροπή σε διανυσματική αναπαράσταση; never "ενσωμάτωση" in the vector sense |
| chunk / vector | τμήμα κειμένου / διάνυσμα | |
| prompt / model / token | προτροπή / μοντέλο / διακριτικό | token (AI and authentication): διακριτικό (MS) |
| AI | τεχνητή νοημοσύνη (ΤΝ) | |
| deploy / deployment / release | ανάπτυξη / ανάπτυξη / έκδοση | MS |
| enable / disable | ενεργοποίηση / απενεργοποίηση | |
| enabled / disabled | ενεργοποιημένο / απενεργοποιημένο | |
| active / inactive | ενεργό / ανενεργό | |
| warning / error / notice | προειδοποίηση / σφάλμα / σημείωση | MS; not "λάθος" for an error message |
| required / optional | υποχρεωτικό / προαιρετικό | |
| default | προεπιλογή | adjective: προεπιλεγμένο |
| priority | προτεραιότητα | |
| sort / ascending / descending | ταξινόμηση / αύξουσα / φθίνουσα | abbreviated: Αύξ. / Φθίν. |
| visible / hidden | ορατό / κρυφό | |
| hide / reveal / show | απόκρυψη / εμφάνιση / εμφάνιση | "Reveal" is not "αποκάλυψη" |
| expand / collapse | ανάπτυξη / σύμπτυξη | MS; also unfold / fold |
| OK / Cancel / Yes / No | OK / Άκυρο / Ναι / Όχι | |
| Back / Next / Previous / Close | Πίσω / Επόμενο / Προηγούμενο / Κλείσιμο | |
| select / browse | επιλογή / περιήγηση | Browse opens the tree picker |
| select all / deselect all / select none | Επιλογή όλων / Κατάργηση επιλογής όλων / Καμία επιλογή | |
| apply | εφαρμογή | Apply changes: Εφαρμογή αλλαγών |
| save / store | αποθήκευση | every "Store" that is not the shop tab |
| discard | απόρριψη | |
| edit | επεξεργασία | |
| remove | αφαίρεση | take out of a list, group or place; nothing is destroyed |
| delete | διαγραφή | destroy |
| new / create / add | νέο / δημιουργία / προσθήκη | |
| refresh / update / reset | ανανέωση / ενημέρωση / επαναφορά | |
| restore | επαναφορά | from the trash |
| assign / assignment | ανάθεση / ανάθεση | |
| approve / approval / approved | έγκριση (εγκρίνω) / έγκριση / εγκρίθηκε | |
| approver | υπεύθυνος έγκρισης | pl. υπεύθυνοι έγκρισης; Approver groups: Ομάδες υπευθύνων έγκρισης |
| deny / reject / rejected | άρνηση / απόρριψη / απορρίφθηκε | |
| accept / accepted | αποδοχή / έγινε αποδοχή | |
| pending | σε εκκρεμότητα / εκκρεμεί | MS; awaits: αναμένει |
| archive (verb) / archived | αρχειοθέτηση / αρχειοθετημένο | an archived version, object or order |
| unarchive | κατάργηση αρχειοθέτησης | |
| archive (place, menu item, log archive) | αρχείο | the stored collection |
| archive (file .tar/.zip/.phar/.ezpkg) | αρχείο συμπίεσης | a .phar: αρχείο phar |
| run (noun) | εκτέλεση | cron run, index run |
| run (verb) | εκτέλεση | button |
| dry run | δοκιμαστική εκτέλεση | |
| self-test | αυτοέλεγχος | |
| rotation / rotate (log) | εναλλαγή / εναλλαγή αρχείων καταγραφής | |
| debug | εντοπισμός σφαλμάτων | MS |
| statistics / system information | στατιστικά / πληροφορίες συστήματος | |
| database | βάση δεδομένων | |
| charset / character set | σύνολο χαρακτήρων | not "κωδικοποίηση" |
| path | διαδρομή | |
| handler | χειριστής | image handler: χειριστής εικόνων |
| wizard | οδηγός | MS |
| backend / provider / endpoint | σύστημα υποστήριξης / πάροχος / τελικό σημείο | |
| method / listener / controller | μέθοδος / ακροατής / ελεγκτής | |
| plugin | πρόσθετο | MS |
| kernel | πυρήνας | |
| script | δέσμη ενεργειών | MS; a PHP script: δέσμη ενεργειών PHP |
| allowlist | λίστα επιτρεπόμενων | |
| read-only | μόνο για ανάγνωση | |
| argument | όρισμα | |
| slot / collection / facets / elevate | υποδοχή / συλλογή / όψεις / προώθηση | Solr |
| core (Solr) | πυρήνας Solr | |
| filter | φίλτρο | |
| tick (checkbox) | επιλογή | "Tick the box": Επιλέξτε το πλαίσιο |
| container | κοντέινερ | the class flag "Is container" and the CSS/Bootstrap container alike |
| locale | τοπικές ρυθμίσεις | the INI key `Locale` and `%locale` stay as code: "σε %locale" |
| localization | τοπική προσαρμογή | MS |
| license | άδεια χρήσης | |
| privilege (DB) / grant | προνόμιο / παραχώρηση | a role grant: ανάθεση ρόλου; "Grant access…": Παραχώρηση πρόσβασης… |
| revoke | ανάκληση | |
| format (file or data) | μορφή | file format: μορφή αρχείου |
| formatting (text) | μορφοποίηση | |
| linked / common | συνδεδεμένο / κοινό | |
| flags (class flags) | σημαίες | |
| teaser | σύνοψη | |
| slug | αναγνωριστικό URL | |
| placeholder | σύμβολο κράτησης θέσης | MS |
| preset | προκαθορισμένη ρύθμιση | |
| information collection / collector | συλλογή πληροφοριών / συλλέκτης πληροφοριών | collected information: συλλεγμένες πληροφορίες |
| overview / summary | επισκόπηση / σύνοψη | |
| tab | καρτέλα | the key stays "Tab" ("Το Tab αυξάνει την εσοχή."); the tab character: στηλοθέτης |
| rich text | εμπλουτισμένο κείμενο | |
| feed | τροφοδοσία | RSS feed: τροφοδοσία RSS |
| session | περίοδος λειτουργίας | MS; not "συνεδρία" |
| scope | εύρος | |
| thread | νήμα | conversation thread and process thread |
| batch | δέσμη | |
| schema / registry / manifest | σχήμα / μητρώο / δήλωση | |
| stylesheet / theme / icon | φύλλο στυλ / θέμα / εικονίδιο | |
| port | θύρα | |
| digest | σύνοψη | e-mail digest: συγκεντρωτικό μήνυμα |
| profile | προφίλ | |
| editor (person) | συντάκτης | |
| editor (software) | πρόγραμμα επεξεργασίας | |
| Editor (siteaccess type) | περιβάλλον σύνταξης | |
| page / pages | σελίδα / σελίδες | |
| link | σύνδεση | MS; hyperlink: υπερ-σύνδεση; symbolic link: συμβολική σύνδεση; content "links to": παραπέμπει σε |
| title / name / description | τίτλος / όνομα / περιγραφή | |
| date / time | ημερομηνία / ώρα | |
| status | κατάσταση | |
| type | τύπος | |
| group | ομάδα | |
| list | λίστα | |
| value / field | τιμή / πεδίο | |
| number / count | αριθμός / πλήθος | |
| width / height / size | πλάτος / ύψος / μέγεθος | |
| server / client / request / response | διακομιστής / πρόγραμμα-πελάτης / αίτηση / απόκριση | |
| task | εργασία | |
| pattern | μοτίβο | |
| repository | αποθετήριο | |
| maintainer | υπεύθυνος συντήρησης | |
| install / uninstall / installation | εγκατάσταση / κατάργηση εγκατάστασης / εγκατάσταση | |
| checksum | άθροισμα ελέγχου | |
| quarantine | καραντίνα | |
| pool | ομάδα | pool of processes: ομάδα διεργασιών |
| mapping | αντιστοίχιση | |
| resolve / resolver | επίλυση / επιλυτής | path resolution |
| checkpoint | σημείο ελέγχου | |
| label | ετικέτα | of a field, button or block (MS); the context keeps it apart from the keyword tag |
| details | λεπτομέρειες | |

## 5. Decisions after the translation

These take precedence where a text is in doubt.

| English | Greek | Distinction |
|---|---|---|
| Denied (approval status) | Δεν εγκρίθηκε | keeps it apart from Rejected «Απορρίφθηκε»; the button Deny: Άρνηση |
| entry | καταχώριση | spelled with -ι-, as the verb καταχωρίζω (registered: καταχωρισμένο); a database row or record: εγγραφή |
| commit (Git) | υποβολή | merge commit: υποβολή συγχώνευσης; a database or Solr commit and `commit()` stay "το commit" |
| online / offline (site, maintenance) | σε λειτουργία / εκτός λειτουργίας | "sell online": στο διαδίκτυο; online documentation: ηλεκτρονική τεκμηρίωση |
| Online Editor | ο Online Editor | a name, always with the masculine article |
| hit / miss / hit rate | επιτυχία / αστοχία / ποσοστό επιτυχιών | cache, OPcache, buffer pool and Solr alike; not "ευστοχία"; search hits: αποτελέσματα |
| shell | κέλυφος | not "φλοιός (εντολών)" |
| deprecation(s) | κατάργηση / καταργήσεις (deprecations) | abbreviation: Καταργ.; not "Παρωχ." |
| staging / production | προπαραγωγή / παραγωγή | staging site: ιστότοπος προπαραγωγής; test site: ιστότοπος δοκιμών; badges DEV/TEST/STAGE/PROD stay Latin |
| host / hostname | κεντρικός υπολογιστής / όνομα κεντρικού υπολογιστή | MS; to host, hosting (an offer): φιλοξενώ, φιλοξενία |
| query / string | ερώτημα / συμβολοσειρά | |
| storage | χώρος αποθήκευσης | not "αποθηκευτικός χώρος" |
| timestamp | χρονοσήμανση | not "χρονοσφραγίδα" |
| backup / secret | αντίγραφο ασφαλείας / μυστικό | |
| redact / masked / unmasked | απόκρυψη / με απόκρυψη / χωρίς απόκρυψη | "μάσκα" only for a bit mask (language mask) |
| array (INI/PHP) / matrix (datatype) | πίνακας / πίνακας | the datatype name: Πίνακας |
| breadcrumb | διαδρομή πλοήγησης | |
| syndication / syndicate (datatype) | διανομή περιεχομένου | |
| conflict / conflicting | διένεξη / αντικρουόμενος | |
| validate / validation | επικύρωση | |
| tutor (Margerite) | εκπαιδευτικός βοηθός | |
| Desktop / Large Desktop / Landscape | Επιτραπέζιος υπολογιστής / Μεγάλη οθόνη υπολογιστή / Οριζόντιος προσανατολισμός | device presets |

Accepted homonyms:

- **Discard and reject** both translate as "απόρριψη". Discard always comes with its object ("Απόρριψη
  προσχεδίου"); Reject stands in the approval context next to «Έγκριση».
- **Hide and redact** both give "απόκρυψη". A redacted value is described as "με απόκρυψη", a hidden node as
  "κρυφό".
- **Version and release** both give "έκδοση". Where a text speaks of both, write "έκδοση (release)" for the release.
- **Audit and check** both give "έλεγχος". In a sentence where both occur, write "έλεγχος (audit)" once.
