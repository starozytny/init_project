import React, { Component } from 'react';

import axios                   from "axios";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import {Input, Checkbox, Radiobox, TextArea} from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@dashboardComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

export function EstimationFormulaire ({ type, onChangeContext, onUpdateList })
{
    let title = "Ajouter une estimation";
    let url = Routing.generate('api_immo_estimations_create');
    let msg = "Félicitation ! Vous avez ajouté une estimation !"

    let form = <EstimationForm
        context={type}
        url={url}
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class EstimationForm extends Component {
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
            constructionYear: "",
            etat: "",
            area: "",
            areaLand: "",
            nbPiece: "",
            nbRoom: "",
            nbParking: "",
            ext: [],
            infos: "",
            errors: [],
            success: false
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        document.getElementById("zipcode").focus()
    }

    handleChange = (e) => {
        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        // if(name === "typeBiens"){
        //     value = (e.currentTarget.checked) ? [...typeBiens, ...[value]] : typeBiens.filter(v => v !== value)
        // }
        this.setState({[name]: value})
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { zipcode, city, lastname, firstname, phone, typeAd, typeBien,
            constructionYear, etat, area, nbPiece, nbParking, ext} = this.state;

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
            {type: "text", id: 'constructionYear', value: constructionYear},
            {type: "text", id: 'etat', value: etat},
            {type: "text", id: 'area', value: area},
            {type: "text", id: 'nbPiece', value: nbPiece},
            {type: "text", id: 'nbParking', value: nbParking},
            {type: "array", id: 'ext', value: ext},
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
                        self.setState( {
                            email: '',
                            typeAd: '',
                            typeBien: ''
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
                constructionYear, etat, area, areaLand, nbPiece, nbRoom, nbParking, ext, infos } = this.state;

        let naturesItems = [
            { value: 0, label: 'Location', identifiant: 'location' },
            { value: 1, label: 'Vente', identifiant: 'vente' },
        ]

        let biensItems = [
            { value: 0, label: "Maison", identifiant: "maison" },
            { value: 1, label: "Appartement", identifiant: "appartement" },
            { value: 2, label: "Parking", identifiant: "parking" },
            { value: 3, label: "Bureaux", identifiant: "bureaux" },
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

        let extItems = [
            { value: "aucun", label: "Aucun", identifiant: "aucun" },
            { value: "balcon", label: "balcon", identifiant: "balcon" },
            { value: "jardin", label: "jardin", identifiant: "jardin" },
            { value: "terrasse", label: "terrasse", identifiant: "terrasse" }
        ]

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="step-1">
                    <div className="line line-2">
                        <Input valeur={zipcode} identifiant="zipcode" errors={errors} onChange={this.handleChange}>Code postal</Input>
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
                    <div className="line line-2">
                        <Input valeur={constructionYear} identifiant="constructionYear" errors={errors} onChange={this.handleChange} placeholder={"Année ou fourchette d'année"}>Année de construction</Input>
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
                    <div className="line line-2">
                        <Input valeur={nbParking} identifiant="nbParking" errors={errors} onChange={this.handleChange} type="number" >Nombre de parking</Input>
                        <Checkbox items={extItems} identifiant="ext" valeur={ext} errors={errors} onChange={this.handleChange}>Extérieurs</Checkbox>
                    </div>

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
                        <Button isSubmit={true}>{context === "create" ? "Ajouter une estimation" : 'Modifier l\'estimation'}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}