import React, { Component } from 'react';

import Routing       from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Sanitize      from "@dashboardComponents/functions/sanitaze";
import { Infos }     from "./split/Infos";
import { Features }  from "./split/Features";
import { Diag }      from "./split/Diag";
import { Financial } from "./split/Financial";
import { MapSingle } from "./split/MapSingle";
import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";

export class AdItem extends Component {
    constructor(props) {
        super(props);

        this.state = {
            subContext: "infos",
            switcher: "image"
        }

        this.handleChangeSubContext = this.handleChangeSubContext.bind(this);
        this.handleChangeSwitcher = this.handleChangeSwitcher.bind(this);
    }

    handleChangeSubContext = (subContext) => { this.setState({ subContext }) }
    handleChangeSwitcher = (switcher) => { this.setState({ switcher }) }

    render () {
        const { elem, onChangeContext } = this.props
        const { subContext, switcher } = this.state;

        let content = null;
        switch (subContext){
            case "financial":
                content = <Financial elem={elem} />
                break;
            case "diag":
                content = <Diag elem={elem} />
                break;
            case "features":
                content = <Features elem={elem} />
                break;
            default:
                content = <Infos elem={elem} />
                break;
        }

        let switcherItems = [
            {id: 0, icon: "image"},
            {id: 1, icon: "placeholder"},
        ]

        return <div>
            <div className="toolbar">
                <div className="item">
                    <Button icon="left-arrow" outline={true} onClick={() => onChangeContext("list")}>Retour</Button>
                </div>
            </div>

            <div className="details-container">
                <div className="details-content-container">
                    <div className="details-pretitle">{elem.agency.name}</div>
                    <div className="details-title">{elem.label}</div>
                    <div className="details-subtitle">
                        <div>{elem.address.zipcode}, {elem.address.city}</div>
                        <div className="details-subtitle-price">
                            {Sanitize.toFormatCurrency(elem.financial.price)} {elem.typeAd === "Location" && "cc/ mois"}
                        </div>
                    </div>
                    <div className="details-general">
                        <div className="details-ad-types">
                            <div className={"role type-ad ad-" + elem.codeTypeAd}>{elem.typeAd}</div>
                            <div className="role type-bien">{elem.typeBien}</div>
                        </div>
                        <div className="details-ad-actions">
                            <ButtonIcon element="a" target="_blank" onClick={Routing.generate('api_immo_ads_print', {'identifiant': elem.identifiant})} icon="print">Imprimer</ButtonIcon>
                        </div>
                    </div>
                    <Navigation subContext={subContext} onChangeContext={this.handleChangeSubContext} />
                    <div className="details-content">
                        {content}
                    </div>
                </div>
                <div className="details-images-container">
                    <div className="switcher-images-map">
                        {switcherItems.map(op => {
                            let active = op.icon === switcher ? " active" : "";
                            return <div className={active} onClick={() => this.handleChangeSwitcher(op.icon)} key={op.id}><span className={"icon-" + op.icon} /></div>
                        })}
                    </div>
                    {switcher === "image" ? <div className="images">
                        {elem.images.length !== 0 ? elem.images.map(img => {
                            return <img src={`/annonces/images/${elem.agency.dirname}/${img.file}`} alt={"Image annonce " + img.rank} key={img.id}/>
                        }) : <img src={`/immo/logos/${elem.agency.logo}`} alt={`Image de ${elem.agency.name}`}/>}
                    </div> : <MapSingle elem={elem} />}

                </div>
            </div>
        </div>
    }
}

function Navigation({ onChangeContext, subContext }){

    let items = [
        {context: "infos",       label: "Infos"},
        {context: "features",    label: "Caractéristiques"},
        {context: "diag",        label: "Diagnostic"},
        {context: "financial",   label: "Financier"},
    ]

    return (
        <div className="details-nav">
            <div className="details-nav-items">
                {items.map(el => {
                    return <div className={subContext === el.context ? "active" : ""} onClick={() => onChangeContext(el.context)} key={el.context}>{el.label}</div>
                })}
            </div>
        </div>
    )
}