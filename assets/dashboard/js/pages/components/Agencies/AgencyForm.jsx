import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input }               from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { Trumb }               from "@dashboardComponents/Tools/Trumb";
import { Drop }                from "@dashboardComponents/Tools/Drop";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@dashboardComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

export function AgencyFormulaire ({ type, onChangeContext, onUpdateList, element })
{
    let title = "Ajouter une demande";
    let url = Routing.generate('api_immo_agency_create');
    let msg = "Félicitation ! Vous avez ajouté une agence !"

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate('api_immo_agency_update', {'id': element.id});
        msg = "Félicitation ! La mise à jour s'est réalisé avec succès !";
    }

    let form = <AgencyForm
        context={type}
        url={url}
        name={element ? element.name : ""}
        dirname={element ? element.dirname : ""}
        website={element ? element.website : ""}
        email={element ? element.email : ""}
        emailLocation={element ? element.emailLocation : ""}
        emailVente={element ? element.emailVente : ""}
        phone={element ? element.phone : ""}
        phoneLocation={element ? element.phoneLocation : ""}
        phoneVente={element ? element.phoneVente : ""}
        address={element ? element.address : ""}
        zipcode={element ? element.zipcode : ""}
        city={element ? element.city : ""}
        lat={element ? element.lat : ""}
        lon={element ? element.lon : ""}
        description={element ? element.description : ""}
        legal={element ? element.legal : ""}
        logo={element ? element.logo : ""}
        tarif={element ? element.tarif : ""}
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class AgencyForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: props.name,
            dirname: props.dirname,
            website: props.website,
            email: props.email,
            emailLocation: props.emailLocation,
            emailVente: props.emailVente,
            phone: props.phone,
            phoneLocation: props.phoneLocation,
            phoneVente: props.phoneVente,
            address: props.address,
            zipcode: props.zipcode,
            city: props.city,
            lat: props.lat,
            lon: props.lon,
            description: { value: props.description ? props.description : "", html: props.description ? props.description : "" },
            legal: { value: props.legal ? props.legal : "", html: props.legal ? props.legal : "" },
            logo: props.logo,
            tarif: props.tarif,
            errors: [],
            success: false,
            critere: ""
        }

        this.inputLogo = React.createRef();
        this.inputTarif = React.createRef();

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeTrumb = this.handleChangeTrumb.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        document.getElementById("name").focus()
    }

    handleChange = (e) => { this.setState({[e.currentTarget.name]: e.currentTarget.value}) }

    handleChangeTrumb = (e) => {
        const { description, legal } = this.state

        let name = e.currentTarget.id;
        let text = e.currentTarget.innerHTML;
        let value = "";
        if(name === "description"){
            value = description.value;
        }else if(name === "legal"){
            value = legal.value;
        }

        this.setState({[name]: {value: value, html: text}})
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { critere, name, dirname, website, email, emailLocation, emailVente,
            phone, phoneLocation, phoneVente, address, zipcode, city, lat, lon,
            description, legal, logo, tarif
        } = this.state;

        if(critere !== ""){
            toastr.error("Veuillez rafraichir la page.");
        }else{
            this.setState({ success: false});

            let paramsToValidate = [
                {type: "text", id: 'name', value: name},
                {type: "text", id: 'dirname', value: dirname},
                {type: "text", id: 'website', value: website},
                {type: "text", id: 'email', value: email},
                {type: "text", id: 'emailLocation', value: emailLocation},
                {type: "text", id: 'emailVente', value: emailVente},
                {type: "text", id: 'phone', value: phone},
                {type: "text", id: 'phoneLocation', value: phoneLocation},
                {type: "text", id: 'phoneVente', value: phoneVente},
                {type: "text", id: 'address', value: address},
                {type: "text", id: 'zipcode', value: zipcode},
                {type: "text", id: 'city', value: city},
                {type: "text", id: 'lat', value: lat},
                {type: "text", id: 'lon', value: lon},
                {type: "text", id: 'description', value: description.html},
                {type: "text", id: 'legal', value: legal.html},
            ];

            let dropLogo = this.inputLogo.current.drop.current.files;
            let dropTarif = this.inputTarif.current.drop.current.files;

            if(context === "create"){
                paramsToValidate = [...paramsToValidate, ...[{type: "array", id: 'logo', value: dropLogo}]];
                paramsToValidate = [...paramsToValidate, ...[{type: "array", id: 'tarif', value: dropTarif}]];
            }

            // validate global
            let validate = Validateur.validateur(paramsToValidate)
            if(!validate.code){
                this.setState({ errors: validate.errors });
            }else{
                Formulaire.loader(true);
                let self = this;

                let formData = new FormData();
                if(dropLogo[0]){
                    formData.append('logo', dropLogo[0].file);
                }
                if(dropTarif[0]){
                    formData.append('tarif', dropTarif[0].file);
                }

                formData.append("data", JSON.stringify(this.state));

                axios({ method: "POST", url: url, data: formData, headers: {'Content-Type': 'multipart/form-data'} })
                    .then(function (response) {
                        let data = response.data;
                        self.props.onUpdateList(data);
                        self.setState({ success: messageSuccess, errors: [] });
                        if(context === "create"){
                            document.body.scrollTop = 0; // For Safari
                            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera

                            toastr.info(messageSuccess);
                            self.setState( {
                                name: "",
                                dirname: "",
                                website: "",
                                email: "",
                                emailLocation: "",
                                emailVente: "",
                                phone: "",
                                phoneLocation: "",
                                phoneVente: "",
                                address: "",
                                zipcode: "",
                                city: "",
                                lat: "",
                                lon: "",
                                description: { value: "", html: "" },
                                legal: { value: "", html: "" },
                                logo: "",
                                tarif: ""
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
    }

    render () {
        const { context } = this.props;
        const { critere, errors, success, name, dirname, website, email, emailLocation, emailVente,
                phone, phoneLocation, phoneVente, address, zipcode, city, lat, lon, description, legal, logo, tarif } = this.state;

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line line-3">
                    <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>Nom / raison sociale</Input>
                    <Input valeur={dirname} identifiant="dirname" errors={errors} onChange={this.handleChange}>Nom du ZIP</Input>
                    <Input valeur={website} identifiant="website" errors={errors} onChange={this.handleChange}>Adresse URL</Input>
                </div>

                <div className="line line-3">
                    <Input valeur={email} identifiant="email" errors={errors} onChange={this.handleChange} type="email" >Adresse e-mail</Input>
                    <Input valeur={emailLocation} identifiant="emailLocation" errors={errors} onChange={this.handleChange} type="email" >Adresse e-mail location</Input>
                    <Input valeur={emailVente} identifiant="emailVente" errors={errors} onChange={this.handleChange} type="email" >Adresse e-mail vente</Input>
                </div>

                <div className="line line-3">
                    <Input valeur={phone} identifiant="phone" errors={errors} onChange={this.handleChange}>Téléphone</Input>
                    <Input valeur={phoneLocation} identifiant="phoneLocation" errors={errors} onChange={this.handleChange}>Téléphone location</Input>
                    <Input valeur={phoneVente} identifiant="phoneVente" errors={errors} onChange={this.handleChange}>Téléphone vente</Input>
                </div>

                <div className="line line-critere">
                    <Input identifiant="critere" valeur={critere} errors={errors} onChange={this.handleChange}>Critère</Input>
                </div>

                <div className="line line-2">
                    <Drop ref={this.inputLogo} identifiant="logo" file={logo} folder="immo/logos" errors={errors} accept={"image/*"} maxFiles={1}
                          label="Téléverser un logo" labelError="Seules les images sont acceptées.">Logo</Drop>
                    <Drop ref={this.inputTarif} identifiant="tarif" file={tarif} folder="immo/tarifs" errors={errors} accept={"application/pdf"} maxFiles={1}
                          label="Téléverser un PDF Tarif" labelError="Seules les PDFs sont acceptées.">Tarif</Drop>
                </div>

                <div className="line line-2">
                    <Trumb identifiant="description" valeur={description.value} errors={errors} onChange={this.handleChangeTrumb}>Description</Trumb>
                    <Trumb identifiant="legal" valeur={legal.value} errors={errors} onChange={this.handleChangeTrumb}>Légal</Trumb>
                </div>

                <div className="line line-3">
                    <Input valeur={address} identifiant="address" errors={errors} onChange={this.handleChange}>Adresse</Input>
                    <Input valeur={zipcode} identifiant="zipcode" errors={errors} onChange={this.handleChange}>Code postal</Input>
                    <Input valeur={city} identifiant="city" errors={errors} onChange={this.handleChange}>Ville</Input>
                </div>

                <div className="line line-2">
                    <Input valeur={lat} identifiant="lat" errors={errors} onChange={this.handleChange} type="number">Latitude</Input>
                    <Input valeur={lon} identifiant="lon" errors={errors} onChange={this.handleChange} type="number">Longitude</Input>
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? "Ajouter une agence" : 'Modifier l\' agence'}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}