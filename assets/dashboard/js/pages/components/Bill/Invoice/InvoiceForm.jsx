import React, { Component } from 'react';

import axios                   from "axios";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, Select, SelectReactSelectize, TextArea } from "@dashboardComponents/Tools/Fields";
import { Button, ButtonIcon }  from "@dashboardComponents/Tools/Button";
import { DatePick }            from "@dashboardComponents/Tools/DatePicker";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import Sanitaze                from "@commonComponents/functions/sanitaze";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

import { ItemInvoiceFormulaire } from "@dashboardPages/components/Bill/Invoice/ItemInvoiceForm";

const URL_CREATE_ELEMENT     = "api_bill_invoices_create";
const URL_UPDATE_GROUP       = "api_bill_invoices_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let arrayZipcodes = [];
let i = 0;

export function InvoiceFormulaire ({ type, onChangeContext, onUpdateList, element, society, items, taxes, unities, products })
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

    let nProducts = [];
    if(element){
        products.forEach(pr => {
            if(pr.identifiant === "FA-" + element.id){
                nProducts.push(pr);
            }
        })
    }

    let form = <Form
        context={type}
        url={url}

        items={items}
        taxes={taxes}
        unities={unities}

        society={society}
        dateInvoice={dateInvoice}

        dateAt={element ? Formulaire.setDateOrEmptyIfNull(element.dateAtJavascript) : Formulaire.setDateOrEmptyIfNull(dateInvoice)}
        dueAt={element ? Formulaire.setDateOrEmptyIfNull(element.dueAtJavascript) : dueAt}
        dueType={element ? Formulaire.setValueEmptyIfNull(element.dueType, 2) : 2}

        toName={element ? Formulaire.setValueEmptyIfNull(element.toName) : ""}
        toAddress={element ? Formulaire.setValueEmptyIfNull(element.toAddress) : ""}
        toComplement={element ? Formulaire.setValueEmptyIfNull(element.toComplement) : ""}
        toZipcode={element ? Formulaire.setValueEmptyIfNull(element.toZipcode) : ""}
        toCity={element ? Formulaire.setValueEmptyIfNull(element.toCity) : ""}
        toEmail={element ? Formulaire.setValueEmptyIfNull(element.toEmail) : ""}
        toPhone1={element ? Formulaire.setValueEmptyIfNull(element.toPhone1) : ""}

        products={nProducts}

        note={element ? Formulaire.setValueEmptyIfNull(element.note) : ""}
        footer={element ? Formulaire.setValueEmptyIfNull(element.footer) : ""}

        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
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
            toComplement: props.toComplement,
            toZipcode: props.toZipcode,
            toCity: props.toCity,
            toEmail: props.toEmail,
            toPhone1: props.toPhone1,
            note: props.note,
            footer: props.footer,
            products: props.products,
            totalHt: 0,
            totalRemise: 0,
            totalTva: 0,
            totalTtc: 0,
            errors: [],
            success: false,
            element: null,
            item: ""
        }

        this.asideSelect = React.createRef();
        this.asideAdd = React.createRef();

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeZipcodeCity = this.handleChangeZipcodeCity.bind(this);
        this.handleChangeDate = this.handleChangeDate.bind(this);

        this.handleSelectItem = this.handleSelectItem.bind(this);
        this.handleChangeItems = this.handleChangeItems.bind(this);
    }

    componentDidMount() {
        Helper.getPostalCodes(this);
    }

    handleChange = (e) => {
        const { dateAt } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "dueType"){
            let val = parseInt(value);
            if(val === 1){
                this.setState({ dueAt: "" })
            }else{
                if(val !== 0 && dateAt !== ""){
                    let dueAt = new Date(dateAt);
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

                    this.setState({ dueAt: dueAt })
                }
            }
        }

        this.setState({[name]: value})
    }

    handleChangeZipcodeCity = (e) => {
        const { arrayPostalCode } = this.state;

        Helper.setCityFromZipcode(this, e, arrayPostalCode ? arrayPostalCode : arrayZipcodes, "toCity")
    }

    handleChangeDate = (name, e) => {
        if(name === "dueAt"){
            this.setState({dueType: 0 })
        }

        this.setState({ [name]: e !== null ? e : "" })
    }

    handleSelectItem = (item) => {
        const { items } = this.props;

        let nItem = null;
        items.forEach(it => {
            if(it.id === item.value){
                nItem = it;
            }
        })

        this.setState({ element: nItem })
    }

    handleChangeItems = (item) => {
        const { products } = this.state;

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

        this.setState({ products: nProducts })
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { dateAt, toName, toAddress, toZipcode, toCity } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: 'dateAt',      value: dateAt},
            {type: "text", id: 'toName',      value: toName},
            {type: "text", id: 'toAddress',   value: toAddress},
            {type: "text", id: 'toZipcode',   value: toZipcode},
            {type: "text", id: 'toCity',      value: toCity},
        ];

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
                    Helper.toTop();
                    if(self.props.onUpdateList){
                        self.props.onUpdateList(data);
                        self.props.onChangeContext("list");
                    }
                    self.setState({ success: messageSuccess, errors: [] });
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(() => {
                    Formulaire.loader(false);
                })
            ;
        }
    }

    render () {
        const { context, society, dateInvoice, items, taxes, unities } = this.props;
        const { element, errors, success, dateAt, dueAt, dueType,
            toName, toAddress, toComplement, toZipcode, toCity, toEmail, toPhone1,
            note, footer, item, products } = this.state;

        let selectDueTypes = [
            { value: 0, label: "Définir manuellement", identifiant: "c-0" },
            { value: 1, label: "Acquitté",             identifiant: "c-1" },
            { value: 2, label: "8 jours",              identifiant: "c-2" },
            { value: 3, label: "14 jours",             identifiant: "c-3" },
            { value: 4, label: "30 jours",             identifiant: "c-4" },
        ]

        let selectItems = [];
        items.forEach(it => {
            selectItems.push({ value: it.id, label: it.name + " : " + Sanitaze.toFormatCurrency(it.price), identifiant: "it-" + it.id })
        })

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Facture</div>
                        </div>
                    </div>

                    <div className="line line-2">
                        <DatePick identifiant="dateAt" valeur={dateAt} minDate={dateInvoice ? new Date(dateInvoice) : ""} errors={errors} onChange={(e) => this.handleChangeDate("dateAt", e)}>
                            Date de facture
                        </DatePick>
                        <div className="form-group" />
                    </div>

                    <div className="line line-2">
                        <Select items={selectDueTypes} identifiant="dueType" valeur={dueType} noEmpty={true} errors={errors} onChange={this.handleChange}>Conditions de paiement</Select>
                        {parseInt(dueType) !== 1 ? <>
                            <DatePick identifiant="dueAt" valeur={dueAt} minDate={dateAt ? dateAt : new Date()} errors={errors} onChange={(e) => this.handleChangeDate("dueAt", e)}>
                                Date d'échéance
                            </DatePick>
                        </> : <div className="form-group" />}
                    </div>
                </div>

                <div className="line">
                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Client</div>
                        </div>

                        <div className="line">
                            <Input valeur={toName} identifiant="toName" errors={errors} onChange={this.handleChange}>* Nom / Raison sociale</Input>
                        </div>

                        <div className="line line-2">
                            <Input valeur={toEmail} identifiant="toEmail" errors={errors} onChange={this.handleChange} type="email">Email</Input>
                            <Input valeur={toPhone1} identifiant="toPhone1" errors={errors} onChange={this.handleChange}>Téléphone</Input>
                        </div>

                        <div className="line">
                            <Input identifiant="toAddress" valeur={toAddress} errors={errors} onChange={this.handleChange}>* Adresse</Input>
                        </div>
                        <div className="line">
                            <Input identifiant="toComplement" valeur={toComplement} errors={errors} onChange={this.handleChange}>Complément d'adresse</Input>
                        </div>
                        <div className="line line-2">
                            <Input identifiant="toZipcode" valeur={toZipcode} errors={errors} onChange={this.handleChangeZipcodeCity} type="number">* Code postal</Input>
                            <Input identifiant="toCity" valeur={toCity} errors={errors} onChange={this.handleChange}>* Ville</Input>
                        </div>
                    </div>
                </div>

                <div className="line">
                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Article(s)</div>
                        </div>
                    </div>

                    <div className="line">
                        <SelectReactSelectize items={selectItems} identifiant="item" placeholder={"Sélectionner un article"}
                                              valeur={item} errors={errors} onChange={(e) => this.handleSelectItem(e)}>
                            Sélectionner un article
                        </SelectReactSelectize>
                    </div>

                    <div className="line">
                        <div className="form-group">
                            <div>OU</div>
                        </div>
                    </div>

                    <div className="line">
                        <ItemInvoiceFormulaire element={element} societyId={society.id} taxes={taxes} unities={unities} key={i++} onSubmit={this.handleChangeItems}/>
                    </div>

                    <div className="line line-products">
                        <div className="form-group">
                            <label>Liste des produits rattachés à la facture</label>
                            {products.map((pr, index) => {
                                return <div className="item" key={index}>
                                    <div className="col-1">{pr.name}</div>
                                    <div className="col-2">
                                        {pr.quantity !== "" ? <>
                                            <div>{pr.quantity} * {Sanitaze.toFormatCurrency(pr.price)}</div>
                                            {(pr.rateTva !== "" && parseFloat(pr.rateTva) !== 0) && <div className="sub">({pr.rateTva}%)</div>}
                                        </> : <div>Prix vide</div>}
                                    </div>
                                    <div className="col-3">
                                        <ButtonIcon icon="trash" onClick={() => this.handleChangeItems(pr)}>Supprimer</ButtonIcon>
                                    </div>
                                </div>
                            })}
                        </div>
                    </div>
                </div>

                <div className="line">
                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Informations de bas de page</div>
                        </div>

                        <div className="line">
                            <TextArea valeur={note} identifiant="note" errors={errors} onChange={this.handleChange}>Note</TextArea>
                        </div>
                        <div className="line">
                            <TextArea valeur={footer} identifiant="footer" errors={errors} onChange={this.handleChange}>Renseignements bancaires</TextArea>
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
