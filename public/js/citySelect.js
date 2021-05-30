$(document).ready(function () {
    $('#ad_region').change( function () {
        let region = $(this).val()
        ajax_by_region(region)
    });

});

$('#ad_region').ready(function (){
    $('#ad_region option').each(function () {
        if ($(this).is(':selected')) {
            ajax_by_region($(this).val(),"edit")
        }
    })
})

function ajax_by_region(region,mode="create") {
    $.get("http://localhost:8000/service/cities_in_region/" + region, function (data) {
        $('#ad_city').empty()
        $.each(data, function (key, value) {
            $('#ad_city').append('<option value=' + key + '>' + value + '</option>');
        });
        $("#ad_city").prop("disabled", false);
        if (mode!=="create"){
            $('#ad_city').val($('#city_value').val())
        }
    });
}
