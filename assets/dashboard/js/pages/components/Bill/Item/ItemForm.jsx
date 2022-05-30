import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import { uid }                 from "uid";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, SelectReactSelectize, TextArea } from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Drop }                from "@dashboardComponents/Tools/Drop";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import Sanitaze                from "@commonComponents/functions/sanitaze";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_CREATE_ELEMENT     = "api_bill_items_create";
const URL_UPDATE_GROUP       = "api_bill_items_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let i = 0;

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

        uid={element ? element.uid : uid()}
        image={element ? element.imageFile : ""}
        reference={element ? Formulaire.setValueEmptyIfNull(element.reference) : ""}
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
        key={i++}
    />

    return <>{onChangeContext ? <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout> : form}</>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            societyId: props.societyId,
            uid: props.uid,
            image: props.image,
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

        this.inputImage = React.createRef();

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

        let inputImage = this.inputImage.current;
        let image = inputImage ? inputImage.drop.current.files : [];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            let formData = new FormData();
            if(image[0]){
                formData.append('image', image[0].file);
            }

            formData.append("data", JSON.stringify(this.state));

            axios({ method: "POST", url: url, data: formData, headers: {'Content-Type': 'multipart/form-data'} })
                .then(function (response) {
                    let data = response.data;

                    Helper.toTop();
                    if(self.props.onUpdateList){
                        self.props.onUpdateList(data);
                    }
                    toastr.info(messageSuccess);
                    self.setState({ success: messageSuccess, errors: [] });
                    if(context === "create"){
                        self.setState( {
                            reference: "",
                            image: "",
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

        let [selectTvas, selectUnities] = helper.getTaxesAndUnitiesSelectItems(taxes, unities);

        return <ClassiqueForm {...this.state} refImage={this.inputImage}
                              context={context} selectTvas={selectTvas} selectUnities={selectUnities}
                              onChange={this.handleChange} onChangeCleave={this.handleChangeCleave}
                              onChangeSelect={this.handleChangeSelect} onSubmit={this.handleSubmit} />
    }
}

function ClassiqueForm (props) {
    const { refImage, context, errors, success, onSubmit, onChange, onChangeCleave, onChangeSelect, selectTvas, selectUnities,
        reference, numero, name, content, unity, price, codeTva, rateTva, image } = props;

    return <>
        <form onSubmit={onSubmit}>

            {success !== false && <Alert type="info">{success}</Alert>}

            <div className="line line-2">
                <Input valeur={reference} identifiant="reference" errors={errors} onChange={onChange}>Référence (max 10 caractères)</Input>
                <Input valeur={numero} identifiant="numero" errors={errors} onChange={onChange}>Numéro comptable</Input>
            </div>

            <div className="line">
                <div className="form-group">
                    <div className="line-separator">
                        <div className="title">Détails</div>
                    </div>

                    <div className="line line-2">
                        <div className="form-group">
                            <div className="line">
                                <Input valeur={name} identifiant="name" errors={errors} onChange={onChange}>* Désignation</Input>
                            </div>

                            <div className="line line-3">
                                <SelectReactSelectize items={selectUnities} identifiant="unity"
                                                      valeur={unity} errors={errors} onChange={(e) => onChangeSelect('unity', e)}>
                                    Unité
                                </SelectReactSelectize>
                                <div className="form-group" />
                                <div className="form-group" />
                            </div>

                            <div className="line line-3">
                                <Input type="cleave" valeur={price} identifiant="price" errors={errors} onChange={onChangeCleave}>Prix unitaire</Input>
                                <SelectReactSelectize items={selectTvas} identifiant="codeTva"
                                                      valeur={codeTva} errors={errors} onChange={(e) => onChangeSelect('codeTva', e)}>
                                    Taux de TVA
                                </SelectReactSelectize>
                                <div className="form-group">
                                    <label>Total TTC</label>
                                    <div>{price !== "" && rateTva !== 0 ? Sanitaze.toFormatCurrency(parseFloat(price) * (rateTva/100) + parseFloat(price)) : ""}</div>
                                </div>
                            </div>
                        </div>

                        <div className="form-group">
                            <div className="line">
                                <TextArea valeur={content} identifiant="content" errors={errors} onChange={onChange}>Description</TextArea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/*<div className="line line-2">*/}
            {/*    <div className="form-group" />*/}
            {/*    <Drop ref={refImage} identifiant="logo" previewFile={image} errors={errors} accept={"image/*"} maxFiles={1}*/}
            {/*          label="Téléverser une image" labelError="Seules les images sont acceptées.">Image illustration</Drop>*/}
            {/*</div>*/}

            <div className="line">
                <div className="form-button">
                    <Button isSubmit={true}>{context === "create" ? TXT_CREATE_BUTTON_FORM : TXT_UPDATE_BUTTON_FORM}</Button>
                </div>
            </div>
        </form>
    </>
}