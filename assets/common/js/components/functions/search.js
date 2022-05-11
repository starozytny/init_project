const Sanitaze = require('@commonComponents/functions/sanitaze')

function search(type, dataImmuable, search) {
    let newData = [];
    search = search.toLowerCase();
    search = Sanitaze.removeAccents(search);
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
        case "changelog":
            if(searchStartWith(v.name.toLowerCase(), search)){
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