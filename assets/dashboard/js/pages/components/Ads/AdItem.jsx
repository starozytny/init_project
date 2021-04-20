import React, { Component } from 'react';

import parseHtml    from 'html-react-parser';

import Sanitize     from "@dashboardComponents/functions/sanitaze";
import { Button }   from "@dashboardComponents/Tools/Button";

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
                content = <div>Financial</div>
                break;
            case "diag":
                content = <div>Diag</div>
                break;
            case "features":
                content = <div>Features</div>
                break;
            default:
                content = <div className="details-tab-infos">
                    <div className="details-tab-infos-main">
                        <div>
                            <div className="label">Identifiant immuable</div>
                            <div>{elem.identifiant}</div>
                        </div>
                        <div>
                            <div className="label">Référence transfert</div>
                            <div>{elem.ref}</div>
                        </div>
                        <div>
                            <div className="label">Référence agence</div>
                            <div>{elem.realRef}</div>
                        </div>
                        {elem.typeAd === "Location" && elem.feature.isMeuble !== 3 && <div>
                            <div className="label">Bien meublé</div>
                            <div>{Sanitize.getTrilieanResponse(elem.feature.isMeuble)}</div>
                        </div>}
                        {elem.dispoString && <div>
                            <div className="label">Disponibilité</div>
                            <div>{elem.dispoString}</div>
                        </div>}
                    </div>

                    <div className="details-tab-infos-content">
                        <div className="content">
                            <div className="label">Description du bien immobilier</div>
                            <p>{parseHtml(elem.content)}</p>
                        </div>
                        <div className="contacts">
                            <div className="label">Contacts</div>
                            <div className="contact">
                                <div>{elem.agency.name}</div>
                                <div>{Sanitize.toFormatPhone(elem.agency.phone)}</div>
                                <div>{elem.agency.email}</div>
                            </div>
                            {elem.responsable && <div className="contact">
                                <div>{elem.responsable.name}</div>
                                <div>{Sanitize.toFormatPhone(elem.responsable.phone)}</div>
                                <div>{elem.responsable.email}</div>
                            </div>}
                        </div>
                    </div>
                </div>
                break;
        }

        let switcherItems = [
            {id: 0, icon: "image"},
            {id: 0, icon: "placeholder"},
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
                            {Sanitize.toFormatCurrency(elem.financial.price)} {elem.typeAd === "Location" && "/ mois"}
                        </div>
                    </div>
                    <div className="details-general">
                        <div className="details-ad-types">
                            <div className={"role type-ad ad-" + elem.codeTypeAd}>{elem.typeAd}</div>
                            <div className="role type-bien">{elem.typeBien}</div>
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
                            return <div className={active} onClick={() => this.handleChangeSwitcher(op.icon)}><span className={"icon-" + op.icon} /></div>
                        })}
                    </div>
                    <div className="images">
                        {elem.images && elem.images.map(img => {
                            return <img src={`/annonces/images/${elem.agency.dirname}/${img.file}`} alt={"Image annonce " + img.rank} key={img.id}/>
                        })}
                    </div>
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