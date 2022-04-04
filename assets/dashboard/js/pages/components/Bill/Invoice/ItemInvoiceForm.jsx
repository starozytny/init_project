import React, { Component } from 'react';

import axios                   from "axios"
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, SelectReactSelectize, TextArea } from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import Sanitaze                from "@commonComponents/functions/sanitaze";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const TXT_CREATE_BUTTON_FORM = "Enregistrer";

export function ItemInvoiceFormulaire ({ element, societyId, taxes, unities })
{
    let form = <Form
        societyId={societyId}
        taxes={taxes}
        unities={unities}

        reference={element ? Formulaire.setValueEmptyIfNull(element.reference) : "ART"}
        numero={element ? Formulaire.setValueEmptyIfNull(element.numero) : ""}
        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        content={element ? Formulaire.setValueEmptyIfNull(element.content) : ""}
        unity={element ? Formulaire.setValueEmptyIfNull(element.unity) : "pièce"}
        price={element ? Formulaire.setToFloat(element.price) : ""}
        quantity={element && element.quantity ? Formulaire.setToFloat(element.quantity, 1) : 1}
        totalHt={element && element.totalHt ? Formulaire.setToFloat(element.totalHt, 0) : 0}
        rateTva={element ? Formulaire.setValueEmptyIfNull(element.rateTva, 20) : 20}
    />

    return <>{form}</>
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
            quantity: props.quantity,
            totalHt: props.totalHt !== 0 ? props.totalHt : (props.quantity * (props.price !== "" ? parseFloat(props.price) : 0)),
            errors: [],
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeCleave = this.handleChangeCleave.bind(this);
        this.handleChangeSelect = this.handleChangeSelect.bind(this);
    }

    handleChange = (e) => { this.setState({[e.currentTarget.name]: e.currentTarget.value}) }

    handleChangeCleave = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.rawValue }) }

    handleChangeSelect = (name, e) => { this.setState({ [name]: e !== undefined ? e.value : "" }) }

    render () {
        const { taxes, unities } = this.props;
        const { errors, reference, numero, name, content, unity, price, rateTva, quantity, totalHt } = this.state;

        let [selectTvas, selectUnities] = helper.getTaxesAndUnitiesSelectItems(taxes, unities);

        return <>
            <div className="line">

                <div className="line line-2">
                    <Input valeur={reference} identifiant="reference" errors={errors} onChange={this.handleChange}>Référence (max 10 caractères)</Input>
                    <Input valeur={numero} identifiant="numero" errors={errors} onChange={this.handleChange}>Numéro comptable</Input>
                </div>
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

            <div className="line line-3">
                <Input type="number" valeur={quantity} identifiant="quantity" errors={errors} onChange={this.handleChange}>Quantité</Input>
                <Input type="cleave" valeur={price} identifiant="price" errors={errors} onChange={this.handleChangeCleave}>Prix unitaire</Input>
                <SelectReactSelectize items={selectTvas} identifiant="rateTva"
                                      valeur={rateTva} errors={errors} onChange={(e) => this.handleChangeSelect('rateTva', e)}>
                    Taux de TVA
                </SelectReactSelectize>
            </div>

            <div className="line">
                <div className="form-group">
                    <label>Total HT</label>
                    <div>{Sanitaze.toFormatCurrency(totalHt)}</div>
                </div>
            </div>

            <div className="line">
                <div className="form-button">
                    <Button type="default" outline={true} isSubmit={false}>Enregistrer l'article</Button>
                </div>
            </div>
        </>
    }
}
