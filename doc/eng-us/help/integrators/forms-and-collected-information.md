---
title: Forms and collected information
audience: integrator, developer, admin
category: integration
order: 210
summary: Getting form answers, polls and lead forms out of Exponential into a CRM or another system - how information collection works, collect.ini, the CSV export and bccie, reading new collections from PHP in a cronjob, mail on submission, and the personal data involved.
keywords: information collection, infocollector, collect.ini, forms, contact form, lead form, poll, feedback, CRM, CSV export, bccie, eZInformationCollection, fetchCollectionsList, dataMap, cronjob, personal data, GDPR
type: help
---
# Forms and collected information

This page is for integrators who connect the forms of an Exponential site to a CRM, a ticket system or a marketing
tool. A form in Exponential is a content object whose class has attributes marked **Information collector**; each
time a visitor sends it, one **collection** is stored with one value per collector attribute. The administration side
is [Collected information](../../guides/collected-information.md).

## Contents

1. [The ways out](#the-ways-out)
2. [collect.ini: what happens on sending](#collectini-what-happens-on-sending)
3. [Reading new collections in a cronjob](#reading-new-collections-in-a-cronjob)
4. [Exports](#exports)
5. [Personal data](#personal-data)
6. [Checklist](#checklist)

## The ways out

| Way | When | Effort |
|---|---|---|
| Mail on each sending (`collect.ini [EmailSettings]`) | The CRM has a mail-in address; low volume | Settings only |
| CSV export per object (`infocollector/export/<object id>`) | Occasional hand-over | None |
| `bccie` exports on a schedule (CSV or SYLK) | A regular file for another team | Settings and cronjob |
| A cronjob of your extension that reads new collections and calls the CRM's API | Leads must arrive within minutes, structured | An extension |

The last one is the robust integration: it is structured, retried and logged. Mail parsing on the CRM side breaks
with every template change.

## collect.ini: what happens on sending

`settings/collect.ini`, overridden in `settings/override/collect.ini.append.php`. Most settings have a default, a
list per collection type (`form`, `poll`, `feedback`) and an attribute that lets the object choose:

| Block | Settings | Meaning |
|---|---|---|
| `[InfoSettings]` | `Type`, `TypeList[]`, `TypeAttribute=collection_type` | Which type a form is |
| `[EmailSettings]` | `SendEmail=enabled`, `SendEmailList[poll]=disabled`, `SendEmailAttribute=collection_email` | Whether a mail goes out on sending |
| `[DisplaySettings]` | `Display=result`, `RedirectURL`, `RedirectURLAttribute=collection_redirect` | What the visitor sees afterwards |
| `[CollectionSettings]` | `CollectAnonymousData`, `CollectSensitiveData`, `CollectionUserData=multiple` | Whether anonymous visitors may send, whether sensitive data is stored, one or many collections per user |

## Reading new collections in a cronjob

Keep the id of the last collection you sent, read everything after it, send, and store the new last id. The tables
are `ezinfocollection` (one row per sending) and `ezinfocollection_attribute`; the class is `eZInformationCollection`
(`kernel/classes/ezinformationcollection.php`).

```php
<?php
/**
 * Sends new collections of one form object to the CRM. $lastID is kept by the caller (a row of your own table).
 */
function myIntegrationSendNewCollections( $formObjectID, $lastID, $crmClient )
{
    $db = eZDB::instance();
    $rows = $db->arrayQuery( 'SELECT id FROM ezinfocollection WHERE contentobject_id = ' . (int)$formObjectID
                           . ' AND id > ' . (int)$lastID . ' ORDER BY id', array( 'limit' => 200 ) );
    foreach ( $rows as $row )
    {
        $collection = eZInformationCollection::fetch( (int)$row['id'] );
        if ( !$collection instanceof eZInformationCollection )
            continue;
        $fields = array();
        foreach ( $collection->dataMap() as $identifier => $attribute )
        {
            $fields[$identifier] = $attribute->attribute( 'data_text' );
        }
        if ( !$crmClient->sendLead( (int)$row['id'], $fields ) )
        {
            return $lastID;               // stop; the next run retries from here
        }
        $lastID = (int)$row['id'];
    }
    return $lastID;
}
```

`dataMap()` indexes the collected attributes by their class attribute identifier. Text values are in `data_text`;
numbers in `data_int` or `data_float`, depending on the datatype. Send the collection id along, so the CRM can
refuse a duplicate when a run is repeated ([Idempotent imports](idempotent-imports-keyed-by-remote-id.md)).

For whole lists, `eZInformationCollection::fetchCollectionsList( $contentObjectID, $creatorID, $userIdentifier,
$limitArray, $sortArray )` and `fetchCollectionsCount()` are the kernel's fetches.

## Exports

- **The CSV export** at **Setup > Collected information** downloads `infocollector/export/<object id>`: UTF-8 with a
  byte order mark, the collection id, sent and changed times in UTC, the sender's user id, one column per collector
  attribute; at most 50 000 collections. Values that a spreadsheet would read as a formula start with an apostrophe.
  Each export is recorded in the audit as `data.export.csv`.
- **`bccie`** (required as `~1.1.18`) exports collected information as CSV or SYLK on demand or by its cronjob parts
  ([bccie](../../features/6.0/extensions/bccie.md)).

## Personal data

Form answers are usually personal data: names, e-mail addresses, messages.

- Send only the fields the receiving system needs.
- Delete collections in Exponential once they are safely in the CRM, if the customer's retention rules allow it
  (**Setup > Collected information**, Remove; recorded as `data.infocollection.remove`).
- Keep exported files where personal data belongs, and delete them when no longer needed.
- Write the data flow into the customer's record of processing; this is not legal advice
  ([Protecting customer data](../partners/protecting-customer-data.md)).

## Checklist

- [ ] Which form objects are integrated, and which fields go where.
- [ ] The mail on sending switched off where the cronjob replaces it, so leads do not arrive twice.
- [ ] The last sent id stored durably; the CRM refuses duplicates by collection id.
- [ ] Failed sends stop the run and are retried; the run is logged and monitored.
- [ ] Retention agreed for collections in Exponential and in the CRM.

## Related pages

- [Collected information](../../guides/collected-information.md)
- [bccie](../../features/6.0/extensions/bccie.md)
- [Cronjobs for integrations](cronjobs-for-integrations.md)
- [Forms and member feedback for associations](../businesses/associations/forms-and-member-feedback.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
