import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Swal                    from "sweetalert2";
import SwalOptions             from "@commonComponents/functions/swalOptions";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { TextArea }            from "@dashboardComponents/Tools/Fields";
import { Button, ButtonIcon }  from "@dashboardComponents/Tools/Button";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Sanitaze                from "@commonComponents/functions/sanitaze";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

import { Form1, View1 } from "@dashboardPages/components/Bill/components/Step1";
import { Form2, View2 } from "@dashboardPages/components/Bill/components/Step2";
import { Form4, View4 } from "@dashboardPages/components/Bill/components/Step4";
import { Products }     from "@dashboardPages/components/Bill/components/Products";

const URL_INVOICE_CREATE_ELEMENT     = "api_bill_invoices_create";
const URL_INVOICE_UPDATE_ELEMENT     = "api_bill_invoices_update";
const URL_QUOTATION_CREATE_ELEMENT   = "api_bill_quotations_create";
const URL_QUOTATION_UPDATE_ELEMENT   = "api_bill_quotations_update";
const URL_AVOIR_CREATE_ELEMENT       = "api_bill_avoirs_create";
const URL_AVOIR_UPDATE_ELEMENT       = "api_bill_avoirs_update";
const URL_INVOICE_INDEX              = "admin_bill_invoices_index";
const URL_QUOTATION_INDEX            = "admin_bill_quotations_index";
const URL_AVOIR_INDEX                = "admin_bill_avoirs_index";

const TXT_CREATE_BUTTON_FORM = "Enregistrer le brouillon";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let arrayZipcodes = [];
let i = 0;

export function DocumentFormulaire ({ page, type, onChangeContext, onUpdateList, element, society,
                                         items, taxes, unities, products, customers, sites,
                                        quotationId=null, quotationRef=null, invoiceId=null, invoiceRef=null })
{
    let title, url, msg;
    let dateAt, dueAt = null, dueType = null, payType = null, valideTo = null;
    let note = "", footer = "";
    let dateLimit = page === "invoice" ? Formulaire.setDateOrEmptyIfNull(society.dateInvoiceJavascript) : new Date();

    if(type === "create"){
        dateAt = Formulaire.setDateOrEmptyIfNull(dateLimit);
    }else{
        dateAt = element ? Formulaire.setDateOrEmptyIfNull(element.dateAtJavascript) : Formulaire.setDateOrEmptyIfNull(dateLimit);
    }

    switch (page) {
        case "avoir":
            title = "Ajouter un avoir";
            url = Routing.generate(URL_AVOIR_CREATE_ELEMENT)
            msg = "Félicitations ! Vous avez ajouté un nouveau avoir !"
            note = element ? Formulaire.setValueEmptyIfNull(element.note) : society.noteAvoir;
            footer = element ? Formulaire.setValueEmptyIfNull(element.footer) : society.footerAvoir;

            if(type === "update"){
                title = "Modifier " + element.numero;
                url = Routing.generate(URL_AVOIR_UPDATE_ELEMENT, {'id': element.id});
                msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
            }

            if(invoiceId){
                dateAt = new Date();
            }

            break;
        case "quotation":
            title = "Ajouter un devis";
            url = Routing.generate(URL_QUOTATION_CREATE_ELEMENT)
            msg = "Félicitations ! Vous avez ajouté un nouveau devis !"
            note = element ? Formulaire.setValueEmptyIfNull(element.note) : society.noteQuotation;
            footer = element ? Formulaire.setValueEmptyIfNull(element.footer) : society.footerQuotation;

            if(type === "update"){
                title = "Modifier " + element.numero;
                url = Routing.generate(URL_QUOTATION_UPDATE_ELEMENT, {'id': element.id});
                msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
            }


            valideTo = new Date();
            valideTo = valideTo.setDate(new Date().getDate() + 30);
            valideTo = new Date(valideTo);

            if(type === "update"){
                valideTo = element ? Formulaire.setDateOrEmptyIfNull(element.valideToJavascript) : valideTo;
            }

            break;
        default:
            title = "Ajouter une facture";
            url = Routing.generate(URL_INVOICE_CREATE_ELEMENT)
            msg = "Félicitations ! Vous avez ajouté une nouvelle facture !"
            dueType = element ? Formulaire.setValueEmptyIfNull(element.dueType, 2) : 2;
            payType = element ? Formulaire.setValueEmptyIfNull(element.payType, 0) : 0;
            note = element ? Formulaire.setValueEmptyIfNull(element.note) : society.noteInvoice;
            footer = element ? Formulaire.setValueEmptyIfNull(element.footer) : society.footerInvoice;

            if(dateAt){
                if(dateLimit){
                    dateLimit.setHours(0,0,0);
                    dateAt.setHours(0,0,0);

                    if(dateAt < dateLimit){
                        dateAt = dateLimit;
                    }
                }

                dueAt = new Date();
                dueAt.setFullYear(dateAt.getFullYear());
                dueAt.setMonth(dateAt.getMonth());
                dueAt.setDate(dateAt.getDate() + 8);

                dueAt = new Date(dueAt);
            }

            if(type === "update"){
                title = "Modifier " + element.numero;
                url = Routing.generate(URL_INVOICE_UPDATE_ELEMENT, {'id': element.id});
                msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";

                dateAt = element ? Formulaire.setDateOrEmptyIfNull(element.dateAtJavascript) : Formulaire.setDateOrEmptyIfNull(dateLimit);
                dueAt = element ? Formulaire.setDateOrEmptyIfNull(element.dueAtJavascript) : dueAt;
            }

            if(type === "create" && element) { // === generate from data quotation
                dateAt = "";
                dueAt = ""
                dueType = "";
            }
            break;
    }

    let [nProducts, totalHt, totalRemise, totalTva, totalTtc] = helper.getProductsAndTotal(element, products);

    let form = <Form
        page={page}
        context={type}
        url={url}

        invoiceId={invoiceId}
        invoiceRef={invoiceRef}

        quotationId={quotationId}
        quotationRef={quotationRef}

        items={items}
        taxes={taxes}
        unities={unities}
        customers={customers}
        sites={sites}

        society={society}
        dateLimit={dateLimit}

        dateAt={dateAt}

        valideTo={page === "quotation" ? valideTo : null}
        dueAt={dueAt}
        dueType={dueType}
        payType={payType}

        numero={element ? Formulaire.setValueEmptyIfNull(element.numero) : ""}

        customer={element ? Formulaire.setValueEmptyIfNull(element.customerId) : ""}
        refCustomer={element ? Formulaire.setValueEmptyIfNull(element.refCustomer) : ""}
        toName={element ? Formulaire.setValueEmptyIfNull(element.toName) : ""}
        toAddress={element ? Formulaire.setValueEmptyIfNull(element.toAddress) : ""}
        toAddress2={element ? Formulaire.setValueEmptyIfNull(element.toAddress2) : ""}
        toComplement={element ? Formulaire.setValueEmptyIfNull(element.toComplement) : ""}
        toZipcode={element ? Formulaire.setValueEmptyIfNull(element.toZipcode) : ""}
        toCity={element ? Formulaire.setValueEmptyIfNull(element.toCity) : ""}
        toCountry={element ? Formulaire.setValueEmptyIfNull(element.toCountry, "France") : "France"}
        toEmail={element ? Formulaire.setValueEmptyIfNull(element.toEmail) : ""}
        toPhone1={element ? Formulaire.setValueEmptyIfNull(element.toPhone1) : ""}

        site={element ? Formulaire.setValueEmptyIfNull(element.siteId) : ""}
        refSite={element ? Formulaire.setValueEmptyIfNull(element.refSite) : ""}
        siName={element ? Formulaire.setValueEmptyIfNull(element.siName) : ""}
        siAddress={element ? Formulaire.setValueEmptyIfNull(element.siAddress) : ""}
        siAddress2={element ? Formulaire.setValueEmptyIfNull(element.siAddress2) : ""}
        siComplement={element ? Formulaire.setValueEmptyIfNull(element.siComplement) : ""}
        siZipcode={element ? Formulaire.setValueEmptyIfNull(element.siZipcode) : ""}
        siCity={element ? Formulaire.setValueEmptyIfNull(element.siCity) : ""}
        siCountry={element ? Formulaire.setValueEmptyIfNull(element.siCountry) : ""}
        siEmail={element ? Formulaire.setValueEmptyIfNull(element.siEmail) : ""}
        siPhone1={element ? Formulaire.setValueEmptyIfNull(element.siPhone1) : ""}

        products={nProducts}

        totalHt={totalHt}
        totalRemise={totalRemise}
        totalTva={totalTva}
        totalTtc={totalTtc}

        note={note ? note : ""}
        footer={footer ? footer : ""}

        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return onChangeContext ? <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout> : <div className="form">{form}</div>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            invoiceId: props.invoiceId,
            invoiceRef: props.invoiceRef,

            quotationId: props.quotationId,
            quotationRef: props.quotationRef,

            societyId: props.society.id,
            dateAt: props.dateAt,

            valideTo: props.valideTo,
            dueAt: props.dueAt,
            dueType: props.dueType,
            payType: props.payType,

            numero: props.numero,

            customer: props.customer,
            refCustomer: props.refCustomer,
            toName: props.toName,
            toAddress: props.toAddress,
            toAddress2: props.toAddress2,
            toComplement: props.toComplement,
            toZipcode: props.toZipcode,
            toCity: props.toCity,
            toCountry: props.toCountry,
            toEmail: props.toEmail,
            toPhone1: props.toPhone1,

            enableSite: props.site || props.siName || props.siAddress ? [1] : [0],
            site: props.site,
            refSite: props.refSite,
            siName: props.siName,
            siAddress: props.siAddress,
            siAddress2: props.siAddress2,
            siComplement: props.siComplement,
            siZipcode: props.siZipcode,
            siCity: props.siCity,
            siCountry: props.siCountry,
            siEmail: props.siEmail,
            siPhone1: props.siPhone1,

            note: props.note,
            footer: props.footer,
            products: props.products,
            totalHt: props.totalHt,
            totalRemise: props.totalRemise,
            totalTva: props.totalTva,
            totalTtc: props.totalTtc,

            errors: [],
            success: false,
            element: null,
            item: "",
            arrayPostalCode: [],
            edit: null,
            toGenerate: false,
            typeProduct: "create"
        }

        this.asideSelect = React.createRef();
        this.asideAdd = React.createRef();

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeZipcodeCity = this.handleChangeZipcodeCity.bind(this);
        this.handleChangeDate = this.handleChangeDate.bind(this);

        this.handleSelectItem = this.handleSelectItem.bind(this);
        this.handleChangeItems = this.handleChangeItems.bind(this);
        this.handleUpdateItem = this.handleUpdateItem.bind(this);
        this.handleSelectCustomer = this.handleSelectCustomer.bind(this);
        this.handleSelectSite = this.handleSelectSite.bind(this);

        this.handleClickEdit = this.handleClickEdit.bind(this);
        this.handleSend = this.handleSend.bind(this);
    }

    componentDidMount() {
        Helper.getPostalCodes(this);
    }

    handleClickEdit = (numero) => { this.setState({ edit: numero === this.state.edit ? null : numero, typeProduct: "create", element: null }) }

    handleChange = (e) => {
        const { dateAt } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "dueType"){
            helper.setDueAt(this, value, dateAt);
        }

        if(name === "refCustomer" || name === "toName" || name === "toAddress" || name === "toAddress2" || name === "toComplement"
            || name === "toZipcode" || name === "toCity" || name === "toCountry" || name === "toEmail" || name === "toPhone1")
        {
            this.setState({ customer: "" })
        }

        if(name === "refSite" || name === "siName" || name === "siAddress" || name === "siAddress2" || name === "siComplement"
            || name === "siZipcode" || name === "siCity" || name === "siCountry" || name === "siEmail" || name === "siPhone1")
        {
            this.setState({ site: "" })
        }

        if(name === "enableSite"){
            if(e.currentTarget.checked){
                value = [parseInt(value)]
            }else{
                value = [0];

                this.setState({
                    site: "",
                    refSite: "",
                    siName: "",
                    siAddress: "",
                    siAddress2: "",
                    siComplement: "",
                    siZipcode: "",
                    siCity: "",
                    siCountry: "",
                    siEmail: "",
                    siPhone1: "",
                })
            }
        }

        this.setState({[name]: value})
    }

    handleChangeZipcodeCity = (nameCity, e) => {
        const { arrayPostalCode } = this.state;

        Helper.setCityFromZipcode(this, e, arrayPostalCode ? arrayPostalCode : arrayZipcodes, nameCity)
    }

    handleChangeDate = (name, e) => {
        helper.changeDateDocument(this, e, name, this.state.dueType, this.props.page);

        this.setState({ [name]: e !== null ? e : "" })
    }

    handleSelectItem = (item) => { helper.selectProduct(this, this.props.items, item); }

    handleChangeItems = (item, context) => { helper.changeProducts(this, context, this.state.products, this.state.totalRemise, item); }

    handleUpdateItem = (item) => { this.setState({ element: item, edit: 3, typeProduct: "update" }) }

    handleSelectCustomer = (elem) => {
        const { customers } = this.props;

        let nCustomer = null;
        if(elem){
            customers.forEach(it => {
                if(it.id === elem.value){
                    nCustomer = it;
                }
            })
        }

        if(nCustomer){
            this.setState({
                customer: Formulaire.setValueEmptyIfNull(nCustomer.id),
                refCustomer: Formulaire.setValueEmptyIfNull(nCustomer.numero),
                toName: Formulaire.setValueEmptyIfNull(nCustomer.name),
                toAddress: Formulaire.setValueEmptyIfNull(nCustomer.address),
                toAddress2: Formulaire.setValueEmptyIfNull(nCustomer.address2),
                toComplement: Formulaire.setValueEmptyIfNull(nCustomer.complement),
                toZipcode: Formulaire.setValueEmptyIfNull(nCustomer.zipcode),
                toCity: Formulaire.setValueEmptyIfNull(nCustomer.city),
                toCountry: Formulaire.setValueEmptyIfNull(nCustomer.country),
                toEmail: Formulaire.setValueEmptyIfNull(nCustomer.email),
                toPhone1: Formulaire.setValueEmptyIfNull(nCustomer.phone),
                payType: Formulaire.setValueEmptyIfNull(nCustomer.payType)
            })
        }
    }

    handleSelectSite = (elem) => {
        const { sites } = this.props;
        const { payType } = this.state;

        let nSite = null;
        if(elem){
            sites.forEach(si => {
                if(si.id === elem.value){
                    nSite = si;
                }
            })
        }

        if(nSite){
            this.setState({
                enableSite: [1],
                site: Formulaire.setValueEmptyIfNull(nSite.id),
                refSite: Formulaire.setValueEmptyIfNull(nSite.numero),
                siName: Formulaire.setValueEmptyIfNull(nSite.name),
                siAddress: Formulaire.setValueEmptyIfNull(nSite.address),
                siAddress2: Formulaire.setValueEmptyIfNull(nSite.address2),
                siComplement: Formulaire.setValueEmptyIfNull(nSite.complement),
                siZipcode: Formulaire.setValueEmptyIfNull(nSite.zipcode),
                siCity: Formulaire.setValueEmptyIfNull(nSite.city),
                siCountry: Formulaire.setValueEmptyIfNull(nSite.country),
                siEmail: Formulaire.setValueEmptyIfNull(nSite.email),
                siPhone1: Formulaire.setValueEmptyIfNull(nSite.phone),
                payType: nSite.payType ? nSite.payType : payType
            })
        }
    }

    handleSubmit = (e, toGenerate=false) => {
        e.preventDefault();

        const { page, context, url, messageSuccess, dateLimit } = this.props;
        const { dateAt, valideTo, dueType, dueAt, refCustomer, toName, toAddress, toAddress2, toComplement, toZipcode, toCity, toCountry,
            refSite, siName, siAddress, siAddress2, siComplement, siZipcode, siCity, siCountry,  } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: 'dateAt',      value: dateAt},
            {type: "text", id: 'refCustomer', value: refCustomer},
            {type: "text", id: 'toName',      value: toName},
            {type: "text", id: 'toAddress',   value: toAddress},
            {type: "text", id: 'toZipcode',   value: toZipcode},
            {type: "text", id: 'toCity',      value: toCity},
            {type: "text", id: 'toCountry',   value: toCountry},
            {type: "length", id: 'toAddress',   value: toAddress, min: 0, max: 40},
        ];

        paramsToValidate = helper.validateDates(page, paramsToValidate, dateLimit, dateAt, dueAt, dueType, valideTo);
        paramsToValidate = helper.checkLength(paramsToValidate, "toAddress2", toAddress2);
        paramsToValidate = helper.checkLength(paramsToValidate, "toComplement", toComplement);

        if(refSite !== "" || siName !== "" || siAddress !== "" || siAddress2 !== "" || siComplement !== "" ||
            siZipcode !== "" || siCity !== "" || siCountry !== "" )
        {
            paramsToValidate = [...paramsToValidate,
                ...[
                    {type: "text", id: 'refSite',     value: refSite},
                    {type: "text", id: 'siName',      value: siName},
                    {type: "text", id: 'siAddress',   value: siAddress},
                    {type: "text", id: 'siZipcode',   value: siZipcode},
                    {type: "text", id: 'siCity',      value: siCity},
                    {type: "text", id: 'siCountry',   value: siCountry},
                ]
            ];

            paramsToValidate = helper.checkLength(paramsToValidate, "siAddress2", siAddress2);
            paramsToValidate = helper.checkLength(paramsToValidate, "siComplement", siComplement);
        }

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            let send = true;

            if(page === "invoice"){
                dateAt.setHours(0, 0, 0);
                if(!helper.validateDatesInvoice(dateAt, dateLimit)){
                    toastr.error('Veuillez vérifier que le date de facture est cohérente avec la dernière date de facturation.');
                    send = false;
                }
            }

            if(send){
                if(toGenerate){
                    Swal.fire(SwalOptions.options("Finaliser",
                        "Une fois finalisée, le document <u>ne pourra plus être modifié</u>. " +
                        "<br><br><b>Un mail sera envoyé au client pour le notifier de la création du document.</b>"))
                        .then((result) => {
                            if (result.isConfirmed) {
                                this.handleSend(page, method, url, messageSuccess, toGenerate)
                            }
                        })
                    ;
                }else{
                    this.handleSend(page, method, url, messageSuccess, toGenerate)
                }
            }

        }
    }

    handleSend = (page, method, url, messageSuccess, toGenerate = false) => {
        Formulaire.loader(true);
        let self = this;

        arrayZipcodes = this.state.arrayPostalCode;
        delete this.state.arrayPostalCode;

        this.state.toGenerate = toGenerate;

        axios({ method: method, url: url, data: this.state })
            .then(function (response) {
                let data = response.data;
                self.setState({ success: messageSuccess, errors: [] });
                toastr.info(messageSuccess);

                let redirectUrl = "";
                switch (page){
                    case "avoir":
                        redirectUrl = Routing.generate(URL_AVOIR_INDEX);
                        break;
                    case "quotation":
                        redirectUrl = Routing.generate(URL_QUOTATION_INDEX);
                        break;
                    default:
                        redirectUrl = Routing.generate(URL_INVOICE_INDEX);
                        break;
                }
                setTimeout(() => {
                    location.href = redirectUrl
                }, 2000)
            })
            .catch(function (error) {
                Formulaire.loader(false);
                Formulaire.displayErrors(self, error);
            })
        ;
    }

    render () {
        const { page, context, society, dateLimit, items, taxes, unities, customers, sites } = this.props;
        const { edit, element, errors, success, numero, dateAt, valideTo, dueAt, dueType, payType, customer, site, refCustomer, refSite, enableSite,
            toName, toAddress, toAddress2, toComplement, toZipcode, toCity, toCountry, toEmail, toPhone1,
            note, footer, typeProduct, item, products, totalHt, totalRemise, totalTva, totalTtc,
            siName, siAddress, siAddress2, siComplement, siZipcode, siCity, siCountry, siEmail, siPhone1 } = this.state;

        let tvas = helper.getTvasTab(products);

        let blocError1 = false, blocError2 = false, blocError4 = false;
        errors.forEach(err => {
            if(err.name === "dateAt"){
                blocError1 = true;
            }

            if(err.name === "refCustomer" || err.name === "toName" || err.name === "toAddress" || err.name === "toZipcode" || err.name === "toCity" || err.name === "toCountry"
                || err.name === "refSite" || err.name === "siName" || err.name === "siAddress" || err.name === "siZipcode" || err.name === "siCity" || err.name === "siCountry")
            {
                blocError2 = true;
            }

            if(err.name === "dueAt" || err.name === "payType" || err.name === "valideTo"){
                blocError4 = true;
            }
        })

        return <>

            {dateLimit && <InfoDateLimit page={page} dateLimit={dateLimit} society={society} />}

            <form onSubmit={(e) => this.handleSubmit(e, false)}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="invoice-line invoice-line-1">
                    <div className="col-1">
                        {society.logo ? <div className="image">
                            <img src={society.logoFile} alt="Logo entreprise"/>
                        </div> : <div className="image no-image"><span>Aucun <br/> logo</span></div>}
                        <div className="infos">
                            <div>
                                <b>n° Siren :</b> <span>{society.siren ? society.siren : <span className="txt-danger">Non renseigné</span>}</span>
                            </div>
                            <div>
                                <b>n° TVA UE :</b> <span>{society.numeroTva ? society.numeroTva : <span className="txt-danger">Non renseigné</span>}</span>
                            </div>
                            <div><b>Email :</b> <span>{society.email}</span> </div>
                            <div><b>Téléphone :</b> <span>{society.phone1}</span> </div>
                        </div>
                    </div>
                    <div className="col-2">
                        <BlocEdit step={1} edit={edit} onClickEdit={this.handleClickEdit} haveError={blocError1}
                                  view={<View1 page={page} context={context} numero={numero} dateAt={dateAt} society={society} />}
                                  form={<Form1 page={page} errors={errors} onChangeDate={this.handleChangeDate}
                                               dateLimit={dateLimit} dateAt={dateAt} />}/>
                    </div>
                </div>

                <div className="invoice-line invoice-line-2">
                    <div className="col-1">
                        <div><b>{society.name}</b></div>
                        <div>{society.address}</div>
                        <div>{society.address2}</div>
                        <div>{society.complement}</div>
                        <div>{society.zipcode}, {society.city}</div>
                        {society.country && <div>
                            {society.country}
                        </div>}
                    </div>
                    <div className="col-2">
                        <BlocEdit step={2} edit={edit} onClickEdit={this.handleClickEdit} haveError={blocError2}
                                  view={<View2 toName={toName} toAddress={toAddress} toComplement={toComplement} toAddress2={toAddress2}
                                               toZipcode={toZipcode} toCity={toCity} toCountry={toCountry} />}
                                  form={<Form2 errors={errors} onChange={this.handleChange} onChangeZipcodeCity={this.handleChangeZipcodeCity}
                                               onSelectCustomer={this.handleSelectCustomer} customers={customers} customer={customer}
                                               onSelectSite={this.handleSelectSite} sites={sites} site={site}
                                               refCustomer={refCustomer} refSite={refSite} enableSite={enableSite}
                                               siName={siName} siAddress={siAddress} siAddress2={siAddress2} siComplement={siComplement}
                                               siZipcode={siZipcode} siCity={siCity} siCountry={siCountry}
                                               siEmail={siEmail} siPhone1={siPhone1}
                                               toName={toName} toAddress={toAddress} toAddress2={toAddress2} toComplement={toComplement}
                                               toZipcode={toZipcode} toCity={toCity} toCountry={toCountry}
                                               toEmail={toEmail} toPhone1={toPhone1} />}/>
                    </div>
                </div>

                <div className="invoice-line invoice-line-references">
                    <div className="col-1"></div>
                    <div className="col-2">
                        {refCustomer ? "Réf. Client : " + refCustomer + (refSite ? " / " + refSite : "") : ""}
                    </div>
                </div>

                <div className="invoice-line invoice-line-3">
                    <Products step={3} edit={edit} onClickEdit={this.handleClickEdit}
                              products={products} onChangeItems={this.handleChangeItems} onUpdateItem={this.handleUpdateItem}
                              onSelectItem={this.handleSelectItem} item={item} items={items} type={typeProduct}
                              element={element} societyId={society.id} taxes={taxes} unities={unities} key={i++}
                    />
                </div>

                <div className="invoice-line invoice-line-4">
                    <div className="col-1">
                        <Tvas tvas={tvas} />
                        <div className="note">
                            <div className="line">
                                <TextArea valeur={note} identifiant="note" errors={errors} onChange={this.handleChange}>Note</TextArea>
                            </div>
                        </div>
                        {page !== "avoir" && <>
                            <div className="line">
                                <BlocEdit step={4} edit={edit} onClickEdit={this.handleClickEdit} haveError={blocError4}
                                          view={<View4 page={page} dueAt={dueAt} dueType={dueType} valideTo={valideTo} payType={payType} />}
                                          form={<Form4 page={page} errors={errors} onChange={this.handleChange} onChangeDate={this.handleChangeDate}
                                                       dateAt={dateAt} dueAt={dueAt} dueType={dueType} valideTo={valideTo} payType={payType} />}/>
                            </div>
                        </>}
                    </div>
                    <div className="col-2">
                        <Totaux totalHt={totalHt} totalRemise={totalRemise} totalTva={totalTva} totalTtc={totalTtc} />
                    </div>
                </div>

                <div className="invoice-line invoice-line-5">
                    <div className="bank">
                        {page === "invoice" && (society.bankIban ? <>
                            <div>
                                <span><b>Renseignements bancaires : </b> <span>{society.bankTitulaire}</span></span>
                            </div>
                            <div>
                                <span><b>IBAN : </b> <span>{society.bankIban} - </span></span>
                                <span><b>BIC : </b> <span>{society.bankBic}</span></span>
                            </div>
                            <div>
                                <span><b>Banque : </b> <span>{society.bankName} - </span></span>
                                <span><b>N° du compte : </b> <span>{society.bankNumero} - </span></span>
                                <span><b>Code banque : </b> <span>{society.bankCode}</span></span>
                            </div>
                        </> : <>
                            <Alert type="warning">
                                Veuillez renseignez vos coordonnées bancaires sur la page des paramètres pour les afficher.
                            </Alert>
                        </>)}
                    </div>
                    <div className="note">
                        <div className="line">
                            <TextArea valeur={footer} identifiant="footer" errors={errors} onChange={this.handleChange}>
                                Note
                            </TextArea>
                        </div>
                    </div>
                </div>

                <div className="line">
                    <div className="form-button">
                        {page !== "quotation" ? <>
                            <Button isSubmit={true} type="warning" onClick={(e) => this.handleSubmit(e, true)}>Finaliser</Button>
                        </> : <>
                            {context === "create" && <Button isSubmit={true} type="warning" onClick={(e) => this.handleSubmit(e, true)}>Finaliser</Button>}
                        </>}
                        <Button isSubmit={true}>{context === "create" ? TXT_CREATE_BUTTON_FORM : TXT_UPDATE_BUTTON_FORM}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}

function BlocEdit ({ edit, onClickEdit, step, view, form, haveError }) {
    return <>
        {edit !== step ? <div className={"bloc-edit" + (haveError ? " bloc-error" : "")} onClick={() => onClickEdit(step)}>
            {view}
        </div> : <div className={"bloc-edit active" + (haveError ? " bloc-error" : "")}>
            <div className="bloc-edit-cancel" onClick={() => onClickEdit(step)}>
                <ButtonIcon icon="cancel">Fermer</ButtonIcon>
            </div>
            {form}
        </div>}
        {haveError && <div className="bloc-error-msg">
            <span className="icon-warning" />
            <span>Veuillez vérifier les champs remplis.</span>
        </div>}
    </>
}

function InfoDateLimit ({ page, dateLimit, society })
{
    return page === "invoice" ? <div className="line line-infos-invoice">
        <div className="form-group">
            <Alert type="reverse" withIcon={false}>
                <label>Dernière date de facturation</label>
                <br/>
                <span>La date de facture doit être supérieur ou égale à <b>{dateLimit.toLocaleDateString()}</b> </span>
                <br/>
                <span>Le prochain numéro de facture est : <b>{society.counterInvoice + 1}</b></span>
            </Alert>
        </div>
    </div> : null;
}

export function Tvas ({ tvas }) {
    return <>
        <div className="tvas">
            <div className="items-table">
                <div className="items items-default">
                    <div className="item item-header">
                        <div className="item-content">
                            <div className="item-body">
                                <div className="infos infos-col-4">
                                    <div className="col-1">Code</div>
                                    <div className="col-2">Base HT</div>
                                    <div className="col-3">Taux TVA</div>
                                    <div className="col-4">Montant TVA</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {tvas.map(tva => {
                        return <div className="item" key={tva.code}>
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">
                                            <div><span className="forMobile">Code TVA : </span><b>{tva.code}</b></div>
                                        </div>
                                        <div className="col-2">
                                            <div className="sub"><span className="forMobile">Base HT : </span>{Sanitaze.toFormatCurrency(tva.base)}</div>
                                        </div>
                                        <div className="col-3">
                                            <div className="sub"><span className="forMobile">Taux TVA : </span>{tva.rate}%</div>
                                        </div>
                                        <div className="col-4">
                                            <div className="sub"><span className="forMobile">Total TVA : </span>{Sanitaze.toFormatCurrency(tva.total)}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    })}
                </div>
            </div>
        </div>
    </>
}

export function Totaux ({ totalHt, totalRemise, totalTva, totalTtc }) {
    return <>
        <div className="totaux">
            <div className="labels">
                <div>Total HT</div>
                <div>Total Remise</div>
                <div><b>NET HT</b></div>
                <div>Total TVA</div>
                <div><b>Total TTC</b></div>
            </div>
            <div className="values">
                <div>{Sanitaze.toFormatCurrency(totalHt)}</div>
                <div>{Sanitaze.toFormatCurrency(totalRemise)}</div>
                <div>{Sanitaze.toFormatCurrency(totalHt - totalRemise)}</div>
                <div>{Sanitaze.toFormatCurrency(totalTva)}</div>
                <div><b>{Sanitaze.toFormatCurrency(totalTtc)}</b></div>
            </div>
        </div>
    </>
}