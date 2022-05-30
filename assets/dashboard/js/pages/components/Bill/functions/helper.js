const axios  = require("axios");
const toastr = require("toastr");
const Swal   = require("sweetalert2");
const SwalOptions = require("@commonComponents/functions/swalOptions");
const Routing = require('@publicFolder/bundles/fosjsrouting/js/router.min.js');

const Formulaire = require('@dashboardComponents/functions/Formulaire');

const URL_GENERATE_QUOTATION = "api_bill_quotations_generate";
const URL_GENERATE_AVOIR     = "api_bill_avoirs_generate";

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
    let nQuantity = quantity !== "" ? parseFloat(quantity) : 0;
    let nPrice = price !== "" ? Formulaire.setToFloat(price) : 0;

    return nQuantity * nPrice;
}

function getConditionPaiementChoices(){
    return [
        { value: 0, label: "Définir manuellement", identifiant: "c-0" },
        // { value: 1, label: "Acquitté",             identifiant: "c-1" },
        { value: 2, label: "8 jours",              identifiant: "c-2" },
        { value: 3, label: "14 jours",             identifiant: "c-3" },
        { value: 4, label: "30 jours",             identifiant: "c-4" },
    ]
}

function getModePaiementChoices(){
    return [
        { value: 0, label: "Virement bancaire", identifiant: "mode-p-0" },
        { value: 1, label: "Chèque",            identifiant: "mode-p-1" },
        { value: 2, label: "Espèces",           identifiant: "mode-p-2" },
        { value: 3, label: "Carte bancaire",    identifiant: "mode-p-3" },
    ]
}

function changeDateDocument(self, e, name, dueType, page = "invoice")
{
    if(e !== null){
        e.setHours(0,0,0);

        if(name === "dueAt"){
            self.setState({ dueType: 0 })
        }

        if(page === "invoice" && name === "dateAt"){
            setDueAt(self, dueType, e);
        }

        if(page === "quotation" && name === "dateAt"){
            setValideTo(self, e)
        }
    }
}

function setValideTo(self, dateAt) {
    if(dateAt !== ""){
        let valideTo = new Date(dateAt);
        valideTo.setHours(0,0,0);

        valideTo = valideTo.setDate(valideTo.getDate() + 30);
        valideTo = new Date(valideTo);

        self.setState({ valideTo: valideTo })
    }
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

function generateInvoice(self, type, url, elem, dateAt, dueAt, dueType)
{
    Swal.fire(SwalOptions.options("Finaliser la facture",
        "Une fois finalisée, la facture <u>ne pourra plus être modifiée</u>. " +
        "En cas d'erreur sur la facture, il faudra faire un avoir pour la rectifier. <br><br>" +
        "<b>Un mail sera envoyé au client pour le notifier de la création de sa facture</b>"))
        .then((result) => {
            if (result.isConfirmed) {
                Formulaire.loader(true);
                axios({ method: "POST", url: url, data: {
                    dateAt: dateAt,
                    dueAt: dueAt,
                    dueType: dueType,
                } })
                    .then(function (response) {
                        let data = response.data;
                        toastr.info("Félicitations ! La facture a été réalisée avec succès !")

                        if(type === "reload"){
                            setTimeout(() => {
                                location.reload()
                            }, 2000)
                        }else{
                            Formulaire.loader(false);
                        }

                        if(self.props.onCloseAside) {
                            self.props.onCloseAside()
                        }
                        if(self.props.onUpdateList && type === "invoice") {
                            self.props.onUpdateList(data, "update")
                        }

                        if(self.props.onUpdateInvoices && type === "contract"){
                            self.props.onUpdateInvoices(data, "create")
                        }

                        if(self.props.onUpdateDateInvoice){
                            self.props.onUpdateDateInvoice(dateAt)
                        }else{
                            self.handleUpdateDateInvoice(dateAt)
                        }

                    })
                    .catch(function (error) {
                        Formulaire.loader(false);
                        Formulaire.displayErrors(self, error);
                    })
                ;
            }
        })
    ;
}

function generateQuotation(self, elem)
{
    Swal.fire(SwalOptions.options("Finaliser le devis",
        "Une fois finalisée, le devis <u>ne pourra plus être modifié</u>. " +
        "<b>Un mail sera envoyé au client pour le notifier de la création du devis</b>"))
        .then((result) => {
            if (result.isConfirmed) {
                Formulaire.loader(true);
                axios({ method: "POST", url: Routing.generate(URL_GENERATE_QUOTATION, {'id': elem.id}), data: {} })
                    .then(function (response) {
                        let data = response.data;
                        toastr.info("Félicitations ! Le devis a été réalisé avec succès !")

                        if (self.props.onUpdateList) {
                            self.props.onUpdateList(data, "update")
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

function generateAvoir(self, elem)
{
    Swal.fire(SwalOptions.options("Finaliser l'avoir",
        "Une fois finalisée, l'avoir <u>ne pourra plus être modifié</u>. " +
        "<b>Un mail sera envoyé au client pour le notifier de la création de l'avoir</b>"))
        .then((result) => {
            if (result.isConfirmed) {
                Formulaire.loader(true);
                axios({ method: "POST", url: Routing.generate(URL_GENERATE_AVOIR, {'id': elem.id}), data: {} })
                    .then(function (response) {
                        let data = response.data;
                        toastr.info("Félicitations ! L'avoir a été réalisé avec succès !")

                        if (self.props.onUpdateList) {
                            self.props.onUpdateList(data, "update")
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

function validateDates(page, paramsToValidate, dateLimit, dateAt, dueAt, dueType, valideTo,
                       nameDateAt = "dateAt", nameDueAt = "dueAt")
{
    if(page === "invoice" && dateLimit){
        dateLimit.setHours(0,0,0);

        if(dateAt !== ""){
            paramsToValidate = [...paramsToValidate,
                ...[{type: "dateCompare", id: nameDateAt, value: dateLimit, idCheck: 'dateLimit', valueCheck: dateAt}]
            ];
        }
    }

    if(page === "invoice" && parseInt(dueType) !== 1){
        if(dueAt === ""){
            paramsToValidate = [...paramsToValidate,
                ...[{type: "date", id: nameDueAt, value: dueAt}]
            ];
        }else{
            if(dateAt !== ""){
                paramsToValidate = [...paramsToValidate,
                    ...[{type: "dateCompare", id: nameDueAt, value: dateAt, idCheck: nameDateAt, valueCheck: dueAt}]
                ];
            }
        }
    }

    if(page === "quotation"){
        if(valideTo === ""){
            paramsToValidate = [...paramsToValidate,
                ...[{type: "date", id: 'valideTo', value: valideTo}]
            ];
        }else{

            if(dateAt !== ""){
                paramsToValidate = [...paramsToValidate,
                    ...[{type: "dateCompare", id: 'valideTo', value: dateAt, idCheck: 'valideTo', valueCheck: valideTo}]
                ];
            }
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

/**
 * Utilisé sur Invoice and Quotation pour archive ou duplicate
 */
function confirmAction (self, context, elem, url, title, text, messageSuccess, typeUpdate = 1) {
    Swal.fire(SwalOptions.options(title, text))
        .then((result) => {
            if (result.isConfirmed) {
                callApiGenerique(self, context, elem, url, title, text, messageSuccess, typeUpdate)
            }
        })
    ;
}

function callApiGenerique(self, context, elem, url, title, text, messageSuccess, typeUpdate = 1) {
    Formulaire.loader(true);

    axios.post(url, {})
        .then(function (response) {
            let data = response.data;

            toastr.info(messageSuccess)

            if (typeUpdate === 1 && self.props.onUpdateList) {
                Formulaire.loader(false);
                self.props.onUpdateList(data, context);
            }else if(typeUpdate === 2 && self.props.onUpdateListCustom){
                Formulaire.loader(false);
                self.props.onUpdateListCustom(data, context);
            }else{
                setTimeout(() => {
                    location.reload()
                }, 2000)
            }
        })
        .catch(function (error) {
            Formulaire.loader(false);
            Formulaire.displayErrors(self, error, "Une erreur est survenue, veuillez contacter le support.")
        })
    ;
}

function getDatePlusOne(year, month)
{
    let monthPlus = month + 1 > 12 ? 1 : month + 1;
    let yearPlus = month + 1 > 12 ? year + 1 : year;

    return [yearPlus, monthPlus];
}


function addZero(value)
{
    value = value + 1;

    if(value <= 9) {
        return "00000" + value;
    }else if(value <= 99) {
        return "0000" + value;
    }else if(value <= 999) {
        return "000" + value;
    }else if(value <= 9999) {
        return "00" + value;
    }else if(value <= 99999) {
        return "0" + value;
    }else if(value <= 999999) {
        return value;
    }
    return "ERROR";
}

function guessNumero(prefix, year, nYear, counter)
{
    if(year !== nYear){
        year = nYear;
        counter = 0;
    }

    return prefix + year + "-" + addZero(counter);
}

function getProductsAndTotal(element, products)
{
    let totalHt = 0,
        totalRemise = element ? Formulaire.setValueEmptyIfNull(element.totalRemise, 0) : 0,
        totalTva = 0,
        totalTtc = 0;

    let nProducts = [];
    if(element){
        products.forEach(pr => {
            if(pr.identifiant === element.identifiant){
                nProducts.push(pr);

                totalHt += pr.quantity * pr.price;
                totalTva += (pr.quantity * pr.price) * (pr.rateTva/100)
            }
        })

        totalTtc = totalHt + totalTva - totalRemise
    }

    return [nProducts, totalHt, totalRemise, totalTva, totalTtc];
}

function changeProducts (self, context, products, totalRemise, item)
{
    let nProducts = [];

    if(context !== "update"){
        let find = false;
        products.forEach(pr => {
            if(pr.uid === item.uid){
                find = true;
            }
        })

        if(!find){
            nProducts = products;
            nProducts.push(item)
        }else{
            nProducts = products.filter(pr => pr.uid !== item.uid)
        }
    }else{
        products.forEach(pr => {
            if(pr.uid === item.uid){
                pr = item;
            }

            nProducts.push(pr);
        })
    }

    let totalHt = 0,
        totalTva = 0,
        totalTtc = 0;

    nProducts.forEach(pr => {
        totalHt += pr.quantity * pr.price;
        totalTva += (pr.quantity * pr.price) * (pr.rateTva/100)
    })

    totalTtc = totalHt + totalTva - totalRemise

    self.setState({ products: nProducts, totalHt: totalHt, totalTva: totalTva, totalTtc: totalTtc })
}

function selectProduct (self, items, item)
{
    let nItem = null;

    if(item){
        items.forEach(it => {
            if(it.id === item.value){
                nItem = it;
            }
        })
    }

    self.setState({ element: nItem, typeProduct: "create" })
}

function getTvasTab (products)
{
    let tvas = [];
    products.forEach(pr => {
        let quantity = pr.quantity;
        let price = pr.price;
        let codeTva = pr.codeTva;
        let rateTva = pr.rateTva;

        if(quantity !== null && quantity !== "" && price !== null && price !== "" && rateTva !== null && rateTva !== ""){
            let montantHt = parseFloat(quantity) * parseFloat(price);
            let total = montantHt * (parseFloat(rateTva) / 100);

            let tmp = [], find = false;
            tvas.forEach(tv => {
                if(tv.code === codeTva){
                    find = true;
                    tv.base += montantHt;
                    tv.total += total;
                }

                tmp.push(tv)
            })

            if(!find){
                tmp.push({
                    code: codeTva,
                    base: Math.round((montantHt + Number.EPSILON) * 100) / 100,
                    rate: rateTva,
                    total: Math.round((total + Number.EPSILON) * 100) / 100
                })
            }

            tvas = tmp;
        }
    })

    return tvas;
}

function validateDatesInvoice(dateAt, dateInvoice)
{
    if(dateInvoice){
        dateInvoice.setHours(0, 0, 0);

        if(dateAt < dateInvoice){
            return false;
        }else{
            return true;
        }
    }else{
        return true;
    }
}

function checkDatesInvoice(self, type, url, elem, dateInvoice)
{
    let dateAt = new Date(elem.dateAtJavascript);
    dateAt.setHours(0, 0, 0);

    if(!validateDatesInvoice(dateAt, dateInvoice)){
        self.setState({ element: elem })
        self.asideGenerate.current.handleOpen();
    }else{
        generateInvoice(self, type, url, elem, dateAt, elem.dueAtJavascript, elem.dueType)
    }
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
    confirmAction,
    generateQuotation,
    callApiGenerique,
    getDatePlusOne,
    getProductsAndTotal,
    guessNumero,
    changeDateDocument,
    changeProducts,
    selectProduct,
    getTvasTab,
    checkDatesInvoice,
    validateDatesInvoice,
    generateAvoir,
    getModePaiementChoices,
}
