import React, { Component } from 'react';

import axios                   from "axios";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, Radiobox }     from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Drop }                from "@dashboardComponents/Tools/Drop";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_CREATE_ELEMENT     = "api_societies_create";
const URL_UPDATE_GROUP       = "api_societies_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let arrayZipcodes = [];
let arrayBicSave = [];

export function SocietyFormulaire ({ type, onChangeContext, onUpdateList, element })
{
    let title = "Ajouter une société";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté une nouvelle société !"

    if(type === "update" || type === "profil"){
        title = "Modifier " + element.name;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
    }

    let form = <Form
        context={type}
        url={url}
        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        logo={element ? element.logoFile : ""}
        siren={element ? Formulaire.setValueEmptyIfNull(element.siren) : ""}
        siret={element ? Formulaire.setValueEmptyIfNull(element.siret) : ""}
        rcs={element ? Formulaire.setValueEmptyIfNull(element.rcs) : ""}
        numeroTva={element ? Formulaire.setValueEmptyIfNull(element.numeroTva) : ""}
        forme={element ? Formulaire.setValueEmptyIfNull(element.forme, 0) : 0}
        address={element ? Formulaire.setValueEmptyIfNull(element.address) : ""}
        address2={element ? Formulaire.setValueEmptyIfNull(element.address2) : ""}
        email={element ? Formulaire.setValueEmptyIfNull(element.email) : ""}
        phone1={element ? Formulaire.setValueEmptyIfNull(element.phone1) : ""}
        zipcode={element ? Formulaire.setValueEmptyIfNull(element.zipcode) : ""}
        city={element ? Formulaire.setValueEmptyIfNull(element.city) : ""}
        complement={element ? Formulaire.setValueEmptyIfNull(element.complement) : ""}
        country={element ? Formulaire.setValueEmptyIfNull(element.country, "France") : "France"}
        bankName={element ? Formulaire.setValueEmptyIfNull(element.bankName) : ""}
        bankNumero={element ? Formulaire.setValueEmptyIfNull(element.bankNumero) : ""}
        bankTitulaire={element ? Formulaire.setValueEmptyIfNull(element.bankTitulaire) : ""}
        bankBic={element ? Formulaire.setValueEmptyIfNull(element.bankBic) : ""}
        bankCode={element ? Formulaire.setValueEmptyIfNull(element.bankCode) : ""}
        bankIban={element ? Formulaire.setValueEmptyIfNull(element.bankIban) : ""}
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: props.name,
            logo: props.logo,
            siren: props.siren,
            siret: props.siret,
            rcs: props.rcs,
            numeroTva: props.numeroTva,
            forme: props.forme,
            address: props.address,
            address2: props.address2,
            zipcode: props.zipcode,
            city: props.city,
            complement: props.complement,
            email: props.email,
            phone1: props.phone1,
            country: props.country,
            bankName: props.bankName,
            bankNumero: props.bankNumero,
            bankTitulaire: props.bankTitulaire,
            bankBic: props.bankBic,
            bankCode: props.bankCode,
            bankIban: props.bankIban,
            errors: [],
            success: false,
            arrayPostalCode: [],
            arrayBic: [],
        }

        this.inputLogo = React.createRef();

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeZipcodeCity = this.handleChangeZipcodeCity.bind(this);
    }

    componentDidMount() {
        Helper.getPostalCodes(this);
        Helper.getBicCodes(this);
    }

    handleChange = (e) => {
        const { arrayBic } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "bankIban"){
            Helper.setBicFromIban(this, value, arrayBic)
        }

        if(name === "bankIban" || name === "bankBic" || name === "bankTitulaire"){
            value = value.toUpperCase()
        }

        this.setState({[name]: value})
    }

    handleChangeZipcodeCity = (e) => {
        const { arrayPostalCode } = this.state;

        Helper.setCityFromZipcode(this, e, arrayPostalCode ? arrayPostalCode : arrayZipcodes)
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name, address, zipcode, city, country, email, phone1, bankName, bankNumero, bankTitulaire, bankBic, bankCode, bankIban } = this.state;

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text", id: 'name',              value: name},
            {type: "text", id: 'address',           value: address},
            {type: "text", id: 'zipcode',           value: zipcode},
            {type: "text", id: 'city',              value: city},
            {type: "text", id: 'email',             value: email},
            {type: "text", id: 'phone1',            value: phone1},
            {type: "text", id: 'country',           value: country},
            {type: "text", id: 'bankName',          value: bankName},
            {type: "text", id: 'bankNumero',        value: bankNumero},
            {type: "text", id: 'bankTitulaire',     value: bankTitulaire},
            {type: "text", id: 'bankBic',           value: bankBic},
            {type: "text", id: 'bankCode',          value: bankCode},
            {type: "text", id: 'bankIban',          value: bankIban},
        ];

        let inputLogo = this.inputLogo.current;
        let logo = inputLogo ? inputLogo.drop.current.files : [];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            let formData = new FormData();
            if(logo[0]){
                formData.append('logo', logo[0].file);
            }

            formData.append("data", JSON.stringify(this.state));

            arrayZipcodes = this.state.arrayPostalCode;
            arrayBicSave = this.state.arrayBic;
            delete this.state.arrayPostalCode;
            delete this.state.arrayBic;

            axios({ method: "POST", url: url, data: formData, headers: {'Content-Type': 'multipart/form-data'} })
                .then(function (response) {
                    let data = response.data;
                    Helper.toTop();
                    if(self.props.onUpdateList){
                        self.props.onUpdateList(data);
                    }
                    self.setState({ success: messageSuccess, errors: [] });
                    if(context === "create"){
                        self.setState( {
                            name: '',
                            siren: '',
                            siret: '',
                            rcs: '',
                            numeroTva: '',
                            forme: 0,
                            email: '',
                            phone1: '',
                            address: '',
                            address2: '',
                            zipcode: '',
                            city: '',
                            complement: '',
                            country: 'France',
                            bankName: '',
                            bankNumero: '',
                            bankTitulaire: '',
                            bankBic: '',
                            bankCode: '',
                            bankIban: '',
                        })
                    }
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
        const { context } = this.props;
        const { errors, success, name, logo, siren, siret, rcs, numeroTva, forme, email, phone1,
            address, address2, zipcode, city, complement, country, bankName, bankNumero, bankTitulaire, bankBic, bankCode, bankIban } = this.state;

        let formItems = [
            { value: 0, label: "EURL", identifiant: "eurl" },
            { value: 1, label: "SARL", identifiant: "sarl" },
            { value: 2, label: "SA",   identifiant: "sa" },
            { value: 3, label: "SNC",  identifiant: "SNC" },
            { value: 4, label: "SAS",  identifiant: "sas" },
        ]

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line line-2">
                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Informations générales</div>
                        </div>

                        <div className="line">
                            <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>Raison sociale</Input>
                        </div>

                        <div className="line">
                            <Radiobox items={formItems} identifiant="forme" valeur={forme} errors={errors} onChange={this.handleChange}>Forme juridique</Radiobox>
                        </div>

                        <div className="line line-2">
                            <Input valeur={email} identifiant="email" errors={errors} onChange={this.handleChange} type="email">Email</Input>
                            <Input valeur={phone1} identifiant="phone1" errors={errors} onChange={this.handleChange}>Téléphone</Input>
                        </div>

                        <div className="line-separator">
                            <div className="title">Details</div>
                        </div>

                        <div className="line line-2">
                            <Input valeur={siren} identifiant="siren" errors={errors} onChange={this.handleChange}>Siren</Input>
                            <Input valeur={siret} identifiant="siret" errors={errors} onChange={this.handleChange}>Siret</Input>
                        </div>
                        <div className="line line-2">
                            <Input valeur={rcs} identifiant="rcs" errors={errors} onChange={this.handleChange}>Rcs</Input>
                            <Input valeur={numeroTva} identifiant="numeroTva" errors={errors} onChange={this.handleChange}>Numéro TVA</Input>
                        </div>

                        <div className="line-separator">
                            <div className="title">Coordonnées bancaires</div>
                        </div>

                        <div className="line line-3">
                            <Input valeur={bankName} identifiant="bankName" errors={errors} onChange={this.handleChange}>Banque</Input>
                            <Input valeur={bankNumero} identifiant="bankNumero" errors={errors} onChange={this.handleChange}>N° du compte</Input>
                            <Input valeur={bankCode} identifiant="bankCode" errors={errors} onChange={this.handleChange}>Code banque</Input>
                        </div>
                        <div className="line">
                            <Input valeur={bankIban} identifiant="bankIban" errors={errors} onChange={this.handleChange}
                                   type="cleave" options={{
                                        blocks: [4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4],
                                        uppercase: true
                                    }}
                            >
                                Iban
                            </Input>
                        </div>
                        <div className="line line-2">
                            <Input valeur={bankTitulaire} identifiant="bankTitulaire" errors={errors} onChange={this.handleChange}>Titulaire du compte</Input>
                            <Input valeur={bankBic} identifiant="bankBic" errors={errors} onChange={this.handleChange}>BIC</Input>
                        </div>
                    </div>

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Localisation</div>
                        </div>

                        <div className="line">
                            <Input identifiant="address" valeur={address} errors={errors} onChange={this.handleChange}>Adresse</Input>
                        </div>
                        <div className="line">
                            <Input identifiant="address2" valeur={address2} errors={errors} onChange={this.handleChange}>Adresse suite</Input>
                        </div>
                        <div className="line">
                            <Input identifiant="complement" valeur={complement} errors={errors} onChange={this.handleChange}>Complément d'adresse</Input>
                        </div>
                        <div className="line line-2">
                            <Input identifiant="zipcode" valeur={zipcode} errors={errors} onChange={this.handleChangeZipcodeCity} type="number">Code postal</Input>
                            <Input identifiant="city" valeur={city} errors={errors} onChange={this.handleChange}>Ville</Input>
                        </div>
                        <div className="line line-2">
                            <Input identifiant="country" valeur={country} errors={errors} onChange={this.handleChange}>Pays</Input>
                            <div className="form-group" />
                        </div>

                        <div className="line-separator">
                            <div className="title">Logo entreprise</div>
                        </div>
                        <div className="line">
                            <div className="line">
                                <Drop ref={this.inputLogo} identifiant="logo" previewFile={logo} errors={errors} accept={"image/*"} maxFiles={1}
                                      label="Téléverser un logo" labelError="Seules les images sont acceptées." />
                            </div>
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
