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

function getConditionPaiementChoices(){
    return [
        { value: 0, label: "Définir manuellement", identifiant: "c-0" },
        { value: 1, label: "Acquitté",             identifiant: "c-1" },
        { value: 2, label: "8 jours",              identifiant: "c-2" },
        { value: 3, label: "14 jours",             identifiant: "c-3" },
        { value: 4, label: "30 jours",             identifiant: "c-4" },
    ]
}

function setDueAt(self, dueType, dateAt) {
    let val = parseInt(dueType);
    if(val === 1){
        this.setState({ dueAt: "" })
    }else{
        if(val !== 0 && dateAt !== ""){
            let dueAt = new Date(dateAt);
            dueAt.setHours(0,0,0);

            switch (val){
                case 2: // 8j
                    dueAt = dueAt.setDate(dueAt.getDate() + 8);
                    break;
                case 3: // 14j
                    dueAt = dueAt.setDate(dueAt.getDate() + 14);
                    break;
                case 4: // 30j
                    dueAt = dueAt.setDate(dueAt.getDate() + 30);
                    break;
                default:
                    break;
            }
            dueAt = new Date(dueAt);

            self.setState({ dueAt: dueAt })
        }
    }
}

module.exports = {
    getTaxesAndUnitiesSelectItems,
    getTotalHt,
    getConditionPaiementChoices,
    setDueAt
}
