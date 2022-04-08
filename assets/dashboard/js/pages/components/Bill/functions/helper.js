const axios  = require("axios");
const toastr = require("toastr");
const Swal   = require("sweetalert2");
const SwalOptions = require("@commonComponents/functions/swalOptions");
const Routing = require('@publicFolder/bundles/fosjsrouting/js/router.min.js');

const Formulaire = require('@dashboardComponents/functions/Formulaire');

const URL_GENERATE_INVOICE = "api_bill_invoices_generate";

function getTaxesAndUnitiesSelectItems(taxes, unities) {
    let selectUnities = [];
    unities.forEach(el => {
        selectUnities.push({ value: el.name, label: el.name, identifiant: "u-" + el.id })
    })

    let selectTvas = [];
    taxes.forEach(el => {
        selectTvas.push({ value: el.code, label: el.rate+" %", identifiant: "t-" + el.id })
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
        self.setState({ dueAt: "" })
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

function generateInvoice(self, elem, dateAt, dueAt, dueType)
{
    Swal.fire(SwalOptions.options("Finaliser la facture",
        "Une fois finalisée, la facture <u>ne pourra plus être modifiée</u>. " +
        "En cas d'erreur sur la facture, il faudra faire un avoir pour la rectifier. <br><br>" +
        "<b>Un mail sera envoyé aux adhérents pour les notifier de la création de leur(s) facture(s)</b>"))
        .then((result) => {
            if (result.isConfirmed) {
                Formulaire.loader(true);
                axios({ method: "POST", url: Routing.generate(URL_GENERATE_INVOICE, {'id': elem.id}), data: {
                    dateAt: dateAt,
                    dueAt: dueAt,
                    dueType: dueType,
                } })
                    .then(function (response) {
                        let data = response.data;
                        toastr.info("Félicitations ! La facture a été réalisée avec succès !")

                        if (self.props.onCloseAside) {
                            self.props.onCloseAside()
                        }
                        if (self.props.onUpdateList) {
                            self.props.onUpdateList(data, "update")
                        }

                        if(self.props.onUpdateDateInvoice){
                            self.props.onUpdateDateInvoice(dateAt)
                        }else{
                            self.handleUpdateDateInvoice(dateAt)
                        }

                    })
                    .catch(function (error) {
                        Formulaire.displayErrors(self, error);
                    })
                    .then(function () {
                        Formulaire.loader(false);
                    })
                ;
            }
        })
    ;
}

function validateDates(paramsToValidate, dateInvoice, dateAt, dueAt, dueType){
    if(dateInvoice){
        if(dateAt !== ""){
            paramsToValidate = [...paramsToValidate,
                ...[{type: "dateCompare", id: 'dateAt', value: new Date(dateInvoice), idCheck: 'dateInvoice', valueCheck: dateAt}]
            ];
        }
    }

    if(parseInt(dueType) !== 1){
        if(dueAt === ""){
            paramsToValidate = [...paramsToValidate,
                ...[{type: "date", id: 'dueAt', value: dueAt}]
            ];
        }else{
            paramsToValidate = [...paramsToValidate,
                ...[{type: "dateCompare", id: 'dueAt', value: dateAt, idCheck: 'dateAt', valueCheck: dueAt}]
            ];
        }
    }

    return paramsToValidate;
}

function setRateTva (self, taxes, name, e,) {
    if(name === "codeTva"){
        let rateTva = 0;

        if(e.value !== undefined){
            taxes.forEach(tva => {
                if(tva.code === parseInt(e.value)){
                    rateTva = tva.rate;
                }
            })
        }

        self.setState({ rateTva: rateTva })
    }
}

function checkLength(paramsToValidate, name, value) {
    if(value && value !== ""){
        paramsToValidate = [...paramsToValidate,
            ...[{type: "length", id: name,   value: value, min: 0, max: 40},]
        ];
    }

    return paramsToValidate
}


module.exports = {
    getTaxesAndUnitiesSelectItems,
    getTotalHt,
    getConditionPaiementChoices,
    setDueAt,
    generateInvoice,
    validateDates,
    setRateTva,
    checkLength,
}
