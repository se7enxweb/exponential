# Georgian (kat-GE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard literary Georgian (ქართული სალიტერატურო ენა), in the neutral, technical register of current Georgian
software (the Georgian localisations of Windows, Microsoft Office, Google and Mozilla) and of the Georgian
Wikipedia. No other locale falls back to `kat-GE`.

- Established Georgian words are used where the software already has them: შენახვა, წაშლა, პარამეტრები,
  საქაღალდე, ჩამოტვირთვა, ატვირთვა, ბმული, ჩანართი. Internationalisms of computing take their usual Georgian form
  with the nominative ending -ი where the word ends in a consonant: სერვერი, სკრიპტი, მოდული, ქეში, ფაილი.
- No Russianisms where Georgian has its own word (not ნასტროიკები for "settings", not პაპკა for "folder").

## 2. Form of address

The interface addresses the user with the formal **თქვენ** (you), **თქვენი** (your), with the plural verb forms
that go with it.

- **Buttons, menu items and links**: the verbal noun (masdar), as Georgian software does: შენახვა, გაუქმება,
  წაშლა, რედაქტირება, გამოქვეყნება, გამოსაქვეყნებლად გაგზავნა, არჩეულის მოცილება, სანაგვეში გადატანა. Not the
  imperative (შეინახეთ) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: პარამეტრები, თარგმანები, მთავარი მდებარეობა.
- **Instructions**: the formal imperative: "აირჩიეთ ელემენტი და დააწკაპუნეთ „კარგი“.", "გთხოვთ, დაელოდოთ ...".
  A reference ("see ...") is "იხილეთ ...".
- **Confirmations and questions**: "ნამდვილად გსურთ ...?": "ნამდვილად გსურთ კლასის წაშლა?".
- **What the user can or may do**: "შეგიძლიათ ...", "არ შეგიძლიათ ...", or impersonal: "მთავარი ენის შეცვლა
  შეუძლებელია ...". "You do not have permission" is "თქვენ არ გაქვთ ... ნებართვა".
- Not allowed: the familiar შენ / შენი, slash forms.

## 3. Script and typography

- **Mkhedruli** (მხედრული), the Georgian script of everyday writing (Unicode U+10D0 to U+10FA). It has no capital
  letters: a sentence, a title and a button begin with the ordinary letter. Mtavruli (U+1C90 to U+1CBF, the
  all-caps forms) is not used, not even where the English source capitalises a heading.
- **Latin text inside Georgian**: product names, codes and acronyms stay in Latin letters. A Georgian case ending
  after an acronym is joined with a hyphen: PHP-ის ფაილი, URL-ის ფსევდონიმი, HTML-კოდი. Often a wording without an
  ending reads better: PHP ფაილი.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Georgian letter or a hyphenated ending
  glued to a placeholder: "%count ობიექტი", not "%count-ი" or "%countობიექტი". Put the placeholder where it needs
  no case ending: "ობიექტი: %name", "%count ელემენტი".
- **Numbers**: a noun after a number stays in the singular, as Georgian grammar wants it: "%count ობიექტი", not
  "ობიექტები". Digits 0-9.
- **Quotation marks**: the Georgian marks „...“ (U+201E, U+201C) where the source has its own quotation marks in
  text; where the source has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in Latin script, at the
  same place as in the source. A label keeps its colon.
- **Word order**: Georgian puts the genitive before its noun and the verb usually at the end; a long English
  compound becomes a genitive phrase: "content class group" is შიგთავსის კლასების ჯგუფი.

## 4. Glossary

Binding for the kernel and every extension.

| English | Georgian | Note |
|---|---|---|
| content | შიგთავსი | |
| content object | შიგთავსის ობიექტი | |
| object | ობიექტი | |
| node | კვანძი | main node: მთავარი კვანძი; parent node: მშობელი კვანძი; child: შვილობილი |
| location | მდებარეობა | main location: მთავარი მდებარეობა |
| item (list entry) | ელემენტი | |
| class | კლასი | the content class; class group: კლასების ჯგუფი; a PHP class: PHP კლასი |
| content class | შიგთავსის კლასი | |
| attribute | ატრიბუტი | |
| datatype | მონაცემთა ტიპი | |
| version | ვერსია | version history: ვერსიების ისტორია |
| draft | მონახაზი | store draft: მონახაზის შენახვა |
| publish | გამოქვეყნება | published: გამოქვეყნებული |
| send for publishing | გამოსაქვეყნებლად გაგზავნა | |
| discard | უარყოფა | discard draft: მონახაზის უარყოფა |
| archived | დაარქივებული | |
| pending | მოლოდინში | |
| rejected | უარყოფილი | |
| siteaccess | საიტზე წვდომა | the configuration unit; plural საიტზე წვდომები |
| site, website | საიტი, ვებსაიტი | |
| section | სექცია | the Exponential section |
| role | როლი | |
| policy | პოლიტიკა | |
| limitation | შეზღუდვა | |
| permission | ნებართვა | access rights: წვდომის უფლებები |
| module | მოდული | |
| function | ფუნქცია | |
| user | მომხმარებელი | |
| user group | მომხმარებელთა ჯგუფი | |
| group | ჯგუფი | |
| anonymous | ანონიმური | anonymous user: ანონიმური მომხმარებელი |
| administrator | ადმინისტრატორი | |
| administration (interface) | ადმინისტრირების ინტერფეისი | |
| username | მომხმარებლის სახელი | the column "Login" too |
| password | პაროლი | |
| log in / sign in | შესვლა | |
| log out / sign out | გასვლა | |
| e-mail | ელფოსტა | e-mail address: ელფოსტის მისამართი |
| cache | ქეში | clear cache: ქეშის გასუფთავება |
| template | შაბლონი | |
| override | გადაფარვა | override template: გადამფარავი შაბლონი |
| fallback | სარეზერვო | fallback language: სარეზერვო ენა |
| design | დიზაინი | |
| workflow | სამუშაო პროცესი | |
| trigger | ტრიგერი | |
| event | მოვლენა | |
| trash | სანაგვე | empty trash: სანაგვის დაცლა; move to trash: სანაგვეში გადატანა |
| bookmark | სანიშნე | |
| settings | პარამეტრები | |
| setup | კონფიგურაცია | the Setup tab; installation: ინსტალაცია |
| layout | განლაგება | Exponential Layouts |
| block | ბლოკი | |
| zone | ზონა | |
| extension | გაფართოება | |
| package | პაკეტი | |
| search | ძიება | the button: ძიება |
| log | ჟურნალი | log file: ჟურნალის ფაილი |
| queue | რიგი | |
| job / task | დავალება | |
| cron | cron | cron job: cron-ის დავალება |
| script | სკრიპტი | |
| worker | დამმუშავებელი პროცესი | |
| kernel, core | ბირთვი | |
| server | სერვერი | |
| database | მონაცემთა ბაზა | |
| folder | საქაღალდე | |
| file | ფაილი | |
| image | სურათი | |
| preview | წინასწარი ნახვა | |
| upload | ატვირთვა | |
| download | ჩამოტვირთვა | |
| export / import | ექსპორტი / იმპორტი | |
| remove | მოცილება | take out of a list or a place |
| delete | წაშლა | destroy |
| edit | რედაქტირება | |
| editor | რედაქტორი | |
| save / store | შენახვა | |
| Store (shop tab) | მაღაზია | the word the shop menu uses for "Shop" |
| cancel | გაუქმება | |
| OK | კარგი | |
| apply | გამოყენება | |
| back | უკან | |
| next / previous | შემდეგი / წინა | |
| enable / disable | ჩართვა / გამორთვა | |
| enabled / disabled | ჩართულია / გამორთულია | |
| active / inactive | აქტიური / არააქტიური | |
| run | გაშვება | |
| start / stop | დაწყება / შეჩერება | |
| session | სესია | |
| hidden / visible | დამალული / ხილული | hide / reveal: დამალვა / გამოჩენა |
| archive | არქივი | |
| order (sort) | რიგი | sort order: დალაგების რიგი |
| order (shop) | შეკვეთა | |
| shop | მაღაზია | |
| basket | კალათა | |
| product | პროდუქტი | |
| customer | კლიენტი | |
| discount | ფასდაკლება | |
| VAT | დღგ | VAT type: დღგ-ის ტიპი |
| price | ფასი | |
| currency | ვალუტა | |
| tag | ტეგი | keyword tag; an HTML tag: HTML ტეგი |
| translation | თარგმანი | |
| language | ენა | main language: მთავარი ენა |
| locale | ლოკალი | |
| state | მდგომარეობა | the object state; state group: მდგომარეობების ჯგუფი |
| status | სტატუსი | |
| view (button) | ნახვა | a module view: ხედი |
| format | ფორმატი | |
| sort | დალაგება | |
| filter | ფილტრი | |
| select | არჩევა | selected: არჩეული |
| browse | დათვალიერება | |
| copy | კოპირება | |
| move | გადატანა | |
| add | დამატება | |
| create | შექმნა | |
| new | ახალი | |
| restore | აღდგენა | |
| install / uninstall | ინსტალაცია / დეინსტალაცია | |
| sub items | ქვეელემენტები | |
| subtree | ქვეხე | |
| related objects | დაკავშირებული ობიექტები | relation: კავშირი |
| URL alias | URL-ის ფსევდონიმი | |
| redirect | გადამისამართება | |
| dashboard | საინფორმაციო დაფა | |
| content structure | შიგთავსის სტრუქტურა | |
| media library | მედიაბიბლიოთეკა | |
| identifier / ID | იდენტიფიკატორი / ID | |
| priority | პრიორიტეტი | |
| owner | მფლობელი | |
| modified / created | შეცვლილი / შექმნილი | |
| date / time | თარიღი / დრო | |
| error / warning | შეცდომა / გაფრთხილება | |
| help | დახმარება | |
| notification | შეტყობინება | |
| wizard | ოსტატი | |
| feed (RSS) | არხი | RSS feed: RSS არხი |
| tab | ჩანართი | |
| audit, audit trail | აუდიტი, აუდიტის ჟურნალი | |
| repository | რეპოზიტორი | |
| checksum | საკონტროლო ჯამი | |
| account | ანგარიში | |
| default | ნაგულისხმევი | default value: ნაგულისხმევი მნიშვნელობა |
| toolbar | ხელსაწყოთა ზოლი | |
| container | კონტეინერი | |
| AI | ხელოვნური ინტელექტი | |
| pattern (name pattern) | ნიმუში | |
| wildcard | ჩანაცვლების სიმბოლო | URL wildcard: URL-ის შაბლონი with ჩანაცვლების სიმბოლო |
| remote ID | დისტანციური ID | |
| placeholder | ჩანაცვლების ველი | |
| collection | კოლექცია | information collector: ინფორმაციის შემგროვებელი |
| approve, approval | დამტკიცება | |
| software | პროგრამული უზრუნველყოფა | |
| creator / modifier | შემქმნელი / შემცვლელი | |
| request (HTTP) | მოთხოვნა | |
| query | მოთხოვნა | a search query: საძიებო მოთხოვნა |
| handler / provider / listener | დამმუშავებელი / მომწოდებელი / მსმენელი | |
| engine | ძრავა | |
| release / upgrade | გამოშვება / განახლება | update: განახლება |
| backup | სარეზერვო ასლი | |
| maintenance | ტექნიკური მომსახურება | |
| directory | დირექტორია | |
| header (HTTP) | სათაური | |
| page layout (pagelayout template) | გვერდის შაბლონი | kept apart from განლაგება (Exponential Layouts) |
| passkey | წვდომის გასაღები | |
| two-step sign-in | ორსაფეხურიანი შესვლა | |
| device / browser | მოწყობილობა / ბრაუზერი | |
| link | ბმული | |
| registration, register | რეგისტრაცია, რეგისტრირება | |
| payment gateway | გადახდის კარიბჭე | |
| form | ფორმა | |
| comment | კომენტარი | |
| newsletter / subscription | საინფორმაციო ბიულეტენი / გამოწერა | |
| field | ველი | |
| value | მნიშვნელობა | |
| list | სია | |
| page | გვერდი | |
| title / name | სათაური / სახელი | the column "Name": სახელი |
| description | აღწერა | |
| type | ტიპი | |
| path | გზა | |
| width / height | სიგანე / სიმაღლე | |
| size | ზომა | |
| none | არცერთი | "no ...": არ არის ... |
| yes / no | დიახ / არა | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is მაღაზია, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is შენახვა.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
