import React, { Component } from 'react';

import axios                   from "axios";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, SelectReactSelectize, TextArea } from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_CREATE_ELEMENT     = "api_bill_items_create";
const URL_UPDATE_GROUP       = "api_bill_items_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

export function ItemFormulaire ({ type, onChangeContext, onUpdateList, element, societyId, taxes, unities })
{
    let title = "Ajouter un article";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté un nouveau article !"

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
    }

    let form = <Form
        context={type}
        url={url}

        societyId={societyId}
        taxes={taxes}
        unities={unities}

        reference={element ? Formulaire.setValueEmptyIfNull(element.reference) : "ART"}
        numero={element ? Formulaire.setValueEmptyIfNull(element.numero) : ""}
        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        content={element ? Formulaire.setValueEmptyIfNull(element.content) : ""}
        unity={element ? Formulaire.setValueEmptyIfNull(element.unity) : "pièce"}
        price={element ? Formulaire.setToFloat(element.price) : ""}
        rateTva={element ? Formulaire.setValueEmptyIfNull(element.rateTva, 20) : 20}
        codeTva={element ? Formulaire.setValueEmptyIfNull(element.codeTva, 1) : 1}

        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <>{onChangeContext ? <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout> : form}</>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            societyId: props.societyId,
            reference: props.reference,
            numero: props.numero,
            name: props.name,
            content: props.content,
            unity: props.unity,
            price: props.price,
            rateTva: props.rateTva,
            codeTva: props.codeTva,
            errors: [],
            success: false
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeCleave = this.handleChangeCleave.bind(this);
        this.handleChangeSelect = this.handleChangeSelect.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => { this.setState({[e.currentTarget.name]: e.currentTarget.value}) }

    handleChangeCleave = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.rawValue }) }

    handleChangeSelect = (name, e) => {
        helper.setRateTva(this, this.props.taxes, name, e);
        this.setState({ [name]: e !== undefined ? e.value : "" })
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name, reference } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text", id: 'name', value: name}
        ];

        if(reference !== ""){
            paramsToValidate = [...paramsToValidate,
                ...[
                    {type: "text", id: 'reference', value: reference},
                    {type: "length", id: 'reference', value: reference, min: 0, max: 10}
                ]
            ];
        }

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            axios({ method: method, url: url, data: this.state })
                .then(function (response) {
                    let data = response.data;
                    Helper.toTop();
                    if(self.props.onUpdateList){
                        self.props.onUpdateList(data);
                    }
                    self.setState({ success: messageSuccess, errors: [] });
                    if(context === "create"){
                        self.setState( {
                            reference: "ART",
                            numero: "",
                            name: "",
                            content: "",
                            unity: "pièce",
                            price: "",
                            rateTva: 20,
                            codeTva: 1,
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
        const { context, taxes, unities } = this.props;
        const { errors, success, reference, numero, name, content, unity, price, codeTva } = this.state;

        let [selectTvas, selectUnities] = helper.getTaxesAndUnitiesSelectItems(taxes, unities);

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">

                    <div className="line line-2">
                        <Input valeur={reference} identifiant="reference" errors={errors} onChange={this.handleChange}>Référence (max 10 caractères)</Input>
                        <Input valeur={numero} identifiant="numero" errors={errors} onChange={this.handleChange}>Numéro comptable</Input>
                    </div>
                </div>

                <div className="line">

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Article</div>
                        </div>

                        <div className="line line-2">
                            <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>* Désignation</Input>
                            <SelectReactSelectize items={selectUnities} identifiant="unity"
                                                  valeur={unity} errors={errors} onChange={(e) => this.handleChangeSelect('unity', e)}>
                                Unité
                            </SelectReactSelectize>
                        </div>

                        <div className="line">
                            <TextArea valeur={content} identifiant="content" errors={errors} onChange={this.handleChange}>Description</TextArea>
                        </div>

                        <div className="line line-2">
                            <Input type="cleave" valeur={price} identifiant="price" errors={errors} onChange={this.handleChangeCleave}>Prix unitaire</Input>
                            <SelectReactSelectize items={selectTvas} identifiant="codeTva"
                                                  valeur={codeTva} errors={errors} onChange={(e) => this.handleChangeSelect('codeTva', e)}>
                                Taux de TVA
                            </SelectReactSelectize>
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
