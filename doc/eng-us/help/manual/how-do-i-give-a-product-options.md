---
title: How do I give a product options such as size or color?
audience: editor
category: manual
order: 170
summary: In the product's options field, add a multioption per choice (for example Size), add its options with any additional price, mark the default, and publish; a class with the newer multi-option field also offers groups, sub levels, images per option, disabled options and rules.
keywords: product options, variants, multioption, option set, additional price, default option, add option, add multioption, multioption2, rules, size, color, range option
type: help
---
# How do I give a product options such as size or color?

Options let a customer choose a variant when they put a product in the basket, for example a size or a color, each
with an optional surcharge. The product class decides which kind of options field it has; on a new installation the
**Product** class has the field **Additional options**.

## Who can do this

Editors who may edit the product.

## Steps

1. Edit the product and go to its options field (**Additional options** on the standard product).
2. Enter an **Option set name** if the field asks for one.
3. Click **Add multioption** for each choice the customer makes, and give it a **Name**, for example *Size*.
4. Click **Add option** under it for each value, for example *S*, *M* and *L*.
5. For each option, enter the **Additional price** (leave it at 0 for no surcharge) and, where shown, a **Priority**
   to order the options.
6. Choose the **Default** option with its radio button.
7. To take something out, tick it and click **Remove selected**.
8. Click **Send for publishing**, then open the product on the site and try the choice.

### The newer multi-option field

Some classes use the newer multi-option field instead. It adds:

- **Add group** and **Add multioption sub level**, for choices that depend on an earlier one (for example a model,
  then the colors of that model);
- an **Image** per option, shown to the customer;
- **Dis** to offer an option that cannot be chosen, for example to force a real choice instead of a default;
- **Rules**, a grid of which options may be combined; **Reset rules** allows everything again.

## What can go wrong

- **The options do not appear on the site.** The product page template does not show this field. Ask whoever looks
  after the design.
- **The basket shows an unexpected price.** The additional price of each chosen option is added to the product's
  price. Check both fields, and the VAT type of the price: see
  [How do I set up VAT for my products?](how-do-i-set-up-vat.md)
- **The class has no options field.** Ask an administrator to add one to the class (**Setup** > **Classes**).

## Related pages

- [Options and variants](../e-commerce/catalog/options-and-variants.md)
- [Adding a product](../customers/commerce/adding-a-product.md)
- [Products and catalog](../customers/commerce/products-and-catalog.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
