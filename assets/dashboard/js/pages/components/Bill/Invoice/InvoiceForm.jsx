import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { TextArea } from "@dashboardComponents/Tools/Fields";
import { Button, ButtonIcon }  from "@dashboardComponents/Tools/Button";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Sanitaze                from "@commonComponents/functions/sanitaze";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

import { Form1, View1 } from "@dashboardPages/components/Bill/Invoice/components/Step1";
import { Form2, View2 } from "@dashboardPages/components/Bill/Invoice/components/Step2";
import { Products }     from "@dashboardPages/components/Bill/Invoice/components/Products";

const URL_CREATE_ELEMENT     = "api_bill_invoices_create";
const URL_UPDATE_GROUP       = "api_bill_invoices_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let arrayZipcodes = [];
let i = 0;

export function InvoiceFormulaire ({ type, onChangeContext, onUpdateList, element, society,
                                       items, taxes, unities, products, customers })
{
    let title = "Ajouter une facture";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté une nouvelle facture !"

    if(type === "update"){
        title = "Modifier " + element.numero;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
    }

    let dateInvoice = society.dateInvoiceJavascript;

    let dueAt = null;
    if(dateInvoice){
        dueAt = new Date();
        dueAt = dueAt.setDate(new Date(dateInvoice).getDate() + 8);

        dueAt = new Date(dueAt);
    }

    let totalHt = 0,
        totalRemise = element ? Formulaire.setValueEmptyIfNull(element.totalRemise, 0) : 0,
        totalTva = 0,
        totalTtc = 0;

    let nProducts = [];
    if(element){
        products.forEach(pr => {
            if(pr.identifiant === "FA-" + element.id){
                nProducts.push(pr);

                totalHt += pr.quantity * pr.price;
                totalTva += (pr.quantity * pr.price) * (pr.rateTva/100)
            }
        })

        totalTtc = totalHt + totalTva - totalRemise
    }

    let form = <Form
        context={type}
        url={url}

        items={items}
        taxes={taxes}
        unities={unities}
        customers={customers}

        society={society}
        dateInvoice={dateInvoice}

        dateAt={element ? Formulaire.setDateOrEmptyIfNull(element.dateAtJavascript) : Formulaire.setDateOrEmptyIfNull(dateInvoice)}
        dueAt={element ? Formulaire.setDateOrEmptyIfNull(element.dueAtJavascript) : dueAt}
        dueType={element ? Formulaire.setValueEmptyIfNull(element.dueType, 2) : 2}

        toName={element ? Formulaire.setValueEmptyIfNull(element.toName) : ""}
        toAddress={element ? Formulaire.setValueEmptyIfNull(element.toAddress) : ""}
        toAddress2={element ? Formulaire.setValueEmptyIfNull(element.toAddress2) : ""}
        toComplement={element ? Formulaire.setValueEmptyIfNull(element.toComplement) : ""}
        toZipcode={element ? Formulaire.setValueEmptyIfNull(element.toZipcode) : ""}
        toCity={element ? Formulaire.setValueEmptyIfNull(element.toCity) : ""}
        toCountry={element ? Formulaire.setValueEmptyIfNull(element.toCountry, "France") : "France"}
        toEmail={element ? Formulaire.setValueEmptyIfNull(element.toEmail) : ""}
        toPhone1={element ? Formulaire.setValueEmptyIfNull(element.toPhone1) : ""}

        products={nProducts}

        totalHt={totalHt}
        totalRemise={totalRemise}
        totalTva={totalTva}
        totalTtc={totalTtc}

        note={element ? Formulaire.setValueEmptyIfNull(element.note) : ""}
        footer={element ? Formulaire.setValueEmptyIfNull(element.footer) : ""}

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
            societyId: props.society.id,
            dateAt: props.dateAt,
            dueAt: props.dueAt,
            dueType: props.dueType,
            toName: props.toName,
            toAddress: props.toAddress,
            toAddress2: props.toAddress2,
            toComplement: props.toComplement,
            toZipcode: props.toZipcode,
            toCity: props.toCity,
            toCountry: props.toCountry,
            toEmail: props.toEmail,
            toPhone1: props.toPhone1,
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
            customer: "",
            item: "",
            arrayPostalCode: [],
            edit: null
        }

        this.asideSelect = React.createRef();
        this.asideAdd = React.createRef();

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeZipcodeCity = this.handleChangeZipcodeCity.bind(this);
        this.handleChangeDate = this.handleChangeDate.bind(this);

        this.handleSelectItem = this.handleSelectItem.bind(this);
        this.handleChangeItems = this.handleChangeItems.bind(this);
        this.handleSelectCustomer = this.handleSelectCustomer.bind(this);

        this.handleClickEdit = this.handleClickEdit.bind(this);
    }

    componentDidMount() {
        Helper.getPostalCodes(this);
    }

    handleClickEdit = (numero) => { this.setState({ edit: numero === this.state.edit ? null : numero }) }

    handleChange = (e) => {
        const { dateAt } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "dueType"){
            helper.setDueAt(this, value, dateAt);
        }

        this.setState({[name]: value})
    }

    handleChangeZipcodeCity = (e) => {
        const { arrayPostalCode } = this.state;

        Helper.setCityFromZipcode(this, e, arrayPostalCode ? arrayPostalCode : arrayZipcodes, "toCity")
    }

    handleChangeDate = (name, e) => {
        const { dueType } = this.state;

        if(e !== null){
            e.setHours(0,0,0);

            if(name === "dueAt"){
                this.setState({ dueType: 0 })
            }

            if(name === "dateAt"){
                helper.setDueAt(this, dueType, e);
            }
        }

        this.setState({ [name]: e !== null ? e : "" })
    }

    handleSelectItem = (item) => {
        const { items } = this.props;

        let nItem = null;

        if(item){
            items.forEach(it => {
                if(it.id === item.value){
                    nItem = it;
                }
            })
        }

        this.setState({ element: nItem })
    }

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
                toName: Formulaire.setValueEmptyIfNull(nCustomer.name),
                toAddress: Formulaire.setValueEmptyIfNull(nCustomer.address),
                toAddress2: Formulaire.setValueEmptyIfNull(nCustomer.address2),
                toComplement: Formulaire.setValueEmptyIfNull(nCustomer.complement),
                toZipcode: Formulaire.setValueEmptyIfNull(nCustomer.zipcode),
                toCity: Formulaire.setValueEmptyIfNull(nCustomer.city),
                toCountry: Formulaire.setValueEmptyIfNull(nCustomer.country),
                toEmail: Formulaire.setValueEmptyIfNull(nCustomer.email),
                toPhone1: Formulaire.setValueEmptyIfNull(nCustomer.phone),
            })
        }
    }

    handleChangeItems = (item) => {
        const { products, totalRemise } = this.state;

        let nProducts = [];
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

        let totalHt = 0,
            totalTva = 0,
            totalTtc = 0;

        nProducts.forEach(pr => {
            totalHt += pr.quantity * pr.price;
            totalTva += (pr.quantity * pr.price) * (pr.rateTva/100)
        })

        totalTtc = totalHt + totalTva - totalRemise

        this.setState({ products: nProducts, totalHt: totalHt, totalTva: totalTva, totalTtc: totalTtc })
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess, dateInvoice } = this.props;
        const { dateAt, dueType, dueAt, toName, toAddress, toAddress2, toComplement, toZipcode, toCity, toCountry } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: 'dateAt',      value: dateAt},
            {type: "text", id: 'toName',      value: toName},
            {type: "text", id: 'toAddress',   value: toAddress},
            {type: "text", id: 'toZipcode',   value: toZipcode},
            {type: "text", id: 'toCity',      value: toCity},
            {type: "text", id: 'toCountry',   value: toCountry},
            {type: "length", id: 'toAddress',   value: toAddress, min: 0, max: 40},
        ];

        paramsToValidate = helper.validateDates(paramsToValidate, dateInvoice, dateAt, dueAt, dueType);
        paramsToValidate = helper.checkLength(paramsToValidate, "toAddress2", toAddress2);
        paramsToValidate = helper.checkLength(paramsToValidate, "toComplement", toComplement);

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            arrayZipcodes = this.state.arrayPostalCode;
            delete this.state.arrayPostalCode;

            axios({ method: method, url: url, data: this.state })
                .then(function (response) {
                    let data = response.data;
                    self.setState({ success: messageSuccess, errors: [] });
                    toastr.info(messageSuccess);

                    setTimeout(() => {
                        location.reload()
                    }, 2000)
                })
                .catch(function (error) {
                    Formulaire.loader(false);
                    Formulaire.displayErrors(self, error);
                })
            ;
        }
    }

    render () {
        const { context, society, dateInvoice, items, taxes, unities, customers } = this.props;
        const { edit, element, errors, success, dateAt, dueAt, dueType,
            toName, toAddress, toAddress2, toComplement, toZipcode, toCity, toCountry, toEmail, toPhone1,
            note, footer, item, products, totalHt, totalRemise, totalTva, totalTtc, customer } = this.state;

        let tvas = [];
        products.forEach(pr => {
            let quantity = pr.quantity;
            let price = pr.price;
            let codeTva = pr.codeTva;
            let rateTva = pr.rateTva;

            if(quantity && quantity !== "" && price && price !== "" && rateTva && rateTva !== ""){
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

        let blocError1 = false, blocError2 = false;
        errors.forEach(err => {
            if(err.name === "dateAt" || err.name === "dueAt"){
                blocError1 = true;
            }

            if(err.name === "toName" || err.name === "toAddress" || err.name === "toZipcode" || err.name === "toCity" || err.name === "toCountry"){
                blocError2 = true;
            }
        })

        return <>

            {dateInvoice && <div className="line line-infos-invoice">
                <div className="form-group">
                    <Alert type="danger" withIcon={false}>
                        <label>Dernière date de facturation</label>
                        <br/>
                        <span>La date de facture doit être supérieur ou égale à <b>{new Date(dateInvoice).toLocaleDateString()}</b> </span>
                        <br/>
                        <span>Le prochain numéro de facture est : <b>{society.counterInvoice + 1}</b></span>
                    </Alert>
                </div>
            </div>}

            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="invoice-line invoice-line-1">
                    <div className="col-1">
                        {society.logo && <div className="image">
                            <img src={society.logoFile} alt="Logo entreprise"/>
                        </div>}
                        <div className="infos">
                            {society.siren && <div>
                                <b>n° Siren :</b> <span>{society.siren}</span>
                            </div>}
                            {society.numeroTva && <div>
                                <b>n° TVA UE :</b> <span>{society.numeroTva}</span>
                            </div>}
                            <div><b>Email :</b> <span>{society.email}</span> </div>
                            <div><b>Téléphone :</b> <span>{society.phone1}</span> </div>
                        </div>
                    </div>
                    <div className="col-2">
                        <BlocEdit step={1} edit={edit} onClickEdit={this.handleClickEdit} haveError={blocError1}
                                  view={<View1 dateAt={dateAt} dueAt={dueAt} />}
                                  form={<Form1 errors={errors} onChange={this.handleChange} onChangeDate={this.handleChangeDate}
                                               dateInvoice={dateInvoice} dateAt={dateAt} dueAt={dueAt} dueType={dueType} />}/>
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
                                               toName={toName} toAddress={toAddress} toComplement={toComplement} toAddress2={toAddress2}
                                               toZipcode={toZipcode} toCity={toCity} toCountry={toCountry}
                                               toEmail={toEmail} toPhone1={toPhone1} />}/>
                    </div>
                </div>

                <div className="invoice-line invoice-line-3">
                    <Products step={3} edit={edit} onClickEdit={this.handleClickEdit}
                              products={products} onChangeItems={this.handleChangeItems}
                              onSelectItem={this.handleSelectItem} item={item} items={items}
                              element={element} societyId={society.id} taxes={taxes} unities={unities} key={i++}
                    />
                </div>

                <div className="invoice-line invoice-line-4">
                    <div className="col-1">
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
                                                        <div className="col-1">{tva.code}</div>
                                                        <div className="col-2">{tva.base}</div>
                                                        <div className="col-3">{tva.rate}</div>
                                                        <div className="col-4">{tva.total}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    })}
                                </div>
                            </div>
                        </div>
                        <div className="note">
                            <div className="line">
                                <TextArea valeur={note} identifiant="note" errors={errors} onChange={this.handleChange}>Note</TextArea>
                            </div>
                        </div>
                    </div>
                    <div className="col-2">
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
                    </div>
                </div>

                <div className="invoice-line invoice-line-5">
                    <div className="bank">
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