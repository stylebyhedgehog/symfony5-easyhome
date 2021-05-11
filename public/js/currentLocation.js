$(document).ready(check)

function check(){
    localStorage.getItem("location")==null ? ymaps.ready(init): setDataInView()
}

function init() {
    let userAddress
    var location = ymaps.geolocation.get();
    location.then(
        function(result) {
            userAddress = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.LocalityName')
            setDataInLocalStorage(userAddress)
            setDataInView()
        },
        function(err) {
            console.log('Ошибка: ' + err)
        }
    );
}

function setDataInView(){
    let lc=localStorage.getItem("location")
    $('#current_city').text(lc)
    $('#ad_filter_city option[value='+lc+']').prop('selected', true);
}
function setDataInLocalStorage(userAddress){
    localStorage.setItem("location",userAddress)
}

