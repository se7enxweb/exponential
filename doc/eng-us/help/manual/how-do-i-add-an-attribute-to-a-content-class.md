---
title: How do I add an attribute to a content class?
audience: admin
category: manual
order: 635
summary: Open the class under Setup > Classes, click Edit, choose the attribute type and Add attribute, fill in name, identifier and the flags, and press OK; every object of the class gets the new, empty field.
keywords: content class, class attribute, add attribute, field, datatype, identifier, required, searchable, information collector, disable translation, class/edit, Apply, OK
context: class/edit
type: help
---
# How do I add an attribute to a content class?

A content class lists the fields (attributes) that every object of the class has. Adding an attribute adds an
empty field to all existing objects of the class when you store the class.

## Who can do this

Users whose role has the module `class` (all functions), usually administrators and site builders.

## Steps

1. Click **Setup** in the top menu and choose **Classes**.
2. Open the class group, for example "Content", and click the class.
   The class page lists its attributes with their types and flags.
3. Click **Edit**.
4. Choose the type of the new attribute, for example **Text line**, and click **Add attribute**.
   The new attribute appears at the end of the list.
5. Fill in **Name**, the label editors see, and **Identifier**, the name templates and settings use.
   The identifier uses lower case letters, digits and `_`, and cannot be changed safely once templates use it.
6. Set the flags you need:
   - **Required**: an object cannot be published without a value.
   - **Searchable**: the value goes into the search index.
   - **Information collector**: visitors fill in the field in a form instead of editors (for feedback forms).
   - **Disable translation**: the value is the same in every language of an object.
7. Fill in the settings of the type, for example the default value or maximum length of a text line.
8. Move the attribute to its place with the arrows or the position number.
9. Click **OK**.
   The class is stored and its objects get the new attribute.

**Apply** stores a draft of the class and keeps the form open; nothing changes for the objects until **OK**.
**Cancel** throws the draft away.

## What can go wrong

- **"The class definition could not be stored."** A name or identifier is missing, or an identifier is used twice.
  The messages at the top of the form name the attribute.
- **The class is locked.** Someone else is editing it. The lock ends when they store or cancel, or after
  `content.ini [ClassSettings] DraftTimeout` seconds (7200 by default).
- **A new required attribute and old objects.** Existing objects get the field empty. They stay published, but the
  next time an editor edits one, it cannot be published until the field is filled in.
- **Storing takes long.** A class with very many objects gets the new field in every object at once. Do it at a quiet
  time.
- **Search does not find the new values.** Changing **Searchable** affects what is indexed from then on. Objects that
  are not edited again need the search index rebuilt (`php bin/php/updatesearchindex.php`).
- **Attention:** Removing an attribute with **Remove selected attributes** and pressing **OK** deletes its content in
  every object of the class. It cannot be undone.

## Related pages

- [Class groups](../../guides/class-groups.md): the class pages in detail
- [Creating a content class](../cms-developers/creating-a-content-class.md)
- [Choosing datatypes](../cms-developers/choosing-datatypes.md)
- [Content classes and attributes](../cms-developers/content-classes-and-attributes.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
