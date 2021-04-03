ident_set = (price, ad_id) => {
    let price_string = price.toString();
    let reversed_price = price_string.split("").reverse().join("");
    let interval_price = reversed_price.toString().replace(/\d{3}(?=.)/g, '$& ');
    let result = interval_price.split("").reverse().join("");
    document.getElementById("price_for_" + ad_id.toString()).innerHTML = result + " р";
}