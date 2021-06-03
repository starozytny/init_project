import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, Checkbox, Radiobox, TextArea } from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@dashboardComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";
import Sanitaze                from "@dashboardComponents/functions/sanitaze";

export function DevisFormulaire ({ type, onChangeContext, onUpdateList })
{
    let title = "Ajouter un devis";
    let url = Routing.generate('api_immo_devis_create');
    let msg = "Félicitation ! Vous avez ajouté un devis !"

    let form = <DevisForm
        context={type}
        url={url}
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class DevisForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            zipcode: "",
            city: "",
            lastname: "",
            firstname: "",
            email: "",
            phone: "",
            typeAd: "",
            typeBien: "",
            etat: "",
            area: "",
            areaLand: "",
            nbPiece: "",
            nbRoom: "",
            infos: "",
            errors: [],
            success: false,
            arrayPostalCode: [],
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeZipcode = this.handleChangeZipcode.bind(this);
    }

    componentDidMount() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        document.getElementById("zipcode").focus();

        Sanitaze.getPostalCodes(this);
    }

    handleChangeZipcode = (e) => {
        const { arrayPostalCode } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(value.length <= 5){
            this.setState({ [name]: value })

            let v = ""
            if(arrayPostalCode.length !== 0){
                v = arrayPostalCode.filter(el => el.cp === value)

                if(v.length === 1){
                    this.setState({ city: v[0].city })
                }
            }
        }
    }

    handleChange = (e) => {
        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        this.setState({[name]: value})
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { zipcode, city, lastname, firstname, phone, typeAd, typeBien, etat, area, nbPiece} = this.state;

        this.setState({ success: false})

        let method = "POST";
        let paramsToValidate = [
            {type: "text", id: 'zipcode', value: zipcode},
            {type: "text", id: 'city', value: city},
            {type: "text", id: 'lastname', value: lastname},
            {type: "text", id: 'firstname', value: firstname},
            {type: "text", id: 'phone', value: phone},
            {type: "text", id: 'typeAd', value: typeAd},
            {type: "text", id: 'typeBien', value: typeBien},
            {type: "text", id: 'etat', value: etat},
            {type: "text", id: 'area', value: area},
            {type: "text", id: 'nbPiece', value: nbPiece},
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            this.setState({ errors: validate.errors });
        }else{
            Formulaire.loader(true);
            let self = this;
            axios({ method: method, url: url, data: self.state })
                .then(function (response) {
                    let data = response.data;
                    self.props.onUpdateList(data);
                    self.setState({ success: messageSuccess, errors: [] });
                    if(context === "create"){
                        document.body.scrollTop = 0; // For Safari
                        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera

                        toastr.info(messageSuccess);
                        self.setState( {
                            zipcode: "",
                            city: "",
                            lastname: "",
                            firstname: "",
                            email: "",
                            phone: "",
                            typeAd: "",
                            typeBien: "",
                            etat: "",
                            area: "",
                            areaLand: "",
                            nbPiece: "",
                            nbRoom: "",
                            infos: "",
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
        const { errors, success, zipcode, city, lastname, firstname, email, phone, typeAd, typeBien,
                etat, area, areaLand, nbPiece, nbRoom, infos } = this.state;

        let naturesItems = [
            { value: 0, label: 'Location', identifiant: 'location' },
            { value: 1, label: 'Vente', identifiant: 'vente' },
        ]

        let biensItems = [
            { value: 0, label: "Maison", identifiant: "maison" },
            { value: 1, label: "Appartement", identifiant: "appartement" },
            { value: 2, label: "Parking", identifiant: "parking" },
            { value: 3, label: "Bureau", identifiant: "bureau" },
            { value: 4, label: "Local", identifiant: "local" },
            { value: 5, label: "Immeuble", identifiant: "immeuble" },
            { value: 6, label: "Terrain", identifiant: "terrain" },
            { value: 7, label: "Commerce", identifiant: "commerce" },
        ]

        let etatItems =[
            { value: 0, label: "Neuf", identifiant: "neuf" },
            { value: 1, label: "Rénové", identifiant: "renove" },
            { value: 2, label: "Moyen", identifiant: "moyen" },
            { value: 3, label: "Bon", identifiant: "bon" },
            { value: 4, label: "Travaux à prévoir", identifiant: "travaux" },
        ]

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="step-1">
                    <div className="line line-2">
                        <Input valeur={zipcode} identifiant="zipcode" errors={errors} onChange={this.handleChangeZipcode}>Code postal</Input>
                        <Input valeur={city} identifiant="city" errors={errors} onChange={this.handleChange}>Ville</Input>
                    </div>

                    <div className="line">
                        <Radiobox items={naturesItems} identifiant="typeAd" valeur={typeAd} errors={errors} onChange={this.handleChange}>Quel est votre projet ?</Radiobox>
                    </div>
                    <div className="line">
                        <Radiobox items={biensItems} identifiant="typeBien" valeur={typeBien} errors={errors} onChange={this.handleChange}>A quel état se trouve le bien ?</Radiobox>
                    </div>
                </div>

                <div className="step-2">
                    <div className="line">
                        <Radiobox items={etatItems} identifiant="etat" valeur={etat} errors={errors} onChange={this.handleChange}>De quel bien s'agit-il ?</Radiobox>
                    </div>

                    <div className="line line-2">
                        <Input valeur={area} identifiant="area" errors={errors} onChange={this.handleChange} type="number" >Surface</Input>
                        <Input valeur={areaLand} identifiant="areaLand" errors={errors} onChange={this.handleChange} type="number" >Surface du terrain (facultatif)</Input>
                    </div>

                    <div className="line line-2">
                        <Input valeur={nbPiece} identifiant="nbPiece" errors={errors} onChange={this.handleChange} type="float" >Nombre de pièce</Input>
                        <Input valeur={nbRoom} identifiant="nbRoom" errors={errors} onChange={this.handleChange} type="float" >Nombre de chambres (facultatif)</Input>
                    </div>
                </div>

                <div className="step-3">
                    <div className="line">
                        <TextArea valeur={infos} identifiant="infos" errors={errors} onChange={this.handleChange} placeholder="Etages, exposition, autres avantages et autres détails.">
                            Informations supplémentaires (facultatif)
                        </TextArea>
                    </div>
                </div>

                <div className="step-4">
                    <div className="line line-2">
                        <Input valeur={lastname} identifiant="lastname" errors={errors} onChange={this.handleChange}>Nom</Input>
                        <Input valeur={firstname} identifiant="firstname" errors={errors} onChange={this.handleChange}>Prénom</Input>
                    </div>

                    <div className="line line-2">
                        <Input valeur={phone} identifiant="phone" errors={errors} onChange={this.handleChange}>Téléphone</Input>
                        <Input valeur={email} identifiant="email" errors={errors} onChange={this.handleChange} type="email" >Adresse e-mail (facultatif)</Input>
                    </div>
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? "Ajouter un devis" : 'Modifier le devis'}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}