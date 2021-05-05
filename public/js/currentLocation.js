ymaps.ready(init);
//TODO СТАВИТЬ В СЕССИЮ ТЕ ВЫЧИСЛЯТЬ ТОЛЬКО ОДИН РАЗ
// https://yandex.ru/dev/maps/geocoder/doc/desc/concepts/input_params.html
let userAddress
function init() {

    var location = ymaps.geolocation.get();
    location.then(
    function(result) {
        // Добавление местоположения на карту.
        userAddress = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.LocalityName')
        $('#current_city').text(userAddress)
        $('#ad_filter_city option[value='+userAddress+']').prop('selected', true);
    },
    function(err) {
        console.log('Ошибка: ' + err)
    }
);
}





