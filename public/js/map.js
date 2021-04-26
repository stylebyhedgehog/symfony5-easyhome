ymaps.ready(init);

function init() {
    var address=$('#address_for_map').text()
    var myGeocoder = ymaps.geocode(address);
    myGeocoder.then(function(res) {
            myMap = new ymaps.Map('map', {
                center: res.geoObjects.get(0).geometry.getCoordinates(),
                zoom : 12
            });
        myMap.geoObjects.add(res.geoObjects);
    },
        function (err) {
            console.log("ошибка")
        });

}
