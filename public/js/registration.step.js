/**
 * Created by Takabazar on 16-Feb-16.
 */

//Error message controller

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}
$('.prev-step').click(function (e) {
    $('html,body').animate({
        scrollTop: $('#form_head').offset().top-65
    },'slow');
});