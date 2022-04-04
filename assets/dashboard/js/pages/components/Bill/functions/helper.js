const Formulaire = require('@dashboardComponents/functions/Formulaire');

function getTaxesAndUnitiesSelectItems(taxes, unities) {
    let selectUnities = [];
    unities.forEach(el => {
        selectUnities.push({ value: el.name, label: el.name, identifiant: "u-" + el.id })
    })

    let selectTvas = [];
    taxes.forEach(el => {
        selectTvas.push({ value: el.rate, label: el.rate+" %", identifiant: "t-" + el.id })
    })

    return [selectTvas, selectUnities];
}

function getTotalHt(quantity, price){
    let nQuantity = quantity !== "" ? parseInt(quantity) : 0;
    let nPrice = price !== "" ? Formulaire.setToFloat(price) : 0;

    return nQuantity * nPrice;
}

module.exports = {
    getTaxesAndUnitiesSelectItems,
    getTotalHt
}
