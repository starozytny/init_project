const Sanitaze = require('@commonComponents/functions/sanitaze')

function search(type, dataImmuable, search) {
    let newData = [];
    search = search.toLowerCase();
    search = Sanitaze.removeAccents();
    newData = dataImmuable.filter(function(v) {
        return switchFunction(type, search, v);
    })

    return newData;
}

function searchStartWith (value, search){
    let val = Sanitaze.removeAccents(value);
    return val.startsWith(search)
}

function switchFunction(type, search, v) {
    switch (type) {
        case "user":
            if(searchStartWith(v.username.toLowerCase(), search)
                || searchStartWith(v.email.toLowerCase(), search)
                || searchStartWith(v.firstname.toLowerCase(), search)
                || searchStartWith(v.lastname.toLowerCase(), search)
            ){
                return v;
            }
            break;
        case "society":
        case "changelog":
            if(searchStartWith(v.name.toLowerCase(), search)){
                return v;
            }
            break;
        case "invoice":
            if(v.numero.toString().toLowerCase().includes(search)
                || v.toName.toLowerCase().startsWith(search)){
                return v;
            }
            break;
        case "customer":
        case "unity":
        case "item":
            if(v.name.toString().toLowerCase().includes(search)){
                return v;
            }
            break;
        case "taxe":
            if(v.rate.toString().toLowerCase().includes(search)){
                return v;
            }
            break;
        default:
            break;
    }
}

module.exports = {
    search
}
