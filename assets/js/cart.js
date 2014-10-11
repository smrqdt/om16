/**
 * Listen for changes on item variant.
 */
$(".item-variant-form").each(function(index, form){
    $(form).find(".new-size").change(function(){
        form.submit();
    });
});
