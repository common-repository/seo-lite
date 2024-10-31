(function ($) {
   $("#seo-lite-form").submit((event) => {
      let tag_cache = [];
      // Iterate all rows in body and cache inputs
      $("#seo-lite-custom-tags-table > tbody > tr").each(function (index) {
         $(this)
            .find(":input")
            .each(function () {
               if (typeof tag_cache[index] === "undefined") {
                  tag_cache[index] = [];
               }
               tag_cache[index][$(this).attr("name")] = $(this).val();
            });
      });

      // We saved the data in tag_cache by index because we can't be sure the inputs will be parsed in the right order
      // ie, we might end up with "content:property" instead of"property:content"
      // Now, iterate tag_cache and generate final array
      let tag_json = {};
      tag_cache.forEach((tag) => {
         if (typeof tag["property"] !== "undefined" && typeof tag["content"] !== "undefined") {
            tag_json[tag["property"]] = tag["content"];
         }
      });
      $("#seo_lite_custom_tags").val(JSON.stringify(tag_json));
   });

   $(".seo-lite-delete-row").click(function () {
      var row = $(this).closest("tr");
      if (row.siblings().length == 0) {
         // If this is the only row, just clear input
         clear_inputs(row);
      } else {
         row.remove();
      }
   });

   $(".seo-lite-add-row").click(function () {
      var last_row = $(this).closest("table").find("tbody > tr:last"),
         new_row = last_row.clone(true); // clone(true) also clones bound events like $(".seo-lite-delete-row").click()
      clear_inputs(new_row);
      last_row.after(new_row);
   });

   // Clears all input tags in the passed element
   function clear_inputs(element) {
      element.find(":input").each(function () {
         if (this.type == "checkbox" || this.type == "radio") {
            this.checked = false;
         } else {
            $(this).val("");
         }
      });
   }
})(jQuery);
