{* Feedback form - Full view *}

<!-- hero starts -->
<section class="pb-12 bg-gradient-to-b from-emerald-50 to-orange-50">
  <div class="lg:container mx-auto px-4">
    <div class="text-center max-w-xl mx-auto mb-12 lg:mb-20">
      <h1 class="text-3xl lg:text-4xl text-gray-900 font-bold mb-4">
        {$node.name|wash()}
      </h1>
      <p class="text-sm text-gray-700">
        {attribute_view_gui attribute=$node.data_map.description}
      </p>
      <p class="text-sm text-gray-700">
        {include name=Validation uri='design:content/collectedinfo_validation.tpl'
          class='message-warning'
          validation=$validation collection_attributes=$collection_attributes}
      </p>
    </div>
    <div class="grid lg:grid-cols-2 gap-12 items-center">
      <div class="space-y-8">
        <form action={"content/action"|ezurl} method="post" class="grid sm:grid-cols-2 gap-4 max-w-xl mx-auto">
          <div class="sm:col-span-1">
            {attribute_view_gui attribute=$node.data_map.sender_name css_class="appearance-none w-full border border-solid border-gray-300 py-2.5 px-4 text-gray-900 bg-white placeholder:text-gray-400 outline-none transition-all duration-300 focus-visible:border-orange-500 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-orange-300" placeholder="Full Name*"}
          </div>
          <div class="sm:col-span-1">
            {attribute_view_gui attribute=$node.data_map.email css_class="appearance-none w-full border border-solid border-gray-300 py-2.5 px-4 text-gray-900 bg-white placeholder:text-gray-400 outline-none transition-all duration-300 focus-visible:border-orange-500 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-orange-300" placeholder="Email Address*"}
          </div>
          <div class="col-span-2">
            {attribute_view_gui attribute=$node.data_map.subject css_class="appearance-none w-full border border-solid border-gray-300 py-2.5 px-4 text-gray-900 bg-white placeholder:text-gray-400 outline-none transition-all duration-300 focus-visible:border-orange-500 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-orange-300" placeholder="Subject*"}
          </div>
          <div class="col-span-2">
            {attribute_view_gui attribute=$node.data_map.message css_class="appearance-none w-full border border-solid border-gray-300 py-2.5 px-4 text-gray-900 bg-white placeholder:text-gray-400 outline-none transition-all duration-300 focus-visible:border-orange-500 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-orange-300 resize-none" placeholder="Message*"}
          </div>
          <div class="col-span-2">
            <input type="hidden" class="defaultbutton" name="ActionCollectInformation" value="{"Send Message"|i18n("design/ezwebin/full/feedback_form")}" onclick="onClick">

              <input type="hidden" class="hidden" name="ActionCollectInformation" value="{"Send Message"|i18n("design/ezwebin/full/feedback_form")}" />
              <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
              <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
              <input type="hidden" name="ViewMode" value="full" />
          </div>
          <div class="col-span-2">
            <button type="submit" class="g-recaptcha btn btn-primary group w-full flex items-center justify-center gap-4 border border-solid border-gray-300 bg-white text-base text-gray-900 font-medium py-2.5 px-4 lg:px-6 transition duration-300 hover:bg-gray-900 hover:text-white" style="width: 100%;" data-sitekey="{ezini( 'Keys', 'PublicKey', 'recaptcha.ini' )}" data-action="submit">
              Send Message
              <i class="fa-solid fa-arrow-right-long duration-100 group-hover:-rotate-45"></i>
            </button>
          </div>
          <script src="https://www.google.com/recaptcha/api.js"></script>
        </form>
        <p class="text-sm text-center">
          Hate forms? Send us an
          <a href="mailto:info@se7enx.com" class="text-orange-500 transition hover:text-orange-600">
            email
          </a>
          instead.
        </p>
      </div>
      <div class="-order-1 lg:order-1">
        <img src={"content/contact-us/illustration.svg"|ezimage()} alt="Contact Us"class="w-96 max-w-full h-auto object-contain mx-auto"/>
      </div>
    </div>
  </div>
</section>
<!-- hero ends -->
<script>
        {literal}
                document.addEventListener("DOMContentLoaded", (event) => {
                        const submitButtons = Array.from(document.querySelectorAll('[data-action="submit"]'));

                        submitButtons.forEach((btn) => {
                                btn.addEventListener('click', () => {
                                        const form = btn.closest('form');

                                        form.submit();
                                });
                        });
                });
        {/literal}
</script>