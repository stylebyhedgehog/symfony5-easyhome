ymaps.ready(init);

function init() {

    myMap = new ymaps.Map('map', {
        center: [55.8021303942946, 49.106238365611695],
        zoom : 12
    });
    $('.address_field').each(function (){
        var currentElement = $(this);
        address=currentElement.text()
        var myGeocoder = ymaps.geocode(address);

        myGeocoder.then(function(res) {
                myMap.geoObjects.add(res.geoObjects);
            },
            function (err) {
                console.log("ошибка")
            });
    })
}

